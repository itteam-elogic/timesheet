<?php
/**
 * eLogivc Admin Panel for Codeigniter 
 * Author: Laxmikanth 
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Timesheet_model extends CI_Model {

    public function __construct() {
	
			parent::__construct();   
	
	}
  

	
	 public function searchClientWiseProjects(){ // Get Dropdown client wise projects 
	
		  
	  $getProjects  = $this->db->select('*')->from('project_details')->where('client_Id', $client_Id)->get()->result();
	  
	   echo '<option value="">Please Select Project</option> <option value="all">All</option>';
	  
	   foreach($getProjects as $key => $getResult):
	   
	    echo '<option value='.$getResult->project_Id.'>'.$getResult->project_name.'</option>';
	   
	   endforeach;
	 
	  //return $getStates;
		 
	}  // Get Dropdown client wise projects 
	
	
	public function getListOfProjectsWithClient($client_Id){ // Get Dropdown client wise projects  with out all feature
	
		  
	  $getProjects  = $this->db->select('*')->from('project_details')->where('client_Id', $client_Id)->get()->result();
	  
	   echo '<option value="">Please Select Project</option>';
	  
	   foreach($getProjects as $key => $getResult):
	   
	    echo '<option value='.$getResult->project_Id.'>'.$getResult->project_name.'</option>';
	   
	   endforeach;
	 
	  //return $getStates;
		 
	}  // Get Dropdown client wise projects 
	
	
	public function getProjectWiseTask($project_Id){ // Get Dropdown Project wise clients
	
			
	 $getTask  = $this->db->select('*')->from('task_details')->where('project_Id', $project_Id)->get()->result();
	  
	   foreach($getTask as $key => $getResult):
	   
	    echo '<option value='.$getResult->task_Id.'>'.$getResult->task_name.'</option>';
	   
	   endforeach;
	
	}
	 
	 
	 
  public function addEmpRecords($data) { // Store Emploee Records into database
  
  	 if($data):
	   
	   		 $this->db->insert('emp_record_details', $data);	
	   
	   endif;
  
  }	 
  
  
  public function getRecords($userType){ // List of employee enter the records based on users
  
         if($userType == 'developer' || $userType == 'manager'):
		    
			$empId =  $this->session->userdata['logged_in_timesheet']['empId'];
			
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_Id,t.task_name');
            $this->db->from('emp_record_details er'); 
            $this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
			$this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
            $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
			$this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
            $this->db->where('er.empId',$empId);
            $this->db->order_by('er.emp_record_id','desc');         
            $recordsQ = $this->db->get(); 
			return $recordsQ->result();
          
		  else: 
		  
		    $this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_Id,t.task_name');
            $this->db->from('emp_record_details er'); 
            $this->db->join('employee_details as emp', 'emp.empId=er.empId', 'left');
			$this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
            $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
			$this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
           // $this->db->where('er.empId',$empId);
            $this->db->order_by('er.emp_record_id','desc');         
            $recordsQ = $this->db->get(); 
			return $recordsQ->result();
		  
		  endif;	
			
			/*if($recordsQ->num_rows() != 0){
			
                return $recordsQ->result();
				
            }else{
			
                return false;
				
            }*/
    
   
  		//$recordsQ 			=	 $this->db->select('*')->from('emp_record_details')->order_by('emp_record_id' , 'desc')->get();
		
		//return $recordsQ->result();
  
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
		
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_Id,t.task_name')
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
		
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_Id,t.task_name')
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
		    
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_Id,t.task_name')
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
		
		
	  }else{ // Search for Manager and Admin 
	  
	  
	 		if($client_Id == 'all' && $project_Id == 'all') :   // Checking all records based on from and to dates only.
		
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_Id,t.task_name')
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
		
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_Id,t.task_name')
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
		    
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_Id,t.task_name')
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
	
	/************************************** Manager Timesheet Log **************************************************/
	
		public function getManagerReportLog_22($params){  //
	    
		
		
		
		$client_Id 		 = 	 $params['client_Id'];
        $project_Id      =	 $params['project_Id'];
		$task_Id     	 =	 $params['task_Id'];
		
		if(!empty($params['empId'])): 
		
				$empId      	 =	 implode(' ,' ,$params['empId']);
				
		else:
				
				$empId      	 =	 'all';
				
		endif;
		
		
		
		
        $form_date		 =	 $params['form_date'];
        $to_date		 = 	 $params['to_date'];
	
		$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
				 ->from('emp_record_details er');   
				 
	   
	   if($client_Id == 'all' &&  $project_Id == 'all' && $task_Id == 'all' && $empId == 'all' ){			
			
			
			
		}

	   if ($client_Id != '') {
            
			if($client_Id != 'all'):
			   
			   $this->db->where('er.client_Id = ',$client_Id);
			   
			endif; 
			
		}
		
        if ($project_Id != '') {
            
			if($project_Id != 'all'):
			   
			   $this->db->where('er.project_Id = ',$project_Id);
			   
			endif;   
			
        }
        if ($task_Id != '') {
            
			if($task_Id != 'all'):
			   
			   $this->db->where('er.task_Id = ',$task_Id);
			   
			endif; 
			
        }
        if ($empId != '') {           
			
			if($empId != 'all'):
			   
			   $this->db->where_in('er.empId',explode(',' ,$empId));
			   
			endif;
        }
		
		
		
		
		
		
		$this->db->where('er.emp_report_dates >= ',$form_date)			
							->where('er.emp_report_dates <= ',$to_date)
							->where('er.status','Approved')
							->join('employee_details as emp', 'emp.empId=er.empId', 'left')
							->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
							->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
							->join('task_details as t', 't.task_Id=er.task_Id', 'left')
							->order_by('er.empId','desc');			
			$recordsQ = $this->db->get(); 
			
			//echo $this->db->last_query();
		
		    return $recordsQ->result(); 
	
		
	} 
	
	/************************************** Manager Timesheet Log Report *******************************************/

	
	public function getManagerReportLog($params){  //	
	  	
		
     if(!empty($params['client_Id'])): 
		
				$client_Id      	 =	 $params['client_Id'];
				
		else:
				
				$client_Id      	 =	 'all';
				
		endif;

		if(!empty($params['project_Id'])): 
		
				$project_Id      	 =	 $params['project_Id'];
				
		else:
				
				$project_Id      	 =	 'all';
				
		endif;
		
		if(!empty($params['task_Id'])): 
		
				$task_Id      	 =	 $params['task_Id'];
				
		else:
				
				$task_Id      	 =	 'all';
				
		endif;

	//$empId      	 =	 implode(' ,' ,$params['empId']);
        if(!empty($params['empId'])): 
		
				$empId      	 =	 implode(' ,' ,$params['empId']);
				
		else:
				
				$empId      	 =	 'all';
				
		endif;
		
		$form_date		 =	 $params['form_date'];
        $to_date		 = 	 $params['to_date'];
		
		
		
		if($client_Id == 'all' && $project_Id == 'all' && $empId == 'all' && $task_Id == 'all') :   // Checking all records based on from and to dates only.
		
			$this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
			->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.empId','desc');
			
			$recordsQ = $this->db->get(); 
		
		    //echo 'List Of All----------'.$this->db->last_query(); 
			 
			return $recordsQ->result();
			
		
		
		elseif($project_Id !='all'):
		
		$this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where('er.project_Id  = ',$project_Id)	
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
			->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.empId','desc');
			
			$recordsQ = $this->db->get(); 
		
		     //echo 'only Project----------'. $this->db->last_query(); 
			 
			return $recordsQ->result();
			
			
		elseif($task_Id !='all'):
		
		$this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where('er.task_Id  = ',$task_Id)	
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
			->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.empId','desc');
			
			$recordsQ = $this->db->get(); 
		
		     //echo 'Task Project----------'. $this->db->last_query(); 
			 
			return $recordsQ->result();
		
		
		elseif($project_Id == 'all' && $empId == 'all' && $task_Id == 'all'):
		
		$this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where('er.client_Id  = ',$client_Id)	
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
			->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.empId','desc');
			
			$recordsQ = $this->db->get(); 
		
		   // echo 'List Of All Project----------'. $this->db->last_query(); 
			 
			return $recordsQ->result();
			
			
		elseif($client_Id == 'all' && $project_Id == 'all' && $task_Id == 'all'):
		 $exp_empIds = explode(',' ,$empId);
		$this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where_in('er.empId ',$exp_empIds)	
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
			->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.empId','desc');
			
			$recordsQ = $this->db->get(); 
		
		   // echo 'List Of All Project----------'. $this->db->last_query(); 
			 
			return $recordsQ->result();
			
			
		elseif($empId == 'all' && $task_Id == 'all'):
		
		$this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where('er.client_Id  = ',$client_Id)
			->where('er.project_Id  = ',$project_Id)	
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
			->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.empId','desc');
			
			$recordsQ = $this->db->get(); 
		
		    //echo 'List Of All Tasks----------'. $this->db->last_query(); 
			 
			return $recordsQ->result();	
			
			
		elseif($task_Id == 'all'):
		
		   if($project_Id == 'all'){
		   
		    $exp_empIds = explode(',' ,$empId);
		   
		   	$this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where('er.client_Id  = ',$client_Id)
			->where_in('er.empId ',$exp_empIds)	
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
			->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.empId','desc');
			
			$recordsQ = $this->db->get(); 
		
		  // echo 'List Of All Employees----------IF--'. $this->db->last_query(); 
			 
			return $recordsQ->result();	
		   
		   }else{
		   
		   	$exp_empIds = explode(',' ,$empId);
			
			$this->db->select('er.* ,emp.empId,emp.name,emp.emp_com_id,emp.department,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_name')
			->from('emp_record_details er')
			->where('er.client_Id  = ',$client_Id)
			->where('er.project_Id  = ',$project_Id)
			->where_in('er.empId ',$exp_empIds)	
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
			->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.empId','desc');
			
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
			->where('er.status','Approved')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
            ->order_by('er.empId','desc');
			
			$recordsQ = $this->db->get(); 
		
		    //echo 'Select wise----------'. $this->db->last_query(); 
			 
			return $recordsQ->result();	
		
		endif;
	 
	}
	
	

}

