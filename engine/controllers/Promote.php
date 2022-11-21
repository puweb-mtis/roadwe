<?php defined('BASEPATH') or exit('No direct script access allowed');

class Promote extends CI_Controller
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
    public function notice()
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
    
    public function notice_view()
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
    
    public function press()
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
    
    public function press_view()
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
    
    public function brochure()
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
