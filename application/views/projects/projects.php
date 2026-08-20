    <!-- Inlude Header here -->
    <?php $this->load->view('includes/cRMHeader'); 
    $createdUser = $this->session->userdata['logged_in_timesheet']['empId'];
    $loggedInEmpId = $this->session->userdata['logged_in_timesheet']['empId'];
    $filterClients = $this->client_model->getClientName();
    ?>
    <!-- Inlude Header here END-->

    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="page-header-wrapper">
            <div class="page-title">
                <div class="title-section">
                    <h1><i class="fa fa-folder-open-o"></i> Manage Projects</h1>
                    <p class="subtitle">View and manage all your projects</p>
                </div>
                <div class="action-buttons">
                    <a class="btn btn-success btn-action" href="<?php echo base_url('projects/add'); ?>" data-toggle="tooltip" title="Add New Project">
                        <i class="fa fa-plus"></i> <span>Add Project</span>
                    </a>
                    <a class="btn btn-info btn-action"
   data-toggle="tooltip"
   title="Refresh Data"
   href="javascript:void(0);"
   onclick="refreshProjects()">
    <i class="fa fa-refresh"></i>
</a>

<script>
function refreshProjects() {

    // Search clear
    $('#searchProject').val('');
    $('#tableSearchProject').val('');

    // Department + Manager reset
    $('#department').val(null).trigger('change');
    $('#manager_name').val(null).trigger('change');

    // From / To reset
    $('#from_year, #from_month, #to_year, #to_month').each(function() {
        $(this).val(null).trigger('change');
        if (typeof syncProjectYmClearState === 'function') {
            syncProjectYmClearState($(this));
        }
    });

    // Reset status
    window.projectStatus = '';

    // Reload data
    loadProjects(1);
}
</script>
                    <!-- <?php if(in_array($this->session->userdata['logged_in_timesheet']['user_type'], array('admin','business_head'))): ?>
                    <a class="btn btn-warning btn-action" data-toggle="tooltip" title="Generate Project Report" href="<?php echo base_url('projects/project_report_information'); ?>">
                        <i class="fa fa-file-text-o"></i> <span>Details Report</span>
                    </a>
                    <?php endif; ?> -->

<a class="btn btn-action"
   data-toggle="tooltip"
   title="Download Details Report"
   href="<?php echo base_url('projects/downloadProjectMasterReport'); ?>"
   style="background-color: #7c49b6; color: #fff; border-color: #28a745;">
    <i class="fa fa-file-excel-o"></i>
    <span>Details Download Report</span>
</a>






<a class="btn btn-warning btn-flat"
   data-toggle="tooltip"
   title="Download Report"
   href="javascript:void(0);"
   onclick="downloadExcel()">
   <i class="fa fa-file-excel-o"></i> Summary Report
</a>




<script>
function downloadExcel() {

    var search = $('#searchProject').val();
var department = $('#department').val() || [];
var manager = $('#manager_name').val() || [];

    var from_year = $('#from_year').val();
    var from_month = $('#from_month').val();

    var to_year = $('#to_year').val();
    var to_month = $('#to_month').val();

    var status = window.projectStatus || '';
    var billing_type = window.billingType || '';
    var client_Id = $('#client_Id').val() || '';
    var project_Id = $('#filter_project_Id').val() || '';

    var currentSortBy = sortBy;
    var currentSortOrder = sortOrder;

    var url = baseUrl + "projects/downloadExcel?" +
        "search=" + encodeURIComponent(search) +
        "&department=" + encodeURIComponent(JSON.stringify(department)) +
        "&manager=" + encodeURIComponent(JSON.stringify(manager)) +
        "&from_year=" + encodeURIComponent(from_year || '') +
        "&from_month=" + encodeURIComponent(from_month || '') +
        "&to_year=" + encodeURIComponent(to_year || '') +
        "&to_month=" + encodeURIComponent(to_month || '') +
        "&status=" + encodeURIComponent(status) +
        "&billing_type=" + encodeURIComponent(billing_type) +
        "&client_Id=" + encodeURIComponent(client_Id) +
        "&project_Id=" + encodeURIComponent(project_Id) +
        "&sort_by=" + encodeURIComponent(currentSortBy) +
        "&sort_order=" + encodeURIComponent(currentSortOrder);

    window.location.href = url;
}
</script>


                </div>
            </div>
        </div>
        
        <div class="filter-section">
    <h4 class="project-filter-heading"><i class="fa fa-filter"></i> Search Filters</h4>

    <div class="row project-filter-main-row">

        <!-- Department -->
<div class="col-md-2 col-sm-6">
    <div class="form-group project-filter-form-group">
        <label class="control-label">Department</label>
        
        

        <?php
        $selectedDept = $this->input->post('department') ? $this->input->post('department') : [];
        ?>

        <?php if($createdUser == '149'): ?>

            <select class="form-control project-filter-select"
                    id="department"
                    name="department[]"
                    multiple>
                    

                <option value="MEP" <?php echo (in_array('MEP', $selectedDept)) ? 'selected' : ''; ?>>MEP</option>

            </select>

        <?php elseif($createdUser == '47'): ?>

            <select class="form-control project-filter-select" 
                    id="department" 
                    name="department[]" 
                    multiple>

                <option value="Architectural" <?php echo (in_array('Architectural', $selectedDept)) ? 'selected' : ''; ?>>
                    Architectural - Structural - 3D Visualization
                </option>

            </select>
<?php else: ?>    

    <select class="form-control project-filter-select" 
            id="department" 
            name="department[]" 
            multiple>

        <option value="all" <?php echo (in_array('all', $selectedDept)) ? 'selected' : ''; ?>>ALL Services</option>

        <option value="MEP" <?php echo (in_array('MEP', $selectedDept)) ? 'selected' : ''; ?>>MEP</option>

        <option value="Architectural" <?php echo (in_array('Architectural', $selectedDept)) ? 'selected' : ''; ?>>Architectural</option>

        <option value="Structural" <?php echo (in_array('Structural', $selectedDept)) ? 'selected' : ''; ?>>Structural</option>

        <option value="3D Visualization" <?php echo (in_array('3D Visualization', $selectedDept)) ? 'selected' : ''; ?>>3D Visualization</option>

        <option value="2D Auto CAD" <?php echo (in_array('2D Auto CAD', $selectedDept)) ? 'selected' : ''; ?>>2D Auto CAD</option>

    </select>

<?php endif; ?>

    </div>
</div>

<div class="col-md-2 col-sm-6">
    <div class="form-group project-filter-form-group">
        <label class="control-label">Client</label>
        <select id="client_Id" name="client_Id" class="form-control project-filter-select">
            <option value="">All Clients</option>
            <?php if (!empty($filterClients)): ?>
                <?php foreach ($filterClients as $client): ?>
                    <option value="<?php echo (int)$client->client_Id; ?>">
                        <?php echo htmlspecialchars($client->client_name, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
</div>

<div class="col-md-2 col-sm-6">
    <div class="form-group project-filter-form-group">
        <label class="control-label">Project</label>
        <select id="filter_project_Id" name="filter_project_Id" class="form-control project-filter-select">
            <option value="">All Projects</option>
        </select>
    </div>
</div>

<div class="col-md-3 col-sm-6">
    <div class="form-group project-filter-form-group project-filter-search-wrap">

        <label class="control-label">Quick Search</label>

        <input type="text"
               name="search_text"
               id="searchProject"
               class="form-control project-filter-input"
               placeholder="Name / Number / Client"
               autocomplete="off">

        <div id="projectSuggestions"></div>

    </div>
</div>

<style>
#projectSuggestions{
    position:absolute;
    left:0;
    width:100%;
    background:#fff;
    border:1px solid #ddd;
    z-index:99999;
    display:none;
    max-height:250px;
    overflow-y:auto;
    box-shadow:0 2px 8px rgba(0,0,0,0.15);
    top: calc(var(--pf-label-height) + var(--pf-control-height) + 8px);
}

