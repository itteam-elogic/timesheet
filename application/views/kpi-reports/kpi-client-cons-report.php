<head>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400&display=swap" rel="stylesheet"> <!-- Roboto Condensed for numbers -->
</head>
<style>
    .client-name-text {
        font-weight: bold;
        color: #4c0bce;
    }
    .client-toggle-icon {
        cursor: pointer;
        user-select: none;
        padding-left: 6px;
        vertical-align: middle;
        display: inline-flex;
        align-items: center;
        color: #2C5AA0;
    }
    .client-toggle-icon svg {
        width: 20px;
        height: 20px;
        fill: none;
        stroke: #2C5AA0 !important;
        stroke-width: 4;
        stroke-linecap: round;
    }
    .project-name-cell {
        padding-left: 24px; /* Indent project names under client */
    }
    /* Unified filter row alignment */
    .filter-group { display: flex; align-items: center; flex-wrap: nowrap; }
    .filter-label { width: 228px; margin-right: 10px; font-weight: bold; color: #014b88; white-space: nowrap; }
    .filter-actions { display: flex; gap: 10px; margin-left: 20px; flex-shrink: 0; }
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
    <button id="generateBtn" onclick="downloadExcel()" class="btn btn-primary">
        <span id="btnText">Generate Report</span>
        <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    </button>
</div>
  </div>
    
    
<script>
function downloadExcel() {
    const btn = document.getElementById('generateBtn');
    const btnText = document.getElementById('btnText');
    const spinner = document.getElementById('spinner');

    // Change button style to show loading
    btn.classList.remove('btn-primary');
    btn.classList.add('btn-success');
    btn.disabled = true;
    spinner.classList.remove('d-none');
    btnText.textContent = 'Downloading...';

    var searchTerms = [];
    if (typeof $ !== 'undefined') {
        var fc = $('#filter_clients').val();
        var fp = $('#filter_pms').val();
        var fpr = $('#filter_project').val();
        if (fc && fc.length) {
            $.each($.makeArray(fc), function(_, v) { if (v && String(v).trim()) searchTerms.push(String(v).trim()); });
        }
        if (fp && fp.length) {
            $.each($.makeArray(fp), function(_, v) { if (v && String(v).trim()) searchTerms.push(String(v).trim()); });
        }
        if (fpr && String(fpr).trim() !== '') {
            searchTerms.push(String(fpr).trim());
        }
    }
    const search = searchTerms.length ? searchTerms.join(', ') : '';
    let department = '';
    if (typeof $ !== 'undefined' && $('#department').length) {
        var deptVal = $('#department').val();
        if (deptVal && (Array.isArray(deptVal) ? deptVal.length : 1)) {
            var arr = Array.isArray(deptVal) ? deptVal.filter(function(v){ return v && v !== '' && v !== '__all__'; }) : (deptVal === '__all__' ? [] : [deptVal]);
            if (arr.length) department = arr.join(',');
        }
    }
    const fromDateEl = document.getElementById('from_date');
    const toDateEl = document.getElementById('to_date');
    const from_date = (fromDateEl && fromDateEl.value) ? fromDateEl.value.trim() : '';
    const to_date = (toDateEl && toDateEl.value) ? toDateEl.value.trim() : '';

    let url = '<?php echo base_url('kpi_reports/generateConsolidatedClientReportExcel'); ?>';
    let params = [];
    
    if (search) {
        params.push('search=' + encodeURIComponent(search));
    }
    if (department) {
        params.push('department=' + encodeURIComponent(department));
    }
    if (typeof $ !== 'undefined') {
        var clientsVal = $('#filter_clients').val();
        if (clientsVal && clientsVal.length) {
            $.each($.makeArray(clientsVal), function(_, v) {
                if (v && String(v).trim()) params.push('clients[]=' + encodeURIComponent(String(v).trim()));
            });
        }
        var pmsVal = $('#filter_pms').val();
        if (pmsVal && pmsVal.length) {
            $.each($.makeArray(pmsVal), function(_, v) {
                if (v && String(v).trim()) params.push('pms[]=' + encodeURIComponent(String(v).trim()));
            });
        }
        var projectVal = $('#filter_project').val();
        if (projectVal && String(projectVal).trim() !== '') {
            params.push('project=' + encodeURIComponent(String(projectVal).trim()));
        }
    }
    if (from_date) {
        params.push('from_date=' + encodeURIComponent(from_date));
    }
    if (to_date) {
        params.push('to_date=' + encodeURIComponent(to_date));
    }
    
    if (params.length > 0) {
        url += '?' + params.join('&');
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
        }, 19000); // Adjust duration if needed
    }, 300); // Slight delay to show spinner before the URL change
}
</script>

    <div class="card">
		<h3 class="card-title"></h3>
		<div class="card-body">

                   <div class="four-report-btn " style="margin-left: 9px;">
    
    <button onclick="redirectToClient()" class="btn btn-primary" >Client Monthly Report</button>
    <button onclick="redirectToCConsolidated()" class="btn btn-primary" style="background-color: #014b88; font-weight: bold; border: 2px solid white;">Client Consolidated Report</button>


</div>
   
            
<div class="row mt-4">
                        <div class="col-md-12">
                            <h3>Consolidated Client Report</h3>
                        </div>
                    </div>          
                  

 
            
   <!------------------------------------------------------------------------------SEARCH------------------------------------------------------------------>      
            
<?php if (in_array($this->session->userdata['logged_in_timesheet']['user_type'], ['admin', 'business_head', 'manager'])): ?>
<?php
    $clients_filter = isset($clients_filter) ? $clients_filter : array();
    $pms_filter = isset($pms_filter) ? $pms_filter : array();
    $project_filter = isset($project_filter) ? $project_filter : '';
    if (!is_array($clients_filter)) { $clients_filter = array(); }
    if (!is_array($pms_filter)) { $pms_filter = array(); }
