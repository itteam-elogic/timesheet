<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<!-- Inlude Header here END-->
<div class="content-wrapper" style="font-size:13px;">
  <div class="page-title">
    <div>
      <h1>Service Agreement</h1>
    </div>
    <div>
        <?php if($this->session->userdata['logged_in_timesheet']['username'] == 'ramakrishna' || $this->session->userdata['logged_in_timesheet']['username'] == 'rupali'):?>  
		<span> <button id="downloadSow" class="btn btn-warning btn-flat">Download Service Agreement into Excel</button></span>
		<?php endif; ?>
        <a class="btn btn-primary btn-flat" href="<?php echo base_url('service_agreement/add_service_agreement'); ?>" data-toggle="tooltip" title="Add Service Agreement"><i class="fa fa-lg fa-plus"></i></a>
        
        <a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url('service_agreement'); ?>"><i class="fa fa-lg fa-refresh"></i></a></div>
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
							<form class="" name="#" id="#" method="post" action="<?php echo base_url('service_agreement');?>">
								<div class="tab-pane fade active in" id="Add">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">Country</label>
												<select class="form-control" id="user_status_type" name="user_status_type">
													<option value="">Please select Country</option>
													<option value="all">All</option>
												<?php $getCountryCodeList = $this->service_agreement_model->getCountryCode();
													
													foreach($getCountryCodeList as $key => $getCode):
                                						?>
													<option value="<?=$getCode->country_code_name;?>"><?=$getCode->country_name;?></option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">From Date</label>
												<input class="form-control" type="text" id="form_date" name="form_date" placeholder="Select From Date" value="" readonly="">
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">To Date</label>
												<input class="form-control" type="text" id="to_date" name="to_date" placeholder="Select To Date" value="" readonly="">
											</div>
										</div>
									</div>
									<div class="card-footer">
										<button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Search</button>
										<a href="<?php echo base_url();?>service_agreement" data-toggle="Go To Report Log!" title="Cancel">
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
	
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="exportSowInfo table table-hover table-bordered" id="organisationTable">
              <thead>
                <tr>
                  <th>Sno</th>
                  <th>C.C</th>
                  <!--<th>C.Code</th>-->
                  <th>P.C</th>
                  <th>S.Date</th>
                  <th>E.Date</th>    
				  <th>C.Name</th>
                  <th>P.Name</th>
				  <th>Lead.O</th>	
                  <th>Type</th>
				  <th>Status</th>
				  <th>Department</th> 
                  <th>I.Status</th>    
                  <th>Date</th> 
                   <th>Created</th>    
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>                
                  <?php foreach($getServieAgreement as  $key => $finalData): 
                  
                       $getEmpName = $this->service_agreement_model->getEmpName($finalData->empId); // get created username and id
                  
                        $agreement_date=date_create($finalData->agreement_date);
                  
                        //$est_deliverable_dates  = date_create($finalData->est_deliverable_dates);
                            
                        $createdD = explode(' ',$finalData->created_by);
                        
                        $createdAt = date_create($createdD[0]);
                  
                        $servie_agreement_id = $finalData->servie_agreement_id;
                      
                        if($finalData->agreement_status == 'Sent Sow'): // Status
                                
                                $statusColour = 'class="label label-warning"';
                  
                        elseif($finalData->agreement_status == 'Received'):
                        
                                $statusColour = 'class="label label-success"';
                   
                        elseif($finalData->agreement_status == 'Trial project' || $finalData->agreement_status == 'No Sow'):
                  
                                $statusColour = 'class="label label-primary"';
                      
                        else:
                  
                                $statusColour = 'class="label label-danger"';
                  
                        endif;
                  
                        
                       if($finalData->agreement_invoice_status == 'Sent Invoive'): // Invoice Status
                                
                                $invoiceColour = 'class="label label-warning"';
                  
                        elseif($finalData->agreement_invoice_status == 'Part Invoice Sent'):
                        
                                $invoiceColour = 'class="label label-primary"';
                   
                        elseif($finalData->agreement_invoice_status == 'Part Payment received'):
                  
                                  $invoiceColour = 'class="label label-success"';
                      
                        else:                  
                              
                                $invoiceColour = 'class="label label-danger"';
                  
                        endif;
                  
                          
                        if($key % 2 == 0){
                            
                               $trColor =   'class="table-info"';  
                            
                        }else{ 
                              
                               $trColor =   'class="table-success"';  
                        } 
                  
                        if($finalData->department == 'Architecture'):
                  
                            $department = "Architecture & Structure";
                       
                        else: 
                            
                            $department = $finalData->department;
                  
                        endif;
				  
				  
				  
				//  echo $finalData->agreement_date;
				 $contractDateBegin = date("Y-m-d");
				$contractDateEnd = $finalData->est_deliverable_dates;

				if($contractDateBegin > $contractDateEnd) {
				  
				  		$endDateStatusColour = 'class="label label-danger"';
				  
				} else {
				
						$endDateStatusColour = 'class="label label-info"';
					
				} 
                      
                  if($finalData->est_deliverable_dates !=''):
                  
                        $deliverable_date_with_text = date_format(date_create($finalData->est_deliverable_dates),"Y M d");
                  
                  else:
                  
                        $deliverable_date_with_text = $finalData->est_deliverable_text;
                  
                  endif;
                  
                  
                  ?>
                  
                   <tr <?=$trColor;?> id="sAId_<?=$servie_agreement_id?>" >
                    <td><?=$key+1;?></td>
					   <td><!--<?=$finalData->agreement_company;?>--> <a href="<?=base_url('service_agreement/agreementDetails?servie_agreement_id='.$servie_agreement_id.'')?>" data-toggle="tooltip" title="View"><?=$finalData->country_code;?> </a></td>
                    <!-- <td><?=$finalData->country_code;?></td> -->
				    <td><?=$finalData->project_code;?></td>
                      <td><span class="label label-info"><?=date_format($agreement_date,"Y M d");?></span></td>
                      <td><span <?=	$endDateStatusColour;?>><?php echo $deliverable_date_with_text; ?></span></td>  
                    <td title="<?=$finalData->client_name;?>"><?=character_limiter($finalData->client_name,8);?></td>
                <td title="<?=$finalData->project_name;?>"><?=character_limiter($finalData->project_name,8);?></td>
					   <td title="<?=$finalData->project_name;?>"><?=character_limiter($finalData->lead_owner,8);?></td>  
					  <td><?=ucfirst($finalData->service_type);?></td> 
				    <td><span <?=$statusColour;?>><?=$finalData->agreement_status;?></span></td>
                    <td title="<?=$department?>"><span class="text-primary"><b><?=character_limiter($department,5);?></b></span></td>
                    <td><span <?=$invoiceColour;?>><?=$finalData->agreement_invoice_status;?></span></td>   
                    <td><?=date_format($createdAt , "Y M d"); ?></td>
                     <td class="text-info"><b><?=$getEmpName;?></b></td>  
                    <td align="center">	
                     <?php if($this->session->userdata['logged_in_timesheet']['empId'] == $finalData->empId ||  $this->session->userdata['logged_in_timesheet']['user_type'] == 'admin' ||  $this->session->userdata['logged_in_timesheet']['empId'] == '47' ||  $this->session->userdata['logged_in_timesheet']['empId'] == '149'): ?>  
                        <?php if($finalData->service_type == 'monthly'): //update monthly service ?>
						<a href="<?=base_url('service_agreement/edit_monthly_service_details?servie_agreement_id='.$servie_agreement_id.'');?>" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a>  |
						<?php else:?>
						<a href="<?=base_url('service_agreement/edit_service_details?servie_agreement_id='.$servie_agreement_id.'');?>" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a> |
						<?php endif;?>
                     <?php endif; ?>    
                    <a href="<?=base_url('service_agreement/agreementDetails?servie_agreement_id='.$servie_agreement_id.'')?>" data-toggle="tooltip" title="View"><i class="fa fa-history"></i></a> 
                       <?php if($this->session->userdata['logged_in_timesheet']['username'] == 'ramakrishna' || $this->session->userdata['logged_in_timesheet']['username'] == 'rupali'):?>
                        | <a data-toggle="tooltip" title="Remove" style="cursor:pointer;" onClick="removeService(<?=$servie_agreement_id;?>)"><i class="fa fa-remove" aria-hidden="true"></i></a> 
						<?php endif; ?> 
					| <a href="<?=base_url('service_agreement/invoiceDetails?servie_agreement_id='.$servie_agreement_id.'')?>" data-toggle="tooltip" title="Invoice"><i class="fa fa-building"></i></a> 
					| <a href="<?=base_url('service_agreement/cloneData?servie_agreement_id='.$servie_agreement_id.'')?>" data-toggle="tooltip" title="Clone"><i class="fa fa-clone"></i></a>
				 </td>                     
				 </tr><?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