.suggestion-item{
    padding:10px;
    cursor:pointer;
    border-bottom:1px solid #eee;
}

.suggestion-item:hover{
    background:#f5f5f5;
}
</style>

<script>
$(document).ready(function(){

    $('#searchProject').on('keyup', function(){

        var keyword = $(this).val();

        if(keyword != ''){

            $.ajax({
                url: "<?php echo base_url('projects/getProjectSuggestions'); ?>",
                method: "GET",
                data: {term: keyword},

                success:function(data){

                    if(data.trim() != ''){
                        $('#projectSuggestions').html(data).show();
                    }else{
                        $('#projectSuggestions').hide();
                    }

                }
            });

        }else{
            $('#projectSuggestions').hide();
        }

    });

    $(document).on('click','.suggestion-item',function(){

        $('#searchProject').val($(this).text());
        $('#projectSuggestions').hide();
        loadProjects(1);

    });

    $(document).click(function(e){

        if(!$(e.target).closest('#searchProject,#projectSuggestions').length){
            $('#projectSuggestions').hide();
        }

    });

});
</script>

        <!-- Project Manager -->

<div class="col-md-3 col-sm-6">
<div class="form-group project-filter-form-group">
<label class="control-label">Project Managers</label>

<?php
$selectedManagers = (array) $this->input->post('manager_name');
?>

<select name="manager_name[]" id="manager_name" class="form-control project-filter-select" multiple>
<?php
$allProjectManagers = $this->project_model->getAllProjectManagers();
if (!empty($allProjectManagers)):
    foreach ($allProjectManagers as $manager):
        $managerId = (string) $manager->empId;
        $isActive = !empty($manager->status) && strtolower($manager->status) === 'active';
        $managerLabel = $manager->name . ($isActive ? '' : ' (Inactive)');
?>
    <option value="<?php echo htmlspecialchars($managerId, ENT_QUOTES, 'UTF-8'); ?>" <?php if (in_array($managerId, $selectedManagers) || in_array((int)$managerId, $selectedManagers, true)) echo 'selected'; ?>>
        <?php echo htmlspecialchars($managerLabel, ENT_QUOTES, 'UTF-8'); ?>
    </option>
<?php
    endforeach;
endif;
?>
</select>
</div>
</div>

    </div><!-- /.project-filter-main-row -->

    <div class="project-filter-bottom-row">

        <div class="project-date-group pf-date-block">
            <label class="control-label">From</label>
            <div class="project-date-fields select-row">
                <div class="select-container pf-ym-select-wrap">
                    <select class="dropdown-box clearable-select project-filter-select" id="from_year" name="from_year">
                        <option value="">Select Year</option>
                        <option value="ALL">ALL</option>
                        <option value="2026">2026</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                        <option value="2021">2021</option>
                        <option value="2020">2020</option>
                        <option value="2019">2019</option>
                        <option value="2018">2018</option>
                        <option value="2017">2017</option>
                        <option value="2016">2016</option>
                    </select>
                </div>
                <div class="select-container pf-ym-select-wrap">
                    <select class="dropdown-box clearable-select project-filter-select" id="from_month" name="from_month">
                        <option value="">Select Month</option>
                        <option value="01">January</option>
                        <option value="02">February</option>
                        <option value="03">March</option>
                        <option value="04">April</option>
                        <option value="05">May</option>
                        <option value="06">June</option>
                        <option value="07">July</option>
                        <option value="08">August</option>
                        <option value="09">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="project-date-group pf-date-block">
            <label class="control-label">To</label>
            <div class="project-date-fields select-row">
                <div class="select-container pf-ym-select-wrap">
                    <select class="dropdown-box clearable-select project-filter-select" id="to_year" name="to_year">
                        <option value="">Select Year</option>
                        <option value="ALL">ALL</option>
                        <option value="2026">2026</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                        <option value="2021">2021</option>
                        <option value="2020">2020</option>
                        <option value="2019">2019</option>
                        <option value="2018">2018</option>
                        <option value="2017">2017</option>
                        <option value="2016">2016</option>
                    </select>
                </div>
                <div class="select-container pf-ym-select-wrap">
                    <select class="dropdown-box clearable-select project-filter-select" id="to_month" name="to_month">
                        <option value="">Select Month</option>
                        <option value="01">January</option>
                        <option value="02">February</option>
                        <option value="03">March</option>
                        <option value="04">April</option>
                        <option value="05">May</option>
                        <option value="06">June</option>
                        <option value="07">July</option>
                        <option value="08">August</option>
                        <option value="09">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="project-filter-actions pf-inline-actions">
            <button type="button" class="btn btn-primary" onclick="loadProjects(1)">
                <i class="fa fa-search"></i> Search
            </button>
            <button type="button" class="btn btn-hourly" onclick="filterStatus('Hourly')">Hourly</button>
            <button type="button" class="btn btn-monthly" onclick="filterStatus('Monthly')">Monthly</button>
            <button type="button" class="btn btn-success" onclick="filterStatus('Process')">In Process</button>
            <button type="button" class="btn btn-warning" onclick="filterStatus('On Hold')">On Hold</button>
            <button type="button" class="btn btn-danger" onclick="filterStatus('Closed')">Closed</button>
            <button type="button" class="btn btn-info" onclick="filterStatus('All')">All</button>
        </div>

    </div><!-- /.project-filter-bottom-row -->

</div><!-- /.filter-section -->

<style>
/* Filter Box */
.filter-section {
    --pf-control-height: 40px;
    --pf-label-height: 22px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 20px 16px;
    box-shadow: 0 2px 10px rgba(31, 80, 118, 0.06);
    margin-bottom: 20px;
}
.project-filter-heading {
    margin: 0 0 14px 0;
    font-size: 16px;
    font-weight: 700;
    color: #1f5076;
}
.project-filter-heading .fa {
    margin-right: 8px;
    color: #337ab7;
}
.project-filter-main-row,
.project-filter-dates-row {
    margin-left: -8px;
    margin-right: -8px;
}
.project-filter-main-row > [class*="col-"],
.project-filter-dates-row > [class*="col-"] {
    padding-left: 8px;
    padding-right: 8px;
}
.project-filter-bottom-row {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 12px 18px;
    margin-top: 4px;
    padding-top: 14px;
    border-top: 1px solid #eef2f6;
    width: 100%;
}
.project-date-group.pf-date-block {
    flex: 1 1 420px;
    min-width: 380px;
    max-width: 540px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px 12px;
    margin-bottom: 0;
}
.project-date-group.pf-date-block > .control-label {
    margin-bottom: 6px;
    font-size: 13px;
    font-weight: 600;
    color: #475569;
}
.project-date-fields {
    display: flex;
    gap: 12px;
    width: 100%;
}
.project-date-fields .select-container {
    flex: 1 1 0;
    min-width: 0;
    max-width: none;
}
.project-date-fields .select-container:first-child {
    flex: 0 0 158px;
    min-width: 158px;
}
.project-date-fields .select-container:last-child {
    flex: 1 1 200px;
    min-width: 200px;
}
.project-filter-actions.pf-inline-actions {
    flex: 1 1 100%;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    align-content: flex-end;
    justify-content: flex-start;
    gap: 8px;
    margin-top: 0;
    padding-top: 0;
    border-top: none;
}
.project-filter-actions.pf-inline-actions .btn {
    margin: 0;
    min-width: 96px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 13px;
    padding: 8px 14px;
    white-space: nowrap;
}
.filter-section .project-filter-form-group {
    margin-bottom: 12px;
}
.filter-section .project-filter-form-group > .control-label {
    display: block;
    margin: 0 0 6px 0;
    font-size: 14px;
    font-weight: 600;
    line-height: var(--pf-label-height);
    min-height: var(--pf-label-height);
    color: #333;
}
.filter-section .project-filter-input,
.filter-section .form-control.project-filter-input {
    height: var(--pf-control-height);
    border: 1px solid #cfd6df;
    border-radius: 8px;
    font-size: 14px;
    box-shadow: none;
    background: #fff;
}
.filter-section .project-filter-search-wrap {
    position: relative;
}

