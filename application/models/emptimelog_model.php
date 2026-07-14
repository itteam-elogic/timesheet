<?php
/**
 * eLogivc Admin Panel for Codeigniter 
 * Author: Laxmikanth 
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Emptimelog_Model extends CI_Model {

    public function __construct() {
	
			parent::__construct();   
	
	}
  

	
	 public function getClientWiseProjects($client_Id){ // Get Dropdown client wise projects 
	
		  
	  $getProjects  = $this->db->select('*')->from('project_details')->where('client_Id', $client_Id)->get()->result();
	  
	   echo '<option value="all">All</option>';
	  
	   foreach($getProjects as $key => $getResult):
	   
	    echo '<option value='.$getResult->project_Id.'>'.$getResult->project_name.'</option>';
	   
	   endforeach;
	 
	  //return $getStates;
		 
	}  // Get Dropdown client wise projects 
	
	
	public function getListOfProjectsWithClient($client_Id){ // Get Dropdown client wise projects  with out all feature
	
		  
	  $getProjects  = $this->db->select('*')->from('project_details')->where('status !=', 'Closed')->where('client_Id', $client_Id)->where('status','Process')->get()->result();
	  
	   echo '<option value="">Please Select Project</option>';
	  
	   foreach($getProjects as $key => $getResult):
	   
        //$hideGeneral = str_replace(" - (General)", "",$getResult->project_name);
        
        //if($hideGeneral == $getResult->project_name){
            
            
	       echo '<option value='.$getResult->project_Id.'>'.$getResult->project_name.'</option>';
            
        
        //}
	   
	   endforeach;
	 
	  //return $getStates;
		 
	}  // Get Dropdown client wise projects 
	
	
	public function getProjectWiseTask($project_Id){ // Get Dropdown Project wise clients
	
			
	 $getTask  = $this->db->select('*')->from('task_details')->where('project_Id', $project_Id)->where('task_Id != 213 AND task_Id != 15704 AND task_Id != 15705 AND task_Id != 15706 AND task_Id != 15876 AND task_Id != 15874 AND task_Id != 12278 AND task_Id != 12277 AND task_Id != 12276 AND task_Id != 3 AND task_Id != 4 AND task_Id != 5' )->where('status', 'Process')->get()->result();
	  
	   foreach($getTask as $key => $getResult):
	   
	    echo '<option value='.$getResult->task_Id.'>'.$getResult->task_name.'</option>';
	   
	   endforeach;
        
		if($project_Id != '5083'){
			
        echo '<option value="12278">Project Coordination</option><option value="12277">Client Communication</option><option value="12276">Team Coordination</option><option value="213">Team Meeting</option><option value="15704">Training</option><option value="15705">Quality Management Review</option><option value="15706">Client Rework</option><option value="5">Internal Rework</option><option value="15874">Project Study</option><option value="15876">Downtime ( System issue / late login )</option><option value="15873">Fun @ Work</option><option value="3">Half Day</option><option value="4">Leave</option>';
			
		}
	
	}
	 
	 
 public function addEmpRecords($data) { // Store Emploee Records into database
  
   if($data):
	   
	   	$this->db->insert_batch('emp_record_details', $data);

		$emp_reportlog_id = $this->db->insert_id();
		
					 
			if(!empty($emp_reportlog_id)){
			
				/*$this->db->select('er.task_Id,er.emp_time_hours,er.comments,er.emp_report_dates,er.status,emp.name,emp.email,emp.user_type,c.client_name,p.project_name');
				$this->db->from('emp_record_details er'); 
				$this->db->from('project_details p');
				$this->db->join('employee_details as emp', 'emp.empId=p.empId', 'left');
				$this->db->join('client_details as c', 'c.client_Id=p.client_Id', 'left');
				//$this->db->where('p.project_Id  = ',$data['project_Id']);
				$this->db->where('er.emp_record_id',$emp_reportlog_id);
				$this->db->order_by('er.emp_record_id','desc');         
				$recordsQ = $this->db->get(); 
			    return $recordsQ->result();*/
				
				$this->db->select('er.task_Id,er.emp_time_hours,er.comments,er.emp_report_dates,er.status,emp.name,emp.email,emp.user_type,c.client_name,c.client_email,p.project_name');
				$this->db->from('emp_record_details er'); 
				//$this->db->from('project_details p');
				$this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
				$this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
				$this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
				$this->db->where('p.project_Id  = ',$data[0]['project_Id']);
				$this->db->where('er.emp_record_id',$emp_reportlog_id);
				$this->db->order_by('er.emp_record_id','desc');         
				$recordsQ = $this->db->get(); 
			    return $recordsQ->result();
				
			}		 
	   
	   endif;
  
  } 
  
  
  public function getRecords($userType){ // List of employee enter the records based on users
  
        if($userType == 'developer'):
		    
			$empId =  $this->session->userdata['logged_in_timesheet']['empId'];
			
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name');
            $this->db->from('emp_record_details er'); 
            $this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
			$this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
            $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
			$this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
            $this->db->where('er.empId',$empId);
            $this->db->order_by('er.emp_record_id','desc');   
			
		elseif($userType == 'manager'):
		
			$empId =  $this->session->userdata['logged_in_timesheet']['empId'];
			
			$getProjectId = $this->getProjects($empId);	
      
			if(!empty($getProjectId)){
                
				    $eProjectIds  = $getProjectId;
                
			}else{
                
				    $eProjectIds  = '';
                
			}
			
			//$exp_projectIds = implode(',' ,$getProjectId);
			
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name');
            $this->db->from('emp_record_details er'); 
            $this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
			$this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
            $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
			$this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
			$this->db->where_in('er.project_Id',$eProjectIds);
			$this->db->or_where('er.empId',$empId);
            $this->db->order_by('er.emp_record_id','desc'); 
		   
		else: 
           
             if($this->session->userdata['logged_in_timesheet']['empId'] == '92'):

                    $this->db->select('er.* ,emp.empId,emp.name,emp.user_type,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name');
                    $this->db->from('emp_record_details er'); 
                    $this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
                    $this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
                    $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
                    $this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
                    $this->db->where_in('er.empId',array('140','384','46','47','183','149'));
                    $this->db->order_by('er.emp_record_id','desc');
	  
	  		elseif($this->session->userdata['logged_in_timesheet']['empId'] == '384'):

                    $this->db->select('er.* ,emp.empId,emp.name,emp.user_type,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name');
                    $this->db->from('emp_record_details er'); 
                    $this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
                    $this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
                    $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
                    $this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
                    $this->db->where_in('er.empId',array('421'));
                    $this->db->order_by('er.emp_record_id','desc');	  		
	  
	  		elseif($this->session->userdata['logged_in_timesheet']['empId'] == '41'):

                    $this->db->select('er.* ,emp.empId,emp.name,emp.user_type,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name');
                    $this->db->from('emp_record_details er'); 
                    $this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
                    $this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
                    $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
                    $this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
                    $this->db->where_in('er.empId',array('53'));
                    $this->db->order_by('er.emp_record_id','desc');
      
            elseif($this->session->userdata['logged_in_timesheet']['empId'] == '448'):

                    $this->db->select('er.* ,emp.empId,emp.name,emp.user_type,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name');
                    $this->db->from('emp_record_details er'); 
                    $this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
                    $this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
                    $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
                    $this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
                    $this->db->where_in('er.empId',array('446','455'));
                    $this->db->order_by('er.emp_record_id','desc');  
      
            elseif($this->session->userdata['logged_in_timesheet']['empId'] == '149'):

                    $this->db->select('er.* ,emp.empId,emp.name,emp.user_type,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name');
                    $this->db->from('emp_record_details er'); 
                    $this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
                    $this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
                    $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
                    $this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
                    $this->db->where_in('er.empId',array('230','146'));
	  				$this->db->order_by('er.emp_record_id','desc'); 
	  
	  			
	  			/* elseif($this->session->userdata['logged_in_timesheet']['empId'] == '146'):

                    $this->db->select('er.* ,emp.empId,emp.name,emp.user_type,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name');
                    $this->db->from('emp_record_details er'); 
                    $this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
                    $this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
                    $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
                    $this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
                    $this->db->where_in('er.empId',array('152','181','157','117'));
	  			    $this->db->order_by('er.emp_record_id','desc'); */

              else:

               $this->db->select('er.* ,emp.empId,emp.name,emp.user_type,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name');
                    $this->db->from('emp_record_details er'); 
                    $this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
                    $this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
                    $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
                    $this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
                    $this->db->where('er.empId != 47 AND er.empId != 230 AND er.empId !=146 AND er.empId !=53 AND er.empId !=421 AND er.empId !=140 AND er.empId !=46 AND er.empId !=149 AND er.empId !=446 AND er.empId !=448 AND er.empId !=183 AND er.empId !=384 AND er.empId !=446 AND er.empId !=455');
                    $this->db->order_by('er.emp_record_id','desc'); 

              endif;	  
      
      
          endif;
	  
		  $recordsQ = $this->db->get(); 
		 //echo 'Project Manager----------------------'.$this->db->last_query();			
		 return $recordsQ->result();
  }
  
  
   public function getUpdateEmpRecords($emp_record_id){ //Get UPdate Records on Particular Users only
   
       $empURQ  	= $this->db->select('*')->from('emp_record_details')->where('emp_record_id' , $emp_record_id)->get();
	  
	   return $empURQ->result();
   
   
   }
   
   public function updateEmpRecords($data , $emp_record_id){ // Update Employee records    
   
   $this->db->where('emp_record_id', $emp_record_id);
		
	    $update = $this->db->update('emp_record_details', $data);
		
		if($update):
			
			  return true; 
			
		endif;
   
   }
   
   public function deleteEmpRecord($emp_record_id){ // Delete Employee Records
   
   
   	$this->db->where('emp_record_id', $emp_record_id)->delete('emp_record_details');
		  
		$deleteQuery = $this->db->affected_rows();
			
	    echo  $deleteQuery;
   
   
   }
	
	
	public function getSearchEmpTimeLog($params){  //
	
		$client_Id 		 = 	 $params['client_Id'];
        $project_Id      =	 $params['project_Id'];
        $form_date		 =	 $params['form_date'];
        $to_date		 = 	 $params['to_date'];
		
		$empId =  $this->session->userdata['logged_in_timesheet']['empId']; // Loged in users		
		
		if($this->session->userdata['logged_in_timesheet']['user_type'] == 'developer') { // Only Search on usertype developer only
		
		if($client_Id == 'all' && $project_Id == 'all') :   // Checking all records based on from and to dates only.
		
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->where('er.empId',$empId)
            ->order_by('er.emp_record_id','desc');
			
			$recordsQ = $this->db->get(); 
			
			//echo 'All---------'.$this->db->last_query();
			
			return $recordsQ->result();
			
			//echo $this->db->last_query();        
			
		elseif($project_Id == 'all'):
		
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where('er.client_Id  = ',$client_Id)		
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->where('er.empId',$empId)
            ->order_by('er.emp_record_id','desc');
			
			$recordsQ = $this->db->get(); 
			//echo '--project--'.$this->db->last_query(); 
			return $recordsQ->result();
			
			//echo $this->db->last_query();   	
			 
        
		else:
		    
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where('er.client_Id  = ',$client_Id)			
			->where('er.project_Id = ',$project_Id)
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->where('er.empId',$empId)
            ->order_by('er.emp_record_id','desc');
			
			$recordsQ = $this->db->get(); 
			
			//echo 'Particula'.$this->db->last_query();  
			
			return $recordsQ->result();
		
		endif;
		
		
	  }
	  
	  elseif($this->session->userdata['logged_in_timesheet']['user_type'] == 'manager') { // Only Search on usertype manager only it's get it's particular project details only....
	  
		$empId =  $this->session->userdata['logged_in_timesheet']['empId'];
			
			$getProjectId = $this->getProjects($empId);			
			if(!empty($getProjectId)){
				$eProjectIds  = $getProjectId;
			}else{
				$eProjectIds  = '';
		}
		
		if($client_Id == 'all' && $project_Id == 'all') :   // Checking all records based on from and to dates only.
		 $this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
			->where_in('er.project_Id',$eProjectIds)
			->or_where('er.empId',$empId)
            ->order_by('er.emp_record_id','desc');
			
			$recordsQ = $this->db->get(); 
			
			//echo 'All---------'.$this->db->last_query();
			
			return $recordsQ->result();
			
			//echo $this->db->last_query();        
			
		elseif($project_Id == 'all'):
		
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where('er.client_Id  = ',$client_Id)		
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->where_in('er.project_Id',$eProjectIds)
            ->order_by('er.emp_record_id','desc');
			
			$recordsQ = $this->db->get(); 
			//echo '--project--'.$this->db->last_query(); 
			return $recordsQ->result();
			
			//echo $this->db->last_query();   	
			 
        
		else:
		    
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where('er.client_Id  = ',$client_Id)			
			->where('er.project_Id = ',$project_Id)
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->where_in('er.project_Id',$eProjectIds)
            ->order_by('er.emp_record_id','desc');
			
			$recordsQ = $this->db->get(); 
			
			//echo 'Particula'.$this->db->last_query();  
			
			return $recordsQ->result();
		
		endif;
	  
	  
	  
	  }else{ // Search for Manager and Admin 
	  
	  
	 		if($client_Id == 'all' && $project_Id == 'all') :   // Checking all records based on from and to dates only.
		
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.emp_record_id','desc');
			
			$recordsQ = $this->db->get(); 
			
			//echo 'All---------'.$this->db->last_query();
			
			return $recordsQ->result();
			
			//echo $this->db->last_query();        
			
		elseif($project_Id == 'all'):
		
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where('er.client_Id  = ',$client_Id)		
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.emp_record_id','desc');
			
			$recordsQ = $this->db->get(); 
			//echo '--project--'.$this->db->last_query(); 
			return $recordsQ->result();
			
			//echo $this->db->last_query();   	
			 
        
		else:
		    
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where('er.client_Id  = ',$client_Id)			
			->where('er.project_Id = ',$project_Id)
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.emp_record_id','desc');
			
			$recordsQ = $this->db->get(); 
			
			//echo 'Particula'.$this->db->last_query();  
			
			return $recordsQ->result();
			
	    endif;
	  
	  }	
		
	
	} // Employee and Admin Search Report Log END
	
	
	public function updateChangePassword($password , $employeeName ){ // Employee can change password
	
	 
	   $update =  $this->db->set('password', md5(strtolower($password)))->where('username', $employeeName)->update('employee_details');  //table name
	  
	   if($update):
			
			  return true; 
			
		endif;
		
	   
	}
	
	
	/* public function searchProjectWiseTask($project_Id){ // Get Dropdown Project wise clients
	
			
	 $getTask  = $this->db->select('*')->from('task_details')->where('project_Id', $project_Id)->get()->result();
	  
	    echo '<option value="">Please Select Task</option> <option value="all">All</option>';
	   
	   foreach($getTask as $key => $getResult):
	   
	    echo '<option value='.$getResult->task_Id.'>'.$getResult->task_name.'</option>';
	   
	   endforeach;
	
	} */
    
    public function searchProjectWiseTask($project_Id,$client_Id){ // Get Dropdown Project wise clients
	
       
      /*  if($project_Id == 'all'):
			
	           $getTask  = $this->db->select('*')->from('task_details')->where('client_Id', $client_Id)->get()->result();
        else:
        
              $getTask  = $this->db->select('*')->from('task_details')->where('project_Id', $project_Id)->get()->result();
	  endif;
	    echo '<option value="all">All</option>';
	   
	   foreach($getTask as $key => $getResult):
	   
	    echo '<option value='.$getResult->task_Id.'>'.$getResult->task_name.'</option>';
	   
	   endforeach; */
		
		
		
		$getProjectGeneralResult  = $this->db->select('*')->from('project_details')->where('project_Id', $project_Id)->get()->result();
		
		foreach($getProjectGeneralResult as $key => $pGeneral):
		
				$hideGeneral = str_replace(" - (General)", "",$pGeneral->project_name);
		
			endforeach;
		
		$getTask  = $this->db->select('*')->from('task_details')->where('project_Id', $project_Id)->get()->result();
	  
        echo '<option value="all">All</option>';
		
		if($hideGeneral.' - (General)' == $pGeneral->project_name): // Project General related Task List
		
			echo '<option value="18580">Team Meeting</option>
					<option value="18581">Quality Management Review</option>
					<option value="18582">Client Rework</option>
					<option value="18583">Internal Rework</option>
					<option value="18584">Client Communication</option>	
					<option value="18585">Project Coordination</option>
					<option value="18586">Project Study</option>';
		
		elseif('General' == $pGeneral->project_name): // PM wise General Related Task List
		
				echo '<option value="18587">One On One</option>
						<option value="18588">Skip Meeting</option>
						<option value="18589">Fun @ Work</option>
						<option value="18590">Downtime (i.e System issue / late login/Early logout / half a day )</option>
						<option value="18591">Training</option>
						<option value="21446">New Joinee Training</option>
						<option value="18592">Leave</option>
						<option value="18593">Estimation</option>
						<option value="18594">New Client Project Study</option>
						<option value="18595">Timesheet for Client</option>
						<option value="18597">Meetings</option>
						<option value="18632">Learning & Development</option>
						<option value="21329">Available</option>
					    <option value="21330">Unplanned Leave</option>
						<option value="21334">KPI Report</option>
					    <option value="21335">Qcare Report</option>
						<option value="21336">Recruitment - Screening</option>
						<option value="21337">Recruitment Interview</option>
						<option value="21550">Managment Task</option>';
	
		else :
        
	   foreach($getTask as $key => $getResult): // Based on Project get the list of task
		
			echo '<option value='.$getResult->task_Id.'>'.$getResult->task_name.'</option>';

	   endforeach;
     	
	endif;
	
	}



