<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Resource_Schedule extends CI_Controller {

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
		
		$this->load->model('resourcelog_model');
		
		
		$this->load->helper('text');
		
		 //$this->load->library('email');
        
		if(empty($this->session->userdata['logged_in_timesheet'])){
		
			redirect('home/login');
		}
		
    }
	
	/* public function index(){
	
			$userType = $this->session->userdata['logged_in_timesheet']['user_type'];
			
			$data['getRecords'] = $this->resourcelog_model->getRecords($userType);
			
			$this->load->view('employee_reports/employee_timelog' , $data);
			
			//$this->load->view('employee_reports/add_employee_timelog');
			
			
	} */
	
	public function add($resource_record_id = NULL){
	
	   if(empty($resource_record_id)) : 
			
			 $this->load->view('resource_schedule/add_resource');
			 
		else:
			
			   $data['updateEmpRecord'] = $this->resourcelog_model->getUpdateEmpRecords($resource_record_id);
			   
			   $this->load->view('resource_schedule/update_resource' , $data);
					
		endif;	   
			
	 
	}
	
	
	public function add_resource(){
		
	if(!empty($this->session->userdata['logged_in_timesheet']['empId'])) :        
       
        $count = count($this->input->post('client_Id'));        
        
        if($count > 0) {
         
            for($i=0; $i<$count; $i++) {
                       
	if(!empty($this->input->post('client_Id')[$i] && $this->input->post('project_Id')[$i] && $this->input->post('task_Id')[$i] && $this->input->post('team_member')[$i])):
                
		$team_members = $this->input->post('team_member');
		//foreach($team_members as $mG => $member) {
			$data[] = array(			
				'emp_report_dates'			 => $this->input->post('emp_report_dates'),
				'department'				 => $this->input->post('department'),
				'client_Id' 				 => $this->input->post('client_Id')[$i],
				'project_Id' 				 => $this->input->post('project_Id')[$i],
				'task_Id' 			 		 => $this->input->post('task_Id')[$i], // Store task with comma separate
				//'team_member'			     => trim($member),
				'team_member'			     => $this->input->post('team_member')[$i],
				'emp_time_hours'			 => $this->input->post('emp_time_hours')[$i],
				'workplace' 			 	 => $this->input->post('workplace')[$i],			
				'comments' 			 		 => $this->input->post('comments')[$i],
				'project_manager' 			 => $this->session->userdata['logged_in_timesheet']['username'],
				'record_created_user_id'	 => $this->session->userdata['logged_in_timesheet']['empId'],
				'created_at'    	 		 => date('Y-m-d H:i:s'),
				'updated_at' 				 => date('Y-m-d H:i:s')
			);
		//}
                
        endif;        
        
            }            
            
        }        
		 
		//echo '<pre>'; print_r($data);

		$getStoredDetails = $this->resourcelog_model->addEmpRecords($data);
			
		redirect('resource_schedule');
		 
	  endif;
	   
	
	}
	
	
	public function update_resource_records() {
		
		//echo '<pre>'; print_r($_REQUEST); exit;
	
		
	 if(!empty($this->session->userdata['logged_in_timesheet']['empId'])) :
	
		$resource_id = $this->input->post('resource_id');
		
		
		$data = array(
			'emp_report_dates'			 => $this->input->post('emp_report_dates'),
			'department'				 => $this->input->post('department'),
			'client_Id' 				 => $this->input->post('client_Id'),
			'project_Id' 				 => $this->input->post('project_Id'),
			'task_Id' 			 		 => $this->input->post('task_Id'), // Store task with comma separate
			'team_member'			     => $this->input->post('team_member'),
			'emp_time_hours'			 => $this->input->post('emp_time_hours'),
			'workplace' 			     => $this->input->post('workplace'),
			'comments' 			 		 => $this->input->post('comments'),			
			'project_manager' 			 => $this->session->userdata['logged_in_timesheet']['username'],
			'record_created_user_id'	 => $this->session->userdata['logged_in_timesheet']['empId'],
			'updated_at' 				 => date('Y-m-d H:i:s')
			);
	
	        $this->resourcelog_model->updateResourceRecords($data , $resource_id);
		
		    redirect('resource_schedule');
		 
	  endif;
			
	
	}
	
	
	public function getListOfProjectsWithClient(){  // Getting Client wise projects
	
	  $client_Id  = $this->input->post('client_Id'); 
	   
	   if(!empty($client_Id)) :
	   
	   		$getProjects = $this->resourcelog_model->getListOfProjectsWithClient($client_Id);
	   
	   endif; 
	
	 }  // Getting Client wise projects END
    
    
    public function getListOfProjectsGeneralWithClient(){  // Getting Client wise projects
	
	  $client_Id  = $this->input->post('client_Id'); 
	   
	   if(!empty($client_Id)) :
	   
	   		$getProjects = $this->resourcelog_model->getListOfProjectsGeneralWithClient($client_Id);
	   
	   endif; 
	
	 }  // Getting Client wise projects END
	 
	 
	 public function getClientProjects(){  // Getting Client wise projects
	
	  $client_Id  = $this->input->post('client_Id'); 
	   
	   if(!empty($client_Id)) :
	   
	   		$getProjects = $this->resourcelog_model->getClientWiseProjects($client_Id);
	   
	   endif; 
	
	 }  // Getting Client wise projects END
	 
	
	public function getProjectsTask(){ // Getting Project wise task
	
		$project_Id  = $this->input->post('project_Id'); 
	   
	   if(!empty($project_Id)) :
	   
	   		$getTask = $this->resourcelog_model->getProjectWiseTaskLWGList($project_Id);
	   
	   endif; 
	
	} // Getting Project wise task END
	
	
	
	
	
  
  
	public function searchProjectsTask(){ // Getting Project wise task
	
		$project_Id  = $this->input->post('project_Id');
       
       if($project_Id == 'all'):
       
                $client_Id   = $this->input->post('client_Id');
       else:
       
                    $client_Id   = NULL;
       endif;
	   
           if(!empty($project_Id)) :

                $getTask = $this->resourcelog_model->searchProjectWiseTask($project_Id , $client_Id);

           endif; 
	
	} 
    
  
	
 
