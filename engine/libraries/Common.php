<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @property CI_Controller $CI CI 오리지날
 */
class Common
{
    public $CI = null;
    public function __construct()
    {
        log_message('info', 'Libraries Common Class 1 Initialized start');
        $this->CI = &get_instance();

        $this->setIsAdmin();

        if ($this->CI->siteinit->_service_mode === 'admin') { // 관리자단 처리
            $this->adminAuthCheck();
            $this->setAdmin();
            $this->setAdminCommonScripts();
        } elseif ($this->CI->siteinit->_service_mode === 'client' || $this->CI->siteinit->_service_mode === 'mobile_client') { // 사용자단 처리
        }
        return;
    }

    private function setIsAdmin()
    {
        if (isset($this->CI->session)) {
            if ($this->CI->session->userdata($this->CI->siteinit->_admin['session'])) {
                $this->CI->siteinit->_is_admin = true;
            }
        }
        return;
    }

    private function adminAuthCheck()
    {
        if ($this->CI->siteinit->_service_mode === 'admin' && $this->CI->siteinit->_is_admin === false) {
            // 로그인 페이지가 아니라면 모조리 로그인 페이지로 튕깁니다.
            if ($this->CI->uri->rsegment(1) !== 'auth') {
                redirect(ADMINURL . '/auth/sign_in');
            }
        }
    }

    private function setAdmin()
    {
        if ($this->CI->siteinit->_is_admin) {
            // 관리자 기본 정보를 가져온다.
            $this->CI->load->model('managerModel');
            // $where['acmg_idx'] = $this->CI->session->userdata($this->CI->siteinit->_admin['session']);
            $where['id'] = $this->CI->session->userdata($this->CI->siteinit->_admin['session']);
            // 컨트롤단에서 사용
            $this->CI->siteinit->_admin_info = $this->CI->managerModel->selectRow($where);
            // html단에서 사용
            $this->CI->load->vars('adminInfo', $this->CI->siteinit->_admin_info);
            unset($where);
        }
        return;
    }

    /*
     * 공통 JS를 뷰화면에서 호출하는 경우는 2가지 입니다.
     * 1. 뷰모드에 따른 config.php파일에 선언된 스크립트가 있고,
     * 2. 해당 컨트롤단에서
     * 단일: $this->scripts[] = $this->common->scripts('sample.js'); or
     * 여러개: $this->scripts[] = $this->common->scripts(array('sample.js','sample2.js')); 있음
     */
    private function setAdminCommonScripts()
    {
        if (isset($this->CI->siteinit->view_config['scripts']) && $this->CI->siteinit->_disable_commonjs === FALSE) {
            $this->CI->scripts[] = $this->CI->siteinit->view_config['scripts'];
        } else {
            $this->CI->scripts[] = $this->scripts('common.ini.js');
        }
        return;
    }

    public function scripts($param = NULL)
    {
        $scripts = $this->paramToArray($param);
        return $scripts;
    }

    private function paramToArray($param)
    {
        $result = [];
        if (is_array($param)) {
            $result = $param;
        } elseif ($param) {
            $result[] = $param;
        }
        return $result;
    }
}
