
    <!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
$createdUser = $this->session->userdata['logged_in_timesheet']['empId'];
?>
<!-- Inlude Header here END-->

<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1>Manage Employees ( Search by Department or Date Range )</h1>
    </div>
    <div><a class="btn btn-primary btn-flat" href="<?php echo base_url('employee/add'); ?>" data-toggle="tooltip" title="Add Employee"><i class="fa fa-lg fa-plus"></i></a>
      <?php echo (!empty($getEmployees)) ? '<a class="btn btn-warning btn-flat" data-toggle="tooltip" title="Download Employee Report" href="#" onclick="downloadExcel()"><i class="fa fa-file-excel-o"></i> Download Employee Report</a>' : ''; ?> 
      </div>
  </div>

    <div class="card">
		<div class="card-body">
			<div class="row">
				<!-- Search for employee with date wise and client , project wise as well. -->
				<div class="col-md-12">
					<div class="bs-component">
						<div class="tab-content" id="myTabContent">
							<!-- Employee Report adding block -->
							<form class="" name="employee_report_search" id="employee_report_search" method="post" action="<?php echo base_url('employee/employee_list_information');?>">
								<input type="hidden" name="status_filter" id="status_filter" value="<?php echo !empty($selected_status_filter) ? htmlspecialchars($selected_status_filter, ENT_QUOTES, 'UTF-8') : 'active'; ?>">
								<div class="tab-pane">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Department</label>
												<?php
													$allDepartments = function_exists('ts_department_options') ? ts_department_options() : array('Architectural','Structural','MEP','3D Visualization','2D Auto CAD','HR','Software','IT','Operations Manager','Accounting','Business Development','Others');
													$selectedDepartments = is_array($selected_department) ? $selected_department : (empty($selected_department) ? array() : array($selected_department));
													$selectedManagers = is_array($selected_project_manager) ? $selected_project_manager : (empty($selected_project_manager) ? array() : array($selected_project_manager));
												?>
												<?php if($createdUser == '149'):?>
													<select class="form-control <?php echo (!empty($selectedDepartments)) ? 'form-control-sm' : ''; ?>" id="department" name="department[]" multiple="multiple">
														<option value="MEP" <?php echo in_array('MEP', $selectedDepartments, true) ? 'selected' : ''; ?>>MEP</option>

												</select>

												<?php elseif($createdUser == '47'): ?>

													<select class="form-control <?php echo (!empty($selectedDepartments)) ? 'form-control-sm' : ''; ?>" id="department" name="department[]" multiple="multiple">

                                                    <option value="Architectural" <?php echo in_array('Architectural', $selectedDepartments, true) ? 'selected' : ''; ?>>Architectural - Structural - 3D Visualization</option>

												</select>

												<?php else: ?>
												<select class="form-control <?php echo (!empty($selectedDepartments)) ? 'form-control-sm' : ''; ?>" id="department" name="department[]" multiple="multiple">
														<?php foreach ($allDepartments as $deptOption): ?>
															<?php if ($deptOption === 'Others') { continue; } ?>
															<option value="<?php echo htmlspecialchars($deptOption, ENT_QUOTES, 'UTF-8'); ?>" <?php echo in_array($deptOption, $selectedDepartments, true) ? 'selected' : ''; ?>>
																<?php echo htmlspecialchars($deptOption, ENT_QUOTES, 'UTF-8'); ?>
															</option>
														<?php endforeach; ?>
												</select>
													<?php endif; ?>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label class="control-label">Project Managers</label>
												<select class="form-control" id="project_manager" name="project_manager[]" multiple="multiple">
													<?php if (!empty($managers)) { foreach ($managers as $mgr) { ?>
														<option value="<?php echo (int)$mgr->empId; ?>" <?php echo in_array((string)$mgr->empId, array_map('strval', $selectedManagers), true) ? 'selected' : ''; ?>>
															<?php echo htmlspecialchars($mgr->name, ENT_QUOTES, 'UTF-8'); ?>
														</option>
													<?php }} ?>
												</select>
											</div>
										</div>
										<div class="col-md-2">
    <div class="form-group">
        <label class="control-label">Search Employee</label>
       <input type="text" class="form-control"
       id="employee_search"
       name="employee_search"
       value="<?php echo !empty($selected_employee_search) ? htmlspecialchars($selected_employee_search, ENT_QUOTES, 'UTF-8') : ''; ?>"
       placeholder="Search Name / ID / Email">

        <div id="employeeSuggestions" class="list-group"
             style="position:absolute; z-index:9999; width:100%; display:none; border:1px solid #ddd; background:#fff;">
        </div>
    </div>
