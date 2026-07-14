<?php
/**
 * eLogivc Admin Panel for Codeigniter 
 * Author: Laxmikanth 
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ticket_Model extends CI_Model {

    public function __construct() {
	
			parent::__construct();   
	
	}
  

	// Read data using username and password
	
	
	public function add_ticket($data){  // Add Client Model 
	
	  if($data):
	   
	   		 $this->db->insert('tickets', $data);	
	   
	   endif;
	
	
	}
    
    public function getTickets( $ticket_id = NULL ){
        
      
            
       $logedInUser = $this->session->userdata['logged_in_timesheet']['empId'];
        
       $itMember = $this->session->userdata['logged_in_timesheet']['username'];    
            
        
        if($this->session->userdata['logged_in_timesheet']['user_type'] == 'admin'){
            
            $listofAllTicket = $this->db->select('*')
						->from('tickets')
						->order_by('ticket_id' , 'desc')->get();
		
		}elseif($itMember == 'suman'){
            
            $listofAllTicket = $this->db->select('*')
						->from('tickets')
						->order_by('ticket_id' , 'desc')->get();
        	
                
        }elseif($itMember == 'nagesh'){
            
            $listofAllTicket = $this->db->select('*')
						->from('tickets as t')                        
						->where('ticket_responsibility','nagesh')
                        ->or_where('emp_id','458')
						->order_by('ticket_id' , 'desc')->get();
            
        
        }elseif($itMember == 'vaibhav'){
            
            $listofAllTicket = $this->db->select('*')
						->from('tickets as t')
                       ->where('ticket_responsibility','vaibhav')
                        ->or_where('emp_id','411')
						->order_by('ticket_id' , 'desc')->get();
          
        
        }else{
                
                $listofAllTicket = $this->db->select('*')
						->from('tickets as t')
						->where('t.emp_id',$logedInUser)
						->order_by('ticket_id' , 'desc')->get();
                
            }
        
          return $listofAllTicket->result();
            
        
    }

	/** Allowed sort columns for tickets */
	private static $ticket_sort_columns = array('ticket_id', 'ticket_username', 'ticket_name', 'ticket_priority', 'ticket_desc', 'ticket_status', 'ticket_responsibility', 'ticket_raised_date', 'ticket_closed_date');

	private function _applyTicketSearch($search) {
		if (empty($search)) return;
		$q = trim($search);
		if ($q === '') return;
		$this->db->group_start()
			->like('ticket_username', $q)->or_like('ticket_name', $q)->or_like('ticket_desc', $q)
			->or_like('ticket_status', $q)->or_like('ticket_priority', $q)->or_like('ticket_responsibility', $q)
			->group_end();
	}

	/**
	 * Total count of tickets (same role-based logic as getTickets) for pagination.
	 */
	public function getTicketsCount($search = '') {
		$logedInUser = $this->session->userdata['logged_in_timesheet']['empId'];
		$itMember = $this->session->userdata['logged_in_timesheet']['username'];

		if ($this->session->userdata['logged_in_timesheet']['user_type'] == 'admin') {
			$this->db->select('ticket_id')->from('tickets');
			$this->_applyTicketSearch($search);
			return $this->db->get()->num_rows();
		}
		if ($itMember == 'suman') {
			$this->db->select('ticket_id')->from('tickets');
			$this->_applyTicketSearch($search);
			return $this->db->get()->num_rows();
		}
		if ($itMember == 'nagesh') {
			$this->db->select('ticket_id')->from('tickets as t')
				->where('ticket_responsibility', 'nagesh')
				->or_where('emp_id', '458');
			$this->_applyTicketSearch($search);
			return $this->db->get()->num_rows();
		}
		if ($itMember == 'vaibhav') {
			$this->db->select('ticket_id')->from('tickets as t')
				->where('ticket_responsibility', 'vaibhav')
				->or_where('emp_id', '411');
			$this->_applyTicketSearch($search);
			return $this->db->get()->num_rows();
		}
		$this->db->select('ticket_id')->from('tickets as t')->where('t.emp_id', $logedInUser);
		$this->_applyTicketSearch($search);
		return $this->db->get()->num_rows();
	}

	/**
	 * Paginated tickets (same role-based logic as getTickets) with limit, offset, search and sort.
	 */
	public function getTicketsPaginated($limit, $offset, $search = '', $sort_col = 'ticket_id', $sort_dir = 'desc') {
		$logedInUser = $this->session->userdata['logged_in_timesheet']['empId'];
		$itMember = $this->session->userdata['logged_in_timesheet']['username'];
		if (!in_array($sort_col, self::$ticket_sort_columns)) $sort_col = 'ticket_id';
		$sort_dir = (strtolower($sort_dir) === 'asc') ? 'asc' : 'desc';

		if ($this->session->userdata['logged_in_timesheet']['user_type'] == 'admin') {
			$this->db->select('*')->from('tickets');
			$this->_applyTicketSearch($search);
			$this->db->order_by($sort_col, $sort_dir)->limit($limit, $offset);
			return $this->db->get()->result();
		}
		if ($itMember == 'suman') {
			$this->db->select('*')->from('tickets');
			$this->_applyTicketSearch($search);
			$this->db->order_by($sort_col, $sort_dir)->limit($limit, $offset);
			return $this->db->get()->result();
		}
		if ($itMember == 'nagesh') {
			$this->db->select('*')->from('tickets as t')
				->where('ticket_responsibility', 'nagesh')
				->or_where('emp_id', '458');
			$this->_applyTicketSearch($search);
			$this->db->order_by($sort_col, $sort_dir)->limit($limit, $offset);
			return $this->db->get()->result();
		}
		if ($itMember == 'vaibhav') {
			$this->db->select('*')->from('tickets as t')
				->where('ticket_responsibility', 'vaibhav')
				->or_where('emp_id', '411');
			$this->_applyTicketSearch($search);
			$this->db->order_by($sort_col, $sort_dir)->limit($limit, $offset);
			return $this->db->get()->result();
		}
		$this->db->select('*')->from('tickets as t')->where('t.emp_id', $logedInUser);
		$this->_applyTicketSearch($search);
		$this->db->order_by($sort_col, $sort_dir)->limit($limit, $offset);
		return $this->db->get()->result();
	}
	
	
	public function getItReports($reportIT){
		
		if(!empty($reportIT['form_date'])) { 
			
			$form_date = $reportIT['form_date'];
			$to_date = $reportIT['to_date'];
			
			$listofAllTicket = $this->db->select('*')->from('tickets')->where('ticket_raised_date >= ',$form_date)->where('ticket_raised_date <= ',$to_date)->order_by('ticket_id' , 'desc')->get();
			
			//echo $this->db->last_query();
		}
			
			return $listofAllTicket->result();
		
	}

	/**
	 * Count of tickets for date range (for pagination), with optional search.
	 */
	public function getItReportsCount($reportIT, $search = '') {
		if (empty($reportIT['form_date'])) {
			return 0;
		}
		$form_date = $reportIT['form_date'];
		$to_date = $reportIT['to_date'];
		$this->db->select('ticket_id')->from('tickets')
			->where('ticket_raised_date >=', $form_date)
			->where('ticket_raised_date <=', $to_date);
		$this->_applyTicketSearch($search);
		return $this->db->get()->num_rows();
	}

	/**
	 * Paginated tickets for date range, with optional search and sort.
	 */
	public function getItReportsPaginated($reportIT, $limit, $offset, $search = '', $sort_col = 'ticket_id', $sort_dir = 'desc') {
		if (empty($reportIT['form_date'])) {
			return array();
		}
		$form_date = $reportIT['form_date'];
		$to_date = $reportIT['to_date'];
		if (!in_array($sort_col, self::$ticket_sort_columns)) $sort_col = 'ticket_id';
		$sort_dir = (strtolower($sort_dir) === 'asc') ? 'asc' : 'desc';
		$this->db->select('*')->from('tickets')
			->where('ticket_raised_date >=', $form_date)
			->where('ticket_raised_date <=', $to_date);
		$this->_applyTicketSearch($search);
		$this->db->order_by($sort_col, $sort_dir)->limit($limit, $offset);
		return $this->db->get()->result();
	}
    
    public function getUpdateTicketDetails($ticket_id){
        
        if(!empty($ticket_id)):
        
                $updateTicketInformation = $this->db->select('*')
						->from('tickets')
                        ->where('ticket_id' , $ticket_id)->get();
        
            return $updateTicketInformation->result();
        
         endif;
        
        
    }
    
    
    public function update_ticket($data , $ticket_id){ // Update ticket Functionality
      
  
  		$this->db->where('ticket_id', $ticket_id);
		
	    $update = $this->db->update('tickets', $data);
        
        
		
		if($update):
			
			  return true; 
        
        
			
		endif;
  
  }
    
    
 public function getticketType(){     
     
      $listofTicketType = $this->db->select('*')
						->from('ticket_common_task as ct')
						->order_by('sno' , 'asc')->get()->result();
     
     return $listofTicketType;     
     
 }    
  
	
	/* public function getTicket($projct_Id = NULL){
	
		 
		if(empty($projct_Id)):
		
			
	    if($this->session->userdata['logged_in_timesheet']['user_type'] == 'manager'){
			
		$logedInUser = $this->session->userdata['logged_in_timesheet']['empId'];
			
			$projectQ = $this->db->select('p.*,c.client_Id,c.client_name,e.name,e.empId')
						->from('project_details as p')
						->join('client_details as c ', 'p.client_Id = c.client_Id', 'left')
						->join('employee_details as e ', 'p.empId = e.empId', 'left')
						->where('p.empId',$logedInUser)
						->order_by('project_Id' , 'desc')->get();
																
		}else{
			
			$projectQ = $this->db->select('p.*,c.client_Id,c.client_name,e.name,e.empId')
						->from('project_details as p')
						->join('client_details as c ', 'p.client_Id = c.client_Id', 'left')
						->join('employee_details as e ', 'p.empId = e.empId', 'left')
						->where('e.status','Active')
						->order_by('project_Id' , 'desc')->get();
			
		}	
		 
		 
		 else:
		
		         $projectQ  	= $this->db->select('*')->from('project_details')->where('project_Id' , $projct_Id)->get();
		
		 endif; 
		
		 //echo $this->db->last_query();
		 
		 return $projectQ->result();
	
	}
	
	
	public function update_project($data , $projct_Id){ // Clent Update Functionality
  
  		$this->db->where('project_Id', $projct_Id);
		
	    $update = $this->db->update('project_details', $data);
		
		if($update):
			
			  return true; 
			
		endif;
  
  }
  
  
   public function delete_project($project_Id){
    
	 
	 	$this->db->where('project_Id', $project_Id)->delete('project_details');
		  
		$deleteQuery = $this->db->affected_rows();
			
	    echo  $deleteQuery;
  
  }
  
  
  
	public function recentProjects(){ // Recent Clients displaying angular js 
	
	    $projectQ 			=	 $this->db->select('p.*,c.client_Id,c.client_name')->from('project_details as p')
																 ->join('client_details as c ', 'p.client_Id = c.client_Id', 'left')
																  ->order_by('project_Id' , 'desc')->limit(5)->get();
		
		
	     return $projectQ->result();
		 
        //echo json_encode($recentQ->result());
		
		
	
	
	 }
	
	
	
	public function getProjectName($taskClientId){ // Displaying List of projects
	
	    if(empty($taskClientId)) :
			
			$projectNamesQ  	= $this->db->select('project_Id,project_name')->from('project_details')->where('status','Process')->order_by('project_Id' , 'desc')->get();
			
		else:
			
			 $projectNamesQ  	= $this->db->select('project_Id,project_name')->from('project_details')->where('status','Process')->where('client_Id',$taskClientId)->order_by('project_Id' , 'desc')->get();
			
		endif;  	
			
			  return $projectNamesQ->result();
			
			
		
	}
	
    */

}

