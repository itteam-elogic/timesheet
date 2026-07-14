<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
$createdUser = $this->session->userdata['logged_in_timesheet']['empId'];
 $getListOfEmployees   	= $this->timesheet_login->getEmployeeName(); // List of Employees
 $getClientNames      		= $this->client_model->getManagerwiseClients(); // List of Clients

 if(!empty($_REQUEST['form_date'])):
		$fromDate = $_REQUEST['form_date'];
		$toDate = $_REQUEST['to_date'];
		$departM = isset($_REQUEST['department']) ? $_REQUEST['department'] : '';
		$clientN = isset($_REQUEST['client_Id']) ? $_REQUEST['client_Id'] : '';
		$pManagerN = isset($_REQUEST['project_manager']) ? $_REQUEST['project_manager'] : '';
		$analyzerrN = isset($_REQUEST['analyzer']) ? $_REQUEST['analyzer'] : '';
		$self_checkerN = isset($_REQUEST['self_checker']) ? $_REQUEST['self_checker'] : '';
		$selectBGC = 'style="background:rebeccapurple; color:white; font-weight: bold"';
	else:
		$fromDate = '';
		$toDate = '';
		$departM = '';
		$clientN = '';
		$pManagerN = '';
		$analyzerrN = '';
		$self_checkerN = '';
		$selectBGC = '';
	endif; 

?>
<!-- Inlude Header here END 3ced9f5d191c6ab6b400cb93b9f52146 -->