</div>

<?php
$selected_from_year  = $this->input->post('from_year');
$selected_from_month = $this->input->post('from_month');

$selected_to_year    = $this->input->post('to_year');
$selected_to_month   = $this->input->post('to_month');
?>

										<div class="col-md-2">
										<div class="date-filter-wrapper">

    <!-- FROM -->
    <div class="date-box">
        <label class="date-label">From</label>

        <div class="date-select-group">

            <div class="select-wrapper">
                <select class="form-control custom-select" id="from_year" name="from_year">
                <option value="">Select Year</option>
                <option value="2026" <?php echo ($selected_from_year == '2026') ? 'selected' : ''; ?>>2026</option>
                <option value="2025" <?php echo ($selected_from_year == '2025') ? 'selected' : ''; ?>>2025</option>
                <option value="2024" <?php echo ($selected_from_year == '2024') ? 'selected' : ''; ?>>2024</option>
                <option value="2023" <?php echo ($selected_from_year == '2023') ? 'selected' : ''; ?>>2023</option>
                <option value="2022" <?php echo ($selected_from_year == '2022') ? 'selected' : ''; ?>>2022</option>
                <option value="2021" <?php echo ($selected_from_year == '2021') ? 'selected' : ''; ?>>2021</option>
                <option value="2020" <?php echo ($selected_from_year == '2020') ? 'selected' : ''; ?>>2020</option>
                <option value="2019" <?php echo ($selected_from_year == '2019') ? 'selected' : ''; ?>>2019</option>
                <option value="2018" <?php echo ($selected_from_year == '2018') ? 'selected' : ''; ?>>2018</option>
                <option value="2017" <?php echo ($selected_from_year == '2017') ? 'selected' : ''; ?>>2017</option>
                <option value="2016" <?php echo ($selected_from_year == '2016') ? 'selected' : ''; ?>>2016</option>
                </select>

                <span class="clear-select" data-target="from_year">
                    <i class="fa fa-times"></i>
                </span>
            </div>

            <div class="select-wrapper">
                <select class="form-control custom-select" id="from_month" name="from_month">
                    <option value="">Select Mon</option>
          			<option value="01" <?php echo ($selected_from_month == '01') ? 'selected' : ''; ?>>January</option>
                    <option value="02"<?php echo ($selected_from_month == '02') ? 'selected' : ''; ?>>February</option>
                    <option value="03" <?php echo ($selected_from_month == '03') ? 'selected' : ''; ?>>March</option>
                    <option value="04" <?php echo ($selected_from_month == '04') ? 'selected' : ''; ?>>April</option>
                    <option value="05" <?php echo ($selected_from_month == '05') ? 'selected' : ''; ?>>May</option>
                    <option value="06" <?php echo ($selected_from_month == '06') ? 'selected' : ''; ?>>June</option>
                    <option value="07" <?php echo ($selected_from_month == '07') ? 'selected' : ''; ?>>July</option>
                    <option value="08" <?php echo ($selected_from_month == '08') ? 'selected' : ''; ?>>August</option>
                    <option value="09" <?php echo ($selected_from_month == '09') ? 'selected' : ''; ?>>September</option>
                    <option value="10" <?php echo ($selected_from_month == '10') ? 'selected' : ''; ?>>October</option>
                    <option value="11" <?php echo ($selected_from_month == '11') ? 'selected' : ''; ?>>November</option>
                    <option value="12" <?php echo ($selected_from_month == '12') ? 'selected' : ''; ?>>December</option>
                </select>

                <span class="clear-select" data-target="from_month">
                    <i class="fa fa-times"></i>
                </span>
            </div>

        </div>
    </div>

    <!-- TO -->
    <div class="date-box">
        <label class="date-label">To</label>

        <div class="date-select-group">

            <div class="select-wrapper">
               <select class="form-control custom-select" id="to_year" name="to_year">
    <option value="">Select Year</option>

    <option value="2026" <?php echo ($selected_to_year == '2026') ? 'selected' : ''; ?>>2026</option>
    <option value="2025" <?php echo ($selected_to_year == '2025') ? 'selected' : ''; ?>>2025</option>
    <option value="2024" <?php echo ($selected_to_year == '2024') ? 'selected' : ''; ?>>2024</option>
    <option value="2023" <?php echo ($selected_to_year == '2023') ? 'selected' : ''; ?>>2023</option>
    <option value="2022" <?php echo ($selected_to_year == '2022') ? 'selected' : ''; ?>>2022</option>
    <option value="2021" <?php echo ($selected_to_year == '2021') ? 'selected' : ''; ?>>2021</option>
    <option value="2020" <?php echo ($selected_to_year == '2020') ? 'selected' : ''; ?>>2020</option>
    <option value="2019" <?php echo ($selected_to_year == '2019') ? 'selected' : ''; ?>>2019</option>
    <option value="2018" <?php echo ($selected_to_year == '2018') ? 'selected' : ''; ?>>2018</option>
    <option value="2017" <?php echo ($selected_to_year == '2017') ? 'selected' : ''; ?>>2017</option>
    <option value="2016" <?php echo ($selected_to_year == '2016') ? 'selected' : ''; ?>>2016</option>
