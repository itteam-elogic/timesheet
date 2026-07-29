<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<?php 

   $getClientNames      		= $this->client_model->getClientName(); // List of Clients
  
   $taskClientId                =  $this->uri->segment('5');
  
   $getListOfProjects   		= $this->project_model->getProjectName($taskClientId); // List of Clients
  
   // $getListOfEmployees   	= $this->timesheet_login->getEmployeeName(); // List of Clients
  
   $taskProjectId = $this->uri->segment('4');
  
   $getListOfTask		   	= $this->task_model->getTaskName($taskProjectId); // List of Clients

   	$getUpdateId = $this->uri->segment('3');

    $hideDateSection = date('Y-m-d');

    $notenteredMemberlist = array("136"); // Members array list
  			
?>

<div class="content-wrapper">
 
    <div class="page-title">
        <div>
            <h1><i class="fa fa-clock-o"></i> Add Report </h1>
        </div>
        <div> <a class="btn btn-primary btn-flat" href="<?php echo base_url();?>empreports" data-toggle="tooltip" title="Go To Report Log!"><i class="fa fa-chevron-circle-left"></i></a> </div>
    </div>
    <div class="card">
        <h3 class="card-title"></h3>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="bs-component">
                        <div class="tab-content" id="myTabContent">
                            <!-- Employee Report adding block -->
                            <form class="" name="add_emp_timelog" id="add_emp_timelog" method="post" action="<?php echo base_url('empreports/add_emp_records');?>">
                                <div class="tab-pane fade active in" id="Add">
                                    <div id="dynamic_field">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Clients</label>
                                                    <select class="form-control" id="client_Id1" name="client_Id[1]" onChange="getProjects(this.value ,'1');">
                                                        <option value="">Please select clients</option>
                                                        <?php foreach($getClientNames as $key => $clientName): ?>
                                                        <option value="<?php echo $clientName->client_Id;?>"><?php echo ucfirst($clientName->client_name);?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Projects</label>
                                                    <select class="form-control" id="project_Id1" name="project_Id[1]" disabled="disabled" onchange="getProjectWiseTask(this.value,'1')">
                                                        <option value="">Please select project</option>
                                                        <?php foreach($getListOfProjects as $key => $projectName): ?>
                                                        <option value="<?php echo $projectName->project_Id;?>"><?php echo ucfirst($projectName->project_name);?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Task</label>
                                                    <select class="form-control" id="task_Id1" name="task_Id[1]" disabled="disabled">
                                                            <option value="">Please select task</option>
                                                            <?php foreach($getListOfTask as $key => $taskName): ?>
                                                            <option value="<?php echo $taskName->task_Id;?>"><?php echo ucfirst($taskName->task_name);?></option>
                                                            <?php endforeach; ?>
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">

                                                <div class="form-group">
                                                    <label class="control-label">Date</label>
                                                    <input class="form-control" type="text" id="emp_report_dates1" name="emp_report_dates[1]" placeholder="Select Date" readonly="">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Hours</label>
                                                    <select class="form-control" id="emp_time_hours1" name="emp_time_hours[1]" colspan="3">
                                                        <option value="">Please select hours</option>
                                                        <?php for ($i=0; $i<24.5;  $i += 0.5) { ?>
                                                        <option value="<?php echo $i;?>"><?php echo $i;?> </option>
                                                        <?php	}?>
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label class=" control-label" for="textArea">Comments</label>
                                                    <textarea class="form-control" id="comments1" name="comments[1]" rows="5"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10 text-right">
                                                <button type="button" name="add_timesheet" id="add_timesheet" class="btn btn-success"><i class="fa fa-fw fa-lg fa-plus-circle"></i> Add More</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Radios</label>
                                                <div class="radio">
                                                    <label>
                                                        <input id="team_member_type1" type="radio" name="team_member_type" value="Regular" checked="checked">
                                                        Regular </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input id="team_member_type2" type="radio" name="team_member_type" value="Substitute">
                                                        Substitute </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input id="team_member_type3" type="radio" name="team_member_type" value="Back Up">
                                                        Back Up </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-footer" id="hideAftersumitButton">
                                        <button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Apply</button>
                                        <a href="<?php echo base_url();?>empreports" data-toggle="Go To Report Log!" title="Cancel">
                                            <button class="btn btn-default icon-btn" type="button"><i class="fa fa-chevron-circle-left"></i>Back</button>
                                        </a>
                                    </div>
                                </div>
                            </form>
                            <!-- Employee Report adding block -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
       
