<head>
    <style>
        .highlighted-project {
            background-color: #d4edda !important;
        }
    </style>
</head>

<!-- Include ExcelJS Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

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
    
</style>

<?php $this->load->view('includes/cRMHeader');
$createdUser = $this->session->userdata['logged_in_timesheet']['empId'];
?>

<?php
if (!empty($_REQUEST['empId'])) {
    $getempId = implode(' ,', $_REQUEST['empId']);
} else {
    $getempId = 'all';
}

if (!empty($_REQUEST['repId'])) {
    $getrepId = implode(' ,', $_REQUEST['repId']);
} else {
    $getrepId = 'all';
}

$getListOfEmployees = $this->timesheet_login->getListOfEmpInformation();
$getListOfManagers = $this->timesheet_login->getReportingManagers(null);
?>

<link href="<?php echo HTTP_CSS_PATH; ?>kpi-style.css" rel="stylesheet" />
<body id="kpiPage" class="client-report-ep">
<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h1>Manage KPI</h1>
        </div>
        <div>
            <a class="btn btn-primary btn-flat" href="#" onclick="clearAllFilters(); return false;"><i class="fa fa-refresh"></i> Reset</a>
            <button type="button" id="generateBtn" onclick="downloadExcel()" class="btn btn-success btn-flat">
                <i class="fa fa-download"></i>
                <span id="btnText">Export Report</span>
                <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
        </div>
    </div>

<script>
// Shared filter prep for Search and Generate Report (year ALL clears month, etc.)
function prepareClientReportFilterFormForSubmit() {
    if (typeof $ === 'undefined' || !$('#filterForm').length) {
        return;
    }
    var vals = $("#department").val() || [];
    if (vals.indexOf("__all__") >= 0 && vals.length > 1) {
        vals = vals.filter(function(x) { return x !== "__all__"; });
        $("#department").val(vals).trigger("change");
    }
    if ($("#filter_project").length) {
        var projVals = $("#filter_project").val() || [];
        if (!$.isArray(projVals)) {
            projVals = projVals ? [projVals] : [];
        }
        projVals = $.grep(projVals, function(v) { return v && String(v).trim() !== ""; });
        if (projVals.length > 1) {
            projVals = [projVals[projVals.length - 1]];
        }
        if (projVals.length) {
            if (typeof ensureSelectOption === 'function') {
                ensureSelectOption($("#filter_project"), projVals[0], projVals[0]);
            }
            $("#filter_project").val(projVals);
        }
    }
    if ($("#from_year").length) {
        var fy = $("#from_year").val();
        if (fy && String(fy).toUpperCase() === "ALL") {
            $("#from_month").val(null).prop("disabled", true).trigger("change");
        } else {
            $("#from_month").prop("disabled", false);
        }
    }
    if ($("#to_year").length) {
        var ty = $("#to_year").val();
        if (ty && String(ty).toUpperCase() === "ALL") {
            $("#to_month").val(null).prop("disabled", true).trigger("change");
        } else {
            $("#to_month").prop("disabled", false);
        }
    }
}

// Generate Report Excel - use same filters as the on-screen grid (current URL or form)
function downloadExcel() {
    const btn = document.getElementById('generateBtn');
    const btnText = document.getElementById('btnText');
    const spinner = document.getElementById('spinner');

    btn.disabled = true;
    spinner.classList.remove('d-none');
    btnText.textContent = 'Downloading...';

    let url = '<?php echo base_url('kpi_reports/generateClientReportExcel'); ?>';
    // Prefer current page query string so Excel matches the searched grid exactly
    if (window.location.search && window.location.search.length > 1) {
        url += window.location.search;
    } else if (typeof $ !== 'undefined' && $('#filterForm').length) {
        prepareClientReportFilterFormForSubmit();
        var qs = $('#filterForm').serialize();
        if (qs) {
            url += '?' + qs;
        }
    }

    setTimeout(function() {
        window.location.href = url;
        setTimeout(function() {
            spinner.classList.add('d-none');
            btn.disabled = false;
            btnText.textContent = 'Export Report';
        }, 19000);
    }, 300);
}

function clearAllFilters() {
    window.location.href = "<?= base_url('kpi_reports/clientReport') ?>";
}
</script>

<div class="card">
    <h3 class="card-title"></h3>
    <div class="card-body">
        <div class="four-report-btn " style="margin-left: 9px;">
            <button onclick="redirectToClient()" class="btn btn-primary" style="background-color: #014b88; font-weight: bold; border: 2px solid white;">Client Report</button>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <h3 id="kpiReportHeading"></h3>
            </div>
            <script>
            $(document).ready(function() {
                if (typeof updateClientReportHeading === 'function') {
                    updateClientReportHeading();
                }
            });
            </script>
        </div>

<?php
$userType = $this->session->userdata['logged_in_timesheet']['user_type'];
if (!is_array($getempId)) {
    $getempId = [$getempId];
}
?>

<?php if (in_array($userType, ['admin', 'business_head', 'manager'])): ?>
<?php
    $clients_filter = isset($clients_filter) ? $clients_filter : array();
    $pms_filter = isset($pms_filter) ? $pms_filter : array();
    $project_filter = isset($project_filter) ? $project_filter : '';
    if ($project_filter === '' && isset($_GET['project'])) {
        $projectRaw = $_GET['project'];
        if (is_array($projectRaw)) {
            foreach ($projectRaw as $p) {
                $p = trim((string) $p);
                if ($p !== '') {
                    $project_filter = $p;
                    break;
                }
            }
        } elseif (trim((string) $projectRaw) !== '') {
            $project_filter = trim((string) $projectRaw);
        }
    }
    if (!is_array($clients_filter)) {
        $clients_filter = array();
    }
    if (!is_array($pms_filter)) {
        $pms_filter = array();
    }
