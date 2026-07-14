<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends CI_Controller {

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
		
		$this->load->helper('text');
		
		// Load database		
		$this->load->model('timesheet_login');
		
		$this->load->model('client_model');
		
		$this->load->model('project_model');
        
        $this->load->model('task_model');
		
		
		if(empty($this->session->userdata['logged_in_timesheet'])){
		
			redirect('home/login');
		}
		
    }
	
	public function index(){
		
			// Load page without data - data will be loaded via AJAX
			$this->load->view('projects/projects');
			
	}
	

	/************************akhila code*********************/
	
	/**
	 * AJAX endpoint for loading projects with pagination and sorting
	 * 
	 
	 */
// 	public function getProjectsAjax(){

//     $page = $this->input->post('page') ?: 1;
//     $limit = $this->input->post('limit') ?: 10;
//     $search = $this->input->post('search') ?: '';

//     $department = $this->input->post('department');
//     $manager = $this->input->post('manager');
//  $from_year = $this->input->post('from_year');
// $from_month = $this->input->post('from_month');
// $to_year = $this->input->post('to_year');
// $to_month = $this->input->post('to_month');
//     $status = $this->input->post('status');

//     $sort_by = $this->input->post('sort_by') ?: 'project_number';
//     $sort_order = $this->input->post('sort_order') ?: 'desc';

//     $offset = ($page - 1) * $limit;

//     // ✅ TOTAL RECORDS
//     $totalRecords = $this->project_model->getTotalProjects(
//         $search, $department, $manager, $from_date, $to_date, $status
//     );

//     // ✅ PAGINATED DATA
//     $projects = $this->project_model->getProjectsPaginated(
//         $limit, $offset, $search, $sort_by, $sort_order,
//         $department, $manager, $from_date, $to_date, $status
//     );

//     // ✅ STATUS COUNTS (IMPORTANT FIX)
// $processCount = $this->project_model->getTotalProjects(
//     $search, $department, $manager, $from_date, $to_date, 'Process'
// );

// $holdCount = $this->project_model->getTotalProjects(
//     $search, $department, $manager, $from_date, $to_date, 'On Hold'
// );

// $closedCount = $this->project_model->getTotalProjects(
//     $search, $department, $manager, $from_date, $to_date, 'Closed'
// );

//     // ✅ RESPONSE
//     echo json_encode([
//         'success' => true,
//         'data' => $projects,
//         'counts' => [
//             'process' => $processCount,
//             'hold' => $holdCount,
//             'closed' => $closedCount
//         ],
//         'pagination' => [
//             'currentPage' => (int)$page,
//             'totalPages' => ceil($totalRecords / $limit),
//             'totalRecords' => $totalRecords,
//             'startRecord' => $offset + 1,
//             'endRecord' => min($offset + $limit, $totalRecords)
//         ]
//     ]);
// }	
	


