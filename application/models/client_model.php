<?php
/**
 * eLogivc Admin Panel for Codeigniter 
 * Author: Laxmikanth 
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Client_Model extends CI_Model {

    public function __construct() {
	
			parent::__construct();   
	
	}
  

	// Read data using username and password
	
	
	public function add_client($data){  // Add Client Model 
	
	  if($data):
	   
	   		 $this->db->insert('client_details', $data);	
	   
	   endif;
	
	
	}
	
	public function getClients($client_Id = NULL){
	
		 
		if(empty($client_Id)):

			$getDepartmentCall = $this->departmentInsights();			
			
			if(in_array($this->session->userdata['logged_in_timesheet']['user_type'], array('manager','business_head'))){
				
			  $logedInUser = $this->session->userdata['logged_in_timesheet']['empId'];				  

			  $clientQ = $this->db->select('c.*,e.name,e.empId')
			  			->from('client_details as c')
						->join('employee_details as e', 'e.empId = c.empId', 'left')
						->where_in('c.department', explode(',', $getDepartmentCall))
						->order_by('client_Id', 'desc')
						->get();

			}else{
				
				$clientQ = $this->db->select('c.*,e.name,e.empId')
						->from('client_details as c')
						->join('employee_details as e', 'e.empId = c.empId', 'left')
						//->where('c.department', $getDepartmentCall)
						->order_by('client_Id', 'desc')
						->get();

			}
			
		else:			
		
			$clientQ  = $this->db->select('*')->from('client_details')
							->where('client_Id' , $client_Id)->order_by('client_Id' , 'desc')->get();
		
		
		endif; 
		
		  return $clientQ->result();
	
	}

	public function searchClients($client_name = '')
	{
		return $this->liveClientSearch($client_name);
	}

	// Client report listing with filters (status, department, date range, manager, client name)
	public function getClientsReport($statusFilter = '', $department = array(), $form_date = '', $to_date = '', $manager = array(), $client_search = '')
	{
		$this->db->select('c.*, e.name, e.empId')
			->from('client_details as c')
			->join('employee_details as e', 'e.empId = c.empId', 'left');

		// Status filter from controller uses: active / inactive
		if (!empty($statusFilter)) {
			if (strtolower($statusFilter) === 'active') {
				$this->db->where('c.status', 'Active');
			} elseif (strtolower($statusFilter) === 'inactive') {
				$this->db->where('c.status', 'InActive');
			}
		}

		// Department filter
		if (!empty($department)) {
			if (!is_array($department)) {
				$department = array($department);
			}
			$department = array_values(array_filter(array_map('trim', $department), function($v) {
				return $v !== '' && strtolower($v) !== 'all';
			}));
			if (!empty($department)) {
				$this->db->where_in('c.department', $department);
			}
		}

		// Date range filter based on client created date
		if (!empty($form_date) && !empty($to_date)) {
			$this->db->where('DATE(c.created_at) >=', $form_date);
			$this->db->where('DATE(c.created_at) <=', $to_date);
		}

		// Manager filter (by empId)
		if (!empty($manager)) {
			if (!is_array($manager)) {
				$manager = array($manager);
			}
			$manager = array_values(array_filter($manager, function($v) {
				return $v !== '' && strtolower((string)$v) !== 'all';
			}));
			if (!empty($manager)) {
				$this->db->where_in('c.empId', $manager);
			}
		}

		// Client name search
		if (!empty($client_search)) {
			$this->db->like('c.client_name', trim($client_search));
		}

		return $this->db->order_by('c.client_Id', 'desc')->get()->result();
	}

	/**
	 * Lightweight client name lookup for live search (suggestions and quick preview rows).
	 */
	public function liveClientSearch($search = '')
	{
		$search = trim((string)$search);
		if ($search === '') {
			return array();
		}
		return $this->db->select('c.*, e.name')
			->from('client_details c')
			->join('employee_details e', 'e.empId = c.empId', 'left')
			->like('c.client_name', $search)
			->order_by('c.client_name', 'asc')
			->limit(30)
			->get()
			->result();
	}
	
	
	public function update_client($data , $client_Id){ // Clent Update Functionality
  
  		$this->db->where('client_Id', $client_Id);
		
	    $update = $this->db->update('client_details', $data);
		
		if($update):
			
			  return true; 
			
		endif;
  
  }
  
  
   public function del_client($client_Id){
  	
		$this->db->where('client_Id', $client_Id)->delete('client_details');
		  
		$deleteQuery = $this->db->affected_rows();
			
	    echo  $deleteQuery;
  
  }
  
  public function update_client_status($client_Id, $status) {
  	
		$this->db->where('client_Id', $client_Id);
		
		$update = $this->db->update('client_details', array(
			'status' => $status,
			'updated_at' => date('Y-m-d H:i:s')
		));
		
		if($update):
			
			return true; 
			
		endif;
		
		return false;
  
  }
	
	
	public function getClientName(){ // Get List of Clients
	
		 $clientNQ  = $this->db->select('c.client_Id,c.client_name')->from('client_details as c')->where('status','Active')->order_by('client_Id' , 'desc')->get();
		 
		 return $clientNQ->result();
		
	}

	public function getClientNameForFilter() {
		$clientNQ = $this->db->select('c.client_Id, c.client_name')
			->from('client_details as c')
			->order_by('client_Id', 'desc')
			->get();
		return $clientNQ->result();
	}

	// Client summary counts for report screen
	public function getClientCounts() {
		$total = (int)$this->db->count_all('client_details');

		$active = (int)$this->db->where('status', 'Active')->count_all_results('client_details');
		$inactive = (int)$this->db->where('status', 'InActive')->count_all_results('client_details');

		return array(
			'total' => $total,
			'active' => $active,
			'inactive' => $inactive
		);
	}

	public function getAllClientsReportProjects($clientIds = 'all', $departments = 'all', $projectManagers = 'all')
	{
		if (!is_array($clientIds)) {
			$clientIds = ($clientIds === 'all' || $clientIds === '' || $clientIds === null) ? 'all' : array($clientIds);
		} elseif (empty($clientIds) || in_array('all', $clientIds)) {
			$clientIds = 'all';
		}
		if (!is_array($departments)) {
			$departments = ($departments === 'all' || $departments === '' || $departments === null) ? 'all' : array($departments);
		} elseif (empty($departments) || in_array('all', $departments)) {
			$departments = 'all';
		}
		if (!is_array($projectManagers)) {
			$projectManagers = ($projectManagers === 'all' || $projectManagers === '' || $projectManagers === null) ? 'all' : array($projectManagers);
		} elseif (empty($projectManagers) || in_array('all', $projectManagers)) {
			$projectManagers = 'all';
		}

		$eLogicClientsIds = array('363','374','370','369','368','367','364','361','355','270','262','253','236','210','85','78','74','49','34','32');

		$this->db->select('p.project_Id, p.project_name, c.client_name');
		$this->db->from('project_details as p');
		$this->db->join('client_details as c', 'c.client_Id = p.client_Id', 'inner');
		$this->db->where('c.status', 'Active');
		$this->db->where_not_in('c.client_Id', $eLogicClientsIds);
		$this->db->where("NOT (LOWER(TRIM(c.client_name)) = 'elogic solutions(nikhil)' AND LOWER(TRIM(p.project_name)) LIKE '%general%')", null, false);

		if ($clientIds !== 'all' && !empty($clientIds)) {
			$this->db->where_in('p.client_Id', $clientIds);
		}
		if ($departments !== 'all' && !empty($departments)) {
			$this->db->where("COALESCE(NULLIF(TRIM(p.project_type), ''), c.department) IS NOT NULL", NULL, FALSE);
			$this->db->where_in("COALESCE(NULLIF(TRIM(p.project_type), ''), c.department)", $departments);
		}
		if ($projectManagers !== 'all' && !empty($projectManagers)) {
			$this->db->where_in('p.empId', $projectManagers);
		}

		$this->db->order_by('p.project_name', 'asc');
		return $this->db->get()->result();
	}

	public function getDistinctDepartments(){ // Distinct department-wise values: from project_details.project_type when set, else client_details.department
		$allowedDepts = array('Architectural', 'MEP', 'Structural', '3D Visualization','2D Auto CAD');
		$this->db->select('COALESCE(NULLIF(TRIM(p.project_type), ""), c.department) as department', FALSE)
			->from('project_details as p')
			->join('client_details as c', 'c.client_Id = p.client_Id', 'inner')
			->where('c.status', 'Active')
			->where("COALESCE(NULLIF(TRIM(p.project_type), ''), c.department) IS NOT NULL AND COALESCE(NULLIF(TRIM(p.project_type), ''), c.department) != ''", NULL, FALSE)
			->where_in('COALESCE(NULLIF(TRIM(p.project_type), ""), c.department)', $allowedDepts);
		$this->db->group_by('COALESCE(NULLIF(TRIM(p.project_type), ""), c.department)', FALSE);
		$this->db->order_by("FIELD(COALESCE(NULLIF(TRIM(p.project_type), ''), c.department), 'Architectural', 'MEP', 'Structural', '3D Visualization','2D Auto CAD')", 'ASC', false);
		$q = $this->db->get();
		return $q->result();
	}
	
	
	public function recentClients(){ // Recent Clients displaying angular js 
	
	    $recentQ =	 $this->db->select('c.*,e.name,e.empId')->from('client_details as c')
												            ->join('employee_details as e ', 'e.empId = c.empId', 'left')
															->where('c.status','Active')->order_by('client_Id' , 'desc')->limit(5)->get();
																 
	     return $recentQ->result();
		 
        //echo json_encode($recentQ->result());
	
	
	 }
    
    
	 public function getProjectNWithIds($project_Id = NULL){


		
		if(!empty($project_Id)): 
		
			$project_Id      	 =	 $project_Id;
			
	   endif;

	  
	
	$projectNamesQ  	= $this->db->select('project_Id,project_name')->from('project_details')
									->where_in('project_Id',explode(',' ,$project_Id))->get()->result();
									//echo $this->db->last_query();

        return $projectNamesQ;
	}
    
    
 /****************************** Getting Result of Resource Billabilityn form client , project based on task result **************************************/
	
	
 public function searchClientReport($params){  //

		
	$client_Id 		 = 	 $params['client_Id'];
   // $project_Id      =	 $params['project_Id'];

	if(!empty($params['project_Id'])): 
	
		$project_Id      	 =	 implode(' ,' ,$params['project_Id']);
		
else:
		
		$project_Id      	 =	 'all';
		
endif;


	$form_date		 =	 $params['form_date'];
	$to_date		 = 	 $params['to_date'];

	//echo '<pre>'; print_r($project_Id); exit;
	
	$empId =  $this->session->userdata['logged_in_timesheet']['empId']; // Loged in users		
	
	 if($client_Id == 'all' && $project_Id == 'all') :   // Checking all records based on from and to dates only.
	
			$resourceBBQ = $this->db->select('er.*,GROUP_CONCAT( `comments` ) as "strengths",GROUP_CONCAT( `emp_time_hours` ) as "work_hours",SUM(er.emp_time_hours) as totalHours, emp.empId,emp.name,p.project_Id,p.project_name,p.resource_billability')
						->from('emp_record_details er')
						->where('er.emp_report_dates >= ',$form_date)			
						->where('er.emp_report_dates <= ',$to_date)
						->join('employee_details as emp', 'emp.empId=er.empId', 'left')
						->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
						->group_by('er.	empId')
						->group_by('er.	project_Id')	
						->order_by('er.emp_record_id','desc')->get();
		
	elseif($project_Id == 'all'):
	
			$resourceBBQ = $this->db->select('er.*,GROUP_CONCAT( `comments` ) as "strengths",GROUP_CONCAT( `emp_time_hours` ) as "work_hours",SUM(er.emp_time_hours) as totalHours, emp.empId,emp.name,p.project_Id,p.project_name,p.resource_billability')
					->from('emp_record_details er')
					->where('er.client_Id  = ',$client_Id)		
					->where('er.emp_report_dates >= ',$form_date)			
					->where('er.emp_report_dates <= ',$to_date)
					->join('employee_details as emp', 'emp.empId=er.empId', 'left')
					->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
					->group_by('er.	empId')
					->group_by('er.	project_Id')	
					->order_by('er.emp_record_id','desc')->get();

					//echo $this->db->last_query();
	
	else:
	
	
  $resourceBBQ = $this->db->select('er.*,GROUP_CONCAT( `comments` ) as "strengths",GROUP_CONCAT( `emp_time_hours` ) as "work_hours",SUM(er.emp_time_hours) as totalHours, emp.empId,emp.name,p.project_Id,p.project_name,p.resource_billability')
							->from('emp_record_details er')
							->where('er.client_Id  = ',$client_Id)			
							->where_in('er.project_Id',explode(',' ,$project_Id))
							->where("er.emp_report_dates BETWEEN '{$form_date}' AND '{$to_date}'")
							->join('employee_details as emp', 'emp.empId=er.empId', 'left')
							->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
							->group_by('er.	emp_report_dates' , 'er.emp_record_id')
							 ->order_by('er.emp_report_dates','asc')->get();
	
   
		
	endif;
  
return $resourceBBQ->result();
	

} 
 
    
    // Employee and Admin Search Report Log END
