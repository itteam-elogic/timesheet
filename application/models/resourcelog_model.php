<?php
/**
 * eLogivc Admin Panel for Codeigniter 
 * Author: Laxmikanth 
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Resourcelog_Model extends CI_Model {

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
	   
	   		$this->db->insert_batch('resource_schedule_information', $data);

		//$emp_reportlog_id = $this->db->insert_id();
			
	   endif;
  
  } 
  
  
  public function getRecords($userType){ // List of employee enter the records based on users
	  
	  $ignoreMangerReporting = array(46,92,140,384,448,421,149,183);
  
        if($userType == 'developer'):
		    
			$empId =  $this->session->userdata['logged_in_timesheet']['empId'];
	  		
			
			$rsPermissionRoleofDeveloper = array('245','372','227','475','248','371','391','339','136','64','168','108','197','416','452','205','481','351','402');
	  	
	  		if(in_array($empId, $rsPermissionRoleofDeveloper)): 
				
				$this->db->select('emp.empId,emp.name,emp.reporting_manger,er.* ,c.client_Id,c.client_name,p.project_Id,p.project_name,p.empId,t.task_name');
				$this->db->from('employee_details emp'); 
				$this->db->join('resource_schedule_information er', 'er.team_member=emp.empId AND er.emp_report_dates = "'.date('Y-m-d').'"', 'left');		
				$this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
				$this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
				$this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
				$this->db->where_not_in('emp.reporting_manger', $ignoreMangerReporting);
				$this->db->where('emp.status' , 'Active');	
				$this->db->where('emp.reporting_manger !=' , '');			
				$this->db->order_by('emp.reporting_manger','asc');
	        
	        else: 
	  
	  				echo 'No Permission';
	  
	      endif;
			
		elseif($userType == 'manager'):
		
			
			//$exp_projectIds = implode(',' ,$getProjectId);
			
			/* $this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,p.empId,t.task_name');
            $this->db->from('resource_schedule_information er'); 
            $this->db->join('employee_details as emp', 'emp.empId=er.team_member', 'left');
			$this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
            $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
			$this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
			$this->db->where('er.emp_report_dates', date('Y-m-d'));
	  		//$this->db->order_by('er.resource_id','desc');
	  		//$this->db->order_by('er.emp_report_dates','desc'); 
	        $this->db->order_by('p.empId','asc');
			$this->db->order_by('er.emp_report_dates','desc'); 
			$this->db->order_by('er.project_manager','asc'); */


			/* 	$this->db->select('emp.empId,emp.name,emp.reporting_manger, rm.name as reporting_manager_name');
            $this->db->from('employee_details emp');
			$this->db->join('employee_details as rm', 'rm.empId=emp.reporting_manger', 'left');
	        $this->db->where('emp.status' , 'Active');	
			$this->db->where('emp.reporting_manger !=' , '');			
			$this->db->order_by('emp.reporting_manger','asc'); */	

			/* You want to display empty rows for team members who have not entered their resource data, 
			without matching with the employee table based on reporting manager. To achieve this, 
			you need to modify the query to include all team members from the employee table, and 
			then left join with the resource_schedule_information table. This will return all team members, 
			even if they don't have any resource data.*/

			$this->db->select('emp.empId,emp.name,emp.reporting_manger,er.* ,c.client_Id,c.client_name,p.project_Id,p.project_name,p.empId,t.task_name');
            $this->db->from('employee_details emp'); 
			$this->db->join('resource_schedule_information er', 'er.team_member=emp.empId AND er.emp_report_dates = "'.date('Y-m-d').'"', 'left');		
			$this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
            $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
			$this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
	  		$this->db->where_not_in('emp.reporting_manger', $ignoreMangerReporting);
			$this->db->where('emp.status' , 'Active');	
			$this->db->where('emp.reporting_manger !=' , '');			
			$this->db->order_by('emp.reporting_manger','asc'); 		   
		
              else:

				/* $this->db->select('emp.empId,emp.name,emp.reporting_manger,er.* ,c.client_Id,c.client_name,p.project_Id,p.project_name,p.empId,t.task_name');
				$this->db->from('employee_details emp'); 
				$this->db->join('resource_schedule_information er', 'er.team_member=emp.empId AND er.emp_report_dates = "'.date('Y-m-d').'"', 'left');		
				$this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
				$this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
				$this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
				$this->db->where('emp.status' , 'Active');	
				$this->db->where('emp.reporting_manger !=' , '');			
				$this->db->order_by('emp.reporting_manger','asc'); */

			$this->db->select('emp.empId,emp.name,emp.reporting_manger,er.* ,c.client_Id,c.client_name,p.project_Id,p.project_name,p.empId,t.task_name');
            $this->db->from('employee_details emp'); 
			$this->db->join('resource_schedule_information er', 'er.team_member=emp.empId AND er.emp_report_dates = "'.date('Y-m-d').'"', 'left');		
			$this->db->join('client_details as c', 'c.client_Id=er.client_Id', 'left');
            $this->db->join('project_details as p', 'p.project_Id=er.project_Id', 'left');
			$this->db->join('task_details as t', 't.task_Id=er.task_Id', 'left');
	  		$this->db->where_not_in('emp.reporting_manger', $ignoreMangerReporting);
			$this->db->where('emp.status' , 'Active');	
			$this->db->where('emp.reporting_manger !=' , '');			
			$this->db->order_by('emp.reporting_manger','asc'); 		   
      
          endif;
	  
		  $recordsQ = $this->db->get(); 
		//echo 'Project Manager----------------------'.$this->db->last_query(); exit;			
		 return $recordsQ->result();
  }
  
  
   public function getUpdateEmpRecords($resource_record_id){ //Get UPdate Records on Particular Users only
   
       $resouceSURQ  	= $this->db->select('*')->from('resource_schedule_information')->where('resource_id' , $resource_record_id)->get();
	  
	   return $resouceSURQ->result();
   
   
   }
   
   public function updateResourceRecords($data , $resource_id){ // Update Employee records    
   
   $this->db->where('resource_id', $resource_id);
		
	    $update = $this->db->update('resource_schedule_information', $data);
		
		if($update):
			
			  return true; 
			
		endif;
   
   }
	
	/*************************************************** Resource schedule search information *****************************/
	