.filter-box {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 0;
    background: transparent;
}
.filter-box-inline {
    border: none;
    padding: 0;
    background: transparent;
}

/* Keep Year + Month Side By Side */
.select-row {
    display: flex;
    gap: 6px;
    margin-top: 0;
    align-items: center;
}

/* Select Container */
.select-container {
    position: relative;
    flex: 1 1 0;
    min-width: 0;
}
.filter-section .pf-ym-select-wrap .select2-container.pf-ym-selected-bg .select2-selection--single {
    background-color: #673ab7 !important;
    border-color: #673ab7 !important;
}
.filter-section .pf-ym-select-wrap .select2-container.pf-ym-selected-bg .select2-selection__rendered {
    color: #fff !important;
    font-weight: 600;
    padding-right: 48px !important;
}
.filter-section .pf-ym-select-wrap .select2-container.pf-ym-selected-bg .select2-selection__arrow b {
    border-color: #fff transparent transparent transparent !important;
}
.filter-section .pf-ym-select-wrap .select2-selection--single {
    position: relative;
}
.filter-section .pf-ym-select-wrap .select2-selection--single .select2-selection__clear {
    position: absolute;
    right: 28px;
    top: 50%;
    transform: translateY(-50%);
    float: none;
    margin: 0;
    padding: 0;
    width: 18px;
    height: 18px;
    font-size: 16px;
    font-weight: 700;
    line-height: 18px;
    text-align: center;
    color: #64748b;
    cursor: pointer;
    z-index: 2;
}
.filter-section .pf-ym-select-wrap .select2-container.pf-ym-selected-bg .select2-selection__clear {
    color: #fff;
    opacity: 0.95;
}
.filter-section .pf-ym-select-wrap .select2-container.pf-ym-selected-bg .select2-selection__clear:hover {
    color: #fff;
    opacity: 1;
}
.filter-section .pf-ym-select-wrap .select2-selection--single .select2-selection__arrow {
    width: 26px;
    right: 2px;
}

/* Dropdown Styling */
.dropdown-box {
    width: 100%;
    height: var(--pf-control-height);
    padding: 5px 28px 5px 8px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background: #fff;
    font-size: 14px;
    transition: all 0.3s ease;
    color: #333;
}

/* Selected Dropdown Effect */
.dropdown-box.selected-box {
    background-color: #673ab7 !important;
    border: 2px solid #e2e2e2 !important;
    color: #fff !important;
    font-weight: 600;
}

.select-container .select2-hidden-accessible.selected-box ~ .select2-container .select2-selection--single {
    background-color: #673ab7 !important;
    border-color: #673ab7 !important;
    color: #fff !important;
}

/* Label */
.label-title {
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 14px;
}
.filter-section .select2-container,
.filter-box .select2-container {
    width: 100% !important;
}
.filter-section .select2-container .select2-selection--single,
.filter-box .select2-container .select2-selection--single {
    height: var(--pf-control-height) !important;
    min-height: var(--pf-control-height) !important;
    border: 1px solid #cfd6df;
    border-radius: 8px;
    background: #fff;
}
.filter-section .select2-container .select2-selection--single .select2-selection__rendered,
.filter-box .select2-container .select2-selection--single .select2-selection__rendered {
    line-height: calc(var(--pf-control-height) - 2px) !important;
    font-size: 14px;
    padding-left: 10px;
}
.filter-section .select2-container .select2-selection--single .select2-selection__arrow,
.filter-box .select2-container .select2-selection--single .select2-selection__arrow {
    height: calc(var(--pf-control-height) - 2px) !important;
    top: 0;
}
.filter-section .select2-container--default .select2-selection--multiple {
    min-height: var(--pf-control-height) !important;
    height: auto !important;
    border: 1px solid #cfd6df;
    border-radius: 8px;
    overflow: visible;
    background: #fff;
    padding: 4px 8px;
    display: flex;
    align-items: center;
    box-sizing: border-box;
}
.filter-section .select2-container--default .select2-selection--multiple .select2-selection__rendered {
    display: flex !important;
    flex-wrap: wrap;
    align-items: center;
    gap: 4px;
    padding: 0 !important;
    line-height: normal !important;
    max-height: none !important;
    overflow: visible !important;
    float: none !important;
}
.filter-section .select2-container--default .select2-selection--multiple .select2-selection__choice {
    float: none !important;
    display: inline-flex !important;
    align-items: center !important;
    margin: 0 !important;
    line-height: 1.3 !important;
    font-size: 12px;
    padding: 4px 10px 4px 8px !important;
    border-radius: 14px;
}
.filter-section .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    float: none !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-right: 4px !important;
    line-height: 1 !important;
    position: static !important;
}
.filter-section .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
    float: none !important;
    margin-top: 0 !important;
    line-height: 28px !important;
}
.filter-section .select2-container--default .select2-selection--multiple .select2-selection__clear {
    float: none !important;
    margin-top: 0 !important;
    align-self: center;
    font-size: 16px;
    color: #64748b;
    cursor: pointer;
}
.filter-section .select2-container--default .select2-selection--multiple .select2-search--inline {
    float: none !important;
}
.filter-section .select2-container--default .select2-selection--multiple .select2-search__field {
    margin-top: 0 !important;
}
.filter-section .select2-container--default .select2-selection--single .select2-selection__clear {
    margin-right: 22px;
    font-size: 16px;
    line-height: 1;
    color: #64748b;
    cursor: pointer;
}
.filter-section .select2-hidden-accessible.selected-box ~ .select2-container .select2-selection--single .select2-selection__clear {
    color: #fff;
    opacity: 0.95;
}
.filter-section .select2-hidden-accessible.selected-box ~ .select2-container .select2-selection--single .select2-selection__clear:hover {
    color: #fff;
    opacity: 1;
}
.filter-section .select2-dropdown,
.filter-box .select2-dropdown {
    z-index: 10050;
    border-radius: 8px;
}
.filter-section .select2-search--dropdown .select2-search__field,
.filter-box .select2-search--dropdown .select2-search__field {
    border: 1px solid #ccc;
    border-radius: 6px;
    padding: 4px 8px;
}
.filter-section .project-filter-dates-row {
    margin-top: 0;
    width: 100%;
}
@media (max-width: 992px) {
    .project-date-group.pf-date-block {
        flex: 1 1 100%;
        min-width: 0;
        max-width: none;
    }
    .project-date-fields .select-container:first-child,
    .project-date-fields .select-container:last-child {
        flex: 1 1 calc(50% - 6px);
        min-width: 140px;
    }
}
</style>


<script>
function syncProjectYmClearState($select) {
    var val = $select.val();
    var hasValue = val !== null && val !== undefined && String(val).trim() !== '';
    var $s2 = $select.next('.select2-container');
    if (hasValue) {
        $select.addClass('selected-box');
        $s2.addClass('pf-ym-selected-bg');
    } else {
        $select.removeClass('selected-box');
        $s2.removeClass('pf-ym-selected-bg');
    }
}

