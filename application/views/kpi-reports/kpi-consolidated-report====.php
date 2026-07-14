<head>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400&display=swap" rel="stylesheet"> <!-- Roboto Condensed for numbers -->
</head>

<!-- Include ExcelJS Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>




<style>
    th {
        font-family: 'Oswald', sans-serif;
        font-weight: 700!important; /* Bold weight for headings */
        font-size: 16px!important;    /* Set the font size for headings */
    }

    td {
        font-family: 'Roboto Condensed', sans-serif;  /* Roboto Condensed for numeric values */
        font-weight: 400!important;  /* Normal weight for readability */
        font-size: 15px !important;  /* Set the font size for values */
    }
</style>


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

$getListOfManagers		= $this->timesheet_login->getReportingManagers(null); // List of Clients


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
    <button id="generateBtnConsolidated" onclick="downloadExcelConsolidated()" class="btn btn-primary">
        <span id="btnTextConsolidated">Generate Report</span>
        <span id="spinnerConsolidated" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    </button>
    <?php else: ?>
    <button id="generateBtnConsolidated" class="btn btn-secondary" disabled title="Quality error log data does not match the selected dates">
        <span id="btnTextConsolidated">Generate Report (Disabled)</span>
    </button>
    <?php endif; ?>
</div>

<script>
function downloadExcelConsolidated() {
    const btn = document.getElementById('generateBtnConsolidated');
    const btnText = document.getElementById('btnTextConsolidated');
    const spinner = document.getElementById('spinnerConsolidated');

    // Change button style to show loading
    btn.classList.remove('btn-primary');
    btn.classList.add('btn-success');
    btn.disabled = true;
    spinner.classList.remove('d-none');
    btnText.textContent = 'Downloading...';

    // Use current Select2 selection so Excel matches grid even without re-applying
    var searchVal = $('#search').val();
    const search = (searchVal && (Array.isArray(searchVal) ? searchVal.length : 1)) ? (Array.isArray(searchVal) ? searchVal.join(', ') : searchVal) : '<?php echo isset($search) ? addslashes($search) : ''; ?>';
    // Department: __all__ = All departments (don't send); otherwise send selected depts
    let department = '';
    const deptVal = $('#department').val();
    if (deptVal && (Array.isArray(deptVal) ? deptVal.length : 1)) {
        var arr = Array.isArray(deptVal) ? deptVal.filter(function(v){ return v && v !== '' && v !== '__all__'; }) : (deptVal === '__all__' ? [] : [deptVal]);
        if (arr.length) department = arr.join(',');
    }
    const fromDate = document.getElementById('from_date') ? document.getElementById('from_date').value : '';
    const toDate = document.getElementById('to_date') ? document.getElementById('to_date').value : '';

    let url = '<?php echo base_url('kpi_reports/generateConsolidatedReportExcel'); ?>?';
    const params = [];
    
    if (search) {
        params.push('search=' + encodeURIComponent(search));
    }
    if (department) {
        params.push('department=' + encodeURIComponent(department));
    }
    if (fromDate) {
        params.push('from_date=' + encodeURIComponent(fromDate));
    }
    if (toDate) {
        params.push('to_date=' + encodeURIComponent(toDate));
    }
    
    if (params.length > 0) {
        url += params.join('&');
    } else {
        url = url.replace('?', '');
    }

    // Allow spinner to be visible before redirect
    setTimeout(() => {
        window.location.href = url;

        // Optional: Reset UI elements after some time (for recovery if needed)
        setTimeout(() => {
            spinner.classList.add('d-none');
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
            btn.disabled = false;
            btnText.textContent = 'Generate Report';
        }, 30000); // Adjust duration if needed
    }, 300); // Slight delay to show spinner before the URL change
}
</script>

      
      


      
  </div>

  <!------------------------------------------------------------------------------CARD 1------------------------------------------------------------------>
    <div class="card">
		<h3 class="card-title"></h3>
		<div class="card-body">

  <!------------------------------------------------------------------------------BUTTONS------------------------------------------------------------------>             
<div class="four-report-btn" style="margin-left: 9px;">
    <button onclick="redirectToMonthlyReport()" class="btn btn-primary" >
        Monthly KPI Report
    </button>

    <button onclick="redirectToConsolidatedReport()" class="btn btn-primary" style="background-color: #014b88; font-weight: bold; border: 2px solid white;">
        Consolidated KPI Report
    </button>

</div>

   <!------------------------------------------------------------------------------HEADINGS------------------------------------------------------------------>                                 
<div class="row mt-4">
                        <div class="col-md-12">
                            <h3>Consolidated KPI Report</h3>

                        </div>
                    </div>          
                  

   <!------------------------------------------------------------------------------SEARCH------------------------------------------------------------------>      
            
