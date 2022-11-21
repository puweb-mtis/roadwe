<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CI_Siteinit
{
    /**
     * 프로파일링 활성/비활성합니다.
     * @var bool
     */
    public $_debug = false;

    /**
     * 서비스 모드 입니다.
     * 사용자 : 'client', 관리자 : 'admin'
     * @var string
     */
    public $_service_mode = 'client';

    /**
     * 관리자 여부를 체크합니다.
     * @var bool
     */
    public $_is_admin = false;

    /**
     * 관리자 정보 입니다.
     * @var array
     */
    public $_admin_info = [];

    /**
     * 회원 여부를 체크합니다.
     * @var bool
     */
    public $_is_member = false;

    /**
     * 회원 정보 입니다.
     * @var array
     */
    public $_member_info = [];

    /**
     * 템플릿 언더바 객체
     * @var object
     */
    public $template_ = null;

    /**
     * 템플릿 언더바 사용여부
     * @var bool
     */
    public $_is_template_ = true;

    /**
     * 클라이언트단 템플릿 설정을 담아둡니다.
     * @var array
     */
    public $_template_config = [];

    /**
     * 요청의 ajax 여부를 설정
     * @var string
     */
    public $_request_type = 'http';

    /**
     * 뷰 파일의 기본적인 경로입니다.
     * 관리자: builds/admin 으로 설정
     * 사용자: builds/서비스별 명으로 설정
     * @var string
     */
    public $_view_dir_path = NULL;

    /**
     * 뷰 파일의 경로입니다.
     * @var string
     */
    public $_view_file_path = NULL;

    /**
     * 공통 스크립트 비활성화 여부를 선언합니다.
     * @var bool
     */
    public $_disable_commonjs = false;

    /**
     * 시스템 글로벌 변수 설정
     * 'title' => 시스템 타이틀
     *
     * @var array
     */
    public $_default = [
        'title' => '시스템 타이틀(기본)',
        'keyword' => '시스템 키워드(기본)',
        'description' => '시스템 설명(기본)',
        'limit' => 20,
    ];

    /**
     * 관리자단 글로벌 변수 설정
     * 'session' => 'sess_acmg_idx' // 관리자 세션키
     *
     * @var array
     */
    public $_admin = [
        'session' => 'sess_acmg_idx',
    ];

    /**
     * 클라이언트단 글로벌 변수 설정
     * @var array
     */
    public $_client = [];

    public function __construct()
    {
        log_message('info', 'CI_Siteinit Class 1 Initialized');
    }

    public function configure()
    {
        log_message('info', 'CI_Siteinit Class configure() loaded 2 start');

        $this->addDefine();

        log_message('info', 'CI_Siteinit Class configure() loaded 2 end');
        return;
    }


    /**
     * 코드이그나이터에서 사용되는 이외의 상수를 추가
     * @return void
     */
    private function addDefine()
    {
        // 관리자 페이지 주소
        define('ADMINURL', 'admin');

        // 템플릿 언더바 폴더 경로 지정
        $tpleng_folder = 'template_';

        if (is_dir(APPPATH . $tpleng_folder . DIRECTORY_SEPARATOR)) {
            $view_folder = APPPATH . strtr(
                trim($tpleng_folder, '/\\'),
                '/\\',
                DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
            );
        } else {
            header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
            echo 'Template_ Engine is not defined .' . SELF;
            exit(3); // EXIT_CONFIG
        }

        define('TPLENGINEPATH', APPPATH . $tpleng_folder . DIRECTORY_SEPARATOR);

        // 리소스 폴더 추가
        $etc_folder = 'etc';
        if (is_dir($etc_folder)) {
            if (($_temp = realpath($etc_folder)) !== FALSE) {
                $etc_folder = $_temp;
            } else {
                $etc_folder = strtr(
                    rtrim($etc_folder, '/\\'),
                    '/\\',
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
                );
            }
        } elseif (is_dir(BASEPATH . $etc_folder . DIRECTORY_SEPARATOR)) {
            $etc_folder = BASEPATH . strtr(
                trim($etc_folder, '/\\'),
                '/\\',
                DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
            );
        } else {
            header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
            echo 'Your etc folder path does not appear to be set correctly. Please open the following file and correct this: ' . SELF;
            exit(3); // EXIT_CONFIG
        }

        define('ETCPATH', $etc_folder . DIRECTORY_SEPARATOR);

        // 파입 업로드 폴더 추가
        define('UPLOADPATH', $etc_folder . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);

        log_message('info', 'CI_Siteinit Class configure loaded 2 and config debug setting');
        return;
    }

    public function initialize()
    {
        log_message('info', 'CI_Siteinit Class initialize() loaded 3 start');

        $this->setRequestType();
        $this->serviceMode();
        $this->addConfig();
        $this->{'set' . $this->_service_mode}();

        // TODO: 템플릿 언더바를 사용 안하는 경우 확인 해야함
        if ($this->_is_template_) {
            $this->initTemplate_();
        }

        log_message('info', 'CI_Siteinit Class initialize() loaded 3 end');
        return;
    }

    /**
     * 요청의 ajax 여부를 구분
     * @var string
     */
    private function setRequestType()
    {
        global $IN;
        $this->_request_type = 'http';
        if ($IN->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') {
            $this->_request_type = 'ajax';
        }
        return;
    }

    /**
     * 현재 접속한 서비스 타입 구분
     * @return void
     */
    private function serviceMode()
    {
        global $URI;
        $this->_service_mode = (preg_match('/\b' . ADMINURL . '\b/', $URI->uri_string())) ? 'admin' : 'client';

        /*if ($this->_service_mode === 'client' && $this->isMobileChk()) {
            $this->_service_mode = 'mobile_client';
        }*/
        return;
    }

    private function addConfig()
    {
        global $CFG;
        $file = $this->_service_mode . DIRECTORY_SEPARATOR . 'config.php';
        $CFG->load($file);
        return;
    }

    /**
     * 모바일 여부 체크
     * @return bool
     */
    protected function isMobileChk()
    {
        if (file_exists(APPPATH . 'config/user_agents.php')) {
            include(APPPATH . 'config/user_agents.php');
            global $IN;
            $agent = trim($IN->server('HTTP_USER_AGENT'));
            foreach ($mobiles as $key => $val) {
                if (false !== (stripos($agent, $key))) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 관리자 기본 변수 값 선언
     * @return void
     */
    private function setAdmin()
    {
        global $CFG;
        $engine_conf = $CFG->item('engineconf');

        $this->_admin['title'] = isset($engine_conf['admin_title']) ? $engine_conf['admin_title'] : $this->_default['title'];
        $this->_admin['keyword'] = isset($engine_conf['admin_keyword']) ? $engine_conf['admin_keyword'] : $this->_default['keyword'];
        $this->_admin['description'] = isset($engine_conf['admin_description']) ? $engine_conf['admin_description'] : $this->_default['description'];
        $this->_admin['limit'] = isset($engine_conf['admin_limit']) ? $engine_conf['admin_limit'] : $this->_default['limit'];
        if (isset($engine_conf['admin_auth_title'])) {
            $this->_admin['admin_auth_title'] = $engine_conf['admin_auth_title'];
        }
        return;
    }

    /**
     * 클라이언트 기본 변수 값 선언
     * @return void
     */
    private function setClient()
    {
        global $CFG;
        $engine_conf = $CFG->item('engineconf');

        $this->_client['title'] = isset($engine_conf['client_title']) ? $engine_conf['client_title'] : $this->_default['title'];
        $this->_client['keyword'] = isset($engine_conf['client_keyword']) ? $engine_conf['client_keyword'] : $this->_default['keyword'];
        $this->_client['description'] = isset($engine_conf['client_description']) ? $engine_conf['client_description'] : $this->_default['description'];
        $this->_client['limit'] = isset($engine_conf['client_limit']) ? $engine_conf['client_limit'] : $this->_default['limit'];
        return;
    }

    /**
     * 템플릿 언더바를 사용하는경우 초기화
     * @return void
     */
    private function initTemplate_()
    {
        $this->dirPathDefine();
        $this->viewDirPath();
        $this->viewFilePath();
        $this->setUseTemplate_();
        $this->addTemplate_();
        return;
    }

    /**
     * 서비스 타입별 기본 경로 및 폴더 구성
     * @return void
     */
    private function dirPathDefine()
    {
        $dir_path = VIEWPATH . $this->_service_mode . DIRECTORY_SEPARATOR;
        $dir_asset_path = $dir_path . 'assets' . DIRECTORY_SEPARATOR;

        $tpl['dir_config_path'] = $dir_path . 'config.php';
        $dir_config_path = $tpl['dir_config_path'];
        if (file_exists($dir_config_path)) {
            require_once $dir_config_path;

            // 파일 설정에 템플릿 언더바 설정이 없을경우 기본적으로는 사용하는 것입니다.
            if (!isset($tpl['is_template_'])) {
                $tpl['is_template_'] = true;
            }

            // 파일 설정에 모듈 폴더 설정이 없을경우 기본폴더는 _modules 입니다.
            if (!isset($tpl['module_path'])) {
                $tpl['module_path'] = '_modules';
            }
            if (!is_dir($dir_path . $tpl['module_path'])) {
                show_error($dir_path . $tpl['module_path'] . ' folder not found. ', 500, $dir_config_path . ' File error.');
            }

            // 파일 설정에 스타일 폴더 설정이 없을경우 기본폴더는 css 입니다.
            if (!isset($tpl['css_path'])) {
                $tpl['css_path'] = 'css';
            }
            if (!is_dir($dir_asset_path . $tpl['css_path'])) {
                show_error($dir_asset_path . $tpl['css_path'] . ' folder not found. ', 500, $dir_config_path . ' File error.');
            }

            // 파일 설정에 스크립트 폴더 설정이 없을경우 기본폴더는 js 입니다.
            if (!isset($tpl['js_path'])) {
                $tpl['js_path'] = 'js';
            }
            if (!is_dir($dir_asset_path . $tpl['js_path'])) {
                show_error($dir_asset_path . $tpl['js_path'] . ' folder not found. ', 500, $dir_config_path . ' File error.');
            }

            // 파일 설정에 이미지 폴더 설정이 없을경우 기본폴더는 images 입니다.
            if (!isset($tpl['image_path'])) {
                $tpl['image_path'] = 'images';
            }
            if (!is_dir($dir_asset_path . $tpl['image_path'])) {
                show_error($dir_asset_path . $tpl['image_path'] . ' folder not found. ', 500, $dir_config_path . ' File error.');
            }

            $this->view_config = $tpl;
        } else {
            show_error($dir_config_path . ' is not found.', 500, $dir_config_path . ' Dirctory is not found.');
        }
        return;
    }

    /**
     * 서비스 타입별 뷰 기본 경로 지정
     * @return void
     */
    private function viewDirPath()
    {
        $this->_view_dir_path = VIEWPATH . $this->_service_mode;
        return;
    }

    /**
     * 서비스 타입별 뷰 파일 경로 지정
     * @return void
     */
    private function viewFilePath()
    {
        global $URI;
        $this->_view_file_path = $URI->rsegment(1) . DIRECTORY_SEPARATOR . $URI->rsegment(2) . '.html';
        return;
    }

    /**
     * 서비스 타입별 템플릿 언더바 사용 여부 지정
     * @return void
     */
    private function setUseTemplate_()
    {
        // 관리자라면 무조건 템플릿 언더바를 사용합니다.
        // TODO: 관리자의 추가 app 일경우 템플릿 언더바를 사용하지 않는 경우도 있습니다.
        if ($this->_service_mode === 'admin') {
            $this->_is_template_ = true;
        } elseif ($this->_service_mode === 'client' || $this->_service_mode === 'mobile_client') {
            // 사용자라면 설정단에서 설정이 있을경우에는 해당하는 경우를 따라가지만 없을경우 디폴트를 사용 합니다.
            if (isset($this->view_config['is_template_']) && is_bool($this->view_config['is_template_'])) {
                $this->_is_template_ = $this->view_config['is_template_'];
            } else {
                show_error('\$tpl[\'is_template_\'] is not bool.', 500, $this->view_config['dir_config_path'] . ' File error.');
            }
        }
        return;
    }

    /**
     * 템플릿 언더바 설정 지정
     * @return void
     */
    private function addTemplate_()
    {
        require_once TPLENGINEPATH . 'Template_.class.php';
        require_once TPLENGINEPATH . 'Template_.compiler.php';

        $compile_path = ETCPATH . '_compile';
        $compile_service_mode_path = $compile_path . DIRECTORY_SEPARATOR . $this->_service_mode;

        // 컴파일 디렉토리가 없으면 생성시킵니다.
        if (!is_dir($compile_path)) {
            @mkdir($compile_path, 0777);
        }

        // 컴파일 뷰 디렉토리가 없으면 생성시킵니다.
        if (!is_dir($compile_service_mode_path)) {
            @mkdir($compile_service_mode_path, 0777);
        }

        $this->template_ = new Template_;
        $this->template_->template_dir = $this->_view_dir_path;
        $this->template_->compile_dir = $compile_service_mode_path;
        $this->template_->prefilter = 'adjustPath';
        return;
    }
}
