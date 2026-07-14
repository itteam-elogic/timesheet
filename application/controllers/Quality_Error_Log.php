<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quality_Error_Log extends CI_Controller {

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
		// Load form helper library
		$this->load->helper('form');
		// Load form validation library
		$this->load->library('form_validation');
		// Load session library
		$this->load->library('session');
		// Load pagination library
		$this->load->library('pagination');
		$this->load->library('excel'); // load excel library		
		// Load database
		
		$this->load->model('timesheet_login');
		
		$this->load->model('client_model');
		
		$this->load->model('project_model');
		
		$this->load->model('task_model');
		
		$this->load->model('emptimelog_model');
		
		$this->load->model('quality_error_log_model');
		
		$this->load->helper('text');
		
		 //$this->load->library('email');
        
		if(empty($this->session->userdata['logged_in_timesheet'])){
		
			redirect('home/login');
		}
		
    }
	
	public function index(){
		$this->load->view('quality_error_log/quality_error_list');
	}

	public function ajax_list(){
		$post = $this->input->post();
		$post['search_value'] = isset($post['search']['value']) ? $post['search']['value'] : '';
		$post['column_search'] = array(
			'c.client_name',
			'p.project_name',
			'project_creator.name',
			'p.project_type',
			'qe.analyzer_report_date',
			'qe.self_checker_name',
			'qe.analyzer_name',
			'qe.analyzer_num_of_errors',
			'qe.analyzer_link',
			'reviewer.name',
			'qe.reviewer_num_of_errors',
			'qe.reviewer_link',
			'qe.status',
			'qe.created_at'
		);
		$post['column_order'] = array(
			null,
			'c.client_name',
			'p.project_name',
			'project_creator.name',
			'p.project_type',
			'qe.analyzer_report_date',
			'qe.self_checker_name',
			'qe.analyzer_name',
			'qe.analyzer_num_of_errors',
			'qe.analyzer_link',
			'reviewer.name',
			'qe.reviewer_num_of_errors',
			'qe.reviewer_link',
			'qe.status',
			'qe.created_at',
			null
		);

		$list = $this->quality_error_log_model->get_datatables($post);
		$data = array();
		$no = isset($post['start']) ? intval($post['start']) : 0;

		$employeeIds = array();
		foreach ($list as $row) {
			if (!empty($row->self_checker_name)) {
				$ids = array_filter(array_map('trim', explode(',', $row->self_checker_name)));
				foreach ($ids as $id) {
					$employeeIds[] = $id;
				}
			}
			if (!empty($row->analyzer_name)) {
				$employeeIds[] = trim($row->analyzer_name);
			}
			if (!empty($row->reviewer_name)) {
				$employeeIds[] = trim($row->reviewer_name);
			}
		}
		$employeeIds = array_unique($employeeIds);
		$employeeNameMap = $this->quality_error_log_model->getEmployeeNamesByIds($employeeIds);

		foreach ($list as $qtyErrorResult) {
			$no++;
			$selfCheckerNames = '';
			if (!empty($qtyErrorResult->self_checker_name)) {
				$ids = array_filter(array_map('trim', explode(',', $qtyErrorResult->self_checker_name)));
				$employeeNames = array();
				foreach ($ids as $id) {
					if (isset($employeeNameMap[$id])) {
						$employeeNames[] = ucwords($employeeNameMap[$id]);
					}
				}
				$selfCheckerNames = implode(', ', $employeeNames);
			}
			$analyzerName = !empty($qtyErrorResult->analyzer_name) && isset($employeeNameMap[$qtyErrorResult->analyzer_name]) ? ucwords($employeeNameMap[$qtyErrorResult->analyzer_name]) : '';
			$reviewerName = !empty($qtyErrorResult->reviewer_name) && isset($employeeNameMap[$qtyErrorResult->reviewer_name]) ? ucwords($employeeNameMap[$qtyErrorResult->reviewer_name]) : '';
			$row = array();
			$row[] = $no;
			$row[] = ucwords($qtyErrorResult->client_name);
			$row[] = ucwords($qtyErrorResult->project_name);
			$row[] = ucwords($qtyErrorResult->project_created_name);
			$row[] = ucwords($qtyErrorResult->project_type);
			$row[] = '<span class="me-1 badge bg-info">'.date('d-M-Y', strtotime($qtyErrorResult->analyzer_report_date)).'</span>';
			$row[] = '<span class="text-primary">'.$selfCheckerNames.'</span>';
			$row[] = '<span class="label label-info">'.$analyzerName.'</span>';
			$row[] = $qtyErrorResult->analyzer_num_of_errors;
			$row[] = $qtyErrorResult->analyzer_link;
			$row[] = '<span class="label label-success">'.$reviewerName.'</span>';
			$row[] = $qtyErrorResult->reviewer_num_of_errors;
			$row[] = $qtyErrorResult->reviewer_link;
			$statusClass = ($qtyErrorResult->status=='Yes') ? 'fa fa-check-circle' : 'fa fa-close';
			$statusLabel = ($qtyErrorResult->status=='Yes') ? 'Yes' : 'No';
			$row[] = '<span id="changeStatusRow_'.$qtyErrorResult->qty_error_id.'"><a class="'.$statusClass.' label '.(($qtyErrorResult->status=='Yes') ? 'label-success' : 'label-danger').'" style="cursor:pointer;" title="Click to toggle status" onClick="update_emp_status('.$qtyErrorResult->qty_error_id.',\''.$qtyErrorResult->status.'\')"> '.$statusLabel.'</a></span>';
			$row[] = date('d-M-Y', strtotime(explode(' ', $qtyErrorResult->created_at)[0]));
			$row[] = '<a href="'.base_url().'quality_error_log/qualitylogview/'.$qtyErrorResult->qty_error_id.'" data-toggle="tooltip" title="View"><i class="fa fa-vimeo"></i></a> | <a href="'.base_url().'quality_error_log/reviewer/'.$qtyErrorResult->qty_error_id.'" data-toggle="tooltip" title="Update Reviewer Information"><i class="fa fa-edit"></i></a>';
			$data[] = $row;
		}

		$output = array(
			"draw" => isset($post['draw']) ? intval($post['draw']) : 0,
			"recordsTotal" => $this->quality_error_log_model->count_all($post),
			"recordsFiltered" => $this->quality_error_log_model->count_filtered($post),
			"data" => $data,
		);

		echo json_encode($output);
	}
	
	
	
	public function add_quality_error_log_details($emp_record_id = NULL){
	
	   if(empty($emp_record_id)) : 
			
			 $this->load->view('quality_error_log/quality_error_form');
			 
		else:
			
			   //$data['updateEmpRecord'] = $this->quality_error_log_model->getUpdateEmpRecords($emp_record_id);
	
		    	//$this->load->view('employee/add_employees' , $data);
				
				//$this->load->view('quality_error_log/quality_error_form' , $data );
					
		endif;	   
			
	 
	}
	
	
	
	public function save_quality_error_data(){		
		
		if(!empty($this->input->post('client_Id'))):
                
			$data = array(
				
					'qty_empId' 					 => $this->session->userdata['logged_in_timesheet']['empId'],
					'qty_client_Id' 				 => $this->input->post('client_Id'),
					'qty_project_Id' 				 => $this->input->post('project_Id'),
					//'qty_error_name' 			 	 => $this->input->post('task_Id'), // Store task with comma separate
				    'self_checker_name'				 => $this->input->post('self_checker_name'), // Store employees with comma separate
					'analyzer_name'					 => $this->input->post('analyzer_name'), 
					'analyzer_num_of_errors'		 => $this->input->post('analyzer_num_of_errors'),
					'analyzer_comments'			     => $this->input->post('analyzer_comments'),
					'analyzer_link' 			 	 => $this->input->post('analyzer_link'),
					'analyzer_report_date' 			 => $this->input->post('analyzer_report_date'),
				    'status'				     	 => 'No',
                    'reviewer_name'					 => $this->input->post('reviewer_name'), 
					'reviewer_num_of_errors'		 => $this->input->post('reviewer_num_of_errors'),
					'reviewer_comments'			     => $this->input->post('reviewer_comments'),
					'reviewer_link' 			 	 => $this->input->post('reviewer_link'),
					'reviewer_updated_date' 		 => date('Y-m-d H:i:s'),	
					'created_at'    	 		     => date('Y-m-d H:i:s'),
					'updated_at' 				     => date('Y-m-d H:i:s')
				
				);
		
			$getStoredDetails = $this->quality_error_log_model->addQtyErrorLogInformation($data);
																	   
			redirect('quality_error_log');
                
        endif;        
		
		
	}

	public function reviewer($qty_error_id = NULL){


			if(!empty($qty_error_id)):

					 $data['updateReviewerData'] = $this->quality_error_log_model->updateReviewerInformation($qty_error_id);

					 $this->load->view('quality_error_log/reviewer_error_form' , $data);

			endif; 	

	}

	public function reviewer_updated_data(){
				
		$reviwer_updated_id = $this->input->post('reviwer_updated_id');
		if(!empty($reviwer_updated_id)):
                
			$data = array(
                    'analyzer_name'					 => $this->input->post('analyzer_name'), 
					'analyzer_num_of_errors'		 => $this->input->post('analyzer_num_of_errors'),
					'analyzer_comments'			     => $this->input->post('analyzer_comments'),
					'analyzer_link' 			 	 => $this->input->post('analyzer_link'),
					'reviewer_name'					 => $this->input->post('reviewer_name'), 
					'reviewer_num_of_errors'		 => $this->input->post('reviewer_num_of_errors'),
					'reviewer_comments'			     => $this->input->post('reviewer_comments'),
					'reviewer_link' 			 	 => $this->input->post('reviewer_link'),
					'reviewer_updated_date' 		 => date('Y-m-d H:i:s')				   				
				);
		
			$getStoredDetails = $this->quality_error_log_model->updateReviewerQuery($reviwer_updated_id , $data);
																	   
			redirect('quality_error_log');
                
        endif;

	}

	public function qualityLogView($qty_error_id = NULL){


		if(!empty($qty_error_id)):

				 $data['viewARData'] = $this->quality_error_log_model->qualityViewInformation($qty_error_id);

				 $this->load->view('quality_error_log/view_quality_error_form' , $data);

		endif; 	

}