<?php if (in_array($this->session->userdata['logged_in_timesheet']['user_type'], ['admin', 'business_head', 'manager'])): ?>            
<form method="get" action="<?= base_url('kpi_reports/consolidatedReport') ?>" id="dateRangeForm">
    <div class="row kpi-consolidated-filter-card" style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <!-- Search Box (Above Date Range) -->
        <div class="col-md-12" style="margin-bottom: 15px;">
            <div class="form-group kpi-search-row" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <label class="control-label" for="search" style="margin-right: 10px; min-width: 90px; font-weight: 600; color: #014b88; font-size: 14px;">Search:</label>
                <div class="kpi-search-wrap" style="max-width: 320px; width: 100%;">
                    <select name="search[]" id="search" class="form-control kpi-search-select" multiple="multiple"
                            style="background-color: #fff; border: 1px solid #d1d9e0; color: #014b88; font-weight: 500; width: 100%; font-size: 13px; border-radius: 8px;">
                        <?php
                        // Case-insensitive dedupe: one option per person (first occurrence is canonical)
                        $searchOptionValuesLower = [];
                        $addOption = function($name) use (&$searchOptionValuesLower) {
                            $name = trim($name);
                            if ($name === '') return;
                            $key = mb_strtolower($name);
                            if (isset($searchOptionValuesLower[$key])) return;
                            $searchOptionValuesLower[$key] = $name;
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
            </div>
        </div>
        
        <!-- Department Selection (Below Search, Above Date Range) -->
        <div class="col-md-12" style="margin-bottom: 15px;">
            <div class="form-group" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <label class="control-label" for="department" style="margin-right: 10px; min-width: 90px; font-weight: 600; color: #014b88; font-size: 14px;">Department:</label>
                <?php
                    // Image-wise: "All departments" as one selectable tag (value __all__); or specific depts
                    $allDepts = ['MEP', 'Architectural', 'Structural', '3D Visualization'];
                    $showAllDepartmentsTag = true; // default: show single "All departments" tag
                    $selectedDepartments = [];
                    if (!empty($department)) {
                        if (is_array($department)) {
                            $selectedDepartments = array_values(array_intersect($allDepts, array_filter($department)));
                        } else {
                            $selectedDepartments = array_values(array_intersect($allDepts, array_filter(array_map('trim', explode(',', $department)))));
                        }
                        if (!empty($selectedDepartments)) $showAllDepartmentsTag = false;
                    }
                ?>
                <select name="department[]" id="department" class="form-control kpi-dept-select" multiple="multiple"
                        style="background-color: #fff; border: 1px solid #d1d9e0; color: #014b88; font-weight: 500; width: 240px; font-size: 13px; border-radius: 8px;">
                    <option value="__all__" <?= $showAllDepartmentsTag ? 'selected' : '' ?>>All departments</option>
                    <option value="MEP" <?= in_array('MEP', $selectedDepartments) ? 'selected' : '' ?>>MEP</option>
                    <option value="Architectural" <?= in_array('Architectural', $selectedDepartments) ? 'selected' : '' ?>>Architectural</option>
                    <option value="Structural" <?= in_array('Structural', $selectedDepartments) ? 'selected' : '' ?>>Structural</option>
                    <option value="3D Visualization" <?= in_array('3D Visualization', $selectedDepartments) ? 'selected' : '' ?>>3D Visualization</option>
                </select>
            </div>
        </div>
        
        <!-- Date Range Selection (Below Department) -->
        <div class="col-md-12">
            <div class="form-group" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <label class="control-label" style="margin-right: 10px; min-width: 90px; font-weight: 600; color: #014b88; font-size: 14px;">Date Range:</label>
                
                <div style="position: relative; display: inline-block;">
                    <input type="text" name="from_date" id="from_date" class="form-control date-highlight" 
                           value="<?= isset($from_date) && !empty($from_date) ? htmlspecialchars($from_date) : date('Y-m-01') ?>" 
                           placeholder="From Date" readonly=""
                           style="background-color: #fff9e6; border: 2px solid #bdbdbd; width: 180px; font-weight: bold; color: #014b88; box-shadow: 0 0 5px rgba(255, 158, 79, 0.5); padding-right: 35px;">
                    <i class="fa fa-calendar" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #014b88; pointer-events: none; font-size: 16px;"></i>
                </div>
                
                <label class="control-label" style="margin-right: 10px; font-weight: bold; color: #014b88; white-space: nowrap;">To:</label>
                
                <div style="position: relative; display: inline-block;">
                    <input type="text" name="to_date" id="to_date" class="form-control date-highlight" 
                           value="<?= isset($to_date) && !empty($to_date) ? htmlspecialchars($to_date) : date('Y-m-t') ?>" 
                           placeholder="To Date" readonly=""
                           style="background-color: #fff9e6; border: 2px solid #bdbdbd; width: 180px; font-weight: bold; color: #014b88; box-shadow: 0 0 5px rgba(255, 158, 79, 0.5); padding-right: 35px;">
                    <i class="fa fa-calendar" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #014b88; pointer-events: none; font-size: 16px;"></i>
                </div>

                <button type="submit" class="btn btn-info" style="background-color: #014b88; color: white; font-weight: bold; padding: 8px 20px; white-space: nowrap; margin-right: 10px;">
                    <i class="fa fa-filter"></i> Search 
                </button>
                <a href="<?php echo base_url('kpi_reports/consolidatedReport'); ?>">
                <button type="button" class="btn " style="background-color: #ea580c; color: white; font-weight: bold; padding: 8px 20px; white-space: nowrap;">
                    <i class="fa fa-refresh"></i>  Clear All Filters
                        
                </button></a>
            </div>
        </div>
    </div>
</form>
<?php endif; ?>
            
            
<script>
    function removeFiltersAndReload() {
        // Clear all filters and reload page
        var url = new URL(window.location.href);
        
        // Remove all filter parameters
        url.searchParams.delete('search');
        url.searchParams.delete('department');
        url.searchParams.delete('from_date');
        url.searchParams.delete('to_date');
        
        // Reload the page with the updated URL
        window.location.href = url.toString();
    }
</script>
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
            url: "<?php echo base_url('kpi_reports/autosuggest_employee_names_cons'); ?>",
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

<script>
    // Initialize jQuery UI Datepicker with month and year dropdowns
    $(document).ready(function() {
        var currentYear = new Date().getFullYear();
        var startYear = 2010;
        var today = $("#from_date").val();
        
        $("#from_date, #to_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: startYear + ':' + currentYear,
            minDate: new Date(startYear, 0, 1), // January 1, 2010
            maxDate: new Date(), // Current date
            numberOfMonths: 1,
            beforeShow: function(input, inst) {
                // Add visible arrows when datepicker is shown
                setTimeout(function() {
                    // Remove any existing arrow-content to prevent duplicates
                    $('.ui-datepicker-prev .ui-icon .arrow-content').remove();
                    $('.ui-datepicker-next .ui-icon .arrow-content').remove();
                    
                    // Set Prev arrow
                    $('.ui-datepicker-prev .ui-icon').each(function() {
                        $(this).html('<span class="arrow-content">Prev</span>');
                        $(this).css({
                            'background-image': 'none',
                            'background': 'none',
                            'text-indent': '0'
                        });
                    });
                    
                    // Set Next arrow
                    $('.ui-datepicker-next .ui-icon').each(function() {
                        $(this).html('<span class="arrow-content">Next</span>');
                        $(this).css({
                            'background-image': 'none',
                            'background': 'none',
                            'text-indent': '0'
                        });
                    });
                }, 10);
            },
            onSelect: function(selectedDate) {
                if (this.id == 'from_date') {
                    var dateMin = $('#from_date').datepicker("getDate");
                    var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(), dateMin.getDate());
                    var rMax = new Date(); // Current date
                    $('#to_date').datepicker("option", "minDate", rMin);
                    $('#to_date').datepicker("option", "maxDate", rMax);
                }
            }
        });
        
        // Function to update arrows - call it whenever datepicker is shown
        function updateDatepickerArrows() {
            setTimeout(function() {
                // Remove all existing arrow content
                $('.ui-datepicker-prev .ui-icon').each(function() {
                    $(this).empty().html('<span class="arrow-content">Prev</span>');
                    $(this).css({
                        'background-image': 'none',
                        'background': 'none',
                        'text-indent': '0'
                    });
                });
                
                $('.ui-datepicker-next .ui-icon').each(function() {
                    $(this).empty().html('<span class="arrow-content">Next</span>');
                    $(this).css({
                        'background-image': 'none',
                        'background': 'none',
                        'text-indent': '0'
                    });
                });
            }, 50);
        }
        
        // Update arrows when datepicker is shown
        $(document).on('focus', '#from_date, #to_date', function() {
            updateDatepickerArrows();
        });
        
        // Also update on month/year change
        $(document).on('change', '.ui-datepicker-month, .ui-datepicker-year', function() {
            updateDatepickerArrows();
        });
        
        // Set initial minDate for to_date if from_date has a value
        if (today) {
            $('#to_date').datepicker("option", "minDate", new Date(today));
        } else {
            $('#to_date').datepicker("option", "minDate", new Date(startYear, 0, 1));
        }
        
    });
