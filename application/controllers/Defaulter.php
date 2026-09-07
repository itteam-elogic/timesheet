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

		$data['getEmpResult'] = $this->filterRowsByExcludedNames(
			$this->defaulter_model->getMemberNotEnterReportLog($filters),
			$this->memberSearchExcludedNames()
		);
		$data['reportingManagers'] = $this->defaulter_model->getReportingManagersList();
		$data['members'] = $this->filterRowsByExcludedNames(
			$this->defaulter_model->getMembersByReportingManager($defaultReportingManager),
			$this->memberSearchExcludedNames()
		);
		$data['selectedReportingManager'] = $defaultReportingManager;
		$data['selectedMemberEmpId'] = $defaultMemberEmpId;
		
		$this->load->view('defaulter/member_defaulter',$data);
		
		
	}
	
	public function previous_user_defaulter(){
		$data = $this->loadPreviousWeekReport();
		$this->load->view('defaulter/previous_week_member_defaulter',$data);
	}

	public function export_member_search_excel(){
		$data = $this->loadMemberSearchReport();
		$this->downloadDefaulterExcel(
			isset($data['getEmpResult']) ? $data['getEmpResult'] : array(),
			isset($data['hoursMatrix']) ? $data['hoursMatrix'] : array(),
			isset($data['listbetweenDates']) ? $data['listbetweenDates'] : array(),
			isset($data['def_form_date']) ? $data['def_form_date'] : '',
			isset($data['def_to_date']) ? $data['def_to_date'] : '',
			'Timesheet Defaulter Report'
		);
	}

	public function export_previous_week_excel(){
		$data = $this->loadPreviousWeekReport();
		$this->downloadDefaulterExcel(
			isset($data['getEmpResult']) ? $data['getEmpResult'] : array(),
			isset($data['hoursMatrix']) ? $data['hoursMatrix'] : array(),
			isset($data['listbetweenDates']) ? $data['listbetweenDates'] : array(),
			isset($data['def_form_date']) ? $data['def_form_date'] : '',
			isset($data['def_to_date']) ? $data['def_to_date'] : '',
			'Previous Week Defaulter Report'
		);
	}

	private function isPrivilegedViewer(){
		$username = isset($this->session->userdata['logged_in_timesheet']['username']) ? strtolower(trim($this->session->userdata['logged_in_timesheet']['username'])) : '';
		$name = isset($this->session->userdata['logged_in_timesheet']['name']) ? strtolower(trim($this->session->userdata['logged_in_timesheet']['name'])) : '';
		return in_array($username, array('shirley', 'krishna', 'suman')) || in_array($name, array('shirley', 'krishna', 'suman'));
	}
	
	public function memberSearch(){
		$data = $this->loadMemberSearchReport();
		$this->load->view('defaulter/member_search_details',$data);
	}

	public function send_member_search_email(){
		header('Content-Type: application/json');

		if(empty($this->session->userdata['logged_in_timesheet'])){
			echo json_encode(array(
				'success' => false,
				'message' => 'Session expired. Please login again.'
			));
			return;
		}

		$data = $this->loadMemberSearchReport();
		$getEmpResult = isset($data['getEmpResult']) ? $data['getEmpResult'] : array();
		if(empty($getEmpResult)){
			echo json_encode(array(
				'success' => false,
				'message' => 'No member data available to send.'
			));
			return;
		}

		$listbetweenDates = isset($data['listbetweenDates']) ? $data['listbetweenDates'] : array();
		$hoursMatrix = isset($data['hoursMatrix']) ? $data['hoursMatrix'] : array();
		$defFormDate = isset($data['def_form_date']) ? $data['def_form_date'] : '';
		$defToDate = isset($data['def_to_date']) ? $data['def_to_date'] : '';
		$periodLabel = date('d M Y', strtotime($defFormDate)) . ' to ' . date('d M Y', strtotime($defToDate));

		$emailBody = $this->buildMemberSearchEmailHtml($getEmpResult, $hoursMatrix, $listbetweenDates, $periodLabel);
		$tmpPath = $this->buildMemberSearchExcelFile($getEmpResult, $hoursMatrix, $listbetweenDates, $defFormDate, $defToDate);

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
		$this->email->to('elogic_pms@elogictech.com,rupali@elogictech.com,jaishree@elogictech.com,laxmikanth@elogictech.com');
		$this->email->subject('Timesheet Defaulter Report - ' . $periodLabel);
		$this->email->message($emailBody);
		if(!empty($tmpPath) && file_exists($tmpPath)){
			$this->email->attach($tmpPath);
		}

		$sent = @$this->email->send();
		if(!empty($tmpPath) && file_exists($tmpPath)){
			@unlink($tmpPath);
		}

		if($sent){
			echo json_encode(array(
				'success' => true,
				'message' => 'Defaulter report has been emailed to elogic_pms@elogictech.com.'
			));
		}else{
			if(function_exists('log_message')){
				log_message('error', 'send_member_search_email failed: ' . $this->email->print_debugger(array('headers', 'subject')));
			}
			echo json_encode(array(
				'success' => false,
				'message' => 'Failed to send email. Please contact the software team.'
			));
		}
	}

	private function collectPostedList($fieldName){
		$rawValues = $this->input->post($fieldName);
		$values = array();
		if(is_array($rawValues)){
			foreach($rawValues as $rawValue){
				$rawValue = trim((string)$rawValue);
				if($rawValue !== '' && strtolower($rawValue) !== 'all'){
					$values[] = $rawValue;
				}
			}
		}elseif($rawValues !== null && trim((string)$rawValues) !== '' && strtolower(trim((string)$rawValues)) !== 'all'){
			$values[] = trim((string)$rawValues);
		}
		return array_values(array_unique($values));
	}

	private function loadMemberSearchReport(){
		$userType = $this->session->userdata['logged_in_timesheet']['user_type'];
		$loggedInEmpId = $this->session->userdata['logged_in_timesheet']['empId'];
		$isPrivilegedViewer = $this->isPrivilegedViewer();

		$selectedReportingManagers = $this->collectPostedList('reporting_manager');
		$selectedMemberEmpIds = $this->collectPostedList('member_empId');
		$selectedDepartments = $this->collectPostedList('department');

		if(!$isPrivilegedViewer){
			if(empty($selectedReportingManagers)){
				if($userType == 'manager'){
					$selectedReportingManagers[] = (string)$loggedInEmpId;
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
			'reporting_manager' => $selectedReportingManagers,
			'member_empId' => $selectedMemberEmpIds,
			'department' => $selectedDepartments
		);

		$rawEmpResult = $this->defaulter_model->getMemberNotEnterReportLog($filters);
		$data['getEmpResult'] = $this->attachNormalizedJoiningDates(
			$this->filterRowsByExcludedNames($rawEmpResult, $this->memberSearchExcludedNames())
		);
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
		$data['members'] = $this->filterRowsByExcludedNames(
			$this->defaulter_model->getMembersByReportingManager($selectedReportingManagers, $selectedDepartments),
			$this->memberSearchExcludedNames()
		);
		$data['departments'] = function_exists('ts_department_options') ? ts_department_options() : array();
		$data['selectedReportingManager'] = $selectedReportingManagers;
		$data['selectedMemberEmpId'] = $selectedMemberEmpIds;
		$data['selectedDepartments'] = $selectedDepartments;

		return $data;
	}

	private function loadPreviousWeekReport(){
		$userType = $this->session->userdata['logged_in_timesheet']['user_type'];
		$loggedInEmpId = $this->session->userdata['logged_in_timesheet']['empId'];
		$isPrivilegedViewer = $this->isPrivilegedViewer();
		$selectedReportingManager = (string)$this->input->post('reporting_manager');
		$selectedMemberEmpIds = $this->collectPostedList('member_empId');

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
		$data['getEmpResult'] = $this->attachNormalizedJoiningDates($this->filterRowsByExcludedNames($rawEmpResult, $previousExcluded));

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

		return $data;
	}

	private function normalizeJoiningDate($rawJoiningDate){
		$rawJoiningDate = trim((string)$rawJoiningDate);
		if($rawJoiningDate === '' || strpos($rawJoiningDate, '0000-00-00') === 0){
			return '';
		}
		if(preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $rawJoiningDate, $m)){
			return $m[1].'-'.$m[2].'-'.$m[3];
		}
		if(preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})/', $rawJoiningDate, $m)){
			$day = str_pad($m[1], 2, '0', STR_PAD_LEFT);
			$month = str_pad($m[2], 2, '0', STR_PAD_LEFT);
			return $m[3].'-'.$month.'-'.$day;
		}
		$ts = strtotime($rawJoiningDate);
		if($ts === false){
			return '';
		}
		return date('Y-m-d', $ts);
	}

	private function resolveEmployeeJoiningDate($member){
		$joinDate = $this->normalizeJoiningDate(isset($member->emp_joining_date) ? $member->emp_joining_date : '');
		if($joinDate === ''){
			$joinDate = $this->normalizeJoiningDate(isset($member->created_at) ? $member->created_at : '');
		}
		return $joinDate;
	}

	private function attachNormalizedJoiningDates($rows){
		if(empty($rows)){
			return $rows;
		}
		foreach($rows as $row){
			$row->emp_joining_date = $this->resolveEmployeeJoiningDate($row);
		}
		return $rows;
	}

	private function isDateBeforeJoining($date, $joiningDate){
		$joinDate = $this->normalizeJoiningDate($joiningDate);
		if($joinDate === '' || $date === ''){
			return false;
		}
		return ($date < $joinDate);
	}

	private function resolveMemberSearchDayCell($hasRecord, $date = '', $joiningDate = ''){
		if($this->isDateBeforeJoining($date, $joiningDate)){
			return array(
				'text' => '',
				'bg' => '9E9E9E',
				'fg' => 'FFFFFF'
			);
		}
		if($hasRecord){
			if(!empty($hasRecord['is_leave'])){
				return array(
					'text' => 'Leave',
					'bg' => '2E7D32',
					'fg' => 'FFFFFF'
				);
			}
			$hoursValue = isset($hasRecord['hours']) ? (float)$hasRecord['hours'] : 0;
			$hoursLabel = ($hoursValue > 0) ? (string)$hoursValue : '0';
			if($hoursValue < 8.5){
				return array(
					'text' => $hoursLabel,
					'bg' => 'C62828',
					'fg' => 'FFFFFF'
				);
			}
			return array(
				'text' => $hoursLabel,
				'bg' => '2E7D32',
				'fg' => 'FFFFFF'
			);
		}
		return array(
			'text' => '0',
			'bg' => 'C62828',
			'fg' => 'FFFFFF'
		);
	}

	private function countMemberSearchInstances($memberHours, $listbetweenDates, $joiningDate = ''){
		$empCount = 0;
		foreach($listbetweenDates as $date){
			if($this->isDateBeforeJoining($date, $joiningDate)){
				continue;
			}
			$hasRecord = isset($memberHours[$date]) ? $memberHours[$date] : null;
			if(!$hasRecord){
				$empCount++;
			}elseif(empty($hasRecord['is_leave']) && $hasRecord['hours'] < 8.5){
				$empCount++;
			}
		}
		return $empCount;
	}

	private function buildMemberSearchEmailHtml($getEmpResult, $hoursMatrix, $listbetweenDates, $periodLabel){
		$border = 'border:1px solid #cccccc;';
		$thStyle = $border . ' padding:8px 10px; background:#c1c1c1; color:#000000; font-weight:bold; font-size:13px; font-family:Arial,Helvetica,sans-serif; text-align:center; vertical-align:middle;';
		$tdStyle = $border . ' padding:8px 10px; font-size:13px; font-family:Arial,Helvetica,sans-serif; vertical-align:middle;';

		$content = '<p style="margin:0 0 16px 0; font-size:15px; color:#333333;">Hi Team,</p>';
		$content .= '<p style="margin:0 0 20px 0; font-size:15px; color:#444444; line-height:1.55;">Please find the Timesheet Defaulter report for <b>' . htmlspecialchars($periodLabel, ENT_QUOTES, 'UTF-8') . '</b>. Leave and Unplanned Leave are shown in green as <b>Leave</b>.</p>';

		$content .= '<table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:collapse; width:100%; font-family:Arial,Helvetica,sans-serif;">';
		$content .= '<thead><tr>';
		$content .= '<th style="' . $thStyle . '">Sno</th>';
		$content .= '<th style="' . $thStyle . '">Manager Name</th>';
		$content .= '<th style="' . $thStyle . '">Employee Name</th>';
		$content .= '<th style="' . $thStyle . '">Employee ID</th>';
		$content .= '<th style="' . $thStyle . '">No of Instances</th>';
		foreach($listbetweenDates as $dateValue){
			$content .= '<th style="' . $thStyle . '">' . htmlspecialchars(date('D / d / M', strtotime($dateValue)), ENT_QUOTES, 'UTF-8') . '</th>';
		}
		$content .= '</tr></thead><tbody>';

		$cntNumber = 0;
		foreach($getEmpResult as $member){
			$memberHours = isset($hoursMatrix[$member->empId]) ? $hoursMatrix[$member->empId] : array();
			$joiningDate = isset($member->emp_joining_date) ? $member->emp_joining_date : '';
			$instanceCount = $this->countMemberSearchInstances($memberHours, $listbetweenDates, $joiningDate);
			$content .= '<tr>';
			$content .= '<td style="' . $tdStyle . ' text-align:center;">' . ($cntNumber + 1) . '</td>';
			$content .= '<td style="' . $tdStyle . '"><b>' . htmlspecialchars(isset($member->manager_name) ? $member->manager_name : '', ENT_QUOTES, 'UTF-8') . '</b></td>';
			$content .= '<td style="' . $tdStyle . '">' . htmlspecialchars(isset($member->name) ? $member->name : '', ENT_QUOTES, 'UTF-8') . '</td>';
			$content .= '<td style="' . $tdStyle . ' text-align:center;">' . htmlspecialchars(isset($member->emp_com_id) ? $member->emp_com_id : '', ENT_QUOTES, 'UTF-8') . '</td>';
			$content .= '<td style="' . $tdStyle . ' text-align:center;">' . $instanceCount . '</td>';
			foreach($listbetweenDates as $date){
				$hasRecord = isset($memberHours[$date]) ? $memberHours[$date] : null;
				$cell = $this->resolveMemberSearchDayCell($hasRecord, $date, $joiningDate);
				$cellStyle = $tdStyle . ' text-align:center; font-weight:bold; background-color:#' . $cell['bg'] . '; color:#' . $cell['fg'] . ';';
				$content .= '<td bgcolor="#' . $cell['bg'] . '" style="' . $cellStyle . '">' . htmlspecialchars($cell['text'], ENT_QUOTES, 'UTF-8') . '</td>';
			}
			$content .= '</tr>';
			$cntNumber++;
		}
		$content .= '</tbody></table>';
		$content .= '<p style="margin:24px 0 0 0; font-size:15px; color:#333333;">Best Regards,<br>eLogic Team</p>';

		return '<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Timesheet Defaulter Report</title>
</head>
<body style="margin:0; padding:28px 16px; background:#eceff1; font-family:Arial,Helvetica,sans-serif; line-height:1.5; color:#333333;">
	<div style="margin:0 auto; background:#ffffff; padding:28px 24px 32px 24px; border:1px solid #dde1e4; border-radius:6px;">
		' . $content . '
	</div>
</body>
</html>';
	}

	private function buildMemberSearchExcelFile($getEmpResult, $hoursMatrix, $listbetweenDates, $defFormDate, $defToDate, $reportTitle = 'Timesheet Defaulter Report'){
		try{
			$objPHPExcel = $this->createDefaulterSpreadsheet($getEmpResult, $hoursMatrix, $listbetweenDates, $defFormDate, $defToDate, $reportTitle);
			$safeFrom = preg_replace('/[^0-9\-]/', '', $defFormDate);
			$safeTo = preg_replace('/[^0-9\-]/', '', $defToDate);
			$filename = 'Timesheet_Defaulter_' . $safeFrom . '_to_' . $safeTo . '.xlsx';
			$tmpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save($tmpPath);
			return $tmpPath;
		}catch(Exception $e){
			if(function_exists('log_message')){
				log_message('error', 'buildMemberSearchExcelFile failed: ' . $e->getMessage());
			}
			return '';
		}
	}

	private function downloadDefaulterExcel($getEmpResult, $hoursMatrix, $listbetweenDates, $defFormDate, $defToDate, $reportTitle){
		try{
			$objPHPExcel = $this->createDefaulterSpreadsheet($getEmpResult, $hoursMatrix, $listbetweenDates, $defFormDate, $defToDate, $reportTitle);
			$safeFrom = preg_replace('/[^0-9\-]/', '', $defFormDate);
			$safeTo = preg_replace('/[^0-9\-]/', '', $defToDate);
			$filename = 'Timesheet_Defaulter_' . $safeFrom . '_to_' . $safeTo . '.xlsx';

			if(ob_get_length()){
				@ob_end_clean();
			}
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
			exit;
		}catch(Exception $e){
			if(function_exists('log_message')){
				log_message('error', 'downloadDefaulterExcel failed: ' . $e->getMessage());
			}
			show_error('Unable to generate the Excel report. Please try again.');
		}
	}

	private function createDefaulterSpreadsheet($getEmpResult, $hoursMatrix, $listbetweenDates, $defFormDate, $defToDate, $reportTitle){
		$this->load->library('excel');
		$objPHPExcel = new PHPExcel();
		$sheet = $objPHPExcel->setActiveSheetIndex(0);
		$sheet->setTitle('Defaulter Report');

		$headers = array('Sno', 'Manager Name', 'Employee Name', 'Employee ID', 'No of Instances');
		foreach($listbetweenDates as $dateValue){
			$headers[] = date('D, d M', strtotime($dateValue));
		}
		$colCount = count($headers);
		$lastCol = $this->excelColumnName($colCount);
		$periodLabel = date('d M Y', strtotime($defFormDate)) . ' to ' . date('d M Y', strtotime($defToDate));

		$totalMembers = count($getEmpResult);
		$totalInstances = 0;
		foreach($getEmpResult as $member){
			$memberHours = isset($hoursMatrix[$member->empId]) ? $hoursMatrix[$member->empId] : array();
			$joiningDate = isset($member->emp_joining_date) ? $member->emp_joining_date : '';
			$totalInstances += $this->countMemberSearchInstances($memberHours, $listbetweenDates, $joiningDate);
		}

		$thinBorder = array(
			'borders' => array(
				'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'D0D7DE'))
			),
			'alignment' => array(
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		$titleStyle = array(
			'font' => array('bold' => true, 'size' => 16, 'color' => array('rgb' => 'FFFFFF')),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '1F5076')),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		$metaStyle = array(
			'font' => array('bold' => true, 'size' => 11, 'color' => array('rgb' => '1F5076')),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'EEF5FB')),
			'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
		);
		$headerStyle = array(
			'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size' => 10),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '1F5076')),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap' => true
			),
			'borders' => array(
				'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '17405F'))
			)
		);
		$centerStyle = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$sheet->mergeCells('A1:' . $lastCol . '1');
		$sheet->setCellValue('A1', $reportTitle);
		$sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($titleStyle);
		$sheet->getRowDimension(1)->setRowHeight(28);

		$sheet->mergeCells('A2:' . $lastCol . '2');
		$sheet->setCellValue('A2', 'Period: ' . $periodLabel . '   |   Members: ' . $totalMembers . '   |   Instances: ' . $totalInstances . '   |   Generated: ' . date('d M Y, h:i A'));
		$sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray($metaStyle);
		$sheet->getRowDimension(2)->setRowHeight(22);

		$sheet->setCellValue('A3', 'Legend');
		$sheet->setCellValue('B3', 'Filled / Leave');
		$sheet->setCellValue('C3', 'Missing / Below 8.5');
		$sheet->setCellValue('D3', 'Before joining');
		if($colCount > 4){
			$sheet->mergeCells('E3:' . $lastCol . '3');
			$sheet->getStyle('E3:' . $lastCol . '3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
		}
		$sheet->getStyle('A3')->applyFromArray(array(
			'font' => array('bold' => true, 'color' => array('rgb' => '1F5076')),
			'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
		));
		$sheet->getStyle('B3')->applyFromArray(array(
			'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '2E7D32')),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
		));
		$sheet->getStyle('C3')->applyFromArray(array(
			'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'C62828')),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
		));
		$sheet->getStyle('D3')->applyFromArray(array(
			'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '9E9E9E')),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
		));
		$sheet->getRowDimension(3)->setRowHeight(20);

		$headerRow = 4;
		$sheet->fromArray($headers, null, 'A' . $headerRow);
		$sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->applyFromArray($headerStyle);
		$sheet->getRowDimension($headerRow)->setRowHeight(26);

		$line = 5;
		$sno = 1;
		foreach($getEmpResult as $member){
			$memberHours = isset($hoursMatrix[$member->empId]) ? $hoursMatrix[$member->empId] : array();
			$joiningDate = isset($member->emp_joining_date) ? $member->emp_joining_date : '';
			$instanceCount = $this->countMemberSearchInstances($memberHours, $listbetweenDates, $joiningDate);
			$rowFill = ($sno % 2 === 0) ? 'F7FBFE' : 'FFFFFF';

			$sheet->setCellValue('A' . $line, $sno);
			$sheet->setCellValue('B' . $line, isset($member->manager_name) ? $member->manager_name : '');
			$sheet->setCellValue('C' . $line, isset($member->name) ? $member->name : '');
			$sheet->setCellValue('D' . $line, isset($member->emp_com_id) ? $member->emp_com_id : '');
			$sheet->setCellValue('E' . $line, $instanceCount);

			$sheet->getStyle('A' . $line . ':E' . $line)->applyFromArray($thinBorder);
			$sheet->getStyle('A' . $line . ':E' . $line)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($rowFill);
			$sheet->getStyle('A' . $line)->applyFromArray($centerStyle);
			$sheet->getStyle('D' . $line . ':E' . $line)->applyFromArray($centerStyle);
			$sheet->getStyle('B' . $line)->getFont()->setBold(true)->getColor()->setRGB('1F5076');
			if($instanceCount > 0){
				$sheet->getStyle('E' . $line)->getFont()->setBold(true)->getColor()->setRGB('C62828');
				$sheet->getStyle('E' . $line)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFEBEE');
			}else{
				$sheet->getStyle('E' . $line)->getFont()->setBold(true)->getColor()->setRGB('2E7D32');
				$sheet->getStyle('E' . $line)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('E8F5E9');
			}

			$colIndex = 6;
			foreach($listbetweenDates as $date){
				$hasRecord = isset($memberHours[$date]) ? $memberHours[$date] : null;
				$cell = $this->resolveMemberSearchDayCell($hasRecord, $date, $joiningDate);
				$colLetter = $this->excelColumnName($colIndex);
				$sheet->setCellValue($colLetter . $line, $cell['text']);
				$sheet->getStyle($colLetter . $line)->applyFromArray(array_merge($thinBorder, $centerStyle, array(
					'font' => array('bold' => true, 'color' => array('rgb' => $cell['fg'])),
					'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => $cell['bg']))
				)));
				$colIndex++;
			}
			$sheet->getRowDimension($line)->setRowHeight(20);
			$line++;
			$sno++;
		}

		if($totalMembers === 0){
			$sheet->mergeCells('A5:' . $lastCol . '5');
			$sheet->setCellValue('A5', 'No members found for the selected filters.');
			$sheet->getStyle('A5')->applyFromArray(array(
				'font' => array('italic' => true, 'color' => array('rgb' => '6C7A89')),
				'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
			));
			$sheet->getRowDimension(5)->setRowHeight(24);
			$line = 6;
		}

		$sheet->getColumnDimension('A')->setWidth(8);
		$sheet->getColumnDimension('B')->setWidth(24);
		$sheet->getColumnDimension('C')->setWidth(24);
		$sheet->getColumnDimension('D')->setWidth(14);
		$sheet->getColumnDimension('E')->setWidth(16);
		for($i = 6; $i <= $colCount; $i++){
			$sheet->getColumnDimension($this->excelColumnName($i))->setWidth(13);
		}

		$sheet->freezePane('F5');
		if($totalMembers > 0){
			$sheet->setAutoFilter('A' . $headerRow . ':' . $lastCol . ($line - 1));
		}
		$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setFitToPage(true);
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);
		$sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
		$sheet->getHeaderFooter()->setOddHeader('&C&B' . $reportTitle);
		$sheet->getHeaderFooter()->setOddFooter('&L' . $periodLabel . '&RPage &P of &N');
		$sheet->getSheetView()->setZoomScale(110);

		return $objPHPExcel;
	}

	private function excelColumnName($index){
		$name = '';
		while($index > 0){
			$index--;
			$name = chr(65 + ($index % 26)) . $name;
			$index = (int)floor($index / 26);
		}
		return $name;
	}

	/** Names hidden from defaulter/memberSearch results, member dropdown, Excel, and email. */
	private function memberSearchExcludedNames(){
		return array(
			'syed farhan',
			'farhan',
			'hemanth kmv',
			'pradip chauhan',
			'pradip',
			'rahul kumar',
			'rupali modi',
			'rupali',
			'aditya arora'
		);
	}

	/** Company IDs hidden from defaulter grids, Excel, email, and member dropdown. */
	private function memberSearchExcludedEmpComIds(){
		return array(
			'596' // Aditya Arora
		);
	}

	/** Names hidden from defaulter/previous_user_defaulter results and member dropdown. */
	private function previousUserDefaulterExcludedNames(){
		return array(
			'syed farhan',
			'hemanth kmv',
			'pradip chauhan',
			'rahul kumar',
			'varsha k',
			'rupali modi',
			'rupali',
			'aditya arora'
		);
	}

	private function normalizePersonName($name){
		$name = html_entity_decode((string)$name, ENT_QUOTES, 'UTF-8');
		$name = str_replace(array("\xC2\xA0", '&nbsp;', "\t", "\r", "\n"), ' ', $name);
		$name = strtolower(trim($name));
		$name = preg_replace('/[^a-z0-9]+/', ' ', $name);
		$name = trim(preg_replace('/\s+/', ' ', $name));
		return $name;
	}

	private function filterRowsByExcludedNames($rows, $excludedNames){
		if(empty($rows)){
			return $rows;
		}
		$excludedEmpComIds = $this->memberSearchExcludedEmpComIds();
		$out = array();
		foreach($rows as $row){
			$empComId = isset($row->emp_com_id) ? trim((string)$row->emp_com_id) : '';
			if($empComId !== '' && in_array($empComId, $excludedEmpComIds, true)){
				continue;
			}
			$name = isset($row->name) ? $this->normalizePersonName($row->name) : '';
			if($name !== '' && $this->isExcludedMemberName($name, $excludedNames)){
				continue;
			}
			$out[] = $row;
		}
		return $out;
	}

	private function isExcludedMemberName($name, $excludedNames){
		$name = $this->normalizePersonName($name);
		if($name === '' || empty($excludedNames)){
			return false;
		}
		$nameTokens = explode(' ', $name);
		foreach($excludedNames as $excluded){
			$excluded = $this->normalizePersonName($excluded);
			if($excluded === ''){
				continue;
			}
			if($name === $excluded){
				return true;
			}
			$excludedTokens = explode(' ', $excluded);
			$missingToken = false;
			foreach($excludedTokens as $token){
				if(!in_array($token, $nameTokens, true)){
					$missingToken = true;
					break;
				}
			}
			if(!$missingToken){
				return true;
			}
		}
		return false;
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
		$selectedReportingManagers = $this->collectPostedList('reporting_manager');
		$selectedDepartments = $this->collectPostedList('department');
		$members = $this->filterRowsByExcludedNames(
			$this->defaulter_model->getMembersByReportingManager($selectedReportingManagers, $selectedDepartments),
			$this->memberSearchExcludedNames()
		);

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
