<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<?php 

   $getClientNames      		= $this->client_model->getClientName(); // List of Clients
  
   $taskClientId                =  $this->uri->segment('5');
  
   $getListOfProjects   		= $this->project_model->getProjectName($taskClientId); // List of Clients
  
   // $getListOfEmployees   	= $this->timesheet_login->getEmployeeName(); // List of Clients
  
   $taskProjectId               = $this->uri->segment('4');
  
   $getListOfTask		     	= $this->task_model->getTaskName($taskProjectId); // List of Clients

   $getUpdateId                 = $this->uri->segment('3');

	$hideDateSection = date('Y-m-d');
 		
?>

<div class="content-wrapper">
  <?php 
           $taskNameList = array(); 
          
		   foreach($updateEmpRecord as $key => $getERData) { 
		
		          	$taskNameList = explode("," , $getERData->task_Id);  // explode with comma in tasks data
		    
             } 
    
            		$getReportTaskInfo		   	= $this->task_model->getTaskReportId($getERData->task_Id); // List of Clients
    
            
   ?>
  <div class="page-title">
    <div>
      <h1><i class="fa fa-clock-o"></i> Update Report </h1>
    </div>
    <div> <a class="btn btn-primary btn-flat" href="<?php echo base_url();?>empreports"  data-toggle="tooltip" title="Go To Report Log!"><i class="fa fa-chevron-circle-left"></i></a> </div>
  </div>
  <div class="card">
    <h3 class="card-title"></h3>
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <div class="bs-component">
            <div class="tab-content" id="myTabContent">
              <!-- Employee Report adding block -->
              <form class="" name="add_emp_timelog" id="add_emp_timelog"  method="post"  action="<?php echo base_url('empreports/update_emp_records');?>">
                <input type="hidden" id="emp_record_id" name="emp_record_id" value="<?php echo $getERData->emp_record_id; ?>" />
                <div class="tab-pane fade active in" id="Add">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Clients</label>
                        <select class="form-control" id="client_Id" name="client_Id" onChange="getProjects(this.value);">
                          <option value="">Please select clients</option>
                          <?php foreach($getClientNames as $key => $clientName): ?>
                          <option value="<?php echo $clientName->client_Id;?>" <?php if($getERData->client_Id == $clientName->client_Id) echo 'selected="selected"'; ?>><?php echo ucfirst($clientName->client_name);?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
					 <div class="col-md-4">
						<div class="form-group">
                        <label class="control-label">Projects</label>
                        <select class="form-control" id="project_Id" name="project_Id"  onchange="getProjectWiseTask(this.value)">
                          <option value="">Please select project</option>
                          <?php foreach($getListOfProjects as $key => $projectName): ?>
                          <option value="<?php echo $projectName->project_Id;?>" <?php if($getERData->project_Id == $projectName->project_Id) echo 'selected="selected"'; ?>><?php echo ucfirst($projectName->project_name);?></option>
                          <?php endforeach; ?>
                        </select>
                      </div> 
					  </div>	 
					  
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Task</label>
                        <select class="form-control" id="task_Id" name="task_Id">
                          <optgroup label="Please select task">
                          <?php foreach($getReportTaskInfo as $key => $taskName): ?>
                          <option value="<?php echo $taskName->task_Id;?>" <?php if(in_array($taskName->task_Id , $taskNameList)) echo 'selected="selected"'; ?>><?php echo ucfirst($taskName->task_name);?></option>
                          <?php endforeach; ?>
                          </optgroup>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      
                      <div class="form-group">
                        <label class="control-label">Date</label>
                        <input class="form-control" type="text" id="emp_report_dates" name="emp_report_dates" placeholder="Select Date" readonly="" value="<?php echo $getERData->emp_report_dates; ?>">
                      </div>
                      <div class="form-group">
                        <label class="control-label">Hours</label>
                        <select class="form-control" id="emp_time_hours" name="emp_time_hours" colspan="3">
                         <option value="">Please select hours</option>
                          <?php for ($i=0; $i<24.5;  $i += 0.5) { ?>
                          <option value="<?php echo $i;?>" <?php if($getERData->emp_time_hours == $i) echo 'selected="selected"'; ?>><?php echo $i;?> </option>
                          <?php } ?>
						  
                        </select>
                      </div>
                    </div>
                    <div class="col-md-8">
                      <div class="form-group">
                        <label class=" control-label" for="textArea">Comments</label>
                        <textarea class="form-control" id="comments" name="comments" rows="5"><?php echo $getERData->comments; ?></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">Radios</label>
                        <div class="radio">
                          <label>
                          <input id="team_member_type1" type="radio" name="team_member_type" value="Regular" <?php echo ($getERData->team_member_type == 'Regular' ? 'checked' : '');?>>
                          Regular </label>
                        </div>
                        <div class="radio">
                          <label>
                          <input id="team_member_type2" type="radio" name="team_member_type" value="Substitute" <?php echo ($getERData->team_member_type == 'Substitute' ? 'checked' : '');?>>
                          Substitute </label>
                        </div>
                        <div class="radio">
                          <label>
                          <input id="team_member_type3" type="radio" name="team_member_type" value="Back Up" <?php echo ($getERData->team_member_type == 'Back Up' ? 'checked' : '');?>>
                          Back Up </label>
                        </div>
                      </div>
                    </div>
                  </div>
				 <div class="card-footer" id="hideAftersumitButton">
					<button  class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update</button>
					<a  href="<?php echo base_url();?>empreports"  data-toggle="Go To Report Log!" title="Cancel">
					<button class="btn btn-default icon-btn" type="button"><i class="fa fa-chevron-circle-left"></i>Back</button>
					</a> 
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
  $("form[name='add_emp_timelog']").validate({
    rules: {
      client_Id        			 : { required : true },
	  project_Id        		 : { required : true },
	  'task_Id'        		 : { required : true },
	  emp_report_dates        	 : { required : true },
	  emp_time_hours        	 : { required : true },
	  comments	        		 : { required : true },
	  team_member_type			 : { required : true }
     
	},
    messages: {
     client_Id				     : "Please Select Client Name",
	 project_Id				     : "Please Select Project Name",
	 'task_Id'				 : "Please Select Task Name",
	 emp_report_dates			 : "Please Select Date",
	 emp_time_hours				 : "Please Select Time",
	 comments					 : "Please Enter Comments",
	 team_member_type			 : "Please Select Team Member Type"
	 },					
     submitHandler: function(form) {
	  //$("#hideAftersumitButton").attr("disabled", true);
	  $("#hideAftersumitButton").html('<i style="color:#009688; font-size:22px;" class="fa fa-spinner" aria-hidden="true"><span> Please wait while we process your request...</span></i>');
      form.submit();
	}
  });
});