?>
<style>
.client-report-filter-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 22px 24px 20px;
    margin-bottom: 24px;
    box-shadow: 0 4px 14px rgba(1, 75, 136, 0.07), 0 2px 6px rgba(15, 23, 42, 0.04);
    border-top: 3px solid #014b88;
}
.client-report-filter-grid .crf-grid-top {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px 16px;
    margin-bottom: 16px;
}
@media (max-width: 1100px) {
    .client-report-filter-grid .crf-grid-top {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
@media (max-width: 560px) {
    .client-report-filter-grid .crf-grid-top {
        grid-template-columns: 1fr;
    }
}
.client-report-filter-grid .crf-field {
    background: linear-gradient(180deg, #fafbfc 0%, #f4f6f9 100%);
    border: 1px solid #e8ecf1;
    border-radius: 10px;
    padding: 12px 14px 14px;
    min-height: 0;
}
.client-report-filter-grid .crf-field-label {
    display: block;
    font-weight: 700;
    font-size: 12px;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    color: #014b88;
    margin-bottom: 8px;
}
.client-report-filter-grid .crf-dates-actions-row {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: 14px 20px;
    margin-top: 2px;
}
.client-report-filter-grid .crf-dates-compact {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 8px 12px;
    flex: 0 1 auto;
}
.client-report-filter-grid .crf-date-sep {
    font-weight: 700;
    font-size: 13px;
    color: #014b88;
    padding-bottom: 11px;
    line-height: 1;
    flex: 0 0 auto;
}
.client-report-filter-grid .crf-field--date {
    width: auto;
    min-width: 140px;
    max-width: 190px;
    flex: 0 0 auto;
}
.client-report-filter-grid .crf-field--date .crf-date-wrap {
    max-width: 190px;
    width: 100%;
}
.client-report-filter-grid .crf-field--date .crf-date-input {
    width: 100% !important;
    max-width: 190px;
    min-width: 140px;
}
.client-report-filter-grid .crf-dates-actions-row .crf-btn-row {
    flex: 0 0 auto;
    margin-left: auto;
    padding-top: 0;
}
@media (max-width: 720px) {
    .client-report-filter-grid .crf-dates-actions-row {
        flex-direction: column;
        align-items: stretch;
    }
    .client-report-filter-grid .crf-dates-actions-row .crf-btn-row {
        margin-left: 0;
        justify-content: flex-start;
    }
}
.client-report-filter-grid .crf-date-wrap {
    position: relative;
    display: block;
    width: 100%;
}
.client-report-filter-grid .crf-date-input {
    height: 40px !important;
    padding: 8px 36px 8px 12px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    border: 1px solid #d1d5db !important;
    border-radius: 8px !important;
    box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.8) !important;
}
.client-report-filter-grid .crf-date-input.crf-date-required {
    background: linear-gradient(180deg, #fffbeb 0%, #fef3c7 100%) !important;
    border: 1px solid #fbbf24 !important;
}
.client-report-filter-grid .crf-date-wrap .fa-calendar {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #014b88;
    pointer-events: none;
    font-size: 15px;
    opacity: 0.85;
}
.client-report-filter-grid .crf-btn-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    padding-top: 4px;
    border-top: none;
}
.client-report-filter-grid .btn-crf-apply {
    background: linear-gradient(180deg, #015a9e 0%, #014b88 100%) !important;
    color: #fff !important;
    font-weight: 700;
    padding: 10px 22px;
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(1, 75, 136, 0.25);
    min-width: 120px;
}
.client-report-filter-grid .btn-crf-apply:hover {
    filter: brightness(1.05);
    color: #fff !important;
}
.client-report-filter-grid .btn-crf-clear {
    background: #fff !important;
    color: #c2410c !important;
    font-weight: 700;
    padding: 10px 18px;
    border: 2px solid #fdba74 !important;
    border-radius: 8px;
}
.client-report-filter-grid .btn-crf-clear:hover {
    background: #fff7ed !important;
    color: #9a3412 !important;
}
.client-report-filter-grid .select2-container {
    width: 100% !important;
    max-width: 100%;
}
.client-report-filter-grid .select2-container .select2-selection--multiple,
.client-report-filter-grid .select2-container .select2-selection--single {
    min-height: 40px !important;
    border: 1px solid #d1d5db !important;
    border-radius: 8px !important;
    background: #fff !important;
}
.client-report-filter-grid .select2-container--default.select2-container--focus .select2-selection--multiple,
.client-report-filter-grid .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #014b88 !important;
    box-shadow: 0 0 0 3px rgba(1, 75, 136, 0.12);
}
.client-report-filter-grid .select2-container--default .select2-selection--multiple .select2-selection__choice,
.client-report-filter-grid .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    background-color: #6d28d9 !important;
    border-color: #5b21b6 !important;
    color: #fff !important;
}
.client-report-filter-grid .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #f5f3ff !important;
}
</style>
<form method="get" action="<?= base_url('kpi_reports/clientconsReport') ?>" id="dateRangeForm">
    <input type="hidden" id="search" value="<?= isset($search) ? htmlspecialchars($search, ENT_QUOTES, 'UTF-8') : '' ?>">
    <div class="client-report-filter-bar client-report-filter-grid">
        <div class="crf-grid-top">
            <div class="crf-field">
                <span class="crf-field-label"><label for="department">Department</label></span>
                <?php
                    $clientConsDepartments = function_exists('ts_primary_delivery_departments')
                        ? ts_primary_delivery_departments()
                        : array('Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP');
                    $selectedDepartmentValues = array();
                    if (isset($department)) {
                        if (is_array($department)) {
                            $selectedDepartmentValues = array_map('strval', $department);
                        } elseif (is_string($department) && trim($department) !== '') {
                            $selectedDepartmentValues = array(trim($department));
                        }
                    }
                ?>
                <select name="department[]" id="department" class="form-control" multiple="multiple" style="width: 100%;">
                    <option value="__all__" <?= empty($selectedDepartmentValues) ? 'selected' : '' ?>>All departments</option>
                    <?php foreach ($clientConsDepartments as $deptOption): ?>
                        <?php $deptOption = trim((string)$deptOption); if ($deptOption === '') { continue; } ?>
                        <option value="<?= htmlspecialchars($deptOption, ENT_QUOTES, 'UTF-8'); ?>" <?= in_array($deptOption, $selectedDepartmentValues, true) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($deptOption, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="crf-field">
                <span class="crf-field-label"><label for="filter_clients">Client's</label></span>
                <select name="clients[]" id="filter_clients" class="form-control" multiple="multiple" style="width: 100%;" data-placeholder="All clients">
                    <?php foreach ($clients_filter as $cf): ?>
                        <?php $cf = trim((string)$cf); if ($cf === '') { continue; } ?>
                        <option value="<?= htmlspecialchars($cf, ENT_QUOTES, 'UTF-8'); ?>" selected><?= htmlspecialchars($cf, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="crf-field">
                <span class="crf-field-label"><label for="filter_project">Project's</label></span>
                <select name="project" id="filter_project" class="form-control" style="width: 100%;" data-placeholder="All projects">
                    <option value="">All projects</option>
                    <?php if (!empty($project_filter)): ?>
                        <option value="<?= htmlspecialchars($project_filter, ENT_QUOTES, 'UTF-8'); ?>" selected><?= htmlspecialchars($project_filter, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="crf-field">
                <span class="crf-field-label"><label for="filter_pms">Project Manager</label></span>
                <select name="pms[]" id="filter_pms" class="form-control" multiple="multiple" style="width: 100%;" data-placeholder="All project managers">
                    <?php foreach ($pms_filter as $pf): ?>
                        <?php $pf = trim((string)$pf); if ($pf === '') { continue; } ?>
                        <option value="<?= htmlspecialchars($pf, ENT_QUOTES, 'UTF-8'); ?>" selected><?= htmlspecialchars($pf, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="crf-dates-actions-row">
            <div class="crf-dates-compact">
                <div class="crf-field crf-field--date">
                    <span class="crf-field-label"><label for="from_date">From Date <span class="text-danger">*</span></label></span>
                    <div class="crf-date-wrap">
                        <input type="text" name="from_date" id="from_date" required
                               class="form-control date-highlight crf-date-input crf-date-required"
                               value="<?= isset($from_date) && !empty($from_date) ? htmlspecialchars($from_date) : date('Y-m-01') ?>"
                               placeholder="From" readonly="">
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>
                <span class="crf-date-sep" aria-hidden="true">to</span>
                <div class="crf-field crf-field--date">
                    <span class="crf-field-label"><label for="to_date">To Date <span class="text-danger">*</span></label></span>
                    <div class="crf-date-wrap">
                        <input type="text" name="to_date" id="to_date" required
                               class="form-control date-highlight crf-date-input crf-date-required"
                               value="<?= isset($to_date) && !empty($to_date) ? htmlspecialchars($to_date) : date('Y-m-t') ?>"
                               placeholder="To" readonly="">
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>
            </div>
            <div class="crf-btn-row">
                <button type="submit" class="btn btn-crf-apply"><i class="fa fa-search"></i> Search</button>
                <button type="button" class="btn btn-crf-clear" onclick="removeFiltersAndReload();"><i class="fa fa-times-circle"></i> Clear all filters</button>
            </div>
        </div>
    </div>
</form>
<?php endif; ?>

<script>
// Toggle icon SVGs (plus = expand, minus = collapse) - define first so always available
window.toggleIconPlus = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="20" height="20"><line x1="8" y1="2" x2="8" y2="14" stroke="#2C5AA0" stroke-width="4" stroke-linecap="round"/><line x1="2" y1="8" x2="14" y2="8" stroke="#2C5AA0" stroke-width="4" stroke-linecap="round"/></svg>';
window.toggleIconMinus = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="20" height="20"><line x1="2" y1="8" x2="14" y2="8" stroke="#2C5AA0" stroke-width="4" stroke-linecap="round"/></svg>';

// Function to highlight search terms in blue and bold in the grid
function highlightSearchTermsInGrid() {
    const searchInput = document.getElementById("search");
    if (!searchInput) return;
    
    const searchValue = searchInput.value.trim();
    const searchTerms = searchValue ? searchValue.split(',').map(t => t.trim()).filter(Boolean) : [];
    
    if (searchTerms.length === 0) {
        // Remove all highlighting if no search terms
        document.querySelectorAll(".client-row .client-name-text").forEach(span => {
            const clientRow = span.closest(".client-row");
            const originalText = clientRow?.getAttribute("data-client") || span.textContent.trim();
            span.textContent = originalText;
        });
        document.querySelectorAll(".project-row .project-name-cell").forEach(cell => {
            const projectRow = cell.closest(".project-row");
            const originalText = projectRow?.getAttribute("data-project") || cell.textContent.trim();
            cell.textContent = originalText;
        });
        return;
    }
    
    // Highlight client names - only the search value parts in blue and bold (update .client-name-text only)
    document.querySelectorAll(".client-row").forEach(clientRow => {
        const clientName = clientRow.getAttribute("data-client")?.trim() || "";
        const clientNameSpan = clientRow.querySelector('td:first-child .client-name-text');
        if (clientNameSpan && clientName) {
            const clientNameLower = clientName.toLowerCase();
            const matches = searchTerms.some(term => term && clientNameLower.includes(term.toLowerCase()));
            if (matches) {
                clientNameSpan.innerHTML = highlightSearchTerms(clientName, searchTerms);
            } else {
                clientNameSpan.textContent = clientName;
            }
        }
    });
    
    // Highlight project names - project name is now in first cell (td.project-name-cell)
    document.querySelectorAll(".project-row").forEach(projectRow => {
        const projectName = projectRow.getAttribute("data-project")?.trim() || "";
        const projectNameCell = projectRow.querySelector('td.project-name-cell');
        if (projectNameCell && projectName) {
            const projectNameLower = projectName.toLowerCase();
            const matches = searchTerms.some(term => term && projectNameLower.includes(term.toLowerCase()));
            if (matches) {
                projectNameCell.innerHTML = highlightSearchTerms(projectName, searchTerms);
            } else {
                projectNameCell.textContent = projectName;
            }
        }
    });
}

// Apply highlighting when page loads with search parameters
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById("search") && document.getElementById("search").value.trim() !== '') {
        setTimeout(function() {
            highlightSearchTermsInGrid();
        }, 100);
    }
});

// Also apply on window load for compatibility
window.addEventListener('load', function() {
    if (document.getElementById("search") && document.getElementById("search").value.trim() !== '') {
        highlightSearchTermsInGrid();
    }
});
</script>
          
<script>
    function removeSearchAndReload() {
        // Create a URL object from the current window location
        var url = new URL(window.location.href);
        
        // Remove the 'search' parameter
        url.searchParams.delete('search');
        
        // Reload the page with the updated URL
        window.location.href = url.toString();
    }
    
    function removeFiltersAndReload() {
        window.location.href = "<?= base_url('kpi_reports/clientconsReport') ?>";
    }
    
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
            
            
<script>
// Function to submit form with search and date filters
function applySearchFilter() {
    document.getElementById("dateRangeForm").submit();
}

// Function to highlight matched search terms in text with blue color and bold
function highlightSearchTerms(text, searchTerms) {
    if (!searchTerms || searchTerms.length === 0 || !text) {
        return text;
    }
    
    let highlightedText = text;
    // Process search terms in reverse order to avoid nested highlighting issues
    const sortedTerms = [...searchTerms].sort((a, b) => b.length - a.length);
    
    sortedTerms.forEach(term => {
        if (term && term.trim()) {
            // Escape special regex characters
            const escapedTerm = term.trim().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            // Create regex that matches the term (case-insensitive) but not already inside a span
            const regex = new RegExp(`(?!<[^>]*>)(${escapedTerm})(?![^<]*</span>)`, 'gi');
            // Replace with highlighted span - blue color (#014b88), bold font weight, light blue background
            highlightedText = highlightedText.replace(regex, '<span style="color: #014b88; font-weight: bold; background-color: #e6f2ff;">$1</span>');
        }
    });
    return highlightedText;
}

// Function for client-side filtering (for auto-apply on page load)
function applySearchFilterClientSide() {
    const searchInput = document.getElementById("search")
                             .value.trim();
    const searchTerms = searchInput
                             .split(',')
                             .map(t => t.trim())
                             .filter(Boolean);
    const searchTermsLower = searchTerms.map(t => t.toLowerCase());

    document.querySelectorAll(".client-row").forEach(clientRow => {
        const clientName  = clientRow.getAttribute("data-client")
                                 ?.trim() || "";
        const clientNameLower = clientName.toLowerCase();
        const managerName = clientRow.getAttribute("data-pm-name")
                                 ?.trim().toLowerCase() || "";
        const clientHash  = clientRow.querySelector(".toggle-projects")
                                 ?.getAttribute("data-client");
        const projectRows = document.querySelectorAll(`.project-${clientHash}`);

        // Does the client itself match (name or PM)?
        const clientMatches = searchTermsLower.length === 0
            || searchTermsLower.some(term =>
                clientNameLower.includes(term) || managerName.includes(term)
            );

        // Highlight client name if it matches (only .client-name-text)
        const clientNameSpan = clientRow.querySelector('td:first-child .client-name-text');
        if (clientNameSpan) {
            if (clientMatches && searchTermsLower.length > 0) {
                clientNameSpan.innerHTML = highlightSearchTerms(clientName, searchTerms);
            } else {
                clientNameSpan.textContent = clientName;
            }
        }

        let matchingProjects = [];
        let hasVisibleProjects = false; // NEW: Track if any projects are visible

        projectRows.forEach(projectRow => {
            const projectName = projectRow.getAttribute("data-project")
                                     ?.trim() || "";
            const projectNameLower = projectName.toLowerCase();
            const projectPM   = projectRow.getAttribute("data-manager")
                                     ?.trim().toLowerCase() || "";

            // two separate checks
            const nameMatch = searchTermsLower.length === 0
                || searchTermsLower.some(term => projectNameLower.includes(term));
            const pmMatch   = searchTermsLower.length === 0
                || searchTermsLower.some(term => projectPM.includes(term));

            // reset
            projectRow.style.display = "none";
            projectRow.classList.remove("highlighted-project");

            // Highlight project name if it matches (first cell is project name)
            const projectNameCell = projectRow.querySelector('td.project-name-cell');
            if (projectNameCell) {
                if (nameMatch && searchTermsLower.length > 0) {
                    projectNameCell.innerHTML = highlightSearchTerms(projectName, searchTerms);
                } else {
                    projectNameCell.textContent = projectName;
                }
            }

            // if it matches either, stash it
            if (nameMatch || pmMatch) {
                matchingProjects.push(projectRow);

                // but only show & highlight immediately on a project-name match
                if (nameMatch && searchTermsLower.length > 0) {
                    projectRow.style.display = "";
                    projectRow.classList.add("highlighted-project");
                    hasVisibleProjects = true; // NEW: Mark that we have visible projects
                }
            }
        });

        // show/hide client
        const shouldShowClient = clientMatches || matchingProjects.length > 0;
        clientRow.style.display = shouldShowClient ? "" : "none";
        clientRow.classList.toggle("expanded", hasVisibleProjects); // NEW: Set expanded class if projects are visible

        // Update toggle symbol based on visibility (plus / minus SVG)
        const toggleBtn = clientRow.querySelector(".toggle-projects");
        if (toggleBtn) {
            var iconHtml = hasVisibleProjects ? (window.toggleIconMinus || '') : (window.toggleIconPlus || '');
            toggleBtn.innerHTML = iconHtml;
            toggleBtn.title = hasVisibleProjects ? "Click to collapse" : "Click to view projects";
            toggleBtn.setAttribute("aria-label", hasVisibleProjects ? "Collapse projects" : "Expand projects");
        }

        // stash project-names for expand/collapse
        clientRow.dataset.matchingProjects = matchingProjects
            .map(r => r.getAttribute("data-project"))
            .join(",");

        // When client matches (client search), show all projects initially hidden
        if (clientMatches && searchTermsLower.length > 0) {
            clientRow.dataset.matchingProjects = Array.from(projectRows)
                .map(r => r.getAttribute("data-project"))
                .join(",");
        }
    });
    
    // If no search terms, remove all highlighting
    if (searchTermsLower.length === 0) {
        document.querySelectorAll(".client-row .client-name-text").forEach(span => {
            const originalText = span.closest(".client-row")?.getAttribute("data-client") || span.textContent;
            span.textContent = originalText;
        });
        document.querySelectorAll(".project-row .project-name-cell").forEach(cell => {
            const originalText = cell.closest(".project-row")?.getAttribute("data-project") || cell.textContent;
            cell.textContent = originalText;
        });
    });
}

