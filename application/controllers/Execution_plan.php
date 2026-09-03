<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Execution_plan extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('session');
		$this->load->model('timesheet_login');
		$this->load->model('execution_plan_model');

		if (empty($this->session->userdata['logged_in_timesheet'])) {
			redirect('home/login');
		}
	}

	public function index() {
		$filterState = $this->build_execution_plan_filter_state();
		$params = $filterState['params'];

		$data['allClientResult'] = $this->execution_plan_model->get_execution_plan_report($params);
		$data['clientGroups'] = $this->build_execution_plan_client_groups($data['allClientResult'], $filterState['selectedManDaysType']);
		$data['projectStatusSummary'] = $this->execution_plan_model->get_project_status_summary_counts_by_filters($params);
		$data['departments'] = $this->execution_plan_model->get_filter_departments();
		$data['clients'] = $this->execution_plan_model->get_filter_clients();
		$data['projects'] = $this->execution_plan_model->get_filter_projects();
		$data['managers'] = $this->execution_plan_model->get_filter_managers();
		$data['years'] = $this->execution_plan_model->get_filter_years();
		$data['months'] = $this->execution_plan_model->get_filter_months();
		$data['project_statuses'] = $this->execution_plan_model->get_filter_project_statuses();
		
		$data['department'] = $filterState['department'];
		$data['client_Id'] = $filterState['client_Id'];
		$data['project_Id'] = $filterState['project_Id'];
		$data['project_manager'] = $filterState['project_manager'];
		$data['man_days'] = $filterState['man_days'];
		$data['project_status'] = $filterState['project_status'];
		$data['from_year'] = $filterState['from_year'];
		$data['from_month'] = $filterState['from_month'];
		$data['to_year'] = $filterState['to_year'];
		$data['to_month'] = $filterState['to_month'];
		$data['selected_man_days_type'] = $filterState['selectedManDaysType'];
		$data['selected_project_status'] = $filterState['selectedProjectStatus'];

		$this->load->view('execution_plan/execution_plan', $data);
	}

	public function export_report() {
		$filterState = $this->build_execution_plan_filter_state();
		$params = $filterState['params'];
		$selectedBillingType = $filterState['selectedManDaysType'];

		$rows = $this->execution_plan_model->get_execution_plan_report($params);
		$clientGroups = $this->build_execution_plan_client_groups($rows, $selectedBillingType);
		$hideResourceColumn = ($selectedBillingType === 'hourly');
		$isHourlyBilling = ($selectedBillingType === 'hourly');
		$isMonthlyBilling = ($selectedBillingType === 'monthly');
		$differenceColumnLabel = "Difference\nP. EST - TS";

		require_once APPPATH . 'third_party/PHPExcel/Classes/PHPExcel.php';
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle('Execution Plan');

		if ($hideResourceColumn) {
			$sheet->setCellValue('A1', 'Project Manager');
			$sheet->setCellValue('B1', 'Client Name / Projects');
			$sheet->setCellValue('C1', 'Start Date');
			$sheet->setCellValue('D1', 'End Date');
			$sheet->setCellValue('E1', 'Billing Type');
			$sheet->setCellValue('F1', 'Timesheet Date');
			$sheet->setCellValue('G1', 'Project Status');
			$sheet->setCellValue('H1', 'Project Estimated Hours');
			$sheet->setCellValue('I1', 'Timesheet Hours');
			$sheet->setCellValue('J1', 'Invoice Hours');
			$sheet->setCellValue('K1', $differenceColumnLabel);
		} else {
			$sheet->setCellValue('A1', 'Project Manager');
			$sheet->setCellValue('B1', 'Client Name / Projects');
			$sheet->setCellValue('C1', 'Start Date');
			$sheet->setCellValue('D1', 'End Date');
			$sheet->setCellValue('E1', 'Billing Type');
			$sheet->setCellValue('F1', 'Timesheet Date');
			$sheet->setCellValue('G1', 'Project Status');
			$sheet->setCellValue('H1', 'Project Estimated Hours');
			$sheet->setCellValue('I1', 'Timesheet Hours');
			$sheet->setCellValue('J1', 'Invoice Hours');
			$sheet->setCellValue('K1', $differenceColumnLabel);
			$sheet->setCellValue('L1', 'Resources');
			$sheet->setCellValue('M1', 'Team Members');
		}

		$headerStyle = array(
			'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '2C5AA0')),
			'alignment' => array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
		);
		$sheet->getStyle($hideResourceColumn ? 'A1:K1' : 'A1:M1')->applyFromArray($headerStyle);
		$sheet->getRowDimension(1)->setRowHeight(30);

		$formatDate = function($value) {
			if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
				return '';
			}
			$ts = strtotime($value);
			return $ts !== false ? date('d-M-Y', $ts) : '';
		};
		$formatHours = function($value) {
			$value = (float)$value;
			if (fmod($value, 1.0) == 0.0) {
				return (string)(int)$value;
			}
			return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
		};
		$formatEstimatedHours = function($value) use ($formatHours) {
			$value = (float)$value;
			if ($value == 0) {
				return 'As Per Actual';
			}
			return $formatHours($value);
		};
		$formatBillingType = function($value) {
			$value = trim((string)$value);
			if ($value === '') {
				return '';
			}
			return is_numeric($value) ? $value : ucfirst(strtolower($value));
		};
		$formatStatus = function($value) {
			$normalized = strtolower(trim((string)$value));
			if ($normalized === '') {
				return '';
			}
			if ($normalized === 'process' || $normalized === 'in process' || $normalized === 'in_process') {
				return 'IN PROCESS';
			}
			if ($normalized === 'closed') {
				return 'CLOSED';
			}
			return strtoupper($normalized);
		};
		$formatTeamMembers = function($value) {
			$value = trim((string)$value);
			if ($value === '') {
				return '';
			}
			$normalized = str_replace(array("\r\n", "\n", "\r", ';', '|'), ',', $value);
			$parts = array_values(array_unique(array_filter(array_map('trim', explode(',', $normalized)), function($item) {
				return $item !== '';
			})));
			if (empty($parts)) {
				return '';
			}
			return implode(', ', $parts) . ' ( ' . count($parts) . ' )';
		};
		$formatTeamMembersCount = function($value, $excludeNames = array()) {
			$value = trim((string)$value);
			if ($value === '') {
				return '';
			}
			$normalized = str_replace(array("\r\n", "\n", "\r", ';', '|'), ',', $value);
			$parts = array_filter(array_map('trim', explode(',', $normalized)), function($item) {
				return $item !== '' && strtolower($item) !== 'please choose team members';
			});
			$excludeMap = array();
			foreach ((array)$excludeNames as $excludeName) {
				$excludeKey = strtolower(trim((string)$excludeName));
				if ($excludeKey !== '' && $excludeKey !== 'n/a') {
					$excludeMap[$excludeKey] = true;
				}
			}
			$unique = array();
			foreach ($parts as $name) {
				$key = strtolower($name);
				if (isset($excludeMap[$key])) {
					continue;
				}
				if (!isset($unique[$key])) {
					$unique[$key] = $name;
				}
			}
			if (empty($unique)) {
				return '';
			}
			return '( ' . count($unique) . ' )';
		};

		$line = 2;
		foreach ($clientGroups as $clientGroup) {
			$clientName = $clientGroup['clientName'];
			$clientManagerDisplay = $clientGroup['clientManagerDisplay'];
			$clientStatus = $this->execution_plan_format_client_status($clientGroup['clientStatus']);
			$clientScheduleTotal = $clientGroup['clientScheduleTotal'];
			$clientTimesheetTotal = $clientGroup['clientTimesheetTotal'];
			$clientInvoiceTotal = $clientGroup['clientInvoiceTotal'];
			$clientBillingTypeDisplay = $clientGroup['clientBillingTypeDisplay'];
			$clientBillingMode = $clientGroup['clientBillingMode'];
			$clientDiff = $this->execution_plan_calculate_difference($clientBillingMode, $clientScheduleTotal, $clientTimesheetTotal, $clientInvoiceTotal);
			$clientStartDate = $clientGroup['clientStartDateTs'] !== null ? date('d-M-Y', $clientGroup['clientStartDateTs']) : '';
			$clientEndDate = $clientGroup['clientEndDateTs'] !== null ? date('d-M-Y', $clientGroup['clientEndDateTs']) : '';
			$clientTimesheetEntryDate = !empty($clientGroup['clientTimesheetEntryDateTs']) ? date('d-M-Y', $clientGroup['clientTimesheetEntryDateTs']) : '';

			if ($hideResourceColumn) {
				$sheet->setCellValue('A' . $line, $clientManagerDisplay);
				$sheet->setCellValue('B' . $line, $clientName);
				$sheet->setCellValue('C' . $line, $clientStartDate);
				$sheet->setCellValue('D' . $line, $clientEndDate);
				$sheet->setCellValue('E' . $line, $clientBillingTypeDisplay);
				$sheet->setCellValue('F' . $line, $clientTimesheetEntryDate);
				$sheet->setCellValue('G' . $line, $clientStatus);
				$sheet->setCellValue('H' . $line, $formatEstimatedHours($clientScheduleTotal));
				$sheet->setCellValue('I' . $line, $formatHours($clientTimesheetTotal));
				$sheet->setCellValue('J' . $line, $formatHours($clientInvoiceTotal));
				$sheet->setCellValue('K' . $line, $formatHours($clientDiff));
				$sheet->getStyle('A' . $line . ':K' . $line)->getFont()->setBold(true);
			} else {
				$sheet->setCellValue('A' . $line, $clientManagerDisplay);
				$sheet->setCellValue('B' . $line, $clientName);
				$sheet->setCellValue('C' . $line, $clientStartDate);
				$sheet->setCellValue('D' . $line, $clientEndDate);
				$sheet->setCellValue('E' . $line, $clientBillingTypeDisplay);
				$sheet->setCellValue('F' . $line, $clientTimesheetEntryDate);
				$sheet->setCellValue('G' . $line, $clientStatus);
				$sheet->setCellValue('H' . $line, $formatEstimatedHours($clientScheduleTotal));
				$sheet->setCellValue('I' . $line, $formatHours($clientTimesheetTotal));
				$sheet->setCellValue('J' . $line, $formatHours($clientInvoiceTotal));
				$sheet->setCellValue('K' . $line, $formatHours($clientDiff));
				$sheet->setCellValue('L' . $line, '');
				$sheet->setCellValue('M' . $line, '');
				$sheet->getStyle('A' . $line . ':M' . $line)->getFont()->setBold(true);
			}
			$line++;

			foreach ($clientGroup['projects'] as $projectRow) {
				$schedule = !empty($projectRow->schedule_hours) ? (float)$projectRow->schedule_hours : 0;
				$timesheet = !empty($projectRow->timesheet_hours) ? (float)$projectRow->timesheet_hours : 0;
				$invoice = !empty($projectRow->invoice_hours) ? (float)$projectRow->invoice_hours : 0;
				$projectBillingMode = $this->execution_plan_billing_mode(isset($projectRow->man_days) ? $projectRow->man_days : '', $selectedBillingType);
				$diff = $this->execution_plan_calculate_difference($projectBillingMode, $schedule, $timesheet, $invoice);
				$projectName = !empty($projectRow->project_name) ? $projectRow->project_name : 'N/A';
				$projectManagerName = !empty($projectRow->project_manager_name) ? trim($projectRow->project_manager_name) : 'N/A';

				if ($hideResourceColumn) {
					$sheet->setCellValue('A' . $line, $projectManagerName);
					$sheet->setCellValue('B' . $line, '  > ' . $projectName);
					$sheet->setCellValue('C' . $line, $formatDate(isset($projectRow->project_start_date) ? $projectRow->project_start_date : ''));
					$sheet->setCellValue('D' . $line, $formatDate(isset($projectRow->project_end_date) ? $projectRow->project_end_date : ''));
					$sheet->setCellValue('E' . $line, $formatBillingType(isset($projectRow->man_days) ? $projectRow->man_days : ''));
					$sheet->setCellValue('F' . $line, $formatDate(isset($projectRow->timesheet_entry_date) ? $projectRow->timesheet_entry_date : ''));
					$sheet->setCellValue('G' . $line, $formatStatus(isset($projectRow->project_status) ? $projectRow->project_status : ''));
					$sheet->setCellValue('H' . $line, $formatEstimatedHours($schedule));
					$sheet->setCellValue('I' . $line, $formatHours($timesheet));
					$sheet->setCellValue('J' . $line, $formatHours($invoice));
					$sheet->setCellValue('K' . $line, $formatHours($diff));
				} else {
					$sheet->setCellValue('A' . $line, $projectManagerName);
					$sheet->setCellValue('B' . $line, '  > ' . $projectName);
					$sheet->setCellValue('C' . $line, $formatDate(isset($projectRow->project_start_date) ? $projectRow->project_start_date : ''));
					$sheet->setCellValue('D' . $line, $formatDate(isset($projectRow->project_end_date) ? $projectRow->project_end_date : ''));
					$sheet->setCellValue('E' . $line, $formatBillingType(isset($projectRow->man_days) ? $projectRow->man_days : ''));
					$sheet->setCellValue('F' . $line, $formatDate(isset($projectRow->timesheet_entry_date) ? $projectRow->timesheet_entry_date : ''));
					$sheet->setCellValue('G' . $line, $formatStatus(isset($projectRow->project_status) ? $projectRow->project_status : ''));
					$sheet->setCellValue('H' . $line, $formatEstimatedHours($schedule));
					$sheet->setCellValue('I' . $line, $formatHours($timesheet));
					$sheet->setCellValue('J' . $line, $formatHours($invoice));
					$sheet->setCellValue('K' . $line, $formatHours($diff));
					$sheet->setCellValue('L' . $line, $formatTeamMembers(isset($projectRow->team_members) ? $projectRow->team_members : ''));
					$assignedTeamCount = isset($projectRow->assigned_team_count) ? (int)$projectRow->assigned_team_count : 0;
					$sheet->setCellValue('M' . $line, ($assignedTeamCount > 0) ? '( ' . $assignedTeamCount . ' )' : '');
				}
				$line++;
			}
		}

		$lastCol = $hideResourceColumn ? 'K' : 'M';
		$lastRow = ($line > 2) ? ($line - 1) : 1;
		$usedRange = 'A1:' . $lastCol . $lastRow;

		$sheet->getStyle($usedRange)->applyFromArray(array(
			'alignment' => array(
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => 'D0D5DD')
				)
			)
		));
		$centerAlign = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap' => true
			)
		);
		if ($hideResourceColumn) {
			$sheet->getStyle('C2:' . $lastCol . $lastRow)->applyFromArray($centerAlign);
			$columnWidths = array(
				'A' => 24, 'B' => 34, 'C' => 15, 'D' => 15, 'E' => 14,
				'F' => 15, 'G' => 14, 'H' => 18, 'I' => 15, 'J' => 15, 'K' => 16
			);
		} else {
			$sheet->getStyle('C2:K' . $lastRow)->applyFromArray($centerAlign);
			$sheet->getStyle('M2:M' . $lastRow)->applyFromArray($centerAlign);
			$sheet->getStyle('L2:L' . $lastRow)->getAlignment()
				->setWrapText(true)
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
				->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$columnWidths = array(
				'A' => 24, 'B' => 34, 'C' => 15, 'D' => 15, 'E' => 14,
				'F' => 15, 'G' => 14, 'H' => 18, 'I' => 15, 'J' => 15, 'K' => 16,
				'L' => 42, 'M' => 16
			);
		}
		foreach ($columnWidths as $col => $width) {
			$sheet->getColumnDimension($col)->setAutoSize(false);
			$sheet->getColumnDimension($col)->setWidth($width);
		}
		$sheet->getStyle('A1:' . $lastCol . '1')->getAlignment()->setWrapText(true);
		$sheet->freezePane('A2');
		$sheet->getRowDimension(1)->setRowHeight(36);
		for ($rowIndex = 2; $rowIndex <= $lastRow; $rowIndex++) {
			$sheet->getRowDimension($rowIndex)->setRowHeight(-1);
		}

		$filename = 'Execution_Plan_Report_' . date('Ymd_His') . '.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}

	public function get_clients_by_departments() {
		$departments = $this->normalize_filter_values($this->input->get_post('department'));
		$clients = $this->execution_plan_model->get_filter_clients_by_departments($departments);
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(array('clients' => $clients)));
	}

	public function get_projects_by_clients() {
		$clientIds = $this->normalize_filter_values($this->input->get_post('client_Id'));
		$departments = $this->normalize_filter_values($this->input->get_post('department'));
		$projects = $this->execution_plan_model->get_filter_projects_by_clients($clientIds, $departments);
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(array('projects' => $projects)));
	}

	private function build_execution_plan_filter_state() {
		$rawProjectStatus = $this->input->get_post('project_status');
		$selectedProjectStatus = '';
		if ($rawProjectStatus !== null) {
			$selectedProjectStatus = is_array($rawProjectStatus) ? reset($rawProjectStatus) : $rawProjectStatus;
			$selectedProjectStatus = strtolower(trim((string)$selectedProjectStatus));
		}
		$rawFromYear = $this->input->get_post('from_year');
		$rawToYear = $this->input->get_post('to_year');
		$rawManDays = $this->input->get_post('man_days');
		$selectedManDaysType = '';
		if ($rawManDays !== null) {
			$rawManDaysValue = is_array($rawManDays) ? reset($rawManDays) : $rawManDays;
			$rawManDaysValue = strtolower(trim((string)$rawManDaysValue));
			if (in_array($rawManDaysValue, array('hourly', 'monthly'), true)) {
				$selectedManDaysType = $rawManDaysValue;
			}
		}

		$department     = $this->normalize_filter_values($this->input->get_post('department'));
		$clientId       = $this->normalize_filter_values($this->input->get_post('client_Id'));
		$projectId      = $this->normalize_filter_values($this->input->get_post('project_Id'));
		$projectManager = $this->normalize_filter_values($this->input->get_post('project_manager'));
		$manDays        = $this->normalize_filter_values($this->input->get_post('man_days'));
		$projectStatus  = $this->normalize_filter_values($this->input->get_post('project_status'));
		if ($rawProjectStatus === null) {
			$projectStatus = array();
		}
		$fromYear       = $this->normalize_filter_values($this->input->get_post('from_year'));
		$fromMonth      = $this->normalize_filter_values($this->input->get_post('from_month'));
		$toYear         = $this->normalize_filter_values($this->input->get_post('to_year'));
		$toMonth        = $this->normalize_filter_values($this->input->get_post('to_month'));

		$isInitialYearLoad = ($rawFromYear === null && $rawToYear === null);
		$applyDefaultYear = ($this->input->get_post('ep_default_year') === '1');

		if (empty($fromYear) && empty($toYear)) {
			if ($applyDefaultYear) {
				$currentYear = (string)date('Y');
				$fromYear = array($currentYear);
				$toYear = array($currentYear);
			} elseif ($isInitialYearLoad && $selectedProjectStatus !== 'all') {
				$currentYear = (string)date('Y');
				$fromYear = array($currentYear);
				$toYear = array($currentYear);
			}
		}

		$params = array(
			'department'      => $department,
			'client_Id'       => $clientId,
			'project_Id'      => $projectId,
			'project_manager' => $projectManager,
			'man_days'        => $manDays,
			'project_status'  => $projectStatus,
			'from_year'       => $fromYear,
			'from_month'      => $fromMonth,
			'to_year'         => $toYear,
			'to_month'        => $toMonth
		);

		return array(
			'params' => $params,
			'selectedManDaysType' => $selectedManDaysType,
			'selectedProjectStatus' => $selectedProjectStatus,
			'department' => $department,
			'client_Id' => $clientId,
			'project_Id' => $projectId,
			'project_manager' => $projectManager,
			'man_days' => $manDays,
			'project_status' => $projectStatus,
			'from_year' => $fromYear,
			'from_month' => $fromMonth,
			'to_year' => $toYear,
			'to_month' => $toMonth
		);
	}

	private function execution_plan_project_date_ts($value) {
		if ($value === null || $value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
			return null;
		}
		$ts = strtotime($value);
		return ($ts !== false) ? $ts : null;
	}

	private function execution_plan_project_sort_ts($projectRow) {
		$endTs = $this->execution_plan_project_date_ts(isset($projectRow->project_end_date) ? $projectRow->project_end_date : '');
		if ($endTs !== null) {
			return $endTs;
		}
		$startTs = $this->execution_plan_project_date_ts(isset($projectRow->project_start_date) ? $projectRow->project_start_date : '');
		return ($startTs !== null) ? $startTs : 0;
	}

	private function execution_plan_compare_projects_desc($a, $b) {
		$aTs = $this->execution_plan_project_sort_ts($a);
		$bTs = $this->execution_plan_project_sort_ts($b);
		if ($aTs !== $bTs) {
			return ($aTs > $bTs) ? -1 : 1;
		}

		$aStart = $this->execution_plan_project_date_ts(isset($a->project_start_date) ? $a->project_start_date : '');
		$bStart = $this->execution_plan_project_date_ts(isset($b->project_start_date) ? $b->project_start_date : '');
		$aStart = ($aStart !== null) ? $aStart : 0;
		$bStart = ($bStart !== null) ? $bStart : 0;
		if ($aStart !== $bStart) {
			return ($aStart > $bStart) ? -1 : 1;
		}

		$aName = !empty($a->project_name) ? $a->project_name : '';
		$bName = !empty($b->project_name) ? $b->project_name : '';
		return strcasecmp($bName, $aName);
	}

	private function execution_plan_compare_client_groups_desc($a, $b) {
		if ($a['sortTs'] !== $b['sortTs']) {
			return ($a['sortTs'] > $b['sortTs']) ? -1 : 1;
		}
		return strcasecmp($b['clientName'], $a['clientName']);
	}

	private function execution_plan_format_billing_type($value) {
		$value = trim((string)$value);
		if ($value === '') {
			return '';
		}
		return is_numeric($value) ? $value : ucfirst(strtolower($value));
	}

	private function build_execution_plan_client_groups($rows, $selectedBillingType = '') {
		$byClient = array();
		foreach ($rows as $row) {
			$clientName = !empty($row->client_name) ? trim($row->client_name) : 'N/A';
			if (!isset($byClient[$clientName])) {
				$byClient[$clientName] = array();
			}
			$byClient[$clientName][] = $row;
		}

		$clientGroups = array();
		foreach ($byClient as $clientName => $projects) {
			usort($projects, array($this, 'execution_plan_compare_projects_desc'));
			$latestProject = !empty($projects) ? $projects[0] : null;

			$clientScheduleTotal = 0;
			$clientTimesheetTotal = 0;
			$clientInvoiceTotal = 0;
			$clientBillingTypes = array();
			$clientTimesheetEntryDateTs = null;

			foreach ($projects as $projectRow) {
				$clientScheduleTotal += !empty($projectRow->schedule_hours) ? (float)$projectRow->schedule_hours : 0;
				$clientTimesheetTotal += !empty($projectRow->timesheet_hours) ? (float)$projectRow->timesheet_hours : 0;
				$clientInvoiceTotal += !empty($projectRow->invoice_hours) ? (float)$projectRow->invoice_hours : 0;

				$timesheetEntryTs = $this->execution_plan_project_date_ts(isset($projectRow->timesheet_entry_date) ? $projectRow->timesheet_entry_date : '');
				if ($timesheetEntryTs !== null && ($clientTimesheetEntryDateTs === null || $timesheetEntryTs > $clientTimesheetEntryDateTs)) {
					$clientTimesheetEntryDateTs = $timesheetEntryTs;
				}

				$billingType = $this->execution_plan_format_billing_type(isset($projectRow->man_days) ? $projectRow->man_days : '');
				if ($billingType !== '') {
					$clientBillingTypes[strtolower($billingType)] = $billingType;
				}
			}

			$clientStartDateTs = null;
			$clientEndDateTs = null;
			if ($latestProject !== null) {
				$clientStartDateTs = $this->execution_plan_project_date_ts(isset($latestProject->client_start_date) ? $latestProject->client_start_date : '');
				$clientEndDateTs = $this->execution_plan_project_date_ts(isset($latestProject->client_end_date) ? $latestProject->client_end_date : '');
			}

			$clientStatus = 'Inactive';
			$clientManagerDisplay = 'N/A';
			if ($latestProject !== null) {
				$clientStatus = !empty($latestProject->client_status) ? trim((string)$latestProject->client_status) : 'Inactive';
				$projectManagerName = !empty($latestProject->project_manager_name) ? trim($latestProject->project_manager_name) : '';
				if ($projectManagerName !== '') {
					$clientManagerDisplay = $projectManagerName;
				}
			}

			$clientBillingTypeDisplay = '';
			if (count($clientBillingTypes) === 1) {
				$clientBillingTypeDisplay = reset($clientBillingTypes);
			} elseif (count($clientBillingTypes) > 1) {
				$clientBillingTypeDisplay = 'Multiple';
			}

			if (count($clientBillingTypes) === 1) {
				$clientBillingMode = $this->execution_plan_billing_mode(reset($clientBillingTypes), $selectedBillingType);
			} else {
				$clientBillingMode = $this->execution_plan_billing_mode('', $selectedBillingType);
			}

			$clientGroups[] = array(
				'clientName' => $clientName,
				'projects' => $projects,
				'clientManagerDisplay' => $clientManagerDisplay,
				'clientStatus' => $clientStatus,
				'clientStartDateTs' => $clientStartDateTs,
				'clientEndDateTs' => $clientEndDateTs,
				'clientScheduleTotal' => $clientScheduleTotal,
				'clientTimesheetTotal' => $clientTimesheetTotal,
				'clientInvoiceTotal' => $clientInvoiceTotal,
				'clientBillingTypeDisplay' => $clientBillingTypeDisplay,
				'clientBillingMode' => $clientBillingMode,
				'clientTimesheetEntryDateTs' => $clientTimesheetEntryDateTs,
				'sortTs' => ($clientEndDateTs !== null) ? $clientEndDateTs : 0
			);
		}

		usort($clientGroups, array($this, 'execution_plan_compare_client_groups_desc'));
		return $clientGroups;
	}

	private function execution_plan_format_client_status($value) {
		$normalized = strtolower(trim((string)$value));
		if ($normalized === 'active') {
			return 'Active';
		}
		if ($normalized === 'inactive' || $normalized === 'in_active' || $normalized === 'in active') {
			return 'Inactive';
		}
		return $value !== '' ? ucwords(strtolower(trim((string)$value))) : 'Inactive';
	}

	private function execution_plan_billing_mode($billingValue, $selectedBillingType = '') {
		$normalized = strtolower(trim((string)$billingValue));
		if ($normalized !== '') {
			if (strpos($normalized, 'hour') !== false) {
				return 'hourly';
			}
			if (strpos($normalized, 'month') !== false) {
				return 'monthly';
			}
		}
		$selectedNormalized = strtolower(trim((string)$selectedBillingType));
		if ($selectedNormalized === 'hourly' || $selectedNormalized === 'monthly') {
			return $selectedNormalized;
		}
		return 'monthly';
	}

	private function execution_plan_calculate_difference($billingMode, $scheduleHours, $timesheetHours, $invoiceHours) {
		$scheduleHours = (float)$scheduleHours;
		$timesheetHours = (float)$timesheetHours;

		// As Per Actual (0 estimated hours): show timesheet hours in Difference column
		if ($scheduleHours == 0) {
			return $timesheetHours;
		}

		if ($billingMode === 'hourly') {
			return $scheduleHours - $timesheetHours;
		}

		// Monthly: show timesheet hours only
		return $timesheetHours;
	}

	private function normalize_filter_values($value) {
		if (is_array($value)) {
			$clean = array();
			foreach ($value as $item) {
				$item = trim((string)$item);
				if ($item !== '' && $item !== 'all') {
					$clean[] = $item;
				}
			}
			return array_values(array_unique($clean));
		}

		$value = trim((string)$value);
		if ($value === '' || $value === 'all') {
			return array();
		}
		return array($value);
	}

}
