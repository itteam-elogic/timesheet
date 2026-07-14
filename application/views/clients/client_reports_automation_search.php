<!-- Inlude Header here -->
  <?php $this->load->view('includes/cRMHeader'); 
  $this->load->helper('department');
  $createdUser = $this->session->userdata['logged_in_timesheet']['empId'];
  $departM = $this->input->post('department');
  if(empty($departM)){
      $departM = array();
  }
  if(!is_array($departM)){
      $departM = array($departM);
  }
  $departmentOptions = function_exists('ts_department_options') ? ts_department_options() : array();
  ?>
<!-- Inlude Header here END-->

 <div class="content-wrapper">
    <div class="page-title">
      <div>
        <h1>Manage Clients</h1>
      </div>
      
      <div>

    <a class="btn btn-primary btn-flat" href="<?php echo base_url('clients/add'); ?>" data-toggle="tooltip" title="Add Client"><i class="fa fa-lg fa-plus"></i></a>
      <a class="btn btn-warning btn-flat"
       data-toggle="tooltip"
       title="Download Client Master Report"
       href="#"
       onclick="downloadExcel()">
       <i class="fa fa-file-excel-o"></i> Download Client Report
    </a>
   
    </div>
    </div>
    <div class="card">
      <div class="card-body">
        <div class="row">
          <!-- Search for employee with date wise and client , project wise as well. -->
          <div class="col-md-10">

            <div class="bs-component">
              <div class="tab-content" id="myTabContent">
                <!-- Employee Report adding block -->
                <form class="" name="client_report_search" id="client_report_search" method="post" action="<?php echo base_url('clients/client_list_information');?>">
                  <div class="tab-pane">
                    <div class="row">


  <div class="col-md-3">
      <div class="form-group">
          <label class="control-label">Department</label>
          <div class="multiple-options">
              <select class="form-control" id="department" name="department[]" multiple>
                  <option value="all" <?= in_array('all', $departM, true) ? 'selected' : ''; ?>>
                      All Departments
                  </option>
                  <?php foreach ($departmentOptions as $deptOption): ?>
                      <option value="<?php echo htmlspecialchars($deptOption, ENT_QUOTES, 'UTF-8'); ?>" <?= in_array($deptOption, $departM, true) ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($deptOption, ENT_QUOTES, 'UTF-8'); ?>
                      </option>
                  <?php endforeach; ?>

  </select>
          </div>
      </div>
  </div>


  <!---------------------akhila code project manager btn--------------------->

  <div class="col-md-3">
<div class="form-group">
<label><b>Project Managers</b></label>

<?php
$selectedManagers = $this->input->post('manager_name');

if(!is_array($selectedManagers)){
    $selectedManagers = array($selectedManagers);
}
?>

<select name="manager_name[]" id="manager_name" class="form-control" multiple>

<option value="384" <?php if(in_array('384',$selectedManagers)) echo "selected"; ?>>Jaishri Jain</option>

<option value="140" <?php if(in_array('140',$selectedManagers)) echo "selected"; ?>>Shirley Rufina</option>

<option value="41" <?php if(in_array('41',$selectedManagers)) echo "selected"; ?>>Sandeep Anupati</option>

<option value="394" <?php if(in_array('394',$selectedManagers)) echo "selected"; ?>>Shivani Patil</option>

<option value="71" <?php if(in_array('71',$selectedManagers)) echo "selected"; ?>>Siva Krishna</option>

<option value="448" <?php if(in_array('448',$selectedManagers)) echo "selected"; ?>>Rahul Kumar</option>

<option value="230" <?php if(in_array('230',$selectedManagers)) echo "selected"; ?>>Srinivas Gollakonda</option>

<option value="146" <?php if(in_array('146',$selectedManagers)) echo "selected"; ?>>Syed Afsar</option>

<option value="149" <?php if(in_array('149',$selectedManagers)) echo "selected"; ?>>Syed Farhan</option>

<option value="47" <?php if(in_array('47',$selectedManagers)) echo "selected"; ?>>Pradip Chauhan</option>

<option value="183" <?php if(in_array('183',$selectedManagers)) echo "selected"; ?>>Hemanth</option>

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