function removeService(sAId){ 
var answer = confirm ("Are you sure you want to delete service agreement?");
if (answer) {
        $.ajax({
                type: "POST",
                url: "<?php echo site_url('service_agreement/remove_service_agreement');?>",
                data: "sAId="+sAId,
				beforeSend: function() {
   						$('#sAId_'+sAId).html('<i class="fa fa-spinner"></i>');
 				 },success: function (response) { 
				       $("#sAId_"+sAId).remove("#sAId_"+sAId).html('');
			     }
            });
      }
}
	
		$("#downloadSow").click(function(){
  $(".exportSowInfo").table2excel({
    // exclude CSS class
    exclude: ".noExl",
    name: "Report for Service Agreements",
    filename: "service_agreement_details", //do not include extension
    fileext: ".xls", // file extension
	  exclude_links: true,
	  exclude_inputs: true,
	  preserveColors: "preserveColors"
	  
	  
  }); 
});
	
	 $(document).ready(function() {
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
                onSelect: function(selectedDate) {
                    if (this.id == 'form_date') {
                        var dateMin = $('#form_date').datepicker("getDate");
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
		//var today = $("#form_date").val();
		
		//var fridayDate = $("#to_date").val();
		
		$("#form_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			numberOfMonths: 1,
		});
		
		$("#to_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			numberOfMonths: 1,
		});
		
	})
</script>
<script src="<?php echo HTTP_JS_PATH; ?>jquery.table2excel.js"></script>
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
