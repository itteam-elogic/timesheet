<?php
/**
 * eLogivc Admin Panel for Codeigniter 
 * Author: Laxmikanth 
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Project_Model extends CI_Model {

public function __construct() {
    parent::__construct();
    $this->load->model('Project_model');
}

 

	// Read data using username and password
	
	
	public function add_project($data){  // Add Client Model 
	
	  if($data):
	   
	   		 $this->db->insert('project_details', $data);	
	   
	   endif;
	
	
	}
	
	public function getProjects($projct_Id = NULL){
	
            //Fetch departments along with client models and their associated project 
			$getDepartmentCall = $this->client_model->departmentInsights();
					 
		if(empty($projct_Id)):		
			
			if(in_array($this->session->userdata['logged_in_timesheet']['user_type'], array('manager','business_head'))){
			
		$logedInUser = $this->session->userdata['logged_in_timesheet']['empId'];
			
			$projectQ = $this->db->select('p.*,c.client_Id,c.client_name,c.department,e.name,e.empId')
						->from('project_details as p')
						->join('client_details as c ', 'p.client_Id = c.client_Id', 'left')
						->join('employee_details as e ', 'p.empId = e.empId', 'left')
						//->where_in('c.department', explode(',', $getDepartmentCall))
						 ->where_in('p.project_type', explode(',', $getDepartmentCall))
						//->where('p.empId',$logedInUser)
						->order_by('project_number' , 'desc')->get();
																
		}else{
			
			if($this->session->userdata['logged_in_timesheet']['empId'] == '92'):
                
                $projectQ = $this->db->select('p.*,c.client_Id,c.client_name,e.name,e.empId')
						->from('project_details as p')
						->join('client_details as c ', 'p.client_Id = c.client_Id', 'left')
						->join('employee_details as e ', 'p.empId = e.empId', 'left')
						->where('p.status','Process')
						->order_by('project_number' , 'desc')->get();
                else:
                
                $projectQ = $this->db->select('p.*,c.client_Id,c.client_name,e.name,e.empId')
						->from('project_details as p')
						->join('client_details as c ', 'p.client_Id = c.client_Id', 'left')
						->join('employee_details as e ', 'p.empId = e.empId', 'left')
						//->where('e.status','Active')
						->order_by('project_number' , 'desc')->get();
                
                              
                endif;
		}	
		 
		 
		 else:
		
		         $projectQ  	= $this->db->select('*')->from('project_details')->where('project_Id' , $projct_Id)->get();
		
		 endif; 
		
		 //echo $this->db->last_query();
		 
		 return $projectQ->result();
	
	}
	
	
	public function update_project($data , $projct_Id){ // Clent Update Functionality
  
  		$this->db->where('project_Id', $projct_Id);
		
	    $update = $this->db->update('project_details', $data);
		
		if($update):
			
			  return true; 
			
		endif;
  
  }
  
  
   public function delete_project($project_Id){
    
	 
	 	$this->db->where('project_Id', $project_Id)->delete('project_details');
		  
		$deleteQuery = $this->db->affected_rows();
			
	    echo  $deleteQuery;
  
  }
  
  
  
	public function recentProjects(){ // Recent Clients displaying angular js 
	
	    $projectQ 			=	 $this->db->select('p.*,c.client_Id,c.client_name')->from('project_details as p')
																 ->join('client_details as c ', 'p.client_Id = c.client_Id', 'left')
																  ->order_by('project_Id' , 'desc')->limit(5)->get();
		
		
	     return $projectQ->result();
		 
        //echo json_encode($recentQ->result());
		
		
	
	
	 }
	
	
	
	public function getProjectName($taskClientId){ // Displaying List of projects
	
	    if(empty($taskClientId)) :
			
			$projectNamesQ  	= $this->db->select('project_Id,project_name')->from('project_details')->order_by('project_Id' , 'desc')->get();
			
		else:
			
			 $projectNamesQ  	= $this->db->select('project_Id,project_name')->from('project_details')->where('client_Id',$taskClientId)->where('status !=', 'Closed')->order_by('project_Id' , 'desc')->get();
			
		endif;  	
			
			  return $projectNamesQ->result();
			
			
		
	}


	public function addProjectNumber(){

	 
		//$lastProjectRecord = $this->db->select('project_number')->from('project_details')->order_by('project_Id', 'desc')->limit(1)->get()->row();
		
		$lastProjectRecord = $this->db->select('MAX(project_number) AS project_number')->from('project_details')->order_by('project_number', 'DESC')->get()->row();
		
		if($lastProjectRecord) {

				$projectNumber = explode('-', $lastProjectRecord->project_number);
				$projectNumber[1] = str_pad(++$projectNumber[1], 3, '0', STR_PAD_LEFT);
				return implode('-', $projectNumber);
			

		} else {

				return null;

		}
	}

	public function getManagers($managerName){

		if(!empty($managerName)):

			$managerQuery  = $this->db->select('p_manager')->from('project_details')
								->where('p_manager',$managerName)->get();


		else:
			$managerQuery  = $this->db->select('empId,name')->from('employee_details')
								->where('user_type','manager')
								->where('status','Active')->order_by('name' , 'asc')->get();

		endif;						
		 
		return $managerQuery->result();

	}

		public function teamMembers(){

			$teamMembersQuery  = $this->db->select('empId,name')->from('employee_details')
								->where_in('user_type',array('developer','manager','admin','business_head','super_admin'))
								->where('status','Active')->order_by('name' , 'asc')->get();
		 
		    return $teamMembersQuery->result();
						

		}

	/************************************ Auto Suggest ******************************************************************************************************* */	
	  public function get_project_name_suggestions($q){
			$this->db->select('project_name');
			$this->db->like('project_name', $q);
			$query = $this->db->get('project_details');
			$result = $query->result_array();
			
			if($result){

			  foreach ($result as $row){

				$hideGeneral = str_replace(" - (General)", "",$row['project_name']);
				
				if($hideGeneral == $row['project_name']):

						$row_set[] = htmlentities(stripslashes($row['project_name'])); //build an array

				endif; 

			  }
			  echo json_encode($row_set); //format the array into json data
			}

		 }



			public function getAutoContactProjectDetails($q){
				$this->db->select('project_contact_name');
				$this->db->like('project_contact_name', $q);
				$this->db->group_by('project_contact_name');
				$query = $this->db->get('service_agreement_details');
				
				$result = $query->result_array();
				if($result){
				  foreach ($result as $row){
					$row_set[] = htmlentities(stripslashes($row['project_contact_name'])); //build an array
				  }
				  echo json_encode($row_set); //format the array into json data
				}
			 }	
