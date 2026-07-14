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
                                                    <select class="form-control" id="client_Id1" name="client_Id[]" onChange="getProjects(this.value ,'1');">
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
                                                    <select class="form-control" id="project_Id1" name="project_Id[]" disabled="disabled" onchange="getProjectWiseTask(this.value,'1')">
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
                                                    <select class="form-control" id="task_Id1" name="task_Id[]" disabled="disabled">
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
                                                    <input class="form-control" type="text" id="emp_report_dates" name="emp_report_dates[]" placeholder="Select Date" readonly="">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Hours</label>
                                                    <select class="form-control" id="emp_time_hours" name="emp_time_hours[]" colspan="3">
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
                                                    <textarea class="form-control" id="comments" name="comments[]" rows="5"></textarea>
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

   //var disabledDates = ["2023-09-18", "2023-09-29","2023-10-02","2023-10-24","2023-11-13","2023-12-25"];
    
    var disabledDates = [""];
    
        jQuery('#add_timesheet').click(function() { 

            kn++;

            jQuery('#dynamic_field').append('<div id="row' + kn + '">' + '<div class="row"><div class="col-md-4"><div class="form-group">' + '<label class="control-label">Clients</label>' + '<select required class="form-control" id="client_Id' + kn + '" name="client_Id[]" onChange="getProjects(this.value,'+ kn +');"><option value="">Please select clients</option>' + '<?php foreach($getClientNames as $key => $clientName){ ?>' + '<option value="<?php echo $clientName->client_Id;?>"><?php echo ucfirst(str_replace("'"," ",$clientName->client_name));?></option>' + '<?php } ?>' + '</select></div></div>' + '<div class="col-md-4"><div class="form-group">' + '<label class="control-label">Projects</label>' + '<select class="form-control" id="project_Id' + kn + '" name="project_Id[]" disabled="disabled" onchange="getProjectWiseTask(this.value,'+ kn +');"><option value="">Please select project</option>' + '<?php foreach($getListOfProjects as $key => $projectName){ ?>' + '<option value="<?php echo $projectName->project_Id;?>"><?php echo ucfirst(str_replace("'"," ",$projectName->project_name));?></option>' + '<?php } ?>' + '</select></div></div>' + '<div class="row"><div class="col-md-4"><div class="form-group">' + '<label class="control-label">Task</label>' + '<select class="form-control" id="task_Id' + kn + '" name="task_Id[]"  disabled="disabled"><option value="">Please select task</option>' + '<?php foreach($getListOfTask as $key => $taskName){ ?>' + '<option value="<?php echo $taskName->task_Id;?>"><?php echo ucfirst(str_replace("'"," ",$taskName->task_name));?></option>' + '<?php } ?>' + '</select></div></div></div></div>' + '<div class="row"><div class="col-md-4"><div class="form-group"><label class="control-label">Date</label> <input class="form-control" type="text" id="emp_report_dates' + kn + '" name="emp_report_dates[]" placeholder="Select Date" readonly=""> </div>' + '<div class="form-group"><label class="control-label">Hours</label><select class="form-control" id="emp_time_hours' + kn + '" name="emp_time_hours[]" colspan="3"> <option value="">Please select hours</option>' + '<?php for ($i=0; $i<10;  $i += 0.5) { ?>' + '<option value="<?php echo $i;?>"><?php echo $i;?> </option>' + '<?php	}?>' + '<?php for ($i=10; $i<100;  $i += 1) { ?>' + '<option value="<?php echo $i;?>"><?php echo $i;?> </option>' + '<?php	}?>	' + '</select></div></div><div class="col-md-8"><div class="form-group"><label class=" control-label" for="textArea">Comments</label><textarea class="form-control" id="comments' + kn + '" name="comments[]" rows="5"></textarea></div></div><div class="form-group"><div class="col-sm-offset-2 col-sm-10 text-right"><button type="button" name="remove" id="' + kn + '" class="btn btn-danger btn_remove"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</button></div></div></div>');


            jQuery('#client_Id' + kn + ',' + '#project_Id' + kn + ',' + '#task_Id' + kn + ',' + '#emp_time_hours' + kn).select2(); // Autosuggest list               

    <?php //if(in_array($this->session->userdata['logged_in_timesheet']['empId'], $notenteredMemberlist)) : ?>
            
       <?php if($hideDateSection >= '2026-06-05') : ?>
            
            jQuery('#emp_report_dates' + kn).datepicker({
                dateFormat: 'yy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                minDate: "2026-06-01",
				maxDate : "2026-07-05",
               // maxDate: new Date()
                // minDate: '1m'
            });
            
            <?php else: ?>          
            
           jQuery('#emp_report_dates' + kn).datepicker({
                dateFormat: 'yy-mm-dd',               
                autoclose: true,               
                todayHighlight: true,               
                minDate: "2026-05-01",
				maxDate : "2026-06-05",
               // minDate: "2026-01-01",
				//maxDate : "2026-04-05",
			  // maxDate: new Date(),			   
                beforeShowDay: function(date){            
                var string = jQuery.datepicker.formatDate('yy-mm-dd', date);             
                return [ disabledDates.indexOf(string) == -1 ]             
        			}
            }); 
            
            <?php endif; ?>
            
            });


    /* DatePicker */
  
        $("form[name='add_emp_timelog']").validate({
            
            rules: {
                'client_Id[]': {
                    required: true
                },
                'project_Id[]': {
                    required: true
                },
                'task_Id[]': {
                    required: true
                },
                'emp_report_dates[]': {
                    required: true
                },
                'emp_time_hours[]': {
                    required: true
                },
                'comments[]': {
                    required: true
                },
                team_member_type: {
                    required: true
                }

            },
            messages: {
                'client_Id[]': "Please Select Client Name",
                'project_Id[]': "Please Select Project Name",
                'task_Id[]': "Please Select Task Name",
                'emp_report_dates[]': "Please Select Date",
                'emp_time_hours[]': "Please Select Time",
                'comments[]': "Please Enter Comments",
                team_member_type: "Please Select Team Member Type"
            },
            submitHandler: function(form) {
                //$("#hideAftersumitButton").attr("disabled", true);
                $("#hideAftersumitButton").html('<i style="color:#009688; font-size:22px;" class="fa fa-spinner" aria-hidden="true"><span> Please wait while we process your request...</span></i>');
                form.submit();
            }
        });

    
  <?php //if(in_array($this->session->userdata['logged_in_timesheet']['empId'], $notenteredMemberlist)) : ?>
   
    <?php if($hideDateSection >= '2026-06-05') : ?>
    
    jQuery('#emp_report_dates').datepicker({
        dateFormat: 'yy-mm-dd',        
        autoclose: true,         
        todayHighlight: true,        
        minDate: "2026-06-01",
		 maxDate : "2026-07-05",
        //maxDate: new Date()
    });
    
 <?php else: ?>
    
    jQuery('#emp_report_dates').datepicker({
        dateFormat: 'yy-mm-dd',        
        autoclose: true,         
        todayHighlight: true,        
         minDate: "2026-05-01",
		 maxDate : "2026-06-05",
         //minDate: "2026-01-01",
		 //maxDate : "2026-04-05",
         //maxDate: new Date(),
         beforeShowDay: function(date){			 
          var string = jQuery.datepicker.formatDate('yy-mm-dd', date);             
			 return [ disabledDates.indexOf(string) == -1 ]
        }
        
    });
    
<?php endif; ?>    

    /* DatePicker */

    /*Ajax Based dropdown option changes on Clients , Projects and Tasks*/

    function getProjects(client_Id,autoPID) { // Getting client wise projects based on client id
        //alert(autoID);
        jQuery.ajax({
            type: "POST",
            url: "<?php echo base_url('empreports/getListOfProjectsWithClient');?>",
            data: 'client_Id=' + client_Id,
            success: function(data) {
                $("#project_Id"+autoPID).html(data);
                $('#project_Id'+autoPID).removeAttr('disabled');

            }
        });
    }

    function getProjectWiseTask(project_Id,autoTID) { // Getting projects wise task based on project id
        // var i= 1;
        jQuery.ajax({
            type: "POST",
            url: "<?php echo base_url('empreports/getProjectsTask');?>",
            data: 'project_Id=' + project_Id,
            success: function(data) {
                $("#task_Id"+autoTID).html(data);
                $('#task_Id'+autoTID).removeAttr('disabled');
            }
        });
    }

    /* Ajax Based dropdown option changes on Clients , Projects and Tasks*/

   jQuery('#client_Id1,#project_Id1,#task_Id1,#emp_time_hours').select2(); // Autosuggest list
    
    jQuery(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            var res = confirm('Are You Sure You Want To Delete This?');
            if (res == true) {
                $('#row' + button_id + '').remove();
                $('#' + button_id + '').remove();
            }
        });
    
</script>
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->