<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1>Quality Error Report</h1>
    </div>
   <div>
		<a class="btn btn-primary btn-flat"  data-toggle="tooltip" title="Add" onclick="downloadExcel()"><i class="fa fa-lg fa-plus"></i> Download Quality Error Log Report</a> | <a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url();?>quality_error_log"><i class="fa fa-lg fa-refresh"></i></a>
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
							<form class="" name="quality_report_search" id="quality_report_search" method="post" action="<?php echo base_url('quality_error_log/quality_report_automation_search');?>">
								<div class="tab-pane">
									<div class="row">
										<div class="col-md-2">
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
														<option value="MEP" <?=$departM == 'MEP' ? 'selected="selected"' : '';?>>MEP</option>
														<option value="Architectural" <?=$departM == 'Architectural' ? 'selected="selected"' : '';?>>Architectural</option>
														<option value="Structural" <?=$departM == 'Structural' ? 'selected="selected"' : '';?>>Structural</option>
														<option value="3D Visualization" <?=$departM == '3D Visualizat' ? 'selected="selected"' : '';?>>3D Visualization</option>
												</select> 
													<?php endif; ?>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label class="control-label">Project Manager</label>
													<select class="form-control" id="project_manager" name="project_manager">
													    <option value="all">ALL</option>
														<?php foreach($this->timesheet_login->getReportingManagers() as $managerResult): ?>
												<option value="<?php echo $managerResult->empId;?>" <?=$pManagerN == $managerResult->empId ? 'selected="selected"' : '';?>><?php echo $managerResult->name; ?></option>
												<?php endforeach; ?>														
												</select>	
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label class="control-label">Analyzer</label>
												<select class="form-control" id="analyzer" name="analyzer">
													    <option value="all">ALL</option>
														<?php foreach($getListOfEmployees as $key => $employeeName): ?>
														<option value="<?php echo $employeeName->empId;?>" <?=$analyzerrN == $employeeName->empId ? 'selected="selected"' : '';?>><?php echo ucfirst($employeeName->name);?></option>
														<?php endforeach; ?>														
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Client</label>
												<select class="form-control" id="client" name="client">
													    <option value="all">ALL</option>
														<?php foreach($getClientNames as $key => $clientName): ?>
													<option value="<?php echo $clientName->client_Id;?>" <?=$clientN == $clientName->client_Id ? 'selected="selected"' : '';?>><?php echo ucfirst($clientName->client_name);?></option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Self Checker</label>
												<select class="form-control" id="self_checker" name="self_checker">
													    <option value="all">ALL</option>
														<?php foreach($getListOfEmployees as $key => $employeeName): ?>
														<option value="<?php echo $employeeName->empId;?>" <?=$self_checkerN == $employeeName->empId ? 'selected="selected"' : '';?>><?php echo ucfirst($employeeName->name);?></option>
														<?php endforeach; ?>														
												</select>
											</div>
										</div>
										
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">From Date</label>
												<input class="form-control" type="text" id="form_date" name="form_date" placeholder="Select From Date" value="<?=$fromDate;?>" readonly="" <?=$selectBGC?>>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">To Date</label>
												<input class="form-control" type="text" id="to_date" name="to_date" placeholder="Select To Date" value="<?=$toDate;?>" readonly="" <?=$selectBGC?>>
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
						<table class="table table-hover table-bordered" id="download_Quality_error_report_data">
							<thead>
								<tr>
									<th nowrap >Month</th>
									<th nowrap >Date</th>
									<th nowrap >C.Name</th>
									<th nowrap >P.Name</th>
                                    <th>P.Manager</th>
                                    <th>Department</th>
									<th nowrap >Self Checker</th>
									<th nowrap >Analyser</th>									
									<th nowrap >Analyser Errors</th>
									<th nowrap >Reviewer</th>
									<th nowrap >Reviewer Errors</th>
                                    <th nowrap >Ensure</th>
                                    <th nowrap >Total Errors</th>
									<th nowrap >Analyser Link</th>
									<th nowrap >Reviewer Link</th>
									</tr>
							</thead>
							<tbody>
								<?php 
				  $i=0;
				  foreach ($searchReportData as $key => $qtyErrorResult) :
								
					$getListOfEmployees = $this->quality_error_log_model->getEmployeeName($qtyErrorResult->self_checker_name); // List of Clients
					
					$monthName = date("F",strtotime($qtyErrorResult->analyzer_report_date));
								
				 	 if($i%2 == 0): $showRowColour = 'class="success"'; else: $showRowColour = 'class="info"'; endif;				  
				  	 $createdExp 		= explode(" " , $qtyErrorResult->created_at);	
				 ?>
								<tr <?php echo $showRowColour; ?> id="delClientRow<?php echo $qtyErrorResult->qty_error_id; ?>" style="font-size:14px;">
									<td><?php echo $monthName;?></td>
									<td><span class="me-1 badge bg-info"><?php echo date("d-M-Y",strtotime($qtyErrorResult->analyzer_report_date));?></span></td>
									<td><?php echo ucwords($qtyErrorResult->client_name);?></td>
									<td><?php echo ucwords($qtyErrorResult->project_name);?></td>
                                    <td><?php echo ucwords($qtyErrorResult->project_created_name);?></td>
                                    <td><?php echo ucwords($qtyErrorResult->project_type);?></td>
									<td><span class="text-primary"><?php echo $getListOfEmployees;?></span></td>
									<td><span class="label label-info"><?php echo $this->quality_error_log_model->getEmployeeName($qtyErrorResult->analyzer_name);?></span></td>
									<td><?php echo $qtyErrorResult->analyzer_num_of_errors;?></td>
									<td><span class="label label-success"><?php echo $this->quality_error_log_model->getEmployeeName($qtyErrorResult->reviewer_name);?></span></td>
									<td><?php echo $qtyErrorResult->reviewer_num_of_errors;?></td>
                                    <td> <?php echo $qtyErrorResult->status;?></td>
                                    <td> <?php echo $qtyErrorResult->analyzer_num_of_errors + $qtyErrorResult->reviewer_num_of_errors;?></td>
									<td><?php echo $qtyErrorResult->analyzer_link;?></td>									
									<td><?php echo $qtyErrorResult->reviewer_link;?></td>
									
									</tr>
								<?php $i++; endforeach; ?>
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
alert('Hi');
  // Get the table element
  var table = document.getElementById('download_Quality_error_report_data');
  
  // Convert table to worksheet
  var ws = XLSX.utils.table_to_sheet(table);
  
  // Create workbook and add worksheet
  var wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "Quality Error Report Data");

  // Generate Excel file
  var wbout = XLSX.write(wb, {bookType:'xlsx', type:'binary'});

  // Convert to blob and save
  var blob = new Blob([s2ab(wbout)], {type:"application/octet-stream"});
  saveAs(blob, 'quality_error_report_excel.xlsx');
}

// Convert string to ArrayBuffer
function s2ab(s) {
  var buf = new ArrayBuffer(s.length);
  var view = new Uint8Array(buf);
  for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
  return buf;
}


$(function() {
		$("form[name='quality_report_search']").validate({
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
	 var today = $("#form_date").val();
            var currentYear = new Date().getFullYear();
            var minDate = new Date(2015, 0, 1); // January 1, 2015
            var maxDate = new Date(); // Current date

		$("#form_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			 changeYear: true,
                yearRange: '2015:' + currentYear,
                minDate: minDate,
                maxDate: maxDate,
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				$("#to_date").datepicker("option", "minDate", selectedDate);
			}
		});
		$("#to_date").datepicker({
			dateFormat: 'yy-mm-dd', 
			changeMonth: true,
			 changeYear: true,
                yearRange: '2015:' + currentYear,
                minDate: minDate,
                maxDate: maxDate,
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				$("#form_date").datepicker("option", "maxDate", selectedDate);
			}
		});
	})
  $('#department,#project_manager,#analyzer,#client,#self_checker').select2(); // Autosuggest list on clients

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
