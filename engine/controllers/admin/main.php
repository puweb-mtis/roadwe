<?php defined('BASEPATH') or exit('No direct script access allowed');

class Main extends CI_Controller
{

    /**
     * 생성자
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 관리자단 메인페이지
     */
    public function index()
    {
        $data = [];

        $data['controll_var'] = "관리자 컨트롤 변수 확인";

        // 화면 뷰
        if ($this->siteinit->_is_template_) {
            $this->load->view(null, $data);
        } else {
            $this->load->view($this->uri->ruri_string(), $data);
        }
        return;
    }
}