function initProjectFilterSelect2() {
    if (!$.fn.select2) {
        return;
    }

    var searchEnabled = {
        width: '100%',
        allowClear: true,
        minimumResultsForSearch: 0
    };

    var $department = $('#department');
    if ($department.length && !$department.hasClass('select2-hidden-accessible')) {
        $department.select2($.extend({}, searchEnabled, {
            placeholder: 'Select Department'
        }));
    }

    var $client = $('#client_Id');
    if ($client.length && !$client.hasClass('select2-hidden-accessible')) {
        $client.select2($.extend({}, searchEnabled, {
            placeholder: 'All Clients'
        }));
    }

    var $projectFilter = $('#filter_project_Id');
    if ($projectFilter.length && !$projectFilter.hasClass('select2-hidden-accessible')) {
        $projectFilter.select2($.extend({}, searchEnabled, {
            placeholder: 'All Projects'
        }));
    }

    var $manager = $('#manager_name');
    if ($manager.length && !$manager.hasClass('select2-hidden-accessible')) {
        $manager.select2($.extend({}, searchEnabled, {
            placeholder: 'Select Managers'
        }));
    }

    $('#from_year, #from_month, #to_year, #to_month').each(function() {
        var $el = $(this);
        if ($el.hasClass('select2-hidden-accessible')) {
            return;
        }
        $el.select2({
            width: '100%',
            allowClear: true,
            placeholder: $el.find('option[value=""]').first().text() || 'Select',
            minimumResultsForSearch: 0,
            dropdownParent: $('body')
        });
        syncProjectYmClearState($el);
        $el.off('change.pfYm select2:clear.pfYm').on('change.pfYm', function () {
            syncProjectYmClearState($(this));
        }).on('select2:clear.pfYm', function () {
            loadProjects(1);
        });
    });

    var $perPage = $('#perPage');
    if ($perPage.length && !$perPage.hasClass('select2-hidden-accessible')) {
        $perPage.select2({
            width: '100%',
            minimumResultsForSearch: 0
        });
    }
}

function loadFilterProjectsByClient(preserveProjectId, skipGridReload) {
    var clientId = $('#client_Id').val() || '';
    var projectId = preserveProjectId ? ($('#filter_project_Id').val() || '') : '';
    $.ajax({
        url: "<?php echo base_url('projects/getProjectsByClient'); ?>",
        type: 'POST',
        data: { client_Id: clientId, project_Id: projectId },
        success: function(html) {
            $('#filter_project_Id').html(html);
            if (!preserveProjectId) {
                if (skipGridReload) {
                    $('#filter_project_Id').val('');
                } else {
                    $('#filter_project_Id').val('').trigger('change');
                }
            }
        }
    });
}

$(document).ready(function () {
    initProjectFilterSelect2();

    $('.clearable-select').on('change', function () {
        syncProjectYmClearState($(this));
    });

});

</script>

<style>
.project-filter-actions .btn-hourly {
 background-color: #20c997;
    color: #fff;
    border-color: #20c997;
}

.btn-hourly:hover {
        background-color: #17a589;
    color: #fff;
}

.btn-monthly {
      background-color: #6f42c1;
    color: #fff;
    border-color: #6f42c1;
}

.btn-monthly:hover {
        background-color: #5a32a3;
    color: #fff;
}
</style>

<div class="table-count">
<div class="summary-card">
    <h2 class="summary-title">Project Summary</h2>

    <table class="summary-table">
        <thead>
            <tr>
                <th>Status</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>In Process</td>
                <td id="count_process" class="count-green">0</td>
            </tr>
            <tr>
                <td>On Hold</td>
                <td id="count_hold" class="count-orange">0</td>
            </tr>
            <tr>
                <td>Closed</td>
                <td id="count_closed" class="count-red">0</td>
            </tr>
        </tbody>
    </table>
</div>
</div>
        <div class="row">
            <div class="col-md-12">
                <div class="card project-card">
                    <div class="card-body">
                        <!-- Search and Filter Section -->
                        <div class="filter-section">
                            <div class="row">
                                <!-- <div class="col-md-4 col-sm-6">
                                    <div class="search-box">
                                        <i class="fa fa-search search-icon"></i>
                                        <input type="text" id="searchProject" class="form-control search-input" placeholder="Search by project name, number, client...">
                                        <button class="btn btn-primary search-btn" type="button" onclick="loadProjects(1)">Search</button>
                                    </div>
                                </div> -->
                                <div class="col-md-2 col-sm-3">
                                    <select id="perPage" class="form-control per-page-select project-filter-select">
                                       
                                        <option value="50">50 per page</option>
                                        <option value="100">100 per page</option>
                                    </select>
                                </div>
                                <div class="col-md-10 col-sm-3 text-right">
                                    <div class="record-info-wrapper">
                                        <span id="recordInfo" class="record-info"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Table Section -->
                    
                        <div class="table-wrapper">
                            <table class="table project-table" id="organisationTable">
                                <thead>
                                    <tr>    
                                        <th width="120" class="sortable" data-sort="project_type" data-label="Type"><span>Department</span> <i class="sort-icon fa fa-sort"></i></th>
                                        <th width="150" class="sortable" data-sort="client_name" data-label="Client"><span>Client</span> <i class="sort-icon fa fa-sort"></i></th>
                                        <th width="240" class="sortable" data-sort="project_name" data-label="Project Name"><span>Project Name</span> <i class="sort-icon fa fa-sort"></i></th>
                                        <th width="90" class="sortable" data-sort="project_number" data-label="P.Number"><span>P.Number</span> <i class="sort-icon fa fa-sort"></i></th>
                                        <th width="160" class="sortable" data-sort="name" data-label="Created By"><span>Project Manager</span> <i class="sort-icon fa fa-sort"></i></th>
                                        <th width="105" class="sortable" data-sort="man_days" data-label="Billing"><span>Billing Type</span> <i class="sort-icon fa fa-sort"></i></th>
                                        <th width="93" class="sortable" data-sort="estimated_hours" data-label="Estimated Hours"><span>Est/Hours</span> <i class="sort-icon fa fa-sort"></i></th>
                                        <th width="105" class="sortable" data-sort="status" data-label="Status"><span>Status</span> <i class="sort-icon fa fa-sort"></i></th>
                                        <th class="sortable" data-sort="project_start_date" data-label="Start Date"><span>Start Date</span> <i class="sort-icon fa fa-sort"></i></th>
                                        <th class="sortable" data-sort="project_end_date" data-label="End Date"><span>End Date</span> <i class="sort-icon fa fa-sort"></i></th>
                                        <!-- <th class="sortable" data-sort="created_at" data-label="Date"><span>Date</span> <i class="sort-icon fa fa-sort"></i></th> -->
                                        <th>Actions</th>  
                                    </tr>
                                </thead>
                                <tbody id="projectTableBody">
                                    <!-- Data will be loaded via AJAX -->   
                                    <tr>
                                        <td colspan="9" class="text-center loading-cell">
                                            <div class="loading-spinner">
                                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                                <p>Loading projects...</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Section -->
                        <div class="pagination-section">
                            <div class="row">
                                <div class="col-md-12">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination custom-pagination" id="paginationControls">
                                            <!-- Pagination will be generated dynamically -->
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal Container -->
    <div id="statusModalContainer"></div>
    <!-- Inlude Footer here -->
    <script type="text/javascript">
        var currentPage = 1;
        var sortBy = 'project_number';
        var sortOrder = 'desc';
        var loggedInEmpId = '<?php echo $loggedInEmpId; ?>';
        var baseUrl = '<?php echo base_url(); ?>';

        function updateSortIcons() {
            $('.project-table thead .sortable').each(function() {
                var $th = $(this);
                var col = $th.data('sort');
                var $icon = $th.find('.sort-icon');
                $icon.removeClass('fa-sort fa-sort-up fa-sort-down').addClass('fa-sort');
                if (col === sortBy) {
                    $th.addClass('sort-active');
                    $icon.removeClass('fa-sort').addClass(sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
                } else {
                    $th.removeClass('sort-active');
                }
            });
        }

        // Load projects on page load
       $(document).ready(function() {

    initProjectFilterSelect2();
    loadFilterProjectsByClient(false, true);

    $('#client_Id').on('change', function() {
        loadFilterProjectsByClient(false, false);
        loadProjects(1);
    });

    $('#filter_project_Id').on('change', function() {
        loadProjects(1);
    });

    updateSortIcons();
    loadProjects(1);

    // Sorting
    $(document).on('click', '.project-table thead .sortable', function() {
        var col = $(this).data('sort');
        if (col === sortBy) {
            sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            sortBy = col;
            sortOrder = 'asc';
        }
        updateSortIcons();
        loadProjects(1);
    });

    // Tooltip
    $('.page-header-wrapper [data-toggle="tooltip"]').tooltip({
        container: 'body',
        placement: 'bottom',
        trigger: 'hover'
    });

    // Search Enter key
    $('#searchProject').on('keypress', function(e) {
        if (e.which == 13) {
            loadProjects(1);
        }
    });

    // ✅ DATE PICKERS WITH CLEAR ICON SUPPORT
    $("#form_date").datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        numberOfMonths: 1,
        onSelect: function(selectedDate) {
            $("#to_date").datepicker("option", "minDate", selectedDate);
            $('#form_date').next('.clear-date').show(); // show ❌
        }
    });

    $("#to_date").datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        numberOfMonths: 1,
        onSelect: function(selectedDate) {
            $("#form_date").datepicker("option", "maxDate", selectedDate);
            $('#to_date').next('.clear-date').show(); // show ❌
        }
    });

    // ✅ SHOW / HIDE CLEAR ICON
    $('#form_date, #to_date').on('change', function () {
        if ($(this).val() !== '') {
            $(this).next('.clear-date').show();
        } else {
            $(this).next('.clear-date').hide();
        }
    });

    // Per-page select (initialized after table markup in initProjectFilterSelect2)
    $(document).on('change', '#perPage', function() {
        loadProjects(1);
    });

});
        
        // Main function to load projects via AJAX