/****************************** Getting Result of Resource Billability form client , project based on task result **************************************/	
	 
	
/************************************ Search the client wise report functionality in excel sheet **************************** */

public function excellientReport($params){  //

		
	$client_Id 		 = 	 $params['client_Id'];
   $project_Id      =	 $params['project_Id'];
	$form_date		 =	 $params['form_date'];
	$to_date		 = 	 $params['to_date'];

	//echo '<pre>'; print_r($project_Id); exit;
	
	$empId =  $this->session->userdata['logged_in_timesheet']['empId']; // Loged in users		
	
	 
  $resourceBBQ = $this->db->select('er.*,GROUP_CONCAT( `comments` ) as "strengths",GROUP_CONCAT( `emp_time_hours` ) as "work_hours",SUM(er.emp_time_hours) as totalHours, emp.empId,emp.name,p.project_Id,p.project_name,p.resource_billability')
							->from('emp_record_details er')
							->where('er.client_Id  = ',$client_Id)			
							->where_in('er.project_Id',explode(',' ,$project_Id))
							->where("er.emp_report_dates BETWEEN '{$form_date}' AND '{$to_date}'")
							->join('employee_details as emp', 'emp.empId=er.empId', 'left')
							->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
							->group_by('er.	emp_report_dates' , 'er.emp_record_id')
							 ->order_by('er.emp_report_dates','asc')->get();
	
    echo $this->db->last_query(); 

  
return $resourceBBQ->result();
	

}

/************************************ Search the client wise report functionality in excel sheet **************************** */
	
	/************************************ Search the client wise report functionality in excel sheet **************************** */

