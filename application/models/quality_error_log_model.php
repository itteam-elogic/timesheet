<?php
/**
 * eLogivc Admin Panel for Codeigniter 
 * Author: Laxmikanth 
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Quality_Error_Log_Model extends CI_Model {

    public function __construct() {
	
			parent::__construct();   
	
	}
	
	
public function addQtyErrorLogInformation($data){
		
		if($data):
	   
				$this->db->insert('quality_error_log', $data);	
	   
	   endif;
   }
	
	
	
public function getQualityErrorResult($limit = 20, $offset = 0){
		$this->db->select('qe.*, emp.empId as error_empId, emp.name as error_emp_name, c.client_Id, c.client_name, p.project_Id, p.project_name, p.project_type,p.empId as project_empId, project_creator.name as project_created_name');
            $this->db->from('quality_error_log qe');
            $this->db->join('employee_details as emp', 'emp.empId = qe.qty_empId', 'left');
            $this->db->join('client_details as c', 'c.client_Id = qe.qty_client_Id', 'left');
            $this->db->join('project_details as p', 'p.project_Id = qe.qty_project_Id', 'left');
            $this->db->join('employee_details as project_creator', 'project_creator.empId = p.empId', 'left');
            $this->db->order_by('qe.qty_error_id', 'desc');
            $this->db->limit($limit, $offset);

            $recordsQ = $this->db->get();
            return $recordsQ->result();
	
}

public function getQualityErrorCount(){
		return $this->db->count_all('quality_error_log');
}

public function getEmployeeNamesByIds($empIds = array()){
		if (empty($empIds)) {
			return array();
		}

		$rows = $this->db->select('empId,name')->from('employee_details')->where_in('empId', $empIds)->get()->result();
		$map = array();
		foreach ($rows as $row) {
			$map[$row->empId] = $row->name;
		}
		return $map;
}	

	
public function getEmployeeName($empIds){ // Getting List of Users
	
		if(!empty($empIds)): 
			
			 $exp_empIds = explode(',' ,$empIds);
	
			$employeeNQ  = $this->db->select('empId,username,name')->from('employee_details')->where_in('empId',$exp_empIds)->order_by('empId' , 'desc')->get()->result();
		 
			$empResult = '';
	
		 	 foreach($employeeNQ as $key => $employeeNQ){
				 
				 $empResult .=  ucwords($employeeNQ->name);
				 
			 }
	
				return  $empResult; 
	
		endif;
		
	}

	private function _get_datatables_query($post){
		$this->db->select('qe.*, emp.empId as error_empId, emp.name as error_emp_name, c.client_Id, c.client_name, p.project_Id, p.project_name, p.project_type, p.empId as project_empId, project_creator.name as project_created_name, reviewer.name as reviewer_name, selfchk.name as self_checker_display');
		$this->db->from('quality_error_log qe');
		$this->db->join('employee_details as emp', 'emp.empId = qe.qty_empId', 'left');
		$this->db->join('client_details as c', 'c.client_Id = qe.qty_client_Id', 'left');
		$this->db->join('project_details as p', 'p.project_Id = qe.qty_project_Id', 'left');
		$this->db->join('employee_details as project_creator', 'project_creator.empId = p.empId', 'left');
		$this->db->join('employee_details as reviewer', 'reviewer.empId = qe.reviewer_name', 'left');
		$this->db->join('employee_details as selfchk', 'selfchk.empId = qe.self_checker_name', 'left');

		if (!empty($post['search_value'])) {
			foreach ($post['column_search'] as $key => $item) {
				if ($key === 0) {
					$this->db->group_start();
					$this->db->like($item, $post['search_value']);
				} else {
					$this->db->or_like($item, $post['search_value']);
				}
				if (count($post['column_search']) - 1 == $key) {
					$this->db->group_end();
				}
			}
		}

		if (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']])) {
			$column = $post['column_order'][$post['order'][0]['column']];
			$this->db->order_by($column, $post['order'][0]['dir']);
		} else {
			$this->db->order_by('qe.qty_error_id', 'desc');
		}
	}

	public function get_datatables($post){
		$this->_get_datatables_query($post);
		if (isset($post['length']) && $post['length'] != -1) {
			$this->db->limit($post['length'], isset($post['start']) ? $post['start'] : 0);
		}
		$query = $this->db->get();
		return $query->result();
	}

	public function count_all($post){
		return $this->db->count_all('quality_error_log');
	}

	public function count_filtered($post){
		$this->_get_datatables_query($post);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function qualityViewInformation($qty_error_id){


		if(!empty($qty_error_id)){ 

			$qualityErrorLog  = $this->db->select('*')->from('quality_error_log')->where('qty_error_id',$qty_error_id)->get()->result();

			return $qualityErrorLog; 

		}



	}

	public function updateReviewerInformation($qty_error_id)
	{
		if (empty($qty_error_id)) {
			return array();
		}

		return $this->db->select('*')
			->from('quality_error_log')
			->where('qty_error_id', $qty_error_id)
			->get()
			->result();
	}

	public function updateReviewerQuery($qty_error_id, $data)
	{
		if (empty($qty_error_id) || empty($data) || !is_array($data)) {
			return false;
		}

		$data['updated_at'] = date('Y-m-d H:i:s');

		return $this->db->where('qty_error_id', $qty_error_id)->update('quality_error_log', $data);
	}

	public function update_analyzer_reviewer_status($qty_error_id , $status){ // Quality error log status updatation for Analyzer or Reviewer.
  	
		
		
		$update = $this->db->set('status',$status)->where('qty_error_id',$qty_error_id) ->update('quality_error_log');
		
		$userQ  = $this->db->select('qty_error_id,status')->from('quality_error_log')->where('qty_error_id' , $qty_error_id)->get()->result();

		//log_message('debug', $this->db->last_query());
		
		foreach($userQ as $key => $getStatus ) { 
		
		   if($getStatus->status == 'Yes'){
			   
			  $activeClass =  'fa fa-check-circle label label-success';
			   
		   }else{
		   
				$activeClass =  'fa fa-close label label-danger';
		   
		   }			   
			   
		 	//echo  "<a class='".$activeClass."' style=cursor:pointer; onClick=update_emp_status(".$getStatus->qty_error_id.",'".$getStatus->status."')> ".$getStatus->status."</a>"; 
            
            echo  "<a class='".$activeClass."' style=cursor:pointer; title='Click to toggle status' onClick=update_emp_status(".$getStatus->qty_error_id.",'".$getStatus->status."')> ".$getStatus->status." </a>"; 
		
		}
  }
	
	
	
public function getListOfProjectsWithClient($client_Id){ // Get Dropdown client wise projects  with out all feature
	
		  
	  $getProjects  = $this->db->select('*')->from('project_details')->where('status !=', 'Closed')->where('client_Id', $client_Id)->where('status','Process')->get()->result();
	  
	   echo '<option value="">Please Select Project</option>';
	  
	   foreach($getProjects as $key => $getResult):
	   
        $hideGeneral = str_replace(" - (General)", "",$getResult->project_name);
        
            if($hideGeneral == $getResult->project_name){


               echo '<option value='.$getResult->project_Id.'>'.$getResult->project_name.'</option>';


            }
	   
	   endforeach;
	 
	  //return $getStates;
		 
	}  // Get Dropdown client wise projects 
    
    
public function qualitySearchReportQuery($search_data){
				
		$department        = $search_data['department'];
        $project_manager   = $search_data['project_manager'];
        $analyzer          = $search_data['analyzer'];
        $client            = $search_data['client'];
        $self_checker      = $search_data['self_checker'];
        $form_date         = $search_data['form_date'];
        $to_date           = $search_data['to_date'];

        // Start building the query
        $this->db->select('qr.*, emp.empId, emp.name, self.empId, self.name as selfcheckerName, reviewer.empId, reviewer.name as reviewerName, c.client_Id, c.client_name, p.project_Id, p.project_name,p.project_type,p.empId as project_empId, project_creator.name as project_created_name')
            ->from('quality_error_log qr')
            ->join('employee_details as emp', 'emp.empId = qr.qty_empId', 'left')
            ->join('employee_details as self', 'self.empId = qr.self_checker_name', 'left')
            ->join('employee_details as reviewer', 'reviewer.empId = qr.reviewer_name', 'left')
            ->join('client_details as c', 'c.client_Id = qr.qty_client_Id', 'left')
            ->join('project_details as p', 'p.project_Id = qr.qty_project_Id', 'left')
            ->join('employee_details as project_creator', 'project_creator.empId = p.empId', 'left'); // this is the new join
    
   

        // Apply date range condition if provided
        if ($form_date && $to_date) {
            $this->db->where('qr.analyzer_report_date >=', $form_date)
                     ->where('qr.analyzer_report_date <=', $to_date);
        }

        // Apply department filter if provided
        if ($department && $department !== 'all') {
            $this->db->where('c.department', $department);
        }

        // Apply project manager filter if provided
        if ($project_manager && $project_manager !== 'all') {
            $this->db->where('p.empId', $project_manager);
        }

        // Apply analyzer filter if provided
        if ($analyzer && $analyzer !== 'all') {
            $this->db->where('qr.analyzer_name', $analyzer);
        }

        // Apply client filter if provided
        if ($client && $client !== 'all') {
            $this->db->where('qr.qty_client_Id', $client);
        }

        // Apply self checker filter if provided
        if ($self_checker && $self_checker !== 'all') {
            $this->db->where('qr.self_checker_name', $self_checker);
        }

        // Order the results by error ID
        $this->db->order_by('qr.qty_error_id', 'desc');

        // Execute the query
        $recordsQ = $this->db->get();

        //echo $this->db->last_query();

        // Return the result
        return $recordsQ->result();
        
        }

	    
	
	
	
}