<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
	
	$monday = date( 'Y-m-d', strtotime( 'last week monday' ) );
	$friday = date( 'Y-m-d', strtotime( 'last week friday' ) );
	$listbetweenDates = isset($listbetweenDates) ? $listbetweenDates : $this->defaulter_model->lastWeekDays();
	$hoursMatrix = isset($hoursMatrix) ? $hoursMatrix : array();
	$getEmpResult = isset($getEmpResult) ? $getEmpResult : array();
	$members = isset($members) ? $members : array();
	$reportingManagers = isset($reportingManagers) ? $reportingManagers : array();
	$selectedMemberEmpId = isset($selectedMemberEmpId) ? $selectedMemberEmpId : array();
	$selectedReportingManager = isset($selectedReportingManager) ? $selectedReportingManager : '';
	$periodLabel = date('d M Y', strtotime($monday)) . ' to ' . date('d M Y', strtotime($friday));

	$totalMembers = 0;
	$totalInstances = 0;
	foreach($getEmpResult as $member){
		$totalMembers++;
		$memberHours = isset($hoursMatrix[$member->empId]) ? $hoursMatrix[$member->empId] : array();
		$joiningDate = isset($member->emp_joining_date) ? trim((string)$member->emp_joining_date) : '';
		foreach($listbetweenDates as $date){
			if($joiningDate !== '' && $date < $joiningDate){
				continue;
			}
			$hasRecord = isset($memberHours[$date]) ? $memberHours[$date] : null;
			if(!$hasRecord){
				$totalInstances++;
			}elseif(empty($hasRecord['is_leave']) && $hasRecord['hours'] < 8.5){
				$totalInstances++;
			}
		}
	}
