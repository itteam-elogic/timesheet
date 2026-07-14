<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
$getClientNames      		= $this->client_model->getManagerwiseClients(); // List of Clients
$getListOfEmployees   		= $this->timesheet_login->getListOfEmpInformation(); // List of Clients	

	$selectedDepartments = array();
	if (!empty($_REQUEST['department'])) {
		$selectedDepartments = is_array($_REQUEST['department']) ? $_REQUEST['department'] : array($_REQUEST['department']);
	}

	if(!empty($_REQUEST['form_date'])):
		$fromDate = $_REQUEST['form_date'];
		$toDate = $_REQUEST['to_date'];
		$departM = !empty($selectedDepartments) ? $selectedDepartments : '';
		$clientN = isset($_REQUEST['client_Id']) ? $_REQUEST['client_Id'] : '';
		$pManagerN = isset($_REQUEST['project_manger']) ? $_REQUEST['project_manger'] : '';
		$teamMemberN = isset($_REQUEST['team_member']) ? $_REQUEST['team_member'] : '';
		$selectBGC = 'style="background:rebeccapurple; color:white; font-weight: bold"';
	else:
		$fromDate = date('Y-m-d');
		$toDate = date('Y-m-d');
		$departM = '';
		$clientN = '';
		$pManagerN = '';
		$teamMemberN = '';
		$selectBGC = 'style="background:rebeccapurple; color:white; font-weight: bold"';
	endif; 
/******************* Department Architectural - Structural - 3D Visualisation  assign Variable ****************************************/
	$learning_development  = 0;
	$totalAvailableTeamMembers = 0;
	$totalTrainingTeamMembers = 0;
	$totalNewJoineeTrainingTM = 0;
	$totalLeaveTM = 0;
	$totalUnPLeaveTM = 0;
	$totalNATM = 0;
	$totalProductionTM = 0; 
	$totalWFHTM = 0; // Initialize total WFH Team Members
	$totalAvailableHours = 0; // Initialize total available hours
	$totalTrainingHours = 0; // Initialize total Training hours
	$totalNJTrainingHours = 0; // Initialize total  New Joinee hour
	$totalLeaveHours = 0; // Initialize total Leave hours
	$totalUNPLeaveHours = 0;  // Initialize total Unplanned Leave hours
	$totalProductionHours = 0;  // Initialize total Unplanned Leave hours
	$totalNAHours	=  0; // Initialize total entered (NA)
	$totalWFHHours = 0; // Initialize total WFH hours
	$wfhTeamMembers = array(); // Initialize WFH Team Members array
    $totalLearning_DevelopmentHours = 0;
	$sumofTotalTrainingHours = 0 ;
	$training_faciliatory	= 0;

/******************* Department Architectural - Structural - 3D Visualisation  assign Variable ****************************************/

/******************* Department MEP - Mechanical - Electrical - Plumbing assign Variable ****************************************/
$mep_learning_development  = 0;
$mep_totalAvailableTeamMembers = 0;
$mep_totalTrainingTeamMembers = 0;
$mep_totalNewJoineeTrainingTM = 0;
$mep_totalLeaveTM = 0;
$mep_totalUnPLeaveTM = 0;
$mep_totalNATM = 0;
$mep_totalProductionTM = 0; 
$mep_totalWFHTM = 0; // Initialize total WFH Team Members
$mep_totalAvailableHours = 0; // Initialize total available hours
$mep_totalTrainingHours = 0; // Initialize total Training hours
$mep_totalNJTrainingHours = 0; // Initialize total  New Joinee hour
$mep_totalLeaveHours = 0; // Initialize total Leave hours
$mep_totalUNPLeaveHours = 0;  // Initialize total Unplanned Leave hours
$mep_totalProductionHours = 0;  // Initialize total Unplanned Leave hours
$mep_totalNAHours	=  0; // Initialize total entered (NA)
$mep_totalWFHHours = 0; // Initialize total WFH hours
$mep_wfhTeamMembers = array(); // Initialize WFH Team Members array
$mep_totalLearning_DevelopmentHours = 0;
$mep_sumofTotalTrainingHours = 0; 
$mep_training_faciliatory	= 0;
$kanth222 = 0;
/******************* Department MEP - Mechanical - Electrical - Plumbing assign Variable ****************************************/

