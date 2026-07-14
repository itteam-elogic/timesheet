		<!-- Include Header here -->
		<?php 
		$this->load->view('includes/cRMHeader'); 

		$getClientNames = $this->client_model->getClientName(); // List of Clients

		$taskClientId = ''; // Getting client ID

		$getListOfProjects = $this->project_model->getProjectName($taskClientId); // List of Clients

		$taskProjectId = '';

		$getListOfTask = $this->task_model->getTaskName($taskProjectId); // List of Clients

		if (!empty($_REQUEST['client_Id']) && !empty($_REQUEST['project_Id'])):
			$getClientID = $_REQUEST['client_Id'];
			$getProjectID = implode(',', $_REQUEST['project_Id']);
			$getFromDate = $_REQUEST['form_date'];
			$getToDate = $_REQUEST['to_date'];

			//7a01a4e0803f00f531feff40a4fd72cf

			$getcTRName = $this->client_model->getClients($getClientID); // get client name based on ClientID

			$getpTRName = $this->client_model->getProjectNWithIds($getProjectID); // get project name based on ProjectID

			$dcpString = "";

			foreach ($getpTRName as $getCSProjectName):
				$dcpString .= $getCSProjectName->project_name . " ,";
			endforeach; 

			$proglang = explode(",", $getProjectID); 
		else:
			$getClientID = '';
			$getProjectID = '';
			$getFromDate = '';
			$getToDate = '';    
		endif;
		?>
		<div class="content-wrapper">
			<div class="page-title">
				<div>
					<h1><i class="fa fa-bell"></i> Client Timesheet Report </h1>
				</div>
				 <div>
					<a class="btn btn-primary btn-flat" href="<?php echo base_url();?>clients/cs_reports" data-toggle="tooltip" title="Client TS Report"><i class="fa fa-search"> Client TS Report</i></a>
					<a class="btn btn-primary btn-flat" href="<?php echo base_url();?>clients/cs_reports" data-toggle="tooltip" title="refresh">
						<i class="fa fa-chevron-circle-left"></i>
					</a>
				</div>
			</div>
			<div class="card">
				<h3 class="card-title"></h3>
				<div class="card-body">
					<div class="row">
						<!-- Search for employee with date wise and client, project wise as well. -->
						<div class="col-md-12">
							<div class="bs-component">
								<div class="tab-content" id="myTabContent">
									<!-- Employee Report adding block -->
									<form name="emp_search_log" id="emp_search_log" method="post" action="<?php echo base_url('clients/client_ts_report');?>">
										<div class="tab-pane fade active in" id="Add">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label class="control-label">Client's</label>
														<select class="form-control" id="client_Id" name="client_Id" onChange="getProjects(this.value);">
															<option value="">Please select client</option>
															<?php foreach ($getClientNames as $key => $clientName): ?>
															<option value="<?php echo $clientName->client_Id;?>" <?php if ($clientName->client_Id == $getClientID) { echo ' selected="selected"'; } ?>>
																<?php echo ucfirst($clientName->client_name);?>
															</option>
															<?php endforeach; ?>
														</select>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label class="control-label">Project's</label>
														<select class="form-control" id="project_Id" name="project_Id[]" multiple>
															<option value="">Please select project</option>
															<option value="all">All</option>
															<?php foreach ($getListOfProjects as $key => $projectName): 
																$str_flag = isset($proglang) && in_array($projectName->project_Id, $proglang) ? "selected='selected'" : ""; ?>
															<option value="<?php echo $projectName->project_Id;?>" <?php echo $str_flag; ?>>
																<?php echo ucfirst($projectName->project_name);?>
															</option>                                                          
															<?php endforeach; ?>
														</select>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label class="control-label">From Date</label>
														<input class="form-control <?=!empty($getFromDate) ? 'bg-primary-client' : ''?>" type="text" id="form_date" name="form_date" value="<?=$getFromDate;?>" placeholder="Select From Date" readonly="">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label class="control-label">To Date</label>
														<input class="form-control <?=!empty($getToDate) ? 'bg-primary-client' : ''?>" type="text" id="to_date" name="to_date" value="<?=$getToDate;?>" placeholder="Select To Date" readonly="">
													</div>
												</div>
											</div>
											<div class="card-footer">
												<button class="btn btn-primary icon-btn" id="searchBtn">
													<i class="fa fa-fw fa-lg fa-check-circle"></i>Search
												</button>
												<a href="<?php echo base_url();?>empreports" data-toggle="Go To Report Log!" title="Cancel">
													<button class="btn btn-default icon-btn" type="button">
														<i class="fa fa-chevron-circle-left"></i>Back
													</button>
												</a>
											</div>
										</div>
									</form>
									<!-- Employee Report adding block -->
								</div>
							</div>
						</div>
						<!-- Search for employee with date wise and client, project wise as well. -->
					</div>
				</div>
			</div>
			<?php if (!empty($resultClientReport)): ?>
			<div class="card">
				<div class="card-body">
				<ul class="nav nav-tabs">
						<li class="active"><a data-toggle="tab" href="#clientInfo">Client Specific Report</a></li>
						<li><a data-toggle="tab" href="#consolidatedInfo">Consolidated Client Specific Report</a></li>
					</ul>
					<div class="tab-content">
						<div id="clientInfo" class="tab-pane fade in active">
							<div class="row">
								<div style="text-align:right; position:relative;bottom:10px; right:18px;">
									<button id="downloadEmployeeData" class="btn btn-primary btn-flat">Export Client Specific Report</button>
								</div>
								<!-- Displaying Search Result -->
								<div class="col-md-12">
									<div class="table-responsive">
										<table class="table table-hover table-bordered" id="table22excel">
											<thead>
												<tr style="display:none;">
													<th colspan="10" bgcolor="#C0E6F5">
														<b><center>Client Timesheet Report for <?php echo $getFromDate . ' - ' . $getToDate; ?></center></b>
													</th>
												</tr>
												<tr>
													<th colspan="3">
														<center style="text-align: right;"><b>Client Name&nbsp;: </b></center>
													</th>
													<th colspan="4">
														<b>&nbsp;&nbsp;<?php echo $getcTRName[0]->client_name; ?></b>
													</th>
												</tr>
												<tr>
													<th colspan="3">
														<center style="text-align: right;"><b>Project Name&nbsp;: </b></center>
													</th>
													<?php if (!empty($dcpString)): ?>
													<th colspan="4">
														<b>&nbsp;&nbsp;<?php echo rtrim($dcpString, ", "); ?></b>
													</th>
													<?php else: ?>
													<th colspan="4">
														<b>ALL</b>
													</th>
													<?php endif; ?>
												</tr>
												<tr>
													<th colspan="3">
														<center style="text-align: right;"><b>Address&nbsp;: </b></center>
													</th>
													<th colspan="4">
														<b>&nbsp;&nbsp;<?php echo $getcTRName[0]->client_desc; ?></b>
													</th>
												</tr>
												<tr>
													<th colspan="3">
														<center style="text-align: right;"><b>Contact Number&nbsp;: </b></center>
													</th>
													<th colspan="4">
														<b>&nbsp;&nbsp;<?php echo $getcTRName[0]->client_contact_num; ?></b>
													</th>
												</tr>
												<tr>
													<th colspan="3">
														<center style="text-align: right;"><b>eMail&nbsp;: </b></center>
													</th>
													<th colspan="4">
														<b>&nbsp;&nbsp;<?php echo $getcTRName[0]->client_email; ?></b>
													</th>
												</tr>
												<tr>
													<th colspan="3">
														<center style="text-align: right;"><b>Date&nbsp;: </b></center>
													</th>
													<th colspan="4">
														<b>&nbsp;&nbsp;<?php echo $getFromDate . ' - ' . $getToDate; ?></b>
													</th>
												</tr>
											</thead>
											<div style="clear:both"></div>                        
											<thead>
												<tr style="font-weight: bold;">
												    <th>Date</th>
													<th nowrap>Employee Name</th>
													<th nowrap>Project Name</th>
													<th class="text-center" nowrap>Total Hours</th>
													<th>Task</th>
												</tr>
											</thead>
											<tbody>
												<?php 
												$projectWiseData = array();
												$projectTotalHours = array();
												foreach ($resultClientReport as $key => $reportResult):
													if (!isset($projectWiseData[$reportResult->project_name])) {
														$projectWiseData[$reportResult->project_name] = array();
														$projectTotalHours[$reportResult->project_name] = 0;
													}
													$projectWiseData[$reportResult->project_name][] = $reportResult;
													$projectTotalHours[$reportResult->project_name] += $reportResult->totalHours;
												endforeach; ?>
												<?php foreach ($projectWiseData as $projectName => $details): ?>
												<?php foreach ($details as $detail): ?>
												<tr>
												<td><span class="label label-info"><?php echo $detail->emp_report_dates; ?></span></td>	
												<td style="color: #4c0bce;"><?php echo $detail->name; ?></td>
												<td style="color: #4c0bce;"><?php echo $projectName; ?></td>
													<td class="text-center"><?php echo $detail->totalHours; ?></td>
													<td><?php echo $detail->strengths; ?></td>
												</tr>
												<?php endforeach; ?>
												<tr>
													<td style="text-align:right;" colspan="3"><b>Total Hours for <?php echo $projectName; ?>:</b></td>
													<td class="text-center"><b><?php echo $projectTotalHours[$projectName]; ?></b></td>
													<td></td>
												</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
								</div>
								<!-- Displaying Search Result -->
							</div>
						</div>
						<div id="consolidatedInfo" class="tab-pane fade">
							<div class="card">
								<div class="card-body">
									<div class="row">
										<div style="text-align:right; position:relative;bottom:10px; right:18px;">
											<button id="downloadEmployeeData_consolidated" class="btn btn-primary btn-flat">Export Client Timesheet Report into Excel</button>
										</div>
										<!-- Displaying Search Result -->
										<div class="col-md-12">
											<div class="table-responsive">
												<table class="table table-hover table-bordered" id="table22_consolidated_excel">
													<div style="clear:both"></div>                        
													<thead>
														<tr style="font-weight: bold;">
															<th>From & To Dates</th>
															<th>Project Manager</th>
															<th>Client Name</th>                                                        
															<th>Projects</th>
															<th>Project Number</th>
															<th>No.of Employees</th>
															<th>Employee Names</th>   
															<th>Total Hours worked</th>

														</tr>
													</thead>
													<tbody>
														<?php 
														if ($getProjectID == 'all'):
															$resourceBBQ = $this->db->select('er.empId, er.project_Id, p.project_name,p.project_number, SUM(er.emp_time_hours) as total_hours, COUNT(DISTINCT er.empId) as num_employees, c.client_name, c.client_Id, emp.name as project_manager_name, GROUP_CONCAT(DISTINCT emp2.name,\' \') as employee_names')
																->from('emp_record_details er')
																->where('er.client_Id', $getClientID)
																->where('er.emp_report_dates >= ', $getFromDate)
																->where('er.emp_report_dates <= ', $getToDate)
																->join('project_details as p', 'p.project_Id = er.project_id', 'left')
																->join('client_details as c', 'c.client_Id = er.client_Id', 'left')
																->join('employee_details as emp', 'emp.empId = p.empId', 'left') // Assuming project_manager_id is the field in project_details that links to the project manager's empId
																->join('employee_details as emp2', 'emp2.empId = er.empId', 'left') // Joining employee_details again to get the employee name
																->group_by('er.project_Id')
																->order_by('p.project_name', 'asc')
																->get()->result(); 
														else:
															$resourceBBQ = $this->db->select('er.empId, er.project_Id, p.project_name,p.project_number, SUM(er.emp_time_hours) as total_hours, COUNT(DISTINCT er.empId) as num_employees, c.client_name, c.client_Id, emp.name as project_manager_name, GROUP_CONCAT(DISTINCT emp2.name,\' \') as employee_names')
																->from('emp_record_details er')
																->where('er.client_Id', $getClientID)
																->where_in('er.project_Id', explode(',', $getProjectID))
																->where('er.emp_report_dates >= ', $getFromDate)
																->where('er.emp_report_dates <= ', $getToDate)
																->join('project_details as p', 'p.project_Id = er.project_id', 'left')
																->join('client_details as c', 'c.client_Id = er.client_Id', 'left')
																->join('employee_details as emp', 'emp.empId = p.empId', 'left') // Assuming project_manager_id is the field in project_details that links to the project manager's empId
																->join('employee_details as emp2', 'emp2.empId = er.empId', 'left') // Joining employee_details again to get the employee name
																->group_by('er.project_Id')
																->order_by('p.project_name', 'asc')
																->get()->result(); 
														endif;

														$rowsSpanCnt = count($resourceBBQ); 
														$totalHours = 0; // Initialize total hours

														$firstRow = true;
														foreach ($resourceBBQ as $key => $reportResult):
															$totalHours += $reportResult->total_hours; // Accumulate total hours
														?>
														<tr>
															<?php if ($firstRow): ?>
																<td rowspan="<?php echo $rowsSpanCnt; ?>"><?php echo $getFromDate . ' - ' . $getToDate; ?></td>
															<td rowspan="<?php echo $rowsSpanCnt; ?>" style="text-align:center; color: #4c0bce;"><?php echo $reportResult->project_manager_name; ?></td>
																<td rowspan="<?php echo $rowsSpanCnt; ?>"><?php echo $getcTRName[0]->client_name; ?></td>

																<?php $firstRow = false; ?>
															<?php endif; ?>
															<td><?php echo $reportResult->project_name; ?></td>
															<td><?php echo $reportResult->project_number; ?></td>
															<td><span class="label label-info"><?php echo $reportResult->num_employees; ?></span></td>        <td><?php echo str_replace(',', '<br>', $reportResult->employee_names); ?></td>
															<td><?php echo $reportResult->total_hours; ?></td>

														</tr>
														<?php endforeach; ?>
														<!-- Displaying Total Hours -->
														<tr>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td><strong>Total Hours: <?php echo $totalHours; ?></strong></td>
															<td></td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
										<!-- Displaying Search Result -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<script language="javascript" type="text/javascript">
			/* DatePicker */
			$(function() {
				$("form[name='emp_search_log']").validate({
					rules: {
						client_Id: {
							required: true
						},
						'project_Id[]': {
							required: true
						},
						form_date: {
							required: true
						},
						to_date: {
							required: true
						}
					},
					messages: {
						client_Id: "Please Select Client Name",
						'project_Id[]': "Please Select Project Name",
						form_date: "Please Select From Date",
						to_date: "Please Select To Date"
					},
					submitHandler: function(form) {
						$("#searchBtn").html('<i class="icon-btn" style="cursor:wait;"><span>Please wait..</span></i>');
						form.submit();
					}
				});
			});

			/* Ajax Based dropdown option changes on Clients, Projects and Tasks */
			function getProjects(client_Id) { // Getting client wise projects based on client id
				$.ajax({
					type: "POST",
					url: "<?php echo base_url('empreports/getClientProjects');?>",
					data: 'client_Id=' + client_Id,
					success: function(data) {
						$("#project_Id").html(data);
						$('#project_Id').removeAttr('disabled');
					}
				});
			}

			$(document).ready(function() {
				var today = $("#form_date").val();
				  var currentYear = new Date().getFullYear();
            var minDate = new Date(2015, 0, 1); // January 1, 2015
            var maxDate = new Date(); // Current date
				$("#form_date, #to_date").datepicker({
					dateFormat: 'yy-mm-dd',
					changeMonth: true,
					   changeYear: true,
                yearRange: '2015:' + currentYear,
                minDate: minDate,
                maxDate: maxDate,
					numberOfMonths: 1,
					onSelect: function(selectedDate) {
						if (this.id == 'form_date') {
							var dateMin = $('#form_date').datepicker("getDate");
							var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(), dateMin.getDate());
							var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(), dateMin.getDate() + 365);
							  // Ensure maxDate doesn't exceed current date
                        if (rMax > maxDate) {
                            rMax = maxDate;
                        }
							$('#to_date').datepicker("option", "minDate", rMin);
							$('#to_date').datepicker("option", "maxDate", rMax);
						}
					}
				});
				$('#to_date').datepicker("option", "minDate", new Date(today));
			});

			$('#client_Id, #project_Id').select2(); // Autosuggest list

			$("#downloadEmployeeData").click(function() {
				$("#table22excel").table2excel({
					// exclude CSS class
					exclude: ".noExl",
					name: "Client Timesheet Report",
					filename: "client_timesheet_report", // do not include extension
					fileext: ".xls", // file extension
					exclude_links: true,
					exclude_inputs: true,
					preserveColors: "preserveColors"
				}); 
			});

			$("#downloadEmployeeData_consolidated").click(function() {
				$("#table22_consolidated_excel").table2excel({
					// exclude CSS class
					exclude: ".noExl",
					name: "Client Consolidated Timesheet Report",
					filename: "client_consolidated_timesheet_report", // do not include extension
					fileext: ".xls", // file extension
					exclude_links: true,
					exclude_inputs: true,
					preserveColors: "preserveColors"
				}); 
			});

		</script>
		<script src="<?php echo HTTP_JS_PATH; ?>jquery.table2excel.js"></script>
		<!-- Include Footer here -->
		<?php $this->load->view('includes/cRMFooter'); ?>
		<!-- Include Footer here END -->
