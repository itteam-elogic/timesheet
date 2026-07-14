<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<?php 

  $getClientNames      		= $this->client_model->getClientName(); // List of Clients
  
   $taskClientId   			= ''; // Getting client ID
  
   $getListOfProjects   		= $this->project_model->getProjectName($taskClientId); // List of Clients
  
 // $getListOfEmployees   	= $this->timesheet_login->getEmployeeName(); // List of Clients
  
   $taskProjectId = '';
  
   $getListOfTask		   	= $this->task_model->getTaskName($taskProjectId); // List of Clients
 
 		
?>

<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1><i class="fa fa-clock-o"></i> Search Employee Reports </h1>
    </div>
    <div> <a class="btn btn-primary btn-flat" href="<?php echo base_url();?>empreports"  data-toggle="tooltip" title="Go To Report Log!"><i class="fa fa-chevron-circle-left"></i></a> </div>
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
              <form class="" name="emp_search_log" id="emp_search_log"  method="post"  action="<?php echo base_url('empreports/searchreportlog');?>">
                <div class="tab-pane fade active in" id="Add">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Client's</label>
                        <select class="form-control" id="client_Id" name="client_Id" onChange="getProjects(this.value);">
                          <option value="">Please select client</option>
						  <option value="all">All</option>
						  <?php foreach($getClientNames as $key => $clientName): ?>
                          <option value="<?php echo $clientName->client_Id;?>"><?php echo ucfirst($clientName->client_name);?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Project's</label>
                        <select class="form-control" id="project_Id" name="project_Id">
						  <option value="">Please select project</option>
						  <?php foreach($getListOfProjects as $key => $projectName): ?>
                          <option value="<?php echo $projectName->project_Id;?>" ><?php echo ucfirst($projectName->project_name);?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">From Date</label>
                        <input class="form-control" type="text" id="form_date" name="form_date" placeholder="Select From Date" readonly="">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">To Date</label>
                        <input class="form-control" type="text" id="to_date" name="to_date" placeholder="Select To Date" readonly="">
                      </div>
                    </div>
                  </div>
                  <div class="card-footer">
                    <button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Search</button>
                    <a  href="<?php echo base_url();?>empreports"  data-toggle="Go To Report Log!" title="Cancel">
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
  <?php if(!empty($resultTimeLog)):?>
  <div class="card">
    <div class="card-body">
      <div class="row">
        <!-- Displaying Search Result -->
	    <div class="col-md-12">
         <div class="table-responsive">
            <table class="table table-hover table-bordered" id="organisationTable">
              <thead>
                <tr>
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
				  foreach ($resultTimeLog as $key => $reportResult) :
				  $totalHours += $reportResult->emp_time_hours; // Total Hours 
				 	 if($i%2 == 0): $showRowColour = 'class="success"'; else: $showRowColour = 'class="info"'; endif;
				$getListOfProjects   	= $this->emptimelog_model->getAddedReportTaskNames($reportResult->task_Id); // List of tasks
				  ?>
                <tr <?php echo $showRowColour; ?> id="delRecordsRow<?php echo $reportResult->emp_record_id; ?>">
                  <td><?php echo $i ?></td>
                  <td nowrap="nowrap"><span class="label label-info"><?php echo ucfirst($reportResult->name);?></span></td>
				  <td nowrap="nowrap"><?php echo ucfirst($reportResult->project_name);?> </td>
				 <td nowrap="nowrap"><a href="#"  data-toggle="tooltip" title="<?php echo $getListOfProjects;?>"><?php echo character_limiter($getListOfProjects,20);?></a></td>
				  <td nowrap="nowrap"><?php echo ucfirst($reportResult->emp_time_hours);?> </td>
				 <td nowrap="nowrap">
				 <?php if($reportResult->empId != $this->session->userdata['logged_in_timesheet']['empId'] || $this->session->userdata['logged_in_timesheet']['user_type'] == 'admin'): ?> 
				 <span id="changeStatusRow_<?php echo $reportResult->emp_record_id; ?>"><a class="<?php echo ($reportResult->status=='Approved')? 'fa fa-check-circle label label-success' : (($reportResult->status=='Rejected')? 'fa fa-registered label label-warning' : 'fa fa-ban label label-danger');?>" style="cursor:pointer;" data-toggle="modal" title="Click To <?php echo ($reportResult->status=='Approved')? 'Unapproved' : 'Approved'?>" data-target="#comment_status_model_<?php echo $reportResult->emp_record_id;?>"> <?php echo $reportResult->status;?></a></span>
				 <?php else: ?>
				 <span class="<?php echo ($reportResult->status=='Approved')? 'fa fa-check-circle label label-success' : (($reportResult->status=='Rejected')? 'fa fa-registered label label-warning' : 'fa fa-ban label label-danger');?>"> <?php echo $reportResult->status;?></span>
				 <?php endif; ?> 
				  </td>
                  
				  
				  <th nowrap="nowrap"><?php echo date('d-M-Y',strtotime($reportResult->emp_report_dates));?></th>
				  
				  
                 <?php if($this->session->userdata['logged_in_timesheet']['empId'] == $reportResult->empId && $reportResult->status !='Approved'): ?>
				  <th nowrap="nowrap"><a data-toggle="modal" data-target="#pm_model_<?php echo $reportResult->emp_record_id;?>" href="#" data-toggle="tooltip" title="View" data-backdrop="static" data-keyboard="false"><i class="fa fa-history" aria-hidden="true"></i></a> | <a href="<?php echo base_url(); ?>empreports/add/<?php echo $reportResult->emp_record_id; ?>/<?php echo $reportResult->project_Id; ?>/<?php echo $reportResult->client_Id; ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a> | <a style="cursor:pointer;" data-toggle="tooltip" title="Delete" onClick="delete_emp_record(<?php echo $reportResult->emp_record_id;?>)"><i class="fa fa-sm fa-trash"></i></a></th>
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
								 <div class="form-group" style="margin-bottom: 25%; margin-top:8%;">
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
                <?php $i++; endforeach; ?>
				<div align="center"><?php  echo 'Total Hours : <b style="color: #1322d2; font-size:20px;">'.$totalHours.'</b>'; ?></div>
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
  $("form[name='emp_search_log']").validate({
    rules: {
      client_Id        			 : { required : true },
	  project_Id        		 : { required : true },
	  form_date        	 		 : { required : true },
	  to_date					 : { required : true }
	 },
    messages: {
     client_Id				     : "Please Select Client Name",
	 project_Id				     : "Please Select Project Name",
	 form_date					 : "Please Select From Date",
	 to_date					 : "Please Select To Date"
	 },					
     submitHandler: function(form) {
      form.submit();
    }
  });
});

