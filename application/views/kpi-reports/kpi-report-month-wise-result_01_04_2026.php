                    
<head>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400&display=swap" rel="stylesheet"> <!-- Roboto Condensed for numbers -->
</head>


<style>
    th {
        font-family: 'Roboto Condensed', sans-serif;  /* Roboto Condensed for numeric values */
        font-weight: 600!important; /* Bold weight for headings */
        font-size: 16px!important;    /* Set the font size for headings */
    }

    td {
        font-family: 'Roboto Condensed', sans-serif;  /* Roboto Condensed for numeric values */
        font-weight: 400!important;  /* Normal weight for readability */
        font-size: 16px !important;  /* Set the font size for values */
    }
</style>


<!-- Include ExcelJS Library -->
<!-- Include ExcelJS Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script type="text/javascript" charset="utf-8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



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
// Initialize row counter early to prevent undefined variable errors
if (!isset($rowCount)) {
    $rowCount = 0;
}

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


// KPI Search dropdown: exclude HR, Software, Accounting, Operations Manager and admin role users
$getListOfEmployees   	= $this->timesheet_login->getListOfEmpInformationForKpiSearch();
$getListOfManagers		= $this->timesheet_login->getListOfEmpInformationForKpiSearch();

//echo '<pre>'; print_r($getListOfManagers); exit;

?>


<link href="<?php echo HTTP_CSS_PATH; ?>kpi-style.css" rel="stylesheet" />
<body id="kpiPage">
<div class="content-wrapper">
  <div class="page-title">      
    <div>
      <h1>Manage KPI</h1>
    </div>
<div class="generate-report-btn" style="margin-left: -45px;">
    <?php if (isset($datesMatch) && $datesMatch): ?>
    <button id="generateBtnArch" onclick="downloadExcelArch()" class="btn btn-primary">
        <span id="btnTextArch">Generate Report</span>
        <span id="spinnerArch" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    </button>
    <?php else: ?>
    <button id="generateBtnArch" class="btn btn-secondary" disabled title="Quality error log data does not match the selected dates">
        <span id="btnTextArch">Generate Report (Disabled)</span>
    </button>
    <?php endif; ?>
</div>

<script>
function downloadExcelArch() {
    const btn = document.getElementById('generateBtnArch');
    const btnText = document.getElementById('btnTextArch');
    const spinner = document.getElementById('spinnerArch');

    // Update button UI to show loading state
    btn.classList.remove('btn-primary');
    btn.classList.add('btn-success');
    btn.disabled = true;
    spinner.classList.remove('d-none');
    btnText.textContent = 'Downloading...';

    const fromDate = document.getElementById('from_date') ? document.getElementById('from_date').value : '';
    const toDate = document.getElementById('to_date') ? document.getElementById('to_date').value : '';
    var searchEl = document.getElementById('search');
    var search = '';
    if (searchEl && searchEl.tagName === 'SELECT') {
        var sv = $(searchEl).val();
        search = (sv && sv.length) ? (Array.isArray(sv) ? [...new Set(sv)].join(', ') : sv) : '';
    } else {
        search = searchEl ? (searchEl.value || '').trim() : '';
    }
    var deptEl = document.getElementById('dept_filter');
    var dept = (deptEl && deptEl.value && deptEl.value !== '__all__') ? deptEl.value : '';

    let url = '<?php echo base_url('kpi_reports/generateMonthWiseEmpDataExcel'); ?>?';
    const params = [];
    
    if (fromDate) {
        params.push('from_date=' + encodeURIComponent(fromDate));
    }
    if (toDate) {
        params.push('to_date=' + encodeURIComponent(toDate));
    }
    if (search) {
        params.push('search=' + encodeURIComponent(search));
    }
    if (dept) {
        params.push('department=' + encodeURIComponent(dept));
    }
    
    url += params.join('&');

    // Allow spinner to appear before redirect
    setTimeout(() => {
        window.location.href = url;

        // Optional: reset button after a delay (for UI recovery if needed)
        setTimeout(() => {
            spinner.classList.add('d-none');
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
            btn.disabled = false;
            btnText.textContent = 'Generate Report';
        }, 4000); // Adjust reset duration as needed

    }, 300); // Short delay to trigger spinner before redirect
}
</script>

      
      
   <!------------------------------------------------------------------------------DOWNLOAD CSV------------------------------------------------------------------> 
   
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

        $canViewGraphs = in_array($userType, ['admin', 'business_head', 'manager']);
    ?>

<!--
    <?php if ($canViewGraphs): ?>
        
        <button onclick="redirectTographs()" class="btn btn-primary">
            Visual Graphs
        </button>

    
    <?php endif; ?>
-->
</div>


  <!------------------------------------------------------------------------------HEADINGS------------------------------------------------------------------>                      
<div class="row mt-4">
                        <div class="col-md-12">
                            <h3>KPI Report</h3>
                        </div>
                    </div>     
            
  <!------------------------------------------------------------------------------DROPDOWNS------------------------------------------------------------------>         
            
            
<?php 
$userType = $this->session->userdata['logged_in_timesheet']['user_type'];

// Handle date selection and department filter
$from_date = isset($from_date) && !empty($from_date) ? $from_date : '';
$to_date = isset($to_date) && !empty($to_date) ? $to_date : '';
$department = isset($department) ? $department : '';

// Set default dates if not provided: previous month (same as getMonthWiseEmpData controller default)
if (empty($from_date) || empty($to_date)) {
    $prevMonth = (int) date('n', strtotime('first day of previous month'));
    $prevYear = (int) date('Y', strtotime('first day of previous month'));
    $from_date = date('Y-m-01', mktime(0, 0, 0, $prevMonth, 1, $prevYear));
    $to_date = date('Y-m-t', mktime(0, 0, 0, $prevMonth, 1, $prevYear));
}

// Convert date range to months array for backward compatibility with existing code
$monthName = [];
if (!empty($from_date) && !empty($to_date)) {
    $startDate = new DateTime($from_date);
    $endDate = new DateTime($to_date);
    $currentDate = clone $startDate;
    $currentDate->modify('first day of this month');
    
    while ($currentDate <= $endDate) {
        $monthName[] = (int)$currentDate->format('n');
        $currentDate->modify('+1 month');
    }
    
    // If no months found, use previous month
    if (empty($monthName)) {
        $prevM = (int) date('n', strtotime('first day of previous month'));
        $monthName = [$prevM];
    }
} else {
    $prevM = (int) date('n', strtotime('first day of previous month'));
    $monthName = [$prevM];
}

if (!is_array($getempId)) {
    $getempId = [$getempId];
}
?>



