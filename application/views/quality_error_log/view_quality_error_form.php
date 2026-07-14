<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<?php 

   $getClientNames      		= $this->client_model->getClientName(); // List of Clients
  
   $taskClientId                =  $this->uri->segment('5');
  
   $getListOfProjects   		= $this->project_model->getProjectName($taskClientId); // List of Clients
  
   $getListOfEmployees   	= $this->timesheet_login->getEmployeeName(); // List of Clients
  
   $taskProjectId = $this->uri->segment('4');
  
   $getListOfTask		   	= $this->task_model->getTaskName($taskProjectId); // List of Clients

   $getUpdateId = $this->uri->segment('3');

    
    $hideDateSection = date('Y-m-d');

    $notenteredMemberlist = array("421"); // Members array list

	foreach($viewARData as $getAnalyzerData){ 
		
		
	
	}
  			
?>

<div class="content-wrapper">

	<div class="page-title">
		<div>
			<h1><i class="fa fa-clock-o"></i> :: Quality Error Log View Information :: </h1>
		</div>
		<div> <a class="btn btn-primary btn-flat" href="<?php echo base_url();?>quality_error_log" data-toggle="tooltip" title="Go To Back!"><i class="fa fa-chevron-circle-left"></i></a> </div>
	</div>
	<div class="card">
		<h3 class="card-title"></h3>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<div class="bs-component">
						<div class="tab-content" id="myTabContent">
							<!-- Employee Report adding block -->
							
								<div class="tab-pane fade active in" id="Add">
									<div id="dynamic_field">
										<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label class="control-label">Client</label>
													<select class="form-control" id="client_Id1" name="client_Id" onChange="getProjects(this.value ,'1');" disabled="disabled">
														<option value="">Please select clients</option>
														<?php foreach($getClientNames as $key => $clientName): 
				 
															$hideeLogicGeneral = str_replace("eLogic Solutions", "",$clientName->client_name);
															//echo '<pre>'; print_r($hideeLogicGeneral);

														  if('eLogic Solutions'.$hideeLogicGeneral != $clientName->client_name){
				 
														?>

														<option value="<?php echo $clientName->client_Id;?>" <?php echo $getAnalyzerData->qty_client_Id == $clientName->client_Id ? "selected" : ""; ?>><?php echo ucfirst($clientName->client_name);?></option>

														<?php } ?>

														<?php endforeach; ?>
													</select>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label class="control-label">Project</label>
													<select class="form-control" id="project_Id1" name="project_Id"  disabled="disabled">
														<option value="">Please select project</option>
														<?php foreach($getListOfProjects as $key => $projectName): ?>
														<option value="<?php echo $projectName->project_Id;?>" <?php echo $getAnalyzerData->qty_project_Id == $projectName->project_Id ? "selected" : ""; ?>><?php echo ucfirst($projectName->project_name);?></option>
														<?php endforeach; ?>
													</select>
												</div>
											</div>
											<div class="col-md-3">
											<div class="form-group">
													<label class="control-label">Date</label>
													<input class="form-control" type="text" id="analyzer_report_date" name="analyzer_report_date" placeholder="Select Date" disabled="disabled" value="<?php echo $getAnalyzerData->analyzer_report_date;?>">
												</div>
											</div>	
											
											<div class="col-md-3">
											<div class="form-group">
													<label class="control-label">Self Checker</label>
													
													 <select class="form-control" id="self_checker_name" name="self_checker_name" disabled="disabled">
														<option value="">Select self checker name</option>
														<?php foreach($getListOfEmployees as $key => $employeeName): ?>
														<option value="<?php echo $employeeName->empId;?>" <?php echo $getAnalyzerData->self_checker_name == $employeeName->empId ? "selected" : ""; ?>><?php echo ucfirst($employeeName->name);?></option>
														<?php endforeach; ?>
													 </select>
													
          										</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-3">											
												<!-- <div class="form-group">
													<label class="control-label">Task</label>
													<select class="form-control" id="task_Id1" name="task_Id">
														<option value="">Please select task</option>
														<option value="Self-Checker">Self-Checker</option>
														<option value="analyze">Analyze</option>
														<option value="reviewer">Reviewer </option>
														<option value="ensure">Ensure</option>
														<option value="auditor ">Auditor</option>
													</select>
												</div> -->
												<div class="form-group">
													<label class="control-label">Analyzer</label>
													
													 <select class="form-control" id="analyzer_name" name="analyzer_name" disabled="disabled">
														<option value="">Select analyzer name</option>
														<?php foreach($getListOfEmployees as $key => $employeeName): ?>
														<option value="<?php echo $employeeName->empId;?>" <?php echo $getAnalyzerData->analyzer_name == $employeeName->empId ? "selected" : ""; ?>><?php echo ucfirst($employeeName->name);?></option>
														<?php endforeach; ?>
													 </select>
													
          										</div>

											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label class="control-label">No of Errors</label>
													<select class="form-control" id="analyzer_num_of_errors" name="analyzer_num_of_errors" disabled="disabled">
														<option value="">Choose number of errors</option>
														<?php for($errCnt = 0; $errCnt <= 100; $errCnt++): ?>
															<option value="<?php echo $errCnt; ?>" <?php echo $getAnalyzerData->analyzer_num_of_errors == $errCnt ? "selected" : ""; ?>><?php echo $errCnt; ?></option>
														<?php endfor; ?>	
													</select>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label class="control-label">Add Link</label>
													<input class="form-control" type="text" id="analyzer_link" name="analyzer_link" placeholder="Please enter link" value="<?php echo $getAnalyzerData->analyzer_link;?>" disabled="disabled">
													
												</div>
											</div>	
											
											<div class="col-md-4">
												<div class="form-group">
													<label class=" control-label" for="textArea">Comments</label>
													<textarea class="form-control" id="analyzer_comments" name="analyzer_comments" rows="1" disabled="disabled"><?php echo $getAnalyzerData->analyzer_comments;?></textarea>
												</div>
											</div>
										</div>
										<!-- Reviewer fill the information -->
										<div class="row">
											<div class="col-md-3">											
												<!-- <div class="form-group">
													<label class="control-label">Task</label>
													<select class="form-control" id="task_Id1" name="task_Id">
														<option value="">Please select task</option>
														<option value="Self-Checker">Self-Checker</option>
														<option value="analyze">Analyze</option>
														<option value="reviewer">Reviewer </option>
														<option value="ensure">Ensure</option>
														<option value="auditor ">Auditor</option>
													</select>
												</div> -->
												<div class="form-group">
													<label class="control-label">Reviewer Name</label>
													
													 <select class="form-control" id="reviewer_name" name="reviewer_name" disabled="disabled">
														<option value="">Select Reviewer name</option>
														<?php foreach($getListOfEmployees as $key => $employeeName): ?>
														<option value="<?php echo $employeeName->empId;?>" <?php echo $getAnalyzerData->reviewer_name == $employeeName->empId ? "selected" : ""; ?>><?php echo ucfirst($employeeName->name);?></option>
														<?php endforeach; ?>
													 </select>
													
          										</div>

											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label class="control-label">No of Errors in Reviewer</label>
													<select class="form-control" id="reviewer_num_of_errors" name="reviewer_num_of_errors" disabled="disabled">
														<option value="">Choose number of errors</option>
														<?php for($errCnt = 0; $errCnt <= 100; $errCnt++): ?>
															<option value="<?php echo $errCnt; ?>" <?php echo $getAnalyzerData->reviewer_num_of_errors == $errCnt ? "selected" : ""; ?>><?php echo $errCnt; ?></option>
														<?php endfor; ?>	
													</select>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label class="control-label">Add Reviewer Link</label>
													<input class="form-control" type="text" id="reviewer_link" name="reviewer_link" placeholder="Please enter link" value="<?php echo $getAnalyzerData->reviewer_link;?>" disabled="disabled">
													
												</div>
											</div>	
											
											<div class="col-md-4">
												<div class="form-group">
													<label class=" control-label" for="textArea">Reviewer Comments</label>
													<textarea class="form-control" id="reviewer_comments" name="reviewer_comments" rows="1"  disabled="disabled"><?php echo $getAnalyzerData->reviewer_comments;?></textarea>
												</div>
											</div>
										</div>
										<!-- Reviewer fill the information -->
									</div>
									<div class="card-footer" id="hideAftersumitButton">
										<a href="<?php echo base_url();?>quality_error_log" data-toggle="Go To Report Log!" title="Cancel">
											<button class="btn btn-default icon-btn" type="button"><i class="fa fa-chevron-circle-left"></i>Back</button>
										</a>
									</div>
								</div>
							
							<!-- Employee Report adding block -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>

