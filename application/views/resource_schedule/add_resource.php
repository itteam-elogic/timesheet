<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<?php 

   $getClientNames      		= $this->client_model->getManagerwiseClients(); // List of Clients
  
   $taskClientId                =  $this->uri->segment('5');
  
   $getListOfProjects   		= $this->project_model->getProjectName($taskClientId); // List of Clients
  
   // $getListOfEmployees   	= $this->timesheet_login->getEmployeeName(); // List of Clients
  
   $taskProjectId = $this->uri->segment('4');
  
   $getListOfTask		   	= $this->task_model->getTaskName($taskProjectId); // List of Clients

   	$getUpdateId = $this->uri->segment('3');

    $hideDateSection = date('Y-m-d');

    $notenteredMemberlist = array("421"); // Members array list

	$memberLoginID = $this->session->userdata['logged_in_timesheet']['empId'];
	//Department select by default Manger wise department.

		if(in_array($memberLoginID, array('146','230','149','245','372','227','475','248','371','391','339','455'))):
	
				$department  = 'MEP-Mechanical';
				$bgColour = 'style="background:rebeccapurple; color:white; font-weight: bold"';
		
		elseif(in_array($memberLoginID, array('41','47','71','182','136','64','168','197'))):
	
				$department  = 'Architectural';
				$bgColour = 'style="background:rebeccapurple; color:white; font-weight: bold"';

		elseif(in_array($memberLoginID, array('53','155','108'))):
	
				$department  = '3D Visualization';
				$bgColour = 'style="background:rebeccapurple; color:white; font-weight: bold"';

		elseif(in_array($memberLoginID, array('394'))):
	
				$department  = 'Structural';
				$bgColour = 'style="background:rebeccapurple; color:white; font-weight: bold"';

		else: 
			   $department  = 'MEP-Mechanical';
			   $bgColour = 'style="background:rebeccapurple; color:white; font-weight: bold"';
				

	   endif;
  			
?>

