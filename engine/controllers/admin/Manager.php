<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 일괄변경 항목
 * 1) [관리자계정] -> [변경텍스트명]
 * 2) [manager] -> [해당로직명] -> class명 첫글자 대문자로 변경
 * 3) [acmg_] -> [driving 테이블의 AS선언명]
 */
class Manager extends CI_Controller
{
    private $idx_column_nm = 'id';            // 관리자계정 테이블 일련번호
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

        // 관리자계정 모델 호출
        $this->load->model('managerModel');
        // 업체부서 모델 호출
        $this->load->model('businessTeamModel');
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
        // 관리자계정 공통 변수 선언
        $this->data = [
            'business_team_list' => $this->businessTeamModel->selectList($where), // 업체부서 리스트
            // 'acmg_permit_cd' => $this->meta->get('acmg_permit_cd', 10), // 관리자계정 접근유형
            // 'acmg_status_cd' => $this->meta->get('acmg_status_cd', 10), // 관리자계정 접근승인여부
        ];
        // debug_var($this->data);

        // 관리자계정 상세 변수 선언
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
        $data['page_title'] = '관리자계정';

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
        $sec_params['sec_date_type'] = ifEmpty($sec_params, 'sec_date_type');
        $sec_params['sec_st_date'] = ifEmpty($sec_params, 'sec_st_date');
        $sec_params['sec_ed_date'] = ifEmpty($sec_params, 'sec_ed_date');
        $sec_params['sec_super_status_cd'] = ifEmpty($sec_params, 'sec_super_status_cd');   //대표관리자 여부
        $sec_params['sec_use_status_cd'] = ifEmpty($sec_params, 'sec_use_status_cd');   //접속 허용 여부
        $sec_params['sec_column'] = ifEmpty($sec_params, 'sec_column');
        $sec_params['sec_keyword'] = ifEmpty($sec_params, 'sec_keyword');
        $sec_params['sec_acmg_status_cd'] = ifEmpty($sec_params, 'sec_acmg_status_cd');

        // 기본 검색 조건 지정
        $sec_params['sec_default'] = true;
        // $sec_params['sec_acmg_status_cd'] = 30; // 차단된 운영자만 가져올 경우

        // 정렬 방법 지정
        if (!isset($sec_params['od_column']) && !isset($sec_params['od_type'])) {
            $sec_params['od_type'] = 'DESC';
            // $sec_params['od_column'] = 'acmg.comm_reg_date';
            $sec_params['od_column'] = 'ACMG.c_datetime';
        }

        // 오프셋, 리밋 지정
        $sec_params['offset'] = isset($sec_params['offset']) ? $sec_params['offset'] : 0;

        if ($this->siteinit->_request_type === 'ajax') {
            $sec_params['limit'] = isset($sec_params['limit']) ? $sec_params['limit'] : $this->siteinit->_admin['limit'];
        } else {
            $sec_params['limit'] = isset($sec_params['limit']) ? $sec_params['limit'] : $this->siteinit->_admin['limit'];
        }

