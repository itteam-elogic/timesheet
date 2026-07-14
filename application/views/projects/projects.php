    <!-- Inlude Header here -->
    <?php $this->load->view('includes/cRMHeader'); 
    $createdUser = $this->session->userdata['logged_in_timesheet']['empId'];
    $loggedInEmpId = $this->session->userdata['logged_in_timesheet']['empId'];
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
    $('#from_year').val('').removeClass('selected-box');
    $('#from_month').val('').removeClass('selected-box');

    $('#to_year').val('').removeClass('selected-box');
    $('#to_month').val('').removeClass('selected-box');

    // Hide clear icons
    $('#from_year').siblings('.clear-icon').hide();
    $('#from_month').siblings('.clear-icon').hide();

    $('#to_year').siblings('.clear-icon').hide();
    $('#to_month').siblings('.clear-icon').hide();

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

    // ADD THESE
    var currentSortBy = sortBy;
    var currentSortOrder = sortOrder;

    var url = baseUrl + "projects/downloadExcel?" +
        "search=" + encodeURIComponent(search) +
        "&department=" + encodeURIComponent(JSON.stringify(department)) +
        "&manager=" + encodeURIComponent(JSON.stringify(manager)) +
        "&from_year=" + from_year +
        "&from_month=" + from_month +
        "&to_year=" + to_year +
        "&to_month=" + to_month +
        "&status=" + encodeURIComponent(status) +
        "&sort_by=" + encodeURIComponent(currentSortBy) +
        "&sort_order=" + encodeURIComponent(currentSortOrder);

    window.location.href = url;
}
</script>


                </div>
            </div>
        </div>
        
        <div class="filter-section">
    <div class="row">

        <!-- Department -->
<div class="col-md-2">
    <div class="form-group">
        <label class="control-label">Department</label>
        
        

        <?php
        $selectedDept = $this->input->post('department') ? $this->input->post('department') : [];
        ?>

        <?php if($createdUser == '149'): ?>

            <select class="form-control <?php echo (!empty($selectedDept)) ? 'form-control-sm' : ''; ?>"
                    id="department"
                    name="department[]"
                    multiple>
                    

                <option value="MEP" <?php echo (in_array('MEP', $selectedDept)) ? 'selected' : ''; ?>>MEP</option>

            </select>

        <?php elseif($createdUser == '47'): ?>

            <select class="form-control <?php echo (!empty($selectedDept)) ? 'form-control-sm' : ''; ?>" 
                    id="department" 
                    name="department[]" 
                    multiple>

                <option value="Architectural" <?php echo (in_array('Architectural', $selectedDept)) ? 'selected' : ''; ?>>
                    Architectural - Structural - 3D Visualization
                </option>

            </select>
<?php else: ?>    

    <select class="form-control <?php echo (!empty($selectedDept)) ? 'form-control-sm' : ''; ?>" 
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


 <script>
	$('#department').select2({
    placeholder: "Select Department",
    allowClear: true,
    width: '100%'
});
</script>

<div class="col-md-3">
    <div class="form-group" style="position:relative;">

        <label><b>Project Name / Number</b></label>

        <input type="text"
               name="search_text"
               id="searchProject"
               class="form-control"
               placeholder="Project Name / Number / Client"
               autocomplete="off">

        <div id="projectSuggestions"></div>

    </div>
</div>

<style>
#projectSuggestions{
    position:absolute;
    top:70px;
    left:0;
    width:100%;
    background:#fff;
    border:1px solid #ddd;
    z-index:99999;
    display:none;
    max-height:250px;
    overflow-y:auto;
    box-shadow:0 2px 8px rgba(0,0,0,0.15);
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

    });

    $(document).click(function(e){

        if(!$(e.target).closest('#searchProject,#projectSuggestions').length){
            $('#projectSuggestions').hide();
        }

    });

});
</script>

        <!-- Project Manager -->

<div class="col-md-3">
<div class="form-group">
<label><b>Project Managers</b></label>

<?php
$selectedManagers = (array) $this->input->post('manager_name');
?>

<select name="manager_name[]" id="manager_name" class="form-control" multiple>

<option value="41" <?php if(in_array('41',$selectedManagers)) echo "selected"; ?>>Sandeep Anupati</option>
<option value="394" <?php if(in_array('394',$selectedManagers)) echo "selected"; ?>>Shivani Patil</option>
<option value="71" <?php if(in_array('71',$selectedManagers)) echo "selected"; ?>>Siva Krishna</option>
<option value="448" <?php if(in_array('448',$selectedManagers)) echo "selected"; ?>>Rahul Kumar</option>
<option value="230" <?php if(in_array('230',$selectedManagers)) echo "selected"; ?>>Srinivas Gollakonda</option>
<option value="146" <?php if(in_array('146',$selectedManagers)) echo "selected"; ?>>Syed Afsar</option>
<option value="149" <?php if(in_array('149',$selectedManagers)) echo "selected"; ?>>Syed Farhan</option>
<option value="47" <?php if(in_array('47',$selectedManagers)) echo "selected"; ?>>Pradip Chauhan</option>
<option value="53" <?php if(in_array('53',$selectedManagers)) echo "selected"; ?>>Rajanikanth Bhasuthkar</option>
<option value="523" <?php if(in_array('523',$selectedManagers)) echo "selected"; ?>>Nikhil Bachawal</option>

