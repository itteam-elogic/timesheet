<?php foreach($viewEmpTaskDetails as $reportResult){  
	
				$getListOfProjects   	= $this->emptimelog_model->getAddedReportTaskNames($reportResult->task_Id); // List of tasks 
	
	}
?>

<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title"><i class="fa fa-user"></i> View details of "
			<?php echo ucfirst($reportResult->project_name);?>"</h4>
	</div>
	<div class="modal-body">
		<p><strong class="popw">Name </strong> :&nbsp;&nbsp;
			<?php echo ucfirst($reportResult->name);?>
		</p>
		<p><strong class="popw">Client Name </strong> :&nbsp;&nbsp;
			<?php echo ucfirst($reportResult->client_name);?>
		</p>
		<p><strong class="popw">Project Name </strong> :&nbsp;&nbsp;
			<?php echo ucfirst($reportResult->project_name);?>
		</p>
		<p><strong class="popw">Task Name </strong> :&nbsp;&nbsp;
			<?php echo $getListOfProjects; ?>
		</p>
		<p><strong class="popw">Hours </strong> :&nbsp;&nbsp;
			<?php echo $reportResult->emp_time_hours; ?>
		</p>
		<p><strong class="popw">Comments </strong> :&nbsp;&nbsp;
			<?php echo ucfirst($reportResult->comments);?> </p>
		<p><strong class="popw">Status </strong> :&nbsp;&nbsp;
			<?php echo $reportResult->status; ?>
		</p>
		<p><strong class="popw">Status Comment </strong> :&nbsp;&nbsp;
			<?php echo $reportResult->comment_status; ?>
		</p>
		<p><strong class="popw">Date </strong> :&nbsp;&nbsp;
			<?php echo date('d-M-Y',strtotime($reportResult->emp_report_dates));?>
		</p>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	</div>
</div>