/********************************************************  Manage Timesheet Report Log in Ussertype Admin Or Manager Added On 04-07-2017 ******************************************************************/
	
	
	
	
	public function getUserTypeAdminReportLog($params){  //
	
	  	$client_Id 		 = 	 $params['client_Id'];
        $project_Id      =	 $params['project_Id'];
		$task_Id     	 =	 $params['task_Id'];
		$empId      	 =	 is_array($params['empId']) ? implode(' ,' , $params['empId']) : $params['empId'];
        $form_date		 =	 $params['form_date'];
        $to_date		 = 	 $params['to_date'];
		$department      =	 isset($params['department']) && is_array($params['department']) ? array_filter($params['department']) : array();
		$reporting_manager = isset($params['reporting_manager']) ? trim((string)$params['reporting_manager']) : 'all';
		
		
		if($client_Id == 'all' && $project_Id == 'all' && $empId == 'all' && $task_Id == 'all') :   // Checking all records based on from and to dates only.
		
			$this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.reporting_manger,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,p.project_type,p.empId AS project_manager_name,t.task_name')
			->from('emp_record_details er')
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
              
            //->where('emp.status','Active')   
			//->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            //->where('emp.department' , 'MEP')  
            ->order_by('er.emp_record_id','desc');
			if (!empty($department)) $this->db->where_in('emp.department', $department);
			if ($reporting_manager !== '' && strtolower($reporting_manager) !== 'all') $this->db->where('emp.reporting_manger', $reporting_manager);
			$recordsQ = $this->db->get(); 
		
		    //echo 'List Of All----------'.$this->db->last_query(); 
			 
			return $recordsQ->result();
			
		
		elseif($project_Id == 'all' && $empId == 'all' && $task_Id == 'all'):
		
		$this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.reporting_manger,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,p.project_type,p.empId AS project_manager_name,t.task_name')
			->from('emp_record_details er')
			->where('er.client_Id  = ',$client_Id)	
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
            //->where('emp.status','Active')
			//->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.emp_record_id','desc');
			if (!empty($department)) $this->db->where_in('emp.department', $department);
			if ($reporting_manager !== '' && strtolower($reporting_manager) !== 'all') $this->db->where('emp.reporting_manger', $reporting_manager);
			$recordsQ = $this->db->get(); 
		
		   // echo 'List Of All Project----------'. $this->db->last_query(); 
			 
			return $recordsQ->result();
			
			
		elseif($client_Id == 'all' && $project_Id == 'all' && $task_Id == 'all'):
		 $exp_empIds = explode(',' ,$empId);
		$this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.reporting_manger,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,p.project_type,p.empId AS project_manager_name,t.task_name')
			->from('emp_record_details er')
			->where_in('er.empId ',$exp_empIds)	
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
            //->where('emp.status','Active')
			//->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.emp_record_id','desc');
			if (!empty($department)) $this->db->where_in('emp.department', $department);
			if ($reporting_manager !== '' && strtolower($reporting_manager) !== 'all') $this->db->where('emp.reporting_manger', $reporting_manager);
			$recordsQ = $this->db->get(); 
		
		   // echo 'List Of All Project----------'. $this->db->last_query(); 
			 
			return $recordsQ->result();
        
        elseif($project_Id == 'all' && $task_Id == 'all'):
        
		 $exp_empIds = explode(',' ,$empId);
		$this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.reporting_manger,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,p.project_type,p.empId AS project_manager_name,t.task_name')
			->from('emp_record_details er')
            ->where('er.client_Id  = ',$client_Id)	
			->where_in('er.empId ',$exp_empIds)	
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
            //->where('emp.status','Active')
			//->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.emp_record_id','desc');
			if (!empty($department)) $this->db->where_in('emp.department', $department);
			if ($reporting_manager !== '' && strtolower($reporting_manager) !== 'all') $this->db->where('emp.reporting_manger', $reporting_manager);
			$recordsQ = $this->db->get(); 
		
		   // echo 'List Of All Project----------'. $this->db->last_query(); 
			 
			return $recordsQ->result();
        
        elseif($project_Id == 'all'):
       
		 $exp_empIds = explode(',' ,$empId);
        
          if($empId == 'all'):
        
                $empProjectAllId = '';
            
            else:
                
                $empProjectAllId = $exp_empIds = explode(',' ,$empId);
        
            endif;
        
		$this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.reporting_manger,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,p.project_type,p.empId AS project_manager_name,t.task_name')
			->from('emp_record_details er')
            ->where('er.task_Id  = ',$task_Id)
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
            //->where('emp.status','Active')
			//->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.emp_record_id','desc');
			if (!empty($department)) $this->db->where_in('emp.department', $department);
			if ($reporting_manager !== '' && strtolower($reporting_manager) !== 'all') $this->db->where('emp.reporting_manger', $reporting_manager);
			$recordsQ = $this->db->get(); 
		
		   // echo 'List Of All Project task----------'. $this->db->last_query(); 
			 
			return $recordsQ->result();
			
			
		elseif($empId == 'all' && $task_Id == 'all'):
		
		$this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.reporting_manger,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,p.project_type,p.empId AS project_manager_name,t.task_name')
			->from('emp_record_details er')
			->where('er.client_Id  = ',$client_Id)
			->where('er.project_Id  = ',$project_Id)	
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
            //->where('emp.status','Active')
			//->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.emp_record_id','desc');
			if (!empty($department)) $this->db->where_in('emp.department', $department);
			if ($reporting_manager !== '' && strtolower($reporting_manager) !== 'all') $this->db->where('emp.reporting_manger', $reporting_manager);
			$recordsQ = $this->db->get(); 
		
		    //echo 'List Of All Tasks----------'. $this->db->last_query(); 
			 
			return $recordsQ->result();	
			
			
		elseif($task_Id == 'all'):
		
		   if($project_Id == 'all'){
		   
		    $exp_empIds = explode(',' ,$empId);
		   
		   	$this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.reporting_manger,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,p.project_type,p.empId AS project_manager_name,t.task_name')
			->from('emp_record_details er')
			->where('er.client_Id  = ',$client_Id)
			->where_in('er.empId ',$exp_empIds)	
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
            //->where('emp.status','Active')
			//->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.emp_record_id','desc');
			if (!empty($department)) $this->db->where_in('emp.department', $department);
			if ($reporting_manager !== '' && strtolower($reporting_manager) !== 'all') $this->db->where('emp.reporting_manger', $reporting_manager);
			$recordsQ = $this->db->get(); 
		
		  // echo 'List Of All Employees----------IF--'. $this->db->last_query(); 
			 
			return $recordsQ->result();	
		   
		   }else{
		   
		   	$exp_empIds = explode(',' ,$empId);
			
			$this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.reporting_manger,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,p.project_type,p.empId AS project_manager_name,t.task_name')
			->from('emp_record_details er')
			->where('er.client_Id  = ',$client_Id)
			->where('er.project_Id  = ',$project_Id)
			->where_in('er.empId ',$exp_empIds)	
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
            //->where('emp.status','Active')    
			//->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.emp_record_id','desc');
			if (!empty($department)) $this->db->where_in('emp.department', $department);
			if ($reporting_manager !== '' && strtolower($reporting_manager) !== 'all') $this->db->where('emp.reporting_manger', $reporting_manager);
			$recordsQ = $this->db->get(); 
		
		    //echo 'List Of All Employees----------ELSE--'. $this->db->last_query(); 
			 
			return $recordsQ->result();	
		   
		   }
		
		
		
		else:	      
			
			$exp_empIds = explode(',' ,$empId);
			
            $this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')

			->from('emp_record_details er')
			->where('er.client_Id  = ',$client_Id)
			->where('er.project_Id  = ',$project_Id)
			->where_in('er.empId ',$exp_empIds)	
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
           // 	->where('emp.status','Active')    
			//->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.emp_record_id','desc');
			if (!empty($department)) $this->db->where_in('emp.department', $department);
			if ($reporting_manager !== '' && strtolower($reporting_manager) !== 'all') $this->db->where('emp.reporting_manger', $reporting_manager);
			$recordsQ = $this->db->get(); 
		
		    //echo 'Select wise----------'. $this->db->last_query(); 
			 
			return $recordsQ->result();	
		
		endif;
	 
	} 
	
	public function getAddedReportTaskNames($getTaskIds){ // Multiple task names dispalying here 
		
		//echo $getTaskIds.'<br>';
		$exp_taskids = explode(',' ,$getTaskIds);
		
		$taskQ  = $this->db->select('task_name')->from('task_details')->where_in('task_Id' , $exp_taskids)->get();
		
		$reporttaskName = array();
		foreach($taskQ->result() as $showTaskNames){
			
				$reporttaskName22 = $showTaskNames->task_name;
				
				$reporttaskName[] = $reporttaskName22;				
		}
		
		$tasknames=implode(' , ',$reporttaskName); 
          
		return $tasknames ;
		
	}

	public function getTaskNamesMapByTaskIds($taskIdsList){
		$allTaskIds = array();
		if (!is_array($taskIdsList)) {
			$taskIdsList = array($taskIdsList);
		}
		foreach ($taskIdsList as $taskIds) {
			$parts = explode(',', (string)$taskIds);
			foreach ($parts as $part) {
				$taskId = trim($part);
				if ($taskId !== '' && strtolower($taskId) !== 'all') {
					$allTaskIds[] = $taskId;
				}
			}
		}
		$allTaskIds = array_values(array_unique($allTaskIds));
		if (empty($allTaskIds)) {
			return array();
		}

		$rows = $this->db->select('task_Id,task_name')
			->from('task_details')
			->where_in('task_Id', $allTaskIds)
			->get()
			->result();

		$taskMap = array();
		foreach ($rows as $row) {
			$taskMap[(string)$row->task_Id] = isset($row->task_name) ? (string)$row->task_name : '';
		}
		return $taskMap;
	}
	