<?php if (in_array($userType, ['admin', 'business_head', 'manager'])): ?>
<style>
.kpi-filter-card {
    background: linear-gradient(145deg, #ffffff 0%, #f4f6f9 100%);
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(1, 75, 136, 0.08);
    border: 1px solid rgba(1, 75, 136, 0.12);
    padding: 24px 28px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}
.kpi-filter-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, #014b88 0%, #0284c7 100%);
    border-radius: 4px 0 0 4px;
}
.kpi-filter-label {
    font-weight: 600;
    color: #0f172a;
    font-size: 14px;
    letter-spacing: 0.02em;
    margin-bottom: 8px;
    display: block;
}
.kpi-filter-label-inline {
    margin-bottom: 0;
    align-self: center;
    color: #4b5563;
    font-weight: 500;
    font-size: 14px;
}
.kpi-filter-search {
    width: 100%;
    max-width: 560px;
    padding: 12px 16px;
    border: 1px solid #000000;
    border-radius: 10px;
    font-size: 14px;
    color: #334155;
    background: #fff;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.kpi-filter-search:focus {
    outline: none;
    border-color: #014b88;
    box-shadow: 0 0 0 3px rgba(1, 75, 136, 0.12);
}
.kpi-filter-search::placeholder {
    color: #94a3b8;
}
/* Search dropdown - employee/manager multi-select */
.kpi-search-select2-wrap .select2-container--default .select2-selection--multiple {
    border: 1px solid #d1d5db !important;
    border-radius: 8px !important;
    min-height: 42px !important;
    padding: 5px 10px !important;
    background: #fff !important;
    display: flex !important;
    align-items: center !important;
    box-sizing: border-box !important;
}
.kpi-search-select2-wrap .select2-container--default .select2-selection--multiple .select2-selection__rendered {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 6px !important;
    flex: 1 !important;
    min-width: 0 !important;
    padding: 0 !important;
}
.kpi-search-select2-wrap .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #6f42c1 !important;
    border: none !important;
    color: #fff !important;
    border-radius: 20px !important;
    padding: 5px 18px 5px 18px !important;
    font-size: 13px !important;
    font-weight: 500;
    margin: 0 !important;
    line-height: 1.3;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
}
.kpi-search-select2-wrap .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #fff !important;
    margin: 0 !important;
    padding: 10px 0px 0px 5px !important;
    font-size: 14px !important;
    line-height: 16px !important;
    order: -1;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 16px !important;
    min-width: 16px !important;
    height: 16px !important;
    border: none !important;
    background: transparent !important;
    opacity: 0.9;
    vertical-align: middle !important;
    flex-shrink: 0 !important;
}
.kpi-search-select2-wrap .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    opacity: 1;
    color: rgba(255,255,255,0.95) !important;
}
.kpi-search-select2-wrap .select2-container--default .select2-selection--multiple .select2-selection__clear {
    align-self: center !important;
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
}
.kpi-search-select2-wrap .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
    color: #9ca3af !important;
    line-height: 28px !important;
}
.kpi-filter-row {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}
.kpi-filter-period {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
/* Year dropdown - same height as month, aligned */
.kpi-filter-year {
    height: 42px;
    padding: 0 32px 0 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #014b88;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 7.5L2 3.5h8z'/%3E%3C/svg%3E") no-repeat right 12px center;
    background-size: 10px;
    min-width: 90px;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    box-sizing: border-box;
    line-height: 40px;
}
.kpi-filter-year:focus {
    outline: none;
    border-color: #014b88;
    box-shadow: 0 0 0 2px rgba(1, 75, 136, 0.12);
}
/* Month multi-select - match year height, clean pills */
.kpi-month-select2 {
    min-width: 280px;
}
.kpi-month-select2 .select2-container {
    height: 42px !important;
}
.kpi-month-select2 .select2-container--default .select2-selection--multiple {
    border: 1px solid #d1d5db !important;
    border-radius: 8px !important;
    min-height: 42px !important;
    height: 42px !important;
    padding: 5px 10px !important;
    background: #fff !important;
    transition: border-color 0.2s, box-shadow 0.2s;
    box-sizing: border-box !important;
    line-height: 1.4 !important;
    display: flex !important; 
    align-items: center !important;
}



.kpi-month-select2 .select2-container--default .select2-selection--multiple .select2-selection__rendered {
    padding: 0 !important;
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 6px !important;
    flex: 1 !important;
    min-width: 0 !important;
}
.kpi-month-select2 .select2-container--default .select2-selection--multiple .select2-selection__clear {
    align-self: center !important;
    margin: 0 !important;
    padding: 5pxconsolidatedReport !important;
    line-height: 1 !important;
    height: auto !important;
    font-size: 18px !important;
    color: #6b7280 !important;
    flex-shrink: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}
.kpi-month-select2 .select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #014b88 !important;
    box-shadow: 0 0 0 2px rgba(1, 75, 136, 0.12);
}
.kpi-month-select2 .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #6f42c1 !important;
    border: none !important;
    color: #fff !important;
    border-radius: 20px !important;
    padding: 5px 12px 5px 20px !important;
    font-size: 13px;
    font-weight: 500;
    margin: 0 !important;
    line-height: 1.3;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
}
.kpi-month-select2 .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #fff !important;
    margin: 0 !important;
    padding: 12px !important;
    font-size: 14px !important;
    line-height: 16px !important;
    order: -1;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 16px !important;
    min-width: 16px !important;
    height: 16px !important;
    border: none !important;
    background: transparent !important;
    opacity: 0.9;
    vertical-align: middle !important;
    flex-shrink: 0 !important;
}
.kpi-month-select2 .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: rgba(255,255,255,0.95) !important;
    opacity: 1;
}
.kpi-month-select2 .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
    color: #9ca3af !important;
    margin-left: 0 !important;
    line-height: 28px !important;
}
.kpi-filter-hint {
    font-size: 13px;
    color: #6b7280;
    align-self: center;
    margin-left: 0;
}
.kpi-filter-actions {
    display: flex;
    align-items: center;
    gap: 12px;
   
}
.kpi-btn-apply {
    background: linear-gradient(135deg, #014b88 0%, #0369a1 100%);
    color: #fff !important;
    font-weight: 600;
    padding: 10px 22px;
    border-radius: 10px;
    border: none;
    box-shadow: 0 2px 8px rgba(1, 75, 136, 0.3);
    transition: transform 0.15s, box-shadow 0.15s;
}
.kpi-btn-apply:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(1, 75, 136, 0.35);
    color: #fff;

}
.kpi-btn-showall {
    background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
    color: #fff !important;
    font-weight: 600;
    padding: 10px 22px;
    border-radius: 10px;
    border: none;
    box-shadow: 0 2px 8px rgba(234, 88, 12, 0.25);
    transition: transform 0.15s, box-shadow 0.15s;
}
.kpi-btn-showall:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
    color: #fff;
}
</style>
    <div class="row">
        <div class="col-md-12">
            <div class="kpi-filter-card">
                <label class="kpi-filter-label" for="dept_filter">Department</label>
                <div class="kpi-filter-period" style="margin-bottom: 20px;">
                    <select id="dept_filter" name="dept_filter" class="kpi-filter-year" style="min-width: 200px;">
                        <option value="__all__" <?= (empty($department) || $department === '__all__' ? 'selected' : ''); ?>>All</option>
                        <option value="MEP" <?= (isset($department) && $department === 'MEP' ? 'selected' : ''); ?>>MEP</option>
                        <option value="Architectural" <?= (isset($department) && $department === 'Architectural' ? 'selected' : ''); ?>>Architectural</option>
                        <option value="Structural" <?= (isset($department) && $department === 'Structural' ? 'selected' : ''); ?>>Structural</option>
                        <option value="3D Visualization" <?= (isset($department) && $department === '3D Visualization' ? 'selected' : ''); ?>>3D Visualization</option>
                    </select>
                </div>
                <label class="kpi-filter-label" for="search">Search</label>
                <div class="kpi-search-select2-wrap" style="max-width: 560px;">
                    <select name="search[]" id="search" class="kpi-filter-search-select" multiple>
                        <?php
                        $searchOptionValues = [];
                        $addOption = function($name) use (&$searchOptionValues) {
                            $name = trim($name);
                            if ($name === '' || isset($searchOptionValues[$name])) return;
                            $searchOptionValues[$name] = true;
                            echo '<option value="' . htmlspecialchars($name) . '">' . htmlspecialchars($name) . '</option>';
                        };
                        ?>
                        <optgroup label="Employees">
                            <?php if (!empty($getListOfEmployees)): foreach ($getListOfEmployees as $emp): 
                                $empName = isset($emp->name) ? trim($emp->name) : '';
                                $addOption($empName);
                            endforeach; endif; ?>
                        </optgroup>
                        <optgroup label="Managers">
                            <?php if (!empty($getListOfManagers)): foreach ($getListOfManagers as $mgr): 
                                $mgrName = is_object($mgr) && isset($mgr->name) ? trim($mgr->name) : (is_string($mgr) ? trim($mgr) : '');
                                $addOption($mgrName);
                            endforeach; endif; ?>
                        </optgroup>
                    </select>
                </div>

                <div class="kpi-filter-row">
                    <label class="kpi-filter-label kpi-filter-label-inline">Year / Month</label>
                    <div class="kpi-filter-period">
                        <select id="year_filter" name="year_filter" class="kpi-filter-year">
                            <?php
                                $currentYearSelect = (int)date('Y');
                                $startYearSelect = $currentYearSelect - 5;
                                // Include year(s) from loaded date range so dropdown can show correct selection
                                if (!empty($from_date)) {
                                    $fromYear = (int)substr($from_date, 0, 4);
                                    if ($fromYear < $startYearSelect) $startYearSelect = $fromYear;
                                    if ($fromYear > $currentYearSelect) $currentYearSelect = $fromYear;
                                }
                                if (!empty($to_date)) {
                                    $toYear = (int)substr($to_date, 0, 4);
                                    if ($toYear < $startYearSelect) $startYearSelect = $toYear;
                                    if ($toYear > $currentYearSelect) $currentYearSelect = $toYear;
                                }
                                $selectedYear = !empty($from_date) ? (int)substr($from_date, 0, 4) : $currentYearSelect;
                                for ($y = $startYearSelect; $y <= $currentYearSelect; $y++):
                            ?>
                                <option value="<?= $y; ?>" <?= ($y == $selectedYear ? 'selected' : ''); ?>><?= $y; ?></option>
                            <?php endfor; ?>
                        </select>
                        <div class="kpi-month-select2" style="min-width: 360px;">
                            <select id="month_filter" name="month_filter[]" class="kpi-filter-months" multiple>
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>
                        <span class="kpi-filter-hint">Select one or more months</span>
                    </div>
                    <div class="kpi-filter-actions">
                        <button type="button" class="btn kpi-btn-apply" onclick="getMonthWisefilterData()">
                            <i class="fa fa-check"></i> Search
                        </button>
                        <button type="button" class="btn kpi-btn-showall" onclick="clearAllFilters();">
                            <i class="fa fa-refresh"></i> Clear All Filters
                        </button>
                    </div>
                </div>

                <input type="hidden" name="from_date" id="from_date" value="<?= htmlspecialchars($from_date) ?>">
                <input type="hidden" name="to_date" id="to_date" value="<?= htmlspecialchars($to_date) ?>">
            </div>
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    (function() {
        var fromVal = document.getElementById('from_date') && document.getElementById('from_date').value;
        var toVal = document.getElementById('to_date') && document.getElementById('to_date').value;
        var selectedMonths = [];
        if (fromVal && toVal) {
            var fromParts = fromVal.split('-');
            var toParts = toVal.split('-');
            if (fromParts.length >= 2 && toParts.length >= 2) {
                var yearEl = document.getElementById('year_filter');
                var monthEl = document.getElementById('month_filter');
                if (yearEl) yearEl.value = fromParts[0];
                if (monthEl) {
                    var startM = parseInt(fromParts[1], 10);
                    var endM = parseInt(toParts[1], 10);
                    for (var i = 0; i < monthEl.options.length; i++) {
                        var opt = monthEl.options[i];
                        var m = parseInt(opt.value, 10);
                        var inRange = (startM <= endM) ? (m >= startM && m <= endM) : (m >= startM || m <= endM);
                        if (inRange) selectedMonths.push(opt.value);
                    }
                }
            }
        }
        if (selectedMonths.length === 0) {
            var d = new Date();
            var prevMonthNum = d.getMonth(); // 0=Jan..11=Dec
            var prevMonth = prevMonthNum === 0 ? 12 : prevMonthNum;
            var prevYear = prevMonthNum === 0 ? d.getFullYear() - 1 : d.getFullYear();
            var yearEl = document.getElementById('year_filter');
            if (yearEl) yearEl.value = String(prevYear);
            selectedMonths = [String(prevMonth)];
        }
        var searchPreselect = [];
        <?php if (!empty($search)): 
            $searchArr = is_array($search) ? $search : array_values(array_filter(array_map('trim', explode(',', $search))));
            $searchArr = array_values(array_unique($searchArr));
        ?>
        searchPreselect = <?= json_encode($searchArr) ?>;
        <?php endif; ?>

        $(document).ready(function() {
            $('#month_filter').select2({
                placeholder: 'Select one or more months',
                allowClear: true,
                width: '100%'
            });
            if (selectedMonths.length) {
                $('#month_filter').val(selectedMonths).trigger('change');
            }
            $('#search').select2({
                placeholder: 'Select employee or manager names',
                allowClear: true,
                width: '100%'
            });
            if (searchPreselect.length) {
                searchPreselect = Array.from(new Set(searchPreselect));
                $('#search').val(searchPreselect).trigger('change');
            }
        });
    })();
    </script>
