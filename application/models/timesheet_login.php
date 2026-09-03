<?php
/**
 * eLogivc Admin Panel for Codeigniter 
 * Author: Laxmikanth 
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Timesheet_Login extends CI_Model {

    public function __construct() {
	
			parent::__construct();   
	
	}
  

	// Read data using username and password
	public function login($data) {
	
		//$condition = "username =" . "'" . $data['username'] . "' AND " . "password =" . "'" . md5($data['password']). "' AND " . "user_type='admin'";
		
		$condition = "username =" . "'" . $data['username'] . "' AND " . "password =" . "'" . md5($data['password']). "' AND " . "status ='Active'";
	    
		$query = $this->db->select('*')->from('employee_details')->where($condition)->limit(1)->get();
		
		if ($query->num_rows() == 1) {
		
			return true;
			
		} else {
		
			return false;
			
		}
	}
	
	
  // Read data from database to show data in admin page
	public function user_information($username) {
	
	$condition = "username =" . "'" . $username . "'";
	
	$query =  $this->db->select('*')->from('employee_details')->where($condition)->limit(1)->get();
	
		if ($query->num_rows() == 1) {
		
			return $query->result();
			
		}else{
		
			return false;
			
		}
   }


 /************************************************************* Employee Informatoin store to database base ******************************************************************************************/
 
 
  public function add_employee($data){   // Save User Information here 
   
	  if($data):
	   
	   		 $this->db->insert('employee_details', $data);	
	   
	   endif;
   }

 
  public function getEmployees($updateID = NULL){  // Showing list of employee information and displaying particular employee update information
  
     if(empty($updateID)): 
	 
		if($this->session->userdata['logged_in_timesheet']['empId'] == '92'):

			$userQ  = $this->db->select('*')->from('employee_details')->where('status', 'Active')->order_by('empId' , 'desc')->get();

	else:

		$userQ  = $this->db->select('*')->from('employee_details')->order_by('empId' , 'desc')->get();
		
	endif; 
	
	  else:
	  
	  		$userQ  = $this->db->select('*')->from('employee_details')->where('empId' , $updateID)->get();
	  
	  endif;		
			  
	 return $userQ->result();
  
  }
  
  public function update_employee($data , $empId){
  
  		$this->db->where('empId', $empId);
		
	    $update = $this->db->update('employee_details', $data);
		
		if($update):
			
			  return true; 
			
		endif;
  
  }
  
  /* public function del_employee($empId){
  	
		$this->db->where('empId', $empId)->delete('employee_details');
		  
		$deleteQuery = $this->db->affected_rows();
			
	    echo  $deleteQuery;
  
  } */
  
    public function update_employee_status($empId , $status){
  	
		
		$update = $this->db->set('status',$status)->where('empId',$empId) ->update('employee_details');
		
		$userQ  = $this->db->select('empId,status')->from('employee_details')->where('empId' , $empId)->get()->result();
		
		foreach($userQ as $key => $getStatus ) { 
		
		   if($getStatus->status == 'Active'){
			   
			  $activeClass =  'fa fa-check-circle label label-success';
			   
		   }else{
		   
				$activeClass =  'fa fa-ban label label-danger';
		   
		   }			   
			   
		 	echo  "<a class='".$activeClass."' style=cursor:pointer; onClick=update_emp_status(".$getStatus->empId.",'".$getStatus->status."')> ".$getStatus->status."</a>"; 
		
		}
  }
  
  
  public function recentEmployees(){ // Recent Clients displaying angular js 
	
	     $getREQ  =  $this->db->select('*')->from('employee_details')->order_by('empId' , 'desc')->limit(5)->get();
		
	     return $getREQ->result();
		 
        //echo json_encode($recentQ->result());
	
	
	 }
  
  public function getEmployeeName($includeInactive = false){ // Getting List of Users
		 $this->db->select('empId,username,name,status')->from('employee_details');
		 if (!$includeInactive) {
			 $this->db->where('status','Active');
		 }
		 $employeeNQ  = $this->db->order_by('empId' , 'desc')->get();
		 
		 return $employeeNQ->result();
		
	}
	
	
	public function getListOfEmpInformation(){ // Getting List of Users
	
		 $employeeNQ  = $this->db->select('empId,username,name')->from('employee_details')->order_by('empId' , 'desc')->get();
		 
		 return $employeeNQ->result();
		
	}

	/**
	 * List of employees/managers for KPI report Search dropdown.
	 * Excludes departments: HR, Software, Accounting, Operations Manager and admin role users.
	 */
	public function getListOfEmpInformationForKpiSearch() {
		$excludedDepartments = array('HR', 'Software', 'Accounting', 'Operations Manager');
		$this->db->select('empId, name');
		$this->db->from('employee_details');
		$this->db->where('status', 'Active');
		$this->db->where_not_in('department', $excludedDepartments);
		$this->db->group_start();
		$this->db->where('user_type IS NULL', null, false);
		$this->db->or_where('LOWER(user_type) !=', 'admin');
		$this->db->group_end();
		$this->db->order_by('name', 'asc');
		return $this->db->get()->result();
	}
  
	
