<?php
$user_type = $this->session->userdata['logged_in_timesheet']['user_type'];
$logged_emp_id = isset($this->session->userdata['logged_in_timesheet']['empId'])
    ? $this->session->userdata['logged_in_timesheet']['empId']
    : null;
// Always resolve HR from employee_details so Actions stays View-only for HR
if (!isset($is_hr_user) || $is_hr_user === null) {
    $is_hr_user = $this->feedback_model->is_hr_department_user($logged_emp_id);
} else {
    $is_hr_user = !empty($is_hr_user) || $this->feedback_model->is_hr_department_user($logged_emp_id);
}
$can_view_all_feedback = !empty($can_view_all_feedback) || in_array($user_type, ['admin', 'superadmin']) || $is_hr_user;
// Managers/admins can manage (edit/update); HR can submit + view all, but Actions stay view-only
$can_manage_feedback = !$is_hr_user && in_array($user_type, ['admin', 'manager', 'business_head', 'superadmin']);
$can_submit_feedback = $can_manage_feedback || $is_hr_user;
?>
<?php $this->load->view('includes/cRMHeader'); ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    .feedback-actions-cell {
        padding: 8px 10px !important;
        white-space: nowrap;
        vertical-align: middle !important;
        text-align: center !important;
        min-width: 120px;
    }
    .feedback-action-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        flex-wrap: nowrap;
    }
    .feedback-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        padding: 0;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        line-height: 1;
        cursor: pointer;
        text-decoration: none !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
    }
    .feedback-action-btn:hover,
    .feedback-action-btn:focus {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(15, 23, 42, 0.14);
        outline: none;
        text-decoration: none !important;
    }
    .feedback-action-btn.feedback-action-view {
        background: #0ea5e9;
        color: #fff !important;
    }
    .feedback-action-btn.feedback-action-view:hover,
    .feedback-action-btn.feedback-action-view:focus {
        background: #0284c7;
        color: #fff !important;
    }
    .feedback-action-btn.feedback-action-edit {
        background: #f59e0b;
        color: #fff !important;
    }
    .feedback-action-btn.feedback-action-edit:hover,
    .feedback-action-btn.feedback-action-edit:focus {
        background: #d97706;
        color: #fff !important;
    }
    .feedback-action-btn.feedback-action-status {
        background: #4361ee;
        color: #fff !important;
    }
    .feedback-action-btn.feedback-action-status:hover,
    .feedback-action-btn.feedback-action-status:focus {
        background: #3651d4;
        color: #fff !important;
    }
    .feedback-table thead th.feedback-actions-th {
        min-width: 120px;
        width: 120px;
    }

    /* Feedback filters panel */
    .feedback-filters-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.08);
        overflow: hidden;
        margin-bottom: 24px;
        background: #fff;
    }
    .feedback-filters-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 24px;
        background: linear-gradient(135deg, #4361ee 0%, #3a56d4 100%);
        color: #fff;
    }
    .feedback-filters-header h3 {
        margin: 0;
        font-size: 17px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .feedback-filters-header h3 i {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.18);
        border-radius: 8px;
        font-size: 14px;
    }
    .feedback-filters-body {
        padding: 24px 24px 8px;
        background: linear-gradient(180deg, #f8faff 0%, #ffffff 100%);
    }
    .feedback-filter-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px 20px;
    }
    @media (max-width: 1199px) {
        .feedback-filter-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 575px) {
        .feedback-filter-grid {
            grid-template-columns: 1fr;
        }
    }
    .feedback-filter-field {
        margin: 0;
    }
    .feedback-filter-field label {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #64748b;
    }
    .feedback-filter-field label i {
        color: #4361ee;
        font-size: 13px;
        width: 16px;
        text-align: center;
    }
    .feedback-filter-field .form-control,
    .feedback-filter-field input[type="date"] {
        height: 42px;
        border: 1px solid #dbe3ef;
        border-radius: 10px;
        background: #fff;
        padding: 8px 14px;
        font-size: 14px;
        color: #1e293b;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .feedback-filter-field .form-control:hover,
    .feedback-filter-field input[type="date"]:hover {
        border-color: #b8c5dc;
    }
    .feedback-filter-field .form-control:focus,
    .feedback-filter-field input[type="date"]:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.12);
        outline: none;
    }
    .feedback-filters-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 18px 24px 22px;
        border-top: 1px solid #eef2f7;
        background: #fff;
    }
    .feedback-filters-actions .btn-apply-filters {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 24px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, #4361ee 0%, #3651d4 100%);
        color: #fff;
        box-shadow: 0 4px 14px rgba(67, 97, 238, 0.35);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .feedback-filters-actions .btn-apply-filters:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(67, 97, 238, 0.4);
        color: #fff;
    }
    .feedback-filters-actions .btn-clear-filters {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 24px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #fff;
        color: #475569;
        transition: background 0.15s ease, border-color 0.15s ease;
    }
    .feedback-filters-actions .btn-clear-filters:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #334155;
        text-decoration: none;
    }
    .feedback-filters-card .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #dbe3ef !important;
        border-radius: 10px !important;
        background: #fff !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
    }
    .feedback-filters-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
        padding-left: 14px !important;
        color: #1e293b !important;
        font-size: 14px !important;
    }
    .feedback-filters-card .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 8px !important;
    }
    .feedback-filters-card .select2-container--default.select2-container--open .select2-selection--single,
    .feedback-filters-card .select2-container--default.select2-selection--single:focus {
        border-color: #4361ee !important;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.12) !important;
    }
