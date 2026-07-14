<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tsr extends CI_Controller {

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
		
		$this->load->helper('text');

		$this->load->model('tsr_model');
		
		 //$this->load->library('email');
        
		if(empty($this->session->userdata['logged_in_timesheet'])){
		
			redirect('home/login');
		}
		
    }
        
/***************************************** TSR Reports *****************************************************/  

public function index(){  // Search Employee Lime Log
	
	if(!empty($this->input->post('client_Id'))) :
	
	 $params = array(
		'client_Id' 		=> $this->input->post('client_Id'),
		'project_Id' 		=> $this->input->post('project_Id'),
		'task_Id'			=> $this->input->post('task_Id'),
		'empId' 		  	=> $this->input->post('empId'),
		'form_date'			=> $this->input->post('form_date'),
		'to_date'			=> $this->input->post('to_date'),            
		);

		
	   $data['allTsrResult'] = $this->tsr_model->getSearchTsrResult($params);     
	  
	   $this->load->view('tsr/tsr_reports',$data);
	 
   else : 
	  
		 $data['allTsrResult'] = '';
		
		  $this->load->view('tsr/tsr_reports',$data);
   
   endif; 	 
		
		
	
}

public function excel(){
	
	$params = array(
		'form_date' => $this->input->get('form_date'),
		'to_date' => $this->input->get('to_date'),
	);

	$this->excel->setActiveSheetIndex(0);
	$this->excel->getActiveSheet()->setTitle('Time Report');
	$this->excel->getActiveSheet()->setCellValue('A1', 'TSR Report Excel Sheet');
	$this->excel->getActiveSheet()->setCellValue('A2', 'Sno');
	$this->excel->getActiveSheet()->setCellValue('B2', 'Employee Name');
	$this->excel->getActiveSheet()->setCellValue('C2', 'Client Name');
	$this->excel->getActiveSheet()->setCellValue('D2', 'Project Name');
	$this->excel->getActiveSheet()->setCellValue('E2', 'Task Name');
	$this->excel->getActiveSheet()->setCellValue('F2', 'Task Hours');
	$this->excel->getActiveSheet()->setCellValue('G2', 'Status');
	$this->excel->getActiveSheet()->setCellValue('H2', 'Added Date');
	$this->excel->getActiveSheet()->setCellValue('I2', 'Entry Date');

	$this->excel->getActiveSheet()->mergeCells('A1:I1');
	$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16)->setBold(true);
	$this->excel->getActiveSheet()->getStyle('A2:I2')->getFont()->setSize(14)->setBold(true);

	for ($col = ord('A'); $col <= ord('I'); $col++) {
		$this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
		$this->excel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
		$this->excel->getActiveSheet()->getStyle(chr($col))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}

	$exportDataInformation = $this->tsr_model->getSearchTsrResult($params);
	$exceldata = [];
	$sno = 0;

	foreach ($exportDataInformation as $row) {
		$sno++;
		$exceldata[] = [
			'Sno' => $sno,
			'Employee Name' => $row->name,
			'Client Name' => $row->client_name,
			'Project Name' => $row->project_name,
			'Task Name' => $row->task_name,
			'Task Hours' => $row->emp_time_hours,
			'Status' => $row->status,
			'Added Date' => $row->emp_report_dates,
			'Entry Date' => $row->created_at,
		];
	}

	$this->excel->getActiveSheet()->fromArray($exceldata, null, 'A3');
	$filename = "Employee_Report_sheet.xls";
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="' . $filename . '"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
	$objWriter->save('php://output');
}
	


/***************************************** TSR Reports *****************************************************/        	
	
}