function clearDate(id) {
    $('#' + id).val('');
    $('#' + id).next('.clear-date').hide();

    // Reset datepicker limits also
    if (id === 'form_date') {
        $("#to_date").datepicker("option", "minDate", null);
    }
    if (id === 'to_date') {
        $("#form_date").datepicker("option", "maxDate", null);
    }

    loadProjects(1); // reload data
}


function loadProjects(page) {
currentPage = page;

var limit = $('#perPage').val();
var search = $.trim($('#searchProject').val() || '');
var department = $('#department').val() || [];
var manager = $('#manager_name').val() || [];
if (!$.isArray(department)) {
    department = department ? [department] : [];
}
if (!$.isArray(manager)) {
    manager = manager ? [manager] : [];
}
var from_year = $('#from_year').val();
var from_month = $('#from_month').val();

var to_year = $('#to_year').val();
var to_month = $('#to_month').val();

var status = window.projectStatus || '';
var billing_type = window.billingType || '';
var client_Id = $('#client_Id').val() || '';
var project_Id = $('#filter_project_Id').val() || '';

console.log("Department :", department);
console.log("Manager :", manager);
console.log("From Year :", from_year);
console.log("From Month :", from_month);
console.log("To Year :", to_year);
console.log("To Month :", to_month);
console.log("Status :", status);
console.log("Billing :", billing_type);

    $('#projectTableBody').html('<tr><td colspan="9" class="text-center">Loading...</td></tr>');

    $.ajax({
        type: "POST",
        url: baseUrl + "projects/getProjectsAjax",
        traditional: true,
       data: {
    page: page,
    limit: limit,
    search: search,
    department: department,
    manager: manager,

    from_year: from_year,
    from_month: from_month,

    to_year: to_year,
    to_month: to_month,

    status: status,
    sort_by: sortBy,
    billing_type: billing_type,
    client_Id: client_Id,
    project_Id: project_Id,
    sort_order: sortOrder
},
        dataType: 'json',

        // ✅ FULL SUCCESS BLOCK WITH COUNTS
        success: function(response) {
            if (response.success) {

                updateSortIcons();

                // TABLE DATA
                renderProjects(response.data, response.pagination);

                // PAGINATION
                renderPagination(response.pagination);

                // RECORD INFO
                updateRecordInfo(response.pagination);

                // ✅ COUNTS (MAIN FIX)
                if (response.counts) {
                    $('#count_process').text(response.counts.process || 0);
                    $('#count_hold').text(response.counts.hold || 0);
                    $('#count_closed').text(response.counts.closed || 0);
                }

            } else {
                $('#projectTableBody').html('<tr><td colspan="9">No Data</td></tr>');

                // ✅ RESET COUNTS IF NO DATA
                $('#count_process').text(0);
                $('#count_hold').text(0);
                $('#count_closed').text(0);
            }
        },

        error: function() {
            $('#projectTableBody').html('<tr><td colspan="9">Error loading data</td></tr>');

            // ✅ RESET COUNTS ON ERROR
            $('#count_process').text(0);
            $('#count_hold').text(0);
            $('#count_closed').text(0);
        }
    });
}
        // ✅ When clicking status buttons
function filterStatus(value) {

    // Keep existing values
    window.projectStatus = window.projectStatus || '';
    window.billingType = window.billingType || '';

    // Billing Type Buttons
    if (value === 'Hourly') {
        window.billingType = 'Hourly';
    }
    else if (value === 'Monthly') {
        window.billingType = 'Monthly';
    }

    // Status Buttons
    else if (value === 'Process') {
        window.projectStatus = 'Process';
    }
    else if (value === 'On Hold') {
        window.projectStatus = 'On Hold';
    }
    else if (value === 'Closed') {
        window.projectStatus = 'Closed';
    }

    // ALL Button
    else if (value === 'All') {
        window.projectStatus = '';
        window.billingType = '';
    }

    console.log("Billing:", window.billingType);
    console.log("Status:", window.projectStatus);

    loadProjects(1);
}

// ✅ Reset status filter
function resetStatus() {
    window.projectStatus = '';
    loadProjects(1);
}
        // Render projects table