<?php endif; ?>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(function() {
    // Search is now a Select2 dropdown (employee/manager names); no autocomplete needed
});
</script>

            
<style>
/* Override autocomplete dropdown */
.ui-autocomplete {
    max-height: 220px !important;
    background-color: #ffffff !important;
    border: 0px solid #ccc !important;
    border-radius: 0px !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05) !important;
    font-size: 14px !important;
    font-family: 'Segoe UI', Tahoma, sans-serif !important;
    padding: 4px 0 !important;
    z-index: 1050 !important;
}

/* List items */
.ui-menu-item-wrapper {
    padding: 8px 12px !important;
    color: #333 !important;
    transition: background 0.2s ease-in-out !important;
    border-bottom: 1px solid #f1f1f1 !important;
    white-space: nowrap !important;
}

.ui-menu-item-wrapper:hover,
.ui-state-active {
    background-color: #f0f4f8 !important;
    color: #014b88 !important;
}

/* Input styling like Select2 */
#search {
    height: 40px;
    padding: 8px 12px;
    border: 1px solid #000;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s ease-in-out;
    background-color: #fff;
}

#search:focus {
    outline: none;
    border-color: #014b88;
    box-shadow: 0 0 0 2px rgba(1, 75, 136, 0.1);
}
</style>           

   <!------------------------------------------------------------------------------------------------------------------------------------------------>            

      
        
    <script>        
    $('#department,#empId,#repId').select2(); // Autosuggest list
    </script>
            
            
		</div>
	</div>    
<!------------------------------------------------------------------------------END OF CARD 1------------------------------------------------------------------>   
    
    
 <!------------------------------------------------------------------------------CARD 2------------------------------------------------------------------>    
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
            <div id="content-wrapper" class="d-flex flex-column">
                <!-- Begin Page Content -->
                <div class="container-fluid">                    

 <!------------------------------------------------------------------------------DYNAMIC MONTH HEADING--------------------------------------------------------------->   
                    
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h3 id="kpiReportHeading"></h3>
                        </div>
                    </div>
                   
<script>

 
function updateKPIReportHeading() {
    var fromDate = document.getElementById('from_date') ? document.getElementById('from_date').value : '';
    var toDate = document.getElementById('to_date') ? document.getElementById('to_date').value : '';
    var deptEl = document.getElementById('dept_filter');
    var deptVal = (deptEl && deptEl.value && deptEl.value !== '__all__') ? deptEl.value : '';
    var deptLabel = deptVal === 'MEP' ? 'MEP' : (deptVal === 'Architectural' ? 'Architectural' : (deptVal === 'Structural' ? 'Structural' : (deptVal === '3D Visualization' ? '3D Visualization' : 'Architecture')));
    if (!deptVal) deptLabel = 'Architecture';

    if (fromDate && toDate) {
        var fromDateObj = new Date(fromDate + 'T00:00:00');
        var toDateObj = new Date(toDate + 'T00:00:00');
        var monthNames = ["January", "February", "March", "April", "May", "June",
                         "July", "August", "September", "October", "November", "December"];
        var fromMonth = monthNames[fromDateObj.getMonth()];
        var fromYear = fromDateObj.getFullYear();
        var toMonth = monthNames[toDateObj.getMonth()];
        var toYear = toDateObj.getFullYear();
        if (fromMonth === toMonth && fromYear === toYear) {
            var headingText = deptLabel + " KPI Report - " + fromMonth + " " + fromYear;
        } else {
            var headingText = deptLabel + " KPI Report - " + fromMonth + " " + fromYear + " to " + toMonth + " " + toYear;
        }
    } else {
        var headingText = deptLabel + " KPI Report";
    }

    $('#kpiReportHeading').text(headingText);
}

// Date inputs change handler - removed month_id change handler
$('#from_date, #to_date').on('change', function () {
    updateKPIReportHeading();
});


// Optional: call once on page load to set initial heading
$(document).ready(function () {
    updateKPIReportHeading();
});

    

      
 $(document).ready(function() {
        // Data for months
        const monthData = {
            "1": { days: 21, hours: 178.5 },
            "2": { days: 20, hours: 170.0 },
            "3": { days: 19, hours: 161.5 },
            "4": { days: 22, hours: 187.0 },
            "5": { days: 21, hours: 178.5 },
            "6": { days: 21, hours: 178.5 },
            "7": { days: 23, hours: 195.5 },
            "8": { days: 20, hours: 170.0 },
            "9": { days: 22, hours: 187.0 },
            "10": { days: 20, hours: 170.0},
            "11": { days: 20, hours: 170.0 },
            "12": { days: 22, hours: 187.0 },
        };

        // Update heading and business days/hours based on the selected date range
        function updateMonthInfo() {
            // Get selected dates
            const fromDate = document.getElementById('from_date') ? document.getElementById('from_date').value : '';
            const toDate = document.getElementById('to_date') ? document.getElementById('to_date').value : '';
            
            if (!fromDate || !toDate) {
                $('#businessDays').text('');
                $('#businessHours').text('');
                return;
            }
            
            // Calculate business days between dates
            const start = new Date(fromDate);
            const end = new Date(toDate);
            let businessDays = 0;
            let currentDate = new Date(start);
            
            while (currentDate <= end) {
                const dayOfWeek = currentDate.getDay();
                if (dayOfWeek !== 0 && dayOfWeek !== 6) { // Not Sunday (0) or Saturday (6)
                    businessDays++;
                }
                currentDate.setDate(currentDate.getDate() + 1);
            }
            
            const businessHours = businessDays * 8.5; // Assuming 8.5 hours per business day
            
            // Display calculated business days and hours
            $('#businessDays').text(`${businessDays} Days`);
            $('#businessHours').text(`${businessHours.toFixed(1)} Hours`);
            
            // Heading is updated by updateKPIReportHeading() function, so we don't update it here
        }

        // Call updateMonthInfo on page load and when dates change
        updateMonthInfo();
        $('#from_date, #to_date').on('change', function() {
            updateMonthInfo();
        });      
    });
     
    
</script>                                 
                    
<!------------------------------------------------------------------------------BUSINESS INFO BOXES--------------------------------------------------------------->                     
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
                    
<!------------------------------------------------------------------------------TABLES--------------------------------------------------------------->   

                   
                    <div id="kpiReportInfomation" style="margin-left: -17px;">
                    
                    


  <!------------------------------------------------------------------------------SUMMARY TABLE------------------------------------------------------------------>
