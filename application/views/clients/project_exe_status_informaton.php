    <!-- Include Header here -->
    <?php 
    $this->load->view('includes/cRMHeader'); 

    $getClientID = !empty($client_Id) ? $client_Id : (!empty($_REQUEST['client_Id']) ? $_REQUEST['client_Id'] : 'all');
    $getProjectID = !empty($_REQUEST['project_Id']) ? $_REQUEST['project_Id'] : '';
    $getFromDate = !empty($form_date) ? $form_date : (!empty($_REQUEST['form_date']) ? $_REQUEST['form_date'] : date('Y-m-d'));
    $getToDate = !empty($to_date) ? $to_date : (!empty($_REQUEST['to_date']) ? $_REQUEST['to_date'] : date('Y-m-d'));
    $getClientNames = $this->client_model->getClientName(); // List of Clients
    ?>
    <div class="content-wrapper">
        <div class="page-title">
            <div>
                <h1><i class="fa fa-bell"></i>Live Project Allocation & Actual hours</h1>
            </div>
            <div>
				<!--<a class="btn btn-primary btn-flat" href="<?php echo base_url();?>clients/cs_reports" data-toggle="tooltip" title="Client TS Report"><i class="fa fa-search"> Client TS Report</i></a> -->
                <a class="btn btn-primary btn-flat" href="<?php echo base_url();?>clients/cs_reports" data-toggle="tooltip" title="refresh">
                    <i class="fa fa-chevron-circle-left"></i>
                </a>
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
                                <form name="emp_search_log" id="emp_search_log" method="post" action="<?php echo base_url('clients/project_exe_status');?>">
                                    <div class="tab-pane fade active in" id="Add">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Client's</label>
                                                    <select class="form-control" id="client_Id" name="client_Id">
															<option value="all">All clients</option>
															<?php foreach ($getClientNames as $key => $clientName): ?>
															<option value="<?php echo $clientName->client_Id;?>" <?php if ($clientName->client_Id == $getClientID) { echo ' selected="selected"'; } ?>>
																<?php echo ucfirst($clientName->client_name);?>
															</option>
															<?php endforeach; ?>
														</select>
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
        <div class="card">
            <div class="card-body">               
                <div class="tab-content">                    
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div style="text-align:right; position:relative;bottom:10px; right:18px;">
                                    <!-- <button id="downloadEmployeeData_total_client_Data" class="btn btn-primary btn-flat">Export Client Timesheet Report into Excel</button> -->
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
                                                    <th style="text-align: center; width: 120px;">Start Date</th>
                                                    <th style="text-align: center; width: 120px;">End Date</th>
                                                    <th style="text-align: center; width: 120px;">Billing Type</th>
                                                    <th style="text-align: right; width: 120px;">Estimated Hours</th>
                                                    <th style="text-align: right; width: 120px;">Actual Hours</th>
                                                    <th style="text-align: right; width: 120px;">Invoiced Hours</th>
                                                    <th style="text-align: right; width: 120px;">Difference Hours</th>
                                                 </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $currentManager = '';
                                                $currentClient = '';
                                                $clientTotalHours = 0;
                                                $clientResourceHours = 0;
                                                $eLogicClientsIds = array('363','374','370','369','368','367','364','361','355','270','262','253','236','210','85','78','74','49','34','32');
                                                $clientIndex = 0;
                                                $managerFirstRow = false;
                                                $firstProjectInClient = false;
                                                
                                                // Filter and sort data by Project Manager name in ascending order (exclude N/A and eLogic clients)
                                                $sortedClientResult = array();
                                                foreach ($allClientResult as $key => $reportResult) {
                                                    $pmName = isset($reportResult->project_manager_name) ? trim($reportResult->project_manager_name) : '';
                                                    $isExcludedClient = in_array($reportResult->client_Id, $eLogicClientsIds);
                                                    $isNaManager = ($pmName === '' || strtoupper($pmName) === 'N/A');
                                                    if (!$isExcludedClient && !$isNaManager) {
                                                        $sortedClientResult[] = $reportResult;
                                                    }
                                                }
                                                
                                                // Sort by project_manager_name in ascending order
                                                usort($sortedClientResult, function($a, $b) {
                                                    $managerA = !empty($a->project_manager_name) ? $a->project_manager_name : 'N/A';
                                                    $managerB = !empty($b->project_manager_name) ? $b->project_manager_name : 'N/A';
                                                    return strcasecmp($managerA, $managerB);
                                                });
                                                
                                                // First pass: Calculate totals for each client (estimated_hours, timesheet_hours, invoice)
                                                $clientTotals = array();
                                                foreach ($sortedClientResult as $key => $reportResult) {
                                                    $clientName = trim($reportResult->client_name);
                                                    $estimatedHours = !empty($reportResult->estimated_hours) ? floatval($reportResult->estimated_hours) : 0;
                                                    $timesheetHours = !empty($reportResult->timesheet_hours) ? $reportResult->timesheet_hours : 0;
                                                    $invoiceHours = !empty($reportResult->project_invoice_amt) ? floatval($reportResult->project_invoice_amt) : 0;
                                                    
                                                    if (!isset($clientTotals[$clientName])) {
                                                        $clientTotals[$clientName] = array('estimated' => 0, 'timesheet' => 0, 'invoice' => 0);
                                                    }
                                                    $clientTotals[$clientName]['estimated'] += $estimatedHours;
                                                    $clientTotals[$clientName]['timesheet'] += $timesheetHours;
                                                    $clientTotals[$clientName]['invoice'] += $invoiceHours;
                                                }
                                                
                                                // Group data by manager (unique), then by client
                                                $byManager = array();
                                                foreach ($sortedClientResult as $key => $reportResult) {
                                                    $managerName = !empty($reportResult->project_manager_name) ? $reportResult->project_manager_name : 'N/A';
                                                    $clientName = trim($reportResult->client_name);
                                                    if (!isset($byManager[$managerName])) {
                                                        $byManager[$managerName] = array();
                                                    }
                                                    if (!isset($byManager[$managerName][$clientName])) {
                                                        $byManager[$managerName][$clientName] = array('projects' => array());
                                                    }
                                                    $byManager[$managerName][$clientName]['projects'][] = $reportResult;
                                                }
                                                ksort($byManager, SORT_STRING);
                                                
                                                // Display: one row per client; show manager name only on first client of each manager (unique)
                                                foreach ($byManager as $projectManagerName => $clients):
                                                    $firstClientInManager = true;
                                                    foreach ($clients as $clientName => $clientData):
                                                        $clientIndex++;
                                                        
                                                        // Calculate totals for this client
                                                        $clientEstimatedTotal = isset($clientTotals[$clientName]['estimated']) ? $clientTotals[$clientName]['estimated'] : 0;
                                                        $clientTimesheetTotal = isset($clientTotals[$clientName]['timesheet']) ? $clientTotals[$clientName]['timesheet'] : 0;
                                                        $clientInvoiceTotal = isset($clientTotals[$clientName]['invoice']) ? $clientTotals[$clientName]['invoice'] : 0;
                                                ?>
                                                <tr class="client-header-row" data-client-index="<?php echo $clientIndex; ?>">
													<td style="text-align:center; vertical-align: middle; color: #2c5aa0; font-weight: 600; padding: 14px 12px; width: 150px; font-size: 16px;"><?php echo $firstClientInManager ? $projectManagerName : ''; ?></td>
                                                    
                                                    <td style="text-align: center; vertical-align: middle; padding: 14px 12px; width: 120px; font-size: 16px;"></td>
                                                    <td style="text-align: right; vertical-align: middle; padding: 14px 12px; width: 120px; font-weight: 600; font-size: 16px;"><strong><?php echo number_format($clientEstimatedTotal, 2); ?></strong></td>
                                                    <td style="text-align: right; vertical-align: middle; padding: 14px 12px; width: 120px; font-weight: 600; font-size: 16px;"><strong><?php echo number_format($clientTimesheetTotal, 2); ?></strong></td>
                                                    <td style="text-align: right; vertical-align: middle; padding: 14px 12px; width: 120px; font-weight: 600; font-size: 16px;"><strong><?php echo number_format($clientInvoiceTotal, 2); ?></strong></td>
                                                    <?php $clientDiff = $clientInvoiceTotal - $clientTimesheetTotal; $clientDiffColor = $clientDiff >= 0 ? '#5cb85c' : '#d9534f'; $clientDiffSign = $clientDiff >= 0 ? '+' : '-'; ?>
                                                    <td style="text-align: right; vertical-align: middle; padding: 14px 12px; width: 120px; font-weight: 700; font-size: 16px; color: <?php echo $clientDiffColor; ?>;"><strong><?php echo $clientDiffSign . number_format(abs($clientDiff), 2); ?></strong></td>                                                    
                                                </tr>
                                                <?php
                                                    $firstClientInManager = false;
                                                    // Display all projects for this client
                                                    foreach ($clientData['projects'] as $projectKey => $reportResult):
                                                        $estimatedHours = !empty($reportResult->estimated_hours) ? floatval($reportResult->estimated_hours) : 0;
                                                        $timesheetHours = !empty($reportResult->timesheet_hours) ? $reportResult->timesheet_hours : 0;
                                                        $invoiceHours = !empty($reportResult->project_invoice_amt) ? floatval($reportResult->project_invoice_amt) : 0;
                                                        $projectName = !empty($reportResult->project_name) ? $reportResult->project_name : 'N/A';
                                                        $projStartDate = !empty($reportResult->project_start_date) && $reportResult->project_start_date != '0000-00-00' && $reportResult->project_start_date != '0000-00-00 00:00:00' ? date('Y-m-d', strtotime($reportResult->project_start_date)) : '';
                                                        $projEndDate = !empty($reportResult->project_end_date) && $reportResult->project_end_date != '0000-00-00' && $reportResult->project_end_date != '0000-00-00 00:00:00' ? date('Y-m-d', strtotime($reportResult->project_end_date)) : '';
                                                ?>
                                                <tr class="client-project-row client-projects-<?php echo $clientIndex; ?>" style="display: none;">
                                                    <td style="padding: 14px 12px; width: 150px;"></td>
                                                    <td style="padding-left: 60px; color: #666; padding: 14px 12px; vertical-align: middle; font-size: 15px;">
														<i class="fa fa-angle-right" style="margin-right: 10px; color: #999; font-size: 14px;"></i>
														<i class="fa fa-folder" style="margin-right: 6px; color: #5cb85c; font-size: 15px;"></i>
														<?php echo $projectName; ?>
													</td>
                                                    <td style="text-align: center; padding: 14px 12px; vertical-align: middle; width: 120px; font-size: 15px;"><?php echo $projStartDate; ?></td>
                                                    <td style="text-align: center; padding: 14px 12px; vertical-align: middle; width: 120px; font-size: 15px;"><?php echo $projEndDate; ?></td>
                                                    <td style="text-align: center; padding: 14px 12px; vertical-align: middle; width: 120px; font-size: 15px;"><?php echo !empty($reportResult->man_days) ? ucfirst(strtolower($reportResult->man_days)) : ''; ?></td>
                                                    <td style="text-align: right; padding: 14px 12px; vertical-align: middle; width: 120px; font-weight: 500; font-size: 15px;"><?php echo number_format($estimatedHours, 2); ?></td>
                                                    <td style="text-align: right; padding: 14px 12px; vertical-align: middle; width: 120px; font-weight: 500; font-size: 15px;"><?php echo number_format($timesheetHours, 2); ?></td>
                                                    <td style="text-align: right; padding: 14px 12px; vertical-align: middle; width: 120px; font-weight: 500; font-size: 15px;"><?php echo number_format($invoiceHours, 2); ?></td>
                                                    <?php $projectDiff = $invoiceHours - $timesheetHours; $projectDiffColor = $projectDiff >= 0 ? '#5cb85c' : '#d9534f'; $projectDiffSign = $projectDiff >= 0 ? '+' : '-'; ?>
                                                    <td style="text-align: right; padding: 14px 12px; vertical-align: middle; width: 120px; font-weight: 600; font-size: 15px; color: <?php echo $projectDiffColor; ?>">
														<?php echo $projectDiffSign . number_format(abs($projectDiff), 2); ?>
													</td>                                                    
                                                </tr>
                                                <?php
                                                    endforeach;
                                                    endforeach;
                                                endforeach; ?>
                                                <?php
                                                ?>
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
            text-align: right !important;
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
        });
       
        $('#client_Id, #project_Id').select2(); // Autosuggest list

        $("#downloadEmployeeData_total_client_Data").click(function() {
            $("#table22excel_all").table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "Client Timesheet Report",
                filename: "client_timesheet_report", // do not include extension
                fileext: ".xls", // file extension
                exclude_links: true,
                exclude_inputs: true,
                preserveColors: "preserveColors"
            }); 
        });
    </script>
    <script src="<?php echo HTTP_JS_PATH; ?>jquery.table2excel.js"></script>
    <!-- Include Footer here -->
    <?php $this->load->view('includes/cRMFooter'); ?>
    <!-- Include Footer here END -->
