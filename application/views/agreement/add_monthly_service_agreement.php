<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader');
    $getCountryCodeList = $this->service_agreement_model->getCountryCode();
    $getProjectCodeLastRow = $this->service_agreement_model->getProjectCodeAutoGeneration();
    $getDepProjects  = $this->project_model->getProjects();

?>
<script type="text/javascript" src="<?php echo HTTP_JS_PATH; ?>fckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo HTTP_JS_PATH; ?>fckeditor/sample.js"></script>
<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h1>Monthly Service Agreement Information</h1>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div>
					<h4 class="line-head">Add New Monthly Service - <a href="add_service_agreement" class="btn btn-link" >Hourly Based</a> | <button class="btn btn-link" type="button" disabled="">Monthly Based </button></h4>
                    <span style="float:right; position:relative; top:-45px;"><a data-toggle="tooltip" title="Back To Service" href="<?php echo base_url('service_agreement');?>"><img src="<?php echo HTTP_IMAGES_PATH;?>new.png"></a>
                    </span>
                </div>
                <div style="clear:both;"></div>
                <form class="form-horizontal" method="post" name="add_monthly_service" id="add_monthly_service" action="<?php echo base_url('service_agreement/addMonthlyService');?>">
				  <input type="hidden" name="agreement_type" id="agreement_type" value="monthly">
                  <div class="row mb-20">						 
                        <div class="col-md-2">
                            <label>Country: <span class="required-star">*</span></label>
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
                            <label>Project Name: <span class="required-star">*</span></label>
                            <select class="form-control valid" id="project_name" name="project_name" onChange="getProjectDetails(this.value);">
                                <option value="">Please choose project</option>
                                <?php foreach($getDepProjects as $key => $getProject): ?>
                                <option value="<?=$getProject->project_Id.'@2222@'.$getProject->project_name;?>"><?=$getProject->project_name?></option>
                                <?php endforeach; ?>
                            </select>
                            <!-- <input class="form-control col-md-8" type="text" name="project_name" id="project_name" placeholder="Enter Project Name" value="<?php echo set_value('project_code'); ?>">-->
                        </div>
                                              
                        
                        <div class="col-md-2">
                            <label>Project Code: <span class="required-star">*</span></label>
                            <input class="form-control col-md-8" type="text" name="project_code" id="project_code" placeholder="Enter Project Code" value="" readonly>
                        </div>
                        <div class="col-md-2">
                            <label>Date: <span class="required-star">*</span></label>
                            <input class="form-control col-md-8" type="text" name="agreement_date" id="agreement_date" placeholder="Enter Agreement Date" value="<?php echo set_value('agreement_date'); ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Department : <span class="required-star">*</span></label>
                            <input class="form-control col-md-8" type="text" name="department" id="department" placeholder="Enter Department" value="" readonly> 
                        </div>
                    </div>

                    <div class="row mb-20">                        
                        <div class="col-md-3">
                            <label>Client Name: <span class="required-star">*</span></label>
                            <input class="form-control col-md-8" type="text" name="client_name" id="client_name" placeholder="Enter Client Name" value="<?php echo set_value('client_name'); ?>">
                        </div> 
						<div class="col-md-2">
                            <label>Lead Owner: </label>
                            <input class="form-control col-md-8" type="text" name="lead_owner" id="lead_owner" placeholder="Enter Lead Owner Name" value="<?php echo set_value('lead_owner'); ?>">
                        </div>
						<div class="col-md-2">
                            <label>Project Client Code: </label>
                            <input class="form-control col-md-8" type="text" name="project_client_code" id="project_client_code" placeholder="Enter Project Client Code" value="<?php echo set_value('project_client_code'); ?>">
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
                            <label>Client Address: <span class="required-star">*</span></label>
                            <textarea class="form-control col-md-8" type="text" name="client_adress" id="client_adress" placeholder="Enter Client Adress" value="<?php echo set_value('client_adress'); ?>" rows="4"></textarea>
                        </div>
                        
                    </div>                    
                    <div class="row mb-20">
                        
                         <div class="col-md-12">
                            <label>Scope of the Work: <span class="required-star">*</span></label>
                          <textarea class="form-control col-md-8"  name="scope_of_the_work" id="scope_of_the_work">Vendor shall make available the designated consultants to provide BIM Architectural services to "Client" on a monthly basis, for up to 180 hours per consultant per month. Hours in addition to the 180 hours per month will be billed at 25 USD per hour. The Services are limited to BIM Architectural Services and do not include MEP, Structural or 3D/animation rendering services. Designated consultants of the vendor (hereafter referred to as 'Consultants') shall work for the client remotely from vendor&apos;s offices in India. The consultants will be available to work on client&apos;s requirements all working days, every month. Vendor will ensure that the designated consultants are fully devoted to client for the entire month for 180 hours. Vendor will invoice client for services provided by vendor on a monthly basis at the end of each month as per the monthly rate provided in clause#2 of this agreement. Client shall pay for these services promptly and shall be responsible for all payment collections, if any, from its customers. If vendor's invoices are not paid promptly, Vendor may suspend work on client&apos;s projects, and may retain any completed work, until payment in full is received.</textarea>
                        </div>
                    </div>
                    
					
                   
                    <div class="row mb-20"><div class="col-md-12"> <h4 class="line-head">Project Contact Details</h4></div></div>
                     <div class="row mb-20">
                        <div class="col-md-3">
                            <label>Contact Name: <span class="required-star">*</span></label>
                             <input class="form-control col-md-8" type="text" name="project_contact_name" id="project_contact_name" placeholder="Enter project contact name" value="<?php echo set_value('project_contact_name'); ?>">
                        </div>
                         <div class="col-md-3">
                            <label>Email Id: <span class="required-star">*</span></label>
                         <input class="form-control col-md-8" type="text" name="project_email_id" id="project_email_id" placeholder="Enter Project contact email id" value="<?php echo set_value('project_email_id'); ?>">
                        </div>
                         <div class="col-md-3">
                            <label>Contact Number: <span class="required-star">*</span></label>
                         <input class="form-control col-md-8" type="text" name="project_contact_number" id="project_contact_number" placeholder="Enter Project contact number" value="<?php echo set_value('project_contact_number'); ?>">
                        </div>
                         <div class="col-md-3">
                            <label>Designation : </label>
                             <input class="form-control col-md-8" type="text" name="project_contact_designation" id="project_contact_designation" placeholder="Enter designation" value="<?php echo set_value('project_contact_designation'); ?>">
                        </div>
                    </div>
                    
                     <div class="row mb-20">
                         <div class="col-md-11"><h4 class="line-head">Billing Contact Details</h4></div>
                          <div class="col-md-1"><div class="toggle lg"><label><input type="checkbox" id="checkbox1"><span class="button-indecator"></span></label>
                </div></div>
                    </div>
                     <div class="row mb-20">
                        <div class="col-md-3">
                            <label>Contact Name: <span class="required-star">*</span></label>
                             <input class="form-control col-md-8" type="text" name="billing_contact_name" id="billing_contact_name" placeholder="Enter billing contact name" value="<?php echo set_value('billing_contact_name'); ?>">
                        </div>
                         <div class="col-md-3">
                            <label>Email Id: <span class="required-star">*</span></label>
                         <input class="form-control col-md-8" type="text" name="billing_email_id" id="billing_email_id" placeholder="Enter billing contact email id" value="<?php echo set_value('billing_email_id'); ?>">
                        </div>
                         <div class="col-md-3">
                            <label>Contact Number: <span class="required-star">*</span></label>
                         <input class="form-control col-md-8" type="text" name="billing_contact_number" id="billing_contact_number" placeholder="Enter billing contact number" value="<?php echo set_value('billing_contact_number'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Designation : <span class="required-star">*</span></label>
                             <input class="form-control col-md-8" type="text" name="billing_contact_designation" id="billing_contact_designation" placeholder="Enter designation" value="<?php echo set_value('billing_contact_designation'); ?>">
                        </div>
                    </div>
                    
                    
                     <div class="row mb-20"><div class="col-md-12"> <h4 class="line-head">LIST OF DESIGNATED CONSULTANTS:</h4></div></div>
					<div class="fieldGroup" id="dynamic_field">
                    <div class="row mb-20">                        
                         <div class="col-md-2">
                            <label>Name: <span class="required-star">*</span></label>
                          <input class="form-control col-md-8" type="text" name="designated_consultants_name[]" id="designated_consultants_name" placeholder="Enter designated consultants name" rows="4" value="<?php echo set_value('designated_consultants_name'); ?>">
                        </div>
						<div class="col-md-2">
                            <label>S.Date Services: <span class="required-star">*</span></label>
                          <input class="form-control col-md-8" type="text" name="designated_start_date_service[]" id="designated_start_date_service" placeholder="Enter start date for services" rows="4" value="<?php echo set_value('designated_start_date_service'); ?>">
                        </div>
						<div class="col-md-2">
                            <label>E.D Services: <span class="required-star">*</span></label>
                          <input class="form-control col-md-8" type="text" name="designated_end_date_service[]" id="designated_end_date_service" placeholder="Enter end date for services" rows="4" value="<?php echo set_value('designated_end_date_service'); ?>">
                        </div>						
						<div class="col-md-6">
                            <label>Desc of the offered service(s): <span class="required-star">*</span></label>
                          <input class="form-control col-md-8" type="text" name="designated_desc_offered_services[]" id="designated_desc_offered_services" placeholder="Description of the offered service(s)" rows="4" value="<?php echo set_value('designated_desc_offered_services'); ?>">
                        </div>							
				    </div>
					<div class="row mb-20">
						<div class="col-md-2">
						<label>Price <span class="required-star">*</span></label>
							<input class="form-control col-md-8" type="text" name="est_cost[]" id="est_cost" placeholder="Enter price" value="<?php echo set_value('est_cost'); ?>">
						</div>
						 <div class="col-md-2">
                            <label>Code: <span class="required-star">*</span></label>
                            <div id="get_amount_code">  
                            <select class="form-control valid" id="country_wise_code" name="country_wise_code[]">                               
                            </select>
                            </div>   
                        </div>
						<div class="col-md-2">
						 <div class="form-group">
						     <div class="col-sm-offset-2 col-sm-10 text-right"  style="top:25px;">
						         <button type="button" name="addmore_monthly_aggrement" id="addmore_monthly_aggrement" class="btn btn-success"><i class="fa fa-fw fa-lg fa-plus-circle"></i> Add More</button>
						     </div>
						 </div>
						 </div>
                        </div>
					</div>
					<!-- Add more fileds section block -->
					<!-- Add more filelds section block -->
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

    //Add more javascript 
    
    var kn = 1;
    
     jQuery('#addmore_monthly_aggrement').click(function() { 

            kn++;
         

            jQuery('#dynamic_field').append('<div id="row' + kn + '"><div class="row mb-20"> <div class="col-md-2"><label>Name: <span class="required-star">*</span></label><input class="form-control col-md-8" type="text" name="designated_consultants_name[]" id="designated_consultants_name'+kn+'" placeholder="Enter designated consultants name" rows="4" value=""></div><div class="col-md-2"><label>S.Date Services: <span class="required-star">*</span></label><input class="form-control col-md-8" type="text" name="designated_start_date_service[]" id="designated_start_date_service'+kn+'" placeholder="Enter start date for services" rows="4" value=""></div><div class="col-md-2"><label>E.D Services: <span class="required-star">*</span></label><input class="form-control col-md-8" type="text" name="designated_end_date_service[]" id="designated_end_date_service'+kn+'" placeholder="Enter end date for services" rows="4" value=""></div><div class="col-md-6"><label>Desc of the offered service(s): <span class="required-star">*</span></label><input class="form-control col-md-8" type="text" name="designated_desc_offered_services[]" id="designated_desc_offered_services'+kn+'" placeholder="Description of the offered service(s)" rows="4" value=""></div></div><div class="row mb-20"><div class="col-md-2"><label>Price <span class="required-star">*</span></label><input class="form-control col-md-8" type="text" name="est_cost[]" id="est_cost'+kn+'" placeholder="Enter price" value=""></div><div class="col-md-2"><label>Code: <span class="required-star">*</span></label><select class="form-control" id="country_wise_code'+kn+'" name="country_wise_code[]"><option value="USD">USD</option><option value="AUD">AUD</option><option value="TL">TL</option><option value="NZD">NZD</option><option value="CAD">CAD</option><option value="EUR">EUR</option><option value="INR" selected="">INR</option></select></div><div class="col-md-2" style="margin-top:2.2%;"><label>&nbsp;</label><button type="button" name="remove" id="' + kn + '" class="btn btn-danger btn_remove"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</button></div> ' + '</div></div>');
         
           jQuery('#designated_start_date_service' + kn).datepicker({
                dateFormat: "yy-mm-dd",
      	         autoclose: true,
      	         todayHighlight: true
            });
         
         jQuery('#designated_end_date_service' + kn).datepicker({
                dateFormat: "yy-mm-dd",
      	         autoclose: true,
      	         todayHighlight: true
            });
         
     
     });
    
    
    jQuery(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            var res = confirm('Are You Sure You Want To Delete This?');
            if (res == true) {
                $('#row' + button_id + '').remove();
                $('#' + button_id + '').remove();
            }
        });
    
    
    
    
   var editor = CKEDITOR.replace('scope_of_the_work');
      // Revalidate the textarea on change
    editor.on('change', function() {
        $('#scope_of_the_work').valid();
    });
        // Wait for the DOM to be ready
    $(function() {
        $("form[name='add_monthly_service']").validate({
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
               // provided_by_client_info : { required : true },
                project_contact_name : {  required : true },                
                project_email_id : { required : true },                
                project_contact_number : { required : true },
                project_contact_designation : { required : true },
                billing_contact_name : { required : true },                
                billing_email_id : { required : true },                
                billing_contact_number : { required : true},
                billing_contact_designation : { required : true },                
                'est_cost[]' : { required : true },
				'country_wise_code[]' : { required : true },
                'designated_consultants_name[]' : { required : true },
				'designated_start_date_service[]' : { required : true },
				'designated_end_date_service[]' : { required : true },
				'designated_desc_offered_services[]' : { required : true }
            },
            messages: {
				agreement_company: "choose agreement company",
                country_code: "enter country code",
                project_code : "enter project code",
                agreement_date: "enter agreement date",
                 department : "choose department",
                client_name  : "enter client name",
                sow_signature  : "Please choose Signature",
                client_adress : "enter client address",
                project_name : "enter project name",
                scope_of_the_work : "enter scope of the work",
                project_contact_name : "enter project contact name",
                project_contact_designation : "enter designation",
                project_email_id     : "enter email id",
                project_contact_number : "enter contact number",
                billing_contact_name  : "enter billing contact name",
                billing_email_id : "enter billing email id",
                billing_contact_number : "enter billing contact number",
                billing_contact_designation : 'enter designation',               
                'est_cost[]' : "enter monthly amount",
				'country_wise_code[]' : "choose code of country",
                'designated_consultants_name[]' : "enter consultant name",
				'designated_start_date_service[]' :  "choose start date service",
				'designated_end_date_service[]'  : "choose end date service",
				'designated_desc_offered_services[]' : "enter description of offered services"
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

/******************** Country name to show all related information about the country and company ******************************************/
	
	 function getCountryWiseDetails(countryCode){
		 
		
		 $.ajax({
				type: "POST",
				url: "<?php echo base_url('service_agreement/getBasedOnCourntyWiseMonthlyResult');?>",
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



    
	/* Add more jquery script */
	
	$(document).ready(function(){
    //group add limit
    var maxGroup = 4;
    
    //add more fields group
    $(".addMore").click(function(){ alert($('body').find('.fieldGroup').length);
        if($('body').find('.fieldGroup').length < maxGroup){
            var fieldHTML = '<div class="fieldGroup">'+$(".fieldGroupCopy").html()+'</div>';
            $('body').find('.fieldGroup:last').after(fieldHTML);
        }else{
            alert('Maximum '+maxGroup+' designated consultants are allowed.');
        }
    });
    
    //remove fields group
    $("body").on("click",".remove",function(){ 
        $(this).parents(".fieldGroup").remove();
    });
});
	
	/* End of the add more jquery script */
$('#agreement_company,#country_code,#country_wise_code,#project_name,#sow_signature').select2();	 // Autosuggest list on clients
	
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