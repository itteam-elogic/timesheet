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

public function update_project_master_model_query($project_status_update_id, $status, $project_end_date = '')
{
	$project_status_update_id = (int)$project_status_update_id;
	$status = trim((string)$status);
	$project_end_date = trim((string)$project_end_date);

	$project = $this->db->select('project_Id, status, project_start_date, project_end_date')
		->from('project_details')
		->where('project_Id', $project_status_update_id)
		->get()
		->row();

	if (empty($project)) {
		return;
	}

	$updateData = array('status' => $status);

	if ($project_end_date !== '') {
		$startTs = strtotime($project->project_start_date);
		$endTs = strtotime($project_end_date);

		if ($startTs === false || $endTs === false || $endTs <= $startTs) {
			header('HTTP/1.1 422 Unprocessable Entity');
			echo 'End Date must be greater than Start Date.';
			return;
		}

		$updateData['project_end_date'] = $project_end_date;
	}

	$this->db->where('project_Id', $project_status_update_id)->update('project_details', $updateData);

	$userQ = $this->db->select('project_Id, status')
		->from('project_details')
		->where('project_Id', $project_status_update_id)
		->get()
		->result();

	foreach ($userQ as $key => $getStatus) {
		$badgeClass = 'status-badge status-closed';
		$statusIcon = 'fa-times-circle';
		$statusName = 'Closed';

		if ($getStatus->status == 'Process') {
			$badgeClass = 'status-badge status-process';
			$statusIcon = 'fa-check-circle';
			$statusName = 'In Process';
		} elseif ($getStatus->status == 'On Hold') {
			$badgeClass = 'status-badge status-hold';
			$statusIcon = 'fa-pause-circle';
			$statusName = 'On Hold';
		}

		echo '<a class="' . $badgeClass . '" style="cursor:pointer;" data-toggle="modal" data-target="#comment_status_model_' . (int)$getStatus->project_Id . '"><i class="fa ' . $statusIcon . '"></i> ' . htmlspecialchars($statusName, ENT_QUOTES, 'UTF-8') . '</a>';
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

    private function normalizeFilterArray($value)
    {
        if ($value === null || $value === '') {
            return array();
        }
        if (!is_array($value)) {
            return array($value);
        }
        return array_values(array_filter($value, function ($item) {
            return $item !== '' && $item !== null;
        }));
    }

    private function normalizeYearFilter($year)
    {
        $year = trim((string)$year);
        if ($year === '' || strtoupper($year) === 'ALL') {
            return '';
        }
        return $year;
    }

    private function normalizeMonthFilter($month)
    {
        $month = trim((string)$month);
        if ($month === '') {
            return 0;
        }
        return (int)$month;
    }

    private function applyProjectListFilters(
        $search = '',
        $department = array(),
        $manager = array(),
        $from_year = '',
        $from_month = '',
        $to_year = '',
        $to_month = '',
        $status = '',
        $billing_type = '',
        $client_Id = '',
        $project_Id = ''
    ) {
        $department = $this->normalizeFilterArray($department);
        $manager = $this->normalizeFilterArray($manager);

        $from_year = $this->normalizeYearFilter($from_year);
        $to_year = $this->normalizeYearFilter($to_year);
        $from_month = $this->normalizeMonthFilter($from_month);
        $to_month = $this->normalizeMonthFilter($to_month);

        $this->db->where("LOWER(c.client_name) NOT LIKE '%elogic solutions%'", null, false);

        if (!empty($billing_type)) {
            $this->db->where('p.man_days', $billing_type);
        }

        if (!empty($department) && !in_array('all', $department, true)) {
            $this->db->where_in('p.project_type', $department);
        }

        if (!empty($manager)) {
            $this->db->where_in('p.empId', $manager);
        }

        if (!empty($client_Id)) {
            $this->db->where('p.client_Id', (int)$client_Id);
        }

        if (!empty($project_Id)) {
            $this->db->where('p.project_Id', (int)$project_Id);
        }

        if ($from_year !== '') {
            if ($from_month >= 1 && $from_month <= 12) {
                $fromDate = sprintf('%04d-%02d-01', (int)$from_year, $from_month);
            } else {
                $fromDate = sprintf('%04d-01-01', (int)$from_year);
            }
            $this->db->where('p.project_start_date >=', $fromDate);
        }

        if ($to_year !== '') {
            if ($to_month >= 1 && $to_month <= 12) {
                $toDate = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', (int)$to_year, $to_month)));
            } else {
                $toDate = sprintf('%04d-12-31', (int)$to_year);
            }
            $this->db->where('p.project_end_date <=', $toDate);
        }

        if (!empty($status)) {
            $this->db->where('p.status', $status);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.project_name', $search);
            $this->db->or_like('p.project_number', $search);
            $this->db->or_like('c.client_name', $search);
            $this->db->or_like('e.name', $search);
            $this->db->group_end();
        }
    }

    private function applyProjectListSort($sort_by = 'project_number', $sort_order = 'desc')
    {
        $allowed_columns = array(
            'project_type' => 'p.project_type',
            'client_name' => 'c.client_name',
            'project_name' => 'p.project_name',
            'project_number' => 'p.project_number',
            'name' => 'e.name',
            'man_days' => 'p.man_days',
            'estimated_hours' => 'p.estimated_hours',
            'status' => 'p.status',
            'project_start_date' => 'p.project_start_date',
            'project_end_date' => 'p.project_end_date',
            'Start Date' => 'p.project_start_date',
            'End Date' => 'p.project_end_date',
        );

        $sort_by = trim((string)$sort_by);
        $sort_order = strtolower(trim((string)$sort_order)) === 'asc' ? 'ASC' : 'DESC';
        $column = isset($allowed_columns[$sort_by]) ? $allowed_columns[$sort_by] : 'p.project_number';

        $this->db->order_by($column, $sort_order);
    }

    public function getProjectsExportList(
        $search = '',
        $sort_by = 'project_number',
        $sort_order = 'desc',
        $department = array(),
        $manager = array(),
        $from_year = '',
        $from_month = '',
        $to_year = '',
        $to_month = '',
        $status = '',
        $billing_type = '',
        $client_Id = '',
        $project_Id = ''
    ) {
        $this->db->select('
            p.project_type,
            c.client_name,
            p.project_name,
            p.project_number,
            e.name as manager_name,
            p.man_days,
            p.estimated_hours,
            p.status,
            p.project_start_date,
            p.project_end_date
        ')
            ->from('project_details as p')
            ->join('client_details as c', 'p.client_Id = c.client_Id', 'left')
            ->join('employee_details as e', 'p.empId = e.empId', 'left');

        $this->applyProjectListFilters(
            $search,
            $department,
            $manager,
            $from_year,
            $from_month,
            $to_year,
            $to_month,
            $status,
            $billing_type,
            $client_Id,
            $project_Id
        );

        $this->applyProjectListSort($sort_by, $sort_order);

        return $this->db->get()->result();
    }

    public function getProjectsByClientId($clientId = '')
    {
        $this->db->select('project_Id, project_name, project_number')
            ->from('project_details')
            ->order_by('project_name', 'asc');

        if ($clientId !== '' && $clientId !== null) {
            $this->db->where('client_Id', (int)$clientId);
        }

        return $this->db->get()->result();
    }


public function getTotalProjects(
    $search = '',
    $department = [],
    $manager = [],
    $from_year = '',
    $from_month = '',
    $to_year = '',
    $to_month = '',
    $status = '',
    $billing_type = '',
    $client_Id = '',
    $project_Id = ''
) {

    $this->db->select('COUNT(*) as total')
        ->from('project_details as p')
        ->join('client_details as c', 'p.client_Id = c.client_Id', 'left')
        ->join('employee_details as e', 'p.empId = e.empId', 'left');

    $this->applyProjectListFilters(
        $search,
        $department,
        $manager,
        $from_year,
        $from_month,
        $to_year,
        $to_month,
        $status,
        $billing_type,
        $client_Id,
        $project_Id
    );

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
    $billing_type = '',
    $client_Id = '',
    $project_Id = ''
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

    $this->db->where("LOWER(p.project_type) NOT LIKE '%general%'", null, false);
    $this->db->where("LOWER(p.project_name) NOT LIKE '%(general)%'", null, false);

    $this->applyProjectListFilters(
        $search,
        $department,
        $manager,
        $from_year,
        $from_month,
        $to_year,
        $to_month,
        '',
        $billing_type,
        $client_Id,
        $project_Id
    );

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
    $billing_type = '',
    $client_Id = '',
    $project_Id = ''
)
{
    $this->db->select('p.*, c.client_name, e.name')
        ->from('project_details as p')
        ->join('client_details as c', 'p.client_Id = c.client_Id', 'left')
        ->join('employee_details as e', 'p.empId = e.empId', 'left');

    $this->applyProjectListFilters(
        $search,
        $department,
        $manager,
        $from_year,
        $from_month,
        $to_year,
        $to_month,
        $status,
        $billing_type,
        $client_Id,
        $project_Id
    );

    $this->applyProjectListSort($sort_by, $sort_order);

    $this->db->limit($limit, $offset);

    return $this->db->get()->result();
}

/*********************************** AJAX Pagination Methods END *************************************************/    

}

