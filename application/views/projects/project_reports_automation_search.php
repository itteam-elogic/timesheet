<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
$createdUser = $this->session->userdata['logged_in_timesheet']['empId'];
?>
<!-- Inlude Header here END-->

<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1>Manage Projects</h1>
    </div>
    <div>
	<a class="btn btn-info btn-flat" data-toggle="tooltip" title="Back to Projects" href="<?php echo base_url('projects'); ?>"><i class="fa fa-lg fa-refresh"></i> Back to Projects</a>
	<?php echo (!empty($this->input->post('department'))) ? '<a class="btn btn-warning btn-flat" data-toggle="tooltip" title="Download Project Master Report" href="#" onclick="downloadExcel()"><i class="fa fa-file-excel-o"></i> Download Project Master Report</a>' : ''; ?>

</div>
  </div>
  <div class="card">
		<div class="card-body">
			<div class="row">
				<!-- Search for employee with date wise and client , project wise as well. -->
				<div class="col-md-12">
					<div class="bs-component">
						<div class="tab-content" id="myTabContent">
							<!-- Employee Report adding block -->
							<form class="" name="project_report_search" id="project_report_search" method="post" action="<?php echo base_url('projects/project_report_information');?>">
								<div class="tab-pane">
									<div class="row">
										<div class="col-md-3">
    <div class="form-group">
        <label class="control-label">Department</label>

        <?php
        $selectedDept = $this->input->post('department') ? $this->input->post('department') : [];
        ?>

        <?php if($createdUser == '149'): ?>

            <select class="form-control <?php echo (!empty($selectedDept)) ? 'form-control-sm' : ''; ?>"
                    id="department"
                    name="department[]"
                    multiple>

                <option value="MEP" <?php echo (in_array('MEP', $selectedDept)) ? 'selected' : ''; ?>>MEP</option>

            </select>

        <?php elseif($createdUser == '47'): ?>

            <select class="form-control <?php echo (!empty($selectedDept)) ? 'form-control-sm' : ''; ?>"
                    id="department"
                    name="department[]"
                    multiple>

                <option value="Architectural" <?php echo (in_array('Architectural', $selectedDept)) ? 'selected' : ''; ?>>
                    Architectural - Structural - 3D Visualization
                </option>

            </select>
<?php else: ?>

    <select class="form-control <?php echo (!empty($selectedDept)) ? 'form-control-sm' : ''; ?>"
            id="department"
            name="department[]"
            multiple>

        <option value="all" <?php echo (in_array('all', $selectedDept)) ? 'selected' : ''; ?>>ALL Services</option>

        <option value="MEP" <?php echo (in_array('MEP', $selectedDept)) ? 'selected' : ''; ?>>MEP</option>

        <option value="Architectural" <?php echo (in_array('Architectural', $selectedDept)) ? 'selected' : ''; ?>>Architectural</option>

        <option value="Structural" <?php echo (in_array('Structural', $selectedDept)) ? 'selected' : ''; ?>>Structural</option>

        <option value="3D Visualization" <?php echo (in_array('3D Visualization', $selectedDept)) ? 'selected' : ''; ?>>3D Visualization</option>

        

    </select>

<?php endif; ?>

    </div>
</div>
										


<script>
	$('#department').select2({
    placeholder: "Select Department",
    allowClear: true,
    width: '100%'
});
</script>


<div class="col-md-3">
<div class="form-group">
<label><b>Project Managers</b></label>

<?php
$selectedManagers = (array) $this->input->post('manager_name');
?>

<select name="manager_name[]" id="manager_name" class="form-control" multiple>


<option value="41" <?php if(in_array('41',$selectedManagers)) echo "selected"; ?>>Sandeep Anupati</option>
<option value="394" <?php if(in_array('394',$selectedManagers)) echo "selected"; ?>>Shivani Patil</option>
<option value="71" <?php if(in_array('71',$selectedManagers)) echo "selected"; ?>>Siva Krishna</option>
<option value="448" <?php if(in_array('448',$selectedManagers)) echo "selected"; ?>>Rahul Kumar</option>
<option value="230" <?php if(in_array('230',$selectedManagers)) echo "selected"; ?>>Srinivas Gollakonda</option>
<option value="146" <?php if(in_array('146',$selectedManagers)) echo "selected"; ?>>Syed Afsar</option>
<option value="149" <?php if(in_array('149',$selectedManagers)) echo "selected"; ?>>Syed Farhan</option>
<option value="47" <?php if(in_array('47',$selectedManagers)) echo "selected"; ?>>Pradip Chauhan</option>
<option value="53" <?php if(in_array('53',$selectedManagers)) echo "selected"; ?>>Rajanikanth Bhasuthkar</option>
<option value="523" <?php if(in_array('523',$selectedManagers)) echo "selected"; ?>>Nikhil Bachawal</option>

