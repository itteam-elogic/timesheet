<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
$createdUser = $this->session->userdata['logged_in_timesheet']['empId'];

$mepManagers = ['146', '230', '149','455'];
$arcManagers = ['41', '394' , '270','47', '182', '71', '53', '155'];

foreach($getkpiReports  as $kpiempDepartment){ 
       
    
    $empDepartment = $kpiempDepartment->department;
    
}

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

$getListOfManagers		= $this->timesheet_login->getReportingManagers(NULL); // List of Clients


//echo '<pre>'; print_r($getListOfManagers); exit;


?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link href="<?php echo HTTP_CSS_PATH; ?>kpi-style.css" rel="stylesheet" />
<body id="kpiPage">
<div class="content-wrapper">
  <div class="page-title">      
    <div>
      <h1>Manage KPI</h1>
    </div>
   
  </div>
  <!------------------------------------------------------------------------------CARD 1------------------------------------------------------------------>
    <div class="card">
		<h3 class="card-title"></h3>
		<div class="card-body">
  
            
  <!------------------------------------------------------------------------------BUTTONS------------------------------------------------------------------> 
            
<div class="four-report-btn" style="margin-left: 9px;">
    <button onclick="redirectToMonthlyReport()" class="btn btn-primary activekpi" >
        Monthly KPI Report
    </button>

    <button onclick="redirectToConsolidatedReport()" class="btn btn-primary">
        Consolidated KPI Report
    </button>

    <?php 
        $sessionData = $this->session->userdata('logged_in_timesheet');
        $userType = isset($sessionData['user_type']) ? strtolower($sessionData['user_type']) : '';

        $canViewClientReport = in_array($userType, ['admin', 'business_head', 'manager']);
    ?>

    <?php if ($canViewClientReport): ?>
        
        <button onclick="redirectTographs()" class="btn btn-primary" style="background-color: #014b88; font-weight: bold; border: 2px solid white;">
            Visual Graphs
        </button>
    
        <button onclick="redirectToClient()" class="btn btn-primary">
            Client Report
        </button>
    
    <?php endif; ?>
</div>


  <!------------------------------------------------------------------------------HEADINGS------------------------------------------------------------------>                      
<div class="row mt-4">
                        <div class="col-md-12">
                            <h3>Visual Graphs</h3>
                        </div>
                    </div>     
            

            
            
		</div>
	</div>    
<!------------------------------------------------------------------------------END OF CARD 1------------------------------------------------------------------>   
    
<div class="row mt-4 d-flex justify-content-center">
    <div class="col-md-6 d-flex justify-content-center">
       <div class="info-box text-center" style="width: 154%; margin-left: -27%;">
            <h4 id="summaryHeading">Architecture Department Summary</h4>
            
            <table id="departmentTable" class="table border-0">
                <thead>
                    <tr>
                        <th><strong>Month</strong></th>
                        <th><strong>Departments</strong></th>
                        <th><strong>Productivity%</strong></th>
                        <th><strong>Project General%</strong></th>

                        <th><strong>Utilization%</strong></th>
                    </tr>
                </thead>
