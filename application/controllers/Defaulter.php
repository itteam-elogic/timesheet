<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Defaulter extends CI_Controller {

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
		$this->load->library('excel'); // load excel library		
		// Load database		
		$this->load->model('timesheet_login');
		
		$this->load->model('client_model');
		
		$this->load->model('project_model');
		
		$this->load->model('task_model');
		
		$this->load->model('emptimelog_model');
		
		$this->load->model('defaulter_model');
		
		$this->load->helper('text');
		
		
		if(empty($this->session->userdata['logged_in_timesheet'])){
		
			redirect('home/login');
		}
		
    }
	
	/* public function index(){
	
			$userType = $this->session->userdata['logged_in_timesheet']['user_type'];
			
			$data['getRecords'] = $this->emptimelog_model->getRecords($userType);
			
			$this->load->view('employee_reports/employee_timelog' , $data);
			
			//$this->load->view('employee_reports/add_employee_timelog');
			
			
	} */
	
	/*******************  We are showing the members not entered the timesheet log weekly wise get all records and send to particular project managers *******************/	
	
	public function user_defaulter(){	   
		$userType = $this->session->userdata['logged_in_timesheet']['user_type'];
		$loggedInEmpId = $this->session->userdata['logged_in_timesheet']['empId'];
		$isPrivilegedViewer = $this->isPrivilegedViewer();
		$defaultReportingManager = '';
		$defaultMemberEmpId = '';

		if(!$isPrivilegedViewer){
			if($userType == 'manager'){
				$defaultReportingManager = $loggedInEmpId;
			}elseif($userType == 'developer' || $userType == 'team_member'){
				$defaultMemberEmpId = $loggedInEmpId;
			}
		}

		$filters = array(
			'reporting_manager' => $defaultReportingManager,
			'member_empId' => $defaultMemberEmpId
		);

		$data['getEmpResult'] = $this->defaulter_model->getMemberNotEnterReportLog($filters);
		$data['reportingManagers'] = $this->defaulter_model->getReportingManagersList();
		$data['members'] = $this->defaulter_model->getMembersByReportingManager($defaultReportingManager);
		$data['selectedReportingManager'] = $defaultReportingManager;
		$data['selectedMemberEmpId'] = $defaultMemberEmpId;
		
		$this->load->view('defaulter/member_defaulter',$data);
		
		
	}
	
	public function previous_user_defaulter(){	   
		$userType = $this->session->userdata['logged_in_timesheet']['user_type'];
		$loggedInEmpId = $this->session->userdata['logged_in_timesheet']['empId'];
		$isPrivilegedViewer = $this->isPrivilegedViewer();
		$selectedReportingManager = (string)$this->input->post('reporting_manager');
		$rawSelectedMembers = $this->input->post('member_empId');
		$selectedMemberEmpIds = array();
		if(is_array($rawSelectedMembers)){
			foreach($rawSelectedMembers as $rawId){
				$rawId = trim((string)$rawId);
				if($rawId !== ''){
					$selectedMemberEmpIds[] = $rawId;
				}
			}
		}elseif($rawSelectedMembers !== null && trim((string)$rawSelectedMembers) !== ''){
			$selectedMemberEmpIds[] = trim((string)$rawSelectedMembers);
		}
		$selectedMemberEmpIds = array_values(array_unique($selectedMemberEmpIds));

		if(!$isPrivilegedViewer){
			if($selectedReportingManager === ''){
				if($userType == 'manager'){
					$selectedReportingManager = $loggedInEmpId;
				}
			}
			if(empty($selectedMemberEmpIds)){
				if($userType == 'developer' || $userType == 'team_member'){
					$selectedMemberEmpIds[] = (string)$loggedInEmpId;
				}
			}
		}
		$filters = array(
			'reporting_manager' => $selectedReportingManager,
			'member_empId' => $selectedMemberEmpIds
		);

		$previousExcluded = $this->previousUserDefaulterExcludedNames();
		$rawEmpResult = $this->defaulter_model->getPreviousWeekMemberNotEnterReportLog($filters);
		$data['getEmpResult'] = $this->filterRowsByExcludedNames($rawEmpResult, $previousExcluded);

		// Build employee IDs for hours summary and compute previous-week date range
		$employeeIds = array();
		foreach($data['getEmpResult'] as $member){
			$employeeIds[] = $member->empId;
		}
		$defFormDate = date('Y-m-d', strtotime('monday last week'));
		$defToDate = date('Y-m-d', strtotime('friday last week'));
		$data['listbetweenDates'] = $this->getWorkingDatesInRange($defFormDate, $defToDate);
		$data['hoursMatrix'] = $this->defaulter_model->getEmployeeHoursSummary($employeeIds, $defFormDate, $defToDate);
		$data['def_form_date'] = $defFormDate;
		$data['def_to_date'] = $defToDate;

		$data['reportingManagers'] = $this->defaulter_model->getReportingManagersList();
		$data['members'] = $this->filterRowsByExcludedNames($this->defaulter_model->getMembersByReportingManager($selectedReportingManager), $previousExcluded);
		$data['selectedReportingManager'] = $selectedReportingManager;
		$data['selectedMemberEmpId'] = $selectedMemberEmpIds;

		$this->load->view('defaulter/previous_week_member_defaulter',$data);
		
		
	}

	private function isPrivilegedViewer(){
		$username = isset($this->session->userdata['logged_in_timesheet']['username']) ? strtolower(trim($this->session->userdata['logged_in_timesheet']['username'])) : '';
		$name = isset($this->session->userdata['logged_in_timesheet']['name']) ? strtolower(trim($this->session->userdata['logged_in_timesheet']['name'])) : '';
		return in_array($username, array('shirley', 'krishna')) || in_array($name, array('shirley', 'krishna'));
	}
	
	public function memberSearch(){

		$userType = $this->session->userdata['logged_in_timesheet']['user_type'];
		$loggedInEmpId = $this->session->userdata['logged_in_timesheet']['empId'];
		$isPrivilegedViewer = $this->isPrivilegedViewer();

		$selectedReportingManager = (string)$this->input->post('reporting_manager');
		$rawSelectedMembers = $this->input->post('member_empId');
		$selectedMemberEmpIds = array();
		if(is_array($rawSelectedMembers)){
			foreach($rawSelectedMembers as $rawId){
				$rawId = trim((string)$rawId);
				if($rawId !== ''){
					$selectedMemberEmpIds[] = $rawId;
				}
			}
		}elseif($rawSelectedMembers !== null && trim((string)$rawSelectedMembers) !== ''){
			$selectedMemberEmpIds[] = trim((string)$rawSelectedMembers);
		}
		$selectedMemberEmpIds = array_values(array_unique($selectedMemberEmpIds));

		if(!$isPrivilegedViewer){
			if($selectedReportingManager === ''){
				if($userType == 'manager'){
					$selectedReportingManager = $loggedInEmpId;
				}
			}
			if(empty($selectedMemberEmpIds)){
				if($userType == 'developer' || $userType == 'team_member'){
					$selectedMemberEmpIds[] = (string)$loggedInEmpId;
				}
			}
		}

		$defFormDate = trim((string)$this->input->post('def_form_date'));
		$defToDate = trim((string)$this->input->post('def_to_date'));
		if($defFormDate === ''){
			$defFormDate = date('Y-m-d', strtotime('monday this week'));
		}
		if($defToDate === ''){
			$defToDate = date('Y-m-d', strtotime('friday this week'));
		}
		$filters = array(
			'reporting_manager' => $selectedReportingManager,
			'member_empId' => $selectedMemberEmpIds
		);

		$rawEmpResult = $this->defaulter_model->getMemberNotEnterReportLog($filters);
		$data['getEmpResult'] = $this->filterRowsByExcludedNames($rawEmpResult, $this->memberSearchExcludedNames());
		$employeeIds = array();
		foreach($data['getEmpResult'] as $member){
			$employeeIds[] = $member->empId;
		}
		$workingDates = $this->getWorkingDatesInRange($defFormDate, $defToDate);
		$data['hoursMatrix'] = $this->defaulter_model->getEmployeeHoursSummary($employeeIds, $defFormDate, $defToDate);
		$data['listbetweenDates'] = $workingDates;
		$data['def_form_date'] = $defFormDate;
		$data['def_to_date'] = $defToDate;
		$data['reportingManagers'] = $this->defaulter_model->getReportingManagersList();
		$data['members'] = $this->filterRowsByExcludedNames($this->defaulter_model->getMembersByReportingManager($selectedReportingManager), $this->memberSearchExcludedNames());
		$data['selectedReportingManager'] = $selectedReportingManager;
		$data['selectedMemberEmpId'] = $selectedMemberEmpIds;
		
		$this->load->view('defaulter/member_search_details',$data);
		
	}

	/** Names hidden from defaulter/memberSearch results and member dropdown. */
	private function memberSearchExcludedNames(){
		return array(
			'syed farhan',
			'hemanth kmv',
			'pradip chauhan',
			'rahul kumar'
		);
	}

	/** Names hidden from defaulter/previous_user_defaulter results and member dropdown. */
	private function previousUserDefaulterExcludedNames(){
		return array(
			'syed farhan',
			'hemanth kmv',
			'pradip chauhan',
			'rahul kumar',
			'varsha k'
		);
	}

	private function filterRowsByExcludedNames($rows, $excludedNames){
		if(empty($rows) || empty($excludedNames)){
			return $rows;
		}
		$out = array();
		foreach($rows as $row){
			$name = isset($row->name) ? strtolower(trim((string)$row->name)) : '';
			$name = preg_replace('/\s+/', ' ', $name);
			if($name !== '' && in_array($name, $excludedNames, true)){
				continue;
			}
			$out[] = $row;
		}
		return $out;
	}

	private function getWorkingDatesInRange($fromDate, $toDate){
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
			if($currentDay !== 'Sat' && $currentDay !== 'Sun' && !in_array($currentDate, $holidayDates)){
				$dates[] = $currentDate;
			}
			$current->modify('+1 day');
		}
		return $dates;
	}

	public function getMembersByManager(){
		$reportingManager = $this->input->post('reporting_manager');
		$members = $this->defaulter_model->getMembersByReportingManager($reportingManager);

		echo '<option value="">All Members</option>';
		foreach($members as $member){
			echo '<option value="'.$member->empId.'">'.ucfirst($member->name).'</option>';
		}
	}
	
	
	public function user_hours_defaulter(){	   
		
		$data['getEmpResult'] = $this->defaulter_model->getMemberNotEnterReportLog();
		
		$this->load->view('defaulter/member_hours_defaulter',$data);
		
		
	}
	
/*******************  We are sending emails from Unapproved PM's List in Pm_groups emails  And also same as in Team Member As well  *******************/		
	
}
