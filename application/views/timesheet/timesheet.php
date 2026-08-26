<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<!-- Inlude Header here END-->
<?php 

  

  if(!empty($_REQUEST['client_Id'])):

		
			$getClientID = $_REQUEST['client_Id'];

		else:

			 $getClientID = 'all';

	endif;

	if(!empty($_REQUEST['project_Id'])):

		
			$getProjectID = $_REQUEST['project_Id'];

		else:

			 $getProjectID = 'all';

	endif;

	if(!empty($_REQUEST['task_Id'])):

		
			$getTaskID = $_REQUEST['task_Id'];

		else:

			 $getTaskID = 'all';

	endif;
		
	if(!empty($_REQUEST['empId'])): 
		
				$getempId      	 =	 implode(' ,' ,$_REQUEST['empId']);
				
		else:
				
				$getempId      	 =	 'all';
				
		endif;

if(!empty($_REQUEST['form_date'])):

		
			$getfromData = $_REQUEST['form_date'];

		else:

			 $getfromData = '';

	endif;

if(!empty($_REQUEST['to_date'])):

		
			$getToDate = $_REQUEST['to_date'];

		else:

			 $getToDate = '';

	endif;

if(!empty($_REQUEST['reporting_manager'])):
			$getReportingManager = $_REQUEST['reporting_manager'];
		else:
			 $getReportingManager = 'all';
	endif;

	if (!empty($_REQUEST['department']) && is_array($_REQUEST['department'])) {
		$getDepartmentIds = array_values(array_filter($_REQUEST['department']));
		if (in_array('all', $getDepartmentIds) || empty($getDepartmentIds)) {
			$getDepartmentIds = array();
		}
	} else {
		$getDepartmentIds = array();
	}

 $getClientNames      		= $this->client_model->getClientName(); // List of Clients
 $departmentList      		= array('Architectural', '3D Visualization', 'Structural', 'MEP', '2D Auto CAD'); // Fixed list for multi-select
  
  $getListOfProjects   		= $this->project_model->getProjectName($getClientID); // List of Clients
  
  $getListOfEmployees   	= $this->timesheet_login->getEmployeeName(true); // Active + Inactive employees
 $getListOfReportingManagers = $this->timesheet_login->getReportManagerName('', true); // Active + Inactive managers
 
  $getListOfTask		   	= $this->task_model->getTaskName($getProjectID); // List of Clients
 $reportingManagerMap = isset($reportingManagerMap) && is_array($reportingManagerMap) ? $reportingManagerMap : array();
 $reportingManagerDepartmentMap = isset($reportingManagerDepartmentMap) && is_array($reportingManagerDepartmentMap) ? $reportingManagerDepartmentMap : array();
 $projectManagerMap = isset($projectManagerMap) && is_array($projectManagerMap) ? $projectManagerMap : array();
 $taskNameByIdMap = isset($taskNameByIdMap) && is_array($taskNameByIdMap) ? $taskNameByIdMap : array();
	
?>

<style>
	/* Department multi-select: purple tags and dropdown styling (match reference image) */
	.department-select-wrap .select2-container--default .select2-selection--multiple .select2-selection__choice {
		background-color: #6f42c1;
		color: #fff;
		border: none;
	}
	.department-select-wrap .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
		color: #fff;
		margin-right: 2px;
	}
	.department-select-wrap .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
		color: #e9ecef;
	}
	.department-select2-dropdown .select2-results__option--selected {
		background-color: #6f42c1 !important;
		color: #fff !important;
	}
	.department-select2-dropdown .select2-results__option--highlighted[aria-selected="false"] {
		background-color: #e9ecef !important;
		color: #212529;
	}
