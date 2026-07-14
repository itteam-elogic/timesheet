<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
	
	$monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
 	
	$friday = date( 'Y-m-d', strtotime( 'friday this week' ) );

?>
<div class="content-wrapper">
	<div class="page-title">
		
        <?php if(!empty($_REQUEST['user_status_type']) == 'developer') : ?>
            <div>
                <h1>Timesheet Report Log </h1>
            </div>
        <?php else: ?>
             <div>
                <h1> Unapproved Timesheet Report Log </h1>
            </div>        
        <?php endif; ?>
		<div> <a class="btn btn-primary btn-flat" href="<?php echo base_url();?>empreports/email_unapproved_pms" data-toggle="tooltip" title="refresh"><i class="fa fa-chevron-circle-left"></i></a> </div>
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
							<form class="" name="user_unapproved_search_log" id="user_unapproved_search_log" method="post" action="<?php echo base_url('empreports/email_unapproved_pms');?>">
								<div class="tab-pane fade active in" id="Add">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">Role</label>
												<select class="form-control" id="user_status_type" name="user_status_type">
													  <option value="">Please select user type</option>
													  <option value="manager">Manager</option>
													  <option value="developer">Member</option>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">Week Start Date</label>
												<input class="form-control" type="text" id="form_date" name="form_date" placeholder="Select From Date" value="<?php echo $monday;?>" readonly="">
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">Week End Date</label>
												<input class="form-control" type="text" id="to_date" name="to_date" placeholder="Select To Date" value="<?php echo $friday; ?>" readonly="">
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
	<?php if(!empty($unapprovedRLResult)): ?>
	<div class="card">
		<div class="card-body">
			
            <div class="page-title">
				<?php if($_REQUEST['user_status_type'] == 'developer') : ?>
                <div>
					<h1>Members Not Entered Timesheet Report Log</h1>
				</div>	
                <?php else: ?>
                <div>
					<h1>Unapproved Project Managers</h1>
				</div>	
                
				<?php 
				 if($friday == date("Y-m-d")): // Only This condition work on weekend that is friday only....
				
					$getWeeklyEmailProcess  = $this->emptimelog_model->getWeeklyApprovedEamilProcess($friday);
				
						if(count($getWeeklyEmailProcess) == '0'): 
				
				  ?> 
				<div id="showEmailMessage"><a href="#" onclick="sendEmailUnapprovedManagersList('weeklyeamil_report','<?php echo $_REQUEST['user_status_type'];?>','<?php echo $_REQUEST['form_date'];?>','<?php echo $_REQUEST['to_date'];?>');"><button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Send Email </button></a></div>
				
				<?php else: ?>
				
				<div class="bs-component">
                      <p class="text-danger">
						  <strong>The details of unapproved timesheets is sent for this week.</strong>
                      </p>
                </div>
				
				<?php endif; ?>
				<?php else: ?>
				<div class="bs-component">
                      <p class="text-danger">
						  <strong>Send Email Button Showing on only Friday, If we are click the button only one time.</strong>
                      </p>
                </div>
				<?php endif; ?>
                
              <?php endif; ?>    
			</div>
            
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-hover table-bordered">
							<thead>
								<tr>
									<th>Sno</th>
									<th>Name</th>
								</tr>
							</thead>
							<tbody>
								<?php $i=0; foreach($unapprovedRLResult as $getResult): $i++;?>
								<tr>
									<th>
										<?php echo $i; ?>
									</th>
									<th>
										<?php echo $getResult?> </th>
								</tr>
								<?php endforeach; ?>
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
		$("form[name='user_unapproved_search_log']").validate({
			rules: {
				user_status_type: {
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
				user_status_type: "Please Select User Type",
				form_date: "Please Select From Date",
				to_date: "Please Select To Date"
			},
			submitHandler: function(form) {
				form.submit();
			}
		});
	});

	/*Ajax Based dropdown option changes on Clients , Projects and Tasks*/

	
	/***************** Email send to the unapproved task reports on project managers *********************/
	
	var userSearchType; 
	
	function sendEmailUnapprovedManagersList(weeklyeamil_report,user_status_type,form_date,to_date){ 
		       $.ajax({
						type: "POST",
						url: "<?php echo base_url('empreports/email_unapproved_pms');?>",
						data: "weeklyeamil_report="+weeklyeamil_report+"&user_status_type="+user_status_type+"&form_date="+form_date+"&to_date="+to_date,
						beforeSend: function() {
								$('#showEmailMessage').html('<i class="fa fa-spinner"></i><p class="text-danger"> Please wait email sent to pm group</p>');
						 },success: function (response) { 
								$("#showEmailMessage").html('<p class="text-danger">Email sent the succssfully</p>');
									//location.reload();
						 }
              });
		
	}
	
   /***************** Email send to the unapproved task reports on project managers *********************/

	$(document).ready(function() {
		var today = $("#form_date").val();
		 var currentYear = new Date().getFullYear();
            var minDate = new Date(2015, 0, 1); // January 1, 2015
            var maxDate = new Date(); // Current date
		var fridayDate = $("#to_date").val();
		
		$("#form_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			 changeYear: true,
                yearRange: '2015:' + currentYear,
                minDate: minDate,
                maxDate: maxDate,
			numberOfMonths: 1,
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
            
            // Set initial minDate for to_date if form_date has a value
            if (today) {
                $('#to_date').datepicker("option", "minDate", new Date(today));
            } else {
                $('#to_date').datepicker("option", "minDate", minDate);
            }
	});

	$('#user_status_type').select2(); // Autosuggest list
</script>

<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->