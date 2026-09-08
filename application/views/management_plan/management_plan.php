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
			<div class="mp-hero-badge"><i class="fa fa-briefcase"></i></div>
			<div>
				<p class="mp-kicker">Reports</p>
				<h1>Management Plan</h1>
				<p>Client timelines, timesheet hours, and invoice hours in one view. Open a client to see month-wise details.</p>
			</div>
		</div>
	</div>

	<div class="mp-summary-row">
		<div class="mp-summary-card is-clients">
			<div class="mp-summary-icon"><i class="fa fa-users"></i></div>
			<div class="mp-summary-copy">
				<span>Clients</span>
				<strong><?php echo (int)$totalRecords; ?></strong>
			</div>
		</div>
		<div class="mp-summary-card is-timesheet">
			<div class="mp-summary-icon"><i class="fa fa-clock-o"></i></div>
			<div class="mp-summary-copy">
				<span>Timesheet Hours</span>
				<strong><?php echo htmlspecialchars(management_plan_hours_display($totalTimesheetHours), ENT_QUOTES); ?></strong>
			</div>
		</div>
		<div class="mp-summary-card is-invoice">
			<div class="mp-summary-icon"><i class="fa fa-file-text-o"></i></div>
			<div class="mp-summary-copy">
				<span>Invoice Hours</span>
				<strong><?php echo htmlspecialchars(management_plan_hours_display($totalInvoiceHours), ENT_QUOTES); ?></strong>
			</div>
		</div>
		<div class="mp-summary-card is-period">
			<div class="mp-summary-icon"><i class="fa fa-calendar"></i></div>
			<div class="mp-summary-copy">
				<span>Selected Period</span>
				<strong><?php echo htmlspecialchars($fromMonthLabel . ' ' . $fromYearLabel . ' - ' . $toMonthLabel . ' ' . $toYearLabel, ENT_QUOTES); ?></strong>
			</div>
		</div>
	</div>

	<div class="mp-panel mp-filter-panel">
		<div class="mp-panel-head">
			<div>
				<h3><i class="fa fa-filter"></i> Filters</h3>
				<small>Choose a client and period, then search</small>
			</div>
		</div>
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
					<a class="mp-btn mp-btn-ghost" href="<?php echo base_url('management_plan'); ?>"><i class="fa fa-refresh"></i> Reset</a>
				</div>
			</div>
		</form>
	</div>

	<div class="mp-panel mp-grid-panel">
		<div class="mp-table-toolbar">
			<div>
				<h3>Client Invoice Grid <span class="mp-count-badge"><?php echo (int)$totalRecords; ?></span></h3>
				<small>Click a client to view month-wise hours</small>
			</div>
			<div class="mp-toolbar-right">
				<button type="button" class="mp-btn mp-btn-ghost mp-btn-sm" id="mp_collapse_all_btn"><i class="fa fa-minus-square-o"></i> Collapse All</button>
				<button type="button" class="mp-btn mp-btn-ghost mp-btn-sm" id="mp_expand_all_btn"><i class="fa fa-plus-square-o"></i> Expand All</button>
				<button type="button" class="mp-btn mp-btn-success mp-btn-sm" id="mp_export_report_btn">
					<i class="fa fa-download"></i> Export Excel
				</button>
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
								$clientInitial = management_plan_initial($clientName);
								$avatarPalette = array('#1d4ed8', '#0f766e', '#b45309', '#6d28d9', '#be123c', '#0369a1');
								$avatarBg = $avatarPalette[ord($clientInitial) % 6];
							?>
							<tr class="client-header-row is-expandable" data-client-index="<?php echo $clientIndex; ?>" data-client-id="<?php echo $clientId; ?>" data-search="<?php echo htmlspecialchars($rowSearch, ENT_QUOTES); ?>" data-loaded="0">
								<td class="mp-col-sno"><?php echo $i; ?></td>
								<td class="client-cell">
									<span class="mp-client-avatar" style="background:<?php echo $avatarBg; ?>"><?php echo htmlspecialchars($clientInitial, ENT_QUOTES); ?></span>
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
.mp-page {
	padding-bottom: 36px;
	font-size: 15px;
	color: #1e3348;
}
.mp-hero {
	position: relative;
	overflow: hidden;
	display: flex;
	justify-content: space-between;
	align-items: center;
	gap: 18px;
	margin-bottom: 18px;
	padding: 26px 28px;
	border-radius: 22px;
	background: linear-gradient(135deg, #0b2a4a 0%, #155a86 48%, #12807c 100%);
	color: #fff;
	box-shadow: 0 18px 36px rgba(11, 42, 74, 0.22);
}
.mp-hero:before,
.mp-hero:after {
	content: "";
	position: absolute;
	border-radius: 50%;
	pointer-events: none;
}
.mp-hero:before {
	width: 220px;
	height: 220px;
	right: -50px;
	top: -80px;
	background: rgba(255,255,255,.08);
}
.mp-hero:after {
	width: 140px;
	height: 140px;
	right: 120px;
	bottom: -70px;
	background: rgba(16, 185, 129, .16);
}
.mp-hero-copy {
	position: relative;
	z-index: 1;
	display: flex;
	align-items: center;
	gap: 16px;
}
.mp-hero-badge {
	width: 58px;
	height: 58px;
	border-radius: 16px;
	background: rgba(255,255,255,.14);
	border: 1px solid rgba(255,255,255,.18);
	display: inline-flex;
	align-items: center;
	justify-content: center;
	font-size: 24px;
	flex: 0 0 58px;
	box-shadow: inset 0 1px 0 rgba(255,255,255,.2);
}
.mp-kicker {
	margin: 0 0 4px;
	text-transform: uppercase;
	letter-spacing: .14em;
	font-size: 12px;
	font-weight: 700;
	opacity: .72;
}
.mp-hero h1 {
	margin: 0 0 6px;
	font-size: 32px;
	font-weight: 800;
	letter-spacing: -0.3px;
}
.mp-hero p { margin: 0; max-width: 680px; opacity: .9; font-size: 15px; line-height: 1.55; }
.mp-hero-actions {
	position: relative;
	z-index: 1;
	display: flex;
	gap: 8px;
	flex-wrap: wrap;
}
.mp-btn {
	border: 0;
	border-radius: 12px;
	font-weight: 700;
	padding: 10px 16px;
	display: inline-flex;
	align-items: center;
	gap: 7px;
	cursor: pointer;
	text-decoration: none !important;
	line-height: 1.2;
	font-size: 15px;
	transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
}
.mp-btn:hover { transform: translateY(-1px); }
.mp-btn-sm { padding: 8px 13px; font-size: 14px; }
.mp-btn-primary { background: #0f4c75; color: #fff; box-shadow: 0 8px 16px rgba(15, 76, 117, .18); }
.mp-btn-primary:hover { background: #0b3b5c; color: #fff; }
.mp-btn-success { background: #10946d; color: #fff; box-shadow: 0 8px 16px rgba(16, 148, 109, .22); }
.mp-btn-success:hover { background: #0c7a5a; color: #fff; }
.mp-btn-ghost {
	background: rgba(255,255,255,.14);
	color: #fff;
	border: 1px solid rgba(255,255,255,.2);
}
.mp-btn-ghost:hover { color: #fff; background: rgba(255,255,255,.22); }
.mp-panel .mp-btn-ghost,
.mp-toolbar-right .mp-btn-ghost {
	background: #fff;
	color: #31546f;
	border: 1px solid #d7e2ec;
	box-shadow: 0 1px 2px rgba(15, 48, 80, .04);
}
.mp-panel .mp-btn-ghost:hover,
.mp-toolbar-right .mp-btn-ghost:hover {
	background: #f4f8fc;
	color: #17364f;
}
.mp-summary-row {
	display: grid;
	grid-template-columns: repeat(4, minmax(0, 1fr));
	gap: 14px;
	margin-bottom: 16px;
}
.mp-summary-card {
	background: #fff;
	border: 1px solid #e4edf5;
	border-radius: 18px;
	padding: 16px 18px;
	display: flex;
	align-items: center;
	gap: 14px;
	box-shadow: 0 8px 22px rgba(15, 48, 80, 0.06);
	min-width: 0;
	position: relative;
	overflow: hidden;
	transition: transform .18s ease, box-shadow .18s ease;
}
.mp-summary-card:hover {
	transform: translateY(-2px);
	box-shadow: 0 14px 28px rgba(15, 48, 80, 0.1);
}
.mp-summary-card:before {
	content: "";
	position: absolute;
	left: 0;
	top: 0;
	bottom: 0;
	width: 5px;
}
.is-clients:before { background: #2563eb; }
.is-timesheet:before { background: #10946d; }
.is-invoice:before { background: #d97706; }
.is-period:before { background: #7c3aed; }
.mp-summary-copy { min-width: 0; }
.mp-summary-card span {
	display: block;
	font-size: 12px;
	font-weight: 800;
	letter-spacing: .06em;
	text-transform: uppercase;
	color: #7a8896;
	margin-bottom: 4px;
}
.mp-summary-card strong {
	display: block;
	font-size: 26px;
	color: #10263b;
	line-height: 1.2;
	word-break: break-word;
}
.mp-summary-card.is-period strong { font-size: 16px; }
.mp-summary-icon {
	width: 46px;
	height: 46px;
	border-radius: 14px;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	flex: 0 0 46px;
	color: #fff;
	font-size: 18px;
	box-shadow: 0 8px 16px rgba(16, 38, 59, .12);
}
.is-clients .mp-summary-icon { background: linear-gradient(180deg, #3b82f6, #1d4ed8); }
.is-timesheet .mp-summary-icon { background: linear-gradient(180deg, #14b889, #0f766e); }
.is-invoice .mp-summary-icon { background: linear-gradient(180deg, #f59e0b, #c2410c); }
.is-period .mp-summary-icon { background: linear-gradient(180deg, #8b5cf6, #6d28d9); }
.mp-panel {
	background: #fff;
	border: 1px solid #e4edf5;
	border-radius: 20px;
	box-shadow: 0 10px 28px rgba(15, 48, 80, 0.06);
	margin-bottom: 16px;
	overflow: hidden;
}
.mp-panel-head,
.mp-table-toolbar {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 12px;
	padding: 16px 20px;
	background: linear-gradient(180deg, #f7fbff 0%, #fff 100%);
	border-bottom: 1px solid #e8eef5;
	flex-wrap: wrap;
}
.mp-panel-head h3,
.mp-table-toolbar h3 {
	margin: 0 0 3px;
	font-size: 18px;
	font-weight: 800;
	color: #10263b;
}
.mp-panel-head h3 i { margin-right: 6px; color: #1d6ea8; }
.mp-panel-head small,
.mp-table-toolbar small { color: #7a8896; font-size: 14px; }
.mp-count-badge {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	min-width: 28px;
	height: 24px;
	padding: 0 8px;
	margin-left: 8px;
	border-radius: 999px;
	background: #e8f3fb;
	color: #155a86;
	font-size: 13px;
	vertical-align: middle;
}
.mp-filter-grid {
	display: flex;
	flex-wrap: wrap;
	gap: 12px;
	align-items: flex-end;
	padding: 16px 20px 18px;
	background: #fbfcfe;
}
.mp-filter-client { flex: 1 1 240px; min-width: 220px; }
.mp-filter-grid label {
	display: block;
	font-size: 12px;
	font-weight: 800;
	text-transform: uppercase;
	letter-spacing: .05em;
	color: #667888;
	margin-bottom: 6px;
}
.mp-ym-panel { flex: 1 1 230px; min-width: 230px; }
.mp-ym-fields { display: flex; gap: 8px; }
.mp-ym-fields select { flex: 1; }
.mp-filter-actions { display: flex; gap: 8px; padding-bottom: 2px; }
.mp-panel .form-control {
	border-radius: 12px;
	border-color: #d5dbe3;
	height: 44px;
	font-size: 15px;
	box-shadow: none;
	background: #fff;
}
.mp-toolbar-right {
	display: flex;
	align-items: center;
	gap: 8px;
	flex-wrap: wrap;
}
.mp-table-wrap { overflow: auto; max-height: calc(100vh - 260px); background: #fff; }
#management_plan_table {
	width: 100%;
	margin-bottom: 0;
	border-collapse: separate;
	border-spacing: 0;
}
#management_plan_table thead th {
	background: linear-gradient(180deg, #16324d, #10263b);
	color: #fff;
	font-weight: 800;
	font-size: 13px;
	text-transform: uppercase;
	letter-spacing: .05em;
	padding: 14px 12px;
	border: 0;
	text-align: center;
	vertical-align: middle;
	white-space: nowrap;
	position: sticky;
	top: 0;
	z-index: 2;
}
#management_plan_table thead th.mp-col-client { text-align: left; }
#management_plan_table tbody td {
	padding: 14px 12px;
	vertical-align: middle;
	font-size: 15px;
	color: #2c3e50;
	border-bottom: 1px solid #eef3f8;
	background: #fff;
}
#management_plan_table .mp-col-sno { text-align: center; width: 54px; color: #8a97a5; font-weight: 700; }
#management_plan_table .mp-col-client { min-width: 300px; text-align: left; }
.client-header-row td { background: #f8fbfe !important; }
.client-header-row.is-expandable { cursor: pointer; }
.client-header-row.is-expandable:hover td { background: #eef6fd !important; }
.client-header-row.is-open td { background: #eaf4ff !important; }
.client-header-row.is-open td:first-child {
	box-shadow: inset 4px 0 0 #10946d;
}
.client-header-row.is-loading td { opacity: .75; }
.client-cell {
	display: flex;
	align-items: center;
	gap: 12px;
	color: #10263b;
	font-size: 16px;
}
.mp-client-avatar {
	width: 38px;
	height: 38px;
	border-radius: 12px;
	color: #fff;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	font-size: 15px;
	font-weight: 800;
	flex: 0 0 38px;
	box-shadow: 0 6px 12px rgba(16, 38, 59, .16);
}
.mp-client-copy {
	display: flex;
	flex-direction: column;
	min-width: 0;
	flex: 1;
	line-height: 1.25;
}
.client-name-text { color: #10263b; font-weight: 800; }
.mp-month-count {
	font-size: 13px;
	color: #6f7f90;
	font-weight: 600;
}
.client-toggle-icon {
	width: 30px;
	height: 30px;
	border-radius: 9px;
	background: #155a86;
	color: #fff;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	flex: 0 0 30px;
	box-shadow: 0 6px 12px rgba(21, 90, 134, .2);
}
.client-header-row.is-open .client-toggle-icon { background: #10946d; }
.client-month-row td {
	background: #f7fafc !important;
	border-bottom: 1px dashed #e4edf5 !important;
}
.client-month-row:hover td { background: #eef7f4 !important; }
.month-cell {
	position: relative;
	padding-left: 64px !important;
}
.month-cell:before {
	content: "";
	position: absolute;
	left: 30px;
	top: 50%;
	width: 16px;
	height: 2px;
	background: #c9d8e6;
	border-radius: 2px;
}
.mp-month-chip {
	display: inline-block;
	background: #e8f4ff;
	border: 1px solid #cfe4f7;
	color: #155a86;
	border-radius: 999px;
	padding: 5px 12px;
	font-size: 14px;
	font-weight: 800;
}
.date-cell, .num-cell { text-align: center; }
.mp-date {
	display: inline-block;
	color: #3d5366;
	font-weight: 700;
	font-size: 15px;
	background: #f4f7fb;
	border: 1px solid #e6edf4;
	border-radius: 999px;
	padding: 5px 10px;
}
.mp-muted { color: #c0c8d0; font-weight: 700; font-size: 15px; }
.mp-hours {
	display: inline-block;
	min-width: 58px;
	padding: 5px 12px;
	border-radius: 999px;
	background: #f3f6fa;
	color: #5a6a7a;
	font-weight: 800;
	font-size: 15px;
}
.mp-hours.has-value { background: #e5f8ef; color: #0f766e; }
.mp-hours.is-total { background: #e4eef8; color: #155a86; }
.mp-empty-state {
	padding: 56px 16px !important;
	text-align: center;
	color: #6c757d;
	background: #fbfcfe !important;
}
.mp-empty-state i { display: block; font-size: 34px; margin-bottom: 10px; color: #b7c3cf; }
.mp-empty-state strong { display: block; font-size: 18px; margin-bottom: 4px; color: #4a5b6b; }
.mp-empty-state span { font-size: 15px; }
.mp-page-loader {
	position: fixed;
	inset: 0;
	background: rgba(10, 24, 40, .32);
	z-index: 9999;
	display: flex;
	align-items: center;
	justify-content: center;
	backdrop-filter: blur(2px);
}
.mp-page-loader-content {
	background: #fff;
	border-radius: 18px;
	padding: 18px 22px;
	display: flex;
	align-items: center;
	gap: 12px;
	box-shadow: 0 18px 40px rgba(0,0,0,.18);
	color: #10263b;
	font-size: 16px;
}
.mp-page-loader-content span { display: block; font-size: 14px; color: #6c7a89; }
.mp-page-loader-spinner {
	width: 22px;
	height: 22px;
	border: 3px solid #dbe7f3;
	border-top-color: #155a86;
	border-radius: 50%;
	animation: mpspin .7s linear infinite;
}
@keyframes mpspin { to { transform: rotate(360deg); } }
#management_plan_search_form .select2-container .select2-selection--multiple,
#management_plan_search_form .select2-container .select2-selection--single {
	min-height: 44px;
	border-color: #d5dbe3;
	border-radius: 12px;
	font-size: 15px;
	background: #fff;
}
#management_plan_search_form .select2-container .select2-selection__rendered {
	font-size: 15px;
}
#management_plan_search_form .select2-container .select2-selection--single .select2-selection__rendered {
	line-height: 42px;
}
#management_plan_search_form .select2-container .select2-selection--multiple .select2-selection__rendered {
	line-height: 28px;
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
	.mp-hero,
	.mp-hero-copy { display: block; }
	.mp-hero-badge { margin-bottom: 12px; }
	.mp-hero-actions { margin-top: 14px; }
	.month-cell { padding-left: 22px !important; }
	.month-cell:before { display: none; }
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