?>
<style>
/* Execution Plan aligned styling for Client Report */
#kpiPage.client-report-ep .content-wrapper .card {
    border-radius: 10px;
    border: 1px solid rgba(0, 0, 0, 0.06);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
}
#kpiPage.client-report-ep .page-title .btn-flat {
    font-weight: 700;
    font-size: 13px;
    letter-spacing: 0.4px;
    text-transform: uppercase;
    border-radius: 8px;
    padding: 8px 16px;
    min-width: 110px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
}
#kpiPage.client-report-ep .page-title .btn-success.btn-flat {
    min-width: 130px;
}
#kpiPage.client-report-ep .ep-filter-refresh-btn {
    font-weight: 700;
    font-size: 13px;
    letter-spacing: 0.4px;
    text-transform: uppercase;
    border-radius: 8px;
    padding: 8px 16px;
    min-width: 110px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
}
#kpiPage.client-report-ep .ep-filter-refresh-btn:hover,
#kpiPage.client-report-ep .ep-filter-refresh-btn:focus {
    color: #fff;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18);
}
.client-report-filter-bar {
    background: #ffffff;
    border: 1px solid #e6eaef;
    border-radius: 10px;
    padding: 18px 20px 16px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
    border-top: none;
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
    background: transparent;
    border: none;
    border-radius: 0;
    padding: 0;
    min-height: 0;
}
.client-report-filter-grid .crf-field-label,
.client-report-filter-grid .crf-field-label label {
    display: block;
    font-weight: 700;
    font-size: 13px;
    letter-spacing: 0;
    text-transform: none;
    color: #2c3e50;
    margin-bottom: 6px;
}
.client-report-filter-grid .crf-dates-actions-row {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: 14px 20px;
    margin-top: 4px;
    padding-top: 10px;
    border-top: 1px solid #eceff3;
}
.client-report-filter-grid .crf-dates-compact {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: 12px;
    flex: 1 1 auto;
}
.client-report-filter-grid .kpi-ym-range-panels {
    display: flex;
    align-items: stretch;
    gap: 12px;
}
.client-report-filter-grid .kpi-ym-panel {
    background: #fafbfc;
    border: 1px solid #e6eaef;
    border-radius: 8px;
    padding: 10px 12px 8px;
    min-width: 280px;
    box-shadow: none;
}
.client-report-filter-grid .kpi-ym-panel-title {
    font-weight: 700;
    color: #2c3e50;
    font-size: 13px;
    line-height: 1.2;
    margin-bottom: 6px;
}
.client-report-filter-grid .kpi-ym-panel-fields {
    display: flex;
    align-items: center;
    gap: 10px;
}
.client-report-filter-grid .kpi-ym-select-wrap {
    position: relative;
    flex: 0 0 auto;
}
.client-report-filter-grid .kpi-ym-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
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
    cursor: pointer;
    transition: all 0.2s ease;
}
.client-report-filter-grid .kpi-ym-select:focus {
    border-color: #014b88;
    outline: none;
    box-shadow: 0 0 0 2px rgba(1, 75, 136, 0.12);
}
.client-report-filter-grid .kpi-ym-select option {
    background-color: #ffffff;
    color: #333333;
    font-weight: normal;
}
.client-report-filter-grid .kpi-ym-select-wrap.kpi-ym-wrap-selected {
    background-color: #6f42c1;
    border-radius: 8px;
    border: 2px solid #6f42c1;
}
.client-report-filter-grid .kpi-ym-select-wrap.kpi-ym-wrap-selected .kpi-ym-select {
    background-color: transparent !important;
    border-color: transparent !important;
    color: #fff !important;
    font-weight: 600;
    padding-right: 52px;
    box-shadow: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23ffffff' d='M1.41 0L6 4.58 10.59 0 12 1.41l-6 6-6-6z'/%3E%3C/svg%3E");
    background-position: right 10px center;
}
.client-report-filter-grid .kpi-ym-select-wrap.kpi-ym-wrap-selected .kpi-ym-select:focus {
    border-color: transparent !important;
    box-shadow: none;
}
.client-report-filter-grid .kpi-ym-clear-icon {
    position: absolute;
    right: 26px;
    top: 50%;
    transform: translateY(-50%);
    color: #fff;
    font-size: 14px;
    line-height: 1;
    cursor: pointer;
    display: none;
    z-index: 2;
    user-select: none;
}
.client-report-filter-grid .kpi-ym-year-select {
    width: 128px;
    min-width: 128px;
}
.client-report-filter-grid .kpi-ym-month-select {
    width: 128px;
    min-width: 128px;
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
@media (max-width: 900px) {
    .client-report-filter-grid .crf-dates-compact {
        flex-wrap: wrap;
    }
    .client-report-filter-grid .kpi-ym-range-panels {
        flex-direction: column;
        width: 100%;
    }
    .client-report-filter-grid .kpi-ym-panel {
        width: 100%;
        min-width: 0;
    }
}
.client-report-filter-grid .crf-btn-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    padding-top: 0;
    border-top: none;
}
.client-report-filter-grid .select2-container {
    width: 100% !important;
    max-width: 100%;
}
.client-report-filter-grid .kpi-ym-select-wrap .select2-container {
    width: 128px !important;
    min-width: 128px;
    max-width: 128px;
}
.client-report-filter-grid .kpi-ym-select-wrap .kpi-ym-clear-icon {
    display: none !important;
}
.client-report-filter-grid .select2-container .select2-selection--multiple,
.client-report-filter-grid .select2-container .select2-selection--single {
    min-height: 40px !important;
    border: 1px solid #d1d5db !important;
    border-radius: 8px !important;
    background: #fff !important;
}
.client-report-filter-grid .kpi-ym-select-wrap .select2-container .select2-selection--single {
    min-height: 38px !important;
    border-color: #cfd6df !important;
}
.client-report-filter-grid .kpi-ym-select-wrap .select2-container .select2-selection__rendered {
    line-height: 36px !important;
    padding-left: 12px !important;
    color: #4a5568 !important;
    font-weight: 500;
}
.client-report-filter-grid .kpi-ym-select-wrap .select2-container .select2-selection__arrow {
    height: 36px;
}
.client-report-filter-grid .kpi-ym-select-wrap .select2-container.cr-ym-selected-bg .select2-selection--single {
    background-color: #6f42c1 !important;
    border-color: #6f42c1 !important;
    box-shadow: 0 1px 3px rgba(111, 66, 193, 0.35);
}
.client-report-filter-grid .kpi-ym-select-wrap .select2-container.cr-ym-selected-bg .select2-selection__rendered {
    color: #fff !important;
    font-weight: 600;
}
.client-report-filter-grid .kpi-ym-select-wrap .select2-container.cr-ym-selected-bg .select2-selection__clear {
    color: #fff !important;
    margin-right: 6px;
}
.client-report-filter-grid .kpi-ym-select-wrap .select2-container.cr-ym-selected-bg .select2-selection__arrow b {
    border-top-color: #fff !important;
}
.client-report-filter-grid .select2-container--default.select2-container--focus .select2-selection--multiple,
.client-report-filter-grid .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #6f42c1 !important;
    box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.15);
}
.client-report-filter-grid .select2-container--default .select2-selection--multiple .select2-selection__choice,
.client-report-filter-grid .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    background-color: #6f42c1 !important;
    border-color: #6f42c1 !important;
    color: #fff !important;
}
.client-report-filter-grid .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #f5f3ff !important;
}
.client-report-filter-grid .select2-container--default .select2-results__option--highlighted[aria-selected],
.client-report-filter-grid .select2-container--default .select2-results__option[aria-selected="true"] {
    background-color: #6f42c1 !important;
    color: #fff !important;
}
.cr-client-report-ym-dropdown.select2-dropdown {
    border: 1px solid #6f42c1;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(111, 66, 193, 0.18);
}
.cr-client-report-ym-dropdown .select2-search--dropdown {
    padding: 8px;
}
.cr-client-report-ym-dropdown .select2-search__field {
    border: 1px solid #6f42c1 !important;
    border-radius: 6px !important;
    padding: 6px 8px !important;
    outline: none;
}
.cr-client-report-ym-dropdown .select2-search__field:focus {
    box-shadow: 0 0 0 2px rgba(111, 66, 193, 0.15);
}
.cr-client-report-ym-dropdown .select2-results__option--highlighted[aria-selected],
.cr-client-report-ym-dropdown .select2-results__option[aria-selected="true"] {
    background-color: #6f42c1 !important;
    color: #fff !important;
}
.client-report-filter-grid #filter_project + .select2-container .select2-selection--multiple {
    background: #fff !important;
    border-color: #d1d5db !important;
}
.client-report-filter-grid #filter_project + .select2-container .select2-selection--multiple .select2-selection__rendered {
    padding: 4px 8px !important;
}
</style>
<form method="get" action="<?= base_url('kpi_reports/clientReport') ?>" id="filterForm">
    <div class="client-report-filter-bar client-report-filter-grid">
        <div class="crf-grid-top">
            <div class="crf-field">
                <span class="crf-field-label"><label for="department">Department</label></span>
                <?php
                    $clientReportDepartments = function_exists('ts_primary_delivery_departments')
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
                    <?php foreach ($clientReportDepartments as $deptOption): ?>
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
                <select name="project[]" id="filter_project" class="form-control" multiple="multiple" style="width: 100%;" data-placeholder="All projects">
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
        <?php
        $kpiStartYear = 2010;
        $kpiEndYear = (int) date('Y');
        
        // Calculate previous month for default values
        $prevMonthTime = strtotime('first day of previous month');
        $prevMonth = (int) date('n', $prevMonthTime);
        $prevYear = (int) date('Y', $prevMonthTime);
        
        // UI dropdown state: only default year/month on fresh load (no date params submitted)
        $hasExplicitFromYear = isset($_GET['from_year']) && $_GET['from_year'] !== '';
        $hasExplicitToYear = isset($_GET['to_year']) && $_GET['to_year'] !== '';
        $hasExplicitFromMonth = isset($_GET['from_month']) && $_GET['from_month'] !== '';
        $hasExplicitToMonth = isset($_GET['to_month']) && $_GET['to_month'] !== '';
        $noDateParams = !$hasExplicitFromYear && !$hasExplicitFromMonth && !$hasExplicitToYear && !$hasExplicitToMonth;

        $uiFromYear = $hasExplicitFromYear ? trim((string) $_GET['from_year']) : '';
        $uiToYear = $hasExplicitToYear ? trim((string) $_GET['to_year']) : '';
        $uiFromMonth = $hasExplicitFromMonth ? (int) $_GET['from_month'] : 0;
        $uiToMonth = $hasExplicitToMonth ? (int) $_GET['to_month'] : 0;
        if ($uiFromYear === '' && $noDateParams) {
            $uiFromYear = (string) $prevYear;
        }
        if ($uiToYear === '' && $noDateParams) {
            $uiToYear = (string) $prevYear;
        }
        if ($uiFromMonth === 0 && $noDateParams) {
            $uiFromMonth = $prevMonth;
        }
        if ($uiToMonth === 0 && $noDateParams) {
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
        ?>
        <div class="crf-dates-actions-row">
            <div class="crf-dates-compact">
                <div class="kpi-ym-range-panels">
                    <div class="kpi-ym-panel">
                        <div class="kpi-ym-panel-title">From</div>
                        <div class="kpi-ym-panel-fields">
                            <div class="kpi-ym-select-wrap">
                                <select name="from_year" id="from_year" class="form-control kpi-ym-select kpi-ym-year-select kpi-ym-clearable" title="From year">
                                    <option value="">Year</option>
                                    <option value="ALL" <?= $uiFromYearIsAll ? 'selected' : '' ?>>All</option>
                                    <?php for ($y = $kpiEndYear; $y >= $kpiStartYear; $y--): ?>
                                    <option value="<?= $y ?>" <?= (!$uiFromYearIsAll && (int) $uiFromYear === $y) ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                                <span class="kpi-ym-clear-icon" onclick="clearClientReportYmSelect('from_year')">&times;</span>
                            </div>
                            <div class="kpi-ym-select-wrap">
                                <select name="from_month" id="from_month" class="form-control kpi-ym-select kpi-ym-month-select kpi-ym-clearable" title="From month">
                                    <option value="">Month</option>
                                    <?php foreach ($kpiMonthLabels as $num => $label): ?>
                                    <option value="<?= $num ?>" <?= ($uiFromMonth > 0 && (int) $num === $uiFromMonth) ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="kpi-ym-clear-icon" onclick="clearClientReportYmSelect('from_month')">&times;</span>
                            </div>
                        </div>
                    </div>
                    <div class="kpi-ym-panel">
                        <div class="kpi-ym-panel-title">To</div>
                        <div class="kpi-ym-panel-fields">
                            <div class="kpi-ym-select-wrap">
                                <select name="to_year" id="to_year" class="form-control kpi-ym-select kpi-ym-year-select kpi-ym-clearable" title="To year">
                                    <option value="">Year</option>
                                    <option value="ALL" <?= $uiToYearIsAll ? 'selected' : '' ?>>All</option>
                                    <?php for ($y = $kpiEndYear; $y >= $kpiStartYear; $y--): ?>
                                    <option value="<?= $y ?>" <?= (!$uiToYearIsAll && (int) $uiToYear === $y) ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                                <span class="kpi-ym-clear-icon" onclick="clearClientReportYmSelect('to_year')">&times;</span>
                            </div>
                            <div class="kpi-ym-select-wrap">
                                <select name="to_month" id="to_month" class="form-control kpi-ym-select kpi-ym-month-select kpi-ym-clearable" title="To month">
                                    <option value="">Month</option>
                                    <?php foreach ($kpiMonthLabels as $num => $label): ?>
                                    <option value="<?= $num ?>" <?= ($uiToMonth > 0 && (int) $num === $uiToMonth) ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="kpi-ym-clear-icon" onclick="clearClientReportYmSelect('to_month')">&times;</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="crf-btn-row">
                <button type="submit" class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i> Search</button>
                <button type="button" class="btn btn-primary btn-flat ep-filter-refresh-btn" onclick="clearAllFilters();"><i class="fa fa-refresh"></i> Reset</button>
            </div>
        </div>
    </div>
</form>
<?php endif; ?>

<?php // Search is handled server-side, no client-side filtering needed ?>

<script>
function syncClientReportYmSelect($select) {
    var val = $select.val();
    var hasValue = val !== null && val !== undefined && String(val).trim() !== '';
    var $container = $select.next('.select2-container');
    if ($container.length) {
        if (hasValue) {
            $container.addClass('cr-ym-selected-bg');
        } else {
            $container.removeClass('cr-ym-selected-bg');
        }
        return;
    }
    var $wrap = $select.closest('.kpi-ym-select-wrap');
    var $clear = $select.siblings('.kpi-ym-clear-icon');
    if (hasValue) {
        $wrap.addClass('kpi-ym-wrap-selected');
        $clear.show();
    } else {
        $wrap.removeClass('kpi-ym-wrap-selected');
        $clear.hide();
    }
}
function clearClientReportYmSelect(id) {
    var $select = $('#' + id);
    $select.val(null).trigger('change');
    syncClientReportYmSelect($select);
    if (id === 'from_year') {
        clearClientReportYmSelect('from_month');
    } else if (id === 'to_year') {
        clearClientReportYmSelect('to_month');
    }
    updateClientReportHeading();
}
function clearClientReportMonthIfYearAll(yearSelectId, monthSelectId) {
    var yearVal = $('#' + yearSelectId).val();
    if (yearVal && String(yearVal).toUpperCase() === 'ALL') {
        var $month = $('#' + monthSelectId);
        $month.val(null).trigger('change');
        syncClientReportYmSelect($month);
    }
}
function updateClientReportHeading() {
    var fromYear = $('#from_year').val();
    var toYear = $('#to_year').val();
    var fromMonthVal = $('#from_month').val();
    var toMonthVal = $('#to_month').val();
    var fromMonthText = fromMonthVal ? $('#from_month option:selected').text() : '';
    var toMonthText = toMonthVal ? $('#to_month option:selected').text() : '';
    if (fromYear && toYear) {
        var fromLabel = (fromMonthText ? fromMonthText + ' ' : '') + fromYear;
        var toLabel = (toMonthText ? toMonthText + ' ' : '') + toYear;
        $('#kpiReportHeading').text('Client Report (' + fromLabel + ' to ' + toLabel + ')');
    } else {
        $('#kpiReportHeading').text('Client Report');
    }
}
function initClientReportYmSelect2() {
    if (!$.fn.select2) {
        return;
    }
    var ymConfig = function(placeholder) {
        return {
            width: '128px',
            placeholder: placeholder,
            allowClear: true,
            minimumResultsForSearch: 0,
            dropdownCssClass: 'cr-client-report-ym-dropdown'
        };
    };
    $('#from_year, #to_year').select2(ymConfig('Year'));
    $('#from_month, #to_month').select2(ymConfig('Month'));
}
$(document).ready(function() {
    initClientReportYmSelect2();
    $('.kpi-ym-clearable').each(function() {
        syncClientReportYmSelect($(this));
    });
    clearClientReportMonthIfYearAll('from_year', 'from_month');
    clearClientReportMonthIfYearAll('to_year', 'to_month');
    updateClientReportHeading();
    $('#from_year').on('change', function() {
        clearClientReportMonthIfYearAll('from_year', 'from_month');
        syncClientReportYmSelect($(this));
        updateClientReportHeading();
    });
    $('#to_year').on('change', function() {
        clearClientReportMonthIfYearAll('to_year', 'to_month');
        syncClientReportYmSelect($(this));
        updateClientReportHeading();
    });
    $('#from_month, #to_month').on('change', function() {
        syncClientReportYmSelect($(this));
        updateClientReportHeading();
    });
});
</script>

<script>
function applySearchFilter() {
    function collectStructuredFilters() {
        var clientTerms = [];
        var pmTerms = [];
        var projectTerms = [];
        if (typeof $ !== "undefined" && $("#filter_clients").length) {
            var c = $("#filter_clients").val();
            if (c && c.length) {
                $.each(c, function(_, v) {
                    if (v && String(v).trim() !== "" && String(v) !== "__all__") {
                        clientTerms.push(String(v).trim().toLowerCase());
                    }
                });
            }
        }
        if (typeof $ !== "undefined" && $("#filter_pms").length) {
            var p = $("#filter_pms").val();
            if (p && p.length) {
                $.each(p, function(_, v) {
                    if (v && String(v).trim() !== "") {
                        pmTerms.push(String(v).trim().toLowerCase());
                    }
                });
            }
        }
        if (typeof $ !== "undefined" && $("#filter_project").length) {
            var pr = $("#filter_project").val();
            if ($.isArray(pr) && pr.length) {
                $.each(pr, function(_, v) {
                    if (v && String(v).trim() !== "") {
                        projectTerms.push(String(v).trim().toLowerCase());
                    }
                });
            } else if (pr && String(pr).trim() !== "") {
                projectTerms.push(String(pr).trim().toLowerCase());
            }
        }
        return {
            clientTerms: clientTerms.filter(function(t, i, a) { return t && a.indexOf(t) === i; }),
            pmTerms: pmTerms.filter(function(t, i, a) { return t && a.indexOf(t) === i; }),
            projectTerms: projectTerms.filter(function(t, i, a) { return t && a.indexOf(t) === i; })
        };
    }

    function matchesAny(haystack, terms) {
        haystack = (haystack || "").trim().toLowerCase();
        if (!haystack || !terms.length) {
            return false;
        }
        return terms.some(function(term) {
            return term && haystack.indexOf(term) !== -1;
        });
    }

    var filters = collectStructuredFilters();
    var hasStructured = filters.clientTerms.length || filters.pmTerms.length || filters.projectTerms.length;
    var projectTerm = filters.projectTerms.length ? filters.projectTerms[0] : "";

    document.querySelectorAll(".client-row").forEach(function(clientRow) {
        var clientName = (clientRow.getAttribute("data-client") || "").trim().toLowerCase();
        var managerName = (clientRow.getAttribute("data-manager") || "").trim().toLowerCase();
        var clientHash = clientRow.querySelector(".toggle-projects")
            ? clientRow.querySelector(".toggle-projects").getAttribute("data-client")
            : null;
        var projectRows = clientHash
            ? document.querySelectorAll(".project-" + clientHash)
            : [];

        var matchingProjects = [];
        var hasVisibleProjects = false;

        projectRows.forEach(function(projectRow) {
            var projectName = (projectRow.getAttribute("data-project") || "").trim().toLowerCase();
            var projectPM = (projectRow.getAttribute("data-manager") || "").trim().toLowerCase();
            var include = true;

            if (hasStructured) {
                if (projectTerm) {
                    include = projectName.indexOf(projectTerm) !== -1;
                    if (include && filters.clientTerms.length) {
                        include = matchesAny(clientName, filters.clientTerms);
                    }
                    if (include && filters.pmTerms.length) {
                        include = matchesAny(managerName, filters.pmTerms) || matchesAny(projectPM, filters.pmTerms);
                    }
                } else {
                    var passesClient = !filters.clientTerms.length || matchesAny(clientName, filters.clientTerms);
                    var clientPmMatches = filters.pmTerms.length && matchesAny(managerName, filters.pmTerms);
                    if (passesClient && !filters.pmTerms.length) {
                        include = true;
                    } else if (passesClient && clientPmMatches) {
                        include = true;
                    } else if (passesClient && filters.pmTerms.length) {
                        include = matchesAny(projectPM, filters.pmTerms);
                    } else if (!filters.clientTerms.length && clientPmMatches) {
                        include = true;
                    } else if (!filters.clientTerms.length && filters.pmTerms.length) {
                        include = matchesAny(projectPM, filters.pmTerms);
                    } else {
                        include = false;
                    }
                }
            }

            projectRow.style.display = "none";
            projectRow.classList.remove("highlighted-project");

            if (include) {
                matchingProjects.push(projectRow);
                if (projectTerm) {
                    projectRow.style.display = "";
                    projectRow.classList.add("highlighted-project");
                    hasVisibleProjects = true;
                }
            }
        });

        var shouldShowClient = false;
        if (!hasStructured) {
            shouldShowClient = true;
        } else if (projectTerm) {
            shouldShowClient = matchingProjects.length > 0;
        } else {
            var passesClient = !filters.clientTerms.length || matchesAny(clientName, filters.clientTerms);
            var clientPmMatches = filters.pmTerms.length && matchesAny(managerName, filters.pmTerms);
            shouldShowClient = (passesClient && !filters.pmTerms.length)
                || (passesClient && clientPmMatches)
                || (!filters.clientTerms.length && clientPmMatches)
                || matchingProjects.length > 0;
        }

        clientRow.style.display = shouldShowClient ? "" : "none";
        clientRow.classList.toggle("expanded", hasVisibleProjects);

        var toggleBtn = clientRow.querySelector(".toggle-projects");
        if (toggleBtn) {
            var icon = toggleBtn.querySelector("i");
            if (icon) {
                icon.className = hasVisibleProjects ? "fa fa-minus" : "fa fa-plus";
            }
        }

        clientRow.dataset.matchingProjects = matchingProjects
            .map(function(r) { return r.getAttribute("data-project"); })
            .join(",");
    });

    document.querySelectorAll(".toggle-projects").forEach(toggle => {
        toggle.addEventListener("click", function () {
            const clientRow  = this.closest(".client-row");
            const clientHash = this.getAttribute("data-client");
            const rows       = document.querySelectorAll(`.project-${clientHash}`);
            const isOpen     = clientRow.classList.contains("expanded");

            if (isOpen) {
                clientRow.classList.remove("expanded");
                rows.forEach(r => r.style.display = "none");
                var icon = this.querySelector("i");
                if (icon) icon.className = "fa fa-plus";
            } else {
                clientRow.classList.add("expanded");
                const allowed = (clientRow.dataset.matchingProjects || "")
                    .split(",")
                    .map(s => s.trim());

                rows.forEach(r => {
                    const name = r.getAttribute("data-project")?.trim() || "";
                    if (allowed.length === 0 || allowed.includes(name)) {
                        r.style.display = "";
                    } else {
                        r.style.display = "none";
                    }
                });
                var icon = this.querySelector("i");
                if (icon) icon.className = "fa fa-minus";
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
            var icon = this.querySelector("i");
            if (icon) icon.className = isHidden ? "fa fa-minus" : "fa fa-plus";
        });
    });
    // Apply client-side filter on load when search has value (matches server-filtered results)
    if (typeof applySearchFilter === 'function') {
        var hasFilter = false;
        if (typeof $ !== 'undefined') {
            var fc = $('#filter_clients').val();
            var fp = $('#filter_pms').val();
            var fpr = $('#filter_project').val();
            if ((fc && fc.length) || (fp && fp.length)
                || ($.isArray(fpr) && fpr.length)
                || (fpr && String(fpr).trim() !== '')) {
                hasFilter = true;
            }
        }
        if (hasFilter) {
            applySearchFilter();
        }
    }
});
</script>

<script>
$(function() {
    var suggestUrl = "<?php echo base_url('kpi_reports/autosuggest_project_names'); ?>";
    var cachedActiveClientsCount = null;
    var departmentFilterPrevSerialized = null;

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
                // Do not use traditional: true — it sends department=A&department=B and PHP keeps only
                // the last value, so multi-department filters break (client/project suggests go empty).
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

    function ensureSelectOption($select, value, text) {
        value = value == null ? "" : String(value).trim();
        if (value === "") {
            return;
        }
        text = text == null || String(text).trim() === "" ? value : String(text).trim();
        var exists = false;
        $select.find("option").each(function() {
            if (String($(this).val()) === value) {
                exists = true;
                return false;
            }
        });
        if (!exists) {
            $select.append(new Option(text, value, true, true));
        }
    }

    function syncProjectFilterSelect($select) {
        // Chip styling comes from shared multi-select CSS; keep at most one project.
        var vals = $select.val() || [];
        if ($.isArray(vals) && vals.length > 1) {
            vals = [vals[vals.length - 1]];
            $select.val(vals).trigger("change");
        }
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
        });
    }
    if ($("#filter_pms").length) {
        $("#filter_pms").select2(ajaxConfig("Managers", "All project managers", true, function() { return {}; }));
    }
    if ($("#filter_project").length) {
        var $projSel = $("#filter_project");
        var savedProject = ($projSel.val() || [])[0];
        if (!savedProject && $projSel.find("option:selected").length) {
            savedProject = $projSel.find("option:selected").first().val();
        }
        if (savedProject && String(savedProject).trim() !== "") {
            ensureSelectOption($projSel, savedProject, savedProject);
        }
        var projectCfg = ajaxConfig("Projects", "All projects", true, function() {
            var c = $("#filter_clients").val() || [];
            c = $.grep($.makeArray(c), function(x) { return x && String(x).trim() !== ""; });
            return { clients: c };
        });
        projectCfg.maximumSelectionLength = 1;
        $projSel.select2(projectCfg);
        if (savedProject && String(savedProject).trim() !== "") {
            ensureSelectOption($projSel, savedProject, savedProject);
            $projSel.val([savedProject]).trigger("change");
        }
        $projSel.on("select2:select", function(e) {
            if (e.params && e.params.data) {
                ensureSelectOption($projSel, e.params.data.id, e.params.data.text);
                $projSel.val([e.params.data.id]).trigger("change");
            }
            syncProjectFilterSelect($projSel);
        });
        $projSel.on("change select2:clear", function() {
            syncProjectFilterSelect($projSel);
        });
        syncProjectFilterSelect($projSel);
    }

    if ($("#department").length) {
        $("#department").select2({
            placeholder: "Select departments",
            allowClear: true,
            multiple: true,
            width: "100%"
        });
        var deptInit = $("#department").val() || [];
        if (!deptInit.length) {
            $("#department").val(["__all__"]).trigger("change");
        }
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

    $("#filterForm").on("submit", function() {
        prepareClientReportFilterFormForSubmit();
    });
});
</script>


