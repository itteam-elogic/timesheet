<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<?php 

  $createdUser = $this->session->userdata['logged_in_timesheet']['username']; // Session Loged in username
 
  $createdUserId = $this->session->userdata['logged_in_timesheet']['empId']; // Session Loged in username

 foreach($updateTicket as $key => $getUpdateTicketData) { }  
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
        <form class="form-horizontal" method="post" name="update_developer_ticket" id="update_developer_ticket" action="<?php echo base_url('ticket/updatedeveloperform');?>">
          <input type="hidden" name="ticket_id" id="ticket_id" value="<?php echo $getUpdateTicketData->ticket_id;?>">
            <div class="form-group">
            <label class="control-label col-md-3"> Created By : <span class="required-star">*</span></label>
            <div class="col-md-4">
             <input class="form-control" type="text" name="ticket_username" id="ticket_username" value="<?php echo $getUpdateTicketData->ticket_username; ?>" readonly>
            </div>
          </div>
		  <div class="form-group">
            <label class="control-label col-md-3">Ticket Name : <span class="required-star">*</span></label>
            <div class="col-md-4">
              <input class="form-control" type="text" name="ticket_name" id="ticket_name" placeholder="Enter ticket name" value="<?php echo $getUpdateTicketData->ticket_name; ?>">
              </div>
          </div>
		  <div class="form-group">
            <label class="control-label col-md-3">Issue Information : <span class="required-star">*</span> </label>
            <div class="col-md-4">
              <textarea class="form-control" name="ticket_desc" id="ticket_desc" placeholder="Enter ticket issue details" rows="3"><?php echo $getUpdateTicketData->ticket_desc; ?></textarea>
            </div>
          </div>
        <div class="form-group">
            <label class="control-label col-md-3">Priority: <span class="required-star">*</span></label>
            <div class="col-md-4">
             <select class="form-control" id="ticket_priority" name="ticket_priority">
                <option value="">Please select priority</option>
                <option value="Low" <?php if($getUpdateTicketData->ticket_priority =='Low') echo 'selected'; ?>>Low</option>
				<option value="Medium" <?php if($getUpdateTicketData->ticket_priority =='Medium') echo 'selected'; ?>>Medium</option>
				<option value="High" <?php if($getUpdateTicketData->ticket_priority =='High') echo 'selected'; ?>>High</option>
             </select>
            </div>
          </div>    
          <div class="form-group">
            <label class="control-label col-md-3">Ticket Raised Date : </label>
            <div class="col-md-4">
              <input class="form-control" type="text" name="ticket_raised_date" id="ticket_raised_date" readonly="" placeholder="Enter ticket raised start date" value="<?php echo $getUpdateTicketData->ticket_raised_date; ?>">
             </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-3">Status : <span class="required-star">*</span></label>
            <div class="col-md-4">
             <select class="form-control" id="ticket_status" name="ticket_status">
                 <option value="">Please Choose Ticket Status</option>
				<?php if($getUpdateTicketData->ticket_status == "Completed"):?>
                 <option value="Reopen" <?php if($getUpdateTicketData->ticket_status == "Reopen") { ?> selected="selected" <?php } ?>>Reopen</option>
				 <option value="Closed" <?php if($getUpdateTicketData->ticket_status == "Closed"){ ?> selected="selected" <?php } ?>>Closed</option>
                 <?php else: ?>
                 <option value="Open" <?php if($getUpdateTicketData->ticket_status == "Open") { ?> selected="selected" <?php } ?>>Open</option>
				 <?php endif; ?>
			 </select>
            </div>
          </div>      
          <div class="card-footer">
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
  $("form[name='update_developer_ticket']").validate({
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
      form.submit();
    }
  });
});

 $(document).ready(function () {
       var today = $("#project_start_date").val();
		$("#ticket_raised_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            numberOfMonths: 1,
        });
        

    })
$('#ticket_priority,#ticket_status').select2();	 // Autosuggest list on clients
</script>
<!-- Organizatoin form validation -->
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
