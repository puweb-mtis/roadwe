<?php defined('BASEPATH') or exit('No direct script access allowed');

class Business extends CI_Controller
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
    public function lidar()
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
    
    public function lidar_view1()
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
    
    public function lidar_view2()
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
    
    public function lidar_view3()
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
    
    public function lidar_view4()
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
    
    public function lidar_view5()
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
    
    public function lidar_view6()
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
    
    public function droid()
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
