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

		$summaryOrder = function_exists('ts_department_summary_order') ? ts_department_summary_order() : array('Architectural','Structural','3D Visualization','2D Auto CAD','MEP','Software','IT','Operations','Marketing','Accounting','Business Development');
		$deptCounts = array();
		$totalCount = 0;
		foreach ($data['getEmployees'] as $row) {
			$dept = isset($row->department) ? trim((string)$row->department) : '';
			if ($dept === '') { continue; }
			$deptKey = strtolower(preg_replace('/\s+/', ' ', $dept));
			if ($deptKey === 'operations' || $deptKey === 'operation manager' || $deptKey === 'operations manager') {
				$dept = 'Operations';
			}
			if (!isset($deptCounts[$dept])) {
				$deptCounts[$dept] = 0;
			}
			$deptCounts[$dept]++;
			$totalCount++;
		}
		$data['departmentSummary'] = $deptCounts;
		$data['departmentSummaryOrder'] = $summaryOrder;
		$data['departmentTotalCount'] = $totalCount;
	if ($this->input->is_ajax_request()) {
    $this->load->view('employee/employee_table_ajax', $data);
} else {
    $this->load->view('employee/employees_report_automation_search', $data);
}
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

/**************************************************************************************************************************************/      
	
	
}