<?php if (isset($datesMatch) && !$datesMatch): ?>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="alert alert-warning" role="alert">
            <strong>Warning:</strong> Quality error log data does not match the selected dates. Grid view and Excel download are disabled.
        </div>
    </div>
</div>
<?php endif; ?>
<?php if( $this->session->userdata['logged_in_timesheet']['user_type'] =='admin' || $this->session->userdata['logged_in_timesheet']['user_type'] =='business_head') : ?> 	
<?php if (isset($datesMatch) && $datesMatch): ?>
<div class="row mt-4 d-flex justify-content-center">
    <div class="col-md-6 d-flex justify-content-center">
       <div class="info-box text-center" style="width: 154%; margin-left: -27%;">
            <h4 id="summaryHeading"><?= ($department === 'MEP') ? 'MEP' : 'Architecture'; ?> Department Summary</h4>
           

            
<table id="departmentTable" class="table border-0">
    <thead>
        <tr>
            <th><strong>Departments</strong></th>
            <th><strong>Productivity%</strong></th>
            <th><strong>Project General%</strong></th>
            <th><strong>eLogic General%</strong></th>
            <th><strong>Availability%</strong></th>
            <th><strong>Utilization%</strong></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        // Define departments to include in summary based on selected department filter
        $departments = ($department === 'MEP') ? ['MEP'] : ['Architecture'];

        // Init totals
        $totals = [];

        foreach ($getkpiReportsSummary as $kpiResult) {
            $dept = in_array($kpiResult->department, ['Architectural', 'Structural', '3D Visualization']) ? 'Architecture' : $kpiResult->department;

            if (!in_array($dept, $departments)) continue;

            // Handle multiple months - aggregate data across selected months
            $monthsToProcess = is_array($monthName) ? $monthName : [$monthName];
            
            // Calculate year for each month from date range
            $monthYearMap = [];
            if (!empty($from_date) && !empty($to_date)) {
                $startDate = new DateTime($from_date);
                $endDate = new DateTime($to_date);
                $currentDate = clone $startDate;
                $currentDate->modify('first day of this month');
                while ($currentDate <= $endDate) {
                    $monthNum = (int)$currentDate->format('n');
                    $yearNum = (int)$currentDate->format('Y');
                    $monthYearMap[$monthNum] = $yearNum;
                    $currentDate->modify('+1 month');
                }
            }
            
            $monthlyTotals = [
                'totalProd' => 0, 'totalGen' => 0, 'totalElog' => 0,
                'totalHours' => 0, 'totalWorkHrs' => 0, 'validMonths' => 0
            ];
            
            foreach ($monthsToProcess as $month) {
                // Get year for this month (default to current year if not in map)
                $yearForMonth = isset($monthYearMap[$month]) ? $monthYearMap[$month] : date('Y');
                // Get production/general hours (use preloaded batch when available for speed)
                $hoursStr = isset($preload['production'][$kpiResult->empId][$month][$yearForMonth]) ? $preload['production'][$kpiResult->empId][$month][$yearForMonth] : $this->kpi_reports_model->empProductionHoursMonthWiseAllStatus($kpiResult->empId, $month, $yearForMonth);
                $hours = explode('@#===', $hoursStr);
                $prod = isset($hours[0]) ? (float)$hours[0] : 0;
                $gen = isset($hours[1]) ? (float)$hours[1] : 0;
                $elog = isset($hours[2]) ? (float)$hours[2] : 0;
                $total = $prod + $gen + $elog;

                // Monthly working hours
                $monthHours = [
                    1 => 178.5, 2 => 170.0, 3 => 161.5, 4 => 187.0,
                    5 => 178.5, 6 => 178.5, 7 => 195.5, 8 => 170.0,
                    9 => 187.0, 10 => 170.0, 11 => 170.0, 12 => 187.0
                ];
                $workHrs = isset($monthHours[$month]) ? $monthHours[$month] : 0;

                if ($workHrs > 0 && $total > 0) {
                    $monthlyTotals['totalProd'] += $prod;
                    $monthlyTotals['totalGen'] += $gen;
                    $monthlyTotals['totalElog'] += $elog;
                    $monthlyTotals['totalHours'] += $total;
                    $monthlyTotals['totalWorkHrs'] += $workHrs;
                    $monthlyTotals['validMonths']++;
                }
            }

            if ($monthlyTotals['validMonths'] == 0 || $monthlyTotals['totalHours'] <= 0) continue;

            // Compute percentages from aggregated totals
            $availability = ($monthlyTotals['totalHours'] / $monthlyTotals['totalWorkHrs']) * 100;
            $productivity = ($monthlyTotals['totalProd'] / $monthlyTotals['totalHours']) * 100;
            $projectGen = ($monthlyTotals['totalGen'] / $monthlyTotals['totalHours']) * 100;
            $elogicGen = ($monthlyTotals['totalElog'] / $monthlyTotals['totalHours']) * 100;
            $utilization = (($monthlyTotals['totalProd'] + $monthlyTotals['totalGen']) / $monthlyTotals['totalHours']) * 100;

            // Initialize if not already
            if (!isset($totals[$dept])) {
                $totals[$dept] = [
                    'count' => 0, 'productivity' => 0, 'projectGen' => 0,
                    'elogicGen' => 0, 'availability' => 0, 'utilization' => 0
                ];
            }

            // Aggregate
            $totals[$dept]['count']++;
            $totals[$dept]['productivity'] += $productivity;
            $totals[$dept]['projectGen'] += $projectGen;
            $totals[$dept]['elogicGen'] += $elogicGen;
            $totals[$dept]['availability'] += $availability;
            $totals[$dept]['utilization'] += $utilization;
        }

        // Render
foreach ($departments as $dept) {
    if (!isset($totals[$dept]) || $totals[$dept]['count'] === 0) continue;

    $c = $totals[$dept]['count'];

    $productivity = round($totals[$dept]['productivity'] / $c);
    $projectGen = round($totals[$dept]['projectGen'] / $c);
    $elogicGen = round($totals[$dept]['elogicGen'] / $c);
    $availability = round($totals[$dept]['availability'] / $c);
    $utilization = round($totals[$dept]['utilization'] / $c);

echo "<tr data-department='{$dept}'>
    <td>{$dept}</td>
    <td style='background-color:#D1E9DC;' title='Productivity'>{$productivity}%</td>
    <td style='background-color:#FDF3D0;' title='Project Generation'>{$projectGen}%</td>
    <td style='background-color:#FFDEC4;' title='eLogic Generation'>{$elogicGen}%</td>
    <td style='background-color:#DDE7F5;' title='Availability'>{$availability}%</td>
    <td style='background-color:#D9CDE6;' title='Utilization'>{$utilization}%</td>
</tr>";


}

        ?>
    </tbody>
</table>

           
         
        </div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>  
  <!------------------------------------------------------------------------------REPORT TABLE------------------------------------------------------------------>
<?php if (isset($datesMatch) && $datesMatch): ?>

<?php
// Array to map month number to month name
$months = [
    '1' => 'Jan',
    '2' => 'Feb',
    '3' => 'Mach',
    '4' => 'Apr',
    '5' => 'May',
    '6' => 'Jun',
    '7' => 'Jul',
    '8' => 'Aug',
    '9' => 'Sep',
    '10' => 'Oct',
    '11' => 'Nov',
    '12' => 'Dec'
];

// Assuming $monthName is the numeric value representing the month
// Check if the month is a developer, then display the respective month name
?>
<div class="kpi-table-scroll-wrapper" style="margin-top: 20px;">
<div id="kpiScrollTop" class="kpi-table-responsive kpi-table-scroll-top" style="overflow-x: auto; overflow-y: hidden; margin-bottom: 0;">
    <div id="kpiScrollTopSpacer" style="height: 1px;"></div>
</div>
<div id="kpiScrollBottom" class="table-responsive kpi-table-responsive" style="overflow-x: auto; overflow-y: visible;">
<table id="employeeTable" class="table table-bordered table-hover">
                                            <thead>
                                                

                                               