<script>
function updateFormAction() {
    var selectedMonth = document.getElementById('month_id').value;
    var form = document.getElementById('monthForm');
    form.action = "<?php echo site_url('kpi_reports/monthWiseKpiReport'); ?>";
    form.submit();
}
</script>

    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-body">
            <div id="content-wrapper" class="d-flex flex-column">
                <div class="container-fluid">

<div class="row mt-4">
    <div class="col-md-12">
        <h3 id="kpiReportHeading"></h3>
    </div>
</div>

<?php
// Display month-wise summary at the top (for both single and multiple months)
$monthsDisplayText = isset($monthsDisplayText) ? $monthsDisplayText : '';
$monthsCovered = isset($monthsCovered) ? $monthsCovered : [];
if (!empty($monthsDisplayText)): 
    $monthCount = count($monthsCovered);
    $monthLabel = $monthCount == 1 ? 'Month' : 'Months';
?>
<div class="row mt-3 mb-3">
    <div class="col-md-12">
        <div class="alert alert-info" style="background-color: #5B9BD5; color: white; border: none; padding: 15px; border-radius: 5px; font-weight: bold; font-size: 14px;">
            <i class="fa fa-calendar" style="margin-right: 8px;"></i>
            <strong>Month-wise Report:</strong> <?php echo htmlspecialchars($monthsDisplayText); ?>
            <?php if ($monthCount > 1): ?>
                (<?php echo $monthCount; ?> <?php echo $monthLabel; ?>)
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$grouped = isset($grouped) && is_array($grouped) ? $grouped : [];
$monthWiseData = isset($monthWiseData) ? $monthWiseData : [];

