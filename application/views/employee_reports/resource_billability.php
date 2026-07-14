<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 

  $getClientNames      			= $this->client_model->getClientName(); // List of Clients
  
   $taskClientId   				= ''; // Getting client ID
  
   $getListOfProjects   		= $this->project_model->getProjectName($taskClientId); // List of Clients
  
   $taskProjectId 				= '';
  
   $getListOfTask		   		= $this->task_model->getTaskName($taskProjectId); // List of Clients
?>
<div class="content-wrapper">
	<div class="page-title">
		<div>
			<h1><i class="fa fa-bell"></i> Resource Billability </h1>
		</div>
		<div> <a class="btn btn-primary btn-flat" href="<?php echo base_url();?>empreports/resource_billability" data-toggle="tooltip" title="refresh"><i class="fa fa-chevron-circle-left"></i></a> </div>
	</div>
	<div class="card">
		<h3 class="card-title"></h3>
		<div class="card-body">
			<div class="row">
				<!-- Search for employee with date wise and client , project wise as well. -->
				<div class="col-md-12">
					<div class="bs-component">
						<div class="tab-content" id="myTabContent">
							<!-- Employee Report adding block -->
							<form class="" name="emp_search_log" id="emp_search_log" method="post" action="<?php echo base_url('empreports/resource_billability');?>">
								<div class="tab-pane fade active in" id="Add">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">Client's</label>
												<select class="form-control" id="client_Id" name="client_Id" onChange="getProjects(this.value);">
													  <option value="">Please select client</option>
													  <option value="all">All</option>
													  <?php foreach($getClientNames as $key => $clientName): ?>
													  <option value="<?php echo $clientName->client_Id;?>"><?php echo ucfirst($clientName->client_name);?></option>
													  <?php endforeach; ?>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">Project's</label>
												<select class="form-control" id="project_Id" name="project_Id">
													  <option value="">Please select project</option>
													  <?php foreach($getListOfProjects as $key => $projectName): ?>
													  <option value="<?php echo $projectName->project_Id;?>" ><?php echo ucfirst($projectName->project_name);?></option>
													  <?php endforeach; ?>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">From Date</label>
												<input class="form-control" type="text" id="form_date" name="form_date" placeholder="Select From Date" readonly="">
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">To Date</label>
												<input class="form-control" type="text" id="to_date" name="to_date" placeholder="Select To Date" readonly="">
											</div>
										</div>
									</div>
									<div class="card-footer">
										<button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Search</button>
										<a href="<?php echo base_url();?>empreports" data-toggle="Go To Report Log!" title="Cancel">
											<button class="btn btn-default icon-btn" type="button"><i class="fa fa-chevron-circle-left"></i>Back</button>
										</a> </div>
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
	<?php if(!empty($resultTimeLog)):?>
	<div class="card">
		<div class="card-body">
			<div class="row">
					<div style="text-align:center; position:relative;bottom:20px;">
					<h3><i class="fa fa-bell"></i> Resource Billability</h3> <span style="position: absolute;top: 0px;right: 21px;">
						<a href="<?php echo base_url()?>empreports/pdfResourceBillable?client_Id=<?php echo $_REQUEST['client_Id'];?>&project_Id=<?php echo $_REQUEST['project_Id'];?>&form_date=<?php echo $_REQUEST['form_date'];?>&to_date=<?php echo $_REQUEST['to_date'];?>">
							<button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Download PDF</button>
						</a></span></div>
				<!-- Displaying Search Result -->
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-hover table-bordered">
							<thead>
								<tr>
									<th>Sno</th>
									<th>Name</th>
									<th>Project Name</th>
									<th>Type</th>
									<th>Billable Hours</th>
									<th>Non-Billable Hours</th>

								</tr>
							</thead>
							<tbody>
								<?php 
									  $i=1;
									  $totalDeveloperBHoursCNT = 0;
									  $totalDeveloperNon_BHoursCNT = 0;
									  foreach ($resultTimeLog as $key => $reportResult) :
								
								  if($i%2 == 0): $showRowColour = 'class="success"'; else: $showRowColour = 'class="info"'; endif;
									
									 	$getListOfProjects   	= $this->emptimelog_model->getAddedReportTaskNames($reportResult->task_Id); // List of tasks
									
								/***************************** Billable And Non-billable hours conditions **************/
									
									if($reportResult->resource_billability == 'Billable'){

											$totalDeveloperBHours   	=  $reportResult->vickty;
											
											$totalDeveloperBHoursCNT   +=  $reportResult->vickty;

									}else{

											$totalDeveloperBHours 		= 	'0';

									} 
								
									if($reportResult->resource_billability == 'Non_billable'){

											$totalDeveloperNon_BHours   	=  $reportResult->vickty;
										
										    $totalDeveloperNon_BHoursCNT    += $reportResult->vickty;

									}else{

											$totalDeveloperNon_BHours 		= 	'0';

									}  
								
						 	  /***************************** Billable And Non-billable hours conditions **************/
								
							 		?>
								<tr <?php echo $showRowColour; ?> id="delRecordsRow
									<?php echo $reportResult->emp_record_id; ?>">
									<td>
										<?php echo $i ?>
									</td>
									<td><span class="label label-info"><?php echo ucfirst($reportResult->name);?></span></td>
									<td>
										<?php echo ucfirst($reportResult->project_name);?> </td>
									<td>
										<?php echo ucfirst($reportResult->resource_billability);?> </td>
									<td>
										<?php echo $totalDeveloperBHours; ?> </td>
									<td>
										<?php echo $totalDeveloperNon_BHours; ?> </td>
								</tr>
								<?php $i++; endforeach; ?>
								<tr>
									<td colspan="4" style="text-align:right;"><b>Billable Hours : </b></td>
									<td colspan="5"><span style="color:#1322d2; font-weight:bold;"><?php echo $totalDeveloperBHoursCNT; ?></span> </td>
								</tr>
								<tr>
									<td colspan="5" style="text-align:right;"><b>Non-Billable Hours : </b></td>
									<td colspan="6"><span style="color:#1322d2; font-weight:bold;"><?php echo $totalDeveloperNon_BHoursCNT; ?> </span> </td>
								</tr>
								
							</tbody>
						</table>
					</div>
				</div>
				<!-- Displaying Search Result -->
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
				project_Id: {
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
				project_Id: "Please Select Project Name",
				form_date: "Please Select From Date",
				to_date: "Please Select To Date"
			},
			submitHandler: function(form) {
				form.submit();
			}
		});
	});

	/*Ajax Based dropdown option changes on Clients , Projects and Tasks*/

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

	})

	$('#client_Id,#project_Id').select2(); // Autosuggest list
</script>

<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->