<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
$getClientNames      		= $this->client_model->getManagerwiseClients(); // List of Clients
	

	if(!empty($_REQUEST['form_date'])):
		$fromDate = $_REQUEST['form_date'];
		$toDate = $_REQUEST['to_date'];
		$departM = isset($_REQUEST['department']) ? $_REQUEST['department'] : '';
		$clientN = isset($_REQUEST['client_Id']) ? $_REQUEST['client_Id'] : '';
		$selectBGC = 'style="background:rebeccapurple; color:white; font-weight: bold"';
	else:
		$fromDate = date('Y-m-d');
		$toDate = date('Y-m-d');
		$departM = '';
		$clientN = '';
		$selectBGC = 'style="background:rebeccapurple; color:white; font-weight: bold"';
	endif; 

	$learning_development  = 0;
	$totalAvailableTeamMembers = 0;
	$totalTrainingTeamMembers = 0;
	$totalNewJoineeTrainingTM = 0;
	$totalLeaveTM = 0;
	$totalUnPLeaveTM = 0;
	$totalNATM = 0;
	$totalProductionTM = 0; 
	$totalWFHTM = 0; // Initialize total WFH Team Members
	$totalAvailableHours = 0; // Initialize total available hours
	$totalTrainingHours = 0; // Initialize total Training hours
	$totalNJTrainingHours = 0; // Initialize total  New Joinee hour
	$totalLeaveHours = 0; // Initialize total Leave hours
	$totalUNPLeaveHours = 0;  // Initialize total Unplanned Leave hours
	$totalProductionHours = 0;  // Initialize total Unplanned Leave hours
	$totalNAHours	=  0; // Initialize total entered (NA)
	$totalWFHHours = 0; // Initialize total WFH hours
	$wfhTeamMembers = array(); // Initialize WFH Team Members array
	foreach($getRecords as $key => $resourceResult_trace){
		
		if($resourceResult_trace->task_name == 'Available'){
			$totalAvailableTeamMembers++;
			$totalAvailableHours += $resourceResult_trace->emp_time_hours;  // Add hours to total available hours
		}elseif($resourceResult_trace->task_name == 'Training'){
			$totalTrainingTeamMembers++;
			$totalTrainingHours += $resourceResult_trace->emp_time_hours;  // Add hours to total Training hours
		}elseif($resourceResult_trace->task_name == 'Learning & Development'){
			$learning_development++;
			
		}elseif($resourceResult_trace->task_name == 'New Joinee Training'){
			$totalNewJoineeTrainingTM++;
			$totalNJTrainingHours += $resourceResult_trace->emp_time_hours;  // Add hours to total New Joinee hours		
		}elseif($resourceResult_trace->task_name == 'Leave'){
			$totalLeaveTM++;
				if($resourceResult_trace->emp_time_hours != '0'){
					$totalLeaveHours += $resourceResult_trace->emp_time_hours;  // Add hours to total Leave  hours
				}else{
					$totalLeaveHours += 8.5; // Add hours to total Leave  hours
				}			
		}elseif($resourceResult_trace->task_name == 'Unplanned Leave'){
			$totalUnPLeaveTM++;
				if($resourceResult_trace->emp_time_hours != '0'){
					$totalUNPLeaveHours += $resourceResult_trace->emp_time_hours;  // Add hours to total Unplanned Leave  hours
				}else{
					$totalUNPLeaveHours += 8.5; // Add hours to total Leave  hours
				}

		}elseif($resourceResult_trace->task_name == ''){
			$totalNATM++;
			$totalNAHours += 8.5; // Add hours to total Unplanned Leave  hours

		}elseif($resourceResult_trace->workplace == 'WFH'){

			if (!in_array($resourceResult_trace->team_member, $wfhTeamMembers)) {
				$wfhTeamMembers[] = $resourceResult_trace->team_member; // Add team member to WFH array
			} 
			$totalWFHTM = count($wfhTeamMembers); // Update total WFH Team Members count
			
			
		}else{
			   $totalProductionTM++;
			   $totalProductionHours += $resourceResult_trace->emp_time_hours; // Add hours to total Unplanned Leave  hours

		}


	}
	//echo 'Production Hours--'.$wfhTeamMembers; 
?>
<!-- Inlude Header here END-->