?>
<div class="content-wrapper ts-defaulter-page">
	<div class="page-title ts-page-title">
		<div>
			<h1><i class="fa fa-calendar-times-o"></i> Previous Week Defaulters</h1>
			<p class="ts-subtitle">Last week missing hours, leave, and new joinees.</p>
		</div>
		<div class="ts-period-badge"><i class="fa fa-clock-o"></i> <?php echo htmlspecialchars($periodLabel, ENT_QUOTES, 'UTF-8'); ?></div>
	</div>

	<div class="card ts-filter-card">
		<div class="card-body">
			<form class="" name="previous_user_defaulter" id="previous_user_defaulter" method="post" action="<?php echo base_url('defaulter/previous_user_defaulter');?>">
				<div class="ts-filter-grid ts-filter-grid-4">
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
					<div class="form-group">
						<label class="control-label">Reporting Manager</label>
						<select class="form-control" id="reporting_manager" name="reporting_manager">
							<option value="">All Reporting Managers</option>
							<?php foreach($reportingManagers as $manager): ?>
								<option value="<?php echo $manager->empId; ?>" <?php echo ((string)$selectedReportingManager === (string)$manager->empId) ? 'selected' : ''; ?>>
									<?php echo ucfirst($manager->name); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label class="control-label">Start Date</label>
						<input class="form-control ts-date-input" type="text" value="<?php echo $monday; ?>" readonly="">
					</div>
					<div class="form-group">
						<label class="control-label">End Date</label>
						<input class="form-control ts-date-input" type="text" value="<?php echo $friday; ?>" readonly="">
					</div>
				</div>
				<div class="ts-filter-actions">
					<button class="btn ts-btn ts-btn-search"><i class="fa fa-search"></i> Search</button>
					<button type="button" id="clearPreviousDefaulterFilters" class="btn ts-btn ts-btn-clear" title="Clear filters"><i class="fa fa-refresh"></i> Clear Filters</button>
				</div>
			</form>
		</div>
	</div>

	<div class="ts-summary-row">
		<div class="ts-summary-card">
			<span class="ts-summary-label">Members</span>
			<strong><?php echo (int)$totalMembers; ?></strong>
		</div>
		<div class="ts-summary-card ts-summary-alert">
			<span class="ts-summary-label">Total Instances</span>
			<strong><?php echo (int)$totalInstances; ?></strong>
		</div>
		<div class="ts-summary-card ts-summary-legend">
			<span class="ts-summary-label">Legend</span>
			<div class="ts-legend">
				<span><i class="ts-dot ts-dot-ok"></i> Filled</span>
				<span><i class="ts-dot ts-dot-leave"></i> Leave</span>
				<span><i class="ts-dot ts-dot-miss"></i> Missing</span>
				<span><i class="ts-dot ts-dot-prejoin"></i> Before joining</span>
			</div>
		</div>
	</div>

	<div class="card ts-report-card">
		<div class="ts-report-head">
			<div>
				<h2>Members Last Week Report Log</h2>
				<p>Green = hours filled / leave, red = missing or below 8.5 hours, gray = before joining date.</p>
			</div>
			<div class="ts-report-actions">
				<a href="<?php echo base_url('defaulter/user_defaulter');?>" class="btn ts-btn ts-btn-current"><i class="fa fa-calendar"></i> Current Week</a>
				<button type="button" id="downloadEmployeeData" class="btn ts-btn ts-btn-export"><i class="fa fa-file-excel-o"></i> Export Excel</button>
			</div>
		</div>
		<div class="card-body ts-report-body">
			<div class="table-responsive ts-table-wrap">
				<table class="table table-bordered text-nowrap ts-grid" id="table2excel">
					<thead>
						<tr>
							<th bgcolor="#1F5076" style="background-color:#1F5076">Sno</th>
							<th bgcolor="#1F5076" style="background-color:#1F5076">Manager Name</th>
							<th bgcolor="#1F5076" style="background-color:#1F5076">Employee Name</th>
							<th bgcolor="#1F5076" style="background-color:#1F5076">Employee ID</th>
							<th bgcolor="#1F5076" style="background-color:#1F5076">No of Instances</th>
							<?php foreach($listbetweenDates as $dateValue): ?>
							<th bgcolor="#1F5076" style="background-color:#1F5076;font-weight:Bold;">
								<span class="ts-date-day"><?php echo date('D', strtotime($dateValue)); ?></span>
								<span class="ts-date-num"><?php echo date('d M', strtotime($dateValue)); ?></span>
							</th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php
						if(empty($getEmpResult)):
						?>
						<tr>
							<td colspan="<?php echo 5 + count($listbetweenDates); ?>" class="ts-empty">No members found for the selected filters.</td>
						</tr>
						<?php
						else:
							$cntNumber = 0;
							foreach($getEmpResult as $member){
								$memberHours = isset($hoursMatrix[$member->empId]) ? $hoursMatrix[$member->empId] : array();
								$joiningDate = isset($member->emp_joining_date) ? trim((string)$member->emp_joining_date) : '';
								$emp_count = 0;
								foreach($listbetweenDates as $date):
									if($joiningDate !== '' && $date < $joiningDate):
										continue;
									endif;
									$hasRecord = isset($memberHours[$date]) ? $memberHours[$date] : null;
									if(!$hasRecord):
										$emp_count++;
									elseif(empty($hasRecord['is_leave']) && $hasRecord['hours'] < 8.5):
										$emp_count++;
									endif;
								endforeach;
								$instanceClass = ($emp_count > 0) ? 'ts-instance ts-instance-alert' : 'ts-instance ts-instance-ok';
						?>
						<tr>
							<td class="ts-col-sno"><?php echo $cntNumber + 1; ?></td>
							<td class="ts-col-mgr"><b><?php echo htmlspecialchars(isset($member->manager_name) ? $member->manager_name : '', ENT_QUOTES, 'UTF-8'); ?></b></td>
							<td class="ts-col-name"><?php echo htmlspecialchars($member->name, ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="ts-col-id"><?php echo htmlspecialchars($member->emp_com_id, ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="ts-col-inst"><span class="<?php echo $instanceClass; ?>"><?php echo $emp_count; ?></span></td>
							<?php foreach($listbetweenDates as $date):
								if($joiningDate !== '' && $date < $joiningDate):
									echo '<td class="ts-cell ts-cell-prejoin" bgcolor="#9E9E9E" style="background-color:#9e9e9e !important; color:#FFF;font-weight:bold;text-align:center;">&nbsp;</td>';
									continue;
								endif;
								$hasRecord = isset($memberHours[$date]) ? $memberHours[$date] : null;
								if($hasRecord):
									if(!empty($hasRecord['is_leave'])):
										echo '<td class="ts-cell ts-cell-leave" bgcolor="#2E7D32" style="background-color:#2e7d32; color:#FFF;font-weight:bold;text-align:center;">Leave</td>';
									elseif($hasRecord['hours'] < 8.5):
										echo '<td class="ts-cell ts-cell-miss" bgcolor="#C62828" style="background-color:#c62828; color:#FFF;font-weight:bold;text-align:center;">'.($hasRecord['hours'] ? $hasRecord['hours'] : '0').'</td>';
									else:
										echo '<td class="ts-cell ts-cell-ok" bgcolor="#2E7D32" style="background-color:#2e7d32; color:#FFF;font-weight:bold;text-align:center;">'.$hasRecord['hours'].'</td>';
									endif;
								else:
									echo '<td class="ts-cell ts-cell-miss" bgcolor="#C62828" style="background-color:#c62828; color:#FFF;font-weight:bold;text-align:center;">0</td>';
								endif;
							endforeach; ?>
						</tr>
						<?php
								$cntNumber++;
							}
						endif;
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<style>
.ts-defaulter-page { padding-bottom: 28px; }
.ts-page-title { align-items: flex-start; margin-bottom: 16px; }
.ts-page-title h1 { margin: 0 0 6px; font-size: 26px; font-weight: 700; color: #1f5076; }
.ts-page-title h1 i { margin-right: 8px; }
.ts-subtitle { margin: 0; color: #6c7a89; font-size: 13px; }
.ts-period-badge {
	background: #eef5fb;
	color: #1f5076;
	border: 1px solid #cfe0ef;
	border-radius: 999px;
	padding: 8px 14px;
	font-weight: 700;
	font-size: 13px;
	white-space: nowrap;
}
.ts-filter-card, .ts-report-card {
	border: 1px solid #e3e8ef;
	border-radius: 12px;
	box-shadow: 0 2px 10px rgba(31, 80, 118, 0.06);
	overflow: hidden;
	margin-bottom: 16px;
}
.ts-filter-head, .ts-report-head {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 12px;
	padding: 14px 18px;
	background: linear-gradient(180deg, #f7fbfe 0%, #eef4f9 100%);
	border-bottom: 1px solid #e3e8ef;
}
.ts-filter-head span, .ts-report-head h2 {
	margin: 0;
	font-size: 16px;
	font-weight: 700;
	color: #1f5076;
}
.ts-report-head p { margin: 4px 0 0; color: #6c7a89; font-size: 12px; }
.ts-report-actions { display: flex; flex-wrap: wrap; gap: 8px; }
.ts-filter-grid {
	display: grid;
	grid-template-columns: repeat(4, minmax(0, 1fr));
	gap: 14px;
}
.ts-filter-card .form-group { margin-bottom: 0; }
.ts-filter-card .control-label {
	font-size: 12px;
	font-weight: 700;
	color: #4a5d6e;
	margin-bottom: 6px;
	text-transform: uppercase;
	letter-spacing: .3px;
}
.ts-filter-card .select2-container { width: 100% !important; }
.ts-filter-card .select2-container--default .select2-selection--multiple,
.ts-filter-card .select2-container--default .select2-selection--single {
	min-height: 38px;
	border: 1px solid #cfd8e3;
	border-radius: 8px;
}
.ts-filter-card .select2-container--default .select2-selection--multiple { padding: 3px 6px; }
.ts-filter-card .select2-container--default .select2-selection--single .select2-selection__rendered {
	line-height: 36px;
	color: #1f5076;
}
.ts-filter-card .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
.ts-date-input {
	background: #1f5076 !important;
	color: #fff !important;
	font-weight: 700;
	border: 0 !important;
	border-radius: 8px;
}
.ts-filter-actions { margin-top: 16px; display: flex; gap: 10px; }
.ts-btn {
	border-radius: 8px !important;
	font-weight: 700;
	padding: 8px 14px;
	border: 0;
}
.ts-btn-search { background: #1f5076; color: #fff; }
.ts-btn-search:hover { background: #163d5b; color: #fff; }
.ts-btn-clear { background: #f4a261; color: #fff; }
.ts-btn-clear:hover { background: #e08b45; color: #fff; }
.ts-btn-current { background: #f4a261; color: #fff; }
.ts-btn-current:hover { background: #e08b45; color: #fff; }
.ts-btn-export { background: #1f5076; color: #fff; }
.ts-btn-export:hover { background: #163d5b; color: #fff; }
.ts-summary-row { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
.ts-summary-card {
	background: #fff;
	border: 1px solid #e3e8ef;
	border-radius: 12px;
	padding: 14px 16px;
	min-width: 150px;
	box-shadow: 0 2px 8px rgba(31, 80, 118, 0.06);
	flex: 1 1 150px;
}
.ts-summary-label {
	display: block;
	font-size: 11px;
	font-weight: 700;
	letter-spacing: .4px;
	text-transform: uppercase;
	color: #7a8896;
	margin-bottom: 6px;
}
.ts-summary-card strong { font-size: 24px; color: #1f5076; }
.ts-summary-alert strong { color: #c62828; }
.ts-legend { display: flex; flex-wrap: wrap; gap: 12px; font-weight: 600; color: #334155; font-size: 13px; }
.ts-dot { display: inline-block; width: 12px; height: 12px; border-radius: 3px; margin-right: 6px; vertical-align: -1px; }
.ts-dot-ok, .ts-dot-leave { background: #2e7d32; }
.ts-dot-miss { background: #c62828; }
.ts-dot-prejoin { background: #9e9e9e; }
.ts-report-body { padding: 0; }
.ts-table-wrap { margin: 0; }
.ts-grid { margin: 0; background: #fff; }
.ts-grid thead th {
	background: #1f5076 !important;
	color: #fff !important;
	font-weight: 700;
	text-align: center;
	vertical-align: middle;
	padding: 10px 8px;
	border-color: #17405f !important;
	position: sticky;
	top: 0;
	z-index: 2;
}
.ts-date-day { display: block; font-size: 11px; opacity: .85; text-transform: uppercase; }
.ts-date-num { display: block; font-size: 13px; }
.ts-grid td {
	vertical-align: middle;
	padding: 8px;
	border-color: #e6edf3 !important;
}
.ts-col-sno, .ts-col-id, .ts-col-inst { text-align: center; }
.ts-col-mgr { color: #1f5076; }
.ts-instance {
	display: inline-block;
	min-width: 28px;
	padding: 3px 8px;
	border-radius: 999px;
	font-weight: 700;
}
.ts-instance-ok { background: #e8f5e9; color: #2e7d32; }
.ts-instance-alert { background: #ffebee; color: #c62828; }
.ts-cell { min-width: 72px; }
.ts-empty { text-align: center; padding: 28px !important; color: #6c7a89; font-weight: 600; }
@media (max-width: 1100px) {
	.ts-filter-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
	.ts-report-head { flex-direction: column; align-items: flex-start; }
}
@media (max-width: 700px) {
	.ts-filter-grid { grid-template-columns: 1fr; }
}
</style>

<script language="javascript" type="text/javascript">
	$('#reporting_manager').select2({
		placeholder: 'All Reporting Managers',
		allowClear: true
	});
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

	$('#clearPreviousDefaulterFilters').on('click', function(e) {
		e.preventDefault();
		$('#reporting_manager').val('').trigger('change');
		$('#member_empId').val(null).trigger('change');
		$('#previous_user_defaulter').submit();
	});

	$("#downloadEmployeeData").click(function(){
		var $exportForm = $('<form>', {
			method: 'POST',
			action: "<?php echo base_url('defaulter/export_previous_week_excel'); ?>"
		});
		$.each($('#previous_user_defaulter').serializeArray(), function(_, field) {
			$exportForm.append($('<input>', { type: 'hidden', name: field.name, value: field.value }));
		});
		$exportForm.appendTo('body').submit().remove();
	});
</script>

<?php $this->load->view('includes/cRMFooter'); ?>
