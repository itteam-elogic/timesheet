<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<!-- Inlude Header here END-->

<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1>Manager's Report Log</h1>
    </div>
    <div>
	  <button type="button" class="btn btn-danger btn-flat " id="button_kanth"><i class="fa fa-fw fa-lg fa-check-circle"></i>Click to Approve All Report Logs</button>
        
		<a class="btn btn-primary btn-flat" href="<?php echo base_url();?>empreports/add" data-toggle="tooltip" title="Add"><i class="fa fa-lg fa-plus"></i></a>
	  
	<a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url();?>empreports/pmreportlogs"><i class="fa fa-lg fa-refresh"></i></a></div>
  </div>
  <div class="row" id="refresh_div_data">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="appovedDatatable">
              <thead>
                <tr>
                  <th><input type="checkbox" class="select-all-approve" id="all"/></th>    
                  <th>Sno</th>
                  <th>Name</th>
				  <th>Project Name</th>
				  <th>Task Name</th>
				  <th>Hours</th>
				  <th>Status</th>
                  <th>Date</th>                 
				  <th>Action</th>				 
                </tr>
              </thead>
              <tbody>
                <?php 
				  $i=1;
				   $totalHours = 0;
				  foreach ($getRecords as $key => $reportResult) :
				  
				  if($this->session->userdata['logged_in_timesheet']['username'] == 'rupali'):
				  
				  $conditionType = 'developer' || 'manager';
				  
				  else:
				  
				  $conditionType = 'manager';
				  
				  endif;
				  
                  if($reportResult->user_type == $conditionType && $reportResult->status != 'Approved'):
                  $totalHours += $reportResult->emp_time_hours; // Total Hours 
				 	 if($i%2 == 0): $showRowColour = 'class="success"'; else: $showRowColour = 'class="info"'; endif;
					$getListOfProjects   	= $this->emptimelog_model->getAddedReportTaskNames($reportResult->task_Id); // List of tasks			
                  ?>
                <tr <?php echo $showRowColour; ?> id="delRecordsRow<?php echo $reportResult->emp_record_id; ?>">
                <td class="center"><input type="checkbox" name="approveChk" value="<?php echo $reportResult->emp_record_id; ?>  "></td>    
                  <td><?php echo $i ?></td>
                  <td><span class="label label-info"><?php echo ucfirst($reportResult->name);?></span> </td>
				  <td nowrap="nowrap"><?php echo ucfirst($reportResult->project_name);?> </td>
				  <td nowrap="nowrap"><a href="#"  data-toggle="tooltip" title="<?php echo $getListOfProjects;?>"><?php echo character_limiter($getListOfProjects,20);?></a></td>
				  <td nowrap="nowrap"><?php echo ucfirst($reportResult->emp_time_hours);?> </td>
				  <td nowrap="nowrap">
				  <?php if($this->session->userdata['logged_in_timesheet']['username'] == 'jaishree' || $this->session->userdata['logged_in_timesheet']['username'] == 'pradip' || $this->session->userdata['logged_in_timesheet']['username'] == 'rahulkumar' || $this->session->userdata['logged_in_timesheet']['username'] == 'farhan' || $this->session->userdata['logged_in_timesheet']['username'] == 'rupali'): ?> 
				  <span id="changeStatusRow_<?php echo $reportResult->emp_record_id; ?>"><a class="<?php echo ($reportResult->status=='Approved')? 'fa fa-check-circle label label-success' : (($reportResult->status=='Rejected')? 'fa fa-registered label label-warning' : 'fa fa-ban label label-danger');?>" style="cursor:pointer;" data-toggle="modal" title="Click To <?php echo ($reportResult->status=='Approved')? 'Unapproved' : 'Approved'?>" data-target="#comment_status_model_<?php echo $reportResult->emp_record_id;?>"> <?php echo $reportResult->status;?></a></span>
				 <?php else: ?>
				 <span class="<?php echo ($reportResult->status=='Approved')? 'fa fa-check-circle label label-success' : (($reportResult->status=='Rejected')? 'fa fa-registered label label-warning' : 'fa fa-ban label label-danger');?>"> <?php echo $reportResult->status;?></span>
				 <?php endif; ?> 
				  </td>
                  <th nowrap="nowrap"><?php echo date('d-M-Y',strtotime($reportResult->emp_report_dates));?></th>
                 <?php if($reportResult->empId == $this->session->userdata['logged_in_timesheet']['empId'] && $reportResult->status !='Approved'): ?>
				  <th nowrap="nowrap"><a href="#" data-toggle="tooltip" title="View"><i class="fa fa-history" aria-hidden="true"></i></a> | <a href="<?php echo base_url(); ?>empreports/add/<?php echo $reportResult->emp_record_id; ?>/<?php echo $reportResult->project_Id; ?>/<?php echo $reportResult->client_Id; ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a> | <a style="cursor:pointer;" data-toggle="tooltip" title="Delete" onClick="delete_emp_record(<?php echo $reportResult->emp_record_id;?>)"><i class="fa fa-sm fa-trash"></i></a></th>
				  <?php else: ?>
				  <th nowrap="nowrap" style="text-align:center"><a data-toggle="modal" data-target="#pm_model_<?php echo $reportResult->emp_record_id;?>" href="#" data-toggle="tooltip" title="View" data-backdrop="static" data-keyboard="false"><i class="fa fa-history" aria-hidden="true"></i></a></th>
				  <?php endif; ?>
				 </tr>
                  
				    <!-- Employee Status Approved And Unapproved Comment Section Block -->
				   <div id="comment_status_model_<?php echo $reportResult->emp_record_id;?>" class="modal fade" role="dialog">
                      <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><i class="fa fa-user"></i> <?php echo ucfirst($reportResult->project_name);?> - <?php echo $getListOfProjects;?></h4>
                          </div>
                          <div class="modal-body">
                             <form class="comment_reject form-horizontal" name="comment_status_ok" id="comment_reject_<?php echo $reportResult->emp_record_id;?>" method="post" action="#">
								<input type="hidden" name="comment_emp_record_id" id="comment_emp_record_id" value="<?php echo $reportResult->emp_record_id;?>"> 
								
								 <div class="form-group" >
									<label class="control-label col-md-3">Status : </label>
									<div class="col-md-9">
										<div class="radio-inline"><label><input required class="label-text" type="radio" name="status" id="status_<?php echo $reportResult->emp_record_id;?>" value="Approved" <?php echo ($reportResult->status=='Approved')?'checked':'' ?>>Approved</label></div>
										<div class="radio-inline"><label><input required  class="label-text" type="radio" name="status" id="status_<?php echo $reportResult->emp_record_id;?>" value="Rejected" <?php echo ($reportResult->status=='Rejected')?'checked':'' ?>>Rejected</label></div>
									</div>
								 </div> 
								<div class="form-group" style="margin-bottom: 25%;margin-top: 8%;">
									<label class="control-label col-md-3">Comment :</label>
										<div class="col-md-8"><textarea  required class="form-control" name="comment_status" id="comment_status_<?php echo $reportResult->emp_record_id;?>" rows="4" placeholder="Enter your comment"><?php if($reportResult->comment_status){ echo $reportResult->comment_status;}?></textarea></div>
								</div>
								 <div class="form-group">&nbsp;</div> 
									<div class="row">
										<div class="col-md-8 col-md-offset-3"><button  class="btn btn-primary icon-btn" type="submit" name="status_ok"> OK </button>&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button></div>
								   </div>
							 </form>                           
                          </div>                         
                        </div>
                      </div>
				    </div>
				 
				  <script>
				  
				  /***************** Reject and Approved form submission ************************/
	
					 $("#comment_reject_"+<?php echo $reportResult->emp_record_id;?>).submit(function(e) {

							var url = '<?php echo base_url('empreports/update_emp_report_status');?>'; // the script where you handle the form input.

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
	
                  /***************** Reject and Approved form submission ************************/	
				  
				  </script>
				    
				  <!-- Employee Status Approved And Unapproved Comment Section Block -->
				  
                  <!-- Model Popup of task details Start-->
                    <div id="pm_model_<?php echo $reportResult->emp_record_id;?>" class="modal fade" role="dialog">
                      <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><i class="fa fa-user"></i> View details of "<?php echo ucfirst($reportResult->project_name);?>"</h4>
                          </div>
                          <div class="modal-body">
                              <p><strong class="popw">Name </strong> :&nbsp;&nbsp;<?php echo ucfirst($reportResult->name);?></p>
                              <p><strong class="popw">Client Name </strong> :&nbsp;&nbsp;<?php echo ucfirst($reportResult->client_name);?></p>
                              <p><strong class="popw">Project Name </strong> :&nbsp;&nbsp;<?php echo ucfirst($reportResult->project_name);?></p>
                              <p><strong class="popw">Task Name </strong> :&nbsp;&nbsp;<?php echo $getListOfProjects; ?></p>
                              <p><strong class="popw">Hours </strong> :&nbsp;&nbsp;<?php echo $reportResult->emp_time_hours; ?></p>
                              <p><strong class="popw">Comments </strong> :&nbsp;&nbsp;<?php echo ucfirst($reportResult->comments);?> </p>
                              <p><strong class="popw">Status </strong> :&nbsp;&nbsp;<?php echo $reportResult->status; ?></p>
							  <p><strong class="popw">Status Comment </strong> :&nbsp;&nbsp;<?php echo $reportResult->comment_status; ?></p>
                              <p><strong class="popw">Date </strong> :&nbsp;&nbsp;<?php echo date('d-M-Y',strtotime($reportResult->emp_report_dates));?></p>                              
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                          </div>
                        </div>

                      </div>
                    </div>
                  <!-- Model Popup of task details END-->
                <?php $i++; endif; endforeach; ?>
				 <div align="center"><?php  echo 'Total Hours : <b style="color: #1322d2; font-size:20px;">'.$totalHours.'</b>'; ?></div>
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
    
  $(document).ready(function() {
        
       // oTable = $('#appovedDatatable').dataTable();
        
        var approvedTable = $('#appovedDatatable').DataTable({
            "lengthMenu": [[100, 200, 500, -1], [100, 200, 500, "All"]]
        });

        $('#appovedDatatable').on('change', '.select-all-approve', function() {
            var checked = $(this).prop('checked');
            $('#appovedDatatable tbody input[name="approveChk"]:not(:disabled)').prop('checked', checked);
        });

        approvedTable.on('draw', function() {
            var allBoxes = $('#appovedDatatable tbody input[name="approveChk"]:not(:disabled)');
            var checkedBoxes = allBoxes.filter(':checked');
            var selectAll = $('#appovedDatatable .select-all-approve').first();
            selectAll.prop('checked', allBoxes.length > 0 && allBoxes.length === checkedBoxes.length);
            selectAll.prop('indeterminate', checkedBoxes.length > 0 && checkedBoxes.length < allBoxes.length);
        });

        $("#button_kanth").click(function() {
            var approveList = [];
            $('#appovedDatatable tbody input[name="approveChk"]:checked:not(:disabled)').each(function() {
                approveList.push($(this).val());
            });

            if (approveList.length === 0) {
                alert('Please select at least one report log to approve.');
                return;
            }

            if (confirm("Are you sure you want to approve the selected report logs?")) {
                var approvedIds = approveList.join(", ");
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('empreports/allEmpApproveList');?>",
                    data: { approvedIds: approvedIds },
                    beforeSend: function() {
                        $('#refresh_div_data').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div>');
                    },
                    success: function() {
                        location.reload(true);
                    },
                    error: function() {
                        alert('Unable to approve selected report logs. Please try again.');
                        location.reload(true);
                    }
                });
            }
        });
    });    
    
