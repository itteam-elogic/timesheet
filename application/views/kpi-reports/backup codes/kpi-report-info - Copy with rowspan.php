<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
$createdUser = $this->session->userdata['logged_in_timesheet']['empId'];
?>
<!-- Inlude Header here END-->
<?php 

if(!empty($_REQUEST['empId'])): 
		
				$getempId      	 =	 implode(' ,' ,$_REQUEST['empId']);
				
		else:
				
				$getempId      	 =	 'all';
				
		endif;

if(!empty($_REQUEST['repId'])): 
		
				$getrepId      	 =	 implode(' ,' ,$_REQUEST['repId']);
				
		else:
				
				$getrepId      	 =	 'all';
				
		endif;


$getListOfEmployees   	= $this->timesheet_login->getListOfEmpInformation(); // List of Clients

$getListOfManagers		= $this->timesheet_login->getReportingManagers(); // List of Clients


//echo '<pre>'; print_r($getListOfManagers); exit;


?>





<link href="<?php echo HTTP_CSS_PATH; ?>kpi-style.css" rel="stylesheet" />
<body id="kpiPage">
<div class="content-wrapper">
  <div class="page-title">      
    <div>
      <h1>Manage KPI</h1>
    </div>
    <div class="generate-report-btn " style="margin-left: -45px;">
    <a class="btn btn-warning btn-flat" data-toggle="tooltip" title="Generate Report" href="#" onclick="downloadreport(); return false;">
    <i class="fa fa-lg fa-download"></i> Generate Report
</a>
</div>
  </div>

    <div class="card">
		<h3 class="card-title"></h3>
		<div class="card-body">

                   <div class="toggle-switch-container" id="YearlyPage">
                        <div class="toggle" id="toggleSwitch">
                            <span class="label-left">KPI Report</span>
                            <div class="slider"></div>
                            <span class="label-right">Consolidated Report</span>
                        </div>
            </div>  
            
                        
<div class="row mt-4">  
    <div>
        <h5>KPI Report</h5>
    </div>
           
</div>            
                    
                      <div class="row">
                        
                        
                        <div class="col-md-6">
                        <div class="form-group">
												<label class="control-label">Department</label>
<!--
												<?php if($createdUser == '8012'):?>
													<select class="form-control <?php echo (!empty($this->input->post('department'))) ? 'form-control-sm' : ''; ?>" id="department" name="department" >
													    
														<option value="MEP" <?php echo ($this->input->post('department') == 'MEP') ? 'selected' : ''; ?>>MEP</option>
														
												</select>
-->
<!--

												<?php elseif($createdUser == '47'): ?>
													
													<select class="form-control <?php echo (!empty($this->input->post('department'))) ? 'form-control-sm' : ''; ?>" id="department" name="department" >
													    													
														<option value="Architectural" <?php echo ($this->input->post('department') == 'Architectural') ? 'selected' : ''; ?>>Architectural - Structural - 3D Visualization </option>
												</select>
-->
												
<!--												<?php else: ?>	-->
												<select class="form-control <?php echo (!empty($this->input->post('department'))) ? 'form-control-sm' : ''; ?>" id="department" name="department" onChange="filterByDepartment(this.value);" >
													    <option value="all">All</option>
														<option value="MEP" <?php echo ($this->input->post('department') == 'MEP') ? 'selected' : ''; ?>>MEP</option>
														<option value="Architectural" <?php echo ($this->input->post('department') == 'Architectural') ? 'selected' : ''; ?>>Architectural</option>
														<option value="Structural" <?php echo ($this->input->post('department') == 'Structural') ? 'selected' : ''; ?>>Structural</option>
														<option value="3D Visualization" <?php echo ($this->input->post('department') == '3D Visualization') ? 'selected' : ''; ?>>3D Visualization</option>
												</select> 