</style>

<div class="content-wrapper feedback-reports-page">
    <div class="feedback-reports-header">
        <div>
            <h1 class="feedback-reports-title">Employee Feedback Reports</h1>
        </div>
        <div class="feedback-reports-header-actions">
            <?php if ($can_submit_feedback): ?>
            <a href="<?php echo base_url('kpi_reports/feedbackForm'); ?>" class="btn btn-primary feedback-submit-btn">
                <i class="fa fa-plus"></i> Submit New Feedback
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $this->session->flashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $this->session->flashdata('error'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (!$can_view_all_feedback): ?>
        <div class="alert alert-info" style="margin-bottom: 20px; border-radius: 6px;">
            <i class="fa fa-info-circle"></i> Showing feedback related to you only. Admin and HR can view all employees' feedback.
        </div>
    <?php endif; ?>

    <?php
    $stat_total = (!empty($stats) && $stats->total !== null && $stats->total !== '') ? (int) $stats->total : 0;
    $stat_pending = (!empty($stats) && $stats->sent_count !== null && $stats->sent_count !== '') ? (int) $stats->sent_count : 0;
    $stat_acknowledged = (!empty($stats) && $stats->acknowledge_count !== null && $stats->acknowledge_count !== '') ? (int) $stats->acknowledge_count : 0;
    ?>

    <!-- Statistics Cards -->
    <div class="row mb-4" style="margin-bottom: 30px;">
        <div class="col-md-4" style="margin-bottom: 15px;">
            <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 8px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                <div class="card-body" style="padding: 25px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h5 class="card-title" style="font-size: 14px; font-weight: 500; margin-bottom: 10px; opacity: 0.9;">Total Feedback</h5>
                            <h2 style="margin: 0; font-size: 36px; font-weight: 700;"><?php echo $stat_total; ?></h2>
                        </div>
                        <div style="font-size: 40px; opacity: 0.3;">
                            <i class="fa fa-comments"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="margin-bottom: 15px;">
            <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; border-radius: 8px; box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);">
                <div class="card-body" style="padding: 25px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h5 class="card-title" style="font-size: 14px; font-weight: 500; margin-bottom: 10px; opacity: 0.9;">Pending Acknowledgment</h5>
                            <h2 style="margin: 0; font-size: 36px; font-weight: 700;"><?php echo $stat_pending; ?></h2>
                        </div>
                        <div style="font-size: 40px; opacity: 0.3;">
                            <i class="fa fa-paper-plane"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="margin-bottom: 15px;">
            <div class="card text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border: none; border-radius: 8px; box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);">
                <div class="card-body" style="padding: 25px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h5 class="card-title" style="font-size: 14px; font-weight: 500; margin-bottom: 10px; opacity: 0.9;">Acknowledge</h5>
                            <h2 style="margin: 0; font-size: 36px; font-weight: 700;"><?php echo $stat_acknowledged; ?></h2>
                        </div>
                        <div style="font-size: 40px; opacity: 0.3;">
                            <i class="fa fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card feedback-filters-card mb-4">
        <div class="feedback-filters-header">
            <h3><i class="fa fa-filter"></i> Filters</h3>
        </div>
        <form method="get" action="<?php echo base_url('kpi_reports/feedbackReports'); ?>">
            <div class="feedback-filters-body">
                <div class="feedback-filter-grid">
                    <div class="feedback-filter-field">
                        <label><i class="fa fa-flag"></i> Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="Sent" <?php echo (isset($filters['status']) && $filters['status'] == 'Sent') ? 'selected' : ''; ?>>Pending Acknowledgment</option>
                            <option value="Acknowledge" <?php echo (isset($filters['status']) && $filters['status'] == 'Acknowledge') ? 'selected' : ''; ?>>Acknowledge</option>
                        </select>
                    </div>
                    <div class="feedback-filter-field">
                        <label><i class="fa fa-building"></i> Department</label>
                        <select name="department" id="department" class="form-control">
                            <option value="">All Departments</option>
                            <?php
                            $departmentOptions = function_exists('ts_department_options')
                                ? ts_department_options()
                                : array('Architectural','Structural','MEP','3D Visualization','2D Auto CAD','HR','Software','IT','Business Development','Accounting','Others');
                            $selectedDept = isset($filters['department']) ? $filters['department'] : '';
                            foreach ($departmentOptions as $deptOption):
                            ?>
                            <option value="<?php echo htmlspecialchars($deptOption, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($selectedDept == $deptOption) ? 'selected' : ''; ?>><?php echo htmlspecialchars($deptOption); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="feedback-filter-field">
                        <label><i class="fa fa-tags"></i> Feedback Type</label>
                        <select name="feedback_type" id="feedback_type" class="form-control">
                            <option value="">All Types</option>
                            <?php
                            $feedbackTypeOptions = array(
                                'Monthly KPI Review',
                                'General Feedback',
                                'Performance improvement plan (PIP)',
                                'Productivity & Efficiency',
                                'Quality Improvement',
                                'Technical Knowledge & Skill Development',
                                'Ownership & Accountability',
                                'Innovation',
                                'Communication & Coordination'
                            );
                            $selectedFType = isset($filters['feedback_type']) ? $filters['feedback_type'] : '';
                            foreach ($feedbackTypeOptions as $ftypeOption):
                            ?>
                            <option value="<?php echo htmlspecialchars($ftypeOption, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($selectedFType == $ftypeOption) ? 'selected' : ''; ?>><?php echo htmlspecialchars($ftypeOption); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="feedback-filter-field">
                        <label><i class="fa fa-calendar"></i> From Date</label>
                        <input type="date" name="from_date" class="form-control" value="<?php echo isset($filters['from_date']) ? $filters['from_date'] : ''; ?>">
                    </div>
                    <div class="feedback-filter-field">
                        <label><i class="fa fa-calendar-check-o"></i> To Date</label>
                        <input type="date" name="to_date" class="form-control" value="<?php echo isset($filters['to_date']) ? $filters['to_date'] : ''; ?>">
                    </div>
                    <?php if ($can_view_all_feedback): ?>
                    <div class="feedback-filter-field">
                        <label><i class="fa fa-user"></i> Employee</label>
                        <select name="empId" id="empId" class="form-control">
                            <option value="">All Employees</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?php echo $emp->empId; ?>" <?php echo (isset($filters['empId']) && (string)$filters['empId'] === (string)$emp->empId) ? 'selected' : ''; ?>>
                                    <?php echo $emp->name . ' (' . $emp->department . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="feedback-filter-field">
                        <label><i class="fa fa-users"></i> Managers</label>
                        <select name="assigned_to" id="assigned_to" class="form-control">
                            <option value="">All Managers</option>
                            <?php foreach ($managers as $mgr): ?>
                                <option value="<?php echo $mgr->empId; ?>" <?php echo (isset($filters['assigned_to']) && (string)$filters['assigned_to'] === (string)$mgr->empId) ? 'selected' : ''; ?>>
                                    <?php echo $mgr->name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="feedback-filters-actions">
                <button type="submit" class="btn btn-apply-filters">
                    <i class="fa fa-filter"></i> Apply Filters
                </button>
                <a href="<?php echo base_url('kpi_reports/feedbackReports'); ?>" class="btn btn-clear-filters">
                    <i class="fa fa-refresh"></i> Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Feedback Table -->
    <div class="card" style="border: 1px solid #e0e0e0; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; padding: 15px 20px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center;">
            <h3 class="card-title" style="margin: 0; font-weight: 600; color: #333; font-size: 18px;">
                <i class="fa fa-list" style="margin-right: 8px; color: #4361ee;"></i>Feedback List
            </h3>
            <a href="<?php echo base_url('kpi_reports/downloadFeedbackReportsExcel?' . http_build_query($filters)); ?>" class="btn btn-success" style="padding: 8px 20px; font-weight: 600; border-radius: 4px;">
                <i class="fa fa-download"></i> Download Excel
            </a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover feedback-table" style="margin-bottom: 0;">
                    <thead>
                        <tr style="background-color: #4361ee; color: white;">
                            <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">SNO</th>
                            <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">From & To Dates</th>
                            <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Department</th>
                            <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Given By</th>
                            <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Reporting Manager</th>
                            <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Project Coordinator</th>
                            <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Team Member</th>
                            <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Type</th>
                            <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Status</th>
                            <th style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Feedback Date</th>
                            <th class="feedback-actions-th" style="text-align: center; vertical-align: middle; padding: 15px; font-weight: 600; border: 1px solid #dee2e6;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($feedback_list)): ?>
                            <?php $sno = 1; ?>
                            <?php foreach ($feedback_list as $feedback): ?>
                                <?php
                                // Parse feedback_month to extract From Date and To Date
                                $date_range_display = 'N/A';
                                
                                if (!empty($feedback->feedback_month)) {
                                    // Format: "2026-JAN to 2026-Mar"
                                    if (preg_match('/(\d{4})-([A-Za-z]{3})\s+to\s+(\d{4})-([A-Za-z]{3})/i', $feedback->feedback_month, $matches)) {
                                        $from_year = $matches[1];
                                        $from_month_abbr = strtoupper($matches[2]);
                                        $to_year = $matches[3];
                                        $to_month_abbr = strtoupper($matches[4]);
                                        
                                        // Convert month abbreviation to number
                                        $month_map = array(
                                            'JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04',
                                            'MAY' => '05', 'JUN' => '06', 'JUL' => '07', 'AUG' => '08',
                                            'SEP' => '09', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12'
                                        );
                                        
                                        if (isset($month_map[$from_month_abbr]) && isset($month_map[$to_month_abbr])) {
                                            $from_date = date('d M Y', strtotime($from_year . '-' . $month_map[$from_month_abbr] . '-01'));
                                            // Get last day of the month
                                            $last_day = date('t', strtotime($to_year . '-' . $month_map[$to_month_abbr] . '-01'));
                                            $to_date = date('d M Y', strtotime($to_year . '-' . $month_map[$to_month_abbr] . '-' . $last_day));
                                            $date_range_display = $from_date . ' - ' . $to_date;
                                        }
                                    }
                                }
                                ?>
                                <tr style="transition: background-color 0.2s;">
                                    <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;"><?php echo $sno++; ?></td>
                                    <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;"><?php echo $date_range_display; ?></td>
                                    <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;"><?php echo $feedback->emp_department ? $feedback->emp_department : $feedback->department; ?></td>
                                    <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;">
                                        <?php 
                                        // Show reporting manager name if available, otherwise show "Self"
                                        if ($feedback->reporting_manager_name) {
                                            echo $feedback->reporting_manager_name;
                                        } else {
                                            echo 'Self';
                                        }
                                        ?>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6; color: #1976d2; font-weight: 600;"><?php echo $feedback->reporting_manager_name ? $feedback->reporting_manager_name : 'N/A'; ?></td>
                                    <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;"><?php echo $feedback->project_coordinator_name ? $feedback->project_coordinator_name : 'N/A'; ?></td>
                                    <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;"><?php echo $feedback->team_member_name ? $feedback->team_member_name : 'N/A'; ?></td>
                                    <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;">
                                        <?php 
                                        // Handle multiple improvement areas stored as JSON
                                        $improvement_areas = array();
                                        if (!empty($feedback->feedback_type)) {
                                            // Try to decode as JSON first (for multiple selections)
                                            $decoded = json_decode($feedback->feedback_type, true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                $improvement_areas = $decoded;
                                            } else {
                                                // If not JSON, treat as single value (backward compatibility)
                                                $improvement_areas = array($feedback->feedback_type);
                                            }
                                        }
                                        
                                        if (!empty($improvement_areas)): 
                                            foreach ($improvement_areas as $area): 
                                                if (!empty(trim($area))):
                                        ?>
                                            <span class="badge badge-info" style="padding: 6px 12px; font-size: 12px; border-radius: 15px; background-color: #17a2b8; margin-right: 5px; margin-bottom: 5px; display: inline-block;">
                                                <?php echo htmlspecialchars(trim($area)); ?>
                                            </span>
                                        <?php 
                                                endif;
                                            endforeach; 
                                        else: 
                                        ?>
                                            <span style="color: #6c757d; font-style: italic;">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;">
                                        <?php
                                        $status_badge_class = $this->feedback_model->get_feedback_status_badge_class($feedback->status);
                                        ?>
                                        <span class="badge feedback-status-badge <?php echo $status_badge_class; ?>"><?php echo htmlspecialchars($this->feedback_model->get_feedback_status_label($feedback->status)); ?></span>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; padding: 12px; border: 1px solid #dee2e6;"><?php echo date('d M Y', strtotime($feedback->created_at)); ?></td>
                                    <td class="feedback-actions-cell">
                                        <div class="feedback-action-group">
                                            <a href="<?php echo base_url('kpi_reports/viewFeedback/' . $feedback->feedback_id); ?>"
                                               class="feedback-action-btn feedback-action-view"
                                               title="View Details"
                                               aria-label="View Details">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <?php
                                            // HR department users: View only (no Edit / Update Status)
                                            if (!$is_hr_user):
                                                $logged_in_empId = intval($this->session->userdata['logged_in_timesheet']['empId']);
                                                $feedback_reporting_manager = !empty($feedback->reporting_manager) ? intval($feedback->reporting_manager) : 0;
                                                $feedback_empId = !empty($feedback->empId) ? intval($feedback->empId) : 0;
                                                $feedback_team_member = !empty($feedback->team_members) ? intval($feedback->team_members) : 0;

                                                $can_update = $can_manage_feedback ||
                                                             ($feedback_reporting_manager > 0 && $feedback_reporting_manager == $logged_in_empId) ||
                                                             ($feedback_empId > 0 && $feedback_empId == $logged_in_empId) ||
                                                             ($feedback_team_member > 0 && $feedback_team_member == $logged_in_empId);

                                                if ($can_update):
                                            ?>
                                            <a href="<?php echo base_url('kpi_reports/editFeedback/' . $feedback->feedback_id); ?>"
                                               class="feedback-action-btn feedback-action-edit"
                                               title="Edit Feedback"
                                               aria-label="Edit Feedback">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="feedback-action-btn feedback-action-status"
                                                    onclick="openUpdateStatusModal(<?php echo (int) $feedback->feedback_id; ?>, '<?php echo addslashes($feedback->status); ?>', '<?php echo addslashes($feedback->response); ?>')"
                                                    title="Update Status"
                                                    aria-label="Update Status">
                                                <i class="fa fa-check"></i>
                                            </button>
                                            <?php
                                                endif;
                                            endif;
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center" style="padding: 30px; color: #999; font-style: italic;">No feedback found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <?php if (!empty($pagination_links)): ?>
                <div class="card-footer" style="background-color: #f8f9fa; border-top: 1px solid #dee2e6; padding: 15px 20px; display: flex; justify-content: center; align-items: center;">
                    <?php echo $pagination_links; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Update Modal for Managers/Admins/HR -->
