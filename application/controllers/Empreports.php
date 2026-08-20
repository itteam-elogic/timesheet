<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Empreports extends CI_Controller {

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
		
		 //$this->load->library('email');
        
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
	
	public function add($emp_record_id = NULL){
	
	   if(empty($emp_record_id)) : 
			
			 $this->load->view('employee_reports/add_employee_timelog');
			 
		else:
			
			   $data['updateEmpRecord'] = $this->emptimelog_model->getUpdateEmpRecords($emp_record_id);
	
		    	//$this->load->view('employee/add_employees' , $data);
				
				$this->load->view('employee_reports/update_employee_timelog' , $data);
					
		endif;	   
			
	 
	}
	
	
	public function add_emp_records(){
	
	if(!empty($this->session->userdata['logged_in_timesheet']['empId'])) :
        
       
        $clientIds = $this->input->post('client_Id');
        $projectIds = $this->input->post('project_Id');
        $taskIds = $this->input->post('task_Id');
        $empTimeHours = $this->input->post('emp_time_hours');
        $comments = $this->input->post('comments');
        $empReportDates = $this->input->post('emp_report_dates');
        $data = array();
        
        if(!empty($clientIds) && is_array($clientIds)) {
         
            foreach($clientIds as $i => $clientId) {
                       
	if(!empty($clientId) && !empty($projectIds[$i]) && !empty($taskIds[$i])):
                
		$data[] = array(
			'empId' 					 => $this->session->userdata['logged_in_timesheet']['empId'],
			'client_Id' 				 => $clientId,
			'project_Id' 				 => $projectIds[$i],
			'task_Id' 			 		 => $taskIds[$i], // Store task with comma separate
			'team_member_type'			 => $this->input->post('team_member_type'),
			'emp_time_hours'			 => isset($empTimeHours[$i]) ? $empTimeHours[$i] : '',
			'comments' 			 		 => isset($comments[$i]) ? $comments[$i] : '',
			'emp_report_dates' 			 => isset($empReportDates[$i]) ? $empReportDates[$i] : '',
			'created_at'    	 		 => date('Y-m-d H:i:s'),
			'updated_at' 				 => date('Y-m-d H:i:s')
			);
                
        endif;        
        
            }
            
            
        }        
	
	        
			//Get Project Manager email id based on Project.
			
			$getStoredDetails = $this->emptimelog_model->addEmpRecords($data);
			//redirect('empreports');
			//if(!empty($getStoredDetails)){ 
			
			$getListOfProjects   	= $this->emptimelog_model->getAddedReportTaskNames($getStoredDetails[0]->task_Id); // List of tasks
			//echo 'kanth'.$getStoredDetails[0]->project_name; exit;
        //if($getStoredDetails[0]->user_type == 'manager'){    
            
        if($getStoredDetails[0]->name == $this->session->userdata['logged_in_timesheet']['name']){    
           
			 	$rashmiReportingManagers = array('rajanikanth@elogictech.com');
			
				$jaisreeReportingManagers = array('laxmikanth@elogictech.com');
			
                $rahulReportingManagers = array('pritha@elogictech.com');
            
                $farhanReportingManagers = array('srinivasg@elogictech.com' , 'afsar@elogictech.com');
			
				$pradipReportingManagers = array('sandeep@elogictech.com','sivakrishna@elogictech.com','shivani@elogictech.com','rashmi@elogictech.com','vamsikrishna@elogictech.com','hari@elogictech.com');            
                $businessHeads = array('pradip@elogictech.com','farhan@elogictech.com','suman@elogictech.com','jaishree@elogictech.com');
                    
                    if(in_array($getStoredDetails[0]->email , $rahulReportingManagers)) :

                            $to_email = 'rahul@elogictech.com'; // mail IDs

                            $nameOfEamilPerson = 'Rahul Kumar';  
            
                    elseif(in_array($getStoredDetails[0]->email , $businessHeads)) :
            
                            $to_email = 'rupali@elogictech.com'; // mail IDs

                            $nameOfEamilPerson = 'Rupali Modi';
			
			       elseif(in_array($getStoredDetails[0]->email , $rashmiReportingManagers)) :
            
                            $to_email = 'rashmi@elogictech.com'; // mail IDs

                            $nameOfEamilPerson = 'Rashmi Hans';
                    
                    elseif(in_array($getStoredDetails[0]->email , $farhanReportingManagers)) :
            
                            $to_email = 'farhan@elogictech.com'; // mail IDs

                            $nameOfEamilPerson = 'Farhan'; 


                    elseif(in_array($getStoredDetails[0]->email , $pradipReportingManagers)) :

                            $to_email = 'pradip@elogictech.com'; // mail IDs

                            $nameOfEamilPerson = 'Pradip Chauhan';
			
					elseif(in_array($getStoredDetails[0]->email , $jaisreeReportingManagers)) :

                            $to_email = 'jaishree@elogictech.com'; // mail IDs

                            $nameOfEamilPerson = 'Jaishree';
			
					else:
			
						    //$to_email = 'laxmikanth@elogictech.com'; // mail IDs

                            //$nameOfEamilPerson = 'Laxmikanth Reddy';

                    endif;
               
           }else{
               
                    $to_email = $getStoredDetails[0]->email; // mail IDs

                  //$to_email = 'laxmikanth@elogictech.com'; // mail IDs

                   $nameOfEamilPerson = $getStoredDetails[0]->name;  
           }
        
            $config['mailtype'] = 'html';
            $config['charset'] = 'iso-8859-1';
            $config['wordwrap'] = TRUE;
            $config['newline'] = "\r\n"; //use double quotes
            $this->email->initialize($config);                        
	        //send mail 
            $this->email->from('info@elogictech.com', 'eLogic Timesheet');
			$this->email->to($to_email);
			$this->email->subject('Task Report' , 'eLogic Timesheet');
			 $body = '<!doctype html><html><head><meta charset="utf-8"><title>eLogic Tech</title></head><body style="width: 95%; margin: 0 auto; background: #f1f1f1; border:1px solid #888; padding: 0 1% 2% 1% "><div align="left" style="margin: 3% auto 2% 6%;"> <img src="http://www.elogictechsolutions.com/assets/images/logo.png" style="width: 180px;"> </div><div style="background: #004b88; padding: 2%; border-radius: 15px; margin-top: 3%;"> <section style="background: #004b88; border-radius: 6px; padding-top: 2%; font-size: 17px;"> <div style=" color: #fff; margin:2% auto 0px auto; padding-left: 6%;">Dear '.ucwords($nameOfEamilPerson).', </div> <div align="left" style=" margin: 1% auto; padding-left: 6%; line-height: 24px; color: #fff;"> <p>I have entered the timesheet for '.date('d-F-Y',strtotime($getStoredDetails[0]->emp_report_dates)).'. </p> </div> <div align="left" style=" margin: 1% auto; padding-left: 6%; line-height: 24px; color: #fff;"> <table> <tbody> <tr> <td width="20%"> Client Name : </td> <td> '.$getStoredDetails[0]->client_name.' </td> </tr> <tr> <td> Project Name : </td> <td> '.$getStoredDetails[0]->project_name.' </td> </tr> <tr> <td> Description : </td> <td> '.$getListOfProjects.' </td> </tr><tr> <td> Status  : </td> <td> '.$getStoredDetails[0]->status.' </td> </tr><tr> <td> Date : </td> <td> '.date('d-F-Y',strtotime($getStoredDetails[0]->emp_report_dates)).' </td> </tr> <tr> <td> Hours : </td> <td> '.$getStoredDetails[0]->emp_time_hours.' hrs </td> </tr> <tr> <td align="top" style="position: relative; top: 0px;"> Comments : </td> <td> '.$getStoredDetails[0]->comments.'. </td> </tr> </tbody> </table> </div> <div align="left" style=" margin: 3% auto 0 6%; line-height: 24px; color: #fff; ">Thanks & Regards, <br> <div style="color: #fff; padding-bottom: 4%;">'.ucwords($this->session->userdata['logged_in_timesheet']['name']).' </div> </div> </section></div></body></html>';
		
				$this->email->message($body);
            
			    $this->email->send();
			
			redirect('empreports');

			//}else{

				//$this->emptimelog_model->addEmpRecords($data);
		
				//redirect('empreports');
			//}
			
			//Get Project Manager email id based on Project.
			
			
		 
	  endif;
	   
	
	}
	
	
	public function update_emp_records() {
	
		
	 if(!empty($this->session->userdata['logged_in_timesheet']['empId'])) :
	
		$emp_record_id = $this->input->post('emp_record_id');
		
		
		$data = array(
			'client_Id' 				 => $this->input->post('client_Id'),
			'project_Id' 				 => $this->input->post('project_Id'),
			'task_Id' 			 		 => $this->input->post('task_Id'), // Store task with comma separate
			'team_member_type'			 => $this->input->post('team_member_type'),
			'emp_time_hours'			 => $this->input->post('emp_time_hours'),
			'comments' 			 		 => $this->input->post('comments'),
			'emp_report_dates' 			 => $this->input->post('emp_report_dates'),
			'status' 					 => 'Unapproved',
			'updated_at' 				 => date('Y-m-d H:i:s')
			);
	
	        $this->emptimelog_model->updateEmpRecords($data , $emp_record_id);
		
		    redirect('empreports');
		 
	  endif;
			
	
	}
	
	
	public function delete(){  // Delete employee single record into databasse
	
		   $emp_record_id  = $this->input->post('emp_record_id');
		   
			if(!empty($emp_record_id)):
			
				$del = $this->emptimelog_model->deleteEmpRecord($emp_record_id);
				
			endif;	
	}
	
	
	public function getListOfProjectsWithClient(){  // Getting Client wise projects
	
	  $client_Id  = $this->input->post('client_Id'); 
	   
	   if(!empty($client_Id)) :
	   
	   		$getProjects = $this->emptimelog_model->getListOfProjectsWithClient($client_Id);
	   
	   endif; 
	
	 }  // Getting Client wise projects END
    
    
    public function getListOfProjectsGeneralWithClient(){  // Getting Client wise projects
	
	  $client_Id  = $this->input->post('client_Id'); 
	   
	   if(!empty($client_Id)) :
	   
	   		$getProjects = $this->emptimelog_model->getListOfProjectsGeneralWithClient($client_Id);
	   
	   endif; 
	
	 }  // Getting Client wise projects END
	 
	 
	 public function getClientProjects(){  // Getting Client wise projects
	
	  $client_Id  = $this->input->post('client_Id'); 
	   
	   if(!empty($client_Id)) :
	   
	   		$getProjects = $this->emptimelog_model->getClientWiseProjects($client_Id);
	   
	   endif; 
	
	 }  // Getting Client wise projects END
	 
	
	public function getProjectsTask(){ // Getting Project wise task
	
		$project_Id  = $this->input->post('project_Id'); 
	   
	   if(!empty($project_Id)) :
	   
	   		$getTask = $this->emptimelog_model->getProjectWiseTaskLWGList($project_Id);
	   
	   endif; 
	
	} // Getting Project wise task END
	
	
	
	public function searchReportLog(){  // Search Employee Lime Log
	
	    if(!empty($this->input->post('client_Id'))) :
		
		 $params = array(
            'client_Id' => $this->input->post('client_Id'),
            'project_Id' => $this->input->post('project_Id'),
            'form_date' => $this->input->post('form_date'),
            'to_date' => $this->input->post('to_date'),            
            );
		
         $data['resultTimeLog'] = $this->emptimelog_model->getSearchEmpTimeLog($params);     
        
		 $this->load->view('employee_reports/employee_timelog_search' , $data);
		 
	   else : 
	   
	       $this->load->view('employee_reports/employee_timelog_search');
	   
	   endif; 	 
			
			
		
	}
	
  
   public function cPass(){  //Change Password
   
   		if(!empty($this->input->post('password'))):
		
			$password		 = 	$this->input->post('password');
			
			$employeeName    =  $this->session->userdata['logged_in_timesheet']['username'];
			
			$this->emptimelog_model->updateChangePassword( $password , $employeeName );
			
			 $this->session->set_flashdata('msg', 'Your Password Successfully Changed!</span>');
              
			  redirect(base_url().'empreports/cPass');
			
		
		else:
		
			$this->load->view('employee_reports/changepassword');
			
		endif;	
		
   
   }	
	
	public function searchProjectsTask(){ // Getting Project wise task
	
		$project_Id  = $this->input->post('project_Id');
       
       if($project_Id == 'all'):
       
                $client_Id   = $this->input->post('client_Id');
       else:
       
                    $client_Id   = NULL;
       endif;
	   
           if(!empty($project_Id)) :

                $getTask = $this->emptimelog_model->searchProjectWiseTask($project_Id , $client_Id);

           endif; 
	
	} 
    
  /*  public function searchProjectsTask(){ // Getting Project wise task
	
		$project_Id  = $this->input->post('project_Id'); 
	   
	   if(!empty($project_Id)) :
	   
	   		$getTask = $this->emptimelog_model->searchProjectWiseTask($project_Id);
	   
	   endif; 
	
	}*/ // Getting Project wise task END	
	
 /*  public function update_emp_report_status(){

			$emp_record_id 	 	 = $this->input->post('emp_record_id');
		    $status 			 = $this->input->post('status');
		   
			if(!empty($emp_record_id)):
			
				$updateStatus = $this->emptimelog_model->update_emp_report_status($emp_record_id , $status);
				
			endif;	

  } */
	
	
   public function update_emp_report_status(){

		    $comment_emp_record_id 	 	 = $this->input->post('comment_emp_record_id');
	        $comment_status 	 	 	 = $this->input->post('comment_status');
		    $status 			 		 = $this->input->post('status');
	  
	   
			if(!empty($comment_emp_record_id)):
			
				$updateStatus = $this->emptimelog_model->update_emp_report_status($comment_emp_record_id,$comment_status,$status);
				//redirect('empreports/pmreportlogs');	
			 
	  		endif;	

    }
    
    
    public function pmreportlogs(){
        
            $userType = 'admin';
			
			$data['getRecords'] = $this->emptimelog_model->getRecords($userType);
			
			$this->load->view('employee_reports/pm_timelog' , $data);
			
			//$this->load->view('employee_reports/add_employee_timelog');
        
    }
    
 	
/******************************** Unapproved Status Information details *****************************************************************************/	
	
   public function unapproved(){
       
      
        if(!empty($this->input->post('project_Id'))) :
		
		 $reportStatus = array(           
             'project_Id' => $this->input->post('project_Id'),
            'form_date' => $this->input->post('form_date'),
            'to_date' => $this->input->post('to_date'),            
            );
		
            
       $data['getRecords'] = $this->emptimelog_model->getReportStatusRecords($reportStatus);
			
       $this->load->view('employee_reports/report_status_records' , $data);
		 
	   else : 
	   
	   $reportStatus = ' Unapproved';
            
       $data['getRecords'] = $this->emptimelog_model->getReportStatusRecords($reportStatus);
			
       $this->load->view('employee_reports/report_status_records' , $data);
	   
	   endif; 	
       
   } 
	
	
  public function unapprovedExeclReport(){
      
       
	    $this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle('Unapproved Report');
		//set cell A1 content with some text
		$this->excel->getActiveSheet()->setCellValue('A1', 'Unapproved Task Report');
	    $this->excel->getActiveSheet()->setCellValue('A2', 'Sno');
	    $this->excel->getActiveSheet()->setCellValue('B2', 'Employee Name');
	  	$this->excel->getActiveSheet()->setCellValue('C2', 'Employee ID');
		$this->excel->getActiveSheet()->setCellValue('D2', 'Client Name');
		$this->excel->getActiveSheet()->setCellValue('E2', 'Project Name');
		$this->excel->getActiveSheet()->setCellValue('F2', 'Task Name');
		$this->excel->getActiveSheet()->setCellValue('G2', 'Task Hours');
		$this->excel->getActiveSheet()->setCellValue('H2', 'Added Date');
		$this->excel->getActiveSheet()->setCellValue('I2', 'Status');
	    $this->excel->getActiveSheet()->setCellValue('J2', 'Created Date');
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
	    $this->excel->getActiveSheet()->getStyle('J2')->getFont()->setSize(14)->setBold(true);
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
        
        if(!empty($this->input->get('project_Id'))) { 
		
		 $reportStatus = array(           
             'project_Id' => $this->input->get('project_Id'),
            'form_date' => $this->input->get('form_date'),
            'to_date' => $this->input->get('to_date'),            
            );
		
	    
        //$reportStatus = ' Unapproved';
	  
        $exportDataInformation = $this->emptimelog_model->getReportStatusRecords($reportStatus);;  // this will return all data into array
            
        }else{
            
            $reportStatus = ' Unapproved';
	  
        $exportDataInformation = $this->emptimelog_model->getReportStatusRecords($reportStatus);;  // this will return all data into array    
            
        }
      
      
				
	    $exceldata="";
		
        $sno = 0;
		
        foreach ($exportDataInformation as $row){ $sno++; 
		 
		    $getListOfProjects   			  = $this->emptimelog_model->getAddedReportTaskNames($row->task_Id);
			$arrangeData['Sno'] 	 	      = $sno;
			$arrangeData['Employee Name'] 	  = $row->name;
			$arrangeData['Employee ID'] 	  = $row->emp_com_id;	 
			$arrangeData['Client Name'] 	  = $row->client_name;
			$arrangeData['Project Name']	  = $row->project_name;
			$arrangeData['Task Name'] 		  = $getListOfProjects;
			$arrangeData['Task Hours']		  = $row->emp_time_hours;
			$arrangeData['Added Date'] 		  = $row->emp_report_dates;
			$arrangeData['status'] 		  	  = $row->status;
			$arrangeData['Created Date'] 	  = $row->created_at;									 
	
                $exceldata[] = $arrangeData;
        }
                //Fill data 
                $this->excel->getActiveSheet()->fromArray($exceldata, null, 'A4');
                 
                $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	  			$this->excel->getActiveSheet()->getStyle('C2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->excel->getActiveSheet()->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('H2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	  			$this->excel->getActiveSheet()->getStyle('J2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                 //$time = time();
                $filename="unapproved_report3_sheet.xlsx"; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');  
                //force user to download the Excel file without writing it to server's HD
                $objWriter->save('php://output');
                 
    }
	  
	  
	  
  
	
	
/******************************** Unapproved Status Information details *****************************************************************************/	
    

/*********************************** Datatable Sort , Search , Pagination For Employee Added Task Report Log ********************************************/
	
	
	
	function index(){
     
		//$userType = $this->session->userdata['logged_in_timesheet']['user_type'];
			
			//$data['getRecords'] = $this->emptimelog_model->getRecords($userType);
			
			$this->load->view('employee_reports/employee_timelog');
			
			//$this->load->view('employee_reports/add_employee_timelog');
		
		
    }
    
    function getdata(){
       // log_message("info",  json_encode($_POST));
         $data = $this->process_get_data();
         $post = $data['post'];
          $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->emptimelog_model->count_all($post),
            "recordsFiltered" =>  $this->emptimelog_model->count_filtered($post),
            "data" => $data['data'],
        );
        unset($post);
        unset($data);
        echo json_encode($output);
        
    }
    
    function process_get_data(){
		
        $post = $this->get_post_input_data();
        
		 $empId =  $this->session->userdata['logged_in_timesheet']['empId'];
		
		 $userType = $this->session->userdata['logged_in_timesheet']['user_type'];
		
	   if($userType == 'developer'):	
		
	  		$post['where'] = array('er.empId' => $empId);
			$post['or_where'] = array();
        	$post['where_in'] = array();
			$post['manager_visibility'] = array();
		
		
		elseif($userType == 'manager'):
			$post['where'] = array();
			$post['or_where'] = array();
			$post['where_in'] = array();
			$post['manager_visibility'] = array(
				'emp_id' => $empId
			);
		
		else:
		
			$post['where'] = array( );
		    $post['or_where'] = array();
        	$post['where_in'] = array();
		
		endif;
		
		if((string)$empId === '47'){
			$allowedDepartments = array('MEP','Architectural','Structural','3d Visualization');
			$post['where_in']['emp.department'] = $allowedDepartments;
		}
		
		$post['column_search'] = array('er.emp_record_id', 'emp.name','c.client_name','p.project_name', 't.task_name','er.emp_time_hours','er.status','er.emp_report_dates');
        $post['column_order'] = array( 'er.emp_record_id', 'emp.name','c.client_name','p.project_name', 't.task_name','er.emp_time_hours','er.status','er.emp_report_dates');
        
        
        $list = $this->emptimelog_model->get_order_list($post);
        $data = array();
        $no = $post['start'];
        
        foreach ($list as $order_list) {
            $no++;
            $row =  $this->order_table_data($order_list, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
                );
    }
    
    function get_post_input_data(){
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        $post['status'] = $this->input->post('status');

        return $post;
    }
    
    function order_table_data($order_list, $no){
        
		
		$getListOfProjects = $this->emptimelog_model->getAddedReportTaskNames($order_list->task_Id); // List of task names based on task id...
		
				
			if($order_list->status=='Approved'):

					$classStatusVal = 'fa fa-check-circle label label-success';

					$statusValueShow = 'Unapproved';

				elseif($order_list->status=='Rejected'):

					$classStatusVal = 'fa fa-registered label label-warning';

					$statusValueShow = 'Approved';

				else:

					$classStatusVal	 = 'fa fa-ban label label-danger';

					$statusValueShow = 'Approved';

				endif;
		
		if($order_list->empId != $this->session->userdata['logged_in_timesheet']['empId'] || $this->session->userdata['logged_in_timesheet']['user_type'] == 'admin'): 
			
				$approveUnApproveStatus ='<span id="changeStatusRow_'.$order_list->emp_record_id.'"><a href="#" class="'.$classStatusVal.'" data-toggle="modal" data-target="#status-pop-modal" title="Click To '.$statusValueShow.'" data-id="'.$order_list->emp_record_id.'" id="empUpdateStatus" > '.$order_list->status.'</a></span>';
		
		
		
			else:
		
			    $approveUnApproveStatus ='<span class="'.$classStatusVal.'"> '.$order_list->status.'</span>';
		
		endif; 
		
		if($order_list->empId == $this->session->userdata['logged_in_timesheet']['empId'] && $order_list->status !='Approved'): 
		
				
			$actionLinks = '<a href="#" data-toggle="modal" data-target="#view-modal" data-id="'.$order_list->emp_record_id.'" id="empViewData" ><i class="fa fa-history" aria-hidden="true"></i></a> | <a href="'.base_url().'empreports/add/'.$order_list->emp_record_id.'/'.$order_list->project_Id.'/'.$order_list->client_Id.'" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a> | <a style="cursor:pointer;" data-toggle="tooltip" title="Delete" onClick="delete_emp_record('.$order_list->emp_record_id.','.$no.')"><i class="fa fa-sm fa-trash"></i></a>';
		
		
		else:
		      
				$actionLinks = '<a href="#" data-toggle="modal" data-target="#view-modal" data-id="'.$order_list->emp_record_id.'" id="empViewData" ><i class="fa fa-history" aria-hidden="true"></i></a>';
		
		endif;
		
		$row = array();
        $row[] = $no;
		$row[] = '<span class="label label-info">'.ucfirst($order_list->name).'</span>';
        $row[] = ucfirst($order_list->client_name);
        $row[] = ucfirst($order_list->project_name);
		$row[] = '<a href="#"  data-toggle="tooltip" title="'.$getListOfProjects.'">'.character_limiter($getListOfProjects,20).'</a>';
		$row[] = $order_list->emp_time_hours;
        $row[] = $approveUnApproveStatus;
        $row[] = '<b>'.date("d-M-Y",strtotime($order_list->emp_report_dates)).'</b>';
        $row[] = $actionLinks;
        
		
		return $row;
		
		
		
    }
	
	
	public function empViewDetails(){
		
		
		    $displayPOPID = $this->input->post('empViewId');
			
			if(!empty($displayPOPID)):
				
				$data['viewEmpTaskDetails'] = $this->emptimelog_model->viewEmpTimeLogTaskDetails($displayPOPID);
		        
				$this->load->view('employee_reports/employee_view_details' , $data);
		   
		    endif;
	}
	
	public function empStatusPopup(){
		
		
			$displayStatusPOPID = $this->input->post('empstatusUpdateId');
			
			if(!empty($displayStatusPOPID)):
				
				$data['viewEmpStatisDetails'] = $this->emptimelog_model->viewEmpTimeLogTaskDetails($displayStatusPOPID);
		        
				$this->load->view('employee_reports/employee_status_form' , $data);
		   
		    endif;
		
	}
	
		
   public function update_emp_pm_report_status(){ // Employee Report Log Update Status Function

		    $comment_emp_record_id 	 	 = $this->input->post('comment_emp_record_id');
	        $comment_status 	 	 	 = $this->input->post('comment_status');
		    $status 			 		 = $this->input->post('status');
	  
	   
			if(!empty($comment_emp_record_id)):
			
				$updateStatus = $this->emptimelog_model->update_emp_pm_report_status($comment_emp_record_id,$comment_status,$status);
				//redirect('empreports/pmreportlogs');	
			 
	  		endif;	

    }
	
/********************************** Datatable Sort , Search , Pagination For Employee Added Task Report Log  **************************************/	
	
/*************************************************** Resource Billability Feature *******************************************************************/
	
		public function resource_billability(){ // Resource Billability feature
		
		if(!empty($this->input->post('client_Id'))) :
		
		 $params = array(
            'client_Id' => $this->input->post('client_Id'),
            'project_Id' => $this->input->post('project_Id'),
            'form_date' => $this->input->post('form_date'),
            'to_date' => $this->input->post('to_date'),            
            );
		
         $data['resultTimeLog'] = $this->emptimelog_model->searchResourceBillability($params);     
        
		 $this->load->view('employee_reports/resource_billability' , $data);
		 
	   else : 
	   
	       $this->load->view('employee_reports/resource_billability');
	   
	   endif; 
		
	}
	
	
	public function pdfResourceBillable(){
		
		
		$params = array(
            'client_Id' => $this->input->get('client_Id'),
            'project_Id' => $this->input->get('project_Id'),
            'form_date' => $this->input->get('form_date'),
            'to_date' => $this->input->get('to_date'),            
            );
		
		
		
		$data['resouceBillabilityPdfResult'] = $this->emptimelog_model->searchResourceBillability($params);     
        
		// Load all views as normal
		$this->load->view('employee_reports/resouce_billability_pdf.php' ,$data);
		// Get output html
		$html = $this->output->get_output();
		
		// Load library
		$this->load->library('dompdf_gen');
		
		// Convert to PDF
		$this->dompdf->load_html($html);
		$this->dompdf->render();
		$this->dompdf->stream("resource_billability_".time().".pdf");
		
		
	}
/*************************************************** Resource Billability Feature *******************************************************************/	
	
/*******************  We are sending emails from Unapproved PM's List in Pm_groups emails  And also same as in Team Member As well  *******************/	
	
	public function email_unapproved_pms(){
		
		  /* $params = array(
                'user_status_type' 	=> $this->input->post('user_status_type'),
                'form_date' 		=> $this->input->post('form_date'),
                'to_date' 			=> $this->input->post('to_date'),            
            ); */
		
		$user_status_type 			 = $this->input->post('user_status_type');
		
        $form_date					 = $this->input->post('form_date');
		
        $to_date 					 = $this->input->post('to_date');
		
        $weeklyeamilReport			 = $this->input->post('weeklyeamil_report'); //sendunapprovedreports_weekly_email
		
        $managerList = $this->db->select('emp.empId,emp.name,emp.user_type,emp.email')->from('employee_details emp')
		 															->where('emp.user_type',$user_status_type)
                                                                    ->where('emp.status ' , 'Active')
		 															->order_by('emp.empId','desc')->get()->result(); 
	  
	 $getEmailIds = array();
		
	 foreach($managerList as $listOfMg){  // List of Project Managers
		  
		 	$managerIds = $listOfMg->empId; // Manager Ids
		 
		   if($user_status_type == 'developer'){
               
                 $listOfMgProjects = $this->db->select('p.empId,p.name')->from('employee_details p')
		 								 ->where('p.empId',$managerIds)
		 								 ->order_by('p.empId','desc')->get()->result();
               
                 foreach($listOfMgProjects as $managerLOP){ // List of Projects Based on Project Managers

				   $pId = $managerLOP->empId; // Project Id's

					$resourceBBQ = $this->db->select('er.status,er.project_Id,er.empId,er.emp_report_dates')
											->from('emp_record_details er')->where('er.empId  = ',$pId)		
											->where('er.emp_report_dates >= ',$form_date)->where('er.emp_report_dates <= ',$to_date)
											 ->order_by('er.emp_record_id','desc')->get()->result();
				  
				   
                     if(empty($resourceBBQ) > 0 ){                       
                          
                         
                         $pMCreatedProjectList  = $this->db->select('emp.name,emp.user_type,emp.email')
								 							->from('employee_details emp')
								 							->where('emp.empId',$pId)
								 							->order_by('emp.empId','desc')->get()->result();
                            
					      foreach($pMCreatedProjectList as $unapprovedPMSList){
							 		
                                    $getEmailIds[] = $unapprovedPMSList->name;
				               
						  }
                         
                         
                     }
                  
			  }	
            
           
           }else{
               
              $listOfMgProjects = $this->db->select('p.project_Id,p.project_name')->from('project_details p')
		 								 ->where('p.empId',$managerIds)
		 								 ->order_by('p.project_Id','desc')->get()->result();
		  
		 
			  foreach($listOfMgProjects as $managerLOP){ // List of Projects Based on Project Managers

				   $pId = $managerLOP->project_Id; // Project Id's

					$resourceBBQ = $this->db->select('er.status,er.project_Id')
											->from('emp_record_details er')->where('er.project_Id  = ',$pId)		
											->where('er.emp_report_dates >= ',$form_date)->where('er.emp_report_dates <= ',$to_date)
											->where('er.status','Unapproved')
											->order_by('er.emp_record_id','desc')->get()->result();
				  
				   foreach($resourceBBQ as $getFinalStatusResult){
					   
					   	
					   		 $pMCreatedProjectList  = $this->db->select('p.project_Id,p.project_name,emp.name,emp.user_type,emp.email')
								 							->from('project_details p')
								 							->join('employee_details as emp', 'emp.empId=p.empId', 'left')
		 								 					->where('p.project_Id',$getFinalStatusResult->project_Id)
								 							->group_by('p.project_Id')
								 							->order_by('p.project_Id','desc')->get()->result();
					   
					        
					      foreach($pMCreatedProjectList as $unapprovedPMSList){
							 		
							  
							   /* $getEmailIds[] = array(
												
													"name" =>  $unapprovedPMSList->name,
													"email" => $unapprovedPMSList->email,
													"status" => $getFinalStatusResult->status,
																						
											);
											
								*/
							  
							    //$getEmailIds[] = $unapprovedPMSList->name.'--'.$unapprovedPMSList->email;
							  
							     $getEmailIds[] = $unapprovedPMSList->name;
							  
						  }
					   
					  	
					   
				   }
				  

			  }	 
               
            } // Else If CLOSED ###################   
		 	    
	   }
	 
		$data['unapprovedRLResult'] = array_unique($getEmailIds);		
		
		if(!empty($weeklyeamilReport)){ // send email function			
				
				$this->sendEmailUnapprovedManagersList(array_unique($getEmailIds));
			    
				$to_date = $this->input->post('to_date');
    			
				$data = array(
						'weekend_date'=>$to_date,
						'sent_email_status'=>'1',
						'created_at'=>date('Y-m-d H:i:s'),
					);

					   $this->db->insert('weekly_email_report_log',$data);			
			
					} //end
		
			$this->load->view('employee_reports/email_unapproved_pms_tms' , $data);
	}
	
	
	public function sendEmailUnapprovedManagersList($unapprovedRLResult){ 
		
		 //echo '<pre>'; print_r($unapprovedRLResult); exit;
		
		
		    //$to_email = 'itteam@elogictech.com'; // mail IDs
		     $config['mailtype'] = 'html';
            $config['charset'] = 'iso-8859-1';
            $config['wordwrap'] = TRUE;
            $config['newline'] = "\r\n"; //use double quotes
            $this->email->initialize($config);                        
	        //send mail 
            //send mail  				
			$this->email->from('sales@elogictech.com', 'eLogic Timesheet');
			$this->email->to('vamsikrishna@elogictech.com,afsar@elogictech.com,sandeep@elogictech.com,sivakrishna@elogictech.com,srinivasg@elogictech.com,laxmikanth@elogictech.com');
            $this->email->cc('rupali@elogictech.com,jaishree@elogictech.com,itteam@elogictech.com');
			$this->email->subject('Approve the timesheets' , 'eLogic Timesheet');
			 $body = '<!doctype html><html><head><meta charset="utf-8"><title>eLogic Tech</title></head><body style="width: 95%; margin: 0 auto; background: #f1f1f1; border:1px solid #888; padding: 0 1% 2% 1% "><div align="left" style="margin: 3% auto 2% 6%;"> <img src="http://www.elogictechsolutions.com/assets/images/logo.png" style="width: 180px;"> </div><div style="background: #004b88; padding: 2%; border-radius: 15px; margin-top: 3%;"> <section style="background: #004b88; border-radius: 6px; padding-top: 2%; font-size: 17px;"> <div style=" color: #fff; margin:2% auto 0px auto; padding-left: 6%;">Dear Team,</div><div align="left" style=" margin: 1% auto; padding-left: 6%; line-height: 24px; color: #fff;"><table> <tbody>';			 
			 foreach($unapprovedRLResult as $sendUnapprovedPMList){				 
				 
				 $body .='<tr><td width="10%">&nbsp;</td> <td> '.$sendUnapprovedPMList.'</td> </tr> ';			 
				 
			 }
			 $body .=' </tbody> </table></div> <div align="left" style=" margin: 1% auto; padding-left: 6%; line-height: 24px; color: #fff;"> <p>Please Approve the timesheets</p></div><div align="left" style=" margin: 3% auto 0 6%; line-height: 24px; color: #fff; ">Thanks & Regards, <br> <div style="color: #fff; padding-bottom: 4%;">Timesheet Admin</div> </div> </section></div></body></html>';
                
				$this->email->message($body);
            
			    $this->email->send();
		 
		
	}
	
/*******************  We are sending emails from Unapproved PM's List in Pm_groups emails  And also same as in Team Member As well  *******************/
    
/***************************************** List of Employee report logs Approvals functionality *****************************************************/
    
    
    
        
        public function allEmpApproveList(){
            
            $approvedIds = $this->input->post('approvedIds');
            
            if(!empty($approvedIds)){
                $this->emptimelog_model->approveCheckedEmpReports($approvedIds);
            }
            
            echo 'ok';
      }
    
        
/***************************************** List of Employee report logs Approvals functionality *****************************************************/        
	
}