</select>
                <span class="clear-select" data-target="to_year">
                    <i class="fa fa-times"></i>
                </span>
            </div>

            <div class="select-wrapper">
               <select class="form-control custom-select" id="to_month" name="to_month">

    <option value="">Select Mon</option>

    <option value="01" <?php echo ($selected_to_month == '01') ? 'selected' : ''; ?>>January</option>
    <option value="02" <?php echo ($selected_to_month == '02') ? 'selected' : ''; ?>>February</option>
    <option value="03" <?php echo ($selected_to_month == '03') ? 'selected' : ''; ?>>March</option>
    <option value="04" <?php echo ($selected_to_month == '04') ? 'selected' : ''; ?>>April</option>
    <option value="05" <?php echo ($selected_to_month == '05') ? 'selected' : ''; ?>>May</option>
    <option value="06" <?php echo ($selected_to_month == '06') ? 'selected' : ''; ?>>June</option>
    <option value="07" <?php echo ($selected_to_month == '07') ? 'selected' : ''; ?>>July</option>
    <option value="08" <?php echo ($selected_to_month == '08') ? 'selected' : ''; ?>>August</option>
    <option value="09" <?php echo ($selected_to_month == '09') ? 'selected' : ''; ?>>September</option>
    <option value="10" <?php echo ($selected_to_month == '10') ? 'selected' : ''; ?>>October</option>
    <option value="11" <?php echo ($selected_to_month == '11') ? 'selected' : ''; ?>>November</option>
    <option value="12" <?php echo ($selected_to_month == '12') ? 'selected' : ''; ?>>December</option>

</select>

                <span class="clear-select" data-target="to_month">
                    <i class="fa fa-times"></i>
                </span>
            </div>

        </div>
    </div>