/************************************************************* Employee Informatoin store to database base ******************************************************************************************/


	public function updateChangePassword($password , $empId ){ // Employee can change password
		
		 
		   $update =  $this->db->set('password', md5(strtolower($password)))->where('empId', $empId)->update('employee_details');  //table name
		  
		   if($update):
				
				  return true; 
				
			endif;
			
		   
		}

/************************************************************ Managers list and BH list *************************************************** */		

public function getReportingManagers($managerId){
		
	   if(!empty($managerId)):	
    
	   $teamMembersQuery  = $this->db->select('empId,name')->from('employee_details')
							->where('empId',$managerId)
							->where('status','Active')->order_by('name' , 'asc')->get()->result();
		foreach($teamMembersQuery as $getManager){
            
            return $getManager->name;
        }
    
    else:
    
    
    $teamMembersQuery  = $this->db->select('empId,name')->from('employee_details')
							//->where_in('user_type',array('manager','business_head'))
							->where('status','Active')->order_by('name' , 'asc')->get()->result();
		return $teamMembersQuery;
    
    endif;
		
	
}

public function getEmployeeDepartmentById($empId){
	if (empty($empId)) {
		return '';
	}
	$row = $this->db->select('department')
		->from('employee_details')
		->where('empId', $empId)
		->limit(1)
		->get()
		->row();
	return (!empty($row) && isset($row->department)) ? trim((string)$row->department) : '';
}

public function getEmployeeMetaMapByIds($empIds){
	$cleanIds = array();
	if (!is_array($empIds)) {
		$empIds = array($empIds);
	}
	foreach ($empIds as $empId) {
		$empId = trim((string)$empId);
		if ($empId !== '' && strtolower($empId) !== 'all') {
			$cleanIds[] = $empId;
		}
	}
	$cleanIds = array_values(array_unique($cleanIds));
	if (empty($cleanIds)) {
		return array();
	}

	$this->db->select('empId,name,department');
	$this->db->from('employee_details');
	$this->db->group_start();
	$this->db->where_in('empId', $cleanIds);
	$this->db->or_where_in('name', $cleanIds);
	$this->db->group_end();
	$rows = $this->db->get()->result();

	$map = array();
	foreach ($rows as $row) {
		$meta = array(
			'name' => isset($row->name) ? (string)$row->name : '',
			'department' => isset($row->department) ? trim((string)$row->department) : ''
		);
		$map[(string)$row->empId] = $meta;
		$nameKey = trim($meta['name']);
		if ($nameKey !== '') {
			$map[$nameKey] = $meta;
		}
	}
	return $map;
}