// Check if we have month-wise data or regular grouped data
if (!empty($monthWiseData)) {
    // Month-wise data is already grouped by month in controller
    $hasMonthWiseData = true;
} else {
    // Use pre-grouped data from controller, or build from flat rows (legacy)
    if (empty($grouped) && !empty($clientInfo)) {
        foreach ($clientInfo as $row) {
            $grouped[$row->client_Id]['client_name'] = $row->client_name;
            $grouped[$row->client_Id]['department'] = $row->department;
            $grouped[$row->client_Id]['clientpm'] = $row->clientpm;
            $grouped[$row->client_Id]['client_pm_name'] = isset($row->client_pm_name) ? $row->client_pm_name : '';
            $grouped[$row->client_Id]['projects'][] = $row;
        }
        uasort($grouped, function($a, $b) {
            $nameA = isset($a['client_name']) ? (string)$a['client_name'] : '';
            $nameB = isset($b['client_name']) ? (string)$b['client_name'] : '';
            return strcasecmp($nameA, $nameB);
        });
    }
    $hasMonthWiseData = false;
}
?>

<?php
$deptKpiSummary = isset($deptKpiSummary) && is_array($deptKpiSummary) ? $deptKpiSummary : array('has_data' => false, 'rows' => array());
$deptKpiRows = (!empty($deptKpiSummary['has_data']) && !empty($deptKpiSummary['rows']) && is_array($deptKpiSummary['rows']))
    ? $deptKpiSummary['rows']
    : array();
