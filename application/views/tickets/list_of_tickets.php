<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
$userType = $this->session->userdata['logged_in_timesheet']['user_type'];
$createdUser = $this->session->userdata['logged_in_timesheet']['username']; // Session Loged in username

$fromDate = isset($form_date) ? $form_date : (isset($_REQUEST['form_date']) ? $_REQUEST['form_date'] : '');
$toDate   = isset($to_date) ? $to_date : (isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : '');
$searchVal = isset($search) ? $search : '';
$sortCol = isset($sort_col) ? $sort_col : 'ticket_id';
$sortDir = isset($sort_dir) ? $sort_dir : 'desc';
$totalRecords = isset($total_records) ? (int)$total_records : 0;
$perPage = isset($per_page) ? (int)$per_page : 15;
$currentPage = isset($current_page) ? (int)$current_page : 1;
$startRecord = $totalRecords > 0 ? (isset($row_offset) ? (int)$row_offset + 1 : 1) : 0;
$endRecord = $totalRecords > 0 ? min(isset($row_offset) ? (int)$row_offset + count(isset($getTicketsInfo) ? $getTicketsInfo : array()) : count(isset($getTicketsInfo) ? $getTicketsInfo : array()), $totalRecords) : 0;
function _ticket_sort_url($form_date, $to_date, $searchVal, $col, $sortCol, $sortDir) {
	$qp = array();
	if (!empty($form_date)) $qp['form_date'] = $form_date;
	if (!empty($to_date)) $qp['to_date'] = $to_date;
	if ($searchVal !== '') $qp['search'] = $searchVal;
	$qp['sort'] = $col;
	$qp['order'] = ($sortCol === $col && $sortDir === 'asc') ? 'desc' : 'asc';
	return base_url('ticket?' . http_build_query($qp));
}

?>
<!-- Inlude Header here END-->

