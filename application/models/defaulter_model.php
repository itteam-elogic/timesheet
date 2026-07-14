<?php
/**
 * eLogivc Admin Panel for Codeigniter 
 * Author: Laxmikanth 
 *
  */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Defaulter_Model extends CI_Model {

    public function __construct() {
	
			parent::__construct();   
	
	}
  
/*********************** Getting the result for member can't entered the report login ********/
	
	public function getMemberNotEnterReportLog($filters = array()){
		
		 //Get list of employee information of query
		$selectedReportingManager = isset($filters['reporting_manager']) ? trim((string)$filters['reporting_manager']) : '';
		$rawSelectedMember = isset($filters['member_empId']) ? $filters['member_empId'] : '';
		$selectedMemberIds = array();
		if(is_array($rawSelectedMember)){
			foreach($rawSelectedMember as $rawId){
				$rawId = trim((string)$rawId);
				if($rawId !== ''){
					$selectedMemberIds[] = $rawId;
				}
			}
		}elseif(trim((string)$rawSelectedMember) !== ''){
			$selectedMemberIds[] = trim((string)$rawSelectedMember);
		}
		$userType = isset($this->session->userdata['logged_in_timesheet']['user_type']) ? $this->session->userdata['logged_in_timesheet']['user_type'] : '';
		$logedInUser = isset($this->session->userdata['logged_in_timesheet']['empId']) ? $this->session->userdata['logged_in_timesheet']['empId'] : '';
		$isPrivilegedViewer = $this->isPrivilegedViewer();
        
		$this->db->select('emp.empId,emp.name,emp.emp_com_id,emp.reporting_manger,mgr.name as manager_name')
			->from('employee_details emp')
			->join('employee_details as mgr', 'mgr.empId = emp.reporting_manger', 'left')
			->where('emp.status','Active')
			->where('emp.reporting_manger !=' , '');

		// Role-based visibility: Admin = all, Manager = own team, Team member = self
		if(!$isPrivilegedViewer){
			if($userType == 'developer' || $userType == 'team_member'){
				$this->db->where('emp.empId', $logedInUser);
			}elseif($userType == 'manager'){
				// Include both team members and the manager's own record
				$this->db->group_start();
				$this->db->where('emp.reporting_manger', $logedInUser);
				$this->db->or_where('emp.empId', $logedInUser);
				$this->db->group_end();
			}
		}

		if($selectedReportingManager !== ''){
			$this->db->group_start();
			$this->db->where('emp.reporting_manger', $selectedReportingManager);
			$this->db->or_where('emp.empId', $selectedReportingManager);
			$this->db->group_end();
		}
		if(!empty($selectedMemberIds)){
			$this->db->where_in('emp.empId', $selectedMemberIds);
		}

		$empData = $this->db->order_by('emp.reporting_manger','asc')->get(); 
        
        
		
		 return $empData->result();
		
  }

	public function getReportingManagersList(){
		$userType = isset($this->session->userdata['logged_in_timesheet']['user_type']) ? $this->session->userdata['logged_in_timesheet']['user_type'] : '';
		$logedInUser = isset($this->session->userdata['logged_in_timesheet']['empId']) ? $this->session->userdata['logged_in_timesheet']['empId'] : '';
		$isPrivilegedViewer = $this->isPrivilegedViewer();

		if($isPrivilegedViewer){
			return $this->db->select('mgr.empId,mgr.name')
				->from('employee_details emp')
				->join('employee_details as mgr', 'mgr.empId = emp.reporting_manger', 'inner')
				->where('emp.status', 'Active')
				->where('emp.reporting_manger !=', '')
				->group_by('mgr.empId')
				->order_by('mgr.name', 'asc')
				->get()
				->result();
		}

		if($userType == 'manager'){
			return $this->db->select('empId,name')
				->from('employee_details')
				->where('empId', $logedInUser)
				->order_by('name', 'asc')
				->get()
				->result();
		}

		if($userType == 'developer' || $userType == 'team_member'){
			return $this->db->select('mgr.empId,mgr.name')
				->from('employee_details emp')
				->join('employee_details as mgr', 'mgr.empId = emp.reporting_manger', 'inner')
				->where('emp.empId', $logedInUser)
				->group_by('mgr.empId')
				->order_by('mgr.name', 'asc')
				->get()
				->result();
		}

		$managerRows = $this->db->select('mgr.empId,mgr.name')
			->from('employee_details emp')
			->join('employee_details as mgr', 'mgr.empId = emp.reporting_manger', 'inner')
			->where('emp.status', 'Active')
			->where('emp.reporting_manger !=', '')
			->group_by('mgr.empId')
			->order_by('mgr.name', 'asc')
			->get()
			->result();

		return $managerRows;
	}

	public function getMembersByReportingManager($reportingManagerEmpId = ''){
		$userType = isset($this->session->userdata['logged_in_timesheet']['user_type']) ? $this->session->userdata['logged_in_timesheet']['user_type'] : '';
		$logedInUser = isset($this->session->userdata['logged_in_timesheet']['empId']) ? $this->session->userdata['logged_in_timesheet']['empId'] : '';
		$isPrivilegedViewer = $this->isPrivilegedViewer();

		$this->db->select('emp.empId,emp.name')
			->from('employee_details emp')
			->where('emp.status', 'Active');

		// Decide target manager id to filter by (when applicable)
		$targetManager = '';
		if(trim((string)$reportingManagerEmpId) !== ''){
			$targetManager = trim((string)$reportingManagerEmpId);
		} elseif(!$isPrivilegedViewer && $userType == 'manager'){
			$targetManager = $logedInUser;
		}

		if($targetManager !== ''){
			// Include both team members and the manager themself
			$this->db->group_start();
			$this->db->where('emp.reporting_manger', $targetManager);
			$this->db->or_where('emp.empId', $targetManager);
			$this->db->group_end();
		} else {
			// No specific manager target: show active employees who have a reporting manager
			$this->db->where('emp.reporting_manger !=', '');
		}

		return $this->db->order_by('emp.name', 'asc')->get()->result();
	}

	public function getEmployeeHoursSummary($employeeIds = array(), $fromDate = '', $toDate = ''){
		if(empty($employeeIds) || $fromDate === '' || $toDate === ''){
			return array();
		}

		$rows = $this->db
			->select('empId, emp_report_dates, SUM(emp_time_hours) as total_hours, MAX(CASE WHEN task_Id = 18592 THEN 1 ELSE 0 END) as is_leave', false)
			->from('emp_record_details')
			->where_in('empId', $employeeIds)
			->where('emp_report_dates >=', $fromDate)
			->where('emp_report_dates <=', $toDate)
			->group_by(array('empId', 'emp_report_dates'))
			->get()
			->result();

		$hoursMatrix = array();
		foreach($rows as $row){
			if(!isset($hoursMatrix[$row->empId])){
				$hoursMatrix[$row->empId] = array();
			}
			$hoursMatrix[$row->empId][$row->emp_report_dates] = array(
				'hours' => (float)$row->total_hours,
				'is_leave' => ((int)$row->is_leave) === 1
			);
		}

		return $hoursMatrix;
	}
	
	//Getting week days and dates in below code
	
	function weeklyDays(){
		
	$date_start = strtotime('-' . date('w') . ' days');
	
		$month = date('M');
		$year  = date('Y');
		
		echo $secondSat =date('Y-m-d', strtotime("second sat of $month $year"));
		echo $forthSat =date('Y-m-d', strtotime("fourth sat of $month $year"));	
		
		
		$user_data = array();

		for ($i = 1; $i < 6; $i++) {
			
        $date = date('Y-m-d', $date_start + ($i * 86400));
		//echo print_r($date);
		
		if(in_array(array($secondSat , $forthSat) , array($date))){
			
			$user_data[] = $date;
		
		} elseif(date('D', strtotime($date)) != 'Sun' && date('D', strtotime($date)) != 'Sat'){	
			
        	$user_data[] = $date;
			
	     }
			
	}
			
	return $user_data;
	
	}
	
	function getBetweenDays($dateDiff){ echo $dateDiff; exit;
		
	$date_start = strtotime('-' . date('w') . ' days');
		
		$user_data = array();

		for ($i = 0; $i < $dateDiff; $i++) {
			
        $date = date('Y-m-d', $date_start + ($i * 86400));
		//echo print_r($date);
		
		if( date('D', strtotime($date)) != 'Sun' ){	
			
        	$user_data[] = $date;
			
	     }
			
	}
			
	return $user_data;
	
	}
	
	
	public function getPreviousWeekMemberNotEnterReportLog($filters = array()){
		
		 //Get list of employee information of query
		$selectedReportingManager = isset($filters['reporting_manager']) ? trim((string)$filters['reporting_manager']) : '';
		$rawSelectedMember = isset($filters['member_empId']) ? $filters['member_empId'] : '';
		$selectedMemberIds = array();
		if(is_array($rawSelectedMember)){
			foreach($rawSelectedMember as $rawId){
				$rawId = trim((string)$rawId);
				if($rawId !== ''){
					$selectedMemberIds[] = $rawId;
				}
			}
		}elseif(trim((string)$rawSelectedMember) !== ''){
			$selectedMemberIds[] = trim((string)$rawSelectedMember);
		}
		$userType = isset($this->session->userdata['logged_in_timesheet']['user_type']) ? $this->session->userdata['logged_in_timesheet']['user_type'] : '';
		$logedInUser = isset($this->session->userdata['logged_in_timesheet']['empId']) ? $this->session->userdata['logged_in_timesheet']['empId'] : '';
		$isPrivilegedViewer = $this->isPrivilegedViewer();
		
		$this->db->select('emp.empId,emp.name,emp.emp_com_id,emp.reporting_manger,mgr.name as manager_name')
			->from('employee_details emp')
			->join('employee_details as mgr', 'mgr.empId = emp.reporting_manger', 'left')
			->where('emp.status','Active')
			->where('emp.designation !=' , 'Business Head')
			->where('emp.user_type !=' , 'admin')
			->order_by('emp.reporting_manger','asc')
			->order_by('emp.name','asc');

		// Role-based visibility: Admin = all, Manager = own team, Team member = self
		if(!$isPrivilegedViewer){
			if($userType == 'developer' || $userType == 'team_member'){
				$this->db->where('emp.empId', $logedInUser);
			}elseif($userType == 'manager'){
				// Include both team members and the manager's own record
				$this->db->group_start();
				$this->db->where('emp.reporting_manger', $logedInUser);
				$this->db->or_where('emp.empId', $logedInUser);
				$this->db->group_end();
			}
		}

		if($selectedReportingManager !== ''){
			$this->db->group_start();
			$this->db->where('emp.reporting_manger', $selectedReportingManager);
			$this->db->or_where('emp.empId', $selectedReportingManager);
			$this->db->group_end();
		}
		if(!empty($selectedMemberIds)){
			$this->db->where_in('emp.empId', $selectedMemberIds);
		}

		$empData = $this->db->get();

		
		
		
		 return $empData->result();
		
  }

	private function isPrivilegedViewer(){
		$username = isset($this->session->userdata['logged_in_timesheet']['username']) ? strtolower(trim($this->session->userdata['logged_in_timesheet']['username'])) : '';
		$name = isset($this->session->userdata['logged_in_timesheet']['name']) ? strtolower(trim($this->session->userdata['logged_in_timesheet']['name'])) : '';
		$privilegedUsers = array('shirley', 'krishna');
		return in_array($username, $privilegedUsers) || in_array($name, $privilegedUsers);
	}
		
	function lastWeekDays(){
		
	$date_start = strtotime('last week monday');
	
		$month = date('M');
		$year  = date('Y');
		
		 $secondSat =date('Y-m-d', strtotime("second sat of $month $year"));
		 $forthSat =date('Y-m-d', strtotime("fourth sat of $month $year"));	
		
		
		$user_data = array();

		for ($i = 0; $i < 5; $i++) {
			
        $date = date('Y-m-d', $date_start + ($i * 86400));
		//echo print_r($date);
		
		if(in_array($forthSat, array($date))){
			
			$user_data[] = $date;
		
		} elseif(date('D', strtotime($date)) != 'Sun' ){	
			
        	$user_data[] = $date;
			
	     }
			
	}
			
	return $user_data;
		
			    
	
      
	}
/*********************** Getting the result for member can't entered the report login ********/	
	
    
/************************************** Manager project list information **********************/
    
    public function getMangerProjects(){
        
                 $logedInUser = $this->session->userdata['logged_in_timesheet']['empId'];
        
                $projectQ = $this->db->select('p.project_Id,p.empId')
						->from('project_details as p')
						->where('p.empId',$logedInUser)
						->order_by('project_Id' , 'desc')->get()->result();
        
         $managerCP = array();
        
        foreach($projectQ as $project_details){
            
            
            $managerCP[] = $project_details->project_Id;
            
        }
        
         return $managerCP;
        
    }

/************************************** Manager project list information **********************/	

public function getManagerName($empId){
		
	if(!empty($empId)):
	
	$teamMembersQuery  = $this->db->select('empId,name')->from('employee_details')
							->where('empId',$empId)->order_by('name' , 'asc')->get()->result();
	
	foreach($teamMembersQuery as $managerResult){
		
		
		return $managerResult->name;
		
	}
	 
		
	
	endif;
					
	
	
}

	
}