<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
	
	$monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
 	
	$friday = date( 'Y-m-d', strtotime( 'friday this week' ) );
  
     $getWeekDates = $this->defaulter_model->weeklyDays(); 

   $listofManagerProjects = $this->defaulter_model->getMangerProjects();
                                    
  //echo '<pre>'; print_r($listofManagerProjects);

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
							<form class="" name="user_defaulter" id="user_defaulter" method="post" action="<?php echo base_url('defaulter/memberSearch');?>">
								<div class="tab-pane fade active in" id="Add">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">Department</label>
												<?php
													$departmentOptions = function_exists('ts_department_options') ? ts_department_options() : array();
												?>
												<select class="form-control" id="department" name="department[]" multiple="multiple">
													<?php foreach($departmentOptions as $deptOption): ?>
														<option value="<?php echo htmlspecialchars($deptOption, ENT_QUOTES, 'UTF-8'); ?>">
															<?php echo htmlspecialchars($deptOption, ENT_QUOTES, 'UTF-8'); ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">Members</label>
												<select class="form-control" id="member_empId" name="member_empId[]" multiple="multiple">
													<?php foreach($members as $member): ?>
														<option value="<?php echo $member->empId; ?>"><?php echo ucfirst($member->name); ?></option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">Reporting Manager</label>
												<select class="form-control" id="reporting_manager" name="reporting_manager[]" multiple="multiple">
													<?php foreach($reportingManagers as $manager): ?>
														<option value="<?php echo $manager->empId; ?>"><?php echo ucfirst($manager->name); ?></option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Week Start Date</label>
												<input class="form-control" type="text" id="def_form_date" name="def_form_date" placeholder="Select From Date" value="<?php echo $monday; ?>" readonly="">
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Week End Date</label>
												<input class="form-control" type="text" id="def_to_date" name="def_to_date" placeholder="Select To Date" value="<?php echo $friday; ?>" readonly="">
											</div>
										</div>
									</div>
									<div class="card-footer">
										<button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Search</button>
										<button type="button" id="clearMemberSearchFilters" class="btn btn-warning icon-btn" title="Clear filters"><i class="fa fa-fw fa-lg fa-refresh"></i>Clear Filters</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div> 
	<!-- Get List of user not filled report log on date wise -->
 
	
	<!-- End of the report log -->

</div>
<script language="javascript" type="text/javascript">
	/* DatePicker */
	$(function() {
		$("form[name='user_defaulter']").validate({
			rules: {
				def_form_date: {
					required: true
				},
				def_to_date: {
					required: true
				}
			},
			messages: {
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

		// Automatically show current week data without clicking Search
		if ($('#def_form_date').val() && $('#def_to_date').val()) {
			$('#user_defaulter').submit();
		}

	})

	$('#department').select2({
		placeholder: 'All Departments',
		allowClear: true,
		multiple: true
	});
	$('#reporting_manager').select2({
		placeholder: 'All Reporting Managers',
		allowClear: true,
		multiple: true
	});
	$('#member_empId').select2({
		placeholder: 'All Members',
		allowClear: true,
		multiple: true
	});

	function reloadMemberOptions() {
		$.ajax({
			type: "POST",
			url: "<?php echo base_url('defaulter/getMembersByManager');?>",
			data: {
				reporting_manager: $('#reporting_manager').val(),
				department: $('#department').val()
			},
			success: function(response) {
				$('#member_empId').html(response).trigger('change');
			}
		});
	}

	$('#reporting_manager').on('change', reloadMemberOptions);
	$('#department').on('change', reloadMemberOptions);

	$('#clearMemberSearchFilters').on('click', function(e) {
		e.preventDefault();
		$('#department').off('change', reloadMemberOptions).val(null).trigger('change');
		$('#reporting_manager').off('change', reloadMemberOptions).val(null).trigger('change');
		$('#member_empId').val(null).trigger('change');
		$('#def_form_date').val('<?php echo $monday; ?>');
		$('#def_to_date').val('<?php echo $friday; ?>');
		$('#department').on('change', reloadMemberOptions);
		$('#reporting_manager').on('change', reloadMemberOptions);
		$('#user_defaulter').submit();
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