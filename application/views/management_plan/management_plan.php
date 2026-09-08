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
	$totalTimesheetHours = 0;
	foreach ($records as $statRow) {
		$totalInvoiceHours += isset($statRow->invoice_hours) ? (float)$statRow->invoice_hours : 0;
		$totalTimesheetHours += isset($statRow->timesheet_hours) ? (float)$statRow->timesheet_hours : 0;
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
	$monthRowsUrl = base_url('management_plan/month_rows');
	$exportUrl = base_url('management_plan/export_report');

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
				return '<span class="mp-muted">-</span>';
			}
			return '<span class="mp-date">' . htmlspecialchars($display, ENT_QUOTES) . '</span>';
		}
	}

	if (!function_exists('management_plan_hours_cell')) {
		function management_plan_hours_cell($value, $strong = false) {
			$display = management_plan_hours_display($value);
			$cls = 'mp-hours';
			if ((float)$value > 0) {
				$cls .= ' has-value';
			}
			if ($strong) {
				$cls .= ' is-total';
			}
			return '<span class="' . $cls . '">' . htmlspecialchars($display, ENT_QUOTES) . '</span>';
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
			<div class="mp-page-loader-spinner"></div>
			<div>
				<strong>Please wait</strong>
				<span>Loading management plan...</span>
			</div>
		</div>
	</div>

	<div class="mp-hero">
		<div class="mp-hero-copy">
			<p class="mp-kicker">Reports</p>
			<h1>Management Plan</h1>
			<p>Client timelines, timesheet hours, and invoice hours in one view. Open a client to see month-wise details.</p>
		</div>
		<div class="mp-hero-actions">
			<a class="mp-btn mp-btn-ghost" href="<?php echo base_url('management_plan'); ?>"><i class="fa fa-refresh"></i> Reset</a>
			<button type="button" class="mp-btn mp-btn-success" id="mp_export_report_btn">
				<i class="fa fa-download"></i> Export Excel
			</button>
		</div>
	</div>

	<div class="mp-summary-row">
		<div class="mp-summary-card is-clients">
			<div class="mp-summary-icon"><i class="fa fa-users"></i></div>
			<div>
				<span>Clients</span>
				<strong><?php echo (int)$totalRecords; ?></strong>
			</div>
		</div>
		<div class="mp-summary-card is-timesheet">
			<div class="mp-summary-icon"><i class="fa fa-clock-o"></i></div>
			<div>
				<span>Timesheet Hours</span>
				<strong><?php echo htmlspecialchars(management_plan_hours_display($totalTimesheetHours), ENT_QUOTES); ?></strong>
			</div>
		</div>
		<div class="mp-summary-card is-invoice">
			<div class="mp-summary-icon"><i class="fa fa-file-text-o"></i></div>
			<div>
				<span>Invoice Hours</span>
				<strong><?php echo htmlspecialchars(management_plan_hours_display($totalInvoiceHours), ENT_QUOTES); ?></strong>
			</div>
		</div>
		<div class="mp-summary-card is-period">
			<div class="mp-summary-icon"><i class="fa fa-calendar"></i></div>
			<div>
				<span>Selected Period</span>
				<strong><?php echo htmlspecialchars($fromMonthLabel . ' ' . $fromYearLabel . ' - ' . $toMonthLabel . ' ' . $toYearLabel, ENT_QUOTES); ?></strong>
			</div>
		</div>
	</div>

	<div class="mp-panel">
		<form id="management_plan_search_form" method="post" action="<?php echo base_url('management_plan'); ?>">
			<div class="mp-filter-grid">
				<div class="mp-filter-client">
					<label>Client</label>
					<select class="form-control" name="client_Id[]" id="client_Id" multiple="multiple">
						<?php if (!empty($clients)) { foreach ($clients as $client) { ?>
							<option value="<?php echo (int)$client->client_Id; ?>" <?php echo in_array((string)$client->client_Id, $client_Id, true) ? 'selected="selected"' : ''; ?>>
								<?php echo htmlspecialchars(management_plan_client_name($client->client_name), ENT_QUOTES); ?>
							</option>
						<?php }} ?>
					</select>
				</div>
				<div class="mp-ym-panel">
					<label>From</label>
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
									<?php echo htmlspecialchars($monthOption->month_name, ENT_QUOTES); ?>
								</option>
							<?php }} ?>
						</select>
					</div>
				</div>
				<div class="mp-ym-panel">
					<label>To</label>
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
									<?php echo htmlspecialchars($monthOption->month_name, ENT_QUOTES); ?>
								</option>
							<?php }} ?>
						</select>
					</div>
				</div>
				<div class="mp-filter-actions">
					<button type="submit" class="mp-btn mp-btn-primary" id="mp_search_btn">
						<i class="fa fa-search"></i> Search
					</button>
					<button type="button" class="mp-btn mp-btn-ghost" id="mp_current_year_btn" data-year="<?php echo htmlspecialchars($currentYearValue, ENT_QUOTES); ?>">
						<i class="fa fa-calendar"></i> <?php echo htmlspecialchars($currentYearValue, ENT_QUOTES); ?>
					</button>
				</div>
			</div>
		</form>
	</div>

	<div class="mp-panel mp-grid-panel">
		<div class="mp-table-toolbar">
			<div>
				<h3>Client Invoice Grid</h3>
				<small>Click a client to load month-wise hours on demand</small>
			</div>
			<div class="mp-toolbar-right">
				<div class="mp-search-wrap">
					<i class="fa fa-search"></i>
					<input type="text" id="mp_quick_search" placeholder="Search client...">
				</div>
				<button type="button" class="mp-btn mp-btn-ghost mp-btn-sm" id="mp_expand_all_btn"><i class="fa fa-plus-square-o"></i> Expand All</button>
				<button type="button" class="mp-btn mp-btn-ghost mp-btn-sm" id="mp_collapse_all_btn"><i class="fa fa-minus-square-o"></i> Collapse All</button>
			</div>
		</div>
		<div class="mp-table-wrap">
			<table class="table" id="management_plan_table">
				<thead>
					<tr>
						<th class="mp-col-sno">#</th>
						<th class="mp-col-client">Client Name</th>
						<th class="mp-col-date">Start Date</th>
						<th class="mp-col-date">End Date</th>
						<th class="mp-col-date">Timesheet Date</th>
						<th class="mp-col-hours">Timesheet Hours</th>
						<th class="mp-col-hours">Invoice Hours</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($records)): ?>
						<tr>
							<td colspan="7" class="mp-empty-state">
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
								$clientId = isset($row->client_Id) ? (int)$row->client_Id : 0;
								$rowSearch = strtolower($clientName);
							?>
							<tr class="client-header-row is-expandable" data-client-index="<?php echo $clientIndex; ?>" data-client-id="<?php echo $clientId; ?>" data-search="<?php echo htmlspecialchars($rowSearch, ENT_QUOTES); ?>" data-loaded="0">
								<td class="mp-col-sno"><?php echo $i; ?></td>
								<td class="client-cell">
									<span class="mp-client-avatar"><?php echo htmlspecialchars(management_plan_initial($clientName), ENT_QUOTES); ?></span>
									<span class="mp-client-copy">
										<span class="client-name-text"><?php echo htmlspecialchars($clientName, ENT_QUOTES); ?></span>
										<span class="mp-month-count">Click to view months</span>
									</span>
									<span class="client-toggle-icon" data-client-index="<?php echo $clientIndex; ?>" title="Show month-wise hours"><i class="fa fa-plus"></i></span>
								</td>
								<td class="date-cell"><?php echo management_plan_date_cell(isset($row->start_date) ? $row->start_date : ''); ?></td>
								<td class="date-cell"><?php echo management_plan_date_cell(isset($row->end_date) ? $row->end_date : '', true); ?></td>
								<td class="date-cell"><?php echo management_plan_date_cell(isset($row->timesheet_date) ? $row->timesheet_date : '', true); ?></td>
								<td class="num-cell"><?php echo management_plan_hours_cell(isset($row->timesheet_hours) ? $row->timesheet_hours : 0, true); ?></td>
								<td class="num-cell"><?php echo management_plan_hours_cell(isset($row->invoice_hours) ? $row->invoice_hours : 0, true); ?></td>
							</tr>
						<?php $i++; endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<iframe id="mp_export_iframe" name="mp_export_iframe" style="display:none;"></iframe>