public function getProjectsAjax()
{
    $page = $this->input->post('page') ?: 1;
    $limit = $this->input->post('limit') ?: 10;
    $search = $this->input->post('search') ?: '';

    $department = $this->input->post('department');
    $manager = $this->input->post('manager');

    // ✅ YEAR MONTH FILTERS
    $from_year = $this->input->post('from_year');
    $from_month = $this->input->post('from_month');
    $to_year = $this->input->post('to_year');
    $to_month = $this->input->post('to_month');

    $status = $this->input->post('status');
$billing_type = $this->input->post('billing_type');
	
	if ($status == 'All') {
    $status = '';
}

    $sort_by = $this->input->post('sort_by') ?: 'project_number';
    $sort_order = $this->input->post('sort_order') ?: 'desc';

    $offset = ($page - 1) * $limit;

    // ✅ TOTAL RECORDS
    $totalRecords = $this->project_model->getTotalProjects(
        $search,
        $department,
        $manager,
        $from_year,
        $from_month,
        $to_year,
        $to_month,
        $status,
        $billing_type
    );

    // ✅ PROJECT LIST
    $projects = $this->project_model->getProjectsPaginated(
        $limit,
        $offset,
        $search,
        $sort_by,
        $sort_order,
        $department,
        $manager,
        $from_year,
        $from_month,
        $to_year,
        $to_month,
        $status,
        $billing_type
    );


    // ✅ STATUS COUNTS
    // ✅ STATUS COUNTS WITHOUT GENERAL PROJECTS
$statusCounts = $this->project_model->getStatusCountsWithoutGeneral(
    $search,
    $department,
    $manager
);

$processCount = !empty($statusCounts->process) ? $statusCounts->process : 0;
$holdCount = !empty($statusCounts->hold) ? $statusCounts->hold : 0;
$closedCount = !empty($statusCounts->closed) ? $statusCounts->closed : 0;



    echo json_encode([
        'success' => true,
        'data' => $projects,
        'counts' => [
            'process' => $processCount,
            'hold' => $holdCount,
            'closed' => $closedCount
        ],
        'pagination' => [
            'currentPage' => (int)$page,
            'totalPages' => ceil($totalRecords / $limit),
            'totalRecords' => $totalRecords,
            'startRecord' => $offset + 1,
            'endRecord' => min($offset + $limit, $totalRecords)
        ]
    ]);
}

	public function add($projct_Id = NULL){
	
	   if(empty($projct_Id)) : 
			
			 $this->load->view('projects/add_project');
			 
		else:
			
			 $data['updateProject'] = $this->project_model->getProjects($projct_Id);
	
		     $this->load->view('projects/add_project' , $data);
					
		endif;	 			
	 
	}

	public function projectInformaton($projct_Id){

			if(!empty($projct_Id)){

				$data['updateProject'] = $this->project_model->getProjects($projct_Id);
	
		     	$this->load->view('projects/view_project' , $data);
			}

	}
	
	public function addproject(){ // Adding new Client function. 	
			
		$this->form_validation->set_error_delimiters('<label class="error">', '</label>');
	
	    $this->form_validation->set_rules('project_name', 'Project name already exit. Please try another project', 'required|trim|callback_exists_projects');
        
        $this->form_validation->set_rules('project_number', 'Project number already exit in another project. Please try to different number', 'required|trim|callback_exists_project_number');
		
		  //$this->form_validation->set_rules('task_name', 'Task name already exit. Please try another task name', 'required|trim|callback_exists_tasks');
		
		if ($this->form_validation->run() == FALSE) {
	
			$this->load->view('projects/add_project');
			
	    }else{
            
           
                
                if(!empty($this->input->post('man_days'))){
                    
                     $man_days = $this->input->post('man_days');
                    
                }else{
                    
                     $man_days = '';
                }

				$p_manager_name = $this->input->post('p_manager');

				// Get logged-in user's employee id & username
				$logged_in_empId = $this->session->userdata['logged_in_timesheet']['empId'];
				$logged_in_username = $this->session->userdata['logged_in_timesheet']['username'];

				// Fetch empId of p_manager based on name from employee table
				$this->db->select('empId, username');
				$this->db->where('name', $p_manager_name);
				$p_manager_query = $this->db->get('employee_details');
				$p_manager_row = $p_manager_query->row();

				if (!empty($p_manager_row)) {
					// If login user and p_manager is same, use logged-in employee ID, else use p_manager's empId
					if ($logged_in_username == $p_manager_row->username) {
						$who_allocated_project_empId = $logged_in_empId;
					} else {
						$who_allocated_project_empId = $p_manager_row->empId;
					}
				} else {
					// fallback: store as empty or handle error if p_manager not found
					$who_allocated_project_empId = $this->session->userdata['logged_in_timesheet']['empId'];
				}

				//echo 'kanth---'.$who_allocated_project_empId; exit;
						
			$data = array(
			'client_Id' 				 => $this->input->post('client_Id'),
			'empId'						 => $who_allocated_project_empId,
			'who_allocated_project_empId' => $this->session->userdata['logged_in_timesheet']['empId'],
            'project_number' 			 => $this->input->post('project_number'),    
			'project_name' 				 => $this->input->post('project_name'),
			'city' 				 		 => $this->input->post('city'),
			'state' 					 => $this->input->post('state'),
			'country' 					 => $this->input->post('country'),
			'pc_code' 				 	 => $this->input->post('pc_code'),
			'p_manager' 				 => $this->input->post('p_manager'),			
            'project_type' 				 => $this->input->post('project_type'),    
			'project_start_date' 		 => $this->input->post('project_start_date'),
			'project_end_date'			 => $this->input->post('project_end_date'),
			'man_days'		             => $man_days, 
			'estimated_hours'			 => $this->input->post('estimated_hours'),
			'notif_hours'				 => $this->input->post('notif_hours'),
			'team_members'				 => implode(',', $this->input->post('team_members')),
			'project_type'				 => $this->input->post('project_type'),
			'status'				 	 => $this->input->post('status'),
			'resource_billability'		 => $this->input->post('resource_billability'),
			'total_site_area'			 => $this->input->post('total_site_area'),
			'construction_technology'	 => $this->input->post('construction_technology'),
			'building_typology'	 		 => $this->input->post('building_typology'),
			'scope_category'	 		 => $this->input->post('scope_category'),
			'technology_category'	 	 => $this->input->post('technology_category'),
			'project_desc' 				 => $this->input->post('project_desc'),
			'link_to_project'			 => $this->input->post('link_to_project'),
			'project_contact_name'	 	 => $this->input->post('project_contact_name'),
			'project_email_id'	 		 => $this->input->post('project_email_id'),
			'project_contact_number'	 => $this->input->post('project_contact_number'),
			'created_at'    			 => date('Y-m-d H:i:s'),
			'updated_at' 		 		 => date('Y-m-d H:i:s')
			);		
	
	     $this->project_model->add_project($data);
            
         $this->cloneproject(); // Call myFunction()    
		
		 redirect('projects');
		
	   }
	
	}
	
	
	public function updateproject(){ // Adding new Client function. 	
			
		
		$projct_Id = $this->input->post('project_id');
		
		$this->form_validation->set_error_delimiters('<label class="error">', '</label>');
	
	    $this->form_validation->set_rules('project_name', 'Project name already exit. Please try another project', 'required|trim|callback_exists_projects');
        
        //$this->form_validation->set_rules('project_number', 'Project number already exit in another project. Please try to different number', 'required|trim|callback_exists_project_number');
        
        
        if(!empty($this->input->post('man_days'))){

             $man_days = $this->input->post('man_days');

        }else{

             $man_days = '';
        }


		       $p_manager_name = $this->input->post('p_manager');

				// Get logged-in user's employee id & username
				$logged_in_empId = $this->session->userdata['logged_in_timesheet']['empId'];
				$logged_in_username = $this->session->userdata['logged_in_timesheet']['username'];

				// Fetch empId of p_manager based on name from employee table
				$this->db->select('empId, username');
				$this->db->where('name', $p_manager_name);
				$p_manager_query = $this->db->get('employee_details');
				$p_manager_row = $p_manager_query->row();

				if (!empty($p_manager_row)) {
					// If login user and p_manager is same, use logged-in employee ID, else use p_manager's empId
					if ($logged_in_username == $p_manager_row->username) {
						$who_allocated_project_empId = $logged_in_empId;
					} else {
						$who_allocated_project_empId = $p_manager_row->empId;
					}
				} else {
					// fallback: store as empty or handle error if p_manager not found
					$who_allocated_project_empId = $this->session->userdata['logged_in_timesheet']['empId'];
				}

				// 'kanth---'.$who_allocated_project_empId; exit;

		
		if ($this->form_validation->run() == FALSE) {
	
			$this->load->view('projects/add_project');
			
			$data = array(
				'client_Id' 				 => $this->input->post('client_Id'),
				'empId'						 => $who_allocated_project_empId,
				'who_allocated_project_empId' => $this->session->userdata['logged_in_timesheet']['empId'],
				'city' 				 		 => $this->input->post('city'),
				'state' 					 => $this->input->post('state'),
				'country' 					 => $this->input->post('country'),
				'pc_code' 				 	 => $this->input->post('pc_code'),
				'p_manager' 				 => $this->input->post('p_manager'),			
				'project_type' 				 => $this->input->post('project_type'),    
				'project_start_date' 		 => $this->input->post('project_start_date'),
				'project_end_date'			 => $this->input->post('project_end_date'),
				'man_days'		             => $man_days, 
				'estimated_hours'			 => $this->input->post('estimated_hours'),
				'notif_hours'				 => $this->input->post('notif_hours'),
				'team_members'				 => implode(',', $this->input->post('team_members')),
				'project_type'				 => $this->input->post('project_type'),
				'status'				 	 => $this->input->post('status'),
				'resource_billability'		 => $this->input->post('resource_billability'),
				'total_site_area'			 => $this->input->post('total_site_area'),
				'construction_technology'	 => $this->input->post('construction_technology'),
				'building_typology'	 		 => $this->input->post('building_typology'),
				'scope_category'	 		 => $this->input->post('scope_category'),
				'technology_category'	 	 => $this->input->post('technology_category'),
				'project_desc' 				 => $this->input->post('project_desc'),
				'link_to_project'			 => $this->input->post('link_to_project'),
				'project_contact_name'	 	 => $this->input->post('project_contact_name'),
				'project_email_id'	 		 => $this->input->post('project_email_id'),
				'project_contact_number'	 => $this->input->post('project_contact_number'),
				'created_at'    			 => date('Y-m-d H:i:s'),
				'updated_at' 		 		 => date('Y-m-d H:i:s')
				);	
	
	     $this->project_model->update_project($data , $projct_Id);
        
        if($this->input->post('status') == 'Closed'){     
            
            $this->task_model->update_task_status($projct_Id); //   //update task status based on project.
            
        }
		 redirect('projects');
			
	    }else{
						
			$data = array(
				'client_Id' 				 => $this->input->post('client_Id'),
				'empId'						 => $who_allocated_project_empId,
				'who_allocated_project_empId' => $this->session->userdata['logged_in_timesheet']['empId'],
				'project_name' 				 => $this->input->post('project_name'),
				'city' 				 		 => $this->input->post('city'),
				'state' 					 => $this->input->post('state'),
				'country' 					 => $this->input->post('country'),
				'pc_code' 				 	 => $this->input->post('pc_code'),
				'p_manager' 				 => $this->input->post('p_manager'),			
				'project_type' 				 => $this->input->post('project_type'),    
				'project_start_date' 		 => $this->input->post('project_start_date'),
				'project_end_date'			 => $this->input->post('project_end_date'),
				'man_days'		             => $man_days, 
				'estimated_hours'			 => $this->input->post('estimated_hours'),
				'notif_hours'				 => $this->input->post('notif_hours'),
				'team_members'				 => implode(',', $this->input->post('team_members')),
				'project_type'				 => $this->input->post('project_type'),
				'status'				 	 => $this->input->post('status'),
				'resource_billability'		 => $this->input->post('resource_billability'),
				'total_site_area'			 => $this->input->post('total_site_area'),
				'construction_technology'	 => $this->input->post('construction_technology'),
				'building_typology'	 		 => $this->input->post('building_typology'),
				'scope_category'	 		 => $this->input->post('scope_category'),
				'technology_category'	 	 => $this->input->post('technology_category'),
				'project_desc' 				 => $this->input->post('project_desc'),
				'link_to_project'			 => $this->input->post('link_to_project'),
				'project_contact_name'	 	 => $this->input->post('project_contact_name'),
				'project_email_id'	 		 => $this->input->post('project_email_id'),
				'project_contact_number'	 => $this->input->post('project_contact_number'),
				'created_at'    			 => date('Y-m-d H:i:s'),
				'updated_at' 		 		 => date('Y-m-d H:i:s')
				);
	
	     $this->project_model->update_project($data , $projct_Id);
            
        if($this->input->post('status') == 'Closed'){     
            
            $this->task_model->update_task_status($projct_Id); //   //update task status based on project.
            
        }    
		
		 redirect('projects');
		
	   }
	
	}

  public function delete(){
  
        $project_Id  = $this->input->post('project_Id');
		   
			if(!empty($project_Id)):
			
				$del = $this->project_model->delete_project($project_Id);
				
			endif;	
  
  }  
  
  public function getRecentProjects(){  //Get Recent Clients Angular js funciton  
  
    	$recentProjectInfo = $this->project_model->recentProjects();
		
		echo json_encode($recentProjectInfo);
  
  }
	
  #uniqueness of task based on client and projects
    function exists_projects($str){ #uniqueness of Car Model
	
        $client_Id = $this->input->post('client_Id');
		
		$project_name = $this->input->post('project_name');
		
		$query = $this->db->get_where('project_details',array('project_name'=>$project_name,'client_Id'=>$client_Id));
	
		$countClientProject = $query->num_rows(); 
		
        if ($countClientProject  == 0){
		
            return TRUE;
			
        }else{
		
            $this->form_validation->set_message('exists_projects', 'Project name already exit particular client. Please try another project!');
            
			 return FALSE;
        }
    }
        
    function exists_project_number($str){        
        
        $project_number = $this->input->post('project_number');
		
		$project_name = $this->input->post('project_name');
		
		$query = $this->db->get_where('project_details',array('project_name'=>$project_name,'project_number'=>$project_number));
	
		$countClientProject = $query->num_rows(); 
		
        if ($countClientProject  == 0){
		
            return TRUE;
			
        }else{
		
            $this->form_validation->set_message('exists_project_number', 'Project number already exit in another project. Please try to different number like (2023-XXX)', 'required|trim|callback_exists_project_number!');
            
			 return FALSE;
        }
    
    }
    
    /******************************************** Clone Project *********************************************************************/
    
     public function cloneproject(){
         
          $cloneprojectId = $this->input->get('project_Id');
		 
		  $cloneVal = $this->input->get('cloneVal');
         
          $getCloneProjectData = $this->project_model->getProjects($cloneprojectId);
         
          if(!empty($getCloneProjectData)){
			  
			  if(!empty($cloneVal == 'passVal')):
			  
			  		$projectNameCloneVal = $getCloneProjectData[0]->project_name;
			  
			  else:
			  
			  		$projectNameCloneVal = $getCloneProjectData[0]->project_name.' - (General)';
			  
			  endif; 


			  $p_manager_name = $getCloneProjectData[0]->p_manager;

			  // Get logged-in user's employee id & username
			  $logged_in_empId = $this->session->userdata['logged_in_timesheet']['empId'];
			  $logged_in_username = $this->session->userdata['logged_in_timesheet']['username'];

			  // Fetch empId of p_manager based on name from employee table
			  $this->db->select('empId, username');
			  $this->db->where('name', $p_manager_name);
			  $p_manager_query = $this->db->get('employee_details');
			  $p_manager_row = $p_manager_query->row();

			  if (!empty($p_manager_row)) {
				  // If login user and p_manager is same, use logged-in employee ID, else use p_manager's empId
				  if ($logged_in_username == $p_manager_row->username) {
					  $who_allocated_project_empId = $logged_in_empId;
				  } else {
					  $who_allocated_project_empId = $p_manager_row->empId;
				  }
			  } else {
				  // fallback: store as empty or handle error if p_manager not found
				  $who_allocated_project_empId = $this->session->userdata['logged_in_timesheet']['empId'];
			  }

			  // 'kanth---'.$who_allocated_project_empId; exit;
              
              
             $data = array(
                    'client_Id' 				 => $getCloneProjectData[0]->client_Id,
                    'empId'						 => $who_allocated_project_empId,
				    'who_allocated_project_empId' => $getCloneProjectData[0]->empId,
                    'project_number' 			 => $getCloneProjectData[0]->project_number,   
                    'project_name' 				 => $projectNameCloneVal, 
					'city' 						 => $getCloneProjectData[0]->city,
					'state' 					 => $getCloneProjectData[0]->state,
					'country' 				 	 => $getCloneProjectData[0]->country,
					'pc_code' 				 	 => $getCloneProjectData[0]->pc_code,
					'p_manager' 				 => $getCloneProjectData[0]->p_manager,
					'p_manager' 				 => $getCloneProjectData[0]->p_manager,
					'project_start_date' 		 => $getCloneProjectData[0]->project_start_date,
					'project_end_date' 			 => $getCloneProjectData[0]->project_end_date,
					'man_days' 					 => $getCloneProjectData[0]->man_days,
					'estimated_hours' 			 => $getCloneProjectData[0]->estimated_hours,
					'notif_hours' 				 => $getCloneProjectData[0]->notif_hours,
					'team_members' 				 => $getCloneProjectData[0]->team_members,
					'project_type'				 => $getCloneProjectData[0]->project_type,
					'status' 				 	 => $getCloneProjectData[0]->status,
                    'resource_billability' 		 => $getCloneProjectData[0]->resource_billability,
                    'total_site_area' 			 => $getCloneProjectData[0]->total_site_area,    
                    'construction_technology' 	 => $getCloneProjectData[0]->construction_technology,
                    'building_typology'			 => $getCloneProjectData[0]->building_typology,
                    'scope_category'			 => $getCloneProjectData[0]->scope_category,
                    'technology_category'		 => $getCloneProjectData[0]->technology_category,
                    'project_desc'		         => $getCloneProjectData[0]->project_desc,
					'link_to_project'		     => $getCloneProjectData[0]->link_to_project,  
					'project_contact_name'		 => $getCloneProjectData[0]->project_contact_name,  
					'project_email_id'		     => $getCloneProjectData[0]->project_email_id,
					'project_contact_number'	 => $getCloneProjectData[0]->project_contact_number,     
					'created_at'    			 => date('Y-m-d H:i:s'),
                    'updated_at' 		 		 => date('Y-m-d H:i:s')
			     );
              
            $this->project_model->add_project($data);
		
		 	echo "<script>alert('You have Successuly clone the record!!!!!');window.location.href='/elogic_timesheet/projects';</script>";  
			  
          }        
     }
    
	 /************************************************ Clone Project *****************************************************************/


	 public function getClientContactInformaiton(){
	
		if (isset($_GET['term'])){
			$q = strtolower($_GET['term']);
			$this->project_model->getAutoContactProjectDetails($q);
		  }
	 }


	 public function getListOfProjects(){

		if (isset($_GET['term'])){
			$q = strtolower($_GET['term']);
			$this->project_model->get_project_name_suggestions($q);
		  }

	 }

	 public function getcontactSetionDetails(){


		$getProContactName = $this->input->post("project_contact_name");

				//echo '<pre>'; print_r($_REQUEST);

				if(!empty($getProContactName)){
		  
					$getContactInformation  = $this->db->select('project_contact_name,project_email_id,project_contact_number')
										->from('service_agreement_details')->where('project_contact_name',$getProContactName)->get()->result();
				
										
				if(!empty($getContactInformation[0]->project_email_id)):	

				   echo trim($getContactInformation[0]->project_email_id).'__'.trim($getContactInformation[0]->project_contact_number);
				   
				endif;



	            }



}


