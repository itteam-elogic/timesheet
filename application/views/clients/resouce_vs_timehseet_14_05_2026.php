    <!-- Include Header here -->
    <?php 
    $this->load->view('includes/cRMHeader'); 

    $getProjectID = !empty($_REQUEST['project_Id']) ? $_REQUEST['project_Id'] : '';
    $getFromDate = !empty($form_date) ? $form_date : (!empty($_REQUEST['form_date']) ? $_REQUEST['form_date'] : date('Y-m-d'));
    $getToDate = !empty($to_date) ? $to_date : (!empty($_REQUEST['to_date']) ? $_REQUEST['to_date'] : date('Y-m-d'));
    ?>
    <div class="content-wrapper">
        <div class="page-title">
            <div>
                <h1><i class="fa fa-bell"></i>Live Project Allocation & Actual hours</h1>
            </div>
            <div>
                <a class="btn btn-primary btn-flat" href="<?php echo base_url('clients/rs_vs_ts');?>" data-toggle="tooltip" title="Refresh"><i class="fa fa-refresh"></i> Refresh</a>
            </div>
        </div>
        <div class="card">
            <h3 class="card-title"></h3>
            <div class="card-body">
                <div class="row">
                    <!-- Search for employee with date wise and client, project wise as well. -->
                    <div class="col-md-12">
                        <div class="bs-component">
                            <div class="tab-content" id="myTabContent">
                                <!-- Employee Report adding block -->
                                <form name="emp_search_log" id="emp_search_log" method="post" action="<?php echo base_url('clients/rs_vs_ts');?>">
                                    <div class="tab-pane fade active in" id="Add">
                                        <input type="hidden" name="client_Id" id="client_Id" value="all">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Search</label>
                                                    <input type="text" class="form-control" id="rs_vs_ts_search" name="rs_vs_ts_search" placeholder="Client Name, Project Name, Project Manager, Department" value="" autocomplete="off">
                                                    <small class="text-muted">Filter grid by client, project, PM or department. Leave empty to show all.</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">From Date</label>
                                                    <input class="form-control" type="text" id="form_date" name="form_date" value="<?=$getFromDate;?>" placeholder="Select From Date" readonly="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">To Date</label>
                                                    <input class="form-control" type="text" id="to_date" name="to_date" value="<?=$getToDate;?>" placeholder="Select To Date" readonly="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <button class="btn btn-primary icon-btn" id="searchBtn">
                                                <i class="fa fa-fw fa-lg fa-check-circle"></i>Search
                                            </button>
                                            <a href="<?php echo base_url();?>empreports" data-toggle="Go To Report Log!" title="Cancel">
                                                <button class="btn btn-default icon-btn" type="button">
                                                    <i class="fa fa-chevron-circle-left"></i>Back
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </form>
                                <!-- Employee Report adding block -->
                            </div>
                        </div>
                    </div>
                    <!-- Search for employee with date wise and client, project wise as well. -->
                </div>
            </div>
        </div>
         <?php if(!empty($allClientResult)):?>
        <?php
                                                // Manager-wise totals (exclude blocked clients and N/A manager)
                                                $eLogicClientsIds = array('363','374','370','369','368','367','364','361','355','270','262','253','236','210','85','78','74','49','34','32','428');
                                                $excludedClientNames = array('elogic solutions ( software )', 'elogic solutions(farhan)');
                                                $excludedManagerNames = array('rupali modi');
                                                $managerTotals = array();
                                                $sortedClientResult = array();
                                                foreach ($allClientResult as $reportResult) {
                                                    $pmName = isset($reportResult->project_manager_name) ? trim($reportResult->project_manager_name) : '';
                                                    $isExcludedClient = in_array($reportResult->client_Id, $eLogicClientsIds);
                                                    $isNaManager = ($pmName === '' || strtoupper($pmName) === 'N/A');
                                                    $isExcludedManager = in_array(strtolower($pmName), $excludedManagerNames, true);
                                                    $clientNameNormalized = strtolower(trim(isset($reportResult->client_name) ? (string)$reportResult->client_name : ''));
                                                    $isBlockedClientName = in_array($clientNameNormalized, $excludedClientNames, true);
                                                    $isElogicSolutionsClient = (bool)preg_match('/^elogic solutions\\s*\\(/i', $clientNameNormalized);
                                                    $dept = trim(isset($reportResult->department) ? $reportResult->department : '');
                                                    $projectNameForFilter = trim(isset($reportResult->project_name) ? $reportResult->project_name : '');
                                                    $isGeneralProject = (strtolower($dept) === 'general' || stripos($projectNameForFilter, 'general') !== false);
                                                    if (($isExcludedClient && !($isElogicSolutionsClient && $isGeneralProject)) || $isNaManager || $isBlockedClientName || $isExcludedManager) continue;
                                                    // Normalize manager key to avoid duplicate buckets from extra spaces/case.
                                                    $managerKey = preg_replace('/\s+/', ' ', strtolower($pmName));
                                                    if (!isset($managerTotals[$managerKey])) {
                                                        $managerTotals[$managerKey] = array(
                                                            'manager_name' => $pmName,
                                                            'resource_hours' => 0,
                                                            'timesheet_hours' => 0,
                                                            'approved_timesheet_hours' => 0,
                                                            'unapproved_timesheet_hours' => 0
                                                        );
                                                    }
                                                    $managerTotals[$managerKey]['resource_hours'] += !empty($reportResult->schedule_hours) ? (float)$reportResult->schedule_hours : 0;
                                                    $managerTotals[$managerKey]['timesheet_hours'] += !empty($reportResult->timesheet_hours) ? (float)$reportResult->timesheet_hours : 0;
                                                    $managerTotals[$managerKey]['approved_timesheet_hours'] += !empty($reportResult->approved_timesheet_hours) ? (float)$reportResult->approved_timesheet_hours : 0;
                                                    $managerTotals[$managerKey]['unapproved_timesheet_hours'] += !empty($reportResult->unapproved_timesheet_hours) ? (float)$reportResult->unapproved_timesheet_hours : 0;
                                                    $sortedClientResult[] = $reportResult;
                                                }
                                                uasort($managerTotals, function($a, $b) {
                                                    return strcasecmp($a['manager_name'], $b['manager_name']);
                                                });
                                                $displayGroups = array();
                                                foreach ($managerTotals as $totals) {
                                                    $displayGroups[$totals['manager_name']] = array(
                                                        'resource_hours' => $totals['resource_hours'],
                                                        'timesheet_hours' => $totals['timesheet_hours'],
                                                        'approved_timesheet_hours' => $totals['approved_timesheet_hours'],
                                                        'unapproved_timesheet_hours' => $totals['unapproved_timesheet_hours']
                                                    );
                                                }
                                                $grandResource = 0;
                                                $grandTimesheet = 0;
                                                foreach ($displayGroups as $d) {
                                                    $grandResource += $d['resource_hours'];
                                                    $grandTimesheet += $d['timesheet_hours'];
                                                }

        ?>
        <div class="card">
            <div class="card-body">               
                <div class="tab-content">                    
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div style="text-align:right; position:relative;bottom:10px; right:18px;">
                                    <button type="button" id="send_rs_vs_ts_report_btn" class="btn btn-info btn-flat" style="margin-right: 8px;"><i class="fa fa-envelope"></i> Send</button>
                                    <button type="button" id="downloadEmployeeData_total_client_Data" class="btn btn-primary btn-flat"><i class="fa fa-file-excel-o"></i> Generate report</button>
                                </div>
                                <!-- Reporting Manager-wise totals summary (top of grid) -->
                                <div class="col-md-12" style="margin-bottom: 16px;">
                                    <div class="table-responsive rs-vs-ts-dept-summary">
                                        <table class="table table-bordered rs-vs-ts-dept-table" id="rs_vs_ts_dept_totals_table">
                                            <thead>
                                                <tr class="rs-vs-ts-dept-thead">
                                                    <th class="rs-vs-ts-th-dept">Manager Name</th>
                                                    <th class="rs-vs-ts-th-num">Resource Hours</th>
                                                    <th class="rs-vs-ts-th-num">Timesheet Hours</th>
                                                    <th class="rs-vs-ts-th-num">Difference Hours</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($displayGroups as $rowLabel => $totals):
                                                    $resH = $totals['resource_hours'];
                                                    $tsH  = $totals['timesheet_hours'];
                                                    $diff = $tsH - $resH;
                                                ?>
                                                <tr class="rs-vs-ts-dept-data-row">
                                                    <td class="rs-vs-ts-cell-dept"><?php echo htmlspecialchars($rowLabel); ?></td>
                                                    <td class="rs-vs-ts-cell-total-hrs"><?php echo number_format($resH, 2); ?></td>
                                                    <td class="rs-vs-ts-cell-invoice-hrs"><?php echo number_format($tsH, 2); ?></td>
                                                    <td class="rs-vs-ts-cell-diff <?php echo $diff < 0 ? 'rs-vs-ts-diff-negative' : ($diff > 0 ? 'rs-vs-ts-diff-positive' : ''); ?>"><?php echo number_format($diff, 2); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <tr class="rs-vs-ts-grand-total-row">
                                                    <td class="rs-vs-ts-cell-dept">Grand Total</td>
                                                    <td class="rs-vs-ts-cell-total-hrs"><?php echo number_format($grandResource, 2); ?></td>
                                                    <td class="rs-vs-ts-cell-invoice-hrs"><?php echo number_format($grandTimesheet, 2); ?></td>
                                                    <td class="rs-vs-ts-cell-diff <?php echo ($grandTimesheet - $grandResource) < 0 ? 'rs-vs-ts-diff-negative' : (($grandTimesheet - $grandResource) > 0 ? 'rs-vs-ts-diff-positive' : ''); ?>"><?php echo number_format($grandTimesheet - $grandResource, 2); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- Displaying Search Result -->
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered" id="table22excel_all">
                                            <div style="clear:both"></div>                        
                                            <thead>
                                                <tr style="font-weight: bold; background-color: #337ab7; color: #fff;">
													<th style="text-align: center;">Project Manager</th>
                                                    <th style="width: 120px;">Client Name / Projects</th>
                                                    <th style="text-align: center; width: 120px;">Resource Hours</th>
                                                    <th style="text-align: center; width: 120px;">Timesheet Hours</th>
													<th style="text-align: center; width: 120px;">Difference</th>
                                                 </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $currentManager = '';
                                                $currentClient = '';
                                                $clientTotalHours = 0;
                                                $clientResourceHours = 0;
                                               $eLogicClientsIds = array('363','374','370','369','368','367','364','361','355','270','262','253','236','210','85','78','74','49','34','32','428');
                                                $clientIndex = 0;
                                                $managerFirstRow = false;
                                                $firstProjectInClient = false;
                                                
                                                // Sort by client_name first (alphabetical), then project_manager_name
                                                usort($sortedClientResult, function($a, $b) {
                                                    $clientA = !empty($a->client_name) ? $a->client_name : '';
                                                    $clientB = !empty($b->client_name) ? $b->client_name : '';
                                                    $clientCompare = strcasecmp($clientA, $clientB);
                                                    if ($clientCompare !== 0) {
                                                        return $clientCompare;
                                                    }
                                                    $managerA = !empty($a->project_manager_name) ? $a->project_manager_name : 'N/A';
                                                    $managerB = !empty($b->project_manager_name) ? $b->project_manager_name : 'N/A';
                                                    return strcasecmp($managerA, $managerB);
                                                });
                                                
                                                // Build unique list for search autosuggest (Client, Project, PM, Department)
                                                $searchSuggestions = array();
                                                foreach ($sortedClientResult as $reportResult) {
                                                    $cn = trim(isset($reportResult->client_name) ? $reportResult->client_name : '');
                                                    $pn = trim(isset($reportResult->project_name) ? $reportResult->project_name : '');
                                                    $pm = trim(isset($reportResult->project_manager_name) ? $reportResult->project_manager_name : '');
                                                    $dept = trim(isset($reportResult->department) ? $reportResult->department : '');
                                                    if ($cn !== '') $searchSuggestions[$cn] = true;
                                                    if ($pn !== '') $searchSuggestions[$pn] = true;
                                                    if ($pm !== '' && $pm !== 'N/A') $searchSuggestions[$pm] = true;
                                                    if ($dept !== '') $searchSuggestions[$dept] = true;
                                                }
                                                $searchSuggestions = array_keys($searchSuggestions);
                                                sort($searchSuggestions, SORT_STRING | SORT_FLAG_CASE);
                                                
                                                // Group data by manager (unique), then by client (same client can appear under multiple managers with different projects)
                                                $byManager = array();
                                                foreach ($sortedClientResult as $key => $reportResult) {
                                                    $managerName = !empty($reportResult->project_manager_name) ? trim($reportResult->project_manager_name) : 'N/A';
                                                    $clientName = trim($reportResult->client_name);
                                                    if (!isset($byManager[$managerName])) {
                                                        $byManager[$managerName] = array(
                                                            'totals' => array('resource_hours' => 0, 'timesheet_hours' => 0, 'approved_timesheet_hours' => 0, 'unapproved_timesheet_hours' => 0),
                                                            'clients' => array()
                                                        );
                                                    }
                                                    $byManager[$managerName]['totals']['resource_hours'] += !empty($reportResult->schedule_hours) ? (float)$reportResult->schedule_hours : 0;
                                                    $byManager[$managerName]['totals']['timesheet_hours'] += !empty($reportResult->timesheet_hours) ? (float)$reportResult->timesheet_hours : 0;
                                                    $byManager[$managerName]['totals']['approved_timesheet_hours'] += !empty($reportResult->approved_timesheet_hours) ? (float)$reportResult->approved_timesheet_hours : 0;
                                                    $byManager[$managerName]['totals']['unapproved_timesheet_hours'] += !empty($reportResult->unapproved_timesheet_hours) ? (float)$reportResult->unapproved_timesheet_hours : 0;
                                                    if (!isset($byManager[$managerName]['clients'][$clientName])) {
                                                        $byManager[$managerName]['clients'][$clientName] = array('projects' => array());
                                                    }
                                                    $byManager[$managerName]['clients'][$clientName]['projects'][] = $reportResult;
                                                }
                                                ksort($byManager, SORT_STRING);

                                                // Display: one row per client; always show manager name so it stays visible even after filtering
                                                foreach ($byManager as $projectManagerName => $managerData):
                                                    $managerResourceTotal = $managerData['totals']['resource_hours'];
                                                    $managerTimesheetTotal = $managerData['totals']['timesheet_hours'];
                                                    $managerApprovedTotal = $managerData['totals']['approved_timesheet_hours'];
                                                    $managerUnapprovedTotal = $managerData['totals']['unapproved_timesheet_hours'];
                                                    $managerDiff = $managerResourceTotal - $managerTimesheetTotal;
                                                ?>
                                                <tr class="manager-total-row" data-manager="<?php echo htmlspecialchars(strtolower(trim($projectManagerName)), ENT_QUOTES, 'UTF-8'); ?>" data-search="<?php echo htmlspecialchars(strtolower(trim($projectManagerName)), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <td style="text-align:center; vertical-align: middle; color: #1f4e79; font-weight: 700; padding: 14px 12px; width: 150px; font-size: 16px;"><?php echo $projectManagerName; ?></td>
                                                    <td style="font-weight: 700; color: #1f4e79; font-size: 16px; padding: 14px 12px; vertical-align: middle; background:#eef5fb;">Team Wise Total Hours</td>
                                                    <td style="text-align: right; vertical-align: middle; padding: 14px 12px; width: 120px; font-weight: 700; font-size: 16px; background:#eef5fb;"><?php echo number_format($managerResourceTotal, 2); ?></td>
                                                    <td style="text-align: right; vertical-align: middle; padding: 14px 12px; width: 120px; font-weight: 700; font-size: 16px; background:#eef5fb;"><?php echo number_format($managerTimesheetTotal, 2); ?></td>
                                                    <td style="text-align: right; vertical-align: middle; padding: 14px 12px; width: 120px; font-weight: 700; font-size: 16px; color: <?php echo $managerDiff != 0 ? '#d9534f' : '#5cb85c'; ?>; background:#eef5fb;"><?php echo number_format($managerDiff, 2); ?></td>
                                                </tr>
                                                <?php
                                                    $clients = $managerData['clients'];
                                                    $isFirstClientForManager = true;
                                                    foreach ($clients as $clientName => $clientData):
                                                        $clientIndex++;
                                                        $firstProj = reset($clientData['projects']);
                                                        $clientDept = isset($firstProj->department) ? trim($firstProj->department) : '';
                                                        $clientSearchText = strtolower(trim($clientName) . ' ' . trim($projectManagerName) . ' ' . $clientDept);
                                                        // Totals for this client under this manager only (same client under different managers shows separate totals)
                                                        $clientScheduleTotal = 0;
                                                        $clientTimesheetTotal = 0;
                                                        foreach ($clientData['projects'] as $reportResult) {
                                                            $clientScheduleTotal += !empty($reportResult->schedule_hours) ? (float)$reportResult->schedule_hours : 0;
                                                            $clientTimesheetTotal += !empty($reportResult->timesheet_hours) ? (float)$reportResult->timesheet_hours : 0;
                                                        }
                                                ?>
                                                <tr class="client-header-row" data-manager="<?php echo htmlspecialchars(strtolower(trim($projectManagerName)), ENT_QUOTES, 'UTF-8'); ?>" data-client-index="<?php echo $clientIndex; ?>" data-search="<?php echo htmlspecialchars($clientSearchText, ENT_QUOTES, 'UTF-8'); ?>">
													<td style="text-align:center; vertical-align: middle; color: #2c5aa0; font-weight: 600; padding: 14px 12px; width: 150px; font-size: 16px;"><?php echo $isFirstClientForManager ? '' : ''; ?></td>
                                                    <td style="font-weight: 600; color: #2c3e50; font-size: 16px; padding: 14px 12px; vertical-align: middle;" class="client-name-cell" data-client-index="<?php echo $clientIndex; ?>">
														<div style="line-height: 1.5;">
															<span class="excel-hide"><i class="fa fa-building" style="margin-right: 8px; color: #337ab7; font-size: 16px;"></i></span>
															<span class="client-name-text" style="font-weight: 600; color: #2c3e50; cursor: pointer; font-size: 16px;"><?php echo $clientName; ?></span>
															<span class="client-toggle-icon excel-hide" data-client-index="<?php echo $clientIndex; ?>" style="cursor: pointer; margin-left: 10px; color: #337ab7; font-weight: bold; font-size: 18px; display: inline-block; width: 20px; text-align: center; vertical-align: middle;">
																<i class="fa fa-plus"></i>
															</span>
														</div>
													</td>
                                                    <td style="text-align: right; vertical-align: middle; padding: 14px 12px; width: 120px; font-weight: 600; font-size: 16px;"><strong><?php echo number_format($clientScheduleTotal, 2); ?></strong></td>
                                                    <td style="text-align: right; vertical-align: middle; padding: 14px 12px; width: 120px; font-weight: 600; font-size: 16px;"><strong><?php echo number_format($clientTimesheetTotal, 2); ?></strong></td>
                                                    <td style="text-align: right; vertical-align: middle; padding: 14px 12px; width: 120px; font-weight: 700; font-size: 16px; color: <?php echo ($clientScheduleTotal - $clientTimesheetTotal) != 0 ? '#d9534f' : '#5cb85c'; ?>;"><strong><?php echo number_format($clientScheduleTotal - $clientTimesheetTotal, 2); ?></strong></td>                                                    
                                                </tr>
                                                <?php
                                                    // Display all projects for this client
                                                    foreach ($clientData['projects'] as $projectKey => $reportResult):
                                                        $scheduleHours = !empty($reportResult->schedule_hours) ? $reportResult->schedule_hours : 0;
                                                        $timesheetHours = !empty($reportResult->timesheet_hours) ? $reportResult->timesheet_hours : 0;
                                                        $projectName = !empty($reportResult->project_name) ? $reportResult->project_name : 'N/A';
                                                        $projDept = isset($reportResult->department) ? trim($reportResult->department) : '';
                                                        $projectSearchText = strtolower(trim($clientName) . ' ' . trim($projectName) . ' ' . trim($projectManagerName) . ' ' . $projDept);
                                                ?>
                                                <tr class="client-project-row client-projects-<?php echo $clientIndex; ?>"
                                                    style="display: none;"
                                                    data-manager="<?php echo htmlspecialchars(strtolower(trim($projectManagerName)), ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-client-index="<?php echo $clientIndex; ?>"
                                                    data-search="<?php echo htmlspecialchars($projectSearchText, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-department="<?php echo htmlspecialchars($projDept, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-schedule-hours="<?php echo (float)$scheduleHours; ?>"
                                                    data-timesheet-hours="<?php echo (float)$timesheetHours; ?>">
                                                    <td style="padding: 14px 12px; width: 150px;"></td>
                                                    <td style="padding-left: 60px; color: #666; padding: 14px 12px; vertical-align: middle; font-size: 15px;">
														<span class="excel-hide"><i class="fa fa-angle-right" style="margin-right: 10px; color: #999; font-size: 14px;"></i><i class="fa fa-folder" style="margin-right: 6px; color: #5cb85c; font-size: 15px;"></i></span>
														<?php echo $projectName; ?>
													</td>
                                                    <td style="text-align: right; padding: 14px 12px; vertical-align: middle; width: 120px; font-weight: 500; font-size: 15px;"><?php echo number_format($scheduleHours, 2); ?></td>
                                                    <td style="text-align: right; padding: 14px 12px; vertical-align: middle; width: 120px; font-weight: 500; font-size: 15px;"><?php echo number_format($timesheetHours, 2); ?></td>
                                                    <td style="text-align: right; padding: 14px 12px; vertical-align: middle; width: 120px; font-weight: 600; font-size: 15px; <?php echo ($scheduleHours - $timesheetHours) != 0 ? 'color: #d9534f;' : 'color: #5cb85c;'; ?>">
														<?php echo number_format($scheduleHours - $timesheetHours, 2); ?>
													</td>                                                    
                                                </tr>
                                                <?php
                                                    endforeach;
                                                    $isFirstClientForManager = false;
                                                    endforeach;
                                                endforeach; ?>
                                                <?php
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>       
        <?php endif;?>
    </div>
    <style>
        /* Date input highlighting styles */
        #form_date, #to_date {
            transition: background-color 0.3s ease;
        }
        #form_date.has-date, #to_date.has-date {
            background-color: #e8f5e9 !important;
            border-color: #4caf50 !important;
            font-weight: 500;
        }
        #form_date.has-date:focus, #to_date.has-date:focus {
            background-color: #c8e6c9 !important;
            border-color: #2e7d32 !important;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }
        
        /* Department-wise summary table (top of grid) - centered, wider */
        .rs-vs-ts-dept-summary {
            max-width: 800px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 16px;
            border: 1px solid #c5cdd3;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        #rs_vs_ts_dept_totals_table {
            margin: 0 auto;
            width: 100%;
            min-width: 560px;
            background: #fff;
            border-collapse: collapse;
            border-radius: 8px;
            table-layout: fixed;
        }
        #rs_vs_ts_dept_totals_table thead th {
            padding: 14px 16px;
            font-size: 14px;
            font-weight: bold;
            border: none;
            vertical-align: middle;
        }
        .rs-vs-ts-th-dept {
            text-align: left;
            width: auto;
        }
        .rs-vs-ts-th-num {
            text-align: right;
            width: 150px;
            min-width: 150px;
        }
        .rs-vs-ts-dept-thead {
            background: rgb(45, 90, 125) !important;
            color: #fff !important;
        }
        .rs-vs-ts-dept-thead th:first-child { border-radius: 8px 0 0 0; }
        .rs-vs-ts-dept-thead th:last-child { border-radius: 0 8px 0 0; }
        #rs_vs_ts_dept_totals_table tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
            font-weight: 600;
            vertical-align: middle;
            font-variant-numeric: tabular-nums;
        }
        #rs_vs_ts_dept_totals_table tbody tr:last-child td {
            border-bottom: none;
        }
        .rs-vs-ts-cell-dept,
        .rs-vs-ts-cell-diff {
            background-color: #eef3f7 !important;
            color: #1e293b;
            text-align: left !important;
        }
        .rs-vs-ts-cell-total-hrs {
            background-color: #f0fdf4 !important;
            color: #166534;
            width: 150px;
            padding-right: 20px !important;
            text-align: center !important;
        }
        .rs-vs-ts-cell-invoice-hrs {
            background-color: #fcfbef !important;
            color: #374151;
            text-align: center !important;
            width: 150px;
            padding-right: 20px !important;
        }
        .rs-vs-ts-cell-diff { text-align: center !important; width: 150px; padding-right: 20px !important; }
        .rs-vs-ts-diff-negative {
            color: #dc2626 !important;
            font-weight: 700 !important;
        }
        .rs-vs-ts-diff-positive {
            color: #16a34a !important;
            font-weight: 700 !important;
        }
        .rs-vs-ts-grand-total-row .rs-vs-ts-cell-dept,
        .rs-vs-ts-grand-total-row .rs-vs-ts-cell-diff {
            background-color: #e0e8f0 !important;
        }
        .rs-vs-ts-grand-total-row .rs-vs-ts-cell-total-hrs {
            background-color: #dcfce7 !important;
        }
        .rs-vs-ts-grand-total-row .rs-vs-ts-cell-invoice-hrs {
            background-color: #fef9c3 !important;
        }
        .rs-vs-ts-grand-total-row {
            border-top: 2px solid rgb(45, 90, 125);
        }
        .rs-vs-ts-grand-total-row td {
            padding: 14px 16px !important;
            padding-right: 20px !important;
            font-weight: 700 !important;
        }
        .rs-vs-ts-grand-total-row .rs-vs-ts-cell-dept {
            padding-right: 16px !important;
        }

        #table22excel_all {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            table-layout: auto;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        #table22excel_all thead th {
            padding: 14px 12px;
            border-bottom: 3px solid #2c5aa0;
            vertical-align: middle;
            background: linear-gradient(to bottom, #337ab7, #2c5aa0);
            color: #fff;
            font-weight: 600;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-right: 1px solid rgba(255,255,255,0.1);
            white-space: nowrap;
        }
        #table22excel_all thead th:last-child {
            border-right: none;
        }
        #table22excel_all tbody td {
            padding: 14px 12px;
            border-bottom: 1px solid #e8e8e8;
            vertical-align: middle;
            font-size: 13px;
            white-space: nowrap;
        }
        #table22excel_all tbody td[style*="text-align: right"] {
            text-align: center !important;
            font-family: 'Courier New', monospace;
            font-weight: 500;
        }
        #table22excel_all tbody tr {
            border-bottom: 1px solid #e8e8e8;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        #table22excel_all tbody tr:hover {
            background-color: #f5f8fa;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .client-header-row {
            background-color: #f8f9fa !important;
            border-left: 4px solid #337ab7;
        }
        .client-name-cell {
            cursor: pointer;
            transition: all 0.2s;
        }
        #table22excel_all tbody tr {
            height: auto;
        }
        .client-name-cell:hover {
            color: #2c5aa0 !important;
        }
        .client-name-cell .client-name-text {
            display: inline-block;
            font-weight: 600;
        }
        .client-toggle-icon {
            transition: all 0.2s ease;
            user-select: none;
        }
        .client-toggle-icon:hover {
            transform: scale(1.2);
        }
        .client-project-row {
            background-color: #ffffff;
        }
        .client-project-row:hover {
            background-color: #f0f4f7;
        }
        .client-project-row td {
            vertical-align: middle !important;
            padding-left: 60px !important;
            white-space: nowrap !important;
        }
        .table-responsive {
            border: 1px solid #d0d0d0;
            border-radius: 6px;
            overflow-x: auto;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        #table22excel_all tbody td[style*="text-align: right"] {
            white-space: nowrap;
        }
        .client-total-row {
            background: linear-gradient(to bottom, #e8f4f8, #d8e8f0) !important;
            border-top: 2px solid #b8d4e8;
            font-weight: 600;
        }
        .client-total-row td {
            vertical-align: middle !important;
            padding: 12px 12px !important;
            white-space: nowrap !important;
        }
        .client-total-row:hover {
            background: linear-gradient(to bottom, #d8e8f0, #c8d8e0) !important;
        }
        /* Improve icon styling */
        .fa-building {
            color: #337ab7;
            font-size: 16px;
        }
        .fa-folder {
            color: #5cb85c;
            font-size: 15px;
        }
        .fa-angle-right {
            color: #999;
            font-size: 14px;
        }
        /* Better spacing for nested content */
        .client-name-cell > div {
            line-height: 1.6;
        }
        /* Number formatting */
        #table22excel_all tbody td[style*="text-align: right"] {
            padding-right: 16px;
        }
        /* Remove duplicate border */
        #table22excel_all tbody tr:last-child td {
            border-bottom: none;
        }
        /* Final fix for manager rowspan cell vertical alignment */
        .manager-rowspan-cell {
            vertical-align: middle !important;
            text-align: center !important;
        }
        /* Force proper alignment for rowspan cells */
        #table22excel_all tbody td.manager-rowspan-cell {
            vertical-align: middle !important;
        }
        /* Ensure all cells in the same row align consistently */
        #table22excel_all tbody tr {
            vertical-align: baseline;
        }
        #table22excel_all tbody tr td {
            vertical-align: middle;
        }
    </style>
    <script language="javascript" type="text/javascript">
        var rsVsTsSuggestions = <?php echo json_encode(isset($searchSuggestions) ? $searchSuggestions : array()); ?>;
        $(document).ready(function() {
            var today = $("#form_date").val();
            
            // Function to check and highlight date inputs
            function highlightDateInputs() {
                if ($('#form_date').val() && $('#form_date').val().trim() !== '') {
                    $('#form_date').addClass('has-date');
                } else {
                    $('#form_date').removeClass('has-date');
                }
                
                if ($('#to_date').val() && $('#to_date').val().trim() !== '') {
                    $('#to_date').addClass('has-date');
                } else {
                    $('#to_date').removeClass('has-date');
                }
            }
            
            // Check on page load
            highlightDateInputs();
            
            $("#form_date, #to_date").datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: '2015:2030',
                numberOfMonths: 1,
                onSelect: function(selectedDate) {
                    // Highlight the input when date is selected
                    $(this).addClass('has-date');
                    
                    if (this.id == 'form_date') {
                        var dateMin = $('#form_date').datepicker("getDate");
                        var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(), dateMin.getDate());
                        var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(), dateMin.getDate() + 365);
                        $('#to_date').datepicker("option", "minDate", rMin);
                        $('#to_date').datepicker("option", "maxDate", rMax);
                    }
                },
                onChangeMonthYear: function(year, month, inst) {
                    // Keep highlighting when navigating months
                    highlightDateInputs();
                }
            });
            $('#to_date').datepicker("option", "minDate", new Date(today));
            
            // Also check on input change (in case dates are changed programmatically)
            $('#form_date, #to_date').on('change blur', function() {
                highlightDateInputs();
            });
            
            // Function to toggle client projects
            function toggleClientProjects(clientIndex) {
                var projectRows = $('.client-projects-' + clientIndex);
                var headerRow = $('.client-header-row[data-client-index="' + clientIndex + '"]');
                var toggleIcon = $('.client-toggle-icon[data-client-index="' + clientIndex + '"] i');
                
                if (projectRows.is(':visible')) {
                    projectRows.hide();
                    headerRow.css('background-color', '#f8f9fa');
                    toggleIcon.removeClass('fa-minus').addClass('fa-plus');
                } else {
                    projectRows.show();
                    headerRow.css('background-color', '#e8f4f8');
                    toggleIcon.removeClass('fa-plus').addClass('fa-minus');
                }
            }
            
            // Make toggle icon clickable
            $('.client-toggle-icon').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                var clientIndex = $(this).data('client-index');
                toggleClientProjects(clientIndex);
            });
            
            // Make client name text clickable
            $('.client-name-text').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                var clientIndex = $(this).closest('.client-header-row').data('client-index');
                toggleClientProjects(clientIndex);
            });
            
            // Add hover effect to toggle icon
            $('.client-toggle-icon').hover(
                function() {
                    $(this).css('color', '#2c5aa0');
                    $(this).css('transform', 'scale(1.2)');
                },
                function() {
                    $(this).css('color', '#337ab7');
                    $(this).css('transform', 'scale(1)');
                }
            );
            
            // Add hover effect to client name text only
            $('.client-name-text').hover(
                function() {
                    $(this).css('color', '#337ab7');
                    $(this).css('text-decoration', 'underline');
                },
                function() {
                    $(this).css('color', '#2c3e50');
                    $(this).css('text-decoration', 'none');
                }
            );
            
            // Initially hide all project rows and total rows (projects are collapsed by default)
            $('.client-project-row, .client-total-row').hide();

            // Rebuild Department summary (top table) based on visible project rows.
            // When a search term is applied, this will show Resource/Timesheet/Diff only for matching client/project/PM/department.
            function rebuildDepartmentSummary() {
                var term = $.trim($('#rs_vs_ts_search').val()).toLowerCase();
                var filterActive = term !== '';

                var managerTotals = {};
                var grandResource = 0;
                var grandTimesheet = 0;

                $('#table22excel_all .manager-total-row:visible').each(function() {
                    if (filterActive && !$(this).is(':visible')) {
                        return;
                    }
                    var managerName = $.trim($(this).find('td:eq(0)').text());
                    if (!managerName) {
                        return;
                    }
                    var resH = parseFloat($(this).find('td:eq(2)').text().replace(/,/g, '')) || 0;
                    var tsH  = parseFloat($(this).find('td:eq(3)').text().replace(/,/g, '')) || 0;

                    if (!managerTotals[managerName]) {
                        managerTotals[managerName] = { resource_hours: 0, timesheet_hours: 0 };
                    }
                    managerTotals[managerName].resource_hours += resH;
                    managerTotals[managerName].timesheet_hours += tsH;

                    grandResource += resH;
                    grandTimesheet += tsH;
                });

                var $tbody = $('#rs_vs_ts_dept_totals_table tbody');
                $tbody.empty();

                // If no rows (e.g. empty data set), do not attempt to render summary.
                var hasAny = false;
                for (var k in managerTotals) {
                    if (Object.prototype.hasOwnProperty.call(managerTotals, k)) {
                        hasAny = true;
                        break;
                    }
                }
                if (!hasAny) {
                    return;
                }

                // Keep manager-wise rows separate (no merge).
                var displayGroups = {};
                Object.keys(managerTotals).sort(function(a, b) {
                    return a.toLowerCase().localeCompare(b.toLowerCase());
                }).forEach(function(managerName) {
                    displayGroups[managerName] = managerTotals[managerName];
                });

                function fmt(num) {
                    return parseFloat(num || 0).toFixed(2);
                }

                $.each(displayGroups, function(rowLabel, totals) {
                    var resH = totals.resource_hours || 0;
                    var tsH  = totals.timesheet_hours || 0;
                    var diff = tsH - resH;
                    var diffClass = '';
                    if (diff < 0) {
                        diffClass = 'rs-vs-ts-diff-negative';
                    } else if (diff > 0) {
                        diffClass = 'rs-vs-ts-diff-positive';
                    }
                    var $tr = $('<tr class="rs-vs-ts-dept-data-row"></tr>');
                    $tr.append($('<td class="rs-vs-ts-cell-dept"></td>').text(rowLabel));
                    $tr.append($('<td class="rs-vs-ts-cell-total-hrs"></td>').text(fmt(resH)));
                    $tr.append($('<td class="rs-vs-ts-cell-invoice-hrs"></td>').text(fmt(tsH)));
                    $tr.append($('<td class="rs-vs-ts-cell-diff"></td>').addClass(diffClass).text(fmt(diff)));
                    $tbody.append($tr);
                });

                // Keep grand total aligned with visible summary rows.
                grandResource = 0;
                grandTimesheet = 0;
                $.each(displayGroups, function(_, totals) {
                    grandResource += (totals.resource_hours || 0);
                    grandTimesheet += (totals.timesheet_hours || 0);
                });
                var grandDiff = grandTimesheet - grandResource;
                var grandDiffClass = '';
                if (grandDiff < 0) {
                    grandDiffClass = 'rs-vs-ts-diff-negative';
                } else if (grandDiff > 0) {
                    grandDiffClass = 'rs-vs-ts-diff-positive';
                }

                var $grandTr = $('<tr class="rs-vs-ts-grand-total-row"></tr>');
                $grandTr.append($('<td class="rs-vs-ts-cell-dept"></td>').text('Grand Total'));
                $grandTr.append($('<td class="rs-vs-ts-cell-total-hrs"></td>').text(fmt(grandResource)));
                $grandTr.append($('<td class="rs-vs-ts-cell-invoice-hrs"></td>').text(fmt(grandTimesheet)));
                $grandTr.append($('<td class="rs-vs-ts-cell-diff"></td>').addClass(grandDiffClass).text(fmt(grandDiff)));
                $tbody.append($grandTr);
            }

            // Search filter: Client Name, Project Name, Project Manager, Department (by default show all)
            function applySearchFilter() {
                var term = $.trim($('#rs_vs_ts_search').val()).toLowerCase();
                if (term === '') {
                    $('.manager-total-row').show();
                    $('.client-header-row').show();
                    $('.client-project-row').hide();
                    $('.client-toggle-icon i').removeClass('fa-minus').addClass('fa-plus');
                    $('.client-header-row').css('background-color', '#f8f9fa');
                    rebuildDepartmentSummary();
                    return;
                }
                var clientIndicesToShow = {};
                $('#table22excel_all .client-project-row').each(function() {
                    var searchText = $(this).data('search') || '';
                    if (searchText.indexOf(term) >= 0) {
                        clientIndicesToShow[$(this).data('client-index')] = true;
                    }
                });
                $('#table22excel_all .client-header-row').each(function() {
                    var searchText = $(this).data('search') || '';
                    var clientIndex = $(this).data('client-index');
                    var show = searchText.indexOf(term) >= 0 || clientIndicesToShow[clientIndex];
                    $(this).toggle(show);
                });
                $('#table22excel_all .client-project-row').each(function() {
                    var searchText = $(this).data('search') || '';
                    var clientIndex = $(this).data('client-index');
                    var parentShown = $('#table22excel_all .client-header-row[data-client-index="' + clientIndex + '"]').is(':visible');
                    var match = searchText.indexOf(term) >= 0;
                    $(this).toggle(match || parentShown);
                });

                // Show manager total row only if at least one row under that manager is visible.
                $('#table22excel_all .manager-total-row').each(function() {
                    var managerKey = ($(this).data('manager') || '').toString();
                    var hasVisibleRows = $('#table22excel_all .client-header-row:visible[data-manager="' + managerKey + '"], #table22excel_all .client-project-row:visible[data-manager="' + managerKey + '"]').length > 0;
                    $(this).toggle(hasVisibleRows);
                });

                // After applying search filter, recompute department summary based only on visible project rows.
                rebuildDepartmentSummary();
            }
            $('#rs_vs_ts_search').on('input keyup', function() {
                applySearchFilter();
            });

            // Initial build of department summary from full data set on page load.
            rebuildDepartmentSummary();
            // Autosuggest for search box (Client, Project, PM, Department)
            $('#rs_vs_ts_search').autocomplete({
                source: function(request, response) {
                    var term = (request.term || '').toLowerCase();
                    if (!term) {
                        response(rsVsTsSuggestions.slice(0, 50));
                        return;
                    }
                    var filtered = rsVsTsSuggestions.filter(function(item) {
                        return item.toLowerCase().indexOf(term) >= 0;
                    });
                    response(filtered.slice(0, 50));
                },
                minLength: 0,
                delay: 150,
                select: function(event, ui) {
                    $(this).val(ui.item.value);
                    applySearchFilter();
                    return false;
                }
            });
        });
       

        $("#send_rs_vs_ts_report_btn").click(function() {
            var $btn = $(this);
            var originalHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
            var formData = {
                form_date: $('#form_date').val(),
                to_date: $('#to_date').val()
            };
            $.ajax({
                url: '<?php echo base_url("clients/send_rs_vs_ts_report_email"); ?>',
                type: 'POST',
                dataType: 'json',
                data: formData,
                success: function(res) {
                    if (res.success) {
                        alert(res.message || 'The report has been sent successfully');
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
            var $tbl = $("#table22excel_all");
            var $deptTbl = $("#rs_vs_ts_dept_totals_table");
            var $clone = $tbl.clone();
            $clone.attr('id', 'table22excel_export_temp');
            $clone.children('div').remove();
            // Remove only rows hidden by search filter (not collapsed project rows). Collect client-index of hidden header rows.
            var hiddenClientIndices = {};
            $tbl.find('tbody tr.client-header-row').each(function() {
                if ($(this).is(':hidden')) hiddenClientIndices[$(this).data('client-index')] = true;
            });
            $clone.find('tbody tr').each(function() {
                var idx = $(this).data('client-index');
                if (idx && hiddenClientIndices[idx]) $(this).remove();
            });
            $clone.find('.excel-hide').remove();
            // Show all project rows in clone so Excel has client + all projects (they are collapsed by default)
            $clone.find('.client-project-row').show().css('display', '');
            // Prepend department summary: build one combined table (summary on top, then grid)
            var $combined = $('<table class="table table-bordered">').attr('id', 'table22excel_export_temp');
            $combined.css({ 'border-collapse': 'collapse', 'width': '100%', 'background-color': '#fff', 'font-family': 'Calibri, Arial, sans-serif', 'table-layout': 'fixed', 'border': '1px solid #e8e8e8' });
            var $thead = $('<thead></thead>');
            var $headerRow = $('<tr></tr>');
            $deptTbl.find('thead th').each(function() { $headerRow.append($('<th></th>').text($(this).text()).css({ 'font-weight': '600', 'font-size': '14px', 'background-color': '#2d5a7d', 'color': '#fff', 'padding': '12px 14px', 'border': '1px solid #2d5a7d' })); });
            $headerRow.append($('<th></th>').css({ 'border': '1px solid #2d5a7d', 'background-color': '#2d5a7d', 'min-width': '20px' }));
            $thead.append($headerRow);
            $combined.append($thead);
            var $body = $('<tbody></tbody>');
            $deptTbl.find('tbody tr').each(function() {
                var $r = $('<tr class="rs-vs-ts-export-summary-row"></tr>');
                $(this).find('td').each(function() { $r.append($('<td></td>').text($(this).text().trim()).css({ 'padding': '12px 14px', 'border': '1px solid #e8e8e8', 'font-weight': '600' })); });
                $r.append($('<td></td>').css({ 'border': '1px solid #e8e8e8' }));
                $body.append($r);
            });
            $body.append($('<tr></tr>').append($('<td colspan="5"></td>').css({ 'border': '1px solid #e8e8e8', 'height': '8px' })));
            var $gridHeader = $('<tr></tr>');
            $tbl.find('thead th').each(function() { $gridHeader.append($('<td></td>').text($(this).text().trim()).css({ 'font-weight': '600', 'font-size': '15px', 'text-transform': 'uppercase', 'background-color': '#337ab7', 'color': '#fff', 'padding': '14px 12px', 'border': '1px solid #2c5aa0' })); });
            $body.append($gridHeader);
            $clone.find('tbody tr').each(function() { $body.append($(this).clone()); });
            $combined.append($body);
            $clone = $combined;
            // Style export to match grid view
            $clone.css({
                'border-collapse': 'collapse',
                'width': '100%',
                'background-color': '#fff',
                'font-family': 'Calibri, Arial, sans-serif',
                'table-layout': 'fixed',
                'border': '1px solid #e8e8e8'
            });
            // Header: same as grid - blue bar, white text, uppercase
            $clone.find('thead th').css({
                'font-weight': '600',
                'font-size': '15px',
                'text-transform': 'uppercase',
                'letter-spacing': '0.5px',
                'background-color': '#337ab7',
                'color': '#ffffff',
                'padding': '14px 12px',
                'border-bottom': '3px solid #2c5aa0',
                'border-right': '1px solid rgba(255,255,255,0.3)',
                'vertical-align': 'middle'
            });
            $clone.find('thead th:last-child').css('border-right', 'none');
            $clone.find('thead th:eq(0)').css({ 'width': '150px', 'text-align': 'center' });
            $clone.find('thead th:eq(1)').css({ 'width': '280px', 'text-align': 'left' });
            $clone.find('thead th:eq(2), thead th:eq(3), thead th:eq(4)').css({ 'width': '120px', 'text-align': 'right' });
            // Body: same borders and padding as grid
            $clone.find('tbody td').css({
                'padding': '14px 12px',
                'border-bottom': '1px solid #e8e8e8',
                'border-right': '1px solid #e8e8e8',
                'vertical-align': 'middle',
                'font-size': '13px'
            });
            $clone.find('tbody tr:not(.rs-vs-ts-export-summary-row) td:nth-child(1)').css('text-align', 'center');
            $clone.find('tbody tr:not(.rs-vs-ts-export-summary-row) td:nth-child(2)').css('text-align', 'left');
            $clone.find('tbody tr:not(.rs-vs-ts-export-summary-row) td:nth-child(3), tbody tr:not(.rs-vs-ts-export-summary-row) td:nth-child(4), tbody tr:not(.rs-vs-ts-export-summary-row) td:nth-child(5)').css({ 'text-align': 'right', 'font-family': "'Courier New', monospace", 'font-weight': '500' });
            $clone.find('tbody tr.rs-vs-ts-export-summary-row td:nth-child(1)').css('text-align', 'left');
            $clone.find('tbody tr.rs-vs-ts-export-summary-row td:nth-child(2), tbody tr.rs-vs-ts-export-summary-row td:nth-child(3), tbody tr.rs-vs-ts-export-summary-row td:nth-child(4)').css('text-align', 'right');
            // Client header rows: same as grid - #f8f9fa, left border accent
            $clone.find('tbody tr.client-header-row').css('background-color', '#f8f9fa');
            $clone.find('tbody tr.client-header-row td').css({
                'font-weight': '600',
                'font-size': '16px',
                'border-bottom': '1px solid #e8e8e8',
                'border-right': '1px solid #e8e8e8'
            });
            $clone.find('tbody tr.client-header-row td:first-child').css({ 'color': '#2c5aa0', 'border-left': '4px solid #337ab7' });
            $clone.find('tbody tr.client-header-row td:nth-child(2)').css('color', '#2c3e50');
            // Project rows: white bg, indented second column, same as grid
            $clone.find('tbody tr.client-project-row').css('background-color', '#ffffff');
            $clone.find('tbody tr.client-project-row td').css({ 'font-size': '15px', 'border-bottom': '1px solid #e8e8e8', 'border-right': '1px solid #e8e8e8' });
            $clone.find('tbody tr.client-project-row td:nth-child(2)').css({ 'padding-left': '60px', 'color': '#666666', 'font-weight': '500' });
            $clone.find('tbody tr.client-project-row td:nth-child(3), tbody tr.client-project-row td:nth-child(4)').css('font-weight', '500');
            $clone.find('tbody tr.client-project-row td:nth-child(5)').css('font-weight', '600');
            // Project Manager names bold in Excel
            $clone.find('tbody tr.client-header-row td:first-child').each(function() {
                var txt = $(this).text().trim();
                if (txt) $(this).html('<strong>' + txt + '</strong>');
            });
            $clone.find('thead th:first').css('text-align', 'left');
            var $wrap = $('<div>').css({ position: 'absolute', left: '-9999px', top: 0 }).append($clone);
            $('body').append($wrap);
            $("#table22excel_export_temp").table2excel({
                exclude: ".noExl",
                name: "Live Project Allocation & Actual hours",
                filename: "rs_vs_ts_report",
                fileext: ".xls",
                exclude_links: true,
                exclude_inputs: true,
                preserveColors: "preserveColors"
            });
            setTimeout(function() { $wrap.remove(); }, 500);
        });
    </script>
    <script src="<?php echo HTTP_JS_PATH; ?>jquery.table2excel.js"></script>
    <!-- Include Footer here -->
    <?php $this->load->view('includes/cRMFooter'); ?>
    <!-- Include Footer here END -->
