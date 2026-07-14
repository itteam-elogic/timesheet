
<head>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400&display=swap" rel="stylesheet"> 
</head>

<style>
    /* Scroll wrapper: vertical + horizontal scroll like reference (dark blue thumb, light track) */
    .kpi-table-scroll-wrapper {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 70vh;
        scrollbar-width: thin;
        scrollbar-color: #014b88 #e9ecef;
    }
    .kpi-table-scroll-wrapper::-webkit-scrollbar {
        width: 14px;
        height: 14px;
    }
    .kpi-table-scroll-wrapper::-webkit-scrollbar-track {
        background: #e9ecef;
        border-radius: 4px;
    }
    .kpi-table-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #014b88;
        border-radius: 4px;
        min-height: 40px;
    }
    .kpi-table-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background: #013a6b;
    }
    .kpi-table-scroll-wrapper::-webkit-scrollbar-corner {
        background: #e9ecef;
    }

    /* 2nd image style: dark blue header, white text, vertical separators */
    .kpi-table-scroll-wrapper thead th {
        background: #014b88 !important;
        color: #fff !important;
        font-weight: 700 !important;
        white-space: normal;
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: normal;
        line-height: 1.25;
        padding: 12px 8px;
        min-height: 44px;
        text-align: center;
        vertical-align: middle;
        min-width: 90px;
        border: none !important;
        border-right: 1px solid rgba(255,255,255,0.4) !important;
        box-shadow: none;
    }
    .kpi-table-scroll-wrapper thead th:last-child {
        border-right: none !important;
    }
    /* Data rows: light grey background like 2nd image */
    .kpi-table-scroll-wrapper tbody tr {
        background: #f5f5f5;
    }
    .kpi-table-scroll-wrapper tbody tr:nth-child(even) {
        background: #ebebeb;
    }
    .kpi-table-scroll-wrapper tbody td {
        border-color: #dee2e6;
        color: #333;
    }
    /* Top horizontal scrollbar (above header, 2nd image style) */
    #kpiScrollTopInner {
        scrollbar-width: thin;
        scrollbar-color: #014b88 #e9ecef;
    }
    #kpiScrollTopInner::-webkit-scrollbar { height: 14px; }
    #kpiScrollTopInner::-webkit-scrollbar-track { background: #e9ecef; border-radius: 4px; }
    #kpiScrollTopInner::-webkit-scrollbar-thumb { background: #014b88; border-radius: 4px; }
    #kpiScrollTopInner::-webkit-scrollbar-thumb:hover { background: #013a6b; }
</style>


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

if (!empty($_REQUEST['empId'])) {
    $empIds = $_REQUEST['empId'];
    if (!is_array($empIds)) {
        $empIds = array($empIds); // Convert to array if it's a single value
    }
    $getempId = implode(', ', $empIds);
} else {
    $getempId = 'all';
}


if(!empty($_REQUEST['repId'])): 
		
				$getrepId      	 =	 implode(' ,' ,$_REQUEST['repId']);
				
		else:
				
				$getrepId      	 =	 'all';
				
		endif;


$getListOfEmployees   	= $this->timesheet_login->getListOfEmpInformation(); // List of Clients

$getListOfManagers		= $this->timesheet_login->getReportingManagers(NULL); // List of Clients


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
    <button id="generateBtn" onclick="downloadExcel()" class="btn btn-primary">
        <span id="btnText">Generate Report</span>
        <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    </button>
</div>