</select>
</div>
</div>

<script>
$(document).ready(function() {
    $('#manager_name').select2({
        placeholder: "Select Managers",
        width: '100%'
    });
});
</script>


    <div class="row">

    <!-- FROM -->
    <div class="col-md-2">
        <div class="filter-box">
            <div class="label-title">From</div>

            <!-- From Year -->
             <div class="select-row">
            <div class="select-container">
                <select class="dropdown-box clearable-select" id="from_year" name="from_year">
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

                <span class="clear-icon" onclick="clearDropdown('from_year')">&times;</span>
            </div>

            <!-- From Month -->
            <div class="select-container mt-2">
               <select class="dropdown-box clearable-select" id="from_month" name="from_month">
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

                <span class="clear-icon" onclick="clearDropdown('from_month')">&times;</span>
            </div>
             </div>
        </div>
    </div>

    <!-- TO -->
    <div class="col-md-2">
        <div class="filter-box">
            <div class="label-title">To</div>

            <!-- To Year -->
             <div class="select-row">
            <div class="select-container">
               <select class="dropdown-box clearable-select" id="to_year" name="to_year">
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

                <span class="clear-icon" onclick="clearDropdown('to_year')">&times;</span>
            </div>

            <!-- To Month -->
            <div class="select-container mt-2">
                <select class="dropdown-box clearable-select" id="to_month" name="to_month">
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

                <span class="clear-icon" onclick="clearDropdown('to_month')">&times;</span>
            </div>
             </div>
        </div>

    </div>

<style>
/* Filter Box */
.filter-box {
    border: 2px solid #ddd;
    border-radius: 8px;
    padding: 8px;
    background: #f9f9f9;
}

/* Keep Year + Month Side By Side */
.select-row {
    display: flex;
    gap: 4px;
    margin-top: 5px;
}

/* Select Container */
.select-container {
    position: relative;
    flex: 1;
}