//$reporting_manger_architectural = array('41','394','53','71','155','182','270','47');

//$reporting_manger_mep = array('146','230','455','149');


	foreach($getRecords as $key => $resourceResult_trace){

		//echo '<pre>'; print_r($resourceResult_trace);

		//echo 'department'.$resourceResult_trace->department.'<br/><br/>';

		/* $reporting_manger_mep = ['146','230','455','149'];

		if (in_array($resourceResult_trace->reporting_manger, $reporting_manger_mep)) {

			if($resourceResult_trace->task_name == ''){
				
				$totalNAHours += 8.5; // Add hours to total Unplanned Leave  hours

			// Additional logic can be added here if needed
			} else{

				$mep_totalNAHours += 8.5; // Add hours to total Unplanned Leave  hours

			}
		} */
		


		
	if(in_array($resourceResult_trace->reporting_manger, ['41','394','53','71','155','182','270','47'])){ 
		
		if($resourceResult_trace->task_name == 'Available'){
			$totalAvailableTeamMembers++;
			$totalAvailableHours += $resourceResult_trace->emp_time_hours;  // Add hours to total available hours
		}elseif($resourceResult_trace->task_name == 'Training' || $resourceResult_trace->task_name == 'Learning & Development'){
				$totalTrainingTeamMembers++;
				$totalTrainingHours += $resourceResult_trace->emp_time_hours;  // Add hours to total Training hours
				
		}elseif($resourceResult_trace->task_name == 'Training Faciliatory'){
			$training_faciliatory += $resourceResult_trace->emp_time_hours;  // Add hours to total Training hours
		}elseif($resourceResult_trace->task_name == 'New Joinee Training'){
			$totalNewJoineeTrainingTM++;
			$totalNJTrainingHours += $resourceResult_trace->emp_time_hours;  // Add hours to total New Joinee hours		
		}elseif($resourceResult_trace->task_name == 'Leave'){
			$totalLeaveTM++;
				if($resourceResult_trace->emp_time_hours != '0'){
					$totalLeaveHours += $resourceResult_trace->emp_time_hours;  // Add hours to total Leave  hours
				}else{
					$totalLeaveHours += 8.5; // Add hours to total Leave  hours
				}			
		}elseif($resourceResult_trace->task_name == 'Unplanned Leave'){
			$totalUnPLeaveTM++;
				if($resourceResult_trace->emp_time_hours != '0'){
					$totalUNPLeaveHours += $resourceResult_trace->emp_time_hours;  // Add hours to total Unplanned Leave  hours
				}else{
					$totalUNPLeaveHours += 8.5; // Add hours to total Leave  hours
				}

		}elseif($resourceResult_trace->task_name == ''){
			$totalNATM++;
			$totalNAHours += 8.5; // Add hours to total Unplanned Leave  hours

		}elseif($resourceResult_trace->workplace == 'WFH'){

			if (!in_array($resourceResult_trace->team_member, $wfhTeamMembers)) {
				$wfhTeamMembers[] = $resourceResult_trace->team_member; // Add team member to WFH array
			} 
			$totalWFHTM = count($wfhTeamMembers); // Update total WFH Team Members count
			
			//$totalWFHHours += $resourceResult_trace->emp_time_hours;  // Add hours to total Training hours
			
		}else{
			   $totalProductionTM++;
			   $totalProductionHours += $resourceResult_trace->emp_time_hours; // Add hours to total Unplanned Leave  hours

		}

		//echo .'Victory'.$sumofTotalTrainingHours = $totalTrainingHours + $totalLearning_DevelopmentHours;

	  }else{

			if($resourceResult_trace->task_name == 'Available'){
				$mep_totalAvailableTeamMembers++;
				$mep_totalAvailableHours += $resourceResult_trace->emp_time_hours;  // Add hours to total available hours
			}elseif($resourceResult_trace->task_name == 'Training' || $resourceResult_trace->task_name == 'Learning & Development'){
				$mep_totalTrainingTeamMembers++;
				$mep_totalTrainingHours += $resourceResult_trace->emp_time_hours;  // Add hours to total Training hours
				$mep_learning_development++; // Increment learning development count for both tasks
				if($resourceResult_trace->workplace == 'WFH'):

						$kanth222 = count($resourceResult_trace->workplace);

				endif;	


			}elseif($resourceResult_trace->task_name == 'Training Faciliatory'){			
				$mep_training_faciliatory += $resourceResult_trace->emp_time_hours;  // Add hours to total Training hours			
			}elseif($resourceResult_trace->task_name == 'New Joinee Training'){
				$mep_totalNewJoineeTrainingTM++;
				$mep_totalNJTrainingHours += $resourceResult_trace->emp_time_hours;  // Add hours to total New Joinee hours		
			}elseif($resourceResult_trace->task_name == 'Leave'){
				$mep_totalLeaveTM++;
					if($resourceResult_trace->emp_time_hours != '0'){
						$mep_totalLeaveHours += $resourceResult_trace->emp_time_hours;  // Add hours to total Leave  hours
					}else{
						$mep_totalLeaveHours += 8.5; // Add hours to total Leave  hours
					}			
			}elseif($resourceResult_trace->task_name == 'Unplanned Leave'){
				$mep_totalUnPLeaveTM++;
					if($resourceResult_trace->emp_time_hours != '0'){
						$mep_totalUNPLeaveHours += $resourceResult_trace->emp_time_hours;  // Add hours to total Unplanned Leave  hours
					}else{
						$mep_totalUNPLeaveHours += 8.5; // Add hours to total Leave  hours
					}
	
			}elseif($resourceResult_trace->task_name == ''){
				$mep_totalNATM++;
				$mep_totalNAHours += 8.5; // Add hours to total Unplanned Leave  hours
	
			}elseif($resourceResult_trace->workplace == 'WFH'){
	
				 if (!in_array($resourceResult_trace->team_member, $mep_wfhTeamMembers)) {
					$mep_wfhTeamMembers[] = $resourceResult_trace->team_member; // Add team member to WFH array
				} 
				$mep_totalWFHTM = count($mep_wfhTeamMembers); // Update total WFH Team Members count
				
				//$mep_totalWFHTM	 += $resourceResult_trace->emp_time_hours;  // Add hours to total Training hours
				
				
			}else{
				   $mep_totalProductionTM++;
				   $mep_totalProductionHours += $resourceResult_trace->emp_time_hours; // Add hours to total Unplanned Leave  hours

		}

		//echo 'Rese----'.$kanth222;

		//$mep_sumofTotalTrainingHours = $totalTrainingHours + $totalLearning_DevelopmentHours;

	}
	

}
	//echo 'Production Hours--'.$wfhTeamMembers; 



