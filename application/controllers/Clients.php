<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clients extends CI_Controller {

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
		
		$this->load->helper('text');
        
        $this->load->library('excel'); // load excel library
		// Load database
		
		// Load database
		$this->load->model('timesheet_login');
		
		$this->load->model('client_model');
        
        $this->load->model('project_model');
		
		$this->load->model('task_model');
		
		if(empty($this->session->userdata['logged_in_timesheet'])){
		
			redirect('home/login');
		} 
		
		
    }
	
	public function index(){
		
			$data['getClients'] = $this->client_model->getClients();
			
			$this->load->view('clients/clients' , $data);
			
	}
	
	public function add($client_Id = NULL){
	
	   if(empty($client_Id)) :
			
			 $this->load->view('clients/add_client');
			 
		else:
			
			 $data['updateClient'] = $this->client_model->getClients($client_Id);
	
		     $this->load->view('clients/add_client' , $data);

		endif;
			
	 
	}
	
	public function addclient(){ // Adding new Client function.

		$this->form_validation->set_error_delimiters('<label class="error">', '</label>');
	
	    $this->form_validation->set_rules('client_name', 'Client name already exit. Please try another name', 'required|trim|is_unique[client_details.client_name]');
		
		//$this->form_validation->set_rules('client_email', 'Client email already exit. Please try another email', 'required|trim|is_unique[client_details.client_email]');
		
		if ($this->form_validation->run() == FALSE) {
	
			$this->load->view('clients/add_client');
			
	    }else{

			$data = array(
			'client_name' 				 => $this->input->post('client_name'),
			'client_email' 				 => $this->input->post('client_email'),
            'client_country' 		     => $this->input->post('client_country'),
            'client_state' 				 => $this->input->post('client_state'),
            'client_city' 				 => $this->input->post('client_city'),
			'client_contact_num' 		 => $this->input->post('client_contact_num'),
			'department' 		 		 => $this->input->post('department'),
			'client_desc'				 => $this->input->post('client_desc'),
			'status' 					 => 'Active',
			'empId'						 => $this->session->userdata['logged_in_timesheet']['empId'],
			'created_at'    			 => date('Y-m-d H:i:s'),
			'updated_at' 		 		 => date('Y-m-d H:i:s')
			);

	     $this->client_model->add_client($data);

		 redirect('clients');

	   }

	}

	public function updateclient(){ // Adding new Client function.

		$client_Id = $this->input->post('client_Id');

		$this->form_validation->set_error_delimiters('<label class="error">', '</label>');

	    $this->form_validation->set_rules('client_name', 'Client name already exit. Please try another name', 'required|trim|callback_exists_clients');

		if ($this->form_validation->run() == FALSE) {

			$data = array(
            'client_email' 				 => $this->input->post('client_email'),
            'client_country' 		     => $this->input->post('client_country'),
            'client_state' 				 => $this->input->post('client_state'),
            'client_city' 				 => $this->input->post('client_city'),
			'client_desc'				 => $this->input->post('client_desc'),
			'client_contact_num' 		 => $this->input->post('client_contact_num'),
			'department' 		 		 => $this->input->post('department'),
			'empId'						 => $this->session->userdata['logged_in_timesheet']['empId'],
			'updated_at' 		 		 => date('Y-m-d H:i:s')
			);

		   $this->load->view('clients/add_client');

	       $this->client_model->update_client($data , $client_Id );

		   redirect('clients');

		   //redirect('clients/add/'.$client_Id);

	    }else{

			$data = array(
			'client_name' 				 => $this->input->post('client_name'),
			'client_email' 				 => $this->input->post('client_email'),
            'client_country' 		     => $this->input->post('client_country'),
            'client_state' 				 => $this->input->post('client_state'),
            'client_city' 				 => $this->input->post('client_city'),
			'client_contact_num' 		 => $this->input->post('client_contact_num'),
			'client_desc'				 => $this->input->post('client_desc'),
			'empId'						 => $this->session->userdata['logged_in_timesheet']['empId'],
			'updated_at' 		 		 => date('Y-m-d H:i:s')
			);

	     $this->client_model->update_client($data , $client_Id );

		 redirect('clients');

	   }

	}

  public function delete(){

        $client_Id  = $this->input->post('client_Id');

			if(!empty($client_Id)):

				$del = $this->client_model->del_client($client_Id);

			endif;

  }

  public function getRecentClients(){  //Get Recent Clients Angular js funciton

		$recentClientInfo = $this->client_model->recentClients();

		echo json_encode($recentClientInfo);

  }

  #uniqueness of task based on client and projects
    function exists_clients($str){ #uniqueness of Car Model

        $client_name = $this->input->post('client_name');

		$query = $this->db->get_where('client_details',array('client_name'=>$client_name));

		$countClients = $query->num_rows();

        if ($countClients  == 0){

            return TRUE;

        }else{

            $this->form_validation->set_message('exists_clients', 'Client name already exit. Please try another client!');

			 return FALSE;
        }
    }

	public function cs_reports(){

		$this->load->view('clients/clients_report_links');

	}

    /*************************************************** Resource Billability Feature *******************************************************************/

		public function client_ts_report(){ // Resource Billability feature

		if(!empty($this->input->post('client_Id') && $this->input->post('project_Id'))) :

		 $params = array(
            'client_Id' => $this->input->post('client_Id'),
            'project_Id' => $this->input->post('project_Id'),
            'form_date' => $this->input->post('form_date'),
            'to_date' => $this->input->post('to_date'),
            );

         $data['resultClientReport'] = $this->client_model->searchClientReport($params);

		 $this->load->view('clients/client_datewise_report' , $data);

	   else :

	       $this->load->view('clients/client_datewise_report');

	   endif;

	}
	/*************************************************** Resource Billability Feature *******************************************************************/

	/************************************ ****************** Download client report Excel & PDF format  *********************************/
	public function pdfClientReport(){

		$params = array(
            'client_Id' => $this->input->get('client_Id'),
            'project_Id' => $this->input->get('project_Id'),
            'form_date' => $this->input->get('form_date'),
            'to_date' => $this->input->get('to_date'),
            );
		
		
		
		$data['resouceBillabilityPdfResult'] = $this->client_model->searchClientReport($params);
        
		// Load all views as normal
		$this->load->view('clients/resouce_billability_pdf.php' ,$data);
		// Get output html
		$html = $this->output->get_output();
		
		// Load library
		$this->load->library('dompdf_gen');
		
		// Convert to PDF
		$this->dompdf->load_html($html);
		$this->dompdf->render();
		$this->dompdf->stream("resource_billability_".time().".pdf");
		
		
	}

	