<?php if ($can_manage_feedback): ?>
<div class="modal fade" id="updateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #4361ee; color: white;">
                <h5 class="modal-title">Update Feedback</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php echo form_open('kpi_reports/updateFeedback'); ?>
            <div class="modal-body">
                <input type="hidden" name="feedback_id" id="modal_feedback_id">
                
                <div class="form-group">
                    <label><strong>Status:</strong></label>
                    <select name="status" id="modal_status" class="form-control" required>
                        <option value="Sent">Pending Acknowledgment</option>
                        <option value="Acknowledge">Acknowledge</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><strong>Assigned To:</strong></label>
                    <select name="assigned_to" id="modal_assigned_to" class="form-control">
                        <option value="">Not Assigned</option>
                        <?php foreach ($managers as $mgr): ?>
                            <option value="<?php echo $mgr->empId; ?>"><?php echo $mgr->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><strong>Response:</strong></label>
                    <textarea name="response" id="modal_response" class="form-control" rows="4" placeholder="Enter your response..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                <button type="submit" class="btn btn-primary">UPDATE FEEDBACK</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Update Modal for Reporting Manager and Team Member -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #4361ee; color: white;">
                <h5 class="modal-title">Update Feedback Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php echo form_open('kpi_reports/updateFeedback'); ?>
            <div class="modal-body">
                <input type="hidden" name="feedback_id" id="update_status_modal_feedback_id">
                
                <div class="form-group">
                    <label><strong>Status: <span style="color: red;">*</span></strong></label>
                    <select name="status" id="update_status_modal_status" class="form-control" required style="width: 100%;">
                        <option value="">-- Select Status --</option>
                        <option value="Sent">Pending Acknowledgment</option>
                        <option value="Acknowledge">Acknowledge</option>
                    </select>
                    <small class="text-muted">Status is required</small>
                </div>

                <div class="form-group">
                    <label><strong>Response:</strong></label>
                    <textarea name="response" id="update_status_modal_response" class="form-control" rows="4" placeholder="Enter your response..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
                <button type="submit" class="btn btn-primary">UPDATE FEEDBACK</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
