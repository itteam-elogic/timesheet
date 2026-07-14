<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
	
	$monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
 	
	$friday = date( 'Y-m-d', strtotime( 'saturday this week' ) );
  
     $getWeekDates = $this->defaulter_model->weeklyDays(); 

   $listofManagerProjects = $this->defaulter_model->getMangerProjects();
                                    
  //echo '<pre>'; print_r($listofManagerProjects);

 $yesterdayDate = date('Y-m-d',strtotime("-1 days"));

?>
<div class="content-wrapper">
	<div class="page-title">
		<div>
			<h1>Timesheet Defaulter Log : <?php echo $monday.'  To  '. $friday;?></h1>
		</div>
		<div> <a class="btn btn-primary btn-flat" href="<?php echo base_url('defaulter/user_defaulter');?>" data-toggle="tooltip" title="refresh"><i class="fa fa-chevron-circle-left"></i></a> </div>
	</div>
	<div class="card">
		<h3 class="card-title"></h3>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<div class="bs-component">
						<div class="tab-content" id="myTabContent">
							<form class="" name="user_defaulter" id="user_defaulter" method="post" action="<?php echo base_url('defaulter/memberSearch');?>">
								<div class="tab-pane fade active in" id="Add">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">Role</label>
												<select class="form-control" id="def_user_status_type" name="def_user_status_type">
													<option value="member">Member</option>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">Week Start Date</label>
												<input class="form-control" type="text" id="def_form_date" name="def_form_date" placeholder="Select From Date" value="" readonly="">
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">Week End Date</label>
												<input class="form-control" type="text" id="def_to_date" name="def_to_date" placeholder="Select To Date" value="" readonly="">
											</div>
										</div>
									</div>
									<div class="card-footer">
										<button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Search</button>
										<a href="<?php echo base_url();?>defaulter" data-toggle="Go To Report Log!" title="Cancel">
											<button class="btn btn-default icon-btn" type="button"><i class="fa fa-chevron-circle-left"></i>Back</button>
										</a> </div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div> 
	<!-- Get List of user not filled report log on date wise -->

	<div class="card">
		
		<div class="card-body">
			<div class="page-title">
				<div>
					<h1>Members Not Entered Report Log</h1>
				</div>
				<div align="left"> <a href="<?php echo base_url('defaulter/previous_user_defaulter');?>" class="btn btn-info btn-flat">Previous Week Report Log</a></div>
				<div> <button id="downloadEmployeeData" class="btn btn-primary btn-flat">Export Member data into Excel</button></div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">	
						<table class="table table-hover table-bordered" id="table2excel">
							<thead>
								<tr>
									<th style="background-color:#c1c1c1">Sno</th>
									<th style="background-color:#c1c1c1">Employee Name</th>
									<th style="background-color:#c1c1c1">No of Instances</th>
                                     <th style="background-color:#c1c1c1;font-weight:Bold;"><?php echo $yesterdayDate; ?></th>
									
								</tr>
							</thead>
							<tbody>
								<?php 
								     //Getting the current week days
                                
                                $cntNumber = 0;
                               

								
								foreach($getEmpResult as $key => $member){ 
                                    
  $getMemberReportLog = $this->db->select('er.empId,er.emp_report_dates,SUM(emp_time_hours) as totalHours')->from('emp_record_details er')->where('er.empId',$member->empId)->where('er.emp_report_dates',$yesterdayDate)->group_by('er.empId')->order_by('er.emp_report_dates' , 'DESC')->get()->result();
						//echo $this->db->last_query().'<br>';
                                    
						foreach($getMemberReportLog as  $numCR => $getWMResult){ 
                          
                           	?>
								<tr>
									<td>
										<?php echo $cntNumber+1;?>
									</td>
									<td>
										<?php echo $member->name;?>
									</td>
									<td>
										<?php echo '1'?>
									</td>
									
									
									
									<?php
											echo $kanthD = '<td style="background-color:#4caf50; color:#FFF;font-weight:bold;text-align:center;">'.$getWMResult->totalHours.'</td>';
								    	//echo $kanthD;
									
									 ?>
									<!-- We are not there in database we have to show dates -->
								</tr>
								<?php  $cntNumber++; } ?>
								<?php  } ?>
							</tbody>
						</table>
					</div>
				</div>
				<!-- Displaying Search Result -->
			</div>
		</div>
	</div>

	<!-- End of the report log -->

</div>
<script language="javascript" type="text/javascript">
	/* DatePicker */
	$(function() {
		$("form[name='user_defaulter']").validate({
			rules: {
				def_user_status_type: {
					required: true
				},
				def_form_date: {
					required: true
				},
				def_to_date: {
					required: true
				}
			},
			messages: {
				def_user_status_type: "Please Select User Type",
				def_form_date: "Please Select From Date",
				def_to_date: "Please Select To Date"
			},
			submitHandler: function(form) {
				form.submit();
			}
		});
	});

	/*Ajax Based dropdown option changes on Clients , Projects and Tasks*/

	$(document).ready(function() {
		var today = $("#def_form_date").val();

		var fridayDate = $("#def_to_date").val();

		$("#def_form_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			numberOfMonths: 1,
		});

		$("#def_to_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			numberOfMonths: 1,
		});

	})

	$('#def_user_status_type').select2(); // Autosuggest list
	
	$("#downloadEmployeeData").click(function(){
  $("#table2excel").table2excel({
    // exclude CSS class
    exclude: ".noExl",
    name: "Report for employees",
    filename: "ReportLog", //do not include extension
    fileext: ".xls", // file extension
	  exclude_links: true,
	  exclude_inputs: true,
	  preserveColors: "preserveColors"
	  
	  
  }); 
});
</script>
<script src="<?php echo HTTP_JS_PATH; ?>jquery.table2excel.js"></script>

<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->