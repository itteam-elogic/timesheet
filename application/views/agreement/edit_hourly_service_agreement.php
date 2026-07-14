<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>

<?php foreach( $geteditinformation as $key => $getServiceData){ 

        $agreement_date=date_create($getServiceData->agreement_date);
        $total_est_cost_amt = ($getServiceData->est_cost * 20);
        $est_deliverable_dates = date_create($getServiceData->est_deliverable_dates);
        $listOfHoulyRates = array(20,25,30);
        if (in_array($getServiceData->country_wise_rate, $listOfHoulyRates)){
                
                $showDisable = 'disabled';
            
                $valOfAmount = '';
            
             }else{
            
                  $showDisable = '';
                
                  $valOfAmount = $getServiceData->country_wise_rate;    
             }
  
} ?>
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
                <form class="form-horizontal" method="post" name="update_new_service" id="update_new_service" action="<?php echo base_url('service_agreement/updateServiceDetails');?>">
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
                                  <option value="Architecture" <?php if ($getServiceData->department == "Architecture") echo 'selected' ; ?>>Architecture & Structure</option>
                                <option value="MEP" <?php if ($getServiceData->department == "MEP") echo 'selected' ; ?>>MEP</option>
                                <option value="Steel" <?php if ($getServiceData->department == "Steel") echo 'selected';?>>Steel</option>
                                <option value="3D" <?php if ($getServiceData->department == "3D") echo 'selected' ; ?>>3D</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-20">                        
                        <div class="col-md-3">
                            <label>Client Name: </label>
                            <input class="form-control col-md-8" type="text" name="client_name" id="client_name" placeholder="Enter Client Name" value="<?=$getServiceData->client_name?>">
                        </div>
						<div class="col-md-2">
                            <label>Lead Owner: </label>
                            <input class="form-control col-md-8" type="text" name="lead_owner" id="lead_owner" placeholder="Enter Lead Owner Name" value="<?=$getServiceData->lead_owner?>">
                        </div>
						<div class="col-md-2">
                            <label>Project Client Code: </label>
                            <input class="form-control col-md-8" type="text" name="project_client_code" id="project_client_code" placeholder="Enter Project Client Code" value="<?=$getServiceData->project_client_code?>">
                        </div>
                         <div class="col-md-2">
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
                        <div class="col-md-3">
                            <label>Client Address: </label>
                            <textarea class="form-control col-md-8" type="text" name="client_adress" id="client_adress" placeholder="Enter Client Adress" rows="4"><?=$getServiceData->client_adress?></textarea>
                        </div>
                        
                    </div>                    
                    <div class="row mb-20">
                        <div class="col-md-3">
                            <label>Project Name: </label>
                            <input class="form-control col-md-8" type="text" name="project_name" id="project_name" placeholder="Enter Project Name" value="<?=$getServiceData->project_name?>">
                        </div>
                         <div class="col-md-9">
                            <label>Scope of the Work: </label>
                          <textarea class="form-control col-md-8" type="text" name="scope_of_the_work" id="scope_of_the_work" placeholder="Enter Scope of the Work" rows="4"><?=$getServiceData->scope_of_the_work?></textarea>
                        </div>
                    </div>
                     <div class="row mb-20">
                        <div class="col-md-6">
                            <label>Deliverables: </label>
                             <textarea class="form-control col-md-8" type="text" name="deliverables" id="deliverables" placeholder="Enter deliverables information" rows="4"><?=$getServiceData->deliverables?></textarea>
                        </div>
                         <div class="col-md-6">
                            <label>Provided By Client: </label>
                          <textarea class="form-control col-md-8" type="text" name="provided_by_client_info" id="provided_by_client_info" placeholder="Enter client informaton" rows="4"><?=$getServiceData->provided_by_client_info?></textarea>
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
                    
                    <div class="row mb-20"><div class="col-md-12"> <h4 class="line-head">Time and Cost Estimation</h4></div></div>
                     <div class="row mb-20">
                        <div class="col-md-3">
                            <label>Total Estimation Hours: </label>
                             <input class="form-control col-md-8" type="text" name="total_est_hours" id="total_est_hours" placeholder="Enter estimation hours" value="<?=$getServiceData->total_est_hours?>">
                        </div>
                       <!--  <div class="col-md-2">
                            <label>Cost </label>
                         <input class="form-control col-md-8" type="text" name="est_cost" id="est_cost" placeholder="Enter cost" value="<?=$getServiceData->est_cost?>">
                        </div> -->
                           <div class="col-md-3">
                            <label>Rate: </label>
                            <select class="form-control valid" id="country_wise_rate" name="country_wise_rate">
                                <option value="">Please choose Rate</option>
                                <option value="20" <?php if ($getServiceData->country_wise_rate == "20" ) echo 'selected' ; ?>>20</option>
                                <option value="25" <?php if ($getServiceData->country_wise_rate == "25" ) echo 'selected' ; ?>>25</option>
                                <option value="30" <?php if ($getServiceData->country_wise_rate == "30" ) echo 'selected' ; ?>>30</option>
                                <option value="other_amt_value" <?php if ($showDisable == "" ) echo 'selected' ; ?>>Enter Other Amount</option>
                            </select>
                        </div>                         
                         <div class="col-md-2">
                            <label>Other Amount: </label>
                             <input class="form-control col-md-8" type="text" name="country_wise_rate_other" id="country_wise_rate_other" placeholder="Enter hourly amount" value="<?=$valOfAmount;?>" <?=$showDisable;?>>
                          </div>
                         <div class="col-md-2">
                            <label>Discount Price: </label>
                            <input class="form-control col-md-8" type="text" name="sow_discount" id="sow_discount" placeholder="Enter discount amount" value="<?php echo $getServiceData->sow_discount;?>">
                        </div>
                          <div class="col-md-2">
                            <label>Code: </label>
                              <div id="get_amount_code"> 
                            <select class="form-control valid" id="country_wise_code" name="country_wise_code">
                                 <?php $getCountryCodeList_22 = $this->service_agreement_model->getCountryCode();
                                 foreach($getCountryCodeList_22 as $key => $getCode_22):
                                ?>
                                <option value="<?=$getCode_22->amount_code;?>" <?php if ($getServiceData->country_wise_code == $getCode_22->amount_code) echo 'selected'; ?>><?=$getCode_22->amount_code?></option>
                                <?php endforeach; ?>
                            </select>
                              </div>  
                        </div>
						  
                         
                    </div>
					 
					<div class="row mb-20">
                       
                         <div class="col-md-3">
                            <label>Deliverable Dates: </label>
                         <input class="form-control col-md-8" type="text" name="est_deliverable_dates" id="est_deliverable_dates"  value="<?=$getServiceData->est_deliverable_dates?>">
                        </div>
                        <div class="col-md-3">
                            <label>Deliverable text : </label>
                         <input class="form-control col-md-8" type="text" name="est_deliverable_text" id="est_deliverable_text" placeholder="Enter deliverable text" value="<?=$getServiceData->est_deliverable_text?>">
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
                         
                    </div>
					 
                    <div class="row mb-20">
                        <div class="col-md-12">
                            <label>Additional Information(Note): </label>
                          <textarea class="form-control col-md-8" type="text" name="est_remarks" id="est_remarks" placeholder="Enter remarks" rows="4"><?=$getServiceData->est_remarks?></textarea>
                        </div>
                    </div>    
                    
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
   var editor_deliverables = CKEDITOR.replace('deliverables');   
    var editor_provided_by_client_info = CKEDITOR.replace('provided_by_client_info'); 
    //var editor_est_remarks = CKEDITOR.replace('est_remarks'); 

    // Revalidate the textarea on change
    editor.on('change', function() {
        $('#scope_of_the_work').valid();
    });
     // Revalidate the textarea on change   
    editor_deliverables.on('change', function() {
        $('#deliverables').valid();
    });
    
     editor_provided_by_client_info.on('change', function() {
        $('#provided_by_client_info').valid();
    });
   /* editor_est_remarks.on('change', function() {
        $('#est_remarks').valid();
    }); */
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
               // scope_of_the_work: { required: true },                
                //deliverables : { required: true },                
                //provided_by_client_info : { required : true },
                
                //scope_of_the_work: { required: true }, 
                'scope_of_the_work': {
                    required: function(textarea) {
                      // update textarea
                      CKEDITOR.instances[textarea.id].updateElement();
                      // strip tags
                      var editorcontent = textarea.value.replace(/<[^>]*>/gi, '');
                      return editorcontent.length === 0;
                    }
                  },
               // deliverables : { required: true }, 
                 'deliverables': {
                    required: function(textarea) {
                      // update textarea
                      CKEDITOR.instances[textarea.id].updateElement();
                      // strip tags
                      var editorcontent = textarea.value.replace(/<[^>]*>/gi, '');
                      return editorcontent.length === 0;
                    }
                  },
                
                // deliverables : { required: true }, 
                 'provided_by_client_info': {
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
                total_est_hours : { required : true},
                //est_cost : { required : true },
                country_wise_rate : { required : true },
                country_wise_code : { required : true },
               // est_deliverable_dates : { required : true },
               /* est_remarks : { required : true },
                //agreement_status : { required : true }
                 'est_remarks': {
                    required: function(textarea) {
                      // update textarea
                      CKEDITOR.instances[textarea.id].updateElement();
                      // strip tags
                      var editorcontent = textarea.value.replace(/<[^>]*>/gi, '');
                      return editorcontent.length === 0;
                    }
                  }, */
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
                deliverables : "enter deliverable information",
                provided_by_client_info : " enter client provided information",
                project_contact_name : "enter project contact name",
                project_email_id     : "enter email id",
                project_contact_number : "enter contact number",
                billing_contact_name  : "enter billing contact name",
                billing_email_id : "enter billing email id",
                billing_contact_number : "enter billing contact number",
                billing_contact_designation : "enter designation",
                total_est_hours : "enter total estimation hours",
                //est_cost : "enter estimation cost",
                country_wise_rate : "choose rate of country",
                 country_wise_code : "choose code of country",
                //est_deliverable_dates : "enter deliverable dates",
               //est_remarks : "enter additional information",
                agreement_status : "Choose Status"
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
	
  $('#agreement_date,#est_deliverable_dates').datepicker({
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
	
	 
	
$('#agreement_company,#country_code,#country_wise_rate,#country_wise_code,#department,#agreement_status,#agreement_invoice_status').select2();	 // Autosuggest list on clients
    
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
/******************** Country name to show all related information about the country and company ******************************************/
    
 $("#country_wise_rate").change(function () { 
            if ($(this).val() == 'other_amt_value') {
                $("#country_wise_rate_other").removeAttr("disabled");
                $("#country_wise_rate_other").focus();
            } else {
                $("#country_wise_rate_other").attr("disabled", "disabled");
            }
        });        
    
   initSample(); // fckeditor sample js file function  
    
</script>
<!-- Organizatoin form validation -->
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->