$tdBase = 'text-align:center;padding:12px 14px;font-weight:600;border:1px solid #c9d4e2;font-size:15px;';
$deptKpiMonthKeys = array();
foreach ($deptKpiRows as $deptKpiScan) {
    if (!empty($deptKpiScan['month_key'])) {
        $deptKpiMonthKeys[(string) $deptKpiScan['month_key']] = true;
    }
}
$hasMultipleDeptKpiMonths = count($deptKpiMonthKeys) > 1;
$consolidatedDeptKpiRows = $hasMultipleDeptKpiMonths ? client_report_dept_kpi_consolidate_rows($deptKpiRows) : array();
$crMonthTabItems = array();
if ($hasMultipleDeptKpiMonths && !empty($monthsCovered) && is_array($monthsCovered)) {
    $crTabYears = array();
    foreach ($monthsCovered as $crTabInfo) {
        if (!empty($crTabInfo['year'])) {
            $crTabYears[(string) $crTabInfo['year']] = true;
        }
    }
    $crTabIncludeYear = count($crTabYears) > 1;
    foreach ($monthsCovered as $crTabKey => $crTabInfo) {
        $crMonthTabItems[(string) $crTabKey] = $crTabIncludeYear
            ? (isset($crTabInfo['label']) ? $crTabInfo['label'] : (string) $crTabKey)
            : (isset($crTabInfo['short']) ? $crTabInfo['short'] : (string) $crTabKey);
    }
}
if ($hasMultipleDeptKpiMonths && empty($crMonthTabItems)) {
    foreach ($deptKpiRows as $deptKpiScan) {
        $crTabKey = isset($deptKpiScan['month_key']) ? (string) $deptKpiScan['month_key'] : '';
        if ($crTabKey === '' || isset($crMonthTabItems[$crTabKey])) {
            continue;
        }
        $crMonthTabItems[$crTabKey] = isset($deptKpiScan['month']) ? (string) $deptKpiScan['month'] : $crTabKey;
    }
}
?>
<?php if (!empty($deptKpiRows)): ?>
<div class="row mt-3 mb-2 client-report-dept-kpi-wrap">
    <div class="col-md-12">
        <h4 class="client-report-dept-kpi-heading">Department &amp; Project Manager Client Summary Report</h4>
        <?php if ($hasMultipleDeptKpiMonths && !empty($crMonthTabItems)): ?>
        <div class="cr-month-tabs" id="clientReportMonthTabs" role="tablist">
            <button type="button" class="cr-month-tab active" data-month-tab="consolidated">Consolidated</button>
            <?php foreach ($crMonthTabItems as $crTabKey => $crTabLabel): ?>
            <button type="button" class="cr-month-tab" data-month-tab="<?= htmlspecialchars($crTabKey, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($crTabLabel, ENT_QUOTES, 'UTF-8') ?></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($hasMultipleDeptKpiMonths && !empty($consolidatedDeptKpiRows)): ?>
        <div class="client-report-dept-kpi-table-wrap cr-dept-kpi-view" id="crDeptKpiConsolidated">
            <table class="table table-bordered client-report-dept-kpi-table">
                <thead>
                    <tr>
                        <th class="cr-dept-kpi-th cr-dept-kpi-th-label">Departments</th>
                        <th class="cr-dept-kpi-th">Prod<br>Hours</th>
                        <th class="cr-dept-kpi-th">PG<br>Hours</th>
                        <th class="cr-dept-kpi-th">Utilization<br>Hours</th>
                        <th class="cr-dept-kpi-th">Productivity<br>%</th>
                        <th class="cr-dept-kpi-th">Project General<br>%</th>
                        <th class="cr-dept-kpi-th">Utilization<br>%</th>
                        <th class="cr-dept-kpi-th">Quality<br>%</th>
                        <th class="cr-dept-kpi-th">Invoiced<br>Hours</th>
                        <th class="cr-dept-kpi-th">Difference</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consolidatedDeptKpiRows as $deptRow): ?>
                    <tr>
                        <td style="text-align:left;font-weight:600;background:#eef2f7;padding:12px 14px;border:1px solid #c9d4e2;font-size:15px;color:#1f5076;"><?= htmlspecialchars(isset($deptRow['label']) ? $deptRow['label'] : '', ENT_QUOTES, 'UTF-8') ?></td>
                        <?= client_report_dept_kpi_metric_tds_html($deptRow, $tdBase) ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <div class="client-report-dept-kpi-table-wrap cr-dept-kpi-view<?= $hasMultipleDeptKpiMonths ? ' cr-view-hidden' : '' ?>" id="crDeptKpiMonthWise">
            <table class="table table-bordered client-report-dept-kpi-table">
                <thead>
                    <tr>
                        <th class="cr-dept-kpi-th cr-dept-kpi-th-month">Month</th>
                        <th class="cr-dept-kpi-th cr-dept-kpi-th-label">Departments</th>
                        <th class="cr-dept-kpi-th">Prod<br>Hours</th>
                        <th class="cr-dept-kpi-th">PG<br>Hours</th>
                        <th class="cr-dept-kpi-th">Utilization<br>Hours</th>
                        <th class="cr-dept-kpi-th">Productivity<br>%</th>
                        <th class="cr-dept-kpi-th">Project General<br>%</th>
                        <th class="cr-dept-kpi-th">Utilization<br>%</th>
                        <th class="cr-dept-kpi-th">Quality<br>%</th>
                        <th class="cr-dept-kpi-th">Invoiced<br>Hours</th>
                        <th class="cr-dept-kpi-th">Difference</th>
                    </tr>
                </thead>
                    <?php
                    $deptKpiRowCount = count($deptKpiRows);
                    $deptKpiIdx = 0;
                    while ($deptKpiIdx < $deptKpiRowCount):
                        $deptRow = $deptKpiRows[$deptKpiIdx];
                        $monthKey = isset($deptRow['month_key']) ? (string) $deptRow['month_key'] : '';
                        $monthName = isset($deptRow['month']) ? (string) $deptRow['month'] : '';
                        $monthSpan = 1;
                        while (($deptKpiIdx + $monthSpan) < $deptKpiRowCount) {
                            $nextKey = isset($deptKpiRows[$deptKpiIdx + $monthSpan]['month_key'])
                                ? (string) $deptKpiRows[$deptKpiIdx + $monthSpan]['month_key']
                                : '';
                            if ($nextKey !== $monthKey) {
                                break;
                            }
                            $monthSpan++;
                        }
                    ?>
                <tbody class="cr-dept-month-group" id="cr-month-group-<?= htmlspecialchars($monthKey, ENT_QUOTES, 'UTF-8') ?>" data-month-key="<?= htmlspecialchars($monthKey, ENT_QUOTES, 'UTF-8') ?>">
                    <?php
                        for ($deptKpiOffset = 0; $deptKpiOffset < $monthSpan; $deptKpiOffset++):
                            $deptRow = $deptKpiRows[$deptKpiIdx + $deptKpiOffset];
                    ?>
                    <tr>
                        <?php if ($deptKpiOffset === 0): ?>
                        <td rowspan="<?= (int) $monthSpan ?>" class="cr-dept-kpi-month-cell" data-month-key="<?= htmlspecialchars($monthKey, ENT_QUOTES, 'UTF-8') ?>" style="text-align:center;font-weight:700;background:#e8f0f8;padding:12px 14px;border:1px solid #c9d4e2;font-size:15px;color:#1f5076;vertical-align:middle;cursor:pointer;"><?= htmlspecialchars($monthName, ENT_QUOTES, 'UTF-8') ?></td>
                        <?php endif; ?>
                        <td style="text-align:left;font-weight:600;background:#eef2f7;padding:12px 14px;border:1px solid #c9d4e2;font-size:15px;color:#1f5076;"><?= htmlspecialchars(isset($deptRow['label']) ? $deptRow['label'] : '', ENT_QUOTES, 'UTF-8') ?></td>
                        <?= client_report_dept_kpi_metric_tds_html($deptRow, $tdBase) ?>
                    </tr>
                    <?php
                        endfor;
                    ?>
                </tbody>
                    <?php
                        $deptKpiIdx += $monthSpan;
                    endwhile;
                    ?>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
