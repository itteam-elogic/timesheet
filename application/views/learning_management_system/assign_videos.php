<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
 

?>
<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h1>Assign Videos</h1>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <label class="control-label col-md-3">Video Name : </label>
                    <div class="col-md-4">
                        <?php echo $videoInfo->lms_video_name; ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <label class="control-label col-md-3">Video Type : </label>
                    <div class="col-md-4">
                        <?php echo $videoInfo->lms_video_type; ?>
                    </div>
                </div>
            </div>
            <!-- <div class="row">
                <div class="col-xs-6 col-md-4"><label class="control-label col-md-4">Video Type#</label> <?php echo $videoInfo->lms_video_type; ?></div>
                <div class="col-xs-6 col-md-8">Description# <?php echo $videoInfo->lms_desc; ?></div>
            </div> -->
                
            </div>
        </div>
    </div>
    <div class="col-md-8">
    <div class="card" style="padding: 0px;">
            <div class="card-body">
        <form id="assignmentForm" class="form-horizontal" action="<?php echo site_url('lms/assignVideo'); ?>" method="post">
            <input type="hidden" name="video_id" value="<?=$videoInfo->videoId?>">
            
            <div class="form-group col-md-12" >
                <label class="control-label col-md-12" style="float: left; text-align:left"  for="selectAllEmployees">
                    <input type="checkbox" id="selectAllEmployees" name="select_all" onchange="toggleEmployeeSelection()">
                    Assign to All Employees
                </label>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3">Select Employees : <span class="required-star">*</span> </label>
                <div class="col-md-9">
                    <select id="employeeSelect" name="employee_ids[]" style="width: 100%;" multiple>
                    <?php foreach ($employees as $employee): ?>
                    <option value="<?php echo $employee->empId; ?>"><?php echo $employee->name; ?></option>
                    <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <!-- <div class="form-group">
                <label class="control-label col-md-3">Select Video : <span class="required-star">*</span> </label>
                <div class="col-md-4">
                <select id="videoSelect" name="video_id" style="width: 100%;">
                    <?php foreach ($videos as $video): ?>
                        <option value="<?php echo $video->id; ?>"><?php echo $video->title; ?></option>
                    <?php endforeach; ?>
                </select>
                </div>
            </div> -->

            <div class="form-group">
                <label class="control-label col-md-3">Completion Date : <span class="required-star">*</span> </label>
                <div class="col-md-9">
                <!-- <input type="date" id="completionDate" name="completion_date"> -->
                <input class="form-control" type="text" 
                id="completionDate" name="completion_date" 
                placeholder="Select Completion Date" readonly="">
                </div>
            </div>

            <div>
                <div class="row" style="padding: 5px; float:right">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>ALlocate</button>
                        <a class="btn btn-default icon-btn" href="<?php echo base_url('lms');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Go Back</a>
                    </div>
                </div>
            </div>
        </form>
            </div></div>     
    </div>
    <div class="col-md-4">
        <div class="card" style="padding: 0px;">
            <div class="card-body" style="max-height: 432px;overflow: auto;overflow-x: hidden;">
            <table class="table table-striped "> 
            <thead> 
                <tr> 
                    <th>Allocated To</th> 
                    <th>Completion Date</th>
                </tr> 
            </thead> 
            <tbody> 
                <?php foreach($assignments as $key => $assignment) {?>
                <tr style="background-color: <?=empty($assignment->name) ? 'yellow' : 'none'?>;">
                <td ><?=!empty($assignment->name) ? $assignment->name: 'All Employees'?></td>
                <td>
                    <?=date_format(date_create($assignment->completion_date), 'd M, Y')?>
                </td>
                </tr>
                <?php  } ?>
            </tbody>
            </table>
      </div>
                            </div>
                        </div>
    <div class="col-md-12" style="display: none;">
        <div class="card">
            <div class="card-body">
                <canvas id="videoChart" width="800" height="400"></canvas>
            </div>
        </div>
    </div>


</div>
<!-- Organizatoin form validation -->
<script>
 
$(document).ready(function () {
            // Initialize Select2 for employee selection
            $('#employeeSelect').select2();
            
            // Initialize jQuery Validate plugin
            $('#assignmentForm').validate({
                rules: {
                    video_id: {
                        required: true
                    },
                    completion_date: {
                        required: true,
                        date: true
                    },
                    'employee_ids[]': {
                        required: function(element) {
                            return !$('#selectAllEmployees').prop('checked');
                        }
                    }
                },
                messages: {
                    video_id: {
                        required: "Please select a video."
                    },
                    completion_date: {
                        required: "Please enter the completion date.",
                        date: "Please enter a valid date."
                    },
                    'employee_ids[]': {
                        required: "Please select at least one employee."
                    }
                }
            });
            $("#completionDate").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			numberOfMonths: 1,
            minDate: 0,
            });

        });
        
        function toggleEmployeeSelection() {
            var selectAllCheckbox = $('#selectAllEmployees');
            var employeeSelect = $('#employeeSelect');
            
            if (selectAllCheckbox.prop('checked')) {
                employeeSelect.prop('disabled', true);
                employeeSelect.val('').trigger('change');
            } else {
                employeeSelect.prop('disabled', false);
            }
        }
</script>


<style>
    .mandaysradio {
        position: absolute;
        margin-top: 13px;
    }
</style>
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->