public function attachReportingManagerFields($rows){
	if (empty($rows) || !is_array($rows)) {
		return $rows;
	}
	$keys = array();
	foreach ($rows as $row) {
		if (!empty($row->reporting_manger)) {
			$keys[] = trim((string)$row->reporting_manger);
		}
	}
	$metaMap = $this->getEmployeeMetaMapByIds($keys);
	foreach ($rows as $row) {
		$rmKey = isset($row->reporting_manger) ? trim((string)$row->reporting_manger) : '';
		$rmName = (!empty($row->reporting_manager_name)) ? trim((string)$row->reporting_manager_name) : '';
		if ($rmKey !== '' && isset($metaMap[$rmKey]) && $rmName === '') {
			$rmName = $metaMap[$rmKey]['name'];
		}
		$empDept = '';
		if (!empty($row->employee_department)) {
			$empDept = trim((string)$row->employee_department);
		} elseif (!empty($row->department) && !in_array($row->department, array('Approved', 'Rejected', 'Pending', 'Process'), true)) {
			$empDept = trim((string)$row->department);
		}
		$row->reporting_manager_name = $rmName;
		$row->employee_department = $empDept;
	}
	return $rows;
}
/********************************************************* Active managers only (manager, business_head + status Active) ************************************************/
	public function getActiveManagers(){
		return $this->db->select('empId,name')->from('employee_details')
			->where_in('user_type', array('manager','business_head'))
			->where('status', 'Active')
			->order_by('name', 'asc')
			->get()->result();
	}
