<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
	
	$monday = date( 'Y-m-d', strtotime( 'last week monday' ) );
 	
	$friday = date( 'Y-m-d', strtotime( 'last week friday' ) );
  
		$listofManagerProjects = $this->defaulter_model->getMangerProjects();

		$listbetweenDates = isset($listbetweenDates) ? $listbetweenDates : $this->defaulter_model->lastWeekDays();
		$hoursMatrix = isset($hoursMatrix) ? $hoursMatrix : array();

?>
<div class="content-wrapper">
	<div class="page-title">
		<div>
			<h1>Timesheet Defaulter Log : <?php echo $monday.'  To  '. $friday;?></h1>
		</div>
		<div> <a class="btn btn-primary btn-flat" href="<?php echo base_url('defaulter/user_defaulter');?>" data-toggle="tooltip" title="refresh"><i class="fa fa-chevron-circle-left"></i></a> </div>
	</div>
	<div class="card">
		<h3 class="card-title"></h3>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<div class="bs-component">
						<div class="tab-content" id="myTabContent">
							<form class="" name="previous_user_defaulter" id="previous_user_defaulter" method="post" action="<?php echo base_url('defaulter/previous_user_defaulter');?>">
								<div class="tab-pane fade active in" id="Add">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Members</label>
												<?php
													$selectedMemberIds = array();
													if(is_array($selectedMemberEmpId)){
														$selectedMemberIds = array_map('strval', $selectedMemberEmpId);
													}elseif($selectedMemberEmpId !== '' && $selectedMemberEmpId !== null){
														$selectedMemberIds = array((string)$selectedMemberEmpId);
													}
												?>
												<select class="form-control" id="member_empId" name="member_empId[]" multiple="multiple">
													<?php foreach($members as $member): ?>
														<option value="<?php echo $member->empId; ?>" <?php echo in_array((string)$member->empId, $selectedMemberIds, true) ? 'selected' : ''; ?>>
															<?php echo ucfirst($member->name); ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Reporting Manager</label>
												<select class="form-control" id="reporting_manager" name="reporting_manager">
													<option value="">All Reporting Managers</option>
													<?php foreach($reportingManagers as $manager): ?>
														<option value="<?php echo $manager->empId; ?>" <?php echo ($selectedReportingManager == $manager->empId) ? 'selected' : ''; ?>>
															<?php echo ucfirst($manager->name); ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Start Date</label>
												<input class="form-control" type="text" value="<?php echo $monday; ?>" readonly="" style="background:rebeccapurple; color:white; font-weight: bold">
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">End Date</label>
												<input class="form-control" type="text" value="<?php echo $friday; ?>" readonly="" style="background:rebeccapurple; color:white; font-weight: bold">
											</div>
										</div>
									</div>
									<div class="card-footer">
										<button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Search</button>
										<a href="<?php echo base_url('defaulter/previous_user_defaulter');?>" data-toggle="Go To Report Log!" title="Cancel">
											<button class="btn btn-default icon-btn" type="button"><i class="fa fa-chevron-circle-left"></i>Back</button>
										</a>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="card">
		<div class="card-body">
			<div class="page-title">
				<div>
					<h1>Members Last Week Report Log</h1>
				</div>
				<div style="display:flex; gap:12px; align-items:center; justify-content:flex-end;">
					<a href="<?php echo base_url('defaulter/user_defaulter');?>" class="btn btn-flat" style="background-color:#f5a623; color:#fff; border-color:#f5a623;">Current Week Report Log</a>
					<button id="downloadEmployeeData" class="btn btn-primary btn-flat">Export Member data into Excel</button>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">	
						<table class="table table-hover table-bordered text-nowrap" id="table2excel">
							<thead>
								<tr>
									<th style="background-color:#c1c1c1">Sno</th>
									<th style="background-color:#c1c1c1">Manager Name</th>
									<th style="background-color:#c1c1c1">Employee Name</th>
									<th style="background-color:#c1c1c1">Employee ID</th>
									<th style="background-color:#c1c1c1">No of Instances</th>
									<?php foreach($listbetweenDates as $dateValue):
										$weekenDays = date(' D / d / M ', strtotime($dateValue));
									?>
									<th style="background-color:#c1c1c1;font-weight:Bold;"><?php echo $weekenDays; ?></th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php
									$cntNumber = 0;
									foreach($getEmpResult as $member){
										$memberHours = isset($hoursMatrix[$member->empId]) ? $hoursMatrix[$member->empId] : array();
										$emp_count = 0;
										foreach($listbetweenDates as $date):
											$hasRecord = isset($memberHours[$date]) ? $memberHours[$date] : null;
											if(!$hasRecord):
												$emp_count++;
											elseif(empty($hasRecord['is_leave']) && $hasRecord['hours'] < 8.5):
												$emp_count++;
											endif;
										endforeach;
										$totalCntofInstance = $emp_count;
								?>
								<tr>
									<td><?php echo $cntNumber + 1; ?></td>
									<td><b><?php echo htmlspecialchars(isset($member->manager_name) ? $member->manager_name : '', ENT_QUOTES, 'UTF-8'); ?></b></td>
									<td><?php echo $member->name; ?></td>
									<td><?php echo $member->emp_com_id; ?></td>
									<td><?php echo $totalCntofInstance; ?></td>
									<?php foreach($listbetweenDates as $date):
										$hasRecord = isset($memberHours[$date]) ? $memberHours[$date] : null;
										if($hasRecord):
											if(!empty($hasRecord['is_leave'])):
												echo '<td style="background-color:#4caf50; color:#FFF;font-weight:bold;text-align:center;">Leave</td>';
											elseif($hasRecord['hours'] < 8.5):
												echo '<td style="background-color:#f44336; color:#FFF;font-weight:bold;text-align:center;">'.($hasRecord['hours'] ? $hasRecord['hours'] : '0').'</td>';
											else:
												echo '<td style="background-color:#4caf50; color:#FFF;font-weight:bold;text-align:center;">'.$hasRecord['hours'].'</td>';
											endif;
										else:
											echo '<td style="background-color:#f44336; color:#FFF;font-weight:bold;text-align:center;">0</td>';
										endif;
									endforeach; ?>
								</tr>
								<?php
										$cntNumber++;
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<!-- Displaying Search Result -->
			</div>
		</div>
	</div>

	<!-- End of the report log -->

</div>
<script language="javascript" type="text/javascript">
	/* DatePicker */
	$(function() {
		$("form[name='user_defaulter']").validate({
			rules: {
				def_user_status_type: {
					required: true
				},
				def_form_date: {
					required: true
				},
				def_to_date: {
					required: true
				}
			},
			messages: {
				def_user_status_type: "Please Select User Type",
				def_form_date: "Please Select From Date",
				def_to_date: "Please Select To Date"
			},
			submitHandler: function(form) {
				form.submit();
			}
		});
	});

	/*Ajax Based dropdown option changes on Clients , Projects and Tasks*/

	$(document).ready(function() {
		var today = $("#def_form_date").val();

		var fridayDate = $("#def_to_date").val();

		$("#def_form_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			numberOfMonths: 1,
		});

		$("#def_to_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			numberOfMonths: 1,
		});

	})

	$('#reporting_manager').select2();
	$('#member_empId').select2({
		placeholder: 'All Members',
		allowClear: true,
		multiple: true
	});

	$('#reporting_manager').on('change', function() {
		$.ajax({
			type: "POST",
			url: "<?php echo base_url('defaulter/getMembersByManager');?>",
			data: {
				reporting_manager: $(this).val()
			},
			success: function(response) {
				$('#member_empId').html(response).trigger('change');
			}
		});
	});
	
	$("#downloadEmployeeData").click(function(){
  $("#table2excel").table2excel({
    // exclude CSS class
    exclude: ".noExl",
    name: "Report for employees",
    filename: "ReportLog", //do not include extension
    fileext: ".xls", // file extension
	  exclude_links: true,
	  exclude_inputs: true,
	  preserveColors: "preserveColors"
	  
	  
  }); 
});
</script>
<script src="<?php echo HTTP_JS_PATH; ?>jquery.table2excel.js"></script>

<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->