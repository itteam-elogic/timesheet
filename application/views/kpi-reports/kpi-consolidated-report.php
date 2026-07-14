<head>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400&display=swap" rel="stylesheet"> <!-- Roboto Condensed for numbers -->
</head>

<!-- Include ExcelJS Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>




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


?>




    <link href="<?php echo HTTP_CSS_PATH; ?>kpi-style.css" rel="stylesheet" />
<body id="kpiPage">
<div class="content-wrapper">
  <div class="page-title">      
    <div>
      <h1>Manage KPI</h1>
    </div>
    <div class="generate-report-btn consolidated-export-report-btn" style="margin-left: -45px;">
    <?php if (isset($datesMatch) && $datesMatch): ?>
    <button id="generateBtnConsolidated" onclick="downloadExcelConsolidated()" class="btn btn-success">
        <i class="fa fa-download"></i>
        <span id="btnTextConsolidated">Export Report</span>
        <span id="spinnerConsolidated" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    </button>
    <?php else: ?>
    <button id="generateBtnConsolidated" class="btn btn-secondary" disabled title="Quality error log data does not match the selected dates">
        <span id="btnTextConsolidated">Export Report (Disabled)</span>
    </button>
    <?php endif; ?>
</div>

<script>
function downloadExcelConsolidated() {
    const btn = document.getElementById('generateBtnConsolidated');
    const btnText = document.getElementById('btnTextConsolidated');
    const spinner = document.getElementById('spinnerConsolidated');

    // Change button style to show loading
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
    if (typeof syncConsolidatedMonthForYearAll === 'function') {
        syncConsolidatedMonthForYearAll();
    }
    const fromYear = document.getElementById('from_year') ? document.getElementById('from_year').value : '';
    const fromMonth = document.getElementById('from_month') ? document.getElementById('from_month').value : '';
    const toYear = document.getElementById('to_year') ? document.getElementById('to_year').value : '';
    const toMonth = document.getElementById('to_month') ? document.getElementById('to_month').value : '';

    let url = '<?php echo base_url('kpi_reports/generateConsolidatedReportExcel'); ?>?';
    const params = [];
    
    if (search) {
        params.push('search=' + encodeURIComponent(search));
    }
    if (department) {
        params.push('department=' + encodeURIComponent(department));
    }
    if (fromYear && toYear) {
        params.push('from_year=' + encodeURIComponent(fromYear));
        params.push('from_month=' + encodeURIComponent(String(fromYear).toUpperCase() === 'ALL' ? '' : (fromMonth || '')));
        params.push('to_year=' + encodeURIComponent(toYear));
        params.push('to_month=' + encodeURIComponent(String(toYear).toUpperCase() === 'ALL' ? '' : (toMonth || '')));
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
            btn.disabled = false;
            btnText.textContent = 'Export Report';
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
        Individual KPI
    </button>

    <button onclick="redirectToConsolidatedReport()" class="btn btn-primary" style="background-color: #014b88; font-weight: bold; border: 2px solid white;">
        PM Wise KPI
    </button>

</div>

   <!------------------------------------------------------------------------------SEARCH------------------------------------------------------------------>      
            
<?php if (in_array($this->session->userdata['logged_in_timesheet']['user_type'], ['admin', 'business_head', 'manager'])): ?>            
<form method="get" action="<?= base_url('kpi_reports/consolidatedReport') ?>" id="dateRangeForm" onsubmit="return prepareConsolidatedDateFormForSubmit();">
    <div class="row kpi-consolidated-filter-card" style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <!-- Search Box (Above Date Range) -->
        <div class="col-md-12" style="margin-bottom: 15px;">
            <div class="form-group kpi-search-row" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <label class="control-label" for="search" style="margin-right: 10px; min-width: 90px; font-weight: 600; color: #014b88; font-size: 14px;">Search:</label>
                <div class="kpi-search-wrap" style="max-width: 320px; width: 100%;">
                    <select name="search[]" id="search" class="form-control kpi-search-select" multiple="multiple"
                            style="background-color: #fff; border: 1px solid #d1d9e0; color: #014b88; font-weight: 500; width: 100%; font-size: 13px; border-radius: 8px;">
                        <?php
                        // Pre-selected manager names only (full list loaded via AJAX)
                        $searchOptionValuesLower = array();
                        $addOption = function ($name) use (&$searchOptionValuesLower) {
                            $name = trim($name);
                            if ($name === '') {
                                return;
                            }
                            $key = mb_strtolower($name);
                            if (isset($searchOptionValuesLower[$key])) {
                                return;
                            }
                            $searchOptionValuesLower[$key] = $name;
                            echo '<option value="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '" selected>' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</option>';
                        };
                        if (!empty($search)) {
                            $searchTermsForOptions = is_array($search) ? $search : array_values(array_filter(array_map('trim', explode(',', $search))));
                            foreach ($searchTermsForOptions as $term) {
                                $addOption($term);
                            }
                        }
                        ?>
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
                    $allDepts = function_exists('ts_primary_delivery_departments')
                        ? ts_primary_delivery_departments()
                        : ['Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP'];
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
                    <?php foreach ($allDepts as $deptOption): ?>
                        <option value="<?= htmlspecialchars($deptOption, ENT_QUOTES, 'UTF-8'); ?>" <?= in_array($deptOption, $selectedDepartments, true) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($deptOption, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <!-- Date Range Selection (Below Department) -->
        <?php
        $kpiStartYear = 2010;
        $kpiEndYear = (int) date('Y');
        if (isset($from_year) && isset($to_year) && $from_year !== '' && $to_year !== '') {
            $filterFromYearIsAll = (strtoupper(trim((string) $from_year)) === 'ALL');
            $filterToYearIsAll = (strtoupper(trim((string) $to_year)) === 'ALL');
            $filterFromYear = $filterFromYearIsAll ? 'ALL' : (int) $from_year;
            $filterFromMonth = ($filterFromYearIsAll || empty($from_month)) ? 0 : (int) $from_month;
            $filterToYear = $filterToYearIsAll ? 'ALL' : (int) $to_year;
            $filterToMonth = ($filterToYearIsAll || empty($to_month)) ? 0 : (int) $to_month;
        } else {
            $filterFromYearIsAll = false;
            $filterToYearIsAll = false;
            $filterFromYear = $kpiEndYear;
            $filterFromMonth = 1;
            $filterToYear = $kpiEndYear;
            $filterToMonth = (int) date('n');
        }
        $kpiMonthLabels = array(
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        );
        ?>
        <div class="col-md-12">
            <div class="form-group kpi-ym-range-row">
                <div class="kpi-ym-range-panels">
                    <div class="kpi-ym-panel">
                        <div class="kpi-ym-panel-title">From</div>
                        <div class="kpi-ym-panel-fields">
                            <div class="kpi-ym-select-wrap">
                            <select name="from_year" id="from_year" class="form-control kpi-ym-select kpi-ym-year-select kpi-ym-highlightable" title="From year">
                                <option value="ALL" <?= ($filterFromYearIsAll ? 'selected' : '') ?>>ALL</option>
                                <?php for ($y = $kpiEndYear; $y >= $kpiStartYear; $y--): ?>
                                <option value="<?= $y ?>" <?= (!$filterFromYearIsAll && (int) $filterFromYear === $y) ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                            </div>
                            <div class="kpi-ym-select-wrap">
                            <select name="from_month" id="from_month" class="form-control kpi-ym-select kpi-ym-month-select kpi-ym-highlightable" title="From month">
                                <option value="">Month</option>
                                <?php foreach ($kpiMonthLabels as $num => $label): ?>
                                <option value="<?= $num ?>" <?= ($filterFromMonth > 0 && (int) $num === $filterFromMonth) ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                            </div>
                        </div>
                    </div>

                    <div class="kpi-ym-panel">
                        <div class="kpi-ym-panel-title">To</div>
                        <div class="kpi-ym-panel-fields">
                            <div class="kpi-ym-select-wrap">
                            <select name="to_year" id="to_year" class="form-control kpi-ym-select kpi-ym-year-select kpi-ym-highlightable" title="To year">
                                <option value="ALL" <?= ($filterToYearIsAll ? 'selected' : '') ?>>ALL</option>
                                <?php for ($y = $kpiEndYear; $y >= $kpiStartYear; $y--): ?>
                                <option value="<?= $y ?>" <?= (!$filterToYearIsAll && (int) $filterToYear === $y) ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                            </div>
                            <div class="kpi-ym-select-wrap">
                            <select name="to_month" id="to_month" class="form-control kpi-ym-select kpi-ym-month-select kpi-ym-highlightable" title="To month">
                                <option value="">Month</option>
                                <?php foreach ($kpiMonthLabels as $num => $label): ?>
                                <option value="<?= $num ?>" <?= ($filterToMonth > 0 && (int) $num === $filterToMonth) ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="kpi-ym-range-actions">
                    <button type="submit" class="btn btn-info kpi-ym-btn-search">
                        <i class="fa fa-filter"></i> Search
                    </button>
                    <a href="<?php echo base_url('kpi_reports/consolidatedReport'); ?>" class="kpi-ym-clear-link">
                        <button type="button" class="btn kpi-ym-btn-clear">
                            <i class="fa fa-refresh"></i> Clear All Filters
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
<?php endif; ?>
            
            
<script>
    function syncConsolidatedYmSelectHighlight($select) {
        var val = $select.val();
        var $wrap = $select.closest('.kpi-ym-select-wrap');
        if (!$wrap.length) {
            return;
        }
        if (val !== null && String(val).trim() !== '') {
            $wrap.addClass('kpi-ym-wrap-selected');
        } else {
            $wrap.removeClass('kpi-ym-wrap-selected');
        }
    }

    function syncAllConsolidatedYmHighlights() {
        if (typeof $ === 'undefined') {
            return;
        }
        $('.kpi-ym-highlightable').each(function() {
            syncConsolidatedYmSelectHighlight($(this));
        });
    }

    function clearConsolidatedMonthIfYearAll(yearSelectId, monthSelectId) {
        var yearEl = document.getElementById(yearSelectId);
        var monthEl = document.getElementById(monthSelectId);
        if (!monthEl) return;
        var yearVal = yearEl ? yearEl.value : '';
        if (yearVal && String(yearVal).toUpperCase() === 'ALL') {
            monthEl.value = '';
        }
        if (typeof $ !== 'undefined') {
            syncConsolidatedYmSelectHighlight($(monthEl));
        }
    }

    function syncConsolidatedMonthForYearAll() {
        clearConsolidatedMonthIfYearAll('from_year', 'from_month');
        clearConsolidatedMonthIfYearAll('to_year', 'to_month');
    }

    function prepareConsolidatedDateFormForSubmit() {
        syncConsolidatedMonthForYearAll();
        var fromYear = document.getElementById('from_year') ? document.getElementById('from_year').value : '';
        var fromMonth = document.getElementById('from_month') ? document.getElementById('from_month').value : '';
        var toYear = document.getElementById('to_year') ? document.getElementById('to_year').value : '';
        var toMonth = document.getElementById('to_month') ? document.getElementById('to_month').value : '';
        var fromYearAll = fromYear && String(fromYear).toUpperCase() === 'ALL';
        var toYearAll = toYear && String(toYear).toUpperCase() === 'ALL';

        if (!fromYear || !toYear) {
            alert('Please select From and To year.');
            return false;
        }
        if (!fromYearAll && (!fromMonth || String(fromMonth).trim() === '')) {
            alert('Please select From month or choose ALL for year.');
            return false;
        }
        if (!toYearAll && (!toMonth || String(toMonth).trim() === '')) {
            alert('Please select To month or choose ALL for year.');
            return false;
        }
        return true;
    }

    (function() {
        syncConsolidatedMonthForYearAll();
    })();

    function removeFiltersAndReload() {
        // Clear all filters and reload page
        var url = new URL(window.location.href);
        
        // Remove all filter parameters
        url.searchParams.delete('search');
        url.searchParams.delete('department');
        url.searchParams.delete('from_date');
        url.searchParams.delete('to_date');
        url.searchParams.delete('from_year');
        url.searchParams.delete('from_month');
        url.searchParams.delete('to_year');
        url.searchParams.delete('to_month');
        
        // Reload the page with the updated URL
        window.location.href = url.toString();
    }
</script>

<style>
.kpi-ym-range-row {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: nowrap;
}
.kpi-ym-range-panels {
    display: flex;
    align-items: stretch;
    gap: 12px;
    flex-shrink: 0;
}
.kpi-ym-panel {
    background: #ffffff;
    border: 1px solid #d8dee6;
    border-radius: 10px;
    padding: 10px 14px 12px;
    min-width: 280px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}
.kpi-ym-panel-title {
    font-weight: 600;
    color: #333;
    font-size: 14px;
    line-height: 1.2;
    margin-bottom: 10px;
}
.kpi-ym-panel-fields {
    display: flex;
    align-items: center;
    gap: 10px;
}
.kpi-ym-select-wrap {
    position: relative;
    flex: 0 0 auto;
}
.kpi-ym-select-wrap.kpi-ym-wrap-selected {
    background-color: #673ab7;
    border-radius: 8px;
    border: 2px solid #e2e2e2;
}
.kpi-ym-select-wrap.kpi-ym-wrap-selected .kpi-ym-select {
    background-color: transparent !important;
    border-color: transparent !important;
    color: #fff !important;
    font-weight: 600;
    box-shadow: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23ffffff' d='M1.41 0L6 4.58 10.59 0 12 1.41l-6 6-6-6z'/%3E%3C/svg%3E");
}
.kpi-ym-select-wrap.kpi-ym-wrap-selected .kpi-ym-select:focus {
    border-color: transparent !important;
    box-shadow: none;
}
.kpi-ym-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    color-scheme: light;
    background-color: #ffffff;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23666' d='M1.41 0L6 4.58 10.59 0 12 1.41l-6 6-6-6z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 12px 8px;
    border: 1px solid #cfd6df;
    color: #4a5568;
    font-weight: 500;
    font-size: 14px;
    height: 38px;
    padding: 6px 32px 6px 12px;
    border-radius: 8px;
    box-shadow: none;
    cursor: pointer;
}
.kpi-ym-select:focus {
    border-color: #014b88;
    outline: none;
    box-shadow: 0 0 0 2px rgba(1, 75, 136, 0.12);
}
.kpi-ym-select option {
    background-color: #ffffff;
    color: #333333;
    font-weight: normal;
}
.kpi-ym-select-wrap.kpi-ym-wrap-selected .kpi-ym-select option {
    background-color: #ffffff !important;
    color: #333333 !important;
}
.kpi-ym-year-select {
    width: 128px;
    min-width: 128px;
    flex: 1 1 128px;
}
.kpi-ym-month-select {
    width: 128px;
    min-width: 128px;
    flex: 1 1 128px;
}
.kpi-ym-range-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-left: auto;
    flex-shrink: 0;
}
.kpi-ym-btn-search {
    background-color: #014b88;
    color: #fff;
    font-weight: bold;
    padding: 8px 20px;
    white-space: nowrap;
    border: none;
}
.kpi-ym-btn-clear {
    background-color: #ea580c;
    color: #fff;
    font-weight: bold;
    padding: 8px 20px;
    white-space: nowrap;
    border: none;
}
.kpi-ym-clear-link {
    text-decoration: none;
}
@media (max-width: 1200px) {
    .kpi-ym-range-row {
        flex-wrap: wrap;
        align-items: flex-start;
    }
    .kpi-ym-range-actions {
        margin-left: 0;
        width: 100%;
        padding-left: 0;
    }
}
@media (max-width: 768px) {
    .kpi-ym-range-panels {
        flex-direction: column;
        width: 100%;
    }
    .kpi-ym-panel {
        width: 100%;
        min-width: 0;
    }
    .kpi-ym-range-actions {
        padding-left: 0;
    }
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
$managerDeptTotals = isset($managerDeptTotals) ? $managerDeptTotals : array();
$managerSelfTotalsByDept = isset($managerSelfTotalsByDept) ? $managerSelfTotalsByDept : array();
$managerNamesById = isset($managerNamesById) ? $managerNamesById : array();
$departmentsToShow = isset($departmentsToShow) ? $departmentsToShow : array();
$monthYearPairs = isset($monthYearPairs) ? $monthYearPairs : array();
$departmentFilter = isset($department) ? $department : '';
$search = isset($search) ? $search : '';

$monthNames = array_map(function ($p) { return $p['month']; }, $monthYearPairs);
$monthNames = array_map('intval', array_filter($monthNames, 'is_numeric'));

$showAllDeptRows = empty($departmentFilter) && empty($search);
?>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="kpi-dept-summary-box">
            <h4 id="summaryHeading">KPI Report</h4>
            <div class="kpi-dept-summary-scroll">
                <table id="departmentTable" class="kpi-dept-summary-table kpi-dept-summary-with-mgr table table-bordered">
                    <thead>
                        <tr>
                            <th class="kpi-dept-col-dept">Department</th>
                            <th class="kpi-dept-col-mgr"><?php echo htmlspecialchars(kpi_consolidated_team_column_heading($search), ENT_QUOTES, 'UTF-8'); ?></th>
                            <th class="kpi-dept-col-hrs kpi-dept-hrs-group-start"><span class="kpi-th-line">Productivity</span><span class="kpi-th-line">Hours</span></th>
                            <th class="kpi-dept-col-hrs"><span class="kpi-th-line">Project General</span><span class="kpi-th-line">Hours</span></th>
                            <th class="kpi-dept-col-hrs"><span class="kpi-th-line">eLogic General</span><span class="kpi-th-line">Hours</span></th>
                            <th class="kpi-dept-col-hrs"><span class="kpi-th-line">Availability</span><span class="kpi-th-line">Hours</span></th>
                            <th class="kpi-dept-col-hrs"><span class="kpi-th-line">Utilization</span><span class="kpi-th-line">Hours</span></th>
                            <th class="kpi-dept-col-total"><span class="kpi-th-line">Total</span><span class="kpi-th-line">Hours</span></th>
                            <th class="kpi-dept-col-pct kpi-dept-pct-group-start"><span class="kpi-th-line">Productivity</span><span class="kpi-th-line">%</span></th>
                            <th class="kpi-dept-col-pct"><span class="kpi-th-line">Project General</span><span class="kpi-th-line">%</span></th>
                            <th class="kpi-dept-col-pct"><span class="kpi-th-line">eLogic General</span><span class="kpi-th-line">%</span></th>
                            <th class="kpi-dept-col-pct"><span class="kpi-th-line">Availability</span><span class="kpi-th-line">%</span></th>
                            <th class="kpi-dept-col-pct"><span class="kpi-th-line">Utilization</span><span class="kpi-th-line">%</span></th>
                        </tr>
                    </thead>
                    <tbody>
<?php
foreach ($departmentsToShow as $dept):
    $mgrBlocks = isset($managerDeptTotals[$dept]) && is_array($managerDeptTotals[$dept]) ? $managerDeptTotals[$dept] : array();
    $displayRows = kpi_consolidated_dept_summary_display_rows($dept, $mgrBlocks, $managerSelfTotalsByDept, $search, $showAllDeptRows);
    if (empty($displayRows)) {
        if (!$showAllDeptRows) {
            continue;
        }
        $displayRows = array(array('mgrId' => '', 'stats' => array('count' => 0, 'totalProd' => 0, 'totalGen' => 0, 'totalElog' => 0, 'totalHours' => 0, 'totalWorkHrs' => 0, 'totalUtilHours' => 0)));
    }
    $deptRowspan = count($displayRows);
    $mgrRowIndex = 0;
    foreach ($displayRows as $displayRow):
        $mgrId = $displayRow['mgrId'];
        $stats = $displayRow['stats'];
        $deptTotalProd = isset($stats['totalProd']) ? (float) $stats['totalProd'] : 0;
        $deptTotalGen = isset($stats['totalGen']) ? (float) $stats['totalGen'] : 0;
        $deptTotalElog = isset($stats['totalElog']) ? (float) $stats['totalElog'] : 0;
        $deptTotalHours = isset($stats['totalHours']) ? (float) $stats['totalHours'] : 0;
        $deptTotalWorkHrs = isset($stats['totalWorkHrs']) ? (float) $stats['totalWorkHrs'] : 0;
        $deptTotalUtilHours = isset($stats['totalUtilHours']) ? (float) $stats['totalUtilHours'] : 0;

        $productivity = $deptTotalHours > 0 ? round(($deptTotalProd / $deptTotalHours) * 100) : 0;
        $projectGen = $deptTotalHours > 0 ? round(($deptTotalGen / $deptTotalHours) * 100) : 0;
        $elogicGen = $deptTotalHours > 0 ? round(($deptTotalElog / $deptTotalHours) * 100) : 0;
        $availability = $deptTotalWorkHrs > 0 ? round(($deptTotalHours / $deptTotalWorkHrs) * 100) : 0;
        $utilization = $deptTotalHours > 0 ? round(($deptTotalUtilHours / $deptTotalHours) * 100) : 0;

        $mgrDisplay = kpi_consolidated_team_display_label($mgrId, '--', $managerNamesById, $search);
?>
                        <tr class="<?php echo $mgrRowIndex === 0 ? 'kpi-dept-group-start' : ''; ?>" data-department="<?php echo htmlspecialchars($dept, ENT_QUOTES, 'UTF-8'); ?>" data-manager-id="<?php echo htmlspecialchars((string) $mgrId, ENT_QUOTES, 'UTF-8'); ?>">
<?php if ($mgrRowIndex === 0): ?>
                            <td class="kpi-dept-col-dept" rowspan="<?php echo (int) $deptRowspan; ?>"><?php echo htmlspecialchars($dept, ENT_QUOTES, 'UTF-8'); ?></td>
<?php endif; ?>
                            <td class="kpi-dept-col-mgr"><?php echo htmlspecialchars($mgrDisplay, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="kpi-dept-col-hrs kpi-dept-bg-prod kpi-dept-hrs-group-start"><?php echo kpi_summary_hours_cell($deptTotalProd); ?></td>
                            <td class="kpi-dept-col-hrs kpi-dept-bg-proj"><?php echo kpi_summary_hours_cell($deptTotalGen); ?></td>
                            <td class="kpi-dept-col-hrs kpi-dept-bg-elog"><?php echo kpi_summary_hours_cell($deptTotalElog); ?></td>
                            <td class="kpi-dept-col-hrs kpi-dept-bg-avail"><?php echo kpi_summary_hours_cell($deptTotalHours); ?></td>
                            <td class="kpi-dept-col-hrs kpi-dept-bg-util"><?php echo kpi_summary_hours_cell($deptTotalUtilHours); ?></td>
                            <td class="kpi-dept-col-total kpi-dept-bg-total"><?php echo kpi_summary_hours_cell($deptTotalHours); ?></td>
                            <td class="kpi-dept-col-pct kpi-dept-bg-prod kpi-dept-pct-group-start"><?php echo $productivity; ?>%</td>
                            <td class="kpi-dept-col-pct kpi-dept-bg-proj"><?php echo $projectGen; ?>%</td>
                            <td class="kpi-dept-col-pct kpi-dept-bg-elog"><?php echo $elogicGen; ?>%</td>
                            <td class="kpi-dept-col-pct kpi-dept-bg-avail"><?php echo $availability; ?>%</td>
                            <td class="kpi-dept-col-pct kpi-dept-bg-util"><?php echo $utilization; ?>%</td>
                        </tr>
<?php
        $mgrRowIndex++;
    endforeach;
endforeach;
?>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
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
    // Sort rows by Reporting Manager (alphabetical), then Employee Name.
    $sortedKpiReports = is_array($getkpiReports) ? $getkpiReports : array();
    usort($sortedKpiReports, function($a, $b) use ($managerNamesById) {
        $managerA = '';
        $managerB = '';
        if (!empty($a->reporting_manger) && isset($managerNamesById[$a->reporting_manger])) {
            $managerA = (string)$managerNamesById[$a->reporting_manger];
        }
        if (!empty($b->reporting_manger) && isset($managerNamesById[$b->reporting_manger])) {
            $managerB = (string)$managerNamesById[$b->reporting_manger];
        }
        $cmp = strcasecmp($managerA, $managerB);
        if ($cmp !== 0) return $cmp;
        $nameA = isset($a->name) ? (string)$a->name : '';
        $nameB = isset($b->name) ? (string)$b->name : '';
        return strcasecmp($nameA, $nameB);
    });
    // Group by employee - loop through employees first
    $primaryDeliveryDepts = function_exists('ts_primary_delivery_departments')
        ? ts_primary_delivery_departments()
        : ['Architectural', 'Structural', '3D Visualization', '2D Auto CAD', 'MEP'];
    foreach ($sortedKpiReports as $kpiResult):
        if (!in_array($kpiResult->department, $primaryDeliveryDepts, true) || empty($kpiResult->reporting_manger)) {
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
#employeeTable th.sortable {
  cursor: pointer;
  user-select: none;
}

#employeeTable th,
#employeeTable td {
  padding: 8px;
  text-align: left;
}

#employeeTable th,
#employeeTable td {
  min-width: 100px;
}
#employeeTable th.sortable.active-sort {
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
            if (btn.textContent.trim() === 'Individual KPI') {
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
    #employeeTable th,
    #employeeTable td {
        padding: 8px;
        text-align: center;
    }
    #employeeTable th:nth-child(1),
    #employeeTable td:nth-child(1) {
        width: 110px;
    }
    #employeeTable th:nth-child(3),
    #employeeTable td:nth-child(3) {
        width: 65px;
    }
    #employeeTable th:nth-child(2),
    #employeeTable th:nth-child(5),
    #employeeTable td:nth-child(2),
    #employeeTable td:nth-child(5) {
        width: 100px;
    }
    #employeeTable th:nth-child(4),
    #employeeTable th:nth-child(6),
    #employeeTable th:nth-child(7),
    #employeeTable th:nth-child(8),
    #employeeTable th:nth-child(9),
    #employeeTable th:nth-child(10),
    #employeeTable th:nth-child(11),
    #employeeTable th:nth-child(12),
    #employeeTable th:nth-child(13),
    #employeeTable th:nth-child(14),
    #employeeTable th:nth-child(15),
    #employeeTable td:nth-child(4),
    #employeeTable td:nth-child(6),
    #employeeTable td:nth-child(7),
    #employeeTable td:nth-child(8),
    #employeeTable td:nth-child(9),
    #employeeTable td:nth-child(10),
    #employeeTable td:nth-child(11),
    #employeeTable td:nth-child(12),
    #employeeTable td:nth-child(13),
    #employeeTable td:nth-child(14),
    #employeeTable td:nth-child(15) {
        width: 90px;
    }
</style>

<script>
$(document).ready(function() {
    syncConsolidatedMonthForYearAll();
    syncAllConsolidatedYmHighlights();
    $('#from_year').on('change', function() {
        clearConsolidatedMonthIfYearAll('from_year', 'from_month');
        syncConsolidatedYmSelectHighlight($(this));
    });
    $('#to_year').on('change', function() {
        clearConsolidatedMonthIfYearAll('to_year', 'to_month');
        syncConsolidatedYmSelectHighlight($(this));
    });
    $('#from_month, #to_month').on('change', function() {
        syncConsolidatedYmSelectHighlight($(this));
    });

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

    // Search multi-select — managers only (AJAX)
    $('#search').select2({
        placeholder: 'Select manager names',
        allowClear: true,
        width: '100%',
        minimumInputLength: 0,
        ajax: {
            url: "<?php echo base_url('kpi_reports/autosuggest_employee_names_cons'); ?>",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { term: params.term || '' };
            },
            processResults: function (data) {
                var names = Array.isArray(data) ? data : [];
                return {
                    results: names.map(function (name) {
                        return { id: name, text: name };
                    })
                };
            },
            cache: true
        }
    });
});
</script>

<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
    