function openUpdateModal(feedbackId, status, response, assignedTo) {
    document.getElementById('modal_feedback_id').value = feedbackId;
    document.getElementById('modal_status').value = status;
    document.getElementById('modal_response').value = response ? response : '';
    document.getElementById('modal_assigned_to').value = assignedTo ? assignedTo : '';
    $('#updateModal').modal('show');
}

function openUpdateStatusModal(feedbackId, status, response) {
    // Set feedback ID
    document.getElementById('update_status_modal_feedback_id').value = feedbackId;
    
    // Set status value
    var statusSelect = document.getElementById('update_status_modal_status');
    if (status && status !== '' && status !== 'null' && status !== 'undefined') {
        statusSelect.value = status;
    } else {
        statusSelect.value = '';
    }
    
    // Set response if provided
    var responseField = document.getElementById('update_status_modal_response');
    if (responseField) {
        responseField.value = (response && response !== 'null' && response !== 'undefined') ? response : '';
    }
    
    // Show modal
    $('#updateStatusModal').modal('show');
}

// Handle update form submission - Simple form POST (for Team Members and Reporting Managers)
$(document).on('submit', '#updateStatusModal form', function(e) {
    var statusSelect = document.getElementById('update_status_modal_status');
    var feedbackId = document.getElementById('update_status_modal_feedback_id').value;
    var responseField = document.getElementById('update_status_modal_response');
    var form = this;
    var submitBtn = $(form).find('button[type="submit"]');
    var originalText = submitBtn.html();
    
    console.log('=== TEAM MEMBER/REPORTING MANAGER FORM SUBMISSION ===');
    console.log('Feedback ID: ' + feedbackId);
    console.log('Status Value: ' + (statusSelect ? statusSelect.value : 'NOT FOUND'));
    console.log('Response Value: ' + (responseField ? responseField.value : 'NOT FOUND'));
    
    // Validate feedback ID
    if (!feedbackId || feedbackId === '' || feedbackId === '0') {
        console.error('ERROR: Feedback ID is missing!');
        alert('Error: Feedback ID is missing. Please refresh and try again.');
        e.preventDefault();
        return false;
    }
    
    // Validate status - REQUIRED for team members and reporting managers
    if (!statusSelect || !statusSelect.value || statusSelect.value === '') {
        console.error('ERROR: Status is required but not selected!');
        alert('Status is required. Please select a status before submitting.');
        if (statusSelect) {
            statusSelect.focus();
            statusSelect.style.borderColor = '#dc3545';
        }
        e.preventDefault();
        return false;
    }
    
    // Validate status value
    if (statusSelect.value !== 'Sent' && statusSelect.value !== 'Acknowledge') {
        console.error('ERROR: Invalid status value: ' + statusSelect.value);
        alert('Invalid status value. Please select either "Pending Acknowledgment" or "Acknowledge".');
        e.preventDefault();
        return false;
    }
    
    // Reset border color if valid
    if (statusSelect) {
        statusSelect.style.borderColor = '';
    }
    
    console.log('✓ Validation passed. Submitting form...');
    console.log('Final values - Feedback ID: ' + feedbackId + ', Status: ' + statusSelect.value);
    
    // Show loading state
    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
    
    // Allow form to submit normally
    return true;
});
</script>