</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-9">
											<div class="form-group" style="margin-top:27px;">
												<button class="btn search-btn" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i> Search</button>
												<a class="btn btn-info icon-btn" href="<?php echo base_url('employee/employee_list_information'); ?>"><i class="fa fa-refresh"></i></a>
												<button class="btn btn-success icon-btn status-btn <?php echo (($selected_status_filter === 'active') || empty($selected_status_filter)) ? 'active' : ''; ?>" data-status="active" type="button">Active</button>
												<button class="btn btn-danger icon-btn status-btn <?php echo ($selected_status_filter === 'inactive') ? 'active' : ''; ?>" data-status="inactive" type="button">Inactive</button>
												<button class="btn btn-primary icon-btn status-btn <?php echo ($selected_status_filter === 'all') ? 'active' : ''; ?>" data-status="all" type="button">All</button>
											</div>
										</div>
									</div>
								</div>
							</form>
							<!-- Employee Report adding block -->
						</div>
					</div>
				</div>
				<!--Search for employee with date wise and client , project wise as well.  -->
			</div>
		</div>
	</div>
    
	<div id="employeeTableContainer">

  <?php if(isset($getEmployees)){ ?>  
  <div class="row">
    <div class="col-md-12">
      <div class="table-responsive employee-summary-wrap">
        <table class="table table-bordered employee-summary-table">
          <thead>
            <tr>
              <th>Department</th>
              <th>All Departments</th>
              <?php foreach($departmentSummaryOrder as $deptName): ?>
                <th><?php echo htmlspecialchars($deptName, ENT_QUOTES, 'UTF-8'); ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th>Employee Count</th>
              <td><?php echo (int)$departmentTotalCount; ?></td>
              <?php foreach($departmentSummaryOrder as $deptName): ?>
                <td><?php echo isset($departmentSummary[$deptName]) ? (int)$departmentSummary[$deptName] : 0; ?></td>
              <?php endforeach; ?>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="employee_report_excel_download">
              <thead>
                <tr>
                 <th>Sno</th>
<th>Name</th>
<th>Emp.Id</th>
<th>Joining Date</th>
<th>Username</th>
<th>Email</th>
<th>Designation</th>
<th>Department</th>
<th>Reporting Manager</th>
<th>User Type</th>
<th>Status</th>
<th>Entry Date</th>
<th>Action</th>
                  
                </tr>
              </thead>
              <tbody>
                <?php 
				  $i=1;
				  if (isset($getEmployees) && !empty($getEmployees)) {
					  foreach ($getEmployees as $key => $empResult) :
					  	 if($i%2 == 0): $showRowColour = 'class="success"'; else: $showRowColour = 'class="info"'; endif;				  
					  	 $createdExp 		= explode(" " , $empResult->created_at);
						
						 if($empResult->user_type == 'manager'):
	
								$statusClass = 'class="label label-danger"';
							
						elseif($empResult->user_type == 'developer'):
	
								$statusClass = 'class="label label-info"';	

					    else:				 
								$statusClass = 'class="label label-primary"';
						 
						 endif;
						 
					  ?>
                  <tr>
                    <td><?php echo $i ?></td>
                    <td><?php echo ucwords($empResult->name);?></td>
                    <td><?php echo ucwords($empResult->emp_com_id);?></td>

<td>
<?php
if(!empty($empResult->emp_joining_date) && $empResult->emp_joining_date != '0000-00-00'){
    echo date('d-M-Y', strtotime($empResult->emp_joining_date));
}else{
    echo '--';
}
?>
</td>

<td><?php echo ucfirst($empResult->username);?></td>
<td><?php echo $empResult->email;?></td>
                      <td><?php echo $empResult->designation;?></td>
                    <td><?php echo $empResult->department;?></td>
                    <td><?php echo !empty($empResult->reporting_manager_name) ? ucwords($empResult->reporting_manager_name) : '--';?></td>
				     <td><?php echo ucfirst($empResult->user_type);?></td>
					 <td>
    <?php echo $empResult->status;?>
</td>

<td><?php echo date('d-M-Y',strtotime($createdExp[0]));?></td>

<td nowrap="nowrap">
                      <?php if($empResult->username != 'admin'): ?>
                        <a href="<?php echo base_url(); ?>employee/add/<?php echo $empResult->empId; ?>" data-toggle="tooltip" title="Edit Employee"><i class="fa fa-edit"></i></a> |
                        <span id="changeStatusRow_<?php echo $empResult->empId; ?>">
                          <a class="<?php echo ($empResult->status=='Active')? 'fa fa-check-circle label label-success' : 'fa fa-ban label label-danger'?>" style="cursor:pointer;" onClick="update_emp_status(<?php echo $empResult->empId;?>,'<?php echo $empResult->status; ?>')"> <?php echo $empResult->status;?></a>
                        </span> |
                        <a href="<?php echo base_url(); ?>employee/cpass/<?php echo $empResult->empId; ?>" data-toggle="tooltip" title="Change Password"><i class="fa fa-key"></i></a>
                      <?php endif; ?>
                     </td>
				    
                  </tr>
                  <?php $i++; endforeach; 
				  } else {
					  echo '<tr><td colspan="13" class="text-center">No records found.</td></tr>';
				  }
				  ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
	</div>
</div>
<!-- Inlude Footer here -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script type="text/javascript">
function downloadExcel() {
  // Get the table element
  var table = document.getElementById('employee_report_excel_download');
  
  // Convert table to worksheet
  var ws = XLSX.utils.table_to_sheet(table);
  
  // Create workbook and add worksheet
  var wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "Employee Report");

  // Generate Excel file
  var wbout = XLSX.write(wb, {bookType:'xlsx', type:'binary'});

  // Convert to blob and save
  var blob = new Blob([s2ab(wbout)], {type:"application/octet-stream"});
  saveAs(blob, 'employee_report_excel.xlsx');
}