<script>
$(function() {

    $("form[name='client_report_search']").validate({

        rules: {
            form_date: {
                required: function() {
                    return isDateRequired();
                }
            },
            to_date: {
                required: function() {
                    return isDateRequired();
                }
            }
        },

        messages: {
            form_date: "Select Date OR use any filter",
            to_date: "Select Date OR use any filter"
        },

        submitHandler: function(form) {
            form.submit();
        }

    });

    function isDateRequired() {

        let manager    = $('#manager_name').val();
        let department = $('#department').val();
        let client     = $('#client_name').val();

        // normalize values
        let hasManager    = manager && manager.length > 0;
        let hasDepartment = department && department.length > 0;
        let hasClient     = client && client.trim() !== "";

        // ✅ If NOTHING selected → require date
        if (!hasManager && !hasDepartment && !hasClient) {
            return true;
        }

        // ✅ Otherwise → NOT required
        return false;
    }

});
</script>

<div class="col-md-3">
    <div class="form-group">
        <label><b>Search Client</b></label>

        <div style="position:relative;">

          <input type="text"
       id="client_search"
       name="client_name"
       class="form-control"
       placeholder="Type Client Name">

            <div id="client_suggestion_box"></div>

        </div>
    </div>
</div>

<style>
#clientSuggestions{
    position:absolute;
    top:70px;
    left:0;
    width:100%;
    background:#fff;
    border:1px solid #ddd;
    z-index:99999;
    display:none;
    max-height:250px;
    overflow-y:auto;
    box-shadow:0 2px 8px rgba(0,0,0,0.15);
}

.client-item{
    padding:10px;
    cursor:pointer;
    border-bottom:1px solid #eee;
}

.client-item:hover{
    background:#f5f5f5;
}
</style>

<script>
$(document).ready(function(){

    // ===============================
    // LIVE DROPDOWN SUGGESTION
    // ===============================
    $("#client_search").keyup(function(){

        var search = $(this).val();

        if(search != ''){

            $.ajax({
                url: "<?php echo base_url('clients/liveClientSearch'); ?>",
                method: "POST",
                data: {
                    search: search,
                    type: 'suggestion'
                },
                success: function(data){

                    $("#client_suggestion_box").html(data);
                }
            });

        } else {

            $("#client_suggestion_box").html('');
        }
    });

    // ===============================
    // CLICK SUGGESTION
    // ===============================
    $(document).on('click', '.client_suggestion', function(){

        var value = $(this).data('name');

        $("#client_search").val(value);
        $("#client_suggestion_box").html('');

        loadClientTable(value);
    });

    // ===============================
    // LOAD TABLE
    // ===============================
    function loadClientTable(search){

        $.ajax({
            url: "<?php echo base_url('clients/liveClientSearch'); ?>",
            method: "POST",
            data: {search: search},
            success: function(data){

                $("#client_report_excel_download tbody").html(data);
            }
        });
    }

});
</script>


<!-- 
                      <div class="col-md-2">
                        <div class="form-group">
                          <label class="control-label">From Date</label>
                          <input class="form-control <?php echo (!empty($this->input->post('form_date'))) ? 'form-control-sm' : ''; ?>" type="text" id="form_date" name="form_date" placeholder="Select From Date" value="<?php echo $this->input->post('form_date'); ?>" readonly="">
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                          <label class="control-label">To Date</label>
                          <input class="form-control  <?php echo (!empty($this->input->post('to_date'))) ? 'form-control-sm' : ''; ?>" type="text" id="to_date" name="to_date" placeholder="Select To Date" value="<?php echo $this->input->post('to_date'); ?>" readonly="">
                        </div>
                      </div> -->



<?php
$selected_from_year  = $this->input->post('from_year');
$selected_from_month = $this->input->post('from_month');

$selected_to_year    = $this->input->post('to_year');
$selected_to_month   = $this->input->post('to_month');
?>

