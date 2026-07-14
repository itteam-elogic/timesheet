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
    <div><a class="btn btn-primary btn-flat" href="<?php echo base_url('employee/add'); ?>" data-toggle="tooltip" title="Add Employee"><i class="fa fa-lg fa-plus"></i></a><a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url('employee'); ?>"><i class="fa fa-lg fa-refresh"></i></a>
      <?php echo (!empty($this->input->post('department'))) ? '<a class="btn btn-warning btn-flat" data-toggle="tooltip" title="Download Client Master Report" href="#" onclick="downloadExcel()"><i class="fa fa-file-excel-o"></i> Download Client Report</a>' : ''; ?> 
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
								<div class="tab-pane">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Department</label>
												<?php if($createdUser == '149'):?>
													<select class="form-control <?php echo (!empty($this->input->post('department'))) ? 'form-control-sm' : ''; ?>" id="department" name="department" >
													    
														<option value="MEP" <?php echo ($this->input->post('department') == 'MEP') ? 'selected' : ''; ?>>MEP</option>
														
												</select>

												<?php elseif($createdUser == '47'): ?>
													
													<select class="form-control <?php echo (!empty($this->input->post('department'))) ? 'form-control-sm' : ''; ?>" id="department" name="department" >
													    													
														<option value="Architectural" <?php echo ($this->input->post('department') == 'Architectural') ? 'selected' : ''; ?>>Architectural - Structural - 3D Visualization </option>
												</select>
												
												<?php else: ?>	
												<select class="form-control <?php echo (!empty($this->input->post('department'))) ? 'form-control-sm' : ''; ?>" id="department" name="department" >
													    <option value="all">ALL Departments</option>
														<option value="MEP" <?php echo ($this->input->post('department') == 'MEP') ? 'selected' : ''; ?>>MEP</option>
														<option value="Architectural" <?php echo ($this->input->post('department') == 'Architectural') ? 'selected' : ''; ?>>Architectural</option>
														<option value="Structural" <?php echo ($this->input->post('department') == 'Structural') ? 'selected' : ''; ?>>Structural</option>
														<option value="3D Visualization" <?php echo ($this->input->post('department') == '3D Visualization') ? 'selected' : ''; ?>>3D Visualization</option>
                                                        
												</select> 
													<?php endif; ?>
											</div>
										</div>
										
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">From Date</label>
												<input class="form-control <?php echo (!empty($this->input->post('form_date'))) ? 'form-control-sm' : ''; ?>" type="text" id="form_date" name="form_date" placeholder="Select From Date" value="<?php echo $this->input->post('form_date'); ?>" readonly="" style="background-color: <?php echo (!empty($this->input->post('form_date'))) ? '#663399' : ''; ?>">
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">To Date</label>
												<input class="form-control  <?php echo (!empty($this->input->post('to_date'))) ? 'form-control-sm' : ''; ?>" type="text" id="to_date" name="to_date" placeholder="Select To Date" value="<?php echo $this->input->post('to_date'); ?>" readonly="">
											</div>
										</div>
										<div class="col-md-3">
											<button class="btn btn-primary icon-btn" style="margin-top:27px;"><i class="fa fa-fw fa-lg fa-check-circle"></i>Search</button>
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
    
    
  <?php if(!empty($this->input->post('department'))){ ?>   
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
                  <th>Username</th>
                  <th>Email</th>
                  <th>Designation</th>
                  <th>Department</th>
				  <th>R.Manager</th>
				  <th>User Type</th>
                  <th>Status</th>    
                  <th>Date</th>
                  
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
                    <td><?php echo ucfirst($empResult->username);?> </td>
                    <td><?php echo $empResult->email;?></td>
                      <td><?php echo $empResult->designation;?></td>
                    <td><?php echo $empResult->department;?></td>
					 <td><?php echo $empResult->reporting_manger;?></td>
				     <td><?php echo ucfirst($empResult->user_type);?></td>
                     <th><?php echo date('d-M-Y',strtotime($createdExp[0]));?></th>
				    <th>
				      <?php echo $empResult->status;?>
				     </th>
                  </tr>
                  <?php $i++; endforeach; 
				  } else {
					  echo '<tr><td colspan="9" class="text-center">No records found.</td></tr>';
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


$(function() {
		$("form[name='client_report_search']").validate({
			rules: {
				form_date: {
					required: true
				},
				to_date: {
					required: true
				}
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


$(document).ready(function() {
		$("#form_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				$("#to_date").datepicker("option", "minDate", selectedDate);
			}
		});
		$("#to_date").datepicker({
			dateFormat: 'yy-mm-dd', 
			changeMonth: true,
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				$("#form_date").datepicker("option", "maxDate", selectedDate);
			}
		});
	})
  $('#department').select2(); // Autosuggest list on clients

</script>
<style>
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
</style>	

<?php $this->load->view('includes/cRMFooter'); ?>