</script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

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

/* Consolidated report – Search box: compact and clean */
.kpi-search-wrap .select2-container { max-width: 100% !important; }
.kpi-search-wrap .select2-container--default .select2-selection--multiple {
    min-height: 36px !important;
    padding: 4px 8px !important;
    border: 1px solid #d1d9e0 !important;
    border-radius: 8px !important;
    background-color: #fff !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.kpi-search-wrap .select2-container--default.select2-container--focus .select2-selection--multiple,
.kpi-search-wrap .select2-container--default.select2-container--open .select2-selection--multiple {
    border-color: #014b88 !important;
    box-shadow: 0 0 0 3px rgba(1, 75, 136, 0.12) !important;
}
.kpi-search-wrap .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background: linear-gradient(135deg, #014b88 0%, #0366a3 100%) !important;
    border: none !important;
    color: #fff !important;
    padding: 2px 6px 2px 8px !important;
    border-radius: 6px !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    line-height: 1.4 !important;
}
.kpi-search-wrap .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: rgba(255,255,255,0.9) !important;
    margin-right: 4px !important;
    font-size: 14px !important;
}
.kpi-search-wrap .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #fff !important;
}
.kpi-search-wrap .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
    color: #6c757d !important;
    font-size: 13px !important;
    margin-left: 2px !important;
}
#search.kpi-search-select {
    height: 36px;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 13px;
}
#search.kpi-search-select:focus {
    outline: none;
    border-color: #014b88;
    box-shadow: 0 0 0 3px rgba(1, 75, 136, 0.12);
}

/* Department dropdown - clear select appearance with arrow */
.select2-container--default .select2-selection--single {
    height: 38px !important;
    padding: 6px 28px 6px 12px !important;
    border: 2px solid #bdbdbd !important;
    border-radius: 6px !important;
    background-color: #fff !important;
    color: #014b88 !important;
    font-weight: bold !important;
    font-size: 14px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
    right: 8px !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 24px !important;
    padding-left: 0 !important;
}
.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #6c757d !important;
}
.select2-dropdown {
    border: 2px solid #014b88 !important;
    border-radius: 6px !important;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #014b88 !important;
    color: #fff !important;
}

/* Department multi-select – compact, matches search style */
.kpi-dept-select + .select2-container .select2-selection--multiple,
#department + .select2-container .select2-selection--multiple {
    min-height: 36px !important;
    padding: 4px 8px !important;
    border: 1px solid #d1d9e0 !important;
    border-radius: 8px !important;
    background-color: #fff !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
}
#department + .select2-container .select2-selection--multiple .select2-selection__choice {
    background: linear-gradient(135deg, #5a4d7a 0%, #6f42c1 100%) !important;
    border: none !important;
    color: #fff !important;
    padding: 2px 6px 2px 8px !important;
    border-radius: 6px !important;
    font-size: 12px !important;
    font-weight: 500 !important;
}
#department + .select2-container .select2-selection--multiple .select2-selection__choice__remove {
    color: rgba(255,255,255,0.9) !important;
    margin-right: 4px !important;
}
#department + .select2-container .select2-selection--multiple .select2-selection__placeholder {
    color: #6c757d !important;
    font-size: 13px !important;
}
</style>

   <!------------------------------------------------------------------------------------------------------------------------------------------------>  

 

            
            
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

                    

  <!------------------------------------------------------------------------------CONSOLIDATED HEADINGS------------------------------------------------------------------>                          
   
   <div class="row mt-4">
                        <div class="col-md-12">
                           