<style>
.client-report-dept-kpi-heading {
    text-align: center;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 14px;
    font-size: 18px;
}
.client-report-ep #content-wrapper {
    overflow: visible;
}
.client-report-dept-kpi-table-wrap {
    display: flex;
    justify-content: center;
    overflow-x: auto;
}
.client-report-dept-kpi-table-wrap.cr-view-hidden {
    display: none !important;
}
.client-report-dept-kpi-table tbody.cr-dept-month-group {
    display: table-row-group;
}
.client-report-dept-kpi-table tbody.cr-dept-month-group.cr-month-hidden {
    display: none !important;
}
.client-report-dept-kpi-table {
    width: 100%;
    min-width: 1180px;
    max-width: 100%;
    border-collapse: collapse;
    margin: 0 auto;
    table-layout: auto;
}
.client-report-dept-kpi-table .cr-dept-kpi-th {
    text-align: center;
    font-weight: 600;
    color: #fff;
    padding: 10px 8px;
    border: 1px solid #2c5aa0;
    font-size: 15px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    line-height: 1.25;
    white-space: normal;
    word-break: break-word;
    vertical-align: middle;
    background: linear-gradient(to bottom, #337ab7, #2c5aa0);
    min-width: 88px;
}
.client-report-dept-kpi-table .cr-dept-kpi-th-month {
    text-align: center;
    min-width: 110px;
}
.client-report-dept-kpi-table .cr-dept-kpi-th-label {
    text-align: left;
    min-width: 140px;
}
.client-report-dept-kpi-table tbody td {
    font-size: 15px;
}
.cr-month-tabs {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
    margin: 0 0 16px;
    position: sticky;
    top: 0;
    z-index: 20;
    background: #fff;
    padding: 8px 0;
}
.cr-month-tab {
    border: 1px solid #2c5aa0;
    background: #fff;
    color: #1f5076;
    font-weight: 600;
    font-size: 14px;
    padding: 7px 16px;
    border-radius: 20px;
    cursor: pointer;
    line-height: 1.2;
}
.cr-month-tab:hover {
    background: #e8f0f8;
}
.cr-month-tab.active {
    background: linear-gradient(to bottom, #337ab7, #2c5aa0);
    color: #fff;
    border-color: #2c5aa0;
}
.cr-dept-kpi-month-cell:hover {
    text-decoration: underline;
}
</style>

<?php if (empty($grouped) && empty($monthWiseData)): ?>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="alert alert-info" role="alert">
            <strong>No data found</strong> for the selected month(s).
        </div>
    </div>
</div>
<?php else: ?>
<div class="client-report-table-wrap">
<table id="employeeTable" class="table table-bordered client-report-table">
    <thead>
        <tr>
            <th title="Client Name">Client Name</th>
            <th title="Project Manager">Project<br>Manager</th>
            <th title="Month">Month</th>
            <th title="Department">Department</th>
            <th title="Start Date">Start<br>Date</th>
            <th title="End Date">End<br>Date</th>
            <th title="Billing">Billing</th>
            <th title="Production Hours">Production<br>Hours</th>
            <th title="Project General Hours">Project General<br>Hours</th>
            <th title="Total Hours">Total<br>Hours</th>
            <th title="Invoiced">Invoiced</th>
            <th title="Quality Errors">Quality<br>Errors</th>
            <th title="Productivity%">Productivity<br>%</th>
            <th title="Project General%">Project General<br>%</th>
            <th title="Difference">Difference</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        // Get date range from controller
        $from_date = isset($from_date) ? $from_date : '';
        $to_date = isset($to_date) ? $to_date : '';
        
        $clientReportMonthIncludeYear = false;
        if (!empty($monthsCovered) && is_array($monthsCovered)) {
            $gridMonthYears = array();
            foreach ($monthsCovered as $monthInfo) {
                if (!empty($monthInfo['year'])) {
                    $gridMonthYears[(string) $monthInfo['year']] = true;
                }
            }
            $clientReportMonthIncludeYear = count($gridMonthYears) > 1;
        }

        // Function to render client and project rows (reusable)
        function renderClientProjects($data, $clientId, $from_date, $to_date, $monthLabel = '', $includeYear = false, $monthKey = '') {
            $clientPmName = '--';
            if (!empty($data['client_pm_name'])) {
                $clientPmName = $data['client_pm_name'];
            }
            
            // Calculate totals for the date range
            $totalProductionHours = 0;
            $totalGeneralHours = 0;
            $totalProjectQualityErrors = 0;
            $totalProjects = count($data['projects']);
            $projectQualityErrors = [];
            
            foreach ($data['projects'] as $proj) {
                // Get production hours from the project data (already filtered by date range)
                $projectProductionHours = isset($proj->total_hours) ? $proj->total_hours : 0;
                $totalProductionHours += $projectProductionHours;
                
                // Get general hours from the project data
                $generalHours = isset($proj->general_hours) ? $proj->general_hours : 0;
                $totalGeneralHours += $generalHours;
                
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
            
            $totalCombined = $totalProductionHours + $totalGeneralHours;
            $totalproductivityPercentage = $totalCombined > 0 ? ($totalProductionHours / $totalCombined) * 100 : 0;
            $totalprojectgeneralPercentage = $totalCombined > 0 ? ($totalGeneralHours / $totalCombined) * 100 : 0;
            
            // Calculate total invoice amount for all projects
            $totalInvoiceAmount = 0;
            foreach ($data['projects'] as $proj) {
                if (isset($proj->project_invoice_amt) && !empty($proj->project_invoice_amt)) {
                    $totalInvoiceAmount += (float)$proj->project_invoice_amt;
                }
            }
            
            // Calculate difference in hours for client row
           // $clientDifference = $totalCombined - $totalInvoiceAmount;

            $clientDifference = $totalInvoiceAmount - $totalCombined;
            
            $billable = '';
            foreach ($data['projects'] as $proj) {
                if (!empty($proj->man_days)) {
                    $billable = $proj->man_days;
                    break;
                }
            }
            
            // Client row dates: MIN/MAX across all non-general projects (same as Execution Plan / Excel export).
            $clientDates = client_report_resolve_client_dates($data);
            $clientStartDateTs = $clientDates['start_ts'];
            $clientEndDateTs = $clientDates['end_ts'];
            
            $monthNameDisplay = client_report_month_display_name($from_date, $monthLabel, $includeYear);
            if ($monthKey === '' && !empty($from_date)) {
                $monthTs = strtotime($from_date);
                if ($monthTs !== false) {
                    $monthKey = date('Y-m', $monthTs);
                }
            }

            $startDateDisplay = client_report_format_client_date_display($clientStartDateTs);
            $endDateDisplay = client_report_format_client_date_display($clientEndDateTs);
            $clientMonthId = md5($clientId . '_' . (isset($from_date) ? $from_date : '') . '_' . (isset($to_date) ? $to_date : '') . '_' . $monthLabel);
            ?>
            <tr class="client-row cr-grid-row"
                data-month-key="<?php echo htmlspecialchars($monthKey, ENT_QUOTES, 'UTF-8'); ?>"
                data-client="<?php echo $data['client_name']; ?>"
                data-project=""
                data-manager="<?php echo $clientPmName; ?>"
                data-department="<?php echo $data['department']; ?>">
                <td title="Client Name" class="client-name-cell">
                    <?php echo htmlspecialchars($data['client_name']); ?>
                    <button type="button" class="toggle-projects toggle-projects-inline" data-client="<?php echo $clientMonthId; ?>" title="Click to view projects" aria-label="Toggle projects">
                        <i class="fa fa-plus"></i>
                    </button>
                </td>
                <td title="Project Manager">
                    <?php echo explode(' ', trim($clientPmName))[0]; ?>
                </td>
                <td title="Month"><?php echo htmlspecialchars($monthNameDisplay, ENT_QUOTES, 'UTF-8'); ?></td>
                <td title="Department"><?php echo $data['department']; ?></td>
                <td title="Start Date"><?php echo $startDateDisplay; ?></td>
                <td title="End Date"><?php echo $endDateDisplay; ?></td>
                <td title="Billable" style="text-align: center !important;"><?php echo ucfirst($billable); ?></td>
                <td title="Production Hours"  style="text-align: center !important; font-weight: bold !important;"><?php echo $totalProductionHours; ?></td>
                <td title="Project General Hours"  style="text-align: center !important; font-weight: bold !important;"><?php echo $totalGeneralHours; ?></td>
                <td title="Total Hours"  style="text-align: center !important; font-weight: bold !important;"><?php echo $totalCombined; ?></td>
                <td title="Invoiced"  style="text-align: center !important;font-weight: bold !important;"><?php echo !empty($totalInvoiceAmount) ? number_format($totalInvoiceAmount, 2) : ''; ?></td>
                <td title="Quality Errors"  style="text-align: center !important;font-weight: bold !important;"><?php echo $k_QualityErrorPercentage;?></td>
                <td title="Productivity%"  style="text-align: center !important;font-weight: bold !important;"><?php echo round($totalproductivityPercentage). '%';?></td>
                <td title="Project General%"  style="text-align: center !important;font-weight: bold !important;"><?php echo round($totalprojectgeneralPercentage). '%';?></td>
                <td title="Difference" style="color: <?php echo $clientDifference >= 0 ? '#28a745' : '#dc3545'; ?>; font-weight: bold !important; text-align: center !important;" ><?php echo number_format($clientDifference, 2); ?></td>
            </tr>
            <?php foreach ($data['projects'] as $proj): ?>
                <?php
                // Get production hours from project data (already filtered by date range)
                $productionHours = isset($proj->total_hours) ? $proj->total_hours : 0;
                
                // Get general hours from project data
                $generalHours = isset($proj->general_hours) ? $proj->general_hours : 0;
                
                $combinedHours = $productionHours + $generalHours;
                $productivityPercentage = $combinedHours > 0 ? ($productionHours / $combinedHours) * 100 : 0;
                $projectgeneralPercentage = $combinedHours > 0 ? ($generalHours / $combinedHours) * 100 : 0;
                
                // Calculate difference in hours for project row
                $projectInvoiceAmount = isset($proj->project_invoice_amt) && !empty($proj->project_invoice_amt) ? (float)$proj->project_invoice_amt : 0;
                $projectDifference = $combinedHours - $projectInvoiceAmount;
                
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
                <tr class="project-row cr-grid-row project-<?php echo $clientMonthId; ?>" style="display: none;"
                    data-month-key="<?php echo htmlspecialchars($monthKey, ENT_QUOTES, 'UTF-8'); ?>"
                    data-client="<?php echo $data['client_name']; ?>"
                    data-project="<?php echo $proj->project_name; ?>"
                    data-manager="<?php echo $proj->pm_name; ?>"
                    data-department="<?php echo $proj->department; ?>">
                    <td title="Project Name" class="project-name-cell"><?php echo htmlspecialchars($proj->project_name); ?></td>
                    <td title="Project Manager">
                        <?php echo explode(' ', trim($proj->pm_name))[0]; ?>
                    </td>
                    <td title="Month"><?php echo htmlspecialchars($monthNameDisplay, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td title="Department"><?php echo $proj->department; ?></td>
                    <td title="Start Date">
                        <?php 
                        $projStartDate = '';
                        if (!empty($proj->project_start_date) && $proj->project_start_date != '0000-00-00' && $proj->project_start_date != '0000-00-00 00:00:00') {
                            $startTimestamp = strtotime($proj->project_start_date);
                            if ($startTimestamp !== false && $startTimestamp > 0) {
                                $formattedDate = date('d-M-Y', $startTimestamp);
                                // Check if formatted date is not 01-Jan-1970
                                if ($formattedDate != '01-Jan-1970') {
                                    $projStartDate = $formattedDate;
                                }
                            }
                        }
                        echo $projStartDate;
                        ?>
                    </td>
                    <td title="End Date">
                        <?php 
                        $projEndDate = '';
                        // Use project_end_date from project_details table
                        if (!empty($proj->project_end_date) && $proj->project_end_date != '0000-00-00' && $proj->project_end_date != '0000-00-00 00:00:00') {
                            $endTimestamp = strtotime($proj->project_end_date);
                            if ($endTimestamp !== false && $endTimestamp > 0) {
                                $formattedDate = date('d-M-Y', $endTimestamp);
                                // Check if formatted date is not 01-Jan-1970
                                if ($formattedDate != '01-Jan-1970') {
                                    $projEndDate = $formattedDate;
                                }
                            }
                        }
                        echo $projEndDate;
                        ?>
                    </td>
                    <td title="Billable"><?php echo $proj->man_days; ?></td>
                    <td title="Productive Hours"><?php echo $productionHours; ?></td>
                    <td title="Project General"><?php echo $generalHours; ?></td>
                    <td title="Total Hours"><?php echo $combinedHours; ?></td>
                    <td title="Invoiced"><?php echo isset($proj->project_invoice_amt) && !empty($proj->project_invoice_amt) ? number_format($proj->project_invoice_amt, 2) : ''; ?></td>
                    <td title="Quality Errors"><?php echo $k_QualityErrorPercentage_22; ?></td>
                    <td title="Productivity%"><?php echo round($productivityPercentage). '%';?></td>
                    <td title="Project General%"><?php echo round($projectgeneralPercentage). '%';?></td>
                    <td title="Difference" style="color: <?php echo $projectDifference >= 0 ? '#28a745' : '#dc3545'; ?>; font-weight: bold;"><?php echo number_format($projectDifference, 2); ?></td>
                </tr>
            <?php endforeach;
        }
        
        // Display month-wise data or regular grouped data
        if ($hasMonthWiseData && !empty($monthWiseData)) {
            // Display month-wise (without month header row)
            foreach ($monthWiseData as $monthKey => $monthData):
                $monthLabel = $monthData['label'];
                $monthFromDate = $monthData['from_date'];
                $monthToDate = $monthData['to_date'];
                $sortedMonthData = isset($monthData['data']) && is_array($monthData['data']) ? $monthData['data'] : array();
                uasort($sortedMonthData, function($a, $b) {
                    $nameA = isset($a['client_name']) ? (string)$a['client_name'] : '';
                    $nameB = isset($b['client_name']) ? (string)$b['client_name'] : '';
                    return strcasecmp($nameA, $nameB);
                });
                foreach ($sortedMonthData as $clientId => $data):
                    renderClientProjects($data, $clientId, $monthFromDate, $monthToDate, $monthLabel, $clientReportMonthIncludeYear, $monthKey);
                endforeach;
            endforeach;
        } else {
            // Display regular grouped data
            foreach ($grouped as $clientId => $data):
                renderClientProjects($data, $clientId, $from_date, $to_date, '', $clientReportMonthIncludeYear);
            endforeach;
        }
        ?> 
    </tbody>
</table>
</div>
<?php endif; ?>

<div class="pagination-container">
    <?php echo $pagination_links; ?>
</div>

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
    background-color: #ddd;
    color: white;
    border-color: #ddd;
}
.pagination a.disabled {
    color: #ccc;
    border-color: #ddd;
    pointer-events: none;
}
</style>

<script>
$(document).ready(function () {
    if (typeof updateKPIReportHeading === 'function') {
        updateKPIReportHeading();
    }
    if ($('#month_id').length) {
        $('#month_id').select2();
        $('#month_id').on('change', function () {
            if (typeof updateKPIReportHeading === 'function') {
                updateKPIReportHeading();
            }
        });
    }
    function scrollToClientReportEl(el) {
        if (!el) {
            return;
        }
        var top = 0;
        if (el.getBoundingClientRect) {
            top = el.getBoundingClientRect().top + (window.pageYOffset || document.documentElement.scrollTop || 0) - 100;
        }
        if (top < 0) {
            top = 0;
        }
        if (typeof window.scrollTo === 'function') {
            try {
                window.scrollTo({ top: top, behavior: 'smooth' });
            } catch (e) {
                window.scrollTo(0, top);
            }
        }
        $('html, body').stop(true).animate({ scrollTop: top }, 350);
    }
    function applyClientReportMonthView(monthKey) {
        var isCons = !monthKey || monthKey === 'consolidated';
        var key = String(monthKey || '');
        $('.cr-month-tab').removeClass('active');
        $('.cr-month-tab[data-month-tab="' + (isCons ? 'consolidated' : key) + '"]').addClass('active');

        if (isCons) {
            $('#crDeptKpiConsolidated').removeClass('cr-view-hidden');
            $('#crDeptKpiMonthWise').addClass('cr-view-hidden');
            $('#crDeptKpiMonthWise tbody.cr-dept-month-group').removeClass('cr-month-hidden');
            $('#employeeTable tbody tr.client-row').css('display', 'table-row');
            $('#employeeTable tbody tr.project-row').css('display', 'none');
            $('#employeeTable .toggle-projects i').removeClass('fa-minus').addClass('fa-plus');
            scrollToClientReportEl(document.getElementById('crDeptKpiConsolidated') || document.getElementById('clientReportMonthTabs'));
            return;
        }

        $('#crDeptKpiConsolidated').addClass('cr-view-hidden');
        $('#crDeptKpiMonthWise').removeClass('cr-view-hidden');
        var $groups = $('#crDeptKpiMonthWise tbody.cr-dept-month-group');
        $groups.addClass('cr-month-hidden');
        var $match = $groups.filter(function () {
            return String($(this).attr('data-month-key') || '') === key;
        });
        if ($match.length) {
            $match.removeClass('cr-month-hidden');
        } else {
            $groups.removeClass('cr-month-hidden');
        }

        $('#employeeTable tbody tr.client-row').each(function () {
            var rowKey = String($(this).attr('data-month-key') || '');
            this.style.display = (rowKey === key) ? 'table-row' : 'none';
        });
        $('#employeeTable tbody tr.project-row').css('display', 'none');
        $('#employeeTable .toggle-projects i').removeClass('fa-minus').addClass('fa-plus');

        var scrollEl = $match.length ? $match.get(0) : document.getElementById('crDeptKpiMonthWise');
        scrollToClientReportEl(scrollEl);
    }
    $(document).on('click', '.cr-month-tab', function (e) {
        e.preventDefault();
        e.stopPropagation();
        applyClientReportMonthView($(this).attr('data-month-tab'));
        return false;
    });
    $(document).on('click', '.cr-dept-kpi-month-cell', function (e) {
        e.preventDefault();
        var key = $(this).attr('data-month-key');
        if (key) {
            applyClientReportMonthView(key);
        }
        return false;
    });
});
</script>
<script>
function redirectToClient() {
    const button = document.querySelector('.four-report-btn');
    button.classList.add('active');
    setTimeout(function() {
        window.location.href = "<?php echo base_url('kpi_reports/clientReport');?>";
    }, 300);
}
</script>

<style>
/* Table wrapper */
.client-report-table-wrap {
    overflow-x: auto;
    margin-top: 10px;
    margin-bottom: 30px;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(0, 0, 0, 0.06);
}

/* Table styling - Execution Plan aligned */
#employeeTable.client-report-table {
    width: 100%;
    min-width: 1620px;
    border-collapse: collapse;
    border-spacing: 0;
    margin-bottom: 0;
    background-color: #ffffff;
    border: none;
    table-layout: auto;
}

#employeeTable thead {
    background: linear-gradient(to bottom, #337ab7, #2c5aa0);
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}
#employeeTable thead tr {
    border-bottom: none;
}

#employeeTable thead th {
    color: #ffffff;
    font-size: 15px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 10px 8px;
    border: none;
    border-right: 1px solid rgba(255, 255, 255, 0.2);
    vertical-align: middle;
    white-space: normal;
    text-align: center;
    line-height: 1.25;
    word-break: break-word;
    overflow-wrap: anywhere;
}