// Convert string to ArrayBuffer
function s2ab(s) {
  var buf = new ArrayBuffer(s.length);
  var view = new Uint8Array(buf);
  for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
  return buf;
}

var status;
function update_emp_status(empId,status){
var updateStatus = (status=='Active')? 'InActive' : 'Active';
var answer = confirm ("Are you sure you want to update status "+updateStatus);
if (answer) {
        $.ajax({
                type: "POST",
                url: "<?php echo base_url('employee/update_emp_status');?>",
                data: "empId="+empId+'&status='+updateStatus,
				beforeSend: function() {
   							$('#changeStatusRow_'+empId).html('<i class="fa fa-spinner"></i>');
 				 },success: function (response) {
				            $("#changeStatusRow_"+empId).html(response);
			     }
            });
      }
}


$(function() {
		$("form[name='employee_report_search']").validate({
			submitHandler: function(form) {
				form.submit();
			}
		});
	});


$(document).ready(function() {
		$("#form_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			yearRange: '2010:+0',
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				$("#to_date").datepicker("option", "minDate", selectedDate);
			}
		});
		$("#to_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			yearRange: '2010:+0',
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				$("#form_date").datepicker("option", "maxDate", selectedDate);
			}
		});
	})
  $('#department').select2({
	  width: '100%',
	  placeholder: 'ALL Departments',
	  allowClear: true,
	  closeOnSelect: false
  });
  $('#project_manager').select2({
	  width: '100%',
	  placeholder: 'Select Managers',
	  allowClear: true,
	  closeOnSelect: false
  });
  $('.status-btn').on('click', function() {
	  $('#status_filter').val($(this).data('status'));
	  $('#employee_report_search').submit();
  });

</script>


<!------------------------search employee dropdown and get data automatically on screen while typing---------------------->


<script>
$(document).ready(function () {

    // Typing search
    $(document).on('keyup', '#employee_search', function () {

        var keyword = $(this).val();

        // Suggestion dropdown
        if (keyword.length > 0) {

            $.ajax({
                url: "<?php echo base_url('employee/searchEmployeeAjax'); ?>",
                type: "POST",
                data: { keyword: keyword },
                success: function (response) {
                    $('#employeeSuggestions').html(response).show();
                }
            });

        } else {
            $('#employeeSuggestions').hide();
        }

        // Auto filter employee table
        $.ajax({
            url: "<?php echo base_url('employee/employee_list_information'); ?>",
            type: "POST",
            data: {
                employee_search: keyword,
                department: $('#department').val(),
                project_manager: $('#project_manager').val(),
                form_date: $('#form_date').val(),
                to_date: $('#to_date').val(),
                status_filter: $('#status_filter').val()
            },
            success: function (response) {

                $('#employeeTableContainer').html(response);

                // keep typed value after reload
                $('#employee_search').val(keyword);
            }
        });

    });

    // Click suggestion
    $(document).on('click', '.employee-item', function () {

        var selectedText = $(this).data('value');

        $('#employee_search').val(selectedText);
        $('#employeeSuggestions').hide();

        $.ajax({
            url: "<?php echo base_url('employee/employee_list_information'); ?>",
            type: "POST",
            data: {
                employee_search: selectedText,
                department: $('#department').val(),
                project_manager: $('#project_manager').val(),
                form_date: $('#form_date').val(),
                to_date: $('#to_date').val(),
                status_filter: $('#status_filter').val()
            },
            success: function (response) {

                $('#employeeTableContainer').html(response);

                // restore selected text
                $('#employee_search').val(selectedText);
            }
        });

    });

    // Hide suggestion dropdown when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('#employee_search, #employeeSuggestions').length) {
            $('#employeeSuggestions').hide();
        }
    });

});
</script>