// expand/collapse handler (icons defined at top of page script)
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".toggle-projects").forEach(toggle => {
        toggle.addEventListener("click", function () {
            const clientRow  = this.closest(".client-row");
            const clientHash = this.getAttribute("data-client");
            const rows       = document.querySelectorAll(`.project-${clientHash}`);
            const isOpen     = clientRow.classList.contains("expanded");

            if (isOpen) {
                // collapse
                clientRow.classList.remove("expanded");
                rows.forEach(r => r.style.display = "none");
                this.innerHTML = window.toggleIconPlus || '';
                this.title = "Click to view projects";
                this.setAttribute("aria-label", "Expand projects");
            } else {
                // expand
                clientRow.classList.add("expanded");
                const allowed = (clientRow.dataset.matchingProjects || "")
                    .split(",")
                    .map(s => s.trim());

                rows.forEach(r => {
                    const name = r.getAttribute("data-project")?.trim() || "";
                    if (allowed.length === 0 || allowed.includes(name)) {
                        r.style.display = "";  // Display the project under the client
                    } else {
                        r.style.display = "none";  // Hide the project if it doesn't match
                    }
                });
                this.innerHTML = window.toggleIconMinus || '';
                this.title = "Click to collapse";
                this.setAttribute("aria-label", "Collapse projects");
            }
        });
        toggle.addEventListener("keydown", function (e) {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                this.click();
            }
        });
    });
}
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".toggle-projects").forEach(function (btn) {
        btn.addEventListener("click", function () {
            var clientId = this.getAttribute("data-client");
            var isHidden = false;

            document.querySelectorAll(".project-" + clientId).forEach(function (row) {
                isHidden = row.style.display === "none";
                row.style.display = isHidden ? "" : "none";
            });

            var iconHtml = isHidden ? (window.toggleIconMinus || '') : (window.toggleIconPlus || '');
            this.innerHTML = iconHtml;
            this.title = isHidden ? "Click to collapse" : "Click to view projects";
        });
    });
});
</script>       
            