<tbody>
    <?php 
    // Define unified departments
    $departments = ['Architecture', 'MEP']; // Added MEP department

    // Init totals
    $totals = [];

    // Define the months array
    $months = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];

    // Get the current month (number)
    $currentMonth = date('n');  // This will return 1 for January, 2 for February, etc.

    // Loop through all KPI reports
    foreach ($getkpiReports as $kpiResult) {
        // Loop over all months
        foreach ($months as $monthNum => $monthName) {
            // Skip current month
            if ($monthNum == $currentMonth) continue;

            // Determine the department based on the existing logic
            $dept = in_array($kpiResult->department, ['Architectural', 'Structural', '3D Visualization']) ? 'Architecture' :
                    (in_array($kpiResult->department, ['MEP']) ? 'MEP' : $kpiResult->department);

            if (!in_array($dept, $departments)) continue;

            // Get production/general hours
            $hours = explode('@#===', $this->kpi_reports_model->empProductionHoursMonthWise($kpiResult->empId, $monthNum));
            $prod = isset($hours[0]) ? (float)$hours[0] : 0;
            $gen = isset($hours[1]) ? (float)$hours[1] : 0;
            $elog = isset($hours[2]) ? (float)$hours[2] :0;
            $total = $prod + $gen + $elog;

            // Monthly working hours
            $monthHours = [
                1 => 178.5, 2 => 170.0, 3 => 161.5, 4 => 187.0,
                5 => 178.5, 6 => 178.5, 7 => 195.5, 8 => 170.0,
                9 => 187.0, 10 => 170.0, 11 => 170.0, 12 => 187.0
            ];
            $workHrs = isset($monthHours[$monthNum]) ? $monthHours[$monthNum] : 0;

            if ($workHrs <= 0 || $total <= 0) continue;

            // Compute percentages
            $availability = ($total / $workHrs) * 100;
            $productivity = ($prod / $total) * 100;
            $projectGen = ($gen / $total) * 100;
            $elogicGen = ($elog / $total) * 100;
            $utilization = (($prod + $gen) / $total) * 100;

            // Initialize if not already
            if (!isset($totals[$monthNum][$dept])) {
                $totals[$monthNum][$dept] = [
                    'count' => 0, 'productivity' => 0, 'projectGen' => 0,
                    'elogicGen' => 0, 'availability' => 0, 'utilization' => 0
                ];
            }

            // Aggregate
            $totals[$monthNum][$dept]['count']++;
            $totals[$monthNum][$dept]['productivity'] += $productivity;
            $totals[$monthNum][$dept]['projectGen'] += $projectGen;
            $totals[$monthNum][$dept]['elogicGen'] += $elogicGen;
            $totals[$monthNum][$dept]['availability'] += $availability;
            $totals[$monthNum][$dept]['utilization'] += $utilization;
        }
    }

    // Render the table
    foreach ($months as $monthNum => $monthName) {
        // Skip current month
        if ($monthNum == $currentMonth) continue;

        // Check if any data exists for this month
        if (!isset($totals[$monthNum]) || count($totals[$monthNum]) === 0) continue;

        // Start row for the month
        echo "<tr><td rowspan='2'>{$monthName}</td>"; // Only show month once (rowspan 2)

        // Loop through the departments and render for each department
        foreach ($departments as $dept) {
            if (!isset($totals[$monthNum][$dept])) continue;

            $c = $totals[$monthNum][$dept]['count'];

            $productivity = round($totals[$monthNum][$dept]['productivity'] / $c);
            $projectGen = round($totals[$monthNum][$dept]['projectGen'] / $c);
            $elogicGen = round($totals[$monthNum][$dept]['elogicGen'] / $c);
            $availability = round($totals[$monthNum][$dept]['availability'] / $c);
            $utilization = round($totals[$monthNum][$dept]['utilization'] / $c);

            // For the first department (Architecture), we display the department name
            if ($dept === 'Architecture') {
                echo "<td><strong>{$dept}<strong></td>
                    <td style='background-color:#D1E9DC;' title='Productivity'>{$productivity}%</td>
                    <td style='background-color:#FDF3D0;' title='Project Generation'>{$projectGen}%</td>

                    <td style='background-color:#D9CDE6;' title='Utilization'>{$utilization}%</td>";
            }

            // For the second department (MEP), no need to show the month again, just department and stats
            if ($dept === 'MEP') {
                echo "<tr><td><strong>{$dept}<strong></td>
                    <td style='background-color:#D1E9DC;' title='Productivity'>{$productivity}%</td>
                    <td style='background-color:#FDF3D0;' title='Project Generation'>{$projectGen}%</td>

                    <td style='background-color:#D9CDE6;' title='Utilization'>{$utilization}%</td></tr>";
            }
        }
    }

    ?>
</tbody>


            </table>
        </div>
    </div>