<!-------------dropdown close code-------------->


<script>
$(document).ready(function () {

    $('#department').select2({
        closeOnSelect: true
    });

    $('#project_manager').select2({
        closeOnSelect: true
    });
});
</script>




<script>
$(document).ready(function () {

    $('#department, #project_manager').on('select2:select', function () {
        $(this).select2('close');
    });

});
</script>

<!------------from/to dates script---------------->

<script>
$(document).ready(function () {

    // ================================
    // Dropdown Change Functionality
    // ================================
    $('.custom-select').on('change', function () {

        var selectValue = $(this).val();
        var clearBtn = $(this).siblings('.clear-select');

        // Show/Hide clear icon
        if (selectValue !== '') {

            clearBtn.show();

            // Add background color
            $(this).addClass('selected-option');

        } else {

            clearBtn.hide();

            // Remove background color
            $(this).removeClass('selected-option');
        }

        // ================================
        // Auto Filter Data
        // ================================
        $.ajax({

            url: "<?php echo base_url('employee/employee_list_information'); ?>",
            type: "POST",

            data: {

                employee_search: $('#employee_search').val(),

                department: $('#department').val(),

                project_manager: $('#project_manager').val(),

                from_year: $('#from_year').val(),
                from_month: $('#from_month').val(),

                to_year: $('#to_year').val(),
                to_month: $('#to_month').val(),

                status_filter: $('#status_filter').val()
            },

            success: function (response) {

                $('#employeeTableContainer').html(response);
            }
        });

    });

    // ================================
    // Clear Dropdown
    // ================================
    $('.clear-select').on('click', function () {

        var target = $(this).data('target');

        $('#' + target)
            .val('')
            .removeClass('selected-option')
            .trigger('change');

        $(this).hide();

        // ================================
        // Reload Data After Clear
        // ================================
        $.ajax({

            url: "<?php echo base_url('employee/employee_list_information'); ?>",
            type: "POST",

            data: {

                employee_search: $('#employee_search').val(),

                department: $('#department').val(),

                project_manager: $('#project_manager').val(),

                from_year: $('#from_year').val(),
                from_month: $('#from_month').val(),

                to_year: $('#to_year').val(),
                to_month: $('#to_month').val(),

                status_filter: $('#status_filter').val()
            },

            success: function (response) {

                $('#employeeTableContainer').html(response);
            }
        });

    });

    // ================================
    // Page Load Check
    // ================================
    $('.custom-select').each(function () {

        if ($(this).val() !== '') {

            $(this).addClass('selected-option');

            $(this).siblings('.clear-select').show();
        }
    });

});
</script>
<!----------end---------------->


<style>



.search-btn {
    background-color: #2579ab !important;
    border-color: #2579ab !important;
    color: #ffffff !important;
    font-weight: 600;
}

.search-btn:hover {
    background-color: #552b80 !important;
    border-color: #552b80 !important;
    color: #ffffff !important;
}

	.form-control-sm{
			font-weight:bold;
			color: #FFF;
			background-color: #663399 !important;
	}
	.select2-container--default .select2-selection--single .select2-selection__rendered{
		    font-weight:bold;
			color: #FFF;
			background-color: #663399 !important;
	}
	.employee-summary-wrap {
    margin-bottom: 8px;
	width: 78%;
    margin: 0 auto 10px auto;
}

