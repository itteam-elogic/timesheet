<?php
/**
 * eLogivc Admin Panel for Codeigniter 
 * Author: Laxmikanth 
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Task_Model extends CI_Model {

    public function __construct() {
	
			parent::__construct();   
	
	}
  

	// Read data using username and password
	
	
	public function add_task($data){  // Add Task  
	
	  
	  if($data):
	   
	   		 $this->db->insert_batch('task_details', $data);	
	   
	   endif;
	
	
	}
	
	public function getTaskList($task_Id = NULL){
	
         //Fetch departments along with client models and their associated project 
		 $getDepartmentCall = $this->client_model->departmentInsights();
		 
		if(empty($task_Id)):
		
			if(in_array($this->session->userdata['logged_in_timesheet']['user_type'], array('manager','business_head'))){
                
				$logedInUser = $this->session->userdata['logged_in_timesheet']['empId'];
		
					$taskQ = $this->db->select('t.*,c.client_Id,c.client_name,p.project_Id,p.project_name,e.name,e.empId')
								->from('task_details as t')
								->join('client_details as c ', 't.client_Id = c.client_Id', 'left')
								->join('project_details as p ', 't.project_Id = p.project_Id', 'left')
								->join('employee_details as e ', 't.empId = e.empId', 'left')
                                ->where_in('c.department', explode(',', $getDepartmentCall))
                               // ->where('t.empId',$logedInUser)
								//->where('t.status','Process')
								->order_by('task_Id' , 'desc')->get();
																
			
			}else{
				
					$taskQ = $this->db->select('t.*,c.client_Id,c.client_name,p.project_Id,p.project_name,e.name,e.empId')
								->from('task_details as t')
								->join('client_details as c ', 't.client_Id = c.client_Id', 'left')
								->join('project_details as p ', 't.project_Id = p.project_Id', 'left')
								->join('employee_details as e ', 't.empId = e.empId', 'left')
								->where('e.status','Active')
                                ->where('t.status','Process')
								->order_by('task_Id' , 'desc')->get();
					
			}	
		
		else:
		
		    $taskQ  = $this->db->select('*')->from('task_details')->where('task_Id' , $task_Id)->get();
		
		endif; 
		
		 // echo $this->db->last_query();
		 
		 return $taskQ->result();
	
	}
	
	
	public function update_task($data , $task_Id){ // Clent Update Functionality
  
  		$this->db->where('task_Id', $task_Id);
		
	    $update = $this->db->update('task_details', $data);
		
		if($update):
			
			  return true; 
			
		endif;
  
  }
    
    
    
    public function update_task_status($projct_Id){    //update task status based on project.
        
    if(!empty($projct_Id)){ 
        
       $updateStatusPT =  $this->db->set('status', 'Closed')->where('project_Id', $projct_Id)->update('task_details');  //table name
        //echo $this->db->last_query(); exit;
        
        if($updateStatusPT):
			
			  return true; 
			
		endif;
        
    }
        
    } 
  
  
   public function delete_task($task_Id){
    
	 
	 	$this->db->where('task_Id', $task_Id)->delete('task_details');
		  
		$deleteQuery = $this->db->affected_rows();
			
	    echo  $deleteQuery;
  
  }

  public function toggle_task_status($task_Id, $new_status){
	$this->db->where('task_Id', $task_Id);
	$updated = $this->db->update('task_details', array(
		'status' => $new_status,
		'updated_at' => date('Y-m-d H:i:s')
	));
	return (bool)$updated;
  }
  
  
  
  public function getTaskName($taskProjectId){ // Displaying List of projects
  
         // echo '$getUpdateId-------'.$getUpdateId; exit;
	
			if(empty($taskProjectId)) :
			
				$taskNamesQ  	= $this->db->select('task_Id,task_name')->from('task_details')->where('status','Process')->group_by('task_name')->order_by('task_Id' , 'desc')->get();
			
			else:
			
				$taskNamesQ  	= $this->db->select('task_Id,task_name')->from('task_details')->where('status','Process')->where('project_Id',$taskProjectId)->order_by('task_Id' , 'desc')->get();
			
			endif;  	
			
			return $taskNamesQ->result();
		
	}
	
	
	
	public function create_task_mapping($data) {
	
  
       //$this->db->insert_batch('task_details', $data);
	   
	   if($data){
		
			$this->db->insert_batch('task_details', $data);
	
	   }
	
	
     }
    
    
     /* public function getTaskReportId($taskId){ // Displaying List of projects
  
        // echo '$getUpdateId-------'.$taskId; exit;
         
         
         $getTaskId = explode (",", $taskId); 
	
			if(empty($taskId)) :
			
				$taskNamesQ  	= $this->db->select('task_Id,task_name')->from('task_details')->where('status','Process')->group_by('task_name')->order_by('task_Id' , 'desc')->get();
			
			else:
			
				$taskNamesQ  	= $this->db->select('task_Id,task_name')->from('task_details')->where('status','Process')->where_in('task_Id',$getTaskId)->order_by('task_Id' , 'desc')->get();
         
         // echo $this->db->last_query();
			
			endif;  	
			
			return $taskNamesQ->result();
		
	} */
	
	public function getTaskReportId($taskId){ // Displaying List of projects
  
        // echo '$getUpdateId-------'.$taskId; exit;
         
         
         $getTaskId = explode (",", $taskId); 
	
			if(empty($taskId)) :
			
				$taskNamesQ  	= $this->db->select('task_Id,client_Id,task_name')->from('task_details')->where('status','Process')->group_by('task_name')->order_by('task_Id' , 'desc')->get();
			
			else:
			
				$taskNamesQ  	= $this->db->select('task_Id,client_Id,task_name')->from('task_details')->where('status','Process')->where_in('task_Id',$getTaskId)->order_by('task_Id' , 'desc')->get();
         
         // echo $this->db->last_query();
			
			endif;  	
			
			return $taskNamesQ->result();
		
	}

