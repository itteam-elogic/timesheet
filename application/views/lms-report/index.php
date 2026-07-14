<!-- Inlude Header here -->
<?php 
$this->load->view('includes/cRMHeader'); 
	
	$monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
 	
	$friday = date( 'Y-m-d', strtotime( 'saturday this week' ) );
  
     $getWeekDates = $this->defaulter_model->weeklyDays(); 

   $listofManagerProjects = $this->defaulter_model->getMangerProjects();
                                    
//    echo '<pre>'; print_r($payload);echo '<pre>'; print_r($report);exit;

  $report_from_date = $this->input->post('report_from_date'); //From dates

   	$report_to_date = $this->input->post('report_to_date');
   
    
	$from_date = new DateTime($report_from_date);

	$to_date = new DateTime($report_to_date);

?>
<div class="content-wrapper">
	<div class="page-title">
		<div>
		<?php if($report_from_date){  ?>
        <h1>Video Report : <?php 
            echo date_format(date_create($report_from_date), "d M, Y").'  To  '. date_format(date_create($report_to_date), "d M, Y");
            ?></h1>
        <?php }else{ ?>
            <h1>Video Report</h1>
            <?php } ?>
		</div>
		<div> <a class="btn btn-primary btn-flat" href="<?php echo base_url('/home');?>" data-toggle="tooltip" title="refresh"><i class="fa fa-chevron-circle-left"></i></a> </div>
	</div>
	<div class="card">
		<h3 class="card-title"></h3>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<div class="bs-component">
						<div class="tab-content" id="myTabContent">
							<form class="" name="reportForm" id="reportForm" method="post" action="<?php echo base_url('lmsreport/fetchReportByEmp');?>">
								<div class="tab-pane fade active in" id="Add">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Employee Name</label>
												<select class="form-control" id="empId" name="empId[]" multiple>
													<?php if($empDet['empId']){ ?>
                                                     <option value="<?=$empDet['empId']?>" selected><?=$empDet['name']?></option>
                                                     <?php } ?>

												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Video</label>
												<select class="form-control" id="videoId" name="videoId[]" multiple>
													<?php 