.employee-summary-table {
       width: 100%;
    margin: 0 auto;
}

.employee-summary-table thead th {
    background: #2f6689;
    color: #fff;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    font-size: 14px;
    border-color: #8ea5b8 !important;
    padding: 5px 4px;
}

.employee-summary-table tbody th,
.employee-summary-table tbody td {
    text-align: center;
    vertical-align: middle;
    font-size: 16px;
    font-weight: 600;
    padding: 6px 5px;
    border-color: #8ea5b8 !important;
    background: #d8cfbc;
    color: #2d2d2d;
}

.employee-summary-table tbody th {
    background: #cfd5de;
    font-size: 16	px;
    color: #1b2f40;
    line-height: 1;
}
	#employee_report_excel_download {
		border: 1px solid #d9e1ea;
		border-radius: 6px;
		overflow: hidden;
	}
	#employee_report_excel_download thead th {
		background: linear-gradient(to bottom, #f8fafc, #edf2f7);
		color: #25364a;
		font-weight: 700;
		font-size: 16px;
		padding: 10px 8px;
		vertical-align: middle;
		border-color: #d7dee8 !important;
		white-space: nowrap;
	}
	#employee_report_excel_download tbody td {
		padding: 8px 8px;
		font-size: 15px;
		color: #2e3e50;
		vertical-align: top;
		border-color: #e2e8f0 !important;
		background: #fff;
	}
	#employee_report_excel_download tbody tr:nth-child(even) td {
		background: #fbfdff;
	}
	#employee_report_excel_download tbody tr:hover td {
		background: #f1f7ff !important;
	}
	#employee_report_excel_download td:nth-child(1),
	#employee_report_excel_download td:nth-child(3),
	#employee_report_excel_download td:nth-child(9),
	#employee_report_excel_download td:nth-child(10),
	#employee_report_excel_download td:nth-child(11) {
		text-align: center;
		white-space: nowrap;
	}
	#employee_report_excel_download td:nth-child(12) {
		white-space: nowrap;
		text-align: center;
	}
	#employee_report_excel_download .label {
		font-size: 13px;
		padding: 3px 8px;
		border-radius: 12px;
	}
	#employee_report_excel_download td a .fa {
		font-size: 15px;
	}
	@media (max-width: 992px) {
		.employee-summary-table thead th {
			font-size: 13px;
		}
		.employee-summary-table tbody th,
		.employee-summary-table tbody td {
			font-size: 16px;
		}
		.employee-summary-table tbody th {
			font-size: 22px;
		}
		#employee_report_excel_download thead th,
		#employee_report_excel_download tbody td {
			font-size: 14px;
			padding: 7px 6px;
		}
	}

	.date-filter-wrapper {
    display: flex;
    gap: 30px;
    margin-top: 5px;
	
}

.date-box {
    border: 1px solid #d7d7d7;
    border-radius: 10px;
    padding: 8px;
    min-width: 240px;
}

.date-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #0c0c0c;
    margin-bottom: 10px;
}

.date-select-group {
    display: flex;
    gap: 12px;
}

.select-wrapper {
    position: relative;
    width: 100%;
}

.custom-select {
    height: 30px;
    border-radius: 10px;
    border: 1px solid #cfcfcf;
    font-size: 11px;
    padding-right: 35px;
	padding: 2px !important;
    background: #fff;
    box-shadow: none;
}

.custom-select:focus {
    border-color: #7b4ba3;
    box-shadow: 0 0 4px rgba(123, 75, 163, 0.3);
}

.clear-select {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #fffdfd;
    cursor: pointer;
    display: none;
    font-size: 12px;
}

.clear-select:hover {
    color: #d9534f;
}
.custom-select.selected-option {
    background-color: #663399 !important;
    color: #ffffff !important;
    font-weight: 600;
    border-color: #663399 !important;
}

.custom-select.selected-option option {
    background-color: #ffffff;
    color: #000000;
}
</style>	

<?php $this->load->view('includes/cRMFooter'); ?>