<div class="col-md-3">
<div class="date-filter-wrapper">

    <!-- FROM -->
    <div class="date-box">
        <label class="date-label">From</label>

        <div class="date-select-group">

            <div class="select-wrapper">
                <select class="form-control custom-select" id="from_year" name="from_year">
                <option value="">Select Year</option>
                <option value="2026" <?php echo ($selected_from_year == '2026') ? 'selected' : ''; ?>>2026</option>
                <option value="2025" <?php echo ($selected_from_year == '2025') ? 'selected' : ''; ?>>2025</option>
                <option value="2024" <?php echo ($selected_from_year == '2024') ? 'selected' : ''; ?>>2024</option>
                <option value="2023" <?php echo ($selected_from_year == '2023') ? 'selected' : ''; ?>>2023</option>
                <option value="2022" <?php echo ($selected_from_year == '2022') ? 'selected' : ''; ?>>2022</option>
                <option value="2021" <?php echo ($selected_from_year == '2021') ? 'selected' : ''; ?>>2021</option>
                <option value="2020" <?php echo ($selected_from_year == '2020') ? 'selected' : ''; ?>>2020</option>
                <option value="2019" <?php echo ($selected_from_year == '2019') ? 'selected' : ''; ?>>2019</option>
                <option value="2018" <?php echo ($selected_from_year == '2018') ? 'selected' : ''; ?>>2018</option>
                <option value="2017" <?php echo ($selected_from_year == '2017') ? 'selected' : ''; ?>>2017</option>
                <option value="2016" <?php echo ($selected_from_year == '2016') ? 'selected' : ''; ?>>2016</option>
                </select>

                <span class="clear-select" data-target="from_year">
                    <i class="fa fa-times"></i>
                </span>
            </div>

            <div class="select-wrapper">
                <select class="form-control custom-select" id="from_month" name="from_month">
                    <option value="">Select Mon</option>
          			<option value="01" <?php echo ($selected_from_month == '01') ? 'selected' : ''; ?>>January</option>
                    <option value="02"<?php echo ($selected_from_month == '02') ? 'selected' : ''; ?>>February</option>
                    <option value="03" <?php echo ($selected_from_month == '03') ? 'selected' : ''; ?>>March</option>
                    <option value="04" <?php echo ($selected_from_month == '04') ? 'selected' : ''; ?>>April</option>
                    <option value="05" <?php echo ($selected_from_month == '05') ? 'selected' : ''; ?>>May</option>
                    <option value="06" <?php echo ($selected_from_month == '06') ? 'selected' : ''; ?>>June</option>
                    <option value="07" <?php echo ($selected_from_month == '07') ? 'selected' : ''; ?>>July</option>
                    <option value="08" <?php echo ($selected_from_month == '08') ? 'selected' : ''; ?>>August</option>
                    <option value="09" <?php echo ($selected_from_month == '09') ? 'selected' : ''; ?>>September</option>
                    <option value="10" <?php echo ($selected_from_month == '10') ? 'selected' : ''; ?>>October</option>
                    <option value="11" <?php echo ($selected_from_month == '11') ? 'selected' : ''; ?>>November</option>
                    <option value="12" <?php echo ($selected_from_month == '12') ? 'selected' : ''; ?>>December</option>
                </select>

                <span class="clear-select" data-target="from_month">
                    <i class="fa fa-times"></i>
                </span>
            </div>

        </div>
    </div>

    <!-- TO -->
    <div class="date-box">
        <label class="date-label">To</label>

        <div class="date-select-group">

            <div class="select-wrapper">
               <select class="form-control custom-select" id="to_year" name="to_year">
    <option value="">Select Year</option>

    <option value="2026" <?php echo ($selected_to_year == '2026') ? 'selected' : ''; ?>>2026</option>
    <option value="2025" <?php echo ($selected_to_year == '2025') ? 'selected' : ''; ?>>2025</option>
    <option value="2024" <?php echo ($selected_to_year == '2024') ? 'selected' : ''; ?>>2024</option>
    <option value="2023" <?php echo ($selected_to_year == '2023') ? 'selected' : ''; ?>>2023</option>
    <option value="2022" <?php echo ($selected_to_year == '2022') ? 'selected' : ''; ?>>2022</option>
    <option value="2021" <?php echo ($selected_to_year == '2021') ? 'selected' : ''; ?>>2021</option>
    <option value="2020" <?php echo ($selected_to_year == '2020') ? 'selected' : ''; ?>>2020</option>
    <option value="2019" <?php echo ($selected_to_year == '2019') ? 'selected' : ''; ?>>2019</option>
    <option value="2018" <?php echo ($selected_to_year == '2018') ? 'selected' : ''; ?>>2018</option>
    <option value="2017" <?php echo ($selected_to_year == '2017') ? 'selected' : ''; ?>>2017</option>
    <option value="2016" <?php echo ($selected_to_year == '2016') ? 'selected' : ''; ?>>2016</option>
    </select>
                <span class="clear-select" data-target="to_year">
                    <i class="fa fa-times"></i>
                </span>
            </div>

            <div class="select-wrapper">
               <select class="form-control custom-select" id="to_month" name="to_month">

    <option value="">Select Mon</option>

    <option value="01" <?php echo ($selected_to_month == '01') ? 'selected' : ''; ?>>January</option>
    <option value="02" <?php echo ($selected_to_month == '02') ? 'selected' : ''; ?>>February</option>
    <option value="03" <?php echo ($selected_to_month == '03') ? 'selected' : ''; ?>>March</option>
    <option value="04" <?php echo ($selected_to_month == '04') ? 'selected' : ''; ?>>April</option>
    <option value="05" <?php echo ($selected_to_month == '05') ? 'selected' : ''; ?>>May</option>
    <option value="06" <?php echo ($selected_to_month == '06') ? 'selected' : ''; ?>>June</option>
    <option value="07" <?php echo ($selected_to_month == '07') ? 'selected' : ''; ?>>July</option>
    <option value="08" <?php echo ($selected_to_month == '08') ? 'selected' : ''; ?>>August</option>
    <option value="09" <?php echo ($selected_to_month == '09') ? 'selected' : ''; ?>>September</option>
    <option value="10" <?php echo ($selected_to_month == '10') ? 'selected' : ''; ?>>October</option>
    <option value="11" <?php echo ($selected_to_month == '11') ? 'selected' : ''; ?>>November</option>
    <option value="12" <?php echo ($selected_to_month == '12') ? 'selected' : ''; ?>>December</option>