<div class="content-wrapper">
	<div class="page-title" style="padding:5px 30px;">
		<div>
			<h1>Resource Schedule Information</h1>
		</div>
		<div>
			<a class="btn btn-primary btn-flat" href="<?php echo base_url();?>resource_schedule/add" data-toggle="tooltip" title="Add"><i class="fa fa-lg fa-plus"></i> Add Resource</a> <?php if(!empty($_REQUEST['form_date'])): ?> | <a class="btn btn-primary btn-flat" id="downloadSearch_resouce_data" data-toggle="tooltip" title="Add"><i class="fa fa-lg fa-plus"></i> Download Resource schedule Report</a> <?php endif;?> | <a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url();?>resource_schedule"><i class="fa fa-lg fa-refresh"></i></a>
		</div>
	</div>

	<!-- <div class="card">
		
		<div class="text-center">
		<span><b style="color:#428bc5; cursor:pointer;" title="Production Hours : <?=$totalNATM;?>">Prod</b> : 
				<span class="badge rounded-pill btn-lg" style="background-color: #428bc5;" title="Production Hours: <?=$totalProductionHours;?>">H<br><?=$totalProductionHours;?></span> 
		</span>	&nbsp;&nbsp;
		<span><b style="color:#C2FFC7; color: #000; cursor:pointer;" title="Training: <?=$totalTrainingTeamMembers;?>">TRN</b> : 
			<span class="badge rounded-pill btn-lg" style="color: #000; background-color: #C2FFC7;" title="Training: <?=$totalTrainingTeamMembers;?>">C<br><?=$totalTrainingTeamMembers;?></span> 
			<span class="badge rounded-pill btn-lg" style="color: #000; background-color: #C2FFC7;" title="Training: <?=$totalTrainingHours;?>">H<br><?=$totalTrainingHours;?></span> 
		   </span>&nbsp;&nbsp;
			<span><b style="color:#ffa500; cursor:pointer;" title="New Joinee Training: <?=$totalNewJoineeTrainingTM;?>">INT</b> : 
			<span class="badge rounded-pill btn-lg" style="background-color: #ffa500;" title="New Joinee Training: <?=$totalNewJoineeTrainingTM;?>">C<br><?=$totalNewJoineeTrainingTM;?></span> 
			<span class="badge rounded-pill btn-lg" style="background-color: #ffa500;" title="New Joinee Training: <?=$totalNJTrainingHours;?>">H<br><?=$totalNJTrainingHours;?></span> 
		</span>&nbsp;&nbsp;
		<span><b style="color:#059212; cursor:pointer;" title="Available: <?=$totalAvailableTeamMembers;?>">AVL </b> : 
			<span class="badge rounded-pill btn-lg" style="background-color: #059212;" title="Available: <?=$totalAvailableTeamMembers;?>">C<br><?=$totalAvailableTeamMembers;?></span>  
			<span class="badge rounded-pill btn-lg" style="background-color: #059212;" title="Available: <?=$totalAvailableHours;?>">H<br><?=$totalAvailableHours;?></span> 
		</span>&nbsp;&nbsp;
			<span><b style="color:#7030a0; cursor:pointer;" title="Planned Leave: <?=$totalLeaveTM;?>">PL</b> : 
			<span class="badge rounded-pill btn-lg" style="background-color: #7030a0;" title="Leave: <?=$totalLeaveTM;?>">C<br><?=$totalLeaveTM;?></span> 
			<span class="badge rounded-pill btn-lg" style="background-color: #7030a0;" title="Leave: <?=$totalLeaveHours;?>">H<br><?=$totalLeaveHours;?></span>
		</span>&nbsp;&nbsp;
			<span><b style="color:#FFE31A; cursor:pointer; color: #000;" title="Unplanned Leave: <?=$totalUnPLeaveTM;?>">UPL</b> : 
			<span class="badge rounded-pill btn-lg" style="background-color: #FFE31A;color: #000;" title="Unplanned Leave: <?=$totalUnPLeaveTM;?>">C<br><?=$totalUnPLeaveTM;?></span> 
			<span class="badge rounded-pill btn-lg" style="background-color: #FFE31A; color: #000;" title="Unplanned Leave: <?=$totalUNPLeaveHours;?>">H<br><?=$totalUNPLeaveHours;?></span> 
		</span>&nbsp;&nbsp;
		<span><b style="color:#FF0000; cursor:pointer;" title="Not Entered: <?=$totalNATM;?>">NA</b> : 
			<span class="badge rounded-pill btn-lg" style="background-color: #FF0000;" title="Not Assessed: <?=$totalNATM;?>">C<br><?=$totalNATM;?></span> 
			<span class="badge rounded-pill btn-lg" style="background-color: #FF0000;" title="Not Assessed: <?=$totalNAHours;?>">H<br><?=$totalNAHours;?></span> 
		</span>&nbsp;&nbsp;
		
		</div>

	</div> -->

	<div class="container-fluid card">
        <div class="row" style="margin-top: 10px;">
            <div class="col-sm-12" style="margin-bottom: 10px; font-size: 13.5px;">
                <div class="row">
                    <div class="col-sm-12">
                        <span><b class="production" title="Production Count" : <?=$totalProductionTM;?>>Production Count : <?=$totalProductionTM;?> </b></span>
						<span><b class="training" title="Training Count" : <?=$totalTrainingTeamMembers;?>>Training Count : <?=$totalTrainingTeamMembers + $learning_development;?> </b></span>
						<span><b class="njtraining" title="Intern Training Count : <?=$totalNewJoineeTrainingTM;?>">Intern Training Count : <?=$totalNewJoineeTrainingTM;?> </b></span>
						<span><b class="available" title="Available Count" : <?=$totalAvailableTeamMembers;?>>Available Count : <?=$totalAvailableTeamMembers;?> </b></span>
						<span><b class="leave" title="Planned Leave Count : <?=$totalLeaveTM;?>">Planned Leave Count : <?=$totalLeaveTM;?> </b></span>
						<span><b class="upleave" title="Unplanned leave count : <?=$totalUnPLeaveTM;?>">Unplanned leave count : <?=$totalUnPLeaveTM;?> </b></span>
						<span><b class="wfh" title="WFH Count : <?=$totalNATM;?>">WFH Count : <?=$totalWFHTM;?> </b></span>
						<span><b class="na" title="Not Assigned count : <?=$totalNATM;?>">Not Assigned count : <?=$totalNATM;?> </b></span>
                    </div>
                  
                </div>
            </div>
        </div>
    </div>

	<div class="card">
		
		<div class="card-body">
			<div class="row">

				<!-- Search for employee with date wise and client , project wise as well. -->
				<div class="col-md-12">
					<div class="bs-component">
						<div class="tab-content" id="myTabContent">
							<!-- Employee Report adding block -->
							<form class="" name="resource_date_search" id="resource_date_search" method="get" action="<?php echo base_url('resource_schedule');?>">
								<div class="tab-pane">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Department</label>
												<select class="form-control" id="department" name="department">
													<option value=""></option>
													<option value="Architectural" <?=$departM == 'Architectural' ? 'selected="selected"' : '';?>>Architectural</option>
													<option value="Structural" <?=$departM == 'Structural' ? 'selected="selected"' : '';?>>Structural</option>
													<option value="MEP-Mechanical" <?=$departM == 'MEP-Mechanical' ? 'selected="selected"' : '';?>>MEP-Mechanical</option>
													<option value="MEP-Electrical" <?=$departM == 'MEP-Electrical' ? 'selected="selected"' : '';?>>MEP - Electrical</option>
													<option value="MEP-Plumbing" <?=$departM == 'MEP-Plumbing' ? 'selected="selected"' : '';?>>MEP-Plumbing</option>
													<option value="3D Visualization" <?=$departM == '3D Visualization' ? 'selected="selected"' : '';?>>3D Visualization</option>
													<!-- <option value="Middle East" <?=$departM == 'Middle East' ? 'selected="selected"' : '';?>>Middle East</option> -->
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Client</label>
												<select class="form-control" id="client_Id" name="client_Id">
													<option value=""></option>
													<?php foreach($getClientNames as $key => $clientName): ?>
													<option value="<?php echo $clientName->client_Id;?>" <?=$clientN == $clientName->client_Id ? 'selected="selected"' : '';?>><?php echo ucfirst($clientName->client_name);?></option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label class="control-label">From Date</label>
												<input class="form-control" type="text" id="form_date" name="form_date" placeholder="Select From Date" value="<?=$fromDate;?>" readonly="" <?=$selectBGC?>>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label class="control-label">To Date</label>
												<input class="form-control" type="text" id="to_date" name="to_date" placeholder="Select To Date" value="<?=$toDate;?>" readonly="" <?=$selectBGC?>>
											</div>
										</div>
										<div class="col-md-2">
											<button class="btn btn-primary icon-btn" style="margin-top:27px;"><i class="fa fa-fw fa-lg fa-check-circle"></i>Search</button>
										</div>

									</div>
								</div>
							</form>
							<!-- Employee Report adding block -->
						</div>
					</div>
				</div>
				<!--Search for employee with date wise and client , project wise as well.  -->
			</div>
		</div>
	</div>
		<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<!-- <center><h4>Total Hours : <span id="countData" style="color: #1322d2; font-size:20px;"></span></h4> </center> -->
						<table class="table table-hover table-bordered" id="table22excel_all">
							<thead>
								<tr>
									<!--<th>Sno</th>-->
									<th>Date</th>
									<th>Department</th>
									<th>Manager</th>
									<th>Team Member</th>
									<th>Client</th>
									<th>Project</th>
									<th>Task</th>
									<th>Hours</th>
									<th>Workplace</th>
									<th>Comments</th>
									<?php if(empty($_REQUEST['form_date'])): ?>
									<th>Action</th>
									<?php endif; ?>
								</tr>
							</thead>
							<tbody>
								<?php $previousManagerName = ''; $rowSpan = 1; $managerCount = array(); 
								foreach($getRecords as $key => $resourceResult): 
								$mangerName = $resourceResult->reporting_manger; 
								if(!isset($managerCount[$mangerName])): 
								 	$managerCount[$mangerName] = 1; 
								 else: 
								 	$managerCount[$mangerName]++; 
								 endif; 
								?>
								<?php endforeach; ?>
								<?php $previousManagerName = ''; $rowSpan = 1; 
								foreach($getRecords as $key => $resourceResult): 

								$mangerName = $resourceResult->reporting_manger; 

								$getTeamwiseMangerName = $this->resourcelog_model->getManagerName($mangerName);

								if($resourceResult->task_name == 'Leave'):
								
									$bgrowC = 'style="background:#7030a0; color:white; font-weight: bold"';
								
								elseif($resourceResult->task_name == 'Training' || $resourceResult->task_name == 'Learning & Development'):
								
									$bgrowC = 'style="background:#C2FFC7; color:#000; font-weight: bold"';
								
								elseif($resourceResult->task_name == 'Unplanned Leave'):
								
									$bgrowC = 'style="background:#FFE31A; color:#000; font-weight: bold"';
								
								elseif($resourceResult->task_name == 'Available'):
								
									$bgrowC = 'style="background:#059212; color:#FFF; font-weight: bold"';
								
								elseif($resourceResult->task_name == 'New Joinee Training'):
								
									$bgrowC = 'style="background:#ffa500; color:#FFF; font-weight: bold"';

								elseif($resourceResult->workplace == 'WFH'):
								
										$bgrowC = 'style="background:#008b8b; color:#FFF; font-weight: bold"';	
								
								elseif($resourceResult->task_name == ''):
								
									$bgrowC = 'style="background:#FF0000; color:#FFF; font-weight: bold"';
								
								
								else:
								
									$bgrowC = '';
								
								endif;		

								if($mangerName != $previousManagerName): 
								 	$rowSpan = $managerCount[$mangerName]; 
								 
									
								 ?>

								<?php if($mangerName != $previousManagerName): 
								if($key !=0):
								?>
								<tr>
									<td colspan="11" style="background:#686868; height:24px;"></td>
								</tr>
								<?php endif; endif; ?>

								<tr <?=$bgrowC;?>>
									<!--<td><?=$key+1;?></td>-->
									<td nowrap><?=$resourceResult->emp_report_dates;?></td>
									<td><?=$resourceResult->department;?></td>
									<td rowspan="<?=$rowSpan;?>" style="background:#D1E9F6; color:#000;"><b><?=ucfirst($getTeamwiseMangerName);?></b></td>
									<td><?=$resourceResult->name;?></td>
									<td><?=$resourceResult->client_name;?></td>
									<td><?=$resourceResult->project_name;?></td>
									<td><?=$resourceResult->task_name;?></td>
									<td><?=$resourceResult->emp_time_hours;?></td>
									<td><?=$resourceResult->workplace;?></td>
									<td><?=$resourceResult->comments;?></td>
									<?php if(empty($_REQUEST['form_date'])): ?>
									<td>
										<?php if (!empty($resourceResult->resource_id)): ?>
										<a href="<?php echo base_url(); ?>resource_schedule/add/<?php echo $resourceResult->resource_id; ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"> </i></a>
										<?php else: ?>
										<a href="<?php echo base_url(); ?>resource_schedule/add" data-toggle="tooltip" title="Add"><i class="fa fa-lg fa-plus"> </i></a>
										<?php endif; ?>
									</td>
									<?php endif; ?>
								</tr>

								<?php else: ?>
								<tr <?=$bgrowC;?>>
									<!--<td><?=$key+1;?></td>-->
									<td nowrap><?=$resourceResult->emp_report_dates;?></td>
									<td><?=$resourceResult->department;?></td>
									<td><?=$resourceResult->name;?></td>
									<td><?=$resourceResult->client_name;?></td>
									<td><?=$resourceResult->project_name;?></td>
									<td><?=$resourceResult->task_name;?></td>
									<td><?=$resourceResult->emp_time_hours;?></td>
									<td><?=$resourceResult->workplace;?></td>
									<td><?=$resourceResult->comments;?></td>
									<?php if(empty($_REQUEST['form_date'])): ?>
									<td>
										<?php if (!empty($resourceResult->resource_id)): ?>
										<a href="<?php echo base_url(); ?>resource_schedule/add/<?php echo $resourceResult->resource_id; ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"> </i></a>
										<?php else: ?>
										<a href="<?php echo base_url(); ?>resource_schedule/add" data-toggle="tooltip" title="Add"><i class="fa fa-lg fa-plus"> </i></a>
										<?php endif; ?>
									</td>

									<?php endif; ?>
								</tr>
								<?php endif; ?>
								<?php $previousManagerName = $mangerName; ?>
								<?php endforeach; 								
								?>
							</tbody>
						</table>
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
		$("form[name='resource_date_search']").validate({
			rules: {
				form_date: {
					required: true
				},
				to_date: {
					required: true
				}
			},
			messages: {
				form_date: "Please Select From Date",
				to_date: "Please Select To Date"
			},
			submitHandler: function(form) {
				form.submit();
			}
		});
	});

	$(document).ready(function() {
		$("#form_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			numberOfMonths: 1,
		});
		$("#to_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			numberOfMonths: 1,
		});

	})

	$("#downloadSearch_resouce_data").click(function() {
		$("#table22excel_all").table2excel({
			// exclude CSS class
			exclude: ".noExl",
			name: "Resource Schedule Report",
			filename: "resouce_report", // do not include extension
			fileext: ".xls", // file extension
			exclude_links: true,
			exclude_inputs: true,
			preserveColors: "preserveColors"
		});
	});

	jQuery('#client_Id,#department').select2(); // Autosuggest list