<tr>
    <?php
    $sessionData = $this->session->userdata('logged_in_timesheet');
    $userType = isset($sessionData['user_type']) ? strtolower($sessionData['user_type']) : '';

    if ($userType != 'developer'): ?>
        <th class="sortable" data-column="0" title="Reporting Manager" style="display: none;">Reporting Manager</th>
        <th class="sortable" data-column="1" title="Employee ID">Employee ID</th>
        <th class="sortable" data-column="2" title="Employee Name">Employee Name</th>
    <?php else: ?>
    <?php endif; ?>

    <th class="sortable" data-column="3" title="Month">Month</th>
    <th class="sortable" data-column="4" title="Productive Hours">Productive Hours</th>
    <th class="sortable" data-column="5" title="Project General Hours">Project General Hours</th>
    <th class="sortable" data-column="6" title="General Hours">General Hours</th>
    <th class="sortable" data-column="7" title="Total Available Hours">Avail. Hours</th>
    <th class="sortable" data-column="8" title="Availability%">Availability %</th>
    <th class="sortable" data-column="9" title="Utilization%">Utilization %</th>
    <th class="sortable" data-column="10" title="General Hours%">General Hours %</th>
    <th class="sortable" data-column="11" title="Productive Hours%">Productive %</th>
    <th class="sortable" data-column="12" title="Productivity Score">P Score</th>
    <th class="sortable" data-column="13" title="Project General%">Proj. General %</th>
    <th class="sortable" data-column="14" title="Project General Score">PG Score</th>
    <th class="sortable" data-column="15" title="Quality Accuracy">Quality Acc.</th>
    <th class="sortable" data-column="16" title="Quality Score">QA Score</th>
    <th class="sortable" data-column="17" title="Process Adherence">Process Adh.</th>
    <th class="sortable" data-column="18" title="Process Adherence Score">PA Score</th>
    <th class="sortable" data-column="19" title="UPL and Attend not updated">Attend Not Upd.</th>
    <th class="sortable" data-column="20" title="Attendance Score">Attend Score</th>
    <th class="sortable" data-column="21" title="No of Late and Early Login">Late/Early Login</th>
    <th class="sortable" data-column="22" title="No of Late and Early Login Score">L/E Score</th>
    <th class="sortable" data-column="23" title="Above and Beyond">Above & Beyond</th>
    <th class="sortable" data-column="24" title="Above and Beyond Score">A&B Score</th>
    <th class="sortable" data-column="25" title="Total Score">Total</th>
</tr>

<style>
    .kpi-table-scroll-wrapper {
        position: relative;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .kpi-table-scroll-top {
        min-height: 0;
        max-height: 14px;
    }
    .kpi-table-scroll-top #kpiScrollTopSpacer {
        height: 1px;
        min-height: 1px;
        overflow: hidden;
    }
    .kpi-table-responsive {
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #014b88 #e8eef4;
    }
    .kpi-table-responsive::-webkit-scrollbar {
        height: 12px;
    }
    .kpi-table-responsive::-webkit-scrollbar-track {
        background: #e8eef4;
        border-radius: 6px;
    }
    .kpi-table-responsive::-webkit-scrollbar-thumb {
        background: #014b88;
        border-radius: 6px;
    }
    .kpi-table-responsive::-webkit-scrollbar-thumb:hover {
        background: #013a6b;
    }
    #employeeTable {
        table-layout: fixed !important;
        width: max-content !important;
        min-width: 100% !important;
        border-collapse: collapse;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        box-sizing: border-box;
    }
    #employeeTable thead {
        position: sticky;
        top: 0;
        z-index: 2;
    }
    #employeeTable thead th {
        text-align: center !important;
        vertical-align: middle !important;
        padding: 14px 16px !important;
        white-space: normal !important;
        word-break: normal !important;
        overflow-wrap: break-word !important;
        word-wrap: break-word;
        min-width: 115px !important;
        width: 115px !important;
        max-width: 115px;
        line-height: 1.4;
        font-size: 16px !important;
        font-weight: 400 !important;
        background: linear-gradient(180deg, #014b88 0%, #013a6b 100%) !important;
        color: #fff !important;
        border: 1px solid #013a6b;
        box-shadow: 0 1px 0 rgba(255,255,255,0.08) inset;
        box-sizing: border-box;
    }
    #employeeTable thead th:first-child {
        min-width: 1px !important;
        width: 1px !important;
        padding: 0 !important;
        border: none !important;
        overflow: hidden;
    }
    #employeeTable thead th:nth-child(2),
    #employeeTable thead th:nth-child(3) {
        min-width: 95px !important;
        width: 95px !important;
    }
    #employeeTable thead th:nth-child(4) {
        min-width: 62px !important;
        width: 62px !important;
    }
    #employeeTable tbody tr {
        transition: background-color 0.15s ease;
    }
    #employeeTable tbody tr:nth-of-type(even) {
        background-color: #f8fbff;
    }
    #employeeTable tbody tr:hover {
        background-color: #e8f4fc;
    }
    #employeeTable tbody td {
        text-align: center !important;
        vertical-align: middle !important;
        padding: 10px 12px !important;
        font-size: 22px;
        border: 1px solid #e0e8ef;
        min-width: 115px !important;
        width: 115px !important;
        box-sizing: border-box;
    }
    #employeeTable tbody td:first-child {
        min-width: 1px !important;
        width: 1px !important;
        padding: 0 !important;
        border: none !important;
        overflow: hidden;
    }
    #employeeTable tbody td:nth-child(2),
    #employeeTable tbody td:nth-child(3) {
        text-align: left !important;
        min-width: 95px !important;
        width: 95px !important;
        padding-left: 14px !important;
    }
    #employeeTable tbody td:nth-child(4) {
        min-width: 62px !important;
        width: 62px !important;
    }
</style>

                                            </thead>

                                            <tbody>
                                           
                                               

<?php 
// Initialize row counter
$rowCount = 0;

// Determine month range
if ($userType == 'developer') {
    $monthLoopRange = range(1, date('n')-1);
} else {
    // For non-developers, loop through selected months (can be array)
    // Use monthName from controller if set, otherwise calculate from date range
    if (!isset($monthName) || empty($monthName)) {
        // Calculate from date range if not provided by controller
        if (!empty($from_date) && !empty($to_date)) {
            $startDate = new DateTime($from_date);
            $endDate = new DateTime($to_date);
            $currentDate = clone $startDate;
            $currentDate->modify('first day of this month');
            $monthName = [];
            while ($currentDate <= $endDate) {
                $monthName[] = (int)$currentDate->format('n');
                $currentDate->modify('+1 month');
            }
        }
        if (empty($monthName)) {
            $prevM = date('n') - 1;
            $prevM = ($prevM < 1) ? 12 : $prevM;
            $monthName = [$prevM];
        }
    }
    
    if (is_array($monthName)) {
        // Ensure all values are integers
        $monthLoopRange = array_map('intval', array_filter($monthName, 'is_numeric'));
    } else {
        $monthLoopRange = [(int)$monthName];
    }
    
    // Ensure monthLoopRange is not empty (default to previous month)
    if (empty($monthLoopRange)) {
        $prevM = date('n') - 1;
        $prevM = ($prevM < 1) ? 12 : $prevM;
        $monthLoopRange = [$prevM];
    }
}

// Get full month names for headers
$fullMonthNames = [
    '1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April',
    '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August',
    '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
];

// Get short month names in capital letters
$shortMonthNames = [
    '1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR',
    '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG',
    '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'
];

// Calculate year from date range if provided (before the loop)
$yearFromDate = null;
if (!empty($from_date)) {
    $startDateObj = new DateTime($from_date);
    $yearFromDate = (int)$startDateObj->format('Y');
}

$preload = isset($preload) ? $preload : [];
$managerNamesById = isset($managerNamesById) ? $managerNamesById : [];

// Create a map of month to year from the date range
$monthYearMap = [];
if (!empty($from_date) && !empty($to_date)) {
    $startDate = new DateTime($from_date);
    $endDate = new DateTime($to_date);
    $currentDate = clone $startDate;
    $currentDate->modify('first day of this month');
    
    while ($currentDate <= $endDate) {
        $monthNum = (int)$currentDate->format('n');
        $yearNum = (int)$currentDate->format('Y');
        $monthYearMap[$monthNum] = $yearNum;
        $currentDate->modify('+1 month');
    }
}