/* Dropdown Styling */
.dropdown-box {
    width: 92%;
    height: 32px;
    padding: 5px 28px 5px 8px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background: #fff;
    font-size: 12px;
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

/* Clear Icon */
.clear-icon {
    position: absolute;
    right: 27px;
    top: 50%;
    transform: translateY(-50%);
    color: #fff;
    font-size: 15px;
    cursor: pointer;
    display: none;
    z-index: 10;
}

/* Label */
.label-title {
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 13px;
}
</style>


<script>
    $('#filterBtn').click(function () {

    var from_year = $('#from_year').val();
    var from_month = $('#from_month').val();

    var to_year = $('#to_year').val();
    var to_month = $('#to_month').val();

    var department = $('#department').val();
    var project_manager = $('#project_manager').val();

    $.ajax({
        url: "<?= base_url('Projects/filterData') ?>",
        type: "POST",
        data: {
            from_year: from_year,
            from_month: from_month,
            to_year: to_year,
            to_month: to_month,
            department: department,
            project_manager: project_manager
        },
        success: function(response) {
            $('#tableData').html(response);
        }
    });

});
</script>


<script>
$(document).ready(function () {

    $('.clearable-select').on('change', function () {

        var clearBtn = $(this).siblings('.clear-icon');

        if ($(this).val() !== '') {

            // Show clear icon
            clearBtn.show();

            // Add selected background
            $(this).addClass('selected-box');

        } else {

            // Hide clear icon
            clearBtn.hide();

            // Remove selected background
            $(this).removeClass('selected-box');
        }

    });

});

/* Clear Dropdown */
function clearDropdown(id) {

    $('#' + id).val('');

    // Remove selected class
    $('#' + id).removeClass('selected-box');

    // Hide clear icon
    $('#' + id).siblings('.clear-icon').hide();

    // Reload projects
    loadProjects(1);
}
</script>

        <!-- Buttons -->
<!-- Buttons -->
<div class="col-md-12 text-center button-wrapper">

    <button class="btn btn-primary" onclick="loadProjects(1)">
        SEARCH
    </button>

    <button type="button" class="btn btn-hourly" onclick="filterStatus('Hourly')">
        HOURLY
    </button>

    <button type="button" class="btn btn-monthly" onclick="filterStatus('Monthly')">
        MONTHLY
    </button>

    <button type="button" class="btn btn-success" onclick="filterStatus('Process')">
        IN PROCESS
    </button>

    <button type="button" class="btn btn-warning" onclick="filterStatus('On Hold')">
        ON HOLD
    </button>

    <button type="button" class="btn btn-danger" onclick="filterStatus('Closed')">
        CLOSED
    </button>

    <button type="button" class="btn btn-info" onclick="filterStatus('All')">
        ALL
    </button>

</div>  

    </div>
</div>


</div>



<style>
.button-wrapper {
    margin-top: 30px;
    text-align: center;
}

.button-wrapper .btn {
    margin: 4px;
}
.btn-hourly {
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
                                    <select id="perPage" class="form-control per-page-select" onchange="loadProjects(1)">
                                       
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
                                        <th class="sortable" data-sort="Start Date" data-label="Start Date"><span>Start Date</span> <i class="sort-icon fa fa-sort"></i></th>
                                        <th class="sortable" data-sort="End Date" data-label="End Date"><span>End Date</span> <i class="sort-icon fa fa-sort"></i></th>
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

    // Select2
    $('#department').select2();

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
var search = $('#searchProject').val();
var department = $('#department').val();
var manager = $('#manager_name').val();
var from_year = $('#from_year').val();
var from_month = $('#from_month').val();

var to_year = $('#to_year').val();
var to_month = $('#to_month').val();

var status = window.projectStatus || '';
var billing_type = window.billingType || '';

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
    sort_order: sortOrder
},
        dataType: 'json',

        // ✅ FULL SUCCESS BLOCK WITH COUNTS
        success: function(response) {
            if (response.success) {

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
        function generateStatusModal(project) {
            var modalHtml = '<div id="comment_status_model_' + project.project_Id + '" class="modal fade" role="dialog">';
            modalHtml += '<div class="modal-dialog">';
            modalHtml += '<div class="modal-content">';
            modalHtml += '<div class="modal-header">';
            modalHtml += '<button type="button" class="close" data-dismiss="modal">&times;</button>';
            modalHtml += '<h4 class="modal-title"><i class="fa fa-cog"></i> Update Project Status</h4>';
            modalHtml += '</div>';
            modalHtml += '<div class="modal-body">';
            modalHtml += '<form class="comment_reject" name="comment_status_ok" id="comment_reject_' + project.project_Id + '" method="post" action="#">';
            modalHtml += '<input type="hidden" name="project_status_update_id" value="' + project.project_Id + '">';
            modalHtml += '<p style="margin-bottom:20px; color:#7f8c8d;"><strong>Project:</strong> ' + project.project_name + '</p>';
            modalHtml += '<div class="status-options">';
            modalHtml += '<label class="status-option ' + (project.status == 'Process' ? 'selected' : '') + '"><input type="radio" name="status" value="Process" ' + (project.status == 'Process' ? 'checked' : '') + ' required><span class="status-option-inner status-process-opt"><i class="fa fa-check-circle"></i> In Process</span></label>';
            modalHtml += '<label class="status-option ' + (project.status == 'On Hold' ? 'selected' : '') + '"><input type="radio" name="status" value="On Hold" ' + (project.status == 'On Hold' ? 'checked' : '') + '><span class="status-option-inner status-hold-opt"><i class="fa fa-pause-circle"></i> On Hold</span></label>';
            modalHtml += '<label class="status-option ' + (project.status == 'Closed' ? 'selected' : '') + '"><input type="radio" name="status" value="Closed" ' + (project.status == 'Closed' ? 'checked' : '') + '><span class="status-option-inner status-closed-opt"><i class="fa fa-times-circle"></i> Closed</span></label>';
            modalHtml += '</div>';
            modalHtml += '<div style="text-align:center; margin-top:25px;"><button class="btn btn-primary btn-lg" type="submit" style="padding:12px 40px; border-radius:8px; background:linear-gradient(135deg, #3498db, #2980b9); border:none;"><i class="fa fa-save"></i> Update Status</button></div>';
            modalHtml += '</form></div></div></div></div>';
            
            // Remove existing modal and add new one
            $('#comment_status_model_' + project.project_Id).remove();
            $('#statusModalContainer').append(modalHtml);
            
            // Bind form submit
            $('#comment_reject_' + project.project_Id).off('submit').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var projectId = project.project_Id;
                
                $.ajax({
                    type: "POST",
                    url: baseUrl + "projects/update_project_master_status",
                    data: formData,
                    beforeSend: function() {
                        $('#changeStatusRow_' + projectId).html('<i class="fa fa-spinner"></i>');
                    },
                    success: function(response) {
                        $('#changeStatusRow_' + projectId).html(response);
                        $('#comment_status_model_' + projectId).modal('hide');
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

        /* Filter Section */
        .filter-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
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