</div>

    
    
 <!------------------------------------------------------------------------------CARD 2------------------------------------------------------------------>    

<!--
<div class="row mt-4 d-flex justify-content-center">
    <div class="col-md-6 d-flex justify-content-center">
        <div class="info-box text-center" style="width: 154%; margin-left: -27%;">
            <h4 id="summaryHeading">Architecture vs MEP - Line Charts</h4>


            <h4>Productivity Comparison</h4>
            <canvas id="productivityChart"></canvas>

            <h4>Project General Comparison</h4>
            <canvas id="projectGenChart"></canvas>

        
            <h4>Utilization Comparison</h4>
            <canvas id="utilizationChart"></canvas>

        </div>
    </div>
</div>
-->
<!--
<script>

    const months = <?php echo json_encode(array_keys($months)); ?>;  
    const monthNames = <?php echo json_encode(array_values($months)); ?>;  

    const architectureProductivity = [];
    const mepProductivity = [];
    const architectureProjectGen = [];
    const mepProjectGen = [];
    const architectureUtilization = [];
    const mepUtilization = [];
    const filteredMonthNames = [];


    <?php
    foreach ($months as $monthNum => $monthName) {
        if ($monthNum == $currentMonth) continue;  

      
        $architectureProductivity = isset($totals[$monthNum]['Architecture']) ? round($totals[$monthNum]['Architecture']['productivity'] / $totals[$monthNum]['Architecture']['count']) : NULL;
        $mepProductivity = isset($totals[$monthNum]['MEP']) ? round($totals[$monthNum]['MEP']['productivity'] / $totals[$monthNum]['MEP']['count']) : null;

        $architectureProjectGen = isset($totals[$monthNum]['Architecture']) ? round($totals[$monthNum]['Architecture']['projectGen'] / $totals[$monthNum]['Architecture']['count']) : NULL;
        $mepProjectGen = isset($totals[$monthNum]['MEP']) ? round($totals[$monthNum]['MEP']['projectGen'] / $totals[$monthNum]['MEP']['count']) : NULL;

        $architectureUtilization = isset($totals[$monthNum]['Architecture']) ? round($totals[$monthNum]['Architecture']['utilization'] / $totals[$monthNum]['Architecture']['count']) : NULL;
        $mepUtilization = isset($totals[$monthNum]['MEP']) ? round($totals[$monthNum]['MEP']['utilization'] / $totals[$monthNum]['MEP']['count']) : NULL;

        // Only push data if neither Architecture nor MEP data is NULL
        if ($architectureProductivity !== NULL && $mepProductivity !== NULL && $architectureProjectGen !== NULL && $mepProjectGen !== NULL && $architectureUtilization !== NULL && $mepUtilization !== NULL) {
    ?>

        architectureProductivity.push(<?php echo $architectureProductivity; ?>);
        mepProductivity.push(<?php echo $mepProductivity; ?>);

        architectureProjectGen.push(<?php echo $architectureProjectGen; ?>);
        mepProjectGen.push(<?php echo $mepProjectGen; ?>);

        architectureUtilization.push(<?php echo $architectureUtilization; ?>);
        mepUtilization.push(<?php echo $mepUtilization; ?>);

        filteredMonthNames.push("<?php echo $monthName; ?>");

    <?php } } ?>