<style>
.content-wrapper.feedback-reports-page {
    padding: 28px 24px 24px;
    background-color: #f5f5f5;
}
.feedback-reports-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-top: 12px;
    margin-bottom: 28px;
    padding: 8px 0 16px;
}
.feedback-reports-title {
    margin: 0;
    font-size: 28px;
    font-weight: 600;
    color: #333;
}
.feedback-reports-header-actions {
    flex-shrink: 0;
    padding-top: 4px;
}
.feedback-submit-btn {
    padding: 12px 24px !important;
    font-weight: 600;
    border-radius: 6px;
    white-space: nowrap;
}
.card {
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-radius: 8px;
}
.badge {
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 15px;
}
.feedback-status-badge {
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 15px;
    color: #fff !important;
    border: none;
    display: inline-block;
    white-space: nowrap;
}
.feedback-status-pending {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
}
.feedback-status-acknowledged {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
}
.feedback-status-default {
    background-color: #6c757d !important;
}
.table tbody tr:hover {
    background-color: #f8f9fa;
}
.table tbody tr:nth-child(even) {
    background-color: #ffffff;
}
.table tbody tr:nth-child(odd) {
    background-color: #fafafa;
}
.form-control:focus {
    border-color: #4361ee;
    box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
}
.btn-primary {
    background-color: #4361ee;
    border-color: #4361ee;
}
.btn-primary:hover {
    background-color: #3a0ca3;
    border-color: #3a0ca3;
}
.btn-info {
    background-color: #17a2b8;
    border-color: #17a2b8;
}
.btn-info:hover {
    background-color: #138496;
    border-color: #117a8b;
}
.alert {
    border-radius: 6px;
    margin-bottom: 20px;
}

