<?php
class MY_Loader extends CI_Loader
{
    protected $CI;

    public function __construct()
    {
        log_message('info', 'Output Class MY_Loader display 1 Initialized');
        $this->CI = &get_instance();
    }

    /**
     * CI 기본 뷰 함수를 오버라이딩 합니다.
     * 템플릿 언더바를 사용할 시 템플릿 언더바 출력을 리턴하고, 사용하지 않을 시 CI 기본 뷰를 리턴합니다.
     * @inheritDoc
     * @return void
     */
    public function view($view, $vars = array(), $return = FALSE)
    {
        log_message('info', 'Output Class MY_Loader display 2 start');

        global $OUT;

        // 출력단 공통 변수 선언
        $this->setCommonVars();

        // 서비스 타입별 공통 변수 선언
        if ($this->CI->siteinit->_service_mode === 'admin') {
            $this->setAdminVars();
        } elseif ($this->CI->siteinit->_service_mode === 'client' || $this->CI->siteinit->_service_mode === 'mobile_client') {
            $isadmin = $this->CI->siteinit->_is_admin;
            $this->setClientVars($isadmin);
        }

        // 디버깅 모드 설정
        if ($this->CI->siteinit->_debug === true) {
            $OUT->enable_profiler = true;
        }

        // 템플릿 언더바를 사용하는 경우 출력단입니다.
        if ($this->CI->siteinit->_is_template_ === true) {
            log_message('info', 'Output Class MY_Loader display 3 template_ start');

            // 템플릿 언더바 템플릿 인클루드 선언
            $this->setModules();

            // 컨트롤에서 추가된 변수 선언
            $this->vars($vars);

            // 템플릿 언더바 출력
            return $this->printTemplate_();
        } else {
            // 사용하지 않는경우의 출력단입니다.
            return parent::_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_prepare_view_vars($vars), '_ci_return' => $return));
        }
        return;

        log_message('info', 'Output Class MY_Loader display 2 end');
    }

    /**
     * 디렉토리 명으로 템플릿 인클루드를 정의합니다.
     * 템플릿 인클루드는 _modules 폴더 밑에 있으며 템플릿에 사용시 #폴더명_파일명 으로 사용이 가능합니다.
     * @return void
     */
    private function setModules()
    {
        $module_folder = '_modules';
        // 클라이언트일땐 다른 폴더가 들어옵니다.
        if ($this->CI->siteinit->_service_mode === 'client' || $this->CI->siteinit->_service_mode === 'mobile_client') {
            $module_folder = $this->CI->siteinit->view_config['module_path'];
        }
        $module_path = $this->CI->siteinit->_view_dir_path . DIRECTORY_SEPARATOR . $module_folder;

        if (is_dir($module_path)) {
            // 디렉토리를 가져와서 배열형태로 만듭니다.
            $dir_map = directory_map($module_path);
            if (is_array($dir_map)) {
                foreach ($dir_map as $dir => $dir_rows) {
                    if (is_array($dir_rows)) {
                        foreach ($dir_rows as $module_path) {
                            $html_path = '_modules' . DIRECTORY_SEPARATOR . $dir . $module_path;
                            $vowels = array('\\', '/');
                            $module_name = str_replace($vowels, '', $dir) . '_' . str_replace(array('.html', '.tpl', '.htm'), '', $module_path);
                            $this->modules[$module_name] = $html_path;
                        }
                    }
                }
            }
        }
        return;
    }

    /**
     * 출력단 공통 변수를 선언합니다.
     * @return void
     */
    private function setCommonVars()
    {
        if (isset($this->CI->scripts)) {
            if (is_array($this->CI->scripts)) { // 배열일경우 중복된것이 오지 않도록 합니다.
                if (count($this->CI->scripts) > 1) {
                    $scripts_merge = array_merge($this->CI->scripts[0], $this->CI->scripts[1]);
                    $this->vars['scripts'] = array_unique($scripts_merge);
                } else {
                    $this->vars['scripts'] = $this->CI->scripts[0];
                }
            } else {
                $this->vars['scripts'] = $this->CI->scripts[0];
            }
        }

        // 주소 배열 합체
        $rseqments = array();
        $rmenu_string = '';
        foreach ($this->CI->uri->rsegment_array() as $seqment) {
            $rseqments[] = urldecode($seqment);
            $rmenu_string .= urldecode($seqment) . '/';
        }

        $seqments = array();
        foreach ($this->CI->uri->segment_array() as $seqment) {
            $seqments[] = urldecode($seqment);
        }
        $this->vars['rseqments'] = $rseqments;
        $this->vars['seqments'] = $seqments;
        $this->vars['rmenu_string'] = rtrim($rmenu_string, '/'); // 관리자 메뉴 네비용 주소 변수
        $this->vars['rurl_string'] = $rseqments[0] . '/' . $rseqments[1];
        $this->vars['frm_name_string'] = $rseqments[0] . '_' . $rseqments[1] . '_frm';
        // 관리자 주소
        $this->vars['admin_url'] = ADMINURL;
        $this->vars['url_suffix'] = $this->CI->config->item('url_suffix');
        $this->vars['host'] = $this->CI->config->item('base_url');
        $this->vars['referer'] = $this->CI->input->server('HTTP_REFERER');
        $this->vars['admin_vars'] = $this->CI->siteinit->_admin;
        $this->vars['client_vars'] = $this->CI->siteinit->_client;
        if ($this->CI->siteinit->_debug === true && isset($this->CI->siteinit->view_config)) {
            $this->vars['view_config'] = $this->CI->siteinit->view_config;
        }
        $this->vars['sess_id'] = $this->CI->session->session_id;
        $this->vars['isdebug'] = $this->CI->siteinit->_debug;
    }

    /**
     * 관리자 메뉴를 정의한다.(권한없음)
     */
    private function setAdminMenu()
    {
        $this->CI->config->load('admin/admin_menu');
        $conf_menu = $this->CI->config->item('admin_menu');

        foreach ($conf_menu as $first_key => $first_val) {
            //1차 메뉴에 따른 클래스 지정
            $conf_menu[$first_key]['class'] = '';
            if ($first_val['link'] === $this->vars['rmenu_string']) {
                if (!empty($first_val['child'])) { //2차 메뉴가 있다면
                    $conf_menu[$first_key]['class'] = 'active pcoded-trigger';
                } else { //2차 메뉴가 없다면
                    $conf_menu[$first_key]['class'] = 'active';
                }
            }

            $conf_menu[$first_key]['view_page_title'] = $first_val['page_title'];
            $conf_menu[$first_key]['view_page_desc'] = $first_val['page_desc'];

            //2차 메뉴 체크 시작
            if (!empty($first_val['child'])) {
                foreach ($first_val['child'] as $child_key => $child_val) {
                    //2차 메뉴에 따른 클래스 지정
                    $conf_menu[$first_key]['child'][$child_key]['class'] = '';
                    //if ($child_val['link'] === $this->vars['rmenu_string']) {
                    if (is_int(strpos($this->vars['rmenu_string'], $child_val['link'])) || ($this->vars['rseqments'][1] === 'mergeview' && is_int(strpos($this->vars['referer'], $child_val['link'])))) {
                        $conf_menu[$first_key]['class'] = 'active pcoded-trigger';
                        $conf_menu[$first_key]['child'][$child_key]['class'] = 'active';

                        $conf_menu[$first_key]['view_page_title'] = $child_val['page_title'];
                        $conf_menu[$first_key]['view_page_desc'] = $child_val['page_desc'];
                    }

                    if (!empty($child_val['child'])) {
                        foreach ($child_val['child'] as $grandchild_key => $grandchild_val) {
                            //3차 메뉴에 따른 클래스 지정
                            $conf_menu[$first_key]['child'][$child_key]['child'][$grandchild_key]['class'] = '';
                            if ($grandchild_val['link'] === $this->vars['rmenu_string'] || ($this->vars['rseqments'][1] === 'mergeview' && is_int(strpos($this->vars['referer'], $grandchild_val['link'])))) {
                                $conf_menu[$first_key]['class'] = 'active pcoded-trigger';
                                $conf_menu[$first_key]['child'][$child_key]['class'] = 'active pcoded-trigger';
                                $conf_menu[$first_key]['child'][$child_key]['child'][$grandchild_key]['class'] = 'active';

                                $conf_menu[$first_key]['view_page_title'] = $grandchild_val['page_title'];
                                $conf_menu[$first_key]['view_page_desc'] = $grandchild_val['page_desc'];
                            }
                        }
                    } else {
                        // 3차 메뉴에 값이 없는 경우 초기화 선언
                        $conf_menu[$first_key]['child'][$child_key]['child'] = array();
                    }
                }
            }
        }

        return $conf_menu;
    }

    /**
     * 관리자 출력단 공통 변수를 선언합니다.
     * @return void
     */
    private function setAdminVars()
    {
        // 관리자 여부
        $this->vars['isadmin'] = $this->CI->siteinit->_is_admin;
        // 관리자일 경우 관리자 메뉴를 불러옵니다.
        if ($this->CI->siteinit->_is_admin) {
            // 기본 메뉴 처리(권한X)
            $this->vars['admin_menu'] = $this->setAdminMenu();
        }
        return;
    }

    /**
     * 클라이언트 서비스 타입별 출력단 공통 변수를 선언합니다.
     * @return void
     */
    private function setClientVars($isadmin)
    {
        if ($isadmin) {
            $this->vars['isadmin'] = $isadmin;
        }
        // 회원정보
        $this->vars['member'] = $this->CI->siteinit->_member_info;
        // 로그인 여부
        $this->vars['ismember'] = $this->CI->siteinit->_is_member;
        return;
    }

    /**
     * 출력단 Template_ 기본 모듈을 정의합니다.
     * 모듈은 _modules 폴더 밑에 있으며 템플릿에 사용시 #폴더명_파일명 으로 사용이 가능합니다.
     * $this->load->view의 첫 번째 인자가 NULL이어도 자동으로 define 동작 합니다.
     * @return void
     */
    public function printTemplate_()
    {
        if (isset($this->modules)) {
            $this->CI->siteinit->template_->define($this->modules);
        }
        if (isset($_GET['lang'])) {
            $site_lang = $_GET['lang'];
        }
        if (!empty($site_lang)) {
            $this->CI->siteinit->template_->define('tpl', $site_lang . DIRECTORY_SEPARATOR . $this->CI->siteinit->_view_file_path);
        } else {
            $this->CI->siteinit->template_->define('tpl', $this->CI->siteinit->_view_file_path);
        }
        $this->CI->siteinit->template_->assign($this->vars);
        $this->CI->siteinit->template_->assign($this->CI->load->get_vars());
        if ($this->CI->siteinit->_debug === TRUE) {
            debug_var($this->vars);
            debug_var($this->CI->load->get_vars());
            debug_var($this->CI->siteinit->template_);
        }
        // 기존: print_방식
        // $this->CI->siteinit->template_->print_('tpl');
        // 변경: fetch 방식
        $fetch = $this->CI->siteinit->template_->fetch('tpl');
        $this->CI->output->append_output($fetch);
        return $this;
    }
}
