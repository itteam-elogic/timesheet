<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
$userType = $this->session->userdata['logged_in_timesheet']['user_type'];
$createdUser = $this->session->userdata['logged_in_timesheet']['username']; // Session Loged in username
?>
<!-- Inlude Header here END-->

<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1>Manage Tickets</h1>
    </div>
    <div><a class="btn btn-primary btn-flat" href="<?php echo base_url('ticket/add'); ?>" data-toggle="tooltip" title="Add Ticket"><i class="fa fa-lg fa-plus"></i></a><a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url('ticket'); ?>"><i class="fa fa-lg fa-refresh"></i></a>
   <?php if($userType == 'admin'): ?>
	<a class="btn btn-primary btn-flat" href="<?php echo base_url('ticket/it_reports'); ?>" data-toggle="tooltip" title="Ticket Report"><i class="fa fa-cloud-download"></i> IT Reports</a><?php endif; ?>
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
                  <th>Created By</th>
                  <th>Ticket Type</th>    
				  <th>Priority</th>
                  <th>Description</th>
                  <th>Status</th>
				  <th>Responsibility</th>	
                  <th>T.Raised Date</th>
                  <th>T.Closed Date</th>       
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
               <?php 
                  $cnt = 1;
                  
                  foreach($getTicketsInfo as $key => $ticketDetails): 
                  
                  if($ticketDetails->ticket_priority == 'High'):
                  
                        $priorityClass = 'class="badge badge-danger"';
                  
                  elseif( $ticketDetails->ticket_priority == 'Medium'):
                  
                        $priorityClass = 'class="label label-info"';
                  
                  else: 
                  
                        $priorityClass = 'class="badge badge-info"';
                  
                  endif;   
                  
                  
                  if($ticketDetails->ticket_status == 'Closed'):
                  
                         $ticketStatusColour = 'class="badge badge-danger"';
                  
                  elseif($ticketDetails->ticket_status == 'In Progress'):
                  
                         $ticketStatusColour = 'class="label label-info"';
                  
                  else:
                  
                         $ticketStatusColour = 'class="badge badge-secondary"';
                      
                    endif;  
                  
                  
                  ?> 
                <tr>
                  <td><?php echo $cnt++; ?> </td>
                  <td><span class="badge badge-secondary"><?php echo $ticketDetails->ticket_username;?></span></td>
                  <td><?php echo $ticketDetails->ticket_name;?></td>
				  <td><span <?php echo $priorityClass; ?>><?php echo $ticketDetails->ticket_priority;?></span></td>
                    <td><a href="#" data-toggle="tooltip" title="<?php echo $ticketDetails->ticket_desc; ?>" data-original-title="<?php echo $ticketDetails->ticket_desc; ?>"><?php echo substr($ticketDetails->ticket_desc,0,30);?>...</a></td>
                    <td><span <?php echo $ticketStatusColour; ?>><?php echo $ticketDetails->ticket_status;?></span></td>
                    <td><span class="badge badge-warning"><?php echo $ticketDetails->ticket_responsibility;?></span></td>
                    <th><span class="badge badge-success"><?php echo $ticketDetails->ticket_raised_date;?></span></th>
                    <th><span class="badge badge-danger"><?php echo $ticketDetails->ticket_closed_date;?></span></th>    
                    
                    
                    <?php if($ticketDetails->ticket_status != 'Closed'):?>
                    
                    <th>
                        <?php  if($userType == 'developer'): ?>
                        
                        
                        <?php if($createdUser =='saipavan' || $createdUser == 'vaibhav' || $createdUser =='tarak'):?>
                        
                            <a href="<?php echo base_url(); ?>ticket/add/<?php echo $ticketDetails->ticket_id; ?>" data-toggle="tooltip" title="" data-original-title="Edit"><i class="fa fa-edit"></i></a>
                        
                        <?php else: ?>
                            <a href="<?php echo base_url(); ?>ticket/developerEditForm/<?php echo $ticketDetails->ticket_id; ?>" data-toggle="tooltip" title="" data-original-title="Edit"><i class="fa fa-edit"></i></a>
                        
                        <?php endif; ?>
                       
                        
                        <?php else: ?>
                        
                            <a href="<?php echo base_url(); ?>ticket/add/<?php echo $ticketDetails->ticket_id; ?>" data-toggle="tooltip" title="" data-original-title="Edit"><i class="fa fa-edit"></i></a>
                        
                        <?php endif; ?>
                        
                        | <a href="<?php echo base_url(); ?>ticket/viewticket/<?php echo $ticketDetails->ticket_id; ?>" data-toggle="tooltip" title="" data-original-title="View"><i class="fa fa-history"></i></a></th>
                    
                  <?php else: ?>  
                    
                    <th>Not Editable | <a href="<?php echo base_url(); ?>ticket/viewticket/<?php echo $ticketDetails->ticket_id; ?>" data-toggle="tooltip" title="" data-original-title="View"><i class="fa fa-history"></i></a></th>
                    
                  <?php  endif; ?>  
                    
                    
				 </tr>
              <?php endforeach; ?>    
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
function delete_project(project_Id){ 
var answer = confirm ("Are you sure you want to delete project?");
if (answer) {
        $.ajax({
                type: "POST",
                url: "<?php echo base_url('projects/delete');?>",
                data: "project_Id="+project_Id,
				beforeSend: function() {
   							 $('#delProjectRow'+project_Id).html('<i class="fa fa-spinner"></i>');
 				 },success: function (response) { 	
					      
				       $("#delProjectRow"+project_Id).remove("#delProjectRow"+project_Id).html('');
			     }
            });
      }
}
</script>
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