<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1>Manage Tickets</h1>
    </div>
    <div>
   <?php if(!empty($fromDate) && !empty($toDate)): ?>
	<a class="btn btn-primary btn-flat" href="<?php echo base_url();?>ticket/ticketsystemreport?form_date=<?php echo urlencode($fromDate);?>&to_date=<?php echo urlencode($toDate);?>" data-toggle="tooltip" title="Ticket Report"><i class="fa fa-cloud-download"></i> Download Report From <?php echo htmlspecialchars($fromDate); ?> To <?php echo htmlspecialchars($toDate); ?>  </a> | <a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url('ticket'); ?>"><i class="fa fa-lg fa-refresh"></i></a>
	<?php else: ?>
	<a class="btn btn-primary btn-flat" href="<?php echo base_url('ticket/add'); ?>" data-toggle="tooltip" title="Add Ticket"><i class="fa fa-lg fa-plus"></i></a><a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url('ticket'); ?>"><i class="fa fa-lg fa-refresh"></i></a>	
	<?php endif; ?>
	  </div>
  </div>
	
	<?php  if($this->session->userdata['logged_in_timesheet']['user_type'] == 'admin' || $this->session->userdata['logged_in_timesheet']['user_type'] == 'manager') : ?>
    <div class="card">
        <h3 class="card-title"></h3>
        <div class="card-body">
            <div class="row">
                <!-- Search for employee with date wise and client , project wise as well. -->
                
                <div class="col-md-12">
                    <div class="bs-component">
                        <div class="tab-content" id="myTabContent">
                            <!-- Employee Report adding block -->
                            <form class="" name="it_report_search" id="it_report_search" method="get" action="<?php echo base_url('ticket');?>">
                                <div class="tab-pane fade active in" id="Add">
                                    <div class="row">
                                         <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">From Date</label>
                                                <input class="form-control" type="text" id="form_date" name="form_date" placeholder="Select From Date" readonly="" value="<?php echo $fromDate;?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">To Date</label>
                                                <input class="form-control" type="text" id="to_date" name="to_date" placeholder="Select To Date" readonly="" value="<?php echo $toDate;?>">
                                            </div>
                                        </div>

                                        <div class="card-footer">
                                            <button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Search</button>
                                            <a href="<?php echo base_url();?>empreports/unapproved" data-toggle="Go To Report Log!" title="Cancel">
                                                <button class="btn btn-default icon-btn" type="button"><i class="fa fa-chevron-circle-left"></i>Back</button>
                                            </a>
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
 <?php endif; ?>
	
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="ticket-toolbar" style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px; padding: 12px 16px; margin-bottom: 16px;">
            <form method="get" action="<?php echo base_url('ticket'); ?>" class="form-inline" style="align-items: center; flex-wrap: wrap; gap: 10px;">
              <?php if (!empty($fromDate)): ?><input type="hidden" name="form_date" value="<?php echo htmlspecialchars($fromDate); ?>"><?php endif; ?>
              <?php if (!empty($toDate)): ?><input type="hidden" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>"><?php endif; ?>
              <label class="control-label" style="margin: 0; font-weight: 600; color: #495057;">Search</label>
              <div class="input-group" style="max-width: 280px;">
                <input type="text" name="search" class="form-control" placeholder="Created by, type, description, status..." value="<?php echo htmlspecialchars($searchVal); ?>" style="border-right: 0;">
                <span class="input-group-btn">
                  <button type="submit" class="btn btn-primary" style="border-radius: 0 4px 4px 0;"><i class="fa fa-search"></i></button>
                </span>
              </div>
              <?php if ($searchVal !== ''): ?>
              <a href="<?php echo base_url('ticket' . (!empty($fromDate) && !empty($toDate) ? '?form_date='.urlencode($fromDate).'&to_date='.urlencode($toDate) : '')); ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
              <?php endif; ?>
            </form>
          </div>
          <div class="table-responsive">
            <table class="table table-hover table-bordered text-nowrap" id="ticketListTable" style="margin-bottom: 0;">
              <thead>
                <tr style="background: #004b88; color: #fff;">
                  <th style="border-color: #003366; font-weight: 600;">Sno</th>
                  <th style="border-color: #003366;"><a href="<?php echo _ticket_sort_url($fromDate, $toDate, $searchVal, 'ticket_username', $sortCol, $sortDir); ?>" style="color: #fff; text-decoration: none;">Created By <?php if ($sortCol === 'ticket_username') echo $sortDir === 'asc' ? ' &#9650;' : ' &#9660;'; ?></a></th>
                  <th style="border-color: #003366;"><a href="<?php echo _ticket_sort_url($fromDate, $toDate, $searchVal, 'ticket_name', $sortCol, $sortDir); ?>" style="color: #fff; text-decoration: none;">Ticket Type <?php if ($sortCol === 'ticket_name') echo $sortDir === 'asc' ? ' &#9650;' : ' &#9660;'; ?></a></th>
				  <th style="border-color: #003366;"><a href="<?php echo _ticket_sort_url($fromDate, $toDate, $searchVal, 'ticket_priority', $sortCol, $sortDir); ?>" style="color: #fff; text-decoration: none;">Priority <?php if ($sortCol === 'ticket_priority') echo $sortDir === 'asc' ? ' &#9650;' : ' &#9660;'; ?></a></th>
                  <th style="border-color: #003366;"><a href="<?php echo _ticket_sort_url($fromDate, $toDate, $searchVal, 'ticket_desc', $sortCol, $sortDir); ?>" style="color: #fff; text-decoration: none;">Description <?php if ($sortCol === 'ticket_desc') echo $sortDir === 'asc' ? ' &#9650;' : ' &#9660;'; ?></a></th>
                  <th style="border-color: #003366;"><a href="<?php echo _ticket_sort_url($fromDate, $toDate, $searchVal, 'ticket_status', $sortCol, $sortDir); ?>" style="color: #fff; text-decoration: none;">Status <?php if ($sortCol === 'ticket_status') echo $sortDir === 'asc' ? ' &#9650;' : ' &#9660;'; ?></a></th>
                  <th style="border-color: #003366; font-weight: 600;">Attachment</th>
				  <th style="border-color: #003366;"><a href="<?php echo _ticket_sort_url($fromDate, $toDate, $searchVal, 'ticket_responsibility', $sortCol, $sortDir); ?>" style="color: #fff; text-decoration: none;">Responsibility <?php if ($sortCol === 'ticket_responsibility') echo $sortDir === 'asc' ? ' &#9650;' : ' &#9660;'; ?></a></th>
                  <th style="border-color: #003366;"><a href="<?php echo _ticket_sort_url($fromDate, $toDate, $searchVal, 'ticket_raised_date', $sortCol, $sortDir); ?>" style="color: #fff; text-decoration: none;">T.Raised Date <?php if ($sortCol === 'ticket_raised_date') echo $sortDir === 'asc' ? ' &#9650;' : ' &#9660;'; ?></a></th>
                  <th style="border-color: #003366;"><a href="<?php echo _ticket_sort_url($fromDate, $toDate, $searchVal, 'ticket_closed_date', $sortCol, $sortDir); ?>" style="color: #fff; text-decoration: none;">T.Closed Date <?php if ($sortCol === 'ticket_closed_date') echo $sortDir === 'asc' ? ' &#9650;' : ' &#9660;'; ?></a></th>
				  <th style="border-color: #003366; font-weight: 600;">T.Duration</th>
                  <th style="border-color: #003366; font-weight: 600;">Action</th>
                </tr>
              </thead>
              <tbody>
               <?php 
                  $cnt = isset($row_offset) ? (int)$row_offset + 1 : 1;
                  
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
				  
				  if(!empty($ticketDetails->ticket_completed_time)){
						
						
						$tOpenDT = $ticketDetails->ticket_raised_date.' '.$ticketDetails->ticket_open_time;
						
						$tClosedDT = $ticketDetails->ticket_closed_date.' '.$ticketDetails->ticket_completed_time;
						
						// Convert string dates to Unix timestamps
					$timestamp1 = strtotime($tOpenDT);
					$timestamp2 = strtotime($tClosedDT);

					// Calculate the difference in seconds
					$timeDiffSeconds = $timestamp2 - $timestamp1;

					// Convert seconds to hours and minutes
					$hours = floor($timeDiffSeconds / 3600); // 1 hour = 3600 seconds
					$minutes = ($timeDiffSeconds % 3600) / 60; // 1 minute = 60 seconds
 
						// Display the result
							$solvedActualTime =  " " . ($hours < 0 ? '-' : '') . abs($hours) . " hr " . abs($minutes) . " min";

					}else{
						
						$solvedActualTime = '';
					}
                  
                  
                  ?> 
                <tr>
                  <td><?php echo $cnt++; ?> </td>
                  <td><span class="badge badge-secondary"><?php echo $ticketDetails->ticket_username;?></span></td>
                  <td><?php echo $ticketDetails->ticket_name;?></td>
				  <td><span <?php echo $priorityClass; ?>><?php echo $ticketDetails->ticket_priority;?></span></td>
                    <td><a href="#" data-toggle="tooltip" title="<?php echo $ticketDetails->ticket_desc; ?>" data-original-title="<?php echo $ticketDetails->ticket_desc; ?>"><?php echo substr($ticketDetails->ticket_desc,0,30);?>...</a></td>
                    <td><span <?php echo $ticketStatusColour; ?>><?php echo $ticketDetails->ticket_status;?></span></td>
                    
                        <td>
							<?php if(!empty($ticketDetails->ticket_upload_image)): ?>
							<a href="" onclick="window.open('<?php echo base_url().'uploads/ticket_uploded_images/'.$ticketDetails->ticket_upload_image;?>','targetWindow', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1090px, height=550px, top=25px left=120px'); return false;">click to open image</a> 
						<?php elseif(!empty($ticketDetails->ticket_closed_upload_image)): ?>
							<a href="" onclick="window.open('<?php echo base_url().'uploads/ticket_uploded_images/ticket_closed_img/'.$ticketDetails->ticket_closed_upload_image;?>','targetWindow', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1090px, height=550px, top=25px left=120px'); return false;">click to open image</a>
							<?php else:?>
							 No image uploaded
					     </td>
				            
					    
				    <?php endif; ?>
                    <!-- <?php if(!empty($ticketDetails->ticket_closed_upload_image)): ?>
                        <td><a href="" onclick="window.open('<?php echo base_url().'uploads/ticket_uploded_images/ticket_closed_img/'.$ticketDetails->ticket_closed_upload_image;?>','targetWindow', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1090px, height=550px, top=25px left=120px'); return false;">click to open image</a> </td>
				           
                        <?php else: ?>
					    <td>No image uploaded</td>
				    <?php endif; ?> -->

                    <td><span class="badge badge-warning"><?php echo ucfirst($ticketDetails->ticket_responsibility);?></span></td>
                    <th><span class="badge badge-success"><?php echo $ticketDetails->ticket_raised_date;?></span></th>
                    <th><span class="badge badge-danger"><?php echo $ticketDetails->ticket_closed_date;?></span></th>    
                    <th><?php echo $solvedActualTime;?></th>    
                    
                    
                    <?php if($ticketDetails->ticket_status != 'Closed'):?>
                    
                    <th>
                        <?php  if($userType == 'developer' || $userType == 'manager' || $userType == 'admin' || $userType == 'business_head'): ?>
                        
                        
                        <?php if($createdUser =='nagesh' || $createdUser =='suman' || $createdUser =='tsadmin'):?>
                        
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
          <div class="ticket-table-footer" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; padding: 14px 16px; background: #f8f9fa; border: 1px solid #e9ecef; border-top: 0; border-radius: 0 0 6px 6px;">
            <div class="ticket-record-info" style="font-size: 13px; color: #495057;">
              <?php if ($totalRecords > 0): ?>
              <strong>Showing <?php echo $startRecord; ?> to <?php echo $endRecord; ?> of <?php echo number_format($totalRecords); ?> records</strong>
              <?php else: ?>
              <strong>No records found</strong>
              <?php endif; ?>
            </div>
            <?php if (!empty($pagination_links)): ?>
            <div class="ticket-pagination"><?php echo $pagination_links; ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<style>
#ticketListTable thead th a:hover { color: #cce5ff !important; text-decoration: underline !important; }
.ticket-pagination .pagination { margin: 0; }
.ticket-pagination .pagination li a { padding: 6px 12px; }
</style>
<!-- Inlude Footer here -->
<script type="text/javascript">
	
$(function() {
        $("form[name='it_report_search']").validate({
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
        $("#form_date, #to_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            numberOfMonths: 1,
            onSelect: function(selectedDate) {
                if (this.id == 'form_date') {
                    var dateMin = $('#form_date').datepicker("getDate");
                    //var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(), dateMin.getDate() + 1);
                    var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(), dateMin.getDate());
                    var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(), dateMin.getDate() + 365);
                    $('#to_date').datepicker("option", "minDate", rMin);
                    $('#to_date').datepicker("option", "maxDate", rMax);
                }


            }
        });

        $('#to_date').datepicker("option", "minDate", new Date(today));

    })	
	
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
