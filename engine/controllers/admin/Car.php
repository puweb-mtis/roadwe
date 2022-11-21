<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 일괄변경 항목
 * 1) [차종] -> [변경텍스트명]
 * 2) [car] -> [해당로직명] -> class명 첫글자 대문자로 변경
 * 3) [vdmd_] -> [driving 테이블의 AS선언명]
 */
class Car extends CI_Controller
{
    private $idx_column_nm = 'id';            // 차종 테이블 일련번호
    private $search_params_type = 'segments';       // segments, querystring

    /**
     * 생성자
     * 
     * @param type void
     * @return return void
     */
    function __construct()
    {
        parent::__construct();

        // 차종 구분 모델 호출
        $this->load->model('carTypeModel');
        // 차량 제조사 모델 호출
        $this->load->model('carProducerModel');
        // 차종 모델 호출
        $this->load->model('carModel');
    }

    /**
     * 화면 공통 변수
     * 
     * @param type void
     * @return return void
     */
    public function formVars()
    {
        $where['sec_default'] = true;
        // 차종 공통 변수 선언
        $this->data = [
            'car_type_list' => $this->carTypeModel->selectList($where), // 차종 구분 리스트
            'car_producer_list' => $this->carProducerModel->selectList($where), // 차량 제조사 리스트            
            'car_model_list' => $this->carModel->selectList($where), // 차종 리스트
        ];
        // debug_var($this->data);

        // 차종 상세 변수 선언
        if ($this->uri->rsegment(2) === 'form') {
        }
    }

    /**
     * 기본(샘플) 페이지
     * 
     * @param type void
     * @return return void
     */
    public function index()
    {
        // $this->siteinit->_debug = true;
        $data = [];
        $data['page_title'] = '차종';

        $data['search_params_type'] = $this->search_params_type;
        if ($this->search_params_type === 'segments') {
            // 검색 조건을 URI문자열 방식으로 처리 할 경우(한글 깨지는 이슈 확인 예정)
            setKeywordUrl();
            $sec_params = getKeywordUrl(3);
        } else {
            // 검색 조건을 쿼리스트링 방식으로 처리 할 경우
            $sec_params = getQueryStringParams();
        }
        // 기본 겁색 초기 값 선언
        $sec_params['sec_column'] = ifEmpty($sec_params, 'sec_column');
        $sec_params['sec_keyword'] = ifEmpty($sec_params, 'sec_keyword');
        $sec_params['sec_date_type'] = ifEmpty($sec_params, 'sec_date_type');
        $sec_params['sec_st_date'] = ifEmpty($sec_params, 'sec_st_date');
        $sec_params['sec_ed_date'] = ifEmpty($sec_params, 'sec_ed_date');
        $sec_params['sec_car_type_cd'] = ifEmpty($sec_params, 'sec_car_type_cd');

        // 기본 검색 조건 지정
        $sec_params['sec_default'] = true;

        // 정렬 방법 지정
        if (!isset($sec_params['od_column']) && !isset($sec_params['od_type'])) {
            $sec_params['od_type'] = 'DESC';
            // $sec_params['od_column'] = 'acmg.comm_reg_date';
            $sec_params['od_column'] = 'VDMD.c_datetime';
        }

        // 오프셋, 리밋 지정
        $sec_params['offset'] = isset($sec_params['offset']) ? $sec_params['offset'] : 0;

        if ($this->siteinit->_request_type === 'ajax') {
            $sec_params['limit'] = isset($sec_params['limit']) ? $sec_params['limit'] : $this->siteinit->_admin['limit'];
        } else {
            $sec_params['limit'] = isset($sec_params['limit']) ? $sec_params['limit'] : $this->siteinit->_admin['limit'];
        }

        $data['cntall'] = $this->carModel->selectCountAll();
        // 커버링 인덱스 쿼리 사용
        // $data_list = $this->carModel->coveringindexActiveList($sec_params);
        // $data['cnt'] = $data_list['rows'];
        // $data['list'] = $data_list['list'];
        // 커버링 인덱스 쿼리 미사용
        $data['cnt'] = $this->carModel->selectCount($sec_params);
        $data['list'] = $this->carModel->selectList($sec_params);
        // debug_last_query();

        if ($this->search_params_type === 'segments') {
            $data['pager'] = getSegmentsPager($data['cnt'], $sec_params['limit']);
        } else {
            $data['pager'] = getQueryStringPager($data['cnt'], $sec_params['limit']);
        }
        $data['page_index'] = (int)$data['cnt'] - (int)$sec_params['offset'];

        if (isset($sec_params)) {
            $data['sec_params'] = $sec_params;
        }

        // 화면 공통 변수 호출
        $this->formVars();
        // 추가 스크립트 선언
        $this->scripts[] = $this->common->scripts(array('car.js'));
        // 화면 뷰
        if (isset($this->data) && ($this->data != null)) {
            $data['set_params'] = $this->data;
        }
        $this->load->view(null, $data);
        return;
    }

