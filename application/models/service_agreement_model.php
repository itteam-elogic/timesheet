<?php
/**
 * eLogivc Admin Panel for Codeigniter 
 * Author: Laxmikanth 
 *
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Service_Agreement_Model extends CI_Model {

    public function __construct() {
	
			parent::__construct();   
	
	}
  

	// Read data using username and password
	
	
	public function add_new_service_agreement_details($data){  // Add Client Model 
	
	  if($data):
	   
	   		 $this->db->insert(' service_agreement_details', $data);	
	   
	   endif;
	
	
	}
    
    public function getAllServieAgreements($servie_agreement_id = NULL){
        
        if(empty($servie_agreement_id)):
        
            $loginUserId = $this->session->userdata['logged_in_timesheet']['empId']; 
          
           if( $this->session->userdata['logged_in_timesheet']['user_type'] == 'manager' ):
        
                if($this->session->userdata['logged_in_timesheet']['username'] == 'pradip'): //Department Architecture realated information
        
                $getAllService = $this->db->select('*')->from('service_agreement_details')->where('department' , 'Architecture')->order_by('servie_agreement_id' , 'desc')->get();
            
                       
                elseif($this->session->userdata['logged_in_timesheet']['username'] == 'farhan'): //Department MEP realated information
        
                $getAllService = $this->db->select('*')->from('service_agreement_details')->where_in('department' , 'MEP')->order_by('servie_agreement_id' , 'desc')->get();
		
		   elseif($loginUserId  == '183'):
		
				$getAllService = $this->db->select('*')->from('service_agreement_details')->order_by('updated_by' , 'desc')->get();
		
                 
               else:
                
                 $getAllService = $this->db->select('*')->from('service_agreement_details')->where('empId' , $loginUserId)->order_by('updated_by' , 'desc')->get();
                    
               endif;
        
             else:
        
            $getAllService = $this->db->select('*')->from('service_agreement_details')->order_by('updated_by' , 'desc')->get();
        
            endif;     
        
             return $getAllService->result();
        
        else:
        
            $getAllService = $this->db->select('*')->from('service_agreement_details')->where('servie_agreement_id',$servie_agreement_id)->order_by('updated_by','desc')->get();
         
            return $getAllService->result();
        
        endif;
        
    }
    
    
    public function removeServiceAgreement($removeSAId = NULL ){
        
        
        if(!empty($removeSAId)) :
        
            $removeServiceRecord = $this->db->where('servie_agreement_id', $removeSAId)->delete('service_agreement_details'); 
        
           return $removeServiceRecord;
        
        
        endif;
        
    }
    
    public function update_service_agreement_details($data,$agreement_update_id){
        
        
        $this->db->where('servie_agreement_id', $agreement_update_id);
		
	    $update = $this->db->update('service_agreement_details', $data);
		
		if($update):
			
			  return true; 
			
		endif;
        
        
    }
    
    
    public function getServiceCompanies($countryCompanyname,$countryCode){ // get List of all companies in database directly with model
        
        if(!empty($countryCompanyname)){
            
            
            $getCompanies = $this->db->select('country_company_name,country_code_name')->from('service_country_code')->where('country_company_name' , $countryCompanyname)->where('country_code_name',$countryCode)->get();
        
            return $getCompanies->result();
            
            
        }else{
        
        $getCompanies = $this->db->select('country_company_name')->from('service_country_code')->order_by('country_id' , 'desc')->get();
        
         return $getCompanies->result();
        }
    }
    
    
    public function getCountryCode(){
        
        $getCountryCode = $this->db->select('*')->from('service_country_code')->order_by('country_id' , 'desc')->get();
        
         return $getCountryCode->result();
        
    }
    
    
    
    public function getCountryBasedCompanyResult($countryCode){ // hourly based agreement country code
        
        
        $getCompany = $this->db->select('*')->from('service_country_code')
			->where('country_code_name',$countryCode)->get()->result();
        
        $getAmtCodeResult = $this->db->select('amount_code')->from('service_country_code')->get()->result();
		
			
		if(count($getCompany)!=0){
		
		echo '<select class="form-control" id="agreement_company" name="agreement_company">';
		
		foreach($getCompany as $getResult){
			
			echo '<option value="'.$getResult->country_company_name.'">'.$getResult->country_company_name.'</option>';
			
		}
		
		echo '</select>';
            
      echo '!@#88_<select class="form-control valid" id="country_wise_code" name="country_wise_code">';
        
            foreach($getAmtCodeResult as $getACResult){
                
                    if($getResult->amount_code == $getACResult->amount_code){
                        
                        $selected = 'selected';
                        
                    } else{
                        
                         $selected = '';
                    }
                        
                        echo '<option value="'.$getACResult->amount_code.'" '.$selected.'>'.$getACResult->amount_code.'</option>';
			
		          }
		echo '<option value="EUR">ERUO</option>';
		echo '</select>';
		
		
	}
        
    }
    
    //We have to get employee name
    
    public function getEmpName($empId){
        
        
        if(!empty($empId)):
        
          $employeeNQ  = $this->db->select('empId,username')->from('employee_details')->where('empId' , $empId)->get()->result();
        
          foreach($employeeNQ as $getUsename):
         
		       return ucfirst($getUsename->username);
        
          endforeach; 
        
        endif;
        
    }
    
    public function getAutoSowClientList($q){
		$this->db->select('DISTINCT(client_name)');
		$this->db->like('client_name', $q);
		$query = $this->db->get('service_agreement_details');
		$result = $query->result_array();
        if($result){
		  foreach ($result as $row){
			$row_set[] = htmlentities(stripslashes($row['client_name'])); //build an array
		  }
		  echo json_encode($row_set); //format the array into json data
		}
     }
	
	public function getProjectCodeAutoGeneration(){
		
		$getAllService = $this->db->select('project_code')->from('service_agreement_details')->where('service_type','monthly')->order_by('servie_agreement_id' , 'desc')->limit(1)->get()->result();
		
		foreach( $getAllService as $getProjectCode ){
			
			 return $getProjectCode->project_code + 1;
			
		}
		
	}
    
    
    public function getCountryBasedCompanyMonthlyResult($countryCode){
        
        
        $getCompany = $this->db->select('*')->from('service_country_code')
			->where('country_code_name',$countryCode)->get()->result();
        
        $getAmtCodeResult = $this->db->select('amount_code')->from('service_country_code')->get()->result();
		
			
		if(count($getCompany)!=0){
		
		echo '<select class="form-control" id="agreement_company" name="agreement_company">';
		
		foreach($getCompany as $getResult){
			
			echo '<option value="'.$getResult->country_company_name.'">'.$getResult->country_company_name.'</option>';
			
		}
		
		echo '</select>';
            
      echo '!@#88_<select class="form-control valid" id="country_wise_code" name="country_wise_code[]">';
        
            foreach($getAmtCodeResult as $getACResult){
                
                    if($getResult->amount_code == $getACResult->amount_code){
                        
                        $selected = 'selected';
                        
                    } else{
                        
                         $selected = '';
                    }
                        
                        echo '<option value="'.$getACResult->amount_code.'" '.$selected.'>'.$getACResult->amount_code.'</option>';
			
		          }
		echo '<option value="EUR">ERUO</option>';
		echo '</select>';
		
		
	}
        
    }
	

/************************************************* Get Project master report information ********************************************************************************* */

public function getProjectBasedDetails($projectId){ // hourly based agreement country code
        
        
    $projectQ = $this->db->select('p.project_Id,p.project_number,p.pc_code,p.project_contact_name,p.project_email_id,p.project_contact_number,c.client_name,c.department,e.name,e.empId')
    ->from('project_details as p')
    ->join('client_details as c ', 'p.client_Id = c.client_Id', 'left')
    ->join('employee_details as e ', 'p.empId = e.empId', 'left')
    ->where('p.project_Id', $projectId)->get()->result();
     //return $projectQ->result();

     foreach($projectQ as $getProjectData){


            echo $getProjectData->project_number.'@#_22'.$getProjectData->pc_code.'@#_22'.$getProjectData->client_name.'@#_22'.$getProjectData->department.'@#_22'.$getProjectData->project_contact_name.'@#_22'.$getProjectData->project_email_id.'@#_22'.$getProjectData->project_contact_number;


     }

    
}

/************************************************* Get Project master report information ********************************************************************************* */	
	
}