/************************************ ****************** Download client report Excel & PDF format  *********************************/
	/**
	 * Resolve all_clients_reports date range from year/month dropdowns or legacy date fields.
	 *
	 * @return array{form_date:string,to_date:string,from_year:mixed,from_month:int,to_year:mixed,to_month:int}
	 */
	private function _resolve_all_clients_report_dates($form_date, $to_date, $from_year, $from_month, $to_year, $to_month, $isSearch = false)
	{
		$startYear = 2010;
		$endYear = (int) date('Y');
		$currentMonth = (int) date('n');
		$nowMonthStart = mktime(0, 0, 0, $currentMonth, 1, $endYear);

		$hasFromYear = ($from_year !== null && $from_year !== '');
		$hasToYear = ($to_year !== null && $to_year !== '');
		$hasFromMonth = ($from_month !== null && $from_month !== '');
		$hasToMonth = ($to_month !== null && $to_month !== '');
		$fromYearIsAll = ($hasFromYear && strtoupper(trim((string) $from_year)) === 'ALL');
		$toYearIsAll = ($hasToYear && strtoupper(trim((string) $to_year)) === 'ALL');
		if ($fromYearIsAll) {
			$hasFromMonth = false;
			$from_month = '';
		}
		if ($toYearIsAll) {
			$hasToMonth = false;
			$to_month = '';
		}

		$hasYearMonth = ($hasFromYear && $hasFromMonth && $hasToYear && $hasToMonth);
		$hasYearOnly = ($hasFromYear && $hasToYear && !$hasYearMonth);

		if (!$hasYearMonth && !$hasYearOnly && $isSearch
			&& $hasFromYear && $hasToYear
			&& !$fromYearIsAll && !$toYearIsAll) {
			if (!$hasFromMonth) {
				$from_month = (int) date('n', strtotime('first day of previous month'));
				$hasFromMonth = true;
			}
			if (!$hasToMonth) {
				$to_month = (int) date('n', strtotime('first day of previous month'));
				$hasToMonth = true;
			}
			$hasYearMonth = true;
		}

		if ($hasYearMonth || $hasYearOnly) {
			$fy = $fromYearIsAll ? $startYear : max($startYear, min($endYear, (int) $from_year));
			$ty = $toYearIsAll ? $endYear : max($startYear, min($endYear, (int) $to_year));
			if ($hasFromMonth) {
				$fm = max(1, min(12, (int) $from_month));
			} else {
				$fm = 1;
			}
			if ($hasToMonth) {
				$tm = max(1, min(12, (int) $to_month));
			} else {
				$tm = ($ty === $endYear) ? $currentMonth : 12;
			}

			if (mktime(0, 0, 0, $fm, 1, $fy) > mktime(0, 0, 0, $tm, 1, $ty)) {
				list($fy, $fm, $ty, $tm) = array($ty, $tm, $fy, $fm);
			}
			if (mktime(0, 0, 0, $tm, 1, $ty) > $nowMonthStart) {
				$ty = $endYear;
				$tm = $currentMonth;
				if (mktime(0, 0, 0, $fm, 1, $fy) > $nowMonthStart) {
					$fy = $endYear;
					$fm = $currentMonth;
				}
			}

			return array(
				'form_date' => sprintf('%04d-%02d-01', $fy, $fm),
				'to_date' => date('Y-m-t', mktime(0, 0, 0, $tm, 1, $ty)),
				'from_year' => $fromYearIsAll ? 'ALL' : $fy,
				'from_month' => $hasFromMonth ? $fm : 0,
				'to_year' => $toYearIsAll ? 'ALL' : $ty,
				'to_month' => $hasToMonth ? $tm : 0,
			);
		}

		if (!empty($form_date) && !empty($to_date)) {
			$fy = (int) date('Y', strtotime($form_date));
			$fm = (int) date('n', strtotime($form_date));
			$ty = (int) date('Y', strtotime($to_date));
			$tm = (int) date('n', strtotime($to_date));
			return array(
				'form_date' => $form_date,
				'to_date' => $to_date,
				'from_year' => $fy,
				'from_month' => $fm,
				'to_year' => $ty,
				'to_month' => $tm,
			);
		}

		$firstDayPrev = new DateTime('first day of last month');
		$lastDayPrev = new DateTime('last day of last month');
		return array(
			'form_date' => $firstDayPrev->format('Y-m-d'),
			'to_date' => $lastDayPrev->format('Y-m-d'),
			'from_year' => (int) $firstDayPrev->format('Y'),
			'from_month' => (int) $firstDayPrev->format('n'),
			'to_year' => (int) $lastDayPrev->format('Y'),
			'to_month' => (int) $lastDayPrev->format('n'),
		);
	}

	private function _all_clients_report_elogic_ids()
	{
		return array('363','374','370','369','368','367','364','361','355','270','262','253','236','210','85','78','74','49','34','32');
	}

	private function _format_all_clients_report_number($v)
	{
		$v = (float) $v;
		$s = number_format($v, 2, '.', ',');
		return preg_replace('/\.?0+$/', '', $s);
	}

	private function _is_project_general_name($projectName)
	{
		$name = trim((string) $projectName);
		if ($name === '') {
			return false;
		}
		return (bool) preg_match('/\s*-\s*\(General\)\s*$/i', $name)
			|| (bool) preg_match('/\s+\(General\)\s*$/i', $name);
	}

	private function _project_production_base_name($projectName)
	{
		$name = trim((string) $projectName);
		$name = preg_replace('/\s*-\s*\(General\)\s*$/i', '', $name);
		$name = preg_replace('/\s+\(General\)\s*$/i', '', $name);
		return trim($name);
	}

	private function _parse_employee_name_list($employeeNames)
	{
		$names = array();
		$parts = preg_split('/\s*,\s*/', (string) $employeeNames);
		foreach ($parts as $part) {
			$name = trim($part);
			if ($name !== '') {
				$names[] = $name;
			}
		}
		return $names;
	}

	private function _all_clients_report_project_key($clientId, $projectName)
	{
		$baseName = $this->_project_production_base_name($projectName);
		return (string) $clientId . '|' . strtolower($baseName);
	}

	private function _apply_production_general_hours($row, $productionHours, $generalHours, $generalInvoice, $employeeNames)
	{
		$uniqueEmployees = array_values(array_unique($employeeNames));
		$row->production_hours = (float) $productionHours;
		$row->general_hours = (float) $generalHours;
		$row->total_hours = (float) $productionHours + (float) $generalHours;
		$invoiceAmt = !empty($row->project_invoice_amt) ? (float) $row->project_invoice_amt : 0;
		$row->project_invoice_amt = $invoiceAmt + (float) $generalInvoice;
		$row->employee_names = implode(', ', $uniqueEmployees);
		$row->num_employees = count($uniqueEmployees);
		return $row;
	}

	private function _merge_all_clients_report_production_projects($allClientResult, $eLogicClientsIds)
	{
		$generalByKey = array();
		$productionRows = array();
		foreach ($allClientResult as $reportResult) {
			if (in_array($reportResult->client_Id, $eLogicClientsIds)) {
				continue;
			}
			$key = $this->_all_clients_report_project_key($reportResult->client_Id, $reportResult->project_name);
			if ($this->_is_project_general_name($reportResult->project_name)) {
				if (!isset($generalByKey[$key])) {
					$generalByKey[$key] = array();
				}
				$generalByKey[$key][] = $reportResult;
			} else {
				$productionRows[] = $reportResult;
			}
		}

		$mergedRows = array();
		$usedGeneralKeys = array();
		foreach ($productionRows as $reportResult) {
			$key = $this->_all_clients_report_project_key($reportResult->client_Id, $reportResult->project_name);
			$generalHours = 0;
			$generalInvoice = 0;
			$employeeNames = $this->_parse_employee_name_list(isset($reportResult->employee_names) ? $reportResult->employee_names : '');
			if (isset($generalByKey[$key])) {
				foreach ($generalByKey[$key] as $generalRow) {
					$generalHours += (float) $generalRow->total_hours;
					$generalInvoice += !empty($generalRow->project_invoice_amt) ? (float) $generalRow->project_invoice_amt : 0;
					$employeeNames = array_merge($employeeNames, $this->_parse_employee_name_list(isset($generalRow->employee_names) ? $generalRow->employee_names : ''));
				}
				$usedGeneralKeys[$key] = true;
			}
			$mergedRows[] = $this->_apply_production_general_hours(
				$reportResult,
				(float) $reportResult->total_hours,
				$generalHours,
				$generalInvoice,
				$employeeNames
			);
		}

		foreach ($generalByKey as $key => $generalRows) {
			if (isset($usedGeneralKeys[$key]) || empty($generalRows)) {
				continue;
			}
			$firstGeneral = clone $generalRows[0];
			$generalHours = 0;
			$generalInvoice = 0;
			$employeeNames = array();
			foreach ($generalRows as $generalRow) {
				$generalHours += (float) $generalRow->total_hours;
				$generalInvoice += !empty($generalRow->project_invoice_amt) ? (float) $generalRow->project_invoice_amt : 0;
				$employeeNames = array_merge($employeeNames, $this->_parse_employee_name_list(isset($generalRow->employee_names) ? $generalRow->employee_names : ''));
			}
			$firstGeneral->project_name = $this->_project_production_base_name($firstGeneral->project_name);
			$firstGeneral->project_invoice_amt = 0;
			$mergedRows[] = $this->_apply_production_general_hours(
				$firstGeneral,
				0,
				$generalHours,
				$generalInvoice,
				$employeeNames
			);
		}

		return $mergedRows;
	}

	private function _parse_all_clients_report_filters($isSearch = true)
	{
		$dates = $this->_resolve_all_clients_report_dates(
			$this->input->post('form_date'),
			$this->input->post('to_date'),
			$this->input->post('from_year'),
			$this->input->post('from_month'),
			$this->input->post('to_year'),
			$this->input->post('to_month'),
			$isSearch
		);

		$clientIds = $this->input->post('client_Id');
		$departments = $this->input->post('department');
		if (!is_array($clientIds)) { $clientIds = ($clientIds === 'all' || $clientIds === '' || $clientIds === null) ? 'all' : array($clientIds); }
		elseif (empty($clientIds)) { $clientIds = 'all'; }
		elseif (in_array('all', $clientIds)) { $clientIds = 'all'; }
		if (!is_array($departments)) { $departments = ($departments === 'all' || $departments === '' || $departments === null) ? 'all' : array($departments); }
		elseif (empty($departments)) { $departments = 'all'; }
		elseif (in_array('all', $departments)) { $departments = 'all'; }

		$projectManagers = $this->input->post('project_manager');
		if (!is_array($projectManagers)) { $projectManagers = ($projectManagers === 'all' || $projectManagers === '' || $projectManagers === null) ? 'all' : array($projectManagers); }
		elseif (empty($projectManagers)) { $projectManagers = 'all'; }
		elseif (in_array('all', $projectManagers)) { $projectManagers = 'all'; }

		$projectId = $this->input->post('project_Id');
		if ($projectId === null || $projectId === '' || $projectId === 'all') {
			$projectId = 'all';
		}

		return array(
			'dates' => $dates,
			'clientIds' => $clientIds,
			'departments' => $departments,
			'projectManagers' => $projectManagers,
			'projectId' => $projectId,
			'params' => array(
				'client_Id' => $clientIds,
				'department' => $departments,
				'project_manager' => $projectManagers,
				'project_Id' => $projectId,
				'form_date' => $dates['form_date'],
				'to_date' => $dates['to_date'],
			),
		);
	}

	private function _build_all_clients_report_display($allClientResult, $departments, $form_date, $to_date)
	{
		$eLogicClientsIds = $this->_all_clients_report_elogic_ids();
		$byDepartment = array();
		foreach ($allClientResult as $r) {
			if (in_array($r->client_Id, $eLogicClientsIds)) {
				continue;
			}
			$projType = isset($r->project_type) ? $r->project_type : '';
			$dept = !empty(trim($projType)) ? trim($projType) : (isset($r->client_department) ? trim($r->client_department) : 'Other');
			if ($dept === '') {
				$dept = 'Other';
			}
			if (!isset($byDepartment[$dept])) {
				$byDepartment[$dept] = array('total_hours' => 0, 'invoice_hours' => 0);
			}
			$byDepartment[$dept]['total_hours'] += (float) $r->total_hours;
			$byDepartment[$dept]['invoice_hours'] += !empty($r->project_invoice_amt) ? (float) $r->project_invoice_amt : 0;
		}

		$isAllDepartments = (empty($departments) || $departments === 'all' || (is_array($departments) && in_array('all', $departments)));
		$summaryRows = array();
		if ($isAllDepartments) {
			$combineDeptKeys = array('Architectural', '3D Visualization', 'Structural');
			$combined = array('total_hours' => 0, 'invoice_hours' => 0);
			foreach ($combineDeptKeys as $key) {
				if (isset($byDepartment[$key])) {
					$combined['total_hours'] += $byDepartment[$key]['total_hours'];
					$combined['invoice_hours'] += $byDepartment[$key]['invoice_hours'];
				}
			}
			if ($combined['total_hours'] > 0 || $combined['invoice_hours'] > 0) {
				$summaryRows['Architectural, 3D Visualization & Structural'] = $combined;
			}
			foreach ($byDepartment as $deptName => $totals) {
				if (in_array($deptName, $combineDeptKeys)) {
					continue;
				}
				$summaryRows[$deptName] = $totals;
			}
		} else {
			$summaryRows = $byDepartment;
		}

		$summaryTitle = 'Department Summary';
		if (!empty($form_date) && !empty($to_date)) {
			$fromTs = strtotime($form_date);
			$toTs = strtotime($to_date);
			if ($fromTs && $toTs) {
				$summaryTitle = date('F Y', $fromTs) . (date('Y-m', $fromTs) !== date('Y-m', $toTs) ? ' - ' . date('F Y', $toTs) : '') . ' Actual ( Vs ) Billable Hours';
			}
		}

		$byClient = array();
		$mergedProjects = $this->_merge_all_clients_report_production_projects($allClientResult, $eLogicClientsIds);
		foreach ($mergedProjects as $reportResult) {
			$cname = $reportResult->client_name;
			if (!isset($byClient[$cname])) {
				$byClient[$cname] = array('pm' => $reportResult->project_manager_name, 'projects' => array());
			}
			$byClient[$cname]['projects'][] = $reportResult;
		}
		if (!empty($byClient)) {
			ksort($byClient, SORT_NATURAL | SORT_FLAG_CASE);
			foreach ($byClient as $cname => $clientData) {
				usort($byClient[$cname]['projects'], function ($a, $b) {
					return strcasecmp((string) $a->project_name, (string) $b->project_name);
				});
			}
		}

		return array(
			'summaryRows' => $summaryRows,
			'summaryTitle' => $summaryTitle,
			'byClient' => $byClient,
		);
	}

	private function _generate_all_clients_report_excel_file($summaryTitle, $summaryRows, $byClient)
	{
		$this->load->library('excel');
		$objPHPExcel = $this->excel;
		$objPHPExcel->setActiveSheetIndex(0);
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle('Client TSR');
		$fmtNum = array($this, '_format_all_clients_report_number');

		$titleStyle = array(
			'font' => array('bold' => true, 'size' => 16, 'color' => array('rgb' => 'FFFFFF')),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '004B88'))
		);
		$summaryHeaderStyle = array(
			'font' => array('bold' => true, 'size' => 12, 'color' => array('rgb' => 'FFFFFF')),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '2E7D32')),
			'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000')))
		);
		$detailHeaderStyle = array(
			'font' => array('bold' => true, 'size' => 12, 'color' => array('rgb' => 'FFFFFF')),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '1565C0')),
			'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000')))
		);
		$dataRowStyle = array(
			'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'CCCCCC'))),
			'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
		);
		$alternateRowStyle = array(
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E3F2FD')),
			'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'CCCCCC'))),
			'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
		);
		$clientHeaderRowStyle = array(
			'font' => array('bold' => true, 'size' => 11),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E8F4FC')),
			'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'CCCCCC'))),
			'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
		);
		$clientNameCellStyle = array(
			'font' => array('bold' => true, 'size' => 11, 'color' => array('rgb' => '4C0BCE')),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E8F4FC')),
			'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'CCCCCC'))),
			'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
		);
		$positiveStyle = array('font' => array('color' => array('rgb' => '2E7D32')));
		$negativeStyle = array('font' => array('color' => array('rgb' => 'C62828')));

		$sheet->getColumnDimension('A')->setWidth(25);
		$sheet->getColumnDimension('B')->setWidth(40);
		$sheet->getColumnDimension('C')->setWidth(16);
		$sheet->getColumnDimension('D')->setWidth(18);
		$sheet->getColumnDimension('E')->setWidth(22);
		$sheet->getColumnDimension('F')->setWidth(15);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(15);
		$sheet->getColumnDimension('I')->setWidth(18);
		$sheet->getColumnDimension('J')->setWidth(50);

		$row = 1;
		$sheet->setCellValue('A' . $row, $summaryTitle);
		$sheet->mergeCells('A' . $row . ':J' . $row);
		$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($titleStyle);
		$sheet->getRowDimension($row)->setRowHeight(35);
		$row += 2;

		$sheet->setCellValue('A' . $row, 'Department');
		$sheet->setCellValue('B' . $row, 'Total Hours');
		$sheet->setCellValue('C' . $row, 'Invoice Hours');
		$sheet->setCellValue('D' . $row, 'Difference Hours');
		$sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($summaryHeaderStyle);
		$row++;

		$summaryRowCount = 0;
		foreach ($summaryRows as $deptName => $totals) {
			$diff = $totals['invoice_hours'] - $totals['total_hours'];
			$diffSign = $diff >= 0 ? '+' : '-';
			$sheet->setCellValue('A' . $row, $deptName);
			$sheet->setCellValue('B' . $row, call_user_func($fmtNum, $totals['total_hours']));
			$sheet->setCellValue('C' . $row, call_user_func($fmtNum, $totals['invoice_hours']));
			$sheet->setCellValue('D' . $row, $diffSign . call_user_func($fmtNum, abs($diff)));
			$sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($summaryRowCount % 2 === 0 ? $dataRowStyle : $alternateRowStyle);
			$sheet->getStyle('D' . $row)->applyFromArray($diff >= 0 ? $positiveStyle : $negativeStyle);
			$sheet->getStyle('B' . $row . ':D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$row++;
			$summaryRowCount++;
		}

		$row += 2;
		$detailHeaderRow = $row;
		$sheet->setCellValue('A' . $row, 'Project Manager');
		$sheet->setCellValue('B' . $row, 'Client Name');
		$sheet->setCellValue('C' . $row, 'Billing Type');
		$sheet->setCellValue('D' . $row, 'Production Hours');
		$sheet->setCellValue('E' . $row, 'Project General Hours');
		$sheet->setCellValue('F' . $row, 'Total Hours');
		$sheet->setCellValue('G' . $row, 'Invoice');
		$sheet->setCellValue('H' . $row, 'Difference');
		$sheet->setCellValue('I' . $row, 'No.of Employees');
		$sheet->setCellValue('J' . $row, 'Employee Name');
		$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($detailHeaderStyle);
		$row++;

		$detailRowCount = 0;
		foreach ($byClient as $clientName => $clientData) {
			$clientProductionHours = 0;
			$clientGeneralHours = 0;
			$clientTotalHours = 0;
			$clientTotalInvoiceHours = 0;
			$billingTypes = array();
			foreach ($clientData['projects'] as $pr) {
				$clientProductionHours += isset($pr->production_hours) ? (float) $pr->production_hours : (float) $pr->total_hours;
				$clientGeneralHours += isset($pr->general_hours) ? (float) $pr->general_hours : 0;
				$clientTotalHours += (float) $pr->total_hours;
				$clientTotalInvoiceHours += !empty($pr->project_invoice_amt) ? (float) $pr->project_invoice_amt : 0;
				if (!empty($pr->man_days)) {
					$bt = ucfirst(strtolower(trim($pr->man_days)));
					if ($bt && !in_array($bt, $billingTypes)) {
						$billingTypes[] = $bt;
					}
				}
			}
			$clientBillingLabel = !empty($billingTypes) ? implode(', ', $billingTypes) : '';
			$clientDiff = $clientTotalInvoiceHours - $clientTotalHours;
			$clientDiffSign = $clientDiff >= 0 ? '+' : '-';

			$sheet->setCellValue('A' . $row, !empty($clientData['pm']) ? $clientData['pm'] : '');
			$sheet->setCellValue('B' . $row, $clientName);
			$sheet->setCellValue('C' . $row, $clientBillingLabel);
			$sheet->setCellValue('D' . $row, call_user_func($fmtNum, $clientProductionHours));
			$sheet->setCellValue('E' . $row, call_user_func($fmtNum, $clientGeneralHours));
			$sheet->setCellValue('F' . $row, call_user_func($fmtNum, $clientTotalHours));
			$sheet->setCellValue('G' . $row, call_user_func($fmtNum, $clientTotalInvoiceHours));
			$sheet->setCellValue('H' . $row, $clientDiffSign . call_user_func($fmtNum, abs($clientDiff)));
			$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($clientHeaderRowStyle);
			$sheet->getStyle('B' . $row)->applyFromArray($clientNameCellStyle);
			$sheet->getStyle('H' . $row)->applyFromArray($clientDiff >= 0 ? $positiveStyle : $negativeStyle);
			$sheet->getStyle('C' . $row . ':I' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$row++;

			foreach ($clientData['projects'] as $r) {
				$projProductionHours = isset($r->production_hours) ? (float) $r->production_hours : (float) $r->total_hours;
				$projGeneralHours = isset($r->general_hours) ? (float) $r->general_hours : 0;
				$projHours = (float) $r->total_hours;
				$projInvoice = !empty($r->project_invoice_amt) ? (float) $r->project_invoice_amt : 0;
				$projDiff = $projInvoice - $projHours;
				$projDiffSign = $projDiff >= 0 ? '+' : '-';
				$sheet->setCellValue('A' . $row, !empty($r->project_manager_name) ? $r->project_manager_name : '');
				$sheet->setCellValue('B' . $row, !empty($r->project_name) ? $r->project_name : '');
				$sheet->setCellValue('C' . $row, !empty($r->man_days) ? ucfirst(strtolower(trim($r->man_days))) : '');
				$sheet->setCellValue('D' . $row, call_user_func($fmtNum, $projProductionHours));
				$sheet->setCellValue('E' . $row, call_user_func($fmtNum, $projGeneralHours));
				$sheet->setCellValue('F' . $row, call_user_func($fmtNum, $r->total_hours));
				$sheet->setCellValue('G' . $row, call_user_func($fmtNum, $projInvoice));
				$sheet->setCellValue('H' . $row, $projDiffSign . call_user_func($fmtNum, abs($projDiff)));
				$sheet->setCellValue('I' . $row, isset($r->num_employees) ? $r->num_employees : '');
				$sheet->setCellValue('J' . $row, isset($r->employee_names) ? str_replace(',', ', ', $r->employee_names) : '');
				$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($detailRowCount % 2 === 0 ? $dataRowStyle : $alternateRowStyle);
				$sheet->getStyle('H' . $row)->applyFromArray($projDiff >= 0 ? $positiveStyle : $negativeStyle);
				$sheet->getStyle('C' . $row . ':I' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$row++;
				$detailRowCount++;
			}
		}

		$sheet->freezePane('A' . ($detailHeaderRow + 1));
		$filename = 'client_timesheet_report_' . date('Y-m-d_His') . '.xlsx';
		$tmpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($tmpPath);
		return array('path' => $tmpPath, 'filename' => $filename);
	}

	public function all_clients_reports(){
		$data['getClientNames']   = $this->client_model->getClientName();
		$data['getDepartments']   = $this->client_model->getDistinctDepartments();
		$data['getListOfManagers'] = $this->timesheet_login->getActiveManagers();

		$data['show_invoice_buttons'] = false;
		$username = isset($this->session->userdata['logged_in_timesheet']['username']) ? $this->session->userdata['logged_in_timesheet']['username'] : '';
		if ($username) {
			$userDetails = $this->timesheet_login->user_information($username);
			$user_dept = (!empty($userDetails[0]->department)) ? trim($userDetails[0]->department) : '';
			$data['show_invoice_buttons'] = (strtolower($user_dept) === 'accounting');
		}

		$isSearch = (strtoupper($this->input->server('REQUEST_METHOD')) === 'POST');
		$filters = $this->_parse_all_clients_report_filters($isSearch);
		$dates = $filters['dates'];
		$form_date = $dates['form_date'];
		$to_date = $dates['to_date'];
		$clientIds = $filters['clientIds'];
		$departments = $filters['departments'];
		$projectManagers = $filters['projectManagers'];
		$projectId = $filters['projectId'];

		$data['allClientResult'] = $this->client_model->allClientsReports($filters['params']);
		$data['getListOfProjects'] = $this->client_model->getAllClientsReportProjects($clientIds, $departments, $projectManagers);
		$display = $this->_build_all_clients_report_display($data['allClientResult'], $departments, $form_date, $to_date);
		$data['summaryRows'] = $display['summaryRows'];
		$data['summaryTitle'] = $display['summaryTitle'];
		$data['byClient'] = $display['byClient'];
		$data['form_date'] = $form_date;
		$data['to_date']   = $to_date;
		$data['from_year'] = $dates['from_year'];
		$data['from_month'] = $dates['from_month'];
		$data['to_year'] = $dates['to_year'];
		$data['to_month'] = $dates['to_month'];
		$data['is_search'] = $isSearch;
		$data['ui_from_year'] = ($isSearch && $this->input->post('from_year') !== null && $this->input->post('from_year') !== '')
			? trim((string) $this->input->post('from_year')) : (string) $dates['from_year'];
		$data['ui_to_year'] = ($isSearch && $this->input->post('to_year') !== null && $this->input->post('to_year') !== '')
			? trim((string) $this->input->post('to_year')) : (string) $dates['to_year'];
		$data['ui_from_month'] = ($isSearch && $this->input->post('from_month') !== null && $this->input->post('from_month') !== '')
			? (int) $this->input->post('from_month') : (int) $dates['from_month'];
		$data['ui_to_month'] = ($isSearch && $this->input->post('to_month') !== null && $this->input->post('to_month') !== '')
			? (int) $this->input->post('to_month') : (int) $dates['to_month'];
		$data['filter_client_Id'] = ($isSearch && is_array($clientIds)) ? $clientIds : ( ($clientIds === 'all') ? array('all') : array() );
		$data['filter_department'] = ($isSearch && is_array($departments)) ? $departments : ( ($departments === 'all') ? array('all') : array() );
		$data['filter_project_manager'] = ($isSearch && is_array($projectManagers)) ? $projectManagers : ( ($projectManagers === 'all') ? array('all') : array() );
		$data['filter_project_Id'] = ($isSearch && $projectId !== 'all') ? $projectId : 'all';
		$this->load->view('clients/all_clients_reports', $data);
	}

	/**
	 * AJAX: projects for All Clients Reports filter (scoped by department / client / PM).
	 */
	public function get_all_clients_report_projects()
	{
		$clientIds = $this->input->get_post('client_Id');
		$departments = $this->input->get_post('department');
		$projectManagers = $this->input->get_post('project_manager');
		if (!is_array($clientIds)) { $clientIds = ($clientIds === 'all' || $clientIds === '' || $clientIds === null) ? 'all' : array($clientIds); }
		elseif (empty($clientIds)) { $clientIds = 'all'; }
		elseif (in_array('all', $clientIds)) { $clientIds = 'all'; }
		if (!is_array($departments)) { $departments = ($departments === 'all' || $departments === '' || $departments === null) ? 'all' : array($departments); }
		elseif (empty($departments)) { $departments = 'all'; }
		elseif (in_array('all', $departments)) { $departments = 'all'; }
		if (!is_array($projectManagers)) { $projectManagers = ($projectManagers === 'all' || $projectManagers === '' || $projectManagers === null) ? 'all' : array($projectManagers); }
		elseif (empty($projectManagers)) { $projectManagers = 'all'; }
		elseif (in_array('all', $projectManagers)) { $projectManagers = 'all'; }

		$projects = $this->client_model->getAllClientsReportProjects($clientIds, $departments, $projectManagers);
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(array('projects' => $projects)));
	}

	/**
	 * Generate Client TSR report as Excel and email to accounts team.
	 */
	public function send_client_report_email() {
		header('Content-Type: application/json');
		$filters = $this->_parse_all_clients_report_filters(true);
		$dates = $filters['dates'];
		$form_date = $dates['form_date'];
		$to_date = $dates['to_date'];
		$departments = $filters['departments'];

		$allClientResult = $this->client_model->allClientsReports($filters['params']);
		$display = $this->_build_all_clients_report_display($allClientResult, $departments, $form_date, $to_date);
		$summaryRows = $display['summaryRows'];
		$summaryTitle = $display['summaryTitle'];
		$byClient = $display['byClient'];
		$fmtNum = array($this, '_format_all_clients_report_number');

		$fileInfo = $this->_generate_all_clients_report_excel_file($summaryTitle, $summaryRows, $byClient);
		$tmpPath = $fileInfo['path'];

		$fromTs = strtotime($form_date);
		$toTs = strtotime($to_date);
		$monthYearLabel = date('F Y', $fromTs);
		if ($fromTs && $toTs && date('Y-m', $fromTs) !== date('Y-m', $toTs)) {
			$monthYearLabel .= ' - ' . date('F Y', $toTs);
		}
		$emailSubject = $monthYearLabel . ' Actual vs Billable Hours Summary';

		$summaryTableHtml = '<table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%; max-width: 600px; font-family: Arial, sans-serif; font-size: 14px;">';
		$summaryTableHtml .= '<thead><tr style="background-color: #1a5276; color: #fff; font-weight: bold;">';
		$summaryTableHtml .= '<th style="text-align: left; padding: 10px;">Department</th>';
		$summaryTableHtml .= '<th style="text-align: center; padding: 10px;">Total Hours</th>';
		$summaryTableHtml .= '<th style="text-align: center; padding: 10px;">Invoice Hours</th>';
		$summaryTableHtml .= '<th style="text-align: center; padding: 10px;">Difference Hours</th></tr></thead><tbody>';
		$rowCount = 0;
		foreach ($summaryRows as $deptName => $totals) {
			$diff = $totals['invoice_hours'] - $totals['total_hours'];
			$diffSign = $diff >= 0 ? '+' : '';
			$diffColor = $diff >= 0 ? '#2E7D32' : '#C62828';
			$rowBg = ($rowCount % 2 === 0) ? '#f5f5f5' : '#fff';
			$summaryTableHtml .= '<tr style="background-color: ' . $rowBg . ';">';
			$summaryTableHtml .= '<td style="padding: 10px; background-color: #e8eef5; font-weight: 600;">' . htmlspecialchars($deptName) . '</td>';
			$summaryTableHtml .= '<td style="padding: 10px; text-align: center;">' . call_user_func($fmtNum, $totals['total_hours']) . '</td>';
			$summaryTableHtml .= '<td style="padding: 10px; text-align: center;">' . call_user_func($fmtNum, $totals['invoice_hours']) . '</td>';
			$summaryTableHtml .= '<td style="padding: 10px; text-align: center; color: ' . $diffColor . '; font-weight: 600;">' . $diffSign . call_user_func($fmtNum, $diff) . '</td>';
			$summaryTableHtml .= '</tr>';
			$rowCount++;
		}
		$summaryTableHtml .= '</tbody></table>';

		$th = 'padding: 8px; text-align: center; border: 1px solid #1a5276;';
		$td = 'padding: 8px; border: 1px solid #ccc; vertical-align: middle;';
		$detailTableHtml = '<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 13px; margin-top: 16px;">';
		$detailTableHtml .= '<thead><tr style="background-color: #1a5276; color: #fff; font-weight: bold;">';
		$detailTableHtml .= '<th style="' . $th . '">Project Manager</th>';
		$detailTableHtml .= '<th style="' . $th . '">Client Name</th>';
		$detailTableHtml .= '<th style="' . $th . '">Billing Type</th>';
		$detailTableHtml .= '<th style="' . $th . '">Production Hours</th>';
		$detailTableHtml .= '<th style="' . $th . '">Project General Hours</th>';
		$detailTableHtml .= '<th style="' . $th . '">Total Hours</th>';
		$detailTableHtml .= '<th style="' . $th . '">Invoice</th>';
		$detailTableHtml .= '<th style="' . $th . '">Difference</th>';
		$detailTableHtml .= '<th style="' . $th . '">No.of Employees</th>';
		$detailTableHtml .= '<th style="' . $th . '">Employee Name</th>';
		$detailTableHtml .= '</tr></thead><tbody>';
		foreach ($byClient as $clientName => $clientData) {
			$clientProductionHours = 0;
			$clientGeneralHours = 0;
			$clientTotalHours = 0;
			$clientTotalInvoiceHours = 0;
			$billingTypes = array();
			foreach ($clientData['projects'] as $pr) {
				$clientProductionHours += isset($pr->production_hours) ? (float) $pr->production_hours : (float) $pr->total_hours;
				$clientGeneralHours += isset($pr->general_hours) ? (float) $pr->general_hours : 0;
				$clientTotalHours += (float) $pr->total_hours;
				$clientTotalInvoiceHours += !empty($pr->project_invoice_amt) ? (float) $pr->project_invoice_amt : 0;
				if (!empty($pr->man_days)) {
					$bt = ucfirst(strtolower(trim($pr->man_days)));
					if ($bt && !in_array($bt, $billingTypes)) {
						$billingTypes[] = $bt;
					}
				}
			}
			$clientBillingLabel = !empty($billingTypes) ? implode(', ', $billingTypes) : '';
			$clientDiff = $clientTotalInvoiceHours - $clientTotalHours;
			$clientDiffSign = $clientDiff >= 0 ? '+' : '-';
			$clientDiffColor = $clientDiff >= 0 ? '#2E7D32' : '#C62828';
			$headerTd = $td . ' background-color: #e8f4fc; font-weight: bold;';
			$detailTableHtml .= '<tr>';
			$detailTableHtml .= '<td style="' . $headerTd . ' text-align:center;">' . htmlspecialchars(!empty($clientData['pm']) ? $clientData['pm'] : '') . '</td>';
			$detailTableHtml .= '<td style="' . $headerTd . ' color:#4c0bce;">' . htmlspecialchars($clientName) . '</td>';
			$detailTableHtml .= '<td style="' . $headerTd . ' text-align:center;">' . htmlspecialchars($clientBillingLabel) . '</td>';
			$detailTableHtml .= '<td style="' . $headerTd . ' text-align:center;">' . call_user_func($fmtNum, $clientProductionHours) . '</td>';
			$detailTableHtml .= '<td style="' . $headerTd . ' text-align:center;">' . call_user_func($fmtNum, $clientGeneralHours) . '</td>';
			$detailTableHtml .= '<td style="' . $headerTd . ' text-align:center;">' . call_user_func($fmtNum, $clientTotalHours) . '</td>';
			$detailTableHtml .= '<td style="' . $headerTd . ' text-align:center;">' . call_user_func($fmtNum, $clientTotalInvoiceHours) . '</td>';
			$detailTableHtml .= '<td style="' . $headerTd . ' text-align:center; color:' . $clientDiffColor . ';">' . $clientDiffSign . call_user_func($fmtNum, abs($clientDiff)) . '</td>';
			$detailTableHtml .= '<td style="' . $headerTd . '"></td>';
			$detailTableHtml .= '<td style="' . $headerTd . '"></td>';
			$detailTableHtml .= '</tr>';
			foreach ($clientData['projects'] as $r) {
				$projProductionHours = isset($r->production_hours) ? (float) $r->production_hours : (float) $r->total_hours;
				$projGeneralHours = isset($r->general_hours) ? (float) $r->general_hours : 0;
				$projHours = (float) $r->total_hours;
				$projInvoice = !empty($r->project_invoice_amt) ? (float) $r->project_invoice_amt : 0;
				$projDiff = $projInvoice - $projHours;
				$projDiffSign = $projDiff >= 0 ? '+' : '-';
				$projDiffColor = $projDiff >= 0 ? '#2E7D32' : '#C62828';
				$detailTableHtml .= '<tr>';
				$detailTableHtml .= '<td style="' . $td . ' text-align:center;">' . htmlspecialchars(!empty($r->project_manager_name) ? $r->project_manager_name : '') . '</td>';
				$detailTableHtml .= '<td style="' . $td . ' padding-left:20px;">' . htmlspecialchars(!empty($r->project_name) ? $r->project_name : '') . '</td>';
				$detailTableHtml .= '<td style="' . $td . ' text-align:center;">' . htmlspecialchars(!empty($r->man_days) ? ucfirst(strtolower(trim($r->man_days))) : '') . '</td>';
				$detailTableHtml .= '<td style="' . $td . ' text-align:center;">' . call_user_func($fmtNum, $projProductionHours) . '</td>';
				$detailTableHtml .= '<td style="' . $td . ' text-align:center;">' . call_user_func($fmtNum, $projGeneralHours) . '</td>';
				$detailTableHtml .= '<td style="' . $td . ' text-align:center;">' . call_user_func($fmtNum, $projHours) . '</td>';
				$detailTableHtml .= '<td style="' . $td . ' text-align:center;">' . call_user_func($fmtNum, $projInvoice) . '</td>';
				$detailTableHtml .= '<td style="' . $td . ' text-align:center; color:' . $projDiffColor . '; font-weight:600;">' . $projDiffSign . call_user_func($fmtNum, abs($projDiff)) . '</td>';
				$detailTableHtml .= '<td style="' . $td . ' text-align:center;">' . (isset($r->num_employees) ? htmlspecialchars((string) $r->num_employees) : '') . '</td>';
				$detailTableHtml .= '<td style="' . $td . ' text-align:center;">' . htmlspecialchars(isset($r->employee_names) ? str_replace(',', ', ', $r->employee_names) : '') . '</td>';
				$detailTableHtml .= '</tr>';
			}
		}
		$detailTableHtml .= '</tbody></table>';

		$viewReportUrl = base_url('clients/all_clients_reports');
		$viewReportButton = '<br><a href="' . $viewReportUrl . '" style="display: inline-block; background-color: #f5d042; color: #1a5276; font-weight: bold; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-family: Arial, sans-serif; font-size: 14px;">View Report</a><br>';

		$emailBody = 'Dear All,<br><br>Please find below the summary of <b style="background-color: #f4d03f; padding: 5px 10px; border-radius: 5px;">' . $monthYearLabel . ' Actual vs Billable Hours</b> across departments.<br><br>' . $summaryTableHtml . '<br>' . $detailTableHtml . '<br>Kindly review the variance and share any clarifications if required.<br>' . $viewReportButton . '<br>Best Regards<br>eLogic Operations';

		$this->load->library('email');
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$this->email->initialize($config);
		$this->email->from('info@elogictech.com', 'eLogic Timesheet');
		$this->email->to('laxmikanth@elogictech.com');
		//$this->email->to('accounts@elogictech.com,afsar@elogictech.com,farhan@elogictech.com,jaishree@elogictech.com,pradip@elogictech.com,rajanikanth@elogictech.com,rupali@elogictech.com,sandeep@elogictech.com,shirley@elogictech.com,sivakrishna@elogictech.com,srinivasg@elogictech.com');
		$this->email->subject($emailSubject);
		$this->email->message($emailBody);
		$this->email->attach($tmpPath);
		$sent = @$this->email->send();
		@unlink($tmpPath);

		if ($sent) {
			echo json_encode(array('success' => true, 'message' => 'Report sent successfully to accounts team.'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Failed to send email. Please try again.'));
		}
	}

	/**
	 * Download Client TSR Excel using the same filtered data as the on-screen grid.
	 */
	public function download_all_clients_report_excel()
	{
		$filters = $this->_parse_all_clients_report_filters(true);
		$dates = $filters['dates'];
		$allClientResult = $this->client_model->allClientsReports($filters['params']);
		$display = $this->_build_all_clients_report_display($allClientResult, $filters['departments'], $dates['form_date'], $dates['to_date']);
		$fileInfo = $this->_generate_all_clients_report_excel_file($display['summaryTitle'], $display['summaryRows'], $display['byClient']);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $fileInfo['filename'] . '"');
		header('Content-Length: ' . filesize($fileInfo['path']));
		header('Cache-Control: max-age=0');
		readfile($fileInfo['path']);
		@unlink($fileInfo['path']);
		exit;
	}

	
	
/************************************client report Excel & PDF format*********************************/

/*********akhila code added active/inactive/project manager btn*********/


public function client_list_information()
{
    // GET OR POST support
    $status = $this->input->post('status');
    if (empty($status)) {
        $status = $this->input->get('status');
    }
    $department    = $this->input->post('department');
    $manager         = $this->input->post('manager_name');
    $form_date       = trim((string)$this->input->post('form_date'));
    $to_date         = trim((string)$this->input->post('to_date'));
    $client_search   = trim((string)$this->input->post('client_name'));

    // Created-at range from year + month dropdowns (optional)
    $from_year  = $this->input->post('from_year');
    $from_month = $this->input->post('from_month');
    $to_year    = $this->input->post('to_year');
    $to_month   = $this->input->post('to_month');

    if ($from_year !== null && $from_year !== '' && $from_month !== null && $from_month !== ''
        && $to_year !== null && $to_year !== '' && $to_month !== null && $to_month !== '') {
        $fy = (int)$from_year;
        $fm = (int)$from_month;
        $ty = (int)$to_year;
        $tm = (int)$to_month;
        if ($fy > 0 && $fm >= 1 && $fm <= 12 && $ty > 0 && $tm >= 1 && $tm <= 12) {
            $form_date = sprintf('%04d-%02d-01', $fy, $fm);
            $to_date   = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $ty, $tm)));
        }
    }

    if (empty($status)) {
        $status = 'active';
    }

    $data['selected_status'] = $status;
    $data['from_year']      = $from_year;
    $data['from_month']     = $from_month;
    $data['to_year']        = $to_year;
    $data['to_month']       = $to_month;

    $department = !empty($department) ? $department : array();
    $manager    = !empty($manager) ? $manager : array();
    if (!is_array($manager)) {
        $manager = ($manager !== null && $manager !== '') ? array($manager) : array();
    }
    $manager = array_values(array_filter(array_map('intval', $manager), function ($v) {
        return $v > 0;
    }));



    // ✅ STATUS FILTER
    if($status == 'active'){
        $statusFilter = 'active';
    }
    elseif($status == 'inactive'){
        $statusFilter = 'inactive';
    }
    else{
        $statusFilter = '';
    }

    // ✅ CALL MODEL WITH ALL FILTERS
	$data['getClients'] = $this->client_model->getClientsReport(
		$statusFilter,
		$department,
		$form_date,
		$to_date,
		$manager,
		$client_search
	);
	

    // ✅ SEND DATA TO VIEW (KEEP OLD VALUES ALSO)
    $data['form_date']     = $form_date;
    $data['to_date']       = $to_date;
    $data['department']    = $department;
    $data['manager_name']  = $manager;
    $data['client_name']   = $client_search;

    // Client Summary (Total / Active / Inactive)
    $data['clientCounts'] = $this->client_model->getClientCounts();

    $this->load->view('clients/client_reports_automation_search', $data);
}



