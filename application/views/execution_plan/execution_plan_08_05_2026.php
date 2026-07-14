<?php $this->load->view('includes/cRMHeader'); ?>
<div class="content-wrapper">
	<div class="page-title">
		<div>
			<h1><i class="fa fa-paper-plane"></i> Execution Plan</h1>
		</div>
		<div>
			<?php
				$selectedBillingType = isset($selected_man_days_type) ? strtolower(trim((string)$selected_man_days_type)) : '';
				if (!in_array($selectedBillingType, array('hourly', 'monthly', 'all'), true)) {
					$selectedBillingType = !empty($man_days) ? strtolower(trim((string)reset($man_days))) : 'hourly';
				}
				if (!in_array($selectedBillingType, array('hourly', 'monthly', 'all'), true)) {
					$selectedBillingType = 'all';
				}
				$baseFilterQuery = array(
					'department' => (array)$department,
					'client_Id' => (array)$client_Id,
					'project_Id' => (array)$project_Id,
					'project_manager' => (array)$project_manager,
					'project_status' => (array)$project_status,
					'from_year' => !empty($from_year) ? reset($from_year) : '',
					'from_month' => !empty($from_month) ? reset($from_month) : '',
					'to_year' => !empty($to_year) ? reset($to_year) : '',
					'to_month' => !empty($to_month) ? reset($to_month) : ''
				);
				$hourlyTabQuery = http_build_query(array_merge($baseFilterQuery, array('man_days' => 'hourly')));
				$monthlyTabQuery = http_build_query(array_merge($baseFilterQuery, array('man_days' => 'monthly')));
				$allTabQuery = http_build_query(array_merge($baseFilterQuery, array('man_days' => 'all')));
			?>
			<div class="ep-billing-tabs-wrap">
				<a class="btn ep-billing-tab <?php echo $selectedBillingType === 'hourly' ? 'active' : ''; ?>" href="<?php echo base_url('execution_plan') . (!empty($hourlyTabQuery) ? ('?' . $hourlyTabQuery) : ''); ?>">Hourly</a>
				<a class="btn ep-billing-tab <?php echo $selectedBillingType === 'monthly' ? 'active' : ''; ?>" href="<?php echo base_url('execution_plan') . (!empty($monthlyTabQuery) ? ('?' . $monthlyTabQuery) : ''); ?>">Monthly</a>
				<a class="btn ep-billing-tab <?php echo $selectedBillingType === 'all' ? 'active' : ''; ?>" href="<?php echo base_url('execution_plan') . (!empty($allTabQuery) ? ('?' . $allTabQuery) : ''); ?>">All</a>
			</div>
			<a class="btn btn-primary btn-flat" href="<?php echo base_url('execution_plan'); ?>"><i class="fa fa-refresh"></i> Refresh</a>
			<?php
				$exportQuery = http_build_query(array(
					'department' => (array)$department,
					'client_Id' => (array)$client_Id,
					'project_Id' => (array)$project_Id,
					'project_manager' => (array)$project_manager,
					'man_days' => $selectedBillingType,
					'project_status' => (array)$project_status,
					'from_year' => !empty($from_year) ? reset($from_year) : '',
					'from_month' => !empty($from_month) ? reset($from_month) : '',
					'to_year' => !empty($to_year) ? reset($to_year) : '',
					'to_month' => !empty($to_month) ? reset($to_month) : ''
				));
			?>
			<a class="btn btn-success btn-flat" href="<?php echo base_url('execution_plan/export_report') . (!empty($exportQuery) ? ('?' . $exportQuery) : ''); ?>">
				<i class="fa fa-download"></i> Export Report
			</a>
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
											if ($deptKey === '' || $deptKey === 'other services') { continue; }
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
										<?php echo htmlspecialchars($client->client_name, ENT_QUOTES, 'UTF-8'); ?>
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
						<button type="button" class="btn ep-status-btn ep-status-all <?php echo ($selectedStatusValue === 'all') ? 'active' : ''; ?>" data-status="all">
							All
						</button>
						<button type="button" class="btn ep-status-btn ep-status-process <?php echo ($selectedStatusValue === 'process' || $selectedStatusValue === 'in process' || $selectedStatusValue === 'in_process') ? 'active' : ''; ?>" data-status="Process">
							In Process
						</button>
						<button type="button" class="btn ep-status-btn ep-status-closed <?php echo ($selectedStatusValue === 'closed') ? 'active' : ''; ?>" data-status="Closed">
							Closed
						</button>
						<button type="button" class="btn ep-status-btn ep-status-hold <?php echo ($selectedStatusValue === 'on hold' || $selectedStatusValue === 'on_hold') ? 'active' : ''; ?>" data-status="On Hold">
							On Hold
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
			if (!function_exists('execution_plan_team_members_count')) {
				function execution_plan_team_members_count($value) {
					$value = trim((string)$value);
					if ($value === '') {
						return 0;
					}
					$normalized = str_replace(array("\r\n", "\n", "\r", ';', '|'), ',', $value);
					$parts = array_filter(array_map('trim', explode(',', $normalized)), function($item) {
						return $item !== '';
					});
					return count($parts);
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
				$differenceColumnLabel = 'Difference';
				if ($isHourlyBilling) {
					$differenceColumnLabel = 'PEST vs TS DIFFERENCE';
				} elseif ($isMonthlyBilling) {
					$differenceColumnLabel = 'INV vs TS DIFFERENCE';
				}
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
								<td>Closed</td>
								<td class="count-closed"><?php echo (int)$projectStatusSummary['closed']; ?></td>
							</tr>
							<tr>
								<td>On Hold</td>
								<td class="count-hold"><?php echo (int)$projectStatusSummary['on_hold']; ?></td>
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
							<th style="text-align:center;">Project Manager</th>
							<th>Client Name / Projects</th>
							<th style="text-align:center;">Start Date</th>
							<th style="text-align:center;">End Date</th>
							<th style="text-align:center; width: 8% !important;">Billing Type</th>
							<?php if (!$hideResourceColumn): ?>
							<th style="text-align:center; width: 6% !important;">Number Of Resources</th>
							<?php endif; ?>
							<th style="text-align:center; width: 10% !important;">Project Status</th>
							<th style="text-align:center; width: 6% !important;" class="ep-col-hours">Project Estimated Hours</th>
							<th style="text-align:center; width: 6% !important;" class="ep-col-hours">Timesheet Hours</th>
							<th style="text-align:center; width: 6% !important;" class="ep-col-hours">Invoice Hours</th>
							<th style="text-align:center; width: 6% !important; text-transform:none !important;" class="ep-col-hours"><?php echo htmlspecialchars($differenceColumnLabel, ENT_QUOTES, 'UTF-8'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$byClient = array();
						foreach ($allClientResult as $row) {
							$clientName = !empty($row->client_name) ? trim($row->client_name) : 'N/A';
							if (!isset($byClient[$clientName])) {
								$byClient[$clientName] = array('projects' => array());
							}
							$byClient[$clientName]['projects'][] = $row;
						}
						ksort($byClient, SORT_STRING);
						$clientIndex = 0;
						foreach ($byClient as $clientName => $clientData):
							$clientIndex++;
								$clientScheduleTotal = 0;
								$clientTimesheetTotal = 0;
								$clientInvoiceTotal = 0;
								$clientBillingTypes = array();
								$clientResourceTotal = 0;
								$clientProjectStatus = '';
								$clientStartDateTs = null;
								$clientEndDateTs = null;
								$clientManagerDisplay = 'N/A';
								foreach ($clientData['projects'] as $projectRow) {
									$clientScheduleTotal += !empty($projectRow->schedule_hours) ? (float)$projectRow->schedule_hours : 0;
									$clientTimesheetTotal += !empty($projectRow->timesheet_hours) ? (float)$projectRow->timesheet_hours : 0;
									$clientInvoiceTotal += !empty($projectRow->invoice_hours) ? (float)$projectRow->invoice_hours : 0;
									$clientResourceTotal += execution_plan_team_members_count(isset($projectRow->team_members) ? $projectRow->team_members : '');
									$projectManagerName = !empty($projectRow->project_manager_name) ? trim($projectRow->project_manager_name) : 'N/A';
									if ($clientManagerDisplay === 'N/A' && $projectManagerName !== '') {
										$clientManagerDisplay = $projectManagerName;
									}
									if ($clientProjectStatus === '' && !empty($projectRow->project_status)) {
										$clientProjectStatus = $projectRow->project_status;
									}
									if (!empty($projectRow->project_start_date) && $projectRow->project_start_date !== '0000-00-00') {
										$startTs = strtotime($projectRow->project_start_date);
										if ($startTs !== false && ($clientStartDateTs === null || $startTs < $clientStartDateTs)) {
											$clientStartDateTs = $startTs;
										}
									}
									if (!empty($projectRow->project_end_date) && $projectRow->project_end_date !== '0000-00-00') {
										$endTs = strtotime($projectRow->project_end_date);
										if ($endTs !== false && ($clientEndDateTs === null || $endTs > $clientEndDateTs)) {
											$clientEndDateTs = $endTs;
										}
									}
									$billingType = execution_plan_man_days_display(isset($projectRow->man_days) ? $projectRow->man_days : '');
									if ($billingType !== '') {
										$clientBillingTypes[strtolower($billingType)] = $billingType;
									}
								}
								if (count($clientBillingTypes) === 1) {
									$clientBillingTypeDisplay = reset($clientBillingTypes);
								} elseif (count($clientBillingTypes) > 1) {
									$clientBillingTypeDisplay = 'Multiple';
								} else {
									$clientBillingTypeDisplay = '';
								}
								if ($isHourlyBilling) {
									//$clientDiff = $clientTimesheetTotal - $clientScheduleTotal;


									$clientDiff = $clientScheduleTotal - $clientTimesheetTotal;


								} else {
									$clientDiff = $clientInvoiceTotal - $clientTimesheetTotal;
								}
								$clientStartDate = $clientStartDateTs !== null ? date('d-M-Y', $clientStartDateTs) : '';
								$clientEndDate = $clientEndDateTs !== null ? date('d-M-Y', $clientEndDateTs) : '';
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
							<?php if (!$hideResourceColumn): ?>
							<td class="num-cell"></td>
							<?php endif; ?>
							<td class="date-cell"><?php echo execution_plan_project_status_badge($clientProjectStatus); ?></td>
							<td class="num-cell"><strong><?php echo execution_plan_hours_display($clientScheduleTotal); ?></strong></td>
							<td class="num-cell"><strong><?php echo execution_plan_hours_display($clientTimesheetTotal); ?></strong></td>
							<td class="num-cell"><strong><?php echo execution_plan_hours_display($clientInvoiceTotal); ?></strong></td>
							<td class="num-cell diff-cell <?php echo $clientDiff < 0 ? 'diff-red' : 'diff-green'; ?>"><strong><?php echo execution_plan_hours_display($clientDiff); ?></strong></td>
						</tr>
						<?php foreach ($clientData['projects'] as $projectRow):
							$scheduleHours = !empty($projectRow->schedule_hours) ? (float)$projectRow->schedule_hours : 0;
							$timesheetHours = !empty($projectRow->timesheet_hours) ? (float)$projectRow->timesheet_hours : 0;
							$invoiceHours = !empty($projectRow->invoice_hours) ? (float)$projectRow->invoice_hours : 0;
							if ($isHourlyBilling) {
								$projectDiff = $timesheetHours - $scheduleHours;
							} else {
								$projectDiff = $invoiceHours - $timesheetHours;
							}
							$projectName = !empty($projectRow->project_name) ? $projectRow->project_name : 'N/A';
							$projectManagerName = !empty($projectRow->project_manager_name) ? trim($projectRow->project_manager_name) : 'N/A';
							$projectSearch = strtolower($projectManagerName . ' ' . $clientName . ' ' . $projectName);
						?>
						<tr class="client-project-row client-projects-<?php echo $clientIndex; ?>" style="display:none;" data-client-index="<?php echo $clientIndex; ?>" data-search="<?php echo htmlspecialchars($projectSearch, ENT_QUOTES, 'UTF-8'); ?>">
							<td class="pm-cell"><?php echo htmlspecialchars($projectManagerName, ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="project-cell" style="font-weight: 600;"><i class="fa fa-angle-right"></i> <?php echo $projectName; ?></td>
							<?php $projectStartDate = execution_plan_date_display(isset($projectRow->project_start_date) ? $projectRow->project_start_date : ''); ?>
							<?php $projectEndDate = execution_plan_date_display(isset($projectRow->project_end_date) ? $projectRow->project_end_date : ''); ?>
							<?php $projectResourceCount = execution_plan_team_members_count(isset($projectRow->team_members) ? $projectRow->team_members : ''); ?>
							<td class="date-cell"><?php echo !empty($projectStartDate) ? '<i class="fa fa-calendar"></i> ' . htmlspecialchars($projectStartDate, ENT_QUOTES, 'UTF-8') : ''; ?></td>
							<td class="date-cell"><?php echo !empty($projectEndDate) ? '<i class="fa fa-calendar"></i> ' . htmlspecialchars($projectEndDate, ENT_QUOTES, 'UTF-8') : ''; ?></td>
							<td class="date-cell"><?php echo htmlspecialchars(execution_plan_man_days_display(isset($projectRow->man_days) ? $projectRow->man_days : ''), ENT_QUOTES, 'UTF-8'); ?></td>
							<?php if (!$hideResourceColumn): ?>
							<td class="num-cell"><?php echo (int)$projectResourceCount; ?></td>
							<?php endif; ?>
							<td class="date-cell"><?php echo execution_plan_project_status_badge(isset($projectRow->project_status) ? $projectRow->project_status : ''); ?></td>
							<td class="num-cell"><?php echo execution_plan_hours_display($scheduleHours); ?></td>
							<td class="num-cell"><?php echo execution_plan_hours_display($timesheetHours); ?></td>
							<td class="num-cell"><?php echo execution_plan_hours_display($invoiceHours); ?></td>
							<td class="num-cell <?php echo $projectDiff != 0 ? 'diff-red' : 'diff-green'; ?>"><?php echo execution_plan_hours_display($projectDiff); ?></td>
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

	#execution_plan_table thead th {
		background: linear-gradient(to bottom, #337ab7, #2c5aa0);
		color: #fff;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.4px;
		font-size: 14px;
	}
	#execution_plan_table {
		table-layout: fixed;
	}
	#execution_plan_table .ep-col-hours {
		width: 120px;
		max-width: 120px;
	}

	.pm-cell { text-align: center; color: #2c5aa0; font-weight: 600; font-size: 16px; }
	.client-cell { font-weight: 600; color: #2c3e50; font-size: 16px; }
	.client-name-text { cursor: pointer; }
	.client-toggle-icon { cursor: pointer; margin-left: 10px; color: #337ab7; font-weight: 700; }
	.client-header-row { background: #f8f9fa; border-left: 4px solid #337ab7; }
	.client-project-row { background: #fff; }
	.project-cell { padding-left: 50px !important; color: #666; font-size: 16px; }
	.date-cell { text-align: center; font-size: 16px; color: #555; white-space: nowrap; }
	.num-cell { text-align: center; font-family: "Courier New", monospace; font-size: 16px; }
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
	.ep-status-badge.status-default { background: #6c757d; }
	#execution_plan_table tbody tr:hover { background-color: #f5f8fa; }
	#execution_plan_table tbody td { vertical-align: middle; }

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
	#execution_plan_search_form .select2-container.ep-selected-bg .select2-selection__rendered {
		background-color: #6f42c1;
		color: #fff !important;
		border-radius: 3px;
		padding-left: 10px !important;
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
		gap: 8px;
		margin-right: 8px;
		vertical-align: middle;
		align-items: center;
		padding: 4px;
		background: linear-gradient(180deg, #f3faf4 0%, #e8f4ea 100%);
		border: 1px solid #c7decb;
		border-radius: 10px;
		box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9), 0 1px 2px rgba(39, 95, 52, 0.10);
	}
	.ep-billing-tab {
		background: #ffffff;
		border: 1px solid #c9dccd;
		color: #2f5e3a;
		font-weight: 700;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		font-size: 12px;
		padding: 9px 16px;
		border-radius: 8px;
		min-width: 94px;
		text-align: center;
		transition: all .2s ease;
		box-shadow: 0 1px 1px rgba(39, 95, 52, 0.08);
	}
	.ep-billing-tab:hover {
		color: #1f4f2b;
		background: #eef8f0;
		border-color: #a8cdb0;
		transform: translateY(-1px);
		box-shadow: 0 3px 8px rgba(39, 95, 52, 0.18);
	}
	.ep-billing-tab.active {
		background: linear-gradient(180deg, #4eb35b 0%, #3b9f48 100%);
		border-color: #358d41;
		color: #fff !important;
		box-shadow: 0 4px 10px rgba(39, 95, 52, 0.28);
	}
	.ep-billing-tab:focus,
	.ep-billing-tab:active {
		outline: none;
		text-decoration: none;
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
			display: flex;
			margin: 0 0 10px 0;
			width: 100%;
			justify-content: space-between;
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

	function updateSelectedBg($select) {
		var value = $select.val();
		var $container = $select.next(".select2-container");
		var hasValue = false;
		if ($.isArray(value)) {
			hasValue = value.length > 0;
		} else {
			hasValue = !!value && value !== "all";
		}
		if (hasValue) {
			$container.addClass("ep-selected-bg");
		} else {
			$container.removeClass("ep-selected-bg");
		}
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
			updateSelectedBg($(this));
		});
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
		$.ajax({
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
		});
	}

	function loadProjectsByClient() {
		var departments = $("#department").val() || [];
		var clientIds = $("#client_Id").val() || [];
		$.ajax({
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

	$(".ep-status-btn").on("click", function() {
		var status = $(this).data("status");
		if (String(status).toLowerCase() === "all") {
			$("#from_year, #from_month, #to_year, #to_month").val("all").trigger("change");
		}
		$("#project_status").val(status);
		$(".ep-status-btn").removeClass("active");
		$(this).addClass("active");
		$("#execution_plan_search_form").trigger("submit");
	});

	function syncActiveStatusButton() {
		var currentStatus = $.trim($("#project_status").val() || "").toLowerCase();
		$(".ep-status-btn").removeClass("active");
		if (currentStatus === "" || currentStatus === "all") {
			$('.ep-status-btn[data-status="all"]').addClass("active");
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
		$('.ep-status-btn[data-status="all"]').addClass("active");
	}

	syncActiveStatusButton();
});
</script>

<?php $this->load->view('includes/cRMFooter'); ?>
