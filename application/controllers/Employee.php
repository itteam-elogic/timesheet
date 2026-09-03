<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends CI_Controller {

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
		// Load database
		 
		$this->load->model('timesheet_login');
		
		if(empty($this->session->userdata['logged_in_timesheet'])){
		
			redirect('home/login');
		}
		
    }
	
	public function index(){
	
			$data['getEmployees'] = $this->timesheet_login->getEmployees();
			
			$this->load->view('employee/employees' , $data);
	}
	
	public function add($empId = NULL){
	
	   if(empty($empId)) :
			
			 $this->load->view('employee/add_employees');
			 
		else:
			
			   $data['updateEmployee'] = $this->timesheet_login->getEmployees($empId);
	
		    	$this->load->view('employee/add_employees' , $data);
					
		endif;	   
			
	 
	}
	
	public function addEmployee(){
	
	     // Adding new organization function.
		
		$this->form_validation->set_error_delimiters('<label class="error">', '</label>');
	
	    $this->form_validation->set_rules('username', 'Username alreddy exit. Please try another Username', 'required|trim|is_unique[employee_details.username]');
		
		//$this->form_validation->set_rules('email', 'Email Id alreddy exit. Please try another Email Id', 'required|trim|is_unique[employee_details.email]');
		
		if ($this->form_validation->run() == FALSE) {
	
			$this->load->view('employee/add_employees');
			
	    }else{
				
			
			/* User profile picture uploding functionality  */
		
			$config['upload_path'] = 'uploads/employee_pic/';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['file_name'] = $empId.'_'.$_FILES['employee_image']['name'];
			$config['overwrite']     = false;
			$config['max_size']	 = '5120';
			 //$this->upload->initialize($config);
			 $this->load->library('upload', $config);
		   //Load upload library and initialize configuration
					if($this->upload->do_upload('employee_image')){
						$uploadData = $this->upload->data();
						$picture = $uploadData['file_name'];
					}else{
						 $picture = 'default.jpg';
					}
		/* User profile picture uploding functionality  */

			$data = array(
            'emp_com_id' 		 => $this->input->post('emp_com_id'),
			'name' 				 => $this->input->post('fname').' '.$this->input->post('lname'),
			'email' 			 => $this->input->post('email'),
			'username' 			 => strtolower($this->input->post('username')),
			'password' 			 => md5(strtolower($this->input->post('password'))),
			'designation'		 => $this->input->post('designation'),
			'emp_joining_date'	 => $this->input->post('emp_joining_date'),
			'user_type'		 	 => $this->input->post('user_type'),
			'reporting_manger'	 => $this->input->post('reporting_manger'),
			'department'		 => $this->input->post('department'),
			'avatar' 			 => $picture,
			'created_at'    	 => date('Y-m-d H:i:s'),
			'updated_at' 		 => date('Y-m-d H:i:s')
			);

	     $this->timesheet_login->add_employee($data);

		 redirect('employee');

	   }

	}

	/*public function updateemployee(){  // Commented an 24-07-2016

	    // Adding new organization function.

		$empId  =  $this->input->post('empId');

		$this->form_validation->set_error_delimiters('<label class="error">', '</label>');

	    $this->form_validation->set_rules('username', 'Username alreddy exit. Please try another Username', 'required|trim|is_unique[employee_details.username]');

		$this->form_validation->set_rules('email', 'Email Id alreddy exit. Please try another Email Id', 'required|trim|is_unique[employee_details.email]');

		if ($this->form_validation->run() == FALSE) {

		    $data['updateEmployee'] = $this->timesheet_login->getEmployees($empId);

			$this->load->view('employee/add_employees' , $data);

	    }else{

			$data = array(
			'name' 				 => $this->input->post('fname').' '.$this->input->post('lname'),
			'email' 			 => $this->input->post('email'),
			'username' 			 => strtolower($this->input->post('username')),
			'password' 			 => md5($this->input->post('password')),
			'designation'		 => $this->input->post('designation'),
			'user_type'		 => $this->input->post('user_type'),
			'avatar' 			 => 'default.jpg',
			'updated_at' 		 => date('Y-m-d H:i:s')
			);

	     $this->timesheet_login->update_employee($data , $empId);

		 redirect('employee');

		// }

	} */
	
	
	public function updateemployee(){
		
	    // Adding new organization function.
		
		$update_empId  = $this->input->post('update_empId'); // Update profile user id
		
		if(empty($update_empId)) { // this condition based on update profile for particular manager or developer
		
		$empId  	 =  $this->input->post('empId');
		
		$username 	 = strtolower($this->input->post('username'));
		
		$userQ = $this->db->get_where('employee_details',array('username'=>$username));
		
		$countUsers = $userQ->num_rows();
		
		
		/* User profile picture uploding functionality  */
		
			$config['upload_path'] = 'uploads/employee_pic/';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['file_name'] = $empId.'_'.$_FILES['employee_image']['name'];
			$config['overwrite']     = false;
			$config['max_size']	 = '5120';
			 //$this->upload->initialize($config);
			 $this->load->library('upload', $config);
		   //Load upload library and initialize configuration
					if($this->upload->do_upload('employee_image')){
						$uploadData = $this->upload->data();
						$picture = $uploadData['file_name'];
					}
		/* User profile picture uploding functionality  */
		
		
		
		if($countUsers == 0 ){
			
		if(!empty($picture)) {
			
			$data = array(
                    'emp_com_id' 		 => $this->input->post('emp_com_id'),
					'name' 				 => $this->input->post('fname').' '.$this->input->post('lname'),
					'email' 			 => $this->input->post('email'),
					'username' 			 => strtolower($this->input->post('username')),
					'designation'		 => $this->input->post('designation'),
					'user_type'		     => $this->input->post('user_type'),
					'reporting_manger'	 => $this->input->post('reporting_manger'),
					'department'		 => $this->input->post('department'),
					'emp_joining_date'	 => $this->input->post('emp_joining_date'),
					'avatar' 			 => $picture,
					'updated_at' 		 => date('Y-m-d H:i:s')
			);

		}else{
		
				$data = array(
                    'emp_com_id' 		 => $this->input->post('emp_com_id'),
					'name' 				 => $this->input->post('fname').' '.$this->input->post('lname'),
					'email' 			 => $this->input->post('email'),
					'username' 			 => strtolower($this->input->post('username')),
					'designation'		 => $this->input->post('designation'),
					'user_type'		     => $this->input->post('user_type'),
					'reporting_manger'	 => $this->input->post('reporting_manger'),
					'department'		 => $this->input->post('department'),
					'emp_joining_date'	 => $this->input->post('emp_joining_date'),
					'updated_at' 		 => date('Y-m-d H:i:s')
			     );
		
		}			
	 
	     $this->timesheet_login->update_employee($data , $empId);
		
		 echo  '<div style="color:#FF0000; font-size:16px; padding:30px;"> Employee details successfully updated. </div>';
	       
		 echo  '<script>setTimeout(function(){window.location.href="'.base_url('employee').'"},3000);</script>';
		
		}else{
			
			if(!empty($picture)) {
				
				$data = array(
                            'emp_com_id' 		 => $this->input->post('emp_com_id'),
							'name' 				 => $this->input->post('fname').' '.$this->input->post('lname'),
							'email' 			 => $this->input->post('email'),
							'designation'		 => $this->input->post('designation'),
							'user_type'		     => $this->input->post('user_type'),
							'reporting_manger'	 => $this->input->post('reporting_manger'),
							'department'		 => $this->input->post('department'),
							'emp_joining_date'	 => $this->input->post('emp_joining_date'),
							'avatar' 			 => $picture,
							'updated_at' 		 => date('Y-m-d H:i:s')
			     );
				
			}else{
				
				$data = array(
                            'emp_com_id' 		 => $this->input->post('emp_com_id'),
							'name' 				 => $this->input->post('fname').' '.$this->input->post('lname'),
							'email' 			 => $this->input->post('email'),
							'designation'		 => $this->input->post('designation'),
							'user_type'		     => $this->input->post('user_type'),
							'reporting_manger'	 => $this->input->post('reporting_manger'),
							'department'		 => $this->input->post('department'),
							'emp_joining_date'	 => $this->input->post('emp_joining_date'),
							'updated_at' 		 => date('Y-m-d H:i:s')
			     );
				
			}			
			
			
	 
	        $this->timesheet_login->update_employee($data , $empId);
			 
			 echo  '<div style="color:#FF0000; font-size:16px; padding:30px;"> Username Already exist in database.</div>';
	       
		     echo  '<script>setTimeout(function(){window.location.href="'.base_url('employee').'"},3000);</script>';
		
		}
		
		}else{

          
		/* User profile picture uploding functionality  */
		
			$config['upload_path'] = 'uploads/employee_pic/';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['file_name'] = $update_empId.'_'.$_FILES['employee_image']['name'];
			$config['overwrite']     = false;
			$config['max_size']	 = '5120';
			 //$this->upload->initialize($config);
			 $this->load->library('upload', $config);
		   //Load upload library and initialize configuration
					if($this->upload->do_upload('employee_image')){
						$uploadData = $this->upload->data();
						$picture = $uploadData['file_name'];
					}
		/* User profile picture uploding functionality  */
		  
		if(!empty($picture)) {
		
			$data = array(
				'name' 				 => $this->input->post('fname').' '.$this->input->post('lname'),
				'avatar' 			 => $picture,
				'mobile_num'         => $this->input->post('mobile_num'),
				'updated_at' 		 => date('Y-m-d H:i:s')
			 );
	 
		}else{
			
				$data = array(
				'name' 				 => $this->input->post('fname').' '.$this->input->post('lname'),
				'mobile_num'         => $this->input->post('mobile_num'),
			);
			
		}
	 
	     $this->timesheet_login->update_employee($data , $update_empId);
		
		 $this->session->set_flashdata('msg', 'Your profile details successfully updated!</span>');
              
		 redirect(base_url().'empreports/cPass');
		
		
        }
		
	
	}
	
	
	/* public function delete(){  // Delete employee single record into databasse
	
		   $empId  = $this->input->post('empId');
		   
			if(!empty($empId)):
			
				$del = $this->timesheet_login->del_employee($empId);
				
			endif;
	}*/
	
	public function update_emp_status(){  // Delete employee single record into databasse
	
		   $empId 	 = $this->input->post('empId');
		   $status 	 = $this->input->post('status');
		   
			if(!empty($empId)):
			
				$del = $this->timesheet_login->update_employee_status($empId , $status);
				
			endif;
	}
	
	
	 public function getRecentEmployees(){  //Get Recent Employee Information
  
    	$recentEmp = $this->timesheet_login->recentEmployees();
		
		echo json_encode($recentEmp);
  
  }
  
  
  public function cPass(){  //Change Password
   
   		$empId  = $this->uri->segment(3);
		
		if(!empty($this->input->post('password'))):
		
			$password		 = 	$this->input->post('password');
			
			$this->timesheet_login->updateChangePassword( $password , $empId );
			
			 $this->session->set_flashdata('msg', 'Your Password Successfully Changed!</span>');
              
			 redirect(base_url().'employee/cPass/'.$empId );
			
		
		else:
		
			$this->load->view('employee/changepassword');
			
		endif;
		
   
   }

    /************************************ ****************** Download employee report Excel & PDF format  *********************************/

   public function employee_list_information(){
		$department = $this->input->post('department');
	$from_year  = $this->input->post('from_year');
$from_month = $this->input->post('from_month');

$to_year    = $this->input->post('to_year');
$to_month   = $this->input->post('to_month');
		$projectManager = $this->input->post('project_manager');
		$employeeSearch = $this->input->post('employee_search');
		$statusFilter = $this->input->post('status_filter');

		$department = $this->normalize_multi_filter($department);
		$projectManager = $this->normalize_multi_filter($projectManager);
		if ($statusFilter === null || $statusFilter === '') {
			$statusFilter = 'active';
		}

		  // KEEP THIS HERE
    $from_date = '';
    $to_date   = '';

    if (!empty($from_year) && !empty($from_month)) {

        $from_date = $from_year . '-' . $from_month . '-01';
    }

    if (!empty($to_year) && !empty($to_month)) {

        $to_date = date(
            'Y-m-t',
            strtotime($to_year . '-' . $to_month . '-01')
        );
    }


		$filters = array(
			'department' => $department,
			'from_year' => $from_year,
			'from_month' => $from_month,
			'to_year' => $to_year,
			'to_month' => $to_month,
			'project_manager' => $projectManager,
			'employee_search' => $employeeSearch,
			'status_filter' => $statusFilter
		);

		$data['getEmployees'] = $this->timesheet_login->getReportMasterEmployee($filters);
		$data['managers'] = $this->timesheet_login->getActiveManagers();
		$data['selected_department'] = $department;
		$data['selected_from_date'] = $from_date;
		$data['selected_to_date'] = $to_date;
		$data['selected_project_manager'] = $projectManager;
		$data['selected_employee_search'] = $employeeSearch;
		$data['selected_status_filter'] = $statusFilter;

		$headcountPeriod = $this->resolve_headcount_period($from_date, $to_date);
		$headcountEmployees = $this->timesheet_login->getEmployeesForHeadcount(array(
			'department' => $department,
			'project_manager' => $projectManager
		));
		$headcountData = $this->build_department_headcount(
			$headcountEmployees,
			$headcountPeriod['from_date'],
			$headcountPeriod['to_date']
		);
		$data['departmentHeadcount'] = $headcountData['rows'];
		$data['departmentHeadcountTotals'] = $headcountData['totals'];
		$data['headcountPeriodLabel'] = $headcountPeriod['label'];
	if ($this->input->is_ajax_request()) {
    $this->load->view('employee/employee_table_ajax', $data);
} else {
    $this->load->view('employee/employees_report_automation_search', $data);
}
   } 

	private function resolve_headcount_period($from_date, $to_date) {
		if (empty($from_date) && empty($to_date)) {
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-t');
		} elseif (!empty($from_date) && empty($to_date)) {
			$to_date = date('Y-m-t', strtotime($from_date));
		} elseif (empty($from_date) && !empty($to_date)) {
			$from_date = date('Y-m-01', strtotime($to_date));
		}

		return array(
			'from_date' => $from_date,
			'to_date' => $to_date,
			'label' => date('d-M-Y', strtotime($from_date)) . ' to ' . date('d-M-Y', strtotime($to_date))
		);
	}

	private function parse_emp_date($value) {
		$value = trim((string)$value);
		if ($value === '' || strpos($value, '0000-00-00') === 0) {
			return '';
		}
		$timestamp = strtotime($value);
		if ($timestamp === false) {
			return '';
		}
		return date('Y-m-d', $timestamp);
	}

	private function is_employee_inactive($status) {
		$normalized = strtolower(preg_replace('/[\s_]+/', '', (string)$status));
		return ($normalized !== 'active');
	}

	private function normalize_headcount_department($dept) {
		$dept = trim((string)$dept);
		$deptKey = strtolower(preg_replace('/\s+/', ' ', $dept));
		$adminHrGroup = array(
			'hr',
			'recruiter',
			'admin',
			'admin & hr',
			'admin and hr',
			'operations',
			'operation manager',
			'operations manager',
			'accounting',
			'hr / recruiter / operations / accounting'
		);
		if (in_array($deptKey, $adminHrGroup, true)) {
			return 'HR / Recruiter / Operations / Accounting';
		}
		$softwareItGroup = array(
			'software',
			'it',
			'software / it',
			'software/it'
		);
		if (in_array($deptKey, $softwareItGroup, true)) {
			return 'Software / IT';
		}
		return $dept;
	}

	private function build_department_headcount($employees, $periodStart, $periodEnd) {
		$summaryOrder = array(
			'Architectural',
			'MEP',
			'3D Visualization',
			'Structural',
			'2D Auto CAD',
			'Software / IT',
			'HR / Recruiter / Operations / Accounting',
			'Business Development',
			'Management'
		);

		$deptStats = array();
		foreach ($employees as $row) {
			$dept = isset($row->department) ? $this->normalize_headcount_department($row->department) : '';
			if ($dept === '') {
				continue;
			}

			$joinDate = $this->parse_emp_date(isset($row->emp_joining_date) ? $row->emp_joining_date : '');
			if ($joinDate === '') {
				$joinDate = $this->parse_emp_date(isset($row->created_at) ? $row->created_at : '');
			}
			if ($joinDate === '') {
				$joinDate = '1970-01-01';
			}

			$leaveDate = '';
			if ($this->is_employee_inactive(isset($row->status) ? $row->status : '')) {
				$leaveDate = $this->parse_emp_date(isset($row->updated_at) ? $row->updated_at : '');
			}

			if (!isset($deptStats[$dept])) {
				$deptStats[$dept] = array(
					'department' => $dept,
					'beginning' => 0,
					'new_joinees' => 0,
					'left_org' => 0,
					'end_count' => 0
				);
			}

			if ($joinDate < $periodStart && ($leaveDate === '' || $leaveDate >= $periodStart)) {
				$deptStats[$dept]['beginning']++;
			}
			if ($joinDate >= $periodStart && $joinDate <= $periodEnd) {
				$deptStats[$dept]['new_joinees']++;
			}
			if ($leaveDate !== '' && $leaveDate >= $periodStart && $leaveDate <= $periodEnd) {
				$deptStats[$dept]['left_org']++;
			}
			if ($joinDate <= $periodEnd && ($leaveDate === '' || $leaveDate > $periodEnd)) {
				$deptStats[$dept]['end_count']++;
			}
		}

		$emptyRow = function ($deptName) {
			return array(
				'department' => $deptName,
				'beginning' => 0,
				'new_joinees' => 0,
				'left_org' => 0,
				'end_count' => 0
			);
		};

		$rows = array();
		$totals = array('beginning' => 0, 'new_joinees' => 0, 'left_org' => 0, 'end_count' => 0);
		foreach ($summaryOrder as $deptName) {
			$stats = isset($deptStats[$deptName]) ? $deptStats[$deptName] : $emptyRow($deptName);
			$rows[] = $stats;
			$totals['beginning'] += $stats['beginning'];
			$totals['new_joinees'] += $stats['new_joinees'];
			$totals['left_org'] += $stats['left_org'];
			$totals['end_count'] += $stats['end_count'];
			unset($deptStats[$deptName]);
		}

		foreach ($deptStats as $stats) {
			if ($stats['beginning'] === 0 && $stats['new_joinees'] === 0 && $stats['left_org'] === 0 && $stats['end_count'] === 0) {
				continue;
			}
			$rows[] = $stats;
			$totals['beginning'] += $stats['beginning'];
			$totals['new_joinees'] += $stats['new_joinees'];
			$totals['left_org'] += $stats['left_org'];
			$totals['end_count'] += $stats['end_count'];
		}

		return array('rows' => $rows, 'totals' => $totals);
	}

	private function normalize_multi_filter($value) {
		if (is_array($value)) {
			$clean = array();
			foreach ($value as $item) {
				$item = trim((string)$item);
				if ($item === '' || strtolower($item) === 'all') {
					continue;
				}
				$clean[] = $item;
			}
			return array_values(array_unique($clean));
		}
		$value = trim((string)$value);
		if ($value === '' || strtolower($value) === 'all') {
			return array();
		}
		return array($value);
	}




