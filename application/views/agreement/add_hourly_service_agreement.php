<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader');
    $getCountryCodeList = $this->service_agreement_model->getCountryCode();
    $getDepProjects  = $this->project_model->getProjects();
?>
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
					<h4 class="line-head">Add New Agreement  -  <button class="btn btn-link" type="button" disabled="">Hourly Based</button> | <a href="monthlyServiceAgreement" class="btn btn-link" >Monthly Based </a></h4>
                    <span style="float:right; position:relative; top:-45px;"><a data-toggle="tooltip" title="Back To Service" href="<?php echo base_url('service_agreement');?>"><img src="<?php echo HTTP_IMAGES_PATH;?>new.png"></a>
                    </span>
                </div>
                <div style="clear:both;"></div>
                <form class="form-horizontal" method="post" name="add_new_service" id="add_new_service" action="<?php echo base_url('service_agreement/addNewService');?>">
                    <div class="row mb-20">
                        <div class="col-md-2">
                            <label>Country: </label>
                            <select class="form-control valid" id="country_code" name="country_code" onChange="getCountryWiseDetails(this.value);">
                                <option value="">Please choose Country Code</option>
                                <?php foreach($getCountryCodeList as $key => $getCode): ?>
                                <option value="<?=$getCode->country_code_name;?>"><?=$getCode->country_name?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Agreement Company: <span class="required-star">*</span></label>
                            <div id="show_country_based_company">
                            <select class="form-control valid" id="agreement_company" name="agreement_company">
                                    
                            </select>
                            </div>    
                        </div>
                        <div class="col-md-2">
                            <label>Project Name: </label>
                            <select class="form-control valid" id="project_name" name="project_name" onChange="getProjectDetails(this.value);">
                                <option value="">Please choose project</option>
                                <?php foreach($getDepProjects as $key => $getProject): ?>
                                <option value="<?=$getProject->project_Id.'@2222@'.$getProject->project_name;?>"><?=$getProject->project_name?></option>
                                <?php endforeach; ?>
                            </select>
                            <!-- <input class="form-control col-md-8" type="text" name="project_name" id="project_name" placeholder="Enter Project Name" value="<?php echo set_value('project_code'); ?>">-->
                        </div>
                        
                        <div class="col-md-2">
                            <label>Project Code: </label>
                            <input class="form-control col-md-8" type="text" name="project_code" id="project_code" placeholder="Enter Project Code" value="" readonly>
                            
                        </div>
                        <div class="col-md-2">
                            <label>Date: </label>
                            <input class="form-control col-md-8" type="text" name="agreement_date" id="agreement_date" placeholder="Enter Agreement Date" value="<?php echo set_value('agreement_date'); ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Department : <span class="required-star">*</span></label>
                            <input class="form-control col-md-8" type="text" name="department" id="department" placeholder="Enter Department" value="" readonly>                            
                        </div>
                    </div>

                    <div class="row mb-20">                        
                        <div class="col-md-3">
                            <label>Client Name: </label>
                            <input class="form-control col-md-8" type="text" name="client_name" id="client_name" placeholder="Enter Client Name" value="" readonly>
                        </div>
						<div class="col-md-2">
                            <label>Lead Owner: </label>
                            <input class="form-control col-md-8" type="text" name="lead_owner" id="lead_owner" placeholder="Enter Lead Owner Name" value="<?php echo set_value('lead_owner'); ?>">
                        </div>
						<div class="col-md-2">
                            <label>Project Client Code: </label>
                            <input class="form-control col-md-8" type="text" name="project_client_code" id="project_client_code" placeholder="Enter Project Client Code" value="" readonly>
                        </div>
                        <div class="col-md-2">
                            <label>Signature: </label>
                            <select class="form-control valid" id="sow_signature" name="sow_signature">
                                  <option value="">Choose Signature</option> 
                                  <option value="rupali">Rupali Modi</option>
                                 <option value="farhan">Syed Farhan</option>
                                <option value="chauhan">Pradip Chauhan</option>
                                <option value="rahul">Rahul Kumar</option>
                                <option value="uppala">Naresh Uppala</option>
                            </select>
                            
                        </div>
                        <div class="col-md-3">
                            <label>Client Address: </label>
                            <textarea class="form-control col-md-8" type="text" name="client_adress" id="client_adress" placeholder="Enter Client Adress" value="<?php echo set_value('client_adress'); ?>" rows="4"></textarea>
                        </div>
                        
                    </div>                    
                    <div class="row mb-20">
                         <div class="col-md-12">
                            <label>Scope of the Work: </label>
                          <textarea class="form-control col-md-8"  name="scope_of_the_work" id="scope_of_the_work"></textarea>
                        </div>
                    </div>
                     <div class="row mb-20">
                        <div class="col-md-6">
                            <label>Deliverables: </label>
                             <textarea class="form-control col-md-8" type="text" name="deliverables" id="deliverables" placeholder="Enter deliverables information" rows="4" value="<?php echo set_value('deliverables'); ?>"></textarea>
                        </div>
                         <div class="col-md-6">
                            <label>Provided By Client: </label>
                          <textarea class="form-control col-md-8" type="text" name="provided_by_client_info" id="provided_by_client_info" placeholder="Enter client informaton" rows="4" value="<?php echo set_value('provided_by_client_info'); ?>"></textarea>
                        </div>
                    </div>
                   
                    <div class="row mb-20"><div class="col-md-12"> <h4 class="line-head">Project Contact Details</h4></div></div>
                     <div class="row mb-20">
                        <div class="col-md-3">
                            <label>Contact Name: </label>
                             <input class="form-control col-md-8" type="text" name="project_contact_name" id="project_contact_name" placeholder="Enter project contact name" value="">
                        </div>
                         <div class="col-md-3">
                            <label>Email Id: </label>
                         <input class="form-control col-md-8" type="text" name="project_email_id" id="project_email_id" placeholder="Enter Project contact email id" value="">
                        </div>
                         <div class="col-md-3">
                            <label>Contact Number: </label>
                         <input class="form-control col-md-8" type="text" name="project_contact_number" id="project_contact_number" placeholder="Enter Project contact number" value="">
                        </div>
                         <div class="col-md-3">
                            <label>Designation : </label>
                             <input class="form-control col-md-8" type="text" name="project_contact_designation" id="project_contact_designation" placeholder="Enter designation" value="<?php echo set_value('project_contact_designation'); ?>">
                        </div>
                    </div>
                    
                     <div class="row mb-20">
                         <div class="col-md-6"><h4 class="line-head">Billing Contact Details</h4></div>
						 <div class="col-md-6"><div class="toggle lg"><label><h4 class="line-head">Project Details is same click to checkbox:</h4><input type="checkbox" id="checkbox1"><span class="button-indecator"></span></label>
                </div></div>
                    </div>
                     <div class="row mb-20">
                        <div class="col-md-3">
                            <label>Contact Name: </label>
                             <input class="form-control col-md-8" type="text" name="billing_contact_name" id="billing_contact_name" placeholder="Enter billing contact name" value="<?php echo set_value('billing_contact_name'); ?>">
                        </div>
                         <div class="col-md-3">
                            <label>Email Id: </label>
                         <input class="form-control col-md-8" type="text" name="billing_email_id" id="billing_email_id" placeholder="Enter billing contact email id" value="<?php echo set_value('billing_email_id'); ?>">
                        </div>
                         <div class="col-md-3">
                            <label>Contact Number: </label>
                         <input class="form-control col-md-8" type="text" name="billing_contact_number" id="billing_contact_number" placeholder="Enter billing contact number" value="<?php echo set_value('billing_contact_number'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Designation : </label>
                             <input class="form-control col-md-8" type="text" name="billing_contact_designation" id="billing_contact_designation" placeholder="Enter designation" value="<?php echo set_value('billing_contact_designation'); ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-20"><div class="col-md-12"> <h4 class="line-head">Time and Cost Estimation</h4></div></div>
                     <div class="row mb-20">
                        <div class="col-md-3">
                            <label>T.Estimation Hours: </label>
                             <input class="form-control col-md-8" type="text" name="total_est_hours" id="total_est_hours" placeholder="Enter estimation hours" value="<?php echo set_value('total_est_hours'); ?>">
                        </div>
                        <!-- <div class="col-md-2">
                            <label>Cost </label>
                         <input class="form-control col-md-8" type="text" name="est_cost" id="est_cost" placeholder="Enter cost" value="<?php echo set_value('est_cost'); ?>">
                        </div> -->
                          <div class="col-md-3">
                            <label>Rate: </label>
                            <select class="form-control valid" id="country_wise_rate" name="country_wise_rate">
                                <option value="">Please choose Rate</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="30">30</option>
                                <option value="other_amt_value">Enter Other Amount</option>
                            </select>
                              
                        </div>
                          <div class="col-md-3">
                            <label>Other Amount: </label>
                             <input class="form-control col-md-8" type="text" name="country_wise_rate_other" id="country_wise_rate_other" placeholder="Enter hourly amount" disabled>
                          </div>
						 <div class="col-md-3">
                            <label>Discount Price: </label>
                            <input class="form-control col-md-8" type="text" name="sow_discount" id="sow_discount" placeholder="Enter discount amount">
                        </div>
                          
						 
                         
                         
                    </div>
					<div class="row mb-20">
					<div class="col-md-2">
						<label>Code: </label>
						<div id="get_amount_code">  
						<select class="form-control valid" id="country_wise_code" name="country_wise_code">                               
						</select>
						</div>    
					</div>
					<div class="col-md-2">
                            <label>Deliverable Dates: </label>
                         <input class="form-control col-md-8" type="text" name="est_deliverable_dates" id="est_deliverable_dates" placeholder="Enter deliverable dates" value="<?php echo set_value('est_deliverable_dates'); ?>">
                        </div>
                        
                    <div class="col-md-2">
                            <label>Deliverable text : </label>
                         <input class="form-control col-md-8" type="text" name="est_deliverable_text" id="est_deliverable_text" placeholder="Enter deliverable text" value="<?php echo set_value('est_deliverable_text'); ?>">
                        </div>    
					
					 <div class="col-md-3">
                            <label>Status: </label>
                            <select class="form-control valid" id="agreement_status" name="agreement_status">
                                <option value="">Please choose Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Sent Sow">Sent Sow</option>
                                <option value="Received">Received</option>
                                 <option value="Trial project">Trial project</option>
                                 <option value="No Sow">No Sow</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Invoice Status: </label>
                            <select class="form-control valid" id="agreement_invoice_status" name="agreement_invoice_status">
                                <option value="">Please choose Status</option>
								<option value="No invoice sent">No invoice sent</option>
                                <option value="Sent Invoive">Sent invoive</option>
                                <option value="Part Invoice Sent">Part invoice Sent</option>
                                <option value="Part Payment received">Part payment received</option>
                                <option value="Payment received">Payment received</option>
                            </select>
                        </div> 
					
					</div>
                    
                    <div class="row mb-20">                        
                         <div class="col-md-12">
                            <label>Additional Information(Note) : </label>
                          <textarea class="form-control col-md-8" type="text" name="est_remarks" id="est_remarks" placeholder="Enter remarks" rows="4" value="<?php echo set_value('est_remarks'); ?>"></textarea>
                        </div>
                    </div>
                    
                    <div class="card-footer" id="hideAfterOrgsumitButton">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-3">
                                <button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Create</button>
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
        $("form[name='add_new_service']").validate({
            ignore: [],
            rules: {
				agreement_company : { required: true },
                country_code : { required : true },                
                project_code : { required : true },                
                agreement_date : { required : true }, 
                department     : { required : true },
                client_name : { required : true }, 
                sow_signature : {required : true }, 
                client_adress : { required : true },                
                project_name : {  required : true  },
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
                
               // provided_by_client_info : { required : true },
                project_contact_name : {  required : true },                
                project_email_id : { required : true },                
                project_contact_number : { required : true },
				project_contact_designation :  { required : true },
                billing_contact_name : { required : true },                
                billing_email_id : { required : true },                
                billing_contact_number : { required : true},
                billing_contact_designation : { required : true },
                total_est_hours : { required : true},
                //est_cost : { required : true },
                country_wise_rate : { required : true },
                country_wise_rate_other : { required : true },
                country_wise_code : { required : true },
                //est_deliverable_dates : { required : true },
				agreement_status : { required : true },
				agreement_invoice_status : { required : true },
                /* est_remarks : { required : true },
                
                 'est_remarks': {
                    required: function(textarea) {
                      // update textarea
                      CKEDITOR.instances[textarea.id].updateElement();
                      // strip tags
                      var editorcontent = textarea.value.replace(/<[^>]*>/gi, '');
                      return editorcontent.length === 0;
                    }
                  },*/
            },
            messages: {
				agreement_company: "choose agreement company",
                country_code: "enter country code",
                project_code : "enter project code",
                agreement_date: "enter agreement date",
                 department : "choose department",
                client_name  : "enter client name",
                 sow_signature  : "enter choose signature",
                client_adress : "enter client address",
                project_name : "enter project name",
                scope_of_the_work : "enter scope of the work",
                deliverables : "enter deliverable information",
                provided_by_client_info : " enter client provided information",
                project_contact_name : "enter project contact name",
                project_email_id     : "enter email id",
                project_contact_number : "enter contact number",
				project_contact_designation : "enter designation",
                billing_contact_name  : "enter billing contact name",
                billing_email_id : "enter billing email id",
                billing_contact_number : "enter billing contact number",
                billing_contact_designation : 'enter designation',
                total_est_hours : "enter total estimation hours",
                //est_cost : "enter estimation cost",
                 country_wise_rate : "choose rate of country",
                 country_wise_rate_other : "enter houly amount",                                   
                 country_wise_code : "choose code of country",
                //est_deliverable_dates : "enter deliverable dates",
				agreement_status : "please choose the status",
				agreement_invoice_status : "please choose invoice status",
                //est_remarks : "enter additional information"
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
$('#agreement_company,#country_code,#country_wise_rate,#country_wise_code,#sow_signature,#project_name').select2();	 // Autosuggest list on clients

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


/******************** Country name to show all related information about the country and company ******************************************/
	

                                $('#project_name').change(function() {
                                    var projectId = $(this).val();
                                    getProjectDetails(projectId);
                                });

                                function getProjectDetails(projctId) { 
                                    
                                    var projectDetails = projctId.split('@2222@');
                                    var projectId = projectDetails[0]; // Extract project ID
                                    var projectName = projectDetails[1]; // Extract project name
                                    // You can use projectId and projectName as needed
                                   // console.log("Project ID: " + projectId);
                                    //console.log("Project Name: " + projectName);
                                    //alert(projectId + projectName); return false;
                                    $.ajax({
                                        type: "POST",
                                        url: "<?php echo base_url('service_agreement/getProjectDetailsBasedOnSow');?>",
                                        //data: 'projctId=' + projectId + '&projectName=' + projectName,
                                        data: 'projctId=' + projectId,
                                        beforeSend: function() { 
                                            $('#project_code').val('Loading...');
                                            $('#client_name').val('Loading...');
                                            $('#project_client_code').val('Loading...');
                                            $('#department').val('Loading...');
                                            $('#project_contact_name').val('Loading...');
                                            $('#project_email_id').val('Loading...');
                                            $('#project_contact_number').val('Loading...');
                                        },
                                        success: function(data) {
                                            var getOCData = data;
                                            var ajaxResult = getOCData.split('@#_22');
                                            if (ajaxResult[0] != '') {
                                                $('#project_code').val(ajaxResult[0]); // Set project code value
                                                $('#project_client_code').val(ajaxResult[1]); // Set project code value
                                                $('#client_name').val(ajaxResult[2]); // Set project code value
                                                $('#department').val(ajaxResult[3]); // Set project code value
                                                $('#project_contact_name').val(ajaxResult[4]); // Set project code value
                                                $('#project_email_id').val(ajaxResult[5]); // Set project code value
                                                $('#project_contact_number').val(ajaxResult[6]); // Set project code value
                                            } else {
                                                $('#project_code').val(''); // Clear if no data
                                                $('#project_client_code').val(''); // Clear if no data
                                                $('#client_name').val(''); // Clear if no data
                                                $('#department').val(''); // Clear if no data
                                                $('#project_contact_name').val(''); // Clear if no data
                                                $('#project_email_id').val(''); // Clear if no data
                                                $('#project_contact_number').val(''); // Clear if no data
                                            }
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
    
/***** Check box select same project and billing details */
    
    $("#checkbox1").on("change",function(){ 

        if (this.checked ) {
            
                $("#billing_contact_name").val($("#project_contact_name").val());
            
                $("#billing_email_id").val($("#project_email_id").val());
            
                $("#billing_contact_number").val($("#project_contact_number").val());
               
                $("#billing_contact_designation").val($("#project_contact_designation").val());

            } else {

                $('#billing_contact_name,#billing_email_id,#billing_contact_number,#billing_contact_designation').val(""); 
                
                $("#billing_contact_name").attr("Enter billing contact name", "project_contact_name");
                
                $("#billing_email_id").attr("Enter billing contact email id", "project_email_id");
                
                $("#billing_contact_number").attr("Enter billing contact name", "project_contact_number");
                
                $("#billing_contact_designation").attr("Enter designation", "project_contact_designation");
                
              }    

   });
    
    
     initSample(); // fckeditor sample js file function
    
    /**** Auto Suggest Organization *********/
	$(function() {
		$("#client_name").autocomplete({
			source: "getSowClientsList", // path to the get_birds method
		});
	});
	/**** Auto Suggest Organization *********/
    
</script>
<style>
	.ui-autocomplete {
		max-height: 250px;
		overflow-y: auto;
		/* prevent horizontal scrollbar */
		overflow-x: hidden;
	}

	/* IE 6 doesn't support max-height
	 * we use height instead, but this forces the menu to always be this tall
	 */

	* html .ui-autocomplete {
		height: 250px;
	}

</style>
<!-- Organizatoin form validation -->
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->