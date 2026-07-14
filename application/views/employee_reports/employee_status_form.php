<?php foreach($viewEmpStatisDetails as $reportResult){  
	
				$getListOfProjects   	= $this->emptimelog_model->getAddedReportTaskNames($reportResult->task_Id); // List of tasks 
	
	}
?>

	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title"><i class="fa fa-user"></i>
				<?php echo ucfirst($reportResult->project_name);?> -
				<?php echo $getListOfProjects;?>
			</h4>
		</div>
		<div class="modal-body">
			<form class="comment_reject form-horizontal" name="comment_status_ok" id="comment_reject_<?php echo $reportResult->emp_record_id;?>" method="post" action="#">
				<input type="hidden" name="comment_emp_record_id" id="comment_emp_record_id" value="<?php echo $reportResult->emp_record_id;?>">

				<div class="form-group">
					<label class="control-label col-md-3">Status : </label>
					<div class="col-md-9">
						<div class="radio-inline"><label><input required class="label-text" type="radio" name="status" id="status_<?php echo $reportResult->emp_record_id;?>" value="Approved" <?php echo ($reportResult->status=='Approved')?'checked':'' ?>>Approved</label></div>
						<div class="radio-inline"><label><input required  class="label-text" type="radio" name="status" id="status_<?php echo $reportResult->emp_record_id;?>" value="Rejected" <?php echo ($reportResult->status=='Rejected')?'checked':'' ?>>Rejected</label></div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3">Comment :</label>
					<div class="col-md-8"><textarea required class="form-control" name="comment_status" id="comment_status_<?php echo $reportResult->emp_record_id;?>" rows="4" placeholder="Enter your comment"><?php if($reportResult->comment_status){ echo $reportResult->comment_status;}?></textarea></div>
				</div>
				<div class="row">
					<div class="col-md-8 col-md-offset-3"><button class="btn btn-primary icon-btn" type="submit" name="status_ok"> OK </button>&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button></div>
				</div>
			</form>
		</div>
	</div>
<script>
 $("#comment_reject_"+<?php echo $reportResult->emp_record_id;?>).submit(function(e) {

		var url = '<?php echo base_url('empreports/update_emp_pm_report_status');?>'; // the script where you handle the form input.

		$.ajax({
			   type: "POST",
			   url: url,
			   data: $("#comment_reject_"+<?php echo $reportResult->emp_record_id;?>).serialize(), // serializes the form's elements.
			 beforeSend: function() {
					$('#changeStatusRow_'+<?php echo $reportResult->emp_record_id;?>).html('<i class="fa fa-spinner"></i>');
			},  
			 success: function(response)
			   {
				  $("#changeStatusRow_"+<?php echo $reportResult->emp_record_id;?>).html(response);
				  $('#comment_status_model_'+<?php echo $reportResult->emp_record_id;?>).modal('hide');
				  $('#status-pop-modal').modal('hide');

			   }
			 });

		e.preventDefault(); // avoid to execute the actual submit of the form.
	});

  $('input[id="status_<?php echo $reportResult->emp_record_id;?>"]').on('click', function(){  // Dynamically Add Approved text in comment box.
		if ($(this).val()=='Approved') {

			//change to "show update"
			 $("#comment_status_"+<?php echo $reportResult->emp_record_id;?>).text("Approved");

		} else  {

			$("#comment_status_"+<?php echo $reportResult->emp_record_id;?>).text("");
		}
	});
</script>
