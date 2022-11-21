<?php defined("BASEPATH") or exit("No direct script access allowed");

class Auth extends CI_Controller
{
    /**
     * 생성자
     * @param type void
     * @return return void
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * 관리자 인증 채크 처리
     * @param type void
     * @return return void
     */
    public function router()
    {
        // TODO: 로그인 여부 체크 후 로딩 페이지 분리 처리(재확인 예정)
        if ($this->siteinit->_is_admin) { //관리자인 경우
            redirect(ADMINURL . '/main/index');
        } else {
            redirect(ADMINURL . '/auth/sign_in');
        }
        return;
    }

    /**
     * 관리자 인증 로그인 뷰
     * @param type void
     * @return return void
     */
    public function sign_in()
    {
        // 프로파일러 컨트롤에서 개별 활성할 경우 사용
        // $this->output->enable_profiler(TRUE);
        $data = [];

        //  관리자 계정 쿠키 가져옴
        if ($this->input->cookie($this->config->item('cookie_prefix') . 'saveid')) {
            $data['save_id'] = $this->input->cookie($this->config->item('cookie_prefix') . 'saveid');
        } else {
            $data['save_id'] = null;
        }

        // 추가 스크립트 선언
        $this->scripts[] = $this->common->scripts(array('signin.js'));
        // 화면 뷰
        $this->load->view(null, $data);
        return;
    }

    /**
     * 관리자 인증 모드별 처리
     * @param string $mode 처리모드
     * @return return void
     */
    public function proc($mode)
    {
        if ($mode === 'sign_in') { // 관리자 인증 로그인 처리
            $this->load->helper('cookie');
            $this->load->library('form_validation');

            if ($this->form_validation->run('admin_sign_in')) {
                $this->load->model('managerModel');

                $where = [];
                $where['acmg_id'] = $this->input->post('acmg_id');
                $where['sec_default'] = true;
                $manager_list = $this->managerModel->selectList($where);
                unset($where);

                if ($manager_list) { // 리스트 정보가 있는 경우
                    $manager_row = $manager_list[0];

                    // if ($manager_row['acmg_idx']) { // 일련번호 정보가 있는 경우
                    if ($manager_row['account']) { // 일련번호 정보가 있는 경우
                        // if ($manager_row['acmg_pw'] == makePasswd($this->input->post('acmg_pw'))) {
                        if ($manager_row['password'] == makePasswd($this->input->post('acmg_pw'))) {
                            // 관리자 쿠키 설정
                            $save_id = $this->input->post('save_id');
                            if (isset($save_id) && $save_id === 'on') {
                                $cookie = [
                                    'name'   => 'saveid',
                                    // 'value'  => $manager_row['acmg_id'],
                                    'value'  => $manager_row['account'],
                                    'expire' => '86500',
                                    'path'   => '/',
                                    'prefix' => $this->config->item('cookie_prefix'),
                                    // 'secure' => TRUE
                                ];
                                /**
                                 * @todo 참조내용
                                 * set_cookie()의 별칭 $this->input->set_cookie(), get_cookie()의 별칭 $this->input->cookie()
                                 * 단 set_cookie(), get_cookie()를 사용할 경우 $this->load->helper('cookie')를 선언해야함
                                 * ex) $this->input->set_cookie($cookie); === set_cookie($cookie);
                                 */
                                set_cookie($cookie);
                                // TODO: 외부 페이지에 쿠키를 전송할 범위를 선언 할때 사용
                                // setcookie($this->config->item('cookie_prefix') . 'saveid', $manager_row['acmg_id'], ['samesite' => 'Lax']);
                                // setcookie($this->config->item('cookie_prefix') . 'saveid', $manager_row['acmg_id'], ['samesite' => 'None', 'secure' => true]);
                            } else {
                                delete_cookie('saveid', '', '/', $this->config->item('cookie_prefix'));
                            }

                            // 관리자 세션 설정
                            $admin_session = [
                                // $this->siteinit->_admin['session'] => $manager_row['acmg_idx'],
                                // 'sess_acmg_id' => $manager_row['acmg_id'],
                                // 'sess_acmg_name' => $manager_row['acmg_name'],
                                // 'sess_acmg_nick' => $manager_row['acmg_nick']
                                $this->siteinit->_admin['session'] => $manager_row['id'],
                                'sess_acmg_id' => $manager_row['account'],
                                'sess_acmg_name' => $manager_row['admin_name']
                            ];
                            $this->session->set_userdata($admin_session);

                            redirect(ADMINURL . '/main/index');
                        } else {
                            $this->js->pageBack($this->lang->line('cm_admin_sign_in_fail'));
                        }
                    } else { // 일련번호 정보가 없는 경우
                        $this->js->pageBack($this->lang->line('cm_admin_sign_in_fail'));
                    }
                } else { // 리스트 정보가 없는 경우
                    $this->js->pageBack($this->lang->line('cm_admin_sign_in_fail'));
                }
            } else {
                // echo validation_errors();
                $this->js->pageBack(str_replace("\n", "\\n", strip_tags(validation_errors())));
            }
        } elseif ($mode === 'sign_out') {
            $unset_session = [$this->siteinit->_admin['session'], 'sess_acmg_id', 'sess_acmg_name', 'sess_acmg_nick'];
            $this->session->unset_userdata($unset_session);
            redirect(ADMINURL . '/auth/sign_in');
        } else {
            show_error('Access is limited on this page.');
        }
    }
}