<script>
$(function() {
    var suggestUrl = "<?php echo base_url('kpi_reports/autosuggest_project_names'); ?>";
    var cachedActiveClientsCount = null;
    var departmentFilterPrevSerialized = null;

    function syncSearchHidden() {
        var searchTerms = [];
        var fc = $("#filter_clients").val();
        var fp = $("#filter_pms").val();
        var fpr = $("#filter_project").val();
        if (fc && fc.length) {
            $.each($.makeArray(fc), function(_, v) { if (v && String(v).trim()) searchTerms.push(String(v).trim()); });
        }
        if (fp && fp.length) {
            $.each($.makeArray(fp), function(_, v) { if (v && String(v).trim()) searchTerms.push(String(v).trim()); });
        }
        if (fpr && String(fpr).trim() !== "") {
            searchTerms.push(String(fpr).trim());
        }
        $("#search").val(searchTerms.length ? searchTerms.join(", ") : "");
    }

    function buildGrouped(data) {
        var rows = (data && data.suggestions) ? data.suggestions : (Array.isArray(data) ? data : []);
        if (data && typeof data.active_clients_count === "number") {
            cachedActiveClientsCount = data.active_clients_count;
        }
        var clientTotal = (cachedActiveClientsCount !== null) ? cachedActiveClientsCount : null;
        var grouped = {};
        $.each(rows, function(i, item) {
            var label = (item.label || item.value || "").trim();
            var value = (item.value || item.label || "").trim();
            var cat = item.category || "Other";
            if (!value) return;
            if (!grouped[cat]) grouped[cat] = [];
            grouped[cat].push({ id: value, text: label });
        });
        return { grouped: grouped, clientTotal: clientTotal };
    }

    function processResultsForCategory(data, only) {
        var r = buildGrouped(data);
        var grouped = r.grouped;
        var clientTotal = r.clientTotal;
        var results = [];
        if (only === "Clients") {
            if (grouped["Clients"] && grouped["Clients"].length) {
                var clientHeader = (clientTotal !== null) ? ("Clients (" + clientTotal + " active)") : "Clients";
                results.push({ text: clientHeader, children: grouped["Clients"] });
            }
        } else if (only === "Projects") {
            if (grouped["Projects"] && grouped["Projects"].length) {
                results.push({ text: "Projects", children: grouped["Projects"] });
            }
        } else if (only === "Managers") {
            if (grouped["Managers"] && grouped["Managers"].length) {
                results.push({ text: "Managers", children: grouped["Managers"] });
            }
        }
        return { results: results };
    }

    function ajaxContextParam(onlyCat) {
        if (onlyCat === "Clients") {
            return "clients";
        }
        if (onlyCat === "Projects") {
            return "projects";
        }
        return "managers";
    }

    function ajaxConfig(onlyCat, placeholder, multiple, extraDataFn) {
        extraDataFn = extraDataFn || function() { return {}; };
        var ctx = ajaxContextParam(onlyCat);
        return {
            ajax: {
                url: suggestUrl,
                dataType: "json",
                delay: 320,
                data: function(params) {
                    var payload = { term: params.term || "", context: ctx };
                    var extra = extraDataFn();
                    if (extra.department && extra.department.length) {
                        payload.department = extra.department;
                    }
                    if (extra.clients && extra.clients.length) {
                        payload.clients = extra.clients;
                    }
                    return payload;
                },
                processResults: function(data) {
                    return processResultsForCategory(data, onlyCat);
                },
                cache: false
            },
            placeholder: placeholder,
            allowClear: true,
            multiple: multiple,
            width: "100%",
            minimumInputLength: 0,
            language: { inputTooShort: function() { return "Type to search"; }, searching: function() { return "Searching…"; } }
        };
    }

    if ($("#filter_clients").length) {
        $("#filter_clients").select2(ajaxConfig("Clients", "All clients", true, function() {
            var d = $("#department").val() || [];
            d = $.grep($.makeArray(d), function(x) { return x && String(x) !== "__all__"; });
            return { department: d };
        }));
        $("#filter_clients").on("change", function() {
            if ($("#filter_project").length) {
                $("#filter_project").val(null).trigger("change");
            }
            syncSearchHidden();
        });
    }
    if ($("#filter_pms").length) {
        $("#filter_pms").select2(ajaxConfig("Managers", "All project managers", true, function() { return {}; }));
        $("#filter_pms").on("change", syncSearchHidden);
    }
    if ($("#filter_project").length) {
        $("#filter_project").select2(ajaxConfig("Projects", "All projects", false, function() {
            var c = $("#filter_clients").val() || [];
            c = $.grep($.makeArray(c), function(x) { return x && String(x).trim() !== ""; });
            return { clients: c };
        }));
        $("#filter_project").on("change", syncSearchHidden);
    }

    if ($("#department").length) {
        $("#department").select2({
            placeholder: "Select departments",
            allowClear: true,
            multiple: true,
            width: "100%"
        });
        $("#department").on("select2:select", function(e) {
            if (e.params && e.params.data && e.params.data.id === "__all__") {
                $("#department").val(["__all__"]).trigger("change");
            } else {
                var vals = $("#department").val() || [];
                if (vals.indexOf("__all__") >= 0) {
                    vals = vals.filter(function(x) { return x !== "__all__"; });
                    $("#department").val(vals).trigger("change");
                }
            }
        });
        $("#department").on("change", function() {
            var cur = JSON.stringify((($("#department").val() || []).slice().sort()));
            if (departmentFilterPrevSerialized === null) {
                departmentFilterPrevSerialized = cur;
                return;
            }
            if (departmentFilterPrevSerialized === cur) {
                return;
            }
            departmentFilterPrevSerialized = cur;
            if ($("#filter_clients").length) {
                $("#filter_clients").val(null).trigger("change");
            }
            if ($("#filter_project").length) {
                $("#filter_project").val(null).trigger("change");
            }
        });
        setTimeout(function() {
            departmentFilterPrevSerialized = JSON.stringify((($("#department").val() || []).slice().sort()));
        }, 0);
    }

    $("#dateRangeForm").on("submit", function() {
        syncSearchHidden();
        var vals = $("#department").val() || [];
        if (vals.indexOf("__all__") >= 0 && vals.length > 1) {
            vals = vals.filter(function(x) { return x !== "__all__"; });
            $("#department").val(vals).trigger("change");
        }
    });

    if ($("#filter_clients").length) {
        syncSearchHidden();
    }
});
</script>

