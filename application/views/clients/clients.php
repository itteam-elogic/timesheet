<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
$createdUser = $this->session->userdata['logged_in_timesheet']['empId'];
?>
<!-- Inlude Header here END-->

<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1>Manage Clients</h1>
    </div>
    <div><a class="btn btn-primary btn-flat" href="<?php echo base_url('clients/add'); ?>" data-toggle="tooltip" title="Add Client"><i class="fa fa-lg fa-plus"></i></a><a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url('clients'); ?>"><i class="fa fa-lg fa-refresh"></i></a>
    <?php if(in_array($this->session->userdata['logged_in_timesheet']['user_type'], array('admin','business_head'))): ?>     
	<a class="btn btn-warning btn-flat" data-toggle="tooltip" title="Generate Clients Report" href="<?php echo base_url('clients/client_list_information'); ?>"><i class="fa fa-lg fa-search"></i> Generate Client Report</a>
    <?php endif; ?>
      </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="organisationTable">
              <thead>
                <tr>
                  <th>Sno</th>
                  <th>Client Name</th>
				   <th>Client Email</th>
                  <th>Created By</th>
                  <th>Description</th>
				  <th>Contact Num</th>
					<th>Department</th>
                    <th>Country</th>
                    <th>State</th>
                    <th>City</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php 
				  $i=1;
				  if(empty($getClients)): ?>
					<tr>
						<td colspan="13" class="text-center">No clients found.</td>
					</tr>
				  <?php else:
				  foreach ($getClients as $key => $clientResult) :
				 	 if($i%2 == 0): $showRowColour = 'class="success"'; else: $showRowColour = 'class="info"'; endif;
				  	 $createdExp 		= explode(" " , $clientResult->created_at);
				  
				   // Handle status display - show Active/Inactive with appropriate colors
				   $currentStatus = !empty($clientResult->status) ? $clientResult->status : 'Inactive';
				   if($currentStatus == 'Active'):
							$statusClass = 'label label-success';
					 else:
							$statusClass = 'label label-danger';
					 endif;
                  
                  //echo 'test---'.$clientResult->empId;
				  
				 ?>
                <tr <?php echo $showRowColour; ?> id="delClientRow<?php echo $clientResult->client_Id; ?>">
                  <td><?php echo $i ?></td>
                  <td><?php echo ucwords($clientResult->client_name);?></td>
				   <td><?php echo $clientResult->client_email;?></td>
                  <td><span class="label label-info"><?php echo ucfirst($clientResult->name);?></span></td>
                  <td><a href="#"  data-toggle="tooltip" title="<?php echo $clientResult->client_desc;?>"><?php echo character_limiter($clientResult->client_desc, 20);?></a></td>
				  <td><?php echo $clientResult->client_contact_num;?></td>
					<td><?php echo $clientResult->department;?></td>
                    <td><?php echo $clientResult->client_country;?></td>
                    <td><?php echo $clientResult->client_state;?></td>
                    <td><?php echo $clientResult->client_city;?></td>
                  <td>
					  <?php
					  // Check if user is admin - only admin can toggle status
					  $isAdmin = in_array($this->session->userdata['logged_in_timesheet']['user_type'], array('admin', 'superadmin'));
					  if($isAdmin): ?>
					  <span id="status_<?php echo $clientResult->client_Id; ?>"
							data-status="<?php echo htmlspecialchars($currentStatus, ENT_QUOTES); ?>"
							class="status-badge <?php echo $statusClass; ?>"
							style="cursor: pointer;"
							onclick="updateClientStatus(<?php echo $clientResult->client_Id; ?>)"
							title="Click to toggle status">
							<?php echo htmlspecialchars($currentStatus); ?>
					  </span>
					  <?php else: ?>
					  <span class="status-badge <?php echo $statusClass; ?>" title="Status (Only admin can change)">
							<?php echo htmlspecialchars($currentStatus); ?>
					  </span>
					  <?php endif; ?>
				  </td>
                  <th><?php echo date('d-M-Y',strtotime($createdExp[0]));?></th>

                  <th>
                       <?php if(!empty($createdUser == $clientResult->empId)):?>
                      <a href="<?php echo base_url(); ?>clients/add/<?php echo $clientResult->client_Id; ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a>
                      <?php endif;?>
                      <?php if(!empty($createdUser == '421')):?>|
                      <a style="cursor:pointer;" data-toggle="tooltip" title="Delete" onClick="delete_client(<?php echo $clientResult->client_Id;?>)"><i class="fa fa-sm fa-trash"></i></a><?php endif; ?>
                    </th>


				 </tr>
                <?php $i++; endforeach;
				  endif; ?>
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
function delete_client(client_Id){
var answer = confirm ("Are you sure you want to delete client?");
if (answer) {
        $.ajax({
                type: "POST",
                url: "<?php echo base_url('clients/delete');?>",
                data: "client_Id="+client_Id,
				beforeSend: function() {
   							 $('#delClientRow'+client_Id).html('<i class="fa fa-spinner"></i>');
 				 },success: function (response) {
				       $("#delClientRow"+client_Id).remove("#delClientRow"+client_Id).html('');
			     }
            });
      }
}

function updateClientStatus(client_Id) {
	var statusElement = $('#status_' + client_Id);
	// Get current status from the element's text or data attribute
	var current_status = statusElement.text().trim() || statusElement.data('status') || 'Active';
	var originalStatus = current_status;

	// Determine new status
	var newStatus = (current_status == 'Active') ? 'Inactive' : 'Active';

	// Show confirmation message
	var confirmMessage = 'Are you sure you want to change the status to ' + newStatus + '?';
	if(!confirm(confirmMessage)) {
		return; // User cancelled
	}

	// Show loading state
	statusElement.html('<i class="fa fa-spinner fa-spin"></i>');
	statusElement.css('pointer-events', 'none');

	$.ajax({
		type: "POST",
		url: "<?php echo base_url('clients/update_status');?>",
		data: {
			client_Id: client_Id,
			current_status: current_status
		},
		dataType: 'json',
		success: function(response) {
			if(response.success) {
				// Update the status display
				var newStatus = response.new_status;
				statusElement.text(newStatus);
				
				// Update the data attribute for future clicks
				statusElement.data('status', newStatus);
				
				// Update the CSS class
				if(newStatus == 'Active') {
					statusElement.removeClass('label-danger').addClass('label-success');
				} else {
					statusElement.removeClass('label-success').addClass('label-danger');
				}
			} else {
				// Revert on error
				statusElement.text(originalStatus);
				statusElement.data('status', originalStatus);
				alert('Error: ' + response.message);
			}
		},
		error: function() {
			// Revert on error
			statusElement.text(originalStatus);
			statusElement.data('status', originalStatus);
			alert('An error occurred while updating the status. Please try again.');
		},
		complete: function() {
			// Re-enable clicking
			statusElement.css('pointer-events', 'auto');
		}
	});
}
</script>
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