    /**
     * 기본(차종) 등록&수정 폼입니다.(등록/수정 폼을 같이 쓸 경우)
     * @param int $idx 차종 일련번호
     */
    public function form($idx = null)
    {
        // $this->siteinit->_debug = true;
        $data = [];

        if (is_null($idx)) { // 등록화면
            $data['crud_mode'] = 'create';
            // row 초기 값 선언
            $data['row'] = $this->carModel->listFields();
        } else { // 수정화면
            try {
                $data['crud_mode'] = 'update';
                $where[$this->idx_column_nm] = $idx;
                $data['row'] = $this->carModel->selectRow($where);
                unset($where);

                if ($data['row']) {
                } else {
                    throw new Exception($this->lang->line('cm_have_seq_data_error'));
                }
            } catch (Exception $exc) {
                $this->js->pageBack($exc->getMessage());
                return false;
            }
        }

        // 화면 공통 변수 호출
        $this->formVars();
        // 추가 스크립트 선언
        $this->scripts[] = $this->common->scripts(array('car.js'));
        // 화면 뷰
        if (isset($this->data) && ($this->data != null)) {
            $data['set_params'] = $this->data;
        }
        $this->load->view(null, $data);
        return;
    }

    /**
     * 폼데이터를 공통으로 가공할때 호출 하는 함수입니다. 
     * @param int $crud_data 폼 전송 데이터
     */
    public function formData($crud_data)
    {
        if ($this->uri->rsegment(3) === 'create') {
        }
        return $crud_data;
    }