/********************************************************  Manage Timesheet Report Log in Ussertype Admin Or Manager Added On 04-07-2017 ******************************************************************/

 /******************** Project Manager get it's created project list *********************************/
  
  public function getProjects($empId){
	  
	  //echo 'testing---------------'.$empId;
	  
	  $this->db->select('p.project_Id')->from('project_details p')
									   ->where('p.empId  = ',$empId)
									   ->order_by('p.project_Id','desc');
	  $recordsQ = $this->db->get()->result(); 
	  
	  $getProjectIds = array();
	  
	  foreach($recordsQ as $getProjectId){
		  
		  $getProjectIds[] = $getProjectId->project_Id;
		  
	  } 
	  
	  return $getProjectIds;
	 
  } 
 
  /* public function update_emp_report_status($emp_record_id , $status){
	  
		$update = $this->db->set('status',$status)->where('emp_record_id',$emp_record_id) ->update('emp_record_details');
		
		$userQ  = $this->db->select('emp_record_id,status')->from('emp_record_details')->where('emp_record_id' , $emp_record_id)->get()->result();
		
		foreach($userQ as $key => $getStatus ) { 
		
		   if($getStatus->status == 'Approved'){
			   
			  $activeClass =  'fa fa-check-circle label label-success';
			   
		   }else{
		   
				$activeClass =  'fa fa-ban label label-danger';
		   
		   }			   
			   
		 	echo  "<a class='".$activeClass."' style=cursor:pointer; onClick=update_emp_report_status(".$getStatus->emp_record_id.",'".$getStatus->status."')> ".$getStatus->status."</a>"; 
		
		}
	  
  } */
	
	
	public function update_emp_report_status($comment_emp_record_id,$comment_status,$status){ 
	  
		$update = $this->db->set('status',$status)->set('comment_status',$comment_status)->where('emp_record_id',$comment_emp_record_id) ->update('emp_record_details');
		
		 $userQ  = $this->db->select('emp_record_id,status')->from('emp_record_details')->where('emp_record_id' , $comment_emp_record_id)->get()->result();
		
		 foreach($userQ as $key => $getStatus ) { 
		
		   if($getStatus->status == 'Approved'){
			   
			  $activeClass =  'fa fa-check-circle label label-success';
			   
		   
		   }elseif($getStatus->status == 'Rejected'){
			   
			   
			   $activeClass = 'fa fa-registered label label-warning';
		   
		   }else{
		   
				$activeClass =  'fa fa-ban label label-danger';
		   
		   }			   
			   
		 	echo  "<a class='".$activeClass."' style=cursor:pointer; data-toggle='modal' data-target='#comment_status_model_".$getStatus->emp_record_id."'> ".$getStatus->status."</a>"; 
		
		}
	  
  }
 
 /******************** Project Manager get it's created project list *********************************/

	public function getRecentApprovedReportLog($userType){
		
		if($userType == 'developer'):
		    
			$empId =  $this->session->userdata['logged_in_timesheet']['empId'];
			
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name');
            $this->db->from('emp_record_details er'); 
            $this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
			$this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
            $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
			$this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
            $this->db->where('er.empId',$empId);
			$this->db->where('er.status','Approved');
			$this->db->limit('10');
            $this->db->order_by('er.emp_record_id','desc');   
		
		    $recordsQ = $this->db->get();
		 
		 return $recordsQ->result();
		 
		 endif;
	}
 
    
