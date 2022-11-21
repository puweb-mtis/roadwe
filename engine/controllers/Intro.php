<?php defined('BASEPATH') or exit('No direct script access allowed');

class Intro extends CI_Controller
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
    
    public function history()
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
    
    public function ci()
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
    
    public function partners()
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
    
    public function location()
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