<?php
// Use data dates for display (defaults to January-current month if form dates are empty)
$display_from_date = isset($data_from_date) ? $data_from_date : (isset($from_date) && !empty($from_date) ? $from_date : '');
$display_to_date = isset($data_to_date) ? $data_to_date : (isset($to_date) && !empty($to_date) ? $to_date : '');

if (!empty($display_from_date) && !empty($display_to_date)) {
    $fromDateFormatted = date('F Y', strtotime($display_from_date));
    $toDateFormatted = date('F Y', strtotime($display_to_date));
    $headingText = $fromDateFormatted . ' - ' . $toDateFormatted;
} else {
    $currentMonth = date('n');  
    $currentMonthName = date('F', mktime(0, 0, 0, $currentMonth, 1)); // Get current month name
    $headingText = 'January - ' . $currentMonthName;
}

// Add search name if available
if (!empty($search)) {
    $headingText .= ' ( ' . htmlspecialchars($search) . ' )';
}

echo '<h3>' . $headingText . '</h3>';
?>

                        </div>
                    </div>     

   <!------------------------------------------------------------------------------DEPT SUMMARY TABLE--------------------------------------------------------->               
<?php if (isset($datesMatch) && $datesMatch): ?>
<?php
// Calculate which months to include based on date range or default behavior (needed for department summary)
// Use data dates for calculations (defaults to January-current month if form dates are empty)
$calc_from_date = isset($data_from_date) ? $data_from_date : (isset($from_date) && !empty($from_date) ? $from_date : '');
$calc_to_date = isset($data_to_date) ? $data_to_date : (isset($to_date) && !empty($to_date) ? $to_date : '');

// Build list of (month, year) pairs so department summary uses correct year for each month (fixes MEP showing 0%)
$monthYearPairs = [];
if (!empty($calc_from_date) && !empty($calc_to_date)) {
    $startDate = new DateTime($calc_from_date);
    $endDate = new DateTime($calc_to_date);
    $currentDate = clone $startDate;
    $currentDate->modify('first day of this month');
    while ($currentDate <= $endDate) {
        $monthYearPairs[] = ['month' => (int)$currentDate->format('n'), 'year' => (int)$currentDate->format('Y')];
        $currentDate->modify('+1 month');
    }
} else {
    $currentMonth = (int) date('n');
    $currentYear = (int) date('Y');
    for ($m = 1; $m <= $currentMonth; $m++) {
        $monthYearPairs[] = ['month' => $m, 'year' => $currentYear];
    }
}
$monthNames = array_map(function($p) { return $p['month']; }, $monthYearPairs);
$monthNames = array_map('intval', array_filter($monthNames, 'is_numeric'));

$preload = isset($preload) ? $preload : [];
$managerNamesById = isset($managerNamesById) ? $managerNamesById : [];

// Calculate department averages (similar to Excel export logic)
// Get session data for department filtering
$sessionData = $this->session->userdata('logged_in_timesheet');
$userType = isset($sessionData['user_type']) ? strtolower($sessionData['user_type']) : '';
$empId = isset($sessionData['empId']) ? $sessionData['empId'] : '';

// Define manager IDs and departments
$mepManagers = ['146', '230', '149', '455'];
$arcManagers = ['41', '394', '270', '47', '182', '71', '53', '155'];
// Consolidated report: always show both departments (MEP and Architecture) for department-wise summary
$departmentsToShow = ['MEP', 'Architecture'];

// Initialize department stats
$departmentStats = [
    'MEP' => [
        'productivity' => 0, 'projectGeneral' => 0, 'elogicGeneral' => 0,
        'availability' => 0, 'utilization' => 0, 'count' => 0
    ],
    'Architecture' => [
        'productivity' => 0, 'projectGeneral' => 0, 'elogicGeneral' => 0,
        'availability' => 0, 'utilization' => 0, 'count' => 0
    ]
];