<!--													<?php endif; ?>-->
											</div>
					</div>
                        
                    
                    <div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Reporting Manager's</label>
							<select class="form-control" id="repId" name="repId" onChange="filterByManager(this.value);" >
								<option value="all">All</option>
                                <?php foreach($getListOfManagers as $key => $managerName): ?>
								<option value="<?php echo $managerName->empId;?>" <?php if($managerName->empId == $getrepId){ echo ' selected="selected"'; } ?>><?php echo ucfirst($managerName->name);?></option>
								<?php endforeach; ?>

							</select>
						</div>
					</div>
                    </div>    
                         <div class="row">
                         <div class="col-md-6">
                            
                    <?php
                    // Ensure $getempId is an array
                    if (!is_array($getempId)) {
                        $getempId = [$getempId];
                    }
                    ?>
                             
                    <div class="form-group">
                        <label class="control-label">Employees</label>

                        <select class="form-control" id="empId" name="empId[]" onChange="filterByEmployee(this);" multiple>
                            <!-- Placeholder for the default "All" option -->
                            <option value="all" <?php echo (empty($getempId) || in_array('all', $getempId)) ? 'selected="selected"' : ''; ?>>All</option>

                            <?php foreach($getListOfEmployees as $key => $employeeName): ?>
                                <option value="<?php echo $employeeName->empId;?>" <?php if(in_array($employeeName->empId, $getempId)){ echo 'selected="selected"'; } ?>>
                                    <?php echo ucfirst($employeeName->username); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>



					</div>
                        
                   
 
            
                            <div class="col-md-6">
                                 <?php 
                $currentMonth = date('n');  
                                $selectedMonth = isset($_GET['month_id']) ? $_GET['month_id'] : $currentMonth; // Get selected month from URL or default to current month  
            ?>
                               
<form method="POST" id="monthForm">
    <div class="form-group">
        <label class="control-label">Month</label>
        <select class="form-control" id="month_id" name="month_id" onchange="updateFormAction()">
            <option value="all">Select a Month</option>
            <option value="1" <?php echo ($selectedMonth == 1) ? 'selected' : ''; ?>>January</option>
            <option value="2" <?php echo ($selectedMonth == 2) ? 'selected' : ''; ?>>February</option>
            <option value="3" <?php echo ($selectedMonth == 3) ? 'selected' : ''; ?>>March</option>
            <option value="4" <?php echo ($selectedMonth == 4) ? 'selected' : ''; ?>>April</option>
            <option value="5" <?php echo ($selectedMonth == 5) ? 'selected' : ''; ?>>May</option>
            <option value="6" <?php echo ($selectedMonth == 6) ? 'selected' : ''; ?>>June</option>
            <option value="7" <?php echo ($selectedMonth == 7) ? 'selected' : ''; ?>>July</option>
            <option value="8" <?php echo ($selectedMonth == 8) ? 'selected' : ''; ?>>August</option>
            <option value="9" <?php echo ($selectedMonth == 9) ? 'selected' : ''; ?>>September</option>
            <option value="10" <?php echo ($selectedMonth == 10) ? 'selected' : ''; ?>>October</option>
            <option value="11" <?php echo ($selectedMonth == 11) ? 'selected' : ''; ?>>November</option>
            <option value="12" <?php echo ($selectedMonth == 12) ? 'selected' : ''; ?>>December</option>
        </select>
    </div>
</form> 
                                                                

</div>
       <script>
    function updateFormAction() {
        var selectedMonth = document.getElementById('month_id').value;
        var form = document.getElementById('monthForm');
        // Update the action URL based on the selected month
        form.action = "<?php echo site_url('kpi_reports/monthWiseKpiReport'); ?>?month_id=" + selectedMonth;
        // Submit the form immediately after changing the action
        form.submit();
    }      
</script> 
                        
                        </div>
            
            
            
            
            
		</div>
	</div>    
    
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
            <div id="content-wrapper" class="d-flex flex-column">
                <!-- Begin Page Content -->
                <div class="container-fluid">                    

                    

                    
   
                    <!-- January KPI Report Heading -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h3 id="kpiReportHeading">January KPI Report</h3>
                        </div>
                    </div>

                    
                    
                    
                    
<div class="row mt-4">
    <!-- Total Business Days Box -->
    <div class="col-md-6">
        <div class="info-box">
            <h4>Total Business Days</h4>
            <p id="businessDays">100 Days</p>
        </div>
    </div>

    <!-- Business Hours Box -->
    <div class="col-md-6">
        <div class="info-box">
            <h4>Total Business Hours</h4>
            <p id="businessHours">100 Hours</p>
        </div>
    </div>
</div>


<div class="row mt-4">
    <div class="col-md-12">
        <?php
// Group the employees by reporting manager
$managerGroups = [];
foreach ($getkpiReports as $kpiResult) {
    $managerGroups[$kpiResult->reporting_manger][] = $kpiResult;
}

// Function to generate a random hex color
function generateRandomColor() {
    $r = rand(180, 255);
    $g = rand(180, 255);
    $b = rand(180, 255);
    return "rgb($r, $g, $b)";
}

