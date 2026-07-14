<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
      <div class="content-wrapper">
        <div class="row user">
          <div class="col-md-12">
            <div class="profile">
              <?php 
				$userDetails =  $this->timesheet_login->user_information($this->session->userdata['logged_in_timesheet']['username']);
		        foreach($userDetails as $key) { $fullname = ucwords($key->name); $designation = ucwords($key->designation);  }
				$name =  explode(' ' , $key->name); // expload the employee name
				//echo '<pre>'; print_r($name);
				?>
			  <div class="info">
				 <?php if(!empty($key->avatar)): ?>
					<img class="user-img" src="<?php echo base_url().'uploads/employee_pic/'.$key->avatar; ?>">
				 <?php else: ?>
					<img class="user-img" src="<?php echo HTTP_IMAGES_PATH; ?>default.jpg">
				 <?php endif; ?>
                <h4><?php echo $fullname; ?></h4>
                <p><?php echo $designation; ?></p>
              </div>
              <div class="cover-image"></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card p-0">
              <ul class="nav nav-tabs nav-stacked user-tabs">
                <li class="active"><a href="#user-timeline" data-toggle="tab">Update Profile</a></li>
                <li><a href="#user-settings" data-toggle="tab">Change Password</a></li>
              </ul>
            </div>
          </div>
          <div class="col-md-9">
            <div class="tab-content">
			  <div class="tab-pane active card user-settings" id="user-timeline">
			  <h4 class="line-head">Update Profile</h4>
			  <?php if($this->session->flashdata('msg')): ?>
   					   <div class="alert alert-success fade in"><p><?php echo $this->session->flashdata('msg'); ?></p></div>
		       <?php endif; ?>
            
               <form class="form-horizontal" method="post" name="add_employee" id="add_employee" enctype="multipart/form-data" action="<?php echo base_url('employee/updateemployee');?>">
				<input type="hidden" name="update_empId" id="update_empId" value="<?php echo $key->empId; ?>" />
                    <div class="row mb-20">
                      <div class="col-md-4">
                        <label>First Name</label>
                         <input class="form-control" type="text" name="fname" id="fname" placeholder="Enter First Name" value="<?php echo $name[0]; ?>">
                      </div>
                      <div class="col-md-4">
                        <label>Last Name</label>
                        <input class="form-control" type="text" name="lname" id="lname" placeholder="Enter Last Name" value="<?php echo $name[1]; ?>">
                      </div>
                    </div>
                    <div class="row">
                      <div class="clearfix"></div>
                      <div class="col-md-8 mb-20">
                        <label>Mobile Number</label>
                        <input class="form-control" type="text" name="mobile_num" id="mobile_num" value="<?php echo $key->mobile_num; ?>">
                      </div>
                      <div class="clearfix"></div>
                       <div class="clearfix"></div>
                      <div class="col-md-8 mb-20">
                        <label>Profile Picture</label>
                        <input class="form-control" type="file" name="employee_image" id="employee_image" placeholder="Upload Photo" onchange="PreviewImage();">
						<br>
						<div class="info"><img id="previewImg" class="user-img" width="120" height="120" src="<?php echo base_url().'uploads/employee_pic/'.$key->avatar; ?>"></div>
                      </div>
                    </div>
                    <div class="row mb-10">
                      <div class="col-md-12">
                        <button class="btn btn-primary"><i class="fa fa-fw fa-lg fa-check-circle"></i> Update Profile</button>
                      </div>
                    </div>
                  </form>
              </div>
              <div class="tab-pane fade" id="user-settings">
                <div class="card user-settings">
                  <h4 class="line-head">Change Password</h4>
				  <?php if($this->session->flashdata('msg')): ?>
   					   <div class="alert alert-success fade in"><p><?php echo $this->session->flashdata('msg'); ?></p></div>
		          <?php endif; ?>
                  <form class="form-horizontal" name="changepass" id="changepass"  method="post"  action="<?php echo base_url('empreports/cPass');?>">
                <div class="form-group">
                  <label class="control-label col-md-3">New Password : <span class="required-star">*</span></label>
                  <div class="col-md-4">
                    <input class="form-control" type="password" name="password" id="password" placeholder="Enter new Password" value="">
                   </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3">Confirm Password : <span class="required-star">*</span></label>
                  <div class="col-md-4">
                    <input class="form-control" type="password" name="cpassword" id="cpassword" placeholder="Enter Confirm password" value="">
                   </div>
                </div>
                <div class="card-footer">
                  <div class="row">
                    <div class="col-md-8 col-md-offset-3">
                      <button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Change Password</button>
                         <a class="btn btn-default icon-btn" href="<?php echo base_url('empreports');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a> </div>
                  </div>
                </div>
              </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
<!-- Inlude Footer here -->
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

  $("form[name='add_employee']").validate({
    rules: {
      fname        		 : { required : true },
	  lname        		 : { required : true },
	  //mobile_num         : { required : true , digits: true , rangelength : [10, 10]},
    },
    messages: {
     fname				 : "Please Enter First Name",
	 lname				 : "Please Enter Last Name",
	 //mobile_num			 : { required : "Please Enter 10 Digits Mobile Number", digits : "Phone number must contain digits only" ,  rangelength : "Mobile Number must have 10 digits" } 
	},					
     submitHandler: function(form) {
      form.submit();
    }
  });
  
});

/* employee profile picture preview images dispalying here */
 function PreviewImage() {
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("employee_image").files[0]);

        oFReader.onload = function (oFREvent) {
            document.getElementById("previewImg").src = oFREvent.target.result;
        };
    };


</script>
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
