<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service_Agreement extends CI_Controller {

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
		$this->load->helper('text');
		$this->load->library('numbertowords');
        // Load database		
		$this->load->model('timesheet_login');
		$this->load->model('service_agreement_model');
        $this->load->model('project_model');
        $this->load->model('client_model');
		
        if(empty($this->session->userdata['logged_in_timesheet'])){
		
			redirect('home/login');
		}
		
		
    }
	
	public function index(){
		
			$data['getServieAgreement'] = $this->service_agreement_model->getAllServieAgreements();
			
            $this->load->view('agreement/service_agreement' , $data);
            //$this->load->view('agreement/service_agreement');
			
	}
    
    public function add_service_agreement(){
        
        $this->load->view('agreement/add_hourly_service_agreement');
        
    }
	
	  public function addNewService(){
        
        /* This condition checking drop down amount or other amount value */
        
          if($this->input->post('country_wise_rate') == 'other_amt_value'):
            
                $hourlyBasedAmt =  $this->input->post('country_wise_rate_other');
        
          else:    
                
                $hourlyBasedAmt =  $this->input->post('country_wise_rate');
        
           endif;

           $projectName = $this->input->post('project_name');
           list($projectId, $projectName) = explode('@2222@', $projectName);           
        /* This condition checking drop down amount or other amount value */
        
         $data = array(
                'empId'						   => $this->session->userdata['logged_in_timesheet']['empId'], 
                'agreement_company' 		   => $this->input->post('agreement_company'),
                'country_code' 				   => $this->input->post('country_code'),
                'project_code' 		           => $this->input->post('project_code'),
                'agreement_date'			   => $this->input->post('agreement_date'),
                'department'                   => $this->input->post('department'),
                'client_name'				   => $this->input->post('client_name'),
                'client_adress'				   => $this->input->post('client_adress'),
                'project_name'				   => $projectName,
                'scope_of_the_work'			   => $this->input->post('scope_of_the_work'),
                'deliverables'                 => $this->input->post('deliverables'),
                'provided_by_client_info'      => $this->input->post('provided_by_client_info'),
                'project_contact_name'         => $this->input->post('project_contact_name'),
                'project_email_id'             => $this->input->post('project_email_id'),
                'project_contact_number'       => $this->input->post('project_contact_number'),
                'project_contact_designation'  => $this->input->post('project_contact_designation'),
                'billing_contact_name'         => $this->input->post('billing_contact_name'),
                'billing_email_id'             => $this->input->post('billing_email_id'),
                'billing_contact_number'       => $this->input->post('billing_contact_number'),
                'billing_contact_designation'  => $this->input->post('billing_contact_designation'),
                'total_est_hours'              => $this->input->post('total_est_hours'),
               // 'est_cost'                     => $this->input->post('est_cost'),
                'country_wise_rate'            => $hourlyBasedAmt,
                'country_wise_code'            => $this->input->post('country_wise_code'),
			    'sow_discount'				   => $this->input->post('sow_discount'),
                'est_deliverable_dates'        => $this->input->post('est_deliverable_dates'),
                'est_deliverable_text'         => $this->input->post('est_deliverable_text'),
			 	'agreement_invoice_status'     => $this->input->post('agreement_invoice_status'),
                'agreement_status'             => $this->input->post('agreement_status'),
                'est_remarks'                  => $this->input->post('est_remarks'),
			 	'lead_owner'                   => $this->input->post('lead_owner'),
			 	'project_client_code'          => $this->input->post('project_client_code'),
                'sow_signature'                => $this->input->post('sow_signature'),
                'created_by'    	           => date('Y-m-d H:i:s'),
                'updated_by' 		           => date('Y-m-d H:i:s')
			);		
	
	     $this->service_agreement_model->add_new_service_agreement_details($data);
		
		 redirect('service_agreement');
        
    }
    
    public function edit_service_details(){
        
         $serviceId = $this->input->get('servie_agreement_id');
          
         if(!empty($serviceId)):
        
            $data['geteditinformation'] = $this->service_agreement_model->getAllServieAgreements($serviceId);
			
            $this->load->view('agreement/edit_hourly_service_agreement' , $data);
         
           endif;
        
        
    }
    
    
    public function updateServiceDetails(){ // update the service agreement details for particular service id
        
        
        $agreement_update_id = $this->input->post('agreement_update_id');
        
        /* This condition checking drop down amount or other amount value */
        
          if($this->input->post('country_wise_rate') == 'other_amt_value'):
            
                $hourlyBasedAmt =  $this->input->post('country_wise_rate_other');
        
          else:    
                
                $hourlyBasedAmt =  $this->input->post('country_wise_rate');
        
           endif;
        
        /* This condition checking drop down amount or other amount value */
        
        if(!empty($agreement_update_id)):
		
		if($this->input->post('agreement_status_updated_date')){
			
			$agreementSUpDate = $this->input->post('agreement_status_updated_date');
			
		}else{
			
			$agreementSUpDate = '';
		}
        
		  $data = array(
                'agreement_company' 		   => $this->input->post('agreement_company'),
                'country_code' 				   => $this->input->post('country_code'),
                'project_code' 		           => $this->input->post('project_code'),
                'agreement_date'			   => $this->input->post('agreement_date'),
                'department'                   => $this->input->post('department'),
                'client_name'				   => $this->input->post('client_name'),
                'client_adress'				   => $this->input->post('client_adress'),
                'project_name'				   => $this->input->post('project_name'),
                'scope_of_the_work'			   => $this->input->post('scope_of_the_work'),
                'deliverables'                 => $this->input->post('deliverables'),
                'provided_by_client_info'      => $this->input->post('provided_by_client_info'),
                'project_contact_name'         => $this->input->post('project_contact_name'),
                'project_email_id'             => $this->input->post('project_email_id'),
                'project_contact_number'       => $this->input->post('project_contact_number'),
                'project_contact_designation'  => $this->input->post('project_contact_designation'),
                'billing_contact_name'         => $this->input->post('billing_contact_name'),
                'billing_email_id'             => $this->input->post('billing_email_id'),
                'billing_contact_number'       => $this->input->post('billing_contact_number'),
                'billing_contact_designation'  => $this->input->post('billing_contact_designation'),
                'total_est_hours'              => $this->input->post('total_est_hours'),
                //'est_cost'                     => $this->input->post('est_cost'),
                'country_wise_rate'            => $hourlyBasedAmt,
                'country_wise_code'            => $this->input->post('country_wise_code'),
			  	'sow_discount'				   => $this->input->post('sow_discount'),
                'est_deliverable_dates'        => $this->input->post('est_deliverable_dates'),
                'est_deliverable_text'         => $this->input->post('est_deliverable_text'),
                'est_remarks'                  => $this->input->post('est_remarks'),
                'agreement_invoice_status'     => $this->input->post('agreement_invoice_status'),
                'agreement_status'             => $this->input->post('agreement_status'),
			  	'agreement_status_updated_date' =>  $agreementSUpDate,
			  	'lead_owner'                  => $this->input->post('lead_owner'),
			 	'project_client_code'          => $this->input->post('project_client_code'),
                'sow_signature'                => $this->input->post('sow_signature'),
                'updated_by' 		           => date('Y-m-d H:i:s')
			);		
	
	     $this->service_agreement_model->update_service_agreement_details($data,$agreement_update_id );		
		 redirect('service_agreement');
        
        endif;
        
         
        
    }
    
    
    public function agreementDetails(){
        
       $servie_agreement_id = $this->input->get('servie_agreement_id'); //we have to get service agreement id to fech particular information.
          
       if(!empty($servie_agreement_id)) :
        
            $data['getServieAgreement'] = $this->service_agreement_model->getAllServieAgreements($servie_agreement_id);
			
            $this->load->view('agreement/agreement_details' , $data);
        
            //$this->load->view('agreement/agreement_details');
        
        endif;
        
        
    }
    
    
 /* Showing country wise related filds information dispalying section using ajax feature */
    
    public function getBasedOnCourntyWiseResult(){
        
       $countryCode = $this->input->post('countryCode');
		
		if(!empty($countryCode)){		
			
			
			    $getSCDropbox = $this->service_agreement_model->getCountryBasedCompanyResult($countryCode);
			
			
		}
        
        
    }
    
