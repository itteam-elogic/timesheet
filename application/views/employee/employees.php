<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<!-- Inlude Header here END-->

<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1>Manage Employees</h1>
    </div>
    <div><a class="btn btn-primary btn-flat" href="<?php echo base_url('employee/employee_list_information'); ?>" data-toggle="tooltip" title="Add Employee"><i class="fa fa-lg fa-plus"></i></a><a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url('employee/employee_list_information'); ?>"><i class="fa fa-lg fa-refresh"></i></a>
        <?php if(in_array($this->session->userdata['logged_in_timesheet']['user_type'], array('admin','business_head')) || $this->session->userdata['logged_in_timesheet']['empId'] ='140' || $this->session->userdata['logged_in_timesheet']['empId'] = '416'): ?>  
     <a class="btn btn-warning btn-flat" data-toggle="tooltip" title="Generate employees Report" href="<?php echo base_url('employee/employee_list_information'); ?>"><i class="fa fa-lg fa-search"></i> Generate employees Report</a>
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
                  <th>Name</th>
                  <th>Emp.Id</th>    
                  <th>Username</th>
                  <th>Email</th>
                  <th>Designation</th>
                   <th>Department</th>    
				  <th>User Type</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php 
				  $i=1;
				  foreach ($getEmployees as $key => $empResult) :
				 	 if($i%2 == 0): $showRowColour = 'class="success"'; else: $showRowColour = 'class="info"'; endif;				  
				  	 $createdExp 		= explode(" " , $empResult->created_at);
					
					 if($empResult->user_type == 'manager'):

							$statusClass = 'class="label label-danger"';
						
					elseif($empResult->user_type == 'developer'):

							$statusClass = 'class="label label-info"';	

				    else:				 
							$statusClass = 'class="label label-primary"';
					 
					 endif;
					 
				  ?>
                <tr <?php echo $showRowColour; ?> id="delEmpRow<?php echo $empResult->empId; ?>">
                  <td><?php echo $i ?></td>
                  <td><?php echo ucwords($empResult->name);?></td>
                  <td><?php echo ucwords($empResult->emp_com_id);?></td>    
                  <td><?php echo ucfirst($empResult->username);?> </td>
                  <td><?php echo $empResult->email;?></td>
                  <td><?php echo $empResult->designation;?></td>
                    <td><?php echo $empResult->department;?></td>
				   <td><span <?php echo $statusClass; ?>><?php echo ucfirst($empResult->user_type);?></span></td>
                   <th><?php echo date('d-M-Y',strtotime($createdExp[0]));?></th>
				  <th>
				  <?php if($empResult->username !='admin'): ?>
                  <a href="<?php echo base_url(); ?>employee/employee_list_information/<?php echo $empResult->empId; ?>" data-toggle="tooltip" title="Edit Employee"><i class="fa fa-edit"></i></a> | 
				  <span id="changeStatusRow_<?php echo $empResult->empId; ?>"><a class="<?php echo ($empResult->status=='Active')? 'fa fa-check-circle label label-success' : 'fa fa-ban label label-danger'?>" style="cursor:pointer;" onClick="update_emp_status(<?php echo $empResult->empId;?>,'<?php echo $empResult->status;; ?>')"> <?php echo $empResult->status;?></a></span> |
				  <a href="<?php echo base_url(); ?>employee/cpass/<?php echo $empResult->empId; ?>" data-toggle="tooltip" title="Change Password"><i class="fa fa-key"></i></a>  
				 <?php endif;?> 
				 </th>
                </tr>
                <?php $i++; endforeach; ?>
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
var status;
function update_emp_status(empId,status){   
var updateStatus = (status=='Active')? 'InActive' : 'Active';
var answer = confirm ("Are you sure you want to update status "+updateStatus);
//alert(updateStatus); 
if (answer) {
        $.ajax({
                type: "POST",
                url: "<?php echo base_url('employee/update_emp_status');?>",
                data: "empId="+empId+'&status='+updateStatus,
				beforeSend: function() {
   							$('#changeStatusRow_'+empId).html('<i class="fa fa-spinner"></i>');
 				 },success: function (response) {  //alert('---' + response)
				            $("#changeStatusRow_"+empId).html(response);
							//location.reload();
			     }
            });
      }
}
</script>
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