//                                                    echo "<pre>";print_r($report);exit;
                                                    if(!empty($videoDet['videoId'])){ ?>
                                                     <option value="<?=$videoDet['videoId']?>" selected><?=$videoDet['videoName']?></option>
                                                     <?php } ?>

												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Start Date</label>
												<input class="form-control" type="text" id="report_from_date" name="report_from_date" placeholder="Select From Date" value="<?=!empty($payload['report_from_date']) ? $payload['report_from_date']: NULL?>" readonly="">
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">End Date</label>
												<input class="form-control" type="text" id="report_to_date" name="report_to_date" placeholder="Select To Date" 
                                                value="<?=!empty($payload['report_to_date']) ? $payload['report_to_date']: NULL?>" readonly="">
											</div>
										</div>
									</div>
									<div  class="col-md-4" style="float: right;text-align:right">
									<div class="form-group">	
                                        <button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Search</button>
                                        <button type="reset" class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Clear</button>
                                    </div>
										 </div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
            <div class="text-muted mx-auto">
            <span class="badge badge-warning">NOTE:</span> To Select All Videos/Employee Keep it Empty it will be consider for All.
        </div>
		</div>
	</div> 
	<!-- Get List of user not filled report log on date wise -->
    <?php if($report && $report['report']){ ?>

    <div class="card">

<div class="card-body">
    <div class="page-title">
        <div>
        <?php if(!empty($payload['empId'])){ ?>
            <h1>Video Report By Employee# <?php
            if(!empty($payload['empid']) && count($payload['empid']) == 1){ 
                echo $report['report'] ? $report['report'][0]['empName']: null;
            }
                ?></h1>
            <?php }elseif(empty($payload['empId']) && !empty($payload['videoId'])){ 
                
                ?>
                <h1>Employee  Report By Video# <?php
                if(!empty($payload['videoId']) && count($payload['videoId']) == 1){ 
                    echo $report['report'] ? $report['report'][0]['videoName']: null;
                }?></h1>
            <?php } ?>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="table2excel">
                    <thead>
                        <tr>
                            <!-- <th style="background-color:#c1c1c1">Sno</th> -->
                            <?php if(!empty($payload['empId'])){ ?>
                                <th style="background-color:#c1c1c1">Video & Employee Name</th>
                            <?php }elseif(empty($payload['empId']) && !empty($payload['videoId'])){ ?>
                                <th style="background-color:#c1c1c1">Video & Employee Name</th>
                            <?php }elseif(empty($payload['empId']) && empty($payload['videoId'])){ ?>
                                <th style="background-color:#c1c1c1">Video & Employee Name</th>
                            <?php } ?>
                            <th style="background-color:#c1c1c1">Started Date</th>
                            <th style="background-color:#c1c1c1">Completed Date</th>
                            <th style="background-color:#c1c1c1">Video Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $cntNumber = 1;
                             //Getting the current week days
                        foreach($report['report'] as $key => $video){ 
                            // echo "$key<pre>";print_r($video);
                        ?>
                        <tr>
                        <?php if(!empty($payload['empId'])){ ?>
                            <!-- <td>
                                Video#<?php echo $video['videoId'];?>
                            </td> -->
                            <td>
                            <?php echo $video['videoName'];?>&nbsp;&&nbsp;<?php echo $video['empName'];?>
                            </td>
                            <?php }elseif(empty($payload['empId']) && !empty($payload['videoId'])){ ?>
                                <!-- <td>
                                Employee#<?php echo $video['empId'];?>
                            </td> -->
                            <td>
                            <?php echo $video['videoName'];?>&nbsp;&&nbsp;<?php echo $video['empName'];?>
                            </td>
                            <?php }elseif(empty($payload['empId']) && empty($payload['videoId'])){ ?>
                                <!-- <td>
                                Video#<?php echo $video['videoId'];?>&nbsp;&&nbsp;Employee#<?php echo $video['empId'];?>
                            </td> -->
                            <td>
                            <?php echo $video['videoName'];?>&nbsp;&&nbsp;<?php echo $video['empName'];?>
                            </td>
                            <?php } ?>
                        
                            <td><?php  echo $video['started_date']; ?></td>
                            <td><?php  echo $video['completed_date']; ?></td>
                            <td><?php  
                            $progressValue = ($video['spent_time'] && $video['video_duration']) ? round(($video['spent_time'] / $video['video_duration']) * 100) : 0;

                            $leftVal = round(100-$progressValue);
                             ?>
                            <div class="col-12">
                                <div class="progress" style="height: 15px">
                                <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar"  
                                    style="width: <?=$progressValue?>%" 
                                    aria-valuenow="<?=$progressValue?>" 
                                    aria-valuemin="0" aria-valuemax="100"><?=$progressValue?>%</div>
                                    <div class="progress-bar progress-bar-striped bg-warning progress-bar-animated" role="progressbar" 
                                    aria-valuenow="<?=$leftVal?>" 
                                    aria-valuemin="0" aria-valuemax="100"
                                    style="width: <?=$leftVal?>%"><span><?=$leftVal?>% left</span></div>
                                    
                                </div>
                                <!-- <?=$leftVal?>% -->
                            </div> 
                            </td>
                        </tr>
                        <?php $cntNumber++; } ?>			
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Displaying Search Result -->
    </div>
</div>
</div>
<?php } ?>
</div>
<script language="javascript" type="text/javascript">
	/* DatePicker */
	$(function() {
		$("form[name='reportForm']").validate({
			rules: {
				empId: {
					required: function(element) {
                        return $("#videoId").is(':empty');
                    }
				},
				videoId: {
					required: function(element) {
                        return $("#empId").is(':empty');
                    }
				},
				report_from_date: {
					required: true
				},
				report_to_date: {
					required: true
				}
			},
			messages: {
				empId: "Please Enter Employee Name",
				report_from_date: "Please Select From Date",
				report_to_date: "Please Select To Date"
			},
			submitHandler: function(form) {
				form.submit();
			}
		});
	});

	/*Ajax Based dropdown option changes on Clients , Projects and Tasks*/

	$(document).ready(function() {
		var today = $("#report_from_date").val();
          var currentYear = new Date().getFullYear();
            var minDate = new Date(2015, 0, 1); // January 1, 2015
            var maxDate = new Date(); // Current date
		var fridayDate = $("#report_to_date").val();

		$("#report_from_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
               changeYear: true,
                yearRange: '2015:' + currentYear,
                minDate: minDate,
                maxDate: maxDate,
			numberOfMonths: 1,
            onSelect: function (date) {
                var dt2 = $('#report_to_date');
                var startDate = $(this).datepicker('getDate');
                var minDate = $(this).datepicker('getDate');
                dt2.datepicker('setDate', minDate);
                startDate.setDate(startDate.getDate() + 7);
                dt2.datepicker('option', 'maxDate', startDate);
                dt2.datepicker('option', 'minDate', minDate);
            }
		});

		$("#report_to_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
               changeYear: true,
                yearRange: '2015:' + currentYear,
                minDate: minDate,
                maxDate: maxDate,
			numberOfMonths: 1,
		});

	})

	$('#empId').select2({
        language: {
            searching: function() {
                return "Please enter search key";
            }
        },
        placeholder: "Start typing, or scroll to name",
        ajax: {
            type: "POST",
            dataType: 'json',
            url: '<?=base_url()?>/lmsreport/fetchEmployeeByKey',		
            processResults: function (data) {
                return {
                    results: data ? $.map(data, function(obj, index) {return { id: obj.empId, text: obj.name };}) : []
                };
            }
        }
    }); // Autosuggest list
    $('#videoId').select2({
        language: {
            searching: function() {
                return "Please enter search key";
            }
        },
        placeholder: "Start typing, or scroll to name",
        ajax: {
            type: "POST",
            dataType: 'json',
            url: '<?=base_url()?>/lmsreport/fetchVideosByKey',		
            processResults: function (data) {
                return {
                    results: data ? $.map(data, function(obj, index) {return { id: obj.videoId, text: obj.videoName };}) : []
                };
            }
        }
    }); // Autosuggest list
    
</script>

<?php $this->load->view('includes/cRMFooter'); ?>
