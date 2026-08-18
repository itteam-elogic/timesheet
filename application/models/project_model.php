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

	public function getHoursNotificationGridProjects()
	{
		$this->ensureHoursNotificationTable();
		$hoursSub = '(SELECT project_Id, SUM(emp_time_hours) AS total_hours
			FROM emp_record_details
			WHERE (status IS NULL OR status = \'\' OR status != \'Rejected\')
			GROUP BY project_Id) hours';
		$lastSub = '(SELECT project_Id, MAX(milestone_hours) AS last_milestone, MAX(created_at) AS last_sent_at
			FROM project_hours_notifications
			GROUP BY project_Id) lastn';

		$this->db->select("p.project_Id, p.project_name, p.project_number, p.notif_hours, p.status, p.estimated_hours, p.p_manager, p.who_allocated_project_empId, p.empId, c.client_name, creator.name as creator_name, creator.email as creator_email, pm.name as manager_name, pm.email as manager_email, COALESCE(hours.total_hours, 0) as completed_hours, COALESCE(lastn.last_milestone, 0) as last_milestone, lastn.last_sent_at", false);
		$this->db->from('project_details p');
		$this->db->join('client_details c', 'c.client_Id = p.client_Id', 'left');
		$this->db->join('employee_details creator', 'creator.empId = p.who_allocated_project_empId', 'left');
		$this->db->join('employee_details pm', 'pm.empId = p.empId', 'left');
		$this->db->join($hoursSub, 'hours.project_Id = p.project_Id', 'left');
		$this->db->join($lastSub, 'lastn.project_Id = p.project_Id', 'left');
		$this->db->where('p.status !=', 'Closed');
		$this->db->where('p.notif_hours IS NOT NULL', null, false);
		$this->db->where('p.notif_hours >', 0);
		$this->db->where('p.estimated_hours >', 0);
		$this->db->where("p.project_name NOT LIKE '%(General)%'");
		$this->db->order_by('hours.total_hours', 'desc');
		$rows = $this->db->get()->result();
		$ready = array();
		if (empty($rows)) {
			return $ready;
		}
		foreach ($rows as $row) {
			$projectName = isset($row->project_name) ? $row->project_name : '';
			if (stripos($projectName, '(General)') !== false) {
				continue;
			}
			$meta = $this->buildHoursNotificationMeta(
				isset($row->estimated_hours) ? $row->estimated_hours : 0,
				isset($row->notif_hours) ? $row->notif_hours : 0,
				isset($row->completed_hours) ? $row->completed_hours : 0,
				isset($row->last_milestone) ? $row->last_milestone : 0
			);
			if (!empty($meta['final_sent']) || empty($meta['max_sends'])) {
				continue;
			}
			if ($meta['reached_milestone'] < $meta['interval'] && empty($meta['is_final'])) {
				continue;
			}
			$row->max_sends = $meta['max_sends'];
			$row->sent_count = $meta['sent_count'];
			$row->remaining_sends = $meta['remaining_sends'];
			$row->remaining_hours = $meta['remaining_hours'];
			$row->is_final = !empty($meta['is_final']) ? 1 : 0;
			$row->can_send = !empty($meta['can_send']) ? 1 : 0;
			$ready[] = $row;
		}
		return $ready;
	}

	public function sendManualHoursNotification($projectId)
	{
		$projectId = (int)$projectId;
		if ($projectId <= 0) {
			return array('success' => false, 'message' => 'Please choose a project.');
		}

		$this->ensureHoursNotificationTable();
		$projects = $this->getProjectsForHoursNotification($projectId);
		if (empty($projects)) {
			return array('success' => false, 'message' => 'Project not found, closed, or notification hours are not set.');
		}

		$project = $projects[0];
		if ((int)$project->project_Id !== $projectId) {
			return array('success' => false, 'message' => 'Project mismatch. Notification was not sent.');
		}
		$projectName = isset($project->project_name) ? $project->project_name : '';
		if (stripos($projectName, '(General)') !== false) {
			return array('success' => false, 'message' => 'General projects are excluded from hours notifications.');
		}

		$interval = isset($project->notif_hours) ? (float)$project->notif_hours : 0;
		$totalHours = $this->getProjectLoggedHours($project->project_Id);
		$estimatedHours = isset($project->estimated_hours) ? (float)$project->estimated_hours : 0;
		$lastMilestone = $this->getLastNotifiedMilestone($project->project_Id);
		$meta = $this->buildHoursNotificationMeta($estimatedHours, $interval, $totalHours, $lastMilestone);

		if (empty($meta['max_sends'])) {
			return array('success' => false, 'message' => 'Estimated hours and notification hours are required.');
		}
		if (!empty($meta['final_sent'])) {
			return array('success' => false, 'message' => 'Notifications have stopped for this project. Estimated hours are already completed.');
		}
		if (empty($meta['can_send'])) {
			return array(
				'success' => false,
				'message' => 'Already sent for the current notification hours. Remaining notifications: ' . $meta['remaining_sends'] . '.'
			);
		}

		$currentMilestone = $meta['reached_milestone'];
		$sent = $this->sendHoursCompletionEmail($project, $totalHours, $currentMilestone, $interval, !empty($meta['is_final']));
		if (!$sent) {
			return array('success' => false, 'message' => 'Failed to send email.');
		}

		$this->saveHoursNotification(
			$project->project_Id,
			$currentMilestone,
			$totalHours,
			$interval,
			$this->buildHoursNotificationRecipients($project)
		);

		$remainingAfterSend = max(0, (int)$meta['remaining_sends'] - 1);
		$message = 'Notification sent for ' . $project->project_name . ' only.';
		if (!empty($meta['is_final'])) {
			$message = 'Final notification sent for ' . $project->project_name . '. Notifications have stopped until Estimated hours are updated or the project is closed.';
		} else {
			$message .= ' Remaining notifications: ' . $remainingAfterSend . '.';
		}

		return array(
			'success' => true,
			'message' => $message,
			'last_sent_at' => date('d-M-Y h:i A')
		);
	}

	public function processHoursCompletionNotifications($projectId = null, $oncePerDay = false)
	{
		$this->ensureHoursNotificationTable();
		if ($oncePerDay && empty($projectId) && !$this->claimDailyHoursNotificationRun()) {
			return array(
				'sent' => 0,
				'checked' => 0,
				'message' => 'Hours notification check already ran today.'
			);
		}
		$projects = $this->getProjectsForHoursNotification($projectId);
		$sentCount = 0;
		$checkedCount = 0;
		$skippedClosed = 0;

		if (empty($projects)) {
			return array(
				'sent' => 0,
				'checked' => 0,
				'message' => 'No active projects with notification hours found.'
			);
		}

		foreach ($projects as $project) {
			$checkedCount++;
			$status = isset($project->status) ? strtolower(trim($project->status)) : '';
			if ($status === 'closed' || $status === 'completed') {
				$skippedClosed++;
				continue;
			}

			$interval = isset($project->notif_hours) ? (float)$project->notif_hours : 0;
			if ($interval <= 0) {
				continue;
			}

			$totalHours = $this->getProjectLoggedHours($project->project_Id);
			$currentMilestone = floor($totalHours / $interval) * $interval;
			if ($currentMilestone < $interval) {
				continue;
			}

			$lastMilestone = $this->getLastNotifiedMilestone($project->project_Id);
			if ($currentMilestone <= $lastMilestone) {
				continue;
			}

			$sent = $this->sendHoursCompletionEmail($project, $totalHours, $currentMilestone, $interval);
			if ($sent) {
				$this->saveHoursNotification($project->project_Id, $currentMilestone, $totalHours, $interval, $this->buildHoursNotificationRecipients($project));
				$sentCount++;
			}
		}

		return array(
			'sent' => $sentCount,
			'checked' => $checkedCount,
			'message' => 'Checked ' . $checkedCount . ' open project(s). Sent ' . $sentCount . ' notification(s). Closed projects are skipped.'
		);
	}

	private function ensureHoursNotificationTable()
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS `project_hours_notifications` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`project_Id` int(11) NOT NULL,
			`milestone_hours` decimal(10,2) NOT NULL,
			`total_hours` decimal(10,2) NOT NULL,
			`notif_interval` decimal(10,2) NOT NULL,
			`sent_to` varchar(500) DEFAULT NULL,
			`created_at` datetime NOT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `project_milestone` (`project_Id`,`milestone_hours`),
			KEY `idx_project_Id` (`project_Id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$this->db->query("CREATE TABLE IF NOT EXISTS `app_cron_runs` (
			`cron_name` varchar(100) NOT NULL,
			`last_run_at` datetime NOT NULL,
			PRIMARY KEY (`cron_name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
	}

	private function claimDailyHoursNotificationRun()
	{
		$row = $this->db->get_where('app_cron_runs', array('cron_name' => 'project_hours_notifications'))->row();
		$today = date('Y-m-d');
		if ($row && substr($row->last_run_at, 0, 10) === $today) {
			return false;
		}
		$now = date('Y-m-d H:i:s');
		if ($row) {
			$this->db->where('cron_name', 'project_hours_notifications')->update('app_cron_runs', array('last_run_at' => $now));
		} else {
			$this->db->insert('app_cron_runs', array(
				'cron_name' => 'project_hours_notifications',
				'last_run_at' => $now
			));
		}
		return true;
	}

	private function getProjectsForHoursNotification($projectId = null)
	{
		$projectId = (int)$projectId;
		if ($projectId <= 0) {
			return array();
		}
		$this->db->select("p.project_Id, p.project_name, p.project_number, p.notif_hours, p.status, p.estimated_hours, p.p_manager, p.who_allocated_project_empId, p.empId, p.man_days, c.client_name, creator.name as creator_name, creator.email as creator_email, pm.name as manager_name, pm.email as manager_email", false);
		$this->db->from('project_details p');
		$this->db->join('client_details c', 'c.client_Id = p.client_Id', 'left');
		$this->db->join('employee_details creator', 'creator.empId = p.who_allocated_project_empId', 'left');
		$this->db->join('employee_details pm', 'pm.empId = p.empId', 'left');
		$this->db->where('p.status !=', 'Closed');
		$this->db->where('p.notif_hours IS NOT NULL', null, false);
		$this->db->where('p.notif_hours >', 0);
		$this->db->where('p.project_Id', $projectId);
		$this->db->limit(1);
		return $this->db->get()->result();
	}

	private function getProjectLoggedHours($projectId)
	{
		$row = $this->db->select('COALESCE(SUM(emp_time_hours), 0) as total_hours', false)
			->from('emp_record_details')
			->where('project_Id', $projectId)
			->where("(status IS NULL OR status = '' OR status != 'Rejected')", null, false)
			->get()
			->row();
		return $row ? (float)$row->total_hours : 0;
	}

	private function getLastNotifiedMilestone($projectId)
	{
		$row = $this->db->select('MAX(milestone_hours) as last_milestone', false)
			->from('project_hours_notifications')
			->where('project_Id', $projectId)
			->get()
			->row();
		return ($row && $row->last_milestone !== null) ? (float)$row->last_milestone : 0;
	}

	private function saveHoursNotification($projectId, $milestoneHours, $totalHours, $interval, $sentTo)
	{
		$existing = $this->db->get_where('project_hours_notifications', array(
			'project_Id' => $projectId,
			'milestone_hours' => $milestoneHours
		))->row();
		$data = array(
			'total_hours' => $totalHours,
			'notif_interval' => $interval,
			'sent_to' => $sentTo,
			'created_at' => date('Y-m-d H:i:s')
		);
		if ($existing) {
			$this->db->where('id', $existing->id)->update('project_hours_notifications', $data);
		} else {
			$data['project_Id'] = $projectId;
			$data['milestone_hours'] = $milestoneHours;
			$this->db->insert('project_hours_notifications', $data);
		}
	}

	private function buildHoursNotificationRecipients($project)
	{
		$emails = array(
			'laxmikanth@elogictech.com',
			'jaishree@elogictech.com',
			'accounts@elogictech.com'
		);
		if (!empty($project->manager_email) && filter_var($project->manager_email, FILTER_VALIDATE_EMAIL)) {
			$emails[] = strtolower(trim($project->manager_email));
		} elseif (!empty($project->creator_email) && filter_var($project->creator_email, FILTER_VALIDATE_EMAIL)) {
			$emails[] = strtolower(trim($project->creator_email));
		}
		return implode(',', array_unique($emails));
	}

	private function buildHoursNotificationMeta($estimated, $interval, $completed, $lastMilestone)
	{
		$estimated = (float)$estimated;
		$interval = (float)$interval;
		$completed = (float)$completed;
		$lastMilestone = (float)$lastMilestone;
		$remainingHours = $estimated - $completed;
		$maxSends = ($interval > 0 && $estimated > 0) ? (int)floor($estimated / $interval) : 0;
		$isFinal = ($estimated > 0 && $completed >= $estimated);
		$reachedMilestone = 0;
		if ($interval > 0 && $maxSends > 0) {
			$maxMilestone = $maxSends * $interval;
			$reachedMilestone = floor($completed / $interval) * $interval;
			if ($reachedMilestone > $maxMilestone) {
				$reachedMilestone = $maxMilestone;
			}
			if ($isFinal) {
				$reachedMilestone = $maxMilestone;
			}
		}
		$sentCount = ($interval > 0 && $lastMilestone > 0) ? (int)floor($lastMilestone / $interval) : 0;
		if ($sentCount > $maxSends) {
			$sentCount = $maxSends;
		}
		$remainingSends = max(0, $maxSends - $sentCount);
		$finalSent = ($maxSends > 0 && $sentCount >= $maxSends);
		$canSend = (!$finalSent && $reachedMilestone > $lastMilestone && $reachedMilestone >= $interval);
		if ($isFinal && !$finalSent && $maxSends > 0) {
			$canSend = true;
		}

		return array(
			'estimated' => $estimated,
			'completed' => $completed,
			'remaining_hours' => $remainingHours,
			'interval' => $interval,
			'max_sends' => $maxSends,
			'sent_count' => $sentCount,
			'remaining_sends' => $remainingSends,
			'reached_milestone' => $reachedMilestone,
			'is_final' => $isFinal,
			'final_sent' => $finalSent,
			'can_send' => $canSend
		);
	}

	private function formatNotificationHours($hours)
	{
		if ($hours === '' || $hours === null) {
			return '0';
		}
		return rtrim(rtrim(number_format((float)$hours, 2, '.', ''), '0'), '.');
	}

	private function sendHoursCompletionEmail($project, $totalHours, $milestoneHours, $interval, $isFinal = false)
	{
		$recipients = $this->buildHoursNotificationRecipients($project);
		if (empty($recipients)) {
			return false;
		}

		$pmName = !empty($project->manager_name) ? $project->manager_name : (!empty($project->p_manager) ? $project->p_manager : 'Team');
		$projectName = !empty($project->project_name) ? $project->project_name : 'Project';
		$estimatedHours = isset($project->estimated_hours) && $project->estimated_hours !== '' ? (float)$project->estimated_hours : 0;
		$completedHours = (float)$totalHours;
		$remainingHours = $estimatedHours - $completedHours;
		$estimatedLabel = $this->formatNotificationHours($estimatedHours);
		$completedLabel = $this->formatNotificationHours($completedHours);
		$remainingLabel = $this->formatNotificationHours($remainingHours);

		$subject = 'Invoice reminder - ' . $projectName . ' hours completed';
		if ($isFinal) {
			$body = '<!doctype html>
			<html>
			<head>
				<meta charset="utf-8">
				<title>Hours Completion Notification</title>
			</head>
			<body style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #222; line-height: 1.6;">
				<p>Hi ' . htmlspecialchars($pmName) . ',</p>
				<p>
					Project: ' . htmlspecialchars($projectName) . '<br>
					Estimated Hours: ' . htmlspecialchars($estimatedLabel) . '<br>
					Completed Hours: ' . htmlspecialchars($completedLabel) . '
				</p>
				<p>Please raise the invoice for the completed hours.</p>
				<p>If the project is completed, please close the project. If it is still active, please update the Estimated hours and Notification reminder hours.</p>
				<p>Thank you<br>eLogicTech</p>
			</body>
			</html>';
		} else {
			$body = '<!doctype html>
			<html>
			<head>
				<meta charset="utf-8">
				<title>Hours Completion Notification</title>
			</head>
			<body style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #222; line-height: 1.6;">
				<p>Hi ' . htmlspecialchars($pmName) . ',</p>
				<p>
					Project: ' . htmlspecialchars($projectName) . '<br>
					Estimated Hours: ' . htmlspecialchars($estimatedLabel) . '<br>
					Completed Hours: ' . htmlspecialchars($completedLabel) . '<br>
					Remaining Hours : ' . htmlspecialchars($remainingLabel) . '
				</p>
				<p>For Project ' . htmlspecialchars($projectName) . ', ' . htmlspecialchars($completedLabel) . ' hours have been completed out of the estimated ' . htmlspecialchars($estimatedLabel) . ' hours. Please raise the invoice for the completed hours.</p>
				<p>Thank you.<br>eLogicTech</p>
			</body>
			</html>';
		}

		$config = array(
			'mailtype' => 'html',
			'charset' => 'utf-8',
			'wordwrap' => TRUE,
			'newline' => "\r\n"
		);
		$this->email->initialize($config);
		$this->email->clear(TRUE);
		$this->email->from('info@elogictech.com', 'eLogicTech');
		$this->email->to($recipients);
		$this->email->subject($subject);
		$this->email->message($body);
		return (bool)@$this->email->send();
	}

}

