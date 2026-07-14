<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<?php 

  $createdUser = $this->session->userdata['logged_in_timesheet']['username']; // Session Loged in username
 
  $createdUserId = $this->session->userdata['logged_in_timesheet']['empId']; // Session Loged in username

 foreach($viewdetails as $key => $getUpdateTicketData) { }  
?>

<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1>View Ticket System</h1>
    </div>
  </div>
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div>
          <h4 class="line-head">View Ticket</h4>
          <span style="float:right; position:relative; top:-45px;"><a data-toggle="tooltip" title="Back To Ticket System" href="<?php echo base_url('ticket');?>"><img src="<?php echo HTTP_IMAGES_PATH;?>new.png"></a> </span> </div>
        <div style="clear:both;"></div>
        <form class="form-horizontal">
        
          
		  <div class="form-group">
            <label class="control-label col-md-3">Fixed the Issue By : </label>
            <div class="col-md-4">
                <label class="control-label"><?php echo ucfirst($getUpdateTicketData->ticket_responsibility); ?></label>
            </div>
          </div>
            <div class="form-group">
            <label class="control-label col-md-3">Ticket Type : </label>
            <div class="col-md-4">
                <label class="control-label "> <?php echo $getUpdateTicketData->ticket_name; ?></label>
              </div>
          </div>
		  <div class="form-group">
            <label class="control-label col-md-3">Ticket Information : <span class="required-star">*</span> </label>
            <div class="col-md-8">
               <label class="control-label" style="text-align:left !important;"><?php echo nl2br($getUpdateTicketData->ticket_desc); ?></label>
            </div>
          </div>
            
         <div class="form-group">
            <label class="control-label col-md-3">Responsibility : <span class="required-star">*</span></label>
            <div class="col-md-4">
                <label class="control-label "><?php echo ucfirst($getUpdateTicketData->ticket_responsibility); ?></label>
            </div>
          </div> 

          <div class="form-group">
            <label class="control-label col-md-3">Uploaded Image : </label>
            <div class="col-md-4">
                <label class="control-label ">
				
<?php if(!empty($getUpdateTicketData->ticket_upload_image)): ?>
		<a href="" onclick="window.open('<?php echo base_url().'uploads/ticket_uploded_images/'.$getUpdateTicketData->ticket_upload_image;?>','targetWindow', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1090px, height=550px, top=25px left=120px'); return false;"><img src="<?php echo base_url().'uploads/ticket_uploded_images/'.$getUpdateTicketData->ticket_upload_image;?>" style="max-width: 100%; height:auto;"></a> 
	<?php elseif(!empty($getUpdateTicketData->ticket_closed_upload_image)): ?>
		<a href="" onclick="window.open('<?php echo base_url().'uploads/ticket_uploded_images/ticket_closed_img/'.$getUpdateTicketData->ticket_closed_upload_image;?>','targetWindow', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1090px, height=550px, top=25px left=120px'); return false;"><img src="<?php echo base_url().'uploads/ticket_uploded_images/ticket_closed_img/'.$getUpdateTicketData->ticket_closed_upload_image;?>" style="max-width: 100%; height:auto;"></a>
		<?php else:?>
		 No image uploaded
<?php endif; ?>
				</label>
            </div>
          </div> 

		  <div class="form-group">
            <label class="control-label col-md-3">Ticket Closed Date : </label>
            <div class="col-md-4">
                 <label class="control-label "><?php echo $getUpdateTicketData->ticket_closed_date; ?></label>
             </div>
          </div>
            
        <div class="form-group">
            <label class="control-label col-md-3">Ticket Closed Update : <span class="required-star">*</span> </label>
            <div class="col-md-8">
               <label class="control-label" style="text-align:left !important;"><?php echo 	nl2br($getUpdateTicketData->ticket_closed_info); ?></label>
            </div>
          </div>    
		   
          <div class="form-group">
            <label class="control-label col-md-3">Status : <span class="required-star">*</span></label>
            <div class="col-md-4">
                 <label class="control-label "><?php echo $getUpdateTicketData->ticket_status; ?></label>
            </div>
          </div>    
            
          <div class="card-footer">
            <div class="row">
              <div class="col-md-8 col-md-offset-3">
                  <button class="btn btn-primary icon-btn"><a  href="<?php echo base_url('ticket');?>"><i class="fa fa-fw fa-lg fa-check-circle"></i>Click to Main page</a></button>
                &nbsp;&nbsp;<a class="btn btn-default icon-btn" href="<?php echo base_url('ticket');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a> </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
 
</div>

<!-- Organizatoin form validation -->
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
