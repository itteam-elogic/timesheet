<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<?php 

  $getUpdateId = $this->uri->segment('3'); // Update Segment 
  
  $getClientNames = $this->client_model->getClientName(); // List of Clients

  $loginManagerName =  $this->session->userdata['logged_in_timesheet']['empId'];  //session user name.
  	
?>

<div class="content-wrapper">
	<div class="page-title">
		<div>
			<h1>Project Master Report</h1>
		</div>
	</div>
	
		<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				 <div>
				 	<h4 class="line-head">Project Information</h4>
				 	<span style="float:right; position:relative; top:-45px;"><a data-toggle="tooltip" title="Back To Projects" href="<?php echo base_url('projects');?>"><img src="<?php echo HTTP_IMAGES_PATH;?>new.png"></a> </span>
				 </div>
				 <div style="clear:both;"></div>
				<?php foreach($updateProject as $key => $getProjectData) { 	 }   ?>
				<form class="form-horizontal">
					<input type="hidden" id="project_id" name="project_id" value="<?php echo $getProjectData->project_Id; ?>" /> 
					<div class="form-group">
						<label class="control-label col-md-2"> Client Name :</label>
						<div class="col-md-3">
							<select class="form-control" id="client_Id" name="client_Id" readonly>
                                <?php $clientName = $this->client_model->getClients($getProjectData->client_Id); ?>
									<option value="<?php echo $clientName[0]->client_Id;?>"><?php echo ucfirst($clientName[0]->client_name);?></option>
									
							</select>
						</div>
						<label class="control-label col-md-2">Project Number : </label>
						<div class="col-md-3">
						<input class="form-control" type="text" readonly name="project_number" id="project_number" value="<?php echo $getProjectData->project_number;?>">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Project Name :</label>
						<div class="col-md-3">
							<input class="form-control" type="text" readonly name="project_name" id="project_name" placeholder="Enter Project Name" value="<?php echo $getProjectData->project_name;?>">
						</div>

						<label class="control-label col-md-2">City :</label>
						<div class="col-md-3">
							<input class="form-control" readonly type="text" name="city" id="city" placeholder="Enter City" value="<?php echo $getProjectData->city;?>">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">State :</label>
						<div class="col-md-3">
                        <select class="form-control" id="state" name="state" readonly>
                            <option value="" selected disabled><?php echo $getProjectData->state;?></option>
                        </select>
						</div>

						<label class="control-label col-md-2">Country :</label>
						<div class="col-md-3">
                        <select class="form-control" id="country" name="country">
                                <option value="" selected disabled><?php echo $getProjectData->country;?></option>
                        </select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Project Client Code : </label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="pc_code" id="pc_code" placeholder="Enter Project Client Code" value="">
						</div>
						<label class="control-label col-md-2">Project Manager :</label>
						<div class="col-md-3">
							<select class="form-control" id="p_manager" name="p_manager" readonly>
								<?php $pManagerName = $this->project_model->getManagers($getProjectData->p_manager); ?>	
                                     <option value=""><?php echo $pManagerName[0]->p_manager; ?></option>							
							 </select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Start Date :</label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="project_start_date" id="project_start_date" readonly="" placeholder="Enter Project Start Date" value="<?php echo $getProjectData->project_start_date;?>">
						</div>
						<label class="control-label col-md-2">End Date :</label>
						<div class="col-md-3">
							<input class="form-control" type="text" name="project_end_date" id="project_end_date" readonly="" placeholder="Enter Project End Date" value="<?php echo $getProjectData->project_end_date;?>">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Man Days :</label>
						<div class="col-md-3">
							<select class="form-control" id="man_days" name="man_days">
								<option value="" selected disabled><?php echo $getProjectData->man_days;?></option>
							</select>
						</div>
						<label class="control-label col-md-2">Estimated Hours :</label>
						<div class="col-md-3">
							<input class="form-control" type="text" readonly name="estimated_hours" id="estimated_hours" placeholder="Enter Estimated Hours" value="<?php echo $getProjectData->estimated_hours;?>">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Notification on Completion of hours :</label>
						<div class="col-md-3">
							<select class="form-control" id="notif_hours" name="notif_hours">
								<option value="" selected disabled><?php echo $getProjectData->notif_hours; ?></option>
							</select>
						</div>
						<label class="control-label col-md-2">Team Members : </label>
						<div class="col-md-3">
							<select class="form-control" id="team_members" name="team_members[]" multiple readonly>
								<option value="" disabled>Please Choose Team Members</option>
								<?php foreach($this->project_model->teamMembers() as $Mteam): ?>
									<option value="<?php echo $Mteam->name;?>" <?php if(strpos($getProjectData->team_members, $Mteam->name) !== false) echo 'selected="selected"'; ?>><?php echo $Mteam->name;?></option>
								<?php endforeach; ?>	
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Project Type :</label>
						<div class="col-md-3">
							<select class="form-control" id="project_type" name="project_type" readonly>
								<option value="" selected disabled><?php echo $getProjectData->project_type; ?></option>
							</select>
						</div>

						
					<div class="form-group">
						<label class="control-label col-md-2">Project Status :</label>
						<div class="col-md-3">
							<select class="form-control" id="status" name="status" readonly>
								<option value="" selected disabled><?=$getProjectData->status;?></option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Resource Billability :</label>
						<div class="col-md-3">
							<select class="form-control" id="resource_billability" name="resource_billability" readonly>
								<option value="" selected disabled><?=$getProjectData->status;?></option>
							</select>
						</div>
						<label class="control-label col-md-2">Total Site Area (Sft.) : </label>
						<div class="col-md-3">
							<input class="form-control" readonly type="text" name="total_site_area" id="total_site_area" placeholder="Enter Total Site Area (Sft.)" value="<?php echo $getProjectData->total_site_area;?>">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2">Construction Technology :</label>
						<div class="col-md-3">
							<select class="form-control" id="construction_technology" name="construction_technology" readonly>
								<option value="" selected disabled><?=$getProjectData->construction_technology;?></option>
							</select>
						</div>
						<label class="control-label col-md-2">Building Typology :</label>
						<div class="col-md-3">
							<select class="form-control" id="building_typology" name="building_typology" readonly>
								<option value="" selected disabled><?=$getProjectData->building_typology;?></option>
							</select>
						</div>
					</div>


					<div class="form-group">
						<label class="control-label col-md-2">Scope Category :</label>
						<div class="col-md-3">
							<select class="form-control" id="scope_category" name="scope_category" readonly>
								<option value="" selected disabled><?=$getProjectData->scope_category;?></option>								
							</select>
						</div>
						<label class="control-label col-md-2">Technology Category :</label>
						<div class="col-md-3">
							<select class="form-control" id="technology_category" name="technology_category" readonly>
								<option value="" selected disabled><?=$getProjectData->technology_category;?></option>
							</select>
						</div>
					</div>


					<div class="form-group">
						<label class="control-label col-md-2">Project Description : </label>
						<div class="col-md-3">
							<textarea class="form-control" readonly name="project_desc" id="project_desc" placeholder="Enter Project Description" rows="2"><?php echo $getProjectData->project_desc; ?></textarea>
						</div>

						<label class="control-label col-md-2">Link to the Project on the Server :</label>
						<div class="col-md-3">
							<input type="text" class="form-control" readonly placeholder="Enter link to the Project" id="link_to_project" name="link_to_project" value="<?php echo $getProjectData->link_to_project; ?>"/>
						</div>
					</div>


					<div class="form-group">
						<div class="row col-md-10 mb-10">
							<div class="col-md-3">
								<h4 class="control-label">Primary Project Contact Info : </h4>
							</div>
						</div>
						<div class="row mb-20 col-md-11">
							<div class="col-md-2"></div>
							<div class="col-md-3">
								<label>Contact Name :</label>
								<input class="form-control col-md-8" readonly type="text" name="project_contact_name" id="project_contact_name" placeholder="Enter project contact name" value="<?php echo $getProjectData->project_contact_name; ?>">
							</div>
							<div class="col-md-3">
								<label>Email Id :</label>
								<input class="form-control col-md-8"  readonly type="text" name="project_email_id" id="project_email_id" placeholder="Enter Project contact email id" value="<?php echo $getProjectData->project_email_id; ?>">
							</div>
							<div class="col-md-3">
								<label>Contact Number :</label>
								<input class="form-control col-md-8" readonly type="text" name="project_contact_number" id="project_contact_number" placeholder="Enter Project contact number" value="<?php echo $getProjectData->project_contact_number; ?>">
							</div>
						</div>
					</div>			
					
					</div>
				</form>
			</div>
		</div>
	</div>
	
</div>
<!-- Organizatoin form validation -->

<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->