/************************************ Auto Suggest ******************************************************************************************************* */	

/***************************************akhila code Project Report Master search information ********************************************************/


public function getReportMasterProjects(
    $department = [], 
    $manager = [], 
    $form_date = '', 
    $to_date = '', 
    $status = '',
    $search_text = ''   // ✅ ADD THIS
){

    $this->db->select('p.*, c.client_name, c.department, e.name, e.empId')
             ->from('project_details as p')
             ->join('client_details as c', 'p.client_Id = c.client_Id', 'left')
             ->join('employee_details as e', 'p.empId = e.empId', 'left');

    // ✅ DEPARTMENT FILTER
    if(!empty($department) && !in_array('all', $department)){
        $this->db->where_in('c.department', $department);
    }

    // ✅ MANAGER FILTER
    if(!empty($manager)){
        $this->db->where_in('p.empId', $manager);
    }

    // ✅ SEARCH FILTER (NEW)
    if(!empty($search_text)){
        $this->db->group_start();
        $this->db->like('p.project_name', $search_text);
        $this->db->or_like('p.project_number', $search_text);
        $this->db->or_like('c.client_name', $search_text);
        $this->db->group_end();
    }

    // ✅ STATUS FILTER (MAIN FIX)
    if(!empty($status) && $status != 'All'){
        $this->db->where('p.status', $status);
    }

    // ✅ DATE FILTER
    if(!empty($form_date)){
        $this->db->where('DATE(p.created_at) >=', $form_date);
    }

    if(!empty($to_date)){
        $this->db->where('DATE(p.created_at) <=', $to_date);
    }

    return $this->db->order_by('p.project_Id', 'desc')->get()->result();
}


public function getStatusCount($status)
{
    $this->db->where('status', $status);
    return $this->db->count_all_results('projects');
}
/*************************************** Project Report Master search information ********************************************************/	
    
    
/*********************************** Project master status update feature function below *************************************************/

public function update_project_master_model_query($project_status_update_id,$status){ 

	//echo $status.'-----'.$project_status_update_id; 
	  
	$update = $this->db->set('status',$status)->where('project_Id',$project_status_update_id) ->update('project_details');
	
	 $userQ  = $this->db->select('project_Id,status')->from('project_details')->where('project_Id' , $project_status_update_id)->get()->result();
	
	 foreach($userQ as $key => $getStatus ) { 
	
	   if($getStatus->status == 'Process'){
		   
		  $activeClass =  'fa fa-check-circle label label-success';
		  $statusName = 'In Process';
		   
	   
	   }elseif($getStatus->status == 'On Hold'){		   
		   
		   $activeClass = 'fa fa-registered label label-warning';
		   $statusName = 'On Hold';
	   
	   }else{
	   
			$activeClass =  'fa fa-ban label label-danger';
			$statusName = 'Closed';
	   
	   }			   
		   
		 echo  "<a class='".$activeClass."' style=cursor:pointer; data-toggle='modal' data-target='#comment_status_model_".$getStatus->project_Id."'> ".$statusName."</a>"; 
	
	}
  
}