/************************************************** Unapproved List of timesheet report log **************************/
    
      public function getReportStatusRecords($reportStatus){ // List of employee enter the records based on users
  
          $project_Id = '';
          $form_date = '';
          $to_date = '';
  
          if(is_array($reportStatus) && !empty($reportStatus['project_Id'])) {
              $project_Id = $reportStatus['project_Id'];
              $form_date = $reportStatus['form_date'];
              $to_date = $reportStatus['to_date'];
          }
  
          $userType = $this->session->userdata['logged_in_timesheet']['user_type'];
          $logedInUser = $this->session->userdata['logged_in_timesheet']['empId'];
          $logedInName = isset($this->session->userdata['logged_in_timesheet']['name']) ? trim($this->session->userdata['logged_in_timesheet']['name']) : '';
          $directReportRows = $this->db->select('empId')->from('employee_details')->where('reporting_manger', $logedInUser)->get()->result();
          $directReportEmpIds = array();
          foreach($directReportRows as $directReportRow){
              $directReportEmpIds[] = $directReportRow->empId;
          }
  
          $this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.reporting_manger,emp.user_type,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name');
          $this->db->from('emp_record_details er');
          $this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
          $this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
          $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
          $this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
          // Normalize status check to avoid missing rows due to case/space variants.
          $this->db->where("LOWER(TRIM(er.status)) = 'unapproved'", NULL, FALSE);
  
          if(!empty($project_Id) && $project_Id != 'all'){
              $this->db->where('er.project_Id', $project_Id);
          }
  
          if(!empty($form_date) && !empty($to_date)){
              $this->db->where('er.emp_report_dates >= ', $form_date);
              $this->db->where('er.emp_report_dates <= ', $to_date);
          }
  
          if($userType == 'admin' && (string)$logedInUser === '1'){
              // Only tsadmin (empId 1) can view all unapproved rows.
          }elseif($userType == 'developer'){
              // Developers should only see their own logs.
              $this->db->where('er.empId', $logedInUser);
          }else{
              // Non-tsadmin users should see:
              // 1) direct reportees
              // 2) users who worked in projects managed/allocated by them
              // 3) project rows mapped by p_manager text
              // 4) project team_members mapping
              // Exclude own entries from managerial unapproved view.
              $this->db->group_start();
              $this->db->where('emp.reporting_manger', $logedInUser);
              $this->db->or_where('p.empId', $logedInUser);
              $this->db->or_where('p.who_allocated_project_empId', $logedInUser);
              if($logedInName !== ''){
                  $this->db->or_where('LOWER(TRIM(p.p_manager))', strtolower($logedInName));
              }
              $this->db->or_where("FIND_IN_SET(CAST(er.empId AS CHAR), REPLACE(COALESCE(p.team_members,''), ' ', '')) >", 0, FALSE);
              $this->db->group_end();
              $this->db->where('er.empId !=', $logedInUser);
          }
  
          $this->db->order_by('emp.reporting_manger','asc');
          $this->db->order_by('er.emp_record_id','desc');
  
          $recordsQ = $this->db->get();
          return $recordsQ->result();
      }
    
    
    /************************************** Manager project list information **********************/
    
    public function getMangerProjectsUnapprovedList(){
        
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
    
/************************************************ Unapproved list of timesheet report log End ***********************/    
    
    
 /************************************************* Datatable *******************************************************************************/
	
	var $order = array('er.emp_record_id' => 'desc'); // default order
	
	function get_order_list($post){
        $this->_get_order_list_query($post);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    
    function _get_order_list_query($post){
         $this->db->select('er.* ,emp.empId,emp.name,emp.user_type,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name');
            $this->db->from('emp_record_details er'); 
            $this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
			$this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
            $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
			$this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
           // $this->db->where('er.empId',$empId);
           
		
        if (!empty($post['manager_visibility'])) {
            $this->db->group_start();
            $this->db->where_in('er.project_Id', $post['manager_visibility']['project_ids']);
            $this->db->or_where('er.empId', $post['manager_visibility']['emp_id']);
            $this->db->group_end();
        } elseif (!empty($post['where'])) {
            $this->db->where($post['where']);
        }
        
        if (!empty($post['where_in'])) {
            foreach ($post['where_in'] as $index => $value){
                $this->db->where_in($index, $value);
            }
        }
		
		
        
        if (!empty($post['search_value'])) {
           
            foreach ($post['column_search'] as $key => $item) { // loop column 
                // if datatable send POST for search
                if ($key === 0) { // first loop
                     $this->db->group_start();
					$this->db->like($item, $post['search_value']);
                   
                } else {
					
                   $this->db->or_like($item, $post['search_value']);
                     
                }
				
				if(count($post['column_search']) - 1 == $key){ //last loop}
                    $this->db->group_end(); //close bracket
				}
             }
           

        }
		
		if(!empty($post['or_where'])){
					$this->db->or_where($post['or_where']);
        }
		

        if (!empty($post['order'])) { // here order processing
            
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
            
        } else if (isset($this->order)) {
			
            $order = $this->order;
			
            $this->db->order_by(key($order), $order[key($order)]);
            
        }
    }
    
    function count_all($post){
		
        $this->_count_all_bb_order($post);
		
        $query = $this->db->count_all_results();
       
        return $query;
    }
    
    public function _count_all_bb_order($post){
        
				$this->db->select('er.*')->from('emp_record_details er');
				$this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');

				if (!empty($post['manager_visibility'])) {
					$this->db->group_start();
					$this->db->where_in('er.project_Id', $post['manager_visibility']['project_ids']);
					$this->db->or_where('er.empId', $post['manager_visibility']['emp_id']);
					$this->db->group_end();
				} elseif (!empty($post['where'])) {
					$this->db->where($post['where']);
				}

				if (!empty($post['where_in'])) {
					foreach ($post['where_in'] as $index => $value){
						$this->db->where_in($index, $value);
					}
				}

				if(!empty($post['or_where'])){
					$this->db->or_where($post['or_where']);
        		}
    }
    
    function count_filtered($post){
		
        $this->_get_order_list_query($post);
		
        $query = $this->db->get();
        
		//	echo $this->db->last_query();
		
		return $query->num_rows();
		
    }
	
	public function viewEmpTimeLogTaskDetails($displayPOPID){
		
		 if(!empty($displayPOPID)){			 
			 
			$this->db->select('er.* ,emp.empId,emp.name,emp.user_type,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name');
            $this->db->from('emp_record_details er'); 
            $this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
			$this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
            $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
			$this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
			 $this->db->where('er.emp_record_id',$displayPOPID);
            $this->db->order_by('er.emp_record_id','desc');         
            $recordsQ = $this->db->get(); 
		 	return $recordsQ->result();
			 
		 }
		
		
	}
	
	public function update_emp_pm_report_status($comment_emp_record_id,$comment_status,$status){  // Employee Report Log Update Status Function
	  
		$update = $this->db->set('status',$status)->set('comment_status',$comment_status)->where('emp_record_id',$comment_emp_record_id) ->update('emp_record_details');
		
		 $userQ  = $this->db->select('emp_record_id,status')->from('emp_record_details')->where('emp_record_id' , $comment_emp_record_id)->get()->result();
		
		 foreach($userQ as $key => $getStatus ) { 
		
		   if($getStatus->status == 'Approved'){
			   
			  $activeClass =  'fa fa-check-circle label label-success';
			   
			  $statusValueShow = 'Unapproved'; 
		   
		   }elseif($getStatus->status == 'Rejected'){
			   
			   
			   $activeClass = 'fa fa-registered label label-warning';
			   
			   $statusValueShow = 'Approved';
		   
		   }else{
		   
				$activeClass =  'fa fa-ban label label-danger';
			   
			    $statusValueShow = 'Approved';
		   
		   }			   
			 
			   
		 	echo '<a href="#" class="'.$activeClass.'" data-toggle="modal" data-target="#status-pop-modal" title="Click To '.$statusValueShow.'" data-id="'.$getStatus->emp_record_id.'" id="empUpdateStatus" > '.$getStatus->status.'</a>'; 
		
		}
	  
  }
	
	
/*************************************************** Datatable *****************************************************************************/	
	
 
 /****************************** Getting Result of Resource Billabilityn form client , project based on task result **************************************/
	
	
	public function searchResourceBillability($params){  //
	
		$client_Id 		 = 	 $params['client_Id'];
        $project_Id      =	 $params['project_Id'];
        $form_date		 =	 $params['form_date'];
        $to_date		 = 	 $params['to_date'];
		
		$empId =  $this->session->userdata['logged_in_timesheet']['empId']; // Loged in users		
		
		 if($client_Id == 'all' && $project_Id == 'all') :   // Checking all records based on from and to dates only.
		
				$resourceBBQ = $this->db->select('er.*, SUM(er.emp_time_hours) as vickty ,emp.empId,emp.name,p.project_Id,p.project_name,p.resource_billability')
							->from('emp_record_details er')
							->where('er.emp_report_dates >= ',$form_date)			
							->where('er.emp_report_dates <= ',$to_date)
							->join('employee_details as emp', 'emp.empId=er.empId', 'left')
							->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
							->group_by('er.	empId')
							->group_by('er.	project_Id')	
							->order_by('er.emp_record_id','desc')->get();
			
		elseif($project_Id == 'all'):
		
				$resourceBBQ = $this->db->select('er.*, SUM(er.emp_time_hours) as vickty ,emp.empId,emp.name,p.project_Id,p.project_name,p.resource_billability')
						->from('emp_record_details er')
						->where('er.client_Id  = ',$client_Id)		
						->where('er.emp_report_dates >= ',$form_date)			
						->where('er.emp_report_dates <= ',$to_date)
						->join('employee_details as emp', 'emp.empId=er.empId', 'left')
						->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
						->group_by('er.	empId')
						->group_by('er.	project_Id')	
						->order_by('er.emp_record_id','desc')->get();
		
		else:
		 
					$resourceBBQ = $this->db->select('er.*, SUM(er.emp_time_hours) as vickty ,emp.empId,emp.name,p.project_Id,p.project_name,p.resource_billability')
								->from('emp_record_details er')
								->where('er.client_Id  = ',$client_Id)			
								->where('er.project_Id = ',$project_Id)
								->where('er.emp_report_dates >= ',$form_date)			
								->where('er.emp_report_dates <= ',$to_date)
								->join('employee_details as emp', 'emp.empId=er.empId', 'left')
								->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
								->group_by('er.	empId')
								->group_by('er.	project_Id')	
								 ->order_by('er.emp_record_id','desc')->get();
			
	    endif;
	  
	return $resourceBBQ->result();
		
	
	} // Employee and Admin Search Report Log END
/****************************** Getting Result of Resource Billability form client , project based on task result **************************************/	
 
/*******************  We are sending emails from Unapproved PM's List in Pm_groups emails  And also same as in Team Member As well  *******************/	

	/* 
 public function unappPMTeamMList($params){
 
 	    $user_status_type       =	 $params['user_status_type'];
        $form_date		 		=	 $params['form_date'];
        $to_date		 		= 	 $params['to_date'];
	 		
	  $managerList = $this->db->select('emp.empId,emp.name,emp.user_type,emp.email')->from('employee_details emp')
		 															->where('emp.user_type',$user_status_type)
		 															->order_by('emp.empId','desc')->get()->result(); 
	 
	// $managerIds = array();
	 $getEmailIds = array();
	 foreach($managerList as $listOfMg){  // List of Project Managers
		  
		 	$managerIds = $listOfMg->empId; // Manager Ids
		 
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
							  
							    
							  			
							  
							  $getEmailIds[] = array(
												array(
													"name" =>  $unapprovedPMSList->name,
													"email" => $unapprovedPMSList->email,
												),												
											);
							  
						  }
					   
					  	
					   
				   }
				  

			  }	 
		 
		   return $getEmailIds;
		  
		 	    
	 }
	 
	
	 
 }

*/
	
public function getWeeklyApprovedEamilProcess($friday){ 
	
	if(!empty($friday)){
		
		$getWeeklyEmailProcess  = $this->db->select('weekend_date , sent_email_status')->from('weekly_email_report_log')->where('weekend_date', $friday)->get()->result();
	   
		return $getWeeklyEmailProcess;
		
		
	}
		
}	
/*******************  We are sending emails from Unapproved PM's List in Pm_groups emails  And also same as in Team Member As well  *******************/
    
    
    
/******************************************************* approveCheckedEmpReports *********************************************************************/
    
    
    public function approveCheckedEmpReports($getApproveList){
        
       if(empty($getApproveList)){
           return false;
       }
        
        $str_arr = array_filter(array_map('trim', explode(",", $getApproveList)));
        if(empty($str_arr)){
            return false;
        }
        
        $allowedIds = $this->getApprovableEmpRecordIds($str_arr);
        if(empty($allowedIds)){
            return false;
        }
        
        return $this->db->set('status', 'Approved')
            ->where_in('emp_record_id', $allowedIds)
            ->update('emp_record_details');
    }
    
    /**
     * Returns emp_record_id values the current user may bulk-approve (same visibility as unapproved list).
     */
    private function getApprovableEmpRecordIds($emp_record_ids){
        $userType = $this->session->userdata['logged_in_timesheet']['user_type'];
        $logedInUser = $this->session->userdata['logged_in_timesheet']['empId'];
        $logedInName = isset($this->session->userdata['logged_in_timesheet']['name']) ? trim($this->session->userdata['logged_in_timesheet']['name']) : '';
        
        $this->db->select('er.emp_record_id');
        $this->db->from('emp_record_details er');
        $this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
        $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
        $this->db->where_in('er.emp_record_id', $emp_record_ids);
        $this->db->where("LOWER(TRIM(er.status)) = 'unapproved'", NULL, FALSE);
        
        if($userType == 'admin' && (string)$logedInUser === '1'){
            // tsadmin may approve any selected unapproved row
        }elseif($userType == 'developer'){
            $this->db->where('er.empId', $logedInUser);
        }else{
            $this->db->where('er.empId !=', $logedInUser);
            $this->db->group_start();
            $this->db->where('emp.reporting_manger', $logedInUser);
            $this->db->or_where('p.empId', $logedInUser);
            $this->db->or_where('p.who_allocated_project_empId', $logedInUser);
            if($logedInName !== ''){
                $this->db->or_where('LOWER(TRIM(p.p_manager))', strtolower($logedInName));
            }
            $this->db->or_where("FIND_IN_SET(CAST(er.empId AS CHAR), REPLACE(COALESCE(p.team_members,''), ' ', '')) >", 0, FALSE);
            $this->db->group_end();
        }
        
        $rows = $this->db->get()->result();
        $ids = array();
        foreach($rows as $row){
            $ids[] = $row->emp_record_id;
        }
        return $ids;
    }
    
    
    public function getListOfProjectsGeneralWithClient($client_Id){ // Get Dropdown client wise projects  with out all feature
	
		  
	  $getProjects  = $this->db->select('*')->from('project_details')->where('client_Id', $client_Id)->where('status','Process')->get()->result();
	  
	   echo '<option value="">Please Select Project</option>';
	  
	   foreach($getProjects as $key => $getResult):
	   
        $hideGeneral = str_replace(" - (General)", "",$getResult->project_name);
        
        if($hideGeneral == $getResult->project_name){
            
            
	       echo '<option value='.$getResult->project_Id.'>'.$getResult->project_name.'</option>';
            
        
        }
	   
	   endforeach;
	 
	  //return $getStates;
		 
	}  // Get Dropdown client wise projects 
    
    
/*************************************************************** approveCheckedEmpReports ************************************************************/    
/********************************************** Employee approved and unapproved report logs List *************************************************/
    
/********************************************** Employee approved and unapproved report logs List *************************************************/
    
	
	//Based on client , project and task List coming to below code static added General tasks
	
	public function getProjectWiseTaskLWGList($project_Id){ // Get Dropdown Project wise clients
	
			
	 	$getProjectGeneralResult  = $this->db->select('*')->from('project_details')->where('project_Id', $project_Id)->where('status','Process')->get()->result();
		
		foreach($getProjectGeneralResult as $key => $pGeneral):
		
				$hideGeneral = str_replace(" - (General)", "",$pGeneral->project_name);
		
			endforeach;
		
		$getTask  = $this->db->select('*')->from('task_details')->where('project_Id', $project_Id)->get()->result();
		  
        echo '<option value="">Please select task</option>';
		
		if($hideGeneral.' - (General)' == $pGeneral->project_name): // Project General related Task List
		
			echo '<option value="18580">Team Meeting</option>
					<option value="18581">Quality Management Review</option>
					<option value="18582">Client Rework</option>
					<option value="18583">Internal Rework</option>
					<option value="18584">Client Communication</option>	
					<option value="18585">Project Coordination</option>
					<option value="18586">Project Study</option>';		
		
		elseif('General' == $pGeneral->project_name): // PM wise General Related Task List
		
			if($project_Id == '5083'){
				
				echo '<option value="18093">New Client </option>
					  <option value="18094">Timesheet for Client</option>
					  <option value="18095">Management Task</option>
					  <option value="18096">Software Task</option>
					  <option value="18097">Estimation</option>
					  <option value="18098">Meetings</option>
					  <option value="18099">IT</option>
					  <option value="18100">HR & Admin Task</option>
					  <option value="18101">Accounts Task </option>
					  <option value="26161">Revit API </option>
					  <option value="18592">Leave</option>
					  <option value="18589">Fun @ Work</option>
					  <option value="18590">Downtime (i.e System issue / late login/Early logout / half a day )</option>
					  <option value="18591">Training</option>
					  <option value="18597">Meetings</option>
					  <option value="18632">Learning & Development</option>
					  <option value="18593">Estimation</option>';
			
			}elseif($project_Id == '432'){

					echo '<option value="18587">One On One</option>
							<option value="18588">Skip Meeting</option>
							<option value="18589">Fun @ Work</option>
							<option value="18590">Downtime (i.e System issue / late login/Early logout / half a day )</option>
							<option value="18591">Training</option>
							<option value="21446">New Joinee Training</option>	
							<option value="18592">Leave</option>						
							<option value="18594">New Client Project Study</option>
							<option value="18595">Timesheet for Client</option>
							<option value="18597">Meetings</option>
							<option value="18632">Learning & Development</option>
							<option value="21330">Unplanned Leave</option>						
							<option value="21335">Qcare Report</option>
							<option value="18593">Estimation</option>
							<option value="21334">KPI Report</option>
							<option value="21336">Recruitment - Screening</option>
							<option value="21337">Recruitment Interview</option>
							<option value="21550">Managment Task</option>
							<option value="21923">Training Faciliatory</option>';

					}elseif($project_Id == '258'){

					echo '<option value="18587">One On One</option>
							<option value="18588">Skip Meeting</option>
							<option value="18589">Fun @ Work</option>
							<option value="18590">Downtime (i.e System issue / late login/Early logout / half a day )</option>
							<option value="18591">Training</option>
							<option value="21446">New Joinee Training</option>	
							<option value="18592">Leave</option>						
							<option value="18594">New Client Project Study</option>
							<option value="18595">Timesheet for Client</option>
							<option value="18597">Meetings</option>
							<option value="18632">Learning & Development</option>
							<option value="21330">Unplanned Leave</option>						
							<option value="21335">Qcare Report</option>
							<option value="18593">Estimation</option>
							<option value="21334">KPI Report</option>
							<option value="21336">Recruitment - Screening</option>
							<option value="21337">Recruitment Interview</option>
							<option value="21550">Managment Task</option>
							<option value="21923">Training Faciliatory</option>';	

			}else{
				
				echo '<option value="18587">One On One</option>
						<option value="18588">Skip Meeting</option>
						<option value="18589">Fun @ Work</option>
						<option value="18590">Downtime (i.e System issue / late login/Early logout / half a day )</option>
						<option value="18591">Training</option>
						<option value="21446">New Joinee Training</option>	
						<option value="18592">Leave</option>						
						<option value="18594">New Client Project Study</option>
						<option value="18595">Timesheet for Client</option>
						<option value="18597">Meetings</option>
						<option value="18632">Learning & Development</option>
						<option value="21329">Available</option>
					    <option value="21330">Unplanned Leave</option>						
					    <option value="21335">Qcare Report</option>
						<option value="21923">Training Faciliatory</option>';
						//endif;		
				}
	
		else :
        
	   foreach($getTask as $key => $getResult): // Based on Project get the list of task
		
			echo '<option value='.$getResult->task_Id.'>'.$getResult->task_name.'</option>';

	   endforeach;
     	
	endif;
		
	
	}
	
//Based on client , project and task List coming to below code END    
	
    
    
	
 }