#employeeTable thead th:last-child {
    border-right: none;
}

#employeeTable thead th:first-child {
    text-align: left;
    padding-left: 16px;
    min-width: 200px;
}

/* Column min-widths so headers do not overlap */
#employeeTable thead th:nth-child(2),
#employeeTable tbody td:nth-child(2) { min-width: 110px; }
#employeeTable thead th:nth-child(3),
#employeeTable tbody td:nth-child(3) { min-width: 110px; }
#employeeTable thead th:nth-child(4),
#employeeTable tbody td:nth-child(4) { min-width: 120px; }
#employeeTable thead th:nth-child(5),
#employeeTable thead th:nth-child(6),
#employeeTable tbody td:nth-child(5),
#employeeTable tbody td:nth-child(6) { min-width: 95px; }
#employeeTable thead th:nth-child(7),
#employeeTable tbody td:nth-child(7) { min-width: 80px; }
#employeeTable thead th:nth-child(8),
#employeeTable tbody td:nth-child(8) { min-width: 95px; }
#employeeTable thead th:nth-child(9),
#employeeTable tbody td:nth-child(9) { min-width: 115px; }
#employeeTable thead th:nth-child(10),
#employeeTable tbody td:nth-child(10),
#employeeTable thead th:nth-child(11),
#employeeTable tbody td:nth-child(11) { min-width: 90px; }
#employeeTable thead th:nth-child(12),
#employeeTable tbody td:nth-child(12) { min-width: 95px; }
#employeeTable thead th:nth-child(13),
#employeeTable tbody td:nth-child(13),
#employeeTable thead th:nth-child(14),
#employeeTable tbody td:nth-child(14) { min-width: 105px; }
#employeeTable thead th:nth-child(15),
#employeeTable tbody td:nth-child(15) { min-width: 95px; }