/**********search employee dropdown and get data automatically on screen while typing code********** */


	public function searchEmployeeAjax()
{
    $keyword = trim($this->input->post('keyword'));

    if (empty($keyword)) {
        echo '';
        return;
    }

    $this->db->select('empId, emp_com_id, name, email');
    $this->db->from('employee_details');

    $this->db->group_start();
    $this->db->like('name', $keyword);
    $this->db->or_like('email', $keyword);
    $this->db->or_like('emp_com_id', $keyword);
    $this->db->group_end();

    $this->db->limit(10);

    $query = $this->db->get();

    if ($query->num_rows() > 0) {

        foreach ($query->result() as $row) {

            echo '<div class="employee-item"
                     data-value="'.$row->name.'"
                     style="padding:8px; cursor:pointer; border-bottom:1px solid #eee;">
                    '.$row->name.' | '.$row->emp_com_id.' | '.$row->email.'
                  </div>';
        }

    } else {

    echo '<div style="padding:8px;">No Employee Found</div>';
    }
}

	public function send_headcount_email() {
		header('Content-Type: application/json');

		if (empty($this->session->userdata['logged_in_timesheet'])) {
			echo json_encode(array(
				'success' => false,
				'message' => 'Session expired. Please login again.'
			));
			return;
		}

		$department = $this->normalize_multi_filter($this->input->post('department'));
		$projectManager = $this->normalize_multi_filter($this->input->post('project_manager'));
		$from_year = $this->input->post('from_year');
		$from_month = $this->input->post('from_month');
		$to_year = $this->input->post('to_year');
		$to_month = $this->input->post('to_month');

		$from_date = '';
		$to_date = '';
		if (!empty($from_year) && !empty($from_month)) {
			$from_date = $from_year . '-' . $from_month . '-01';
		}
		if (!empty($to_year) && !empty($to_month)) {
			$to_date = date('Y-m-t', strtotime($to_year . '-' . $to_month . '-01'));
		}

		$headcountPeriod = $this->resolve_headcount_period($from_date, $to_date);
		$headcountEmployees = $this->timesheet_login->getEmployeesForHeadcount(array(
			'department' => $department,
			'project_manager' => $projectManager
		));
		$headcountData = $this->build_department_headcount(
			$headcountEmployees,
			$headcountPeriod['from_date'],
			$headcountPeriod['to_date']
		);

		if (empty($headcountData['rows'])) {
			echo json_encode(array(
				'success' => false,
				'message' => 'No department headcount data available to send.'
			));
			return;
		}

		$emailBody = $this->build_headcount_email_html(
			$headcountData['rows'],
			$headcountData['totals'],
			$headcountPeriod['label']
		);

		$this->load->library('email');
		$emailConfig = array(
			'mailtype' => 'html',
			'charset' => 'utf-8',
			'wordwrap' => true,
			'newline' => "\r\n",
			'crlf' => "\r\n"
		);
		$this->email->initialize($emailConfig);
		$this->email->from('info@elogictech.com', 'eLogic Timesheet');
		$this->email->to('laxmikanth@elogictech.com');
		$this->email->subject('Department Headcount Report - ' . $headcountPeriod['label']);
		$this->email->message($emailBody);
		$sent = @$this->email->send();

		if ($sent) {
			echo json_encode(array(
				'success' => true,
				'message' => 'Headcount report has been emailed to laxmikanth@elogictech.com.'
			));
		} else {
			if (function_exists('log_message')) {
				log_message('error', 'send_headcount_email failed: ' . $this->email->print_debugger(array('headers', 'subject')));
			}
			echo json_encode(array(
				'success' => false,
				'message' => 'Failed to send email. Please contact the software team.'
			));
		}
	}

	private function build_headcount_email_html($rows, $totals, $periodLabel) {
		$border = 'border:1px solid #8aa0b8;';
		$thStyle = $border . ' padding:12px 10px; background:#2c5282; color:#ffffff; font-weight:bold; font-size:14px; font-family:Arial,Helvetica,sans-serif; text-align:center; vertical-align:middle;';
		$tdDeptStyle = $border . ' padding:10px 12px; background:#eefaf6; color:#1a365d; font-size:14px; font-family:Arial,Helvetica,sans-serif; text-align:left; vertical-align:middle;';
		$tdNumStyle = $border . ' padding:10px 12px; background:#eefaf6; color:#1a365d; font-size:14px; font-weight:bold; font-family:Arial,Helvetica,sans-serif; text-align:center; vertical-align:middle;';
		$tfStyle = $border . ' padding:12px 10px; background:#2c5282; color:#ffffff; font-weight:bold; font-size:14px; font-family:Arial,Helvetica,sans-serif; text-align:center; vertical-align:middle;';
		$tfDeptStyle = $border . ' padding:12px 12px; background:#2c5282; color:#ffffff; font-weight:bold; font-size:14px; font-family:Arial,Helvetica,sans-serif; text-align:left; vertical-align:middle;';

		$table = '<table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:collapse; width:100%; max-width:820px; margin:0 auto; font-family:Arial,Helvetica,sans-serif;">';
		$table .= '<thead><tr>';
		$table .= '<th style="' . $thStyle . ' text-align:left;">Department</th>';
		$table .= '<th style="' . $thStyle . '">Beginning of the Month Head Count</th>';
		$table .= '<th style="' . $thStyle . '">New Joinees</th>';
		$table .= '<th style="' . $thStyle . '">Left Org</th>';
		$table .= '<th style="' . $thStyle . '">End of the Month Head count</th>';
		$table .= '</tr></thead><tbody>';

		foreach ($rows as $row) {
			$table .= '<tr>';
			$table .= '<td style="' . $tdDeptStyle . '">' . htmlspecialchars($row['department'], ENT_QUOTES, 'UTF-8') . '</td>';
			$table .= '<td style="' . $tdNumStyle . '">' . (int)$row['beginning'] . '</td>';
			$table .= '<td style="' . $tdNumStyle . '">' . (int)$row['new_joinees'] . '</td>';
			$table .= '<td style="' . $tdNumStyle . '">' . (int)$row['left_org'] . '</td>';
			$table .= '<td style="' . $tdNumStyle . '">' . (int)$row['end_count'] . '</td>';
			$table .= '</tr>';
		}

		$table .= '</tbody><tfoot><tr>';
		$table .= '<th style="' . $tfDeptStyle . '">Total</th>';
		$table .= '<th style="' . $tfStyle . '">' . (int)$totals['beginning'] . '</th>';
		$table .= '<th style="' . $tfStyle . '">' . (int)$totals['new_joinees'] . '</th>';
		$table .= '<th style="' . $tfStyle . '">' . (int)$totals['left_org'] . '</th>';
		$table .= '<th style="' . $tfStyle . '">' . (int)$totals['end_count'] . '</th>';
		$table .= '</tr></tfoot></table>';

		$content = '<p style="margin:0 0 18px 0; font-size:16px; color:#333333; font-family:Arial,Helvetica,sans-serif;">Hi,</p>';
		$content .= '<p style="margin:0 0 22px 0; font-size:15px; color:#444444; line-height:1.6; font-family:Arial,Helvetica,sans-serif;">Please find below the department headcount report for <b>' . htmlspecialchars($periodLabel, ENT_QUOTES, 'UTF-8') . '</b>.</p>';
		$content .= $table;
		$content .= '<p style="margin:14px 0 0 0; text-align:center; font-size:14px; font-weight:bold; color:#2d3748; font-family:Arial,Helvetica,sans-serif;">Period: ' . htmlspecialchars($periodLabel, ENT_QUOTES, 'UTF-8') . '</p>';
		$content .= '<p style="margin:28px 0 0 0; font-size:15px; color:#333333; font-family:Arial,Helvetica,sans-serif;">Best Regards,<br>eLogic Team</p>';

		return '<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Department Headcount Report</title>
</head>
<body style="margin:0; padding:28px 16px; background:#eceff1; font-family:Arial,Helvetica,sans-serif; color:#333333;">
	<div style="margin:0 auto; max-width:860px; background:#ffffff; padding:28px 24px 32px 24px; border:1px solid #dde1e4; border-radius:6px;">
		' . $content . '
	</div>
</body>
</html>';
	}

/**************************************************************************************************************************************/      
	
	
}