function renderProjects(projects, pagination) {
    var html = '';
    var startNum = pagination.startRecord;
    
    if (projects.length === 0) {
        html = '<tr><td colspan="11" class="text-center no-data-cell">';
        html += '<div class="no-data"><i class="fa fa-folder-open-o fa-3x"></i><p>No projects found</p></div>';
        html += '</td></tr>';
    } else {
        $.each(projects, function(index, project) {
            var statusBadgeClass = '';
            var statusName = '';
            var statusIcon = '';
            
            // Status
            if (project.status == 'Process') {
                statusBadgeClass = 'status-badge status-process';
                statusIcon = 'fa-check-circle';
                statusName = 'In Process';
            } else if (project.status == 'On Hold') {
                statusBadgeClass = 'status-badge status-hold';
                statusIcon = 'fa-pause-circle';
                statusName = 'On Hold';
            } else if (project.status == 'Billing Complete') {
                statusBadgeClass = 'status-badge status-billing';
                statusIcon = 'fa-money';
                statusName = 'Billing Done';
            } else if (project.status == 'Pending') {
                statusBadgeClass = 'status-badge status-pending';
                statusIcon = 'fa-clock-o';
                statusName = 'Pending';
            } else {
                statusBadgeClass = 'status-badge status-closed';
                statusIcon = 'fa-times-circle';
                statusName = 'Closed';
            }
            
            // // Created Date
            // var createdDate = '';
            // if (project.created_at) {
            //     var dateParts = project.created_at.split(' ')[0].split('-');
            //     var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            //     createdDate = dateParts[2] + '-' + months[parseInt(dateParts[1]) - 1] + '-' + dateParts[0];
            // }

      var startDate = '-';
if (project.project_start_date) {
    var sd = project.project_start_date.split('-');
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    startDate = sd[2] + '-' + months[parseInt(sd[1]) - 1] + '-' + sd[0];
}


            // End Date
       var endDate = '-';
if (project.project_end_date) {
    var ed = project.project_end_date.split('-');
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    endDate = ed[2] + '-' + months[parseInt(ed[1]) - 1] + '-' + ed[0];
}
            
            html += '<tr class="project-row" id="delProjectRow' + project.project_Id + '">';

            // Department
            html += '<td class="type-cell"><span class="type-badge">' + ucfirst(project.project_type || '-') + '</span></td>';

            // Client
            html += '<td class="client-cell">' + ucfirst(project.client_name || '-') + '</td>';

            // Project Name
            html += '<td class="pname-cell"><span class="project-name">' + ucfirst(project.project_name || '') + '</span></td>';

            // Project Number
            html += '<td class="pnumber-cell"><span class="project-number">' + (project.project_number || '-') + '</span></td>';

            // Project Manager
            html += '<td class="creator-cell"><span class="creator-badge"><i class="fa fa-user"></i> ' + ucfirst(project.name || '') + '</span></td>';

            // Billing Type
            html += '<td class="billing-cell">' + ucfirst(project.man_days || '-') + '</td>';


            // Estimated Hours
html += '<td class="hours-cell">' + (project.estimated_hours || '-') + '</td>';


            // Status
            html += '<td class="status-cell">';
            if (project.empId == loggedInEmpId) {
                html += '<span id="changeStatusRow_' + project.project_Id + '">';
                html += '<a class="' + statusBadgeClass + '" style="cursor:pointer;" data-toggle="modal" data-target="#comment_status_model_' + project.project_Id + '"><i class="fa ' + statusIcon + '"></i> ' + statusName + '</a>';
                html += '</span>';
            } else {
                html += '<span onclick="alert(\'Access Restricted: You Did Not Create This Project.\');" class="' + statusBadgeClass + '" style="cursor:pointer;"><i class="fa ' + statusIcon + '"></i> ' + statusName + '</span>';
            }
            html += '</td>';

            // ✅ Start Date
            html += '<td class="date-cell"><i class="fa fa-calendar"></i> ' + startDate + '</td>';

            // ✅ End Date
         html += '<td class="date-cell"><i class="fa fa-calendar"></i> ' + endDate + '</td>';

            // Created Date
            // html += '<td class="date-cell"><i class="fa fa-calendar"></i> ' + createdDate + '</td>';

            // Actions
            html += '<td class="action-cell">';
            html += '<div class="action-buttons-group">';
            html += '<a href="' + baseUrl + 'projects/projectInformaton/' + project.project_Id + '" class="action-btn btn-view" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></a>';
            html += '<a href="' + baseUrl + 'projects/add/' + project.project_Id + '" class="action-btn btn-edit" data-toggle="tooltip" title="Edit Project"><i class="fa fa-pencil"></i></a>';
            html += '<a href="' + baseUrl + 'projects/cloneproject?project_Id=' + project.project_Id + '&cloneVal=passVal" class="action-btn btn-clone" data-toggle="tooltip" title="Clone Project"><i class="fa fa-copy"></i></a>';
            html += '</div>';
            html += '</td>';

            html += '</tr>';
            
            // Status modal
            if (project.empId == loggedInEmpId) {
                generateStatusModal(project);
            }
        });
    }
    
    $('#projectTableBody').html(html);

    $('[data-toggle="tooltip"]').tooltip({
        container: 'body',
        placement: 'top',
        trigger: 'hover'
    });
}
        
        // Generate status update modal
        function normalizeProjectDbDate(value) {
            if (!value) {
                return '';
            }
            var raw = String(value).split(' ')[0];
            if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
                return raw;
            }
            return raw;
        }

        function endDateAfterStartDate(startDate, endDate) {
            if (!startDate || !endDate) {
                return false;
            }
            var s = startDate.split('-');
            var e = endDate.split('-');
            if (s.length !== 3 || e.length !== 3) {
                return false;
            }
            var startTs = new Date(parseInt(s[0], 10), parseInt(s[1], 10) - 1, parseInt(s[2], 10)).getTime();
            var endTs = new Date(parseInt(e[0], 10), parseInt(e[1], 10) - 1, parseInt(e[2], 10)).getTime();
            return endTs > startTs;
        }

        function initProjectStatusEndDatepicker(projectId, startDate, endDate) {
            var $end = $('#project_end_date_' + projectId);
            if (!$end.length || !$.fn.datepicker) {
                return;
            }
            if ($end.hasClass('hasDatepicker')) {
                $end.datepicker('destroy');
            }
            var minEnd = null;
            if (startDate) {
                var parts = startDate.split('-');
                if (parts.length === 3) {
                    minEnd = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10) + 1);
                }
            }
            $end.datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                minDate: minEnd
            });
            if (endDate) {
                $end.datepicker('setDate', endDate);
            }
        }

        function generateStatusModal(project) {
            var isClosed = (project.status === 'Closed');
            var startDb = normalizeProjectDbDate(project.project_start_date);
            var endDb = normalizeProjectDbDate(project.project_end_date);
            var modalId = 'comment_status_model_' + project.project_Id;
            var formId = 'comment_reject_' + project.project_Id;
            var errId = 'project_status_error_' + project.project_Id;

            var modalHtml = '<div id="' + modalId + '" class="modal fade project-status-modal" role="dialog">';
            modalHtml += '<div class="modal-dialog">';
            modalHtml += '<div class="modal-content">';
            modalHtml += '<div class="modal-header">';
            modalHtml += '<button type="button" class="close" data-dismiss="modal">&times;</button>';
            if (isClosed) {
                modalHtml += '<h4 class="modal-title"><i class="fa fa-unlock"></i> Activate Project</h4>';
            } else {
                modalHtml += '<h4 class="modal-title"><i class="fa fa-cog"></i> Update Project Status</h4>';
            }
            modalHtml += '</div>';
            modalHtml += '<div class="modal-body">';
            modalHtml += '<form class="comment_reject" name="comment_status_ok" id="' + formId + '" method="post" action="#">';
            modalHtml += '<input type="hidden" name="project_status_update_id" value="' + project.project_Id + '">';
            modalHtml += '<p class="project-status-modal-intro"><strong>Project:</strong> ' + $('<div>').text(project.project_name || '').html() + '</p>';

            if (isClosed) {
                modalHtml += '<div class="form-group">';
                modalHtml += '<label>Start Date</label>';
                modalHtml += '<input type="text" class="form-control" value="' + startDb + '" readonly>';
                modalHtml += '</div>';
                modalHtml += '<div class="form-group">';
                modalHtml += '<label>End Date <span class="text-danger">*</span></label>';
                modalHtml += '<input type="text" class="form-control project-status-end-date" name="project_end_date" id="project_end_date_' + project.project_Id + '" value="' + endDb + '" readonly placeholder="Select end date" required>';
                modalHtml += '</div>';
                modalHtml += '<div class="form-group">';
                modalHtml += '<label>Status</label>';
                modalHtml += '<select name="status" class="form-control project-status-select" required>';
                modalHtml += '<option value="Process" selected>In Process</option>';
                modalHtml += '<option value="On Hold">On Hold</option>';
                modalHtml += '<option value="Closed">Closed</option>';
                modalHtml += '</select>';
                modalHtml += '</div>';
            } else {
                modalHtml += '<div class="form-group">';
                modalHtml += '<label>Status</label>';
                modalHtml += '<select name="status" class="form-control project-status-select" required>';
                modalHtml += '<option value="Process"' + (project.status == 'Process' ? ' selected' : '') + '>In Process</option>';
                modalHtml += '<option value="On Hold"' + (project.status == 'On Hold' ? ' selected' : '') + '>On Hold</option>';
                modalHtml += '<option value="Closed"' + (project.status == 'Closed' ? ' selected' : '') + '>Closed</option>';
                modalHtml += '</select>';
                modalHtml += '</div>';
            }

            modalHtml += '<p id="' + errId + '" class="project-status-error text-danger" style="display:none;"></p>';
            modalHtml += '<div class="text-center project-status-modal-actions">';
            modalHtml += '<button class="btn btn-primary btn-lg" type="submit"><i class="fa fa-save"></i> ' + (isClosed ? 'Activate Project' : 'Update Status') + '</button>';
            modalHtml += '</div>';
            modalHtml += '</form></div></div></div></div>';

            $('#' + modalId).remove();
            $('#statusModalContainer').append(modalHtml);

            if (isClosed) {
                $('#' + modalId).on('shown.bs.modal', function() {
                    initProjectStatusEndDatepicker(project.project_Id, startDb, endDb);
                });
            }

            $('#' + formId).off('submit').on('submit', function(e) {
                e.preventDefault();
                var $err = $('#' + errId);
                $err.hide().text('');

                if (isClosed) {
                    var endVal = $.trim($('#project_end_date_' + project.project_Id).val() || '');
                    if (!endDateAfterStartDate(startDb, endVal)) {
                        $err.text('End Date must be greater than Start Date.').show();
                        return;
                    }
                }

                var formData = $(this).serialize();
                var projectId = project.project_Id;

                $.ajax({
                    type: 'POST',
                    url: baseUrl + 'projects/update_project_master_status',
                    data: formData,
                    beforeSend: function() {
                        $('#changeStatusRow_' + projectId).html('<i class="fa fa-spinner fa-spin"></i>');
                    },
                    success: function(response) {
                        $('#' + modalId).modal('hide');
                        if (typeof loadProjects === 'function') {
                            loadProjects(typeof currentPage !== 'undefined' ? currentPage : 1);
                        } else {
                            $('#changeStatusRow_' + projectId).html(response);
                        }
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseText && $.trim(xhr.responseText)) ? $.trim(xhr.responseText) : 'Unable to update project status.';
                        $err.text(msg).show();
                        loadProjects(typeof currentPage !== 'undefined' ? currentPage : 1);
                    }
                });
            });
        }
        
        // Render pagination controls
        function renderPagination(pagination) {
            var html = '';
            var totalPages = pagination.totalPages;
            var currentPage = pagination.currentPage;
            
            if (totalPages <= 1) {
                $('#paginationControls').html('');
                return;
            }
            
            // Previous button
            html += '<li class="' + (currentPage == 1 ? 'disabled' : '') + '">';
            html += '<a href="javascript:void(0);" onclick="' + (currentPage > 1 ? 'loadProjects(' + (currentPage - 1) + ')' : '') + '" aria-label="Previous">';
            html += '<span aria-hidden="true">&laquo;</span></a></li>';
            
            // Page numbers
            var startPage = Math.max(1, currentPage - 2);
            var endPage = Math.min(totalPages, currentPage + 2);
            
            // First page
            if (startPage > 1) {
                html += '<li><a href="javascript:void(0);" onclick="loadProjects(1)">1</a></li>';
                if (startPage > 2) {
                    html += '<li class="disabled"><a href="javascript:void(0);">...</a></li>';
                }
            }
            
            // Page range
            for (var i = startPage; i <= endPage; i++) {
                html += '<li class="' + (i == currentPage ? 'active' : '') + '">';
                html += '<a href="javascript:void(0);" onclick="loadProjects(' + i + ')">' + i + '</a></li>';
            }
            
            // Last page
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += '<li class="disabled"><a href="javascript:void(0);">...</a></li>';
                }
                html += '<li><a href="javascript:void(0);" onclick="loadProjects(' + totalPages + ')">' + totalPages + '</a></li>';
            }
            
            // Next button
            html += '<li class="' + (currentPage == totalPages ? 'disabled' : '') + '">';
            html += '<a href="javascript:void(0);" onclick="' + (currentPage < totalPages ? 'loadProjects(' + (currentPage + 1) + ')' : '') + '" aria-label="Next">';
            html += '<span aria-hidden="true">&raquo;</span></a></li>';
            
            $('#paginationControls').html(html);
        }
        
        // Update record info display
        function updateRecordInfo(pagination) {
            if (pagination.totalRecords > 0) {
                var info = '<i class="fa fa-database"></i> Showing ' + pagination.startRecord + ' - ' + pagination.endRecord + ' of ' + pagination.totalRecords + ' projects';
                $('#recordInfo').html(info);
                $('.record-info-wrapper').show();
            } else {
                $('#recordInfo').html('<i class="fa fa-database"></i> No projects found');
            }
        }
        
        // Helper function to capitalize first letter
        function ucfirst(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
        
        // Delete project function
        function delete_project(project_Id) {
            var answer = confirm("Are you sure you want to delete project?");
            if (answer) {
                $.ajax({
                    type: "POST",
                    url: baseUrl + "projects/delete",
                    data: "project_Id=" + project_Id,
                    beforeSend: function() {
                        $('#delProjectRow' + project_Id).html('<td colspan="9"><i class="fa fa-spinner"></i> Deleting...</td>');
                    },
                    success: function(response) {
                        $('#delProjectRow' + project_Id).fadeOut(300, function() {
                            $(this).remove();
                            loadProjects(currentPage); // Reload current page
                        });
                    }
                });
            }
        }

        $(function() {
            $("form[name='project_report_search']").validate({
                rules: {
                    form_date: { required: true },
                    to_date: { required: true }
                },
                messages: {
                    form_date: "Please Select From Date",
                    to_date: "Please Select To Date"
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        });
    </script>

    <style>
        /* Page Header Styles */
        .page-header-wrapper {
            margin-bottom: 25px;
        }
        .page-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .title-section h1 {
            font-size: 26px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .title-section h1 i {
            color: #3498db;
        }
        .title-section .subtitle {
            color: #7f8c8d;
            font-size: 14px;
            margin: 5px 0 0 0;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn-action {
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border: none;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-action i {
            margin-right: 5px;
        }
        .btn-action.btn-success {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
        }
        .btn-action.btn-info {
            background: linear-gradient(135deg, #2980b9, #3498db);
        }
        .btn-action.btn-warning {
            background: linear-gradient(135deg, #e67e22, #f39c12);
            color: #fff;
        }

        /* Card Styles */
        .project-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .project-card .card-body {
            padding: 25px;
        }

        /* Filter Section - details in inline filter panel styles */
        .filter-section {
            margin-bottom: 20px;
        }
        .search-box {
            position: relative;
            display: flex;
            align-items: center;
        }
        .search-icon {
            position: absolute;
            left: 15px;
            color: #95a5a6;
            z-index: 10;
        }
        .search-input {
            padding-left: 40px;
            height: 45px;
            border-radius: 8px 0 0 8px;
            border: 2px solid #e0e0e0;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .search-input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        .search-btn {
            height: 45px;
            border-radius: 0 8px 8px 0;
            padding: 0 20px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            font-weight: 500;
        }
        .search-btn:hover {
            background: linear-gradient(135deg, #2980b9, #1f6dad);
        }
        .per-page-select {
            height: 45px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            cursor: pointer;
            font-size: 14px;
        }
        .per-page-select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        .record-info-wrapper {
            display: inline-block;
            padding: 12px 20px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: #fff;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 500;
        }

        /* Table Styles - no horizontal scrollbar; table fits container */
        .table-wrapper {
            overflow-x: hidden;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .project-table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            table-layout: fixed;
        }
        .project-table thead tr {
            background: linear-gradient(135deg, #2c3e50, #34495e);
        }
        .project-table thead th {
            color: #fff;
            font-weight: 600;
            font-size: 13px;
            padding: 18px 12px;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.4;
        }
        .project-table thead th.sortable {
            cursor: pointer;
            user-select: none;
        }
        .project-table thead th.sortable:hover {
            background: rgba(255,255,255,0.1);
        }
        .project-table thead th.sortable .sort-icon {
            margin-left: 6px;
            opacity: 0.7;
        }
        .project-table thead th.sortable.sort-active .sort-icon {
            opacity: 1;
        }
        .project-table tbody tr {
            transition: all 0.2s ease;
        }
        .project-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .project-table tbody tr:hover {
            background-color: #e8f4fd;
            transform: scale(1.001);
        }
        .project-table tbody td {
            padding: 18px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #eee;
            font-size: 13px;
            line-height: 1.5;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Cell Styles */
        .sno-cell {
            font-weight: 600;
            color: #7f8c8d;
            text-align: center;
        }
        .project-number {
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
        }
        .project-name {
            font-weight: 600;
            color: #2c3e50;
        }
        .type-badge {
            background: #e8f4fd;
            color: #2980b9;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }
        .creator-badge {
            background: linear-gradient(135deg, #00b894, #00cec9);
            color: #fff;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .date-cell {
            color: #7f8c8d;
            font-size: 12px;
        }
        .date-cell i {
            color: #3498db;
            margin-right: 5px;
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .status-badge:hover {
            transform: scale(1.05);
            text-decoration: none;
        }
        .status-process {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: #fff;
        }
        .status-hold {
            background: linear-gradient(135deg, #f39c12, #f1c40f);
            color: #fff;
        }
        .status-billing {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            color: #fff;
        }
        .status-pending {
            background: linear-gradient(135deg, #e67e22, #d35400);
            color: #fff;
        }
        .status-closed {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: #fff;
        }

        /* Action Buttons */
        .action-buttons-group {
            display: flex;
            gap: 6px;
            justify-content: center;
        }
        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #fff;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            color: #fff;
            text-decoration: none;
        }
        .btn-view {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }
        .btn-edit {
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }
        .btn-clone {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
        }

        /* Pagination Styles */
        .pagination-section {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .custom-pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .custom-pagination li a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            border-radius: 8px;
            background: #f8f9fa;
            color: #2c3e50;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            border: 2px solid transparent;
        }
        .custom-pagination li a:hover {
            background: #e8f4fd;
            border-color: #3498db;
            color: #3498db;
        }
        .custom-pagination li.active a {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: #fff;
            border-color: transparent;
        }
        .custom-pagination li.disabled a {
            background: #f0f0f0;
            color: #bdc3c7;
            cursor: not-allowed;
        }

        /* Loading & No Data */
        .loading-cell {
            padding: 60px 20px !important;
        }
        .loading-spinner {
            text-align: center;
            color: #3498db;
        }
        .loading-spinner p {
            margin-top: 15px;
            color: #7f8c8d;
            font-size: 14px;
        }
        .no-data-cell {
            padding: 60px 20px !important;
        }
        .no-data {
            text-align: center;
            color: #bdc3c7;
        }
        .no-data i {
            margin-bottom: 15px;
        }
        .no-data p {
            font-size: 16px;
            margin: 0;
        }

        /* Modal Improvements */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .modal-header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: #fff;
            border-radius: 12px 12px 0 0;
            padding: 18px 25px;
        }
        .modal-header .close {
            color: #fff;
            opacity: 0.8;
        }
        .modal-header .modal-title {
            font-weight: 600;
        }
        .modal-body {
            padding: 25px;
        }

        /* Status Options in Modal */
        .status-options {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .status-option {
            cursor: pointer;
            margin: 0;
        }
        .status-option input[type="radio"] {
            display: none;
        }
        .status-option-inner {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 15px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 3px solid transparent;
        }
        .status-process-opt {
            background: #e8f8f0;
            color: #27ae60;
        }
        .status-option input:checked + .status-process-opt {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: #fff;
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        }
        .status-hold-opt {
            background: #fef5e7;
            color: #f39c12;
        }
        .status-option input:checked + .status-hold-opt {
            background: linear-gradient(135deg, #f39c12, #f1c40f);
            color: #fff;
            box-shadow: 0 5px 15px rgba(243, 156, 18, 0.4);
        }
        .status-closed-opt {
            background: #fdeaea;
            color: #e74c3c;
        }
        .status-option input:checked + .status-closed-opt {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: #fff;
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
        }
        .status-option:hover .status-option-inner {
            transform: translateY(-3px);
        }
        .project-status-modal .project-status-modal-intro {
            margin-bottom: 16px;
            color: #7f8c8d;
        }
        .project-status-modal .form-group label {
            font-weight: 600;
            color: #333;
        }
        .project-status-modal .form-control[readonly] {
            background: #f5f5f5;
            cursor: not-allowed;
        }
        .project-status-modal-actions {
            margin-top: 20px;
        }
        .project-status-error {
            margin-top: 10px;
            font-weight: 600;
        }

        /* Tooltip alignment - show above/below trigger without clipping */
        .tooltip {
            z-index: 1070;
        }
        .tooltip-inner {
            max-width: 220px;
            padding: 8px 12px;
            text-align: center;
            white-space: nowrap;
        }
        .tooltip.top {
            padding: 5px 0;
        }
        .tooltip.top .tooltip-arrow {
            bottom: 0;
            left: 50%;
            margin-left: -5px;
            border-width: 5px 5px 0;
            border-top-color: #000;
        }
        .tooltip-inner {
            border-radius: 6px;
        }

        /* Make table fully bordered */
.project-table {
    border-collapse: collapse !important;
    width: 100%;
}

/* Header borders */
.project-table thead th {
    border: 1px solid #dcdcdc;
}

/* Body cell borders */
.project-table tbody td {
    border: 1px solid #e0e0e0;
}

/* Optional: outer border */
.project-table {
    border: 1px solid #dcdcdc;
}

/* Keep hover effect without breaking borders */
.project-table tbody tr:hover {
    background-color: #e8f4fd;
}

    
        /* Responsive */
        @media (max-width: 768px) {
            .page-title {
                flex-direction: column;
                align-items: flex-start;
            }
            .filter-section .row > div {
                margin-bottom: 10px;
            }
            .record-info-wrapper {
                display: block;
                text-align: center;
            }
            .btn-action span {
                display: none;
            }
            .status-options {
                flex-direction: column;
            }
        }
        

.table-count{
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    padding: 0.5%;
    margin-bottom: 1%;
}       
.summary-card {
    width: 40%;
    margin: 20px auto;
    text-align: center;
}
.summary-title {
    color: #6a1b9a;
    font-weight: bold;
    margin-bottom: 15px;
}
.summary-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 16px;
}
.summary-table thead {
    background-color: #2f5d7c;
    color: #fff;
}
.summary-table th,
.summary-table td {
    padding: 12px;
    border: 1px solid #ccc;
    text-align: center;
}
.summary-table tbody tr:nth-child(odd) {
    background-color: #e8eef5;
}
.summary-table tbody tr:nth-child(even) {
     background-color: #e8eef5;
}
/* Count Colors */
.count-green {
    color: green;
    font-weight: bold;
    background-color: #e5f8ed;
}
.count-orange {
    color: orange;
    font-weight: bold;
    background-color: #e5f8ed;
}
.count-red {
    color: red;
    font-weight: bold;
    background-color: #e5f8ed;
}

.date-input-box {
    position: relative;
}

.clear-date {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 18px;
    color: red;
    display: none;
}

.date-input-box input:not(:placeholder-shown) + .clear-date {
    display: block;
}


       

    </style>
    <?php $this->load->view('includes/cRMFooter'); ?>
    <!-- Inlude Footer here END-->