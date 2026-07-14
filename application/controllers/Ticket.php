<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ticket extends CI_Controller {

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
        
        $this->load->model('ticket_model');
		
		$this->load->model('project_model');
        
		if(empty($this->session->userdata['logged_in_timesheet'])){
		
			redirect('home/login');
		}
		
    }
	
	public function index(){
		$this->load->library('pagination');

		$form_date = $this->input->get_post('form_date');
		$to_date = $this->input->get_post('to_date');
		$search = $this->input->get('search') ? trim($this->input->get('search')) : '';
		$sort_col = $this->input->get('sort') ? $this->input->get('sort') : 'ticket_id';
		$sort_dir = $this->input->get('order') ? $this->input->get('order') : 'desc';
		$per_page = 15;
		$page = max(1, (int) $this->input->get('page'));

		$query_params = array();
		if (!empty($form_date)) $query_params['form_date'] = $form_date;
		if (!empty($to_date)) $query_params['to_date'] = $to_date;
		if ($search !== '') $query_params['search'] = $search;
		if ($sort_col !== 'ticket_id') $query_params['sort'] = $sort_col;
		if ($sort_dir !== 'desc') $query_params['order'] = $sort_dir;
		$base_query = empty($query_params) ? 'ticket' : 'ticket?' . http_build_query($query_params);

		if (!empty($form_date) && !empty($to_date)) {
			$reportIT = array('form_date' => $form_date, 'to_date' => $to_date);
			$total_rows = $this->ticket_model->getItReportsCount($reportIT, $search);
			$offset = ($page - 1) * $per_page;
			$data['getTicketsInfo'] = $this->ticket_model->getItReportsPaginated($reportIT, $per_page, $offset, $search, $sort_col, $sort_dir);
		} else {
			$total_rows = $this->ticket_model->getTicketsCount($search);
			$offset = ($page - 1) * $per_page;
			$data['getTicketsInfo'] = $this->ticket_model->getTicketsPaginated($per_page, $offset, $search, $sort_col, $sort_dir);
		}

		$config['base_url'] = base_url($base_query) . (strpos($base_query, '?') !== false ? '&' : '?');
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $per_page;
		$config['use_page_numbers'] = true;
		$config['page_query_string'] = true;
		$config['query_string_segment'] = 'page';
		$config['full_tag_open'] = '<ul class="pagination pagination-sm">';
		$config['full_tag_close'] = '</ul>';
		$config['first_tag_open'] = $config['last_tag_open'] = $config['next_tag_open'] = $config['prev_tag_open'] = '<li>';
		$config['first_tag_close'] = $config['last_tag_close'] = $config['next_tag_close'] = $config['prev_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		$data['pagination_links'] = $this->pagination->create_links();
		$data['form_date'] = $form_date;
		$data['to_date'] = $to_date;
		$data['search'] = $search;
		$data['sort_col'] = $sort_col;
		$data['sort_dir'] = $sort_dir;
		$data['row_offset'] = $offset;
		$data['total_records'] = $total_rows;
		$data['per_page'] = $per_page;
		$data['current_page'] = $page;

		$this->load->view('tickets/list_of_tickets', $data);
	}
	
	public function add($ticket_id = NULL){
	
	   if(empty($ticket_id)) : 
			
			 $this->load->view('tickets/add_ticket');
			 
		else:
			
			 $data['updateTicket'] = $this->ticket_model->getUpdateTicketDetails($ticket_id);
	
		     $this->load->view('tickets/update_ticket' , $data);
					
		endif;	   
			
	 
	}
    
    public function developerEditForm($ticket_id = NULL){
	
	   if(!empty($ticket_id)) : 
			
		 $data['updateTicket'] = $this->ticket_model->getUpdateTicketDetails($ticket_id);
	
		     $this->load->view('tickets/developer_update_ticket' , $data);
					
			
			
		endif;	   
			
	 
	}
    
    public function viewticket($ticket_id = NULL){
        
        if(!empty($ticket_id)):
        
        $data['viewdetails'] = $this->ticket_model->getUpdateTicketDetails($ticket_id);
	
		     $this->load->view('tickets/view_ticket' , $data);
        
        endif;
        
    }
	
	public function addticket(){ // Adding new Client function. 	
			
			date_default_timezone_set('Asia/Kolkata');
        
            $currentTime = date( 'Y-m-d h:i:s A', time () ); //Ticket Created Date
		
		    $ticket_open_time = date('G:i');

            if(!empty($this->ticketUploadImage())){
				
				$ticketImageSavtodb = $this->ticketUploadImage();
				
			}else{
				
				$ticketImageSavtodb = '';
			}

            
        $data = array(
                'emp_id' 				     => $this->input->post('emp_id'),
                'emp_emailId' 				 => $this->input->post('emp_emailId'),
                'ticket_username' 			 => $this->input->post('ticket_username'),
                'ticket_name' 				 => $this->input->post('ticket_name'),
                'ticket_desc' 				 => $this->input->post('ticket_desc'),
                'ticket_priority' 		     => $this->input->post('ticket_priority'),
                'ticket_raised_date'		 => $this->input->post('ticket_raised_date'),
                'ticket_status'				 => $this->input->post('ticket_status'),
                'ticket_upload_image'	     => $ticketImageSavtodb,
				'ticket_open_time'			 =>  $ticket_open_time,
                'created_by'    			 => $currentTime,
                'updated_by' 		 		 => $currentTime
			);
        
			$config['mailtype'] = 'html';
            $config['charset'] = 'iso-8859-1';
            $config['wordwrap'] = TRUE;
            $config['newline'] = "\r\n"; //use double quotes
            $this->email->initialize($config);                        
	        //send mail 
            $this->email->from('info@elogictech.com', 'eLogic Timesheet');
			$this->email->to('itteam@elogictech.com');
			$this->email->subject('Ticket System' , 'eLogic Timesheet');
		
		
			  $contact_body = '<!doctype html><html><head><meta charset="utf-8"><title>eLogic Tech</title></head><body style="width: 95%; margin: 0 auto; background: #f1f1f1; border:1px solid #888; padding: 0 1% 2% 1% ">
    <div align="left" style="margin: 3% auto 2% 6%;"> <img src="https://www.elogictech.com/assets/frontend/images/logo.png" style="width: 180px;"></div>
    <div style="background: #004b88; padding: 2%; border-radius: 15px; margin-top: 3%;">
        <section style="background: #004b88; border-radius: 6px; padding-top: 2%; font-size: 17px;">
            <div style=" color: #fff; margin:2% auto 0px auto; padding-left: 6%;">Dear Team, </div>
            <div align="left" style=" margin: 1% auto; padding-left: 6%; line-height: 24px; color: #fff;">
                <p>I have raised the ticket on '.date('d-F-Y',strtotime($this->input->post('ticket_raised_date'))).'. </p>
            </div>
            <div align="left" style=" margin: 1% auto; padding-left: 6%; line-height: 24px; color: #fff;">
                <table>
                    <tbody>
                        <tr>
                            <td> Ticket Type : </td>
                            <td> '.$this->input->post('ticket_name').' </td>
                        </tr>
                        <tr>
                            <td> Ticket Description : </td>
                            <td> '.$this->input->post('ticket_desc').' </td>
                        </tr>
                        <tr>
                            <td> Status : </td>
                            <td> '.$this->input->post('ticket_status').' </td>
                        </tr>
                        <tr>
                            <td>Raised Date : </td>
                            <td> '.date('d-F-Y',strtotime($this->input->post('ticket_raised_date'))).' </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div align="left" style=" margin: 3% auto 0 6%; line-height: 24px; color: #fff; ">Thanks & Regards, <br>
                <div style="color: #fff; padding-bottom: 4%;">'.ucwords($this->input->post('ticket_username')).' </div>
            </div>
        </section>
    </div>
</body>

</html>';        
        	    
        
        if(!empty($this->input->post('emp_id'))){ 
            
         $this->email->message($contact_body);

        $this->email->send();
        
        }
        
         $this->ticket_model->add_ticket($data);
		
		 redirect('ticket');
		
	  
	
	}
	
	
	public function updateticket(){ // Adding new Client function. 	
			
		
		$ticket_id = $this->input->post('ticket_id');
        
        $emp_emailId = $this->input->post('emp_emailId');
        
        date_default_timezone_set('Asia/Kolkata');
        
        $updateDateTime = date( 'Y-m-d h:i:s A', time () ); //Ticket Created Date
		
		if($this->input->post('ticket_status') == 'Completed'){
			
			$ticket_completed_time = date('G:i');
			
		}else{
			
				$ticket_completed_time = '';
		}
		
		

        if(!empty($this->ticketUploadClosedImage())){
				
				$ticketImageClosedSavtodb = $this->ticketUploadClosedImage();
				
			}else{
				
				$ticketImageClosedSavtodb = '';
			}
        
			$data = array(
                'ticket_responsibility' 	 => $this->input->post('ticket_responsibility'),
                'ticket_status' 			 => $this->input->post('ticket_status'),
                'ticket_closed_date' 		 => $this->input->post('ticket_closed_date'),
                'ticket_closed_info' 		 => $this->input->post('ticket_closed_info'),
                'ticket_closed_upload_image' => $ticketImageClosedSavtodb,  
				'ticket_completed_time'		 => $ticket_completed_time,
				'updated_by' 		 		 => $updateDateTime
			);		
	
         
           $config['mailtype'] = 'html';
            $config['charset'] = 'iso-8859-1';
            $config['wordwrap'] = TRUE;
            $config['newline'] = "\r\n"; //use double quotes
            $this->email->initialize($config);                        
	        //send mail 
            $this->email->from('info@elogictech.com', 'eLogic Timesheet');
			$this->email->to($emp_emailId);
			$this->email->subject('Ticket System' , 'eLogic Timesheet');
		
			 $body = '<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>eLogic Tech</title>
</head>
<body style="width: 95%; margin: 0 auto; background: #f1f1f1; border:1px solid #888; padding: 0 1% 2% 1% ">
    <div align="left" style="margin: 3% auto 2% 6%;"> <img src="https://www.elogictech.com/assets/frontend/images/logo.png" style="width: 180px;"> </div>
    <div style="background: #004b88; padding: 2%; border-radius: 15px; margin-top: 3%;">
        <section style="background: #004b88; border-radius: 6px; padding-top: 2%; font-size: 17px;">
            <div style=" color: #fff; margin:2% auto 0px auto; padding-left: 6%;">Dear Team, </div>
            <div align="left" style=" margin: 1% auto; padding-left: 6%; line-height: 24px; color: #fff;">
                <p>Ticket Status from "'.ucfirst($this->input->post('ticket_status')).'" on '.date('d-F-Y',strtotime($this->input->post('ticket_closed_date'))).'. Please review</p>
            </div>
            <div align="left" style=" margin: 1% auto; padding-left: 6%; line-height: 24px; color: #fff;">
                <table>
                    <tbody>
                        <tr>
                            <td> Responsibility  : </td>
                            <td> '.ucfirst($this->input->post('ticket_responsibility')).' </td>
                        </tr>
                        <tr>
                            <td> Status  : </td>
                            <td> '.$this->input->post('ticket_status').' </td>
                        </tr>                       
                        <tr>
                            <td> Ticket Closed Date : </td>
                            <td> '.date('d-F-Y',strtotime($this->input->post('ticket_closed_date'))).' </td>
                        </tr>
                        <tr>
                            <td> Ticket Closed Update  : </td>
                            <td> '.ucfirst($this->input->post('ticket_closed_info')).' </td>
                        </tr>
                        <tr>
                            <td align="top" style="position: relative; top: 0px;"> Issue Information : </td>
                            <td> '.$this->input->post('ticket_desc').'. </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div align="left" style=" margin: 3% auto 0 6%; line-height: 24px; color: #fff; ">Thanks & Regards, <br>
                <div style="color: #fff; padding-bottom: 4%;">IT Team</div>
            </div>
        </section>
    </div>
</body>
</html>';
        
	
	    
    if(!empty($this->input->post('ticket_closed_date'))){     
		
        if(!empty($data)){ 
             
            //$this->email->message($body);

           // $this->email->send();
         
        }
    }
        
	     $this->ticket_model->update_ticket($data , $ticket_id);
		
		 redirect('ticket');
	
	}
    
 public function updatedeveloperform(){
     
     
     $ticket_id = $this->input->post('ticket_id');
     
        if($this->input->post('ticket_status') == 'Closed'){
			
			date_default_timezone_set('Asia/Kolkata');
			
			$ticket_closed_time = date('G:i');
			
		}else{
			
				$ticket_closed_time = '';
		}
	 
	 
         $data = array(

                    'ticket_name' 				 => $this->input->post('ticket_name'),
                    'ticket_desc' 				 => $this->input->post('ticket_desc'),
                    'ticket_priority' 		     => $this->input->post('ticket_priority'),
                    'ticket_raised_date'		 => $this->input->post('ticket_raised_date'),
                    'ticket_status'				 => $this->input->post('ticket_status'),
			 		'ticket_closed_time'		 => $ticket_closed_time
                    
                );
     
          $this->ticket_model->update_ticket($data , $ticket_id);
		
		  redirect('ticket');
     
 }    

  public function delete(){
  
        $project_Id  = $this->input->post('project_Id');
		   
			if(!empty($project_Id)):
			
				$del = $this->project_model->delete_project($project_Id);
				
			endif;	
  
  }
  
  
  public function getRecentProjects(){  //Get Recent Clients Angular js funciton  
  
    	$recentProjectInfo = $this->project_model->recentProjects();
		
		echo json_encode($recentProjectInfo);
  
  }
	
	
	
  #uniqueness of task based on client and projects
    function exists_projects($str){ #uniqueness of Car Model
	
        $client_Id = $this->input->post('client_Id');
		
		$project_name = $this->input->post('project_name');
		
		$query = $this->db->get_where('project_details',array('project_name'=>$project_name,'client_Id'=>$client_Id));
	
		$countClientProject = $query->num_rows(); 
		
        if ($countClientProject  == 0){
		
            return TRUE;
			
        }else{
		
            $this->form_validation->set_message('exists_projects', 'Project name already exit particular client. Please try another project!');
            
			 return FALSE;
        }
    }	
	
/******************************************** Ticket System Report Generation Form and To Date   START ********************/

	
	  public function ticketSystemReport(){      
       
	    $this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle('Ticket System Report');
		//set cell A1 content with some text
		$this->excel->getActiveSheet()->setCellValue('A1', 'Ticket System Report');
	    $this->excel->getActiveSheet()->setCellValue('A2', 'Sno');
	    $this->excel->getActiveSheet()->setCellValue('B2', 'Created By');
		$this->excel->getActiveSheet()->setCellValue('C2', 'Ticket Type');
		$this->excel->getActiveSheet()->setCellValue('D2', 'Priority');
		$this->excel->getActiveSheet()->setCellValue('E2', 'Description');
		$this->excel->getActiveSheet()->setCellValue('F2', 'Status');
		$this->excel->getActiveSheet()->setCellValue('G2', 'Responsibility');
		$this->excel->getActiveSheet()->setCellValue('H2', 'Ticket Raised Date');
		$this->excel->getActiveSheet()->setCellValue('I2', 'Open Time'); 
	    $this->excel->getActiveSheet()->setCellValue('J2', 'Ticket Closed Date');
		$this->excel->getActiveSheet()->setCellValue('K2', 'Completed Time'); 
		$this->excel->getActiveSheet()->setCellValue('L2', 'Duration'); 
		$this->excel->getActiveSheet()->setCellValue('M2', 'Ticket Closed Info');
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
		$this->excel->getActiveSheet()->getStyle('K2')->getFont()->setSize(14)->setBold(true); 
		$this->excel->getActiveSheet()->getStyle('L2')->getFont()->setSize(14)->setBold(true); 
		$this->excel->getActiveSheet()->getStyle('M2')->getFont()->setSize(14)->setBold(true); 
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
        
        
		 $reportIT = array(           
            
			'form_date' => $this->input->get('form_date'),
            'to_date' => $this->input->get('to_date'), 
			 
            );
		
	    
        //$reportStatus = ' Unapproved';
			
			
	  
        $exportDataInformation = $this->ticket_model->getItReports($reportIT);  // this will return all data into array
    			
	    $exceldata="";
		
        $sno = 0;
		
        foreach ($exportDataInformation as $row){ $sno++; 
												 
			
			if(!empty($row->ticket_completed_time)){
						
					$time    = str_replace(":",".",$row->ticket_open_time);
				
					$time_1  = str_replace(":",".",$row->ticket_completed_time);
				
				    $tOpenDT = $row->ticket_raised_date.' '.$row->ticket_open_time;
						
					$tClosedDT = $row->ticket_closed_date.' '.$row->ticket_completed_time;
						
						// Convert string dates to Unix timestamps
					$timestamp1 = strtotime($tOpenDT);
					$timestamp2 = strtotime($tClosedDT);

					// Calculate the difference in seconds
					$timeDiffSeconds = $timestamp2 - $timestamp1;

					// Convert seconds to hours and minutes
					$hours = floor($timeDiffSeconds / 3600); // 1 hour = 3600 seconds
					$minutes = ($timeDiffSeconds % 3600) / 60; // 1 minute = 60 seconds
 
						// Display the result
							$solvedActualTime =  " " . ($hours < 0 ? '-' : '') . abs($hours) . "." . abs($minutes) . "";

					}else{
						
						$solvedActualTime = '';
					}
                  
												 
												 
		 
		    
			$arrangeData['Sno'] 	 	      		= $sno;
			$arrangeData['Created By'] 	  	  		= $row->ticket_username;
			$arrangeData['Ticket Type'] 	  		= $row->ticket_name;
			$arrangeData['Priority']	 	  		= $row->ticket_priority;
			$arrangeData['Description'] 	  		= $row->ticket_desc;
			$arrangeData['Status']		 	  		= $row->ticket_status;
			$arrangeData['Responsibility'] 		  	= ucfirst($row->ticket_responsibility);
			$arrangeData['Ticket Raised Date'] 		= $row->ticket_raised_date;
			$arrangeData['Open Time'] 				= $time;
			$arrangeData['Ticket Closed Date'] 	    = $row->ticket_closed_date;
			$arrangeData['Completed Time'] 	    	= $time_1;
			$arrangeData['Duration'] 	    		= $solvedActualTime;
			$arrangeData['Ticket Closed Info'] 	    = $row->ticket_closed_info;									 
												 
	
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
	  			$this->excel->getActiveSheet()->getStyle('K2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	  			$this->excel->getActiveSheet()->getStyle('L2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	  			$this->excel->getActiveSheet()->getStyle('M2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                 //$time = time();
                $filename="ticketsystem_Report_sheet.xls"; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
                //force user to download the Excel file without writing it to server's HD
                $objWriter->save('php://output');
                 
    }
	
	
/******************************************** Ticket System Report Generation Form and To Date   START ********************/		
public function ticketUploadImage($path=null){
    $config['upload_path'] = !empty($path) ? $path : 'uploads/ticket_uploded_images/';
    $config['allowed_types'] = 'gif|jpg|jpeg|png|GIF|JPG|PNG|JPEG'; 
    $config['file_name'] = time()."_".$_FILES['ticket_upload_image']['name'];
    $config['overwrite']     = false;
    $config['max_size']	 = '1000000000000000';
     //$this->upload->initialize($config);
       // echo "<pre>";print_r($config);exit;
     $this->load->library('upload', $config);
   //Load upload library and initialize configuration
            if($this->upload->do_upload('ticket_upload_image')){
                $uploadData = $this->upload->data();
                return $filename = $uploadData['file_name']; 
            }
//       echo "<pre>";print_r($config); exit;
}



public function ticketUploadClosedImage($path=null){
    $config['upload_path'] = !empty($path) ? $path : 'uploads/ticket_uploded_images/ticket_closed_img';
    $config['allowed_types'] = 'gif|jpg|jpeg|png|GIF|JPG|PNG|JPEG'; 
    $config['file_name'] = time()."_".$_FILES['ticket_closed_upload_image']['name'];
    $config['overwrite']     = false;
    $config['max_size']	 = '1000000000000000';
     //$this->upload->initialize($config);
       // echo "<pre>";print_r($config);exit;
     $this->load->library('upload', $config);
   //Load upload library and initialize configuration
            if($this->upload->do_upload('ticket_closed_upload_image')){
                $uploadData = $this->upload->data();
                return $filename = $uploadData['file_name']; 
            }
//       echo "<pre>";print_r($config); exit;
}

/***************************************** Ticket System uploading image functionality**************************************/



}