// Render the table
?>
<table id="employeeTable" class="table table-bordered">
    <thead>
        <tr>
            <th>Department</th>
            <th>Reporting Manager</th>
            <th>EE ID</th>
            <th>Employee</th>
            <th>Productive Hours</th>
            <th>Project General Hours</th>
            <th>eLogic General Hours</th>
            <th>Total Available Hours</th>
            <th>Productive Hours %</th>
            <th>Project General Hours %</th>
            <th>eLogic Hours %</th>
            <th>Availability %</th>
            <th>Utilization %</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Iterate over the grouped employees by manager
        foreach ($managerGroups as $managerId => $employees) {
            $getTeamwiseMangerName = $this->resourcelog_model->getManagerName($managerId);
            // Generate a random color for this manager's reporting cell
            $randomColor = generateRandomColor();
            
            // Loop through each employee in the current group
            foreach ($employees as $index => $kpiResult) {
                
                
                
                
                
                // Fetch employee data
                $getTotalProductionH = $this->kpi_reports_model->empProductionHours($kpiResult->empId);
                $productionHoursArray = explode('@#===', $getTotalProductionH);
                $totalProductionHours = isset($productionHoursArray[0]) ? $productionHoursArray[0] : 0;
                $totalEmpProductionGeneralHours = isset($productionHoursArray[1]) ? $productionHoursArray[1] : 0;
                $totalEmpGeneralHours = isset($productionHoursArray[2]) ? $productionHoursArray[2] : 0;
                $totalHours = array_sum([$totalProductionHours, $totalEmpGeneralHours, $totalEmpProductionGeneralHours]);
                $productivityPercentage = $totalHours > 0 ? ($totalProductionHours / $totalHours) * 100 : 0;
                $projectgeneralPercentage = $totalHours > 0 ? ($totalEmpProductionGeneralHours / $totalHours) * 100 : 0;
                $elogicgeneralPercentage = $totalHours > 0 ? ($totalEmpGeneralHours / $totalHours) * 100 : 0;
               $utilizationPercentage = $totalHours > 0 ? (($totalProductionHours + $totalEmpProductionGeneralHours) / $totalHours) * 100 : 0;

                
                // If it's the first employee under this manager, set the rowspan
                $rowspan = ($index == 0) ? count($employees) : 0;
                ?>
                <tr data-department="<?php echo $kpiResult->department; ?>" data-manager="<?php echo $kpiResult->reporting_manger; ?>" data-employee="<?php echo $kpiResult->empId; ?>">
                    <td><?php echo $kpiResult->department; ?></td>
                    <!-- If it's the first row for this manager, add rowspan with random color -->
                    <?php if ($index == 0): ?>
                        <td rowspan="<?php echo $rowspan; ?>" style="background-color: <?php echo $randomColor; ?>;">
                            <?php echo $getTeamwiseMangerName; ?>
                        </td>
                    <?php endif; ?>
                    <td><?php echo $kpiResult->emp_com_id; ?></td>
                    <td><?php echo $kpiResult->name; ?></td>
                    <td><?php echo $totalProductionHours; ?></td>
                    <td><?php echo $totalEmpProductionGeneralHours; ?></td>
                    <td><?php echo $totalEmpGeneralHours; ?></td>
                    <td><?php echo $totalHours; ?></td>
                    <td><?php echo round($productivityPercentage, 2);?></td>
                    <td><?php echo round($projectgeneralPercentage, 2);?></td>
                    <td><?php echo round($elogicgeneralPercentage, 2);?></td>
                    <td>90</td>
                    <td><?php echo round($utilizationPercentage, 2);?></td>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>

    </div>
</div>

                </div>
                <!-- End of Page Content -->

           
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Inlude Footer here -->

    
    <script>
 $(document).ready(function() {
        // Data for months
        const monthData = {
            "1": { days: 22, hours: 176 },
            "2": { days: 20, hours: 160 },
            "3": { days: 23, hours: 184 },
            "4": { days: 22, hours: 176 },
            "5": { days: 23, hours: 184 },
            "6": { days: 22, hours: 176 },
            "7": { days: 23, hours: 184 },
            "8": { days: 22, hours: 176 },
            "9": { days: 22, hours: 176 },
            "10": { days: 23, hours: 184 },
            "11": { days: 22, hours: 176 },
            "12": { days: 23, hours: 184 },
        };

        // Update heading and business days/hours based on the selected month
        function updateMonthInfo(selectedMonth) {
            // Debugging: See the selected month
            console.log("Selected Month:", selectedMonth);

            if (selectedMonth === 'all') {
                $('#kpiReportHeading').text('');
                $('#businessDays').text('');
                $('#businessHours').text('');
            } else {
                // Get the text of the selected option directly from the dropdown
                const selectedMonthText = $('#month_id option:selected').text();
                console.log("Selected Month Text:", selectedMonthText);  // Debugging line

                // Check if the monthText is correct
                if (selectedMonthText) {
                    // Update the heading and business days/hours
                    $('#kpiReportHeading').text(`${selectedMonthText} KPI Report`);
                    $('#businessDays').text(`${monthData[selectedMonth].days} Days`);
                    $('#businessHours').text(`${monthData[selectedMonth].hours} Hours`);
                } else {
                    console.log("Selected month text is not valid.");
                }
            }
        }

        // Trigger the update when the page loads (for the default selected month)
        const defaultMonth = $('#month_id').val(); // Get the selected month value on page load
        console.log("Default Month on Page Load:", defaultMonth);  // Debugging line
        updateMonthInfo(defaultMonth);

        // Update on month selection (this part was missing to handle changes)
        $('#month_id').change(function() {
            const selectedMonth = $(this).val();  // Get the selected value from the dropdown
            console.log("Month Changed to:", selectedMonth);  // Debugging line
            updateMonthInfo(selectedMonth); // Call the function to update the information
        });
   // New Script to Fill in Percentages (Productivity, Project General, etc.)
    function updateTablePercentages() {
        let table = $('#employeeTable'); 
        let rows = table.find('tr'); // Get all rows

        rows.each(function(i, row) {
            // Skip the header row (i = 0 means the header)
            if (i === 0) return;

            let cells = $(row).find('td');

            // Extract values from cells
            let productionHours = parseFloat(cells.eq(4).text());  // Production Hours (cells[4])
            let projectGeneralHours = parseFloat(cells.eq(5).text());  // Project General Hours (cells[5])
            let elogicHours = parseFloat(cells.eq(6).text());  // Elogic Hours (cells[6])
            let availableHours = parseFloat(cells.eq(7).text());  // Available Hours (cells[7])

            // Extract the current month (assuming it’s in cells[0] as an example, adjust if needed)
            let month = $('#month_id').val();
            let businessHours = monthData[month] ? monthData[month].hours : 176; // Default to 176 hours if invalid month

            // Calculate and update the percentages
           
            
           
            let availabilityPercentage = (availableHours / businessHours) * 100;
            

            // Fill the corresponding cells with the calculated values
            
            
            
            cells.eq(11).text(availabilityPercentage.toFixed(2));  // Availability % in cell[11]
            
        });
    }

    // Call this function when needed (e.g., after month change, or initially on page load)
    updateTablePercentages();

    // Optional: Call updateTablePercentages() whenever the month changes (if your table needs to refresh with new business hours)
    $('#month_id').change(function() {
        updateTablePercentages();  // Update table percentages when the month changes
    });
        
        
        
        
        
    });
</script>
    <script>
    function filterByDepartment(department) {
    // Get all rows in the table
    var rows = document.querySelectorAll('tbody tr');

    // Loop through all the rows and filter based on department
    rows.forEach(function(row) {
        var departmentInRow = row.getAttribute('data-department'); // Get the department of the row

        // If "all" is selected, show all rows, otherwise filter based on department
        if (department === 'all' || departmentInRow === department) {
            row.style.display = '';  // Show row
        } else {
            row.style.display = 'none';  // Hide row
        }
    });
}
</script>

<script>
    function filterByManager(reporting_manger) {
    // Get all rows in the table
    var rows = document.querySelectorAll('tbody tr');

    // Loop through all the rows and filter based on department
    rows.forEach(function(row) {
        var managerInRow = row.getAttribute('data-manager'); // Get the department of the row

        // If "all" is selected, show all rows, otherwise filter based on department
        if (reporting_manger === 'all' || managerInRow === reporting_manger) {
            row.style.display = '';  // Show row
        } else {
            row.style.display = 'none';  // Hide row
        }
    });
}
</script>

<script>
    function filterByEmployee(empIds) {
    // Get all rows in the table
    var rows = document.querySelectorAll('tbody tr');

    // Convert the empIds to an array if it's a single value (e.g. when it's "all")
    if (empIds === 'all') {
        // Show all rows if "all" is selected
        rows.forEach(function(row) {
            row.style.display = '';  // Show row
        });
    } else {
        // If multiple employees are selected, split them into an array
        var selectedEmpIds = Array.from(empIds.selectedOptions).map(option => option.value);

        rows.forEach(function(row) {
            var employeeInRow = row.getAttribute('data-employee'); // Get the employee id of the row

            // Check if the employee is selected
            if (selectedEmpIds.includes(employeeInRow) || selectedEmpIds.includes('all')) {
                row.style.display = '';  // Show row
            } else {
                row.style.display = 'none';  // Hide row
            }
        });
    }
}

</script>    
    

    
     <script>
    $('#month_id,#department,#empId,#repId').select2(); // Autosuggest list
    </script>
    
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
