<?php defined('BASEPATH') or exit('No direct script access allowed');

class Cs extends CI_Controller
{
    
    /**
     * 생성자
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 문의하기
     */
    public function inquiry()
    {
        $data = [];
        
        $data['controll_var'] = "클라이언트 컨트롤 변수 확인";
        
        // 화면 뷰
        if ($this->siteinit->_is_template_) {
            $this->load->view(null, $data);
        } else {
            $this->load->view('client/' . $this->uri->ruri_string(), $data);
        }
        return;
    }
    
    /**
     * 자주 묻는 질문
     */
    public function faq()
    {
        $data = [];
        
        $data['controll_var'] = "클라이언트 컨트롤 변수 확인";
        
        // 화면 뷰
        if ($this->siteinit->_is_template_) {
            $this->load->view(null, $data);
        } else {
            $this->load->view('client/' . $this->uri->ruri_string(), $data);
        }
        return;
    }
}
