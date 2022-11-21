<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('makePasswd')) {
    /**
     * 사이트의 비밀번호 암호화 방식입니다.
     * SHA512 암호화 후에 base64로 한번더 인코딩
     * 
     * @param string $inputPasswd 사용자 입력 패스워드
     * @return bool 암호화된 문자열
     */
    function makePasswd($inputPasswd)
    {
        $r = base64_encode(hash('sha512', $inputPasswd, true));
        return $r;
    }
}

if (!function_exists('ifEmpty')) {
    /**
     * 초기값이 없을때 나는 오류 방지를 위한 기본값을 지정합니다.
     *
     * @param array $arr
     * @param [type] $key
     * @param [type] $default
     * @return void
     */
    function ifEmpty($arr = array(), $key = null, $default = null)
    {
        if (array_key_exists($key, $arr)) {
            if (empty($arr[$key])) {
                return $arr[$key] = $default;
            } else {
                return $arr[$key];
            }
        }
        return $default;
    }
}

if (!function_exists('addCommColumn')) {
    /**
     * 데이터 insert / update 시 공통 컬럼값을 지정합니다.
     *
     * @param array $params 
     * @param string $mode
     * @return array
     * @todo 클라이언트 별도 처리 예정
     */
    function addCommColumn($params = null, $mode = 'write')
    {
        $CI = &get_instance();
        $comm_params = $params;

        $comm_id = '';
        $comm_name = '';
        if (is_cli()) {
            $comm_id = 'SYSTEM_CRON';
            $comm_name = '시스템크론';
        } else {
            if ($CI->siteinit->_service_mode === 'admin') {
                // $comm_id = $CI->siteinit->_admin_info['acmg_id'];
                // $comm_name = $CI->siteinit->_admin_info['acmg_name'];
                $comm_id = $CI->siteinit->_admin_info['account'];
                $comm_name = $CI->siteinit->_admin_info['admin_name'];
            } elseif ($CI->siteinit->_service_mode === 'client') {
                // TODO: 클라이언트 별도 처리 예정
            }
        }

        if ($mode == 'write') {
            // $comm_params['comm_mod_id'] = $comm_id;
            // $comm_params['comm_mod_name'] = $comm_name;
            // $comm_params['comm_mod_date'] = date('Y-m-d H:i:s');
            // $comm_params['comm_reg_id'] = $comm_id;
            // $comm_params['comm_reg_name'] = $comm_name;
            // if (!isset($comm_params['comm_reg_date'])) {
            //     $comm_params['comm_reg_date'] = date('Y-m-d H:i:s');
            // }
            $comm_params['m_account'] = $comm_id;
            $comm_params['m_name'] = $comm_name;
            $comm_params['m_datetime'] = date('Y-m-d H:i:s');
            $comm_params['c_account'] = $comm_id;
            $comm_params['c_name'] = $comm_name;
            if (!isset($comm_params['c_datetime'])) {
                $comm_params['c_datetime'] = date('Y-m-d H:i:s');
            }
            if ($CI->input->server('HTTP_X_FORWARDED_FOR')) {
                $comm_params['comm_remote_ip'] = $CI->input->server('HTTP_X_FORWARDED_FOR');
            } else {
                $comm_params['comm_remote_ip'] = $CI->input->ip_address();
            }
        } elseif ($mode == 'modify') {
            // $comm_params['comm_mod_id'] = $comm_id;
            // $comm_params['comm_mod_name'] = $comm_name;
            // $comm_params['comm_mod_date'] = date('Y-m-d H:i:s');
            $comm_params['m_account'] = $comm_id;
            $comm_params['m_name'] = $comm_name;
            $comm_params['m_datetime'] = date('Y-m-d H:i:s');
        }
        return $comm_params;
    }
}

if (!function_exists('isJson')) {
    /**
     * 데이터가 json 형태인지 확인합니다.
     * 
     * @param string $string
     * @return bool true|false
     */
    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}