public function allClientsReports($params){  //

		
	$client_Id 		 = 	 isset($params['client_Id']) ? $params['client_Id'] : 'all';
    $project_Id      =	 isset($params['project_Id']) ? $params['project_Id'] : 'all';
	$department      =	 isset($params['department']) ? $params['department'] : 'all';
	$project_manager =	 isset($params['project_manager']) ? $params['project_manager'] : 'all';
	$form_date		 =	 $params['form_date'];
	$to_date		 = 	 $params['to_date'];

	// Normalize to array for multi-select
	if (!is_array($client_Id)) { $client_Id = ($client_Id === 'all' || $client_Id === '') ? 'all' : array($client_Id); }
	if (!is_array($department)) { $department = ($department === 'all' || $department === '') ? 'all' : array($department); }
	if (!is_array($project_manager)) { $project_manager = ($project_manager === 'all' || $project_manager === '') ? 'all' : array($project_manager); }
	
	$empId =  $this->session->userdata['logged_in_timesheet']['empId']; // Logged in users		
	
	 if(!empty($form_date) && !empty($to_date)):
		// Month-wise invoice: sum invoice hours across full selected month range.
		$fromYm = (int) date('Ym', strtotime($form_date));
		$toYm = (int) date('Ym', strtotime($to_date));
		if ($toYm < $fromYm) {
			$tempYm = $fromYm;
			$fromYm = $toYm;
			$toYm = $tempYm;
		}

		$invoiceTotalsSubquery = '(SELECT pim.project_Id, SUM(pim.invoice_hours) as total_invoice_hours
			FROM project_invoice_monthly pim
			WHERE ((pim.invoice_year * 100) + pim.invoice_month) >= ' . $fromYm . '
			  AND ((pim.invoice_year * 100) + pim.invoice_month) <= ' . $toYm . '
			GROUP BY pim.project_Id) pim_totals';

		$resourceBBQ = $this->db->select('er.empId, er.project_Id, p.project_name, p.man_days, COALESCE(MAX(pim_totals.total_invoice_hours), 0) as project_invoice_amt, p.project_type, p.empId as project_manager_empId, SUM(er.emp_time_hours) as total_hours, COUNT(DISTINCT er.empId) as num_employees, c.client_name, c.client_Id, c.department as client_department, emp.name as project_manager_name, GROUP_CONCAT(DISTINCT emp2.name,\' \') as employee_names')
		->from('emp_record_details er')
		->join('project_details as p', 'p.project_Id = er.project_id', 'left')
		->join($invoiceTotalsSubquery, 'pim_totals.project_Id = p.project_Id', 'left')
		->join('client_details as c', 'c.client_Id = er.client_Id', 'left')
		->join('employee_details as emp', 'emp.empId = p.empId', 'left')
		->join('employee_details as emp2', 'emp2.empId = er.empId', 'left')
		->where('er.emp_report_dates >= ', $form_date)
		->where('er.emp_report_dates <= ', $to_date)
		->where("NOT (LOWER(TRIM(c.client_name)) = 'elogic solutions(nikhil)' AND LOWER(TRIM(p.project_name)) LIKE '%general%')", null, false);

		if ($client_Id !== 'all' && !empty($client_Id)) {
			$this->db->where_in('c.client_Id', $client_Id);
		}
		// Department-wise filter: use project_details.project_type when set, else client_details.department
		if ($department !== 'all' && !empty($department)) {
			$this->db->where("COALESCE(NULLIF(TRIM(p.project_type), ''), c.department) IS NOT NULL", NULL, FALSE);
			$this->db->where_in("COALESCE(NULLIF(TRIM(p.project_type), ''), c.department)", $department);
		}
		// Project Manager filter: p.empId is the project manager in project_details
		if ($project_manager !== 'all' && !empty($project_manager)) {
			$this->db->where_in('p.empId', $project_manager);
		}
		if ($project_Id !== 'all' && !empty($project_Id)) {
			$this->db->where('p.project_Id', $project_Id);
		}
		
		$resourceBBQ = $this->db->group_by('er.project_Id, er.client_Id, p.project_name, p.man_days, p.project_type, p.empId, c.department, emp.name')
		->order_by('emp.name', 'asc')
		->order_by('c.client_name', 'asc')
		->order_by('p.project_name', 'asc')
		->get();

	 return $resourceBBQ->result();
	 else:
		

		$resourceBBQ = $this->db->select('er.empId, er.project_Id, p.project_name, p.man_days, p.project_invoice_amt, p.invoice_updated_month, p.project_type, p.empId as project_manager_empId, SUM(er.emp_time_hours) as total_hours, COUNT(DISTINCT er.empId) as num_employees, c.client_name, c.client_Id, c.department as client_department, emp.name as project_manager_name, GROUP_CONCAT(DISTINCT emp2.name,\' \') as employee_names')
		->from('emp_record_details er')
		->join('project_details as p', 'p.project_Id = er.project_id', 'left')
		->join('client_details as c', 'c.client_Id = er.client_Id', 'left')
		->join('employee_details as emp', 'emp.empId = p.empId', 'left')
		->join('employee_details as emp2', 'emp2.empId = er.empId', 'left')
		->where('c.status', 'Active');

		if ($client_Id !== 'all' && !empty($client_Id)) {
			$this->db->where_in('c.client_Id', $client_Id);
		}
		// Department-wise filter: use project_details.project_type when set, else client_details.department
		if ($department !== 'all' && !empty($department)) {
			$this->db->where("COALESCE(NULLIF(TRIM(p.project_type), ''), c.department) IS NOT NULL", NULL, FALSE);
			$this->db->where_in("COALESCE(NULLIF(TRIM(p.project_type), ''), c.department)", $department);
		}
		// Project Manager filter: p.empId is the project manager in project_details
		if ($project_manager !== 'all' && !empty($project_manager)) {
			$this->db->where_in('p.empId', $project_manager);
		}
		if ($project_Id !== 'all' && !empty($project_Id)) {
			$this->db->where('p.project_Id', $project_Id);
		}
		
		$resourceBBQ = $this->db->group_by('er.project_Id, er.client_Id, p.project_name, p.man_days, p.project_invoice_amt, p.invoice_updated_month, p.project_type, p.empId, c.department, emp.name')
		->order_by('emp.name', 'asc')
		->order_by('c.client_name', 'asc')
		->order_by('p.project_name', 'asc')
		->get();
		//echo $this->db->last_query(); 
     	return $resourceBBQ->result();


	 endif; 
	

}


/************************************ Search the client wise report functionality in excel sheet **************************** */

public function getManagerwiseClients(){ // Get List of Clients Manager wise
	
			
		/* $loginUserType = $this->session->userdata['logged_in_timesheet']['empId']; 	
		
		if(in_array($loginUserType, array('146','230','149','455'))):
		
			$clientNQ  = $this->db->select('c.client_Id,c.client_name')->from('client_details as c')->where_in('department', ['MEP' , 'Middle East'])->where('status','Active')->order_by('client_Id' , 'desc')->get();
		
		elseif(in_array($loginUserType, array('41','47','71'))):
		
			$clientNQ  = $this->db->select('c.client_Id,c.client_name')->from('client_details as c')->where('department', 'Architectural')->where('status','Active')->order_by('client_Id' , 'desc')->get();
		
		elseif(in_array($loginUserType, array('53','155'))):
		
			$clientNQ  = $this->db->select('c.client_Id,c.client_name')->from('client_details as c')->where('department', '3D Visualization')->where('status','Active')->order_by('client_Id' , 'desc')->get();
		
		elseif(in_array($loginUserType, array('394'))):
		
			$clientNQ  = $this->db->select('c.client_Id,c.client_name')->from('client_details as c')->where('department', 'Structural')->where('status','Active')->order_by('client_Id' , 'desc')->get();
		
		else: 
		
			$clientNQ  = $this->db->select('c.client_Id,c.client_name')->from('client_details c')->where('status','Active')->order_by('client_Id' , 'desc')->get();
		
		endif;
		 
		 	return $clientNQ->result(); */
	
	$clientNQ  = $this->db->select('c.client_Id,c.client_name')->from('client_details c')->where('status','Active')->order_by('client_Id' , 'desc')->get();
	
		return $clientNQ->result(); 
		
	}


	/*************************************************************** Department wise showing data based on Managers  *************************************************************/

		public function departmentInsights(){

				$empId = $this->session->userdata['logged_in_timesheet']['empId']; // Logged in user's ID
				
				// Map manager IDs to departments using string keys
				$managerDepartments = [
					'146,230,149,455' => 'MEP',
					'41,47,71,53,155,394' => 'Architectural,3D Visualization,Structural,2D Auto CAD',
					'421' => 'Software',
					'46' => 'IT',
				];

				// Check which department group the manager belongs to
				foreach($managerDepartments as $managerIds => $department) {
					$managerIdArray = explode(',', $managerIds);
					if(in_array($empId, $managerIdArray)) {
						return $department;
					}
				}

				// If not found in any group, get from database
				$query = $this->db->select('e.department')
								 ->from('employee_details e')
								 ->where('e.status', 'Active')
								 ->where('e.empId', $empId)
								 ->where('e.user_type', 'manager')
								 ->get();

				$result = $query->row();
				
				return $result ? $result->department : null;
				
		}

	/*************************************************************** Department wise showing data based on Managers  *************************************************************/
    
    public function getReportMasterClients(){
        
    $department = $this->input->post('department');
	$form_date = $this->input->post('form_date');
	$to_date = $this->input->post('to_date');

	//Fetch departments along with client models and their associated project 
	$getDepartmentCall = $this->client_model->departmentInsights();
			 
	if(in_array($this->session->userdata['logged_in_timesheet']['user_type'], array('admin','business_head'))){
	
		$logedInUser = $this->session->userdata['logged_in_timesheet']['empId'];
	
	$clientSQ = $this->db->select('c.*,e.name,e.empId')
				->from('client_details as c')
				->join('employee_details as e ', 'c.empId = e.empId', 'left');
				//->where_in('c.department', explode(',', $getDepartmentCall));
	
				if($department != 'all') {
					
					if(in_array($this->session->userdata['logged_in_timesheet']['user_type'], array('business_head'))){

						$this->db->where('c.department', $department);
						
					}else{

						$this->db->where('c.department', $department);

					}

				}				
				if(!empty($form_date) && !empty($to_date)) {
					$this->db->where('DATE(c.created_at) >=', $form_date);
					$this->db->where('DATE(c.created_at) <=', $to_date);
				}
	
	$clientSQ = $this->db->order_by('c.created_at', 'desc')->get();
											
   }
 return $clientSQ->result();
    
}
	
