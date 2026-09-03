<?php if(isset($getEmployees)){
	$headcountRows = isset($departmentHeadcount) ? $departmentHeadcount : array();
	$headcountTotals = isset($departmentHeadcountTotals) ? $departmentHeadcountTotals : array('beginning' => 0, 'new_joinees' => 0, 'left_org' => 0, 'end_count' => 0);
	$periodLabel = isset($headcountPeriodLabel) ? $headcountPeriodLabel : '';
?>
  <div class="row">
    <div class="col-md-12">
      <div class="employee-summary-card">
        <div class="employee-summary-card-head">
          <div class="employee-summary-title"><i class="fa fa-users"></i> Department Headcount</div>
          <div class="employee-summary-head-actions">
            <?php if ($periodLabel !== '') { ?>
            <div class="employee-summary-period-badge"><i class="fa fa-calendar"></i> Period: <?php echo htmlspecialchars($periodLabel, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php } ?>
            <button type="button" class="btn btn-success btn-flat employee-summary-send-btn" id="send_headcount_email" title="Send headcount report">
              <i class="fa fa-paper-plane"></i> Sent
            </button>
          </div>
        </div>
        <div class="table-responsive">
          <table class="employee-summary-table">
            <thead>
              <tr>
                <th class="hc-col-dept">Department</th>
                <th>Beginning of the<br>Month Head Count</th>
                <th>New Joinees</th>
                <th>Left Org</th>
                <th>End of the<br>Month Head Count</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($headcountRows)) { foreach ($headcountRows as $headcountRow) {
                $newJoinees = (int)$headcountRow['new_joinees'];
                $leftOrg = (int)$headcountRow['left_org'];
              ?>
              <tr>
                <td class="hc-dept"><?php echo htmlspecialchars($headcountRow['department'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="hc-num"><?php echo (int)$headcountRow['beginning']; ?></td>
                <td class="hc-num"><span class="hc-chip <?php echo ($newJoinees > 0) ? 'hc-chip-join' : 'hc-chip-muted'; ?>"><?php echo $newJoinees; ?></span></td>
                <td class="hc-num"><span class="hc-chip <?php echo ($leftOrg > 0) ? 'hc-chip-left' : 'hc-chip-muted'; ?>"><?php echo $leftOrg; ?></span></td>
                <td class="hc-num hc-end"><?php echo (int)$headcountRow['end_count']; ?></td>
              </tr>
              <?php }} else { ?>
              <tr>
                <td colspan="5" class="hc-empty">No department data found.</td>
              </tr>
              <?php } ?>
            </tbody>
            <tfoot>
              <tr>
                <th class="hc-dept">Total</th>
                <th class="hc-num"><?php echo (int)$headcountTotals['beginning']; ?></th>
                <th class="hc-num"><?php echo (int)$headcountTotals['new_joinees']; ?></th>
                <th class="hc-num"><?php echo (int)$headcountTotals['left_org']; ?></th>
                <th class="hc-num"><?php echo (int)$headcountTotals['end_count']; ?></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="employee_report_excel_download">
              <thead>
                <tr>
                 <th>Sno</th>
<th>Name</th>
<th>Emp.Id</th>
<th>Joining Date</th>
<th>Username</th>
<th>Email</th>
<th>Designation</th>
<th>Department</th>
<th>Reporting Manager</th>
<th>User Type</th>
<th>Status</th>
<th>Entry Date</th>
<th>Action</th>
                  
                </tr>
              </thead>
              <tbody>
                <?php 
				  $i=1;
				  if (isset($getEmployees) && !empty($getEmployees)) {
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
                  <tr>
                    <td><?php echo $i ?></td>
                    <td><?php echo ucwords($empResult->name);?></td>
                    <td><?php echo ucwords($empResult->emp_com_id);?></td>

<td>
<?php
if(!empty($empResult->emp_joining_date) && $empResult->emp_joining_date != '0000-00-00'){
    echo date('d-M-Y', strtotime($empResult->emp_joining_date));
}else{
    echo '--';
}
?>
</td>

<td><?php echo ucfirst($empResult->username);?></td>
<td><?php echo $empResult->email;?></td>
                      <td><?php echo $empResult->designation;?></td>
                    <td><?php echo $empResult->department;?></td>
                    <td><?php echo !empty($empResult->reporting_manager_name) ? ucwords($empResult->reporting_manager_name) : '--';?></td>
				     <td><?php echo ucfirst($empResult->user_type);?></td>
					 <td>
    <?php echo $empResult->status;?>
</td>

<td><?php echo date('d-M-Y',strtotime($createdExp[0]));?></td>

<td nowrap="nowrap">
                      <?php if($empResult->username != 'admin'): ?>
                        <a href="<?php echo base_url(); ?>employee/add/<?php echo $empResult->empId; ?>" data-toggle="tooltip" title="Edit Employee"><i class="fa fa-edit"></i></a> |
                        <span id="changeStatusRow_<?php echo $empResult->empId; ?>">
                          <a class="<?php echo ($empResult->status=='Active')? 'fa fa-check-circle label label-success' : 'fa fa-ban label label-danger'?>" style="cursor:pointer;" onClick="update_emp_status(<?php echo $empResult->empId;?>,'<?php echo $empResult->status; ?>')"> <?php echo $empResult->status;?></a>
                        </span> |
                        <a href="<?php echo base_url(); ?>employee/cpass/<?php echo $empResult->empId; ?>" data-toggle="tooltip" title="Change Password"><i class="fa fa-key"></i></a>
                      <?php endif; ?>
                     </td>
				    
                  </tr>
                  <?php $i++; endforeach; 
				  } else {
					  echo '<tr><td colspan="13" class="text-center">No records found.</td></tr>';
				  }
				  ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
