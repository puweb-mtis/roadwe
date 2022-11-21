<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 공통 메타 로직 모음
 * @author jbKim
 * @since version 1.0
 */
class Meta
{
    public $CI = null;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('commonModel');
    }

    /**
     * set
     *
     * 사이트 메타 정보를 저장한다.
     *
     * @param string        $meta_group     메타그룹
     * @param array	        $meta_array     메타배열값
     * @param int           $meta_type      10:사이트구성정보, 20:사이트기본정보
     * @return void
     */
    public function set($meta_group, $meta_array = [], $meta_type = 10)
    {
        $insert_data['cmmt_group'] = $meta_group;
        $insert_data['cmmt_type'] = $meta_type;
        if (is_array($meta_array)) {
            foreach ($meta_array as $meta_key => $meta_val) {
                // 제외할 key명 선언
                $remove_key = array('referer', 'mode', 'meta_group');
                if (in_array($meta_key, $remove_key)) {
                    continue;
                }

                if (!empty($meta_array[$meta_key])) {
                    // 배열 형태 데이터 처리
                    if (is_array($meta_val)) {
                        $meta_val = json_encode($meta_val);
                    }
                    $insert_data['cmmt_key'] = $meta_key;
                    $insert_data['cmmt_val'] = $meta_val;
                    $this->CI->commonModel->set($insert_data);
                }
            }
            unset($insert_data);
        } else {
            show_error('\'\$meta_array\' is not array.', 500, null . ' parameter is not array.');
        }
        return;
    }

    /**
     * get
     *
     * 사이트 메타 정보를 추출한다.
     * 메타키 값이 있을 경우 해당 메타그룹에 메타키 값만 추출한다.
     * 메타키 값이 없는 경우 해당 메타그룹으로 목록을 추출한다.
     *
     * @param string        $meta_group     메타그룹
     * @param int           $meta_type      10:사이트구성정보, 20:사이트기본정보
     * @param string        $meta_key       메타키
     * @return array|void
     */
    public function get($meta_group, $meta_type = 10, $meta_key = '')
    {
        if (!is_null($result = $this->CI->commonModel->get($meta_group, $meta_type, $meta_key))) {
            foreach ($result as $row) {
                // 배열 형태 데이터 처리
                if (isJson($row['cmmt_val'])) {
                    $row['cmmt_val'] = json_decode($row['cmmt_val'], true);
                }
                $return_ary[$row['cmmt_key']] = $row['cmmt_val'];
            }
            if (isset($return_ary))
                return $return_ary;
        }
        return;
    }

    /**
     * uploadSetting
     *
     * 파입 업로드 경로및 기본값을 선언한다.
     *
     * @param string		$parent_tbl			부모 테이블명
     * @return array
     */
    public function uploadSetting($parent_tbl = null, $exts = null, $upload_path = null, $max_size = null)
    {
        $up_setting = [];

        // 부모테이블명(데이터 가져올때 구분을 위해)
        if ($parent_tbl) {
            $up_setting['set_parent_tbl'] = $parent_tbl;
        } else {
            $up_setting['set_parent_tbl'] = 'default_tbl';
        }

        // 파입 업로드 가능한 확장자 체크
        if ($exts) {
            $up_setting['set_allowed_types'] = $exts;
        } else {
            // eX) jpeg|jpg|png|gif|bmp|pdf|zip|dwg|xls|doc|hwp|alz|step|rar|dxf|xlsx|bak|stp|sldprt|mp4|pptx
            $up_setting['set_allowed_types'] = 'xlsx|jpeg|jpg|png|gif';
        }

        // 파입 업로드 경로
        if ($upload_path) { // 다른 경로를 사용할 경우 사용
            $up_setting['set_upload_path'] = $upload_path;
        } else {
            // 파일 업로드 절대 경로 선언
            $real_path = UPLOADPATH . $parent_tbl;

            // 해당 폴더가 있는지 검사하고 없으면 생성
            if (!is_dir($real_path)) {
                @mkdir($real_path, 0700, true);
            } elseif (!is_writable($real_path)) {
                @chmod($real_path, 0700);
            }

            $up_setting['set_upload_path'] = $real_path;
        }

        // 파일 업로드 최대 사이즈
        if ($max_size) {
            $up_setting['set_max_size'] = $max_size;
        } else {
            $up_setting['set_max_size'] = 10240;
        }

        return $up_setting;
    }

    /**
     * setUpload
     *
     * 파일 업로드및 정보를 DB에 저장한다.
     *
     * @param string		$parent_tbl         부모 테이블명
     * @param int			$seq				부모ID
     * @param string        $upload_path        저장 폴더 이름
     * @param string		$file_field_name	파일필드명
     * @param string		$exts				확장자
     * @param string		$max_size			업로드 파일 최대 용량
     * @param int			$gubun				파일구분(10:일반, 20:에디터)
     * @return void
     */
    public function setFileUpload($parent_tbl, $seq = 0, $upload_path = null, $file_field_name = 'userfile', $exts = null,  $max_size = 10240, $gubun = 10)
    {
        $up_setting = $this->uploadSetting($parent_tbl, $exts, $upload_path, $max_size);

        $this->CI->load->library('upload');

        $uploaded_files = $_FILES;
        $uploaded_file_count = count($_FILES[$file_field_name]['name']);

        $result['upload_data'] = [];
        for ($i = 0; $i < $uploaded_file_count; $i++) {
            if ($uploaded_files[$file_field_name]['name'][$i] == null)
                continue;

            unset($_FILES);

            $_FILES[$file_field_name]['name']       = $uploaded_files[$file_field_name]['name'][$i];
            $_FILES[$file_field_name]['type']       = $uploaded_files[$file_field_name]['type'][$i];
            $_FILES[$file_field_name]['tmp_name']   = $uploaded_files[$file_field_name]['tmp_name'][$i];
            $_FILES[$file_field_name]['error']      = $uploaded_files[$file_field_name]['error'][$i];
            $_FILES[$file_field_name]['size']       = $uploaded_files[$file_field_name]['size'][$i];

            $upload_config = array(
                'upload_path'       => $up_setting['set_upload_path'],
                'allowed_types'     => $up_setting['set_allowed_types'],
                'max_size'          => $up_setting['set_max_size'],
                'encrypt_name'      => true
            );
            $this->CI->upload->initialize($upload_config);

            if ($this->CI->upload->do_upload()) {
                $result['upload_data'][$i] = $this->CI->upload->data();
            } else {
                $result = array('errors_msg' => $this->CI->upload->display_errors());
            }
        }

        $upfiles = new stdClass();
        if (!empty($result['upload_data'])) {
            $res_file = [];

            $tbl_prefix = 'cmmtf';

            foreach ($result['upload_data'] as $idx => $file) {
                $res_file[$idx][$tbl_prefix . '_type']          = $gubun;
                $res_file[$idx][$tbl_prefix . '_parent_tbl']    = $up_setting['set_parent_tbl'];
                $res_file[$idx][$tbl_prefix . '_parent_idx']    = $seq;
                $res_file[$idx][$tbl_prefix . '_path']          = $up_setting['set_upload_path'];
                $res_file[$idx][$tbl_prefix . '_file']          = $file['file_name'];
                $res_file[$idx][$tbl_prefix . '_file_origin']   = $file['client_name'];
                $res_file[$idx][$tbl_prefix . '_ext']           = str_replace('.', '', $file['file_ext']);
                $res_file[$idx][$tbl_prefix . '_size']          = $file['file_size'];
                if (function_exists('mime_content_type')) {
                    $mime_type = mime_content_type($file['full_path']);
                } else {
                    $mime_type = $file['image_type'];
                }
                $res_file[$idx]['cmmtf_mime'] = $mime_type;

                // insert_id필요해서 uploadBatch 사용하지 않고 하나씩 처리
                $insert_id = $this->CI->commonModel->insert($res_file[$idx], $parent_tbl);
                $result['upload_data'][$idx][$tbl_prefix . '_idx'] = $insert_id;
            }
            // insert_id필요하지 않는 경우 일괄 등록 처리
            // $this->CI->commonModel->uploadBatch($res_file);

            $upfiles->code = 100;
            $upfiles->message = '파일이 정상적으로 업로드 되었습니다.';
            $upfiles->resfile = $result;
        } else {
            $upfiles->code = 404;
            $upfiles->message = $result['errors_msg'];
        }
        $this->CI->upfile = $upfiles;
    }

    /**
     * getFile
     *
     * 파일 업로드 정보를 추출한다.
     *
     * @param string		$parent_tbl			부모테이블명
     * @param int			$parent_no			특정일련번호(부모seq)
     * @param int			$gubun				파일구분(10:일반, 20:에디터)
     * @param int 			$cmmtf_idx			키 지정시
     * @return void
     */
    public function getFileLoad($parent_tbl, $parent_no, $gubun = 10, $cmmtf_idx = null)
    {
        if (!is_null($result = $this->CI->commonModel->getFileLoad($parent_tbl, $parent_no, $gubun = 10, $cmmtf_idx))) {
            foreach ($result as $row) {
                if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1') { // 로컬인경우 경로 치환
                    $search_txt = getcwd();
                    $row['cmmtf_path'] = str_replace($search_txt, '', $row['cmmtf_path']);
                    $row['cmmtf_path'] = str_replace('\\', '/', $row['cmmtf_path']);
                }
                $return_ary[] = $row;
            }
            if (isset($return_ary))
                return $return_ary;
        } else {
            return false;
        }
    }   
    
    /**
     * uploadRemove
     *
     * 파일 삭제 및 정보를 DB에 삭제한다.
     *
     * @param string		$parent_tbl         부모 테이블명
     * @param int			$parent_no			특정일련번호(부모seq)
     * @param int 			$cmmtf_idx			키 지정시*
     * @param int			$gubun				파일구분(10:일반, 20:에디터)
     * @return void
     */
    public function uploadRemove($parent_tbl, $parent_no = 0, $cmmtf_idx = null, $gubun = 10)
    {
        $file_remove_arg = $this->getFileLoad($parent_tbl, $parent_no, $gubun, $cmmtf_idx);
        if (is_array($file_remove_arg)) {
            foreach ($file_remove_arg as $key => $val) {
                $real_file = UPLOADPATH . $val['cmmtf_parent_tbl'] . '/' . $val['cmmtf_file'];
                if (file_exists($real_file)) {
                    @unlink($real_file);
                }
            }
        }

        $this->CI->commonModel->uploadRemove($parent_tbl, $parent_no, $cmmtf_idx, $gubun);
        unset($where);
    }    
}