/****************************** Getting Result of Resource Scheudle vs Timesheet Report log entries information**************************************/
	
	
public function compareResouceTimesheet($params){  //


	$client_Id 		 = 	 $params['client_Id'];
    $project_Id      =	 'all';
	$form_date		 =	 $params['form_date'];
	$to_date		 = 	 $params['to_date'];

	
	
	$empId =  $this->session->userdata['logged_in_timesheet']['empId']; // Logged in users		
	
	 if(!empty($form_date) && !empty($to_date)):

		if($client_Id  == 'all'){
		
		// Pre-aggregate by (project, client, date, reporting manager) so team-member hours roll up manager-wise.
		$rs_per_day = "(SELECT rs.project_Id, rs.client_Id, DATE(rs.emp_report_dates) as report_date, CASE WHEN COALESCE(ed.reporting_manger, 0) > 0 AND COALESCE(ed.reporting_manger, 0) <> COALESCE(ed.empId, 0) THEN ed.reporting_manger ELSE 0 END as manager_empId, SUM(rs.emp_time_hours) as day_schedule_hours FROM resource_schedule_information rs LEFT JOIN employee_details ed ON ed.empId = rs.team_member WHERE DATE(rs.emp_report_dates) >= " . $this->db->escape($form_date) . " AND DATE(rs.emp_report_dates) <= " . $this->db->escape($to_date) . " GROUP BY rs.project_Id, rs.client_Id, DATE(rs.emp_report_dates), CASE WHEN COALESCE(ed.reporting_manger, 0) > 0 AND COALESCE(ed.reporting_manger, 0) <> COALESCE(ed.empId, 0) THEN ed.reporting_manger ELSE 0 END) rs_per_day";
		$ts_per_day = "(SELECT er.project_Id, er.client_Id, DATE(er.emp_report_dates) as report_date, CASE WHEN COALESCE(ed.reporting_manger, 0) > 0 AND COALESCE(ed.reporting_manger, 0) <> COALESCE(ed.empId, 0) THEN ed.reporting_manger ELSE 0 END as manager_empId, SUM(er.emp_time_hours) as day_ts_hours, SUM(CASE WHEN er.status = 'Approved' THEN er.emp_time_hours ELSE 0 END) as day_ts_approved_hours, SUM(CASE WHEN er.status <> 'Approved' OR er.status IS NULL THEN er.emp_time_hours ELSE 0 END) as day_ts_unapproved_hours FROM emp_record_details er LEFT JOIN employee_details ed ON ed.empId = er.empId WHERE DATE(er.emp_report_dates) >= " . $this->db->escape($form_date) . " AND DATE(er.emp_report_dates) <= " . $this->db->escape($to_date) . " GROUP BY er.project_Id, er.client_Id, DATE(er.emp_report_dates), CASE WHEN COALESCE(ed.reporting_manger, 0) > 0 AND COALESCE(ed.reporting_manger, 0) <> COALESCE(ed.empId, 0) THEN ed.reporting_manger ELSE 0 END) ts_per_day";
		$emp_names_inner = "(SELECT project_Id, client_Id, manager_empId, GROUP_CONCAT(DISTINCT name ORDER BY name SEPARATOR ' ') as employee_names FROM (
			SELECT rs.project_Id, rs.client_Id, CASE WHEN COALESCE(ed.reporting_manger, 0) > 0 AND COALESCE(ed.reporting_manger, 0) <> COALESCE(ed.empId, 0) THEN ed.reporting_manger ELSE 0 END as manager_empId, ed.name FROM resource_schedule_information rs INNER JOIN employee_details ed ON ed.empId = rs.team_member WHERE DATE(rs.emp_report_dates) >= " . $this->db->escape($form_date) . " AND DATE(rs.emp_report_dates) <= " . $this->db->escape($to_date) . "
			UNION
			SELECT er.project_Id, er.client_Id, CASE WHEN COALESCE(ed.reporting_manger, 0) > 0 AND COALESCE(ed.reporting_manger, 0) <> COALESCE(ed.empId, 0) THEN ed.reporting_manger ELSE 0 END as manager_empId, ed.name FROM emp_record_details er INNER JOIN employee_details ed ON ed.empId = er.empId WHERE DATE(er.emp_report_dates) >= " . $this->db->escape($form_date) . " AND DATE(er.emp_report_dates) <= " . $this->db->escape($to_date) . " AND er.status = 'Approved'
		) u GROUP BY project_Id, client_Id, manager_empId)";
		$emp_names_scalar = "(SELECT en.employee_names FROM " . $emp_names_inner . " en WHERE en.project_Id = rs_per_day.project_Id AND en.client_Id = rs_per_day.client_Id AND en.manager_empId = rs_per_day.manager_empId LIMIT 1)";

		// Query 1: Resource schedule with timesheet (date-wise aggregated to avoid double-count)
		$query1 = $this->db->select('NULL as empId,
			rs_per_day.project_Id, 
			p.project_name, 
			COALESCE(SUM(ts_per_day.day_ts_hours), 0) as timesheet_hours,
			COALESCE(SUM(ts_per_day.day_ts_approved_hours), 0) as approved_timesheet_hours,
			COALESCE(SUM(ts_per_day.day_ts_unapproved_hours), 0) as unapproved_timesheet_hours,
			COALESCE(SUM(rs_per_day.day_schedule_hours), 0) as schedule_hours,
			0 as num_employees,
			c.client_name, 
			rs_per_day.client_Id, 
			COALESCE(NULLIF(TRIM(rm.name), \'\'), \'N/A\') as project_manager_name,
			COALESCE(NULLIF(TRIM(p.project_type), \'\'), c.department) as department,
			COALESCE(' . $emp_names_scalar . ', \'\') as employee_names', FALSE)
		->from($rs_per_day)
		->join($ts_per_day, 'rs_per_day.project_Id = ts_per_day.project_Id AND rs_per_day.client_Id = ts_per_day.client_Id AND rs_per_day.report_date = ts_per_day.report_date AND rs_per_day.manager_empId = ts_per_day.manager_empId', 'left')
		->join('project_details as p', 'p.project_Id = rs_per_day.project_Id', 'left')
		->join('client_details as c', 'c.client_Id = rs_per_day.client_Id', 'left')
		->join('employee_details as emp', 'emp.empId = p.empId', 'left')
		->join('employee_details as rm', 'rm.empId = rs_per_day.manager_empId', 'left')
		->group_by('rs_per_day.project_Id, rs_per_day.client_Id, rs_per_day.manager_empId, p.project_name, c.client_name, rm.name, emp.name, p.project_type, c.department')
		->get_compiled_select();
		
		// Reset query builder
		$this->db->reset_query();
		
		// Query 2: Get timesheet data that doesn't have resource schedule
		$rs_check = "(SELECT rs.project_Id, rs.client_Id, DATE(rs.emp_report_dates) as report_date, CASE WHEN COALESCE(ed.reporting_manger, 0) > 0 AND COALESCE(ed.reporting_manger, 0) <> COALESCE(ed.empId, 0) THEN ed.reporting_manger ELSE 0 END as manager_empId FROM resource_schedule_information rs LEFT JOIN employee_details ed ON ed.empId = rs.team_member WHERE DATE(rs.emp_report_dates) >= " . $this->db->escape($form_date) . " AND DATE(rs.emp_report_dates) <= " . $this->db->escape($to_date) . " GROUP BY rs.project_Id, rs.client_Id, DATE(rs.emp_report_dates), CASE WHEN COALESCE(ed.reporting_manger, 0) > 0 AND COALESCE(ed.reporting_manger, 0) <> COALESCE(ed.empId, 0) THEN ed.reporting_manger ELSE 0 END) rs_check";
		$query2 = $this->db->select('er.empId, 
			er.project_Id, 
			p.project_name, 
			SUM(er.emp_time_hours) as timesheet_hours,
			SUM(CASE WHEN er.status = \'Approved\' THEN er.emp_time_hours ELSE 0 END) as approved_timesheet_hours,
			SUM(CASE WHEN er.status <> \'Approved\' OR er.status IS NULL THEN er.emp_time_hours ELSE 0 END) as unapproved_timesheet_hours,
			0 as schedule_hours,
			COUNT(DISTINCT er.empId) as num_employees, 
			c.client_name, 
			er.client_Id, 
			COALESCE(NULLIF(TRIM(emp_rm.name), \'\'), \'N/A\') as project_manager_name,
			COALESCE(NULLIF(TRIM(p.project_type), \'\'), c.department) as department,
			GROUP_CONCAT(DISTINCT emp2.name,\' \') as employee_names')
		->from('emp_record_details er')
		->join('employee_details as emp2', 'emp2.empId = er.empId', 'left')
		->join($rs_check, 'rs_check.project_Id = er.project_Id AND rs_check.client_Id = er.client_Id AND rs_check.report_date = DATE(er.emp_report_dates) AND rs_check.manager_empId = CASE WHEN COALESCE(emp2.reporting_manger, 0) > 0 AND COALESCE(emp2.reporting_manger, 0) <> COALESCE(emp2.empId, 0) THEN emp2.reporting_manger ELSE 0 END', 'left', false)
		->where('DATE(er.emp_report_dates) >= ', $form_date)
		->where('DATE(er.emp_report_dates) <= ', $to_date)
		->where('rs_check.project_Id IS NULL', null, false)
		->join('project_details as p', 'p.project_Id = er.project_id', 'left')
		->join('client_details as c', 'c.client_Id = er.client_Id', 'left')
		->join('employee_details as emp', 'emp.empId = p.empId', 'left')
		->join('employee_details as emp_rm', 'emp_rm.empId = CASE WHEN COALESCE(emp2.reporting_manger, 0) > 0 AND COALESCE(emp2.reporting_manger, 0) <> COALESCE(emp2.empId, 0) THEN emp2.reporting_manger ELSE 0 END', 'left')
		->group_by('er.project_Id, er.client_Id, CASE WHEN COALESCE(emp2.reporting_manger, 0) > 0 AND COALESCE(emp2.reporting_manger, 0) <> COALESCE(emp2.empId, 0) THEN emp2.reporting_manger ELSE 0 END, p.project_name, c.client_name, emp_rm.name, emp.name, p.project_type, c.department')
		->get_compiled_select();
		
		// Union both queries
		$resourceBBQ = $this->db->query("($query1) UNION ($query2) ORDER BY client_name ASC, project_name ASC");

		}else{

			// Pre-aggregate by (project, client, date, reporting manager) for specific client.
			$rs_per_day = "(SELECT rs.project_Id, rs.client_Id, DATE(rs.emp_report_dates) as report_date, CASE WHEN COALESCE(ed.reporting_manger, 0) > 0 AND COALESCE(ed.reporting_manger, 0) <> COALESCE(ed.empId, 0) THEN ed.reporting_manger ELSE 0 END as manager_empId, SUM(rs.emp_time_hours) as day_schedule_hours FROM resource_schedule_information rs LEFT JOIN employee_details ed ON ed.empId = rs.team_member WHERE DATE(rs.emp_report_dates) >= " . $this->db->escape($form_date) . " AND DATE(rs.emp_report_dates) <= " . $this->db->escape($to_date) . " AND rs.client_Id = " . $this->db->escape($client_Id) . " GROUP BY rs.project_Id, rs.client_Id, DATE(rs.emp_report_dates), CASE WHEN COALESCE(ed.reporting_manger, 0) > 0 AND COALESCE(ed.reporting_manger, 0) <> COALESCE(ed.empId, 0) THEN ed.reporting_manger ELSE 0 END) rs_per_day";
			$ts_per_day = "(SELECT er.project_Id, er.client_Id, DATE(er.emp_report_dates) as report_date, CASE WHEN COALESCE(ed.reporting_manger, 0) > 0 AND COALESCE(ed.reporting_manger, 0) <> COALESCE(ed.empId, 0) THEN ed.reporting_manger ELSE 0 END as manager_empId, SUM(er.emp_time_hours) as day_ts_hours, SUM(CASE WHEN er.status = 'Approved' THEN er.emp_time_hours ELSE 0 END) as day_ts_approved_hours, SUM(CASE WHEN er.status <> 'Approved' OR er.status IS NULL THEN er.emp_time_hours ELSE 0 END) as day_ts_unapproved_hours FROM emp_record_details er LEFT JOIN employee_details ed ON ed.empId = er.empId WHERE DATE(er.emp_report_dates) >= " . $this->db->escape($form_date) . " AND DATE(er.emp_report_dates) <= " . $this->db->escape($to_date) . " AND er.client_Id = " . $this->db->escape($client_Id) . " GROUP BY er.project_Id, er.client_Id, DATE(er.emp_report_dates), CASE WHEN COALESCE(ed.reporting_manger, 0) > 0 AND COALESCE(ed.reporting_manger, 0) <> COALESCE(ed.empId, 0) THEN ed.reporting_manger ELSE 0 END) ts_per_day";
			$emp_names_inner = "(SELECT project_Id, client_Id, manager_empId, GROUP_CONCAT(DISTINCT name ORDER BY name SEPARATOR ' ') as employee_names FROM (
				SELECT rs.project_Id, rs.client_Id, CASE WHEN COALESCE(ed.reporting_manger, 0) > 0 AND COALESCE(ed.reporting_manger, 0) <> COALESCE(ed.empId, 0) THEN ed.reporting_manger ELSE 0 END as manager_empId, ed.name FROM resource_schedule_information rs INNER JOIN employee_details ed ON ed.empId = rs.team_member WHERE DATE(rs.emp_report_dates) >= " . $this->db->escape($form_date) . " AND DATE(rs.emp_report_dates) <= " . $this->db->escape($to_date) . " AND rs.client_Id = " . $this->db->escape($client_Id) . "
				UNION
				SELECT er.project_Id, er.client_Id, CASE WHEN COALESCE(ed.reporting_manger, 0) > 0 AND COALESCE(ed.reporting_manger, 0) <> COALESCE(ed.empId, 0) THEN ed.reporting_manger ELSE 0 END as manager_empId, ed.name FROM emp_record_details er INNER JOIN employee_details ed ON ed.empId = er.empId WHERE DATE(er.emp_report_dates) >= " . $this->db->escape($form_date) . " AND DATE(er.emp_report_dates) <= " . $this->db->escape($to_date) . " AND er.client_Id = " . $this->db->escape($client_Id) . " AND er.status = 'Approved'
			) u GROUP BY project_Id, client_Id, manager_empId)";
			$emp_names_scalar = "(SELECT en.employee_names FROM " . $emp_names_inner . " en WHERE en.project_Id = rs_per_day.project_Id AND en.client_Id = rs_per_day.client_Id AND en.manager_empId = rs_per_day.manager_empId LIMIT 1)";

			// Query 1: Resource schedule with timesheet (date-wise aggregated) for specific client
			$query1 = $this->db->select('NULL as empId,
				rs_per_day.project_Id, 
				p.project_name, 
				COALESCE(SUM(ts_per_day.day_ts_hours), 0) as timesheet_hours,
				COALESCE(SUM(ts_per_day.day_ts_approved_hours), 0) as approved_timesheet_hours,
				COALESCE(SUM(ts_per_day.day_ts_unapproved_hours), 0) as unapproved_timesheet_hours,
				COALESCE(SUM(rs_per_day.day_schedule_hours), 0) as schedule_hours,
				0 as num_employees,
				c.client_name, 
				rs_per_day.client_Id, 
				COALESCE(NULLIF(TRIM(rm.name), \'\'), \'N/A\') as project_manager_name,
				COALESCE(NULLIF(TRIM(p.project_type), \'\'), c.department) as department,
				COALESCE(' . $emp_names_scalar . ', \'\') as employee_names', FALSE)
			->from($rs_per_day)
			->join($ts_per_day, 'rs_per_day.project_Id = ts_per_day.project_Id AND rs_per_day.client_Id = ts_per_day.client_Id AND rs_per_day.report_date = ts_per_day.report_date AND rs_per_day.manager_empId = ts_per_day.manager_empId', 'left')
			->join('project_details as p', 'p.project_Id = rs_per_day.project_Id', 'left')
			->join('client_details as c', 'c.client_Id = rs_per_day.client_Id', 'left')
			->join('employee_details as emp', 'emp.empId = p.empId', 'left')
			->join('employee_details as rm', 'rm.empId = rs_per_day.manager_empId', 'left')
			->group_by('rs_per_day.project_Id, rs_per_day.client_Id, rs_per_day.manager_empId, p.project_name, c.client_name, rm.name, emp.name, p.project_type, c.department')
			->get_compiled_select();
			
			// Reset query builder
			$this->db->reset_query();
			
			// Query 2: Get timesheet data that doesn't have resource schedule for specific client
			$rs_check = "(SELECT rs.project_Id, rs.client_Id, DATE(rs.emp_report_dates) as report_date, CASE WHEN COALESCE(ed.reporting_manger, 0) > 0 AND COALESCE(ed.reporting_manger, 0) <> COALESCE(ed.empId, 0) THEN ed.reporting_manger ELSE 0 END as manager_empId FROM resource_schedule_information rs LEFT JOIN employee_details ed ON ed.empId = rs.team_member WHERE DATE(rs.emp_report_dates) >= " . $this->db->escape($form_date) . " AND DATE(rs.emp_report_dates) <= " . $this->db->escape($to_date) . " AND rs.client_Id = " . $this->db->escape($client_Id) . " GROUP BY rs.project_Id, rs.client_Id, DATE(rs.emp_report_dates), CASE WHEN COALESCE(ed.reporting_manger, 0) > 0 AND COALESCE(ed.reporting_manger, 0) <> COALESCE(ed.empId, 0) THEN ed.reporting_manger ELSE 0 END) rs_check";
			$query2 = $this->db->select('er.empId, 
				er.project_Id, 
				p.project_name, 
				SUM(er.emp_time_hours) as timesheet_hours,
				SUM(CASE WHEN er.status = \'Approved\' THEN er.emp_time_hours ELSE 0 END) as approved_timesheet_hours,
				SUM(CASE WHEN er.status <> \'Approved\' OR er.status IS NULL THEN er.emp_time_hours ELSE 0 END) as unapproved_timesheet_hours,
				0 as schedule_hours,
				COUNT(DISTINCT er.empId) as num_employees, 
				c.client_name, 
				er.client_Id, 
				COALESCE(NULLIF(TRIM(emp_rm.name), \'\'), \'N/A\') as project_manager_name,
				COALESCE(NULLIF(TRIM(p.project_type), \'\'), c.department) as department,
				GROUP_CONCAT(DISTINCT emp2.name,\' \') as employee_names')
			->from('emp_record_details er')
			->join('employee_details as emp2', 'emp2.empId = er.empId', 'left')
			->join($rs_check, 'rs_check.project_Id = er.project_Id AND rs_check.client_Id = er.client_Id AND rs_check.report_date = DATE(er.emp_report_dates) AND rs_check.manager_empId = CASE WHEN COALESCE(emp2.reporting_manger, 0) > 0 AND COALESCE(emp2.reporting_manger, 0) <> COALESCE(emp2.empId, 0) THEN emp2.reporting_manger ELSE 0 END', 'left', false)
			->where('DATE(er.emp_report_dates) >= ', $form_date)
			->where('DATE(er.emp_report_dates) <= ', $to_date)
			->where('er.client_Id = ',$client_Id)
			->where('rs_check.project_Id IS NULL', null, false)
			->join('project_details as p', 'p.project_Id = er.project_id', 'left')
			->join('client_details as c', 'c.client_Id = er.client_Id', 'left')
			->join('employee_details as emp', 'emp.empId = p.empId', 'left')
			->join('employee_details as emp_rm', 'emp_rm.empId = CASE WHEN COALESCE(emp2.reporting_manger, 0) > 0 AND COALESCE(emp2.reporting_manger, 0) <> COALESCE(emp2.empId, 0) THEN emp2.reporting_manger ELSE 0 END', 'left')
			->group_by('er.project_Id, er.client_Id, CASE WHEN COALESCE(emp2.reporting_manger, 0) > 0 AND COALESCE(emp2.reporting_manger, 0) <> COALESCE(emp2.empId, 0) THEN emp2.reporting_manger ELSE 0 END, p.project_name, c.client_name, emp_rm.name, emp.name, p.project_type, c.department')
			->get_compiled_select();
			
			// Union both queries
			$resourceBBQ = $this->db->query("($query1) UNION ($query2) ORDER BY client_name ASC, project_name ASC");

		}

		//echo 'kanth==='.$this->db->last_query();
		return $resourceBBQ->result();

	 else:
		

		$resourceBBQ = $this->db->select('er.empId, er.project_Id, p.project_name, SUM(er.emp_time_hours) as total_hours, COUNT(DISTINCT er.empId) as num_employees, c.client_name, c.client_Id, emp.name as project_manager_name, GROUP_CONCAT(DISTINCT emp2.name,\' \') as employee_names')
		->from('emp_record_details er')
		->where('c.status', 'Active')
		->join('project_details as p', 'p.project_Id = er.project_id', 'left')
		->join('client_details as c', 'c.client_Id = er.client_Id', 'left')
		->join('employee_details as emp', 'emp.empId = p.empId', 'left') // Assuming project_manager_id is the field in project_details that links to the project manager's empId
		->join('employee_details as emp2', 'emp2.empId = er.empId', 'left') // Joining employee_details again to get the employee name
		->group_by('er.project_Id, er.client_Id')
		->order_by('c.client_name', 'asc')
		->order_by('p.project_name', 'asc')
		->get();
		//echo $this->db->last_query(); 
     	return $resourceBBQ->result();


	 endif; 
	

} 
  
/******************************Getting Result of Resource Scheudle vs Timesheet Report log entries information **************************************/	

/**
 * Returns team members (grouped by their reporting manager) who did not fully
 * fill their timesheet during the given date range. Uses the same day-wise
 * rule as the Timesheet Defaulter report: each working day must have at least
 * 8.5 hours entered (leave days are excluded).
 *
 * Output: array keyed by lowercased manager name, each value is an array of
 *   { member_empId, member_name, scheduled_hours, timesheet_hours, unfilled_days }
 */
public function getRsVsTsDefaultersByManager($formDate, $toDate, $clientId = 'all'){

	if(empty($formDate) || empty($toDate)){
		return array();
	}

	$fromEsc = $this->db->escape($formDate);
	$toEsc   = $this->db->escape($toDate);

	$this->db->select('emp.empId AS member_empId, emp.name AS member_name, mgr.empId AS manager_empId, mgr.name AS manager_name')
		->from('employee_details emp')
		->join('employee_details mgr', 'mgr.empId = emp.reporting_manger', 'inner')
		->where('emp.status', 'Active')
		->where('emp.reporting_manger >', 0)
		->where('emp.reporting_manger <> emp.empId', null, false);

	if(!empty($clientId) && $clientId !== 'all'){
		$cidEsc = $this->db->escape($clientId);
		$this->db->where(
			"emp.empId IN (SELECT DISTINCT rs.team_member FROM resource_schedule_information rs WHERE rs.client_Id = $cidEsc AND DATE(rs.emp_report_dates) >= $fromEsc AND DATE(rs.emp_report_dates) <= $toEsc)",
			null,
			false
		);
	}else{
		$this->db->where(
			"emp.empId IN (SELECT DISTINCT rs.team_member FROM resource_schedule_information rs WHERE DATE(rs.emp_report_dates) >= $fromEsc AND DATE(rs.emp_report_dates) <= $toEsc)",
			null,
			false
		);
	}

	$employees = $this->db->order_by('mgr.name', 'asc')->order_by('emp.name', 'asc')->get()->result();
	if(empty($employees)){
		return array();
	}

	$employeeIds = array();
	foreach($employees as $employee){
		$employeeIds[] = $employee->member_empId;
	}

	$this->load->model('defaulter_model');
	$hoursMatrix = $this->defaulter_model->getEmployeeHoursSummary($employeeIds, $formDate, $toDate);
	$workingDates = $this->_getRsVsTsWorkingDates($formDate, $toDate);
	$expectedDailyHours = 8.5;
	$grouped = array();

	foreach($employees as $employee){
		$memberHours = isset($hoursMatrix[$employee->member_empId]) ? $hoursMatrix[$employee->member_empId] : array();
		$unfilledDays = 0;
		$totalTimesheetHours = 0;

		foreach($workingDates as $date){
			$dayRecord = isset($memberHours[$date]) ? $memberHours[$date] : null;
			if($dayRecord){
				$totalTimesheetHours += (float)$dayRecord['hours'];
				if(empty($dayRecord['is_leave']) && (float)$dayRecord['hours'] < $expectedDailyHours){
					$unfilledDays++;
				}
			}else{
				$unfilledDays++;
			}
		}

		if($unfilledDays <= 0){
			continue;
		}

		$mgrName = isset($employee->manager_name) ? trim((string)$employee->manager_name) : '';
		if($mgrName === ''){
			continue;
		}

		$key = strtolower(preg_replace('/\s+/', ' ', $mgrName));
		if(!isset($grouped[$key])){
			$grouped[$key] = array();
		}

		$expectedHours = count($workingDates) * $expectedDailyHours;
		$grouped[$key][] = (object) array(
			'member_empId' => $employee->member_empId,
			'member_name' => $employee->member_name,
			'manager_empId' => $employee->manager_empId,
			'manager_name' => $mgrName,
			'scheduled_hours' => $expectedHours,
			'timesheet_hours' => $totalTimesheetHours,
			'unfilled_days' => $unfilledDays
		);
	}

	return $grouped;
}

/**
 * Working days in range for RS vs TS defaulter checks (weekends and holidays excluded).
 */
private function _getRsVsTsWorkingDates($fromDate, $toDate){
	$holidayDates = array(
		'2026-01-01', '2026-01-15', '2026-01-26', '2026-05-01',
		'2026-10-02', '2026-10-20', '2026-11-09', '2026-12-25',
		'2026-03-19', '2026-09-14'
	);
	$dates = array();
	$current = new DateTime($fromDate);
	$end = new DateTime($toDate);
	while($current <= $end){
		$currentDate = $current->format('Y-m-d');
		$currentDay = $current->format('D');
		if($currentDay !== 'Sat' && $currentDay !== 'Sun' && !in_array($currentDate, $holidayDates, true)){
			$dates[] = $currentDate;
		}
		$current->modify('+1 day');
	}
	return $dates;
}
	
/****************************** Getting Project execution status information *****************************************************************/
	
	
public function projectExecutionStatusInformation($params){  //


	$client_Id 		 = 	 $params['client_Id'];
    $project_Id      =	 'all';
	$form_date		 =	 $params['form_date'];
	$to_date		 = 	 $params['to_date'];

	
	
	$empId =  $this->session->userdata['logged_in_timesheet']['empId']; // Logged in users		
	
	 if(!empty($form_date) && !empty($to_date)):

		if($client_Id  == 'all'){
		
		// Subquery: project-wise total timesheet hours (all entered hours, not filtered by from/to date)
		$ts_totals_subquery = '(SELECT project_Id, client_Id, SUM(emp_time_hours) as total_timesheet_hours FROM emp_record_details GROUP BY project_Id, client_Id) ts_totals';
		
		// Query 1: Get resource schedule data; timesheet_hours = project total (all time)
		$query1 = $this->db->select('COALESCE(er.empId, rs.team_member) as empId, 
			COALESCE(er.project_Id, rs.project_Id) as project_Id, 
			p.project_name, 
			p.estimated_hours,
			p.project_start_date,
			p.project_end_date,
			p.man_days,
			COALESCE(p.project_invoice_amt, 0) as project_invoice_amt,
			COALESCE(ts_totals.total_timesheet_hours, 0) as timesheet_hours,
			COALESCE(SUM(rs.emp_time_hours), 0) as schedule_hours,
			COUNT(DISTINCT COALESCE(er.empId, rs.team_member)) as num_employees, 
			c.client_name, 
			COALESCE(er.client_Id, rs.client_Id) as client_Id, 
			emp.name as project_manager_name,
			GROUP_CONCAT(DISTINCT COALESCE(emp2.name, emp3.name),\' \') as employee_names')
		->from('resource_schedule_information rs')
		->join($ts_totals_subquery, 'ts_totals.project_Id = rs.project_Id AND ts_totals.client_Id = rs.client_Id', 'left')
		->join('emp_record_details er', 'rs.project_Id = er.project_Id AND DATE(rs.emp_report_dates) = DATE(er.emp_report_dates) AND rs.client_Id = er.client_Id', 'left')
		->where('DATE(rs.emp_report_dates) >= ', $form_date)
		->where('DATE(rs.emp_report_dates) <= ', $to_date)
		->join('project_details as p', 'p.project_Id = rs.project_Id', 'left')
		->join('client_details as c', 'c.client_Id = rs.client_Id', 'left')
		->join('employee_details as emp', 'emp.empId = p.empId', 'left')
		->join('employee_details as emp2', 'emp2.empId = er.empId', 'left')
		->join('employee_details as emp3', 'emp3.empId = rs.team_member', 'left')
		->group_by('rs.project_Id, rs.client_Id, p.project_name, p.estimated_hours, p.project_start_date, p.project_end_date, p.man_days, p.project_invoice_amt, ts_totals.total_timesheet_hours')
		->get_compiled_select();
		
		// Reset query builder
		$this->db->reset_query();
		
		// Query 2: Get timesheet data that doesn't have resource schedule; timesheet_hours = project total (all time)
		$query2 = $this->db->select('er.empId, 
			er.project_Id, 
			p.project_name, 
			p.estimated_hours,
			p.project_start_date,
			p.project_end_date,
			p.man_days,
			COALESCE(p.project_invoice_amt, 0) as project_invoice_amt,
			COALESCE(ts_totals.total_timesheet_hours, 0) as timesheet_hours,
			0 as schedule_hours,
			COUNT(DISTINCT er.empId) as num_employees, 
			c.client_name, 
			er.client_Id, 
			emp.name as project_manager_name,
			GROUP_CONCAT(DISTINCT emp2.name,\' \') as employee_names')
		->from('emp_record_details er')
		->join($ts_totals_subquery, 'ts_totals.project_Id = er.project_Id AND ts_totals.client_Id = er.client_Id', 'left')
		->join('resource_schedule_information rs', 'rs.project_Id = er.project_Id AND DATE(rs.emp_report_dates) = DATE(er.emp_report_dates) AND rs.client_Id = er.client_Id', 'left')
		->where('DATE(er.emp_report_dates) >= ', $form_date)
		->where('DATE(er.emp_report_dates) <= ', $to_date)
		->where('rs.emp_report_dates IS NULL')
		->join('project_details as p', 'p.project_Id = er.project_id', 'left')
		->join('client_details as c', 'c.client_Id = er.client_Id', 'left')
		->join('employee_details as emp', 'emp.empId = p.empId', 'left')
		->join('employee_details as emp2', 'emp2.empId = er.empId', 'left')
		->group_by('er.project_Id, er.client_Id, p.project_name, p.estimated_hours, p.project_start_date, p.project_end_date, p.man_days, p.project_invoice_amt, ts_totals.total_timesheet_hours')
		->get_compiled_select();
		
		// Union both queries
		$resourceBBQ = $this->db->query("($query1) UNION ($query2) ORDER BY client_name ASC, project_name ASC");

		}else{

			// Subquery: project-wise total timesheet hours (all entered hours, not filtered by from/to date)
			$ts_totals_subquery = '(SELECT project_Id, client_Id, SUM(emp_time_hours) as total_timesheet_hours FROM emp_record_details GROUP BY project_Id, client_Id) ts_totals';

			// Query 1: Get resource schedule data with timesheet data (if exists) for specific client; timesheet_hours = project total (all time)
			$query1 = $this->db->select('COALESCE(er.empId, rs.team_member) as empId, 
				COALESCE(er.project_Id, rs.project_Id) as project_Id, 
				p.project_name, 
				p.estimated_hours,
				p.project_start_date,
				p.project_end_date,
				p.man_days,
				COALESCE(p.project_invoice_amt, 0) as project_invoice_amt,
				COALESCE(ts_totals.total_timesheet_hours, 0) as timesheet_hours,
				COALESCE(SUM(rs.emp_time_hours), 0) as schedule_hours,
				COUNT(DISTINCT COALESCE(er.empId, rs.team_member)) as num_employees, 
				c.client_name, 
				COALESCE(er.client_Id, rs.client_Id) as client_Id, 
				emp.name as project_manager_name,
				GROUP_CONCAT(DISTINCT COALESCE(emp2.name, emp3.name),\' \') as employee_names')
			->from('resource_schedule_information rs')
			->join($ts_totals_subquery, 'ts_totals.project_Id = rs.project_Id AND ts_totals.client_Id = rs.client_Id', 'left')
			->join('emp_record_details er', 'rs.project_Id = er.project_Id AND DATE(rs.emp_report_dates) = DATE(er.emp_report_dates) AND rs.client_Id = er.client_Id', 'left')
			->where('DATE(rs.emp_report_dates) >= ', $form_date)
			->where('DATE(rs.emp_report_dates) <= ', $to_date)
			->where('rs.client_Id = ',$client_Id)
			->join('project_details as p', 'p.project_Id = rs.project_Id', 'left')
			->join('client_details as c', 'c.client_Id = rs.client_Id', 'left')
			->join('employee_details as emp', 'emp.empId = p.empId', 'left')
			->join('employee_details as emp2', 'emp2.empId = er.empId', 'left')
			->join('employee_details as emp3', 'emp3.empId = rs.team_member', 'left')
			->group_by('rs.project_Id, rs.client_Id, p.project_name, p.estimated_hours, p.project_start_date, p.project_end_date, p.man_days, p.project_invoice_amt, ts_totals.total_timesheet_hours')
			->get_compiled_select();
			
			// Reset query builder
			$this->db->reset_query();
			
			// Query 2: Get timesheet data that doesn't have resource schedule for specific client; timesheet_hours = project total (all time)
			$query2 = $this->db->select('er.empId, 
				er.project_Id, 
				p.project_name, 
				p.estimated_hours,
				p.project_start_date,
				p.project_end_date,
				p.man_days,
				COALESCE(p.project_invoice_amt, 0) as project_invoice_amt,
				COALESCE(ts_totals.total_timesheet_hours, 0) as timesheet_hours,
				0 as schedule_hours,
				COUNT(DISTINCT er.empId) as num_employees, 
				c.client_name, 
				er.client_Id, 
				emp.name as project_manager_name,
				GROUP_CONCAT(DISTINCT emp2.name,\' \') as employee_names')
			->from('emp_record_details er')
			->join($ts_totals_subquery, 'ts_totals.project_Id = er.project_Id AND ts_totals.client_Id = er.client_Id', 'left')
			->join('resource_schedule_information rs', 'rs.project_Id = er.project_Id AND DATE(rs.emp_report_dates) = DATE(er.emp_report_dates) AND rs.client_Id = er.client_Id', 'left')
			->where('DATE(er.emp_report_dates) >= ', $form_date)
			->where('DATE(er.emp_report_dates) <= ', $to_date)
			->where('er.client_Id = ',$client_Id)
			->where('rs.emp_report_dates IS NULL')
			->join('project_details as p', 'p.project_Id = er.project_id', 'left')
			->join('client_details as c', 'c.client_Id = er.client_Id', 'left')
			->join('employee_details as emp', 'emp.empId = p.empId', 'left')
			->join('employee_details as emp2', 'emp2.empId = er.empId', 'left')
			->group_by('er.project_Id, er.client_Id, p.project_name, p.estimated_hours, p.project_start_date, p.project_end_date, p.man_days, p.project_invoice_amt, ts_totals.total_timesheet_hours')
			->get_compiled_select();
			
			// Union both queries
			$resourceBBQ = $this->db->query("($query1) UNION ($query2) ORDER BY client_name ASC, project_name ASC");

		}

		//echo 'kanth==='.$this->db->last_query();
		return $resourceBBQ->result();

	 else:
		
		
		$resourceBBQ = $this->db->select('er.empId, er.project_Id, p.project_name, SUM(er.emp_time_hours) as total_hours, COUNT(DISTINCT er.empId) as num_employees, c.client_name, c.client_Id, emp.name as project_manager_name, GROUP_CONCAT(DISTINCT emp2.name,\' \') as employee_names')
		->from('emp_record_details er')
		->where('c.status', 'Active')
		->join('project_details as p', 'p.project_Id = er.project_id', 'left')
		->join('client_details as c', 'c.client_Id = er.client_Id', 'left')
		->join('employee_details as emp', 'emp.empId = p.empId', 'left') // Assuming project_manager_id is the field in project_details that links to the project manager's empId
		->join('employee_details as emp2', 'emp2.empId = er.empId', 'left') // Joining employee_details again to get the employee name
		->group_by('er.project_Id, er.client_Id')
		->order_by('c.client_name', 'asc')
		->order_by('p.project_name', 'asc')
		->get();
		//echo $this->db->last_query(); 
     	return $resourceBBQ->result();


	 endif; 
	

} 
  
/****************************** Getting Project execution status information ***************************************/


}