<style>
/* Date Range Input Styling */
.date-highlight {
    font-size: 15px !important;
    padding: 8px 12px !important;
    transition: all 0.3s ease;
}

.date-highlight:hover {
    background-color: #fff3d1 !important;
    border-color: #ff8c00 !important;
    box-shadow: 0 0 10px rgba(255, 158, 79, 0.7) !important;
    transform: scale(1.02);
}

.date-highlight:focus {
    outline: none !important;
    background-color: #fffbeb !important;
    border-color: #ff6600 !important;
    box-shadow: 0 0 15px rgba(255, 158, 79, 0.9) !important;
    transform: scale(1.02);
}

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
</style>


   <!------------------------------------------------------------------------------------------------------------------------------------------------>  

 
                        
    </div>                    
	</div>    
    
  <div class="row">
    
      <div class="card">
        <div class="card-body">
            <div id="content-wrapper" class="d-flex flex-column">
                <!-- Begin Page Content -->
                <div class="container-fluid">                    



   
   <div class="row mt-4">
                        <div class="col-md-12">
                           

<?php
    // Display date range or default period - Format as 01-Nov-2025 to 30-Nov-2025
    if (!empty($from_date) && !empty($to_date)) {
        $fromDisplay = date('d-M-Y', strtotime($from_date));
        $toDisplay = date('d-M-Y', strtotime($to_date));
        $periodDisplay = $fromDisplay . ' to ' . $toDisplay;
    } else {
        $currentYear = date('Y');
        $currentMonth = date('n'); // Current month number (1-12)
        
        // Show data up to current month (not previous)
        $monthToShow = $currentMonth;
        $yearToShow = $currentYear;
        
        $monthName = date('F', mktime(0, 0, 0, $monthToShow, 1));
        $periodDisplay = 'January - ' . $monthName . ' ' . $yearToShow;
    }
?>

<h3><?php echo $periodDisplay; ?></h3>

                        </div>
                    </div>     

