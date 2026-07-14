<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<!-- Inlude Header here END-->

<div class="content-wrapper">
	<div class="page-title">
		<div>
			<h1>Report Log</h1>
		</div>
		<div>
			
			<a class="btn btn-primary btn-flat" href="<?php echo base_url();?>empreports/add" data-toggle="tooltip" title="Add"><i class="fa fa-lg fa-plus"></i> Add Report Log</a>
            
			<!-- <a class="btn btn-danger btn-flat" href="<?php echo base_url();?>empreports/unapproved" data-toggle="tooltip" title="List of Unapproved Information"><i class="fa fa-fw fa-lg fa-check-circle"></i>List of Unapproved Report Logs</a> -->
            
			<a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url();?>empreports"><i class="fa fa-lg fa-refresh"></i></a></div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<!-- <center><h4>Total Hours : <span id="countData" style="color: #1322d2; font-size:20px;"></span></h4> </center> -->
					<table class="table table-hover table-bordered" id="datatable1">
							<thead>
								<tr>
									<th>Sno</th>
									<th>Name</th>
                                     <th>C.Name</th>
                                    <th>P.Name</th>
									<th>Task Name</th>
									<th>Hours</th>
									<th>Status</th>
									<th>Date</th>
									<th>Action</th>
								</tr>
							</thead>
							 <tfoot>
								<tr>
									<th colspan="4" style="text-align:right">Total:</th>
									<th colspan="4"></th>
								</tr>
							</tfoot>
							<tbody>
								<!-- VIEW DETAILS OF EMPLOYEE TASK REPORT INFORMATION POP DISPALYING ANOTHER PAGE POPUP USING AJAX -->
									<div id="view-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
										<div class="modal-dialog">
											<div id="modal-loader" style="display: none; text-align: center;"></div>
											 <div id="dynamic-content"></div>
										</div>
									</div>
								
								<!-- VIEW DETAILS OF EMPLOYEE TASK REPORT INFORMATION POP DISPALYING ANOTHER PAGE POPUP USING AJAX -->
								
								<!--EMPLOYEE STATUS UPDATE POPUP -->
									<div id="status-pop-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
										<div class="modal-dialog">
											<div id="status-modal-loader" style="display: none; text-align: center;"></div>
											 <div id="status-dynamic-content"></div>
										</div>
									</div>
								
								<!-- VIEW DETAILS OF EMPLOYEE TASK REPORT INFORMATION POP DISPALYING ANOTHER PAGE POPUP USING AJAX -->
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
	function delete_emp_record(emp_record_id,refreshID) {
		var answer = confirm("Are you sure you want to delete record?");

		if (answer) {
			$.ajax({	
				type: "POST",
				url: "<?php echo base_url('empreports/delete');?>",
				data: "emp_record_id=" + emp_record_id,
				beforeSend: function() {
					$('#delRecordsRow' + refreshID).html('<i class="fa fa-spinner"></i>');
				},
				success: function(response) {

					$("#delRecordsRow" + refreshID).remove("#delRecordsRow" + refreshID).html('');
				}
			});
		}
	}
</script>
<script type="text/javascript">

	$(document).on('click', '#empViewData', function(e) {

		e.preventDefault();

		var empViewId = $(this).data('id'); // it will get id of clicked row

		$('#dynamic-content').html(''); // leave it blank before ajax call
		$('#modal-loader').show('<i class="fa fa-spinner"></i>'); // load ajax loader

		$.ajax({
				url: '<?php echo base_url('empreports/empViewDetails');?>',
				type: 'POST',
				data: 'empViewId=' + empViewId,

			})
			.done(function(data) {
				//console.log(data);
				$('#dynamic-content').html('');
				$('#dynamic-content').html(data); // load response 
				$('#modal-loader').hide(); // hide ajax loader	
			})
			.fail(function() {
				$('#dynamic-content').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
				$('#modal-loader').hide();
			});

	});
	
	$(document).on('click', '#empUpdateStatus', function(e) {

		e.preventDefault();

		var empstatusUpdateId = $(this).data('id'); // it will get id of clicked row

		$('#status-dynamic-content').html(''); // leave it blank before ajax call
		$('#status-modal-loader').show('<i class="fa fa-spinner"></i>'); // load ajax loader

		$.ajax({
				url: '<?php echo base_url('empreports/empStatusPopup');?>',
				type: 'POST',
				data: 'empstatusUpdateId=' + empstatusUpdateId,

			})
			.done(function(data) {
				//console.log(data);
				$('#status-dynamic-content').html('');
				$('#status-dynamic-content').html(data); // load response 
				$('#status-modal-loader').hide(); // hide ajax loader	
			})
			.fail(function() {
				$('#status-dynamic-content').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
				$('#status-modal-loader').hide();
			});

	});
</script>


<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->