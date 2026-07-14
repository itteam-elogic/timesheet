<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LmsReport extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	
	 public function __construct() {
		
		parent::__construct();
		$this->load->helper('url');
		// Load form helper library
		$this->load->helper('form');
		// Load form validation library
		$this->load->library('form_validation');
		// Load session library
		$this->load->library('session');
		$this->load->library('excel'); // load excel library
		$this->load->helper('text');
		
		// Load database		
		$this->load->model('timesheet_login');

		$this->load->model('defaulter_model');
	    
        $this->load->model('lms_model');
        
        $this->load->model('service_agreement_model');
				
		if(empty($this->session->userdata['logged_in_timesheet'])){
		
			redirect('home/login');
		}
		
    }
	
	public function index(){  // Search Employee Lime Log
        // $this->load->view('lms-report/index');
		$postData = $this->input->post();
		$empDet = null;$report = null;
		if(count($postData) > 0){ 
			$report= $this->lms_model->fetchReportByEmp($postData);
			$empDet= $this->lms_model->getEmployeeDetails($postData['empId']);
		}
		$data['empDet'] = $empDet ? $empDet : null;
		$data['payload'] = $postData ? $postData : null;
		$data['report'] = $report ? $report : [];
		$this->load->view('lms-report/fetchReportByEmp', $data);
		
	}

	public function fetchEmployeeByKey(){
		$postData = $this->input->post();
		if(!empty($postData['term'])):
            $employeesArr= $this->lms_model->fetchEmployeeByKey($postData['term']);
            echo json_encode($employeesArr);
        
        endif;
	}
	public function fetchVideosByKey(){
		$postData = $this->input->post();
		
		if(!empty($postData['term'])):
            $videosArr= $this->lms_model->fetchVideosByKey($postData['term']);
            echo json_encode($videosArr);
        
        endif;
	}
	public function fetchReportByEmp(){
		$postData = $this->input->post();
		// echo "<pre>";print_r($postData);exit;
		$empDet = null;$report = array(
			'total_videos_watched' => 0,
			'report' => []
		);$videoDet=NULL;
		if(count($postData) > 0){
			if(!empty($postData['empId']) && count($postData['empId'])> 0){  
				foreach($postData['empId'] as $k=>$empId){ 
					if(!empty($postData['videoId']) && count($postData['videoId'])> 0){
						foreach($postData['videoId'] as $v=>$videoId){ 
							$payload = array('empId'=> $empId, 'videoId'=> $videoId, 
							'report_from_date'=> !empty($postData['report_from_date']) ? $postData['report_from_date']: NULL, 
							'report_to_date'=> !empty($postData['report_to_date']) ? $postData['report_to_date']: NULL );
							$rslt = $this->lms_model->fetchReportByEmp($payload);
							$report['total_videos_watched'] += $rslt['total_videos_watched'];
							$report['report'] = array_merge($rslt['report'], $report['report']);
						}
					}else{
						$payload = array('empId'=> $empId, 'videoId'=> NULL, 
						'report_from_date'=> !empty($postData['report_from_date']) ? $postData['report_from_date']: NULL, 
						'report_to_date'=> !empty($postData['report_to_date']) ? $postData['report_to_date']: NULL );
						// echo "<pre>";print_r($this->lms_model->fetchReportByEmp($payload));
						$rslt = $this->lms_model->fetchReportByEmp($payload);
						$report['total_videos_watched'] += $rslt['total_videos_watched'];
						$report['report'] = array_merge($rslt['report'], $report['report']);

					}
				}
			}elseif(!empty($postData['videoId']) && count($postData['videoId'])> 0){  
				foreach($postData['videoId'] as $v=>$videoId){ 
					$payload = array('empId'=> NULL, 'videoId'=> $videoId, 
						'report_from_date'=> !empty($postData['report_from_date']) ? $postData['report_from_date']: NULL, 
						'report_to_date'=> !empty($postData['report_to_date']) ? $postData['report_to_date']: NULL );
					$rslt = $this->lms_model->fetchReportByEmp($payload);
					$report['total_videos_watched'] += $rslt['total_videos_watched'];
					$report['report'] = array_merge($rslt['report'], $report['report']);
				}
			}elseif(empty($postData['videoId']) && empty($postData['empId'])){
				$payload = array('empId'=> NULL, 'videoId'=> NULL, 
						'report_from_date'=> !empty($postData['report_from_date']) ? $postData['report_from_date']: NULL, 
						'report_to_date'=> !empty($postData['report_to_date']) ? $postData['report_to_date']: NULL );
				$rslt = $this->lms_model->fetchReportByEmp($payload);
				$report['total_videos_watched'] += $rslt['total_videos_watched'];
				$report['report'] = array_merge($rslt['report'], $report['report']);
			}
		}
		$data['empDet'] = null;
		$data['payload'] = $postData ? $postData : null;
		$data['report'] = $report ? $report : [];
        $data['videoDet'] = null;
    //    echo "<pre>";print_r($data);exit;
		$this->load->view('lms-report/fetchReportByEmp', $data);
	}
}