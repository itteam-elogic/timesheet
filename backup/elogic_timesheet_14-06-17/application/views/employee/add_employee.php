<?php $this->load->view('includes/header'); ?>
<!-- Middle Part Start -->
<div class="mainpanel">
  <div class="contentpanel">
    <div class="col-md-10">
      <ol class="breadcrumb breadcrumb-quirk">
        <li><a href="<?php echo base_url(); ?>Home"><i class="fa fa-home mr5"></i> Home</a></li>
        <li class="active"><i class="fa fa-users"></i> Add Employee</li>
      </ol>
    </div>
    <div class="col-md-2"> <a href="<?php echo base_url(); ?>employee" class="btn btn-success" title="Manage Employee" ><i class="fa fa-lg fa-user-plus" aria-hidden="true"> Manage Employee </i></a> </div>
    <div class="row">
      <div class="col-md-12">
        <div class="panel">
          <div class="panel-heading nopaddingbottom">
            <h4 class="panel-title">Add Employee</h4>
           </div>
          <div class="panel-body">
            <hr>
            <form id="basicForm" action="form-validation.html" class="form-horizontal">
              <div class="row">
                <div class="col-md-6">
                  <label>First Name</label>
				  
				  <div class="input-group mb15">
				
                <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
               <input type="text" name="fname" class="form-control" placeholder="Type your First name..." required />
              </div>
                </div>
				
               <div class="col-md-6">
                  <label>Last Name</label>
				   <div class="input-group mb15">
				    <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                 <input type="text" name="lname" class="form-control" placeholder="Type your Last Name..." required />
                </div>
				</div>
              </div>
			  
			  <br />
			  <div class="row">
                <div class="col-md-6">
                  <label>UserName</label>
				   <div class="input-group mb15">
				     <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                <input type="text" name="username" class="form-control" placeholder="Type your name..." required />
			</div>
                </div>
				
               <div class="col-md-6">
                  <label>Password</label>
				 <div class="input-group mb15">
				   <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
			
                <input type="text" name="password" class="form-control" placeholder="Type your Password..." required />
				
                </div>
				</div>
              </div>
			  	  <br />
				  <div class="row">
                <div class="col-md-6">
                  <label>Email</label>
				   <div class="input-group mb15">
				   <span class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></span>
            <input type="text" name="email" class="form-control" placeholder="Type your email..." required />
                </div>
				</div>
               <div class="col-md-6">
                  <label>Designation</label>
				   <div class="input-group mb15">
				   <span class="input-group-addon"><i class="fa fa-briefcase" aria-hidden="true"></i></span>
                <select id="select1" class="form-control" style="width: 100%" data-placeholder="Basic Select2 Box">
                  <option value="">&nbsp;</option>
                  <option value="Software Engineer">Software Engineer</option>
                  <option value="MEP Engineer">MEP Engineer</option>
                  <option value="3D Visualizer">3D Visualizer</option>
                  <option value="Project Manager">Project Manager</option>
                  <option value="Team Lead">Team Lead</option>
                </select>
            </div>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-sm-9 col-sm-offset-3">
                  <button class="btn btn-success btn-quirk btn-wide mr5">Submit</button>
                  <button type="reset" class="btn btn-quirk btn-wide btn-default">Reset</button>
                </div>
              </div>
            </form>
          </div>
          <!-- panel-body -->
        </div>
        <!-- panel -->
      </div>
      <!-- col-md-6 -->
      <!-- col-md-6 -->
    </div>
    <!--row -->
  </div>
  <!-- contentpanel -->
</div>
<script>

$(document).ready(function(){

// Basic Form
  $('#basicForm').validate({
    highlight: function(element) {
      $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
    },
    success: function(element) {
      $(element).closest('.form-group').removeClass('has-error');
    }
  });

});
</script>
<!-- Middle Part Start END -->
<?php $this->load->view('includes/footer'); ?>