</select>

                <span class="clear-select" data-target="to_month">
                    <i class="fa fa-times"></i>
                </span>
            </div>

        </div>
    </div>

</div>
</div>

    <!--akhila code active/inactive btns-->


<div class="col-md-6">
    <div class="action-btn-wrapper">
<button class="btn btn-primary icon-btn" style="margin-top:27px;">Search</button>

<a class="btn btn-info btn-flat" data-toggle="tooltip" style="margin-top:27px;border-radius: 3px 3px 3px 3px;" title="Refresh" href="<?php echo base_url('clients/client_list_information'); ?>"><i class="fa fa-lg fa-refresh"></i></a>

<a href="<?= base_url('clients/client_list_information?status=active'); ?>"
   class="btn btn-success btn-flat" style="margin-top:27px;border-radius: 3px 3px 3px 3px;"
   onclick="return applyStatusWithFilters(event, 'active')">
   ACTIVE
</a>

<a href="<?= base_url('clients/client_list_information?status=inactive'); ?>"
   class="btn btn-danger btn-flat" style="margin-top:27px;border-radius: 3px 3px 3px 3px;"
   onclick="return applyStatusWithFilters(event, 'inactive')">
   INACTIVE
</a>

<a href="<?= base_url('clients/client_list_information?status=all'); ?>"
   class="btn btn-info btn-flat" style="margin-top:27px;border-radius: 3px 3px 3px 3px;"
   onclick="return applyStatusWithFilters(event, 'all')">
   ALL
</a>

</div>
</div>



<script>
function applyStatus(status)
{
    // create hidden input if not exists
    if($('#status_input').length === 0){
        $('<input>').attr({
            type: 'hidden',
            id: 'status_input',
            name: 'status'
        }).appendTo('#client_report_search');
    }

    $('#status_input').val(status);

    // submit form with all filters
    $('#client_report_search').submit();
}
</script>