?>
<!-- Inlude Header here END-->

<div class="content-wrapper">
	<div class="page-title" style="padding:5px 30px;">
		<div>
			<h1>Resource Schedule Information</h1>
		</div>
		<div>
			<a class="btn btn-primary btn-flat" href="<?php echo base_url();?>resource_schedule/add" data-toggle="tooltip" title="Add"><i class="fa fa-lg fa-plus"></i> Add Resource</a> |  <a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url();?>resource_schedule"><i class="fa fa-lg fa-refresh"></i></a>
		</div>
	</div>

	<!-- <div class="card">
		
		<div class="text-center">
		<span><b style="color:#428bc5; cursor:pointer;" title="Production Hours : <?=$totalNATM;?>">Prod</b> : 
				<span class="badge rounded-pill btn-lg" style="background-color: #428bc5;" title="Production Hours: <?=$totalProductionHours;?>">H<br><?=$totalProductionHours;?></span> 
		</span>	&nbsp;&nbsp;
		<span><b style="color:#C2FFC7; color: #000; cursor:pointer;" title="Training: <?=$totalTrainingTeamMembers;?>">TRN</b> : 
			<span class="badge rounded-pill btn-lg" style="color: #000; background-color: #C2FFC7;" title="Training: <?=$totalTrainingTeamMembers;?>">C<br><?=$totalTrainingTeamMembers;?></span> 
			<span class="badge rounded-pill btn-lg" style="color: #000; background-color: #C2FFC7;" title="Training: <?=$totalTrainingHours;?>">H<br><?=$totalTrainingHours;?></span> 
		   </span>&nbsp;&nbsp;
			<span><b style="color:#ffa500; cursor:pointer;" title="New Joinee Training: <?=$totalNewJoineeTrainingTM;?>">INT</b> : 
			<span class="badge rounded-pill btn-lg" style="background-color: #ffa500;" title="New Joinee Training: <?=$totalNewJoineeTrainingTM;?>">C<br><?=$totalNewJoineeTrainingTM;?></span> 
			<span class="badge rounded-pill btn-lg" style="background-color: #ffa500;" title="New Joinee Training: <?=$totalNJTrainingHours;?>">H<br><?=$totalNJTrainingHours;?></span> 
		</span>&nbsp;&nbsp;
		<span><b style="color:#059212; cursor:pointer;" title="Available: <?=$totalAvailableTeamMembers;?>">AVL </b> : 
			<span class="badge rounded-pill btn-lg" style="background-color: #059212;" title="Available: <?=$totalAvailableTeamMembers;?>">C<br><?=$totalAvailableTeamMembers;?></span>  
			<span class="badge rounded-pill btn-lg" style="background-color: #059212;" title="Available: <?=$totalAvailableHours;?>">H<br><?=$totalAvailableHours;?></span> 
		</span>&nbsp;&nbsp;
			<span><b style="color:#7030a0; cursor:pointer;" title="Planned Leave: <?=$totalLeaveTM;?>">PL</b> : 
			<span class="badge rounded-pill btn-lg" style="background-color: #7030a0;" title="Leave: <?=$totalLeaveTM;?>">C<br><?=$totalLeaveTM;?></span> 
			<span class="badge rounded-pill btn-lg" style="background-color: #7030a0;" title="Leave: <?=$totalLeaveHours;?>">H<br><?=$totalLeaveHours;?></span>
		</span>&nbsp;&nbsp;
			<span><b style="color:#FFE31A; cursor:pointer; color: #000;" title="Unplanned Leave: <?=$totalUnPLeaveTM;?>">UPL</b> : 
			<span class="badge rounded-pill btn-lg" style="background-color: #FFE31A;color: #000;" title="Unplanned Leave: <?=$totalUnPLeaveTM;?>">C<br><?=$totalUnPLeaveTM;?></span> 
			<span class="badge rounded-pill btn-lg" style="background-color: #FFE31A; color: #000;" title="Unplanned Leave: <?=$totalUNPLeaveHours;?>">H<br><?=$totalUNPLeaveHours;?></span> 
		</span>&nbsp;&nbsp;
		<span><b style="color:#FF0000; cursor:pointer;" title="Not Entered: <?=$totalNATM;?>">NA</b> : 
			<span class="badge rounded-pill btn-lg" style="background-color: #FF0000;" title="Not Assessed: <?=$totalNATM;?>">C<br><?=$totalNATM;?></span> 
			<span class="badge rounded-pill btn-lg" style="background-color: #FF0000;" title="Not Assessed: <?=$totalNAHours;?>">H<br><?=$totalNAHours;?></span> 
		</span>&nbsp;&nbsp;
		
		</div>

	</div> -->

	

	<div class="card">		
		<div class="card-body">
			<div class="row">

				<!-- Search for employee with date wise and client , project wise as well. -->
				<div class="col-md-12">
					<div class="bs-component">
						<div class="tab-content" id="myTabContent">
							<!-- Employee Report adding block -->
							<form class="" name="resource_date_search" id="resource_date_search" method="get" action="<?php echo base_url('resource_schedule');?>">
								<div class="tab-pane">
									<div class="row">
