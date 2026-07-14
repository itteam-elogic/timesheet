<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<!-- Inlude Header here END-->

<div class="content-wrapper">
	<div class="page-title">
	<div>
		<h1>List of All Quality Error Logs</h1>
	</div>
	<div>
	<a class="btn btn-primary btn-flat" href="<?php echo base_url('quality_error_log/add_quality_error_log_details'); ?>" data-toggle="tooltip" title="Add Error Log"><i class="fa fa-lg fa-plus"></i></a><a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url('quality_error_log'); ?>"><i class="fa fa-lg fa-refresh"></i></a>
	<a class="btn btn-warning btn-flat" data-toggle="tooltip" title="Generate Quality Error Log Report" href="<?php echo base_url('quality_error_log/quality_report_automation_search'); ?>"><i class="fa fa-lg fa-search"></i>Generate Quality Error Log Report</a>
	</div>
</div>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-hover table-bordered" id="qualityErrorLogTable">
							<thead>
								<tr>
									<th>Sno</th>
									<th>C.Name</th>
									<th>P.Name</th>
                                    <th>P.Manager</th>
                                    <th>Department</th>
									<th>Date</th>                                    
									<th>Self Checker</th>
									<th>Analyzer</th>									
									<th>N.Errors</th>
									<th>Link</th>
									<th>Reviewer</th>									
									<th>R.N.Errors</th>
									<th>R.Link</th>
									<th>Ensure</th>
									<th>C.Date</th>									
									<th>Action</th>
								</tr>
							</thead>
							<tbody><?php if (!empty($getQuality)) : ?>
								<?php 
				  $i = 0;
				  $pageOffset = isset($pageOffset) ? $pageOffset : 0;
				  $employeeNameMap = isset($employeeNameMap) ? $employeeNameMap : array();
				  foreach ($getQuality as $key => $qtyErrorResult) :
								
$employeeNames = array();
					if (!empty($qtyErrorResult->self_checker_name)) {
						$ids = explode(',', $qtyErrorResult->self_checker_name);
						foreach ($ids as $idValue) {
							$idValue = trim($idValue);
							if ($idValue !== '' && isset($employeeNameMap[$idValue])) {
								$employeeNames[] = ucwords($employeeNameMap[$idValue]);
							}
						}
					}
					$selfCheckerDisplay = !empty($employeeNames) ? implode(', ', $employeeNames) : '';
					$analyzerName = isset($employeeNameMap[$qtyErrorResult->analyzer_name]) ? ucwords($employeeNameMap[$qtyErrorResult->analyzer_name]) : '';
					$reviewerName = isset($employeeNameMap[$qtyErrorResult->reviewer_name]) ? ucwords($employeeNameMap[$qtyErrorResult->reviewer_name]) : '';
								
				 	 if($i%2 == 0): $showRowColour = 'class="success"'; else: $showRowColour = 'class="info"'; endif;				  
				  	 $createdExp 		= explode(" " , $qtyErrorResult->created_at);	
				 ?>
								<tr <?php echo $showRowColour; ?> id="delClientRow<?php echo $qtyErrorResult->qty_error_id; ?>" style="font-size:14px;">
									<td><?php echo $pageOffset + $i + 1; ?></td>
									<td><?php echo ucwords($qtyErrorResult->client_name);?></td>
									<td><?php echo ucwords($qtyErrorResult->project_name);?></td>
                                    <td><?php echo ucwords($qtyErrorResult->project_created_name);?></td>
                                    <td><?php echo ucwords($qtyErrorResult->project_type);?></td>
									<td><span class="me-1 badge bg-info"><?php echo date("d-M-Y",strtotime($qtyErrorResult->analyzer_report_date));?></span></td>
									<td><span class="text-primary"><?php echo $selfCheckerDisplay;?></span></td>
									<td><span class="label label-info"><?php echo $analyzerName;?></span></td>
									<td><?php echo $qtyErrorResult->analyzer_num_of_errors;?></td>
									<td><?php echo $qtyErrorResult->analyzer_link;?></td>
									<td><span class="label label-success"><?php echo $reviewerName;?></span></td>
									<td><?php echo $qtyErrorResult->reviewer_num_of_errors;?></td>
									<td><?php echo $qtyErrorResult->reviewer_link;?></td>
									<td><span id="changeStatusRow_<?php echo $qtyErrorResult->qty_error_id; ?>"><a class="<?php echo ($qtyErrorResult->status=='Yes')? 'fa fa-check-circle label label-success' : 'fa fa-close label label-danger'?>" style="cursor:pointer;" title="Click to toggle status" onClick="update_emp_status(<?php echo $qtyErrorResult->qty_error_id;?>,'<?php echo $qtyErrorResult->status;; ?>')"> <?php echo ($qtyErrorResult->status=='Yes')? 'Yes' : 'No';?></a></span></td>
									<th><?php echo date('d-M-Y',strtotime($createdExp[0]));?></th>
									<th><a href="<?php echo base_url(); ?>quality_error_log/qualitylogview/<?php echo $qtyErrorResult->qty_error_id; ?>" data-toggle="tooltip" title="View"><i class="fa fa-vimeo"></i></a> | 
									<a href="<?php echo base_url(); ?>quality_error_log/reviewer/<?php echo $qtyErrorResult->qty_error_id; ?>" data-toggle="tooltip" title="Update Reviewer Information"><i class="fa fa-edit"></i></a>											
									</tr>
								<?php $i++; endforeach; ?><?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Inlude Footer here -->
<script type="text/javascript">
	function delete_client(client_Id) {
		var answer = confirm("Are you sure you want to delete client?");
		if (answer) {
			$.ajax({
				type: "POST",
				url: "<?php echo base_url('clients/delete');?>",
				data: "client_Id=" + client_Id,
				beforeSend: function() {
					$('#delClientRow' + client_Id).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
				},
				success: function(response) {
					$("#delClientRow" + client_Id).fadeOut(300, function() {
						$(this).remove();
					});
				}
			});
		}
	}


var status;
function update_emp_status(qty_error_id,status){ 
	
	
var updateStatus = (status=='No')? 'Yes' : 'No';
var answer = confirm ("Are you sure you want to Ensure status "+updateStatus);

//alert(status); return false;
//alert(updateStatus); 
if (answer) {
        $.ajax({
                type: "POST",
                url: "<?php echo base_url('quality_error_log/analyzerReviewerStatus');?>",
                data: "qty_error_id="+qty_error_id+'&status='+updateStatus,
				beforeSend: function() {
   							$('#changeStatusRow_'+qty_error_id).html('<i class="fa fa-spinner"></i>');
 				 },success: function (response) { 
				            $("#changeStatusRow_"+qty_error_id).html(response);
							//location.reload();
			     }
            });
      }
}

</script>
<?php $this->load->view('includes/cRMFooter'); ?>

<!-- Inlude Footer here END-->