</style>
<div class="content-wrapper">
	<div class="page-title">
		<div>
			<h1><i class="fa fa-clock-o"></i>Timesheet</h1>
		</div>
		<div>
			<a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url('timesheet');?>"><i class="fa fa-lg fa-refresh"></i></a>
		</div>
	</div>
	<div class="card">
		<h3 class="card-title"></h3>
		<div class="card-body">
			<form class="" name="timesheet_search" id="timesheet_search" method="post" action="<?php base_url('timesheet'); ?>">
				<div class="row">
					<div class="col-md-4 col-sm-6">
						<div class="form-group department-select-wrap">
							<label class="control-label">Department</label>
							<select class="form-control" id="department" name="department[]" multiple="multiple">
								<option value="all">All departments</option>
								<?php foreach($departmentList as $dept): ?>
								<option value="<?php echo htmlspecialchars($dept);?>" <?php if(in_array($dept, $getDepartmentIds)){ echo ' selected="selected"'; } ?>><?php echo $dept;?></option>
								<?php endforeach; ?>
							</select>
							<small class="text-muted">Select multiple departments. Leave empty or choose "All departments" for no filter.</small>
						</div>
					</div>
					<div class="col-md-4 col-sm-6">
						<div class="form-group">
							<label class="control-label">Client's</label>
							<select class="form-control" id="client_Id" name="client_Id" onChange="searchProjects(this.value);">
								<option value="all">All</option>
								<?php foreach($getClientNames as $key => $clientName): ?>
								<option value="<?php echo $clientName->client_Id;?>" <?php if($clientName->client_Id == $getClientID){ echo ' selected="selected"'; } ?>><?php echo ucfirst($clientName->client_name);?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-md-4 col-sm-6">
						<div class="form-group">
							<label class="control-label">Project's</label>
							<select class="form-control" id="project_Id" name="project_Id" onchange="searchProjectWiseTask(this.value)">
								<option value="all">All</option>
								<?php foreach($getListOfProjects as $key => $projectName): ?>
								<option value="<?php echo $projectName->project_Id;?>" <?php if($projectName->project_Id == $getProjectID){ echo ' selected="selected"'; } ?>><?php echo ucfirst($projectName->project_name);?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4 col-sm-6">
						<div class="form-group">
							<label class="control-label">Task's</label>
							<select class="form-control" id="task_Id" name="task_Id">
								<option value="all">All</option>
								<?php foreach($getListOfTask as $key => $taskName): ?>
								<option value="<?php echo $taskName->task_Id;?>" <?php if($taskName->task_Id == $getTaskID){ echo ' selected="selected"'; } ?>><?php echo ucfirst($taskName->task_name);?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-md-4 col-sm-6">
						<div class="form-group">
							<label class="control-label">Employee's</label>
							<select class="form-control" id="empId" name="empId[]">
								<option value="all">All</option>
								<?php foreach($getListOfEmployees as $key => $employeeName):
									$empStatusLabel = (!empty($employeeName->status) && strtolower($employeeName->status) !== 'active') ? ' (Inactive)' : '';
								?>
								<option value="<?php echo $employeeName->empId;?>" <?php if($employeeName->empId == $getempId){ echo ' selected="selected"'; } ?>><?php echo ucfirst($employeeName->name).$empStatusLabel;?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-md-4 col-sm-6">
						<div class="form-group">
							<label class="control-label">Reporting Manager</label>
							<select class="form-control" id="reporting_manager" name="reporting_manager">
								<option value="all">All</option>
								<?php foreach($getListOfReportingManagers as $manager):
									$managerStatusLabel = (!empty($manager->status) && strtolower($manager->status) !== 'active') ? ' (Inactive)' : '';
								?>
								<option value="<?php echo $manager->empId;?>" <?php if((string)$manager->empId === (string)$getReportingManager){ echo ' selected="selected"'; } ?>><?php echo ucfirst($manager->name).$managerStatusLabel;?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>
			<div class="row">
    <div class="col-md-6 col-sm-6">
        <div class="form-group">
            <label class="control-label">From</label>
            <input class="form-control" type="text" id="form_date" name="form_date"
                placeholder="Select From Date"
                value="<?php echo $getfromData; ?>" readonly="">
        </div>
    </div>

    <div class="col-md-6 col-sm-6">
        <div class="form-group">
            <label class="control-label">To</label>
            <input class="form-control" type="text" id="to_date" name="to_date"
                placeholder="Select To Date"
                value="<?php echo $getToDate; ?>" readonly="">
        </div>
    </div>