<div class="content-wrapper">
 
    <div class="page-title">
        <div>
            <h1><i class="fa fa-clock-o"></i> Resource Schedule </h1>
        </div>
        <div> <a class="btn btn-primary btn-flat" href="<?php echo base_url();?>resource_schedule/add" data-toggle="tooltip" title="Go To Report Log!"><i class="fa fa-chevron-circle-left"></i></a> </div>
    </div>
    <div class="card">
        <h3 class="card-title"></h3>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="bs-component">
                        <div class="tab-content" id="myTabContent">
                            <!-- Employee Report adding block -->
                            <form class="" name="add_resource" id="add_resource" method="post" action="<?php echo base_url('resource_schedule/add_resource');?>">
                                <div class="tab-pane fade active in" id="Add">
                                    <div id="dynamic_field">
                                        <div class="row">
											<div class="col-md-3">
                                                    <label class="control-label">Date</label>
                                                    <input class="form-control" type="text" id="emp_report_dates" name="emp_report_dates" placeholder="Select Date" readonly="" value="<?php echo date("Y-m-d");?>" style="background:rebeccapurple; color:white; font-weight: bold">
                                               </div>
											 <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Department</label>
                                                   <select class="form-control" id="department" name="department">
														<option value=""></option>
														<option value="Architectural" <?=$department == 'Architectural' ? 'selected="selected"' : '';?>>Architectural</option>
														<option value="Structural" <?=$department == 'Structural' ? 'selected="selected"' : '';?>>Structural</option>
														<option value="2D Auto CAD" <?=$department == '2D Auto CAD' ? 'selected="selected"' : '';?>>2D Auto CAD</option>
													   <option value="MEP-Mechanical" <?=$department == 'MEP-Mechanical' ? 'selected="selected"' : '';?>>MEP-Mechanical</option>
													   <option value="MEP-Electrical" <?=$department == 'MEP-Electrical' ? 'selected="selected"' : '';?>>MEP - Electrical</option>
													   <option value="MEP-Plumbing" <?=$department == 'MEP-Plumbing' ? 'selected="selected"' : '';?>>MEP-Plumbing</option>
														<option value="3D Visualization" <?=$department == '3D Visualization' ? 'selected="selected"' : '';?>>3D Visualization</option>
														<!-- <option value="Middle East" <?=$department == 'Middle East' ? 'selected="selected"' : '';?>>Middle East</option> -->
													</select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
											<div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Clients</label>
                                                    <select class="form-control" id="client_Id1" name="client_Id[]" onChange="getProjects(this.value ,'1');">
                                                        <option value=""></option>
                                                        <?php foreach($getClientNames as $key => $clientName): ?>
                                                        <option value="<?php echo $clientName->client_Id;?>"><?php echo ucfirst($clientName->client_name);?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
											 <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Projects</label>
                                                    <select class="form-control" id="project_Id1" name="project_Id[]" disabled="disabled" onchange="getProjectWiseTask(this.value,'1')">
                                                        <option value=""></option>
                                                        <?php foreach($getListOfProjects as $key => $projectName): ?>
                                                        <option value="<?php echo $projectName->project_Id;?>"><?php echo ucfirst($projectName->project_name);?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
											 <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Task</label>
                                                    <select class="form-control" id="task_Id1" name="task_Id[]" disabled="disabled">
                                                            <option value=""></option>
                                                            <?php foreach($getListOfTask as $key => $taskName): ?>
                                                            <option value="<?php echo $taskName->task_Id;?>"><?php echo ucfirst($taskName->task_name);?></option>
                                                            <?php endforeach; ?>
                                                        
                                                    </select>
                                                </div>
                                            </div>
											<div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Team Member</label>
                                                    <select class="form-control" id="team_member1" name="team_member[]">
                                                          <option value=""></option>
															<?php foreach($this->project_model->teamMembers() as $Mteam): ?>
																<option value="<?php echo $Mteam->empId;?>"><?php echo $Mteam->name;?></option>
															<?php endforeach; ?>                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-1">                                                
                                                <div class="form-group">
                                                    <label class="control-label">Hours</label>
                                                    <select class="form-control" id="emp_time_hours" name="emp_time_hours[]" colspan="3">
                                                        <option value=""></option>
                                                        <?php for ($i=0; $i<24.5;  $i += 0.5) { ?>
                                                        <option value="<?php echo $i;?>"><?php echo $i;?> </option>
                                                        <?php	}?>
                                                        
                                                    </select>
                                                </div>
                                            </div>
											<div class="col-md-1">                                                
                                                <div class="form-group">
                                                    <label class="control-label">Workplace</label>
                                                    <select class="form-control" id="workplace" name="workplace[]" colspan="3">
														<option value="WFO">WFO</option>
														<option value="WFH">WFH</option>
                                                    </select>
                                                </div>
                                            </div>
											<div class="col-md-2">                                                
                                                <div class="form-group">
                                                    <label class=" control-label" for="textArea">Comments</label>
                                                    <textarea class="form-control" id="comments" name="comments[]" rows="1" cols="20"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10 text-right">
                                                <button type="button" name="add_timesheet" id="add_timesheet" class="btn btn-success"><i class="fa fa-fw fa-lg fa-plus-circle"></i> Add More</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer" id="hideAftersumitButton">
                                        <button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Submit</button>
                                        <a href="<?php echo base_url();?>resource_schedule" data-toggle="Go To Report Log!" title="Cancel">
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

	jQuery('#add_timesheet').click(function() { 

            kn++;

            jQuery('#dynamic_field').append('<div id="row' + kn + '">' + '<div class="row"><div class="col-md-2"><div class="form-group">' + '<label class="control-label">Clients</label>' + '<select required class="form-control" id="client_Id' + kn + '" name="client_Id[]" onChange="getProjects(this.value,'+ kn +');"><option value=""></option>' + '<?php foreach($getClientNames as $key => $clientName){ ?>' + '<option value="<?php echo $clientName->client_Id;?>"><?php echo ucfirst(str_replace("'"," ",$clientName->client_name));?></option>' + '<?php } ?>' + '</select></div></div>' + '<div class="col-md-2"><div class="form-group">' + '<label class="control-label">Projects</label>' + '<select class="form-control" id="project_Id' + kn + '" name="project_Id[]" disabled="disabled" onchange="getProjectWiseTask(this.value,'+ kn +');"><option value=""></option>' + '<?php foreach($getListOfProjects as $key => $projectName){ ?>' + '<option value="<?php echo $projectName->project_Id;?>"><?php echo ucfirst(str_replace("'"," ",$projectName->project_name));?></option>' + '<?php } ?>' + '</select></div></div>' + '<div class="col-md-2"><div class="form-group">' + '<label class="control-label">Task</label>' + '<select class="form-control" id="task_Id' + kn + '" name="task_Id[]"  disabled="disabled"><option value=""></option>' + '<?php foreach($getListOfTask as $key => $taskName){ ?>' + '<option value="<?php echo $taskName->task_Id;?>"><?php echo ucfirst(str_replace("'"," ",$taskName->task_name));?></option>' + '<?php } ?>' + '</select></div></div><div class="col-md-2"><div class="form-group">' + '<label class="control-label">Team Member</label>' + '<select class="form-control" id="team_member' + kn + '" name="team_member[]"><option value=""></option>' + '<?php foreach($this->project_model->teamMembers() as $key => $Mteam){ ?>' + '<option value="<?php echo $Mteam->empId;?>"><?php echo ucfirst(str_replace("'"," ",$Mteam->name));?></option>' + '<?php } ?>' + '</select></div></div>' + '<div class="col-md-1"><div class="form-group"><label class="control-label">Hours</label><select class="form-control" id="emp_time_hours' + kn + '" name="emp_time_hours[]" colspan="3"> <option value=""></option>' + '<?php for ($i=0; $i<10;  $i += 0.5) { ?>' + '<option value="<?php echo $i;?>"><?php echo $i;?> </option>' + '<?php	}?>' + '<?php for ($i=10; $i<100;  $i += 1) { ?>' + '<option value="<?php echo $i;?>"><?php echo $i;?> </option>' + '<?php	}?>	' + '</select></div></div><div class="col-md-1"><div class="form-group"><label class="control-label">Workplace</label><select class="form-control" id="workplace' + kn + '" name="workplace[]" colspan="3"><option value="WFO">WFO</option><option value="WFH">WFH</option></select></div></div><div class="col-md-2"><div class="form-group"><label class=" control-label" for="textArea">Comments</label><textarea class="form-control" id="comments' + kn + '" name="comments[]" rows="1"></textarea></div></div><div class="form-group"><div class="col-sm-offset-2 col-sm-10 text-right"><button type="button" name="remove" id="' + kn + '" class="btn btn-danger btn_remove"><i class="fa fa-fw fa-lg fa-times-circle"></i>Remove</button></div></div></div>');


            jQuery('#client_Id' + kn + ',' + '#project_Id' + kn + ',' + '#task_Id' + kn + ',' + '#team_member' + kn + ',' + '#workplace' + kn + ',' + '#emp_time_hours' + kn).select2(); // Autosuggest list               

            
            });


    /* DatePicker */
  
        $("form[name='add_resource']").validate({
            
            rules: {
                
				'department' : {					
					required: true
				},
				
				'client_Id[]': {
                    required: true
                },
                'project_Id[]': {
                    required: true
                },
                'task_Id[]': {
                    required: true
                },
				'team_member[]':{
					required: true
				},		
                
                'emp_time_hours[]': {
                    required: true
                },
                'comments[]': {
                    required: function(element) {  //alert($('#project_Id' + kn).val() + 'kanth');
                        return $.inArray($('#project_Id' + kn).val(), ['156','258','381','390','432','1617','2058','2268','2549','4332','5083','5209','5473','5536','5701','5795']) !== -1;
                    }
                },
                
            },
            messages: {
				'department'		: "Please Select Department",
                'client_Id[]'		: "Please Select Client Name",
                'project_Id[]'		: "Please Select Project Name",
                'task_Id[]'			: "Please Select Task Name",
				'team_member[]'		: "Please Select Employee Name",
                'emp_time_hours[]'	: "Please Select Time",
				'comments[]'		: "Please enter a comment for this project",
                
            },
            submitHandler: function(form) {
                //$("#hideAftersumitButton").attr("disabled", true);
                $("#hideAftersumitButton").html('<i style="color:#009688; font-size:22px;" class="fa fa-spinner" aria-hidden="true"><span> Please wait while we process your request...</span></i>');
                form.submit();
            }
        });

    
  

    /* DatePicker */

    /*Ajax Based dropdown option changes on Clients , Projects and Tasks*/

    function getProjects(client_Id,autoPID) { // Getting client wise projects based on client id
        //alert(client_Id);
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

   jQuery('#client_Id1,#project_Id1,#task_Id1,#team_member1,#emp_time_hours,#department,#workplace').select2(); // Autosuggest list
    
    jQuery(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            var res = confirm('Are You Sure You Want To Delete This?');
            if (res == true) {
                $('#row' + button_id + '').remove();
                $('#' + button_id + '').remove();
            }
        });
    
</script>
<style>
	.select2-container--default .select2-selection--single .select2-selection__rendered { background-color: #663399; color: #fff;font-weight: bold;}
</style>
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->