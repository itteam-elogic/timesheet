<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>

<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1><i class="fa fa-key"></i> Change Password </h1>
    </div>
    <div> <a class="btn btn-primary btn-flat" href="<?php echo base_url();?>empreports"  data-toggle="tooltip" title="Go To Report Log!"><i class="fa fa-chevron-circle-left"></i></a> </div>
  </div>
  <div class="card">
   
	<?php if($this->session->flashdata('msg')): ?>
   			<div class="alert alert-success fade in"><p><?php echo $this->session->flashdata('msg'); ?></p></div>
	<?php endif; ?>
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <div class="bs-component">
            <div class="tab-content" id="myTabContent">
              <!-- Employee Report adding block -->
			  
              <form class="form-horizontal" name="changepass" id="changepass"  method="post"  action="<?php echo base_url('employee/cPass/'.$this->uri->segment(3));?>">
                <div class="form-group">
                  <label class="control-label col-md-3">New Password : <span class="required-star">*</span></label>
                  <div class="col-md-4">
                    <input class="form-control" type="password" name="password" id="password" placeholder="Enter New Password" value="">
                   </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3">Confirm Password : <span class="required-star">*</span></label>
                  <div class="col-md-4">
                    <input class="form-control" type="password" name="cpassword" id="cpassword" placeholder="Enter Confirm Password" value="">
                   </div>
                </div>
                <div class="card-footer">
                  <div class="row">
                    <div class="col-md-8 col-md-offset-3">
                      <button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Change Password</button>
                         <a class="btn btn-default icon-btn" href="<?php echo base_url('employee');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a> </div>
                  </div>
                </div>
              </form>
              <!-- Employee Report adding block -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script language="javascript" type="text/javascript">
/* DatePicker */
$(function() {
  $("form[name='changepass']").validate({
    rules: {
      password        			 : { required : true },
	  cpassword        		 	 : { required : true ,equalTo: "#password" }
	  
     
	},
    messages: {
     password				     : "Please Enter Password",
	 cpassword				     : "Enter Password And Confirm Password Must Same",
	 },					
     submitHandler: function(form) {
      form.submit();
    }
  });
});



</script>
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
