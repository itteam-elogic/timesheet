<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>

<?php foreach( $geteditinformation as $key => $getServiceData){ 

        $agreement_date=date_create($getServiceData->agreement_date);
        $total_est_cost_amt = ($getServiceData->est_cost * 20);
        $est_deliverable_dates = date_create($getServiceData->est_deliverable_dates);
      
} 

//Designation multiple values result 

        $monthlydesignatedName = $getServiceData->designated_consultants_name; // designation name
        
        $monthlydesignatedConsultName = explode(',', $monthlydesignatedName);
        
        $designated_start_date_service  = explode(',', $getServiceData->designated_start_date_service);
        
        $designated_end_date_service  = explode(',', $getServiceData->designated_end_date_service);
        
        $designated_desc_offered_services  = explode(',', $getServiceData->designated_desc_offered_services);

        $monthlyCWCode = explode(',', $getServiceData->country_wise_code);

        $monthlyCWPrice = explode(',', $getServiceData->est_cost);

//Designation multiple values result 
//echo 'kanth---'.$getServiceData->department;
?>
<?php //echo '<pre>'; print_r($geteditinformation); ?>
<script type="text/javascript" src="<?php echo HTTP_JS_PATH; ?>fckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo HTTP_JS_PATH; ?>fckeditor/sample.js"></script>
<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h1>Service Agreement Information</h1>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div>
                    <h4 class="line-head">Update Agreement Information</h4>
                    <span style="float:right; position:relative; top:-45px;"><a data-toggle="tooltip" title="Back To Service" href="<?php echo base_url('service_agreement/');?>"><img src="<?php echo HTTP_IMAGES_PATH;?>new.png"></a>
                    </span>
                </div>
                <div style="clear:both;"></div>
                <form class="form-horizontal" method="post" name="update_new_service" id="update_new_service" action="<?php echo base_url('service_agreement/updateMonthlyServiceDetails');?>">
                    <input type="hidden" name="agreement_update_id" id="agreement_update_id" value="<?=$getServiceData->servie_agreement_id?>">
                    <div class="row mb-20">
                        <div class="col-md-3">
                            <label>Country: </label>
                            <select class="form-control valid" id="country_code" name="country_code" onChange="getCountryWiseDetails(this.value);">
                                <option value="">Please choose Country Code</option>
                                <?php $getCountryCodeList = $this->service_agreement_model->getCountryCode();
                                 foreach($getCountryCodeList as $key => $getCode):
                                ?>
                                <option value="<?=$getCode->country_code_name;?>" <?php if ($getServiceData->country_code == $getCode->country_code_name) echo 'selected'; ?>><?=$getCode->country_name?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Agreement Company: <span class="required-star">*</span></label>
                             <div id="show_country_based_company">
                            <select class="form-control valid" id="agreement_company" name="agreement_company">
                                <option value="">Please choose Agreement Company</option>
                                <?php $getCompanyList = $this->service_agreement_model->getServiceCompanies($getServiceData->agreement_company,$getServiceData->country_code);
                                 foreach($getCompanyList as $key => $companyNames):
                                ?>
                                <option value="<?=$companyNames->country_company_name	?>" <?php if ($getServiceData->agreement_company == $companyNames->country_company_name	 ) echo 'selected' ; ?>><?=$companyNames->country_company_name	?></option>
                                <?php endforeach; ?>
                            </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <label>Project Code: </label>
                            <input class="form-control col-md-8" type="text" name="project_code" id="project_code" placeholder="Enter Project Code" value="<?=$getServiceData->project_code?>">
                        </div>
                        <div class="col-md-2">
                            <label>Date: </label>
                            <input class="form-control col-md-8" type="text" name="agreement_date" id="agreement_date" placeholder="Enter Agreement Date" value="<?=$getServiceData->agreement_date?>">
                        </div>
                        <div class="col-md-2">
                            <label>Department : <span class="required-star">*</span></label>                            
                            <select class="form-control valid" id="department" name="department">
                                  <option value="">Choose Department</option> 
                                  <option value="Architecture" <?php if($getServiceData->department == "Architecture") echo 'selected' ; ?>>Architecture & Structure</option>
                                <option value="MEP" <?php if($getServiceData->department == "MEP") echo 'selected' ; ?>>MEP</option>
                                <option value="Steel" <?php if($getServiceData->department == "Structural") echo 'selected';?>>Structural</option>
                                <option value="3D" <?php if($getServiceData->department == "3D") echo 'selected' ; ?>>3D</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-20">                        
                        <div class="col-md-3">
                            <label>Client Name: </label>
                            <input class="form-control col-md-8" type="text" name="client_name" id="client_name" placeholder="Enter Client Name" value="<?=$getServiceData->client_name?>">
                        </div> 
                        <div class="col-md-3">
                            <label>Signature: </label>
                            <select class="form-control valid" id="sow_signature" name="sow_signature">
                                <option value="">Choose Signature</option> 
                                <option value="rupali" <?php if ($getServiceData->sow_signature == "rupali" ) echo 'selected' ; ?>>Rupali Modi</option>
                                <option value="farhan" <?php if ($getServiceData->sow_signature == "farhan" ) echo 'selected' ; ?>>Syed Farhan</option>
                                <option value="chauhan" <?php if ($getServiceData->sow_signature == "chauhan" ) echo 'selected' ; ?>>Pradip Chauhan</option>
                                <option value="rahul" <?php if ($getServiceData->sow_signature == "rahul" ) echo 'selected' ; ?>>Rahul Kumar</option>
                                <option value="uppala" <?php if ($getServiceData->sow_signature == "uppala" ) echo 'selected' ; ?>>Naresh Uppala</option>
                            </select>
                            
                        </div>
                        <div class="col-md-6">
                            <label>Client Address: </label>
                            <textarea class="form-control col-md-8" type="text" name="client_adress" id="client_adress" placeholder="Enter Client Adress" rows="4"><?=$getServiceData->client_adress?></textarea>
                        </div>
                        
                    </div>                    
                    <div class="row mb-20">
                        <div class="col-md-3">
                            <label>Services: </label>
                            <input class="form-control col-md-8" type="text" name="project_name" id="project_name" placeholder="Enter Project Name" value="<?=$getServiceData->project_name?>">
                        </div>
						<div class="col-md-2">
                            <label>Lead Owner: </label>
                            <input class="form-control col-md-8" type="text" name="lead_owner" id="lead_owner" placeholder="Enter Lead Owner Name" value="<?=$getServiceData->lead_owner?>">
                        </div>
						<div class="col-md-2">
                            <label>Project Client Code: </label>
                            <input class="form-control col-md-8" type="text" name="project_client_code" id="project_client_code" placeholder="Enter Project Client Code" value="<?=$getServiceData->project_client_code?>">
                        </div>
                         <div class="col-md-5">
                            <label>Scope of the Work: </label>
                          <textarea class="form-control col-md-8" type="text" name="scope_of_the_work" id="scope_of_the_work" placeholder="Enter Scope of the Work" rows="4"><?=$getServiceData->scope_of_the_work?></textarea>
                        </div>
                    </div>
                    <div class="row mb-20"><div class="col-md-12"> <h4 class="line-head">Project Contact Details</h4></div></div>
                     <div class="row mb-20">
                        <div class="col-md-3">
                            <label>Contact Name: </label>
                             <input class="form-control col-md-8" type="text" name="project_contact_name" id="project_contact_name" placeholder="Enter project contact name" value="<?=$getServiceData->project_contact_name?>">
                        </div>
                         <div class="col-md-3">
                            <label>Email Id: </label>
                         <input class="form-control col-md-8" type="text" name="project_email_id" id="project_email_id" placeholder="Enter Project contact email id" value="<?=$getServiceData->project_email_id?>">
                        </div>
                         <div class="col-md-3">
                            <label>Contact Number: </label>
                         <input class="form-control col-md-8" type="text" name="project_contact_number" id="project_contact_number" placeholder="Enter Project contact number" value="<?=$getServiceData->project_contact_number?>">
                        </div>
                         <div class="col-md-3">
                            <label>Designation : </label>
                             <input class="form-control col-md-8" type="text" name="project_contact_designation" id="project_contact_designation" placeholder="Enter designation" value="<?=$getServiceData->project_contact_designation?>">
                        </div>
                    </div>
                    
                     <div class="row mb-20"><div class="col-md-12"> <h4 class="line-head">Billing Contact Details</h4></div></div>
                     <div class="row mb-20">
                        <div class="col-md-3">
                            <label>Contact Name: </label>
                             <input class="form-control col-md-8" type="text" name="billing_contact_name" id="billing_contact_name" placeholder="Enter billing contact name" value="<?=$getServiceData->billing_contact_name?>">
                        </div>
                         <div class="col-md-3">
                            <label>Email Id: </label>
                         <input class="form-control col-md-8" type="text" name="billing_email_id" id="billing_email_id" placeholder="Enter billing contact email id" value="<?=$getServiceData->billing_email_id?>">
                        </div>
                         <div class="col-md-3">
                            <label>Contact Number: </label>
                         <input class="form-control col-md-8" type="text" name="billing_contact_number" id="billing_contact_number" placeholder="Enter billing contact number" value="<?=$getServiceData->billing_contact_number?>">
                        </div>
                         <div class="col-md-3">
                            <label>Designation : </label>
                             <input class="form-control col-md-8" type="text" name="billing_contact_designation" id="billing_contact_designation" placeholder="Enter designation" value="<?=$getServiceData->billing_contact_designation?>">
                        </div>
                    </div>
                    
                    <div class="row mb-20"><div class="col-md-12"> <h4 class="line-head">LIST OF DESIGNATED CONSULTANTS:</h4></div></div>  
                    
                    <?php for($dc=0; $dc < count($monthlydesignatedConsultName); $dc++): ?> 
                    
					<div class="row mb-20">                        
                         <div class="col-md-2">
                            <label>Name: <span class="required-star">*</span></label>
                          <input class="form-control col-md-8" type="text" name="designated_consultants_name[]" id="designated_consultants_name" placeholder="Enter designated consultants name" rows="4" value="<?=$monthlydesignatedConsultName[$dc]?>">
                        </div>
						<div class="col-md-2">
                            <label>S.Date Services: <span class="required-star">*</span></label>
                          <input class="form-control col-md-8" type="text" name="designated_start_date_service[]" id="designated_start_date_service" placeholder="Enter start date for services" rows="4" value="<?=$designated_start_date_service[$dc];?>">
                        </div>
						<div class="col-md-2">
                            <label>E.D Services: <span class="required-star">*</span></label>
                          <input class="form-control col-md-8" type="text" name="designated_end_date_service[]" id="designated_end_date_service" placeholder="Enter end date for services" rows="4" value="<?=$designated_end_date_service[$dc];?>">
                        </div>						
						<div class="col-md-6">
                            <label>Desc of the offered service(s): <span class="required-star">*</span></label>
                          <input class="form-control col-md-8" type="text" name="designated_desc_offered_services[]" id="designated_desc_offered_services" placeholder="Description of the offered service(s)" rows="4" value="<?=$designated_desc_offered_services[$dc];?>">
                        </div>							
				    </div>
                    
					<div class="row mb-20">
						<div class="col-md-3">
						<label>Price <span class="required-star">*</span></label>
							<input class="form-control col-md-8" type="text" name="est_cost[]" id="est_cost" placeholder="Enter price" value="<?=$monthlyCWPrice[$dc]?>">
						</div>
						 <div class="col-md-3">
                            <label>Code: <span class="required-star">*</span></label>
                            <div id="get_amount_code">  
                          <select class="form-control valid" id="country_wise_code" name="country_wise_code[]">
                                 <?php $getCountryCodeList_22 = $this->service_agreement_model->getCountryCode();
                                 foreach($getCountryCodeList_22 as $key => $getCode_22):
                                ?>
                                <option value="<?=$getCode_22->amount_code;?>" <?php if ($monthlyCWCode[$dc] == $getCode_22->amount_code) echo 'selected'; ?>><?=$getCode_22->amount_code?></option>
                                <?php endforeach; ?>
                            </select>                           
                           </div>   
                        </div>
						<div class="col-md-3">
                            <label>Status: </label>
                            <select class="form-control valid" id="agreement_status" name="agreement_status">
                                <option value="">Please choose Status</option>
                                <option value="Pending" <?php if ($getServiceData->agreement_status == "Pending" ) echo 'selected' ; ?>>Pending</option>
                                <option value="Sent Sow" <?php if ($getServiceData->agreement_status == "Sent Sow" ) echo 'selected' ; ?>>Sent Sow</option>
                                <option value="Received" <?php if ($getServiceData->agreement_status == "Received" ) echo 'selected' ; ?>>Received</option>
                                 <option value="Trial project" <?php if ($getServiceData->agreement_status == "Trial project" ) echo 'selected' ; ?>>Trial project</option>
                                 <option value="No Sow" <?php if ($getServiceData->agreement_status == "No Sow" ) echo 'selected' ; ?>>No Sow</option>
                            </select>
						<span id="show_status_updated_date" style="font-size:14px; color:#FF0023; font-weight:bold;"></span> 
							 <?php if($getServiceData->agreement_status_updated_date!=''):
							 
							 echo 'Status Updated Date : '.$getServiceData->agreement_status_updated_date;
							 
							 endif; 
							 
							 ?>
                        </div>
                        <div class="col-md-3">
                            <label>Invoice Status: </label>
                            <select class="form-control valid" id="agreement_invoice_status" name="agreement_invoice_status">
                                <option value="">Please choose Status</option>
                                <option value="Sent Invoive" <?php if ($getServiceData->agreement_invoice_status == "Sent Invoive" ) echo 'selected' ; ?>>Sent Invoive</option>
                                <option value="Part Invoice Sent" <?php if ($getServiceData->agreement_invoice_status == "Part Invoice Sent" ) echo 'selected' ; ?>>Part Invoice Sent</option>
                                <option value="Part Payment received" <?php if ($getServiceData->agreement_invoice_status == "Part Payment received" ) echo 'selected' ; ?>>Part Payment received</option>
                                <option value="Payment received" <?php if ($getServiceData->agreement_invoice_status == "Payment received" ) echo 'selected' ; ?>>Payment received</option>
                            </select>
                        </div> 
						<!-- <div class="col-md-2">
						 <label>Click to More Fileds</label>
                           <button style="margin-top:4%;" class="btn btn-primary btn-sm addMore" type="button">Add More</button>   </div> --> 
                        </div>
                    
                    <?php endfor; ?>
                    
					<div class="card-footer" id="hideAfterOrgsumitButton">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-3">
                                <button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update</button>
                                <a class="btn btn-default icon-btn" href="<?php echo base_url('service_agreement');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a> </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Organizatoin form validation -->