    /**
     * 기본(차종) 처리단입니다.(등록&수정)
     * @param string $crud_mode 처리타입
     * @param int $idx 해당일련번호
     */
    public function crudProc($crud_mode = null, $idx = null)
    {
        if ($crud_mode === 'create' || $crud_mode === 'update') { // 등록 && 수정
            $this->load->library('form_validation');
            $crud_data = $this->input->post();

            // 폼 값 서버단 검증 처리
            $form_validation_rules = ($crud_mode === 'create') ? 'car_create' : 'car_update';
            if ($this->form_validation->run($form_validation_rules)) {
                $crud_data = $this->formData($crud_data);

                try {
                    if ($crud_mode === 'create') { // 등록
                        $vdmd_key = $this->carModel->insert($crud_data);
                    } elseif ($crud_mode === 'update') { // 수정
                        $update_where[$this->idx_column_nm] = $idx;
                        $vdmd_key = $this->carModel->update($crud_data, $update_where);
                        unset($update_where);
                    }

                    if ($vdmd_key) {
                        if ($crud_mode === 'create') { // 등록
                            ajaxExcep("cm_car_{$crud_mode}_success", site_url(ADMINURL . '/car/index'));
                            // $this->js->pageRedirect(site_url(ADMINURL . '/car/index'), $this->lang->line("cm_car_{$crud_mode}_success"));
                            // return false;
                        } else { // 수정
                            ajaxExcep("cm_car_{$crud_mode}_success", $this->input->post('referer'));
                            // $this->js->pageRedirect($this->input->post('referer'), $this->lang->line("cm_car_{$crud_mode}_success"));
                            // return false;
                        }
                    } else {
                        throw new Exception($this->lang->line("cm_car_{$crud_mode}_error"));
                    }
                } catch (Exception $exc) {
                    $this->js->pageBack($exc->getMessage());
                    return false;
                }
            } else {
                $this->js->pageBack(str_replace("\n", "\\n", strip_tags(validation_errors())));
            }
            return;
        } elseif ($crud_mode === 'putall') { // 전체(데이터)수정
        } elseif ($crud_mode === 'put') { // 개별(컬럼)수정
            $update_data = [];

            $column_nm = ($this->input->post('column_nm')) ? $this->input->post('column_nm') : $this->uri->rsegment(4);
            $column_val = ($this->input->post('column_val')) ? $this->input->post('column_val') : $this->uri->rsegment(5);

            $update_data[$column_nm] = $column_val;
            $update_where[$this->idx_column_nm] = ($this->input->post('pk_idx')) ? $this->input->post('pk_idx') : (int)$this->uri->rsegment(6);
            $update_res = $this->carModel->update($update_data, $update_where);
            unset($update_data);
            unset($update_where);

            ajaxExcep('cm_car_update_success', null, true);
            // json 타입 리턴일 경우 사용
            // $json['result'] = true;
            // $json['msg'] = $this->lang->line('cm_car_update_success');
            // echo json_encode($json);
            // return;
        } elseif ($crud_mode === 'deleteall') { // 전체(데이터)논리 삭제 - 원데이타 보존을 위해 업데이트로 처리
            try {
                $update_data = [];
                $ary_del_idx = $this->input->post($this->idx_column_nm);

                if (isset($ary_del_idx)) {
                    for ($i = 0; $i < count($ary_del_idx); $i++) {
                        $del_idx = $ary_del_idx[$i];

                        $update_data[] = [
                            "{$this->idx_column_nm}" => $del_idx,
                            'comm_del_yn' => 'Y'
                        ];
                    }
                    $this->carModel->updateBatch($update_data);

                    ajaxExcep('cm_car_delete_success', null, true);
                    // json 타입 리턴일 경우 사용
                    // $json['result'] = true;
                    // $json['msg'] = $this->lang->line('cm_car_delete_success');
                    // echo json_encode($json);
                    // return;
                } else {
                    throw new Exception($this->lang->line('cm_have_seq_data_error'));
                }
            } catch (Exception $exc) {
                if ($this->siteinit->_request_type === 'ajax') {
                    ajaxExcep('cm_have_seq_data_error', null, true);
                } else {
                    $this->js->pageBack($exc->getMessage());
                    return false;
                }
            }
        } elseif ($crud_mode === 'delete') { // 개별(데이터)물리 삭제
            if ($this->uri->rsegment(4)) {
                try {
                    $where[$this->idx_column_nm] = $this->uri->rsegment(4);
                    $data['row'] = $this->carModel->selectRow($where);
                    unset($where);

                    if ($data['row']) {
                        $where[$this->idx_column_nm] = $data['row'][$this->idx_column_nm];
                        $this->carModel->delete($where);
                        unset($where);

                        ajaxExcep("cm_car_{$crud_mode}_success", $this->input->server('HTTP_REFERER'));
                        // $this->js->pageRedirect($this->input->server('HTTP_REFERER'), $this->lang->line("cm_car_{$crud_mode}_success"));
                        // return false;
                    } else {
                        throw new Exception($this->lang->line('cm_have_seq_data_error'));
                    }
                } catch (Exception $exc) {
                    if ($this->common->request_type === 'ajax') {
                        ajaxExcep('cm_have_seq_data_error', null, true);
                    } else {
                        $this->js->pageBack($exc->getMessage());
                        return false;
                    }
                }
            } else {
                ajaxExcep("cm_car_{$crud_mode}_error", site_url(ADMINURL . '/car/index'));
                // $this->js->pageRedirect(site_url(ADMINURL . '/car/index'), $this->lang->line("cm_car_{$crud_mode}_error"));
                // return false;
            }
        } else {
            show_error('Access is limited on this page.');
        }
        return;
    }

    /**
     * 요청별 정보를 불러옵니다.
     * 기본 동작은 AJAX 호출입니다.
     * @param type void
     * @return json $data
     */
    public function loadData($req_type = null)
    {
        // $result_data = [];

        //기본 검색 조건 지정
        // $where['sec_default'] = true;

        if ($req_type === 'car') { //차종 정보를 가져온다.
            // 해당 테이블 단일행 가져오기
            // $where['id'] = $this->input->post('vdmd_idx');
            // $result_data = $this->carModel->selectRow($where);

            // 해당 쿼리 데이터 가져오기
            $where['vdmd_idx'] = $this->input->post('vdmd_idx');
            $result_list = $this->carModel->selectList($where);
            if ($result_list) { // 리스트 정보가 있는 경우
                $result_data = $result_list[0];
            }
        } elseif ($req_type === 'driver') { //운전기사 정보를 가져온다.
            // 해당 쿼리 데이터 가져오기
            $where['vddv_idx'] = $this->input->post('vddv_idx');
            $result_list = $this->driverModel->selectList($where);
            if ($result_list) { // 리스트 정보가 있는 경우
                $result_data = $result_list[0];
            }
        } elseif ($req_type === 'transport') { //운송회사 정보를 가져온다.
            // 해당 쿼리 데이터 가져오기
            $where['tpcp_idx'] = $this->input->post('tpcp_idx');
            $result_list = $this->transportCompanyModel->selectList($where);
            if ($result_list) { // 리스트 정보가 있는 경우
                $result_data = $result_list[0];
            }
        }
        unset($where);

        $flag = (isset($result_data)) ? true : false;

        $json['result'] = $flag;
        $json['list'] = $result_data;
        echo json_encode($json);
    }
}