/* Showing country wise related filds information dispalying section using ajax feature */

    
 /* Showing country wise related filds information dispalying section using ajax feature */
    
    public function getBasedOnCourntyWiseMonthlyResult(){
        
       $countryCode = $this->input->post('countryCode');
		
		if(!empty($countryCode)){		
			
			
			$getSCDropbox = $this->service_agreement_model->getCountryBasedCompanyMonthlyResult($countryCode);
			
			
		}
        
        
    }
    
/* Showing country wise related filds information dispalying section using ajax feature */    
    
    
 public function remove_service_agreement(){
     
     $rserviceAId = $this->input->post('sAId'); 
     
     if(!empty($rserviceAId)):
     
        $deleteModel = $this->service_agreement_model->removeServiceAgreement($rserviceAId);
     
     endif;
     
 }    
    
    public function getSowClientsList(){ 
	
      if (isset($_GET['term'])){
		  $q = strtolower($_GET['term']);
		  $this->service_agreement_model->getAutoSowClientList($q);
		}
   }
	
	
	public function invoiceDetails(){		
		
		$servie_agreement_id = $this->input->get('servie_agreement_id'); //we have to get service agreement id to fech particular information.
          
       if(!empty($servie_agreement_id)) :
        
            $data['getInvoice'] = $this->service_agreement_model->getAllServieAgreements($servie_agreement_id);
			
            $this->load->view('agreement/invoice' , $data);
        
            //$this->load->view('agreement/agreement_details');
        
        endif;
		
	}
