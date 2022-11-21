<?php defined('BASEPATH') or exit('No direct script access allowed');

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Samplecrud extends CI_Controller
{
    private $idx_column_nm = 'acmg_idx';    // 운영자 테이블 일련번호

    /**
     * 생성자
     * 
     * @param type void
     * @return return void
     */
    function __construct()
    {
        parent::__construct();

        // 운영자 모델 호출
        $this->load->model('managerModel');        
    }

    /**
     * 기본(샘플) 페이지
     * 
     * @param type void
     * @return return void
     */
    public function index()
    {
        $data = [];

        // 화면 뷰
        $this->load->view(null, $data);
        return;
    }

    public function formVars()
    {
        // 샘플 공통 변수 선언
        $this->data = [
            'phone_cd'      => $this->meta->get('phone_cd', 10),       // 전화번호 앞자리
            'cellphone_cd'  => $this->meta->get('cellphone_cd', 10),   // 휴대폰 앞자리
        ];

        // 샘플 상세 변수 선언
        if ($this->uri->rsegment(2) === 'form') {
            $this->data['def_yn_txt_cd'] = $this->meta->get('def_yn_txt_cd', 10);  // yn 텍스트 기본 값
            $this->data['interest_cd'] = $this->meta->get('interest_cd', 10);      // 관심 분야 목록
        }
    }    
    
    /**
     * 기본(메다데이타) 등록&수정 폼입니다.(등록/수정 폼을 같이 쓸 경우)
     * 
     * @param string $mete_group 해당메타그룹
     * @return return void
     */
    public function form($mete_group = null)
    {
        $data = [];

        // 첨부 파일 갯수
        $data['files_cnt'] = 5;

        if (is_null($mete_group)) { // 등록화면
            $data['crud_mode'] = 'create';
        } else { // 수정화면
            $data['crud_mode'] = 'update';

            // 메타 데이터 호출 방법(그룹)
            $data[$mete_group] = $this->meta->get($mete_group, 20, null);
            // 메타 데이터 호출 방법(단일)
            $data['get_add_desc'] = $this->meta->get('siteinfo', 20, 'add_desc');
            // 메타 파일 데이터 호출 방법
            $data['files'] = $this->meta->getFileLoad($mete_group, 0);

            if ($data['files']) {
                $data['files_cnt'] = (int) ($data['files_cnt'] - count($data['files']));
            }            
        }

        // 화면 공통 변수 호출
        $this->formVars();
        // 추가 스크립트 선언
        $this->scripts[] = $this->common->scripts('samplecrud.js');
        // 화면 뷰
        if (isset($this->data) && ($this->data != null)) {
            $data['set_params'] = $this->data;
        }
        $this->load->view(null, $data);
        return;
    }

    /**
     * 기본(메다데이타) 처리단입니다.(등록&수정)
     * 
     * @param string $crud_mode 처리타입
     * @param int $no 해당일련번호
     */
    public function crudProc($crud_mode = null, $no = null)
    {
        if ($crud_mode === 'create' || $crud_mode === 'update') { // 등록 && 수정
            $meta_group = $this->input->post('meta_group');
            $crud_data = $this->input->post();

            // 수정인 경우에만
            if ($crud_mode === 'update') {
                if (isset($crud_data['filekey'])) {
                    foreach ($crud_data['filekey'] as $key => $val) {
                        // 첨부파일일련번호가 있고 첨부파일삭제여부가 Y인 경우(파일삭제)
                        if (!empty($crud_data['filekey'][$key]) && $crud_data['filedelyn'][$key] === 'Y') {
                            $this->meta->uploadRemove($meta_group, 0, $crud_data['filekey'][$key]);
                        }
                    }
                }
            }            

            // 파일이 존재 하는 경우 파일 업로드 처리
            if ($_FILES) {                
                $file_existst_cnt = 0;
                foreach ($_FILES['userfile']['name'] as $key => $val) {
                    if (!empty($val)) {
                        $file_existst_cnt++;
                    }
                }

                if ($file_existst_cnt > 0) {
                    // 파일 업로드 처리
                    $this->meta->setFileUpload($meta_group);
                    if ($this->upfile->code === 100) {
                        // TODO 정보 업데이트 예시 처리 예정
                    } else { // 이미지 업로드에 실패하였습니다.
                        ajaxExcep('cm_fileupload_fail');
                        return;
                    }
                }
            }            

            // 메타 데이터 처리
            try {
                if ($meta_group) {
                    // 메타 데이터 INSERT 방법(그룹)
                    $this->meta->set($meta_group, $crud_data, 20);

                    if ($crud_mode === 'create') { // 등록
                        // 헬퍼 함수 호출 방식
                        ajaxExcep("cm_meta_{$crud_mode}_success", site_url(ADMINURL . '/samplecrud/index'));                        
                        // 기본 방식
                        // $this->js->pageRedirect(site_url(ADMINURL . '/samplecrud/index'), $this->lang->line("cm_meta_{$crud_mode}_success"));
                        // return false;
                    } else { // 수정
                        // 헬퍼 함수 호출 방식
                        ajaxExcep("cm_meta_{$crud_mode}_success", $this->input->post('referer'));                        
                        // 기본 방식
                        // $this->js->pageRedirect($this->input->post('referer'), $this->lang->line("cm_meta_{$crud_mode}_success"));
                        // return false;
                    }
                } else {
                    throw new Exception($this->lang->line('cm_meta_group_set_error'));
                }
            } catch (Exception $exc) {
                $this->js->pageBack($exc->getMessage());
                return false;
            }
            return;
        } elseif ($crud_mode === 'put') { // 개별(컬럼)수정
            $update_data = [];

            $column_nm = $this->input->post('column_nm');
            $column_val = $this->input->post('column_val');

            $update_data[$column_nm] = $column_val;
            $update_where[$this->idx_column_nm] = $this->input->post('idx_no');
            $update_res = $this->managerModel->update($update_data, $update_where);
            
            unset($update_data);
            unset($update_where);

            // 헬퍼 함수 호출 방식
            ajaxExcep('cm_proc_update_success', null, true);            
            // 기본 방식
            // $json['result'] = TRUE;
            // $json['msg'] = $this->lang->line('cm_proc_update_success');
            // echo json_encode($json);
            return;            
        } else {
            show_error('Access is limited on this page.');
        }
        return;
    }
    
    /**
     * 메타데이타 등록 처리
     * @return return void
     */
    public function setMetadata()
    {
        // 테이블 초기화
        // $this->db->truncate('common_meta');
        // 테이블 특정 조건 삭제
        $tables = array('common_meta');
        $this->db->where('cmmt_type', 10);
        $this->db->delete($tables);

        // 파일을 통한 메타데이타 리스트 로드
        $this->config->load(ADMINURL . DIRECTORY_SEPARATOR . 'meta_data_set');
        $meta_list = $this->config->item('basic', 'site');

        foreach ($meta_list as $key => $val) {
            $this->meta->set($key, $val, 10);
        }

        $this->js->pageRedirect(site_url(ADMINURL . '/samplecrud/index'), $this->lang->line('cm_meta_create_success'));
        return false;
    }

    /**
     * Composer(Dompdf) 사용법 예시
     */
    public function composerDompdf()
    {
        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml('hello world');
        // $pdf_html_path = VIEWPATH."admin".DIRECTORY_SEPARATOR."member".DIRECTORY_SEPARATOR."index.html";
        // $html = file_get_contents($pdf_html_path);
        // $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        // $dompdf->stream();
        $dompdf->stream("파일명지정", array("Attachment" => 0));
    }

    /**
     * Composer(Monolog) 사용법 예시
     */
    public function composerMonolog()
    {
        $log = new Logger('ci3');
        $log->pushHandler(new StreamHandler('D:\mework\LOCAL_CI3_DEVLAB_SAMPLE\etc\logs\monolog.log', Logger::INFO));

        $this->formVars();
        $log->info(serialize($this->data));
        $log->error('Bar');
    }

    /**
     * Composer(PhpSpreadsheet) 사용법 예시
     */    
    public function directImport()
    {
		$data = [];
        
		$helper = new \PhpOffice\PhpSpreadsheet\Helper\Sample();
		
		// $filePath = __DIR__ .DIRECTORY_SEPARATOR. '/../../../etc/uploads/import/skulist.xlsx';
		$filePath = UPLOADPATH.DIRECTORY_SEPARATOR.'import'.DIRECTORY_SEPARATOR.'skulist.xlsx';        

        if (file_exists($filePath)) {
            $helper->log(pathinfo($filePath, PATHINFO_BASENAME) . ' 파일을 읽는중...');
			$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
			// debug_var($spreadsheet);
			$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
			// $highestRow = $spreadsheet->getActiveSheet()->getHighestRow();
			// $highestDataRow = $spreadsheet->getActiveSheet()->getHighestDataRow();
			
			if ($sheetData) {    
                debug_var($sheetData);
            }        
        } else {
            $helper->log(pathinfo($filePath, PATHINFO_BASENAME) . '(ERROR:파일이 존재 하지 않음)');
        }
        return;
    }
}