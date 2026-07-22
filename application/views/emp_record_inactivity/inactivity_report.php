<!-- Include Header -->
<?php $this->load->view('includes/cRMHeader');

$fromDate = isset($filters['from_date']) ? $filters['from_date'] : date('Y-m-01', strtotime('-6 months'));
$toDate = isset($filters['to_date']) ? $filters['to_date'] : date('Y-m-t');
$fromYear = isset($filters['from_year']) ? $filters['from_year'] : 'ALL';
$fromMonth = (isset($filters['from_month']) && $filters['from_month'] !== '') ? (int)$filters['from_month'] : 0;
$toYear = isset($filters['to_year']) ? $filters['to_year'] : 'ALL';
$toMonth = (isset($filters['to_month']) && $filters['to_month'] !== '') ? (int)$filters['to_month'] : 0;
$fromYearIsAll = (strtoupper((string)$fromYear) === 'ALL');
$toYearIsAll = (strtoupper((string)$toYear) === 'ALL');
if ($fromYearIsAll) {
    $fromMonth = 0;
}
if ($toYearIsAll) {
    $toMonth = 0;
}
$eriStartYear = 2010;
$eriEndYear = (int)date('Y');
$eriMonthLabels = array(
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
);
$selectedDepartment = isset($filters['department']) ? $filters['department'] : '';
$selectedClientId = isset($filters['client_Id']) ? $filters['client_Id'] : '';
$selectedProjectId = isset($filters['project_Id']) ? $filters['project_Id'] : '';
$selectedProjectStatus = isset($filters['project_status']) ? $filters['project_status'] : '';
$clients = isset($clients) ? $clients : array();
$projects = isset($projects) ? $projects : array();
$departments = isset($departments) ? $departments : array();
$projectStatuses = isset($projectStatuses) ? $projectStatuses : array();
$records = isset($records) ? $records : array();
$canCloseProjects = !empty($canCloseProjects);
$totalRecords = count($records);

if (!function_exists('format_inactivity_project_date')) {
    function format_inactivity_project_date($dateValue) {
        if (empty($dateValue) || $dateValue === '0000-00-00' || $dateValue === '0000-00-00 00:00:00') {
            return '—';
        }
        $timestamp = strtotime($dateValue);
        return ($timestamp !== false) ? date('d-M-Y', $timestamp) : '—';
    }
}

if (!function_exists('eri_project_status_class')) {
    function eri_project_status_class($status) {
        $key = strtolower(trim((string)$status));
        if ($key === 'process') {
            return 'eri-badge eri-badge-process';
        }
        if ($key === 'closed') {
            return 'eri-badge eri-badge-closed';
        }
        if (strpos($key, 'hold') !== false) {
            return 'eri-badge eri-badge-hold';
        }
        return 'eri-badge eri-badge-default';
    }
}

if (!function_exists('eri_project_status_label')) {
    function eri_project_status_label($status) {
        $key = strtolower(trim((string)$status));
        if ($key === 'process') {
            return 'In Process';
        }
        return (string)$status;
    }
}
?>