/********************************************************* Reporting Manager ************************************************/
    
    public function getReportManagerName($managerId, $includeInactive = false){
		
	   if(!empty($managerId)):	
    
	   $this->db->select('empId,name,status')->from('employee_details')
							->where('empId',$managerId);
		if (!$includeInactive) {
			$this->db->where('status','Active');
		}
		$teamMembersQuery = $this->db->order_by('name' , 'asc')->get()->result();
		foreach($teamMembersQuery as $getManager){
            
            return $getManager->name;
        }
    
    else:
    
    
    $this->db->select('empId,name,status')->from('employee_details')
							->where_in('user_type',array('manager','admin'));
		if (!$includeInactive) {
			$this->db->where('status','Active');
		}
		$teamMembersQuery = $this->db->order_by('name' , 'asc')->get()->result();
		return $teamMembersQuery;
    
    endif;
		
	
}

    


    
    /*************************************************************** Department wise showing data based on Managers  *************************************************************/
    
    public function getReportMasterEmployee($department = null, $form_date = null, $to_date = null){
        $filters = array();
        if (is_array($department)) {
            $filters = $department;
            $department = isset($filters['department']) ? $filters['department'] : null;
           $from_year  = isset($filters['from_year']) ? $filters['from_year'] : $this->input->post('from_year');
$from_month = isset($filters['from_month']) ? $filters['from_month'] : $this->input->post('from_month');

$to_year    = isset($filters['to_year']) ? $filters['to_year'] : $this->input->post('to_year');
$to_month   = isset($filters['to_month']) ? $filters['to_month'] : $this->input->post('to_month');
        }

        if($department === null) {
            $department = $this->input->post('department');
        }
        if($form_date === null) {
            $form_date = $this->input->post('form_date');
        }
        if($to_date === null) {
            $to_date = $this->input->post('to_date');
        }

        $projectManager = isset($filters['project_manager']) ? $filters['project_manager'] : $this->input->post('project_manager');
        $employeeSearch = isset($filters['employee_search']) ? trim((string)$filters['employee_search']) : trim((string)$this->input->post('employee_search'));
        $statusFilter = isset($filters['status_filter']) ? strtolower(trim((string)$filters['status_filter'])) : strtolower(trim((string)$this->input->post('status_filter')));

		// FROM FILTERS
if (!empty($from_year)) {

    $this->db->where('YEAR(employee_details.created_at) >=', $from_year);
}

if (!empty($from_month)) {

    $this->db->where('MONTH(employee_details.created_at) >=', $from_month);
}

// TO FILTERS
if (!empty($to_year)) {

    $this->db->where('YEAR(employee_details.created_at) <=', $to_year);
}

if (!empty($to_month)) {

    $this->db->where('MONTH(employee_details.created_at) <=', $to_month);
}

        if ($statusFilter === '') {
            $statusFilter = 'active';
        }
        $department = $this->normalizeMultiFilterValues($department);
        $projectManager = $this->normalizeMultiFilterValues($projectManager);

        $this->db->select('employee_details.*, manager.name as reporting_manager_name');
        $this->db->from('employee_details');
        $this->db->join('employee_details as manager', 'manager.empId = employee_details.reporting_manger', 'left');

        if (!empty($department)) {
            $departmentFilters = array();
            foreach ($department as $deptValue) {
                $deptKey = strtolower(trim((string)$deptValue));
                if ($deptKey === 'operations') {
                    $departmentFilters[] = 'Operations';
                    $departmentFilters[] = 'Operations Manager';
                    continue;
                }
                $departmentFilters[] = $deptValue;
            }
            $departmentFilters = array_values(array_unique($departmentFilters));
            $this->db->where_in('employee_details.department', $departmentFilters);
        }
        if (!empty($projectManager)) {
            $this->db->where_in('employee_details.reporting_manger', $projectManager);
        }
        if (!empty($employeeSearch)) {
            $this->db->group_start();
            $this->db->like('employee_details.name', $employeeSearch);
            $this->db->or_like('employee_details.emp_com_id', $employeeSearch);
            $this->db->or_like('employee_details.email', $employeeSearch);
            $this->db->group_end();
        }
       
        if ($statusFilter === 'active') {
            $this->db->where('employee_details.status', 'Active');
        } elseif ($statusFilter === 'inactive') {
    $this->db->where('employee_details.status', 'Inactive');
}
        $this->db->order_by('employee_details.created_at', 'desc');
        $employeeSQ = $this->db->get();
        return $employeeSQ->result();
    }

	private function normalizeMultiFilterValues($value) {
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

	/**
	 * Employees used for department headcount (Active + Inactive, no created_at date filter).
	 */
	public function getEmployeesForHeadcount($filters = array()) {
		$department = $this->normalizeMultiFilterValues(isset($filters['department']) ? $filters['department'] : array());
		$projectManager = $this->normalizeMultiFilterValues(isset($filters['project_manager']) ? $filters['project_manager'] : array());

		$this->db->select('empId, name, department, emp_joining_date, created_at, updated_at, status, user_type');
		$this->db->from('employee_details');

		if (!empty($department)) {
			$departmentFilters = array();
			foreach ($department as $deptValue) {
				$deptKey = strtolower(trim((string)$deptValue));
				if ($deptKey === 'operations' || $deptKey === 'hr' || $deptKey === 'recruiter' || $deptKey === 'accounting' || $deptKey === 'admin & hr' || $deptKey === 'admin and hr' || $deptKey === 'hr / recruiter / operations / accounting') {
					$departmentFilters[] = 'HR';
					$departmentFilters[] = 'Recruiter';
					$departmentFilters[] = 'Admin';
					$departmentFilters[] = 'Admin & HR';
					$departmentFilters[] = 'Operations';
					$departmentFilters[] = 'Operations Manager';
					$departmentFilters[] = 'Accounting';
					continue;
				}
				if ($deptKey === 'software' || $deptKey === 'it' || $deptKey === 'software / it' || $deptKey === 'software/it') {
					$departmentFilters[] = 'Software';
					$departmentFilters[] = 'IT';
					continue;
				}
				$departmentFilters[] = $deptValue;
			}
			$departmentFilters = array_values(array_unique($departmentFilters));
			$this->db->where_in('department', $departmentFilters);
		}

		if (!empty($projectManager)) {
			$this->db->where_in('reporting_manger', $projectManager);
		}

		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * Get distinct department names from employee_details for dropdown (multi-select).
	 */
	public function getDistinctEmployeeDepartments() {
		$this->db->select('department');
		$this->db->from('employee_details');
		$this->db->where('status', 'Active');
		$this->db->where('department IS NOT NULL', NULL, FALSE);
		$this->db->where("TRIM(department) != ''", NULL, FALSE);
		$this->db->group_by('department');
		$this->db->order_by('department', 'ASC');
		$q = $this->db->get();
		return $q->result();
	}
	
    
    
    
    
}