/*Ajax Based dropdown option changes on Clients , Projects and Tasks*/

function getProjects(client_Id) {   // Getting client wise projects based on client id
	$.ajax({
	type: "POST",
	url: "<?php echo base_url('empreports/getClientProjects');?>",
	data:'client_Id='+client_Id,
	success: function(data){
		$("#project_Id").html(data);
		$('#project_Id').removeAttr('disabled');
	}
});
}

 $(document).ready(function () {
       var today = $("#form_date").val();
         var currentYear = new Date().getFullYear();
            var minDate = new Date(2015, 0, 1); // January 1, 2015
            var maxDate = new Date(); // Current date
		$("#form_date, #to_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
             changeYear: true,
                yearRange: '2015:' + currentYear,
                minDate: minDate,
                maxDate: maxDate,
            numberOfMonths: 1,
            onSelect: function (selectedDate) {
                if (this.id == 'form_date') {
                    var dateMin = $('#form_date').datepicker("getDate");
                    //var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(), dateMin.getDate() + 1);
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
        $('#to_date').datepicker("option", "minDate", new Date(today));

    })

$('#client_Id,#project_Id').select2();	 // Autosuggest list


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
	
	function delete_emp_record(emp_record_id) {
		var answer = confirm("Are you sure you want to delete record?");

		if (answer) {
			$.ajax({	
				type: "POST",
				url: "<?php echo base_url('empreports/delete');?>",
				data: "emp_record_id=" + emp_record_id,
				beforeSend: function() {
					$('#delRecordsRow' + emp_record_id).html('<i class="fa fa-spinner"></i>');
				},
				success: function(response) {

					$("#delRecordsRow" + emp_record_id).remove("#delRecordsRow" + emp_record_id).html('');
				}
			});
		}
	}
	
</script>
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