<script>
function applyStatusWithFilters(e, status)
{
    e.preventDefault();

    // =====================================
    // CREATE STATUS INPUT
    // =====================================

    if($('#status_input').length === 0){

        $('<input>').attr({
            type: 'hidden',
            id: 'status_input',
            name: 'status'
        }).appendTo('#client_report_search');
    }

    // =====================================
    // SET STATUS VALUE
    // =====================================

    $('#status_input').val(status);

    // =====================================
    // SUBMIT COMPLETE FORM
    // =====================================

    $('#client_report_search').submit();

    return false;
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

    <!----------------akhila code Client Summary all/active/inactive count table--------------------------->


   <div class="row panel" style="margin-bottom:20px; display:flex; justify-content:center;">
    <div class="col-md-5">

        <div>
            <div class="panel-heading text-center" style="font-size: 25px; color:#663399;">
                <b>Client Summary</b>
            </div>

            <table class="table table-bordered text-center" style="text-align:center;">
                <thead style="background:#2c5d7c; color:#fff;">
                    <tr>
                        <th style="text-align:center; font-size: 16px;">Type</th>
                        <th style="text-align:center; font-size: 16px;">Count</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align:center; color:#2c5d7c; font-size: 16px; background-color: #e8eef5;"><b>Total Clients</b></td>
                        <td style="text-align:center; font-size: 16px; background-color: #d5f5e3; font-weight: 600;"><?php echo $clientCounts['total']; ?></td>
                    </tr>
                    <tr>
                        <td style="text-align:center; color:#2c5d7c; font-size: 16px; background-color: #e8eef5;"><b>Active Clients</b></td>
                        <td style="text-align:center; color:green; font-size: 16px; background-color: #d5f5e3; font-weight: 600;"><?php echo $clientCounts['active']; ?></td>
                    </tr>
                    <tr>
                        <td style="text-align:center; color:#2c5d7c; font-size: 16px; background-color: #e8eef5;"><b>Inactive Clients</b></td>
                        <td style="text-align:center; color:red; font-size: 16px; background-color: #d5f5e3; font-weight: 600;"><?php echo $clientCounts['inactive']; ?></td>
                    </tr>
                </tbody>
            </table>

        </div>

    </div>
</div>

    <?php if(!empty($getClients)){ ?>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover table-bordered" id="client_report_excel_download">
                <thead>
                  <tr>
                    <th>Sno</th>
                    <th>Client Name</th>
            <th>Client Email</th>
                    <th>Project Manager</th>
                    <th>Description</th>
            <th>Contact Num</th>
            <th>Department</th>
                      <th>Country</th>
                      <th>State</th>
                      <th>City</th>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
            $i=1;
            foreach ($getClients as $key => $clientResult) :
            if($i%2 == 0): $showRowColour = 'class="success"'; else: $showRowColour = 'class="info"'; endif;
              $createdExp 		= explode(" " , $clientResult->created_at);

            if($clientResult->status == 'Active'):

                $statusClass = 'class="label label-success"';
            else:
                $statusClass = 'class="label label-danger"';

            endif;

                    //echo 'test---'.$clientResult->empId;

          ?>
                  <tr <?php echo $showRowColour; ?> id="delClientRow<?php echo $clientResult->client_Id; ?>">
                    <td><?php echo $i ?></td>
                    <td><?php echo ucwords($clientResult->client_name);?></td>
            <td><?php echo $clientResult->client_email;?></td>
                    <td><span class="label label-info"><?php echo ucfirst($clientResult->name);?></span></td>
                    <td><a href="#"  data-toggle="tooltip" title="<?php echo $clientResult->client_desc;?>"><?php echo character_limiter($clientResult->client_desc, 20);?></a></td>
            <td><?php echo $clientResult->client_contact_num;?></td>
            <td><?php echo $clientResult->department;?></td>
                      <td><?php echo $clientResult->client_country;?></td>
                      <td><?php echo $clientResult->client_state;?></td>
                      <td><?php echo $clientResult->client_city;?></td>
                    <td>
    <button 
        class="status-btn <?php echo strtolower($clientResult->status); ?>"
        id="statusBtn_<?php echo $clientResult->client_Id; ?>"
        onclick="toggleStatus(<?php echo $clientResult->client_Id; ?>, '<?php echo $clientResult->status; ?>')">

        <?php if($clientResult->status == 'Active'){ ?>
            Active
        <?php } else { ?>
            Inactive
        <?php } ?>

    </button>
</td>
                    <th><?php echo date('d-M-Y',strtotime($createdExp[0]));?></th>
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
<!-- Inlude Footer here -->


<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script type="text/javascript">
function downloadExcel() {
  // Get the table element
  var table = document.getElementById('client_report_excel_download');
  
  // Convert table to worksheet
  var ws = XLSX.utils.table_to_sheet(table);
  
  // Create workbook and add worksheet
  var wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "Client Report");

  // Generate Excel file
  var wbout = XLSX.write(wb, {bookType:'xlsx', type:'binary'});

  // Convert to blob and save
  var blob = new Blob([s2ab(wbout)], {type:"application/octet-stream"});
  saveAs(blob, 'client_report.xlsx');
}

// Convert string to ArrayBuffer
function s2ab(s) {
  var buf = new ArrayBuffer(s.length);
  var view = new Uint8Array(buf);
  for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
  return buf;
}


$(function() {
		$("form[name='client_report_search']").validate({
			rules: {
				form_date: {
					required: true
				},
				to_date: {
					required: true
				}
			},
			messages: {
				form_date: "Please Select From Date",
				to_date: "Please Select To Date"
			},
			submitHandler: function(form) {
				form.submit();
			}
		});
	});


$(document).ready(function() {
		$("#form_date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			yearRange: 'c-10:c',
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				$("#to_date").datepicker("option", "minDate", selectedDate);
			}
		});
		$("#to_date").datepicker({
			dateFormat: 'yy-mm-dd', 
			changeMonth: true,
			changeYear: true,
			yearRange: 'c-10:c',
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				$("#form_date").datepicker("option", "maxDate", selectedDate);
			}
		});
	})
  $('#department').select2(); // Autosuggest list on clients