// Loop through months first (month-wise grouping)
foreach ($monthLoopRange as $currentMonthName):
    // Ensure $currentMonthName is an integer
    $currentMonthName = (int)$currentMonthName;
    
    // Calculate year for the month - use map if available, otherwise use year from from_date, otherwise current year
    if (isset($monthYearMap[$currentMonthName])) {
        $monthYear = $monthYearMap[$currentMonthName];
    } elseif ($yearFromDate !== null) {
        $monthYear = $yearFromDate;
    } else {
        $monthYear = date('Y');
    }
    
    // Now loop through all employees for this month
    foreach ($getkpiReports as $kpiResult):

        // 🔽 All your existing logic should now refer to $currentMonth instead of $monthName 🔽
        // $currentMonthName is already cast to integer in the outer loop
        $currentMonth = $currentMonthName;
        // Pass the year from date range; use preloaded batch when available for speed
        $getTotalProductionH = isset($preload['production'][$kpiResult->empId][$currentMonth][$monthYear]) ? $preload['production'][$kpiResult->empId][$currentMonth][$monthYear] : $this->kpi_reports_model->empProductionHoursMonthWiseAllStatus($kpiResult->empId, $currentMonth, $monthYear);
        $productionHoursArray = explode('@#===', $getTotalProductionH);
        $totalProductionHours = !empty($productionHoursArray[0]) ? $productionHoursArray[0] : 0; 
        $totalEmpProductionGeneralHours = isset($productionHoursArray[1]) ? $productionHoursArray[1] : 0;
        $totalEmpGeneralHours = isset($productionHoursArray[2]) ? $productionHoursArray[2] : 0;
        $totalHours = array_sum([$totalProductionHours, $totalEmpGeneralHours, $totalEmpProductionGeneralHours]);

        // Calculate percentages
        $productivityPercentage = $totalHours > 0 ? ($totalProductionHours / $totalHours) * 100 : 0;
        $projectgeneralPercentage = $totalHours > 0 ? ($totalEmpProductionGeneralHours / $totalHours) * 100 : 0;
        $elogicgeneralPercentage = $totalHours > 0 ? ($totalEmpGeneralHours / $totalHours) * 100 : 0;
        $utilizationPercentage = $totalHours > 0 ? (($totalProductionHours + $totalEmpProductionGeneralHours) / $totalHours) * 100 : 0;

        // Use preloaded batch when available for speed
        $instanceData = isset($preload['defaulter'][$kpiResult->empId][$currentMonth][$monthYear]) ? $preload['defaulter'][$kpiResult->empId][$currentMonth][$monthYear] : $this->kpi_reports_model->timesheetDefaulter($kpiResult->empId, $currentMonth, $monthYear);
        $timespent = isset($preload['lms'][$kpiResult->empId][$currentMonth][$monthYear]) ? $preload['lms'][$kpiResult->empId][$currentMonth][$monthYear] : $this->kpi_reports_model->LMShours($kpiResult->empId, $currentMonth, $monthYear);
        $quality = isset($preload['quality'][$kpiResult->empId][$currentMonth][$monthYear]) ? $preload['quality'][$kpiResult->empId][$currentMonth][$monthYear] : $this->kpi_reports_model->qualityLog($kpiResult->empId, $currentMonth, $monthYear);
        $timespentinhrs = $timespent / 360;

        // Perk: use preloaded batch when available (by date range)
        $monthStart = $monthYear . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '-01';
        $monthEnd = date('Y-m-t', strtotime($monthStart));
        $absentdata = isset($preload['perk'][$kpiResult->emp_com_id][$currentMonth][$monthYear]) ? $preload['perk'][$kpiResult->emp_com_id][$currentMonth][$monthYear] : $this->kpi_reports_model->perkabsentByDateRange($kpiResult->emp_com_id, $monthStart, $monthEnd);
        $perkArray = explode('Perk@#', $absentdata);
        $perkEmpAbsentDays = isset($perkArray[0]) ? (int)$perkArray[0] : 0;
        $lateLogin = isset($perkArray[1]) ? (int)$perkArray[1] : 0;
        $earlyOut = isset($perkArray[2]) ? (int)$perkArray[2] : 0;
        $LateloginandEarlyout = max($lateLogin + $earlyOut - 3, 0);

        // Set monthWorkingHours
        switch ($currentMonth):
            case '1': $monthWorkingHours = 178.5; break;
            case '2': $monthWorkingHours = 170.0; break;
            case '3': $monthWorkingHours = 161.5; break;
            case '4': $monthWorkingHours = 187.0; break;
            case '5': $monthWorkingHours = 178.5; break;
            case '6': $monthWorkingHours = 178.5; break;
            case '7': $monthWorkingHours = 195.5; break;
            case '8': $monthWorkingHours = 170.0; break;
            case '9': $monthWorkingHours = 187.0; break;
            case '10': $monthWorkingHours = 170.0; break;
            case '11': $monthWorkingHours = 170.0; break;
            case '12': $monthWorkingHours = 187.0; break;
            default: $monthWorkingHours = 0; break;
        endswitch;

        $availabilityPercentage = ($totalHours / $monthWorkingHours) * 100;

        // Display: normal rows (allowed dept + has reporting manager) OR searched person's own row OR MEP rows when MEP department selected
        $searchIncludedIds = isset($search_included_emp_ids) && is_array($search_included_emp_ids) ? $search_included_emp_ids : array();
        $isSearchIncludedRow = !empty($searchIncludedIds) && in_array($kpiResult->empId, $searchIncludedIds);
        $normalDisplay = !in_array($kpiResult->department, ['IT', 'HR', 'Software','Operations Manager','MEP','']) && !empty($kpiResult->reporting_manger);
        $mepDisplay = ($department === 'MEP' && $kpiResult->department === 'MEP' && !empty($kpiResult->reporting_manger));
        if ($normalDisplay || $isSearchIncludedRow || $mepDisplay):
    
    $getTeamwiseMangerName = isset($managerNamesById[$kpiResult->reporting_manger]) ? $managerNamesById[$kpiResult->reporting_manger] : $this->resourcelog_model->getManagerName($kpiResult->reporting_manger);
    $firstName = $getTeamwiseMangerName ? strtok($getTeamwiseMangerName, ' ') : '';
    if ($firstName === false) $firstName = '';
?>


                                                    
                                                <?php if($totalHours !=0){ ?> 
                                                <tr data-department="<?php echo $kpiResult->department; ?>" 
                                                        data-manager="<?php echo $kpiResult->reporting_manger; ?>" 
                                                        data-employee="<?php echo $kpiResult->empId; ?>" 
                                                        data-productinHours="<?php echo $totalProductionHours; ?>">
                                                    
                                                    
                                                    
                                                        
                                                    
                                                    <?php if ($userType != 'developer'): ?>
                                                    
                                                    

                                                     
                                                         <td style="display: none;"><strong><?php echo $firstName; ?></strong></td>
                                                        <td><?php echo $kpiResult->emp_com_id; ?></td>
                                                        <td style="background-color: #f0f0f0;">
    <strong>
        <?php 
        // Assuming $kpiResult->name contains the full name
        $nameParts = explode(" ", $kpiResult->name); // Split the full name by space
        echo $nameParts[0]; // Output the first name (first part of the name)
        ?>
    </strong>
                                                        </td>
                                                    <?php endif; ?>

                                                        <td title="Month"><?php echo $shortMonthNames[$currentMonth]; ?></td>
                                                        <td title="Productive Hours"><?php echo $totalProductionHours;?></td>
                                                        <td title="Project General Hours"><?php echo $totalEmpProductionGeneralHours;?></td>
                                                        <td title="General Hours"><?php echo $totalEmpGeneralHours;?></td>
                                                        <td title="Total Available Hours"><?php echo $totalHours;?></td>
                                                        <td title="Availability%"><?php echo round($availabilityPercentage). '%'; ?></td>
                                                        <td title="Utilization%"><?= round($utilizationPercentage) . '%'; ?></td>
                                                        <td title="General Hours%"><?php echo round($elogicgeneralPercentage). '%';?></td>
                                                        <td title="Productive Hours%"><?php echo round($productivityPercentage). '%';?></td>
                                                         <td title="Productivity Score" style="background-color: #C2E5F0;">
                                                        <strong>
                                                        <?php 
                                                        if ($productivityPercentage == 0) {
                                                             $productivityScore = 0;
                                                             echo $productivityScore;
                                                        } elseif ($productivityPercentage >= 85) {
                                                            $productivityScore = 20;
                                                            echo $productivityScore;
                                                        } elseif ($productivityPercentage >= 80) {
                                                            $productivityScore = 15;
                                                            echo $productivityScore;
                                                        } else {
                                                            $productivityScore = 10;
                                                            echo $productivityScore;
                                                        }
                                                        ?>
                                                        </strong>
                                                        </td>
                                                        <td title="Project General%"><?php echo round($projectgeneralPercentage). '%';?></td>
                                                        <td title="Project General Score" style="background-color: #C2E5F0; "><strong><?php 
        if ($projectgeneralPercentage == 0) {
            $projectGeneralScore = 0;
            echo $projectGeneralScore;
        } elseif ($projectgeneralPercentage <= 20) {
            $projectGeneralScore = 15;
            echo $projectGeneralScore;
        } elseif ($projectgeneralPercentage > 20 && $projectgeneralPercentage <= 30) {
            $projectGeneralScore = 10;
            echo $projectGeneralScore;
        } else {
            $projectGeneralScore = 5;
            echo $projectGeneralScore;
        }
        ?></strong></td>
                                                        <td title="Quality Accuracy"><?php 
                                                        if ($quality === null || $quality === '') {
                                                            echo '--';
                                                        } elseif ($quality == 0) {
                                                            echo '100%';
                                                        } else {
                                                            echo round($quality) . '%';
                                                        }
                                                        ?></td>
                                                        <td title="Quality Score" style="background-color: #C2E5F0; "><strong><?php 
        if ($quality === null || $quality === '') {
            $qualityScore = 0;
            echo $qualityScore;
        } elseif ($quality == 0) {
            // Zero errors means 100% quality, so give highest score
            $qualityScore = 20;
            echo $qualityScore;
        } elseif ($quality > 94) {
            $qualityScore = 20;
            echo $qualityScore;
        } elseif ($quality >= 90 && $quality <= 94) {
            $qualityScore = 10;
            echo $qualityScore;
        } else {
            $qualityScore = 5;
            echo $qualityScore;
        }
        ?></strong></td>
                                                        <td title="Process Adherence"><?php echo !empty($instanceData) ? $instanceData : 0; ?></td>
                                                        <td title="Process Adherence Score" style="background-color: #C2E5F0; "><strong><?php 
        if ($instanceData == 0) {
            $processAdherenceScore = 15;
            echo $processAdherenceScore;
        } elseif ($instanceData <= 5) {
            $processAdherenceScore = 10;
            echo $processAdherenceScore;
        } elseif ($instanceData >= 6) {
            $processAdherenceScore = 5;
            echo $processAdherenceScore;
        }
        ?></strong></td>
                                                        <td title="UPL and Attend not updated"><?php echo !empty($perkEmpAbsentDays) ? $perkEmpAbsentDays : 0; ?></td>
                                                        <td title="Attendance Score" style="background-color: #C2E5F0; "><strong><?php 
        if ($perkEmpAbsentDays == 0) {
            $attendanceScore = 10;
            echo $attendanceScore;
        } elseif ($perkEmpAbsentDays <= 5) {
            $attendanceScore = 5;
            echo $attendanceScore;
        } elseif ($perkEmpAbsentDays >= 6) {
            $attendanceScore = 2;
            echo $attendanceScore;
        }
        ?></strong></td>
                                                        <td title="No of Late and Early Login"><?php echo $LateloginandEarlyout; ?></td>
                                                        <td title="No of Late and Early Login Score" style="background-color: #C2E5F0; "><strong><?php 
        if ($LateloginandEarlyout == 0) {
            $lateLoginScore = 10;
            echo $lateLoginScore;
        } elseif ($LateloginandEarlyout <= 5) {
            $lateLoginScore = 5;
            echo $lateLoginScore;
        } elseif ($LateloginandEarlyout >= 6) {
            $lateLoginScore = 2;
            echo $lateLoginScore;
        }
        ?></strong></td>
                                                       <td title="Above and Beyond">
    <?php 
    if (!empty($timespent)) {
        // Get full hours
        $hours = floor($timespent / 3600);
        
        // Get remaining minutes after hours
        $minutes = floor(($timespent % 3600) / 60);
        
        // Output in the format hours.minutes (no seconds)
        echo $hours . '.' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
    } else {
        echo '0';  // Default value if no time is set
    }
    ?>
</td>

 <td title="Above and Beyond Score" style="background-color: #C2E5F0;">
    <strong>
        <?php 
        // Convert $timespent to hours and minutes
        $hours = floor($timespent / 3600);
        
        // Check the score based on hours
        if ($hours >= 10) {
            $ABScore = 10;  // For 10 or more hours
            echo $ABScore;
        } elseif ($hours >= 8) {
            $ABScore = 8;   // For 8-9 hours
            echo $ABScore;
        } else {
            $ABScore = 6;   // For less than 8 hours
            echo $ABScore;
        }
        ?>
    </strong>
</td>
                                                        <td title="Total Score" style="
    background-color: 
        <?php 
            // Sum all the individual scores
            $totalScore = $productivityScore + $projectGeneralScore + $qualityScore + $processAdherenceScore + $attendanceScore + $lateLoginScore + $ABScore;
            
            // Check the total score and apply the background color based on the range
            if ($totalScore >= 90) {
                echo '#A8E6A1'; // Pastel green
            } elseif ($totalScore >= 80) {
                echo '#FFF59D'; // Pastel yellow
            } else {
                echo '#FFCDD2'; // Pastel red
            }
        ?>
">
    <strong>
        <?php echo $totalScore; ?>
    </strong>
</td>
                                                        

                                                    </tr>
                                              <?php 
                                              $rowCount++; // Increment counter for each displayed row
                                              } ?>
                                                <?php endif; ?>
                                                  <?php 
                                                    endforeach; // End employee loop
                                                endforeach; // End month loop
                                                ?>
                                                
                                                
                                                
                                                
                                                
       
                                            </tbody>
                                        </table>