const ctxProductivity = document.getElementById('productivityChart').getContext('2d');
new Chart(ctxProductivity, {
    type: 'line',
    data: {
        labels: filteredMonthNames,
        datasets: [{
            label: 'Architecture Productivity',
            data: architectureProductivity,
            borderColor: 'rgba(0, 123, 255, 1)',
            fill: false,
            tension: 0.1
        }, {
            label: 'MEP Productivity',
            data: mepProductivity,
            borderColor: 'rgba(40, 167, 69, 1)',
            fill: false,
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Productivity Comparison'
            }
        }
    }
});


    const ctxProjectGen = document.getElementById('projectGenChart').getContext('2d');
    new Chart(ctxProjectGen, {
        type: 'line',
        data: {
            labels: filteredMonthNames,
            datasets: [{
                label: 'Architecture Project General',
                data: architectureProjectGen,
                borderColor: 'rgba(0, 123, 255, 1)',
                fill: false,
                tension: 0.1
            }, {
                label: 'MEP Project General',
                data: mepProjectGen,
                borderColor: 'rgba(40, 167, 69, 1)',
                fill: false,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Project General Comparison'
                }
            }
        }
    });

    const ctxUtilization = document.getElementById('utilizationChart').getContext('2d');
    new Chart(ctxUtilization, {
        type: 'line',
        data: {
            labels: filteredMonthNames,
            datasets: [{
                label: 'Architecture Utilization',
                data: architectureUtilization,
                borderColor: 'rgba(0, 123, 255, 1)',
                fill: false,
                tension: 0.1
            }, {
                label: 'MEP Utilization',
                data: mepUtilization,
                borderColor: 'rgba(40, 167, 69, 1)',
                fill: false,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Utilization Comparison'
                }
            }
        }
    });
</script>
-->




    
     <!------------------------------------------------------------------------------CARD 2------------------------------------------------------------------>    
<!-- Include Chart.js CDN -->
<div class="row mt-4 d-flex justify-content-center">
    <div class="col-md-6 d-flex justify-content-center">
       <div class="info-box text-center" style="width: 154%; margin-left: -27%;">
            <h4>Departments Summary</h4>
            <table id="departmentTable" class="table border-0">
                <thead>
                    <tr>
                        <th><strong>Departments</strong></th>
                        <th><strong>Avg Productivity%</strong></th>
                        <th><strong>Avg Project General%</strong></th>
                        <th><strong>Avg eLogic General%</strong></th>
                        
                    </tr>
                </thead>
                <tbody>   
<?php 
$currentMonth = date('m');
$monthNames = range(1, $currentMonth);
$empId = $this->session->userdata['logged_in_timesheet']['empId'];

$departmentProductivity = [];
$departmentProjectGeneral = [];
$departmentElogicGeneral = [];

$departmentCounts = [];

$archDepartments = ['Architectural', 'Structural', '3D Visualization'];
$targetDepartments = array_merge($archDepartments, ['MEP']);

foreach ($getkpiReports as $kpiResult):
    if (!in_array($kpiResult->department, $targetDepartments)) continue;

    $dept = in_array($kpiResult->department, $archDepartments) ? 'Architecture' : $kpiResult->department;

    $totalProductivityPercentage = 0;
    $totalProjectGeneralPercentage = 0;
    $totalElogicGeneralPercentage = 0;
    $validMonths = 0;

    foreach ($monthNames as $month) {
        $getTotalProductionH = $this->kpi_reports_model->empProductionHoursMonthWise($kpiResult->empId, $month);
        $productionHoursArray = explode('@#===', $getTotalProductionH);

        $monthlyProduction = isset($productionHoursArray[0]) ? $productionHoursArray[0] : 0;
        $monthlyProjectGeneral = isset($productionHoursArray[1]) ? $productionHoursArray[1] : 0;
        $monthlyElogicGeneral = isset($productionHoursArray[2]) ? $productionHoursArray[2] : 0;

        $monthlyTotalHours = $monthlyProduction + $monthlyProjectGeneral + $monthlyElogicGeneral;

        $monthWorkingHours = [
            1 => 178.5, 2 => 170.0, 3 => 161.5, 4 => 187.0,
            5 => 178.5, 6 => 178.5, 7 => 195.5, 8 => 170.0,
            9 => 187.0, 10 => 170.0, 11 => 170.0, 12 => 187.0
        ];
        $workHrs = isset($monthWorkingHours[$month]) ? $monthWorkingHours[$month] : 0;


        if ($workHrs <= 0 || $monthlyTotalHours <= 0) continue;

        $monthlyProductivityPercentage = ($monthlyProduction / $monthlyTotalHours) * 100;
        $monthlyProjectGeneralPercentage = ($monthlyProjectGeneral / $monthlyTotalHours) * 100;
        $monthlyElogicGeneralPercentage = ($monthlyElogicGeneral / $monthlyTotalHours) * 100;

        $totalProductivityPercentage += $monthlyProductivityPercentage;
        $totalProjectGeneralPercentage += $monthlyProjectGeneralPercentage;
        $totalElogicGeneralPercentage += $monthlyElogicGeneralPercentage;

        $validMonths++;
    }

    $productivityPercentage = ($validMonths > 0) ? ($totalProductivityPercentage / $validMonths) : 0;
    $projectGeneralPercentage = ($validMonths > 0) ? ($totalProjectGeneralPercentage / $validMonths) : 0;
    $elogicGeneralPercentage = ($validMonths > 0) ? ($totalElogicGeneralPercentage / $validMonths) : 0;

    if (!isset($departmentProductivity[$dept])) {
        $departmentProductivity[$dept] = 0;
        $departmentProjectGeneral[$dept] = 0;
        $departmentElogicGeneral[$dept] = 0;
        $departmentCounts[$dept] = 0;
    }

    $departmentProductivity[$dept] += $productivityPercentage;
    $departmentProjectGeneral[$dept] += $projectGeneralPercentage;
    $departmentElogicGeneral[$dept] += $elogicGeneralPercentage;
    $departmentCounts[$dept]++;