/************************* Monthly Service Agreement Functionality *******************************/
	
	public function monthlyServiceAgreement(){
		
		$this->load->view('agreement/add_monthly_service_agreement');
		
	}
	
	public function addMonthlyService(){ // saving monthly agreement
        
        
        $projectName = $this->input->post('project_name');
        list($projectId, $projectName) = explode('@2222@', $projectName);  
        
        
        
        $data = array(
                'empId'					 => $this->session->userdata['logged_in_timesheet']['empId'],
			 	'service_type'			       => 'monthly',
                'agreement_company' 		   => $this->input->post('agreement_company'),
                'country_code' 				   => $this->input->post('country_code'),
                'project_code' 		           => $this->input->post('project_code'),
                'agreement_date'			   => $this->input->post('agreement_date'),
                'department'                   => $this->input->post('department'),
                'client_name'				   => $this->input->post('client_name'),
                'client_adress'				   => $this->input->post('client_adress'),
                'project_name'				   =>  $projectName,
                'scope_of_the_work'			   => $this->input->post('scope_of_the_work'),
                'project_contact_name'         => $this->input->post('project_contact_name'),
                'project_email_id'             => $this->input->post('project_email_id'),
                'project_contact_number'       => $this->input->post('project_contact_number'),
                'project_contact_designation'  => $this->input->post('project_contact_designation'),
                'billing_contact_name'         => $this->input->post('billing_contact_name'),
                'billing_email_id'             => $this->input->post('billing_email_id'),
                'billing_contact_number'       => $this->input->post('billing_contact_number'),
                'billing_contact_designation'  => $this->input->post('billing_contact_designation'),
			 	'designated_consultants_name'  => implode(',',$this->input->post('designated_consultants_name')),
			 	'designated_start_date_service' => implode(',',$this->input->post('designated_start_date_service')),
			 	'designated_end_date_service'  => implode(',',$this->input->post('designated_start_date_service')),
			 	'designated_desc_offered_services' => implode(',',$this->input->post('designated_desc_offered_services')),
                'est_cost'                     => implode(',',$this->input->post('est_cost')),
                'country_wise_code'            => implode(',',$this->input->post('country_wise_code')),
			    'created_by'    	           => date('Y-m-d H:i:s'),
			 	'lead_owner'                  => $this->input->post('lead_owner'),
			 	'project_client_code'         => $this->input->post('project_client_code'),
                'sow_signature'                => $this->input->post('sow_signature'),
			 	'created_by'    	           => date('Y-m-d H:i:s'),
                'updated_by' 		           => date('Y-m-d H:i:s')
			);		
            //echo '<pre>'; print_r( $data); exit;
			$this->service_agreement_model->add_new_service_agreement_details($data);
		
        redirect('service_agreement');
        
    }
	
	public function edit_monthly_service_details(){
        
         $serviceId = $this->input->get('servie_agreement_id');
          
         if(!empty($serviceId)):
        
            $data['geteditinformation'] = $this->service_agreement_model->getAllServieAgreements($serviceId);
			
            $this->load->view('agreement/edit_monthly_service_agreement' , $data);
         
           endif;
        
        
    }
	
	public function updateMonthlyServiceDetails(){ // update the service agreement details for particular service id
        
        
        $agreement_update_id = $this->input->post('agreement_update_id');
        
        if(!empty($agreement_update_id)):
		
		if($this->input->post('agreement_status_updated_date')){
			
			$agreementSUpDate = $this->input->post('agreement_status_updated_date');
			
		}else{
			
			$agreementSUpDate = '';
		}
        
          $data = array(
                'agreement_company' 		   => $this->input->post('agreement_company'),
                'country_code' 				   => $this->input->post('country_code'),
                'project_code' 		           => $this->input->post('project_code'),
                'agreement_date'			   => $this->input->post('agreement_date'),
                'department'                   => $this->input->post('department'),
                'client_name'				   => $this->input->post('client_name'),
                'client_adress'				   => $this->input->post('client_adress'),
                'project_name'				   => $this->input->post('project_name'),
                'scope_of_the_work'			   => $this->input->post('scope_of_the_work'),
                'project_contact_name'         => $this->input->post('project_contact_name'),
                'project_email_id'             => $this->input->post('project_email_id'),
                'project_contact_number'       => $this->input->post('project_contact_number'),
                'project_contact_designation'  => $this->input->post('project_contact_designation'),
                'billing_contact_name'         => $this->input->post('billing_contact_name'),
                'billing_email_id'             => $this->input->post('billing_email_id'),
                'billing_contact_number'       => $this->input->post('billing_contact_number'),
                'billing_contact_designation'  => $this->input->post('billing_contact_designation'),
			 	'designated_consultants_name'  => implode(',',$this->input->post('designated_consultants_name')),
			 	'designated_start_date_service' => implode(',',$this->input->post('designated_start_date_service')),
			 	'designated_end_date_service'  => implode(',',$this->input->post('designated_start_date_service')),
			 	'designated_desc_offered_services' => implode(',',$this->input->post('designated_desc_offered_services')),
                'est_cost'                     => implode(',',$this->input->post('est_cost')),
                'country_wise_code'            => implode(',',$this->input->post('country_wise_code')),
			  	'agreement_invoice_status'     => $this->input->post('agreement_invoice_status'),
                'agreement_status'             	=> $this->input->post('agreement_status'),
			  	'agreement_status_updated_date' =>$agreementSUpDate,
			  	'lead_owner'                  	=> $this->input->post('lead_owner'),
			 	'project_client_code'         	=> $this->input->post('project_client_code'),
                'sow_signature'                => $this->input->post('sow_signature'),
			  	'updated_by' 		           	=> date('Y-m-d H:i:s')
			   );		
	
        echo '<pre>'; print_r($data);
        
	     //$this->service_agreement_model->update_service_agreement_details($data,$agreement_update_id );		
		 redirect('service_agreement');
        
        endif;
        
         
        
    }
	