</div>
<script>
(function() {
    var scrollTop = document.getElementById('kpiScrollTop');
    var scrollBottom = document.getElementById('kpiScrollBottom');
    var spacer = document.getElementById('kpiScrollTopSpacer');
    var table = document.getElementById('employeeTable');
    if (!scrollTop || !scrollBottom || !spacer || !table) return;
    function setSpacerWidth() {
        spacer.style.width = (table.scrollWidth || table.offsetWidth) + 'px';
    }
    function syncScroll(from, to) {
        to.scrollLeft = from.scrollLeft;
    }
    setSpacerWidth();
    window.addEventListener('resize', setSpacerWidth);
    scrollTop.addEventListener('scroll', function() { syncScroll(scrollTop, scrollBottom); });
    scrollBottom.addEventListener('scroll', function() { syncScroll(scrollBottom, scrollTop); });
    setTimeout(setSpacerWidth, 100);
})();
</script>
</div>
<?php endif; ?>


<style>
th.sortable {
  cursor: pointer;
  user-select: none;
}

th, td {
  padding: 8px;
  text-align: left;
}

th.sortable.active-sort {
  box-shadow: inset 0 -8px 0 #C2E5F0;
}

#employeeTable thead th {
  text-align: center !important;
}
#employeeTable tbody td {
  text-align: center !important;
}
#employeeTable tbody td:nth-child(2),
#employeeTable tbody td:nth-child(3) {
  text-align: left !important;
}

</style>

<script>
  document.querySelectorAll("th.sortable").forEach((header, columnIndex) => {
    header.addEventListener("click", () => {
      const table = header.closest("table");
      const tbody = table.querySelector("tbody");
      const rows = Array.from(tbody.querySelectorAll("tr"));
      const isAscending = header.classList.toggle("asc");
      table.querySelectorAll("th.sortable").forEach(th => {
        if (th !== header) th.classList.remove("asc", "desc", "active-sort");
      });
      header.classList.add("active-sort");
      header.classList.toggle("desc", !isAscending);

      rows.sort((a, b) => {
        const cellA = a.children[columnIndex].innerText.trim();
        const cellB = b.children[columnIndex].innerText.trim();

        // Check if numeric
        const aNum = parseFloat(cellA.replace(/[^0-9.-]+/g, ""));
        const bNum = parseFloat(cellB.replace(/[^0-9.-]+/g, ""));

        const bothNumbers = !isNaN(aNum) && !isNaN(bNum);
        const compareA = bothNumbers ? aNum : cellA.toLowerCase();
        const compareB = bothNumbers ? bNum : cellB.toLowerCase();

        if (compareA < compareB) return isAscending ? -1 : 1;
        if (compareA > compareB) return isAscending ? 1 : -1;
        return 0;
      });

      // Append sorted rows
      rows.forEach(row => tbody.appendChild(row));
    });
  });
</script>


<?php if (isset($rowCount) && $rowCount > 0): ?>
<div class="pagination-container">
    <?php echo $pagination_links; ?>
</div>
<?php else: ?>
<div class="no-data-message" style="text-align: center; padding: 40px 20px; margin-top: 20px;">
    <p style="font-size: 18px; color: #666; font-weight: bold;">No data found for the selected month(s).</p>
    <p style="font-size: 14px; color: #999; margin-top: 10px;">Please try selecting different month(s) or search criteria.</p>
</div>
<?php endif; ?>
                    
<style>
.pagination {
    display: flex;
    justify-content: center;
    gap: 0px;
    margin-top: 20px;
    flex-wrap: wrap;
}

  .pagination a {
    text-decoration: none;
    padding: 8px 12px;
    background-color: #f0f0f0;
    color: #333;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
  }
    
.page-link {
    padding: 8px 12px;

    border-radius: 5px;
    text-decoration: none;
    color: #007bff;
    transition: 0.3s;
}

  .pagination a:hover {
    background-color: #ddd;
  }

  .pagination a.active {
    background-color: #ddd;  /* Same as hover color */
    color: white;
    border-color: #ddd;      /* Same border color as hover */
  }
    
      .pagination a.disabled {
    color: #ccc;
    border-color: #ddd;
    pointer-events: none;
  }
    
</style>
            


  <!------------------------------------------------------------------------------SUMMARY HEADING------------------------------------------------------------------>