/************************************ akhila code download excelsheet code***********************************/

	public function getAllTaskReportData($search = '')
{
    $this->db->select('
        t.*,
        p.project_name,
        c.client_name
    ');

    // ✅ USE YOUR ACTUAL TABLE NAME
    $this->db->from('task_details t');

    $this->db->join(
        'project_details p',
        'p.project_Id = t.project_Id',
        'left'
    );

    $this->db->join(
        'client_details c',
        'c.client_Id = p.client_Id',
        'left'
    );

    // ==========================
    // UNIVERSAL SEARCH
    // ==========================

    if(!empty($search))
    {
        $this->db->group_start();

        $this->db->like('t.task_name', $search);

        $this->db->or_like('p.project_name', $search);

        $this->db->or_like('c.client_name', $search);

        $this->db->group_end();
    }

    return $this->db
        ->order_by('t.task_Id', 'DESC')
        ->get()
        ->result();
}
	
	/***************************************** Autosuggest task list ***************************************/
	
	/* public function get_task_names_auto_suggestions($q){		
			
		    $this->db->select('task_Id,client_Id,task_name');
			$this->db->like('task_name', $q);
		    $this->db->where('status','Process');
			$this->db->group_by('task_name');
			$this->db->order_by('task_name', 'asc');
			$query = $this->db->get('task_details');
			$result = $query->result_array();
			
			if($result){

			  foreach ($result as $row){
				  
				  $eLogicClientTaskIds = array("374", "370","369","368","367","364","363","361","355",'14');
				  
				  if (!in_array($row['client_Id'], $eLogicClientTaskIds)) { 
				  
				  		$row_set[] = stripslashes($row['task_name']); //build an array	
				  
				  }
			  }
			  echo json_encode($row_set); //format the array into json data
			}
		
		} */


		public function get_task_names_auto_suggestions($q){		
			
			$taskNamesQ  	= $this->db->select('task_Id,task_name')->from('task_details')->where('status','Process')->group_by('task_name')->order_by('task_Id' , 'desc')->get();

			return $taskNamesQ->result();

		} 
	
/***************************************** Autosuggest task list ***************************************/	

}

