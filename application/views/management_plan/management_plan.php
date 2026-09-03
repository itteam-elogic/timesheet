<?php $this->load->view('includes/cRMHeader'); ?>
<?php
	$records = isset($records) ? $records : array();
	$clients = isset($clients) ? $clients : array();
	$years = isset($years) ? $years : array();
	$months = isset($months) ? $months : array();
	$client_Id = isset($client_Id) ? (array)$client_Id : array();
	$from_year = isset($from_year) ? (array)$from_year : array();
	$from_month = isset($from_month) ? (array)$from_month : array();
	$to_year = isset($to_year) ? (array)$to_year : array();
	$to_month = isset($to_month) ? (array)$to_month : array();
	$totalRecords = count($records);
	$totalInvoiceHours = 0;
	$totalMonthRows = 0;
	foreach ($records as $statRow) {
		$totalInvoiceHours += isset($statRow->invoice_hours) ? (float)$statRow->invoice_hours : 0;
		$totalMonthRows += (!empty($statRow->month_rows) && is_array($statRow->month_rows)) ? count($statRow->month_rows) : 0;
	}
	$monthNameMap = array(
		1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
		5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
		9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
	);
	$fromYearLabel = !empty($from_year) ? reset($from_year) : 'All';
	$toYearLabel = !empty($to_year) ? reset($to_year) : 'All';
	$fromMonthLabel = (!empty($from_month) && isset($monthNameMap[(int)reset($from_month)])) ? $monthNameMap[(int)reset($from_month)] : 'All months';
	$toMonthLabel = (!empty($to_month) && isset($monthNameMap[(int)reset($to_month)])) ? $monthNameMap[(int)reset($to_month)] : 'All months';
	$currentYearValue = (string)date('Y');

	if (!function_exists('management_plan_date_display')) {
		function management_plan_date_display($value, $withTime = false) {
			if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
				return '';
			}
			$ts = strtotime($value);
			if ($ts === false) {
				return '';
			}
			if ($withTime && date('H:i:s', $ts) !== '00:00:00') {
				return date('d-M-Y H:i', $ts);
			}
			return date('d-M-Y', $ts);
		}
	}

	if (!function_exists('management_plan_hours_display')) {
		function management_plan_hours_display($value) {
			$value = (float)$value;
			if (fmod($value, 1.0) == 0.0) {
				return (string)(int)$value;
			}
			return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
		}
	}

	if (!function_exists('management_plan_client_name')) {
		function management_plan_client_name($value) {
			return ucfirst(str_replace("'", " ", trim((string)$value)));
		}
	}

	if (!function_exists('management_plan_date_cell')) {
		function management_plan_date_cell($value, $withTime = false) {
			$display = management_plan_date_display($value, $withTime);
			if ($display === '') {
				return '<span class="mp-date-empty">-</span>';
			}
			return '<span class="mp-date-pill"><i class="fa fa-calendar"></i> ' . htmlspecialchars($display, ENT_QUOTES, 'UTF-8') . '</span>';
		}
	}

	if (!function_exists('management_plan_hours_cell')) {
		function management_plan_hours_cell($value, $strong = false) {
			$display = management_plan_hours_display($value);
			$cls = ((float)$value > 0) ? 'mp-hours-pill has-value' : 'mp-hours-pill';
			if ($strong) {
				$cls .= ' is-total';
			}
			return '<span class="' . $cls . '">' . htmlspecialchars($display, ENT_QUOTES, 'UTF-8') . '</span>';
		}
	}

	if (!function_exists('management_plan_initial')) {
		function management_plan_initial($value) {
			$value = trim((string)$value);
			return ($value !== '') ? strtoupper(substr($value, 0, 1)) : '?';
		}
	}
?>

