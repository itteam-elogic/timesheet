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
			<h1><i class="fa fa-clock-o"></i> Update Resource Schedule Information </h1>
		</div>
		<div> <a class="btn btn-primary btn-flat" href="<?php echo base_url();?>resource_schedule" data-toggle="tooltip" title="Go To Report Log!"><i class="fa fa-chevron-circle-left"></i></a> </div>
	</div>
	<div class="card">
		<h3 class="card-title"></h3>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<div class="bs-component">
						<div class="tab-content" id="myTabContent">
							<!-- Employee Report adding block -->
							<form class="" name="update_resource" id="update_resource" method="post" action="<?php echo base_url('resource_schedule/update_resource_records');?>">
								<input type="hidden" id="resource_id" name="resource_id" value="<?php echo $getERData->resource_id; ?>" />
								<div class="tab-pane fade active in" id="Add">
									<div id="dynamic_field">
										<div class="row">
											<div class="col-md-3">
												<label class="control-label">Date</label>
												<input class="form-control" type="text" id="emp_report_dates" name="emp_report_dates" placeholder="Select Date" readonly="" value="<?php echo $getERData->emp_report_dates;?>" style="background:rebeccapurple; color:white; font-weight: bold">
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label class="control-label">Department</label>
													<select class="form-control" id="department" name="department">
														<option value="" selected="selected">Choose Department</option>
														<option value="Architectural" <?=$getERData->department == 'Architectural' ? 'selected="selected"' : '';?>>Architectural</option>
														<option value="Structural" <?=$getERData->department == 'Structural' ? 'selected="selected"' : '';?>>Structural</option>
														<option value="2D Auto CAD" <?=$getERData->department == '2D Auto CAD' ? 'selected="selected"' : '';?>>2D Auto CAD</option>
													   <option value="MEP-Mechanical" <?=$getERData->department == 'MEP-Mechanical' ? 'selected="selected"' : '';?>>MEP-Mechanical</option>
													   <option value="MEP-Electrical" <?=$getERData->department == 'MEP-Electrical' ? 'selected="selected"' : '';?>>MEP - Electrical</option>
													   <option value="MEP-Plumbing" <?=$getERData->department == 'MEP-Plumbing' ? 'selected="selected"' : '';?>>MEP-Plumbing</option>
														<option value="3D Visualization" <?=$getERData->department == '3D Visualization' ? 'selected="selected"' : '';?>>3D Visualization</option>
														<!-- <option value="Middle East" <?=$getERData->department == 'Middle East' ? 'selected="selected"' : '';?>>Middle East</option>-->
													</select>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-2">
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
											<div class="col-md-2">
												<div class="form-group">
													<label class="control-label">Projects</label>
													<select class="form-control" id="project_Id" name="project_Id" onchange="getProjectWiseTask(this.value)">
														<option value="">Please select project</option>
														<?php foreach($getListOfProjects as $key => $projectName): ?>
														<option value="<?php echo $projectName->project_Id;?>" <?php if($getERData->project_Id == $projectName->project_Id) echo 'selected="selected"'; ?>><?php echo ucfirst($projectName->project_name);?></option>
														<?php endforeach; ?>
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label class="control-label">Task</label>
													<select class="form-control" id="task_Id" name="task_Id" >
														<option value="">Please select task</option>
														<?php foreach($getReportTaskInfo as $key => $taskName): ?>
														<option value="<?php echo $taskName->task_Id;?>" <?php if(in_array($taskName->task_Id , $taskNameList)) echo 'selected="selected"'; ?>><?php echo ucfirst($taskName->task_name);?></option>
														<?php endforeach; ?>

													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label class="control-label">Team Member</label>
													<select class="form-control" id="team_member" name="team_member" multiple>
														<option value="">Please Choose Team Members</option>
														<?php foreach($this->project_model->teamMembers() as $Mteam): ?>
														<option value="<?php echo $Mteam->empId;?>" <?php if($getERData->team_member == $Mteam->empId) echo 'selected="selected"'; ?>><?php echo $Mteam->name;?></option>
														<?php endforeach; ?>
													</select>
												</div>
											</div>
											<div class="col-md-1">
												<div class="form-group">
													<label class="control-label">Hours</label>
													<select class="form-control" id="emp_time_hours" name="emp_time_hours" colspan="3">
														<option value="">Please select hours</option>
														<?php for ($i=0; $i<24.5;  $i += 0.5) { ?>
														<option value="<?php echo $i;?>" <?php if($getERData->emp_time_hours == $i) echo 'selected="selected"'; ?>><?php echo $i;?> </option>
														<?php	}?>

													</select>
												</div>
											</div>
											<div class="col-md-1">
												<div class="form-group">
													<label class="control-label">Workplace</label>
													<select class="form-control" id="workplace" name="workplace" colspan="3">
														<option value="WFO" <?=$getERData->workplace == 'WFO' ? 'selected="selected"' : '';?>>WFO</option>
														<option value="WFH" <?=$getERData->workplace == 'WFH' ? 'selected="selected"' : '';?>>WFH</option>
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label class=" control-label" for="textArea">Comments</label>
													<textarea class="form-control" id="comments" name="comments" rows="1" cols="20"><?=$getERData->comments;?></textarea>
												</div>
											</div>
										</div>

									</div>

									<div class="card-footer" id="hideAftersumitButton">
										<button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update</button>
										<a href="<?php echo base_url();?>resource_schedule" data-toggle="Go To Report Log!" title="Cancel">
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
		$("form[name='update_resource']").validate({
			rules: {
                
				'department' : {					
					required: true
				},
				
				'client_Id': {
                    required: true
                },
                'project_Id': {
                    required: true
                },
                'task_Id': {
                    required: true
                },
				'team_member':{
					required: true
				},		
                
                'emp_time_hours': {
                    required: true
                },
                'comments': {
                    required: function(element) {  //alert($('#project_Id' + kn).val() + 'kanth');
                        return $.inArray($('#project_Id' + kn).val(), ['156','258','381','390','432','1617','2058','2268','2549','4332','5083','5209','5473','5536','5701','5795']) !== -1;
                    }
                },
                
            },
            messages: {
				'department'		: "Please Select Department",
                'client_Id'		    : "Please Select Client Name",
                'project_Id'		: "Please Select Project Name",
                'task_Id'			: "Please Select Task Name",
				'team_member'		: "Please Select Employee Name",
                'emp_time_hours'	: "Please Select Time",
				'comments'		: "Please enter a comment for this project",
                
            },
			submitHandler: function(form) {
				//$("#hideAftersumitButton").attr("disabled", true);
				$("#hideAftersumitButton").html('<i style="color:#009688; font-size:22px;" class="fa fa-spinner" aria-hidden="true"><span> Please wait while we process your request...</span></i>');
				form.submit();
			}
		});
	});

	<?php if($hideDateSection >= '2024-09-05') : ?>

	$('#emp_report_dates').datepicker({
		dateFormat: 'yy-mm-dd',
		autoclose: true,
		todayHighlight: true,
		minDate: "2024-08-26",
		maxDate: "2024-10-05",
		//maxDate: new Date()
	});

	<?php else: ?>

	$('#emp_report_dates').datepicker({
		dateFormat: 'yy-mm-dd',
		autoclose: true,
		todayHighlight: true,
		minDate: "2024-08-26",
		maxDate: "2024-10-05",
		//maxDate: new Date()


	});

	<?php endif; ?>

	/* DatePicker */

	/*Ajax Based dropdown option changes on Clients , Projects and Tasks*/

	function getProjects(client_Id) { // Getting client wise projects based on client id
		$.ajax({
			type: "POST",
			url: "<?php echo base_url('empreports/getListOfProjectsWithClient');?>",
			data: 'client_Id=' + client_Id,
			success: function(data) {
				$("#project_Id").html(data);
				$('#project_Id').removeAttr('disabled');
			}
		});
	}

	function getProjectWiseTask(project_Id) { // Getting projects wise task based on project id
		$.ajax({
			type: "POST",
			url: "<?php echo base_url('empreports/getProjectsTask');?>",
			data: 'project_Id=' + project_Id,
			success: function(data) {
				$("#task_Id").html(data);
				$('#task_Id').removeAttr('disabled');
			}
		});
	}

	/* Ajax Based dropdown option changes on Clients , Projects and Tasks*/

	$('#client_Id,#project_Id,#task_Id,#team_member,#department').select2(); // Autosuggest list
</script>
<style>
	.select2-container--default .select2-selection--single .select2-selection__rendered { background: #663399;  color: #FFF; font-weight: bold; }
</style>
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->