<script>
    

    $(document).ready(function() {
    function updateDepartmentSummaryHeading() {
        var fromDate = document.getElementById('from_date') ? document.getElementById('from_date').value : '';
        var toDate = document.getElementById('to_date') ? document.getElementById('to_date').value : '';
        var search = document.getElementById('search') ? document.getElementById('search').value.trim() : '';
        var searchFromUrl = '<?php echo isset($search) && !empty($search) ? htmlspecialchars($search, ENT_QUOTES) : ''; ?>';
        
        // Use search from URL if available, otherwise use from input field
        if (!search && searchFromUrl) {
            search = searchFromUrl;
        }
        
        if (fromDate && toDate) {
            // Format dates to show month names
            var fromDateObj = new Date(fromDate + 'T00:00:00');
            var toDateObj = new Date(toDate + 'T00:00:00');
            
            var monthNames = ["January", "February", "March", "April", "May", "June",
                             "July", "August", "September", "October", "November", "December"];
            
            var fromMonth = monthNames[fromDateObj.getMonth()];
            var fromYear = fromDateObj.getFullYear();
            var toMonth = monthNames[toDateObj.getMonth()];
            var toYear = toDateObj.getFullYear();
            
            // If same month, show single month format
            if (fromMonth === toMonth && fromYear === toYear) {
                var dateText = fromMonth + " " + fromYear;
            } else {
                // If different months, show range
                var dateText = fromMonth + " " + fromYear + " to " + toMonth + " " + toYear;
            }
        } else {
            var dateText = new Date().toLocaleString('default', { month: 'long', year: 'numeric' });
        }

        // Build heading with search name if available
        var headingText = dateText + " Architecture Department Summary";
        if (search) {
            headingText += " ( " + search + " )";
        }
        
        $('#summaryHeading').text(headingText); // Update heading
    }

    // Set default on page load
    updateDepartmentSummaryHeading();

    // Attach event listener for date inputs change
    $('#from_date, #to_date').on('change', updateDepartmentSummaryHeading);
    
    // Attach event listener for search input change
    $('#search').on('input change', updateDepartmentSummaryHeading);
});

    
</script>

  <!------------------------------------------------------------------------------TABLE CELLS------------------------------------------------------------------>

<style>
    th, td {
        padding: 8px;
        text-align: center;
    }
    
        th:nth-child(1),
    td:nth-child(1) {
        width: 80px!important; /* Increase width for Department, Reporting Manager, Employee */
    }
    
    th:nth-child(2),
    td:nth-child(2) {
        width: 65px!important; /* Increase width for Department, Reporting Manager, Employee */
    }

    th:nth-child(3), 
    td:nth-child(3) {
        width: 81px; /* Increase width for Department, Reporting Manager, Employee */
    }
    
     th:nth-child(4), th:nth-child(5), th:nth-child(6), th:nth-child(7),
    td:nth-child(4), td:nth-child(5), td:nth-child(6), td:nth-child(7)
    {
    width: 65px !important;
}

    th:nth-child(8),th:nth-child(9), th:nth-child(10), th:nth-child(12),th:nth-child(14), th:nth-child(24),
    td:nth-child(8),td:nth-child(9), td:nth-child(10),  td:nth-child(12),td:nth-child(14), td:nth-child(24){
    width: 66px !important;
}
    
  th:nth-child(11), th:nth-child(13),th:nth-child(15), th:nth-child(16), th:nth-child(17), th:nth-child(18), th:nth-child(19), th:nth-child(20), th:nth-child(21), th:nth-child(22), th:nth-child(23), 
  td:nth-child(11), td:nth-child(13),td:nth-child(15), td:nth-child(16), td:nth-child(17), td:nth-child(18), td:nth-child(19), td:nth-child(20), td:nth-child(21), td:nth-child(22), td:nth-child(23) {
    width: 63px !important;
}

    th:nth-child(25),
    td:nth-child(25){
    width: 65px !important;
}

/* KPI month-wise table: column widths */
#employeeTable.table thead th,
#employeeTable.table tbody td {
    min-width: 115px !important;
    width: 115px !important;
}
#employeeTable.table thead th:first-child,
#employeeTable.table tbody td:first-child {
    min-width: 1px !important;
    width: 1px !important;
}
#employeeTable.table thead th:nth-child(2),
#employeeTable.table thead th:nth-child(3),
#employeeTable.table tbody td:nth-child(2),
#employeeTable.table tbody td:nth-child(3) {
    min-width: 95px !important;
    width: 95px !important;
}
#employeeTable.table thead th:nth-child(4),
#employeeTable.table tbody td:nth-child(4) {
    min-width: 62px !important;
    width: 62px !important;
}
    
</style>

                    
</div>
                    
      <script>                             
   

          
function getMonthWisefilterData(event) {
    if (event) {
        event.preventDefault();
    }
    
    var yearEl = document.getElementById('year_filter');
    var year = yearEl ? parseInt(yearEl.value, 10) : new Date().getFullYear();
    // Use Select2 value when available (native options may not reflect Select2 selection)
    var selectedMonths = [];
    if (typeof $ !== 'undefined' && $('#month_filter').data('select2')) {
        var val = $('#month_filter').val();
        if (val && val.length) selectedMonths = val.map(function(v) { return parseInt(v, 10); });
    }
    if (selectedMonths.length === 0) {
        var monthEl = document.getElementById('month_filter');
        if (monthEl) {
            for (var i = 0; i < monthEl.options.length; i++) {
                if (monthEl.options[i].selected) selectedMonths.push(parseInt(monthEl.options[i].value, 10));
            }
        }
    }
    if (selectedMonths.length === 0) {
        // Default to previous month when nothing selected (not current month)
        var d = new Date();
        var prevMonthNum = d.getMonth() === 0 ? 12 : d.getMonth(); // getMonth() is 0-based; Jan=0 -> prev=Dec=12
        selectedMonths = [prevMonthNum];
    }
    var searchEl = document.getElementById('search');
    var search = '';
    if (searchEl && searchEl.tagName === 'SELECT') {
        var sv = $(searchEl).val();
        search = (sv && sv.length) ? (Array.isArray(sv) ? [...new Set(sv)].join(', ') : sv) : '';
    } else if (searchEl) {
        search = searchEl.value.trim();
    }
    // Use only the month(s) selected by the user; do not add current month when searching
    selectedMonths.sort(function(a, b) { return a - b; });
    
    var firstMonth = selectedMonths[0];
    var lastMonth = selectedMonths[selectedMonths.length - 1];
    var fromDate = year + '-' + String(firstMonth).padStart(2, '0') + '-01';
    var lastDay = new Date(year, lastMonth, 0).getDate();
    var toDate = year + '-' + String(lastMonth).padStart(2, '0') + '-' + String(lastDay).padStart(2, '0');
    
    var deptEl = document.getElementById('dept_filter');
    var dept = (deptEl && deptEl.value && deptEl.value !== '__all__') ? deptEl.value : '';
    var url = "<?php echo base_url('kpi_reports/getMonthWiseEmpData'); ?>?";
    url += 'from_date=' + encodeURIComponent(fromDate);
    url += '&to_date=' + encodeURIComponent(toDate);
    if (search) url += '&search=' + encodeURIComponent(search);
    if (dept) url += '&department=' + encodeURIComponent(dept);
    window.location.href = url;
}

function clearAllFilters() {
    // Clear all filters and reload page - will show default date range (previous month)
    window.location.href = "<?php echo base_url('kpi_reports/getMonthWiseEmpData'); ?>";
}   
 
          
      
   </script>  
    
     
 <!----------------------------------------------------------------------------END OF TABLES--------------------------------------------------------------->         
        </div>
        <!-- End of Page Content -->
        </div>
      </div>
    </div>
  </div>
</div>

    
    
    
<script>   

 <!----------------------------------------------------------------------------FILTER BY REPORTING MANAGER--------------------------------------------------------->  

    function filterByManager(reporting_manager) {
    // Get all rows in the table
    var rows = document.querySelectorAll('tbody tr');

    // Get the employee dropdown
    var empSelect = document.getElementById("empId");
    
    // Clear the employee dropdown first, and reset it to "All"
    empSelect.innerHTML = '<option value="all">All</option>';

    // Loop through all the rows and filter based on the selected reporting manager
    rows.forEach(function(row) {
        var managerInRow = row.getAttribute('data-manager'); // Get the manager of the row
        var empId = row.getAttribute('data-employee'); // Get the employee ID of the row
        var empName = row.cells[3].textContent.trim(); // Assuming employee's name is in the 4th cell (adjust index as needed)

        // If the row belongs to the selected manager, show the row and add the employee to the dropdown
        if (reporting_manager === 'all' || managerInRow === reporting_manager) {
            row.style.display = '';  // Show the row

            // Add employee to the dropdown if not already added
            if (!empSelect.querySelector(`option[value="${empId}"]`)) {
                var option = document.createElement("option");
                option.value = empId;
                option.text = empName; // Display employee's name in the dropdown
                empSelect.appendChild(option);
            }
        } else {
            row.style.display = 'none';  // Hide the row
        }
    });

}

 <!----------------------------------------------------------------------------FILTER BY EMPLOYEE--------------------------------------------------------------->  
     
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
     
  <!----------------------------------------------------------------------------BUTTON FUNCTIONS SCRIPT--------------------------------------------------------------->      
        

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
            graphsBtn = btn;
        }
    });

    if (graphsBtn) {
        graphsBtn.classList.add('active');
    }

    setTimeout(function() {
        window.location.href = "<?php echo base_url('kpi_reports/graphs');?>";
    }, 300);
}
    

    
    </script>
    
    
<style>
    .activekpi { background-color: #014b88 !important; font-weight: bold; border: 0px solid white; }
    </style>
   
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