/***********************************************************akhila code Project Report Master search ************************************************ */

public function project_report_information(){

    $data = array();

    // If no POST → just load empty page (refresh case)
    if(empty($_POST)){
        $data['getProjects'] = [];
        $this->load->view('projects/project_reports_automation_search', $data);
        return;
    }

    // ✅ Get form values
    $department   = $this->input->post('department');
    $manager      = $this->input->post('manager_name');
    $search_text  = $this->input->post('search_text');
    $form_date    = $this->input->post('form_date');
    $to_date      = $this->input->post('to_date');
    $filter_type  = $this->input->post('filter_type'); // ✅ THIS IS CORRECT
	

    // Convert to array if needed
    if(!is_array($department)) {
        $department = !empty($department) ? [$department] : [];
    }

    if(!is_array($manager)) {
        $manager = !empty($manager) ? [$manager] : [];
    }

    // ✅ PASS CORRECT VARIABLE (MAIN FIX)
    $data['getProjects'] = $this->Project_model->getReportMasterProjects(
        $department,
        $manager,
        $form_date,
        $to_date,
        $filter_type,   // ✅ FIXED HERE (instead of $status)
        $search_text    // ✅ also pass search text if needed
    );

    // Load view
    $this->load->view('projects/project_reports_automation_search', $data);
}