<div class="content-wrapper mp-page">
	<div id="mp_page_loader" class="mp-page-loader" style="display:none;">
		<div class="mp-page-loader-content">
			<div class="mp-page-loader-spinner"><i class="fa fa-spinner fa-spin"></i></div>
			<div>
				<strong>Please wait</strong>
				<span>Loading management plan...</span>
			</div>
		</div>
	</div>

	<div class="page-title mp-page-title">
		<div>
			<h1><i class="fa fa-briefcase"></i> Management Plan</h1>
			<p class="mp-subtitle">Client start / end dates, latest timesheet date, and invoice hours. Click a client to view month-wise details.</p>
		</div>
		<div class="mp-title-actions">
			<a class="btn btn-default mp-btn" href="<?php echo base_url('management_plan'); ?>"><i class="fa fa-refresh"></i> Reset</a>
			<button type="button" class="btn btn-success mp-btn" id="mp_export_report_btn">
				<i class="fa fa-download"></i> Export Excel
			</button>
		</div>
	</div>

	<div class="mp-summary-row">
		<div class="mp-summary-card">
			<span class="mp-summary-label">Clients</span>
			<strong><?php echo (int)$totalRecords; ?></strong>
		</div>
		<div class="mp-summary-card">
			<span class="mp-summary-label">Invoice Hours</span>
			<strong><?php echo htmlspecialchars(management_plan_hours_display($totalInvoiceHours), ENT_QUOTES, 'UTF-8'); ?></strong>
		</div>
		<div class="mp-summary-card">
			<span class="mp-summary-label">Month Rows</span>
			<strong><?php echo (int)$totalMonthRows; ?></strong>
		</div>
		<div class="mp-summary-card mp-summary-period">
			<span class="mp-summary-label">Selected Period</span>
			<strong><?php echo htmlspecialchars($fromMonthLabel . ' ' . $fromYearLabel, ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($toMonthLabel . ' ' . $toYearLabel, ENT_QUOTES, 'UTF-8'); ?></strong>
		</div>
	</div>

	<div class="card mp-filter-card">
		<div class="card-body">
			<form id="management_plan_search_form" method="post" action="<?php echo base_url('management_plan'); ?>">
				<div class="mp-filter-grid">
					<div class="mp-filter-client">
						<label class="control-label">Client</label>
						<select class="form-control" name="client_Id[]" id="client_Id" multiple="multiple">
							<?php if (!empty($clients)) { foreach ($clients as $client) { ?>
								<option value="<?php echo (int)$client->client_Id; ?>" <?php echo in_array((string)$client->client_Id, $client_Id, true) ? 'selected="selected"' : ''; ?>>
									<?php echo htmlspecialchars(management_plan_client_name($client->client_name), ENT_QUOTES, 'UTF-8'); ?>
								</option>
							<?php }} ?>
						</select>
					</div>
					<div class="mp-ym-panel">
						<div class="mp-ym-title">From</div>
						<div class="mp-ym-fields">
							<select class="form-control" name="from_year" id="from_year">
								<option value="all" <?php echo empty($from_year) ? 'selected="selected"' : ''; ?>>All</option>
								<?php if (!empty($years)) { foreach ($years as $yearOption) { ?>
									<option value="<?php echo (int)$yearOption->year; ?>" <?php echo in_array((string)$yearOption->year, $from_year, true) ? 'selected="selected"' : ''; ?>>
										<?php echo (int)$yearOption->year; ?>
									</option>
								<?php }} ?>
							</select>
							<select class="form-control" name="from_month" id="from_month">
								<option value="all" <?php echo empty($from_month) ? 'selected="selected"' : ''; ?>>Month</option>
								<?php if (!empty($months)) { foreach ($months as $monthOption) { ?>
									<option value="<?php echo (int)$monthOption->month_number; ?>" <?php echo in_array((string)$monthOption->month_number, $from_month, true) ? 'selected="selected"' : ''; ?>>
										<?php echo htmlspecialchars($monthOption->month_name, ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php }} ?>
							</select>
						</div>
					</div>
					<div class="mp-ym-panel">
						<div class="mp-ym-title">To</div>
						<div class="mp-ym-fields">
							<select class="form-control" name="to_year" id="to_year">
								<option value="all" <?php echo empty($to_year) ? 'selected="selected"' : ''; ?>>All</option>
								<?php if (!empty($years)) { foreach ($years as $yearOption) { ?>
									<option value="<?php echo (int)$yearOption->year; ?>" <?php echo in_array((string)$yearOption->year, $to_year, true) ? 'selected="selected"' : ''; ?>>
										<?php echo (int)$yearOption->year; ?>
									</option>
								<?php }} ?>
							</select>
							<select class="form-control" name="to_month" id="to_month">
								<option value="all" <?php echo empty($to_month) ? 'selected="selected"' : ''; ?>>Month</option>
								<?php if (!empty($months)) { foreach ($months as $monthOption) { ?>
									<option value="<?php echo (int)$monthOption->month_number; ?>" <?php echo in_array((string)$monthOption->month_number, $to_month, true) ? 'selected="selected"' : ''; ?>>
										<?php echo htmlspecialchars($monthOption->month_name, ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php }} ?>
							</select>
						</div>
					</div>
					<div class="mp-filter-actions">
						<button type="submit" class="btn btn-primary mp-btn" id="mp_search_btn">
							<i class="fa fa-search"></i> Search
						</button>
						<button type="button" class="btn btn-default mp-btn" id="mp_current_year_btn" data-year="<?php echo htmlspecialchars($currentYearValue, ENT_QUOTES, 'UTF-8'); ?>">
							<i class="fa fa-calendar"></i> <?php echo htmlspecialchars($currentYearValue, ENT_QUOTES, 'UTF-8'); ?>
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="card mp-table-card">
		<div class="mp-table-toolbar">
			<div>
				<h3><i class="fa fa-table"></i> Client Invoice Grid</h3>
				<small class="mp-hint"><i class="fa fa-hand-pointer-o"></i> Click a client name or + to open month-wise invoice hours</small>
			</div>
			<div class="mp-toolbar-right">
				<div class="mp-search-wrap">
					<i class="fa fa-search"></i>
					<input type="text" id="mp_quick_search" placeholder="Search client...">
				</div>
				<button type="button" class="btn btn-default btn-sm mp-btn" id="mp_expand_all_btn"><i class="fa fa-plus-square-o"></i> Expand All</button>
				<button type="button" class="btn btn-default btn-sm mp-btn" id="mp_collapse_all_btn"><i class="fa fa-minus-square-o"></i> Collapse All</button>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive mp-table-wrap">
				<table class="table table-bordered" id="management_plan_table">
					<thead>
						<tr>
							<th class="mp-col-sno">#</th>
							<th class="mp-col-client">Client Name</th>
							<th class="mp-col-date">Start Date</th>
							<th class="mp-col-date">End Date</th>
							<th class="mp-col-date">Timesheet Date</th>
							<th class="mp-col-hours">Invoice Hours</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($records)): ?>
							<tr>
								<td colspan="6" class="mp-empty-state">
									<i class="fa fa-inbox"></i>
									<strong>No records found</strong>
									<span>Try another client or date range.</span>
								</td>
							</tr>
						<?php else: ?>
							<?php $i = 1; $clientIndex = 0; foreach ($records as $row): ?>
								<?php
									$clientIndex++;
									$clientName = management_plan_client_name(isset($row->client_name) ? $row->client_name : '');
									$monthRows = isset($row->month_rows) && is_array($row->month_rows) ? $row->month_rows : array();
									$monthCount = count($monthRows);
									$rowSearch = strtolower($clientName);
								?>
								<tr class="client-header-row<?php echo ($monthCount > 0) ? ' is-expandable' : ''; ?>" data-client-index="<?php echo $clientIndex; ?>" data-search="<?php echo htmlspecialchars($rowSearch, ENT_QUOTES, 'UTF-8'); ?>">
									<td class="mp-col-sno"><?php echo $i; ?></td>
									<td class="client-cell">
										<span class="mp-client-avatar"><?php echo htmlspecialchars(management_plan_initial($clientName), ENT_QUOTES, 'UTF-8'); ?></span>
										<span class="mp-client-copy">
											<span class="client-name-text"><?php echo htmlspecialchars($clientName, ENT_QUOTES, 'UTF-8'); ?></span>
											<?php if ($monthCount > 0): ?>
												<span class="mp-month-count"><?php echo (int)$monthCount; ?> month<?php echo $monthCount === 1 ? '' : 's'; ?></span>
											<?php endif; ?>
										</span>
										<?php if ($monthCount > 0): ?>
											<span class="client-toggle-icon" data-client-index="<?php echo $clientIndex; ?>" title="Show month-wise hours"><i class="fa fa-plus"></i></span>
										<?php endif; ?>
									</td>
									<td class="date-cell"><?php echo management_plan_date_cell(isset($row->start_date) ? $row->start_date : ''); ?></td>
									<td class="date-cell"><?php echo management_plan_date_cell(isset($row->end_date) ? $row->end_date : '', true); ?></td>
									<td class="date-cell"><?php echo management_plan_date_cell(isset($row->timesheet_date) ? $row->timesheet_date : '', true); ?></td>
									<td class="num-cell"><?php echo management_plan_hours_cell(isset($row->invoice_hours) ? $row->invoice_hours : 0, true); ?></td>
								</tr>
								<?php foreach ($monthRows as $monthRow):
									$yearVal = isset($monthRow->year_val) ? (int)$monthRow->year_val : 0;
									$monthVal = isset($monthRow->month_val) ? (int)$monthRow->month_val : 0;
									$monthLabel = ($yearVal > 0 && $monthVal >= 1 && $monthVal <= 12)
										? date('M Y', strtotime(sprintf('%04d-%02d-01', $yearVal, $monthVal)))
										: 'N/A';
									$monthStart = ($yearVal > 0 && $monthVal >= 1 && $monthVal <= 12)
										? sprintf('%04d-%02d-01', $yearVal, $monthVal)
										: '';
									$monthEnd = ($monthStart !== '') ? date('Y-m-t', strtotime($monthStart)) : '';
									$monthSearch = strtolower($clientName . ' ' . $monthLabel);
								?>
								<tr class="client-month-row client-months-<?php echo $clientIndex; ?>" style="display:none;" data-client-index="<?php echo $clientIndex; ?>" data-search="<?php echo htmlspecialchars($monthSearch, ENT_QUOTES, 'UTF-8'); ?>">
									<td class="mp-col-sno"></td>
									<td class="month-cell">
										<span class="month-name-inner">
											<span class="mp-month-chip"><?php echo htmlspecialchars($monthLabel, ENT_QUOTES, 'UTF-8'); ?></span>
										</span>
									</td>
									<td class="date-cell"><?php echo management_plan_date_cell($monthStart); ?></td>
									<td class="date-cell"><?php echo management_plan_date_cell($monthEnd, true); ?></td>
									<td class="date-cell"><?php echo management_plan_date_cell(isset($monthRow->timesheet_date) ? $monthRow->timesheet_date : '', true); ?></td>
									<td class="num-cell"><?php echo management_plan_hours_cell(isset($monthRow->invoice_hours) ? $monthRow->invoice_hours : 0); ?></td>
								</tr>
								<?php endforeach; ?>
							<?php $i++; endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<iframe id="mp_export_iframe" name="mp_export_iframe" style="display:none;"></iframe>

<style>
.mp-page { padding-bottom: 24px; }
.mp-page-title {
	align-items: flex-start;
	margin-bottom: 16px;
}
.mp-page-title h1 {
	margin: 0 0 6px;
	font-size: 26px;
	font-weight: 700;
	color: #1f5076;
}
.mp-subtitle {
	margin: 0;
	color: #6c7a89;
	font-size: 13px;
	max-width: 640px;
	line-height: 1.45;
}
.mp-title-actions { display: flex; gap: 8px; }
.mp-btn {
	border-radius: 8px !important;
	font-weight: 600;
	padding: 8px 14px;
}
.mp-summary-row {
	display: flex;
	flex-wrap: wrap;
	gap: 12px;
	margin-bottom: 16px;
}
.mp-summary-card {
	background: #fff;
	border: 1px solid #e3e8ef;
	border-radius: 12px;
	padding: 14px 16px;
	min-width: 150px;
	box-shadow: 0 2px 8px rgba(31, 80, 118, 0.06);
	flex: 1 1 150px;
}
.mp-summary-card .mp-summary-label {
	display: block;
	font-size: 11px;
	font-weight: 700;
	letter-spacing: .4px;
	text-transform: uppercase;
	color: #7a8896;
	margin-bottom: 4px;
}
.mp-summary-card strong {
	font-size: 22px;
	color: #1f5076;
	line-height: 1.2;
}
.mp-summary-period strong { font-size: 14px; }
.mp-filter-card,
.mp-table-card {
	border: 1px solid #e3e8ef;
	border-radius: 12px;
	box-shadow: 0 2px 10px rgba(31, 80, 118, 0.07);
	overflow: hidden;
	margin-bottom: 18px;
}
.mp-filter-card .card-body { padding: 16px 18px 14px; }
.mp-filter-grid {
	display: flex;
	flex-wrap: wrap;
	gap: 12px;
	align-items: flex-end;
}
.mp-filter-client { flex: 1 1 240px; min-width: 220px; }
.mp-filter-card .control-label,
.mp-ym-title {
	font-size: 12px;
	font-weight: 700;
	text-transform: uppercase;
	letter-spacing: 0.3px;
	color: #5a6a7a;
	margin-bottom: 6px;
}
.mp-ym-panel {
	background: #f8fafc;
	border: 1px solid #e2e8f0;
	border-radius: 10px;
	padding: 10px 12px;
	min-width: 260px;
	flex: 1 1 240px;
}
.mp-ym-fields {
	display: flex;
	gap: 8px;
}
.mp-ym-fields select { flex: 1; }
.mp-filter-actions {
	display: flex;
	gap: 8px;
	padding-bottom: 2px;
}
.mp-filter-card .form-control {
	border-radius: 8px;
	border-color: #d5dbe3;
	height: 38px;
}
.mp-table-toolbar {
	display: flex;
	align-items: flex-start;
	justify-content: space-between;
	gap: 12px;
	padding: 14px 18px;
	background: #f8fafc;
	border-bottom: 1px solid #e8edf3;
	flex-wrap: wrap;
}
.mp-table-toolbar h3 {
	margin: 0 0 4px;
	font-size: 16px;
	font-weight: 700;
	color: #1f5076;
}
.mp-hint {
	color: #7a8896;
	font-size: 12px;
}
.mp-toolbar-right {
	display: flex;
	align-items: center;
	gap: 8px;
	flex-wrap: wrap;
}
.mp-search-wrap {
	position: relative;
}
.mp-search-wrap i {
	position: absolute;
	left: 10px;
	top: 50%;
	transform: translateY(-50%);
	color: #8a97a5;
}
#mp_quick_search {
	width: 210px;
	height: 34px;
	border-radius: 8px;
	border: 1px solid #d5dbe3;
	padding: 6px 10px 6px 30px;
}
.mp-table-wrap { padding: 0; }
#management_plan_table {
	width: 100%;
	margin-bottom: 0;
	border-collapse: separate;
	border-spacing: 0;
}
#management_plan_table thead th {
	background: linear-gradient(to bottom, #337ab7, #2c5aa0);
	color: #fff;
	font-weight: 700;
	font-size: 12px;
	text-transform: uppercase;
	letter-spacing: 0.3px;
	padding: 12px 10px;
	border-color: #2c5aa0 !important;
	text-align: center;
	vertical-align: middle;
	white-space: nowrap;
	position: sticky;
	top: 0;
	z-index: 2;
}
#management_plan_table tbody td {
	padding: 11px 10px;
	vertical-align: middle;
	font-size: 13px;
	color: #2c3e50;
	border-color: #e8edf3 !important;
	background: #fff;
}
#management_plan_table .mp-col-sno { text-align: center; width: 54px; color: #7a8896; }
#management_plan_table .mp-col-client { min-width: 280px; text-align: left; }
#management_plan_table .mp-col-date { min-width: 150px; }
#management_plan_table .mp-col-hours { min-width: 120px; }
.client-header-row td {
	background: #f4f8fc !important;
	font-weight: 600;
	border-bottom: 1px solid #dbe7f3 !important;
}
.client-header-row.is-expandable { cursor: pointer; }
.client-header-row.is-expandable:hover td { background: #eaf3fb !important; }
.client-header-row.is-open td {
	background: #e3eef9 !important;
	border-bottom-color: #c5d8ec !important;
}
.client-cell {
	display: flex;
	align-items: center;
	gap: 10px;
	color: #1f5076;
	font-size: 14px;
}
.mp-client-avatar {
	width: 32px;
	height: 32px;
	border-radius: 50%;
	background: #2c5aa0;
	color: #fff;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	font-size: 13px;
	font-weight: 700;
	flex: 0 0 32px;
}
.mp-client-copy {
	display: flex;
	flex-direction: column;
	min-width: 0;
	flex: 1;
	line-height: 1.25;
}
.client-name-text { color: #1f5076; }
.mp-month-count {
	font-size: 11px;
	color: #6f7f90;
	font-weight: 600;
}
.client-toggle-icon {
	width: 26px;
	height: 26px;
	border-radius: 6px;
	background: #2c5aa0;
	color: #fff;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	flex: 0 0 26px;
}
.client-header-row.is-open .client-toggle-icon { background: #1e7e34; }
.client-month-row td { background: #fcfdff !important; }
.client-month-row:hover td { background: #f7fbff !important; }
.month-cell { padding-left: 56px !important; }
.mp-month-chip {
	display: inline-block;
	background: #eef4fb;
	border: 1px solid #d4e2f1;
	color: #245f8a;
	border-radius: 999px;
	padding: 4px 10px;
	font-size: 12px;
	font-weight: 700;
	letter-spacing: .2px;
}
.date-cell { text-align: center; }
.mp-date-pill {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	background: #f4f7fb;
	border: 1px solid #e1e8f0;
	border-radius: 999px;
	padding: 4px 10px;
	color: #4a5b6b;
	font-size: 12px;
	font-weight: 600;
	white-space: nowrap;
}
.mp-date-pill i { color: #6c7a89; }
.mp-date-empty { color: #c0c8d0; font-weight: 600; }
.num-cell { text-align: center; }
.mp-hours-pill {
	display: inline-block;
	min-width: 52px;
	padding: 4px 10px;
	border-radius: 999px;
	background: #f1f4f8;
	color: #5a6a7a;
	font-weight: 700;
	font-family: "Courier New", monospace;
}
.mp-hours-pill.has-value {
	background: #e6f6ec;
	color: #1e7e34;
}
.mp-hours-pill.is-total {
	background: #dceefe;
	color: #1f5076;
}
.mp-empty-state {
	padding: 48px 16px !important;
	text-align: center;
	color: #6c757d;
}
.mp-empty-state i { display: block; font-size: 30px; margin-bottom: 8px; color: #adb5bd; }
.mp-empty-state strong { display: block; font-size: 16px; margin-bottom: 4px; color: #4a5b6b; }
.mp-empty-state span { font-size: 13px; }
.mp-page-loader {
	position: fixed;
	inset: 0;
	background: rgba(255,255,255,.7);
	z-index: 9999;
	display: flex;
	align-items: center;
	justify-content: center;
}
.mp-page-loader-content {
	background: #fff;
	border: 1px solid #e3e8ef;
	border-radius: 12px;
	padding: 18px 22px;
	display: flex;
	align-items: center;
	gap: 12px;
	box-shadow: 0 8px 24px rgba(0,0,0,.08);
	color: #1f5076;
}
.mp-page-loader-content span { display: block; font-size: 12px; color: #6c7a89; }
.mp-page-loader-spinner { font-size: 22px; color: #2c5aa0; }
#management_plan_search_form .select2-container .select2-selection--multiple,
#management_plan_search_form .select2-container .select2-selection--single {
	min-height: 38px;
	border-color: #d5dbe3;
	border-radius: 8px;
}
#management_plan_search_form .select2-container.mp-selected-bg .select2-selection--single,
#management_plan_search_form .select2-container.mp-selected-bg .select2-selection--multiple {
	background-color: #6f42c1 !important;
	border-color: #6f42c1 !important;
}
#management_plan_search_form .select2-container.mp-selected-bg .select2-selection__rendered,
#management_plan_search_form .select2-container.mp-selected-bg .select2-selection__placeholder {
	color: #fff !important;
}
#management_plan_search_form .select2-container.mp-selected-bg .select2-selection__arrow b {
	border-top-color: #fff !important;
}
@media (max-width: 992px) {
	.mp-page-title { display: block; }
	.mp-title-actions { margin-top: 10px; }
	.month-cell { padding-left: 22px !important; }
}
</style>

<script>
$(document).ready(function() {
	function updateSelectedBg($el) {
		var $container = $el.next(".select2-container");
		var val = $el.val();
		var hasValue = false;
		if ($.isArray(val)) {
			hasValue = val.length > 0;
		} else {
			hasValue = val !== null && val !== "" && val !== "all";
		}
		if (hasValue) {
			$container.addClass("mp-selected-bg");
		} else {
			$container.removeClass("mp-selected-bg");
		}
	}

	if ($.fn.select2) {
		$('#client_Id').select2({
			placeholder: 'All clients',
			allowClear: true,
			width: '100%'
		});
		$('#from_year, #from_month, #to_year, #to_month').select2({
			minimumResultsForSearch: Infinity,
			width: '100%'
		}).on("change", function() {
			updateSelectedBg($(this));
		});
		$('#client_Id').on("change", function() {
			updateSelectedBg($(this));
		});
		$("#from_year, #from_month, #to_year, #to_month, #client_Id").each(function() {
			updateSelectedBg($(this));
		});
	}

	function setOpenState(clientIndex, isOpen) {
		var $header = $('.client-header-row[data-client-index="' + clientIndex + '"]');
		var $icon = $('.client-toggle-icon[data-client-index="' + clientIndex + '"] i');
		var $months = $(".client-months-" + clientIndex);
		if (isOpen) {
			$months.show();
			$header.addClass("is-open");
			$icon.removeClass("fa-plus").addClass("fa-minus");
		} else {
			$months.hide();
			$header.removeClass("is-open");
			$icon.removeClass("fa-minus").addClass("fa-plus");
		}
	}

	function toggleClientMonths(clientIndex) {
		var $months = $(".client-months-" + clientIndex);
		if (!$months.length) {
			return;
		}
		setOpenState(clientIndex, !$months.is(":visible"));
	}

	$(document).on("click", ".client-header-row.is-expandable", function(e) {
		e.preventDefault();
		toggleClientMonths($(this).data("client-index"));
	});

	$("#mp_expand_all_btn").on("click", function() {
		$(".client-header-row.is-expandable:visible").each(function() {
			setOpenState($(this).data("client-index"), true);
		});
	});

	$("#mp_collapse_all_btn").on("click", function() {
		$(".client-header-row.is-expandable").each(function() {
			setOpenState($(this).data("client-index"), false);
		});
	});

	$("#mp_quick_search").on("keyup", function() {
		var query = $.trim($(this).val()).toLowerCase();
		$(".client-header-row").each(function() {
			var $header = $(this);
			var idx = $header.data("client-index");
			var match = query === "" || String($header.data("search") || "").indexOf(query) !== -1;
			$header.toggle(match);
			if (!match) {
				setOpenState(idx, false);
			}
		});
	});

	$("#mp_current_year_btn").on("click", function() {
		var year = String($(this).data("year") || "");
		$("#from_year").val(year).trigger("change");
		$("#to_year").val(year).trigger("change");
		$("#from_month").val("all").trigger("change");
		$("#to_month").val("all").trigger("change");
		$("#mp_page_loader").show();
		$("#management_plan_search_form").trigger("submit");
	});

	$("#management_plan_search_form").on("submit", function() {
		$("#mp_page_loader").show();
	});

	$('#mp_export_report_btn').on('click', function() {
		var $form = $('#management_plan_search_form');
		var originalAction = $form.attr('action');
		var originalTarget = $form.attr('target');
		$form.attr('action', '<?php echo base_url('management_plan/export_report'); ?>');
		$form.attr('target', 'mp_export_iframe');
		$form.trigger('submit');
		setTimeout(function() {
			$form.attr('action', originalAction);
			if (originalTarget) {
				$form.attr('target', originalTarget);
			} else {
				$form.removeAttr('target');
			}
			$("#mp_page_loader").hide();
		}, 800);
	});
});
</script>

<?php $this->load->view('includes/cRMFooter'); ?>