// Calculate averages per employee per month (same logic as Excel)
if (!empty($getkpiReports)) {
    foreach ($getkpiReports as $kpiResult) {
        // Apply same filters
        if (in_array($kpiResult->department, ['IT', 'HR', 'Software', 'Operations Manager', NULL]) || empty($kpiResult->reporting_manger)) {
            continue;
        }
        
        // Group departments
        $dept = $kpiResult->department;
        if (in_array($dept, ['Architectural', 'Structural', '3D Visualization'])) {
            $dept = 'Architecture';
        } elseif ($dept !== 'MEP') {
            continue;
        }
        
        // Initialize totals for this employee
        $totals = [
            'productivity' => 0, 'projectGeneral' => 0, 'elogicGeneral' => 0,
            'availability' => 0, 'utilization' => 0, 'validMonths' => 0
        ];
        
        // Calculate monthly values (use preloaded batch data when available for speed)
        foreach ($monthYearPairs as $monthYear) {
            $month = (int) $monthYear['month'];
            $yearForMonth = (int) $monthYear['year'];
            $monthlyData = isset($preload['production'][$kpiResult->empId][$month][$yearForMonth]) ? $preload['production'][$kpiResult->empId][$month][$yearForMonth] : $this->kpi_reports_model->empProductionHoursMonthWiseCons($kpiResult->empId, $month, $yearForMonth);
            $productionHoursArray = is_string($monthlyData) ? explode('@#===', $monthlyData) : ['0', '0', '0'];
            
            $monthlyProduction = isset($productionHoursArray[0]) ? (float)$productionHoursArray[0] : 0;
            $monthlyProjectGeneral = isset($productionHoursArray[1]) ? (float)$productionHoursArray[1] : 0;
            $monthlyElogicGeneral = isset($productionHoursArray[2]) ? (float)$productionHoursArray[2] : 0;
            $monthlyTotalHours = $monthlyProduction + $monthlyProjectGeneral + $monthlyElogicGeneral;
            
            // Skip invalid months
            $monthWorkingHours = [
                1 => 178.5, 2 => 170.0, 3 => 161.5, 4 => 187.0, 5 => 178.5, 
                6 => 178.5, 7 => 195.5, 8 => 170.0, 9 => 187.0, 10 => 170.0, 
                11 => 170.0, 12 => 187.0
            ];
            $workHrs = isset($monthWorkingHours[$month]) ? $monthWorkingHours[$month] : 0;
            
            if ($workHrs <= 0 || $monthlyTotalHours <= 0) continue;
            
            // Calculate percentages
            $totals['productivity'] += ($monthlyProduction / $monthlyTotalHours) * 100;
            $totals['projectGeneral'] += ($monthlyProjectGeneral / $monthlyTotalHours) * 100;
            $totals['elogicGeneral'] += ($monthlyElogicGeneral / $monthlyTotalHours) * 100;
            $totals['availability'] += ($monthlyTotalHours / $workHrs) * 100;
            $totals['utilization'] += (($monthlyProduction + $monthlyProjectGeneral) / $monthlyTotalHours) * 100;
            $totals['validMonths']++;
        }
        
        // Calculate average availability percentage
        $avgAvailabilityPercentage = ($totals['validMonths'] > 0) ? ($totals['availability'] / $totals['validMonths']) : 0;
        
        // Exclude if avgAvailabilityPercentage is 0
        if (round($avgAvailabilityPercentage) == 0) {
            continue;
        }
        
        // Add to department totals if valid data exists
        if ($totals['validMonths'] > 0) {
            $departmentStats[$dept]['productivity'] += $totals['productivity'] / $totals['validMonths'];
            $departmentStats[$dept]['projectGeneral'] += $totals['projectGeneral'] / $totals['validMonths'];
            $departmentStats[$dept]['elogicGeneral'] += $totals['elogicGeneral'] / $totals['validMonths'];
            $departmentStats[$dept]['availability'] += $totals['availability'] / $totals['validMonths'];
            $departmentStats[$dept]['utilization'] += $totals['utilization'] / $totals['validMonths'];
            $departmentStats[$dept]['count']++;
        }
    }
}
?>
<div class="row mt-4">
    <div class="col-md-12" style="display: flex; justify-content: center;">
        <table class="table table-bordered" style="width: 80%; max-width: 900px; border-collapse: collapse; margin: 0 auto;">
            <thead>
                <tr style="background-color: #014b88;">
                    <th style="text-align: center; font-weight: bold; color: white; padding: 12px;">Departments</th>
                    <th style="text-align: center; font-weight: bold; color: white; padding: 12px;">Productivity%</th>
                    <th style="text-align: center; font-weight: bold; color: white; padding: 12px;">Project General%</th>
                    <th style="text-align: center; font-weight: bold; color: white; padding: 12px;">eLogic General%</th>
                    <th style="text-align: center; font-weight: bold; color: white; padding: 12px;">Availability%</th>
                    <th style="text-align: center; font-weight: bold; color: white; padding: 12px;">Utilization%</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departmentsToShow as $dept): ?>
                    <?php
                    if ($departmentStats[$dept]['count'] > 0) {
                        $avgProductivity = $departmentStats[$dept]['productivity'] / $departmentStats[$dept]['count'];
                        $avgProjectGeneral = $departmentStats[$dept]['projectGeneral'] / $departmentStats[$dept]['count'];
                        $avgElogicGeneral = $departmentStats[$dept]['elogicGeneral'] / $departmentStats[$dept]['count'];
                        $avgAvailability = $departmentStats[$dept]['availability'] / $departmentStats[$dept]['count'];
                        $avgUtilization = $departmentStats[$dept]['utilization'] / $departmentStats[$dept]['count'];
                    } else {
                        $avgProductivity = $avgProjectGeneral = $avgElogicGeneral = $avgAvailability = $avgUtilization = 0;
                    }
                    ?>
                    <tr>
                        <td style="text-align: center; font-weight: bold; background-color: #ffffff; padding: 12px;"><?php echo $dept; ?></td>
                        <td style="text-align: center; background-color: #d4edda; padding: 12px; font-weight: bold;"><?php echo round($avgProductivity); ?>%</td>
                        <td style="text-align: center; background-color: #fff3cd; padding: 12px; font-weight: bold;"><?php echo round($avgProjectGeneral); ?>%</td>
                        <td style="text-align: center; background-color: #ffd7a3; padding: 12px; font-weight: bold;"><?php echo round($avgElogicGeneral); ?>%</td>
                        <td style="text-align: center; background-color: #d1ecf1; padding: 12px; font-weight: bold;"><?php echo round($avgAvailability); ?>%</td>
                        <td style="text-align: center; background-color: #e2d5f3; padding: 12px; font-weight: bold;"><?php echo round($avgUtilization); ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

   <!------------------------------------------------------------------------------REPORT TABLE------------------------------------------------------------------>
<?php if (isset($datesMatch) && !$datesMatch): ?>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="alert alert-warning" role="alert">
            <strong>Warning:</strong> Quality error log data does not match the selected dates. Grid view and Excel download are disabled.
        </div>
    </div>
</div>
<?php endif; ?>
<?php if (isset($datesMatch) && $datesMatch): ?>
                 