</div>
				<div class="card-footer">
					<button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Search</button>
					<a href="<?php echo base_url();?>empreports" data-toggle="Go To Report Log!" title="Cancel">
						<button class="btn btn-default icon-btn" type="button"><i class="fa fa-chevron-circle-left"></i>Back</button>
					</a>
				</div>
			</form>
		</div>

	</div>

	<?php if(count($getManageReportLog != 0)):?>
	<div class="card">
		<div class="card-body">
			<div class="row">
				<?php if(!empty($getManageReportLog)): ?>

				<div align="center">
					<a href="<?php echo base_url()?>timesheet/excel?client_Id=<?php echo $getClientID;?>&project_Id=<?php echo $getProjectID;?>&task_Id=<?php echo $getTaskID;?>&empId=<?php echo $getempId;?>&reporting_manager=<?php echo urlencode($getReportingManager);?>&form_date=<?php echo isset($_REQUEST['form_date']) ? $_REQUEST['form_date'] : '';?>&to_date=<?php echo isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : '';?>&department=<?php echo !empty($getDepartmentIds) ? htmlspecialchars(implode(',', $getDepartmentIds)) : '';?>">
						<button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Export To Excel Report</button>
					</a>
				</div>
				<?php endif; ?>
				<!-- Displaying Search Result -->
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-hover table-bordered" id="organisationTable">
							<thead>
								<tr>
									<th>Sno</th>
									<th>Name</th>
									<th>EmpId</th>
                                    <th nowrap="nowrap">Reporting Manager</th>
									<th>Client Name</th>
									<th>Project Name</th>
                                    <th nowrap="nowrap">Project Manager</th>
                                     <th>Department</th>
                                    <th>Task Name</th>
									<th>Hours</th>
									<th>Comments</th>
									<th>Status</th>                                    
									<th>Date</th>
									<th>E.Date</th>
								</tr>
							</thead>
							<tbody>
								<?php 
				  $i=1;
				  $totalHours = 0;
				  if( !empty($getManageReportLog) ) :
				  		foreach ($getManageReportLog as $key => $reportResult) :
				  			$reportingManagerId = isset($reportResult->reporting_manger) ? (string)$reportResult->reporting_manger : '';
				  			$reportManagerName = isset($reportingManagerMap[$reportingManagerId]) ? $reportingManagerMap[$reportingManagerId] : '';
				  			if (empty($reportManagerName) && !empty($reportResult->reporting_manager_name)) {
				  				$reportManagerName = $reportResult->reporting_manager_name;
				  			}
				  			if (empty($reportManagerName)) { $reportManagerName = 'N/A'; }
				  			$totalHours += $reportResult->emp_time_hours; // Total Hours
				  	 		if($i%2 == 0): $showRowColour = 'class="success"'; else: $showRowColour = 'class="info"'; endif;
				      		$taskId = isset($reportResult->task_Id) ? (string)$reportResult->task_Id : '';
				      		$taskIdParts = array_filter(array_map('trim', explode(',', $taskId)));
				      		$taskNames = array();
				      		foreach ($taskIdParts as $taskIdPart) {
				      			if (isset($taskNameByIdMap[$taskIdPart]) && $taskNameByIdMap[$taskIdPart] !== '') {
				      				$taskNames[] = $taskNameByIdMap[$taskIdPart];
				      			}
				      		}
				      		$getListOfProjects = !empty($taskNames) ? implode(' , ', $taskNames) : '';
	                                
	                                $managerEmpId = isset($reportResult->project_manager_name) ? (string)$reportResult->project_manager_name : '';
	                                $ProjectManagerName = isset($projectManagerMap[$managerEmpId]) ? $projectManagerMap[$managerEmpId] : '';
	                                $managerDepartment = '';
	                                if (!empty($reportResult->employee_department)) {
	                                	$managerDepartment = trim((string)$reportResult->employee_department);
	                                } elseif (!empty($reportResult->department) && !in_array($reportResult->department, array('Approved', 'Rejected', 'Pending', 'Process'), true)) {
	                                	$managerDepartment = trim((string)$reportResult->department);
	                                }
				  ?>
								<tr <?php echo $showRowColour; ?> id="delRecordsRow<?php echo $reportResult->emp_record_id; ?>">
									<td><?php echo $i ?></td>
									<td nowrap="nowrap"><span class="label label-info"><?php echo ucfirst($reportResult->name);?></span></td>
									<td nowrap="nowrap"><?php echo ucfirst($reportResult->emp_com_id);?></td>
                                    <td nowrap="nowrap"><?php echo htmlspecialchars($reportManagerName);?> </td>
									<td nowrap="nowrap"><?php echo ucfirst($reportResult->client_name);?> </td>
									<td nowrap="nowrap"><?php echo ucfirst($reportResult->project_name);?> </td>
                                    <td nowrap="nowrap"><?php echo $ProjectManagerName?> </td>
                                    <td nowrap="nowrap"><?php echo !empty($managerDepartment) ? $managerDepartment : 'N/A'; ?></td>
                                    <td nowrap="nowrap"><a href="#" data-toggle="tooltip" title="<?php echo $getListOfProjects;?>"><?php echo character_limiter($getListOfProjects,20);?></a></td>
									<td nowrap="nowrap"><?php echo ucfirst($reportResult->emp_time_hours);?> </td>
									<td nowrap="nowrap"><a href="#" data-toggle="tooltip" title="<?php echo $reportResult->comments;?>"><?php echo character_limiter($reportResult->comments, 20);?></a></td>
									<td nowrap="nowrap"> <span class="<?php echo ($reportResult->status=='Approved')? 'fa fa-check-circle label label-success' : (($reportResult->status=='Rejected')? 'fa fa-registered label label-warning' : 'fa fa-ban label label-danger');?>"> <?php echo $reportResult->status;?></span></td>
									<td nowrap="nowrap"><?php echo date('d-M-Y',strtotime($reportResult->emp_report_dates));?></td>
									<td nowrap="nowrap"><span class="me-1 badge bg-secondary"><?php echo date('d-M-Y',strtotime($reportResult->created_at));?></span></td>
								</tr>
								<?php $i++; endforeach; ?>
								<?php endif; ?>
								<?php if($getManageReportLog > 1): ?>
								<span style="position:absolute; margin-left:60%; margin-top:-2%;"><?php  echo 'Total Hours : <b style="color: #1322d2; font-size:20px;">'.$totalHours.'</b>'; ?></span>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
				<!-- Displaying Search Result -->
			</div>
		</div>
	</div>

	<?php endif; ?>