/*********************************** Datatable Sort , Search , Pagination For Employee Added Task Report Log ********************************************/
	
		
	function index(){
     
		    if(!empty($this->input->get('form_date') && $this->input->get('to_date'))){   
		
			
			$params = array(
            'form_date' => $this->input->get('form_date'),
            'to_date' => $this->input->get('to_date'),
			'department' => $this->input->get('department'),
			'client_Id' => $this->input->get('client_Id'),
			'project_manger' => $this->input->get('project_manger'),
			'team_member' => $this->input->get('team_member'),	
            );
		
		$data['getRecords'] = $this->resourcelog_model->getResourceData($params); 
		
		$this->load->view('resource_schedule/resource_schedule_information' , $data);	
				
				
			}else{
				
			  $userType = $this->session->userdata['logged_in_timesheet']['user_type'];
			
			  $data['getRecords'] = $this->resourcelog_model->getRecords($userType);
			
			  $this->load->view('resource_schedule/resource_schedule_information' , $data);
				
			}
		
    	}
	
	
   
/********************************** Datatable Sort , Search , Pagination For Employee Added Task Report Log  **************************************/	
	

    
    /*********************************** Datatable Sort , Search , Pagination For Employee Added Task Report Log ********************************************/
	
		
	function search_res_report(){
     
		    if(!empty($this->input->get('form_date') && $this->input->get('to_date'))){   
		
			
			$params = array(
            'form_date' => $this->input->get('form_date'),
            'to_date' => $this->input->get('to_date'),
			'department' => $this->input->get('department'),
			'client_Id' => $this->input->get('client_Id'),
			'project_manger' => $this->input->get('project_manger'),
			'team_member' => $this->input->get('team_member'),	
            );
		
		$data['getRecords'] = $this->resourcelog_model->getResourceData($params); 
		
		$this->load->view('resource_schedule/search_resource_schedule_information' , $data);	
				
				
			}else{
				
			  $userType = $this->session->userdata['logged_in_timesheet']['user_type'];
			
			  $data['getRecords'] = $this->resourcelog_model->getRecords($userType);
			
			  $this->load->view('resource_schedule/search_resource_options' , $data);
				
			}
		
    	}
	
	
   
	/**
	 * Build today's resource schedule as Excel and email it to laxmikanth@elogictech.com
	 * Triggered via AJAX from the Resource Schedule screen.
	 */
	public function send_today_resource_schedule_email() {
		header('Content-Type: application/json');
		
		// Get today's records in the same way as the index() method (no filters, just today)
		$userType = isset($this->session->userdata['logged_in_timesheet']['user_type'])
			? $this->session->userdata['logged_in_timesheet']['user_type']
			: '';
		
		if (empty($userType)) {
			echo json_encode(array(
				'success' => false,
				'message' => 'Session expired. Please login again.'
			));
			return;
		}
		
		$records = $this->resourcelog_model->getRecords($userType);
		if (empty($records)) {
			echo json_encode(array(
				'success' => false,
				'message' => 'No resource schedule data available for today.'
			));
			return;
		}

		// Build summary metrics (same logic as grid header)
		$archManagers = array('41','394','53','71','155','182','270','47');
		$excludedHeadcountMembers = array(
			'arch' => array(array('pradip', 'chauhan')),
			'mep'  => array(array('syed', 'farhan')),
		);
		$isExcludedFromHeadcount = function($normalizedMember, $groupKey) use ($excludedHeadcountMembers) {
			if (empty($normalizedMember) || !isset($excludedHeadcountMembers[$groupKey])) {
				return false;
			}
			foreach ($excludedHeadcountMembers[$groupKey] as $requiredTokens) {
				$matched = true;
				foreach ($requiredTokens as $token) {
					if (strpos($normalizedMember, $token) === false) {
						$matched = false;
						break;
					}
				}
				if ($matched) {
					return true;
				}
			}
			return false;
		};
		$summary = array(
			'arch' => array(
				'production_hours'       => 0,
				'training_hours'         => 0,
				'intern_hours'           => 0,
				'training_fac_hours'     => 0,
				'available_hours'        => 0,
				'not_assigned_hours'     => 0,
				'planned_leave_count'    => 0,
				'unplanned_leave_count'  => 0,
				'wfh_members'            => array(),
				'wfh_count'              => 0,
				'dept_members'           => array(),
				'employee_count'         => 0,
				'leave_hours'            => 0,
				'capacity_hours'         => 0,
			),
			'mep' => array(
				'production_hours'       => 0,
				'training_hours'         => 0,
				'intern_hours'           => 0,
				'training_fac_hours'     => 0,
				'available_hours'        => 0,
				'not_assigned_hours'     => 0,
				'planned_leave_count'    => 0,
				'unplanned_leave_count'  => 0,
				'wfh_members'            => array(),
				'wfh_count'              => 0,
				'dept_members'           => array(),
				'employee_count'         => 0,
				'leave_hours'            => 0,
				'capacity_hours'         => 0,
			),
		);

		foreach ($records as $r) {
			$groupKey = (isset($r->reporting_manger) && in_array($r->reporting_manger, $archManagers)) ? 'arch' : 'mep';
			$hours = isset($r->emp_time_hours) ? (float)$r->emp_time_hours : 0;
			$taskName = isset($r->task_name) ? trim($r->task_name) : '';
			$workplace = isset($r->workplace) ? trim($r->workplace) : '';
			$teamMember = isset($r->team_member) ? $r->team_member : null;
			if (!empty($teamMember)) {
				$normalizedMember = strtolower(trim(preg_replace('/\s+/', ' ', $teamMember)));
				if (!$isExcludedFromHeadcount($normalizedMember, $groupKey)) {
					$summary[$groupKey]['dept_members'][$teamMember] = true;
				}
			}

			if ($taskName === 'Available') {
				$summary[$groupKey]['available_hours'] += $hours;
			} elseif ($taskName === 'Training' || $taskName === 'Learning & Development') {
				$summary[$groupKey]['training_hours'] += $hours;
			} elseif ($taskName === 'Training Faciliatory') {
				$summary[$groupKey]['training_fac_hours'] += $hours;
			} elseif ($taskName === 'New Joinee Training') {
				$summary[$groupKey]['intern_hours'] += $hours;
			} elseif ($taskName === 'Leave') {
				$summary[$groupKey]['planned_leave_count'] += 1;
				$summary[$groupKey]['leave_hours'] += ($hours != 0 ? $hours : 8.5);
			} elseif ($taskName === 'Unplanned Leave') {
				$summary[$groupKey]['unplanned_leave_count'] += 1;
				$summary[$groupKey]['leave_hours'] += ($hours != 0 ? $hours : 8.5);
			} elseif ($taskName === '') {
				// Not assigned hours – fall back to 8.5 hours when 0
				$summary[$groupKey]['not_assigned_hours'] += ($hours != 0 ? $hours : 8.5);
			}

			// WFH count (unique team members)
			if ($workplace === 'WFH' && !empty($teamMember)) {
				if (!in_array($teamMember, $summary[$groupKey]['wfh_members'], true)) {
					$summary[$groupKey]['wfh_members'][] = $teamMember;
				}
			}

			// Production hours: anything not covered above, not empty task, not WFH
			$isSpecialTask = in_array($taskName, array(
				'Available',
				'Training',
				'Learning & Development',
				'Training Faciliatory',
				'New Joinee Training',
				'Leave',
				'Unplanned Leave',
				''
			), true);
			if (!$isSpecialTask && $workplace !== 'WFH' && $hours > 0) {
				$summary[$groupKey]['production_hours'] += $hours;
			}
		}
		$summary['arch']['wfh_count'] = count($summary['arch']['wfh_members']);
		$summary['mep']['wfh_count'] = count($summary['mep']['wfh_members']);

		$getActiveEmployeeCount = function($departments, $excludeRules = array(), $useLike = false) {
			$query = $this->db->select('name')
				->from('employee_details')
				->where('status', 'Active');
			if ($useLike) {
				$query->like('department', $departments);
			} else {
				$query->where_in('department', $departments);
			}
			$rows = $query->get()->result();
			$count = 0;
			foreach ($rows as $row) {
				$name = isset($row->name) ? strtolower(trim(preg_replace('/\s+/', ' ', $row->name))) : '';
				if ($name === '') {
					continue;
				}
				$isExcluded = false;
				foreach ($excludeRules as $requiredTokens) {
					$matched = true;
					foreach ($requiredTokens as $token) {
						if (strpos($name, $token) === false) {
							$matched = false;
							break;
						}
					}
					if ($matched) {
						$isExcluded = true;
						break;
					}
				}
				if (!$isExcluded) {
					$count++;
				}
			}
			return $count;
		};

		// Employee count must come from active employee master, not timesheet row count.
		$summary['arch']['employee_count'] = $getActiveEmployeeCount(
			array('Architectural', 'Structural', '3D Visualization'),
			$excludedHeadcountMembers['arch'],
			false
		);
		$summary['mep']['employee_count'] = $getActiveEmployeeCount(
			'MEP',
			$excludedHeadcountMembers['mep'],
			true
		);

		$dailyStdHours = 8.5;
		foreach (array('arch', 'mep') as $deptKey) {
			$totalStdHours = $summary[$deptKey]['employee_count'] * $dailyStdHours;
			$summary[$deptKey]['capacity_hours'] = max(0, $totalStdHours - (float)$summary[$deptKey]['leave_hours']);
		}
		
		// Prepare Excel sheet
		$this->load->library('excel');
		$objPHPExcel = $this->excel;
		$objPHPExcel->setActiveSheetIndex(0);
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle('Today Resource Schedule');
		
		// Header row
		$row = 1;
		$sheet->setCellValue('A' . $row, 'Date');
		$sheet->setCellValue('B' . $row, 'Department');
		$sheet->setCellValue('C' . $row, 'Manager');
		$sheet->setCellValue('D' . $row, 'Team Member');
		$sheet->setCellValue('E' . $row, 'Client');
		$sheet->setCellValue('F' . $row, 'Project');
		$sheet->setCellValue('G' . $row, 'Task');
		$sheet->setCellValue('H' . $row, 'Hours');
		$sheet->setCellValue('I' . $row, 'Workplace');
		$sheet->setCellValue('J' . $row, 'Comments');

		// Header styling (blue background, white bold text, centered)
		$headerStyle = array(
			'font' => array(
				'bold' => true,
				'size' => 11,
				'color' => array('rgb' => 'FFFFFF')
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
			),
			'fill' => array(
				'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => '4F81BD')
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => 'D9D9D9')
				)
			)
		);
		$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($headerStyle);
		$sheet->getRowDimension($row)->setRowHeight(22);
		$sheet->setAutoFilter('A' . $row . ':J' . $row);
		
		// Slightly wider columns for readability
		$sheet->getColumnDimension('A')->setWidth(12);
		$sheet->getColumnDimension('B')->setWidth(18);
		$sheet->getColumnDimension('C')->setWidth(22);
		$sheet->getColumnDimension('D')->setWidth(22);
		$sheet->getColumnDimension('E')->setWidth(28);
		$sheet->getColumnDimension('F')->setWidth(36);
		$sheet->getColumnDimension('G')->setWidth(24);
		$sheet->getColumnDimension('H')->setWidth(10);
		$sheet->getColumnDimension('I')->setWidth(14);
		$sheet->getColumnDimension('J')->setWidth(50);
		
		// Data rows
		$row = 2;
		// Styles matching grid colors
		$leaveStyle = array(
			'fill' => array(
				'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => '7030A0')
			),
			'font' => array(
				'color' => array('rgb' => 'FFFFFF'),
				'bold'  => true
			)
		);
		$trainingStyle = array(
			'fill' => array(
				'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'C2FFC7')
			),
			'font' => array(
				'color' => array('rgb' => '000000'),
				'bold'  => true
			)
		);
		$unplannedLeaveStyle = array(
			'fill' => array(
				'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'FFE31A')
			),
			'font' => array(
				'color' => array('rgb' => '000000'),
				'bold'  => true
			)
		);
		$availableStyle = array(
			'fill' => array(
				'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => '059212')
			),
			'font' => array(
				'color' => array('rgb' => 'FFFFFF'),
				'bold'  => true
			)
		);
		$newJoineeStyle = array(
			'fill' => array(
				'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'FFA500')
			),
			'font' => array(
				'color' => array('rgb' => 'FFFFFF'),
				'bold'  => true
			)
		);
		$wfhStyle = array(
			'fill' => array(
				'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => '008B8B')
			),
			'font' => array(
				'color' => array('rgb' => 'FFFFFF'),
				'bold'  => true
			)
		);
		$trainingFacStyle = array(
			'fill' => array(
				'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'FFB6C1')
			),
			'font' => array(
				'color' => array('rgb' => '000000'),
				'bold'  => true
			)
		);
		$notEnteredRowStyle = array(
			'fill' => array(
				'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'FF0000')
			),
			'font' => array(
				'color' => array('rgb' => 'FFFFFF'),
				'bold'  => true
			)
		);
		// Pre-compute manager groups so we can merge/stylize Manager column like the grid
		$managerCounts    = array();
		$managerFirstRow  = array();
		$managerNames     = array();
		foreach ($records as $r) {
			$mid = isset($r->reporting_manger) ? $r->reporting_manger : null;
			if (empty($mid)) {
				continue;
			}
			if (!isset($managerCounts[$mid])) {
				$managerCounts[$mid] = 0;
				$managerNames[$mid]  = $this->resourcelog_model->getManagerName($mid);
			}
			$managerCounts[$mid]++;
		}

		// Style for dark separator row between managers (matches grid #686868 bar)
		$separatorStyle = array(
			'fill' => array(
				'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => '686868')
			),
			'borders' => array(
				'top' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '686868')
				),
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '686868')
				)
			)
		);
		$prevManagerId = null;

		foreach ($records as $r) {
			$mid = isset($r->reporting_manger) ? $r->reporting_manger : null;

			// Add a solid dark separator row when manager changes (like grid grey bar)
			if ($prevManagerId !== null && $mid !== $prevManagerId) {
				$sheet->mergeCells('A' . $row . ':J' . $row);
				$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($separatorStyle);
				$sheet->getRowDimension($row)->setRowHeight(14);
				$row++;
			}

			$sheet->setCellValue('A' . $row, isset($r->emp_report_dates) ? $r->emp_report_dates : '');
			$sheet->setCellValue('B' . $row, isset($r->department) ? $r->department : '');

			if (!empty($mid) && !isset($managerFirstRow[$mid])) {
				// First row for this manager: remember start row and set name
				$managerFirstRow[$mid] = $row;
				$managerName = isset($managerNames[$mid]) ? $managerNames[$mid] : '';
				$sheet->setCellValue('C' . $row, $managerName);
			} else {
				// Subsequent rows under same manager will be merged later
				$sheet->setCellValue('C' . $row, '');
			}

			$sheet->setCellValue('D' . $row, isset($r->name) ? $r->name : '');
			$sheet->setCellValue('E' . $row, isset($r->client_name) ? $r->client_name : '');
			$sheet->setCellValue('F' . $row, isset($r->project_name) ? $r->project_name : '');
			$sheet->setCellValue('G' . $row, isset($r->task_name) ? $r->task_name : '');
			$sheet->setCellValue('H' . $row, isset($r->emp_time_hours) ? $r->emp_time_hours : '');
			$sheet->setCellValue('I' . $row, isset($r->workplace) ? $r->workplace : '');
			$sheet->setCellValue('J' . $row, isset($r->comments) ? $r->comments : '');

			// Apply task / workplace based coloring similar to grid
			$taskName = isset($r->task_name) ? trim($r->task_name) : '';
			$workplace = isset($r->workplace) ? trim($r->workplace) : '';
			if ($taskName === 'Leave') {
				$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($leaveStyle);
			} elseif ($taskName === 'Training' || $taskName === 'Learning & Development') {
				$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($trainingStyle);
			} elseif ($taskName === 'Unplanned Leave') {
				$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($unplannedLeaveStyle);
			} elseif ($taskName === 'Available') {
				$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($availableStyle);
			} elseif ($taskName === 'New Joinee Training') {
				$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($newJoineeStyle);
			} elseif ($workplace === 'WFH' || $taskName === 'Project Coordination') {
				// Same teal style used in grid for WFH and requested for Project Coordination
				$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($wfhStyle);
			} elseif ($taskName === 'Training Faciliatory') {
				$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($trainingFacStyle);
			} elseif ($taskName === '') {
				// Resource not entered
				$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($notEnteredRowStyle);
			}

			// Increase row height for better readability
			$sheet->getRowDimension($row)->setRowHeight(22);
			$row++;
			$prevManagerId = $mid;
		}

		// Merge and style Manager column per manager group (light blue background, bold text)
		if (!empty($managerFirstRow)) {
			$managerColStyle = array(
				'fill' => array(
					'type'  => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'D1E9F6')
				),
				'font' => array(
					'bold'  => true,
					'color' => array('rgb' => '000000')
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
					'wrap'       => true
				),
				'borders' => array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => 'B7D3EA')
					)
				)
			);
			foreach ($managerFirstRow as $mid => $startRow) {
				$span   = isset($managerCounts[$mid]) ? (int)$managerCounts[$mid] : 1;
				$endRow = $startRow + $span - 1;
				if ($span > 1) {
					$sheet->mergeCells('C' . $startRow . ':C' . $endRow);
				}
				$sheet->getStyle('C' . $startRow . ':C' . $endRow)->applyFromArray($managerColStyle);
			}
		}

		// Apply table-style borders and alignment to data range
		$dataEndRow = $row - 1;
		if ($dataEndRow >= 2) {
			$dataStyle = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => 'D9D9D9')
					)
				),
				'alignment' => array(
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				)
			);
			$sheet->getStyle('A2:J' . $dataEndRow)->applyFromArray($dataStyle);
			// Center numeric and short code columns
			$sheet->getStyle('H2:H' . $dataEndRow)
				->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle('I2:I' . $dataEndRow)
				->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}

		// Freeze header row
		$sheet->freezePane('A2');

		// Append summary section (Architectural / MEP) similar to on-screen header
		$summaryTitleStyle = array(
			'font' => array(
				'bold' => true,
				'size' => 13,
				'color' => array('rgb' => '333333')
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
			),
			'fill' => array(
				'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'F2F2F2')
			),
			'borders' => array(
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => 'CCCCCC')
				)
			)
		);
		$badgeBaseStyle = array(
			'font' => array(
				'bold' => true,
				'size' => 10,
				'color' => array('rgb' => 'FFFFFF')
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => true
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => 'FFFFFF')
				)
			)
		);
		// Colors taken from CSS classes in the view
		$badgeStyles = array(
			'production' => array_merge_recursive($badgeBaseStyle, array(
				'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '428BC5'))
			)),
			'training' => array_merge_recursive($badgeBaseStyle, array(
				'font' => array('color' => array('rgb' => '000000')),
				'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'C2FFC7'))
			)),
			'njtraining' => array_merge_recursive($badgeBaseStyle, array(
				'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFA500'))
			)),
			'faciliatory' => array_merge_recursive($badgeBaseStyle, array(
				'font' => array('color' => array('rgb' => '000000')),
				'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFB6C1'))
			)),
			'available' => array_merge_recursive($badgeBaseStyle, array(
				'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '059212'))
			)),
			'na' => array_merge_recursive($badgeBaseStyle, array(
				'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FF0000'))
			)),
			'leave' => array_merge_recursive($badgeBaseStyle, array(
				'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '7030A0'))
			)),
			'upleave' => array_merge_recursive($badgeBaseStyle, array(
				'font' => array('color' => array('rgb' => '000000')),
				'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFE31A'))
			)),
			'wfh' => array_merge_recursive($badgeBaseStyle, array(
				'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '008B8B'))
			)),
		);

		$summaryRow = $dataEndRow + 3;
		// Architectural row
		$sheet->setCellValue('A' . $summaryRow, 'Architectural - Structural - 3D Visualisation');
		$sheet->mergeCells('A' . $summaryRow . ':J' . $summaryRow);
		$sheet->getStyle('A' . $summaryRow . ':J' . $summaryRow)->applyFromArray($summaryTitleStyle);
		$sheet->getRowDimension($summaryRow)->setRowHeight(22);

		$summaryRow++;
		$archMetrics = array(
			array('col' => 'A', 'key' => 'production',  'label' => 'Production Hours : ' . $summary['arch']['production_hours']),
			array('col' => 'B', 'key' => 'training',    'label' => 'Training Hours : ' . $summary['arch']['training_hours']),
			array('col' => 'C', 'key' => 'njtraining',  'label' => 'Intern Training Hours : ' . $summary['arch']['intern_hours']),
			array('col' => 'D', 'key' => 'faciliatory', 'label' => 'Training Faciliatory Hours : ' . $summary['arch']['training_fac_hours']),
			array('col' => 'E', 'key' => 'available',   'label' => 'Available Hours : ' . $summary['arch']['available_hours']),
			array('col' => 'F', 'key' => 'na',          'label' => 'Not Assigned Hours : ' . $summary['arch']['not_assigned_hours']),
			array('col' => 'G', 'key' => 'leave',       'label' => 'Planned Leave Count : ' . $summary['arch']['planned_leave_count']),
			array('col' => 'H', 'key' => 'upleave',     'label' => 'Unplanned leave Count : ' . $summary['arch']['unplanned_leave_count']),
			array('col' => 'I', 'key' => 'wfh',         'label' => 'WFH Count : ' . $summary['arch']['wfh_count']),
		);
		foreach ($archMetrics as $m) {
			$coord = $m['col'] . $summaryRow;
			$sheet->setCellValue($coord, $m['label']);
			if (isset($badgeStyles[$m['key']])) {
				$sheet->getStyle($coord)->applyFromArray($badgeStyles[$m['key']]);
			}
			$sheet->getRowDimension($summaryRow)->setRowHeight(30);
		}

		// MEP row
		$summaryRow += 2;
		$sheet->setCellValue('A' . $summaryRow, 'MEP - Mechanical - Electrical - Plumbing');
		$sheet->mergeCells('A' . $summaryRow . ':J' . $summaryRow);
		$sheet->getStyle('A' . $summaryRow . ':J' . $summaryRow)->applyFromArray($summaryTitleStyle);
		$sheet->getRowDimension($summaryRow)->setRowHeight(22);

		$summaryRow++;
		$mepMetrics = array(
			array('col' => 'A', 'key' => 'production',  'label' => 'Production Hours : ' . $summary['mep']['production_hours']),
			array('col' => 'B', 'key' => 'training',    'label' => 'Training Hours : ' . $summary['mep']['training_hours']),
			array('col' => 'C', 'key' => 'njtraining',  'label' => 'Intern Training Hours : ' . $summary['mep']['intern_hours']),
			array('col' => 'D', 'key' => 'faciliatory', 'label' => 'Training Faciliatory Hours : ' . $summary['mep']['training_fac_hours']),
			array('col' => 'E', 'key' => 'available',   'label' => 'Available Hours : ' . $summary['mep']['available_hours']),
			array('col' => 'F', 'key' => 'na',          'label' => 'Not Assigned Hours : ' . $summary['mep']['not_assigned_hours']),
			array('col' => 'G', 'key' => 'leave',       'label' => 'Planned Leave Count : ' . $summary['mep']['planned_leave_count']),
			array('col' => 'H', 'key' => 'upleave',     'label' => 'Unplanned leave Count : ' . $summary['mep']['unplanned_leave_count']),
			array('col' => 'I', 'key' => 'wfh',         'label' => 'WFH Count : ' . $summary['mep']['wfh_count']),
		);
		foreach ($mepMetrics as $m) {
			$coord = $m['col'] . $summaryRow;
			$sheet->setCellValue($coord, $m['label']);
			if (isset($badgeStyles[$m['key']])) {
				$sheet->getStyle($coord)->applyFromArray($badgeStyles[$m['key']]);
			}
			$sheet->getRowDimension($summaryRow)->setRowHeight(30);
		}
		
		// Save to temp file
		$filename = 'Today_Resource_Schedule_' . date('Y-m-d_His') . '.xlsx';
		$tmpPath  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($tmpPath);
		
		// Email the Excel file
		$this->load->library('email');
		$emailConfig = array(
			'mailtype' => 'html',
			'charset'  => 'utf-8',
			'wordwrap' => true,
			'newline'  => "\r\n",
			'crlf'     => "\r\n",
		);
		$this->email->initialize($emailConfig);
		$this->email->from('info@elogictech.com', 'eLogic Timesheet');
		$this->email->to('elogic_pms@elogictech.com,rupali@elogictech.com,jaishree@elogictech.com,laxmikanth@elogictech.com');

		//$this->email->to('laxmikanth@elogictech.com');

		// Use report date label from data (fallback to today) in DD MMM YYYY
		$reportDate = isset($records[0]->emp_report_dates) && !empty($records[0]->emp_report_dates)
			? $records[0]->emp_report_dates
			: date('Y-m-d');
		$reportDateLabel = date('d M Y', strtotime($reportDate));

		$this->email->subject('Daily Team Member–Wise Resource Schedule – ' . $reportDateLabel);

		// Helper to format hours nicely
		$fmtHours = function($value) {
			$value = (float)$value;
			return preg_replace('/\\.0+$/', '', number_format($value, 1));
		};

		$archLeave = (int)$summary['arch']['planned_leave_count'];
		$archWfh   = (int)$summary['arch']['wfh_count'];

		$mepLeave = (int)$summary['mep']['planned_leave_count'];
		$mepWfh   = (int)$summary['mep']['wfh_count'];

		$archTotalHours = (float)$summary['arch']['production_hours']
			+ (float)$summary['arch']['training_hours']
			+ (float)$summary['arch']['available_hours']
			+ (float)$summary['arch']['not_assigned_hours'];
		$mepTotalHours = (float)$summary['mep']['production_hours']
			+ (float)$summary['mep']['training_hours']
			+ (float)$summary['mep']['available_hours']
			+ (float)$summary['mep']['not_assigned_hours'];

		$pctColor = function($pct) {
			if ($pct >= 75) {
				return '#1e7e34'; // green
			}
			if ($pct >= 50) {
				return '#b26a00'; // amber
			}
			return '#c0392b'; // red
		};

		$fmtHoursWithPct = function($hours, $totalHours) use ($fmtHours, $pctColor) {
			$hoursNum = (float)$hours;
			$pct = ($totalHours > 0) ? round(($hoursNum / $totalHours) * 100) : 0;
			$color = $pctColor($pct);
			return $fmtHours($hoursNum) . ' <span style="color:' . $color . '; font-weight:700;">(' . $pct . '%)</span>';
		};
		$fmtUtilizationWithCoreFormula = function($billableHours, $capacityHours) use ($fmtHours, $pctColor) {
			$billableNum = (float)$billableHours;
			$capacityNum = (float)$capacityHours;
			$pct = ($capacityNum > 0) ? round(($billableNum / $capacityNum) * 100) : 0;
			$color = $pctColor($pct);
			return $fmtHours($billableNum) . ' <span style="color:' . $color . '; font-weight:700;">(' . $pct . '%)</span>';
		};

		$buildPieChartUrl = function($title, $labels, $values, $colors) {
			$datalabelFormatterFn = "function(value, ctx) { var data = ctx.chart.data.datasets[0].data; var total = 0; for (var i = 0; i < data.length; i++) { total += Number(data[i]); } if (!total) { return '0%'; } return Math.round((Number(value) / total) * 100) + '%'; }";
			$labelsJson = json_encode($labels);
			$valuesJson = json_encode($values);
			$colorsJson = json_encode($colors);
			$titleJson  = json_encode($title);
			$configJs = "{plugins:['chartjs-plugin-datalabels'],type:'pie',data:{labels:" . $labelsJson . ",datasets:[{data:" . $valuesJson . ",backgroundColor:" . $colorsJson . ",borderColor:'#ffffff',borderWidth:3}]},options:{responsive:true,maintainAspectRatio:false,animation:false,layout:{padding:8},title:{display:false,text:" . $titleJson . ",fontSize:18,fontStyle:'bold',fontColor:'#1f2d3d'},legend:{display:true,position:'top',labels:{boxWidth:18,fontSize:15,fontStyle:'bold',fontColor:'#2f3b4a'}},plugins:{datalabels:{display:true,color:'#ffffff',textStrokeColor:'#1f2d3d',textStrokeWidth:3,font:{size:13,weight:'bold'},anchor:'center',align:'center',clamp:true,formatter:" . $datalabelFormatterFn . "}}}}";
			return 'https://quickchart.io/chart?width=460&height=270&format=png&backgroundColor=white&v=2.9.4&devicePixelRatio=2&ts=' . time() . '&c=' . rawurlencode($configJs);
		};

		$buildPercentLegendLabels = function($baseLabels, $values) {
			$total = array_sum($values);
			$out = array();
			foreach ($baseLabels as $idx => $label) {
				$v = isset($values[$idx]) ? (float)$values[$idx] : 0;
				$pct = ($total > 0) ? round(($v / $total) * 100) : 0;
				$out[] = $label . ' (' . $pct . '%)';
			}
			return $out;
		};

		// Body copy unchanged; layout matches planned-vs-actual email style (white card, navy table, gold CTA)
		$content = '<p style="margin:0 0 20px 0; font-size:16px; color:#333333;">Hi Team,</p>';
		$content .= '<p style="margin:0 0 28px 0; font-size:16px; color:#444444; line-height:1.65;">Please find below the Team Member–Wise <b style="background-color: #f4d03f; padding: 5px 10px; border-radius: 5px;">Resource Schedule for ' . $reportDateLabel . '</b>.</p>';

		$chartLabels = array('Utilization', 'Available');
		$chartColors = array('#2c5aa0', '#0B6623');
		$combinedCapacityHours = (float)$summary['arch']['capacity_hours'] + (float)$summary['mep']['capacity_hours'];
		$combinedUtilizationHours = (float)$summary['arch']['production_hours'] + (float)$summary['mep']['production_hours'];
		$combinedAvailableHours = max(0, $combinedCapacityHours - $combinedUtilizationHours);
		$combinedChartValues = array(
			$combinedUtilizationHours,
			$combinedAvailableHours
		);
		$combinedChartLabels = $buildPercentLegendLabels($chartLabels, $combinedChartValues);
		$combinedChartTitle = 'Architecture / Structure / 3D Visualization + MEP (Mechanical / Electrical / Plumbing)';
		$combinedChartUrl = $buildPieChartUrl($combinedChartTitle, $combinedChartLabels, $combinedChartValues, $chartColors);

		$content .= '<table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:separate; border-spacing:12px 0; width:100%; margin:0 0 24px 0; font-family:Arial,Helvetica,sans-serif;">';
		$content .= '<tr>';
		$content .= '<td style="padding:0; text-align:center; vertical-align:top; width:100%; border:1px solid #d9dee3; border-radius:10px; background:#ffffff; box-shadow:0 1px 3px rgba(0,0,0,0.06);"><img src="' . htmlspecialchars($combinedChartUrl, ENT_QUOTES, 'UTF-8') . '" alt="Combined utilization and available chart" style="display:block; width:100%; max-width:640px; margin:8px auto; border-radius:8px;"></td>';
		$content .= '</tr>';
		$content .= '</table>';

		$cellBorder = 'border:1px solid #cccccc;';
		$thStyle = $cellBorder . ' padding:17px 14px; background:#1a5276; color:#ffffff; font-weight:bold; font-size:18px; font-family:Arial,Helvetica,sans-serif; vertical-align:middle;';
		$content .= '<table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:collapse; width:100%; max-width:100%; margin:0 0 8px 0; font-family:Arial,Helvetica,sans-serif; table-layout:auto;">';
		$content .= '<thead><tr>';
		$content .= '<th style="' . $thStyle . ' text-align:left; white-space:nowrap;">Task</th>';
		$content .= '<th style="' . $thStyle . ' text-align:center; line-height:1.35;">Architecture / Structure / 3D Visualization <br>Hours</th>';
		$content .= '<th style="' . $thStyle . ' text-align:center; line-height:1.35;">MEP (Mechanical / Electrical / Plumbing) <br>Hours</th>';
		$content .= '</tr></thead><tbody>';
		$summaryRows = array(
			array('Total Employees', (string)$summary['arch']['employee_count'], (string)$summary['mep']['employee_count']),
			array('Employees Available Today', (string)$summary['arch']['employee_count'] - $archLeave, (string)$summary['mep']['employee_count'] - $mepLeave),
			array('Utilization Hours', $fmtUtilizationWithCoreFormula($summary['arch']['production_hours'], $summary['arch']['capacity_hours']), $fmtUtilizationWithCoreFormula($summary['mep']['production_hours'], $summary['mep']['capacity_hours'])),
			array('Training Hours', $fmtHoursWithPct($summary['arch']['training_hours'], $archTotalHours), $fmtHoursWithPct($summary['mep']['training_hours'], $mepTotalHours)),
			array('Available Hours', $fmtHoursWithPct($summary['arch']['available_hours'], $archTotalHours), $fmtHoursWithPct($summary['mep']['available_hours'], $mepTotalHours)),
			array('Not Assigned Hours', $fmtHoursWithPct($summary['arch']['not_assigned_hours'], $archTotalHours), $fmtHoursWithPct($summary['mep']['not_assigned_hours'], $mepTotalHours)),
			array('Planned Leave Count', (string)$archLeave, (string)$mepLeave),
			array('WFH Count', (string)$archWfh, (string)$mepWfh),
		);
		$rowIdx = 0;
		foreach ($summaryRows as $sr) {
			$rowBg = ($rowIdx % 2 === 0) ? '#f4f7f9' : '#ffffff';
			$tdLabel = $cellBorder . ' padding:16px 16px; text-align:left; font-weight:bold; font-size:17px; color:#333333; background:' . $rowBg . '; vertical-align:middle; white-space:nowrap;';
			$tdNum   = $cellBorder . ' padding:16px 16px; font-weight:bold; text-align:center; font-size:17px; color:#333333; background:' . $rowBg . '; vertical-align:middle;';
			$content .= '<tr>';
			$content .= '<td style="' . $tdLabel . '">' . htmlspecialchars($sr[0], ENT_QUOTES, 'UTF-8') . '</td>';
			$content .= '<td style="' . $tdNum . '">' . $sr[1] . '</td>';
			$content .= '<td style="' . $tdNum . '">' . $sr[2] . '</td>';
			$content .= '</tr>';
			$rowIdx++;
		}
		$tdFoot = $cellBorder . ' padding:14px 16px; background:#e9eff4; font-size:16px; vertical-align:middle;';
		
		$content .= '</tbody></table>';

		$content .= '<p style="margin:28px 0 22px 0; font-size:16px; color:#444444; line-height:1.65;">Please find the attached file for the detailed team member–wise allocation.</p>';
		$content .= '<a href="http://172.168.0.12:82/elogic_timesheet/resource_schedule" style="display:inline-block; padding:14px 36px; font-weight:bold; font-size:15px; color:#222222; background:#f4d03f; border-radius:6px; text-decoration:none; border:1px solid #d4b82e;" target="_blank">View Report</a>';
		$content .= '<p style="margin:32px 0 0 0; font-size:16px; color:#333333;">Best Regards,<br>eLogic Team</p>';

		$emailBody = '<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Daily Team Member–Wise Resource Schedule</title>