</script>



<script>
    function toggleStatus(clientId, currentStatus)
{
    var newStatus = (currentStatus.toLowerCase() === 'active') ? 'Inactive' : 'Active';

    // 🔔 Confirmation popup
    var confirmAction = confirm("Are you sure you want to change status to " + newStatus + "?");

    if(!confirmAction){
        return; // ❌ Cancel clicked
    }

    // ✅ Proceed with AJAX
    $.ajax({
        url: "<?php echo base_url('clients/update_status'); ?>",
        type: "POST",
        dataType: "json",
        data: {
            client_Id: clientId,
            current_status: currentStatus
        },
        success: function(response)
        {
            if(response.success)
            {
                var btn = $("#statusBtn_" + clientId);

                if(response.new_status == "Active")
                {
                    btn.removeClass('inactive').addClass('active');
                    btn.text('Active');
                }
                else
                {
                    btn.removeClass('active').addClass('inactive');
                    btn.text('Inactive');
                }

                // update onclick dynamically
                btn.attr("onclick", "toggleStatus("+clientId+", '"+response.new_status+"')");
            }
            else
            {
                alert(response.message);
            }
        }
    });
}
</script>



<!------------from/to dates script---------------->

<!------------from/to dates script---------------->

<script>
$(document).ready(function () {

    // ================================
    // Dropdown Change Functionality
    // ================================
    $('.custom-select').on('change', function () {

        var selectValue = $(this).val();
        var clearBtn = $(this).siblings('.clear-select');

        // Show/Hide clear icon
        if (selectValue !== '') {

            clearBtn.show();

            // Add background color
            $(this).addClass('selected-option');

        } else {

            clearBtn.hide();

            // Remove background color
            $(this).removeClass('selected-option');
        }

        // ================================
        // Auto Filter Data
        // ================================
        $.ajax({

            url: "<?php echo base_url('clients/client_list_information'); ?>",
            type: "POST",

            data: {

                client_search: $('#client_search').val(),

                department: $('#department').val(),

                manager_name: $('#manager_name').val(),

                from_year: $('#from_year').val(),
                from_month: $('#from_month').val(),

                to_year: $('#to_year').val(),
                to_month: $('#to_month').val(),

                status: $('#status_input').val()
            },

            success: function (response) {

                var newHtml = $(response).find('#client_report_excel_download').closest('.table-responsive').html();

                $('.table-responsive').html(newHtml);
            }
        });

    });

    // ================================
    // Clear Dropdown
    // ================================
    $('.clear-select').on('click', function () {

        var target = $(this).data('target');

        $('#' + target)
            .val('')
            .removeClass('selected-option')
            .trigger('change');

        $(this).hide();

    });

    // ================================
    // Page Load Check
    // ================================
    $('.custom-select').each(function () {

        if ($(this).val() !== '') {

            $(this).addClass('selected-option');

            $(this).siblings('.clear-select').show();
        }
    });

});
</script>