<div class="col-md-3">
     <div class="form-group">
         <label class="control-label">Department</label>
         <div class="multiple-options">
              <select class="form-control" id="department" name="department[]" multiple>
    <option value="all" <?= in_array('all', $selectedDepartments) ? 'selected="selected"' : ''; ?>>All Departments</option>
    <option value="Architectural" <?= in_array('Architectural', $selectedDepartments) ? 'selected="selected"' : ''; ?>>Architectural</option>
    <option value="Structural" <?= in_array('Structural', $selectedDepartments) ? 'selected="selected"' : ''; ?>>Structural</option>
    <option value="2D Auto CAD" <?= in_array('2D Auto CAD', $selectedDepartments) ? 'selected="selected"' : ''; ?>>2D Auto CAD</option>
    <option value="MEP" <?= in_array('MEP', $selectedDepartments) ? 'selected="selected"' : ''; ?>>MEP</option>
    <option value="3D Visualization" <?= in_array('3D Visualization', $selectedDepartments) ? 'selected="selected"' : ''; ?>>3D Visualization</option>
</select>

         </div>
    </div>
</div>
										<div class="col-md-2">
											<div class="form-group">
												<label class="control-label">Client</label>
												<select class="form-control" id="client_Id" name="client_Id">
												  <option value="all" selected="selected">ALL</option>
													<?php foreach($getClientNames as $key => $clientName): ?>
													<option value="<?php echo $clientName->client_Id;?>" <?=$clientN == $clientName->client_Id ? 'selected="selected"' : '';?>><?php echo ucfirst($clientName->client_name);?></option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
									<div class="col-md-2">
    <div class="form-group">
        <label class="control-label">Manager</label>
        <select class="form-control" id="project_manger" name="project_manger">
            <option value="all" selected="selected">ALL</option>

            <?php
            $allowedManagers = [
                'sandeep anupati',
                'shivani patil',
                'siva krishna',
                'rahul kumar',
                'srinivas gollakonda',
                'syed afsar',
                'syed farhan',
                'pradip chauhan',
                'rajanikanth basuthkar',
                'nikhil bachawal'
            ];

            foreach($this->timesheet_login->getReportingManagers() as $managerResult):
                if(in_array(strtolower(trim($managerResult->name)), $allowedManagers)):
            ?>
                <option value="<?php echo $managerResult->empId; ?>" 
                    <?= $pManagerN == $managerResult->empId ? 'selected="selected"' : ''; ?>>
                    <?php echo $managerResult->name; ?>
                </option>
            <?php
                endif;
            endforeach;
            ?>
        </select>
    </div>