</head>
<body style="margin:0; padding:36px 16px; background:#eceff1; font-family: Arial, Helvetica, sans-serif; line-height:1.65; color:#333333;">
	<div style="margin:0 auto; background:#ffffff; padding:36px 32px 40px 32px; border:1px solid #dde1e4; border-radius:6px; box-shadow:0 1px 4px rgba(0,0,0,0.06);">
		' . $content . '
	</div>
</body>
</html>';

		$this->email->message($emailBody);

		// Attach only if the temp file exists
		if (file_exists($tmpPath)) {
			$this->email->attach($tmpPath);
		}

		$sent = @$this->email->send();
		@unlink($tmpPath);
		
		if ($sent) {	
			echo json_encode(array(
				'success' => true,
				'message' => 'Today\'s resource schedule has been emailed to elogic_pms@elogictech.com.'
			));
		} else {
			// Log detailed email error for troubleshooting
			if (function_exists('log_message')) {
				log_message('error', 'send_today_resource_schedule_email failed: ' . $this->email->print_debugger(array('headers', 'subject', 'body')));
			}
			echo json_encode(array(
				'success' => false,
				'message' => 'Failed to send email. Please contact the software team.'
			));
		}
	}
   
/********************************** Datatable Sort , Search , Pagination For Employee Added Task Report Log  **************************************/	
      
	
}