endforeach;

// Departments to show in table
$displayDepartments = ['Organisation', 'Architecture', 'MEP'];

// Calculate Organisation (Architecture + MEP)
$orgDepartments = ['Architecture', 'MEP'];
$totalOrgProductivity = 0;
$totalOrgProjectGeneral = 0;
$totalOrgElogicGeneral = 0;
$totalOrgCount = 0;

foreach ($orgDepartments as $dName) {
    if (isset($departmentCounts[$dName])) {
        $totalOrgProductivity += $departmentProductivity[$dName];
        $totalOrgProjectGeneral += $departmentProjectGeneral[$dName];
        $totalOrgElogicGeneral += $departmentElogicGeneral[$dName];
        $totalOrgCount += $departmentCounts[$dName];
    }
}
?>

<tbody>
    <?php if ($totalOrgCount > 0): ?>
    <tr data-department="Organisation">
        <td><strong>Organisation</strong></td>
        <td style="background-color: #D1E9DC;"><?php echo number_format($totalOrgProductivity / $totalOrgCount, 2); ?>%</td>
        <td style="background-color: #FDF3D0;"><?php echo number_format($totalOrgProjectGeneral / $totalOrgCount, 2); ?>%</td>
        <td style="background-color: #FFDEC4;"><?php echo number_format($totalOrgElogicGeneral / $totalOrgCount, 2); ?>%</td>
    </tr>
    <?php endif; ?>

    <?php foreach (['Architecture', 'MEP'] as $dept): 
        $count = isset($departmentCounts[$dept]) ? $departmentCounts[$dept] : 0;

        $avgProd = $count > 0 ? $departmentProductivity[$dept] / $count : 0;
        $avgProj = $count > 0 ? $departmentProjectGeneral[$dept] / $count : 0;
        $avgElog = $count > 0 ? $departmentElogicGeneral[$dept] / $count : 0;
    ?>
    <tr data-department="<?php echo $dept; ?>">
        <td><?php echo $dept; ?></td>
        <td style="background-color: #D1E9DC;"><?php echo number_format($avgProd, 2); ?>%</td>
        <td style="background-color: #FDF3D0;"><?php echo number_format($avgProj, 2); ?>%</td>
        <td style="background-color: #FFDEC4;"><?php echo number_format($avgElog, 2); ?>%</td>
    </tr>
    <?php endforeach; ?>