</div>
										<div class="col-md-2">
											<div class="form-group">
												<label class="control-label">Employee</label>
											<select class="form-control" id="team_member" name="team_member">
												<option value="all">ALL</option>
												<?php foreach($getListOfEmployees as $key => $employeeName): ?>
												<option value="<?php echo $employeeName->empId;?>" <?=$teamMemberN == $employeeName->empId ? 'selected="selected"' : '';?>><?php echo ucfirst($employeeName->name);?></option>
												<?php endforeach; ?>
											</select>
											</div>
										</div>
										<div class="col-md-1">
											<div class="form-group">
												<label class="control-label">From Date</label>
												<input class="form-control" type="text" id="form_date" name="form_date" placeholder="Select From Date" value="<?php echo !empty($_REQUEST['form_date']) ? $fromDate : ''; ?>" readonly="" <?=$selectBGC?>>
											</div>
										</div>
										<div class="col-md-1">
											<div class="form-group">
												<label class="control-label">To Date</label>
												<input class="form-control" type="text" id="to_date" name="to_date" placeholder="Select To Date" value="<?php echo !empty($_REQUEST['to_date']) ? $toDate : ''; ?>" readonly="" <?=$selectBGC?>>
											</div>
										</div>
										<div class="col-md-1">
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
		


</div>
<!-- Inlude Footer here -->
<script language="javascript" type="text/javascript">
	/* DatePicker */
	$(function() {
		$("form[name='resource_date_search']").validate({
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
		});
		$("#to_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			  changeYear: true,
                yearRange: '2015:' + currentYear,
                minDate: minDate,
                maxDate: maxDate,
			numberOfMonths: 1,
		});

	})

	$("#downloadSearch_resouce_data").click(function() {
		$("#table22excel_all").table2excel({
			// exclude CSS class
			exclude: ".noExl",
			name: "Resource Schedule Report",
			filename: "resource_report", // do not include extension
			fileext: ".xls", // file extension
			exclude_links: true,
			exclude_inputs: true,
			preserveColors: "preserveColors"
		});
	});

	jQuery('#client_Id,#project_manger,#team_member').select2(); // Autosuggest list