<style>
.eri-page-title h1 {
    margin-bottom: 4px;
}
.eri-page-title .eri-subtitle {
    color: #6c757d;
    font-size: 14px;
    margin: 0;
}
.eri-table-card {
    border: 1px solid #e3e8ef;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(31, 80, 118, 0.07);
    overflow: hidden;
}
.eri-table-card .card-body {
    padding: 0;
}
.eri-table-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    background: #f8fafc;
    border-bottom: 1px solid #e8edf3;
}
.eri-table-toolbar h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #1f5076;
}
.eri-table-wrap {
    padding: 0 12px 12px;
}
#inactivityReportTable {
    width: 100% !important;
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}
#inactivityReportTable thead th {
    background: linear-gradient(to bottom, #337ab7, #2c5aa0);
    color: #fff;
    font-weight: 700;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.35px;
    padding: 11px 10px;
    border-color: #2c5aa0 !important;
    vertical-align: middle;
    white-space: nowrap;
    line-height: 1.3;
}
#inactivityReportTable tbody td {
    padding: 10px;
    vertical-align: middle;
    font-size: 13px;
    color: #2c3e50;
    border-color: #e8edf3 !important;
    background: #fff;
}
#inactivityReportTable tbody tr:nth-child(even) td {
    background: #f9fbfd;
}
#inactivityReportTable tbody tr:hover td {
    background: #eef4fb;
}
#inactivityReportTable .eri-col-sno,
#inactivityReportTable .eri-col-empid,
#inactivityReportTable .eri-col-days {
    text-align: center;
    white-space: nowrap;
}
#inactivityReportTable .eri-col-date {
    text-align: center;
    white-space: nowrap;
    min-width: 108px;
}
#inactivityReportTable .eri-col-project {
    min-width: 180px;
    max-width: 280px;
}
#inactivityReportTable .eri-col-client {
    min-width: 130px;
    max-width: 200px;
}
#inactivityReportTable .eri-col-project .eri-cell-inner,
#inactivityReportTable .eri-col-client .eri-cell-inner {
    display: flex;
    align-items: baseline;
    min-width: 0;
    max-width: 100%;
}
#inactivityReportTable .eri-col-project .eri-cell-text,
#inactivityReportTable .eri-col-client .eri-cell-text {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    min-width: 0;
    flex: 1 1 auto;
}
#inactivityReportTable .eri-col-project .eri-cell-id {
    flex: 0 0 auto;
    white-space: nowrap;
    margin-left: 4px;
}
#inactivityReportTable .eri-col-employee {
    font-weight: 600;
    color: #1f5076;
    white-space: nowrap;
}
#inactivityReportTable .eri-col-manager {
    white-space: nowrap;
}
.eri-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    white-space: nowrap;
}
.eri-badge-process { background: #d4edda; color: #155724; }
.eri-badge-closed { background: #f8d7da; color: #721c24; }
.eri-badge-hold { background: #fff3cd; color: #856404; }
.eri-badge-default { background: #e9ecef; color: #495057; }
.eri-badge-never { background: #ffe8cc; color: #9a5200; }
.eri-days-pill {
    display: inline-block;
    min-width: 42px;
    padding: 4px 8px;
    border-radius: 999px;
    font-weight: 700;
    text-align: center;
    font-size: 12px;
}
.eri-days-low { background: #fff3cd; color: #856404; }
.eri-days-mid { background: #ffe0b2; color: #bf360c; }
.eri-days-high { background: #ffcdd2; color: #b71c1c; }
.eri-empty-state {
    padding: 36px 16px !important;
    text-align: center;
    color: #6c757d;
    font-size: 15px;
}
.eri-empty-state i {
    display: block;
    font-size: 28px;
    margin-bottom: 8px;
    color: #adb5bd;
}
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    padding: 12px 6px 0;
}
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    border-radius: 6px;
    border: 1px solid #d5dbe3;
    padding: 4px 8px;
}
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    padding: 10px 6px 14px;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 4px !important;
}
.eri-close-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.eri-close-actions .btn-danger {
    border-radius: 6px;
    font-weight: 700;
}
.eri-col-select {
    width: 36px;
    text-align: center;
    white-space: nowrap;
}
.eri-status-toggle {
    cursor: pointer;
}
.eri-status-toggle:hover {
    opacity: 0.85;
    box-shadow: 0 0 0 1px rgba(51, 122, 183, 0.35);
}
.eri-filter-card {
    border: 1px solid #e3e8ef;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(31, 80, 118, 0.06);
    margin-bottom: 18px;
}
.eri-filter-card .card-body {
    padding: 18px 20px 8px;
}
.eri-filter-title {
    font-size: 15px;
    font-weight: 700;
    color: #1f5076;
    margin: 0 0 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid #edf1f5;
}
.eri-filter-title i {
    margin-right: 8px;
    color: #337ab7;
}
.eri-filter-card .control-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #5a6a7a;
}
.eri-filter-card .form-control {
    border-radius: 6px;
    border-color: #d5dbe3;
    height: 36px;
}
.eri-filter-actions {
    padding-top: 4px;
}
.eri-filter-actions .btn {
    border-radius: 6px;
    min-width: 96px;
}
.eri-filter-card .eri-dates-actions-row {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: 12px;
    margin-top: 4px;
}
.eri-filter-card .eri-dates-compact {
    flex: 1 1 auto;
}
.eri-filter-card .kpi-ym-range-panels {
    display: flex;
    align-items: stretch;
    flex-wrap: wrap;
    gap: 12px;
}
.eri-filter-card .kpi-ym-panel {
    background: #ffffff;
    border: 1px solid #d8dee6;
    border-radius: 10px;
    padding: 10px 14px 12px;
    min-width: 280px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}
.eri-filter-card .kpi-ym-panel-title {
    font-weight: 600;
    color: #333;
    font-size: 14px;
    line-height: 1.2;
    margin-bottom: 10px;
}
.eri-filter-card .kpi-ym-panel-fields {
    display: flex;
    align-items: center;
    gap: 10px;
}
.eri-filter-card .kpi-ym-select-wrap {
    position: relative;
    flex: 0 0 auto;
}
.eri-filter-card .kpi-ym-select {
    appearance: none;
    -webkit-appearance: none;
    background-color: #ffffff;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23666' d='M1.41 0L6 4.58 10.59 0 12 1.41l-6 6-6-6z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 12px 8px;
    border: 1px solid #cfd6df;
    color: #4a5568;
    font-weight: 500;
    font-size: 14px;
    height: 38px;
    padding: 6px 32px 6px 12px;
    border-radius: 8px;
    min-width: 128px;
}
.eri-filter-card .kpi-ym-year-select { min-width: 110px; }
.eri-filter-card .kpi-ym-month-select { min-width: 140px; }
.eri-filter-card .kpi-ym-select-wrap.kpi-ym-wrap-selected {
    background-color: #673ab7;
    border-radius: 8px;
    border: 2px solid #e2e2e2;
}
.eri-filter-card .kpi-ym-select-wrap.kpi-ym-wrap-selected .kpi-ym-select {
    background-color: transparent !important;
    border-color: transparent !important;
    color: #fff !important;
    font-weight: 600;
    padding-right: 52px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23ffffff' d='M1.41 0L6 4.58 10.59 0 12 1.41l-6 6-6-6z'/%3E%3C/svg%3E");
}
.eri-filter-card .kpi-ym-clear-icon {
    display: none;
    position: absolute;
    right: 28px;
    top: 50%;
    transform: translateY(-50%);
    color: #fff;
    font-size: 18px;
    line-height: 1;
    cursor: pointer;
    z-index: 2;
    font-weight: 700;
}
.eri-filter-card .kpi-ym-select-wrap.kpi-ym-wrap-selected .kpi-ym-clear-icon {
    display: block;
}
.eri-filter-card .select2-container .select2-selection--single {
    height: 38px;
    border: 1px solid #cfd6df;
    border-radius: 8px;
}
.eri-filter-card .select2-container .select2-selection__rendered {
    line-height: 36px;
    padding-left: 12px;
}
.eri-filter-card .select2-container.cr-ym-selected-bg .select2-selection--single {
    background-color: #673ab7 !important;
    border-color: #673ab7 !important;
    color: #fff;
}
.eri-filter-card .select2-container.cr-ym-selected-bg .select2-selection__rendered {
    color: #fff !important;
    font-weight: 600;
}
.eri-filter-card .select2-container.cr-ym-selected-bg .select2-selection__arrow b {
    border-color: #fff transparent transparent transparent;
}
</style>

<div class="content-wrapper">
    <div class="page-title eri-page-title">
        <div>
            <h1><i class="fa fa-exclamation-triangle"></i> Timesheet Inactivity Report</h1>
            <p class="eri-subtitle">Projects with no timesheet log from <?php echo date('M Y', strtotime($fromDate)); ?> to <?php echo date('M Y', strtotime($toDate)); ?><?php echo ($fromYearIsAll || $toYearIsAll) ? ' (All = last 6 months)' : ''; ?>.</p>
        </div>
        <div>
            <a class="btn btn-info btn-flat" href="<?php echo base_url('emp_record_inactivity'); ?>" data-toggle="tooltip" title="Refresh">
                <i class="fa fa-lg fa-refresh"></i>
            </a>
        </div>
    </div>

    <div class="card eri-filter-card">
        <div class="card-body">
            <h4 class="eri-filter-title"><i class="fa fa-filter"></i> Search Filters</h4>
            <form method="post" action="<?php echo base_url('emp_record_inactivity/search'); ?>" id="eri_filter_form">
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Department</label>
                            <select name="department" id="department" class="form-control">
                                <option value="">All Departments</option>
                                <?php foreach ($departments as $dept): ?>
                                    <?php if (empty($dept->department)) continue; ?>
                                    <option value="<?php echo htmlspecialchars($dept->department, ENT_QUOTES, 'UTF-8'); ?>"
                                        <?php echo $selectedDepartment === $dept->department ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dept->department, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Client</label>
                            <select name="client_Id" id="client_Id" class="form-control">
                                <option value="">All Clients</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?php echo (int)$client->client_Id; ?>"
                                        <?php echo (string)$selectedClientId === (string)$client->client_Id ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($client->client_name, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Project</label>
                            <select name="project_Id" id="project_Id" class="form-control">
                                <option value="">All Projects</option>
                                <?php foreach ($projects as $project): ?>
                                    <option value="<?php echo (int)$project->project_Id; ?>"
                                        <?php echo (string)$selectedProjectId === (string)$project->project_Id ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($project->project_name, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Status</label>
                            <select name="project_status" id="project_status" class="form-control">
                                <option value="">All Status</option>
                                <?php foreach ($projectStatuses as $statusRow): ?>
                                    <?php if (empty($statusRow->project_status)) continue; ?>
                                    <?php
                                        $statusValue = trim((string)$statusRow->project_status);
                                        $statusLabel = eri_project_status_label($statusValue);
                                    ?>
                                    <option value="<?php echo htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8'); ?>"
                                        <?php echo $selectedProjectStatus === $statusValue ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="eri-dates-actions-row">
                    <div class="eri-dates-compact">
                        <div class="kpi-ym-range-panels">
                            <div class="kpi-ym-panel">
                                <div class="kpi-ym-panel-title">From</div>
                                <div class="kpi-ym-panel-fields">
                                    <div class="kpi-ym-select-wrap">
                                        <select name="from_year" id="from_year" class="form-control kpi-ym-select kpi-ym-year-select kpi-ym-clearable" title="From year">
                                            <option value="">Year</option>
                                            <option value="ALL" <?php echo $fromYearIsAll ? 'selected' : ''; ?>>All</option>
                                            <?php for ($y = $eriEndYear; $y >= $eriStartYear; $y--): ?>
                                                <option value="<?php echo $y; ?>" <?php echo (!$fromYearIsAll && (string)$fromYear === (string)$y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                        <span class="kpi-ym-clear-icon" onclick="clearEriYmSelect('from_year')">&times;</span>
                                    </div>
                                    <div class="kpi-ym-select-wrap">
                                        <select name="from_month" id="from_month" class="form-control kpi-ym-select kpi-ym-month-select kpi-ym-clearable" title="From month" <?php echo $fromYearIsAll ? 'disabled' : ''; ?>>
                                            <option value="">Month</option>
                                            <?php foreach ($eriMonthLabels as $num => $label): ?>
                                                <option value="<?php echo $num; ?>" <?php echo ($fromMonth > 0 && (int)$num === $fromMonth) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="kpi-ym-clear-icon" onclick="clearEriYmSelect('from_month')">&times;</span>
                                    </div>
                                </div>
                            </div>
                            <div class="kpi-ym-panel">
                                <div class="kpi-ym-panel-title">To</div>
                                <div class="kpi-ym-panel-fields">
                                    <div class="kpi-ym-select-wrap">
                                        <select name="to_year" id="to_year" class="form-control kpi-ym-select kpi-ym-year-select kpi-ym-clearable" title="To year">
                                            <option value="">Year</option>
                                            <option value="ALL" <?php echo $toYearIsAll ? 'selected' : ''; ?>>All</option>
                                            <?php for ($y = $eriEndYear; $y >= $eriStartYear; $y--): ?>
                                                <option value="<?php echo $y; ?>" <?php echo (!$toYearIsAll && (string)$toYear === (string)$y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                        <span class="kpi-ym-clear-icon" onclick="clearEriYmSelect('to_year')">&times;</span>
                                    </div>
                                    <div class="kpi-ym-select-wrap">
                                        <select name="to_month" id="to_month" class="form-control kpi-ym-select kpi-ym-month-select kpi-ym-clearable" title="To month" <?php echo $toYearIsAll ? 'disabled' : ''; ?>>
                                            <option value="">Month</option>
                                            <?php foreach ($eriMonthLabels as $num => $label): ?>
                                                <option value="<?php echo $num; ?>" <?php echo ($toMonth > 0 && (int)$num === $toMonth) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="kpi-ym-clear-icon" onclick="clearEriYmSelect('to_month')">&times;</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="eri-filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <a href="<?php echo base_url('emp_record_inactivity'); ?>" class="btn btn-default">
                            <i class="fa fa-refresh"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card eri-table-card">
        <div class="eri-table-toolbar">
            <h3><i class="fa fa-table"></i> Inactivity Grid</h3>
            <div class="eri-close-actions">
                <span class="text-muted"><?php echo (int)$totalRecords; ?> record<?php echo $totalRecords === 1 ? '' : 's'; ?> found</span>
                <?php if ($canCloseProjects): ?>
                    <button type="button" class="btn btn-danger btn-sm" id="eri_close_selected_btn" disabled>
                        <i class="fa fa-times-circle"></i> Close Selected
                    </button>
                    <button type="button" class="btn btn-success btn-sm" id="eri_reopen_selected_btn" disabled>
                        <i class="fa fa-play-circle"></i> Set In Process
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive eri-table-wrap">
                <table class="table table-bordered" id="inactivityReportTable">
                    <thead>
                        <tr>
                            <?php if ($canCloseProjects): ?>
                                <th class="eri-col-select"><input type="checkbox" id="eri_select_all_process" title="Select all Process/Closed projects on this page"></th>
                            <?php endif; ?>
                            <th class="eri-col-sno">#</th>
                            <th class="eri-col-client">Client</th>
                            <th class="eri-col-project">Project</th>
                            <th>Dept</th>
                            <th>Status</th>
                            <th class="eri-col-date">Start Date</th>
                            <th class="eri-col-date">End Date</th>
                            <th class="eri-col-date">Last Log Date</th>
                            <th class="eri-col-days">Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="<?php echo $canCloseProjects ? 10 : 9; ?>" class="eri-empty-state">
                                    <i class="fa fa-inbox"></i>
                                    No inactive records found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $i = 1; foreach ($records as $row): ?>
                                <?php
                                    $daysInactive = ($row->days_since_last_entry !== null && $row->days_since_last_entry !== '') ? (int)$row->days_since_last_entry : null;
                                    $daysClass = 'eri-days-low';
                                    if ($daysInactive !== null) {
                                        if ($daysInactive >= 180) {
                                            $daysClass = 'eri-days-high';
                                        } elseif ($daysInactive >= 90) {
                                            $daysClass = 'eri-days-mid';
                                        }
                                    }
                                    $isProcessProject = !empty($row->project_Id)
                                        && strtolower(trim((string)$row->project_status)) === 'process';
                                    $isClosedProject = !empty($row->project_Id)
                                        && strtolower(trim((string)$row->project_status)) === 'closed';
                                    $canToggleStatus = $isProcessProject || $isClosedProject;
                                ?>
                                <tr data-project-id="<?php echo !empty($row->project_Id) ? (int)$row->project_Id : 0; ?>">
                                    <?php if ($canCloseProjects): ?>
                                        <td class="eri-col-select">
                                            <?php if ($canToggleStatus): ?>
                                                <input type="checkbox"
                                                    class="eri-status-check"
                                                    value="<?php echo (int)$row->project_Id; ?>"
                                                    data-status="<?php echo $isProcessProject ? 'process' : 'closed'; ?>">
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                    <td class="eri-col-sno"><?php echo $i; ?></td>
                                    <td class="eri-col-client">
                                        <span class="eri-cell-inner">
                                            <span class="eri-cell-text" title="<?php echo htmlspecialchars($row->client_name, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($row->client_name, ENT_QUOTES, 'UTF-8'); ?></span>
                                        </span>
                                    </td>
                                    <td class="eri-col-project">
                                        <span class="eri-cell-inner">
                                            <span class="eri-cell-text" title="<?php echo htmlspecialchars($row->project_name, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($row->project_name, ENT_QUOTES, 'UTF-8'); ?></span>
                                            <span class="text-muted eri-cell-id">(<?php echo (int)$row->project_Id; ?>)</span>
                                        </span>
                                    </td>
                                    <td><?php echo !empty($row->department) ? htmlspecialchars($row->department, ENT_QUOTES, 'UTF-8') : '—'; ?></td>
                                    <td>
                                        <?php if ($row->project_status !== '—'): ?>
                                            <?php if ($canCloseProjects && $canToggleStatus): ?>
                                                <span class="eri-status-cell eri-status-toggle <?php echo eri_project_status_class($row->project_status); ?>"
                                                    data-project-id="<?php echo (int)$row->project_Id; ?>"
                                                    data-status="<?php echo strtolower(trim((string)$row->project_status)); ?>"
                                                    title="<?php echo $isProcessProject ? 'Click to close project' : 'Click to set In Process'; ?>">
                                                    <?php echo htmlspecialchars(eri_project_status_label($row->project_status), ENT_QUOTES, 'UTF-8'); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="eri-status-cell <?php echo eri_project_status_class($row->project_status); ?>">
                                                    <?php echo htmlspecialchars(eri_project_status_label($row->project_status), ENT_QUOTES, 'UTF-8'); ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td class="eri-col-date"><?php echo format_inactivity_project_date(isset($row->project_start_date) ? $row->project_start_date : ''); ?></td>
                                    <td class="eri-col-date"><?php echo format_inactivity_project_date(isset($row->project_end_date) ? $row->project_end_date : ''); ?></td>
                                    <td class="eri-col-date">
                                        <?php if (!empty($row->emp_report_dates)): ?>
                                            <?php echo date('d-M-Y', strtotime($row->emp_report_dates)); ?>
                                        <?php else: ?>
                                            <span class="eri-badge eri-badge-never">Never</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="eri-col-days">
                                        <?php if ($daysInactive !== null): ?>
                                            <span class="eri-days-pill <?php echo $daysClass; ?>"><?php echo $daysInactive; ?></span>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php $i++; endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function syncEriYmSelect($select) {
    var val = $select.val();
    var hasValue = val !== null && val !== undefined && String(val).trim() !== '';
    var $container = $select.next('.select2-container');
    if ($container.length) {
        if (hasValue) {
            $container.addClass('cr-ym-selected-bg');
        } else {
            $container.removeClass('cr-ym-selected-bg');
        }
        return;
    }
    var $wrap = $select.closest('.kpi-ym-select-wrap');
    if (hasValue) {
        $wrap.addClass('kpi-ym-wrap-selected');
    } else {
        $wrap.removeClass('kpi-ym-wrap-selected');
    }
}

function clearEriMonthIfYearAll(yearId, monthId) {
    var yearVal = $('#' + yearId).val();
    var $month = $('#' + monthId);
    if (yearVal && String(yearVal).toUpperCase() === 'ALL') {
        $month.val('').prop('disabled', true).trigger('change');
    } else {
        $month.prop('disabled', false);
    }
    syncEriYmSelect($month);
}

function clearEriYmSelect(id) {
    var $select = $('#' + id);
    $select.val('').trigger('change');
    syncEriYmSelect($select);
    if (id === 'from_year') {
        clearEriYmSelect('from_month');
        clearEriMonthIfYearAll('from_year', 'from_month');
    } else if (id === 'to_year') {
        clearEriYmSelect('to_month');
        clearEriMonthIfYearAll('to_year', 'to_month');
    }
}

$(function() {
    if ($.fn.select2) {
        $('#from_year, #to_year').select2({
            width: '110px',
            placeholder: 'Year',
            allowClear: true,
            minimumResultsForSearch: 0
        });
        $('#from_month, #to_month').select2({
            width: '150px',
            placeholder: 'Month',
            allowClear: true,
            minimumResultsForSearch: 0
        });
    }

    $('.kpi-ym-clearable').each(function() {
        syncEriYmSelect($(this));
    });

    clearEriMonthIfYearAll('from_year', 'from_month');
    clearEriMonthIfYearAll('to_year', 'to_month');

    $('#from_year').on('change', function() {
        clearEriMonthIfYearAll('from_year', 'from_month');
        syncEriYmSelect($(this));
    });
    $('#to_year').on('change', function() {
        clearEriMonthIfYearAll('to_year', 'to_month');
        syncEriYmSelect($(this));
    });

    $('.kpi-ym-clearable').on('change', function() {
        syncEriYmSelect($(this));
    });

    $('#client_Id').on('change', function() {
        var clientId = $(this).val();
        $.ajax({
            url: "<?php echo base_url('emp_record_inactivity/getProjectsByClient'); ?>",
            type: "POST",
            data: { client_Id: clientId },
            success: function(response) {
                $('#project_Id').html(response);
            }
        });
    });

    $('#eri_filter_form').on('submit', function() {
        $('#from_month, #to_month').prop('disabled', false);
    });

    var $table = $('#inactivityReportTable');
    var hasDataTable = false;
    if ($table.find('tbody tr td').length > 1 && !$table.find('tbody tr td.eri-empty-state').length) {
        var dtColumnTargets = {
            sno: <?php echo $canCloseProjects ? 1 : 0; ?>,
            days: <?php echo $canCloseProjects ? 9 : 8; ?>,
            center: <?php echo $canCloseProjects ? '[1, 5, 6, 7, 8, 9]' : '[0, 4, 5, 6, 7, 8]'; ?>
        };

        $table.DataTable({
            deferRender: true,
            processing: true,
            stateSave: false,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            order: [[dtColumnTargets.days, 'desc']],
            autoWidth: false,
            scrollX: true,
            columnDefs: [
                <?php if ($canCloseProjects): ?>
                { targets: 0, orderable: false, searchable: false, width: '36px' },
                <?php endif; ?>
                { targets: dtColumnTargets.sno, orderable: false, width: '40px' },
                { targets: dtColumnTargets.center, className: 'text-center' }
            ],
            language: {
                search: 'Quick Search:',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ records',
                infoEmpty: 'No records available',
                zeroRecords: 'No matching records found'
            }
        });
        hasDataTable = true;
    }

    function getVisibleStatusChecks() {
        if (hasDataTable) {
            return $('#inactivityReportTable tbody tr:visible .eri-status-check');
        }
        return $('.eri-status-check');
    }

    function getSelectedProjectIdsByStatus(statusKey) {
        var ids = [];
        $('.eri-status-check:checked').each(function() {
            if (String($(this).data('status')).toLowerCase() === statusKey) {
                var projectId = parseInt($(this).val(), 10);
                if (projectId > 0 && ids.indexOf(projectId) === -1) {
                    ids.push(projectId);
                }
            }
        });
        return ids;
    }

    function updateStatusButtonState() {
        var processCount = getSelectedProjectIdsByStatus('process').length;
        var closedCount = getSelectedProjectIdsByStatus('closed').length;
        $('#eri_close_selected_btn').prop('disabled', processCount === 0);
        $('#eri_reopen_selected_btn').prop('disabled', closedCount === 0);
    }

    $(document).on('change', '.eri-status-check', function() {
        updateStatusButtonState();
        var visibleChecks = getVisibleStatusChecks();
        var visibleChecked = visibleChecks.filter(':checked').length;
        $('#eri_select_all_process').prop('checked', visibleChecks.length > 0 && visibleChecks.length === visibleChecked);
    });

    $('#eri_select_all_process').on('change', function() {
        var isChecked = $(this).is(':checked');
        getVisibleStatusChecks().prop('checked', isChecked);
        updateStatusButtonState();
    });

    if (hasDataTable) {
        $table.on('draw.dt', function() {
            $('#eri_select_all_process').prop('checked', false);
            updateStatusButtonState();
        });
    }

    $(document).on('click', '.eri-status-toggle', function() {
        var $badge = $(this);
        var projectId = parseInt($badge.data('project-id'), 10);
        var currentStatus = String($badge.data('status')).toLowerCase();

        if (!projectId || (currentStatus !== 'process' && currentStatus !== 'closed')) {
            return;
        }

        var nextLabel = currentStatus === 'process' ? 'Closed' : 'In Process';
        if (!confirm('Change project status to ' + nextLabel + '?')) {
            return;
        }

        $badge.css('pointer-events', 'none').text('Updating...');

        $.ajax({
            url: "<?php echo base_url('emp_record_inactivity/toggleProjectStatus'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                project_id: projectId,
                current_status: currentStatus
            },
            success: function(response) {
                if (!response.success) {
                    alert(response.message || 'Unable to update project status.');
                    window.location.reload();
                    return;
                }

                var newStatus = String(response.new_status || '').toLowerCase();
                var displayStatus = newStatus === 'process' ? 'In Process' : 'Closed';
                $badge
                    .data('status', newStatus)
                    .attr('title', newStatus === 'process' ? 'Click to close project' : 'Click to set In Process')
                    .removeClass('eri-badge-process eri-badge-closed eri-badge-default')
                    .addClass(newStatus === 'process' ? 'eri-badge-process' : 'eri-badge-closed')
                    .text(displayStatus)
                    .css('pointer-events', '');

                $('tr[data-project-id="' + projectId + '"] .eri-status-check').each(function() {
                    $(this).data('status', newStatus);
                });
            },
            error: function() {
                alert('Unable to update project status. Please try again.');
                window.location.reload();
            }
        });
    });

    function submitBulkStatusUpdate(action) {
        var statusKey = action === 'close' ? 'process' : 'closed';
        var projectIds = getSelectedProjectIdsByStatus(statusKey);
        var actionLabel = action === 'close' ? 'close' : 'set In Process';
        var url = action === 'close'
            ? "<?php echo base_url('emp_record_inactivity/closeSelectedProcessProjects'); ?>"
            : "<?php echo base_url('emp_record_inactivity/reopenSelectedProcessProjects'); ?>";
        var $btn = action === 'close' ? $('#eri_close_selected_btn') : $('#eri_reopen_selected_btn');
        var defaultHtml = action === 'close'
            ? '<i class="fa fa-times-circle"></i> Close Selected'
            : '<i class="fa fa-play-circle"></i> Set In Process';

        if (projectIds.length === 0) {
            alert('Please select at least one ' + (action === 'close' ? 'Process' : 'Closed') + ' project.');
            return;
        }

        if (!confirm('Update ' + projectIds.length + ' selected project(s) to ' + actionLabel + '? Only status will be changed.')) {
            return;
        }

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');

        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: { project_ids: projectIds },
            success: function(response) {
                alert(response.message || 'Update completed.');
                if (response.success) {
                    window.location.reload();
                } else {
                    $btn.prop('disabled', false).html(defaultHtml);
                    updateStatusButtonState();
                }
            },
            error: function() {
                alert('Unable to update selected projects. Please try again.');
                $btn.prop('disabled', false).html(defaultHtml);
                updateStatusButtonState();
            }
        });
    }

    $('#eri_close_selected_btn').on('click', function() {
        submitBulkStatusUpdate('close');
    });

    $('#eri_reopen_selected_btn').on('click', function() {
        submitBulkStatusUpdate('reopen');
    });

    updateStatusButtonState();
});
</script>

<?php $this->load->view('includes/cRMFooter'); ?>