</select>
</div>
</div>

<script>
$(document).ready(function() {
    $('#manager_name').select2({
        placeholder: "Select Managers",
        width: '100%'
    });
});
</script>


<div class="col-md-3">
    <div class="form-group">
        <label><b>Search</b></label>
        <input type="text" 
               name="search_text" 
               id="search_text"
               class="form-control"
               placeholder="Project Name / Number / Client"
               value="<?php echo $this->input->post('search_text'); ?>">
    </div>
</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">From Date</label>
												<input class="form-control <?php echo (!empty($this->input->post('form_date'))) ? 'form-control-sm' : ''; ?>" type="text" id="form_date" name="form_date" placeholder="Select From Date" value="<?php echo $this->input->post('form_date'); ?>" readonly="" style="background-color: <?php echo (!empty($this->input->post('form_date'))) ? '#663399' : ''; ?>">
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">To Date</label>
												<input class="form-control  <?php echo (!empty($this->input->post('to_date'))) ? 'form-control-sm' : ''; ?>" type="text" id="to_date" name="to_date" placeholder="Select To Date" value="<?php echo $this->input->post('to_date'); ?>" readonly="">
											</div>
										</div>


<div class="col-md-5">
    <div style="margin-top:27px; display:flex; gap:10px; flex-wrap:wrap;">

        <!-- SEARCH -->
        <button type="submit" name="filter_type" value="" class="btn btn-primary">
SEARCH
</button>

       <button type="button" class="btn btn-info" onclick="resetFilters()">
    <i class="fa fa-refresh"></i>
</button>

        <!-- STATUS FILTER BUTTONS -->

<button type="submit" name="filter_type" value="Process" class="btn btn-success">
IN PROCESS
</button>

<button type="submit" name="filter_type" value="On Hold" class="btn btn-warning">
ON HOLD
</button>

<button type="submit" name="filter_type" value="Closed" class="btn btn-danger">
CLOSED
</button>

    </div>
</div>

<script>
function resetFilters() {
    // reset form fields
    document.getElementById("project_report_search").reset();

    // reset select2
    $('#department').val(null).trigger('change');
    $('#manager_name').val(null).trigger('change');

    // reload page clean
    window.location.href = "<?php echo base_url('projects/project_report_information'); ?>";
}
</script>

									</div>
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
	<?php 
$dept = $this->input->post('department');
$manager = $this->input->post('manager_name');
$form_date = $this->input->post('form_date');
$to_date = $this->input->post('to_date');