<?php if($hideDateSection >= '2026-06-05') : ?>
    
    $('#emp_report_dates').datepicker({
        dateFormat: 'yy-mm-dd',        
        autoclose: true,         
        todayHighlight: true,        
        minDate: "2026-06-01",
		 maxDate : "2026-07-05",
        //maxDate: new Date()
    });
    
 <?php else: ?>
    
    $('#emp_report_dates').datepicker({
        dateFormat: 'yy-mm-dd',        
        autoclose: true,         
        todayHighlight: true,        
        minDate: "2026-05-01",
		    maxDate : "2026-06-05",
         //maxDate: new Date()
         
        
    });
 
<?php endif; ?>   
	  
/* DatePicker */

/*Ajax Based dropdown option changes on Clients , Projects and Tasks*/

function getProjects(client_Id) {   // Getting client wise projects based on client id
	$.ajax({
	type: "POST",
	url: "<?php echo base_url('empreports/getListOfProjectsWithClient');?>",
	data:'client_Id='+client_Id,
	success: function(data){
		$("#project_Id").html(data);
		$('#project_Id').removeAttr('disabled');
	}
});
}

function getProjectWiseTask(project_Id) {   // Getting projects wise task based on project id
	$.ajax({
	type: "POST",
	url: "<?php echo base_url('empreports/getProjectsTask');?>",
	data:'project_Id='+project_Id,
	success: function(data){
		$("#task_Id").html(data);
		$('#task_Id').removeAttr('disabled');
	}
});
}

/* Ajax Based dropdown option changes on Clients , Projects and Tasks*/

$('#client_Id,#project_Id,#task_Id,#emp_time_hours').select2();	 // Autosuggest list


</script>
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