<script>
    $("#client_search").keyup(function(){

    var search = $(this).val();

    $.ajax({
        url: "<?php echo base_url('clients/liveClientSearch'); ?>",
        method: "POST",
        data: {search: search},
        success: function(data){
            $("#client_report_excel_download tbody").html(data);
        }
    });

});
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
    .status-btn {
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    color: #fff;
    cursor: pointer;
    font-size: 12px;
}

.status-btn.active {
    background-color: #28a745;
}

.status-btn.inactive {
    background-color: #dc3545;
}
/* ✅ Make table fit screen */
#client_report_excel_download {
    width: 100%;
    table-layout: fixed;   /* 🔥 important */
    font-size: 14px;
}
/* ✅ S.No column (1st column) */
#client_report_excel_download th:nth-child(1),
#client_report_excel_download td:nth-child(1) {
    width: 40px;
    text-align: center;
}
/* ✅ Fix email column overflow (3rd column) */
#client_report_excel_download th:nth-child(3),
#client_report_excel_download td:nth-child(3) {
    width: 220px;        /* 👈 increase as needed (200–260px) */
    max-width: 220px;     /* adjust if needed */
    word-break: break-all;     /* 🔥 breaks long emails */
    overflow-wrap: break-word;
    white-space: normal;       /* allow wrapping */
}
/* ✅ Project Manager column (4th column) */
#client_report_excel_download th:nth-child(4),
#client_report_excel_download td:nth-child(4) {
    width: 180px;        /* 👈 adjust: 160px / 180px / 200px */
    max-width: 180px;
}
/* ✅ Description column (5th column) */
#client_report_excel_download th:nth-child(5),
#client_report_excel_download td:nth-child(5) {
    width: 220px;        /* 👈 increase (200–260px) */
    max-width: 220px;
}
/* ✅ Contact Num column (6th column) */
#client_report_excel_download th:nth-child(6),
#client_report_excel_download td:nth-child(6) {
    width: 140px;        /* 👈 adjust: 130px / 150px */
    max-width: 140px;
    white-space: nowrap; /* keep number in single line */
}
/* ✅ Country column (7th column) */
#client_report_excel_download th:nth-child(7),
#client_report_excel_download td:nth-child(7) {
    width: 140px;        /* 👈 adjust: 130px / 150px */
    max-width: 140px;
    white-space: normal; /* allow wrapping if long */
    word-break: break-word;
}
/* ✅ Status column (11th column) */
#client_report_excel_download th:nth-child(11),
#client_report_excel_download td:nth-child(11) {
    width: 80px;
}

/* ✅ Reduce button size inside status */
.status-btn {
    padding: 3px 8px;
    font-size: 12px;
}

/* ✅ Increase row height slightly */
#client_report_excel_download tr {
    height: 50px;   /* 👈 adjust: 48px / 50px / 55px */
}

/* ✅ Add more vertical spacing inside cells */
#client_report_excel_download td,
#client_report_excel_download th {
    padding-top: 10px;
    padding-bottom: 10px;
}
</style>	



<style>

	.date-filter-wrapper {
    display: flex;
    gap: 30px;
    margin-top: 5px;
	
}

.date-box {
    border: 1px solid #d7d7d7;
    border-radius: 10px;
    padding: 8px;
    min-width: 240px;
}

.date-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #0c0c0c;
    margin-bottom: 10px;
}

.date-select-group {
    display: flex;
    gap: 12px;
}

.select-wrapper {
    position: relative;
    width: 100%;
}

.custom-select {
    height: 30px;
    border-radius: 10px;
    border: 1px solid #cfcfcf;
    font-size: 11px;
    padding-right: 35px;
	padding: 2px !important;
    background: #fff;
    box-shadow: none;
}

.custom-select:focus {
    border-color: #7b4ba3;
    box-shadow: 0 0 4px rgba(123, 75, 163, 0.3);
}

.clear-select {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #fffdfd;
    cursor: pointer;
    display: none;
    font-size: 12px;
}

.clear-select:hover {
    color: #d9534f;
}
.custom-select.selected-option {
    background-color: #663399 !important;
    color: #ffffff !important;
    font-weight: 600;
    border-color: #663399 !important;
}

.custom-select.selected-option option {
    background-color: #ffffff;
    color: #000000;
}
</style>


<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
