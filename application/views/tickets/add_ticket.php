<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>

<?php 
    
    $createdUser = $this->session->userdata['logged_in_timesheet']['username']; // Session Loged in username
 
    $createdUserId = $this->session->userdata['logged_in_timesheet']['empId']; // Session Loged in username

    $sessionEmail = $this->session->userdata['logged_in_timesheet']['email'];

    if(!empty($sessionEmail)){
        
        $logedSessionEmail = $this->session->userdata['logged_in_timesheet']['email'];
        
    }else{
        
        $logedSessionEmail = 'laxmikanth@elogictech.com';
        
    }

?>
<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1>Ticket System</h1>
    </div>
  </div> 
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div>
          <h4 class="line-head">Add Your Ticket</h4>
          <span style="float:right; position:relative; top:-45px;"><a data-toggle="tooltip" title="Back To Tickets" href="<?php echo base_url('ticket');?>"><img src="<?php echo HTTP_IMAGES_PATH;?>new.png"></a> </span> </div>
        <div style="clear:both;"></div>
        <form class="form-horizontal" method="post" name="add_ticket" id="add_ticket" enctype="multipart/form-data" action="<?php echo base_url('ticket/addticket');?>">
          <input type="hidden" name="emp_id" id="emp_id" class="form-control" value="<?php echo $createdUserId;?>">
          <input type="hidden" name="emp_emailId" id="emp_emailId" class="form-control" value="<?php echo $logedSessionEmail;?>">    
		  <div class="form-group">
            <label class="control-label col-md-3"> Created By : <span class="required-star">*</span></label>
            <div class="col-md-4">
             <input class="form-control" type="text" name="ticket_username" id="ticket_username" value="<?php echo ucfirst($createdUser); ?>" readonly>
            </div>
          </div>
		  <div class="form-group">
            <label class="control-label col-md-3">Ticket Type : <span class="required-star">*</span></label>
            <div class="col-md-4">
               <select class="form-control" id="ticket_name" name="ticket_name">
                <option value="">Please select ticket type</option>
                   <option disabled><span class="topicstype">Hardware Topics</span></option>  
                   <?php                    
                        
                        $getListofTopics = $this->ticket_model->getticketType();
                             
                        foreach($getListofTopics as $key =>$typeResult):
                        
                        if($typeResult->ticket_type_heading == 'Hardware'):  ?>       
                            
                                    <option value="<?php echo $typeResult->ticket_type_task_name;?>"><?php echo $typeResult->ticket_type_task_name;?></option>
                   
                        <?php endif; endforeach; ?>
                                      
                   
                   <?php  
                   
                   echo '<option disabled>Software Topics</option>';
                   
                    foreach($getListofTopics as $key =>$typeResult):
                   
                            if($typeResult->ticket_type_heading == 'Software'):  ?>       
                            
                                    <option value="<?php echo $typeResult->ticket_type_task_name;?>"><?php echo $typeResult->ticket_type_task_name;?></option>
                   
                        <?php endif; endforeach; ?>
				      
             </select>
                
              </div>
          </div>
		  <div class="form-group">
            <label class="control-label col-md-3">Ticket Information : <span class="required-star">*</span> </label>
            <div class="col-md-4">
              <textarea class="form-control" name="ticket_desc" id="ticket_desc" placeholder="Enter ticket issue details" rows="3"><?php echo set_value('ticket_desc'); ?></textarea>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-3">Upload Image : </label>
            <div class="col-md-4">
            <input class="form-control" id="ticket_upload_image" name="ticket_upload_image" type="file" aria-describedby="fileHelp" placeholder="Upload Image here" onchange="PreviewImage();"><label class="text-danger">Note : Upload image format( jpg | jpeg | png | GIF | JPG | PNG | JPEG)</label>
            </div>
            <div class="info" class="justify-content-center">
                  <img id="previewImg" class="user-img" width="120" height="120" src="<?php echo HTTP_IMAGES_PATH; ?>default.jpg ">
             </div>
          </div>
                               
        <div class="form-group">
            <label class="control-label col-md-3">Priority: <span class="required-star">*</span></label>
            <div class="col-md-4">
             <select class="form-control" id="ticket_priority" name="ticket_priority">
                <option value="">Please select priority</option>
                <option value="Low">Low</option>
				<option value="Medium">Medium</option>
				<option value="High">High</option>
             </select>
            </div>
          </div>    
          <div class="form-group">
            <label class="control-label col-md-3">Ticket Raised Date : <span class="required-star">*</span></label>
            <div class="col-md-4">
              <input class="form-control" type="text" name="ticket_raised_date" id="ticket_raised_date" readonly="" placeholder="Enter ticket raised start date" value="<?php echo date("Y-m-d"); ?>">
             </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-3">Status : <span class="required-star">*</span></label>
            <div class="col-md-4">
             <select class="form-control" id="ticket_status" name="ticket_status">
                <option value="Open">Open</option>
             </select>
            </div>
          </div>     
          <div class="card-footer" id="hideAftersumitButton">
            <div class="row">
              <div class="col-md-8 col-md-offset-3">
                <button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Create</button>&nbsp;&nbsp;<a class="btn btn-default icon-btn" href="<?php echo base_url('ticket');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a> </div>
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
  $("form[name='add_ticket']").validate({
    rules: {
      ticket_name        	     : { required : true },
	  ticket_desc        		 : { required : true },
	  ticket_priority        	 : { required : true },
	  ticket_raised_date         : { required : true },
      ticket_status              : { required : true },    
     
	},
    messages: {
     ticket_name				 : "Please Enter Ticket Subject",
	 ticket_desc				 : "Please Enter Ticket Issue Information",
	 ticket_priority			 : "Please Select Ticket Priority",
	 ticket_raised_date			 : "Please Enter Ticket Raised Date",
     ticket_status               : "Please Select Status",
		 },					
     submitHandler: function(form) {
         
         $("#hideAftersumitButton").html('<i style="color:#009688; font-size:22px;" class="fa fa-spinner" aria-hidden="true"><span> Please wait while we process your request...</span></i>');
         
      form.submit();
    }
  });
});

 $(document).ready(function () {
       var today = $("#project_start_date").val();
		$("#ticket_raised_date").datepicker({
            dateFormat: 'yy-mm-dd',
           todayHighlight: true,
		   maxDate: new Date()
        });
        

    })
$('#ticket_name,#ticket_priority,#ticket_status').select2();	 // Autosuggest list on clients

/* Ticket image upload priviewImage showing script here */
function PreviewImage() {
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("ticket_upload_image").files[0]);

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
