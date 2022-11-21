<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 일괄변경 항목
 * 1) [샘플운영자] -> [변경텍스트명]
 * 2) [sample] -> [해당로직명] -> class명 첫글자 대문자로 변경
 * 3) [spmg_] -> [driving 테이블의 AS선언명]
 */
class Sample extends CI_Controller
{
    private $idx_column_nm = 'spmg_idx';            // 샘플운영자 테이블 일련번호
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

        // 샘플운영자 모델 호출
        $this->load->model('sampleModel');
    }
    
    /**
     * 화면 공통 변수
     * 
     * @param type void
     * @return return void
     */
    public function formVars()
    {
        // 샘플운영자 공통 변수 선언
        $this->data = [
            'spmg_permit_cd' => $this->meta->get('spmg_permit_cd', 10), // 샘플운영자 접근유형
            'spmg_status_cd' => $this->meta->get('spmg_status_cd', 10), // 샘플운영자 접근승인여부
        ];

        // 샘플운영자 상세 변수 선언
        if ($this->uri->rsegment(2) === 'form') {
            // 이메일/전화&팩스번호/휴대폰번호 리스트를 가져온다.
            $this->data['email_cd'] = $this->meta->get('email_cd', 10);
            $this->data['phone_cd'] = $this->meta->get('phone_cd', 10);
            $this->data['cellphone_cd'] = $this->meta->get('cellphone_cd', 10);
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
        $data = [];
        $data['page_title'] = '샘플운영자';

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
        $sec_params['sec_date_type'] = ifEmpty($sec_params,'sec_date_type');
        $sec_params['sec_st_date'] = ifEmpty($sec_params,'sec_st_date');
        $sec_params['sec_ed_date'] = ifEmpty($sec_params,'sec_ed_date');
        $sec_params['sec_spmg_permit_cd'] = ifEmpty($sec_params,'sec_spmg_permit_cd');
        $sec_params['sec_column'] = ifEmpty($sec_params,'sec_column');
        $sec_params['sec_keyword'] = ifEmpty($sec_params,'sec_keyword');
        $sec_params['sec_spmg_status_cd'] = ifEmpty($sec_params,'sec_spmg_status_cd');

        // 기본 검색 조건 지정
        $sec_params['sec_default'] = true;
        // $sec_params['sec_spmg_status_cd'] = 30; // 차단된 운영자만 가져올 경우

        // 정렬 방법 지정
        if (!isset($sec_params['od_column']) && !isset($sec_params['od_type'])) {
            $sec_params['od_type'] = 'DESC';
            $sec_params['od_column'] = 'SPMG.comm_reg_date';
        }

        // 오프셋, 리밋 지정
        $sec_params['offset'] = isset($sec_params['offset']) ? $sec_params['offset'] : 0;
        
        if ($this->siteinit->_request_type === 'ajax') {
            $sec_params['limit'] = isset($sec_params['limit']) ? $sec_params['limit'] : $this->siteinit->_admin['limit'];
        } else {
            $sec_params['limit'] = isset($sec_params['limit']) ? $sec_params['limit'] : 1;
        }

        $data['cntall'] = $this->sampleModel->selectCountAll();
        // 커버링 인덱스 쿼리 사용
        // $data_list = $this->sampleModel->coveringindexActiveList($sec_params);
        // $data['cnt'] = $data_list['rows'];
        // $data['list'] = $data_list['list'];
        // 커버링 인덱스 쿼리 미사용
        $data['cnt'] = $this->sampleModel->selectCount($sec_params);
        $data['list'] = $this->sampleModel->selectList($sec_params);

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
        $this->scripts[] = $this->common->scripts(array('sample.js'));
        // 화면 뷰
        if (isset($this->data) && ($this->data != null)) {
            $data['set_params'] = $this->data;
        }
        $this->siteinit->_view_file_path = "sample/design_list.html";
        $this->load->view(null, $data);
        return;
    }
    
    /**
     * 기본(샘플운영자) 등록&수정 폼입니다.(등록/수정 폼을 같이 쓸 경우)
     * @param int $idx 샘플운영자 일련번호
     */
    public function form($idx = null)
    {
        $data = [];

        if (is_null($idx)) { // 등록화면
            $data['crud_mode'] = 'create';
        } else { // 수정화면
            try {
                $data['crud_mode'] = 'update';
                $where[$this->idx_column_nm] = $idx;
                $data['row'] = $this->sampleModel->selectRow($where);
                unset($where);

                if ($data['row']) {
                    // 이메일/전화&팩스번호/휴대폰번호 배열 변환
                    $data['row']['spmg_email'] = explode('@', $data['row']['spmg_email']);
                    $data['row']['spmg_phone'] = explode('-', $data['row']['spmg_phone']);
                    $data['row']['spmg_fax'] = explode('-', $data['row']['spmg_fax']);
                    $data['row']['spmg_cellphone'] = explode('-', $data['row']['spmg_cellphone']);
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
        $this->scripts[] = $this->common->scripts(array('sample.js'));
        // 화면 뷰
        if (isset($this->data) && ($this->data != null)) {
            $data['set_params'] = $this->data;
        }
        $this->siteinit->_view_file_path = "sample/design_form.html";
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
            // 패스워드 생성
            $crud_data['spmg_pw'] = makePasswd($crud_data['spmg_pw']);
        }

        // 이메일
        if (is_array($crud_data['spmg_email_txt'])) {
            $crud_data['spmg_email'] = implode('@', $crud_data['spmg_email_txt']);
        }

        // 전화번호
        if (is_array($crud_data['spmg_phone_txt'])) {
            $crud_data['spmg_phone'] = implode('-', $crud_data['spmg_phone_txt']);
        }

        // 휴대폰번호
        if (is_array($crud_data['spmg_cellphone_txt'])) {
            $crud_data['spmg_cellphone'] = implode('-', $crud_data['spmg_cellphone_txt']);
        }

        // 팩스번호
        if (is_array($crud_data['spmg_fax_txt'])) {
            $crud_data['spmg_fax'] = implode('-', $crud_data['spmg_fax_txt']);
        }

        return $crud_data;
    }    
    
    /**
     * 기본(샘플운영자) 처리단입니다.(등록&수정)
     * @param string $crud_mode 처리타입
     * @param int $idx 해당일련번호
     */
    public function crudProc($crud_mode = null, $idx = null)
    {
        if ($crud_mode === 'create' || $crud_mode === 'update') { // 등록 && 수정
            $this->load->library('form_validation');
            $crud_data = $this->input->post();

            // 폼 값 서버단 검증 처리
            $form_validation_rules = ($crud_mode === 'create') ? 'sample_create' : 'sample_update';
            if ($this->form_validation->run($form_validation_rules)) {
                $crud_data = $this->formData($crud_data);

                try {
                    if ($crud_mode === 'create') { // 등록
                        $spmg_key = $this->sampleModel->insert($crud_data);
                    } elseif ($crud_mode === 'update') { // 수정
                        if (isset($crud_data['passwd_chg'])) { // 비밀번호 변경 체크인 경우
                            if (!empty($crud_data['spmg_pw']) && !empty($crud_data['spmg_re_pw'])) { // 비밀번호&비밀번호확인 값이 있는 경우
                                if ($crud_data['spmg_pw'] === $crud_data['spmg_re_pw']) { // 비밀번호&비밀번호확인 값이 동일한 경우
                                    if ($this->siteinit->_admin_info['acmg_pw'] === makePasswd($crud_data['admin_password'])) {
                                        // 패스워드
                                        $crud_data['spmg_pw'] = makePasswd($crud_data['spmg_pw']);
                                    } else {
                                        $this->js->pageBack($this->lang->line('cm_sample_passwd_admin_error'));
                                        return false;
                                    }
                                } else {
                                    $this->js->pageBack($this->lang->line('cm_sample_passwd_matches_error'));
                                    return false;
                                }
                            } else {
                                $this->js->pageBack($this->lang->line('cm_sample_passwd_valid_error'));
                                return false;
                            }
                        }

                        $update_where[$this->idx_column_nm] = $idx;
                        $spmg_key = $this->sampleModel->update($crud_data, $update_where);
                        unset($update_where);
                    }

                    if ($spmg_key) {
                        if ($crud_mode === 'create') { // 등록
                            ajaxExcep("cm_sample_{$crud_mode}_success", site_url(ADMINURL . '/sample/index'));
                            // $this->js->pageRedirect(site_url(ADMINURL . '/sample/index'), $this->lang->line("cm_sample_{$crud_mode}_success"));
                            // return false;
                        } else { // 수정
                            ajaxExcep("cm_sample_{$crud_mode}_success", $this->input->post('referer'));
                            // $this->js->pageRedirect($this->input->post('referer'), $this->lang->line("cm_sample_{$crud_mode}_success"));
                            // return false;
                        }
                    } else {
                        throw new Exception($this->lang->line("cm_sample_{$crud_mode}_error"));
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
            $update_res = $this->sampleModel->update($update_data, $update_where);
            unset($update_data);
            unset($update_where);

            ajaxExcep('cm_sample_update_success', null, true);
            // json 타입 리턴일 경우 사용
            // $json['result'] = true;
            // $json['msg'] = $this->lang->line('cm_sample_update_success');
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
                    $this->sampleModel->updateBatch($update_data);

                    ajaxExcep('cm_sample_delete_success', null, true);
                    // json 타입 리턴일 경우 사용
                    // $json['result'] = true;
                    // $json['msg'] = $this->lang->line('cm_sample_delete_success');
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
                    $data['row'] = $this->sampleModel->selectRow($where);
                    unset($where);

                    if ($data['row']) {
                        $where[$this->idx_column_nm] = $data['row'][$this->idx_column_nm];
                        $this->sampleModel->delete($where);
                        unset($where);

                        ajaxExcep("cm_sample_{$crud_mode}_success", $this->input->server('HTTP_REFERER'));
                        // $this->js->pageRedirect($this->input->server('HTTP_REFERER'), $this->lang->line("cm_sample_{$crud_mode}_success"));
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
                ajaxExcep("cm_sample_{$crud_mode}_error", site_url(ADMINURL . '/sample/index'));
                // $this->js->pageRedirect(site_url(ADMINURL . '/sample/index'), $this->lang->line("cm_sample_{$crud_mode}_error"));
                // return false;
            }            
        } else {
            show_error('Access is limited on this page.');
        }
        return;
    }    
}