if(!empty($getProjects)) { 
?>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
          
		  <div style="max-height: 400px; overflow-y: auto;">
		  <table class="table table-hover table-bordered" id="organisationTable444" style="white-space: nowrap;">
              <thead>
                <tr>
                  <th style="font-weight: bold; height: 30px;">Sno</th>
                  <th style="font-weight: bold; height: 30px;">Department</th>     
                  <th style="font-weight: bold; height: 30px;">Project Number</th>    
                  <th style="font-weight: bold; height: 30px;">Project Name</th>
                  <th style="font-weight: bold; height: 30px;">City</th>
                  <th style="font-weight: bold; height: 30px;">State</th>
                  <th style="font-weight: bold; height: 30px;">Country</th> 
                  <th style="font-weight: bold; height: 30px;">Client Proj. No.</th>    
                  <th style="font-weight: bold; height: 30px;">Client name</th>
                  <th style="font-weight: bold; height: 30px;">Project Manager</th>
                  <th style="font-weight: bold; height: 30px;">Start Date</th>
                  <th style="font-weight: bold; height: 30px;">End Date</th>
                  <th style="font-weight: bold; height: 30px;">Man Days</th>
                  <th style="font-weight: bold; height: 30px;">Team Members</th>
                  <th style="font-weight: bold; height: 30px;">Link to the Project on the Server</th>
                  <th style="font-weight: bold; height: 30px;">Status</th>
                  <th style="font-weight: bold; height: 30px;">Comments</th>
                  <th style="font-weight: bold; height: 30px;">Total Site Area (Sft.)</th>
                  <th style="font-weight: bold; height: 30px;">Built-up Area (Sft.)</th>
                  <th style="font-weight: bold; height: 30px;">Construction Technology</th>
                  <th style="font-weight: bold; height: 30px;">Building Typology</th>
                  <th style="font-weight: bold; height: 30px;">Scope Category</th>
                  <th style="font-weight: bold; height: 30px;">Technology Category</th>                    
                  <th style="font-weight: bold; height: 30px;">Primary Project Contact Info</th>
                </tr>
              </thead>
              <tbody>
                <?php 
				  $i=1;
				  foreach ($getProjects as $key => $projectResult) :
				 	 if($i%2 == 0): $showRowColour = 'class="success"'; else: $showRowColour = 'class="info"'; endif;				  
				  	 $createdExp = explode(" " , $projectResult->created_at);
                      if($projectResult->status == 'Process'):
                          $statusClass = 'class="label label-success"';
                      elseif($projectResult->status == 'Pending'):
                          $statusClass = 'class="label label-warning"';				 
                      else:				 
                          $statusClass = 'class="label label-danger"';
                      endif;
				 ?>
                <tr <?php echo $showRowColour; ?> id="delProjectRow<?php echo $projectResult->project_Id; ?>">
                  <td><?php echo $i ?></td>
                  <td><?php echo ucfirst($projectResult->project_type);?></td>      
                  <td><?php echo ucfirst($projectResult->project_number);?></td>    
                  <td><?php echo ucfirst($projectResult->project_name);?></td>
                  <td><?php echo ucfirst($projectResult->city);?></td>
                  <td><?php echo ucfirst($projectResult->state);?></td>
                  <td><?php echo ucfirst($projectResult->country);?></td>
                  <td><?php echo ucfirst($projectResult->pc_code);?></td>
                  <td><?php echo ucfirst($projectResult->client_name);?></td>
                  <td><?php echo ucfirst($projectResult->p_manager);?></td>                 
				  <td><?php echo ucfirst($projectResult->project_start_date);?></td>
                  <td><?php echo ucfirst($projectResult->project_end_date);?></td>
                  <td><?php echo ucfirst($projectResult->man_days);?></td>
                  <td><?php echo ucfirst($projectResult->team_members);?></td>
                  <td><?php echo ucfirst($projectResult->link_to_project);?></td>
                  <td><?php echo $projectResult->status;?></td>
                  <td><?php echo ucfirst($projectResult->project_desc);?></td>
                  <td><?php echo empty($projectResult->total_site_area) ? 'NA' : $projectResult->total_site_area;?></td>
                  <td><?php echo empty($projectResult->total_building_area) ? 'NA' : $projectResult->total_building_area;?></td>
                  <td><?php echo ucfirst($projectResult->construction_technology);?></td>
                  <td><?php echo ucfirst($projectResult->building_typology);?></td>
                  <td><?php echo ucfirst($projectResult->scope_category);?></td>
                  <td><?php echo ucfirst($projectResult->technology_category);?></td>
    			  <td><?php echo $projectResult->project_contact_name.' ,'.$projectResult->project_email_id.','.$projectResult->project_contact_number;?></td>
                </tr>
                <?php $i++; endforeach; ?>
              </tbody>
            </table>
            </div>
          </div>
        </div>
      </div>
    </div>	
	<?php } ?>
  </div>
</div>
<!-- Inlude Footer here -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script type="text/javascript">
function downloadExcel() {
  // Get the table element
  var table = document.getElementById('organisationTable444');
  
  // Convert table to worksheet
  var ws = XLSX.utils.table_to_sheet(table);
  
  // Create workbook and add worksheet
  var wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "Project Report");

  // Generate Excel file
  var wbout = XLSX.write(wb, {bookType:'xlsx', type:'binary'});

  // Convert to blob and save
  var blob = new Blob([s2ab(wbout)], {type:"application/octet-stream"});
  saveAs(blob, 'project_report.xlsx');
}

// Convert string to ArrayBuffer
function s2ab(s) {
  var buf = new ArrayBuffer(s.length);
  var view = new Uint8Array(buf);
  for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
  return buf;
}

function delete_project(project_Id){ 
var answer = confirm ("Are you sure you want to delete project?");
if (answer) {
        $.ajax({
                type: "POST",
                url: "<?php echo base_url('projects/delete');?>",
                data: "project_Id="+project_Id,
				beforeSend: function() {
   							 $('#delProjectRow'+project_Id).html('<i class="fa fa-spinner"></i>');
 				 },success: function (response) { 	
					      
				       $("#delProjectRow"+project_Id).remove("#delProjectRow"+project_Id).html('');
			     }
            });
      }
}

$(function() {
    $("form[name='project_report_search']").validate({
        rules: {},
        submitHandler: function(form) {
            form.submit();
        }
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
				$("#to_date").datepicker("option", "minDate", selectedDate);
			}
		});
		$("#to_date").datepicker({
			dateFormat: 'yy-mm-dd', 
			changeMonth: true,
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				$("#form_date").datepicker("option", "maxDate", selectedDate);
			}
		});
	})
  $('#department').select2(); // Autosuggest list on clients

</script>
<style>
	.form-control-sm{
			font-weight:bold;
			color: #FFF;
			background-color: #663399 !important;

	}
	.select2-container--default .select2-selection--single .select2-selection__rendered{
		    font-weight:bold;
			color: #FFF;
			background-color: #663399 !important;

	}
</style>	
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