</div>
<script language="javascript" type="text/javascript">

	$(function() {
		$("form[name='timesheet_search']").validate({
			rules: {
				client_Id: {
					required: true
				},
				project_Id: {
					required: true
				},
				task_Id: {
					required: true
				},
				"empId[]": {
					required: true
				},
				form_date: {
					required: true
				},
				to_date: {
					required: true
				}
			},
			messages: {
				client_Id: "Please Select Client Name",
				project_Id: "Please Select Project Name",
				task_Id: "Please Select Task Name",
				"empId[]": "Please Select Employee Name",
				form_date: "Please Select From Date",
				to_date: "Please Select To Date"
			},
			submitHandler: function(form) {
				form.submit();
			}
		});
	});

	var searchAllVar;

	// Client wise projects
	function searchProjects(client_Id) {
		searchAllVar = client_Id;

		$.ajax({
			type: "POST",
			url: "<?php echo base_url('empreports/getClientProjects');?>",
			data: 'client_Id=' + client_Id,
			success: function(data) {
				$("#project_Id").html(data);
				$('#project_Id').removeAttr('disabled');
			}
		});
	}

	// Project wise tasks
	function searchProjectWiseTask(project_Id) {

		$.ajax({
			type: "POST",
			url: "<?php echo base_url('empreports/searchProjectsTask');?>",
			data: 'project_Id=' + project_Id + '&client_Id=' + searchAllVar,
			success: function(data) {
				$("#task_Id").html(data);
				$('#task_Id').removeAttr('disabled');
			}
		});
	}

	$(document).ready(function() {

		// Select2 Dropdowns
		$('#client_Id,#project_Id,#empId,#task_Id,#reporting_manager').select2();

		// Department Select2
		$('#department').select2({
			placeholder: 'Select departments...',
			allowClear: true,
			closeOnSelect: false,
			width: '100%',
			dropdownCssClass: 'department-select2-dropdown'
		});

		// Department "All" option logic
		$('#department').on('select2:select', function(e) {
			var data = e.params.data;

			if (data.id === 'all') {
				$(this).val(['all']).trigger('change');
			}
		});

		$('#department').on('select2:unselect', function(e) {
			var data = e.params.data;

			if (data.id === 'all') {
				$(this).val(null).trigger('change');
			}
		});

		// Datepicker
		var today = $("#form_date").val();

		$("#form_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			yearRange: "2015:2027",
			showButtonPanel: true,
			numberOfMonths: 1,

			beforeShow: function(input, inst) {
				setTimeout(function() {
					inst.dpDiv.css({
						zIndex: 9999,
						marginTop: '5px'
					});
				}, 0);
			},

			onSelect: function(selectedDate) {

				var dateMin = $('#form_date').datepicker("getDate");

				var rMin = new Date(
					dateMin.getFullYear(),
					dateMin.getMonth(),
					dateMin.getDate()
				);

				var rMax = new Date(
					dateMin.getFullYear(),
					dateMin.getMonth(),
					dateMin.getDate() + 365
				);

				$('#to_date').datepicker("option", "minDate", rMin);
				$('#to_date').datepicker("option", "maxDate", rMax);
			}
		});

		$("#to_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			yearRange: "2015:2027",
			showButtonPanel: true,
			numberOfMonths: 1,

			beforeShow: function(input, inst) {
				setTimeout(function() {
					inst.dpDiv.css({
						zIndex: 9999,
						marginTop: '5px'
					});
				}, 0);
			}
		});

		$('#to_date').datepicker("option", "minDate", new Date(today));

	});

	// Generate Excel
	function generateEmployeeExcelReport() {
		alert('Hello');
	}

</script>
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->