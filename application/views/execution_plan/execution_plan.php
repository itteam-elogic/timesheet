<?php $this->load->view('includes/cRMHeader'); ?>
<div class="content-wrapper">
	<div id="ep_page_loader" class="ep-page-loader">
		<div class="ep-page-loader-content">
			<div class="ep-page-loader-spinner"><i class="fa fa-spinner fa-spin"></i></div>
			<div class="ep-page-loader-text">
				<strong>Please wait</strong>
				<span>Loading filters and data...</span>
			</div>
		</div>
	</div>
	<iframe id="ep_export_iframe" name="ep_export_iframe" style="display:none;"></iframe>
	<div class="page-title">
		<div>
			<h1><i class="fa fa-paper-plane"></i> Execution Plan</h1>
		</div>
		<div>
			<?php
				$selectedBillingType = isset($selected_man_days_type) ? strtolower(trim((string)$selected_man_days_type)) : '';
				if (!in_array($selectedBillingType, array('hourly', 'monthly'), true)) {
					$selectedBillingType = '';
					if (!empty($man_days)) {
						$candidateBillingType = strtolower(trim((string)reset($man_days)));
						if (in_array($candidateBillingType, array('hourly', 'monthly'), true)) {
							$selectedBillingType = $candidateBillingType;
						}
					}
				}
			?>
			<a class="btn btn-primary btn-flat" href="<?php echo base_url('execution_plan'); ?>"><i class="fa fa-refresh"></i> Reset</a>
			<button type="button" class="btn btn-success btn-flat" id="ep_export_report_btn">
				<i class="fa fa-download"></i> Export Report
			</button>
		</div>
	</div>

	<div class="card">
		<div class="card-body">
			<form id="execution_plan_search_form" method="post" action="<?php echo base_url('execution_plan'); ?>">
				<div class="row ep-filter-row">
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">Department</label>
							<select class="form-control" name="department[]" id="department" multiple="multiple">
								<?php
									$masterDepartments = function_exists('ts_primary_delivery_departments')
										? ts_primary_delivery_departments()
										: array('Architectural','Structural','3D Visualization','2D Auto CAD','MEP');
									$departmentOptions = array();
									foreach ($masterDepartments as $masterDept) {
										$normalizedMasterDept = strtolower(trim((string)$masterDept));
										if ($normalizedMasterDept === '') { continue; }
										$departmentOptions[$normalizedMasterDept] = $masterDept;
									}
									if (!empty($departments)) {
										foreach ($departments as $dept) {
											$deptValue = isset($dept->department) ? trim((string)$dept->department) : '';
											$deptKey = strtolower($deptValue);
											if ($deptKey === '' || $deptKey === 'other services' || in_array($deptKey, array('business development', 'downtime'), true)) { continue; }
											if ($deptKey === 'operations manager') {
												$deptKey = 'operations';
											}
											if (!isset($departmentOptions[$deptKey])) {
												$departmentOptions[$deptKey] = $deptValue;
											}
										}
									}
									foreach ($departmentOptions as $deptValue):
								?>
									<option value="<?php echo htmlspecialchars($deptValue, ENT_QUOTES, 'UTF-8'); ?>" <?php echo in_array((string)$deptValue, (array)$department, true) ? 'selected="selected"' : ''; ?>>
										<?php echo htmlspecialchars($deptValue, ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Client</label>
							<select class="form-control" name="client_Id[]" id="client_Id" multiple="multiple">
								<?php if (!empty($clients)) { foreach ($clients as $client) { ?>
									<option value="<?php echo (int)$client->client_Id; ?>" <?php echo in_array((string)$client->client_Id, (array)$client_Id, true) ? 'selected="selected"' : ''; ?>>
										<?php echo htmlspecialchars(ucfirst(str_replace("'", " ", $client->client_name)), ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php }} ?>
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Project</label>
							<select class="form-control" name="project_Id[]" id="project_Id" multiple="multiple">
								<?php if (!empty($projects)) { foreach ($projects as $project) { ?>
									<option value="<?php echo (int)$project->project_Id; ?>" <?php echo in_array((string)$project->project_Id, (array)$project_Id, true) ? 'selected="selected"' : ''; ?>>
										<?php echo htmlspecialchars($project->project_name, ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php }} ?>
							</select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">Project Manager</label>
							<select class="form-control" name="project_manager[]" id="project_manager" multiple="multiple">
								<?php if (!empty($managers)) { foreach ($managers as $manager) { ?>
									<option value="<?php echo (int)$manager->empId; ?>" <?php echo in_array((string)$manager->empId, (array)$project_manager, true) ? 'selected="selected"' : ''; ?>>
										<?php echo htmlspecialchars($manager->name, ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php }} ?>
							</select>
						</div>
					</div>
				</div>
				<input type="hidden" name="man_days" id="man_days" value="<?php echo htmlspecialchars($selectedBillingType, ENT_QUOTES, 'UTF-8'); ?>">
				<input type="hidden" name="project_status" id="project_status" value="<?php echo !empty($project_status) ? htmlspecialchars(reset($project_status), ENT_QUOTES, 'UTF-8') : ''; ?>">
				<input type="hidden" name="ep_default_year" id="ep_default_year" value="">
				<div class="row ep-ym-row">
					<div class="col-md-6">
						<div class="form-group ep-ym-group">
							<label class="control-label">From</label>
							<div class="row">
								<div class="col-xs-5" style="padding-right: 6px;">
									<select class="form-control" name="from_year" id="from_year">
										<option value="all" <?php echo empty($from_year) ? 'selected="selected"' : ''; ?>>All</option>
										<?php if (!empty($years)) { foreach ($years as $yearOption) { ?>
											<option value="<?php echo (int)$yearOption->year; ?>" <?php echo in_array((string)$yearOption->year, (array)$from_year, true) ? 'selected="selected"' : ''; ?>>
												<?php echo (int)$yearOption->year; ?>
											</option>
										<?php }} ?>
									</select>
								</div>
								<div class="col-xs-7" style="padding-left: 6px;">
									<select class="form-control" name="from_month" id="from_month">
										<option value="all" <?php echo empty($from_month) ? 'selected="selected"' : ''; ?>>Month</option>
										<?php if (!empty($months)) { foreach ($months as $monthOption) { ?>
											<option value="<?php echo (int)$monthOption->month_number; ?>" <?php echo in_array((string)$monthOption->month_number, (array)$from_month, true) ? 'selected="selected"' : ''; ?>>
												<?php echo htmlspecialchars($monthOption->month_name, ENT_QUOTES, 'UTF-8'); ?>
											</option>
										<?php }} ?>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group ep-ym-group">
							<label class="control-label">To</label>
							<div class="row">
								<div class="col-xs-5" style="padding-right: 6px;">
									<select class="form-control" name="to_year" id="to_year">
										<option value="all" <?php echo empty($to_year) ? 'selected="selected"' : ''; ?>>All</option>
										<?php if (!empty($years)) { foreach ($years as $yearOption) { ?>
											<option value="<?php echo (int)$yearOption->year; ?>" <?php echo in_array((string)$yearOption->year, (array)$to_year, true) ? 'selected="selected"' : ''; ?>>
												<?php echo (int)$yearOption->year; ?>
											</option>
										<?php }} ?>
									</select>
								</div>
								<div class="col-xs-7" style="padding-left: 6px;">
									<select class="form-control" name="to_month" id="to_month">
										<option value="all" <?php echo empty($to_month) ? 'selected="selected"' : ''; ?>>Month</option>
										<?php if (!empty($months)) { foreach ($months as $monthOption) { ?>
											<option value="<?php echo (int)$monthOption->month_number; ?>" <?php echo in_array((string)$monthOption->month_number, (array)$to_month, true) ? 'selected="selected"' : ''; ?>>
												<?php echo htmlspecialchars($monthOption->month_name, ENT_QUOTES, 'UTF-8'); ?>
											</option>
										<?php }} ?>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer" style="padding-left:0;">
					<button class="btn btn-primary icon-btn" type="submit">
						<i class="fa fa-fw fa-lg fa-check-circle"></i>Search
					</button>
					<?php
						$selectedStatusValue = isset($selected_project_status) ? strtolower(trim((string)$selected_project_status)) : '';
						if ($selectedStatusValue === '' && !empty($project_status)) {
							$selectedStatusValue = strtolower(trim((string)reset($project_status)));
						}
					?>
					<?php
						$currentYearValue = (string)date('Y');
						$fromYearValue = !empty($from_year) ? (string)reset($from_year) : '';
						$toYearValue = !empty($to_year) ? (string)reset($to_year) : '';
						$fromMonthValue = !empty($from_month) ? (string)reset($from_month) : '';
						$toMonthValue = !empty($to_month) ? (string)reset($to_month) : '';
						$isCurrentYearSelected = ($fromYearValue === $currentYearValue && $toYearValue === $currentYearValue && $fromMonthValue === '' && $toMonthValue === '');
					?>
					<div class="ep-status-buttons-wrap">
						<div class="ep-billing-tabs-wrap">
							<button type="button" class="btn ep-billing-tab <?php echo $selectedBillingType === 'hourly' ? 'active' : ''; ?>" data-billing-type="hourly">Hourly</button>
							<button type="button" class="btn ep-billing-tab <?php echo $selectedBillingType === 'monthly' ? 'active' : ''; ?>" data-billing-type="monthly">Monthly</button>
						</div>
						<button type="button" class="btn ep-status-btn ep-status-process <?php echo ($selectedStatusValue === 'process' || $selectedStatusValue === 'in process' || $selectedStatusValue === 'in_process') ? 'active' : ''; ?>" data-status="Process">
							In Process
						</button>
						<button type="button" class="btn ep-status-btn ep-status-closed <?php echo ($selectedStatusValue === 'closed') ? 'active' : ''; ?>" data-status="Closed">
							Closed
						</button>
						<button type="button" class="btn ep-status-btn ep-status-hold <?php echo ($selectedStatusValue === 'on hold' || $selectedStatusValue === 'on_hold') ? 'active' : ''; ?>" data-status="On Hold">
							On Hold
						</button>
						<button type="button" class="btn btn-primary btn-flat ep-filter-refresh-btn" id="ep_show_all_btn">
							<i class="fa fa-list"></i> Show All
						</button>
						<!-- <button type="button" class="btn ep-year-btn <?php echo $isCurrentYearSelected ? 'active' : ''; ?>" id="ep_current_year_btn" data-year="<?php echo htmlspecialchars($currentYearValue, ENT_QUOTES, 'UTF-8'); ?>">
							Current Year
						</button>-->
					</div>
				</div>
			</form>
		</div>
	</div>

	<?php if (!empty($allClientResult)): ?>
		<?php
			if (!function_exists('execution_plan_hours_display')) {
				function execution_plan_hours_display($value) {
					$value = (float)$value;
					if (fmod($value, 1.0) == 0.0) {
						return (string)(int)$value;
					}
					return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
				}
			}
			if (!function_exists('execution_plan_estimated_hours_display')) {
				function execution_plan_estimated_hours_display($value) {
					$value = (float)$value;
					if ($value == 0) {
						return 'As Per Actual';
					}
					return execution_plan_hours_display($value);
				}
			}
			if (!function_exists('execution_plan_date_display')) {
				function execution_plan_date_display($value) {
					if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
						return '';
					}
					$ts = strtotime($value);
					if ($ts === false) {
						return '';
					}
					return date('d-M-Y', $ts);
				}
			}
			if (!function_exists('execution_plan_man_days_display')) {
				function execution_plan_man_days_display($value) {
					if ($value === null || $value === '') {
						return '';
					}
					$value = trim((string)$value);
					if ($value === '') {
						return '';
					}
					if (is_numeric($value)) {
						return $value;
					}
					return ucfirst(strtolower($value));
				}
			}
			if (!function_exists('execution_plan_project_status_display')) {
				function execution_plan_project_status_display($value) {
					if ($value === null || $value === '') {
						return '';
					}
					$value = trim((string)$value);
					if ($value === '') {
						return '';
					}
					return ucwords(strtolower($value));
				}
			}
			if (!function_exists('execution_plan_project_status_badge')) {
				function execution_plan_project_status_badge($value) {
					$normalized = strtolower(trim((string)$value));
					if ($normalized === '' || $normalized === 'all') {
						return '';
					}
					$label = strtoupper($normalized);
					$class = 'status-default';
					if ($normalized === 'process' || $normalized === 'in process' || $normalized === 'in_process') {
						$label = 'IN PROCESS';
						$class = 'status-process';
					} elseif ($normalized === 'closed') {
						$label = 'CLOSED';
						$class = 'status-closed';
					}
					return '<span class="ep-status-badge ' . $class . '"><i class="fa fa-times-circle"></i> ' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
				}
			}
			if (!function_exists('execution_plan_client_status_badge')) {
				function execution_plan_client_status_badge($value) {
					$normalized = strtolower(trim((string)$value));
					if ($normalized === '') {
						$normalized = 'inactive';
					}
					if ($normalized === 'active') {
						return '<span class="ep-status-badge status-active"><i class="fa fa-check-circle"></i> Active</span>';
					}
					if ($normalized === 'inactive' || $normalized === 'in_active' || $normalized === 'in active') {
						return '<span class="ep-status-badge status-inactive"><i class="fa fa-times-circle"></i> Inactive</span>';
					}
					return '<span class="ep-status-badge status-default"><i class="fa fa-circle"></i> ' . htmlspecialchars(ucwords($normalized), ENT_QUOTES, 'UTF-8') . '</span>';
				}
			}
			if (!function_exists('execution_plan_team_members_list')) {
				function execution_plan_team_members_list($value, $excludeNames = array()) {
					$value = trim((string)$value);
					if ($value === '') {
						return array();
					}
					$normalized = str_replace(array("\r\n", "\n", "\r", ';', '|'), ',', $value);
					$parts = array_filter(array_map('trim', explode(',', $normalized)), function($item) {
						return $item !== '' && strtolower($item) !== 'please choose team members';
					});
					$excludeMap = array();
					foreach ((array)$excludeNames as $excludeName) {
						$excludeKey = strtolower(trim((string)$excludeName));
						if ($excludeKey !== '' && $excludeKey !== 'n/a') {
							$excludeMap[$excludeKey] = true;
						}
					}
					$unique = array();
					foreach ($parts as $name) {
						$key = strtolower($name);
						if (isset($excludeMap[$key])) {
							continue;
						}
						if (!isset($unique[$key])) {
							$unique[$key] = $name;
						}
					}
					return array_values($unique);
				}
			}
			if (!function_exists('execution_plan_team_members_display')) {
				function execution_plan_team_members_display($value, $excludeNames = array()) {
					$names = execution_plan_team_members_list($value, $excludeNames);
					if (empty($names)) {
						return '';
					}
					$escaped = array_map(function($name) {
						return htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
					}, $names);
					return implode(', ', $escaped) . ' ( ' . count($names) . ' )';
				}
			}
			if (!function_exists('execution_plan_team_members_count_display')) {
				function execution_plan_team_members_count_display($value, $excludeNames = array()) {
					$names = execution_plan_team_members_list($value, $excludeNames);
					if (empty($names)) {
						return '';
					}
					return '( ' . count($names) . ' )';
				}
			}
			if (!function_exists('execution_plan_billing_mode')) {
				function execution_plan_billing_mode($billingValue, $selectedBillingType = '') {
					$normalized = strtolower(trim((string)$billingValue));
					if ($normalized !== '') {
						if (strpos($normalized, 'hour') !== false) {
							return 'hourly';
						}
						if (strpos($normalized, 'month') !== false) {
							return 'monthly';
						}
					}
					$selectedNormalized = strtolower(trim((string)$selectedBillingType));
					if ($selectedNormalized === 'hourly' || $selectedNormalized === 'monthly') {
						return $selectedNormalized;
					}
					return 'monthly';
				}
			}
			if (!function_exists('execution_plan_calculate_difference')) {
				function execution_plan_calculate_difference($billingMode, $scheduleHours, $timesheetHours, $invoiceHours) {
					$scheduleHours = (float)$scheduleHours;
					$timesheetHours = (float)$timesheetHours;
					$forceGreen = false;

					// As Per Actual (0 estimated hours): show timesheet hours in green in Difference column
					if ($scheduleHours == 0) {
						$diff = $timesheetHours;
						$forceGreen = true;
					} elseif ($billingMode === 'hourly') {
						$diff = $scheduleHours - $timesheetHours;
					} else {
						// Monthly: show timesheet hours only
						$diff = $timesheetHours;
					}

					$diffClass = $forceGreen ? 'diff-green' : (($diff < 0) ? 'diff-red' : 'diff-green');
					return array('value' => $diff, 'class' => $diffClass);
				}
			}
			$deptTotals = array();
			$grandResource = 0;
			$grandTimesheet = 0;
			foreach ($allClientResult as $r) {
				$dept = !empty($r->department) ? trim($r->department) : 'N/A';
				if (!isset($deptTotals[$dept])) {
					$deptTotals[$dept] = array('resource_hours' => 0, 'timesheet_hours' => 0);
				}
				$deptTotals[$dept]['resource_hours'] += !empty($r->schedule_hours) ? (float)$r->schedule_hours : 0;
				$deptTotals[$dept]['timesheet_hours'] += !empty($r->timesheet_hours) ? (float)$r->timesheet_hours : 0;
			}
			foreach ($deptTotals as $t) {
				$grandResource += $t['resource_hours'];
				$grandTimesheet += $t['timesheet_hours'];
			}
			ksort($deptTotals, SORT_STRING);
		?>

	<div class="card">
		<div class="card-body">
			<?php
				$hideResourceColumn = isset($selectedBillingType) && $selectedBillingType === 'hourly';
				$isHourlyBilling = isset($selectedBillingType) && $selectedBillingType === 'hourly';
				$isMonthlyBilling = isset($selectedBillingType) && $selectedBillingType === 'monthly';
				$differenceColumnLabel = 'Difference<br/>P. EST - TS';
			?>
			<?php
				$projectStatusSummary = isset($projectStatusSummary) && is_array($projectStatusSummary) ? $projectStatusSummary : array(
					'in_process' => 0,
					'closed' => 0,
					'on_hold' => 0,
					'total' => 0
				);
				if (!isset($projectStatusSummary['total'])) {
					$projectStatusSummary['total'] = (int)$projectStatusSummary['in_process'] + (int)$projectStatusSummary['closed'] + (int)$projectStatusSummary['on_hold'];
				}
			?>
			<div class="execution-plan-summary-wrap">
				<div class="execution-plan-summary-total-projects">
					Total Projects: <strong><?php echo (int)$projectStatusSummary['total']; ?></strong>
				</div>
				<div class="table-responsive">
					<table class="table table-bordered execution-plan-summary-table">
						<thead>
							<tr>
								<th>Project Status</th>
								<th>Count</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>In Process</td>
								<td class="count-process"><?php echo (int)$projectStatusSummary['in_process']; ?></td>
							</tr>
							<tr>
								<td>On Hold</td>
								<td class="count-hold"><?php echo (int)$projectStatusSummary['on_hold']; ?></td>
							</tr>
							<tr>
								<td>Closed</td>
								<td class="count-closed"><?php echo (int)$projectStatusSummary['closed']; ?></td>
							</tr>
							<tr>
								<td>Total</td>
								<td class="count-total"><?php echo (int)$projectStatusSummary['total']; ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="table-responsive">
				<table class="table table-hover table-bordered" id="execution_plan_table">
					<thead>
						<tr>
							<th class="ep-col-pm">Project Manager</th>
							<th class="ep-col-client">Client Name / Projects</th>
							<th class="ep-col-date">Start Date</th>
							<th class="ep-col-date">End Date</th>
							<th class="ep-col-billing">Billing Type</th>
							<th class="ep-col-date">Timesheet Date</th>
							<th class="ep-col-status">Project Status</th>
							<th class="ep-col-hours">Project Estimated Hours</th>
							<th class="ep-col-hours">Timesheet Hours</th>
							<th class="ep-col-hours">Invoice Hours</th>
							<th class="ep-col-hours" style="text-transform:none !important;"><?php echo $differenceColumnLabel; ?></th>
							<?php if (!$hideResourceColumn): ?>
							<th class="ep-col-resources">Resources</th>
							<th class="ep-col-team">Team Members</th>
							<?php endif; ?>
						</tr>
					</thead>
					<tbody>
						<?php
						$clientGroups = isset($clientGroups) && is_array($clientGroups) ? $clientGroups : array();
						$clientIndex = 0;
						foreach ($clientGroups as $clientGroup):
							$clientIndex++;
							$clientName = $clientGroup['clientName'];
							$clientData = array('projects' => $clientGroup['projects']);
							$clientScheduleTotal = $clientGroup['clientScheduleTotal'];
							$clientTimesheetTotal = $clientGroup['clientTimesheetTotal'];
							$clientInvoiceTotal = $clientGroup['clientInvoiceTotal'];
							$clientBillingTypeDisplay = $clientGroup['clientBillingTypeDisplay'];
							$clientBillingMode = $clientGroup['clientBillingMode'];
							$clientStatus = $clientGroup['clientStatus'];
							$clientManagerDisplay = $clientGroup['clientManagerDisplay'];
							$clientDiffResult = execution_plan_calculate_difference($clientBillingMode, $clientScheduleTotal, $clientTimesheetTotal, $clientInvoiceTotal);
							$clientDiff = $clientDiffResult['value'];
							$clientDiffClass = $clientDiffResult['class'];
							$clientStartDate = $clientGroup['clientStartDateTs'] !== null ? date('d-M-Y', $clientGroup['clientStartDateTs']) : '';
							$clientEndDate = $clientGroup['clientEndDateTs'] !== null ? date('d-M-Y', $clientGroup['clientEndDateTs']) : '';
							$clientTimesheetEntryDate = !empty($clientGroup['clientTimesheetEntryDateTs']) ? date('d-M-Y', $clientGroup['clientTimesheetEntryDateTs']) : '';
							$rowSearch = strtolower($clientManagerDisplay . ' ' . $clientName);
						?>
						<tr class="client-header-row" data-client-index="<?php echo $clientIndex; ?>" data-search="<?php echo htmlspecialchars($rowSearch, ENT_QUOTES, 'UTF-8'); ?>">
							<td class="pm-cell"><?php echo htmlspecialchars($clientManagerDisplay, ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="client-cell">
								<span class="client-name-text"><?php echo $clientName; ?></span>
								<span class="client-toggle-icon" data-client-index="<?php echo $clientIndex; ?>"><i class="fa fa-plus"></i></span>
							</td>
							<td class="date-cell"><?php echo !empty($clientStartDate) ? '<i class="fa fa-calendar"></i> ' . htmlspecialchars($clientStartDate, ENT_QUOTES, 'UTF-8') : ''; ?></td>
							<td class="date-cell"><?php echo !empty($clientEndDate) ? '<i class="fa fa-calendar"></i> ' . htmlspecialchars($clientEndDate, ENT_QUOTES, 'UTF-8') : ''; ?></td>
							<td class="date-cell"><strong><?php echo htmlspecialchars($clientBillingTypeDisplay, ENT_QUOTES, 'UTF-8'); ?></strong></td>
							<td class="date-cell"><?php echo !empty($clientTimesheetEntryDate) ? '<i class="fa fa-calendar"></i> ' . htmlspecialchars($clientTimesheetEntryDate, ENT_QUOTES, 'UTF-8') : ''; ?></td>
							<td class="date-cell"><?php echo execution_plan_client_status_badge($clientStatus); ?></td>
							<td class="num-cell"><strong><?php echo execution_plan_estimated_hours_display($clientScheduleTotal); ?></strong></td>
							<td class="num-cell"><strong><?php echo execution_plan_hours_display($clientTimesheetTotal); ?></strong></td>
							<td class="num-cell"><strong><?php echo execution_plan_hours_display($clientInvoiceTotal); ?></strong></td>
							<td class="num-cell diff-cell <?php echo $clientDiffClass; ?>"><strong><?php echo execution_plan_hours_display($clientDiff); ?></strong></td>
							<?php if (!$hideResourceColumn): ?>
							<td class="num-cell"></td>
							<td class="num-cell"></td>
							<?php endif; ?>
						</tr>
						<?php foreach ($clientData['projects'] as $projectRow):
							$scheduleHours = !empty($projectRow->schedule_hours) ? (float)$projectRow->schedule_hours : 0;
							$timesheetHours = !empty($projectRow->timesheet_hours) ? (float)$projectRow->timesheet_hours : 0;
							$invoiceHours = !empty($projectRow->invoice_hours) ? (float)$projectRow->invoice_hours : 0;
							$projectBillingTypeRaw = isset($projectRow->man_days) ? $projectRow->man_days : '';
							$projectBillingMode = execution_plan_billing_mode($projectBillingTypeRaw, isset($selectedBillingType) ? $selectedBillingType : '');
							$projectDiffResult = execution_plan_calculate_difference($projectBillingMode, $scheduleHours, $timesheetHours, $invoiceHours);
							$projectDiff = $projectDiffResult['value'];
							$projectDiffClass = $projectDiffResult['class'];
							$projectName = !empty($projectRow->project_name) ? $projectRow->project_name : 'N/A';
							$projectManagerName = !empty($projectRow->project_manager_name) ? trim($projectRow->project_manager_name) : 'N/A';
							$projectResourceNames = execution_plan_team_members_display(isset($projectRow->team_members) ? $projectRow->team_members : '');
							$assignedTeamCount = isset($projectRow->assigned_team_count) ? (int)$projectRow->assigned_team_count : 0;
							$projectAssignedTeamCount = ($assignedTeamCount > 0) ? '( ' . $assignedTeamCount . ' )' : '';
							$projectSearch = strtolower($projectManagerName . ' ' . $clientName . ' ' . $projectName . ' ' . strip_tags($projectResourceNames) . ' ' . (isset($projectRow->assigned_team_members) ? $projectRow->assigned_team_members : ''));
						?>
						<tr class="client-project-row client-projects-<?php echo $clientIndex; ?>" style="display:none;" data-client-index="<?php echo $clientIndex; ?>" data-search="<?php echo htmlspecialchars($projectSearch, ENT_QUOTES, 'UTF-8'); ?>">
							<td class="pm-cell"><?php echo htmlspecialchars($projectManagerName, ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="project-cell"><span class="project-name-inner"><i class="fa fa-angle-right"></i><span class="project-name-text"><?php echo $projectName; ?></span></span></td>
							<?php $projectStartDate = execution_plan_date_display(isset($projectRow->project_start_date) ? $projectRow->project_start_date : ''); ?>
							<?php $projectEndDate = execution_plan_date_display(isset($projectRow->project_end_date) ? $projectRow->project_end_date : ''); ?>
							<?php $projectTimesheetEntryDate = execution_plan_date_display(isset($projectRow->timesheet_entry_date) ? $projectRow->timesheet_entry_date : ''); ?>
							<td class="date-cell"><?php echo !empty($projectStartDate) ? '<i class="fa fa-calendar"></i> ' . htmlspecialchars($projectStartDate, ENT_QUOTES, 'UTF-8') : ''; ?></td>
							<td class="date-cell"><?php echo !empty($projectEndDate) ? '<i class="fa fa-calendar"></i> ' . htmlspecialchars($projectEndDate, ENT_QUOTES, 'UTF-8') : ''; ?></td>
							<td class="date-cell"><?php echo htmlspecialchars(execution_plan_man_days_display(isset($projectRow->man_days) ? $projectRow->man_days : ''), ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="date-cell"><?php echo !empty($projectTimesheetEntryDate) ? '<i class="fa fa-calendar"></i> ' . htmlspecialchars($projectTimesheetEntryDate, ENT_QUOTES, 'UTF-8') : ''; ?></td>
							<td class="date-cell"><?php echo execution_plan_project_status_badge(isset($projectRow->project_status) ? $projectRow->project_status : ''); ?></td>
							<td class="num-cell"><?php echo execution_plan_estimated_hours_display($scheduleHours); ?></td>
							<td class="num-cell"><?php echo execution_plan_hours_display($timesheetHours); ?></td>
							<td class="num-cell"><?php echo execution_plan_hours_display($invoiceHours); ?></td>
							<td class="num-cell <?php echo $projectDiffClass; ?>"><?php echo execution_plan_hours_display($projectDiff); ?></td>
							<?php if (!$hideResourceColumn): ?>
							<td class="resource-names-cell"><?php echo $projectResourceNames; ?></td>
							<td class="num-cell"><?php echo $projectAssignedTeamCount; ?></td>
							<?php endif; ?>
						</tr>
						<?php endforeach; ?>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>

<style>
	.content-wrapper .card {
		border-radius: 10px;
		border: 1px solid rgba(0,0,0,0.06);
		box-shadow: 0 2px 10px rgba(0,0,0,0.04);
	}

	.execution-plan-summary-title {
		font-weight: 700;
		margin-bottom: 10px;
		color: #3b2f7f;
		font-size: 48px;
		text-align: center;
		letter-spacing: .4px;
		line-height: 1.1;
	}
	.execution-plan-summary-wrap {
		max-width: 680px;
		margin: 10px auto 26px;
		padding: 14px 14px 12px;
		background: #ffffff;
		border: 1px solid #d9e1ec;
		border-radius: 14px;
		box-shadow: 0 4px 14px rgba(30, 52, 82, 0.08);
	}
	.execution-plan-summary-total-projects {
		text-align: right;
		font-size: 16px;
		font-weight: 700;
		color: #1f5076;
		margin: 0 4px 10px 0;
	}
	.execution-plan-summary-table {
		margin-bottom: 0;
		border-radius: 10px;
		overflow: hidden;
	}
	.execution-plan-summary-table thead th {
		background: linear-gradient(to bottom, #2f6689, #255471);
		color: #fff;
		font-weight: 700;
		text-align: center;
		font-size: 18px;
		padding: 12px 8px;
		letter-spacing: .3px;
		border-color: #c9d4e2 !important;
	}
	.execution-plan-summary-table tbody td {
		text-align: center;
		font-size: 18px;
		font-weight: 700;
		background: #eef2f7;
		padding: 12px 10px;
		border-color: #c9d4e2 !important;
	}
	.execution-plan-summary-table tbody td:first-child {
		color: #1f5076;
		background: #e8edf4;
	}
	.execution-plan-summary-table .count-process,
	.execution-plan-summary-table .count-closed,
	.execution-plan-summary-table .count-hold,
	.execution-plan-summary-table .count-total {
		background: #c8dfd3;
	}
	.execution-plan-summary-table .count-process { color: #1e8a3a; }
	.execution-plan-summary-table .count-closed { color: #d32f2f; }
	.execution-plan-summary-table .count-hold { color: #ef6c00; }
	.execution-plan-summary-table .count-total { color: #1f5076; }
	@media (max-width: 992px) {
		.execution-plan-summary-title { font-size: 30px; }
		.execution-plan-summary-table thead th,
		.execution-plan-summary-table tbody td { font-size: 20px; }
	}

	#execution_plan_dept_totals_table {
		min-width: 680px;
		border-collapse: separate;
		border-spacing: 0;
	}
	#execution_plan_dept_totals_table thead th {
		background: linear-gradient(to bottom, #337ab7, #2c5aa0);
		color: #fff;
		font-weight: 700;
		text-transform: uppercase;
		letter-spacing: 0.4px;
		padding: 12px 14px;
	}
	#execution_plan_dept_totals_table tbody td {
		padding: 12px 14px;
		font-weight: 600;
		vertical-align: middle;
	}
	.execution-plan-grand-total td {
		background: #eef3f7;
		border-top: 2px solid #2c5aa0;
	}
	.ep-page-loader {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: rgba(20, 36, 58, 0.62);
		z-index: 9999;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	.ep-page-loader.hidden {
		display: none;
	}
	.ep-page-loader-content {
		background: linear-gradient(180deg, #2c5aa0 0%, #1f447c 100%);
		border: 1px solid #18406f;
		box-shadow: 0 12px 32px rgba(12, 24, 40, 0.35);
		border-radius: 12px;
		padding: 18px 22px;
		color: #fff;
		min-width: 280px;
		display: inline-flex;
		align-items: center;
		gap: 12px;
	}
	.ep-page-loader-spinner {
		width: 38px;
		height: 38px;
		border-radius: 50%;
		background: rgba(255, 255, 255, 0.14);
		display: flex;
		align-items: center;
		justify-content: center;
		flex: 0 0 auto;
	}
	.ep-page-loader-content .fa {
		color: #fff;
		font-size: 18px;
	}
	.ep-page-loader-text {
		display: flex;
		flex-direction: column;
		line-height: 1.25;
	}
	.ep-page-loader-text strong {
		font-size: 16px;
		font-weight: 700;
		letter-spacing: 0.2px;
	}
	.ep-page-loader-text span {
		font-size: 13px;
		font-weight: 500;
		opacity: 0.9;
		margin-top: 2px;
	}

	#execution_plan_table thead th {
		background: linear-gradient(to bottom, #337ab7, #2c5aa0);
		color: #fff;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.3px;
		font-size: 12px;
		text-align: center;
		vertical-align: middle;
		white-space: normal;
		line-height: 1.25;
		padding: 10px 8px;
	}
	#execution_plan_table {
		table-layout: auto;
		width: 100%;
		min-width: 1680px;
		border-collapse: collapse;
	}
	#execution_plan_table tbody td {
		vertical-align: middle;
		padding: 10px 8px;
		overflow: hidden;
	}
	#execution_plan_table .ep-col-pm { min-width: 170px; width: 170px; }
	#execution_plan_table .ep-col-client { min-width: 240px; width: 240px; }
	#execution_plan_table .ep-col-date { min-width: 130px; width: 130px; }
	#execution_plan_table .ep-col-billing { min-width: 110px; width: 110px; }
	#execution_plan_table .ep-col-resources { min-width: 260px; width: 260px; }
	#execution_plan_table .ep-col-team { min-width: 110px; width: 110px; }
	#execution_plan_table .ep-col-status { min-width: 120px; width: 120px; }
	#execution_plan_table .ep-col-hours {
		min-width: 110px;
		width: 110px;
	}

	.pm-cell {
		text-align: center;
		color: #2c5aa0;
		font-weight: 600;
		font-size: 14px;
		white-space: normal;
		word-break: break-word;
		line-height: 1.3;
	}
	.client-cell {
		font-weight: 600;
		color: #2c3e50;
		font-size: 14px;
		white-space: normal;
		word-break: break-word;
		line-height: 1.3;
	}
	.client-name-text { cursor: pointer; }
	.client-toggle-icon { cursor: pointer; margin-left: 8px; color: #337ab7; font-weight: 700; display: inline-block; }
	.client-header-row { background: #f8f9fa; border-left: 4px solid #337ab7; }
	.client-project-row { background: #fff; }
	.project-cell {
		padding-left: 22px !important;
		color: #555;
		font-size: 14px;
		font-weight: 600;
		vertical-align: middle;
	}
	.project-name-inner {
		display: flex;
		align-items: flex-start;
		gap: 6px;
		max-width: 100%;
	}
	.project-name-inner i {
		flex: 0 0 auto;
		line-height: 1.3;
		color: #888;
		margin-top: 2px;
	}
	.project-name-text {
		display: block;
		white-space: normal;
		word-break: break-word;
		line-height: 1.3;
	}
	.date-cell {
		text-align: center;
		font-size: 13px;
		color: #555;
		white-space: nowrap;
	}
	.num-cell {
		text-align: center;
		font-family: "Courier New", monospace;
		font-size: 14px;
		white-space: nowrap;
	}
	.resource-names-cell {
		text-align: left;
		font-size: 13px;
		color: #333;
		white-space: normal;
		word-break: break-word;
		line-height: 1.35;
		font-family: inherit;
	}
	.diff-red { color: #000000; font-weight: 700;  background-color: #d9534f;}
	.diff-green { color: #000000; font-weight: 700; background-color: #5cb85c;}
	.ep-status-badge {
		display: inline-block;
		padding: 6px 12px;
		border-radius: 20px;
		color: #fff;
		font-size: 12px;
		font-weight: 700;
		letter-spacing: .4px;
		text-transform: uppercase;
		white-space: nowrap;
	}
	.ep-status-badge i { margin-right: 5px; }
	.ep-status-badge.status-process { background: #2eb872; }
	.ep-status-badge.status-closed { background: #d9534f; }
	.ep-status-badge.status-active { background: #2eb872; }
	.ep-status-badge.status-inactive { background: #d9534f; }
	.ep-status-badge.status-default { background: #6c757d; }
	#execution_plan_table tbody tr:hover { background-color: #f5f8fa; }

	/* Execution plan filter dropdown selected/highlight colors */
	#execution_plan_search_form + .select2-container .select2-selection--single,
	#execution_plan_search_form .select2-container .select2-selection--single,
	#execution_plan_search_form .select2-container .select2-selection--multiple {
		border-color: #d2d6de;
	}
	#execution_plan_search_form + .select2-container .select2-selection__rendered,
	#execution_plan_search_form .select2-container .select2-selection__rendered {
		background-color: transparent;
		color: #555 !important;
	}
	#execution_plan_search_form .select2-container.ep-selected-bg .select2-selection--single,
	#execution_plan_search_form .select2-container.ep-selected-bg .select2-selection--multiple {
		background-color: #6f42c1 !important;
		border-color: #6f42c1 !important;
	}
	#execution_plan_search_form .select2-container.ep-selected-bg .select2-selection__rendered {
		background-color: transparent !important;
		color: #fff !important;
		border-radius: 3px;
		padding-left: 10px !important;
	}
	#execution_plan_search_form .ep-ym-group .select2-container.ep-selected-bg .select2-selection--single {
		background-color: #6f42c1 !important;
		border-color: #6f42c1 !important;
		box-shadow: 0 1px 3px rgba(111, 66, 193, 0.35);
	}
	#execution_plan_search_form .ep-ym-group .select2-container.ep-selected-bg .select2-selection__rendered {
		color: #fff !important;
		font-weight: 600;
	}
	#execution_plan_search_form .ep-ym-group .select2-container.ep-selected-bg .select2-selection__clear {
		color: #fff !important;
	}
	#execution_plan_search_form .select2-container.ep-selected-bg .select2-selection__arrow b {
		border-top-color: #fff !important;
	}
	.select2-container--default .select2-results__option--highlighted[aria-selected],
	.select2-container--default .select2-results__option[aria-selected="true"] {
		background-color: #6f42c1 !important;
		color: #fff !important;
	}
	#execution_plan_search_form .select2-container--default .select2-selection--multiple .select2-selection__choice {
		background-color: #6f42c1;
		border-color: #6f42c1;
		color: #fff;
	}

	/* Year/month row visual polish */
	#execution_plan_search_form .ep-ym-row {
		margin-top: 4px;
		padding-top: 10px;
		border-top: 1px solid #eceff3;
	}
	#execution_plan_search_form .ep-ym-group {
		background: #fafbfc;
		border: 1px solid #e6eaef;
		border-radius: 8px;
		padding: 10px 12px 8px;
	}
	#execution_plan_search_form .ep-ym-group .control-label {
		display: block;
		margin-bottom: 6px;
		font-weight: 700;
		color: #2c3e50;
	}
	#execution_plan_search_form .ep-ym-group .select2-container .select2-selection--single,
	#execution_plan_search_form .ep-ym-group .select2-container .select2-selection--multiple {
		min-height: 36px;
		border-radius: 6px;
	}
	#execution_plan_search_form .ep-ym-group .select2-container .select2-selection__rendered {
		line-height: 34px !important;
	}
	#execution_plan_search_form .ep-ym-group .select2-container .select2-selection__arrow {
		height: 34px;
	}
	.col-md-6 {
        width: 25% !important;
    }
	.ep-status-buttons-wrap {
		display: inline-flex;
		margin-left: 10px;
		gap: 10px;
		flex-wrap: wrap;
		vertical-align: middle;
		align-items: center;
	}
	.ep-billing-tabs-wrap {
		display: inline-flex;
		gap: 6px;
		margin: 0;
		vertical-align: middle;
		align-items: center;
		padding: 4px;
		background: #f4f6f8;
		border: 1px solid #d5dbe3;
		border-radius: 10px;
		box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.95), 0 1px 2px rgba(0, 0, 0, 0.06);
	}
	.ep-billing-tab {
		background: #ffffff;
		border: 1px solid #d0d7e2;
		color: #3d566e;
		font-weight: 700;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		font-size: 12px;
		padding: 9px 16px;
		border-radius: 8px;
		min-width: 94px;
		text-align: center;
		transition: all .2s ease;
		box-shadow: none;
		cursor: pointer;
	}
	.ep-billing-tab:hover {
		color: #2a4055;
		background: #f8fafc;
		border-color: #b8c4d4;
		transform: translateY(-1px);
		box-shadow: 0 2px 6px rgba(0, 0, 0, 0.10);
	}
	.ep-billing-tab.active {
		color: #fff !important;
		border-color: transparent;
		box-shadow: 0 3px 8px rgba(0, 0, 0, 0.18);
	}
	.ep-billing-tab[data-billing-type="hourly"].active {
		background: linear-gradient(180deg, #2f8fd9 0%, #1f7ac0 100%);
		border-color: #1a6fad;
	}
	.ep-billing-tab[data-billing-type="hourly"]:hover:not(.active) {
		color: #1f7ac0;
		background: #eef6fc;
		border-color: #9ec9ea;
	}
	.ep-billing-tab[data-billing-type="monthly"].active {
		background: linear-gradient(180deg, #9b59b6 0%, #8e44ad 100%);
		border-color: #7d3c98;
	}
	.ep-billing-tab[data-billing-type="monthly"]:hover:not(.active) {
		color: #8e44ad;
		background: #f7f0fa;
		border-color: #d7b8e5;
	}
	.ep-billing-tab:focus,
	.ep-billing-tab:active {
		outline: none;
		text-decoration: none;
	}
	.ep-filter-refresh-btn {
		font-weight: 700;
		font-size: 13px;
		letter-spacing: .4px;
		text-transform: uppercase;
		border-radius: 8px;
		padding: 8px 16px;
		min-width: 110px;
		box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
	}
	.ep-filter-refresh-btn:hover,
	.ep-filter-refresh-btn:focus {
		color: #fff;
		text-decoration: none;
		box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18);
	}
	.ep-status-btn {
		font-weight: 700;
		font-size: 13px;
		letter-spacing: .6px;
		text-transform: uppercase;
		border-radius: 8px;
		padding: 8px 16px;
		min-width: 100px;
		transition: all .2s ease-in-out;
		box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
		color: #fff;
	}
	.ep-status-all {
		background: #4b5d73;
		border: 1px solid #3f4f63;
	}
	.ep-status-process {
		background: #44b549;
		border: 1px solid #38963d;
	}
	.ep-status-hold {
		background: #f39c12;
		border: 1px solid #d7860a;
	}
	.ep-status-closed {
		background: #f44336;
		border: 1px solid #da3125;
	}
	.ep-status-btn:hover {
		color: #fff;
		transform: translateY(-1px);
		opacity: 0.95;
		box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18);
	}
	.ep-status-btn.active {
		filter: saturate(1.2) brightness(0.95);
		box-shadow: 0 5px 14px rgba(0, 0, 0, 0.28), 0 0 0 3px rgba(255, 255, 255, 0.9), 0 0 0 5px rgba(51, 122, 183, 0.35);
		transform: translateY(-1px);
	}
	.ep-status-btn:focus,
	.ep-status-btn:active {
		outline: none;
	}
	.ep-year-btn {
		background: #1f7ac0;
		border: 1px solid #1769a6;
		color: #fff;
		font-weight: 700;
		font-size: 13px;
		letter-spacing: .4px;
		text-transform: uppercase;
		border-radius: 8px;
		padding: 8px 16px;
		min-width: 125px;
		transition: all .2s ease-in-out;
		box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
	}
	.ep-year-btn:hover {
		color: #fff;
		transform: translateY(-1px);
		opacity: 0.95;
		box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18);
	}
	.ep-year-btn.active {
		filter: saturate(1.2) brightness(0.95);
		box-shadow: 0 5px 14px rgba(0, 0, 0, 0.28), 0 0 0 3px rgba(255, 255, 255, 0.9), 0 0 0 5px rgba(31, 122, 192, 0.35);
		transform: translateY(-1px);
	}
	.ep-year-btn:focus,
	.ep-year-btn:active {
		outline: none;
	}
	@media (max-width: 768px) {
		.ep-billing-tabs-wrap {
			display: inline-flex;
			margin: 0;
			width: auto;
			justify-content: flex-start;
		}
		.ep-billing-tab {
			min-width: 0;
			flex: 1 1 auto;
		}
		.ep-status-buttons-wrap {
			margin-left: 0;
			margin-top: 10px;
		}
		.ep-status-btn {
			min-width: 90px;
			padding: 7px 12px;
			font-size: 12px;
		}
		.ep-year-btn {
			min-width: 110px;
			padding: 7px 12px;
			font-size: 12px;
		}
	}
</style>

<script type="text/javascript">
$(document).ready(function() {
	var clientsByDepartmentUrl = "<?php echo base_url('execution_plan/get_clients_by_departments'); ?>";
	var projectsByClientUrl = "<?php echo base_url('execution_plan/get_projects_by_clients'); ?>";
	var epSearchUrl = "<?php echo base_url('execution_plan'); ?>";
	var epExportUrl = "<?php echo base_url('execution_plan/export_report'); ?>";
	var epCurrentYear = String(<?php echo (int)date('Y'); ?>);
	var $epPageLoader = $("#ep_page_loader");
	var epLoaderCounter = 1;
	var epExportInProgress = false;
	var epExportHideTimer = null;

	function showEpPageLoader() {
		epLoaderCounter++;
		$epPageLoader.removeClass("hidden");
	}

	function hideEpPageLoader() {
		epLoaderCounter = Math.max(0, epLoaderCounter - 1);
		if (epLoaderCounter === 0) {
			$epPageLoader.addClass("hidden");
		}
	}

	function forceHideEpPageLoader() {
		epLoaderCounter = 0;
		epExportInProgress = false;
		if (epExportHideTimer) {
			clearTimeout(epExportHideTimer);
			epExportHideTimer = null;
		}
		$epPageLoader.addClass("hidden");
	}

	function updateSelectedBg($select) {
		var value = $select.val();
		var $container = $select.next(".select2-container");
		var fieldId = $select.attr("id") || "";
		var hasValue = false;
		if ($.isArray(value)) {
			hasValue = value.length > 0;
		} else if ($.inArray(fieldId, ["from_year", "to_year", "from_month", "to_month"]) !== -1) {
			hasValue = (value !== null && value !== undefined && value !== "");
		} else {
			hasValue = !!value && value !== "all";
		}
		if (hasValue) {
			$container.addClass("ep-selected-bg");
		} else {
			$container.removeClass("ep-selected-bg");
		}
	}

	var epSyncingDates = false;

	function epNormalizeYearMonthValue(val) {
		val = String(val === null || val === undefined ? "all" : val);
		return (val === "" ? "all" : val);
	}

	function epYearMonthKey(yearVal, monthVal, boundary) {
		yearVal = epNormalizeYearMonthValue(yearVal);
		monthVal = epNormalizeYearMonthValue(monthVal);
		if (yearVal === "all") {
			return null;
		}
		var year = parseInt(yearVal, 10);
		if (isNaN(year) || year <= 0) {
			return null;
		}
		if (monthVal === "all") {
			return boundary === "from" ? (year * 100 + 1) : (year * 100 + 12);
		}
		var month = parseInt(monthVal, 10);
		if (isNaN(month) || month < 1 || month > 12) {
			return boundary === "from" ? (year * 100 + 1) : (year * 100 + 12);
		}
		return year * 100 + month;
	}

	function epUpdateDateRangeConstraints() {
		var fromYear = epNormalizeYearMonthValue($("#from_year").val());
		var fromMonth = epNormalizeYearMonthValue($("#from_month").val());
		var toYear = epNormalizeYearMonthValue($("#to_year").val());
		var toMonth = epNormalizeYearMonthValue($("#to_month").val());
		var fromYearNum = (fromYear === "all") ? null : parseInt(fromYear, 10);
		var toYearNum = (toYear === "all") ? null : parseInt(toYear, 10);
		var fromMonthNum = (fromMonth === "all") ? null : parseInt(fromMonth, 10);

		$("#to_year option").each(function() {
			var val = epNormalizeYearMonthValue($(this).val());
			if (val === "all" || fromYearNum === null) {
				$(this).prop("disabled", false);
				return;
			}
			$(this).prop("disabled", parseInt(val, 10) < fromYearNum);
		});

		$("#from_year option").each(function() {
			var val = epNormalizeYearMonthValue($(this).val());
			if (val === "all" || toYearNum === null) {
				$(this).prop("disabled", false);
				return;
			}
			$(this).prop("disabled", parseInt(val, 10) > toYearNum);
		});

		$("#to_month option").each(function() {
			var val = epNormalizeYearMonthValue($(this).val());
			if (val === "all" || fromYearNum === null || toYearNum === null || fromYear !== toYear || fromMonthNum === null) {
				$(this).prop("disabled", false);
				return;
			}
			$(this).prop("disabled", parseInt(val, 10) < fromMonthNum);
		});

		$("#from_month option").each(function() {
			var val = epNormalizeYearMonthValue($(this).val());
			if (val === "all" || fromYearNum === null || toYearNum === null || fromYear !== toYear || toMonth === "all") {
				$(this).prop("disabled", false);
				return;
			}
			var toMonthNum = parseInt(toMonth, 10);
			$(this).prop("disabled", parseInt(val, 10) > toMonthNum);
		});
	}

	function enforceExecutionPlanDateRange(changedId) {
		if (epSyncingDates) {
			return;
		}

		var fromYear = epNormalizeYearMonthValue($("#from_year").val());
		var fromMonth = epNormalizeYearMonthValue($("#from_month").val());
		var toYear = epNormalizeYearMonthValue($("#to_year").val());
		var toMonth = epNormalizeYearMonthValue($("#to_month").val());

		if (fromYear === "all" || toYear === "all") {
			epUpdateDateRangeConstraints();
			return;
		}

		var fromKey = epYearMonthKey(fromYear, fromMonth, "from");
		var toKey = epYearMonthKey(toYear, toMonth, "to");
		if (fromKey === null || toKey === null || fromKey <= toKey) {
			epUpdateDateRangeConstraints();
			return;
		}

		epSyncingDates = true;
		if (changedId === "from_year" || changedId === "from_month" || changedId === "") {
			$("#to_year").val(fromYear).trigger("change");
			$("#to_month").val(fromMonth).trigger("change");
		} else {
			$("#from_year").val(toYear).trigger("change");
			$("#from_month").val(toMonth).trigger("change");
		}
		epSyncingDates = false;
		epUpdateDateRangeConstraints();
		updateSelectedBg($("#from_year"));
		updateSelectedBg($("#from_month"));
		updateSelectedBg($("#to_year"));
		updateSelectedBg($("#to_month"));
	}

	if ($.fn.select2) {
		var $filters = $("#department, #client_Id, #project_Id, #project_manager, #from_year, #from_month, #to_year, #to_month");
		$filters.select2({
			width: "100%",
			placeholder: "Select options",
			allowClear: true
		});
		$filters.each(function() {
			updateSelectedBg($(this));
		});
		$filters.on("change", function() {
			if (!epSyncingDates && $.inArray(this.id, ["from_year", "to_year", "from_month", "to_month"]) !== -1) {
				$("#ep_default_year").val("");
				enforceExecutionPlanDateRange(this.id);
			}
			updateSelectedBg($(this));
		});
		enforceExecutionPlanDateRange("");
	}

	function setSelectOptions($select, items, valueKey, textKey) {
		var previousValues = $select.val() || [];
		var previousLookup = {};
		$.each(previousValues, function(_, item) {
			previousLookup[String(item)] = true;
		});

		$select.empty();
		$.each(items, function(_, item) {
			var optionValue = String(item[valueKey]);
			var optionText = item[textKey];
			var option = new Option(optionText, optionValue, false, !!previousLookup[optionValue]);
			$select.append(option);
		});
		$select.trigger("change");
	}

	function loadClientsByDepartment(loadProjectsAfter) {
		var departments = $("#department").val() || [];
		showEpPageLoader();
		return $.ajax({
			url: clientsByDepartmentUrl,
			type: "POST",
			dataType: "json",
			data: { department: departments }
		}).done(function(response) {
			var clients = response && response.clients ? response.clients : [];
			setSelectOptions($("#client_Id"), clients, "client_Id", "client_name");
			if (loadProjectsAfter) {
				loadProjectsByClient();
			}
		}).always(function() {
			hideEpPageLoader();
		});
	}

	function loadProjectsByClient() {
		var departments = $("#department").val() || [];
		var clientIds = $("#client_Id").val() || [];
		showEpPageLoader();
		return $.ajax({
			url: projectsByClientUrl,
			type: "POST",
			dataType: "json",
			data: {
				department: departments,
				client_Id: clientIds
			}
		}).done(function(response) {
			var projects = response && response.projects ? response.projects : [];
			setSelectOptions($("#project_Id"), projects, "project_Id", "project_name");
		}).always(function() {
			hideEpPageLoader();
		});
	}

	$("#department").on("change", function() {
		loadClientsByDepartment(true);
	});

	$("#client_Id").on("change", function() {
		loadProjectsByClient();
	});

	if (($("#department").val() || []).length > 0 || ($("#client_Id").val() || []).length > 0) {
		loadClientsByDepartment(true);
	}

	$(".page-title a.btn.btn-primary.btn-flat").on("click", function() {
		showEpPageLoader();
	});

	$("#ep_current_year_btn").on("click", function() {
		var currentYear = String($(this).data("year") || "");
		if (!currentYear) {
			return;
		}
		$("#from_year").val(currentYear).trigger("change");
		$("#to_year").val(currentYear).trigger("change");
		$("#from_month").val("all").trigger("change");
		$("#to_month").val("all").trigger("change");
		$("#execution_plan_search_form").trigger("submit");
	});

	function toggleClientProjects(clientIndex) {
		var projectRows = $(".client-projects-" + clientIndex);
		var icon = $('.client-toggle-icon[data-client-index="' + clientIndex + '"] i');
		if (projectRows.is(":visible")) {
			projectRows.hide();
			icon.removeClass("fa-minus").addClass("fa-plus");
		} else {
			projectRows.show();
			icon.removeClass("fa-plus").addClass("fa-minus");
		}
	}

	$(".client-toggle-icon").on("click", function(e) {
		e.preventDefault();
		e.stopPropagation();
		toggleClientProjects($(this).data("client-index"));
	});

	$(".client-name-text").on("click", function(e) {
		e.preventDefault();
		e.stopPropagation();
		var idx = $(this).closest(".client-header-row").data("client-index");
		toggleClientProjects(idx);
	});

	$(".ep-billing-tab").on("click", function() {
		var billingType = String($(this).data("billing-type") || "").toLowerCase();
		if (!billingType) {
			return;
		}
		resetExecutionPlanFilters({
			billingType: billingType,
			status: "",
			dateMode: "currentYear"
		});
	});

	$("#ep_show_all_btn").on("click", function() {
		resetExecutionPlanFilters({
			billingType: "",
			status: "",
			dateMode: "all"
		});
	});

	function applyExecutionPlanDateDefaults(dateMode) {
		if (dateMode === "preserve") {
			return;
		}
		epSyncingDates = true;
		if (dateMode === "currentYear") {
			$("#ep_default_year").val("1");
			$("#from_year").val(epCurrentYear).trigger("change");
			$("#to_year").val(epCurrentYear).trigger("change");
			$("#from_month, #to_month").val("all").trigger("change");
			updateSelectedBg($("#from_year"));
			updateSelectedBg($("#to_year"));
			updateSelectedBg($("#from_month"));
			updateSelectedBg($("#to_month"));
			epSyncingDates = false;
			enforceExecutionPlanDateRange("");
			return;
		}
		$("#ep_default_year").val("");
		$("#from_year, #from_month, #to_year, #to_month").val("all").trigger("change");
		updateSelectedBg($("#from_year"));
		updateSelectedBg($("#to_year"));
		updateSelectedBg($("#from_month"));
		updateSelectedBg($("#to_month"));
		epSyncingDates = false;
		enforceExecutionPlanDateRange("");
	}

	function resetExecutionPlanFilters(options) {
		options = options || {};
		var billingType = options.billingType || "";
		var status = options.status !== undefined ? options.status : "";
		var dateMode = options.dateMode || "preserve";

		$("#department, #client_Id, #project_Id, #project_manager").val(null).trigger("change");
		applyExecutionPlanDateDefaults(dateMode);
		$("#project_status").val(status);
		$("#man_days").val(billingType);

		$(".ep-status-btn").removeClass("active");
		if (status) {
			var statusKey = String(status).toLowerCase();
			if (statusKey === "process" || statusKey === "in process" || statusKey === "in_process") {
				$('.ep-status-btn[data-status="Process"]').addClass("active");
			} else if (statusKey === "closed") {
				$('.ep-status-btn[data-status="Closed"]').addClass("active");
			} else if (statusKey === "on hold" || statusKey === "on_hold") {
				$('.ep-status-btn[data-status="On Hold"]').addClass("active");
			}
		}

		$(".ep-billing-tab").removeClass("active");
		if (billingType === "hourly") {
			$('.ep-billing-tab[data-billing-type="hourly"]').addClass("active");
		} else if (billingType === "monthly") {
			$('.ep-billing-tab[data-billing-type="monthly"]').addClass("active");
		}

		$("#execution_plan_search_form").trigger("submit");
	}

	$(".ep-status-btn").on("click", function() {
		var status = $(this).data("status");
		$("#ep_default_year").val("");
		$("#project_status").val(status);
		$(".ep-status-btn").removeClass("active");
		$(this).addClass("active");
		$("#execution_plan_search_form").trigger("submit");
	});

	$("#execution_plan_search_form button[type='submit']").on("click", function() {
		$("#ep_default_year").val("");
	});

	$("#ep_export_report_btn").on("click", function() {
		epExportInProgress = true;
		showEpPageLoader();
		var $form = $("#execution_plan_search_form");
		enforceExecutionPlanDateRange("");
		["from_year", "to_year", "from_month", "to_month"].forEach(function(fieldId) {
			var $field = $("#" + fieldId);
			var value = $field.val();
			if (value === null || value === undefined || value === "") {
				$field.val("all");
			}
			updateSelectedBg($field);
		});
		var originalAction = $form.attr("action");
		var originalTarget = $form.attr("target");
		$form.attr("action", epExportUrl);
		$form.attr("target", "ep_export_iframe");
		$form.trigger("submit");
		$form.attr("action", originalAction);
		if (originalTarget) {
			$form.attr("target", originalTarget);
		} else {
			$form.removeAttr("target");
		}
		if (epExportHideTimer) {
			clearTimeout(epExportHideTimer);
		}
		epExportHideTimer = setTimeout(function() {
			forceHideEpPageLoader();
		}, 2500);
	});

	$("#ep_export_iframe").on("load", function() {
		if (epExportInProgress) {
			forceHideEpPageLoader();
		}
	});

	$("#execution_plan_search_form").on("submit", function() {
		if (!epExportInProgress) {
			showEpPageLoader();
		}
		enforceExecutionPlanDateRange("");
		["from_year", "to_year", "from_month", "to_month"].forEach(function(fieldId) {
			var $field = $("#" + fieldId);
			var value = $field.val();
			if (value === null || value === undefined || value === "") {
				$field.val("all");
			}
			updateSelectedBg($field);
		});
	});

	function syncActiveStatusButton() {
		var currentStatus = $.trim($("#project_status").val() || "").toLowerCase();
		$(".ep-status-btn").removeClass("active");
		if (currentStatus === "" || currentStatus === "all") {
			return;
		}
		if (currentStatus === "process" || currentStatus === "in process" || currentStatus === "in_process") {
			$('.ep-status-btn[data-status="Process"]').addClass("active");
			return;
		}
		if (currentStatus === "closed") {
			$('.ep-status-btn[data-status="Closed"]').addClass("active");
			return;
		}
		if (currentStatus === "on hold" || currentStatus === "on_hold") {
			$('.ep-status-btn[data-status="On Hold"]').addClass("active");
			return;
		}
	}

	syncActiveStatusButton();
	hideEpPageLoader();
});
</script>

<?php $this->load->view('includes/cRMFooter'); ?>