/*********************************** Project master status update feature function below *************************************************/

/***********************************akhila code AJAX Pagination Methods *************************************************/

	/**
	 * Get total count of projects for pagination
	 */



	/*************************all counts code with general projects *****************************/



// public function getTotalProjects($search = '', $department = [], $manager = [], $from_date = '', $to_date = '', $status = '') {

//     $this->db->select('COUNT(*) as total')
//         ->from('project_details as p')
//         ->join('client_details as c', 'p.client_Id = c.client_Id', 'left')
//         ->join('employee_details as e', 'p.empId = e.empId', 'left');

//     // ✅ FORCE REMOVE GENERAL PROJECTS (CASE INSENSITIVE)
//  $this->db->where("LOWER(p.project_type) NOT LIKE '%general%'");

//     // ✅ Department filter (AFTER removing general)
//     if (!empty($department) && !in_array('all', $department)) {
//         $this->db->where_in('p.project_type', $department);
//     }

//     // ✅ Manager filter
//     if (!empty($manager)) {
//         $this->db->where_in('p.empId', $manager);
//     }

//     // ✅ Date filter
//     if (!empty($from_date)) {
//         $this->db->where('p.project_start_date >=', $from_date);
//     }

//     if (!empty($to_date)) {
//         $this->db->where('p.project_end_date <=', $to_date);
//     }

//     // ✅ Status filter
//     if (!empty($status)) {
//         $this->db->where('p.status', $status);
//     }

//     // ✅ Search
//     if (!empty($search)) {
//         $this->db->group_start();
//         $this->db->like('p.project_name', $search);
//         $this->db->or_like('p.project_number', $search);
//         $this->db->or_like('c.client_name', $search);
//         $this->db->or_like('e.name', $search);
//         $this->db->group_end();
//     }

//     return $this->db->get()->row()->total;
// }



	/*************************without general projects counts code *****************************/



public function getTotalProjects(
    $search = '',
    $department = [],
    $manager = [],
    $from_year = '',
    $from_month = '',
    $to_year = '',
    $to_month = '',
    $status = '',
    $billing_type = ''
) {

    $this->db->select('COUNT(*) as total')
        ->from('project_details as p')
        ->join('client_details as c', 'p.client_Id = c.client_Id', 'left')
        ->join('employee_details as e', 'p.empId = e.empId', 'left');



    // ✅ REMOVE ELOGIC SOLUTIONS PROJECTS FROM COUNTS
    $this->db->where("LOWER(c.client_name) NOT LIKE '%elogic solutions%'", null, false);

    // ✅ Billing Type Filter
    if (!empty($billing_type)) {
        $this->db->where('p.man_days', $billing_type);
    }

    // ✅ Department filter
    if (!empty($department) && !in_array('all', $department)) {
        $this->db->where_in('p.project_type', $department);
    }

    // ✅ Manager filter
    if (!empty($manager)) {
        $this->db->where_in('p.empId', $manager);
    }

    // ✅ FROM / TO FILTER (YEAR OR MONTH OR BOTH)

    // FROM YEAR
    if (!empty($from_year)) {
        $this->db->where('YEAR(p.project_start_date) >=', $from_year);
    }

    // FROM MONTH
    if (!empty($from_month)) {
        $this->db->where('MONTH(p.project_start_date) >=', $from_month);
    }

    // TO YEAR
    if (!empty($to_year)) {
        $this->db->where('YEAR(p.project_end_date) <=', $to_year);
    }

    // TO MONTH
    if (!empty($to_month)) {
        $this->db->where('MONTH(p.project_end_date) <=', $to_month);
    }

    // ✅ Status filter
    if (!empty($status)) {
        $this->db->where('p.status', $status);
    }

    // ✅ Search filter
    if (!empty($search)) {
        $this->db->group_start();
        $this->db->like('p.project_name', $search);
        $this->db->or_like('p.project_number', $search);
        $this->db->or_like('c.client_name', $search);
        $this->db->or_like('e.name', $search);
        $this->db->group_end();
    }

    return $this->db->get()->row()->total;
}



