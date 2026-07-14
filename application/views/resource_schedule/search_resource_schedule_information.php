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
			<a class="btn btn-primary btn-flat" href="<?php echo base_url();?>resource_schedule/add" data-toggle="tooltip" title="Add"><i class="fa fa-lg fa-plus"></i> Add Resource</a> <?php //if(!empty($_REQUEST['form_date'])): ?> | <a class="btn btn-primary btn-flat" id="downloadSearch_resouce_data" data-toggle="tooltip" title="Add"><i class="fa fa-lg fa-plus"></i> Download Resource schedule Report</a> <?php //endif;?> | <a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url();?>resource_schedule"><i class="fa fa-lg fa-refresh"></i></a>
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

	<div class="container-fluid card">
	<div style="margin-bottom: 20px;"><h4>Architectural - Structural - 3D Visualisation </h4></div>
	<div class="row" style="margin-top: 10px;">
            <div class="col-sm-12" style="margin-bottom: 10px; font-size: 13.5px;">
			
                <div class="row">
                    <div class="col-sm-12">
                        <span><b class="production" title="Production hours" : <?=$totalProductionHours;?>>Production Hours : <?=$totalProductionHours;?> </b></span>
						<span><b class="training" title="Training hours" : <?=$totalTrainingHours;?>>Training Hours  : <?=$totalTrainingHours;?> </b></span>
						<span><b class="njtraining" title="Intern Training hours : <?=$totalNJTrainingHours;?>">Intern Training Hours : <?=$totalNJTrainingHours;?> </b></span>
						<span><b class="faciliatory" title="Training Faciliatory hours: <?=$training_faciliatory;?>">Training Faciliatory Hours: <?=$training_faciliatory;?> </b></span>
						<span><b class="available" title="Available hours" : <?=$totalAvailableHours;?>>Available Hours : <?=$totalAvailableHours;?> </b></span>
                        <span><b class="na" title="Not Assigned hours : <?=$totalNAHours;?>">Not Assigned Hours : <?=$totalNAHours;?> </b></span>
						<span><b class="leave" title="Planned Leave Count: <?=$totalLeaveTM;?>">Planned Leave Count : <?=$totalLeaveTM;?> </b></span>
						<span><b class="upleave" title="Unplanned leave Count : <?=$totalUnPLeaveTM;?>">Unplanned leave Count : <?=$totalUnPLeaveTM;?> </b></span>
						<span><b class="wfh" title="WFH Count : <?=$totalWFHTM;?>">WFH Count : <?=$totalWFHTM;?> </b></span>
                    </div>
                </div>
            </div>
        </div>
		<div style="margin-bottom: 20px;"><h4>MEP - Mechanical - Electrical - Plumbing</h4></div>
	<div class="row" style="margin-top: 10px;">
            <div class="col-sm-12" style="margin-bottom: 10px; font-size: 13.5px;">
			
                <div class="row">
                    <div class="col-sm-12">
                        <span><b class="production" title="Production hours" : <?=$mep_totalProductionHours;?>>Production Hours : <?=$mep_totalProductionHours;?> </b></span>
						<span><b class="training" title="Training hours" : <?=$mep_totalTrainingHours;?>>Training Hours  : <?=$mep_totalTrainingHours;?> </b></span>
						<span><b class="njtraining" title="Intern Training hours : <?=$mep_totalNJTrainingHours;?>">Intern Training Hours : <?=$mep_totalNJTrainingHours;?> </b></span>
						<span><b class="faciliatory" title="Training Faciliatory hours: <?=$mep_training_faciliatory;?>">Training Faciliatory Hours: <?=$mep_training_faciliatory;?> </b></span>
						<span><b class="available" title="Available hours" : <?=$mep_totalAvailableHours;?>>Available Hours : <?=$mep_totalAvailableHours;?> </b></span>
                        <span><b class="na" title="Not Assigned hours : <?=$mep_totalNAHours;?>">Not Assigned Hours : <?=$mep_totalNAHours;?> </b></span>
						<span><b class="leave" title="Planned Leave Count : <?=$mep_totalLeaveTM;?>">Planned Leave Count : <?=$mep_totalLeaveTM;?> </b></span>
						<span><b class="upleave" title="Unplanned leave Count : <?=$mep_totalUnPLeaveTM;?>">Unplanned leave Count : <?=$mep_totalUnPLeaveTM;?> </b></span>
						<span><b class="wfh" title="WFH Count : <?=$mep_totalWFHTM;?>">WFH Count : <?=$mep_totalWFHTM+$kanth222;?> </b></span>
                    </div>
                </div>
            </div>
        </div>

        <!--<div class="row" style="margin-top: 10px;">	      
            <div class="col-sm-12" style="margin-bottom: 10px; font-size: 13.5px;">
                <div class="row">
                    <div class="col-sm-12">
                        <span><b class="production" title="Production Count" : <?=$totalProductionTM;?>>Production Count : <?=$totalProductionTM;?> </b></span>
						<span><b class="training" title="Training Count" : <?=$totalTrainingTeamMembers;?>>Training Count  : <?=$totalTrainingTeamMembers + $learning_development;?> </b></span>
						<span><b class="njtraining" title="Intern Training Count : <?=$totalNewJoineeTrainingTM;?>">Intern Training Count : <?=$totalNewJoineeTrainingTM;?> </b></span>
						<span><b class="available" title="Available Count" : <?=$totalAvailableTeamMembers;?>>Available Count : <?=$totalAvailableTeamMembers .'==='.$totalAvailableTeamMembers;?> </b></span>
						<span><b class="leave" title="Planned Leave Count : <?=$totalLeaveTM;?>">Planned Leave Count : <?=$totalLeaveTM;?> </b></span>
						<span><b class="upleave" title="Unplanned leave count : <?=$totalUnPLeaveTM;?>">Unplanned leave count : <?=$totalUnPLeaveTM;?> </b></span>
						<span><b class="wfh" title="WFH Count : <?=$totalNATM;?>">WFH Count : <?=$totalWFHTM;?> </b></span>
						<span><b class="na" title="Not Assigned count : <?=$totalNATM;?>">Not Assigned count : <?=$totalNATM;?> </b></span>
                    </div>
                </div>
            </div> 
        </div>-->

    </div>

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
										<div class="col-md-2">
											<div class="form-group">
												<label class="control-label">Department</label>
												<select class="form-control" id="department" name="department">
												<option value="all" <?= empty($selectedDepartments) || in_array('all', $selectedDepartments) ? 'selected="selected"' : ''; ?>>ALL</option>
													<option value="Architectural" <?= in_array('Architectural', $selectedDepartments) ? 'selected="selected"' : ''; ?>>Architectural</option>
													<option value="Structural" <?= in_array('Structural', $selectedDepartments) ? 'selected="selected"' : ''; ?>>Structural</option>
													<option value="2D Auto CAD" <?= in_array('2D Auto CAD', $selectedDepartments) ? 'selected="selected"' : ''; ?>>2D Auto CAD</option>
													<option value="MEP-Mechanical" <?= in_array('MEP-Mechanical', $selectedDepartments) ? 'selected="selected"' : ''; ?>>MEP-Mechanical</option>
													<option value="MEP-Electrical" <?= in_array('MEP-Electrical', $selectedDepartments) ? 'selected="selected"' : ''; ?>>MEP - Electrical</option>
													<option value="MEP-Plumbing" <?= in_array('MEP-Plumbing', $selectedDepartments) ? 'selected="selected"' : ''; ?>>MEP-Plumbing</option>
													<option value="3D Visualization" <?= in_array('3D Visualization', $selectedDepartments) ? 'selected="selected"' : ''; ?>>3D Visualization</option>
													<!-- <option value="Middle East" <?=$departM == 'Middle East' ? 'selected="selected"' : '';?>>Middle East</option> -->
												</select>
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
                								<?php foreach($this->timesheet_login->getReportingManagers() as $managerResult): ?>
												<option value="<?php echo $managerResult->empId;?>" <?=$pManagerN == $managerResult->empId ? 'selected="selected"' : '';?>><?php echo $managerResult->name; ?></option>
												<?php endforeach; ?>
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
												<input class="form-control" type="text" id="form_date" name="form_date" placeholder="Select From Date" value="<?=$fromDate;?>" readonly="" <?=$selectBGC?>>
											</div>
										</div>
										<div class="col-md-1">
											<div class="form-group">
												<label class="control-label">To Date</label>
												<input class="form-control" type="text" id="to_date" name="to_date" placeholder="Select To Date" value="<?=$toDate;?>" readonly="" <?=$selectBGC?>>
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
		<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<!-- <center><h4>Total Hours : <span id="countData" style="color: #1322d2; font-size:20px;"></span></h4> </center> -->
						<table class="table table-hover table-bordered" id="table22excel_all">
							<thead>
								<tr>
									<!--<th>Sno</th>-->
									<th>Date</th>
									<th>Department</th>
									<th>Manager</th>
									<th>Team Member</th>
									<th>Client</th>
									<th>Project</th>
									<th>Task</th>
									<th>Hours</th>
									<th>Workplace</th>
									<th>Comments</th>
									<?php if(empty($_REQUEST['form_date'])): ?>
									<th>Action</th>
									<?php endif; ?>
								</tr>
							</thead>
							<tbody>
								<?php $previousManagerName = ''; $rowSpan = 1; $managerCount = array(); 
								foreach($getRecords as $key => $resourceResult): 
								$mangerName = $resourceResult->reporting_manger; 
								if(!isset($managerCount[$mangerName])): 
								 	$managerCount[$mangerName] = 1; 
								 else: 
								 	$managerCount[$mangerName]++; 
								 endif; 
								?>
								<?php endforeach; ?>
								<?php $previousManagerName = ''; $rowSpan = 1; 
								foreach($getRecords as $key => $resourceResult): 

								$mangerName = $resourceResult->reporting_manger; 

								$getTeamwiseMangerName = $this->resourcelog_model->getManagerName($mangerName);

								if($resourceResult->task_name == 'Leave'):
								
									$bgrowC = 'style="background:#7030a0; color:white; font-weight: bold"';
								
								elseif($resourceResult->task_name == 'Training' || $resourceResult->task_name == 'Learning & Development'):
								
									$bgrowC = 'style="background:#C2FFC7; color:#000; font-weight: bold"';
								
								elseif($resourceResult->task_name == 'Unplanned Leave'):
								
									$bgrowC = 'style="background:#FFE31A; color:#000; font-weight: bold"';
								
								elseif($resourceResult->task_name == 'Available'):
								
									$bgrowC = 'style="background:#059212; color:#FFF; font-weight: bold"';
								
								elseif($resourceResult->task_name == 'New Joinee Training'):
								
									$bgrowC = 'style="background:#ffa500; color:#FFF; font-weight: bold"';

								elseif($resourceResult->workplace == 'WFH'):
								
										$bgrowC = 'style="background:#008b8b; color:#FFF; font-weight: bold"';	
								
								elseif($resourceResult->task_name == 'Training Faciliatory'):
								
											$bgrowC = 'style="background:#FFB6C1; color:#000; font-weight: bold"';		
								
								elseif($resourceResult->task_name == ''):
								
									$bgrowC = 'style="background:#FF0000; color:#FFF; font-weight: bold"';
								
								
								else:
								
									$bgrowC = '';
								
								endif;		

								if($mangerName != $previousManagerName): 
								 	$rowSpan = $managerCount[$mangerName]; 
								 
									
								 ?>

								<?php if($mangerName != $previousManagerName): 
								if($key !=0):
								?>
								<tr>
									<td colspan="11" style="background:#686868; height:24px;"></td>
								</tr>
								<?php endif; endif; ?>

								<tr <?=$bgrowC;?>>
									<!--<td><?=$key+1;?></td>-->
									<td nowrap><?=$resourceResult->emp_report_dates;?></td>
									<td><?=$resourceResult->department;?></td>
									<td rowspan="<?=$rowSpan;?>" style="background:#D1E9F6; color:#000;"><b><?=ucfirst($getTeamwiseMangerName);?></b></td>
									<td><?=$resourceResult->name;?></td>
									<td><?=$resourceResult->client_name;?></td>
									<td><?=$resourceResult->project_name;?></td>
									<td><?=$resourceResult->task_name;?></td>
									<td><?=$resourceResult->emp_time_hours;?></td>
									<td><?=$resourceResult->workplace;?></td>
									<td><?=$resourceResult->comments;?></td>
									<?php if(empty($_REQUEST['form_date'])): ?>
									<td>
										<?php if (!empty($resourceResult->resource_id)): ?>
										<a href="<?php echo base_url(); ?>resource_schedule/add/<?php echo $resourceResult->resource_id; ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"> </i></a>
										<?php else: ?>
										<a href="<?php echo base_url(); ?>resource_schedule/add" data-toggle="tooltip" title="Add"><i class="fa fa-lg fa-plus"> </i></a>
										<?php endif; ?>
									</td>
									<?php endif; ?>
								</tr>

								<?php else: ?>
								<tr <?=$bgrowC;?>>
									<!--<td><?=$key+1;?></td>-->
									<td nowrap><?=$resourceResult->emp_report_dates;?></td>
									<td><?=$resourceResult->department;?></td>
									<td><?=$resourceResult->name;?></td>
									<td><?=$resourceResult->client_name;?></td>
									<td><?=$resourceResult->project_name;?></td>
									<td><?=$resourceResult->task_name;?></td>
									<td><?=$resourceResult->emp_time_hours;?></td>
									<td><?=$resourceResult->workplace;?></td>
									<td><?=$resourceResult->comments;?></td>
									<?php if(empty($_REQUEST['form_date'])): ?>
									<td>
										<?php if (!empty($resourceResult->resource_id)): ?>
										<a href="<?php echo base_url(); ?>resource_schedule/add/<?php echo $resourceResult->resource_id; ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"> </i></a>
										<?php else: ?>
										<a href="<?php echo base_url(); ?>resource_schedule/add" data-toggle="tooltip" title="Add"><i class="fa fa-lg fa-plus"> </i></a>
										<?php endif; ?>
									</td>

									<?php endif; ?>
								</tr>
								<?php endif; ?>
								<?php $previousManagerName = $mangerName; ?>
								<?php endforeach; 								
								?>
							</tbody>
						</table>
					</div>
				</div>
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
		$("#form_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			numberOfMonths: 1,
		});
		$("#to_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
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

	jQuery('#client_Id,#department,#project_manger,#team_member').select2(); // Autosuggest list
</script>
<script src="<?php echo HTTP_JS_PATH; ?>jquery.table2excel.js"></script>
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