<script>
function downloadExcel() {
    const btn = document.getElementById('generateBtn');
    const btnText = document.getElementById('btnText');
    const spinner = document.getElementById('spinner');

    // Change color and show spinner
    btn.classList.remove('btn-primary');
    btn.classList.add('btn-success');
    spinner.classList.remove('d-none');
    btn.disabled = true;
    btnText.textContent = 'Downloading...';

    const fromDate = document.getElementById('from_date') ? document.getElementById('from_date').value : '';
    const toDate = document.getElementById('to_date') ? document.getElementById('to_date').value : '';
    const search = '<?php echo isset($search) ? $search : ''; ?>';

    let url = '<?php echo base_url('kpi_reports/generateMonthWiseEmpDataExcel_mep'); ?>?';
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
    
    url += params.join('&');

    // Delay to show the spinner before redirect
    setTimeout(() => {
        window.location.href = url;

        // Optional: Reset UI after some time (if needed)
        setTimeout(() => {
            spinner.classList.add('d-none');
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
            btn.disabled = false;
            btnText.textContent = 'Generate Report';
        }, 4000); // Adjust duration as needed

    }, 300); // Slight delay to show spinner before download starts
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
        Individual KPI
    </button>

    <button onclick="redirectToConsolidatedReport()" class="btn btn-primary">
        PM Wise KPI
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
$userType = isset($this->session->userdata['logged_in_timesheet']['user_type'])
    ? strtolower(trim((string) $this->session->userdata['logged_in_timesheet']['user_type']))
    : '';
$canViewKpiDeptSummary = in_array($userType, ['admin', 'business_head', 'manager', 'superadmin'], true);
// Year and selected months come from controller (default: previous month)
$year = isset($year) ? (int)$year : (int)date('Y', strtotime('first day of previous month'));
$selected_months = isset($selected_months) && is_array($selected_months) ? $selected_months : [(int)date('n', strtotime('first day of previous month'))];
$from_date = isset($from_date) ? $from_date : '';
$to_date = isset($to_date) ? $to_date : '';
$monthName = isset($monthName) && is_array($monthName) ? $monthName : $selected_months;

if (!is_array($getempId)) {
    $getempId = [$getempId];
}
$startYear = 2010;
$endYear = (int)date('Y');
?>



<?php if ($canViewKpiDeptSummary): ?>
    <input type="hidden" name="from_date" id="from_date" value="<?= htmlspecialchars($from_date) ?>">
    <input type="hidden" name="to_date" id="to_date" value="<?= htmlspecialchars($to_date) ?>">
    <div class="kpi-filter-panel">
        <div class="kpi-filter-row kpi-filter-search-row">
            <label class="kpi-filter-label" for="search">Search</label>
            <input type="text" name="search" id="search" class="kpi-filter-search-input"
                   placeholder="Enter employee or manager names (comma separated for multiple)"
                   value="<?= isset($search) ? htmlspecialchars($search) : '' ?>">
        </div>
        <div class="kpi-filter-row kpi-filter-year-month-row">
            <label class="kpi-filter-label">Year / Month</label>
            <select id="kpi_year" class="kpi-filter-year-select">
                <?php for ($y = $endYear; $y >= $startYear; $y--): ?>
                <option value="<?= $y ?>" <?= ($y == $year) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <div class="kpi-month-tag-wrapper" id="kpi_month_tag_wrapper">
                <div class="kpi-month-pills" id="kpi_month_pills"></div>
                <select id="kpi_month_add" class="kpi-month-add-select" title="Select one or more months">
                    <option value="">Select one or more months</option>
                    <?php
                    $monthLabels = ['1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December'];
                    foreach ($monthLabels as $num => $label):
                    ?>
                    <option value="<?= $num ?>"><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <select name="kpi_month[]" id="kpi_month" multiple="multiple" style="display: none;">
                <?php foreach (['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec'] as $num => $label): ?>
                <option value="<?= $num ?>" <?= in_array((int)$num, $selected_months) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
            <div class="kpi-filter-actions">
                <button type="button" class="kpi-btn-search" onclick="getMonthWisefilterData_MEP()">
                    <i class="fa fa-check"></i> SEARCH
                </button>
                <button type="button" class="kpi-btn-clear" onclick="clearAllFilters_MEP();">
                    <i class="fa fa-refresh"></i> CLEAR ALL FILTERS
                </button>
            </div>
        </div>
    </div>
    <style>
        .kpi-filter-panel { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .kpi-filter-row { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; }
        .kpi-filter-search-row { margin-bottom: 16px; }
        .kpi-filter-label { font-weight: 600; color: #333; min-width: 100px; font-size: 14px; }
        .kpi-filter-search-input { flex: 1; min-width: 280px; padding: 10px 14px; border: 1px solid #ced4da; border-radius: 6px; font-size: 14px; color: #333; background: #fff; }
        .kpi-filter-search-input::placeholder { color: #6c757d; }
        .kpi-filter-year-month-row { align-items: flex-start; }
        .kpi-filter-year-select { width: 100px; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 6px; font-size: 14px; color: #333; background: #fff; }
        .kpi-month-tag-wrapper { display: inline-flex; flex-wrap: wrap; align-items: center; gap: 8px; min-height: 40px; padding: 6px 10px; border: 1px solid #ced4da; border-radius: 6px; background: #fff; min-width: 260px; }
        .kpi-month-pills { display: inline-flex; flex-wrap: wrap; gap: 6px; align-items: center; }
        .kpi-month-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #6f42c1; color: #fff; border-radius: 20px; font-size: 13px; font-weight: 500; }
        .kpi-month-pill .kpi-pill-remove { cursor: pointer; opacity: 0.9; font-size: 14px; line-height: 1; padding: 0 2px; }
        .kpi-month-pill .kpi-pill-remove:hover { opacity: 1; }
        .kpi-month-add-select { border: none; outline: none; color: #6c757d; font-size: 14px; background: transparent; min-width: 180px; padding: 4px 0; }
        .kpi-filter-actions { display: flex; gap: 10px; margin-left: auto; flex-shrink: 0; }
        .kpi-btn-search { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #014b88; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 14px; cursor: pointer; white-space: nowrap; }
        .kpi-btn-search:hover { background: #013a6b; color: #fff; }
        .kpi-btn-clear { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #ff9e4f; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 14px; cursor: pointer; white-space: nowrap; }
        .kpi-btn-clear:hover { background: #e88a3a; color: #fff; }
    </style>
    <script>
    (function() {
        var monthNames = {'1':'January','2':'February','3':'March','4':'April','5':'May','6':'June','7':'July','8':'August','9':'September','10':'October','11':'November','12':'December'};
        function renderPills() {
            var sel = document.getElementById('kpi_month');
            var container = document.getElementById('kpi_month_pills');
            if (!sel || !container) return;
            container.innerHTML = '';
            var opts = sel.options;
            for (var i = 0; i < opts.length; i++) {
                if (!opts[i].selected) continue;
                var val = opts[i].value;
                var pill = document.createElement('span');
                pill.className = 'kpi-month-pill';
                pill.innerHTML = (monthNames[val] || val) + ' <span class="kpi-pill-remove" aria-label="Remove">&times;</span>';
                var rm = pill.querySelector('.kpi-pill-remove');
                if (rm) rm.addEventListener('click', function(v){ return function(e){ e.preventDefault(); removeMonth(v); }}(val));
                container.appendChild(pill);
            }
        }
        function addMonth(val) {
            var sel = document.getElementById('kpi_month');
            if (!sel) return;
            var opt = sel.querySelector('option[value="'+val+'"]');
            if (opt && !opt.selected) { opt.selected = true; renderPills(); }
            var addSel = document.getElementById('kpi_month_add');
            if (addSel) addSel.value = '';
        }
        function removeMonth(val) {
            var sel = document.getElementById('kpi_month');
            if (!sel) return;
            var opt = sel.querySelector('option[value="'+val+'"]');
            if (opt) { opt.selected = false; renderPills(); }
        }
        document.getElementById('kpi_month_add').addEventListener('change', function() {
            if (this.value) { addMonth(this.value); }
        });
        renderPills();
    })();
    </script>
<?php endif; ?>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(function() {
    // Trigger autocomplete for each term separated by commas
    $("#search").on('input', function() {
        var inputVal = $(this).val(); // Get the current input value
        var terms = inputVal.split(','); // Split the input value by commas
        
        // If there are more than one term, loop through them
        if (terms.length > 1) {
            // Trigger autocomplete for the last term
            var lastTerm = terms[terms.length - 1].trim(); // Get the last term
            if (lastTerm.length >= 2) { // Only trigger if the last term has 2 or more characters
                // Call autocomplete for the last term
                getAutocompleteSuggestions(lastTerm);
            }
        } else {
            // If there's only one term or less, just trigger the usual autocomplete
            var lastTerm = inputVal.trim();
            if (lastTerm.length >= 2) { // Trigger when the term has 2 or more characters
                getAutocompleteSuggestions(lastTerm);
            }
        }
    });

    function getAutocompleteSuggestions(term) {
        $.ajax({
            url: "<?php echo base_url('kpi_reports/autosuggest_employee_names'); ?>",
            dataType: "json",
            data: {
                term: term
            },
            success: function(data) {
                // Show the results in the dropdown
                $("#search").autocomplete("option", "source", data);
                $("#search").autocomplete("search", term); // Force the autocomplete to update
            }
        });
    }

    // Prevent Enter key submission if search is empty
    $("#search").on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            var searchVal = $(this).val().trim();
            if (!searchVal) {
                // If search is empty, prevent default and show alert
                alert('Please enter an employee name to search');
                return false;
            }
            // If search has value, trigger filter
            getMonthWisefilterData_MEP(e);
        }
    });

    // Default autocomplete initialization
    $("#search").autocomplete({
        minLength: 2, // Minimum characters before suggestions show
        select: function(event, ui) {
            var currentVal = $(this).val();
            var terms = currentVal.split(','); // Split the current value by commas
            terms[terms.length - 1] = ui.item.value; // Replace the last term with the selected value
            $(this).val(terms.join(', ') + ', '); // Join the terms back with a comma and space
            return false;
        },
        focus: function(event, ui) {
            // Prevent the default behavior of focusing on the item
            event.preventDefault();
        }
    });
});
</script>


<style>
/* Datepicker styling */
.ui-datepicker {
    font-size: 14px;
    background: #ffffff;
    border: 2px solid #014b88;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    width: 260px;
    padding: 0;
    z-index: 9999;
}

.ui-datepicker-header {
    background: linear-gradient(to bottom, #014b88 0%, #003366 100%);
    color: #ffffff;
    border: none;
    border-radius: 4px 4px 0 0;
    padding: 10px 8px;
    margin: 0;
    position: relative;
}

.ui-datepicker-title {
    color: #ffffff;
    font-weight: bold;
    text-align: center;
}

.ui-datepicker-prev,
.ui-datepicker-next {
    background-color: transparent;
    border: none;
    cursor: pointer;
    width: 30px;
    height: 30px;
    top: 50%;
    transform: translateY(-50%);
    position: absolute;
}

.ui-datepicker-prev {
    left: 8px;
}

.ui-datepicker-next {
    right: 8px;
}

.ui-datepicker-prev .ui-icon,
.ui-datepicker-next .ui-icon {
    background-image: none !important;
    background: none !important;
    text-indent: 0 !important;
    width: 100% !important;
    height: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    position: relative !important;
    overflow: hidden !important;
}

/* Hide any default jQuery UI icon content */
.ui-datepicker-prev .ui-icon:before,
.ui-datepicker-next .ui-icon:before,
.ui-datepicker-prev .ui-icon:after,
.ui-datepicker-next .ui-icon:after {
    display: none !important;
    content: none !important;
}

.ui-datepicker-prev .ui-icon .arrow-content,
.ui-datepicker-next .ui-icon .arrow-content {
    color: #000000;
    font-size: 12px;
    font-weight: bold;
    display: block !important;
    line-height: 30px;
    text-align: center;
    white-space: nowrap;
    width: 100%;
    position: relative;
    z-index: 1;
}

.ui-datepicker .ui-datepicker-prev span, 
.ui-datepicker .ui-datepicker-next span {
    display: block;
    position: absolute;
    left: 8%;
    margin-left: -8px;
    top: 34%;
    margin-top: -8px;
    font-size: 11px !important;
}

/* Ensure only arrow-content is shown, hide everything else */
.ui-datepicker-prev .ui-icon > *:not(.arrow-content),
.ui-datepicker-next .ui-icon > *:not(.arrow-content) {
    display: none !important;
}

.ui-datepicker-prev .ui-icon:empty:before,
.ui-datepicker-next .ui-icon:empty:before {
    display: none !important;
}

.ui-datepicker-prev:hover,
.ui-datepicker-next:hover {
    background-color: rgba(255, 255, 255, 0.25) !important;
}

.ui-datepicker select.ui-datepicker-month,
.ui-datepicker select.ui-datepicker-year {
    font-size: 11px;
    padding: 2px 4px;
    margin: 0 2px;
    background-color: #ffffff;
    border: 1px solid #bdbdbd;
    border-radius: 3px;
    color: #014b88;
    font-weight: bold;
}

.ui-datepicker table {
    background-color: #ffffff;
    width: 100%;
    border-collapse: separate;
    border-spacing: 2px;
    margin: 0;
    padding: 8px;
}

.ui-datepicker td {
    padding: 1px;
    width: 14.28%;
    text-align: center;
}

.ui-datepicker td a {
    color: #333333;
    text-align: center;
    padding: 8px 4px;
    display: block;
    min-height: 26px;
    line-height: 26px;
    text-decoration: none;
    font-size: 13px;
}

.ui-datepicker td a:hover {
    background-color: #e6f2ff;
    color: #014b88;
}

.ui-datepicker td.ui-datepicker-today a {
    background-color: #fff9e6;
    color: #014b88;
    font-weight: bold;
    border: 2px solid #ff9e79;
}

.ui-datepicker td.ui-datepicker-current-day a {
    background-color: #014b88;
    color: #ffffff;
    font-weight: bold;
}

.ui-datepicker td.ui-datepicker-other-month a {
    color: #cccccc !important;
}

.ui-datepicker th {
    background: linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%);
    color: #014b88;
    font-weight: bold;
    border-bottom: 2px solid #014b88;
    padding: 8px 4px;
    text-align: center;
    width: 14.28%;
    font-size: 12px;
}

.ui-datepicker th:nth-child(n),
.ui-datepicker td:nth-child(n) {
    min-width: 24px;
    max-width: 24px;
}

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
    border: 0px solid #ccc;
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
    // Removed month_id select2 - using date inputs now
    </script>
            
            
		</div>
	</div>    
<!------------------------------------------------------------------------------END OF CARD 1------------------------------------------------------------------>   
    
    
 <!------------------------------------------------------------------------------CARD 2------------------------------------------------------------------>    
  <div class="row">
    <div class="col-md-12">
      <div class="card" style="width: 106%; margin-left: -43px;">
        <div class="card-body">
            <div id="content-wrapper" class="d-flex flex-column">
                <!-- Begin Page Content -->
                <div class="container-fluid">                    

 <!------------------------------------------------------------------------------DEPT BUTTONS------------------------------------------------------------------>       

                    
<div class="four-report-btn" style="margin-left: 9px;">
    
    <?php 
        $empId = $this->session->userdata['logged_in_timesheet']['empId'];
        $userType = $this->session->userdata['logged_in_timesheet']['user_type'];
    

        $isMEPManager = in_array($empId, $mepManagers);
        $isARCManager = in_array($empId, $arcManagers);
    


        // Decide autoSelectDept for JS
        if ($isMEPManager) {
            $autoSelectDept = 'MEP';
        } elseif ($userType == 'developer') {
             $autoSelectDept = ($empDepartment == 'MEP') ? 'MEP' : 'Architecture';
        } else {
            $autoSelectDept = 'MEP'; // Default for others
        }
    ?>
    
    <?php if ($isMEPManager): ?>
    <button onclick="redirectToMEP()" class="btn btn-primary activekpi" style = "background-color: #014b88 !important; font-weight: bold; border: 0px solid white; ">
        MEP
    </button>
    
    <?php elseif ($isARCManager): ?>
    <button onclick="redirectToArch()" class="btn btn-primary">
        Architecture
    </button>

     <?php else: ?>
     <?php if ($userType == 'developer'): ?>
    <?php if ($empDepartment == 'MEP'): ?>
    <button onclick="redirectToMEP()" class="btn btn-primary" style = "background-color: #014b88 !important; font-weight: bold; border: 0px solid white; ">
        MEP
    </button>

    <?php else: ?>
    <button onclick="redirectToArch()" class="btn btn-primary">
        Architecture
    </button>
    
    <?php endif; ?>
    <?php else: ?>
    <button onclick="redirectToMEP()" class="btn btn-primary" style = "background-color: #014b88 !important; font-weight: bold; border: 0px solid white; " >
        MEP
    </button>
    <button onclick="redirectToArch()" class="btn btn-primary">
        Architecture
    </button>
    <?php endif; ?>
    <?php endif; ?>
</div>   

                    
                    
<script>
 function redirectToMEP() {
    // Add animation effect (optional)
    const button = document.querySelector('.four-report-btn button');
    button.classList.add('active');
     
    var fromDate = document.getElementById('from_date') ? document.getElementById('from_date').value : '';
    var toDate = document.getElementById('to_date') ? document.getElementById('to_date').value : '';
    // Wait for 300ms (like toggle switch) and then redirect
    setTimeout(function() {
        var url = "<?php echo base_url('kpi_reports/getMonthWiseEmpData_mep'); ?>";
        if (fromDate && toDate) {
            url += "?from_date=" + encodeURIComponent(fromDate) + "&to_date=" + encodeURIComponent(toDate);
        }
        window.location.href = url;
    }, 300); // 300ms delay
}

 function redirectToArch() {
    // Add animation effect (optional)
    const button = document.querySelector('.four-report-btn button');
    button.classList.add('active');
  
    var fromDate = document.getElementById('from_date') ? document.getElementById('from_date').value : '';
    var toDate = document.getElementById('to_date') ? document.getElementById('to_date').value : '';
    
    // Wait for 300ms (like toggle switch) and then redirect
    setTimeout(function() {
        var url = "<?php echo base_url('kpi_reports/getMonthWiseEmpData'); ?>";
        if (fromDate && toDate) {
            url += "?from_date=" + encodeURIComponent(fromDate) + "&to_date=" + encodeURIComponent(toDate);
        }
        window.location.href = url;
    }, 300); // 300ms delay
}
                    
                    </script>
  
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
            var headingText = "MEP KPI Report - " + fromMonth + " " + fromYear;
        } else {
            // If different months, show range
            var headingText = "MEP KPI Report - " + fromMonth + " " + fromYear + " to " + toMonth + " " + toYear;
        }
    } else {
        var headingText = "MEP KPI Report";
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
<!--
             
     
<div id="modal-loader-kpi" style="display: block; text-align: center;">
            <i class="fa fa-spinner fa-spin" style="font-size: 82px;"> </i>
</div> 
-->
                    
<div id="" style="margin-left: -17px;">


  <!------------------------------------------------------------------------------SUMMARY TABLE------------------------------------------------------>
<?php if ($canViewKpiDeptSummary): ?>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="kpi-dept-summary-box">
            <h4 id="summaryHeading">MEP KPI Report</h4>

<div class="kpi-dept-summary-scroll">
<table id="departmentTable" class="kpi-dept-summary-table">
    <thead>
        <tr>
            <th class="kpi-dept-col-dept">Departments</th>
            <th class="kpi-dept-col-pct"><span class="kpi-th-line">Productivity</span><span class="kpi-th-line">%</span></th>
            <th class="kpi-dept-col-pct"><span class="kpi-th-line">Project General</span><span class="kpi-th-line">%</span></th>
            <th class="kpi-dept-col-pct"><span class="kpi-th-line">eLogic General</span><span class="kpi-th-line">%</span></th>
            <th class="kpi-dept-col-pct"><span class="kpi-th-line">Availability</span><span class="kpi-th-line">%</span></th>
            <th class="kpi-dept-col-pct"><span class="kpi-th-line">Utilization</span><span class="kpi-th-line">%</span></th>
            <th class="kpi-dept-col-hrs kpi-dept-hrs-group-start"><span class="kpi-th-line">Productivity</span><span class="kpi-th-line">Hours</span></th>
            <th class="kpi-dept-col-hrs"><span class="kpi-th-line">Project General</span><span class="kpi-th-line">Hours</span></th>
            <th class="kpi-dept-col-hrs"><span class="kpi-th-line">eLogic General</span><span class="kpi-th-line">Hours</span></th>
            <th class="kpi-dept-col-hrs"><span class="kpi-th-line">Availability</span><span class="kpi-th-line">Hours</span></th>
            <th class="kpi-dept-col-hrs"><span class="kpi-th-line">Utilization</span><span class="kpi-th-line">Hours</span></th>
            <th class="kpi-dept-col-total"><span class="kpi-th-line">Total</span><span class="kpi-th-line">Hours</span></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        // Define unified department
        $departments = ['MEP'];

        // Init totals
        $totals = [];

        foreach ($getkpiReportsSummary as $kpiResult) {
           $dept = $kpiResult->department === 'MEP' ? 'MEP' : $kpiResult->department;


            if (!in_array($dept, $departments)) continue;

            // Aggregate production/general hours across all months in the date range
            $prod = 0;
            $gen = 0;
            $elog = 0;
            $totalWorkHrs = 0;
            
            // Monthly working hours
            $monthHours = [
                1 => 178.5, 2 => 170.0, 3 => 161.5, 4 => 187.0,
                5 => 178.5, 6 => 178.5, 7 => 195.5, 8 => 170.0,
                9 => 187.0, 10 => 170.0, 11 => 170.0, 12 => 187.0
            ];
            
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
            
            // Loop through each month and aggregate (Approved + Unapproved + Rejected for MEP)
            $monthsToProcess = is_array($monthName) ? $monthName : [(int)$monthName];
            foreach ($monthsToProcess as $month) {
                $month = (int)$month; // Ensure it's an integer
                // Get year for this month (default to current year if not in map)
                $yearForMonth = isset($monthYearMap[$month]) ? $monthYearMap[$month] : date('Y');
                // Use MEP-specific function so P/PG/E include Approved + Unapproved + Rejected
                $hours = explode('@#===', $this->kpi_reports_model->empProductionHoursMonthWiseMEP($kpiResult->empId, $month, $yearForMonth));
                $prod += isset($hours[0]) ? (float)$hours[0] : 0;
                $gen += isset($hours[1]) ? (float)$hours[1] : 0;
                $elog += isset($hours[2]) ? (float)$hours[2] : 0;
                $totalWorkHrs += isset($monthHours[$month]) ? $monthHours[$month] : 0;
            }
            
            $total = $prod + $gen + $elog;
            $workHrs = $totalWorkHrs;

            if ($workHrs <= 0 || $total <= 0) continue;

            // Initialize if not already
            if (!isset($totals[$dept])) {
                $totals[$dept] = [
                    'count' => 0,
                    'totalProd' => 0, 'totalGen' => 0, 'totalElog' => 0,
                    'totalHours' => 0, 'totalWorkHrs' => 0, 'totalUtilHours' => 0
                ];
            }

            // Aggregate department totals
            $totals[$dept]['count']++;
            $totals[$dept]['totalProd'] += $prod;
            $totals[$dept]['totalGen'] += $gen;
            $totals[$dept]['totalElog'] += $elog;
            $totals[$dept]['totalHours'] += $total;
            $totals[$dept]['totalWorkHrs'] += $workHrs;
            $totals[$dept]['totalUtilHours'] += ($prod + $gen);
        }

// Render
foreach ($departments as $dept) {
    if (!isset($totals[$dept]) || $totals[$dept]['count'] === 0) continue;

    $deptTotalProd = $totals[$dept]['totalProd'];
    $deptTotalGen = $totals[$dept]['totalGen'];
    $deptTotalElog = $totals[$dept]['totalElog'];
    $deptTotalHours = $totals[$dept]['totalHours'];
    $deptTotalWorkHrs = $totals[$dept]['totalWorkHrs'];
    $deptTotalUtilHours = $totals[$dept]['totalUtilHours'];

    $productivity = $deptTotalHours > 0 ? round(($deptTotalProd / $deptTotalHours) * 100) : 0;
    $projectGen = $deptTotalHours > 0 ? round(($deptTotalGen / $deptTotalHours) * 100) : 0;
    $elogicGen = $deptTotalHours > 0 ? round(($deptTotalElog / $deptTotalHours) * 100) : 0;
    $availability = $deptTotalWorkHrs > 0 ? round(($deptTotalHours / $deptTotalWorkHrs) * 100) : 0;
    $utilization = $deptTotalHours > 0 ? round(($deptTotalUtilHours / $deptTotalHours) * 100) : 0;

echo "<tr data-department='{$dept}'>
    <td class='kpi-dept-col-dept'>{$dept}</td>
    <td class='kpi-dept-col-pct kpi-dept-bg-prod' title='Productivity'>{$productivity}%</td>
    <td class='kpi-dept-col-pct kpi-dept-bg-proj' title='Project General'>" . $projectGen . "%</td>
    <td class='kpi-dept-col-pct kpi-dept-bg-elog' title='eLogic General'>" . $elogicGen . "%</td>
    <td class='kpi-dept-col-pct kpi-dept-bg-avail' title='Availability'>" . $availability . "%</td>
    <td class='kpi-dept-col-pct kpi-dept-bg-util' title='Utilization'>" . $utilization . "%</td>
    <td class='kpi-dept-col-hrs kpi-dept-bg-prod kpi-dept-hrs-group-start' title='Productivity Hours'>" . kpi_summary_hours_cell($deptTotalProd) . "</td>
    <td class='kpi-dept-col-hrs kpi-dept-bg-proj' title='Project General Hours'>" . kpi_summary_hours_cell($deptTotalGen) . "</td>
    <td class='kpi-dept-col-hrs kpi-dept-bg-elog' title='eLogic General Hours'>" . kpi_summary_hours_cell($deptTotalElog) . "</td>
    <td class='kpi-dept-col-hrs kpi-dept-bg-avail' title='Availability Hours'>" . kpi_summary_hours_cell($deptTotalHours) . "</td>
    <td class='kpi-dept-col-hrs kpi-dept-bg-util' title='Utilization Hours'>" . kpi_summary_hours_cell($deptTotalUtilHours) . "</td>
    <td class='kpi-dept-col-total kpi-dept-bg-total' title='Total Available Hours'>" . kpi_summary_hours_cell($deptTotalHours) . "</td>
</tr>";


}

        ?>
    </tbody>
</table>
</div>
        </div>
    </div>
</div>
<?php endif; ?> 


  <!---------------------------------------------------------------------REPORT TABLE------------------------------------------------------------------>

    
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
<!-- Top horizontal scrollbar (above header, 2nd image style) -->
<div class="kpi-table-scroll-top-bar" id="kpiScrollTopBar" style="margin-top: 20px; border: 1px solid #ddd; border-bottom: none; border-radius: 4px 4px 0 0; height: 18px;">
    <div id="kpiScrollTopInner" style="overflow-x: auto; overflow-y: hidden; height: 100%;">
        <span class="scroll-spacer" id="kpiScrollSpacer" style="display: inline-block; height: 1px;"></span>
    </div>
</div>
<!-- Table with vertical + horizontal scroll -->
<div class="kpi-table-scroll-wrapper" id="kpiTableScrollWrapper" style="border: 1px solid #ddd; border-top: none; border-radius: 0 0 4px 4px;">
<table id="employeeTable" style="margin-left: -9px; min-width: max-content; table-layout: auto;" class="table table-bordered">
                                            <thead>
                                                

                                               
<tr>
<?php
                $sessionData = $this->session->userdata('logged_in_timesheet');
        $userType = isset($sessionData['user_type']) ? strtolower($sessionData['user_type']) : '';
                if ($userType != 'developer'): ?>
                <th class="sortable" data-column="0" style="display: none;">Reporting<br>Manager</th>
                <th class="sortable" data-column="1">Employee<br>ID</th>
                <th class="sortable" data-column="2">Employee<br>Name</th>
                <th class="sortable" data-column="3">Month</th>
            <?php else: ?>
                <th class="sortable" data-column="3">Month</th>
            <?php endif; ?>
    <th class="sortable" data-column="4">Productive<br>Hours</th>
    <th class="sortable" data-column="5">Project General<br>Hours</th>
    <th class="sortable" data-column="6">General<br>Hours</th>
    <th class="sortable" data-column="7">Total Available<br>Hours</th>
    <th class="sortable" data-column="8">Productive<br>Hours%</th>
    <th class="sortable" data-column="9">General<br>Hours%</th>
    <th class="sortable" data-column="10">Utilization%</th>
    <th class="sortable" data-column="11">Availability%</th>
    <th class="sortable" data-column="12">Availability<br>Score</th>
    <th class="sortable" data-column="13">Project<br>General%</th>
    <th class="sortable" data-column="14">Project General<br>Score</th>
    <th class="sortable" data-column="15">Quality<br>Accuracy</th>
    <th class="sortable" data-column="16">Quality<br>Score</th>
    <th class="sortable" data-column="17">Process<br>Adherence</th>
    <th class="sortable" data-column="18">Process Adherence<br>Score</th>
    <th class="sortable" data-column="19">UPL and Attend<br>not updated</th>
    <th class="sortable" data-column="20">Attendance<br>Score</th>
    <th class="sortable" data-column="21">No of Late and<br>Early Login</th>
    <th class="sortable" data-column="22">Late/Early Login<br>Score</th>
    <th class="sortable" data-column="23">Above and<br>Beyond</th>
    <th class="sortable" data-column="24">Above and Beyond<br>Score</th>
    <th class="sortable" data-column="25">Total<br>Score</th>
</tr>
                                                
<style>
    #employeeTable th {
        text-align: center;
        vertical-align: middle !important;
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
            $monthName = [date('n')];
        }
    }
    
    if (is_array($monthName)) {
        // Ensure all values are integers
        $monthLoopRange = array_map('intval', array_filter($monthName, 'is_numeric'));
    } else {
        $monthLoopRange = [(int)$monthName];
    }
    
    // Ensure monthLoopRange is not empty
    if (empty($monthLoopRange)) {
        $monthLoopRange = [date('n')];
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
        // Pass the year from date range to handle December correctly
        // Use MEP-specific function so P/PG/E include Approved + Unapproved + Rejected for MEP
        $getTotalProductionH = $this->kpi_reports_model->empProductionHoursMonthWiseMEP($kpiResult->empId , $currentMonth, $monthYear);
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

        // Pass the year from date range to handle December correctly
        $instanceData = $this->kpi_reports_model->timesheetDefaulter($kpiResult->empId , $currentMonth, $monthYear);
        $timespent = $this->kpi_reports_model->LMShours($kpiResult->empId, $currentMonth, $monthYear) ?: 0;
        $quality = $this->kpi_reports_model->qualityLog($kpiResult->empId, $currentMonth, $monthYear);
    
        // Use date-range perk lookup so 2026-02-01 to 2026-02-28 matches perk_monthly_data (calendar or 26-25 cycle)
        $monthStart = $monthYear . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '-01';
        $monthEnd = date('Y-m-t', strtotime($monthStart));
        $absentdata = $this->kpi_reports_model->perkabsentByDateRange($kpiResult->emp_com_id, $monthStart, $monthEnd);
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

        // Display only valid data
        if (!in_array($kpiResult->department, ['IT', 'HR', 'Software','Operations Manager','Structural','Architectural','3D Visualization','']) && !empty($kpiResult->reporting_manger)):
    $getTeamwiseMangerName = $this->resourcelog_model->getManagerName($kpiResult->reporting_manger);
    $firstName = strtok($getTeamwiseMangerName, ' ');  // Extracts the first word (i.e., first name)                                        
?>

                                                    
                                                <?php if($totalHours !=0){ ?> 
                                                <tr data-department="<?php echo $kpiResult->department; ?>" 
                                                        data-manager="<?php echo $kpiResult->reporting_manger; ?>" 
                                                        data-employee="<?php echo $kpiResult->empId; ?>" 
                                                        data-productinHours="<?php echo kpi_hours_display($totalProductionHours); ?>">
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
                                                        <td>
                                                        <?php 
                                                        // Calculate year for the month based on date range
                                                        $monthYear = date('Y');
                                                        if (!empty($from_date) && !empty($to_date)) {
                                                            $startDate = new DateTime($from_date);
                                                            $endDate = new DateTime($to_date);
                                                            $currentDate = clone $startDate;
                                                            $currentDate->modify('first day of this month');
                                                            
                                                            // Find which year this month belongs to
                                                            while ($currentDate <= $endDate) {
                                                                $currentMonthNum = (int)$currentDate->format('n');
                                                                if ($currentMonthNum == $currentMonthName) {
                                                                    $monthYear = (int)$currentDate->format('Y');
                                                                    break;
                                                                }
                                                                $currentDate->modify('+1 month');
                                                            }
                                                        }
                                                        
                                                        // Display short month name in capital letters
                                                        echo $shortMonthNames[$currentMonthName];
                                                        ?>
                                                    </td> <!-- Month Name for Non-Developer -->
                                                    <?php else: ?>
                    <td>
                        <?php 
                        // Calculate year for the month based on date range
                        $monthYear = date('Y');
                        if (!empty($from_date) && !empty($to_date)) {
                            $startDate = new DateTime($from_date);
                            $endDate = new DateTime($to_date);
                            $currentDate = clone $startDate;
                            $currentDate->modify('first day of this month');
                            
                            // Find which year this month belongs to
                            while ($currentDate <= $endDate) {
                                $currentMonthNum = (int)$currentDate->format('n');
                                if ($currentMonthNum == $currentMonthName) {
                                    $monthYear = (int)$currentDate->format('Y');
                                    break;
                                }
                                $currentDate->modify('+1 month');
                            }
                        }
                        
                        // Display short month name in capital letters
                        echo $shortMonthNames[$currentMonthName];
                        ?>
                    </td> <!-- Month Name for Developer -->
                <?php endif; ?>


                                                        <td title="Productive Hours"><?php echo kpi_hours_display($totalProductionHours);?></td>
                                                        <td title="Project General Hours"><?php echo kpi_hours_display($totalEmpProductionGeneralHours);?></td>
                                                        <td title="General Hours"><?php echo kpi_hours_display($totalEmpGeneralHours);?></td>
                                                        <td title="Total Available Hours"><?php echo kpi_hours_display($totalHours);?></td>
                                                        <td title="Productive Hours%"><?php echo round($productivityPercentage). '%';?></td>
                                                        <td title="General Hours%"><?php echo round($elogicgeneralPercentage). '%';?></td>
                                                        <td title="Utilization%"><?=round($utilizationPercentage). '%';?></td>
                                                        <td title="Availability%"><?php echo round($availabilityPercentage). '%';?></td>
                                                        <td title="Availability Score" style="background-color: #C2E5F0; "><strong><?php 
                                                        if ($availabilityPercentage == 0) {
                                                             $availabilityScore = 0;
                                                             echo $availabilityScore;
                                                        } elseif ($availabilityPercentage >= 90) {
                                                            $availabilityScore = 15;
                                                            echo $availabilityScore;
                                                        } elseif ($availabilityPercentage >= 80 && $availabilityPercentage <= 89) {
                                                            $availabilityScore = 10;
                                                            echo $availabilityScore;
                                                        } else {
                                                            $availabilityScore = 5;
                                                            echo $availabilityScore;
                                                        }
                                                        ?></strong></td>
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
                                                        if ($quality === null || $quality === '' || (float)$quality == 0.0) {
                                                            echo '--';
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
            $ABScore = 15;  // For 10 or more hours
            echo $ABScore;
        } elseif ($hours >= 8) {
            $ABScore = 10;   // For 8-9 hours
            echo $ABScore;
        } else {
            $ABScore = 5;   // For less than 8 hours
            echo $ABScore;
        }
        ?>
    </strong>
</td>
                                                        <td title="Total Score" style="
    background-color: 
        <?php 
            // Sum all the individual scores
            $totalScore = $availabilityScore + $projectGeneralScore + $qualityScore + $processAdherenceScore + $attendanceScore + $lateLoginScore + $ABScore;
            
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
</div><!-- /.kpi-table-scroll-wrapper -->

<script>
(function() {
    var wrapper = document.getElementById('kpiTableScrollWrapper');
    var topInner = document.getElementById('kpiScrollTopInner');
    var spacer = document.getElementById('kpiScrollSpacer');
    var table = document.getElementById('employeeTable');
    if (!wrapper || !topInner || !spacer || !table) return;
    function setSpacerWidth() {
        spacer.style.width = table.scrollWidth + 'px';
    }
    function syncFromWrapper() {
        topInner.scrollLeft = wrapper.scrollLeft;
    }
    function syncFromTop() {
        wrapper.scrollLeft = topInner.scrollLeft;
    }
    setSpacerWidth();
    wrapper.addEventListener('scroll', syncFromWrapper);
    topInner.addEventListener('scroll', syncFromTop);
    if (window.ResizeObserver) {
        new ResizeObserver(setSpacerWidth).observe(table);
    }
    window.addEventListener('resize', setSpacerWidth);
})();
</script>

<style>
th.sortable {
  cursor: pointer;
  user-select: none; /* Prevent selection of the header text */
}

#employeeTable th, #employeeTable td {
  padding: 8px;
  text-align: left;
}

#employeeTable th, #employeeTable td {
  min-width: 100px; /* Adjust this to fit your needs */
}
th.sortable.active-sort {
  box-shadow: inset 0 -8px 0 #C2E5F0;
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

<?php if ($rowCount > 0): ?>
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
        var headingText = dateText + " MEP KPI Report";
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
    #employeeTable td {
        padding: 8px;
        text-align: center;
    }
    /* Header min-widths so full names fit; body cells follow */
    #employeeTable th:nth-child(1), #employeeTable td:nth-child(1) { min-width: 100px; }
    #employeeTable th:nth-child(2), #employeeTable td:nth-child(2) { min-width: 85px; }
    #employeeTable th:nth-child(3), #employeeTable td:nth-child(3) { min-width: 120px; }
    #employeeTable th:nth-child(4), #employeeTable td:nth-child(4) { min-width: 70px; }
    #employeeTable th:nth-child(5), #employeeTable td:nth-child(5) { min-width: 115px; }
    #employeeTable th:nth-child(6), #employeeTable td:nth-child(6) { min-width: 95px; }
    #employeeTable th:nth-child(7), #employeeTable td:nth-child(7) { min-width: 130px; }
    #employeeTable th:nth-child(8), #employeeTable td:nth-child(8) { min-width: 100px; }
    #employeeTable th:nth-child(9), #employeeTable td:nth-child(9) { min-width: 95px; }
    #employeeTable th:nth-child(10), #employeeTable td:nth-child(10) { min-width: 85px; }
    #employeeTable th:nth-child(11), #employeeTable td:nth-child(11) { min-width: 95px; }
    #employeeTable th:nth-child(12), #employeeTable td:nth-child(12) { min-width: 115px; }
    #employeeTable th:nth-child(13), #employeeTable td:nth-child(13) { min-width: 105px; }
    #employeeTable th:nth-child(14), #employeeTable td:nth-child(14) { min-width: 135px; }
    #employeeTable th:nth-child(15), #employeeTable td:nth-child(15) { min-width: 115px; }
    #employeeTable th:nth-child(16), #employeeTable td:nth-child(16) { min-width: 95px; }
    #employeeTable th:nth-child(17), #employeeTable td:nth-child(17) { min-width: 120px; }
    #employeeTable th:nth-child(18), #employeeTable td:nth-child(18) { min-width: 155px; }
    #employeeTable th:nth-child(19), #employeeTable td:nth-child(19) { min-width: 175px; }
    #employeeTable th:nth-child(20), #employeeTable td:nth-child(20) { min-width: 115px; }
    #employeeTable th:nth-child(21), #employeeTable td:nth-child(21) { min-width: 165px; }
    #employeeTable th:nth-child(22), #employeeTable td:nth-child(22) { min-width: 145px; }
    #employeeTable th:nth-child(23), #employeeTable td:nth-child(23) { min-width: 115px; }
    #employeeTable th:nth-child(24), #employeeTable td:nth-child(24) { min-width: 175px; }
    #employeeTable th:nth-child(25), #employeeTable td:nth-child(25) { min-width: 95px; }
    #employeeTable th:nth-child(26), #employeeTable td:nth-child(26) { min-width: 85px; }
</style>        
                    
</div>
                         

<script>   
    
function getMonthWisefilterData_MEP(event) {
    if (event) {
        event.preventDefault();
    }
    var year = document.getElementById('kpi_year') ? document.getElementById('kpi_year').value : '';
    var monthSelect = document.getElementById('kpi_month');
    var months = [];
    if (monthSelect && monthSelect.options) {
        for (var i = 0; i < monthSelect.options.length; i++) {
            if (monthSelect.options[i].selected) {
                months.push(monthSelect.options[i].value);
            }
        }
    }
    var search = document.getElementById('search') ? document.getElementById('search').value.trim() : '';
    if (!year || months.length === 0) {
        alert('Please select Year and at least one Month');
        return false;
    }
    var url = "<?php echo base_url('kpi_reports/getMonthWiseEmpData_mep'); ?>?year=" + encodeURIComponent(year);
    months.forEach(function(m) {
        url += '&month[]=' + encodeURIComponent(m);
    });
    if (search) {
        url += '&search=' + encodeURIComponent(search);
    }
    window.location.href = url;
}

function clearAllFilters_MEP() {
    // Reload with no params – controller shows default (previous month)
    window.location.href = "<?php echo base_url('kpi_reports/getMonthWiseEmpData_mep'); ?>";
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



  <!----------------------------------------------------------------BUTTON FUNCTIONS SCRIPT--------------------------------------------------------------->     


function redirectToConsolidatedReport() {
    // Safely select the Consolidated KPI Report button
    var buttons = document.querySelectorAll('.four-report-btn button');
    var consolidatedBtn = null;

    buttons.forEach(function(btn) {
        if (btn.textContent.trim() === 'PM Wise KPI') {
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
    
    

   <!----------------------------------------------------------------------------monthly kpi css--------------------------------------------------------------->     
<style>
    .activekpi { background-color: #014b88 !important; font-weight: bold; border: 0px solid white; }
    </style>
   
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
