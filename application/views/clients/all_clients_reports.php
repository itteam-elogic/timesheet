    <?php 
    $this->load->view('includes/cRMHeader'); 

    $getClientID    = isset($filter_client_Id) ? (array) $filter_client_Id : array();
    $getDepartment  = isset($filter_department) ? (array) $filter_department : array();
    $getProjectID   = isset($filter_project_Id) ? $filter_project_Id : 'all';
    $getProjectManager = isset($filter_project_manager) ? (array) $filter_project_manager : array();
    $getFromDate    = !empty($form_date) ? $form_date : '';
    $getToDate      = !empty($to_date) ? $to_date : '';
    $getClientNames = isset($getClientNames) ? $getClientNames : array();
    $getDepartments = isset($getDepartments) ? $getDepartments : array();
    $getListOfManagers = isset($getListOfManagers) ? $getListOfManagers : array();
    $getListOfProjects = isset($getListOfProjects) ? $getListOfProjects : array();
    $isSearch = isset($is_search) ? (bool) $is_search : false;

    $kpiStartYear = 2010;
    $kpiEndYear = (int) date('Y');
    $prevMonthTime = strtotime('first day of previous month');
    $prevMonth = (int) date('n', $prevMonthTime);
    $prevYear = (int) date('Y', $prevMonthTime);
    $uiFromYear = isset($ui_from_year) ? (string) $ui_from_year : (string) $prevYear;
    $uiToYear = isset($ui_to_year) ? (string) $ui_to_year : (string) $prevYear;
    $uiFromMonth = isset($ui_from_month) ? (int) $ui_from_month : $prevMonth;
    $uiToMonth = isset($ui_to_month) ? (int) $ui_to_month : $prevMonth;
    if (!$isSearch && $uiFromMonth === 0) {
        $uiFromMonth = $prevMonth;
    }
    if (!$isSearch && $uiToMonth === 0) {
        $uiToMonth = $prevMonth;
    }
    $uiFromYearIsAll = (strtoupper($uiFromYear) === 'ALL');
    $uiToYearIsAll = (strtoupper($uiToYear) === 'ALL');
    if ($uiFromYearIsAll) {
        $uiFromMonth = 0;
    }
    if ($uiToYearIsAll) {
        $uiToMonth = 0;
    }
    $kpiMonthLabels = array(
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    );
    // Project manager names to exclude from the dropdown (all_clients_reports) — comparison is case-insensitive
    $excludedManagerNames = array('Hemanth kmv', 'Laxmikanth Suram', 'Rahul Kumar', 'Shirley Rufina', 'Suman Kumar');
    $excludedManagerNamesLower = array_map('strtolower', array_map('trim', $excludedManagerNames));

    ?>
    <div class="content-wrapper">
        <div class="page-title">
            <div>
                <h1><i class="fa fa-bell"></i>Client TSR</h1>
            </div>
            <div>
				<a class="btn btn-primary btn-flat" href="<?php echo base_url();?>clients/cs_reports" data-toggle="tooltip" title="Back" style="background-color:#1f2933;";><i class="fa fa-arrow-left"> Back</i></a>
                <a class="btn btn-primary btn-flat" href="<?php echo base_url();?>clients/all_clients_reports" data-toggle="tooltip" title="refresh">
                    <i class="fa fa-chevron-circle-left"></i>
                </a>
            </div>
        </div>
        <div class="card">
            <h3 class="card-title"></h3>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bs-component">
                            <div class="tab-content" id="myTabContent">
                                <form name="emp_search_log" id="emp_search_log" method="post" action="<?php echo base_url('clients/all_clients_reports');?>">
                                    <input type="hidden" name="form_date" id="form_date" value="<?= htmlspecialchars($getFromDate, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="to_date" id="to_date" value="<?= htmlspecialchars($getToDate, ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="tab-pane fade active in" id="Add">
                                        <style>
                                        .client-report-filter-bar { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px 24px 20px; margin-bottom: 16px; box-shadow: 0 4px 14px rgba(1,75,136,.07); border-top: 3px solid #014b88; }
                                        .client-report-filter-grid .crf-grid-top { display: grid; grid-template-columns: repeat(4,minmax(0,1fr)); gap: 14px 16px; margin-bottom: 16px; }
                                        @media (max-width:1100px) { .client-report-filter-grid .crf-grid-top { grid-template-columns: repeat(2,minmax(0,1fr)); } }
                                        @media (max-width:560px) { .client-report-filter-grid .crf-grid-top { grid-template-columns: 1fr; } }
                                        .client-report-filter-grid .crf-field { background: linear-gradient(180deg,#fafbfc 0%,#f4f6f9 100%); border: 1px solid #e8ecf1; border-radius: 10px; padding: 12px 14px 14px; }
                                        .client-report-filter-grid .crf-field-label { display: block; font-weight: 700; font-size: 12px; letter-spacing: .02em; text-transform: uppercase; color: #014b88; margin-bottom: 8px; }
                                        .client-report-filter-grid .crf-dates-actions-row { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 14px 20px; }
                                        .client-report-filter-grid .kpi-ym-range-panels { display: flex; align-items: stretch; gap: 12px; flex-wrap: wrap; }
                                        .client-report-filter-grid .kpi-ym-panel { background: #fff; border: 1px solid #d8dee6; border-radius: 10px; padding: 10px 14px 12px; min-width: 280px; box-shadow: 0 1px 2px rgba(0,0,0,.04); }
                                        .client-report-filter-grid .kpi-ym-panel-title { font-weight: 600; color: #333; font-size: 14px; margin-bottom: 10px; }
                                        .client-report-filter-grid .kpi-ym-panel-fields { display: flex; align-items: center; gap: 10px; }
                                        .client-report-filter-grid .kpi-ym-select-wrap { position: relative; }
                                        .client-report-filter-grid .kpi-ym-select { appearance: none; -webkit-appearance: none; background-color: #fff; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23666' d='M1.41 0L6 4.58 10.59 0 12 1.41l-6 6-6-6z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; border: 1px solid #cfd6df; color: #4a5568; font-weight: 500; font-size: 14px; height: 38px; padding: 6px 32px 6px 12px; border-radius: 8px; width: 128px; cursor: pointer; }
                                        .client-report-filter-grid .kpi-ym-select option { background: #fff; color: #333; font-weight: normal; }
                                        .client-report-filter-grid .kpi-ym-select-wrap.kpi-ym-wrap-selected { background: #673ab7; border-radius: 8px; border: 2px solid #e2e2e2; }
                                        .client-report-filter-grid .kpi-ym-select-wrap.kpi-ym-wrap-selected .kpi-ym-select { background-color: transparent !important; border-color: transparent !important; color: #fff !important; font-weight: 600; padding-right: 52px; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23ffffff' d='M1.41 0L6 4.58 10.59 0 12 1.41l-6 6-6-6z'/%3E%3C/svg%3E"); }
                                        .client-report-filter-grid .kpi-ym-clear-icon { position: absolute; right: 26px; top: 50%; transform: translateY(-50%); color: #fff; font-size: 16px; cursor: pointer; display: none; z-index: 2; }
                                        .client-report-filter-grid .crf-btn-row { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 12px; }
                                        .client-report-filter-grid .btn-crf-apply { background: linear-gradient(180deg,#015a9e 0%,#014b88 100%) !important; color: #fff !important; font-weight: 700; padding: 10px 22px; border: none; border-radius: 8px; min-width: 120px; }
                                        .client-report-filter-grid .btn-crf-clear { background: #fff !important; color: #c2410c !important; font-weight: 700; padding: 10px 18px; border: 2px solid #fdba74 !important; border-radius: 8px; }
                                        .client-report-filter-grid .select2-container { width: 100% !important; }
                                        .client-report-filter-grid .select2-container .select2-selection--multiple,
                                        .client-report-filter-grid .select2-container .select2-selection--single { min-height: 40px !important; border: 1px solid #d1d5db !important; border-radius: 8px !important; }
                                        .client-report-filter-grid .select2-container--default .select2-selection--multiple .select2-selection__choice { background-color: #6d28d9 !important; border-color: #5b21b6 !important; color: #fff !important; }
                                        .client-report-filter-grid .select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color: #f5f3ff !important; }
                                        </style>
                                        <div class="client-report-filter-bar client-report-filter-grid">
                                            <div class="crf-grid-top">
                                                <div class="crf-field">
                                                    <span class="crf-field-label"><label for="department">Department</label></span>
                                                    <select class="form-control" id="department" name="department[]" multiple="multiple" style="width:100%;">
                                                        <option value="all" <?php echo (empty($getDepartment) || in_array('all', $getDepartment)) ? 'selected="selected"' : ''; ?>>All departments</option>
                                                        <?php foreach ($getDepartments as $d): ?>
                                                        <option value="<?php echo htmlspecialchars($d->department, ENT_QUOTES, 'UTF-8'); ?>" <?php echo in_array($d->department, $getDepartment) ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars($d->department); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="crf-field">
                                                    <span class="crf-field-label"><label for="client_Id">Client's</label></span>
                                                    <select class="form-control" id="client_Id" name="client_Id[]" multiple="multiple" style="width:100%;">
                                                        <option value="all" <?php echo (empty($getClientID) || in_array('all', $getClientID)) ? 'selected="selected"' : ''; ?>>All clients</option>
                                                        <?php foreach ($getClientNames as $cn): ?>
                                                        <option value="<?php echo (int)$cn->client_Id; ?>" <?php echo in_array($cn->client_Id, $getClientID) ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars(ucfirst(str_replace("'", " ", $cn->client_name))); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="crf-field">
                                                    <span class="crf-field-label"><label for="project_Id">Project's</label></span>
                                                    <select class="form-control" id="project_Id" name="project_Id" style="width:100%;">
                                                        <option value="all" <?php echo ($getProjectID === '' || $getProjectID === 'all') ? 'selected="selected"' : ''; ?>>All projects</option>
                                                        <?php foreach ($getListOfProjects as $proj): ?>
                                                        <option value="<?php echo (int) $proj->project_Id; ?>" <?php echo ($getProjectID !== 'all' && (string) $getProjectID === (string) $proj->project_Id) ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars($proj->project_name); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="crf-field">
                                                    <span class="crf-field-label"><label for="project_manager">Project Manager</label></span>
                                                    <select class="form-control" id="project_manager" name="project_manager[]" multiple="multiple" style="width:100%;">
                                                        <option value="all" <?php echo (empty($getProjectManager) || in_array('all', $getProjectManager)) ? 'selected="selected"' : ''; ?>>All project managers</option>
                                                        <?php foreach ($getListOfManagers as $pm):
                                                            if (in_array(strtolower(trim($pm->name)), $excludedManagerNamesLower)) continue;
                                                        ?>
                                                        <option value="<?php echo (int)$pm->empId; ?>" <?php echo in_array($pm->empId, $getProjectManager) ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars($pm->name); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="crf-dates-actions-row">
                                                <div class="kpi-ym-range-panels">
                                                    <div class="kpi-ym-panel">
                                                        <div class="kpi-ym-panel-title">From</div>
                                                        <div class="kpi-ym-panel-fields">
                                                            <div class="kpi-ym-select-wrap">
                                                                <select name="from_year" id="from_year" class="form-control kpi-ym-select kpi-ym-clearable" title="From year">
                                                                    <option value="">Year</option>
                                                                    <option value="ALL" <?php echo $uiFromYearIsAll ? 'selected' : ''; ?>>ALL</option>
                                                                    <?php for ($y = $kpiEndYear; $y >= $kpiStartYear; $y--): ?>
                                                                    <option value="<?php echo $y; ?>" <?php echo (!$uiFromYearIsAll && (int)$uiFromYear === $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                                                    <?php endfor; ?>
                                                                </select>
                                                                <span class="kpi-ym-clear-icon" onclick="clearClientsYmSelect('from_year')">&times;</span>
                                                            </div>
                                                            <div class="kpi-ym-select-wrap">
                                                                <select name="from_month" id="from_month" class="form-control kpi-ym-select kpi-ym-clearable" title="From month">
                                                                    <option value="">Month</option>
                                                                    <?php foreach ($kpiMonthLabels as $num => $label): ?>
                                                                    <option value="<?php echo $num; ?>" <?php echo ($uiFromMonth > 0 && (int)$num === $uiFromMonth) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <span class="kpi-ym-clear-icon" onclick="clearClientsYmSelect('from_month')">&times;</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="kpi-ym-panel">
                                                        <div class="kpi-ym-panel-title">To</div>
                                                        <div class="kpi-ym-panel-fields">
                                                            <div class="kpi-ym-select-wrap">
                                                                <select name="to_year" id="to_year" class="form-control kpi-ym-select kpi-ym-clearable" title="To year">
                                                                    <option value="">Year</option>
                                                                    <option value="ALL" <?php echo $uiToYearIsAll ? 'selected' : ''; ?>>ALL</option>
                                                                    <?php for ($y = $kpiEndYear; $y >= $kpiStartYear; $y--): ?>
                                                                    <option value="<?php echo $y; ?>" <?php echo (!$uiToYearIsAll && (int)$uiToYear === $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                                                    <?php endfor; ?>
                                                                </select>
                                                                <span class="kpi-ym-clear-icon" onclick="clearClientsYmSelect('to_year')">&times;</span>
                                                            </div>
                                                            <div class="kpi-ym-select-wrap">
                                                                <select name="to_month" id="to_month" class="form-control kpi-ym-select kpi-ym-clearable" title="To month">
                                                                    <option value="">Month</option>
                                                                    <?php foreach ($kpiMonthLabels as $num => $label): ?>
                                                                    <option value="<?php echo $num; ?>" <?php echo ($uiToMonth > 0 && (int)$num === $uiToMonth) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <span class="kpi-ym-clear-icon" onclick="clearClientsYmSelect('to_month')">&times;</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="crf-btn-row">
                                                    <button type="submit" class="btn btn-crf-apply" id="searchBtn"><i class="fa fa-search"></i> Search</button>
                                                    <button type="button" class="btn btn-crf-clear" onclick="clearAllClientsFilters();"><i class="fa fa-times-circle"></i> Clear all filters</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="search_validation_alert" class="alert alert-danger" role="alert" style="display:none; margin-bottom:15px;"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            /* Match Project dropdown height to Client/Department Select2 height */
            #project_Id + .select2-container .select2-selection--single { min-height: 38px; }
            #project_Id + .select2-container .select2-selection--single .select2-selection__rendered { line-height: 36px; }
        </style>
         <?php if(!empty($allClientResult)):?>
        <?php
        $summaryRows = isset($summaryRows) ? $summaryRows : array();
        $summaryTitle = isset($summaryTitle) ? $summaryTitle : 'Department Summary';
        $byClient = isset($byClient) ? $byClient : array();
        $fmtNum = function($v) { $v = (float)$v; $s = number_format($v, 2, '.', ','); return preg_replace('/\.?0+$/', '', $s); };
        ?>
        <!-- Department-wise summary card (design: white card, dark blue header, pastel metric columns) -->
        <style>
            .dept-summary-card { width: 100%; }
            .dept-summary-card .dept-summary-table-wrap { width: 50%; margin-left: auto; margin-right: auto; }
            .dept-summary-card .dept-summary-table { width: 100%; }
            .dept-summary-card .dept-summary-table thead th { font-size: 1.2rem; font-weight: 700; }
            .dept-summary-card .dept-summary-table tbody td { font-size: 1.15rem; font-weight: 600; }
            .dept-summary-card .dept-summary-table tbody td.dept-name-cell { font-size: 1.2rem; font-weight: 700; background-color: #e8eef5; color: #1a3a5c; border-left: 3px solid #1a5276; }
        </style>
        <div class="card dept-summary-card" style="margin-bottom: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div class="card-body" style="padding: 20px 24px;">
                <h4 id="dept_summary_title" class="text-center" style="margin: 0 0 16px 0; font-weight: 600; font-size: 24px; color: #7030a0;"><?php echo htmlspecialchars($summaryTitle); ?></h4>
                <div class="table-responsive dept-summary-table-wrap">
                    <table id="dept_summary_table_export" class="table table-bordered dept-summary-table" style="margin: 0; border-collapse: collapse; border-radius: 6px; overflow: hidden;">
                        <thead>
                            <tr style="background-color: #1a5276; color: #fff; font-weight: bold; ">
                                <th style="padding: 12px 16px; text-align: left; border: 1px solid #1a5276; font-size: 16px;">Department</th>
                                <th style="padding: 12px 16px; text-align: center; border: 1px solid #1a5276; font-size:16px;" >Total Hours</th>
                                <th style="padding: 12px 16px; text-align: center; border: 1px solid #1a5276; font-size:16px;">Invoice Hours</th>
                                <th style="padding: 12px 16px; text-align: center; border: 1px solid #1a5276; font-size:16px;">Difference Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($summaryRows as $deptName => $totals):
                                $diff = $totals['invoice_hours'] - $totals['total_hours'];
                                $diffColor = $diff >= 0 ? '#5cb85c' : '#d9534f';
                                $diffSign = $diff >= 0 ? '+' : '-';
                            ?>
                            <tr>
                                <td class="dept-name-cell" style="padding: 14px 16px; font-size: 16px; font-weight: 600;"><?php echo htmlspecialchars($deptName); ?></td>
                                <td style="padding: 12px 16px; text-align: center; background-color: #d5f5e3; font-weight: 600; font-size: 16px; border: 1px solid #dee2e6;"><?php echo $fmtNum($totals['total_hours']); ?></td>
                                <td style="padding: 12px 16px; text-align: center; background-color: #fef9e7; font-weight: 600; font-size: 16px; border: 1px solid #dee2e6;"><?php echo $fmtNum($totals['invoice_hours']); ?></td>
                                <td style="padding: 12px 16px; text-align: center; background-color: #ebf5fb; font-weight: 700; font-size: 16px; color: <?php echo $diffColor; ?>; border: 1px solid #dee2e6;"><?php echo $diffSign . $fmtNum(abs($diff)); ?></td>
                            </tr> 
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <style>
            .client-toggle-icon { color: #2c7bb6 !important; }
            .client-toggle-icon:hover { color: #1a5276 !important; }
            .client-toggle-icon i { color: inherit; }
        </style>
        <div class="card">
            <div class="card-body">               
                <div class="tab-content">                    
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div style="text-align:right; position:relative;bottom:10px; right:18px;">
                                    <?php 
                                        $userType = strtolower(isset($this->session->userdata['logged_in_timesheet']['user_type']) ? $this->session->userdata['logged_in_timesheet']['user_type'] : '');
                                        // Show 'Send' button only for user_type accounts or admin
                                        if ($userType === 'admin' || $userType === 'accounts'):
                                    ?>
                                    <button type="button" id="send_client_report_btn" class="btn btn-info btn-flat" style="margin-right: 8px;"><i class="fa fa-envelope"></i> Send</button>
                                    <?php endif; ?>
                                    <button id="downloadEmployeeData_total_client_Data" class="btn btn-primary btn-flat">Export</button>
                                </div>
                                <!-- Displaying Search Result -->
                                <div class="col-md-12">
                                    <style>
                                            #table22excel_all thead th { text-align: center; vertical-align: middle; padding: 12px 8px; white-space: nowrap; }
                                            #table22excel_all thead th.wrap-heading { white-space: normal; line-height: 1.25; max-width: 110px; }
                                            #table22excel_all thead th:nth-child(1),
                                            #table22excel_all thead th:nth-child(2) { text-align: center; }
                                            #table22excel_all thead th:nth-child(3),
                                            #table22excel_all thead th:nth-child(4),
                                            #table22excel_all thead th:nth-child(5),
                                            #table22excel_all thead th:nth-child(6),
                                            #table22excel_all thead th:nth-child(7),
                                            #table22excel_all thead th:nth-child(8),
                                            #table22excel_all thead th:nth-child(9) { text-align: center; }
                                            #table22excel_all td { vertical-align: middle !important; padding: 10px 8px; }
                                            #table22excel_all .num-col { text-align: right; min-width: 4em; }
                                            #table22excel_all .center-col { text-align: center; }
                                        </style>
                                        <div class="table-responsive">
                                        <table class="table table-hover table-bordered" id="table22excel_all">
                                            <div style="clear:both"></div>                        
                                            <thead>
                                            <tr style="background-color: #1a5276; color: #fff; font-weight: bold; font-size: 16px;">
													<th>Project Manager</th>
                                                    <th>Client Name</th>
                                                    <th>Billing Type</th>
                                                    <th class="wrap-heading">Production<br>Hours</th>
                                                    <th class="wrap-heading">Project General<br>Hours</th>
                                                    <th>Total Hours</th>
                                                    <th>Invoice</th>
                                                    <th>Difference</th>
                                                    <th>No.of Employees</th>
                                                    <th>Employee Name</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $clientIndex = 0;
                                                foreach ($byClient as $clientName => $clientData):
                                                    $clientIndex++;
                                                    $clientProductionHours = 0;
                                                    $clientGeneralHours = 0;
                                                    $clientTotalHours = 0;
                                                    $clientTotalInvoiceHours = 0;
                                                    foreach ($clientData['projects'] as $r) {
                                                        $clientProductionHours += isset($r->production_hours) ? (float) $r->production_hours : (float) $r->total_hours;
                                                        $clientGeneralHours += isset($r->general_hours) ? (float) $r->general_hours : 0;
                                                        $clientTotalHours += $r->total_hours;
                                                        $clientTotalInvoiceHours += !empty($r->project_invoice_amt) ? (float)$r->project_invoice_amt : 0;
                                                    }
                                                ?>
                                                <?php 
                                                $billingTypes = array();
                                                foreach ($clientData['projects'] as $pr) {
                                                    if (!empty($pr->man_days)) {
                                                        $bt = ucfirst(strtolower(trim($pr->man_days)));
                                                        if ($bt && !in_array($bt, $billingTypes)) $billingTypes[] = $bt;
                                                    }
                                                }
                                                $clientBillingLabel = !empty($billingTypes) ? implode(', ', $billingTypes) : '';
                                                $clientDiff = $clientTotalInvoiceHours - $clientTotalHours; 
                                                $clientDiffColor = $clientDiff >= 0 ? '#5cb85c' : '#d9534f'; 
                                                $clientDiffSign = $clientDiff >= 0 ? '+' : '-'; 
                                                ?>
                                                <tr class="client-header-row" data-client-index="<?php echo $clientIndex; ?>" style="background-color: #e8f4fc;">
                                                    <td style="text-align:center; color: #333; vertical-align: middle; background-color: #e8f4fc;"><h5 style="margin: 0;"><?php echo htmlspecialchars($clientData['pm']); ?></h5></td>
                                                    <td style="font-weight: 600; color: #4c0bce; background-color: #e8f4fc;">
                                                        <span class="client-name-text" style="cursor: pointer;"><?php echo htmlspecialchars($clientName); ?></span>
                                                        <span class="client-toggle-icon" data-client-index="<?php echo $clientIndex; ?>" style="cursor: pointer; margin-left: 8px; font-weight: bold; font-size: 16px; display: inline-block; transition: color 0.2s;"><i class="fa fa-plus"></i></span>
                                                    </td>
                                                    <td class="center-col" style="background-color: #e8f4fc;"><?php echo htmlspecialchars($clientBillingLabel); ?></td>
                                                    <td class="num-col" style="font-weight: bold; font-size: 16px; color: #0d47a1; background-color: #e8f4fc;"><?php echo $fmtNum($clientProductionHours); ?></td>
                                                    <td class="num-col" style="font-weight: bold; font-size: 16px; color: #0d47a1; background-color: #e8f4fc;"><?php echo $fmtNum($clientGeneralHours); ?></td>
                                                    <td class="num-col" style="font-weight: bold; font-size: 16px; color: #0d47a1; background-color: #e8f4fc;"><?php echo $fmtNum($clientTotalHours); ?></td>
                                                    <td class="num-col" style="font-weight: bold; font-size: 16px; color: #0d47a1; background-color: #e8f4fc;"><?php echo $fmtNum($clientTotalInvoiceHours); ?></td>
                                                    <td class="num-col" style="font-weight: bold; color: <?php echo $clientDiffColor; ?>; background-color: #e8f4fc;"><?php echo $clientDiffSign . $fmtNum(abs($clientDiff)); ?></td>
                                                    <td style="background-color: #e8f4fc;"></td>
                                                    <td style="background-color: #e8f4fc;"></td>
                                                </tr>
                                                <?php foreach ($clientData['projects'] as $reportResult):
                                                    $projProductionHours = isset($reportResult->production_hours) ? (float) $reportResult->production_hours : (float) $reportResult->total_hours;
                                                    $projGeneralHours = isset($reportResult->general_hours) ? (float) $reportResult->general_hours : 0;
                                                ?>
                                                <tr class="client-project-row client-projects-<?php echo $clientIndex; ?>" style="display: none;">
                                                    <td style="font-size: 14px; font-weight: bold; color: #555; text-align: center;"><?php echo htmlspecialchars(!empty($reportResult->project_manager_name) ? $reportResult->project_manager_name : ''); ?></td>
                                                    <td style="padding-left: 28px; color: #666;"><i class="fa fa-angle-right" style="margin-right: 6px;"></i><i class="fa fa-folder" style="margin-right: 4px; color: #5cb85c;"></i><?php echo htmlspecialchars($reportResult->project_name); ?></td>
                                                    <td class="center-col"><?php echo !empty($reportResult->man_days) ? ucfirst(strtolower($reportResult->man_days)) : ''; ?></td>
                                                    <td class="num-col"><?php echo $fmtNum($projProductionHours); ?></td>
                                                    <td class="num-col"><?php echo $fmtNum($projGeneralHours); ?></td>
                                                    <td class="num-col"><?php echo $fmtNum($reportResult->total_hours); ?></td>
                                                    <td class="num-col" data-invoice-amt="<?php echo !empty($reportResult->project_invoice_amt) && $reportResult->project_invoice_amt > 0 ? $fmtNum($reportResult->project_invoice_amt) : '0'; ?>">
                                                        <span id="invoice_amt_<?php echo $reportResult->project_Id; ?>" class="invoice-amount-cell">
                                                            <?php echo !empty($reportResult->project_invoice_amt) && $reportResult->project_invoice_amt > 0 ? $fmtNum($reportResult->project_invoice_amt) : '0'; ?>
                                                        </span>
                                                        <?php if (!empty($show_invoice_buttons)): ?>
                                                        <span class="noExl" style="display: block; text-align: center; margin-top: 4px;">
                                                            <br>
                                                            <button type="button" class="btn btn-sm btn-primary noExl" id="invoice_btn_<?php echo $reportResult->project_Id; ?>"
                                                                data-project-id="<?php echo $reportResult->project_Id; ?>"
                                                                data-project-name="<?php echo htmlspecialchars($reportResult->project_name, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-invoice-amt="<?php echo !empty($reportResult->project_invoice_amt) ? $reportResult->project_invoice_amt : 0; ?>">
                                                                <i class="fa <?php echo !empty($reportResult->project_invoice_amt) ? 'fa-edit' : 'fa-plus'; ?>"></i> <?php echo !empty($reportResult->project_invoice_amt) ? 'Update Invoice' : 'Add Invoice'; ?>
                                                            </button>
                                                        </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <?php $projHours = floatval($reportResult->total_hours); $projInvoice = !empty($reportResult->project_invoice_amt) ? floatval($reportResult->project_invoice_amt) : 0; $projDiff = $projInvoice - $projHours; $projDiffColor = $projDiff >= 0 ? '#5cb85c' : '#d9534f'; $projDiffSign = $projDiff >= 0 ? '+' : '-'; ?>
                                                    <td class="num-col" style="font-weight: 600; color: <?php echo $projDiffColor; ?>"><?php echo $projDiffSign . $fmtNum(abs($projDiff)); ?></td>
                                                    <td class="center-col"><span class="label label-info"><?php echo $reportResult->num_employees; ?></span></td>
                                                    <td style="text-align: center;"><?php echo str_replace(',', '<br>', htmlspecialchars($reportResult->employee_names)); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <tr class="client-total-row client-projects-<?php echo $clientIndex; ?>" style="display: none; background-color: #e8e8e8;">
                                                    <td colspan="10" style="background-color: #e8e8e8; height: 20px;"></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- Displaying Search Result -->
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>       
        <?php endif;?>
    </div>
    
    <!-- Invoice Modal -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="invoiceModalLabel">Add/Update Invoice</h4>
                </div>
                <div class="modal-body">
                    <form id="invoiceForm">
                        <input type="hidden" id="invoice_project_id" name="project_id">
                        <div class="form-group">
                            <label for="invoice_project_name">Project Name:</label>
                            <input type="text" class="form-control" id="invoice_project_name" readonly>
                        </div>
                        <div class="form-group">
                            <label for="invoice_amount">Invoice Hours:</label>
                            <input type="number" class="form-control" id="invoice_amount" name="invoice_amount" step="0.01" min="0" required placeholder="Enter invoice amount">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveInvoice()">Update Invoice</button>
                </div>
            </div>
        </div>
    </div>
    
    <script language="javascript" type="text/javascript">
        function syncClientsYmSelect($select) {
            var val = $select.val();
            var $wrap = $select.closest('.kpi-ym-select-wrap');
            var $clear = $select.siblings('.kpi-ym-clear-icon');
            if (val !== null && String(val).trim() !== '') {
                $wrap.addClass('kpi-ym-wrap-selected');
                $clear.show();
            } else {
                $wrap.removeClass('kpi-ym-wrap-selected');
                $clear.hide();
            }
        }
        function clearClientsYmSelect(id) {
            var $select = $('#' + id);
            $select.val('');
            syncClientsYmSelect($select);
            if (id === 'from_year') {
                clearClientsYmSelect('from_month');
            } else if (id === 'to_year') {
                clearClientsYmSelect('to_month');
            }
        }
        function clearClientsMonthIfYearAll(yearSelectId, monthSelectId) {
            var yearVal = $('#' + yearSelectId).val();
            if (yearVal && String(yearVal).toUpperCase() === 'ALL') {
                var $month = $('#' + monthSelectId);
                $month.val('');
                syncClientsYmSelect($month);
            }
        }
        function clearAllClientsFilters() {
            window.location.href = '<?php echo base_url('clients/all_clients_reports'); ?>';
        }
        function resolveClientsReportHiddenDates() {
            var fy = $('#from_year').val();
            var fm = $('#from_month').val();
            var ty = $('#to_year').val();
            var tm = $('#to_month').val();
            if (!fy || !ty) {
                return false;
            }
            var fromYearAll = String(fy).toUpperCase() === 'ALL';
            var toYearAll = String(ty).toUpperCase() === 'ALL';
            if (!fromYearAll && (!fm || String(fm).trim() === '')) {
                return false;
            }
            if (!toYearAll && (!tm || String(tm).trim() === '')) {
                return false;
            }
            var startYear = 2010;
            var endYear = new Date().getFullYear();
            var currentMonth = new Date().getMonth() + 1;
            var fromY = fromYearAll ? startYear : parseInt(fy, 10);
            var toY = toYearAll ? endYear : parseInt(ty, 10);
            var fromM = (fm && String(fm).trim() !== '') ? parseInt(fm, 10) : 1;
            var toM = (tm && String(tm).trim() !== '') ? parseInt(tm, 10) : ((toY === endYear) ? currentMonth : 12);
            var fromDate = fromY + '-' + String(fromM).padStart(2, '0') + '-01';
            var lastDay = new Date(toY, toM, 0).getDate();
            var toDate = toY + '-' + String(toM).padStart(2, '0') + '-' + String(lastDay).padStart(2, '0');
            $('#form_date').val(fromDate);
            $('#to_date').val(toDate);
            return true;
        }

        $(document).ready(function() {
            $('.kpi-ym-clearable').each(function() {
                syncClientsYmSelect($(this));
            });
            clearClientsMonthIfYearAll('from_year', 'from_month');
            clearClientsMonthIfYearAll('to_year', 'to_month');
            $('.kpi-ym-clearable').on('change', function() {
                syncClientsYmSelect($(this));
            });
            $('#from_year').on('change', function() {
                clearClientsMonthIfYearAll('from_year', 'from_month');
                syncClientsYmSelect($(this));
            });
            $('#to_year').on('change', function() {
                clearClientsMonthIfYearAll('to_year', 'to_month');
                syncClientsYmSelect($(this));
            });

            // Toggle client projects: + show, - hide
            function toggleClientProjects(clientIndex) {
                var $projectRows = $('.client-projects-' + clientIndex);
                var $headerRow = $('.client-header-row[data-client-index="' + clientIndex + '"]');
                var $icon = $('.client-toggle-icon[data-client-index="' + clientIndex + '"] i');
                if ($projectRows.is(':visible')) {
                    $projectRows.hide();
                    $icon.removeClass('fa-minus').addClass('fa-plus');
                } else {
                    $projectRows.show();
                    $icon.removeClass('fa-plus').addClass('fa-minus');
                }
            }
            $('.client-toggle-icon').on('click', function(e) {
                e.preventDefault();
                var clientIndex = $(this).data('client-index');
                toggleClientProjects(clientIndex);
            });
            $('.client-name-text').on('click', function(e) {
                e.preventDefault();
                var clientIndex = $(this).closest('.client-header-row').data('client-index');
                toggleClientProjects(clientIndex);
            });

            $('#emp_search_log').on('submit', function(e) {
                var $alert = $('#search_validation_alert');
                $alert.hide().text('');

                var fromYear = $.trim($('#from_year').val());
                var fromMonth = $.trim($('#from_month').val());
                var toYear = $.trim($('#to_year').val());
                var toMonth = $.trim($('#to_month').val());

                if (!fromYear || !toYear) {
                    e.preventDefault();
                    $alert.text('Please select From and To year to search.').show();
                    return false;
                }
                if (fromYear.toUpperCase() !== 'ALL' && !fromMonth) {
                    e.preventDefault();
                    $alert.text('Please select From month when year is not ALL.').show();
                    return false;
                }
                if (toYear.toUpperCase() !== 'ALL' && !toMonth) {
                    e.preventDefault();
                    $alert.text('Please select To month when year is not ALL.').show();
                    return false;
                }
                if (!resolveClientsReportHiddenDates()) {
                    e.preventDefault();
                    $alert.text('Please enter valid From and To dates.').show();
                    return false;
                }
            });

            function bindAllOptionSelect2(selector) {
                var $el = $(selector);
                $el.select2({ placeholder: 'Select…', allowClear: true });
                $el.on('select2:select', function(e) {
                    if (e.params.data.id === 'all') {
                        $el.val(['all']).trigger('change');
                    } else {
                        var vals = $el.val() || [];
                        var i = vals.indexOf('all');
                        if (i !== -1) {
                            vals.splice(i, 1);
                            $el.val(vals).trigger('change');
                        }
                    }
                });
            }
            bindAllOptionSelect2('#client_Id');
            bindAllOptionSelect2('#department');
            bindAllOptionSelect2('#project_manager');
            $('#project_Id').select2({ placeholder: 'All projects', allowClear: true });

            var projectsFilterUrl = '<?php echo base_url('clients/get_all_clients_report_projects'); ?>';
            function setProjectOptions(projects) {
                var previous = $('#project_Id').val();
                var $select = $('#project_Id');
                $select.empty();
                $select.append(new Option('All projects', 'all', false, !previous || previous === 'all'));
                $.each(projects || [], function(_, item) {
                    var id = String(item.project_Id);
                    $select.append(new Option(item.project_name, id, false, previous === id));
                });
                if (previous && previous !== 'all' && !$select.find('option[value="' + previous + '"]').length) {
                    $select.val('all');
                }
                $select.trigger('change');
            }
            function loadProjectsByFilters() {
                $.ajax({
                    url: projectsFilterUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        department: $('#department').val(),
                        client_Id: $('#client_Id').val(),
                        project_manager: $('#project_manager').val()
                    }
                }).done(function(response) {
                    setProjectOptions(response && response.projects ? response.projects : []);
                });
            }
            $('#department, #client_Id, #project_manager').on('change', function() {
                loadProjectsByFilters();
            });

            $('#send_client_report_btn').on('click', function() {
            var $btn = $(this);
            var originalHtml = $btn.html();
            if (!resolveClientsReportHiddenDates()) {
                if (!$('#form_date').val() || !$('#to_date').val()) {
                    alert('Please select From and To year and month before sending the report.');
                    return;
                }
            }
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
            var formData = {
                form_date: $('#form_date').val(),
                to_date: $('#to_date').val(),
                from_year: $('#from_year').val(),
                from_month: $('#from_month').val(),
                to_year: $('#to_year').val(),
                to_month: $('#to_month').val(),
                department: $('#department').val(),
                client_Id: $('#client_Id').val(),
                project_Id: $('#project_Id').val(),
                project_manager: $('#project_manager').val()
            };
            $.ajax({
                url: '<?php echo base_url("clients/send_client_report_email"); ?>',
                type: 'POST',
                dataType: 'json',
                data: formData,
                success: function(res) {
                    if (res.success) {
                        alert(res.message || 'Report sent to laxmikanth@elogictech.com');
                    } else {
                        alert(res.message || 'Failed to send report.');
                    }
                },
                error: function(xhr) {
                    var msg = 'Failed to send report.';
                    try {
                        var r = JSON.parse(xhr.responseText);
                        if (r.message) msg = r.message;
                    } catch (e) {}
                    alert(msg);
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
            });

            $("#downloadEmployeeData_total_client_Data").click(function() {
                if (!resolveClientsReportHiddenDates()) {
                    if (!$('#form_date').val() || !$('#to_date').val()) {
                        alert('Please select From and To year and month before exporting.');
                        return;
                    }
                }
                var $form = $('<form>', {
                    method: 'POST',
                    action: '<?php echo base_url('clients/download_all_clients_report_excel'); ?>',
                    target: '_blank'
                });
                $('#emp_search_log').find('input, select').each(function() {
                    var $el = $(this);
                    var name = $el.attr('name');
                    if (!name) return;
                    if ($el.is(':checkbox') || $el.is(':radio')) {
                        if (!$el.is(':checked')) return;
                    }
                    if ($el.is('select[multiple]')) {
                        ($el.val() || []).forEach(function(val) {
                            $form.append($('<input>', { type: 'hidden', name: name, value: val }));
                        });
                    } else {
                        $form.append($('<input>', { type: 'hidden', name: name, value: $el.val() }));
                    }
                });
                $('body').append($form);
                $form.submit();
                $form.remove();
            });
        });

        // Function to open invoice modal
        function openInvoiceModal(projectId, projectName, currentAmount) {
            $('#invoice_project_id').val(projectId);
            $('#invoice_project_name').val(projectName);
            $('#invoice_amount').val(currentAmount);
            $('#invoiceModalLabel').text('Add/Update Invoice - ' + projectName);
            $('#invoiceModal').modal('show');
        }
        
        // Handle invoice button clicks using data attributes (handles special characters properly)
        $(document).on('click', 'button[data-project-id]', function() {
            var projectId = $(this).data('project-id');
            var projectName = $(this).data('project-name');
            var invoiceAmt = $(this).data('invoice-amt') || 0;
            openInvoiceModal(projectId, projectName, invoiceAmt);
        });
        
        // Function to save invoice
        function saveInvoice() {
            var projectId = $('#invoice_project_id').val();
            var invoiceAmount = $('#invoice_amount').val();
            
            if (!invoiceAmount || invoiceAmount < 0) {
                alert('Please enter a valid invoice amount.');
                return;
            }
            
            // Show loading
            var saveBtn = $('.modal-footer .btn-primary');
            var originalText = saveBtn.html();
            saveBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
            
            $.ajax({
                url: '<?php echo base_url("clients/update_project_invoice"); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    project_id: projectId,
                    invoice_amount: invoiceAmount,
                    form_date: $('#form_date').val()
                },
                success: function(response) {
                    if (response.success) {
                        var n = parseFloat(invoiceAmount);
                        var formattedAmount = (n === Math.floor(n)) ? String(Math.floor(n)) : (Math.round(n * 100) / 100).toString().replace(/\.?0+$/, '');
                        
                        // Update the invoice amount display
                        $('#invoice_amt_' + projectId).text(formattedAmount);
                        
                        // Update the data-invoice-amt attribute for Excel export
                        $('#invoice_amt_' + projectId).closest('td').attr('data-invoice-amt', formattedAmount);
                        
                        // Update button text, icon, and data attribute
                        var invoiceBtn = $('#invoice_btn_' + projectId);
                        invoiceBtn.attr('data-invoice-amt', invoiceAmount);
                        if (parseFloat(invoiceAmount) > 0) {
                            invoiceBtn.html('<i class="fa fa-edit"></i> Update Invoice');
                        } else {
                            invoiceBtn.html('<i class="fa fa-plus"></i> Add Invoice');
                        }
                        
                        $('#invoiceModal').modal('hide');
                        alert('Invoice updated successfully!');
                    } else {
                        alert('Error: ' + (response.message || 'Failed to update invoice'));
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error updating invoice. Please try again.');
                    console.error('Error:', error);
                },
                complete: function() {
                    saveBtn.html(originalText).prop('disabled', false);
                }
            });
        }
    </script>
    <!-- Include Footer here -->
    <?php $this->load->view('includes/cRMFooter'); ?>
    <!-- Include Footer here END -->
