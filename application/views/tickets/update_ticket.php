<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<?php 

  $createdUser = $this->session->userdata['logged_in_timesheet']['username']; // Session Loged in username
 
  $createdUserId = $this->session->userdata['logged_in_timesheet']['empId']; // Session Loged in username

 foreach($updateTicket as $key => $getUpdateTicketData) { }  

 if(!empty($getUpdateTicketData->emp_emailId)){
        
        $logedSessionEmail = $getUpdateTicketData->emp_emailId;
        
    }else{
        
        $logedSessionEmail = 'laxmikanth@elogictech.com';
        
    }

?>

<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1>Update Ticket System</h1>
    </div>
  </div>
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div>
          <h4 class="line-head">Update Ticket</h4>
          <span style="float:right; position:relative; top:-45px;"><a data-toggle="tooltip" title="Back To Projects" href="<?php echo base_url('ticket');?>"><img src="<?php echo HTTP_IMAGES_PATH;?>new.png"></a> </span> </div>
        <div style="clear:both;"></div>
        <form class="form-horizontal" method="post" name="update_ticket_info" id="update_ticket_info" enctype="multipart/form-data" action="<?php echo base_url('ticket/updateticket');?>">
            <input type="hidden" name="ticket_id" id="ticket_id" value="<?php echo $getUpdateTicketData->ticket_id;?>">     <input type="hidden" name="emp_emailId" id="emp_emailId" class="form-control" value="<?php echo $logedSessionEmail;?>">         
            <div class="form-group">
            <label class="control-label col-md-3">Ticket Created By : <span class="required-star">*</span></label>
            <div class="col-md-4">
             <span class="label label-info" style="font-size: 14px;"><?php echo $getUpdateTicketData->ticket_username;?></span>
            </div>
          </div>
            <div class="form-group">
            <label class="control-label col-md-3">Fixed the Issue By : <span class="required-star">*</span></label>
            <div class="col-md-4">
             <input class="form-control" type="text" name="ticket_username" id="ticket_username" value="<?php echo ucfirst($createdUser); ?>" readonly>
            </div>
          </div>
            <div class="form-group">
            <label class="control-label col-md-3">Ticket Type : <span class="required-star">*</span></label>
            <div class="col-md-4">
                <select class="form-control" id="ticket_name" name="ticket_name">
                <option value="">Please select ticket type</option>
                 <option  disabled>Hardware Topics</option>  
                   <?php                    
                        
                        $getListofTopics = $this->ticket_model->getticketType();
                             
                        foreach($getListofTopics as $key =>$typeResult):
                        
                        if($typeResult->ticket_type_heading == 'Hardware'):  ?>       
                            
                                    <option value="<?php echo $typeResult->ticket_type_task_name;?>" <?php if($typeResult->ticket_type_task_name == $getUpdateTicketData->ticket_name) { ?> selected="selected" <?php } ?>><?php echo $typeResult->ticket_type_task_name;?></option>
                   
                        <?php endif; endforeach; ?>
                                      
                   
                   <?php  
                   
                   echo '<option disabled>Software Topics</option>';
                   
                    foreach($getListofTopics as $key =>$typeResult):
                   
                            if($typeResult->ticket_type_heading == 'Software'):  ?>       
                            
                                    <option value="<?php echo $typeResult->ticket_type_task_name;?>" <?php if($typeResult->ticket_type_task_name == $getUpdateTicketData->ticket_name) { ?> selected="selected" <?php } ?>><?php echo $typeResult->ticket_type_task_name;?></option>
                   
                        <?php endif; endforeach; ?>
				      
             </select>
                
                
              </div>
          </div>
            
		  <div class="form-group">
            <label class="control-label col-md-3">Ticket Information : <span class="required-star">*</span> </label>
            <div class="col-md-4">
              <textarea class="form-control" name="ticket_desc" id="ticket_desc" placeholder="Enter ticket issue details" rows="3" readonly><?php echo $getUpdateTicketData->ticket_desc; ?></textarea>
            </div>
          </div>
            
           
         <div class="form-group">
            <label class="control-label col-md-3">Responsibility : <span class="required-star">*</span></label>
            <div class="col-md-4">
             <select class="form-control" id="ticket_responsibility" name="ticket_responsibility">
                <option value="">Please select status</option>
                <option value="suman" <?php if($getUpdateTicketData->ticket_responsibility == "suman") { ?> selected="selected" <?php } ?>>Suman kumar</option>
				<option value="nagesh" <?php if($getUpdateTicketData->ticket_responsibility == "nagesh") { ?> selected="selected" <?php } ?>>Nagesh Gajbhare</option>
                <!-- <option value="vaibhav" <?php if($getUpdateTicketData->ticket_responsibility == "vaibhav") { ?> selected="selected" <?php } ?>>Vaibhav</option> --> 
             </select>
            </div>
          </div>
          
          <div class="form-group">
            <label class="control-label col-md-3">Upload Image : </label>
            <div class="col-md-4">
				<input class="form-control" id="ticket_closed_upload_image" name="ticket_closed_upload_image" type="file" aria-describedby="fileHelp" placeholder="Upload Image here" onchange="PreviewImage();"><label class="text-danger">Note : Upload image format( jpg | jpeg | png | GIF | JPG | PNG | JPEG)</label>
            </div>
            <div class="info" class="justify-content-center">
                  <img id="previewImg" class="user-img" width="120" height="120" src="<?php echo HTTP_IMAGES_PATH; ?>default.jpg ">
             </div>
          </div>
			
        <?php if($createdUser != 'tsadmin'): ?>  
			<div class="form-group">
            <label class="control-label col-md-3">Ticket Closed Date : <span class="required-star">*</span></label>
            <div class="col-md-4">
              <input class="form-control" type="text" name="ticket_closed_date" id="ticket_closed_date" readonly="" placeholder="Enter Project End Date" value="<?php echo date("Y-m-d"); ?>">
             </div>
          </div>
			
           
        <div class="form-group">
            <label class="control-label col-md-3">Ticket Closed Update : <span class="required-star">*</span> </label>
            <div class="col-md-4">
              <textarea class="form-control" name="ticket_closed_info" id="ticket_closed_info" placeholder="Ticket Closed Information" rows="3"><?php echo $getUpdateTicketData->ticket_closed_info; ?></textarea>
            </div>
          </div>    
		 <?php endif; ?>
			
          <div class="form-group">
            <label class="control-label col-md-3">Status : <span class="required-star">*</span></label>
            <div class="col-md-4">
             <select class="form-control" id="ticket_status" name="ticket_status">
                 <option value="">Please Choose Ticket Status</option>
                <option value="Open" <?php if($getUpdateTicketData->ticket_status == "Open") { ?> selected="selected" <?php } ?>>Open</option>
                <option value="In Progress" <?php if($getUpdateTicketData->ticket_status == "In Progress") { ?> selected="selected" <?php } ?>>In Progress</option>
				<option value="Completed" <?php if($getUpdateTicketData->ticket_status == "Completed"){ ?> selected="selected" <?php } ?>>Completed</option>
			 </select>
            </div>
          </div>    
            
           <div class="card-footer" id="hideAftersumitButton">
            <div class="row">
              <div class="col-md-8 col-md-offset-3">
                <button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update</button>
                &nbsp;&nbsp;<a class="btn btn-default icon-btn" href="<?php echo base_url('ticket');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a> </div>
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
$(function() {
  $("form[name='update_ticket_info']").validate({
    rules: {
      ticket_responsibility      : { required : true },
    <?php if($createdUser !='suman' && $createdUser !='tsadmin'):	?>    
	  ticket_closed_date         : { required : true },
      ticket_closed_info         : { required : true },	    
      ticket_status              : { required : true },    
     <?php endif;?>
	},
    messages: {
     ticket_responsibility		  : "Please Choose Responsiblity Person Name",
     <?php if($createdUser !='suman' && $createdUser !='tsadmin'):	?>     
	 ticket_closed_date			  : "Please Select Ticket Closed Date",	
     ticket_closed_info			  : "Please ticket closed information",	    
     ticket_status                : "Please Select Status",
    <?php endif;?>    
		 },					
     submitHandler: function(form) {
         
         $("#hideAftersumitButton").html('<i style="color:#009688; font-size:22px;" class="fa fa-spinner" aria-hidden="true"><span> Please wait while we process your request...</span></i>'); 
         
      form.submit();
    }
  });
});

 $(document).ready(function () {
       var today = $("#ticket_closed_date").val();
		$("#ticket_closed_date").datepicker({
            dateFormat: 'yy-mm-dd',
            todayHighlight: true,
		    maxDate: new Date()
        });
        

    })
$('#ticket_name,#ticket_priority,#ticket_status,#ticket_responsibility').select2();	 // Autosuggest list on clients
/* Ticket image upload priviewImage showing script here */
function PreviewImage() {
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("ticket_closed_upload_image").files[0]);

        oFReader.onload = function (oFREvent) {
            document.getElementById("previewImg").src = oFREvent.target.result;
        };
    };
/* Ticket image upload priviewImage showing script here */
</script>
<!-- Organizatoin form validation -->
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