<table id="employeeTable" style ="margin-left: -22px; " class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    
    <th class="sortable" data-column="0" title="Department">Department</th>                                                
    <th class="sortable" data-column="1" title="Reporting Manager">Reporting Manager</th>
    <th class="sortable" data-column="2" title="Employee ID">Employee ID</th>
    <th class="sortable" data-column="3" title="Employee Name">Employee Name</th>
    <th class="sortable" data-column="4" title="Avg Productivity %">Avg Productivity %</th>
    <th class="sortable" data-column="5" title="Avg Project General %">Avg Project General %</th>
    <th class="sortable" data-column="6" title="Avg eLogic General %">Avg eLogic General %</th>
    <th class="sortable" data-column="7" title="Avg Availability %">Avg Availability %</th>
    <th class="sortable" data-column="8" title="Avg Utilization %">Avg Utilization %</th>
    <th class="sortable" data-column="9" title="Avg Quality Accuracy %">Avg Quality Accuracy %</th>
    <th class="sortable" data-column="10" title="Total Process Adherence">Total Process Adherence</th>
    <th class="sortable" data-column="11" title="Total UPL and Attend not updated">Total UPL and Attend not updated</th>
    <th class="sortable" data-column="12" title="Total No of Late and Early Login">Total No of Late and Early Login</th>
    <th class="sortable" data-column="13" title="Above and Beyond">Above & Beyond</th>
</tr>

<style>
    th {
        text-align: center;         /* Center horizontally */
        vertical-align: middle !important;     /* Center vertically */
        padding: 10px;              /* Add some padding for better spacing */
    }
</style>

                                            </thead>

                                            <tbody>
                                           
 
<?php 

// Initialize row counter to track if any data rows were rendered
$rowCount = 0;

// $monthNames is already calculated above in the department summary section
// Get full month names for headers
$fullMonthNames = [
    '1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April',
    '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August',
    '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
];

// Sort (month, year) descending for main table (newest first)
$monthYearPairsDesc = $monthYearPairs;
usort($monthYearPairsDesc, function($a, $b) {
    if ($a['year'] != $b['year']) return $b['year'] - $a['year'];
    return $b['month'] - $a['month'];
});
rsort($monthNames);