/************************* Monthly Service Agreement Functionality ********************************/
	
/****************************** Agreement Clone feature ******************************************/
	
	public function cloneData(){
		
         $cloneserviceId = $this->input->get('servie_agreement_id');
		
		 if(!empty($cloneserviceId)):
		 
			$getCloneStoreData = $this->service_agreement_model->getAllServieAgreements($cloneserviceId);
		
		if($getCloneStoreData[0]->service_type == 'monthly'){			
			
		
		$data = array(
                'empId'					 => $this->session->userdata['logged_in_timesheet']['empId'],
			 	'service_type'			       => 'monthly',
                'agreement_company' 		   => $getCloneStoreData[0]->agreement_company,
                'country_code' 				   => $getCloneStoreData[0]->country_code,
                'project_code' 		           => $getCloneStoreData[0]->project_code,
                'agreement_date'			   => $getCloneStoreData[0]->agreement_date,
                'department'                   => $getCloneStoreData[0]->department,
                'client_name'				   => $getCloneStoreData[0]->client_name,
                'client_adress'				   => $getCloneStoreData[0]->client_adress,
                'project_name'				   => $getCloneStoreData[0]->project_name,
                'scope_of_the_work'			   => $getCloneStoreData[0]->scope_of_the_work,
                'project_contact_name'         => $getCloneStoreData[0]->project_contact_name,
                'project_email_id'             => $getCloneStoreData[0]->project_email_id,
                'project_contact_number'       => $getCloneStoreData[0]->project_contact_number,
                'project_contact_designation'  => $getCloneStoreData[0]->project_contact_designation,
                'billing_contact_name'         => $getCloneStoreData[0]->billing_contact_name,
                'billing_email_id'             => $getCloneStoreData[0]->billing_email_id,
                'billing_contact_number'       => $getCloneStoreData[0]->billing_contact_number,
                'billing_contact_designation'  => $getCloneStoreData[0]->billing_contact_designation,
			 	'designated_consultants_name'  => $getCloneStoreData[0]->designated_consultants_name,
			 	'designated_start_date_service' => $getCloneStoreData[0]->designated_start_date_service,
			 	'designated_end_date_service'  => $getCloneStoreData[0]->designated_end_date_service,
			 	'designated_desc_offered_services' => $getCloneStoreData[0]->designated_desc_offered_services,
                'est_cost'                     => $getCloneStoreData[0]->est_cost,
                'country_wise_code'            => $getCloneStoreData[0]->country_wise_code,
			    'created_by'    	           => date('Y-m-d H:i:s'),				
			 	'updated_by' 		            => date('Y-m-d H:i:s')
			);		
	
			$this->service_agreement_model->add_new_service_agreement_details($data);
		
		 	echo "<script>alert('You have Successuly clone the record!!!!!');window.location.href='/elogic_timesheet/service_agreement';</script>";
			
			
		}else{ 
        
         $data = array(
                'empId'						   => $this->session->userdata['logged_in_timesheet']['empId'], 
                'agreement_company' 		   => $getCloneStoreData[0]->agreement_company,
                'country_code' 				   => $getCloneStoreData[0]->country_code,
                'project_code' 		           => $getCloneStoreData[0]->project_code,
                'agreement_date'			   => $getCloneStoreData[0]->agreement_date,
                'department'                   => $getCloneStoreData[0]->department,
                'client_name'				   => $getCloneStoreData[0]->client_name,
                'client_adress'				   => $getCloneStoreData[0]->client_adress,
                'project_name'				   => $getCloneStoreData[0]->project_name,
                'scope_of_the_work'			   => $getCloneStoreData[0]->scope_of_the_work,
                'deliverables'                 => $getCloneStoreData[0]->deliverables,
                'provided_by_client_info'      => $getCloneStoreData[0]->provided_by_client_info,
                'project_contact_name'         => $getCloneStoreData[0]->project_contact_name,
                'project_email_id'             => $getCloneStoreData[0]->project_email_id,
                'project_contact_number'       => $getCloneStoreData[0]->project_contact_number,
                'project_contact_designation'  => $getCloneStoreData[0]->project_contact_designation,
                'billing_contact_name'         => $getCloneStoreData[0]->billing_contact_name,
                'billing_email_id'             => $getCloneStoreData[0]->billing_email_id,
                'billing_contact_number'       => $getCloneStoreData[0]->billing_contact_number,
                'billing_contact_designation'  => $getCloneStoreData[0]->billing_contact_designation,
                'total_est_hours'              => $getCloneStoreData[0]->total_est_hours,
               // 'est_cost'                     => $this->input->post('est_cost'),
                'country_wise_rate'            => $getCloneStoreData[0]->country_wise_rate,
                'country_wise_code'            => $getCloneStoreData[0]->country_wise_code,
			    'sow_discount'				   => $getCloneStoreData[0]->sow_discount,
                'est_deliverable_dates'        => $getCloneStoreData[0]->est_deliverable_dates,
			 	'agreement_invoice_status'     => $getCloneStoreData[0]->agreement_invoice_status,
                'agreement_status'             => $getCloneStoreData[0]->agreement_status,
                'est_remarks'                  => $getCloneStoreData[0]->est_remarks,
			 	'created_by'    	           => date('Y-m-d H:i:s'),
                'updated_by' 		           => date('Y-m-d H:i:s')
			);		
	
			
			
	  $this->service_agreement_model->add_new_service_agreement_details($data);
		
			echo "<script>alert('You have Successuly clone the record!!!!!');window.location.href='/elogic_timesheet/service_agreement';</script>";
			
	// redirect('service_agreement');
		
	  }	
		
	 endif;	
		
	}

	
/****************************** Agreement Clone feature ******************************************/
		
 /****************************************  Get Project master report information  dispalying section using ajax feature ******************************/
    
 public function getProjectDetailsBasedOnSow(){
        
    $projctId = $this->input->post('projctId');

    //echo 'test==='.$projctId; exit;
     
     if(!empty($projctId)){	         
         
             $getProjectInformation = $this->service_agreement_model->getProjectBasedDetails($projctId);         
         
     }
          
 }
 
/****************************************  Get Project master report information  dispalying section using ajax feature ******************************/
    
   
}