<div id="clientConsReportDeptKpiHost" class="row mt-3 mb-2" data-url="<?= htmlspecialchars(site_url('kpi_reports/clientReportDepartmentKpiJson'), ENT_QUOTES, 'UTF-8'); ?>"></div>
<script>
(function ($) {
    function escHtml(s) { return $('<div/>').text(s == null ? '' : String(s)).html(); }
    function pctCell(v) { return (v === null || v === undefined || v === '') ? '-' : (parseInt(v, 10) + '%'); }
    function hoursCell(v) {
        if (v === null || v === undefined || v === '') return '-';
        var n = parseFloat(v);
        return isNaN(n) ? '-' : ((n % 1 === 0) ? String(Math.round(n)) : n.toFixed(2).replace(/\.?0+$/, ''));
    }
    function numCell(v) {
        if (v === null || v === undefined || v === '') return '-';
        var n = parseFloat(v);
        return isNaN(n) ? '-' : n.toFixed(2);
    }
    function diffCell(v) {
        if (v === null || v === undefined || v === '') return '-';
        var n = parseFloat(v);
        if (isNaN(n)) return '-';
        return '<span style="color:' + (n >= 0 ? '#28a745' : '#dc3545') + ';">' + numCell(v) + '</span>';
    }
    var thS = 'text-align:center;font-weight:bold;color:white;padding:12px 8px;border:1px solid #0a3d66;';
    var tdS = 'text-align:center;padding:12px 8px;font-weight:bold;border:1px solid #ccc;';
    $(function () {
        var $host = $('#clientConsReportDeptKpiHost');
        if (!$host.length) return;
        var qs = $('#dateRangeForm').length ? $('#dateRangeForm').serialize() : window.location.search.replace(/^\?/, '');
        $.ajax({ url: $host.data('url'), data: qs, dataType: 'json', method: 'GET' }).done(function (res) {
            if (!res || !res.has_data || !res.rows || !res.rows.length) return;
            var html = '<div class="col-md-12"><h4 style="text-align:center;font-weight:700;color:#014b88;margin-bottom:14px;">Department &amp; Project Manager Client Summary Report</h4>'
                + '<div style="display:flex;justify-content:center;overflow-x:auto;">'
                + '<table class="table table-bordered client-report-dept-kpi-table" style="width:100%;max-width:1200px;border-collapse:collapse;margin:0 auto;">'
                + '<thead><tr style="background-color:#014b88;">'
                + '<th style="text-align:left;font-weight:bold;color:white;padding:12px 14px;border:1px solid #0a3d66;">Departments</th>'
                + '<th style="' + thS + '">Prod Hours</th><th style="' + thS + '">PG Hours</th><th style="' + thS + '">Utilization Hours</th>'
                + '<th style="' + thS + '">Productivity%</th><th style="' + thS + '">Project General%</th><th style="' + thS + '">Utilization%</th>'
                + '<th style="' + thS + '">Quality %</th><th style="' + thS + '">Invoiced hours</th><th style="' + thS + '">Difference</th>'
                + '</tr></thead><tbody>';
            for (var i = 0; i < res.rows.length; i++) {
                var r = res.rows[i];
                html += '<tr><td style="text-align:left;font-weight:bold;background:#fff;padding:12px 14px;border:1px solid #ccc;">' + escHtml(r.label) + '</td>'
                    + '<td style="' + tdS + 'background:#fff;">' + hoursCell(r.prod_hours) + '</td>'
                    + '<td style="' + tdS + 'background:#fff;">' + hoursCell(r.pg_hours) + '</td>'
                    + '<td style="' + tdS + 'background:#fff;">' + hoursCell(r.utilization_hours) + '</td>'
                    + '<td style="' + tdS + 'background:#d4edda;">' + pctCell(r.productivity_pct) + '</td>'
                    + '<td style="' + tdS + 'background:#fff3cd;">' + pctCell(r.project_general_pct) + '</td>'
                    + '<td style="' + tdS + 'background:#e2d5f3;">' + pctCell(r.utilization_pct) + '</td>'
                    + '<td style="' + tdS + 'background:#fff;">' + pctCell(r.quality_pct) + '</td>'
                    + '<td style="' + tdS + 'background:#fff;">' + numCell(r.invoiced_hours) + '</td>'
                    + '<td style="' + tdS + 'background:#fff;">' + diffCell(r.difference) + '</td></tr>';
            }
            html += '</tbody></table></div></div>';
            $host.html(html);
        });
    });
})(jQuery);
</script>

<?php
// Get date range from controller
$from_date = isset($from_date) ? $from_date : '';
$to_date = isset($to_date) ? $to_date : '';

$grouped = [];
if (!empty($clientCons) && is_array($clientCons)) {
    foreach ($clientCons as $row) {
        $grouped[$row->client_Id]['client_name'] = $row->client_name;
        $grouped[$row->client_Id]['department'] = $row->department;
        $grouped[$row->client_Id]['clientpm'] = $row->clientpm;
        $grouped[$row->client_Id]['projects'][] = $row;
    }
    // Ensure Client Name column is alphabetical.
    uasort($grouped, function($a, $b) {
        $nameA = isset($a['client_name']) ? (string)$a['client_name'] : '';
        $nameB = isset($b['client_name']) ? (string)$b['client_name'] : '';
        return strcasecmp($nameA, $nameB);
    });
} else {
    echo "<p style='color:red;'>No consolidated client data found.</p>";
}


                    ?>

<table id="employeeTable" class="table table-bordered mt-4">
    <thead>
        <tr>
            <th title="Client Name">Client Name</th>
            <th title="Project Manager">Project Manager</th>
            <th title="Department">Department</th>
            <th title="Billing" style>Billing</th>
            <th title="Start Date">Start Date</th>
            <th title="End Date">End Date</th>
            <th title="Production Hours">Production Hours</th>
            <th title="Project General Hours">Project General Hours</th>
            <th title="Total Hours">Total Hours</th>
            <th title="Invoiced%">Invoiced</th>
            <th title="Quality Errors">Quality Errors</th>
            <th title="Productivity%">Productivity%</th>
            <th title="Project General%">Project General%</th>
            <th title="Difference">Difference</th>
            <style>
                th {
                    text-align: center;
                    vertical-align: middle !important;
                    padding: 10px;
                }
            </style>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($grouped as $clientId => $data): ?>
            <?php
        
        
        $totalconsProductionHours = 0;
$totalconsGeneralHours = 0;
$totalProjects = count($data['projects']);
$projectQualityErrors = [];
$totalProjectQualityErrors = 0;