#employeeTable tbody td {
    padding: 12px 14px;
    vertical-align: middle;
    border-bottom: 1px solid #e8ecf0;
    border-right: 1px solid #eef2f6;
    white-space: nowrap;
    background-color: #ffffff;
    font-size: 15px;
}

#employeeTable tbody td:last-child {
    border-right: none;
}

#employeeTable tbody tr:hover {
    background-color: #f5f8fa;
}

#employeeTable tbody tr:last-child td {
    border-bottom: none;
}

/* Client row styling */
#employeeTable tbody tr.client-row {
    background-color: #f8f9fa;
    border-left: 4px solid #337ab7;
}

#employeeTable tbody tr.client-row td {
    font-weight: 600;
    font-size: 15px;
}

#employeeTable tbody tr.client-row:hover {
    background-color: #f5f8fa;
}

#employeeTable tbody tr.client-row:hover td {
    background-color: transparent;
}

/* Project row styling */
#employeeTable tbody tr.project-row {
    background-color: #ffffff;
}

#employeeTable tbody tr.project-row:nth-child(even) {
    background-color: #fafafa;
}

#employeeTable tbody tr.project-row:nth-child(even) td {
    background-color: #fafafa;
}

#employeeTable tbody tr.project-row:hover {
    background-color: #f3f4f6;
}

#employeeTable tbody tr.project-row:hover td {
    background-color: #f3f4f6;
}

/* Column alignment for body cells */
#employeeTable th:nth-child(1),
#employeeTable td:nth-child(1) {
    min-width: 200px;
    width: 200px;
}

#employeeTable td:nth-child(1) {
    text-align: left;
    font-weight: 600;
    color: #2c3e50;
    font-size: 15px;
}

#employeeTable td:nth-child(2) {
    text-align: center;
    color: #2c5aa0;
    font-weight: 600;
    font-size: 15px;
}

#employeeTable td:nth-child(3),
#employeeTable td:nth-child(4),
#employeeTable td:nth-child(5),
#employeeTable td:nth-child(6) {
    text-align: center;
}

#employeeTable td:nth-child(7),
#employeeTable td:nth-child(8),
#employeeTable td:nth-child(9),
#employeeTable td:nth-child(10),
#employeeTable td:nth-child(11),
#employeeTable td:nth-child(12),
#employeeTable td:nth-child(13),
#employeeTable td:nth-child(14),
#employeeTable td:nth-child(15) {
    text-align: center;
    padding-right: 12px;
}

#employeeTable td:nth-child(8),
#employeeTable td:nth-child(9),
#employeeTable td:nth-child(10),
#employeeTable td:nth-child(11) {
    font-weight: 500;
}

/* Client name cell - Execution Plan client row style */
#employeeTable tbody tr.client-row td.client-name-cell {
    white-space: normal;
    word-wrap: break-word;
    max-width: 280px;
    line-height: 1.4;
    background: #f8f9fa !important;
    color: #2c3e50;
    font-weight: 600;
    font-size: 15px;
    padding: 12px 14px;
    border-left: none;
    border-bottom: 1px solid #e8ecf0;
    box-shadow: none;
}
/* (+) toggle - only the + symbol, no background */
.toggle-projects-inline {
    margin-left: 10px;
    padding: 0;
    font-size: 16px;
    border: none;
    background: transparent;
    color: #2C5AA0;
    cursor: pointer;
    vertical-align: middle;
    display: inline-block;
    transition: color 0.2s;
    min-width: 24px;
    text-align: center;
}
.toggle-projects-inline:hover {
    background: transparent;
    color: #2C5AA0;
}
.toggle-projects-inline i {
    font-size: 15px;
    font-weight: bold;
    color: #2C5AA0;
}

/* Project name cell - Execution Plan project row style */
#employeeTable tbody tr.project-row td.project-name-cell {
    background: #fff !important;
    border-left: none;
    padding: 12px 14px 12px 50px;
    font-weight: 600;
    font-size: 15px;
    white-space: normal;
    word-wrap: break-word;
    max-width: 280px;
    line-height: 1.4;
    color: #666;
    border-bottom: 1px solid #e8ecf0;
}

/* Date columns styling */
#employeeTable tbody td:nth-child(5),
#employeeTable tbody td:nth-child(6) {
    color: #555;
    font-size: 15px;
    text-align: center;
    white-space: nowrap;
}

/* Percentage columns styling */
#employeeTable tbody td:nth-child(12),
#employeeTable tbody td:nth-child(13),
#employeeTable tbody td:nth-child(14) {
    font-weight: 600;
    color: #2c3e50;
    font-size: 15px;
}

/* Number columns styling */
#employeeTable tbody td:nth-child(7),
#employeeTable tbody td:nth-child(8),
#employeeTable tbody td:nth-child(9),
#employeeTable tbody td:nth-child(10),
#employeeTable tbody td:nth-child(11) {
    color: #1f2937;
    font-weight: 500;
    font-size: 15px;
    font-family: "Courier New", monospace;
    text-align: center;
}

.ui-datepicker .ui-datepicker-prev span, .ui-datepicker .ui-datepicker-next span{

    left: 8% !important;
    top:34% !important;

}

/* Responsive adjustments */
@media (max-width: 1400px) {
    #employeeTable thead th {
        font-size: 11px;
        padding: 8px 6px;
    }
    
    #employeeTable tbody td {
        font-size: 15px;
        padding: 10px 8px;
    }
    
    .toggle-projects-inline {
        font-size: 16px;
        padding: 3px 8px;
    }
}
</style>

<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
</body>
