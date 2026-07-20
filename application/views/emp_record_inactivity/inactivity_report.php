<!-- Include Header -->
<?php $this->load->view('includes/cRMHeader');

$fromDate = isset($filters['from_date']) ? $filters['from_date'] : date('Y-m-d', strtotime('-6 months'));
$toDate = isset($filters['to_date']) ? $filters['to_date'] : date('Y-m-d');
$selectedClientId = isset($filters['client_Id']) ? $filters['client_Id'] : '';
$selectedProjectId = isset($filters['project_Id']) ? $filters['project_Id'] : '';
$selectedReportingManager = isset($filters['reporting_manager']) ? $filters['reporting_manager'] : '';
$selectedEmployeeName = isset($filters['employee_name']) ? $filters['employee_name'] : '';
$selectedProjectStatus = isset($filters['project_status']) ? $filters['project_status'] : '';
$includeNeverEntered = !empty($filters['include_never_entered']) && $filters['include_never_entered'] === '1';
$records = isset($records) ? $records : array();
$projectStatuses = isset($projectStatuses) ? $projectStatuses : array();
$canCloseProjects = !empty($canCloseProjects);

$totalRecords = count($records);
$neverEnteredCount = 0;
$maxInactiveDays = 0;
foreach ($records as $rec) {
    if (empty($rec->last_entry_date)) {
        $neverEnteredCount++;
    }
    if ($rec->days_since_last_entry !== null && $rec->days_since_last_entry !== '' && (int)$rec->days_since_last_entry > $maxInactiveDays) {
        $maxInactiveDays = (int)$rec->days_since_last_entry;
    }
}

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
.eri-summary-row {
    margin-bottom: 18px;
}
.eri-summary-card {
    background: #fff;
    border: 1px solid #e3e8ef;
    border-radius: 10px;
    padding: 16px 18px;
    box-shadow: 0 2px 8px rgba(31, 80, 118, 0.06);
    min-height: 88px;
}
.eri-summary-card .eri-summary-label {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    font-weight: 700;
    margin-bottom: 6px;
}
.eri-summary-card .eri-summary-value {
    font-size: 28px;
    font-weight: 700;
    line-height: 1.1;
    color: #1f5076;
}
.eri-summary-card.eri-card-warning .eri-summary-value { color: #d97706; }
.eri-summary-card.eri-card-danger .eri-summary-value { color: #dc3545; }
.eri-summary-card.eri-card-info .eri-summary-value { color: #2c5aa0; }

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
.eri-filter-card .eri-date-input {
    background: #f4f7fb;
    color: #1f5076;
    font-weight: 600;
}
.eri-filter-actions {
    padding-top: 4px;
}
.eri-filter-actions .btn {
    border-radius: 6px;
    min-width: 96px;
}
.eri-checkbox-wrap {
    margin-top: 8px;
    padding: 8px 10px;
    background: #f8fafc;
    border: 1px solid #e8edf3;
    border-radius: 6px;
}
.eri-checkbox-wrap label {
    margin: 0;
    font-weight: 600;
    color: #495057;
    font-size: 13px;
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
    max-width: 260px;
    word-break: break-word;
}
#inactivityReportTable .eri-col-client {
    min-width: 130px;
    max-width: 180px;
    word-break: break-word;
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
.eri-process-check {
    transform: scale(1.1);
    cursor: pointer;
}
.eri-status-toggle {
    cursor: pointer;
}
.eri-status-toggle:hover {
    opacity: 0.85;
    box-shadow: 0 0 0 1px rgba(51, 122, 183, 0.35);
}
</style>

<div class="content-wrapper">
    <div class="page-title eri-page-title">
        <div>
            <h1><i class="fa fa-exclamation-triangle"></i> Timesheet Inactivity Report</h1>
            <p class="eri-subtitle">Employees with no timesheet entries from <?php echo date('d-M-Y', strtotime($fromDate)); ?> to <?php echo date('d-M-Y', strtotime($toDate)); ?></p>
        </div>
        <div>
            <a class="btn btn-info btn-flat" href="<?php echo base_url('emp_record_inactivity'); ?>" data-toggle="tooltip" title="Refresh">
                <i class="fa fa-lg fa-refresh"></i>
            </a>
        </div>
    </div>

    <div class="row eri-summary-row">
        <div class="col-md-4 col-sm-4">
            <div class="eri-summary-card eri-card-info">
                <div class="eri-summary-label">Total Inactive Records</div>
                <div class="eri-summary-value"><?php echo (int)$totalRecords; ?></div>
            </div>
        </div>
        <div class="col-md-4 col-sm-4">
            <div class="eri-summary-card eri-card-warning">
                <div class="eri-summary-label">Never Entered</div>
                <div class="eri-summary-value"><?php echo (int)$neverEnteredCount; ?></div>
            </div>
        </div>
        <div class="col-md-4 col-sm-4">
            <div class="eri-summary-card eri-card-danger">
                <div class="eri-summary-label">Max Days Inactive</div>
                <div class="eri-summary-value"><?php echo $maxInactiveDays > 0 ? (int)$maxInactiveDays : '—'; ?></div>
            </div>
        </div>
    </div>

    <div class="card eri-filter-card">
        <div class="card-body">
            <h4 class="eri-filter-title"><i class="fa fa-filter"></i> Search Filters</h4>
            <form name="inactivity_search" id="inactivity_search" method="post" action="<?php echo base_url('emp_record_inactivity/search'); ?>">
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label">From Date</label>
                            <input class="form-control eri-date-input" type="text" id="from_date" name="from_date" value="<?php echo htmlspecialchars($fromDate, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label">To Date</label>
                            <input class="form-control eri-date-input" type="text" id="to_date" name="to_date" value="<?php echo htmlspecialchars($toDate, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Client</label>
                            <select class="form-control" id="client_Id" name="client_Id">
                                <option value="">All Clients</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?php echo (int)$client->client_Id; ?>" <?php echo ((string)$selectedClientId === (string)$client->client_Id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($client->client_name, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Project</label>
                            <select class="form-control" id="project_Id" name="project_Id">
                                <option value="">All Projects</option>
                                <?php foreach ($projects as $project): ?>
                                    <option value="<?php echo (int)$project->project_Id; ?>" <?php echo ((string)$selectedProjectId === (string)$project->project_Id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($project->project_name, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Reporting Manager</label>
                            <select class="form-control" id="reporting_manager" name="reporting_manager">
                                <option value="">All Reporting Managers</option>
                                <?php foreach ($reportingManagers as $manager): ?>
                                    <option value="<?php echo (int)$manager->empId; ?>" <?php echo ((string)$selectedReportingManager === (string)$manager->empId) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($manager->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Status</label>
                            <select class="form-control" id="project_status" name="project_status">
                                <option value="">All Status</option>
                                <?php foreach ($projectStatuses as $statusRow): ?>
                                    <?php $statusValue = isset($statusRow->project_status) ? trim((string)$statusRow->project_status) : ''; ?>
                                    <?php if ($statusValue === '') { continue; } ?>
                                    <option value="<?php echo htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ((string)$selectedProjectStatus === (string)$statusValue) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars(eri_project_status_label($statusValue), ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Employee Name</label>
                            <input class="form-control" type="text" id="employee_name" name="employee_name" value="<?php echo htmlspecialchars($selectedEmployeeName, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search by name">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Options</label>
                            <div class="eri-checkbox-wrap">
                                <label>
                                    <input type="checkbox" name="include_never_entered" value="1" <?php echo $includeNeverEntered ? 'checked' : ''; ?>>
                                    Include never entered employees
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group eri-filter-actions text-right">
                            <button class="btn btn-primary icon-btn" type="submit">
                                <i class="fa fa-search"></i> Search
                            </button>
                            <a href="<?php echo base_url('emp_record_inactivity'); ?>" class="btn btn-default icon-btn">
                                <i class="fa fa-undo"></i> Reset
                            </a>
                        </div>
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
                            <th class="eri-col-employee">Employee</th>
                            <th class="eri-col-empid">Emp ID</th>
                            <th class="eri-col-manager">Manager</th>
                            <th class="eri-col-client">Client</th>
                            <th class="eri-col-project">Project</th>
                            <th>Dept</th>
                            <th>Status</th>
                            <th class="eri-col-date">Start Date</th>
                            <th class="eri-col-date">End Date</th>
                            <th class="eri-col-date">Last Entry</th>
                            <th class="eri-col-days">Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="<?php echo $canCloseProjects ? 13 : 12; ?>" class="eri-empty-state">
                                    <i class="fa fa-inbox"></i>
                                    No inactive records found for the selected period.
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
                                    <td class="eri-col-employee"><?php echo ucwords($row->employee_name); ?></td>
                                    <td class="eri-col-empid"><?php echo htmlspecialchars($row->emp_com_id, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="eri-col-manager"><?php echo !empty($row->reporting_manager) ? ucfirst($row->reporting_manager) : '—'; ?></td>
                                    <td class="eri-col-client" title="<?php echo htmlspecialchars($row->client_name, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($row->client_name, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="eri-col-project" title="<?php echo htmlspecialchars($row->project_name, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($row->project_name, ENT_QUOTES, 'UTF-8'); ?></td>
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
                                        <?php if (!empty($row->last_entry_date)): ?>
                                            <?php echo date('d-M-Y', strtotime($row->last_entry_date)); ?>
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
$(function() {
    $("#from_date, #to_date").datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        numberOfMonths: 1
    });

    var $table = $('#inactivityReportTable');
    var hasDataTable = false;
    if ($table.find('tbody tr td').length > 1 && !$table.find('tbody tr td.eri-empty-state').length) {
        var dtColumnTargets = {
            sno: <?php echo $canCloseProjects ? 1 : 0; ?>,
            days: <?php echo $canCloseProjects ? 12 : 11; ?>,
            center: <?php echo $canCloseProjects ? '[3, 8, 9, 10, 11, 12]' : '[2, 7, 8, 9, 10, 11]'; ?>,
            projectClient: <?php echo $canCloseProjects ? '[5, 6]' : '[4, 5]'; ?>
        };

        $table.DataTable({
            deferRender: true,
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
                { targets: dtColumnTargets.center, className: 'text-center' },
                { targets: dtColumnTargets.projectClient, render: function(data, type) {
                    if (type === 'display' && data && data.length > 42) {
                        return '<span title="' + $('<div>').text(data).html() + '">' + $('<div>').text(data).html().substring(0, 42) + '…</span>';
                    }
                    return data;
                }}
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
});
</script>

<?php $this->load->view('includes/cRMFooter'); ?>