foreach ($data['projects'] as $proj) {
// Use production hours from query result (already calculated in the query) - avoids N+1 queries
$productiveHours = isset($proj->total_hours) ? (float)$proj->total_hours : 0;

        
    $totalconsProductionHours += $productiveHours;
    
    
    // Get PG (Project General) hours for this specific project - calculated per project based on project ID
    $generalHours = isset($proj->general_hours) ? $proj->general_hours : 0;
    $totalconsGeneralHours += $generalHours;
    
    // Calculate quality errors - only if date matches date range (if date range is provided)
    $hasValidQualityError = false;
    if (!empty($from_date) && !empty($to_date)) {
        // If date range is provided, check if quality error log date matches
        if (!empty($proj->analyzer_report_date)) {
            $errorDate = strtotime($proj->analyzer_report_date);
            $fromDate = strtotime($from_date);
            $toDate = strtotime($to_date);
            if ($errorDate >= $fromDate && $errorDate <= $toDate) {
                $hasValidQualityError = true;
            }
        }
    } else {
        // If no date range, consider quality error valid if it exists
        $hasValidQualityError = !empty($proj->analyzer_num_of_errors) || !empty($proj->reviewer_num_of_errors);
    }
    
    if ($hasValidQualityError) {
        $analyzerErrors = isset($proj->analyzer_num_of_errors) ? $proj->analyzer_num_of_errors : 0;
        $reviewerErrors = isset($proj->reviewer_num_of_errors) ? $proj->reviewer_num_of_errors : 0;
        $projectTotalErrors = $analyzerErrors + $reviewerErrors;
        $projectQualityErrors[$proj->project_Id] = $projectTotalErrors;
        $totalProjectQualityErrors += $projectTotalErrors;
    } else {
        // Mark as empty/null for this project
        $projectQualityErrors[$proj->project_Id] = null;
    }
     

}   
        
         $totalconsCombined = $totalconsProductionHours + $totalconsGeneralHours; 
         $consproductivityPercentage = $totalconsCombined > 0 ? ($totalconsProductionHours / $totalconsCombined) * 100 : 0;
        $consprojectgeneralPercentage = $totalconsCombined > 0 ? ($totalconsGeneralHours / $totalconsCombined) * 100 : 0;
        
        // Calculate total invoice amount for all projects
        $totalconsInvoiceAmount = 0;
        foreach ($data['projects'] as $proj) {
            if (isset($proj->project_invoice_amt)) {
                $invoiceAmt = (float)$proj->project_invoice_amt;
                if ($invoiceAmt > 0) {
                    $totalconsInvoiceAmount += $invoiceAmt;
                }
            }
        }
        
        // Calculate difference in hours for client row
       // $consClientDifference = $totalconsCombined - $totalconsInvoiceAmount;

        $consClientDifference = $totalconsInvoiceAmount - $totalconsCombined;
        
        // Calculate QA percentage - only count projects with valid quality errors
        $k_QualityErrorPercentage = '--';
        $projectsWithValidErrors = 0;
        $sumPercentages = 0;
        foreach ($projectQualityErrors as $projId => $projErrors) {
            if ($projErrors !== null) {
                $sumPercentages += (100 - $projErrors);
                $projectsWithValidErrors++;
            }
        }
        if ($projectsWithValidErrors > 0) {
            $avgPercentage = $sumPercentages / $projectsWithValidErrors;
            $k_QualityErrorPercentage = round($avgPercentage) . '%';
        }
    
            $clientPmName = '--'; // Default value for client manager name
            $billable = '';
            foreach ($data['projects'] as $proj) {
                if (!empty($proj->man_days)) {
                    $billable = $proj->man_days;
                    break;
                }
            }

            // Helper function to validate and format date
            $isValidDate = function($dateStr) {
                if (empty($dateStr) || $dateStr == '0000-00-00' || $dateStr == '0000-00-00 00:00:00') {
                    return false;
                }
                $timestamp = strtotime($dateStr);
                if ($timestamp === false || $timestamp < 0) {
                    return false;
                }
                // Check if date is not 1970-01-01 (invalid date fallback)
                $formatted = date('Y-m-d', $timestamp);
                if ($formatted == '1970-01-01') {
                    return false;
                }
                return $timestamp;
            };
            
            // Calculate earliest start date and latest end date from all projects
            // Use last_work_date from emp_record_details for end date instead of project_end_date
            $earliestStartDate = null;
            $latestEndDate = null;
            foreach ($data['projects'] as $proj) {
                if (!empty($proj->project_start_date)) {
                    $projStartDate = $isValidDate($proj->project_start_date);
                    if ($projStartDate !== false && ($earliestStartDate === null || $projStartDate < $earliestStartDate)) {
                        $earliestStartDate = $projStartDate;
                    }
                }
                // Use last_work_date from emp_record_details if available, otherwise fall back to project_end_date
                $endDateToUse = !empty($proj->last_work_date) ? $proj->last_work_date : (isset($proj->project_end_date) ? $proj->project_end_date : null);
                if (!empty($endDateToUse)) {
                    $projEndDate = $isValidDate($endDateToUse);
                    if ($projEndDate !== false && ($latestEndDate === null || $projEndDate > $latestEndDate)) {
                        $latestEndDate = $projEndDate;
                    }
                }
            }

            // Use preloaded client project manager name from query (avoids N+1 DB queries)
            if (!empty($data['projects']) && isset($data['projects'][0]->client_pm_name)) {
                $clientPmName = $data['projects'][0]->client_pm_name;
            }
            ?>
            <tr class="client-row"
                data-client="<?php echo $data['client_name']; ?>"
                data-project=""
                data-manager="<?php echo $clientPmName; ?>"
                data-department="<?php echo $data['department']; ?>">
                <td title="Client Name" class="client-name-cell">
                    <span class="client-name-text"><?php echo htmlspecialchars($data['client_name']); ?></span>
                    <span class="toggle-projects client-toggle-icon" data-client="<?php echo md5($clientId); ?>" title="Click to view projects" role="button" tabindex="0" aria-label="Expand projects"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="20" height="20"><line x1="8" y1="2" x2="8" y2="14" stroke="#2C5AA0" stroke-width="4" stroke-linecap="round"/><line x1="2" y1="8" x2="14" y2="8" stroke="#2C5AA0" stroke-width="4" stroke-linecap="round"/></svg></span>
                </td>
                <td title="Project Manager">
  <?php echo explode(' ', trim($clientPmName))[0]; ?>
</td>
                <td title="Department"><?php echo $data['department']; ?></td>
                <td title="Billable"><?php echo ucfirst($billable); ?></td>
                <td title="Start Date">
                    <?php 
                    if ($earliestStartDate !== null) {
                        $formattedDate = date('d-M-Y', $earliestStartDate);
                        // Check if formatted date is not 01-Jan-1970
                        if ($formattedDate != '01-Jan-1970') {
                            echo $formattedDate;
                        }
                    }
                    ?>
                </td>
                <td title="End Date">
                    <?php 
                    if ($latestEndDate !== null) {
                        $formattedDate = date('d-M-Y', $latestEndDate);
                        // Check if formatted date is not 01-Jan-1970
                        if ($formattedDate != '01-Jan-1970') {
                            echo $formattedDate;
                        }
                    }
                    ?>
                </td>
                <td title="Production Hours" style="text-align: center !important; font-weight: bold !important;"><?php echo $totalconsProductionHours; ?></td>
                <td title="Project General Hours" style="text-align: center !important; font-weight: bold !important;"><?php echo $totalconsGeneralHours; ?></td>
                <td  title="Total Hours" style="text-align: center !important; font-weight: bold !important;"><?php echo $totalconsCombined; ?></td>
                <td title="Invoiced" style="text-align: center !important; font-weight: bold !important;"><?php echo $totalconsInvoiceAmount > 0 ? number_format($totalconsInvoiceAmount, 2) : ''; ?></td>
                <td title="Quality Errors" style="text-align: center !important; font-weight: bold !important;"><?php echo $k_QualityErrorPercentage; ?></td>
                <td title="Productivity%" style="text-align: center !important; font-weight: bold !important;"><?php echo round($consproductivityPercentage). '%'; ?></td>
                <td title="Project General%" style="text-align: center !important; font-weight: bold !important;"><?php echo round($consprojectgeneralPercentage). '%'; ?></td>
                <td title="Difference" style="color: <?php echo $consClientDifference >= 0 ? '#28a745' : '#dc3545'; ?>; text-align: center !important; font-weight: bold !important;"><?php echo number_format($consClientDifference, 2); ?></td>
            </tr>

            <?php foreach ($data['projects'] as $proj): ?>
        
        <?php
                // Use production hours from query result (already calculated) - avoids N+1 queries
                $productiveHours = isset($proj->total_hours) ? (float)$proj->total_hours : 0;
        
                // Get PG (Project General) hours for this specific project - calculated per project based on project ID
                $generalHours = isset($proj->general_hours) ? $proj->general_hours : 0;
               
        $combinedHours = $productiveHours + $generalHours;
        
        $productivityPercentage = $combinedHours > 0 ? ($productiveHours / $combinedHours) * 100 : 0;
        $projectgeneralPercentage = $combinedHours > 0 ? ($generalHours / $combinedHours) * 100 : 0;
        
        // Calculate difference in hours for project row
        $consProjectInvoiceAmount = isset($proj->project_invoice_amt) && !empty($proj->project_invoice_amt) ? (float)$proj->project_invoice_amt : 0;
        $consProjectDifference = $combinedHours - $consProjectInvoiceAmount;
        
        // Calculate individual project QA percentage - only if date matches date range (if date range is provided)
        $k_QualityErrorPercentage_22 = '--';
        if (!empty($from_date) && !empty($to_date)) {
            // If date range is provided, check if quality error log date matches
            if (!empty($proj->analyzer_report_date)) {
                $errorDate = strtotime($proj->analyzer_report_date);
                $fromDate = strtotime($from_date);
                $toDate = strtotime($to_date);
                if ($errorDate >= $fromDate && $errorDate <= $toDate) {
                    $analyzerErrors = isset($proj->analyzer_num_of_errors) ? $proj->analyzer_num_of_errors : 0;
                    $reviewerErrors = isset($proj->reviewer_num_of_errors) ? $proj->reviewer_num_of_errors : 0;
                    $projectTotalErrors = $analyzerErrors + $reviewerErrors;
                    $k_QualityErrorPercentage_22 = (100 - $projectTotalErrors) . '%';
                }
            }
        } else {
            // If no date range, show quality error if it exists
            $analyzerErrors = isset($proj->analyzer_num_of_errors) ? $proj->analyzer_num_of_errors : 0;
            $reviewerErrors = isset($proj->reviewer_num_of_errors) ? $proj->reviewer_num_of_errors : 0;
            $projectTotalErrors = $analyzerErrors + $reviewerErrors;
            $k_QualityErrorPercentage_22 = (100 - $projectTotalErrors) . '%';
        }
                ?>
                <tr class="project-row project-<?php echo md5($clientId); ?>" style="display: none;"
                    data-client="<?php echo $data['client_name']; ?>"
                    data-project="<?php echo $proj->project_name; ?>"
                    data-manager="<?php echo $proj->pm_name; ?>"
                    data-department="<?php echo $proj->department; ?>">
                    <td title="Project Name" class="project-name-cell"><?php echo htmlspecialchars($proj->project_name); ?></td>
                    <td title="Project Manager">
  <?php echo explode(' ', trim($proj->pm_name))[0]; ?>