/* Select2 Dropdown Styling */
.select2-container--default .select2-selection--single {
    height: 40px !important;
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    background-color: white !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 37px !important;
    padding-left: 8px !important;
    color: #333 !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
    right: 5px !important;
}

.select2-container--default.select2-container--open .select2-selection--single {
    border-color: #4361ee !important;
}

.select2-dropdown {
    border: 1px solid #4361ee !important;
    border-radius: 4px !important;
}

.select2-container--default .select2-results__option[aria-selected=true] {
    background-color: #ddd !important;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #4361ee !important;
    color: white !important;
}

/* Pagination Styling */
.pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    justify-content: center;
    flex-wrap: wrap;
}

.pagination li {
    margin: 0 3px;
}

.pagination .page-link {
    display: block;
    padding: 8px 12px;
    color: #4361ee;
    text-decoration: none;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    transition: all 0.3s;
    background-color: white;
}

.pagination .page-link:hover {
    background-color: #4361ee;
    color: white;
    border-color: #4361ee;
}

.pagination .active .page-link {
    background-color: #4361ee;
    border-color: #4361ee;
    color: white;
    cursor: default;
}

.pagination .active .page-link:hover {
    background-color: #3651d4;
    border-color: #3651d4;
}
</style>

<script>
$(document).ready(function() {
    // Initialize select2 for filter dropdowns
    $('#status, #department, #feedback_type, #empId, #assigned_to, #modal_status, #modal_assigned_to').select2({
        width: '100%'
    });
    
    // DO NOT initialize select2 for team member modal - use native select for better form submission
    // This ensures the value is always properly submitted
    
    // Reset modal on close
    $('#updateStatusModal').on('hidden.bs.modal', function() {
        document.getElementById('update_status_modal_feedback_id').value = '';
        document.getElementById('update_status_modal_status').value = '';
        document.getElementById('update_status_modal_response').value = '';
    });

    <?php if (!empty($open_status_feedback)): ?>
    openUpdateStatusModal(
        <?php echo (int) $open_status_feedback->feedback_id; ?>,
        <?php echo json_encode($open_status_feedback->status); ?>,
        <?php echo json_encode(!empty($open_status_feedback->response) ? $open_status_feedback->response : ''); ?>
    );
    <?php endif; ?>
});
</script>

<?php $this->load->view('includes/cRMFooter'); ?>