public function downloadProjectMasterReport()
{
    $this->load->database();

    $this->db->select("
        p.project_type,
        p.project_number,
        p.project_name,
        p.city,
        p.state,
        p.country,
        p.pc_code,
        c.client_name,
        e.name AS project_manager,
        p.project_start_date,
        p.project_end_date,
        p.man_days,
        p.team_members,
        p.link_to_project,
        p.status,
        p.project_desc,
        p.total_site_area,
        p.total_building_area,
        p.construction_technology,
        p.building_typology,
        p.scope_category,
        p.technology_category,
        p.project_contact_name,
        p.project_email_id,
        p.project_contact_number
    ");

    $this->db->from('project_details p');

    $this->db->join('client_details c', 'c.client_Id = p.client_Id', 'left');

    // IMPORTANT FIX
    $this->db->join('employee_details e', 'e.empId = p.empId', 'left');

    $projects = $this->db->get()->result_array();

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Project_Master_Report.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<table border='1'>";

    echo "<tr>
        <th>Department</th>
        <th>Project Number</th>
        <th>Project Name</th>
        <th>City</th>
        <th>State</th>
        <th>Country</th>
        <th>Client Project No</th>
        <th>Client Name</th>
        <th>Project Manager</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Man Days</th>
        <th>Team Members</th>
        <th>Project Link</th>
        <th>Status</th>
        <th>Comments</th>
        <th>Total Site Area</th>
        <th>Built-up Area</th>
        <th>Construction Technology</th>
        <th>Building Typology</th>
        <th>Scope Category</th>
        <th>Technology Category</th>
        <th>Contact Info</th>
    </tr>";

    foreach ($projects as $row) {

        echo "<tr>";

        echo "<td>".(!empty($row['project_type']) ? $row['project_type'] : '-')."</td>";

        echo "<td>".(!empty($row['project_number']) ? $row['project_number'] : '-')."</td>";

        echo "<td>".(!empty($row['project_name']) ? $row['project_name'] : '-')."</td>";

        echo "<td>".(!empty($row['city']) ? $row['city'] : '-')."</td>";

        echo "<td>".(!empty($row['state']) ? $row['state'] : '-')."</td>";

        echo "<td>".(!empty($row['country']) ? $row['country'] : '-')."</td>";

        echo "<td>".(!empty($row['pc_code']) ? $row['pc_code'] : '-')."</td>";

        echo "<td>".(!empty($row['client_name']) ? $row['client_name'] : '-')."</td>";

        echo "<td>".(!empty($row['project_manager']) ? $row['project_manager'] : '-')."</td>";

        echo "<td>".(!empty($row['project_start_date']) ? $row['project_start_date'] : '-')."</td>";

        echo "<td>".(!empty($row['project_end_date']) ? $row['project_end_date'] : '-')."</td>";

        echo "<td>".(!empty($row['man_days']) ? $row['man_days'] : '-')."</td>";

        echo "<td>".(!empty($row['team_members']) ? $row['team_members'] : '-')."</td>";

        echo "<td>".(!empty($row['link_to_project']) ? $row['link_to_project'] : '-')."</td>";

        echo "<td>".(!empty($row['status']) ? $row['status'] : '-')."</td>";

        echo "<td>".(!empty($row['project_desc']) ? $row['project_desc'] : '-')."</td>";

        echo "<td>".(!empty($row['total_site_area']) ? $row['total_site_area'] : '-')."</td>";

        echo "<td>".(!empty($row['total_building_area']) ? $row['total_building_area'] : '-')."</td>";

        echo "<td>".(!empty($row['construction_technology']) ? $row['construction_technology'] : '-')."</td>";

        echo "<td>".(!empty($row['building_typology']) ? $row['building_typology'] : '-')."</td>";

        echo "<td>".(!empty($row['scope_category']) ? $row['scope_category'] : '-')."</td>";

        echo "<td>".(!empty($row['technology_category']) ? $row['technology_category'] : '-')."</td>";

        $contactInfo = '';

        if (!empty($row['project_contact_name'])) {
            $contactInfo .= $row['project_contact_name'];
        }

        if (!empty($row['project_email_id'])) {
            $contactInfo .= ' , ' . $row['project_email_id'];
        }

        if (!empty($row['project_contact_number'])) {
            $contactInfo .= ' , ' . $row['project_contact_number'];
        }

        echo "<td>".(!empty($contactInfo) ? $contactInfo : '-')."</td>";

        echo "</tr>";
    }

    echo "</table>";
    exit;
}




public function getProjectSuggestions()
{
    $term = $this->input->get('term');

    $this->db->select('
        p.project_name,
        p.project_number,
        c.client_name
    ');

    $this->db->from('project_details p');
    $this->db->join('client_details c', 'c.client_Id = p.client_Id', 'left');

    $this->db->group_start();
    $this->db->like('p.project_name', $term);
    $this->db->or_like('p.project_number', $term);
    $this->db->or_like('c.client_name', $term);
    $this->db->group_end();

    $this->db->limit(20);

    $query = $this->db->get();

    if($query->num_rows() > 0){

        $shown = [];

        foreach($query->result() as $row){

            if(!empty($row->project_name) && !in_array($row->project_name, $shown)){
                echo '<div class="suggestion-item">'.$row->project_name.'</div>';
                $shown[] = $row->project_name;
            }

            if(!empty($row->project_number) && !in_array($row->project_number, $shown)){
                echo '<div class="suggestion-item">'.$row->project_number.'</div>';
                $shown[] = $row->project_number;
            }

            if(!empty($row->client_name) && !in_array($row->client_name, $shown)){
                echo '<div class="suggestion-item">'.$row->client_name.'</div>';
                $shown[] = $row->client_name;
            }
        }
    }
}




public function downloadExcel()
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $search = $this->input->get('search');
    $department = json_decode($this->input->get('department'), true);
    $manager = json_decode($this->input->get('manager'), true);
    $from_year = $this->input->get('from_year');
    $from_month = $this->input->get('from_month');
    $to_year = $this->input->get('to_year');
    $to_month = $this->input->get('to_month');
    $status = $this->input->get('status');

    $this->db->select('
        p.project_type,
        c.client_name,
        p.project_name,
        p.project_number,
        e.name as manager_name,
        p.man_days,
        p.status,
        p.project_start_date,
        p.project_end_date
    ');

    $this->db->from('project_details p');
    $this->db->join('client_details c', 'c.client_Id = p.client_Id', 'left');
    $this->db->join('employee_details e', 'e.empId = p.empId', 'left');

    // ==========================
    // DEFAULT SCREEN FILTERS
    // ==========================

    $this->db->where('p.status IS NOT NULL', null, false);
    $this->db->where('p.status !=', '');

    $this->db->where("LOWER(p.project_type) NOT LIKE '%general%'", null, false);

    $this->db->where("LOWER(p.project_name) NOT LIKE '%(general)%'", null, false);
    $this->db->where("LOWER(p.project_name) NOT LIKE '%- general%'", null, false);
    $this->db->where("LOWER(p.project_name) NOT LIKE '%general%'", null, false);
    $this->db->where("LOWER(p.project_name) NOT LIKE '%test%'", null, false);
    $this->db->where("LOWER(p.project_name) NOT LIKE '%trail project%'", null, false);
    $this->db->where("LOWER(p.project_name) NOT LIKE '%client calls%'", null, false);
    $this->db->where("LOWER(p.project_name) NOT LIKE '%elogic%'", null, false);

    $this->db->where("LOWER(c.client_name) NOT LIKE '%client calls%'", null, false);
    $this->db->where("LOWER(c.client_name) NOT LIKE '%elogic%'", null, false);
    $this->db->where("LOWER(c.client_name) NOT LIKE '%elogic solutions%'", null, false);

    $excludedClients = [
        'elogic (ather)',
        'elogic points cloud training',
        'elogicsolutions(raghava)',
        'elogictech solutions'
    ];

    foreach ($excludedClients as $client) {
        $this->db->where("LOWER(c.client_name) !=", strtolower($client));
    }

    // ==========================
    // SEARCH FILTER
    // ==========================
    if (!empty($search)) {
        $this->db->group_start();
        $this->db->like('p.project_name', $search);
        $this->db->or_like('p.project_number', $search);
        $this->db->or_like('c.client_name', $search);
        $this->db->group_end();
    }

    // ==========================
    // DEPARTMENT FILTER
    // ==========================
    if (!empty($department) && is_array($department) && !in_array('all', $department)) {
        $this->db->where_in('p.project_type', $department);
    }

    // ==========================
    // MANAGER FILTER
    // ==========================
    if (!empty($manager) && is_array($manager)) {
        $this->db->where_in('p.empId', $manager);
    }

    // ==========================
    // DATE FILTER
    // ==========================
    if (!empty($from_year) && !empty($from_month)) {
        $fromDate = $from_year . '-' . $from_month . '-01';
        $this->db->where('p.project_start_date >=', $fromDate);
    }

    if (!empty($to_year) && !empty($to_month)) {
        $toDate = date('Y-m-t', strtotime($to_year . '-' . $to_month . '-01'));
        $this->db->where('p.project_end_date <=', $toDate);
    }

    // ==========================
    // STATUS FILTER
    // ==========================
    if (!empty($status)) {

        $status = strtolower(trim($status));

        if ($status == 'process') {
            $this->db->group_start();
            $this->db->where_in('LOWER(p.status)', ['process', 'in process', 'in_process']);
            $this->db->group_end();
        }
        elseif ($status == 'closed') {
            $this->db->where('LOWER(p.status)', 'closed');
        }
        elseif ($status == 'on hold') {
            $this->db->group_start();
            $this->db->where_in('LOWER(p.status)', ['on hold', 'on_hold']);
            $this->db->group_end();
        }
    }

    // ==========================
    // SORTING
    // ==========================

    $sort_by = trim($this->input->get('sort_by'));
    $sort_order = strtolower(trim($this->input->get('sort_order')));

    $allowed_columns = [
        'project_type'   => 'p.project_type',
        'client_name'    => 'c.client_name',
        'project_name'   => 'p.project_name',
        'project_number' => 'p.project_number',
        'name'           => 'e.name',
        'man_days'       => 'p.man_days',
        'status'         => 'p.status'
    ];

    if ($sort_order != 'asc' && $sort_order != 'desc') {
        $sort_order = 'desc';
    }

    if (!empty($sort_by) && isset($allowed_columns[$sort_by])) {
        $this->db->order_by($allowed_columns[$sort_by], $sort_order);
    } else {
        $this->db->order_by('p.project_Id', 'DESC');
    }

    // IMPORTANT MISSING LINE
    $query = $this->db->get();

    // ==========================
    // DOWNLOAD HEADERS
    // ==========================

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=filtered_projects.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "Department\tClient\tProject Name\tProject Number\tProject Manager\tBilling Type\tStatus\tStart Date\tEnd Date\n";

    foreach ($query->result() as $row) {

        echo $row->project_type . "\t" .
             $row->client_name . "\t" .
             $row->project_name . "\t" .
             $row->project_number . "\t" .
             $row->manager_name . "\t" .
             $row->man_days . "\t" .
             $row->status . "\t" .
             $row->project_start_date . "\t" .
             $row->project_end_date . "\n";
    }

    exit;
}



/*********************************************************** Project Report Master search ************************************************ */
    
/*********************************** Project master status update feature function below *************************************************/

public function update_project_master_status(){

	$project_status_update_id 	 	 = $this->input->post('project_status_update_id');
	$status 			 		 = $this->input->post('status');

	
	if(!empty($project_status_update_id)):
	
		$updateStatus = $this->project_model->update_project_master_model_query($project_status_update_id,$status);
		//redirect('empreports/pmreportlogs');	
	 
	  endif;	

}

/************************************************************  Project master status update feature function END *************************************/    



}