if (!function_exists('ajaxExcep')) {
    /**
     * ajax 에서 처리되는 예외처리를 합니다.
     * ajax 타입으로 호출 했을시에는 에러메세지를,
     * http 방식으로 호출 했을때에는 alert를 띄웁니다.
     * @param string $msg_lang      기본 에러 메세지
     * @param string $$redirect     리다이텍트 경로URL
     * @param bool   $return_result 리턴 결과
     * @param bool   $return        리턴 여부
     * @param bool   $req_type      요청 타입
     */
    function ajaxExcep($msg_lang = 'nc_error_url', $redirect = null, $return_result = false, $return = false, $req_type = null)
    {
        $CI = &get_instance();

        if (is_null($req_type)) {
            $req_type = $CI->siteinit->_request_type;
        }

        if ($req_type === 'http') {
            if ($redirect) {
                $CI->js->pageRedirect($redirect, $CI->lang->line($msg_lang));
                return false;
            } else {
                $CI->js->pageBack($CI->lang->line($msg_lang));
                return false;
            }
        } elseif ($req_type === 'ajax') {
            $result = array();
            $result['result'] = $return_result;
            $result['msg'] = $CI->lang->line($msg_lang);
            if ($return === true) {
                return $result;
            } elseif ($return === false) {
                echo json_encode($result);
                return;
            }
        }
        return;
    }
}

if (!function_exists('getQueryStringParams')) {

    function getQueryStringParams()
    {
        $CI = &get_instance();
        return array_filter($CI->input->get());
    }
}

if (!function_exists('getQueryStringPager')) {

    /**
     * CI의 기본 쿼리스트링 페이징을 처리를 합니다.
     * @param int $cnt 총 갯수
     * @param int $perpage 퍼페이지
     *
     * @return string HTML으로 된 PAGER를 구성합니다.
     */
    function getQueryStringPager($total_rows, $per_page = 10)
    {
        $CI = &get_instance();

        $base_url = '/' . $CI->uri->uri_string() . $CI->config->item('url_suffix');

        $config['base_url'] = $base_url;
        // 페이지 전체 레코드의 수
        $config['total_rows'] = $total_rows;
        // 한 페이지에 보여질 row수
        $config['per_page'] = $per_page;
        // 페이지네이션 노출 갯수
        $config['num_links'] = 5;
        // 페이지네이션 라이브러리 기본값 변경(URI세그먼트(미지정 & FALSE) 또는 쿼리스트링(TRUE))
        $config['page_query_string'] = TRUE;
        // 페이지네이션에 기본적으로 전달되는 쿼리스트링 명 변경(기본값:per_page)
        $config['query_string_segment'] = 'offset';
        // URI 새그먼트 후 URL에 접미사하기 전에 기존의 쿼리 문자열 인수를 추가(ex)검색 조건)
        $config['reuse_query_string'] = TRUE;

        // 사용자 정의 접미사가 경로에 추가(사용할 경우 에러 발생)
        // $config['use_global_url_suffix'] = TRUE;

        $config['full_tag_open'] = '<div style=\'text-align:center;\'><ul class=\'pagination\'>';
        $config['full_tag_close'] = '</ul></div>';

        $config['first_link'] = '&lt;&lt;';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';

        $config['prev_link'] = '&lt;';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        $config['next_link'] = '&gt;';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';

        $config['last_link'] = '&gt;&gt;';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li><a class=\'active\'>';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        // 페이지네이션 클래스에 의해 렌더링 되는 모든 링크에 추가 속성 선언
        // $config['attributes'] = array('class' => 'page-link');

        $CI->load->library('pagination');
        $CI->pagination->initialize($config);
        return $CI->pagination->create_links();
    }
}

if (!function_exists('setKeywordUrl')) {

    function setKeywordUrl($exceptParams = null)
    {
        $CI = &get_instance();

        // 제외항목 우선 제거
        if (is_array($exceptParams)) {
            foreach ($exceptParams as $val) {
                if (isset($_POST[$val])) {
                    unset($_POST[$val]);
                }
            }
        }

        $params = $CI->input->post();
        $params_ary = array();
        if (is_array($params) && count($params) > 0) {
            foreach ($params as $key => $val) {
                if (is_array($CI->input->post($key))) {
                    $params_ary[$key] = 'Array';
                    $params_ary[$key . '_CNT'] = count($CI->input->post($key));
                    foreach ($CI->input->post($key) as $key1 => $data) {
                        /*
                         * http://stackoverflow.com/questions/2678551/when-to-encode-space-to-plus-or-20
                         *
                         *  space가 + 로 해석되는 문제가 있어서
                         *  urlencode ==> rawurlencode 변경
                         */
                        // $params_ary[$key.'_'.$key1] = urlencode($data);
                        $params_ary[$key . '_' . $key1] = rawurlencode($data);
                    }
                    continue;
                } elseif (strlen($val) > 0) {
                    // $params_ary[$key] = urlencode($val);
                    $params_ary[$key] = rawurlencode($val);
                }
            }
            $url = site_url('/' . $CI->uri->uri_string() . '/' . $CI->uri->assoc_to_uri($params_ary));
            redirect($url);
            return;
        }
    }
}