public function getResourceData($params){

    $ignoreMangerReportingTeam = array(46,92,140,384,448,149,183,421);

    if(!empty($params)) {

        $form_date      = $params['form_date'];
        $to_date        = $params['to_date'];
        $departMent     = $params['department'];   // <-- ARRAY now
        $clientId       = $params['client_Id'];
        $project_manger = $params['project_manger'];
        $team_member    = $params['team_member'];

        $this->db->select('emp.empId, emp.name, emp.reporting_manger, er.*, 
                           c.client_Id, c.client_name, 
                           p.project_Id, p.project_name, p.empId, 
                           t.task_name');

        $this->db->from('employee_details emp');

        $this->db->join(
            'resource_schedule_information er',
            "er.team_member = emp.empId 
             AND er.emp_report_dates >= '$form_date' 
             AND er.emp_report_dates <= '$to_date'",
            'left'
        );

        $this->db->join('client_details as c', 'c.client_Id = er.client_Id', 'left');
        $this->db->join('project_details as p', 'p.project_Id = er.project_Id', 'left');
        $this->db->join('task_details as t', 't.task_Id = er.task_Id', 'left');

        $this->db->where('emp.status', 'Active');
        $this->db->where('emp.reporting_manger !=', '');
        $this->db->where_not_in('emp.reporting_manger', $ignoreMangerReportingTeam);

        /* ===================== FIXED DEPARTMENT FILTER ===================== */
   /* ===================== FINAL DEPARTMENT FILTER ===================== */
if (!empty($departMent)) {

    // MULTI SELECT
    if (is_array($departMent)) {

        // If "all" selected → no filter
        if (!in_array('all', $departMent)) {

            $this->db->group_start();

            foreach ($departMent as $dept) {

                if ($dept === 'MEP') {
                    // Match all MEP related departments
                    $this->db->or_like('er.department', 'MEP');
                } else {
                    $this->db->or_where('er.department', $dept);
                }
            }

            $this->db->group_end();
        }

    }
    // SINGLE SELECT
    else {

        if ($departMent === 'MEP') {
            $this->db->like('er.department', 'MEP');
        }
        elseif ($departMent !== 'all') {
            $this->db->where('er.department', $departMent);
        }
    }
}
/* ================================================================== */

        /* =================================================================== */

        if ($clientId != 'all') {
            $this->db->where('er.client_Id', $clientId);
        }

        if ($project_manger != 'all') {
            $this->db->where('emp.reporting_manger', $project_manger);
        }

        if ($team_member != 'all') {
            $this->db->where('emp.empId', $team_member);
        }

        $this->db->order_by('emp.reporting_manger', 'asc');

        $recordsQ = $this->db->get();
        // echo $this->db->last_query(); exit;

        return $recordsQ->result();
    }
}


	
	/*************************************************** Resource schedule search information *****************************/
	
	public function getManagerName($empId){
		
		if(!empty($empId)):
		
		$teamMembersQuery  = $this->db->select('empId,name')->from('employee_details')
								->where('empId',$empId)->order_by('name' , 'asc')->get()->result();
		
		foreach($teamMembersQuery as $managerResult){
			
			
			return $managerResult->name;
			
		}
		    
		
		endif;
						
		
		
	}
    
    
	
 }