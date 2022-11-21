<?php defined('BASEPATH') or exit('No direct script access allowed');

class Recruit extends CI_Controller
{
    
    /**
     * 생성자
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 클라이언트 메인페이지
     */
    public function index()
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
    
    /*
     * 채용공고 리스트
     * */
    public function announce_list()
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
    
    /*
     * 채용공고 뷰
     * */
    public function announce_view()
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