if (!function_exists('getKeywordUrl')) {

    function getKeywordUrl($assoc = 1)
    {
        $CI = &get_instance();
        $keyword_ary = $CI->uri->ruri_to_assoc($assoc);
        $return = array();
        foreach ($keyword_ary as $key => $data) {
            if ($data === 'Array') {
                for ($i = 0; $i < $keyword_ary[$key . '_CNT']; $i++) {
                    $tmpAry[] = rawurldecode($keyword_ary[$key . '_' . $i]);
                }
                // $return[$key] = urldecode($tmpAry);
                // $return[$key] = rawurldecode($tmpAry);
                $return[$key] = $tmpAry;
                continue;
            }
            if (strrpos($key, '_CNT')) {
                continue;
            }
            // $return[$key] = urldecode($data);
            $return[$key] = rawurldecode($data);
        }
        return $return;
    }
}

if (!function_exists('getSegmentsPager')) {

    /**
     * CI의 기본 URI새그먼트 페이징을 처리를 합니다.
     * @param int $cnt 총 갯수
     * @param int $perpage 퍼페이지
     *
     * @return string HTML으로 된 PAGER를 구성합니다.
     */
    function getSegmentsPager($total_rows, $per_page = 10, $uri_segment = null, $is_ruri_string = true)
    {
        $CI = &get_instance();
        if (is_bool($is_ruri_string)) {
            if ($is_ruri_string === TRUE) {
                $base_url = '/' . preg_replace('/\/offset\/(.*)/', '', $CI->uri->ruri_string()) . '/offset/';
            } elseif ($is_ruri_string === FALSE) {
                $base_url = '/' . preg_replace('/\/offset\/(.*)/', '', $CI->uri->uri_string()) . '/offset/';
            }
        } else {
            $base_url = '/' . preg_replace('/\/offset\/(.*)/', '', $is_ruri_string) . '/offset/';
        }
        $config['base_url'] = $base_url;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $per_page;
        $config['use_global_url_suffix'] = TRUE;
        $config['reuse_query_string'] = TRUE;

        $config['num_links'] = 5;
        if ($uri_segment) {
            $config['uri_segment'] = $uri_segment;
        }

        $config['full_tag_open'] = '<div style=\'text-align:center;\'><ul class=\'pagination\'>';
        $config['full_tag_close'] = '</ul></div>';

        $config['first_link'] = '&lt;&lt;';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';

        $pattens = array('/\/offset\/(.*)/', '/\/limit\/(.*)/');
        if (is_bool($is_ruri_string)) {
            if ($is_ruri_string === TRUE) {
                $config['first_url'] = '/' . preg_replace($pattens, '', $CI->uri->ruri_string()) . $CI->config->item('url_suffix');
            } else {
                $config['first_url'] = '/' . preg_replace($pattens, '', $CI->uri->uri_string()) . $CI->config->item('url_suffix');
            }
        } else {
            $config['first_url'] = '/' . $is_ruri_string . $CI->config->item('url_suffix');
        }

        $config['prev_link'] = '&lt;';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        $config['next_link'] = '&gt;';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';

        $config['last_link'] = '&gt;&gt;';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li><a class=\'active\'>';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $CI->load->library('pagination');
        $CI->pagination->initialize($config);
        return $CI->pagination->create_links();
    }
}

if (!function_exists('getDateFromBit')) {
    /**
     * getDateFromBit
     * 요일 bit 값을 받아서 한글 요일로 변환
     * 
     * @parameter: week
     * @return: array
     */
    function getDateFromBit($week)
    {
        $result = array();
        if (!$week) {
            return $result;
        }
        $day_ko = array(1 => "일", 2 => "월", 4 => "화", 8 => "수", 16 => "목", 32 => "금", 64 => "토");
        foreach ($day_ko as $key => $val) {
            if (($week & $key) == $key) {
                $result[] = $val;
            }
        }
        return $result;
    }
}

if (!function_exists('getBitFromDate')) {
    /**
     *  날짜를 요일 bit로 반환
     *
     *  @parameter: 2022-09-01
     *  @return bit value
     */
    function getBitFromDate($date)
    {
        $driving_date = date('w', strtotime($date));

        return 1 << $driving_date;
    }
}