<script type="text/javascript">
	$("form[name='reviewer_add_quality_errorlog']").validate({

		rules: {
			reviewer_name: {
				required: true
			},
			reviewer_num_of_errors: {
				required: true
			},
			reviewer_link: {
				required: true
			},
			reviewer_comments: {
				required: true
			}		

		},
		messages: {
			reviewer_name: "Please Select Task Name",
			reviewer_num_of_errors: "Please choose number of errors",
			reviewer_comments: "Please Enter Comments",
			reviewer_link: "Please Enter Valid Url"
		},
		submitHandler: function(form) {
			//$("#hideAftersumitButton").attr("disabled", true);
			$("#hideAftersumitButton").html('<i style="color:#009688; font-size:22px;" class="fa fa-spinner" aria-hidden="true"><span> Please wait while we process your request...</span></i>');
			form.submit();
		}
	});

	jQuery('#analyzer_report_date').datepicker({
		dateFormat: 'yy-mm-dd',
		autoclose: true,
		todayHighlight: true,
		//minDate: "2024-01-05",
		maxDate: new Date()
	});

	/* DatePicker */

	/*Ajax Based dropdown option changes on Clients , Projects and Tasks*/

	function getProjects(client_Id, autoPID) { // Getting client wise projects based on client id
		//alert(autoID);
		jQuery.ajax({
			type: "POST",
			url: "<?php echo base_url('empreports/getListOfProjectsWithClient');?>",
			data: 'client_Id=' + client_Id,
			success: function(data) {
				$("#project_Id" + autoPID).html(data);
				$('#project_Id' + autoPID).removeAttr('disabled');

			}
		});
	}

	function getProjectWiseTask(project_Id, autoTID) { // Getting projects wise task based on project id
		
		  $('#task_Id' + autoTID).removeAttr('disabled');
	}

	/* Ajax Based dropdown option changes on Clients , Projects and Tasks*/

	jQuery('#client_Id1,#project_Id1,#task_Id1,#self_checker_name,#analyzer_name,#analyzer_num_of_errors,#reviewer_name,#reviewer_num_of_errors').select2(); // Autosuggest list
</script>
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->