</script>
<script src="<?php echo HTTP_JS_PATH; ?>jquery.table2excel.js"></script>
<style>
	.select2-container--default .select2-selection--single .select2-selection__rendered {
		background-color: #663399;
		color: #fff;
		font-weight: bold;
	}

	.btn-lg {
  width: 50px !important;
  height: 50px !important;
  padding: 10px 10px !important;
  font-size: 12px !important;
  line-height: 1.33 !important;
  border-radius: 25px !important;
  cursor: pointer;
}

	.leave{background-color: #7030a0; cursor: pointer; padding: 10px; color: #FFF;}
	.training{background-color: #C2FFC7;cursor: pointer; padding: 10px; color: #000;}
	.njtraining{background-color: #ffa500;cursor: pointer; padding: 10px; color: #000; }
	.upleave{background-color: #FFE31A; cursor: pointer; padding: 10px; color: #000;}
	.available {background-color: #059212; cursor: pointer; padding: 10px; color: #FFF;}
	.na { background-color: #FF0000; cursor: pointer; padding: 10px; color: #FFF;}
	.wfh { background-color: #008b8b; cursor: pointer; padding: 10px; color: #FFF;} 
	.production { background-color: #428bc5;  cursor: pointer; padding: 10px; color: #fff;}
</style>
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->