</script>
<script src="<?php echo HTTP_JS_PATH; ?>jquery.table2excel.js"></script>




<script>
$(document).ready(function () {

    $('#department').select2({
        placeholder: "Select Departments",
        width: '100%'
    });

    <?php if (!empty($selectedDepartments)): ?>
    $('#department').val(<?php echo json_encode($selectedDepartments); ?>).trigger('change');
    <?php endif; ?>

    $('#department').on('change', function () {
        let selected = $(this).val();

        if (selected && selected.includes('all')) {
            // keep ONLY "all"
            $(this).val(['all']).trigger('change.select2');
        }
    });

});
</script>

<style>

/* Selected items container */
.select2-container--default
.select2-selection--multiple
.select2-selection__rendered {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
	
}

/* Selected option pill (LOOK LIKE PLACEHOLDER) */
.multiple-options .form-control .option {
    background-color: #673AB7;
    color: #fff;
    border-radius: 3px;
    cursor: default;
    float: left;
    margin-right: 6px;
    margin-top: 6px;
    padding: 1px 6px;
}
/* ================= SELECT2 HEIGHT FIX ================= */

/* Match Bootstrap input height */
.select2-container .select2-selection--multiple {
    min-height: 30px;          /* Bootstrap default */
    padding: 4px 5px;
    border-radius: 4px;
    border: 1px solid #ced4da;
}

/* Align text properly */
.select2-container--default .select2-selection--multiple .select2-selection__rendered {
    line-height: 28px;
}

/* Selected item (tag) style */
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    margin-top: 4px;
    font-size: 14px;
}

/* Placeholder alignment */
.select2-container--default .select2-selection--multiple .select2-search__field {
    height: 28px;
    margin-top: 4px;
}

/* Force full width like other dropdowns */
.select2-container {
    width: 100% !important;
}

/* ====================================================== */

</style>





<style>
	.select2-container--default .select2-selection--single .select2-selection__rendered {
		background-color: #663399;
		color: #fff;
		font-weight: bold;
	}

	.btn-lg {
  width: 50px !important;
  height: 50px !important;
  padding: 10px 10px !important;
  font-size: 12px !important;
  line-height: 1.33 !important;
  border-radius: 25px !important;
  cursor: pointer;
}	

	.leave{background-color: #7030a0; cursor: pointer; padding: 10px; color: #FFF;}
	.training{background-color: #C2FFC7;cursor: pointer; padding: 10px; color: #000;}
	.njtraining{background-color: #ffa500;cursor: pointer; padding: 10px; color: #000; }
	.upleave{background-color: #FFE31A; cursor: pointer; padding: 10px; color: #000;}
	.available {background-color: #059212; cursor: pointer; padding: 10px; color: #FFF;}
	.na { background-color: #FF0000; cursor: pointer; padding: 10px; color: #FFF;}
	.wfh { background-color: #008b8b; cursor: pointer; padding: 10px; color: #FFF;} 
	.production { background-color: #428bc5;  cursor: pointer; padding: 10px; color: #fff;}
	.faciliatory { background-color: #FFB6C1;  cursor: pointer; padding: 10px; color: #000;}
</style>
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->