<script type="text/javascript" language="javascript">
    // Wait for the DOM to be ready
    
    var editor = CKEDITOR.replace('scope_of_the_work');
   

    // Revalidate the textarea on change
    editor.on('change', function() {
        $('#scope_of_the_work').valid();
    });
 
    // Wait for the DOM to be ready
    
    $(function() {
        $("form[name='update_new_service']").validate({
             ignore: [],
            rules: {
                agreement_company : { required: true },
                country_code : { required : true },                
                project_code : { required : true },                
                agreement_date : { required : true },
                department     : { required : true },
                client_name : { required : true },
                sow_signature : { required : true }, 
                client_adress : { required : true },                
                project_name : {  required : true  },
                'scope_of_the_work': {
                    required: function(textarea) {
                      // update textarea
                      CKEDITOR.instances[textarea.id].updateElement();
                      // strip tags
                      var editorcontent = textarea.value.replace(/<[^>]*>/gi, '');
                      return editorcontent.length === 0;
                    }
                  },
                project_contact_name : {  required : true },                
                project_email_id : { required : true },                
                project_contact_number : { required : true },                
                billing_contact_name : { required : true },                
                billing_email_id : { required : true },                
                billing_contact_number : { required : true},  
                billing_contact_designation : { required : true },                
                est_cost : { required : true },
				country_wise_code : { required : true },
                designated_consultants_name : { required : true },
				designated_start_date_service : { required : true },
				designated_end_date_service : { required : true },
				designated_desc_offered_services : { required : true }
                 
            },
            messages: {
                agreement_company: "choose agreement company",
                country_code: "enter country code",
                project_code : "enter project code",
                agreement_date: "enter agreement date",
                department     : "choose department",
                client_name  : "enter client name",
                sow_signature  : "please choose signature",
                client_adress : "enter client address",
                project_name : "enter project name",
                scope_of_the_work : "enter scope of the work",
                project_contact_name : "enter project contact name",
                project_email_id     : "enter email id",
                project_contact_number : "enter contact number",
                billing_contact_name  : "enter billing contact name",
                billing_email_id : "enter billing email id",
                billing_contact_number : "enter billing contact number",
                billing_contact_designation : "enter designation",
				est_cost : "enter monthly amount",
				country_wise_code : "choose code of country",
                designated_consultants_name : "enter consultant name",
				designated_start_date_service :  "choose start date service",
				designated_end_date_service  : "choose end date service",
				designated_desc_offered_services : "enter description of offered services",
                agreement_status : "Choose Status"
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
  $('#agreement_date,#designated_start_date_service,#designated_end_date_service').datepicker({
      	dateFormat: "yy-mm-dd",
      	autoclose: true,
      	todayHighlight: true
      });
	
$( "#agreement_status" ) .change(function () {  // show_status_updated_date	
document.getElementById("show_status_updated_date").innerHTML= '<input class="form-control col-md-8"  type="text" name="agreement_status_updated_date" id="agreement_status_updated_date" placeholder="Enter Status Updated Date" value='+$.datepicker.formatDate('yy-mm-dd', new Date())+'>';
	
	 $('#agreement_status_updated_date').datepicker({
      	dateFormat: "yy-mm-dd",
      	autoclose: true,
      	todayHighlight: true
      });
	
});
	
$('#agreement_company,#country_code,#department,#agreement_status,#agreement_invoice_status').select2();	 // Autosuggest list on clients
    
    /******************** Country name to show all related information about the country and company ******************************************/
	
	 function getCountryWiseDetails(countryCode){
		 
		
		 $.ajax({
				type: "POST",
				url: "<?php echo base_url('service_agreement/getBasedOnCourntyWiseResult');?>",
				data:'countryCode='+countryCode,
			 	beforeSend: function() {
   	                $('#show_country_based_company').html('<i class="fa fa-spinner"></i>');
				},
				success: function(data){ 
					var getOCData = data;
				    var ajaxResult = getOCData.split('!@#88_');	
					if(ajaxResult[0]!=''){
					$("#show_country_based_company").html(ajaxResult[0]);
					$("#get_amount_code").html(ajaxResult[1]);
				   }else{
					   $("#show_country_based_company").html('<select class="form-control valid" id="agreement_company" name="agreement_company" disabled></select>');
				   }
					//$('#org_contact_name').select2();   
				}
        });
		 
	 }
  initSample(); // fckeditor sample js file function    
</script>
<!-- Organizatoin form validation -->
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->