</td>

                    <td title="Department"><?php echo $proj->department; ?></td>
                    <td title="Billable"><?php echo $proj->man_days; ?></td>
                    <td title="Start Date">
                        <?php 
                        $startDate = '--';
                        if (!empty($proj->project_start_date) && $proj->project_start_date != '0000-00-00' && $proj->project_start_date != '0000-00-00 00:00:00') {
                            $startTimestamp = strtotime($proj->project_start_date);
                            if ($startTimestamp !== false && $startTimestamp > 0) {
                                $formattedDate = date('d-M-Y', $startTimestamp);
                                // Check if formatted date is not 01-Jan-1970
                                if ($formattedDate != '01-Jan-1970') {
                                    $startDate = $formattedDate;
                                }
                            }
                        }
                        echo $startDate;
                        ?>
                    </td>
                    <td title="End Date">
                        <?php 
                        $endDate = '';
                        // Use last_work_date from emp_record_details if available, otherwise fall back to project_end_date
                        $endDateToUse = !empty($proj->last_work_date) ? $proj->last_work_date : (isset($proj->project_end_date) ? $proj->project_end_date : null);
                        if (!empty($endDateToUse) && $endDateToUse != '0000-00-00' && $endDateToUse != '0000-00-00 00:00:00') {
                            $endTimestamp = strtotime($endDateToUse);
                            if ($endTimestamp !== false && $endTimestamp > 0) {
                                $formattedDate = date('d-M-Y', $endTimestamp);
                                // Check if formatted date is not 01-Jan-1970
                                if ($formattedDate != '01-Jan-1970') {
                                    $endDate = $formattedDate;
                                }
                            }
                        }
                        echo $endDate;
                        ?>
                    </td>
                    <td title="Production Hours"><?php echo $productiveHours; ?></td>
                    <td title="Project General Hours"><?php echo $generalHours; ?></td>
                    
                     <td title="Total Hours"><?php echo $combinedHours; ?></td>
                    <td title="Invoiced"><?php 
                        $invoiceAmt = isset($proj->project_invoice_amt) ? (float)$proj->project_invoice_amt : 0;
                        echo $invoiceAmt > 0 ? number_format($invoiceAmt, 2) : '';
                    ?></td>
                    <td title="Quality Errors"><?php echo $k_QualityErrorPercentage_22; ?></td>
                    <td title="Productivity%"><?php echo round($productivityPercentage). '%'; ?></td>
                    <td title="Project General%"><?php echo round($projectgeneralPercentage). '%'; ?></td>
                <td title="Difference" style="color: <?php echo $consProjectDifference >= 0 ? '#28a745' : '#dc3545'; ?>; font-weight: bold;"><?php echo number_format($consProjectDifference, 2); ?></td>
                    
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </tbody>
</table>

    <script>        
    $('#month_id').select2(); // Autosuggest list
    </script>
                </div>                   

                <!-- End of Page Content -->
        </div>
      </div>
    </div></div>

      <script>
        
$('#month_id,#projectId,#client_id,#repId').select2(); // Autosuggest list

   

function redirectToClient() {
    // Add animation effect (optional)
    const button = document.querySelector('.four-report-btn');
    button.classList.add('active');
    
    // Get current month (0-11, so we add 1 to make it 1-12)
    const currentMonth = new Date().getMonth() + 1;
    // Subtract 1 to get previous month (as in your original code)
    const monthValue = currentMonth - 1;
    
    // Wait for 300ms (like toggle switch) and then redirect
    setTimeout(function() {
        window.location.href = "<?php echo base_url('kpi_reports/clientReport?month_id='); ?>" + monthValue;
    }, 300);
}

function redirectToCConsolidated() {
    // Safely select the Consolidated KPI Report button
    const button = document.querySelector('.four-report-btn');
    
        button.classList.add('active');
    

    setTimeout(function() {
        window.location.href = "<?php echo base_url('kpi_reports/clientconsReport');?>";
    }, 300);
}
    </script>
      
<style>
   th, td {
    padding: 8px;
    text-align: center;
}

/* 14 columns: Client Name, PM, Dept, Billing, Start, End, P, PG, TH, I, QA, P%, PG%, Diff */
th:nth-child(1), td:nth-child(1) {
    width: 200px; /* Client / Project name column */
}
th:nth-child(2), td:nth-child(2) {
    width: 105px; /* PM */
}
th:nth-child(3), th:nth-child(4), th:nth-child(5),
td:nth-child(3), td:nth-child(4), td:nth-child(5) {
    width: 105px;
}

th:nth-child(6), th:nth-child(7),
td:nth-child(6), td:nth-child(7) {
    width: 110px; /* Start Date and End Date */
}
       
th:nth-child(8), th:nth-child(9), th:nth-child(10), th:nth-child(11),
td:nth-child(8), td:nth-child(9), td:nth-child(10), td:nth-child(11) {
    width: 80px;
}

th:nth-child(12), th:nth-child(13), th:nth-child(14),
td:nth-child(12), td:nth-child(13), td:nth-child(14) {
    width: 100px;
}
   
</style>
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
    
<!-- Inlude Footer here END-->
    