public function searchClientsAjax()
{
    $client_name = $this->input->post('client_name');

    $data = $this->client_model->searchClients($client_name);

    $output = '';
    $i = 1;

    foreach($data as $row){

        $output .= '<tr>
            <td>'.$i.'</td>
            <td>'.ucwords($row->client_name).'</td>
            <td>'.$row->client_email.'</td>
            <td>'.$row->client_contact_num.'</td>
            <td>'.$row->department.'</td>
            <td>'.$row->client_country.'</td>
            <td>'.$row->client_state.'</td>
            <td>'.$row->client_city.'</td>
            <td>'.$row->status.'</td>
        </tr>';

        $i++;
    }

    echo $output;
}


/*************akhila code client search input *************/

public function liveClientSearch()
{
    $search = $this->input->post('search');
    $type   = $this->input->post('type');

    $clients = $this->client_model->liveClientSearch($search);

    if ($type === 'suggestion') {
        $output = '';
        if (!empty($clients)) {
            foreach ($clients as $row) {
                $name = htmlspecialchars($row->client_name, ENT_QUOTES, 'UTF-8');
                $output .= '<div class="client_suggestion client-item" data-name="' . $name . '">' . $name . '</div>';
            }
        }
        echo $output;
        return;
    }

    $output = '';
    $i = 1;

    if (!empty($clients)) {
        foreach ($clients as $row) {

            $statusClass = ($row->status == 'Active')
                ? 'label label-success'
                : 'label label-danger';

            $desc = !empty($row->client_desc) ? htmlspecialchars($row->client_desc, ENT_QUOTES, 'UTF-8') : '-';

            $output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.htmlspecialchars(ucwords($row->client_name), ENT_QUOTES, 'UTF-8').'</td>
                <td>'.htmlspecialchars($row->client_email, ENT_QUOTES, 'UTF-8').'</td>
                <td><span class="label label-info">'.htmlspecialchars(ucfirst($row->name), ENT_QUOTES, 'UTF-8').'</span></td>

                <td>
                    <a href="#" data-toggle="tooltip" title="'.$desc.'">
                        '.character_limiter($row->client_desc, 20).'
                    </a>
                </td>

                <td>'.htmlspecialchars($row->client_contact_num, ENT_QUOTES, 'UTF-8').'</td>
                <td>'.htmlspecialchars($row->department, ENT_QUOTES, 'UTF-8').'</td>
                <td>'.htmlspecialchars($row->client_country, ENT_QUOTES, 'UTF-8').'</td>
                <td>'.htmlspecialchars($row->client_state, ENT_QUOTES, 'UTF-8').'</td>
                <td>'.htmlspecialchars($row->client_city, ENT_QUOTES, 'UTF-8').'</td>

                <td>
                    <span class="'.$statusClass.'">'.htmlspecialchars($row->status, ENT_QUOTES, 'UTF-8').'</span>
                </td>

                <td>'.(!empty($row->created_at) ? date('d-M-Y', strtotime($row->created_at)) : '-').'</td>
            </tr>';

            $i++;
        }
    } else {
        $output = '<tr><td colspan="12" style="text-align:center;">No Data Found</td></tr>';
    }

    echo $output;
}
/*******************************************end***********************************************/
    
  /**************************************************************************************************************************************/
    