// Check if getkpiReports has data
if (!empty($getkpiReports)):
    // Group by employee - loop through employees first
    foreach ($getkpiReports as $kpiResult):
        // Apply filters first
        if (in_array($kpiResult->department, ['IT', 'HR', 'Software','Operations Manager',NULL]) || empty($kpiResult->reporting_manger)) {
            continue;
        }
        
        $getTeamwiseMangerName = isset($managerNamesById[$kpiResult->reporting_manger]) ? $managerNamesById[$kpiResult->reporting_manger] : $this->resourcelog_model->getManagerName($kpiResult->reporting_manger);
        $firstName = $getTeamwiseMangerName ? strtok($getTeamwiseMangerName, ' ') : '';  // Extracts the first word (i.e., first name)
        if ($firstName === false) $firstName = '';
        
        // Initialize totals for this employee across all months
        $totals = [
            'productivity' => 0,
            'projectGeneral' => 0,
            'elogicGeneral' => 0,
            'availability' => 0,
            'utilization' => 0,
            'quality' => 0,
            'instanceData' => 0,
            'perkEmpAbsentDays' => 0,
            'lateloginandEarlyout' => 0,
            'timeSpent' => 0,
            'validMonths' => 0
        ];
        
        // Loop through all months (with year) and aggregate data - pass year so MEP data shows
        foreach ($monthYearPairsDesc as $monthYear):
            $month = (int) $monthYear['month'];
            $yearForMonth = (int) $monthYear['year'];
            
            // Get data for this month (use preloaded batch when available for speed)
            $getTotalProductionH = isset($preload['production'][$kpiResult->empId][$month][$yearForMonth]) ? $preload['production'][$kpiResult->empId][$month][$yearForMonth] : $this->kpi_reports_model->empProductionHoursMonthWiseCons($kpiResult->empId, $month, $yearForMonth);
            $productionHoursArray = is_string($getTotalProductionH) ? explode('@#===', $getTotalProductionH) : ['0', '0', '0'];
            $monthlyabsentdata = isset($preload['perk'][$kpiResult->emp_com_id][$month][$yearForMonth]) ? $preload['perk'][$kpiResult->emp_com_id][$month][$yearForMonth] : $this->kpi_reports_model->perkabsent($kpiResult->emp_com_id, $month, $yearForMonth);
            $perkArray = explode('Perk@#', $monthlyabsentdata);
            
            $monthlyProduction = isset($productionHoursArray[0]) ? $productionHoursArray[0] : 0;
            $monthlyProjectGeneral = isset($productionHoursArray[1]) ? $productionHoursArray[1] : 0;
            $monthlyElogicGeneral = isset($productionHoursArray[2]) ? $productionHoursArray[2] : 0;
            $monthlyperkEmpAbsentDays = !empty($perkArray[0]) ? (int)$perkArray[0] : 0;
            $monthlylateLogin = !empty($perkArray[1]) ? (int)$perkArray[1] : 0;
            $monthlyearlyOut = !empty($perkArray[2]) ? (int)$perkArray[2] : 0;
            // Same as monthly report: 3 allowance per month
            $monthlyLateloginandEarlyout = max($monthlylateLogin + $monthlyearlyOut - 3, 0);
            
            $monthlyTotalHours = $monthlyProduction + $monthlyProjectGeneral + $monthlyElogicGeneral;
            
            // Skip if no hours for this month
            if ($monthlyTotalHours <= 0) {
                continue;
            }
            
            // Set month-wise working hours
            $monthWorkingHours = [
                1 => 178.5, 2 => 170.0, 3 => 161.5, 4 => 187.0, 5 => 178.5,
                6 => 178.5, 7 => 195.5, 8 => 170.0, 9 => 187.0, 10 => 170.0,
                11 => 170.0, 12 => 187.0
            ];
            $workHrs = isset($monthWorkingHours[$month]) ? $monthWorkingHours[$month] : 0;
            
            if ($workHrs <= 0) {
                continue;
            }
            
            // Calculate monthly percentages
            $monthlyProductivityPercentage = ($monthlyProduction / $monthlyTotalHours) * 100;
            $monthlyProjectGeneralPercentage = ($monthlyProjectGeneral / $monthlyTotalHours) * 100;
            $monthlyElogicGeneralPercentage = ($monthlyElogicGeneral / $monthlyTotalHours) * 100;
            $monthlyAvailability = ($monthlyTotalHours / $workHrs) * 100;
            $monthlyUtilization = (($monthlyProduction + $monthlyProjectGeneral) / $monthlyTotalHours) * 100;
            
            // Get other monthly data (use preloaded batch when available for speed)
            $monthlyTimeSpent = isset($preload['lms'][$kpiResult->empId][$month][$yearForMonth]) ? $preload['lms'][$kpiResult->empId][$month][$yearForMonth] : $this->kpi_reports_model->LMShours($kpiResult->empId, $month, $yearForMonth);
            $monthlyinstanceData = isset($preload['defaulter'][$kpiResult->empId][$month][$yearForMonth]) ? $preload['defaulter'][$kpiResult->empId][$month][$yearForMonth] : $this->kpi_reports_model->timesheetDefaulter($kpiResult->empId, $month, $yearForMonth);
            $monthlyQuality = isset($preload['quality'][$kpiResult->empId][$month][$yearForMonth]) ? $preload['quality'][$kpiResult->empId][$month][$yearForMonth] : $this->kpi_reports_model->qualityyearlylog($kpiResult->empId, $month);
            
            // Accumulate totals
            $totals['productivity'] += $monthlyProductivityPercentage;
            $totals['projectGeneral'] += $monthlyProjectGeneralPercentage;
            $totals['elogicGeneral'] += $monthlyElogicGeneralPercentage;
            $totals['availability'] += $monthlyAvailability;
            $totals['utilization'] += $monthlyUtilization;
            $totals['quality'] += $monthlyQuality;
            $totals['instanceData'] += $monthlyinstanceData;
            $totals['perkEmpAbsentDays'] += $monthlyperkEmpAbsentDays;
            $totals['lateloginandEarlyout'] += $monthlyLateloginandEarlyout;
            $totals['timeSpent'] += $monthlyTimeSpent;
            $totals['validMonths']++;
        endforeach;
        
        // Only display if employee has valid data
        if ($totals['validMonths'] > 0):
            // Calculate averages
            $avgProductivity = $totals['productivity'] / $totals['validMonths'];
            $avgProjectGeneral = $totals['projectGeneral'] / $totals['validMonths'];
            $avgElogicGeneral = $totals['elogicGeneral'] / $totals['validMonths'];
            $avgAvailability = $totals['availability'] / $totals['validMonths'];
            $avgUtilization = $totals['utilization'] / $totals['validMonths'];
            $avgQuality = $totals['quality'] / $totals['validMonths'];
            
            // Totals (sum across all months)
            $totalInstanceData = $totals['instanceData'];
            $totalPerkEmpAbsentDays = $totals['perkEmpAbsentDays'];
            $totalLateloginandEarlyout = $totals['lateloginandEarlyout'];
            
            // Format aboveBeyondHours (total time spent)
            $totalTimeSpent = $totals['timeSpent'];
            if (!empty($totalTimeSpent)) {
                $hours = floor($totalTimeSpent / 3600);
                $minutes = floor(($totalTimeSpent % 3600) / 60);
                $aboveBeyondHours = $hours . '.' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
            } else {
                $aboveBeyondHours = '0';
            }
            
            $rowCount++; // Increment row counter when a row is rendered
            ?>
            <tr data-department="<?php echo $kpiResult->department; ?>" 
                data-manager="<?php echo $kpiResult->reporting_manger; ?>" 
                data-employee="<?php echo $kpiResult->empId; ?>" >

                <td><strong><?php echo $kpiResult->department; ?></strong></td>
                <td><strong><?php echo $firstName; ?></strong></td>
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
                <td><?php echo round($avgProductivity). '%'; ?></td>
                <td><?php echo round($avgProjectGeneral). '%'; ?></td>
                <td><?php echo round($avgElogicGeneral). '%';?></td>
                <td><?php echo round($avgAvailability). '%'; ?></td>
                <td><?php echo round($avgUtilization). '%';?></td>
                <td><?php echo round($avgQuality). '%';?></td>
                <td><?php echo round($totalInstanceData); ?></td>
                <td><?php echo round($totalPerkEmpAbsentDays); ?></td>
                <td><?php echo round($totalLateloginandEarlyout); ?></td>
                <td><?php echo $aboveBeyondHours; ?></td>
            </tr>
            <?php
        endif;
    endforeach; // End employee loop 
else:
    // No records found from database query
    $rowCount = 0;
endif;
?>
                                            </tbody>
                                        </table>
<?php endif; ?>

  
<style>
th.sortable {
  cursor: pointer;
  user-select: none; /* Prevent selection of the header text */
}

th, td {
  padding: 8px;
  text-align: left;
}

th, td {
  min-width: 100px; /* Adjust this to fit your needs */
}
th.sortable.active-sort {
  box-shadow: inset 0 -8px 0 #C2E5F0;
}

</style>

     
                    
<?php if ($rowCount > 0): ?>
<div class="pagination-container">
    <?php echo $pagination_links; ?>
</div>
<?php else: ?>
<div class="no-data-message" style="text-align: center; padding: 40px 20px; margin-top: 20px;">
    <p style="font-size: 18px; color: #666; font-weight: bold;">No data found for the selected date range.</p>
    <p style="font-size: 14px; color: #999; margin-top: 10px;">Please try selecting a different date range or search criteria.</p>
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
                    
                           
            
 <!----------------------------------------------------------------------------END OF TABLES--------------------------------------------------------------->       
                </div>
                <!-- End of Page Content -->
        </div>
      </div>
    </div>
  </div>