</div>

<script type="text/javascript">
    
    var kn = 1;
    var disabledDates = [""];

    // Unique indexed names (client_Id[1], client_Id[2], ...) so each Add More row is validated.
    function addTimesheetRowRules(rowIndex) {
        jQuery('#client_Id' + rowIndex).rules('add', {
            required: true,
            messages: { required: "Please Select Client Name" }
        });
        jQuery('#project_Id' + rowIndex).rules('add', {
            required: true,
            messages: { required: "Please Select Project Name" }
        });
        jQuery('#task_Id' + rowIndex).rules('add', {
            required: true,
            messages: { required: "Please Select Task Name" }
        });
        jQuery('#emp_report_dates' + rowIndex).rules('add', {
            required: true,
            messages: { required: "Please Select Date" }
        });
        jQuery('#emp_time_hours' + rowIndex).rules('add', {
            required: true,
            messages: { required: "Please Select Time" }
        });
        jQuery('#comments' + rowIndex).rules('add', {
            required: true,
            messages: { required: "Please Enter Comments" }
        });
    }

    function initTimesheetDatepicker(selector) {
        <?php if($hideDateSection >= '2026-06-05') : ?>
        jQuery(selector).datepicker({
            dateFormat: 'yy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            minDate: "2026-06-01",
            maxDate: "2026-07-05"
        });
        <?php else: ?>
        jQuery(selector).datepicker({
            dateFormat: 'yy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            minDate: "2026-05-01",
            maxDate: "2026-06-05",
            beforeShowDay: function(date) {
                var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
                return [disabledDates.indexOf(string) == -1];
            }
        });
        <?php endif; ?>
    }

    jQuery('#add_timesheet').click(function() {
        kn++;

        jQuery('#dynamic_field').append(
            '<div id="row' + kn + '">' +
                '<div class="row">' +
                    '<div class="col-md-4"><div class="form-group">' +
                        '<label class="control-label">Clients</label>' +
                        '<select class="form-control" id="client_Id' + kn + '" name="client_Id[' + kn + ']" onChange="getProjects(this.value,' + kn + ');">' +
                            '<option value="">Please select clients</option>' +
                            '<?php foreach($getClientNames as $key => $clientName){ ?>' +
                            '<option value="<?php echo $clientName->client_Id;?>"><?php echo ucfirst(str_replace("'"," ",$clientName->client_name));?></option>' +
                            '<?php } ?>' +
                        '</select>' +
                    '</div></div>' +
                    '<div class="col-md-4"><div class="form-group">' +
                        '<label class="control-label">Projects</label>' +
                        '<select class="form-control" id="project_Id' + kn + '" name="project_Id[' + kn + ']" disabled="disabled" onchange="getProjectWiseTask(this.value,' + kn + ');">' +
                            '<option value="">Please select project</option>' +
                            '<?php foreach($getListOfProjects as $key => $projectName){ ?>' +
                            '<option value="<?php echo $projectName->project_Id;?>"><?php echo ucfirst(str_replace("'"," ",$projectName->project_name));?></option>' +
                            '<?php } ?>' +
                        '</select>' +
                    '</div></div>' +
                    '<div class="col-md-4"><div class="form-group">' +
                        '<label class="control-label">Task</label>' +
                        '<select class="form-control" id="task_Id' + kn + '" name="task_Id[' + kn + ']" disabled="disabled">' +
                            '<option value="">Please select task</option>' +
                            '<?php foreach($getListOfTask as $key => $taskName){ ?>' +
                            '<option value="<?php echo $taskName->task_Id;?>"><?php echo ucfirst(str_replace("'"," ",$taskName->task_name));?></option>' +
                            '<?php } ?>' +
                        '</select>' +
                    '</div></div>' +
                '</div>' +
                '<div class="row">' +
                    '<div class="col-md-4">' +
                        '<div class="form-group">' +
                            '<label class="control-label">Date</label>' +
                            '<input class="form-control" type="text" id="emp_report_dates' + kn + '" name="emp_report_dates[' + kn + ']" placeholder="Select Date" readonly="">' +
                        '</div>' +
                        '<div class="form-group">' +
                            '<label class="control-label">Hours</label>' +
                            '<select class="form-control" id="emp_time_hours' + kn + '" name="emp_time_hours[' + kn + ']" colspan="3">' +
                                '<option value="">Please select hours</option>' +
                                '<?php for ($i=0; $i<24.5;  $i += 0.5) { ?>' +
                                '<option value="<?php echo $i;?>"><?php echo $i;?> </option>' +
                                '<?php } ?>' +
                            '</select>' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-md-8"><div class="form-group">' +
                        '<label class="control-label" for="textArea">Comments</label>' +
                        '<textarea class="form-control" id="comments' + kn + '" name="comments[' + kn + ']" rows="5"></textarea>' +
                    '</div></div>' +
                '</div>' +
                '<div class="form-group">' +
                    '<div class="col-sm-offset-2 col-sm-10 text-right">' +
                        '<button type="button" name="remove" id="' + kn + '" class="btn btn-danger btn_remove"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</button>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );

        jQuery('#client_Id' + kn + ', #project_Id' + kn + ', #task_Id' + kn + ', #emp_time_hours' + kn).select2();
        initTimesheetDatepicker('#emp_report_dates' + kn);
        addTimesheetRowRules(kn);
    });

    $("form[name='add_emp_timelog']").validate({
        ignore: ':hidden:not(.select2-hidden-accessible)',
        rules: {
            'client_Id[1]': { required: true },
            'project_Id[1]': { required: true },
            'task_Id[1]': { required: true },
            'emp_report_dates[1]': { required: true },
            'emp_time_hours[1]': { required: true },
            'comments[1]': { required: true },
            team_member_type: { required: true }
        },
        messages: {
            'client_Id[1]': "Please Select Client Name",
            'project_Id[1]': "Please Select Project Name",
            'task_Id[1]': "Please Select Task Name",
            'emp_report_dates[1]': "Please Select Date",
            'emp_time_hours[1]': "Please Select Time",
            'comments[1]': "Please Enter Comments",
            team_member_type: "Please Select Team Member Type"
        },
        errorPlacement: function(error, element) {
            if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('.select2'));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            $("#hideAftersumitButton").html('<i style="color:#009688; font-size:22px;" class="fa fa-spinner" aria-hidden="true"><span> Please wait while we process your request...</span></i>');
            form.submit();
        }
    });

    initTimesheetDatepicker('#emp_report_dates1');

    function getProjects(client_Id, autoPID) {
        jQuery.ajax({
            type: "POST",
            url: "<?php echo base_url('empreports/getListOfProjectsWithClient');?>",
            data: 'client_Id=' + client_Id,
            success: function(data) {
                $("#project_Id" + autoPID).html(data);
                $('#project_Id' + autoPID).removeAttr('disabled').trigger('change.select2');
            }
        });
    }

    function getProjectWiseTask(project_Id, autoTID) {
        jQuery.ajax({
            type: "POST",
            url: "<?php echo base_url('empreports/getProjectsTask');?>",
            data: 'project_Id=' + project_Id,
            success: function(data) {
                $("#task_Id" + autoTID).html(data);
                $('#task_Id' + autoTID).removeAttr('disabled').trigger('change.select2');
            }
        });
    }

    jQuery('#client_Id1,#project_Id1,#task_Id1,#emp_time_hours1').select2();

    jQuery(document).on('click', '.btn_remove', function() {
        var button_id = jQuery(this).attr("id");
        var res = confirm('Are You Sure You Want To Delete This?');
        if (res == true) {
            jQuery('#row' + button_id).remove();
        }
    });
    
</script>
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->