public function analyzerReviewerStatus(){	

	$qty_error_id 	 = $this->input->post('qty_error_id');
    
	$status 	 = $this->input->post('status');
	
	 if(!empty($qty_error_id)):
	 
		 	$del = $this->quality_error_log_model->update_analyzer_reviewer_status($qty_error_id , $status);
		 
	 endif;	

}
    
    
    
    public function getListOfProjectsWithClient(){  // Getting Client wise projects
	
	  $client_Id  = $this->input->post('client_Id'); 
	   
	   if(!empty($client_Id)) :
	   
	   		$getProjects = $this->quality_error_log_model->getListOfProjectsWithClient($client_Id);
	   
	   endif; 
	
	 }  // Getting Client wise projects END
	
    
     public function quality_report_automation_search(){

	$department  = $this->input->post('department'); 	
 
	if(!empty($department)):

			$search_data = array(				
				'department' 					 => $this->input->post('department'),
				'project_manager' 				 => $this->input->post('project_manager'),
				'analyzer' 						 => $this->input->post('analyzer'),
				'client' 			 			 => $this->input->post('client'), 
			    'self_checker'					 => $this->input->post('self_checker'),
				'form_date'						 => $this->input->post('form_date'), 
				'to_date'						 => $this->input->post('to_date'), 
				);

				//echo '<pre>'; print_r($search_data); exit;

			$data['searchReportData'] = $this->quality_error_log_model->qualitySearchReportQuery($search_data);

			 $this->load->view('quality_error_log/quality_report_automation_search' , $data);

	else:	 

			$this->load->view('quality_error_log/quality_report_automation_search');

	 endif;	
 
 }
    
    
	
	
}