        $data['cntall'] = $this->managerModel->selectCountAll();
        // 커버링 인덱스 쿼리 사용
        // $data_list = $this->managerModel->coveringindexActiveList($sec_params);
        // $data['cnt'] = $data_list['rows'];
        // $data['list'] = $data_list['list'];
        // 커버링 인덱스 쿼리 미사용
        $data['cnt'] = $this->managerModel->selectCount($sec_params);
        $data['list'] = $this->managerModel->selectList($sec_params);

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
        $this->scripts[] = $this->common->scripts(array('manager.js'));
        // 화면 뷰
        if (isset($this->data) && ($this->data != null)) {
            $data['set_params'] = $this->data;
        }
        $this->load->view(null, $data);
        return;
    }

    /**
     * 기본(관리자계정) 등록&수정 폼입니다.(등록/수정 폼을 같이 쓸 경우)
     * @param int $idx 관리자계정 일련번호
     */
    public function form($idx = null)
    {
        $data = [];

        if (is_null($idx)) { // 등록화면
            $data['crud_mode'] = 'create';
            // row 초기 값 선언
            $data['row'] = $this->managerModel->listFields();
        } else { // 수정화면
            try {
                $data['crud_mode'] = 'update';
                $where[$this->idx_column_nm] = $idx;
                $data['row'] = $this->managerModel->selectRow($where);
                unset($where);

                if ($data['row']) {
                    // 이메일/전화&팩스번호/휴대폰번호 배열 변환(나중에 리뉴얼 할 때 사용)
                    // $data['row']['acmg_email'] = explode('@', $data['row']['acmg_email']);
                    // $data['row']['acmg_phone'] = explode('-', $data['row']['acmg_phone']);
                    // $data['row']['acmg_fax'] = explode('-', $data['row']['acmg_fax']);
                    // $data['row']['acmg_cellphone'] = explode('-', $data['row']['acmg_cellphone']);
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
        $this->scripts[] = $this->common->scripts(array('manager.js'));
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
        $this->load->library('uniquecodegenerator');

        if ($this->uri->rsegment(3) === 'create') {
            // 패스워드 생성
            $crud_data['password'] = makePasswd($crud_data['password']);
            // 관리자 고유번호 생성
            $crud_data['admin_code'] = $this->uniquecodegenerator->getCodeOfAdminAccount();
        }

        // 이메일
        // if (is_array($crud_data['acmg_email_txt'])) {
        //     $crud_data['acmg_email'] = implode('@', $crud_data['acmg_email_txt']);
        // }

        // 전화번호
        // if (is_array($crud_data['acmg_phone_txt'])) {
        //     $crud_data['acmg_phone'] = implode('-', $crud_data['acmg_phone_txt']);
        // }

        // 휴대폰번호
        // if (is_array($crud_data['acmg_cellphone_txt'])) {
        //     $crud_data['acmg_cellphone'] = implode('-', $crud_data['acmg_cellphone_txt']);
        // }

        // 팩스번호
        // if (is_array($crud_data['acmg_fax_txt'])) {
        //     $crud_data['acmg_fax'] = implode('-', $crud_data['acmg_fax_txt']);
        // }

        return $crud_data;
    }

    /**
     * 기본(관리자계정) 처리단입니다.(등록&수정)
     * @param string $crud_mode 처리타입
     * @param int $idx 해당일련번호
     */
    public function crudProc($crud_mode = null, $idx = null)
    {
        if ($crud_mode === 'create' || $crud_mode === 'update') { // 등록 && 수정
            $this->load->library('form_validation');
            $crud_data = $this->input->post();

            // 폼 값 서버단 검증 처리
            $form_validation_rules = ($crud_mode === 'create') ? 'manager_create' : 'manager_update';
            if ($this->form_validation->run($form_validation_rules)) {
                $crud_data = $this->formData($crud_data);

                try {
                    if ($crud_mode === 'create') { // 등록
                        $acmg_key = $this->managerModel->insert($crud_data);
                    } elseif ($crud_mode === 'update') { // 수정
                        if (isset($crud_data['passwd_chg'])) { // 비밀번호 변경 체크인 경우
                            if (!empty($crud_data['acmg_pw']) && !empty($crud_data['acmg_re_pw'])) { // 비밀번호&비밀번호확인 값이 있는 경우
                                if ($crud_data['acmg_pw'] === $crud_data['acmg_re_pw']) { // 비밀번호&비밀번호확인 값이 동일한 경우
                                    if ($this->siteinit->_admin_info['acmg_pw'] === makePasswd($crud_data['admin_password'])) {
                                        // 패스워드
                                        $crud_data['acmg_pw'] = makePasswd($crud_data['acmg_pw']);
                                    } else {
                                        $this->js->pageBack($this->lang->line('cm_manager_passwd_admin_error'));
                                        return false;
                                    }
                                } else {
                                    $this->js->pageBack($this->lang->line('cm_manager_passwd_matches_error'));
                                    return false;
                                }
                            } else {
                                $this->js->pageBack($this->lang->line('cm_manager_passwd_valid_error'));
                                return false;
                            }
                        }

                        $update_where[$this->idx_column_nm] = $idx;
                        $acmg_key = $this->managerModel->update($crud_data, $update_where);
                        unset($update_where);
                    }

                    if ($acmg_key) {
                        if ($crud_mode === 'create') { // 등록
                            ajaxExcep("cm_manager_{$crud_mode}_success", site_url(ADMINURL . '/manager/index'));
                            // $this->js->pageRedirect(site_url(ADMINURL . '/manager/index'), $this->lang->line("cm_manager_{$crud_mode}_success"));
                            // return false;
                        } else { // 수정
                            ajaxExcep("cm_manager_{$crud_mode}_success", $this->input->post('referer'));
                            // $this->js->pageRedirect($this->input->post('referer'), $this->lang->line("cm_manager_{$crud_mode}_success"));
                            // return false;
                        }
                    } else {
                        throw new Exception($this->lang->line("cm_manager_{$crud_mode}_error"));
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
            $update_res = $this->managerModel->update($update_data, $update_where);
            unset($update_data);
            unset($update_where);

            ajaxExcep('cm_manager_update_success', null, true);
            // json 타입 리턴일 경우 사용
            // $json['result'] = true;
            // $json['msg'] = $this->lang->line('cm_manager_update_success');
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
                    $this->managerModel->updateBatch($update_data);

                    ajaxExcep('cm_manager_delete_success', null, true);
                    // json 타입 리턴일 경우 사용
                    // $json['result'] = true;
                    // $json['msg'] = $this->lang->line('cm_manager_delete_success');
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
                    $data['row'] = $this->managerModel->selectRow($where);
                    unset($where);

                    if ($data['row']) {
                        $where[$this->idx_column_nm] = $data['row'][$this->idx_column_nm];
                        $this->managerModel->delete($where);
                        unset($where);

                        ajaxExcep("cm_manager_{$crud_mode}_success", $this->input->server('HTTP_REFERER'));
                        // $this->js->pageRedirect($this->input->server('HTTP_REFERER'), $this->lang->line("cm_manager_{$crud_mode}_success"));
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
                ajaxExcep("cm_manager_{$crud_mode}_error", site_url(ADMINURL . '/manager/index'));
                // $this->js->pageRedirect(site_url(ADMINURL . '/manager/index'), $this->lang->line("cm_manager_{$crud_mode}_error"));
                // return false;
            }
        } else {
            show_error('Access is limited on this page.');
        }
        return;
    }
}
