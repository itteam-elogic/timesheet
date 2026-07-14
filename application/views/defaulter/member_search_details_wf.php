<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
	
	$monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
 	
	$friday = date( 'Y-m-d', strtotime( 'saturday this week' ) );
  
    //$getWeekDates = $this->defaulter_model->getBetweenDays(); 

    $def_form_date = $this->input->post('def_form_date'); //From dates

   	$def_to_date = $this->input->post('def_to_date');
   
    
	$from_date = new DateTime($def_form_date);

	$to_date = new DateTime($def_to_date);



 //   $getWeekDates = $this->defaulter_model->getBetweenDays($dateDiff); 

   //echo '<pre>'; print_r($getWeekDates);
//echo $def_form_date.'====='.$def_to_date; exit;

?>
<div class="content-wrapper">
	<div class="page-title">
		<div>
			<h1>Timesheet Defaulter Search Log : <?php echo $def_form_date.'  To  '. $def_to_date;?></h1>
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
												<label class="control-label">Start Date</label>
												<input class="form-control" type="text" id="def_form_date" name="def_form_date" placeholder="Select From Date" value="" readonly="">
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">End Date</label>
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
									<?php 
									
									$listbetweenDates = array();
                                    
									for ($date = $from_date; $date <= $to_date; $date->modify('+1 day')) :
								
										$totalDays = $date->format('D / d / M');
                                    
									//echo $date->format('D');
									if(($date->format('D') != 'Sun')):
                                    
	 								    $listbetweenDates[] = $date->format("Y-m-d");
									
									
									?>
									<th style="background-color:#c1c1c1;font-weight:Bold;"><?php echo $totalDays; ?></th>
									<?php endif; ?>
									<?php endfor; ?>
								</tr>
							</thead>
							<tbody>
								<?php 
                                
                                $cntNumber = 0;
								     //Getting the current week days
							

				/* $getMemberReportLog = $this->db->select('er.empId,er.emp_report_dates')
					  ->from('emp_record_details er')->where('er.empId',$member->empId)->where('er.emp_report_dates >=', $def_form_date)->where('er.emp_report_dates <=', $def_to_date)->group_by('er.empId')->order_by('er.emp_report_dates' , 'DESC')->get()->result(); */
                                    
                                    
                                    
                                    
                  /*  $this->db->select('er.empId,er.emp_report_dates,emp.empId,emp.name');
                    $this->db->from('emp_record_details er');                                   
                    //$this->db->join('employee_details', 'er.empId',$member->empId, 'left');
                    $this->db->join('employee_details emp', 'er.empId = emp.empId', 'left');
                    
                    $this->db->where('er.emp_report_dates >=', $def_form_date)->where('er.emp_report_dates <=', $def_to_date);                
                    $this->db->group_by('emp.empId')->order_by('er.emp_report_dates' , 'DESC');
                    $query = $this->db->get();          
                     $result = $query->result(); */   
                                
                                $eLogicTeam = array();
                                
                                 $arrHansini = array();   
                                
                      foreach($getEmpResult as $key => $member){ 
                          
                          $eLogicTeam[] =  $member->empId;
                          
                      }
                                
                                //echo '<pre>'; print_r($eLogicTeam);
                                
                                $this->db->select('er.empId,er.emp_report_dates,emp.name');
                    $this->db->from('emp_record_details er');                                   
                    //$this->db->join('employee_details', 'er.empId',$member->empId, 'left');
                    $this->db->join('employee_details emp', 'er.empId = emp.empId', 'left');
                    $this->db->where_in('er.empId', $eLogicTeam);
                    $this->db->where('er.emp_report_dates <', $def_form_date)->where('er.emp_report_dates >', $def_to_date);                
                    $this->db->group_by('emp.empId')->order_by('er.emp_report_dates' , 'DESC');
                    $query = $this->db->get();          
                     $result = $query->result(); 
                   
                                    
                        echo $this->db->last_query();            
                                    
                                
                        
						foreach($result as  $numCR => $getWMResult){ 
								
								
								//echo $this->db->last_query();
								
							$arrHansini[] =  $getWMResult->emp_report_dates;
								
							  		
								//echo '<pre>'; print_r($listbetweenDates);
								
								$ayyappaS =  array_diff($listbetweenDates,$arrHansini);
								
								$arrayToStringVal =  implode("&nbsp; , &nbsp;" , $ayyappaS); //Array to string conversion 
							   
							    echo '<pre>'; print_r($listbetweenDates);
								?>
								<tr>
									<td>
										<?php echo $cntNumber+1;?>
									</td>
									<td>
										<?php echo $getWMResult->name;?>
									</td>
									<td><?php echo count($ayyappaS); ?></td>
                                    
									<?php for($days = 0; $days < count($listbetweenDates); $days++): 
                            
                            
									
									if(in_array($listbetweenDates[$days] , $ayyappaS)){
									
											$kanthD = '<td style="background-color:#f44336; color:#FFF;font-weight:bold;text-align:center;">No</td>'; 
								}else{
									
											$kanthD = '<td style="background-color:#4caf50; color:#FFF;font-weight:bold;text-align:center;">Yes</td>';
								}
										//echo $kanthD;
									
									 ?>
									<?php echo $kanthD; ?>
									
									<?php endfor;?>
									<!-- We are not there in database we have to show dates -->
								</tr>
								<?php $cntNumber++; } ?>			
								
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

	$("#downloadEmployeeData").click(function() {
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