/********************************************************************** Comparing resource schedule data vs Timesheet report log entires data ***************************************************************************************/

public function rs_vs_ts(){ // Resource Billability feature
		
	// Get parameters from POST or GET
	$clientId = $this->input->post('client_Id') ? $this->input->post('client_Id') : $this->input->get('client_Id');
	$projectId = $this->input->post('project_Id') ? $this->input->post('project_Id') : $this->input->get('project_Id');
	$formDate = $this->input->post('form_date') ? $this->input->post('form_date') : $this->input->get('form_date');
	$toDate = $this->input->post('to_date') ? $this->input->post('to_date') : $this->input->get('to_date');
	
	// Default: previous day; if Saturday or Sunday use previous Friday
	$defaultDate = date('Y-m-d', strtotime('-1 day'));
	$dayOfWeek = (int) date('w', strtotime($defaultDate)); // 0=Sunday, 6=Saturday
	if ($dayOfWeek == 0) {
		$defaultDate = date('Y-m-d', strtotime('-3 day')); // Sunday -> Friday
	} elseif ($dayOfWeek == 6) {
		$defaultDate = date('Y-m-d', strtotime('-2 day')); // Saturday -> Friday
	}
	if (empty($formDate)) {
		$formDate = $defaultDate;
	}
	if (empty($toDate)) {
		$toDate = $defaultDate;
	}
	
	// Always load data with default dates if no POST request (initial page load)
	// If no client selected, use 'all' for initial load
	if(empty($clientId) && empty($this->input->post('client_Id'))) {
		$clientId = 'all';
	}
	
	// If no client selected but we have dates, load 'all' clients
	if(empty($clientId)) {
		$clientId = 'all';
	}
	
	$params = array(
		'client_Id' => $clientId,
		'project_Id' => !empty($projectId) ? $projectId : 'all',
		'form_date' => $formDate,
		'to_date' => $toDate,            
	);

	$data['allClientResult'] = $this->client_model->compareResouceTimesheet($params);
	$data['managerDefaulters'] = $this->client_model->getRsVsTsDefaultersByManager($formDate, $toDate, $clientId);
	$data['form_date'] = $formDate;
	$data['to_date'] = $toDate;
	$data['client_Id'] = $clientId;

	$this->load->helper('rs_vs_ts');
	$this->load->view('clients/resouce_vs_timehseet',$data);
	
	
	
	/* if(!empty($this->input->post('client_Id'))) :
		
		 $params = array(
            'client_Id' => $this->input->post('client_Id'),
            'project_Id' => $this->input->post('project_Id'),
            'form_date' => $this->input->post('form_date'),
            'to_date' => $this->input->post('to_date'),            
            );
		
         $data['resultClientReport'] = $this->client_model->compareResouceTimesheet($params);     
        
		 $this->load->view('clients/resouce_vs_timehseet' , $data);
		 
	   else : 
	   
	       $this->load->view('clients/resouce_vs_timehseet');
	   
	   endif; 
	   */
		
	}

	/**
	 * Send RS vs TS report (Live Project Allocation & Actual hours) by email to laxmikanth@elogictech.com
	 */
	public function send_rs_vs_ts_report_email() {
		header('Content-Type: application/json');
		$formDate = $this->input->post('form_date');
		$toDate   = $this->input->post('to_date');
		$this->_set_rs_vs_ts_default_dates($formDate, $toDate);
		$result = $this->_do_send_rs_vs_ts_report($formDate, $toDate);
		echo json_encode($result);
	}

	/**
	 * Cron endpoint: run daily at 2:30 PM IST to auto-send RS vs TS report to laxmikanth@elogictech.com
	 * Call via: GET your-site/index.php/clients/send_rs_vs_ts_report_cron?key=YOUR_CRON_KEY
	 * Configure key in application/config/config.php as $config['rs_vs_ts_cron_key']
	 */
	public function send_rs_vs_ts_report_cron() {
		$cronKey = $this->config->item('rs_vs_ts_cron_key');
		if (!empty($cronKey) && $this->input->get('key') !== $cronKey) {
			header('HTTP/1.0 403 Forbidden');
			echo 'Forbidden';
			return;
		}
		$formDate = null;
		$toDate   = null;
		$this->_set_rs_vs_ts_default_dates($formDate, $toDate);
		$result = $this->_do_send_rs_vs_ts_report($formDate, $toDate);
		header('Content-Type: text/plain; charset=utf-8');
		if ($result['success']) {
			echo 'Report sent to elogic_pms@elogictech.com at ' . date('Y-m-d H:i:s') . ' (Report dates: ' . $formDate . ' to ' . $toDate . ')';
		} else {
			echo 'Failed: ' . $result['message'];
		}
	}

	private function _set_rs_vs_ts_default_dates(&$formDate, &$toDate) {
		if (!empty($formDate) && !empty($toDate)) return;
		$defaultDate = date('Y-m-d', strtotime('-1 day'));
		$dayOfWeek = (int) date('w', strtotime($defaultDate));
		if ($dayOfWeek == 0) $defaultDate = date('Y-m-d', strtotime('-3 day'));
		elseif ($dayOfWeek == 6) $defaultDate = date('Y-m-d', strtotime('-2 day'));
		if (empty($formDate)) $formDate = $defaultDate;
		if (empty($toDate)) $toDate = $defaultDate;
	}

	/**
	 * Build and email RS vs TS report for the given date range. Returns array('success' => bool, 'message' => string).
	 */
	private function _do_send_rs_vs_ts_report($formDate, $toDate) {
		$this->load->helper('rs_vs_ts');
		$params = array(
			'client_Id'  => 'all',
			'project_Id' => 'all',
			'form_date'  => $formDate,
			'to_date'    => $toDate,
		);
		$allClientResult = $this->client_model->compareResouceTimesheet($params);
		if (empty($allClientResult)) {
			return array('success' => false, 'message' => 'No data for the selected date range.');
		}
		// Fetch team members (per reporting manager) who did NOT fully fill the timesheet
		// during the report date range. Used in the email's manager-wise summary table.
		$managerDefaultersEmail = $this->client_model->getRsVsTsDefaultersByManager($formDate, $toDate, 'all');
		$eLogicClientsIds = array('363','374','370','369','368','367','364','361','355','270','262','253','236','210','85','78','74','49','34','32');
		$excludedClientNames = array('elogic solutions ( software )', 'elogic solutions(farhan)');
		$excludedManagerNames = array('rupali modi');
		$sortedClientResult = array();
		foreach ($allClientResult as $reportResult) {
			$pmName = isset($reportResult->project_manager_name) ? trim($reportResult->project_manager_name) : '';
			$isExcluded = in_array($reportResult->client_Id, $eLogicClientsIds);
			$isNaManager = ($pmName === '' || strtoupper($pmName) === 'N/A');
			$isExcludedManager = in_array(strtolower($pmName), $excludedManagerNames, true);
			$dept = isset($reportResult->department) ? trim((string)$reportResult->department) : '';
			$projectNameForFilter = isset($reportResult->project_name) ? trim((string)$reportResult->project_name) : '';
			$clientNameNormalized = strtolower(trim(isset($reportResult->client_name) ? (string)$reportResult->client_name : ''));
			$isBlockedClientName = in_array($clientNameNormalized, $excludedClientNames, true);
			$isElogicSolutionsClient = (bool)preg_match('/^elogic solutions\\s*\\(/i', $clientNameNormalized);
			$isGeneralProject = (strtolower($dept) === 'general' || stripos($projectNameForFilter, 'general') !== false);
			if (($isExcluded && !($isElogicSolutionsClient && $isGeneralProject)) || $isNaManager || $isBlockedClientName || $isExcludedManager) {
				continue;
			}
			if ($isExcluded && $isElogicSolutionsClient && !$isGeneralProject) {
				continue;
			}
			$sortedClientResult[] = $reportResult;
		}
		$allowedManagerNames = array();
		foreach ($allClientResult as $reportResult) {
			$pmName = isset($reportResult->project_manager_name) ? trim((string)$reportResult->project_manager_name) : '';
			if ($pmName === '' || strtoupper($pmName) === 'N/A') {
				continue;
			}
			$dept = strtolower(trim(isset($reportResult->department) ? (string)$reportResult->department : ''));
			$clientNameNormalized = strtolower(trim(isset($reportResult->client_name) ? (string)$reportResult->client_name : ''));
			$isElogicSolutionsClient = (bool)preg_match('/^elogic solutions\\s*\\(/i', $clientNameNormalized);
			$projectNameForFilter = trim(isset($reportResult->project_name) ? (string)$reportResult->project_name : '');
			$isGeneralProject = ($dept === 'general' || stripos($projectNameForFilter, 'general') !== false);
			$isPradipManager = (bool)preg_match('/^pradip\\s+chauhan/i', $pmName);
			$hasAllowedDept = in_array($dept, array('architectural', '3d visualization', 'structural', 'mep'), true);
			$isAllowedElogicGeneral = ($isElogicSolutionsClient && $isGeneralProject);
			if ($hasAllowedDept || $isPradipManager || $isAllowedElogicGeneral) {
				$allowedManagerNames[strtolower($pmName)] = true;
			}
		}
		if (!empty($allowedManagerNames)) {
			$sortedClientResult = array_values(array_filter($sortedClientResult, function($reportResult) use ($allowedManagerNames, $excludedManagerNames) {
				$pmName = isset($reportResult->project_manager_name) ? trim((string)$reportResult->project_manager_name) : '';
				if (in_array(strtolower($pmName), $excludedManagerNames, true)) {
					return false;
				}
				$isAllowedManager = isset($allowedManagerNames[strtolower($pmName)]);
				if ($isAllowedManager) {
					return true;
				}
				$clientNameNormalized = strtolower(trim(isset($reportResult->client_name) ? (string)$reportResult->client_name : ''));
				$isElogicSolutionsClient = (bool)preg_match('/^elogic solutions\\s*\\(/i', $clientNameNormalized);
				$dept = strtolower(trim(isset($reportResult->department) ? (string)$reportResult->department : ''));
				$projectNameForFilter = trim(isset($reportResult->project_name) ? (string)$reportResult->project_name : '');
				$isGeneralProject = ($dept === 'general' || stripos($projectNameForFilter, 'general') !== false);
				return ($isElogicSolutionsClient && $isGeneralProject);
			}));
		}
		usort($sortedClientResult, function($a, $b) {
			$ma = !empty($a->project_manager_name) ? $a->project_manager_name : 'N/A';
			$mb = !empty($b->project_manager_name) ? $b->project_manager_name : 'N/A';
			return rs_vs_ts_compare_manager_names($ma, $mb);
		});
		$byManager = array();
		foreach ($sortedClientResult as $reportResult) {
			$managerName = !empty($reportResult->project_manager_name) ? $reportResult->project_manager_name : 'N/A';
			$clientName = trim($reportResult->client_name);
			if (!isset($byManager[$managerName])) $byManager[$managerName] = array();
			if (!isset($byManager[$managerName][$clientName])) $byManager[$managerName][$clientName] = array('projects' => array());
			$byManager[$managerName][$clientName]['projects'][] = $reportResult;
		}
		uksort($byManager, 'rs_vs_ts_compare_manager_names');
		
		// Manager-wise summary for email notification table
		$managerSummaryEmail = array();
		$emailGrandResource = 0;
		$emailGrandTimesheet = 0;
		foreach ($byManager as $managerName => $clients) {
			$resH = 0;
			$tsH = 0;
			foreach ($clients as $clientData) {
				foreach ($clientData['projects'] as $reportResult) {
					$resH += !empty($reportResult->schedule_hours) ? (float)$reportResult->schedule_hours : 0;
					$tsH += !empty($reportResult->timesheet_hours) ? (float)$reportResult->timesheet_hours : 0;
				}
			}
			$managerSummaryEmail[$managerName] = array(
				'resource_hours' => $resH,
				'timesheet_hours' => $tsH
			);
			$emailGrandResource += $resH;
			$emailGrandTimesheet += $tsH;
		}

		// Department-wise summary (same grouping as view: Architectural + Structural + 3D Viz, then MEP)
		$deptTotals = array();
		foreach ($sortedClientResult as $reportResult) {
			$dept = trim(isset($reportResult->department) ? $reportResult->department : '');
			if ($dept === '') $dept = 'N/A';
			if (!isset($deptTotals[$dept])) $deptTotals[$dept] = array('resource_hours' => 0, 'timesheet_hours' => 0);
			$deptTotals[$dept]['resource_hours']   += !empty($reportResult->schedule_hours)   ? (float)$reportResult->schedule_hours   : 0;
			$deptTotals[$dept]['timesheet_hours'] += !empty($reportResult->timesheet_hours) ? (float)$reportResult->timesheet_hours : 0;
		}
		$group1Resource = 0;
		$group1Timesheet = 0;
		foreach ($deptTotals as $dname => $totals) {
			if (strtolower(trim((string)$dname)) !== 'mep') {
				$group1Resource   += $deptTotals[$dname]['resource_hours'];
				$group1Timesheet += $deptTotals[$dname]['timesheet_hours'];
			}
		}
		$displayGroups = array(
			'Architectural, 3D Visualization & Structural' => array('resource_hours' => $group1Resource, 'timesheet_hours' => $group1Timesheet),
			'MEP' => isset($deptTotals['MEP']) ? $deptTotals['MEP'] : array('resource_hours' => 0, 'timesheet_hours' => 0)
		);
		$grandResource = 0;
		$grandTimesheet = 0;
		foreach ($displayGroups as $d) {
			$grandResource += $d['resource_hours'];
			$grandTimesheet += $d['timesheet_hours'];
		}

		$this->load->library('excel');
		$objPHPExcel = $this->excel;
		$objPHPExcel->setActiveSheetIndex(0);
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle('RS vs TS Report');
		$headerStyle = array(
			'font' => array('bold' => true, 'size' => 12, 'color' => array('rgb' => 'FFFFFF')),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '337AB7')),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
			'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '2C5AA0')))
		);
		$deptHeaderStyle = array(
			'font' => array('bold' => true, 'size' => 12, 'color' => array('rgb' => 'FFFFFF')),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '2D5A7D')),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
			'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '2D5A7D')))
		);
		$deptRowStyle = array(
			'font' => array('bold' => true, 'size' => 11),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'EEF3F7')),
			'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'E8E8E8')))
		);
		$clientRowStyle = array(
			'font' => array('bold' => true, 'size' => 11),
			'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'F8F9FA')),
			'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'E8E8E8')))
		);
		$projectRowStyle = array(
			'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'E8E8E8')))
		);
		$sheet->getColumnDimension('A')->setWidth(22);
		$sheet->getColumnDimension('B')->setWidth(35);
		$sheet->getColumnDimension('C')->setWidth(16);
		$sheet->getColumnDimension('D')->setWidth(18);
		$sheet->getColumnDimension('E')->setWidth(14);
		$row = 1;
		// Department summary at top
		$sheet->setCellValue('A'.$row, 'Department');
		$sheet->setCellValue('B'.$row, 'Resource Hours');
		$sheet->setCellValue('C'.$row, 'Timesheet Hours');
		$sheet->setCellValue('D'.$row, 'Difference Hours');
		$sheet->getStyle('A'.$row.':D'.$row)->applyFromArray($deptHeaderStyle);
		$sheet->getStyle('B'.$row.':D'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$row++;
		foreach ($displayGroups as $label => $totals) {
			$resH = $totals['resource_hours'];
			$tsH = $totals['timesheet_hours'];
			$diff = $resH - $tsH;
			$sheet->setCellValue('A'.$row, $label);
			$sheet->setCellValue('B'.$row, number_format($resH, 2));
			$sheet->setCellValue('C'.$row, number_format($tsH, 2));
			$sheet->setCellValue('D'.$row, number_format($diff, 2));
			$sheet->getStyle('A'.$row.':D'.$row)->applyFromArray($deptRowStyle);
			$sheet->getStyle('B'.$row.':D'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sheet->getStyle('D'.$row)->getFont()->getColor()->setRGB($diff != 0 ? 'D9534F' : '5CB85C');
			$row++;
		}
		$grandDiff = $grandResource - $grandTimesheet;
		$sheet->setCellValue('A'.$row, 'Grand Total');
		$sheet->setCellValue('B'.$row, number_format($grandResource, 2));
		$sheet->setCellValue('C'.$row, number_format($grandTimesheet, 2));
		$sheet->setCellValue('D'.$row, number_format($grandDiff, 2));
		$sheet->getStyle('A'.$row.':D'.$row)->applyFromArray($deptRowStyle);
		$sheet->getStyle('B'.$row.':D'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$sheet->getStyle('D'.$row)->getFont()->getColor()->setRGB($grandDiff != 0 ? 'D9534F' : '5CB85C');
		$row++;
		$row++; // blank row
		// Main grid header
		$sheet->setCellValue('A'.$row, 'Project Manager');
		$sheet->setCellValue('B'.$row, 'Client Name / Projects');
		$sheet->setCellValue('C'.$row, 'Resource Hours');
		$sheet->setCellValue('D'.$row, 'Timesheet Hours');
		$sheet->setCellValue('E'.$row, 'Difference');
		$sheet->getStyle('A'.$row.':E'.$row)->applyFromArray($headerStyle);
		$sheet->getStyle('C'.$row.':E'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$row++;
		foreach ($byManager as $projectManagerName => $clients) {
			$firstClientInManager = true;
			foreach ($clients as $clientName => $clientData) {
				// Totals for this client under this manager only (same client under different managers = separate totals)
				$clientScheduleTotal = 0;
				$clientTimesheetTotal = 0;
				foreach ($clientData['projects'] as $reportResult) {
					$clientScheduleTotal += !empty($reportResult->schedule_hours) ? (float)$reportResult->schedule_hours : 0;
					$clientTimesheetTotal += !empty($reportResult->timesheet_hours) ? (float)$reportResult->timesheet_hours : 0;
				}
				$diff = $clientScheduleTotal - $clientTimesheetTotal;
				$sheet->setCellValue('A'.$row, $firstClientInManager ? $projectManagerName : '');
				$sheet->setCellValue('B'.$row, $clientName);
				$sheet->setCellValue('C'.$row, number_format($clientScheduleTotal, 2));
				$sheet->setCellValue('D'.$row, number_format($clientTimesheetTotal, 2));
				$sheet->setCellValue('E'.$row, number_format($diff, 2));
				$sheet->getStyle('A'.$row.':E'.$row)->applyFromArray($clientRowStyle);
				$sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$diffStyle = array('font' => array('color' => array('rgb' => ($diff != 0) ? 'D9534F' : '5CB85C')));
				$sheet->getStyle('E'.$row)->applyFromArray($diffStyle);
				$sheet->getStyle('C'.$row.':E'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$row++;
				$firstClientInManager = false;
				foreach ($clientData['projects'] as $reportResult) {
					$sh = !empty($reportResult->schedule_hours) ? (float)$reportResult->schedule_hours : 0;
					$th = !empty($reportResult->timesheet_hours) ? (float)$reportResult->timesheet_hours : 0;
					$projDiff = $sh - $th;
					$projectName = !empty($reportResult->project_name) ? $reportResult->project_name : 'N/A';
					$sheet->setCellValue('A'.$row, '');
					$sheet->setCellValue('B'.$row, '  ' . $projectName);
					$sheet->setCellValue('C'.$row, number_format($sh, 2));
					$sheet->setCellValue('D'.$row, number_format($th, 2));
					$sheet->setCellValue('E'.$row, number_format($projDiff, 2));
					$sheet->getStyle('A'.$row.':E'.$row)->applyFromArray($projectRowStyle);
					$projDiffStyle = array('font' => array('color' => array('rgb' => ($projDiff != 0) ? 'D9534F' : '5CB85C')));
					$sheet->getStyle('E'.$row)->applyFromArray($projDiffStyle);
					$sheet->getStyle('C'.$row.':E'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$row++;
				}
			}
		}
		$filename = 'Rs_vs_Ts_Report_' . date('Y-m-d_His') . '.xlsx';
		$tmpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($tmpPath);

		// Prepare email
		$this->load->library('email');
		$emailConfig = array('mailtype' => 'html', 'charset' => 'utf-8');
		$this->email->initialize($emailConfig);
		$this->email->from('info@elogictech.com', 'eLogic Timesheet');
		$this->email->to('laxmikanth@elogictech.com');
		//$this->email->to('elogic_pms@elogictech.com,jaishree@elogictech.com,laxmikanth@elogictech.com');
		// Use single date label in DD MMM YYYY format for subject/body
		$reportDateLabel = !empty($formDate) ? date('d M Y', strtotime($formDate)) : date('d M Y');
		$this->email->subject('Planned vs Actual Hours Report – ' . $reportDateLabel);

		// Department summary table — layout matches billable-hours email style (navy header, alternating rows, #ccc borders)
		$cellBorder = 'border:1px solid #cccccc;';
		// Inline styles for the "Not Filled Timesheet By" chips (email-safe: no gradients/shadows).
		$chipStyle = 'display:inline-block; background:#fde68a; color:#7c2d12; border:1px solid #d97706; border-radius:12px; padding:3px 10px; margin:2px 4px 2px 0; font-size:12px; font-weight:bold; line-height:1.4; white-space:nowrap;';
		$summaryTableHtml = '<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; margin:0 0 8px 0; font-family: Arial, Helvetica, sans-serif;">';
		$summaryTableHtml .= '<thead><tr>';
		$summaryTableHtml .= '<th style="padding:16px 18px; text-align:left; font-size:14px; font-weight:bold; color:#ffffff; background:#1a5276; ' . $cellBorder . '">Manager Name</th>';
		$summaryTableHtml .= '<th style="padding:16px 18px; text-align:center; font-size:14px; font-weight:bold; color:#ffffff; background:#1a5276; ' . $cellBorder . '">Resource Hours</th>';
		$summaryTableHtml .= '<th style="padding:16px 18px; text-align:center; font-size:14px; font-weight:bold; color:#ffffff; background:#1a5276; ' . $cellBorder . '">Timesheet Hours</th>';
		$summaryTableHtml .= '<th style="padding:16px 18px; text-align:center; font-size:14px; font-weight:bold; color:#ffffff; background:#1a5276; ' . $cellBorder . '">Difference Hours</th>';
		$summaryTableHtml .= '<th style="padding:16px 18px; text-align:left; font-size:14px; font-weight:bold; color:#ffffff; background:#b45309; ' . $cellBorder . '">Not Filled Timesheet By</th>';
		$summaryTableHtml .= '</tr></thead><tbody>';
		$dataRowIndex = 0;
		$totalDefaultersEmail = 0;
		foreach ($managerSummaryEmail as $rowLabel => $totals) {
			$resH = $totals['resource_hours'];
			$tsH = $totals['timesheet_hours'];
			$diff = $tsH - $resH;
			$rowBg = ($dataRowIndex % 2 === 0) ? '#f4f7f9' : '#ffffff';
			$defaultersCellBg = ($dataRowIndex % 2 === 0) ? '#fffaf0' : '#fffbf3';
			$diffStyle = 'text-align:center; font-size:14px; font-weight:bold; ' . $cellBorder . ' padding:16px 18px; background:' . $rowBg . ';';
			$diffStyle .= ($diff < 0) ? ' color:#c0392b;' : ' color:#333333;';
			// Build defaulters cell HTML for this manager
			$mgrKeyLookup = strtolower(preg_replace('/\s+/', ' ', trim($rowLabel)));
			$defaultersForRow = isset($managerDefaultersEmail[$mgrKeyLookup]) ? $managerDefaultersEmail[$mgrKeyLookup] : array();
			$defaultersCellHtml = '';
			if (!empty($defaultersForRow)) {
				foreach ($defaultersForRow as $defRow) {
					$defName = isset($defRow->member_name) ? trim((string)$defRow->member_name) : '';
					if ($defName === '') continue;
					$defSched = isset($defRow->scheduled_hours) ? (float)$defRow->scheduled_hours : 0;
					$defTs    = isset($defRow->timesheet_hours) ? (float)$defRow->timesheet_hours : 0;
					$defPending = $defSched - $defTs;
					$tipText = 'Resource: ' . number_format($defSched, 2) . ' hrs | Timesheet: ' . number_format($defTs, 2) . ' hrs | Pending: ' . number_format($defPending, 2) . ' hrs';
					$defaultersCellHtml .= '<span style="' . $chipStyle . '" title="' . htmlspecialchars($tipText, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($defName, ENT_QUOTES, 'UTF-8') . '</span>';
					$totalDefaultersEmail++;
				}
			}
			if ($defaultersCellHtml === '') {
				$defaultersCellHtml = '<span style="color:#16a34a; font-weight:bold; font-size:13px;">&#10003; All filled</span>';
			}
			$summaryTableHtml .= '<tr>';
			$summaryTableHtml .= '<td style="padding:16px 18px; text-align:left; font-size:14px; font-weight:bold; color:#333333; background:' . $rowBg . '; ' . $cellBorder . '">' . htmlspecialchars($rowLabel, ENT_QUOTES, 'UTF-8') . '</td>';
			$summaryTableHtml .= '<td style="padding:16px 18px; text-align:center; font-size:14px; color:#333333; background:' . $rowBg . '; ' . $cellBorder . '">' . number_format($resH, 2) . '</td>';
			$summaryTableHtml .= '<td style="padding:16px 18px; text-align:center; font-size:14px; color:#333333; background:' . $rowBg . '; ' . $cellBorder . '">' . number_format($tsH, 2) . '</td>';
			$summaryTableHtml .= '<td style="' . $diffStyle . '">' . number_format($diff, 2) . '</td>';
			$summaryTableHtml .= '<td style="padding:12px 14px; text-align:left; font-size:13px; color:#333333; background:' . $defaultersCellBg . '; ' . $cellBorder . '">' . $defaultersCellHtml . '</td>';
			$summaryTableHtml .= '</tr>';
			$dataRowIndex++;
		}
		$grandDiff = $emailGrandTimesheet - $emailGrandResource;
		$totalBg = '#e8edf2';
		$totalDefaultersBg = '#ffedd5';
		$gDiffStyle = 'text-align:center; font-size:14px; font-weight:bold; ' . $cellBorder . ' padding:16px 18px; background:' . $totalBg . ';';
		$gDiffStyle .= ($grandDiff < 0) ? ' color:#c0392b;' : ' color:#333333;';
		$grandDefaultersText = $totalDefaultersEmail > 0
			? ('Total: ' . $totalDefaultersEmail . ' member' . ($totalDefaultersEmail === 1 ? '' : 's'))
			: 'All members filled';
		$summaryTableHtml .= '<tr>';
		$summaryTableHtml .= '<td style="padding:16px 18px; text-align:left; font-size:14px; font-weight:bold; color:#222222; background:' . $totalBg . '; ' . $cellBorder . '">Grand Total</td>';
		$summaryTableHtml .= '<td style="padding:16px 18px; text-align:center; font-size:14px; font-weight:bold; color:#222222; background:' . $totalBg . '; ' . $cellBorder . '">' . number_format($emailGrandResource, 2) . '</td>';
		$summaryTableHtml .= '<td style="padding:16px 18px; text-align:center; font-size:14px; font-weight:bold; color:#222222; background:' . $totalBg . '; ' . $cellBorder . '">' . number_format($emailGrandTimesheet, 2) . '</td>';
		$summaryTableHtml .= '<td style="' . $gDiffStyle . '">' . number_format($grandDiff, 2) . '</td>';
		$summaryTableHtml .= '<td style="padding:16px 18px; text-align:left; font-size:13px; font-weight:bold; font-style:italic; color:#7c2d12; background:' . $totalDefaultersBg . '; ' . $cellBorder . '">' . htmlspecialchars($grandDefaultersText, ENT_QUOTES, 'UTF-8') . '</td>';
		$summaryTableHtml .= '</tr></tbody></table>';

		$emailBody = '<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Daily Planned vs Actual Hours Report</title>
</head>
<image.pngbody style="margin:0; padding:36px 16px; background:#eceff1; font-family: Arial, Helvetica, sans-serif; line-height:1.65; color:#333333;">
	<div style="margin:0 auto; background:#ffffff; padding:36px 32px 40px 32px; border:1px solid #dde1e4; border-radius:6px; box-shadow:0 1px 4px rgba(0,0,0,0.06);">
		<p style="margin:0 0 20px 0; font-size:15px; color:#333333;">Dear Team,</p>
		<p style="margin:0 0 28px 0; font-size:15px; color:#444444;">Please find attached the Daily <b style="background-color: #f4d03f; padding: 5px 10px; border-radius: 5px;"> Planned vs Actual Hours Report for ' . $reportDateLabel . '</b>. The manager-wise summary is shown in the table below.</p>
		' . $summaryTableHtml . '
		<p style="margin:28px 0 22px 0; font-size:15px; color:#444444;">Please find the attached file for the detailed view, or click on the button below.</p>
		<a href="http://172.168.0.12:82/elogic_timesheet/clients/rs_vs_ts"
		   style="display:inline-block; padding:14px 36px; font-weight:bold; font-size:15px; color:#222222; background:#f4d03f; border-radius:6px; text-decoration:none; border:1px solid #d4b82e;"
		   target="_blank">View Report</a>
		<p style="margin:32px 0 0 0; font-size:15px; color:#333333;">Best Regards<br>eLogic Team</p>
	</div>
</body>
</html>';

		$this->email->message($emailBody);
		$this->email->attach($tmpPath);
		$sent = @$this->email->send();
		@unlink($tmpPath);
		if ($sent) {
			return array('success' => true, 'message' => 'Report sent successfully');
		}
		return array('success' => false, 'message' => 'Failed to send email. Please try again.');
	}
    
 /************************************************** Resource schedule vs timesheet ****************************************/   
 
 /**
  * Update project invoice amount via AJAX
  */
 public function update_project_invoice() {
     $this->load->library('form_validation');
     $this->load->library('email');
     
     // Only Accounting department can add/update invoice
     $username = isset($this->session->userdata['logged_in_timesheet']['username']) ? $this->session->userdata['logged_in_timesheet']['username'] : '';
     $is_accounting = false;
     if ($username) {
         $userDetails = $this->timesheet_login->user_information($username);
         $user_dept = (!empty($userDetails[0]->department)) ? trim($userDetails[0]->department) : '';
         $is_accounting = (strtolower($user_dept) === 'accounting');
     }
     if (!$is_accounting) {
         echo json_encode(array(
             'success' => false,
             'message' => 'Permission denied. Only Accounting department can add or update invoice.'
         ));
         return;
     }
     
     $this->form_validation->set_rules('project_id', 'Project ID', 'required|numeric');
     $this->form_validation->set_rules('invoice_amount', 'Invoice Amount', 'required|numeric');
     
     if ($this->form_validation->run() == FALSE) {
         echo json_encode(array(
             'success' => false,
             'message' => validation_errors()
         ));
         return;
     }
     
     $project_id = (int) $this->input->post('project_id');
     $invoice_amount = (float) $this->input->post('invoice_amount');
     $form_date = $this->input->post('form_date');
     
     // Month-wise invoice: use report date range month (from all_clients_reports). If not sent, use current month.
     if (empty($form_date)) {
         $form_date = date('Y-m-d');
     }
     $invoice_year = (int) date('Y', strtotime($form_date));
     $invoice_month = (int) date('n', strtotime($form_date));
     
    // Get project details before update (including department)
    $this->db->select('p.project_name, p.project_invoice_amt as old_invoice_amt, p.project_type, c.client_name, c.department as client_department, emp.name as project_manager_name');
    $this->db->from('project_details p');
    $this->db->join('client_details c', 'c.client_Id = p.client_Id', 'left');
    $this->db->join('employee_details emp', 'emp.empId = p.empId', 'left');
    $this->db->where('p.project_Id', $project_id);
    $project_details = $this->db->get()->row();
    
    if (!$project_details) {
        echo json_encode(array('success' => false, 'message' => 'Project not found.'));
        return;
    }
    
    // Get department: prefer project_type, fallback to client department
    $department = !empty($project_details->project_type) ? $project_details->project_type : (!empty($project_details->client_department) ? $project_details->client_department : '');
     
     // Save month-wise invoice in project_invoice_monthly (for all_clients_reports date-range view)
     $this->db->where('project_Id', $project_id);
     $this->db->where('invoice_year', $invoice_year);
     $this->db->where('invoice_month', $invoice_month);
     $existing = $this->db->get('project_invoice_monthly')->row();
     
     if ($existing) {
         $this->db->where('project_Id', $project_id);
         $this->db->where('invoice_year', $invoice_year);
         $this->db->where('invoice_month', $invoice_month);
         $update_result = $this->db->update('project_invoice_monthly', array('invoice_hours' => $invoice_amount));
     } else {
         $update_result = $this->db->insert('project_invoice_monthly', array(
             'project_Id' => $project_id,
             'invoice_year' => $invoice_year,
             'invoice_month' => $invoice_month,
             'invoice_hours' => $invoice_amount
         ));
     }
     
    if ($update_result) {
        // Send email notification
        $this->sendInvoiceNotificationEmail($project_details, $invoice_amount, $department);
        
        echo json_encode(array(
            'success' => true,
            'message' => 'Invoice updated successfully for ' . date('F Y', strtotime($form_date))
        ));
    } else {
        echo json_encode(array(
            'success' => false,
            'message' => 'Failed to update invoice'
        ));
    }
 }
 
/**
 * Send email notification when invoice is added/updated
 */
private function sendInvoiceNotificationEmail($project_details, $invoice_amount, $department = '') {
    // Email configuration
    $config['mailtype'] = 'html';
    $config['charset'] = 'utf-8';
    $config['wordwrap'] = TRUE;
    $config['newline'] = "\r\n";
    $this->email->initialize($config);
    
    // Get current user info
    $current_user = $this->session->userdata['logged_in_timesheet'];
    $updated_by = !empty($current_user['name']) ? $current_user['name'] : 'System';
    
    // Determine if it's a new invoice or update
    $old_amount = !empty($project_details->old_invoice_amt) ? $project_details->old_invoice_amt : 0;
    $is_new = ($old_amount == 0 || empty($old_amount));
    $action = $is_new ? 'Added' : 'Updated';
    
    // Determine recipient email based on department
    $recipient_email = 'pradip@elogictech.com'; // Default for other departments pradip@elogictech.com
    if (!empty($department) && strtoupper($department) == 'MEP') {
        $recipient_email = 'farhan@elogictech.com';  // farhan@elogictech.com
    }
    
    // Email subject
    $project_name = !empty($project_details->project_name) ? $project_details->project_name : 'Project';
    $subject = 'Project Invoice Hours ' . $action . ' - ' . $project_name;
     
     // Email body
     $body = '<!doctype html>
     <html>
     <head>
         <meta charset="utf-8">
         <title>Invoice Notification</title>
     </head>
     <body style="width: 95%; margin: 0 auto; background: #f1f1f1; border:1px solid #888; padding: 0 1% 2% 1%;">
         <div align="left" style="margin: 3% auto 2% 6%;">
             <img src="https://www.elogictech.com/assets/frontend/images/logo.png" style="width: 180px;">
         </div>
        <div style="background: #004b88; padding: 2%; border-radius: 15px; margin-top: 3%;">
            <h2 style="color: #fff; margin: 0;">Project Invoice Hours ' . $action . ' Notification</h2>
        </div>
        <div style="background: #fff; padding: 3%; margin-top: 2%; border-radius: 10px;">
            <p>Dear Team,</p>
            
            <p>Project invoice hours have been <strong>' . strtolower($action) . '</strong> in the system. Please find the details below:</p>
             
             <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                 <tr style="background: #f5f5f5;">
                     <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; width: 30%;">Project Name:</td>
                     <td style="padding: 10px; border: 1px solid #ddd;">' . (!empty($project_details->project_name) ? htmlspecialchars($project_details->project_name) : 'N/A') . '</td>
                 </tr>
                 <tr>
                     <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Client Name:</td>
                     <td style="padding: 10px; border: 1px solid #ddd;">' . (!empty($project_details->client_name) ? htmlspecialchars($project_details->client_name) : 'N/A') . '</td>
                 </tr>
                <tr style="background: #f5f5f5;">
                    <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Project Manager:</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">' . (!empty($project_details->project_manager_name) ? htmlspecialchars($project_details->project_manager_name) : 'N/A') . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Department:</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">' . (!empty($department) ? htmlspecialchars($department) : 'N/A') . '</td>
                </tr>';
    
    if (!$is_new) {
        $body .= '<tr style="background: #f5f5f5;">
                    <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Previous Invoice Hours:</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">' . number_format($old_amount, 2) . ' Hours</td>
                </tr>';
    }
    
    $body .= '<tr style="background: #e8f5e9;">
                    <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; color: #2e7d32;">' . ($is_new ? 'Invoice Hours:' : 'Updated Invoice Hours:') . '</td>
                    <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; color: #2e7d32;">' . number_format($invoice_amount, 2) . ' Hours</td>
                </tr>
                 <tr>
                     <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Updated By:</td>
                     <td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($updated_by) . '</td>
                 </tr>
                 <tr style="background: #f5f5f5;">
                     <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Date & Time:</td>
                     <td style="padding: 10px; border: 1px solid #ddd;">' . date('d-M-Y h:i A') . '</td>
                 </tr>
             </table>
             
             <p>Please review the invoice details in the system.</p>
             
             <p>Best regards,<br>
             ' . htmlspecialchars($updated_by) . '</p>
         </div>
     </body>
     </html>';  
     
    // Send email
    $this->email->from('info@elogictech.com', 'eLogic Timesheet');
    $this->email->to($recipient_email);
    $this->email->subject($subject);
    $this->email->message($body);
    
    // Send email (don't fail if email fails)
    @$this->email->send();
}

/**
 * Update client status via AJAX
 * Only admin users can update client status
 */
public function update_status()
{
    // Load model (IMPORTANT)
    $this->load->model('client_model');

    // Set JSON header
    header('Content-Type: application/json');

    // Check if user is admin
    $user_type = $this->session->userdata['logged_in_timesheet']['user_type'];

    if($user_type != 'admin' && $user_type != 'superadmin') {
        echo json_encode(array(
            'success' => false,
            'message' => 'Permission denied. Only admin users can update client status.'
        ));
        return;
    }

    // Get POST data safely
    $client_Id = $this->input->post('client_Id', TRUE);
    $current_status = $this->input->post('current_status', TRUE);

    // Validation
    if(empty($client_Id)) {
        echo json_encode(array(
            'success' => false,
            'message' => 'Client ID is required'
        ));
        return;
    }

    // Toggle status
    if(strtolower(trim($current_status)) == 'active') {
        $new_status = 'Inactive';
    } else {
        $new_status = 'Active';
    }

    // Update DB
    $result = $this->client_model->update_client_status($client_Id, $new_status);

    // Response
    if($result) {
        echo json_encode(array(
            'success'    => true,
            'message'    => 'Status updated successfully',
            'new_status' => $new_status
        ));
    } else {
        echo json_encode(array(
            'success' => false,
            'message' => 'Failed to update status'
        ));
    }
}



/********************************************************************** Project execution status information ***************************************************************************************/

public function project_exe_status(){ // Project execution status information
		
	// Get parameters from POST or GET
	$clientId = $this->input->post('client_Id') ? $this->input->post('client_Id') : $this->input->get('client_Id');
	$projectId = $this->input->post('project_Id') ? $this->input->post('project_Id') : $this->input->get('project_Id');
	$formDate = $this->input->post('form_date') ? $this->input->post('form_date') : $this->input->get('form_date');
	$toDate = $this->input->post('to_date') ? $this->input->post('to_date') : $this->input->get('to_date');
	
	// Set default dates to today if not provided
	if(empty($formDate)) {
		$formDate = date('Y-m-d');
	}
	if(empty($toDate)) {
		$toDate = date('Y-m-d');
	}
	
	// Always load data with default dates if no POST request (initial page load)
	// If no client selected, use 'all' for initial load
	if(empty($clientId) && empty($this->input->post('client_Id'))) {
		$clientId = 'all';
	}
	
	// If no client selected but we have dates, load 'all' clients
	if(empty($clientId)) {
		$clientId = 'all';
	}
	
	$params = array(
		'client_Id' => $clientId,
		'project_Id' => !empty($projectId) ? $projectId : 'all',
		'form_date' => $formDate,
		'to_date' => $toDate,            
	);

	$data['allClientResult'] = $this->client_model->projectExecutionStatusInformation($params);
	$data['form_date'] = $formDate;
	$data['to_date'] = $toDate;
	$data['client_Id'] = $clientId;

	$this->load->view('clients/project_exe_status_informaton',$data);
	
	
	
	/* if(!empty($this->input->post('client_Id'))) :
		
		 $params = array(
            'client_Id' => $this->input->post('client_Id'),
            'project_Id' => $this->input->post('project_Id'),
            'form_date' => $this->input->post('form_date'),
            'to_date' => $this->input->post('to_date'),            
            );
		
         $data['resultClientReport'] = $this->client_model->compareResouceTimesheet($params);     
        
		 $this->load->view('clients/resouce_vs_timehseet' , $data);
		 
	   else : 
	   
	       $this->load->view('clients/resouce_vs_timehseet');
	   
	   endif; 
	   */
		
	}
    
 /************************************************** Project execution status information ****************************************/   


}