</tbody>

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
<!--
    
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="row">
  <div class="col-md-12">
    <div class="card" style="width: 106%; margin-left: -43px;">
      <div class="card-body">
        <div id="content-wrapper" class="d-flex flex-column">

          <div class="container-fluid">

            <div class="mb-4 d-flex justify-content-center" style="width: 60%; height: 300px; margin: auto;">
              <canvas id="pieChart1"></canvas>
            </div>

            <div class="mb-4 d-flex justify-content-center" style="width: 60%; height: 300px; margin: auto;">
              <canvas id="pieChart2"></canvas>
            </div>


            <div class="mb-4 d-flex justify-content-center" style="width: 60%; height: 300px; margin: auto;">
              <canvas id="pieChart3"></canvas>
            </div>

          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const pieData = {
    labels: ['Red', 'Blue', 'Yellow'],
    datasets: [{
      data: [10, 20, 30],
      backgroundColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 205, 86, 1)'],
      borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 205, 86, 1)'],
      borderWidth: 1
    }]
  };

  const pieConfig = {
    type: 'pie',
    data: pieData,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true
        }
      }
    }
  };

  new Chart(document.getElementById('pieChart1'), pieConfig);
  new Chart(document.getElementById('pieChart2'), pieConfig);
  new Chart(document.getElementById('pieChart3'), pieConfig);
</script>
-->


    


  <!----------------------------------------------------------------------------BUTTON FUNCTIONS SCRIPT--------------------------------------------------------------->     <script>   
 
        
      // Function to handle Consolidated KPI Report button click
 function redirectToMonthlyReport() {
    // Add animation effect (optional)
    const button = document.querySelector('.four-report-btn button:nth-child(3)');
    button.classList.add('active');

    // Wait for 300ms (like toggle switch) and then redirect
    setTimeout(function() {
        window.location.href = "<?php echo base_url('kpi_reports/index');?>";
    }, 300); // 300ms delay
}

function redirectToConsolidatedReport() {
    // Safely select the Consolidated KPI Report button
    var buttons = document.querySelectorAll('.four-report-btn button');
    var consolidatedBtn = null;

    buttons.forEach(function(btn) {
        if (btn.textContent.trim() === 'Consolidated KPI Report') {
            consolidatedBtn = btn;
        }
    });

    if (consolidatedBtn) {
        consolidatedBtn.classList.add('active');
    }

    setTimeout(function() {
        window.location.href = "<?php echo base_url('kpi_reports/consolidatedReport');?>";
    }, 300);
}
    
function redirectTographs() {
    // Safely select the Consolidated KPI Report button
    var buttons = document.querySelectorAll('.four-report-btn button');
    var graphsBtn = null;

    buttons.forEach(function(btn) {
        if (btn.textContent.trim() === 'Detailed Graphs') {
            consolidatedBtn = btn;
        }
    });

    if (graphsBtn) {
        graphsBtn.classList.add('active');
    }

    setTimeout(function() {
        window.location.href = "<?php echo base_url('kpi_reports/graphs');?>";
    }, 300);
}
    
    
        function redirectToClient() {
    

    // Add animation effect (optional)
    const button = document.querySelector('.four-report-btn button:nth-child(3)');
    button.classList.add('active');

    // Wait for 300ms (like toggle switch) and then redirect
    setTimeout(function() {
        window.location.href = "<?php echo base_url('kpi_reports/clientReport');?>";
    }, 300);
}
    
    </script>
    
    
  <!----------------------------------------------------------------------------auto load for user type--------------------------------------------------------------->     
<script>
    // Set from PHP
    var autoSelectDept = "<?php echo $autoSelectDept; ?>";

    // Auto-select on load
    window.onload = function () {
        if (autoSelectDept === "MEP") {
            const mepBtn = document.getElementById("mepButton");
            if (mepBtn) redirectToMEP(mepBtn);
        } else if (autoSelectDept === "Architecture") {
            const archBtn = document.getElementById("archButton");
            if (archBtn) redirectToArch(archBtn);
        }
    };
</script>

   <!----------------------------------------------------------------------------monthly kpi css--------------------------------------------------------------->     

   
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