function delete_emp_record(emp_record_id){ 
var answer = confirm ("Are you sure you want to delete record?");
if (answer) {
        $.ajax({
                type: "POST",
                url: "<?php echo base_url('empreports/delete');?>",
                data: "emp_record_id="+emp_record_id,
				beforeSend: function() {
   							 $('#delRecordsRow'+emp_record_id).html('<i class="fa fa-spinner"></i>');
 				 },success: function (response) { 	
					      
				       $("#delRecordsRow"+emp_record_id).remove("#delRecordsRow"+emp_record_id).html('');
			     }
            });
      }
}
</script>
<script type="text/javascript">
var status;
function update_emp_report_status(emp_record_id,status){   
var updateStatus = (status=='Approved')? 'Unapproved' : 'Approved';
var answer = confirm ("Are you sure you want to "+updateStatus+" report log ");
//alert(updateStatus); 
if (answer) {
        $.ajax({
                type: "POST",
                url: "<?php echo base_url('empreports/update_emp_report_status');?>",
                data: "emp_record_id="+emp_record_id+'&status='+updateStatus,
				beforeSend: function() {
   							$('#changeStatusRow_'+emp_record_id).html('<i class="fa fa-spinner"></i>');
 				 },success: function (response) {  //alert('---' + response)
				            $("#changeStatusRow_"+emp_record_id).html(response);
							//location.reload();
			     }
            });
      }
}
</script>
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