<style>
.mp-page { padding-bottom: 32px; }
.mp-hero {
	display: flex;
	justify-content: space-between;
	align-items: flex-end;
	gap: 16px;
	margin-bottom: 18px;
	padding: 22px 24px;
	border-radius: 18px;
	background: linear-gradient(135deg, #14375a 0%, #1f5f8b 55%, #2a7aa8 100%);
	color: #fff;
	box-shadow: 0 12px 28px rgba(20, 55, 90, 0.18);
}
.mp-kicker {
	margin: 0 0 4px;
	text-transform: uppercase;
	letter-spacing: .12em;
	font-size: 11px;
	font-weight: 700;
	opacity: .75;
}
.mp-hero h1 {
	margin: 0 0 6px;
	font-size: 28px;
	font-weight: 700;
}
.mp-hero p { margin: 0; max-width: 620px; opacity: .88; font-size: 13px; line-height: 1.5; }
.mp-hero-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.mp-btn {
	border: 0;
	border-radius: 10px;
	font-weight: 700;
	padding: 9px 14px;
	display: inline-flex;
	align-items: center;
	gap: 7px;
	cursor: pointer;
	text-decoration: none !important;
	line-height: 1.2;
}
.mp-btn-sm { padding: 7px 11px; font-size: 12px; }
.mp-btn-primary { background: #1f5f8b; color: #fff; }
.mp-btn-primary:hover { background: #16496c; color: #fff; }
.mp-btn-success { background: #1f9d6a; color: #fff; }
.mp-btn-success:hover { background: #178258; color: #fff; }
.mp-btn-ghost {
	background: rgba(255,255,255,.14);
	color: #fff;
	border: 1px solid rgba(255,255,255,.18);
}
.mp-panel .mp-btn-ghost,
.mp-toolbar-right .mp-btn-ghost {
	background: #f4f7fb;
	color: #31546f;
	border: 1px solid #dbe4ee;
}
.mp-summary-row {
	display: grid;
	grid-template-columns: repeat(4, minmax(0, 1fr));
	gap: 12px;
	margin-bottom: 16px;
}
.mp-summary-card {
	background: #fff;
	border: 1px solid #e6edf4;
	border-radius: 16px;
	padding: 16px;
	display: flex;
	align-items: center;
	gap: 12px;
	box-shadow: 0 6px 18px rgba(20, 55, 90, 0.06);
	min-width: 0;
}
.mp-summary-card span {
	display: block;
	font-size: 11px;
	font-weight: 700;
	letter-spacing: .04em;
	text-transform: uppercase;
	color: #7a8896;
	margin-bottom: 3px;
}
.mp-summary-card strong {
	display: block;
	font-size: 22px;
	color: #17364f;
	line-height: 1.2;
	word-break: break-word;
}
.mp-summary-card.is-period strong { font-size: 14px; }
.mp-summary-icon {
	width: 42px;
	height: 42px;
	border-radius: 12px;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	flex: 0 0 42px;
	color: #fff;
	font-size: 18px;
}
.is-clients .mp-summary-icon { background: #2c5aa0; }
.is-timesheet .mp-summary-icon { background: #1f9d6a; }
.is-invoice .mp-summary-icon { background: #d97706; }
.is-period .mp-summary-icon { background: #7c3aed; }
.mp-panel {
	background: #fff;
	border: 1px solid #e6edf4;
	border-radius: 16px;
	box-shadow: 0 8px 22px rgba(20, 55, 90, 0.06);
	margin-bottom: 16px;
	overflow: hidden;
}
.mp-filter-grid {
	display: flex;
	flex-wrap: wrap;
	gap: 12px;
	align-items: flex-end;
	padding: 16px 18px;
}
.mp-filter-client { flex: 1 1 240px; min-width: 220px; }
.mp-filter-grid label {
	display: block;
	font-size: 11px;
	font-weight: 700;
	text-transform: uppercase;
	letter-spacing: .04em;
	color: #667888;
	margin-bottom: 6px;
}
.mp-ym-panel { flex: 1 1 230px; min-width: 230px; }
.mp-ym-fields { display: flex; gap: 8px; }
.mp-ym-fields select { flex: 1; }
.mp-filter-actions { display: flex; gap: 8px; padding-bottom: 2px; }
.mp-panel .form-control {
	border-radius: 10px;
	border-color: #d5dbe3;
	height: 38px;
	box-shadow: none;
}
.mp-table-toolbar {
	display: flex;
	align-items: flex-start;
	justify-content: space-between;
	gap: 12px;
	padding: 16px 18px;
	background: linear-gradient(to right, #f7fbff, #fff);
	border-bottom: 1px solid #e8edf3;
	flex-wrap: wrap;
}
.mp-table-toolbar h3 {
	margin: 0 0 3px;
	font-size: 16px;
	font-weight: 700;
	color: #17364f;
}
.mp-table-toolbar small { color: #7a8896; }
.mp-toolbar-right {
	display: flex;
	align-items: center;
	gap: 8px;
	flex-wrap: wrap;
}
.mp-search-wrap { position: relative; }
.mp-search-wrap i {
	position: absolute;
	left: 11px;
	top: 50%;
	transform: translateY(-50%);
	color: #8a97a5;
}
#mp_quick_search {
	width: 210px;
	height: 36px;
	border-radius: 10px;
	border: 1px solid #d5dbe3;
	padding: 6px 10px 6px 30px;
}
.mp-table-wrap { overflow: auto; max-height: calc(100vh - 280px); }
#management_plan_table {
	width: 100%;
	margin-bottom: 0;
	border-collapse: separate;
	border-spacing: 0;
}
#management_plan_table thead th {
	background: #17364f;
	color: #fff;
	font-weight: 700;
	font-size: 11px;
	text-transform: uppercase;
	letter-spacing: .04em;
	padding: 13px 12px;
	border: 0;
	text-align: center;
	vertical-align: middle;
	white-space: nowrap;
	position: sticky;
	top: 0;
	z-index: 2;
}
#management_plan_table tbody td {
	padding: 12px;
	vertical-align: middle;
	font-size: 13px;
	color: #2c3e50;
	border-bottom: 1px solid #eef3f8;
	background: #fff;
}
#management_plan_table .mp-col-sno { text-align: center; width: 54px; color: #7a8896; }
#management_plan_table .mp-col-client { min-width: 280px; text-align: left; }
.client-header-row td { background: #f8fbfe !important; }
.client-header-row.is-expandable { cursor: pointer; }
.client-header-row.is-expandable:hover td { background: #eef6fd !important; }
.client-header-row.is-open td { background: #e7f1fb !important; }
.client-header-row.is-loading td { opacity: .75; }
.client-cell {
	display: flex;
	align-items: center;
	gap: 10px;
	color: #17364f;
	font-size: 14px;
}
.mp-client-avatar {
	width: 34px;
	height: 34px;
	border-radius: 11px;
	background: linear-gradient(180deg, #2c5aa0, #1f4578);
	color: #fff;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	font-size: 13px;
	font-weight: 700;
	flex: 0 0 34px;
}
.mp-client-copy {
	display: flex;
	flex-direction: column;
	min-width: 0;
	flex: 1;
	line-height: 1.25;
}
.client-name-text { color: #17364f; font-weight: 700; }
.mp-month-count {
	font-size: 11px;
	color: #6f7f90;
	font-weight: 600;
}
.client-toggle-icon {
	width: 28px;
	height: 28px;
	border-radius: 8px;
	background: #1f5f8b;
	color: #fff;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	flex: 0 0 28px;
}
.client-header-row.is-open .client-toggle-icon { background: #1f9d6a; }
.client-month-row td { background: #fcfdff !important; }
.client-month-row:hover td { background: #f7fbff !important; }
.month-cell { padding-left: 58px !important; }
.mp-month-chip {
	display: inline-block;
	background: #eef5fc;
	border: 1px solid #d5e5f4;
	color: #245f8a;
	border-radius: 999px;
	padding: 4px 10px;
	font-size: 12px;
	font-weight: 700;
}
.date-cell, .num-cell { text-align: center; }
.mp-date { color: #4a5b6b; font-weight: 600; }
.mp-muted { color: #c0c8d0; font-weight: 600; }
.mp-hours {
	display: inline-block;
	min-width: 54px;
	padding: 4px 10px;
	border-radius: 999px;
	background: #f3f6fa;
	color: #5a6a7a;
	font-weight: 700;
}
.mp-hours.has-value { background: #e7f7ee; color: #178258; }
.mp-hours.is-total { background: #e4eef8; color: #1f5f8b; }
.mp-empty-state {
	padding: 52px 16px !important;
	text-align: center;
	color: #6c757d;
}
.mp-empty-state i { display: block; font-size: 30px; margin-bottom: 8px; color: #adb5bd; }
.mp-empty-state strong { display: block; font-size: 16px; margin-bottom: 4px; color: #4a5b6b; }
.mp-page-loader {
	position: fixed;
	inset: 0;
	background: rgba(16, 32, 48, .28);
	z-index: 9999;
	display: flex;
	align-items: center;
	justify-content: center;
}
.mp-page-loader-content {
	background: #fff;
	border-radius: 16px;
	padding: 18px 22px;
	display: flex;
	align-items: center;
	gap: 12px;
	box-shadow: 0 16px 40px rgba(0,0,0,.16);
	color: #17364f;
}
.mp-page-loader-content span { display: block; font-size: 12px; color: #6c7a89; }
.mp-page-loader-spinner {
	width: 22px;
	height: 22px;
	border: 3px solid #dbe7f3;
	border-top-color: #1f5f8b;
	border-radius: 50%;
	animation: mpspin .7s linear infinite;
}
@keyframes mpspin { to { transform: rotate(360deg); } }
#management_plan_search_form .select2-container .select2-selection--multiple,
#management_plan_search_form .select2-container .select2-selection--single {
	min-height: 38px;
	border-color: #d5dbe3;
	border-radius: 10px;
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
@media (max-width: 1100px) {
	.mp-summary-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 992px) {
	.mp-hero { display: block; }
	.mp-hero-actions { margin-top: 14px; }
	.month-cell { padding-left: 22px !important; }
}
@media (max-width: 640px) {
	.mp-summary-row { grid-template-columns: 1fr; }
}
</style>

<script>
$(document).ready(function() {
	var monthRowsUrl = <?php echo json_encode($monthRowsUrl); ?>;
	var exportUrl = <?php echo json_encode($exportUrl); ?>;
	var loadingClientIds = {};

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

	function hoursCell(value, raw, isTotal) {
		var cls = 'mp-hours';
		if (parseFloat(raw) > 0) {
			cls += ' has-value';
		}
		if (isTotal) {
			cls += ' is-total';
		}
		return '<span class="' + cls + '">' + $('<div/>').text(value || '0').html() + '</span>';
	}

	function dateCell(value) {
		if (!value) {
			return '<span class="mp-muted">-</span>';
		}
		return '<span class="mp-date">' + $('<div/>').text(value).html() + '</span>';
	}

	function monthRowHtml(clientIndex, clientName, month) {
		var search = (String(clientName || '') + ' ' + String(month.month_label || '')).toLowerCase();
		return '<tr class="client-month-row client-months-' + clientIndex + '" data-client-index="' + clientIndex + '" data-search="' + $('<div/>').text(search).html() + '">' +
			'<td class="mp-col-sno"></td>' +
			'<td class="month-cell"><span class="mp-month-chip">' + $('<div/>').text(month.month_label || 'N/A').html() + '</span></td>' +
			'<td class="date-cell">' + dateCell(month.start_date) + '</td>' +
			'<td class="date-cell">' + dateCell(month.end_date) + '</td>' +
			'<td class="date-cell">' + dateCell(month.timesheet_date) + '</td>' +
			'<td class="num-cell">' + hoursCell(month.timesheet_hours, month.timesheet_hours_raw, false) + '</td>' +
			'<td class="num-cell">' + hoursCell(month.invoice_hours, month.invoice_hours_raw, false) + '</td>' +
		'</tr>';
	}

	function emptyMonthRowHtml(clientIndex) {
		return '<tr class="client-month-row client-months-' + clientIndex + '" data-client-index="' + clientIndex + '">' +
			'<td></td><td class="month-cell" colspan="6"><span class="mp-muted">No month-wise data for this period</span></td>' +
		'</tr>';
	}

	function renderMonths($header, months) {
		var clientIndex = $header.data('client-index');
		var clientName = $.trim($header.find('.client-name-text').text());
		$header.nextUntil('.client-header-row', '.client-month-row').remove();
		var html = '';
		if (!months || !months.length) {
			html = emptyMonthRowHtml(clientIndex);
			$header.find('.mp-month-count').text('No months');
		} else {
			for (var i = 0; i < months.length; i++) {
				html += monthRowHtml(clientIndex, clientName, months[i]);
			}
			$header.find('.mp-month-count').text(months.length + (months.length === 1 ? ' month' : ' months'));
		}
		$header.after(html);
		$header.attr('data-loaded', '1');
	}

	function setOpenState($header, isOpen) {
		var clientIndex = $header.data('client-index');
		var $icon = $header.find('.client-toggle-icon i');
		var $months = $('.client-months-' + clientIndex);
		if (isOpen) {
			$months.show();
			$header.addClass('is-open');
			$icon.removeClass('fa-plus').addClass('fa-minus');
		} else {
			$months.hide();
			$header.removeClass('is-open');
			$icon.removeClass('fa-minus').addClass('fa-plus');
		}
	}

	function loadMonths(clientIds, done) {
		var payload = $('#management_plan_search_form').serializeArray();
		for (var i = 0; i < clientIds.length; i++) {
			payload.push({ name: 'expand_client_Id[]', value: clientIds[i] });
		}
		$.ajax({
			url: monthRowsUrl,
			type: 'POST',
			dataType: 'json',
			data: payload
		}).done(function(response) {
			done(response && response.months ? response.months : {});
		}).fail(function() {
			done({});
		});
	}

	function expandHeader($header, afterLoad) {
		var clientId = String($header.data('client-id') || '');
		if ($header.attr('data-loaded') === '1') {
			setOpenState($header, true);
			if (afterLoad) { afterLoad(); }
			return;
		}
		if (loadingClientIds[clientId]) {
			return;
		}
		loadingClientIds[clientId] = true;
		$header.addClass('is-loading');
		$header.find('.mp-month-count').text('Loading...');
		loadMonths([clientId], function(monthsByClient) {
			loadingClientIds[clientId] = false;
			$header.removeClass('is-loading');
			renderMonths($header, monthsByClient[clientId] || []);
			setOpenState($header, true);
			if (afterLoad) { afterLoad(); }
		});
	}

	$(document).on('click', '.client-header-row.is-expandable', function(e) {
		e.preventDefault();
		var $header = $(this);
		if ($header.hasClass('is-open')) {
			setOpenState($header, false);
			return;
		}
		expandHeader($header);
	});

	$('#mp_expand_all_btn').on('click', function() {
		var $headers = $('.client-header-row.is-expandable:visible');
		var unloadedIds = [];
		$headers.each(function() {
			var $header = $(this);
			var clientId = String($header.data('client-id') || '');
			if ($header.attr('data-loaded') === '1') {
				setOpenState($header, true);
			} else if (clientId !== '') {
				unloadedIds.push(clientId);
			}
		});
		if (!unloadedIds.length) {
			return;
		}
		$('#mp_page_loader').show();
		loadMonths(unloadedIds, function(monthsByClient) {
			$headers.each(function() {
				var $header = $(this);
				var clientId = String($header.data('client-id') || '');
				if ($header.attr('data-loaded') !== '1') {
					renderMonths($header, monthsByClient[clientId] || []);
				}
				setOpenState($header, true);
			});
			$('#mp_page_loader').hide();
		});
	});

	$('#mp_collapse_all_btn').on('click', function() {
		$('.client-header-row.is-expandable').each(function() {
			setOpenState($(this), false);
		});
	});

	$('#mp_quick_search').on('keyup', function() {
		var query = $.trim($(this).val()).toLowerCase();
		$('.client-header-row').each(function() {
			var $header = $(this);
			var match = query === '' || String($header.data('search') || '').indexOf(query) !== -1;
			$header.toggle(match);
			if (!match) {
				setOpenState($header, false);
			} else if ($header.hasClass('is-open')) {
				$('.client-months-' + $header.data('client-index')).show();
			}
		});
	});

	$('#mp_current_year_btn').on('click', function() {
		var year = String($(this).data('year') || '');
		$('#from_year').val(year).trigger('change');
		$('#to_year').val(year).trigger('change');
		$('#from_month').val('all').trigger('change');
		$('#to_month').val('all').trigger('change');
		$('#mp_page_loader').show();
		$('#management_plan_search_form').trigger('submit');
	});

	$('#management_plan_search_form').on('submit', function() {
		$('#mp_page_loader').show();
	});

	$('#mp_export_report_btn').on('click', function() {
		var $form = $('#management_plan_search_form');
		var originalAction = $form.attr('action');
		var originalTarget = $form.attr('target');
		$form.attr('action', exportUrl);
		$form.attr('target', 'mp_export_iframe');
		$form.trigger('submit');
		setTimeout(function() {
			$form.attr('action', originalAction);
			if (originalTarget) {
				$form.attr('target', originalTarget);
			} else {
				$form.removeAttr('target');
			}
			$('#mp_page_loader').hide();
		}, 800);
	});
});
</script>

<?php $this->load->view('includes/cRMFooter'); ?>
