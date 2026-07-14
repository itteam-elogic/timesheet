<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<?php 

  
  $getClientNames      	= $this->client_model->getClientName(); // List of Clients
  
  $taskClientId         =  '';
  
  $getListOfProjects   	= $this->project_model->getProjectName($taskClientId); // List of Clients
  
  $getListOfEmployees   = $this->timesheet_login->getEmployeeName(); // List of Clients
  
  $taskProjectId 		= '';
  
  $getListOfTask		= $this->task_model->getTaskName($taskProjectId); // List of Clients
		
?>

<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1>Task Mapping Information</h1>
    </div>
  </div>
 
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div>
          <h4 class="line-head">Task Mapping</h4>
		  
		 
		  
          <span style="float:right; position:relative; top:-45px;"><a data-toggle="tooltip" title="Back To Task List" href="<?php echo base_url('task');?>"><img src="<?php echo HTTP_IMAGES_PATH;?>new.png"></a> </span> </div>
        <div style="clear:both;"></div>
        <form class="form-horizontal" method="post" name="save_task_mapping" id="save_task_mapping" action="<?php echo base_url('task/saveTaskMappingData');?>">
          
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
             <select class="form-control" id="project_Id" name="project_Id" disabled="disabled">
                <option value="">Please select project</option>
                <?php foreach($getListOfProjects as $key => $projectName): ?>
				<option value="<?php echo $projectName->project_Id;?>" <?php echo set_select('project_Id', $projectName->project_Id)?>><?php echo ucfirst($projectName->project_name);?></option>
				<?php endforeach; ?>
             </select>
            </div>
          </div>
		  <div class="form-group">
            <label class="control-label col-md-3"> Task List : <span class="required-star">*</span></label>
            <div class="col-md-4">
             <select class="form-control" id="task_name" name="task_name[]" multiple>
                
				
				<option value="">Please select Task</option>
                
				  <?php foreach($getListOfTask as $key => $taskName): ?>
                          <option value="<?php echo $taskName->task_name;?>"><?php echo ucfirst($taskName->task_name);?></option>
                  <?php endforeach; ?>
			
             </select>
            </div>
          </div>
		  
		  <div class="card-footer">
            <div class="row">
              <div class="col-md-8 col-md-offset-3">
                <button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Create</button>
                   <a class="btn btn-default icon-btn" href="<?php echo base_url('task');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a> </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  
</div>
<!-- Organizatoin form validation -->
<script type="text/javascript" language="javascript">
// Wait for the DOM to be ready
$(function() {
  $("form[name='save_task_mapping']").validate({
    rules: {
      client_Id        			 : { required : true },
	  project_Id        		 : { required : true },
	  'task_name[]'       			 : { required : true },
	},
    messages: {
     client_Id				     : "Please Select Client",
	 project_Id				     : "Please Select Project",
	 'task_name[]'				     : "Please Select Employee",
	 },					
     submitHandler: function(form) {
      form.submit();
    }
  });
});


$('#client_Id,#project_Id,#task_name').select2();	 // Autosuggest list

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
