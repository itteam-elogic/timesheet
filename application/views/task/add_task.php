<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<?php 

  $getUpdateId 			= $this->uri->segment('3'); // Update Segment 
  
  $getClientNames      	= $this->client_model->getClientName(); // List of Clients
  
  $taskClientId   			= $this->uri->segment('4'); // Getting client ID
  
  $getListOfProjects   	= $this->project_model->getProjectName($taskClientId); // List of Clients
  
  $getListOfEmployees   	= $this->timesheet_login->getEmployeeName(); // List of Clients

 

      $taskNames_Vic = $this->task_model->getTaskReportId(null);

$taskNamesArray = array();

foreach ($taskNames_Vic as $task) {

  $eLogicClientTaskIds = array("374", "370","369","368","367","364","363","361","355",'14');
				  
  if (!in_array($task->client_Id, $eLogicClientTaskIds)) { 

    		$taskNamesArray[] = $task->task_name;

  }
}

//$taskNamesArray = explode(',', $taskNamesArray);

$taskNamesArray =  implode(',', $taskNamesArray).'<br/>';

$victoryTask =  htmlspecialchars($taskNamesArray); 
?>

<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1>Task Information</h1>
    </div>
  </div>
  <?php if(empty($getUpdateId)): ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div>
          <h4 class="line-head">Add Task</h4>
          <span style="float:right; position:relative; top:-45px;"><a data-toggle="tooltip" title="Back To Task List" href="<?php echo base_url('task');?>"><img src="<?php echo HTTP_IMAGES_PATH;?>new.png"></a> </span> </div>
        <div style="clear:both;"></div>
        <form class="form-horizontal" method="post" name="add_task" id="add_task" action="<?php echo base_url('task/addTask');?>">
          
		  <div class="form-group">
            <label class="control-label col-md-3"> Clients : <span class="required-star">*</span></label>
            <div class="col-md-4">
             <select class="form-control" id="client_Id" name="client_Id" onChange="getProjects(this.value);">
                <option value="">Please select client</option>
                <?php foreach($getClientNames as $key => $clientName): 
				 
				$hideeLogicGeneral = str_replace("eLogic Solutions", "",$clientName->client_name);
					//echo '<pre>'; print_r($hideeLogicGeneral);
				 
				  if('eLogic Solutions'.$hideeLogicGeneral != $clientName->client_name){
				 
				 ?>
				 
				<option value="<?php echo $clientName->client_Id;?>" <?php echo set_select('client_Id', $clientName->client_Id)?>><?php echo ucfirst($clientName->client_name);?></option>
				
				 <?php } ?>
				 
				<?php endforeach; ?>
             </select>
            </div>
          </div>
		  <div class="form-group">
            <label class="control-label col-md-3"> Projects : <span class="required-star">*</span></label>
            <div class="col-md-4">
             <select class="form-control" id="project_Id" name="project_Id">
                <option value="">Please select project</option>
                <?php foreach($getListOfProjects as $key => $projectName): ?>
                
                 <?php 
                               
                 
                 $hideGeneral = str_replace(" - (General)", "",$projectName->project_name);
                                  
                 if($hideGeneral == $projectName->project_name){
                 
                 ?>
                    <option value="<?php echo $projectName->project_Id;?>"><?php echo ucfirst($projectName->project_name);?>
                        
                 <?php } ?>
                        
                 </option>
                 
				<?php endforeach; ?>
                 
             </select>
            </div>
          </div>
		  <?php /*?><div class="form-group">
            <label class="control-label col-md-3"> Employees : </label>
            <div class="col-md-4">
             <select class="form-control" id="empId" name="empId[]" multiple>
                <option value="">Please select employee</option>
                <?php foreach($getListOfEmployees as $key => $employeeName): ?>
				<option value="<?php echo $employeeName->empId;?>" <?php echo set_select('empId[]', $employeeName->empId)?>><?php echo ucfirst($employeeName->username);?></option>
				<?php endforeach; ?>
             </select>
            </div>
          </div><?php */?>
		  <div class="form-group">
            <label class="control-label col-md-3">Task Name : <span class="required-star">*</span></label>
            <div class="col-md-4">
              <input class="form-control" type="text" name="task_name" id="task_name" placeholder="Enter Task Name" value="<?php echo set_value('task_name'); ?>">
              <?php echo form_error('task_name'); ?> </div>
          </div>
		  <div class="form-group">
            <label class="control-label col-md-3">Description : </label>
            <div class="col-md-4">
              <textarea class="form-control" name="task_desc" id="task_desc" placeholder="Enter Task Description" rows="3"><?php echo set_value('task_desc'); ?></textarea>
            </div>
          </div>		  
		  <div class="form-group">
            <label class="control-label col-md-3">Status : <span class="required-star">*</span></label>
            <div class="col-md-4">
             <select class="form-control" id="status" name="status">
                <option value="">Please select status</option>
                <option value="Process" <?php echo set_select('status', 'Process')?>>Process</option>
				<option value="Pending" <?php echo set_select('status', 'Pending')?>>Pending</option>
				<option value="Closed" <?php echo set_select('status', 'Closed')?>>Closed</option>
             </select>
            </div>
          </div>		  
          <div class="card-footer">
            <div class="row">
              <div class="col-md-8 col-md-offset-3">
                <button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Create</button>
                &nbsp;&nbsp;<a class="btn btn-default icon-btn" href="<?php echo base_url('task');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a> </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div>
          <h4 class="line-head">Update Task</h4>
          <span style="float:right; position:relative; top:-45px;"><a data-toggle="tooltip" title="Back To Task List" href="<?php echo base_url('task');?>"><img src="<?php echo HTTP_IMAGES_PATH;?>new.png"></a> </span> </div>
        <div style="clear:both;"></div>
        
		<?php 
				 $empNameList = array();
				 
		          foreach($updateTask as $key => $getTaskData) { 	
		
		  			$empNameList = explode("," , $getTaskData->empId ); 
			   }  
			?>
		
		<form class="form-horizontal" method="post" name="add_task" id="add_task" action="<?php echo base_url('task/updatetask');?>">
          <input type="hidden" id="task_Id" name="task_Id" value="<?php echo $getTaskData->task_Id; ?>" />
		  <div class="form-group">
            <label class="control-label col-md-3"> Clients : <span class="required-star">*</span></label>
            <div class="col-md-4">
             <select class="form-control" id="client_Id" name="client_Id" onChange="getProjects(this.value);">
                <option value="">Please select client</option>
                <?php foreach($getClientNames as $key => $clientName): ?>
				<option value="<?php echo $clientName->client_Id;?>" <?php if($getTaskData->client_Id == $clientName->client_Id) echo 'selected="selected"'; ?>><?php echo ucfirst($clientName->client_name);?></option>
				<?php endforeach; ?>
             </select>
            </div>
          </div>
		  <div class="form-group">
            <label class="control-label col-md-3"> Projects : <span class="required-star">*</span></label>
            <div class="col-md-4">
             <select class="form-control" id="project_Id" name="project_Id">
                <option value="">Please select project</option>
                <?php foreach($getListOfProjects as $key => $projectName): ?>
				<option value="<?php echo $projectName->project_Id;?>" <?php if($getTaskData->project_Id == $projectName->project_Id) echo 'selected="selected"'; ?>><?php echo ucfirst($projectName->project_name);?></option>
				<?php endforeach; ?>
             </select>
            </div>
          </div>
		  <?php /*?><div class="form-group">
            <label class="control-label col-md-3"> Employees : </label>
            <div class="col-md-4">
             <select class="form-control" id="empId" name="empId[]" multiple>
                <option value="">Please select employee</option>
                <?php foreach($getListOfEmployees as $key => $employeeName): ?>
				<option value="<?php echo $employeeName->empId;?>" <?php if(in_array($employeeName->empId , $empNameList)) echo 'selected="selected"'; ?>><?php echo ucfirst($employeeName->username);?></option>
				<?php endforeach; ?>
             </select>
            </div>
          </div><?php */?>
		  <div class="form-group">
            <label class="control-label col-md-3">Task Name : <span class="required-star">*</span></label>
            <div class="col-md-4">
              <input class="form-control" type="text" name="task_name" id="task_name" placeholder="Enter Task Name" value="<?php echo $getTaskData->task_name; ?>">
              <?php echo form_error('task_name'); ?> </div>
          </div>
		  <div class="form-group">
            <label class="control-label col-md-3">Description :  </label>
            <div class="col-md-4">
              <textarea class="form-control" name="task_desc" id="task_desc" placeholder="Enter Task Description" rows="3"><?php echo $getTaskData->task_desc; ?></textarea>
            </div>
          </div>		  
		  <div class="form-group">
            <label class="control-label col-md-3">Status : <span class="required-star">*</span></label>
            <div class="col-md-4">
             <select class="form-control" id="status" name="status">
                <option value="">Please select status</option>
                <option value="Process" <?php if($getTaskData->status == 'Process') echo 'selected="selected"'; ?>>Process</option>
				<option value="Pending" <?php if($getTaskData->status == 'Pending') echo 'selected="selected"'; ?>>Pending</option>
				<option value="Closed" <?php if($getTaskData->status == 'Closed') echo 'selected="selected"'; ?>>Closed</option>
             </select>
            </div>
          </div>		  
          <div class="card-footer">
            <div class="row">
              <div class="col-md-8 col-md-offset-3">
                <button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update</button>
                &nbsp;&nbsp;<a class="btn btn-default icon-btn" href="<?php echo base_url('task');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a> </div>
            </div>
          </div>
        </form>
        
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>
<!-- Organizatoin form validation -->
<script type="text/javascript" language="javascript">
// Wait for the DOM to be ready
$(function() {
  $("form[name='add_task']").validate({
    rules: {
      client_Id        			 : { required : true },
	  project_Id        		 : { required : true },
	 // 'empId[]'       			 : { required : true },
	  task_name        		 	 : { required : true },
	 // task_desc        		 	 : { required : true },
	  status        		 	 : { required : true },
     
	},
    messages: {
     client_Id				     : "Please Select Client",
	 project_Id				     : "Please Select Project",
	// 'empId[]'				     : "Please Select Employee",
	 task_name				 	 : "Please Enter  Task",
	 //task_desc				     : "Please Enter Task Description",
	 status				 		 : "Please Select Task Status",
	 },					
     submitHandler: function(form) {
      form.submit();
    }
  });
		
  var availableTags = <?php echo json_encode(explode(',', $victoryTask)); ?>;
  
    function split( val ) {
      return val.split( /,\s*/ );
    }
    function extractLast( term ) {
      return split( term ).pop();
    }
 
    $( "#task_name" )
      // don't navigate away from the field on tab when selecting an item
      .on( "keydown", function( event ) {
        if ( event.keyCode === $.ui.keyCode.TAB &&
            $( this ).autocomplete( "instance" ).menu.active ) {
          event.preventDefault();
        }
      })
      .autocomplete({
        minLength: 0,
        source: function( request, response ) {
          // delegate back to autocomplete, but extract the last term
          response( $.ui.autocomplete.filter(
            availableTags, extractLast( request.term ) ) );
        },
        focus: function() {
          // prevent value inserted on focus
          return false;
        },
        select: function( event, ui ) {
          var terms = split( this.value );
          // remove the current input
          terms.pop();
          // add the selected item
          terms.push( ui.item.value );
          // add placeholder to get the comma-and-space at the end
          terms.push( "" );
          this.value = terms.join( ", " );
          return false;
        }
      });
	
});

 $(document).ready(function () {
       var today = $("#project_start_date").val();
		$("#project_start_date, #project_end_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            numberOfMonths: 1,
            onSelect: function (selectedDate) {
                if (this.id == 'project_start_date') {
                    var dateMin = $('#project_start_date').datepicker("getDate");
                    //var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(), dateMin.getDate() + 1);
					var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(), dateMin.getDate());
                    var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(), dateMin.getDate() + 365);
                    $('#project_end_date').datepicker("option", "minDate", rMin);
                    $('#project_end_date').datepicker("option", "maxDate", rMax);
                }
               

            }
        });
        $('#project_end_date').datepicker("option", "minDate", new Date(today));

    })
	
$('#client_Id,#project_Id,#empId').select2();	 // Autosuggest list

function getProjects(client_Id) {   // Getting client wise projects based on client id
	$.ajax({
	type: "POST",
	url: "<?php echo base_url('empreports/getListOfProjectsGeneralWithClient');?>",
	data:'client_Id='+client_Id,
	success: function(data){
		$("#project_Id").html(data);
		$('#project_Id').removeAttr('disabled');
	}
});
}
</script>
<!-- Organizatoin form validation -->
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