public function getStatusCountsWithoutGeneral(
    $search = '',
    $department = [],
    $manager = [],
    $from_year = '',
    $from_month = '',
    $to_year = '',
    $to_month = '',
    $billing_type = ''
)
{
    $this->db->select("
        SUM(CASE WHEN p.status = 'Process' THEN 1 ELSE 0 END) as process,
        SUM(CASE WHEN p.status = 'On Hold' THEN 1 ELSE 0 END) as hold,
        SUM(CASE WHEN p.status = 'Closed' THEN 1 ELSE 0 END) as closed
    ");

    $this->db->from('project_details as p');
    $this->db->join('client_details as c', 'p.client_Id = c.client_Id', 'left');
    $this->db->join('employee_details as e', 'p.empId = e.empId', 'left');

    // ✅ REMOVE GENERAL PROJECTS ONLY FROM COUNT TABLE
    $this->db->where("LOWER(p.project_type) NOT LIKE '%general%'", null, false);
    $this->db->where("LOWER(p.project_name) NOT LIKE '%(general)%'", null, false);

    // ✅ REMOVE ELOGIC SOLUTIONS
    $this->db->where("LOWER(c.client_name) NOT LIKE '%elogic solutions%'", null, false);

    // Department Filter
    if (!empty($department) && !in_array('all', $department)) {
        $this->db->where_in('p.project_type', $department);
    }

    // Manager Filter
    if (!empty($manager)) {
        $this->db->where_in('p.empId', $manager);
    }

    // Billing Type
    if (!empty($billing_type)) {
        $this->db->where('p.man_days', $billing_type);
    }

    // From Year
    if (!empty($from_year)) {
        $this->db->where('YEAR(p.project_start_date) >=', $from_year);
    }

    // From Month
    if (!empty($from_month)) {
        $this->db->where('MONTH(p.project_start_date) >=', $from_month);
    }

    // To Year
    if (!empty($to_year)) {
        $this->db->where('YEAR(p.project_end_date) <=', $to_year);
    }

    // To Month
    if (!empty($to_month)) {
        $this->db->where('MONTH(p.project_end_date) <=', $to_month);
    }

    // Search
    if (!empty($search)) {
        $this->db->group_start();
        $this->db->like('p.project_name', $search);
        $this->db->or_like('p.project_number', $search);
        $this->db->or_like('c.client_name', $search);
        $this->db->or_like('e.name', $search);
        $this->db->group_end();
    }

    return $this->db->get()->row();
}


/***************akhila code*************************** */
	/**
	 * Get paginated projects with optional sorting
	 */
public function getProjectsPaginated(
    $limit,
    $offset,
    $search = '',
    $sort_by = 'project_number',
    $sort_order = 'desc',
    $department = [],
    $manager = [],
    $from_year = '',
    $from_month = '',
    $to_year = '',
    $to_month = '',
    $status = '',
    $billing_type = ''
)
{
    $this->db->select('p.*, c.client_name, e.name')
        ->from('project_details as p')
        ->join('client_details as c', 'p.client_Id = c.client_Id', 'left')
        ->join('employee_details as e', 'p.empId = e.empId', 'left');


    // ✅ REMOVE ELOGIC SOLUTIONS PROJECTS
    $this->db->where("LOWER(c.client_name) NOT LIKE '%elogic solutions%'", null, false);

    // ✅ FILTER: Department
    if (!empty($department) && !in_array('all', $department)) {
        $this->db->where_in('p.project_type', $department);
    }

    // ✅ FILTER: Billing Type
    if (!empty($billing_type)) {
        $this->db->where('p.man_days', $billing_type);
    }

    // ✅ FILTER: Manager
    if (!empty($manager)) {
        $this->db->where_in('p.empId', $manager);
    }

    // ✅ FROM / TO FILTER (YEAR OR MONTH OR BOTH)

    // FROM YEAR
    if (!empty($from_year)) {
        $this->db->where('YEAR(p.project_start_date) >=', $from_year);
    }

    // FROM MONTH
    if (!empty($from_month)) {
        $this->db->where('MONTH(p.project_start_date) >=', $from_month);
    }

    // TO YEAR
    if (!empty($to_year)) {
        $this->db->where('YEAR(p.project_end_date) <=', $to_year);
    }

    // TO MONTH
    if (!empty($to_month)) {
        $this->db->where('MONTH(p.project_end_date) <=', $to_month);
    }

    // ✅ FILTER: Status
    if (!empty($status)) {
        $this->db->where('p.status', $status);
    }

    // ✅ SEARCH
    if (!empty($search)) {
        $this->db->group_start();
        $this->db->like('p.project_name', $search);
        $this->db->or_like('p.project_number', $search);
        $this->db->or_like('c.client_name', $search);
        $this->db->or_like('e.name', $search);
        $this->db->group_end();
    }

    // ✅ SORT
    $this->db->order_by($sort_by, $sort_order);

    // ✅ LIMIT
    $this->db->limit($limit, $offset);

    return $this->db->get()->result();
}

/*********************************** AJAX Pagination Methods END *************************************************/    

}