</div>

    <!------------------------------------------------------------------------------REPORT TABLE----------------------------------------------------------------> 
     
 
  <!----------------------------------------------------------------------------BUTTON FUNCTIONS SCRIPT--------------------------------------------------------------->      
        
<?php
// DEBUG: Print contents to verify
// Remove this once verified


// Get department of the logged-in employee (or the first item as fallback)
$empDepartment = '';
if (!empty($getkpiReports)) {
    foreach ($getkpiReports as $report) {
        if (isset($report->department)) {
            $empDepartment = $report->department;
            break; // stop at first valid department
        }
    }
}
?>

<script>
    // Echo PHP variable into JavaScript safely
    var empDepartment = "<?php echo addslashes($empDepartment); ?>";
    console.log("Department:", empDepartment); // Debug in browser console

    function redirectToMonthlyReport() {
        // Find the correct button
        var buttons = document.querySelectorAll('.four-report-btn button');
        var monthlyBtn = null;

        buttons.forEach(function(btn) {
            if (btn.textContent.trim() === 'Monthly KPI Report') {
                monthlyBtn = btn;
            }
        });

        if (monthlyBtn) {
            monthlyBtn.classList.add('active');
        }

        var monthValue = new Date().getMonth();

        setTimeout(function() {
            if (empDepartment === 'MEP') {
                window.location.href = "<?php echo base_url('kpi_reports/getMonthWiseEmpData_mep'); ?>" + "?month_id=" + monthValue;
            } else {
                window.location.href = "<?php echo base_url('kpi_reports/getMonthWiseEmpData'); ?>" + "?month_id=" + monthValue;
            }
        }, 300);
    }



    // Function to handle Consolidated KPI Report button click
function redirectToConsolidatedReport() {
    

    // Add animation effect (optional)
    const button = document.querySelector('.four-report-btn button:nth-child(3)');
    button.classList.add('active');

    // Wait for 300ms (like toggle switch) and then redirect
    setTimeout(function() {
        window.location.href = "<?php echo base_url('kpi_reports/consolidatedReport');?>";
    }, 300);
}
        
    </script>
    
      <!------------------------------------------------------------------------------TABLE CELLS------------------------------------------------------------------>
    <style>
    th, td {
        padding: 8px;
        text-align: center;
    }
th:nth-child(1),td:nth-child(1) {
        width: 110px; /* Increase width for Department, Reporting Manager, Employee */
    }
      
    th:nth-child(3),    
     td:nth-child(3){
        width: 65px; /* Increase width for Department, Reporting Manager, Employee */
    }
        
    th:nth-child(2), th:nth-child(5), 
    td:nth-child(2), td:nth-child(5) {
        width: 100px; /* Increase width for Department, Reporting Manager, Employee */
    }

    th:nth-child(4), th:nth-child(6), th:nth-child(7), th:nth-child(8), th:nth-child(9),
    th:nth-child(10), th:nth-child(11), th:nth-child(12), th:nth-child(13), th:nth-child(14),
    th:nth-child(15),
    td:nth-child(4), td:nth-child(6), td:nth-child(7), td:nth-child(8), td:nth-child(9),
    td:nth-child(10), td:nth-child(11), td:nth-child(12), td:nth-child(13), td:nth-child(14),
    td:nth-child(15) {
        width: 90px; /* Reduce width for other columns */
    }
</style>

<script>
$(document).ready(function() {
    // Initialize department multi-select
    var $dept = $('#department');
    $dept.select2({
        placeholder: 'All departments',
        allowClear: true,
        multiple: true,
        width: '300px',
        minimumResultsForSearch: -1
    });
    // "All departments" and specific depts mutually exclusive (image behaviour)
    var normalizing = false;
    $dept.on('change', function() {
        if (normalizing) return;
        var current = $dept.val() || [];
        if (current.indexOf('__all__') !== -1 && current.length > 1) {
            normalizing = true;
            current = current.filter(function(v) { return v !== '__all__'; });
            $dept.val(current).trigger('change');
            normalizing = false;
        }
    });
    $dept.on('select2:select', function(e) {
        var val = e.params.data.id;
        var current = $dept.val() || [];
        if (val === '__all__') {
            normalizing = true;
            $dept.val(['__all__']).trigger('change');
            normalizing = false;
        } else if (current.indexOf('__all__') !== -1) {
            normalizing = true;
            current = current.filter(function(v) { return v !== '__all__'; });
            if (current.indexOf(val) === -1) current.push(val);
            $dept.val(current).trigger('change');
            normalizing = false;
        }
    });

    // Initialize Search multi-select (employees + managers)
    var searchPreselect = [];
    var searchCanonicalMap = <?= isset($searchOptionValuesLower) ? json_encode($searchOptionValuesLower) : '{}' ?>;
    <?php if (!empty($search)): 
        $searchArr = is_array($search) ? $search : array_values(array_filter(array_map('trim', explode(',', $search))));
        // Map to canonical names (case-insensitive) so same person doesn't show twice
        $canonical = isset($searchOptionValuesLower) ? $searchOptionValuesLower : [];
        $preselect = [];
        foreach ($searchArr as $term) {
            $key = mb_strtolower(trim($term));
            $preselect[] = isset($canonical[$key]) ? $canonical[$key] : $term;
        }
        $searchArr = array_values(array_unique($preselect));
    ?>
    searchPreselect = <?= json_encode($searchArr) ?>;
    <?php endif; ?>

    $('#search').select2({
        placeholder: 'Select employee or manager names',
        allowClear: true,
        width: '100%'
    });
    if (searchPreselect.length) {
        $('#search').val(searchPreselect).trigger('change');
    }
});
</script>

<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
    