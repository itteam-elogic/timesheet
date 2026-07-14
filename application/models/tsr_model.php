<?php
/**
 * eLogivc Admin Panel for Codeigniter 
 * Author: Laxmikanth 
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tsr_Model extends CI_Model {

    public function __construct() {
	
			parent::__construct();   
	
	}


	
	public function getSearchTsrResult($params){ 

		$form_date		 =	 $params['form_date'];
        $to_date		 = 	 $params['to_date'];
		
		
		$empId =  $this->session->userdata['logged_in_timesheet']['empId']; // Loged in users		
		
		
			$this->db->select('er.* ,emp.empId,emp.name,c.client_Id,c.client_name,p.project_Id,p.project_name,t.task_Id,t.task_name')
			->from('emp_record_details er')
			->where('er.emp_report_dates >= ',$form_date)			
			->where('er.emp_report_dates <= ',$to_date)
			->where('er.empId != 421 AND er.empId != 450 AND er.empId !=411 AND er.empId !=410 AND er.empId !=140 AND er.empId !=416 AND er.empId !=46 AND er.empId !=446 AND er.empId !=456 AND er.empId !=452 AND er.empId !=384 AND er.empId !=384')
            ->join('employee_details as emp', 'emp.empId=er.empId', 'left')
			->join('client_details as c', 'c.client_Id=er.client_Id', 'left')
            ->join('project_details as p', 'p.project_Id=er.project_Id', 'left')
			->join('task_details as t', 't.task_Id=er.task_Id', 'left')
			->order_by('emp.name','asc')
            ->order_by('er.emp_record_id','desc');
			
			$recordsQ = $this->db->get(); 
			
			//echo $this->db->last_query();
			
			return $recordsQ->result();
			
	  }
	
	

}

