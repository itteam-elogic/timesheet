<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timesheet extends CI_Controller {

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
		$this->load->helper('text');
		
		// Load database		
		$this->load->model('timesheet_login');
		
		$this->load->model('client_model');
		
		$this->load->model('project_model');
		
		$this->load->model('task_model');
		
		$this->load->model('emptimelog_model');
		
		$this->load->model('timesheet_model');
				
		if(empty($this->session->userdata['logged_in_timesheet'])){
		
			redirect('home/login');
		}
		
    }
	
	/*public function index(){
	
	    	$this->load->view('timesheet/timesheet');
	
	}
	*/
	
	
	public function index(){  // Search Employee Lime Log
	
	    if(!empty($this->input->post('client_Id'))) :
		
		 $params = array(
            'client_Id' 		=> $this->input->post('client_Id'),
            'project_Id' 		=> $this->input->post('project_Id'),
			'task_Id'			=> $this->input->post('task_Id'),
			'empId' 		  	=> $this->input->post('empId'),
			'reporting_manager' => $this->input->post('reporting_manager'),
            'form_date'			=> $this->input->post('form_date'),
            'to_date'			=> $this->input->post('to_date'),
			'department'		=> $this->_timesheet_department_filter($this->input->post('department')),
            );
			
		//echo '<pre>'; print_r($params);	 exit;
		
           $data['getManageReportLog'] = $this->emptimelog_model->getUserTypeAdminReportLog($params);
		   $data['reportingManagerMap'] = array();
		   $data['reportingManagerDepartmentMap'] = array();
		   $data['projectManagerMap'] = array();
		   $data['taskNameByIdMap'] = array();

		   if (!empty($data['getManageReportLog'])) {
		   		$reportingManagerIds = array();
		   		$projectManagerIds = array();
		   		$taskIdsList = array();
		   		foreach ($data['getManageReportLog'] as $row) {
		   			$reportingManagerIds[] = isset($row->reporting_manger) ? (string)$row->reporting_manger : '';
		   			$projectManagerIds[] = isset($row->project_manager_name) ? (string)$row->project_manager_name : '';
		   			$taskIdsList[] = isset($row->task_Id) ? (string)$row->task_Id : '';
		   		}

		   		$reportingManagerMetaMap = $this->timesheet_login->getEmployeeMetaMapByIds($reportingManagerIds);
		   		$projectManagerMetaMap = $this->timesheet_login->getEmployeeMetaMapByIds($projectManagerIds);
		   		$data['taskNameByIdMap'] = $this->emptimelog_model->getTaskNamesMapByTaskIds($taskIdsList);

		   		foreach ($reportingManagerMetaMap as $empId => $meta) {
		   			$data['reportingManagerMap'][(string)$empId] = isset($meta['name']) ? $meta['name'] : '';
		   			$data['reportingManagerDepartmentMap'][(string)$empId] = isset($meta['department']) ? $meta['department'] : '';
		   		}
		   		foreach ($projectManagerMetaMap as $empId => $meta) {
		   			$data['projectManagerMap'][(string)$empId] = isset($meta['name']) ? $meta['name'] : '';
		   		}
		   }
        
		 	$this->load->view('timesheet/timesheet' , $data);
		 
	   else : 
	      
		    $data['getManageReportLog'] = '';
			$data['reportingManagerMap'] = array();
			$data['reportingManagerDepartmentMap'] = array();
			$data['projectManagerMap'] = array();
			$data['taskNameByIdMap'] = array();
		    
	      	$this->load->view('timesheet/timesheet',$data);
	   
	   endif; 	 	
			
			
		
	}

	/** Normalize department param: multi-select can send array; 'all' or empty = no filter. */
	private function _timesheet_department_filter($department) {
		if (empty($department)) return array();
		$arr = is_array($department) ? $department : array($department);
		$arr = array_values(array_filter($arr));
		if (in_array('all', $arr) || empty($arr)) return array();
		return array_values(array_diff($arr, array('all')));
	}
    
	
	public function managerTimereport(){  // Search Employee Lime Log
	
	    if(!empty($this->input->post('form_date'))) :
		
		 $params = array(
					'client_Id' 		=> $this->input->post('client_Id'),
					'project_Id' 		=> $this->input->post('project_Id'),
					'task_Id'			=> $this->input->post('task_Id'),
					'empId' 		  	=> $this->input->post('empId'),
					'form_date'			=> $this->input->post('form_date'),
					'to_date'			=> $this->input->post('to_date')            
            );
			
		
		
            $data['getManageReportLog'] = $this->timesheet_model->getManagerReportLog($params);     
        
		 	$this->load->view('timesheet/manager_timesheet' , $data);
		 
	   else : 
	      
		    $data['getManageReportLog'] = '';
		    
	      	$this->load->view('timesheet/manager_timesheet',$data);
	   
	   endif; 
			
		
	}
	
	
 
	public function manager_excel(){
		
		$params = array(
				'client_Id' 		=> $this->input->get('client_Id'),
				'project_Id' 		=> $this->input->get('project_Id'),
				'task_Id'			=> $this->input->get('task_Id'),
				'empId' 		  	=> explode(' ,' , $this->input->get('empId')),
				'form_date'			=> $this->input->get('form_date'),
				'to_date'			=> $this->input->get('to_date'),            
		);
//echo '<pre>'; print_r($params); exit;
	    $this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle('Time Report');
		//set cell A1 content with some text
		$this->excel->getActiveSheet()->setCellValue('A1', 'Time Report Excel Sheet');
	    $this->excel->getActiveSheet()->setCellValue('A2', 'Sno');
	    $this->excel->getActiveSheet()->setCellValue('B2', 'Employee Name');
		$this->excel->getActiveSheet()->setCellValue('C2', 'Client Name');
		$this->excel->getActiveSheet()->setCellValue('D2', 'Project Name');
		$this->excel->getActiveSheet()->setCellValue('E2', 'Task Name');
		$this->excel->getActiveSheet()->setCellValue('F2', 'Task Hours');
		$this->excel->getActiveSheet()->setCellValue('G2', 'Added Date');
        $this->excel->getActiveSheet()->setCellValue('H2', 'Entry Date');
		$this->excel->getActiveSheet()->setCellValue('I2', 'Comments');
		//merge cell A1 until F1
		$this->excel->getActiveSheet()->mergeCells('A1:H1');
		//set aligment to center for that merged cell (A1 to H1)
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//make the font become bold
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setSize(14)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('C2')->getFont()->setSize(14)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('D2')->getFont()->setSize(14)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('E2')->getFont()->setSize(14)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('F2')->getFont()->setSize(14)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('G2')->getFont()->setSize(14)->setBold(true);
        $this->excel->getActiveSheet()->getStyle('H2')->getFont()->setSize(14)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('I2')->getFont()->setSize(14)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('A3')->getFill()->getStartColor()->setARGB('#4286f4');
		
       for($col = ord('A'); $col <= ord('H'); $col++){ //set column dimension $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
                 //change the font size
				 
				$this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
				  
                $this->excel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
                 
                if(chr($col) == 'E'){ 
                $this->excel->getActiveSheet()->getStyle(chr($col))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				}else if(chr($col) == 'H'){ 
                $this->excel->getActiveSheet()->getStyle(chr($col))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				}else{
					$this->excel->getActiveSheet()->getStyle(chr($col))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				}	
        }
        
        $exportDataInformation = $this->timesheet_model->getManagerReportLog($params);   // this will return all data into array
		
		
	    $exceldata="";
		
        $sno = 0;
		
        foreach ($exportDataInformation as $row){ $sno++; 
		 
		    $getListOfProjects   			  = $this->emptimelog_model->getAddedReportTaskNames($row->task_Id);
			$arrangeData['Sno'] 	 	      = $sno;
			$arrangeData['Employee Name'] 	  = $row->name;
			$arrangeData['Client Name'] 	  = $row->client_name;
			$arrangeData['Project Name']	  = $row->project_name;
			$arrangeData['Task Name'] 		  = $getListOfProjects;
			$arrangeData['Task Hours']		  = $row->emp_time_hours;
			$arrangeData['Added Date'] 		  = $row->emp_report_dates;
            $arrangeData['Entry Date'] 		  = $row->created_at;
			$arrangeData['comments'] 		  = $row->comments;
	
                $exceldata[] = $arrangeData;
        }
                //Fill data 
                $this->excel->getActiveSheet()->fromArray($exceldata, null, 'A4');
                 
                $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('H2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                 //$time = time();
                $filename="Manager_Employee_Report_sheet.xlsx"; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');  
                //force user to download the Excel file without writing it to server's HD
                $objWriter->save('php://output');
                 
    }
	
	
	public function excel(){
               
       
		$departmentGet = $this->input->get('department');
		$departmentArr = $this->_timesheet_department_filter(
			is_string($departmentGet) && strpos($departmentGet, ',') !== false
				? array_map('trim', explode(',', $departmentGet))
				: $departmentGet
		);
		$params = array(
				'client_Id' 		=> $this->input->get('client_Id'),
				'project_Id' 		=> $this->input->get('project_Id'),
				'task_Id'			=> $this->input->get('task_Id'),
				'empId' 		  	=> explode(' ,' , $this->input->get('empId')),
				'reporting_manager' => $this->input->get('reporting_manager'),
				'form_date'			=> $this->input->get('form_date'),
				'to_date'			=> $this->input->get('to_date'),
				'department'		=> $departmentArr,
		);

	    $this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle('Time Report');
		//set cell A1 content with some text
		$this->excel->getActiveSheet()->setCellValue('A1', 'Time Report Excel Sheet');
	    $this->excel->getActiveSheet()->setCellValue('A2', 'Sno');
	    $this->excel->getActiveSheet()->setCellValue('B2', 'Name');
        $this->excel->getActiveSheet()->setCellValue('C2', 'Employee ID');
        $this->excel->getActiveSheet()->setCellValue('D2', 'Reporting Manager');
        $this->excel->getActiveSheet()->setCellValue('E2', 'Department');
		$this->excel->getActiveSheet()->setCellValue('F2', 'Client Name');
		$this->excel->getActiveSheet()->setCellValue('G2', 'Project Name');
        $this->excel->getActiveSheet()->setCellValue('H2', 'Project Manager');
        $this->excel->getActiveSheet()->setCellValue('I2', 'Task Name');
		$this->excel->getActiveSheet()->setCellValue('J2', 'Task Hours');        
		$this->excel->getActiveSheet()->setCellValue('K2', 'Status');
        $this->excel->getActiveSheet()->setCellValue('L2', 'Added Date');
        $this->excel->getActiveSheet()->setCellValue('M2', 'Entry Date');
		$this->excel->getActiveSheet()->setCellValue('N2', 'Comments');
		//merge cell A1 until F1
		$this->excel->getActiveSheet()->mergeCells('A1:N1');
		//set aligment to center for that merged cell (A1 to H1)
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//make the font become bold
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setSize(14)->setBold(true);
        $this->excel->getActiveSheet()->getStyle('C2')->getFont()->setSize(14)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('D2')->getFont()->setSize(14)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('E2')->getFont()->setSize(14)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('F2')->getFont()->setSize(14)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('G2')->getFont()->setSize(14)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('H2')->getFont()->setSize(14)->setBold(true);
        $this->excel->getActiveSheet()->getStyle('I2')->getFont()->setSize(14)->setBold(true);
        $this->excel->getActiveSheet()->getStyle('J2')->getFont()->setSize(14)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('K2')->getFont()->setSize(14)->setBold(true);
        $this->excel->getActiveSheet()->getStyle('L2')->getFont()->setSize(14)->setBold(true);
        $this->excel->getActiveSheet()->getStyle('M2')->getFont()->setSize(14)->setBold(true);
        $this->excel->getActiveSheet()->getStyle('N2')->getFont()->setSize(14)->setBold(true);
		$this->excel->getActiveSheet()->getStyle('O3')->getFill()->getStartColor()->setARGB('#4286f4');
		
       for($col = ord('A'); $col <= ord('L'); $col++){ //set column dimension $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
                 //change the font size
				 
				$this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
				  
                $this->excel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
                 
                if(chr($col) == 'E'){ 
                $this->excel->getActiveSheet()->getStyle(chr($col))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				}else if(chr($col) == 'F'){ 
                $this->excel->getActiveSheet()->getStyle(chr($col))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				}else if(chr($col) == 'K'){ 
                $this->excel->getActiveSheet()->getStyle(chr($col))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				}else{
					$this->excel->getActiveSheet()->getStyle(chr($col))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				}	
        }
        
        
        $exportDataInformation = $this->emptimelog_model->getUserTypeAdminReportLog($params);  // this will return all data into array
				
	    $exceldata="";
		
        $sno = 0;
		
        $managerDepartmentCache = array();
        foreach ($exportDataInformation as $row){ $sno++; 
                                                 
            $ProjectManagerName = $this->timesheet_login->getReportingManagers($row->project_manager_name); // getting reporting Manager Name and Project created Manager Name in same function.
            $reportManagerName = $this->timesheet_login->getReportManagerName($row->reporting_manger, true);// Getting reporting manager name       
            $reportingManagerEmpId = isset($row->reporting_manger) ? (string)$row->reporting_manger : '';
            if (!array_key_exists($reportingManagerEmpId, $managerDepartmentCache)) {
                $managerDepartmentCache[$reportingManagerEmpId] = $this->timesheet_login->getEmployeeDepartmentById($reportingManagerEmpId);
            }
            $managerDepartment = $managerDepartmentCache[$reportingManagerEmpId];
                                                 
            $addedDate=date_create($row->emp_report_dates);
            $addedDateData = date_format($addedDate,"d/m/Y");
            $createdAt = date_create($row->created_at);
            $createdAtData = date_format($createdAt,"d/m/Y");
		 
		    $getListOfProjects   			  = $this->emptimelog_model->getAddedReportTaskNames($row->task_Id);
			$arrangeData['Sno'] 	 	      = $sno;
			$arrangeData['Name'] 	          = $row->name;
            $arrangeData['Employee ID'] 	  = $row->emp_com_id;
            $arrangeData['Reporting Manager'] = $reportManagerName;
            $arrangeData['Department']	      = !empty($managerDepartment) ? $managerDepartment : 'N/A';
			$arrangeData['Client Name'] 	  = $row->client_name;
			$arrangeData['Project Name']	  = $row->project_name;
            $arrangeData['Project Manager']   = $ProjectManagerName;   
            $arrangeData['Task Name'] 		  = $getListOfProjects;
			$arrangeData['Task Hours']		  = $row->emp_time_hours;           
            $arrangeData['Status'] 		       = $row->status;                                      
			$arrangeData['Added Date'] 		  = $addedDateData;                                                
            $arrangeData['Entry Date'] 		  = $createdAtData;                                     
			$arrangeData['comments'] 		  = $row->comments;
	
                $exceldata[] = $arrangeData;
        }
                //Fill data 
                $this->excel->getActiveSheet()->fromArray($exceldata, null, 'A4');
                 
                $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('H2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('J2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->excel->getActiveSheet()->getStyle('K2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->excel->getActiveSheet()->getStyle('L2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->excel->getActiveSheet()->getStyle('M2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->excel->getActiveSheet()->getStyle('N2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                 //$time = time();
        
                $filename="Employee_Report_sheet.xlsx"; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache

                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');  
                //force user to download the Excel file without writing it to server's HD
                $objWriter->save('php://output');
                 
    }
	
	
}
