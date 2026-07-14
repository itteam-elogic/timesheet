<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
$createdUser = $this->session->userdata['logged_in_timesheet']['empId'];
?>
<!-- Inlude Header here END-->

<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1>Task List</h1>
    </div>
    <div style="display:flex; gap:8px;">

    <a class="btn btn-primary btn-flat"
       href="<?php echo base_url('task/add'); ?>"
       data-toggle="tooltip"
       title="Add Task">
        <i class="fa fa-lg fa-plus"></i>
    </a>

    <a class="btn btn-info btn-flat"
       data-toggle="tooltip"
       title="Refresh"
       href="<?php echo base_url('task'); ?>">
        <i class="fa fa-lg fa-refresh"></i>
    </a>

<a class="btn btn-success btn-flat"
   href="<?php echo base_url('task/downloadTaskReport'); ?>?<?php echo http_build_query($_GET); ?>">
   <i class="fa fa-file-excel-o"></i> Download
</a>

</div>
  </div>



 <!-- Excel Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script>

function downloadTaskReport()
{
    var formData = $('#task_search_form').serialize();

    window.location.href =
        "<?php echo base_url('task/downloadTaskReport'); ?>?" + formData;
}

</script>

<script>

function downloadTaskExcel()
{
    // ✅ CHANGE THIS TABLE ID TO YOUR TASK TABLE ID
    var table = document.getElementById('task_table');

    // Convert table to worksheet
    var ws = XLSX.utils.table_to_sheet(table);

    // Create workbook
    var wb = XLSX.utils.book_new();

    // Add worksheet
    XLSX.utils.book_append_sheet(wb, ws, "Task Report");

    // Write workbook
    var wbout = XLSX.write(wb, {
        bookType: 'xlsx',
        type: 'binary'
    });

    // Save file
    saveAs(
        new Blob([s2ab(wbout)], {
            type: "application/octet-stream"
        }),
        'task_report.xlsx'
    );
}

// String to ArrayBuffer
function s2ab(s)
{
    var buf = new ArrayBuffer(s.length);

    var view = new Uint8Array(buf);

    for (var i = 0; i < s.length; i++) {

        view[i] = s.charCodeAt(i) & 0xFF;
    }

    return buf;
}

</script>

</script>


  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <style>
            .task-toolbar {
              display: flex;
              justify-content: space-between;
              align-items: center;
              margin-bottom: 12px;
              gap: 10px;
            }
            .task-search {
              max-width: 340px;
              width: 100%;
            }
            #taskTable thead th {
              background: #2c6d9b;
              color: #fff;
              text-transform: uppercase;
              font-size: 12px;
              letter-spacing: 0.3px;
            }
            #taskTable tbody tr:hover {
              background: #eef7ff !important;
            }
            #taskPagination li a {
              color: #2c6d9b;
              border-radius: 3px;
              margin: 0 2px;
            }
            #taskPagination li.disabled a {
              color: #9aa8b5;
              cursor: not-allowed;
              background: #f5f7f9;
            }
            #taskPagination li.ellipsis span {
              display: block;
              padding: 6px 10px;
              color: #8aa0b2;
            }
            #taskPagination li.active a {
              background: #2c6d9b;
              border-color: #2c6d9b;
              color: #fff;
            }
            .serachcount{
              display: flex;
              gap: 57%;
              margin-bottom: 1%;
            }
          </style>

<script>

$(document).ready(function(){

    // ============================
    // SEARCH BUTTON CLICK
    // ============================

    $('#taskSearchBtn').click(function(){

        var value = $('#taskSearchBox').val().toLowerCase();

        $('#task_table tbody tr').filter(function(){

            $(this).toggle(

                $(this).text().toLowerCase().indexOf(value) > -1

            );

        });

    });

    // ============================
    // ENTER KEY SEARCH
    // ============================

    $('#taskSearchBox').keyup(function(e){

        if(e.keyCode == 13){

            $('#taskSearchBtn').click();
        }

    });

    // ============================
    // RESET BUTTON
    // ============================

    $('#taskResetBtn').click(function(){

        $('#taskSearchBox').val('');

        $('#task_table tbody tr').show();

    });

});

</script>



<div class="serachcount">

    <div id="taskPaginationInfo"
         style="font-size:15px; color:#666; margin-top: 10px;">
    </div>
        <!-- Universal Search -->
    <div style="display:flex; align-items:center; gap:10px; font-size: 15px;">
<label for="Project/Client/Task Search">Project/Client/Task Search</label>
        <input type="text"
               id="taskSearchBox"
               class="form-control task-search"
               placeholder="Search Project / Client / Task"
               style="width:280px;">
    </div>

</div>


          <div class="table-responsive" id="task_table">
            <table class="table table-hover table-bordered" id="taskTable">
              <thead>
                <tr>
                  <th>Sno</th>
                  <th>Task Name</th>
				  <th>Project Name</th>
                  <th>Client Name</th>
				  <th>Created By</th>
				  <th>Status</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php 
				  $i=1;
				  foreach ($getTaskList as $key => $taskResult) :
				 	 if($i%2 == 0): $showRowColour = 'class="success"'; else: $showRowColour = 'class="info"'; endif;				  
				  	 $createdExp 		= explode(" " , $taskResult->created_at);	
					 
					 if($taskResult->status == 'Process'):

							$statusClass = 'class="label label-success"';
						
					 elseif($taskResult->status == 'Pending'):

							$statusClass = 'class="label label-warning"';				 
					 else:				 
							$statusClass = 'class="label label-danger"';
					 
					 endif;
					 $statusValue = trim((string)$taskResult->status);
					 if ($statusValue !== 'Process' && $statusValue !== 'Closed') {
						$statusValue = 'Process';
					 }
				 ?>
                <tr <?php echo $showRowColour; ?> id="delTaskRow<?php echo $taskResult->task_Id; ?>">
                  <td><?php echo $i ?></td>
                  <td><?php echo ucfirst($taskResult->task_name);?> </td>
				  <td><?php echo ucfirst($taskResult->project_name);?> </td>
				  <td><?php echo ucfirst($taskResult->client_name);?> </td>
				  <td><span class="label label-info"><?php echo ucfirst($taskResult->name);?></span></td>
				  <td>
					<a href="javascript:void(0)" class="task-status-toggle" data-task-id="<?php echo (int)$taskResult->task_Id; ?>" data-current-status="<?php echo htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8'); ?>">
						<span <?php echo $statusClass; ?>><?php echo $taskResult->status;?></span>
					</a>
				  </td>
                  <th><?php echo date('d-M-Y',strtotime($createdExp[0]));?></th>
                   <?php if(!empty($createdUser == $taskResult->empId)):?>
                  <th>
                <a href="<?php echo base_url(); ?>task/add/<?php echo $taskResult->task_Id; ?>/<?php echo $taskResult->client_Id; ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a> 
                  <!-- | <a style="cursor:pointer;" data-toggle="tooltip" title="Delete" onClick="delete_project(<?php echo $taskResult->task_Id;?>)"><i class="fa fa-sm fa-trash"></i></a> -->
                  </th>
                  <?php else: ?>
                    <th> - - </th>
                    <?php endif; ?>
				 </tr>
                <?php $i++; endforeach; ?>
              </tbody>
            </table>
            <div id="taskPaginationWrapper" style="margin-top:10px; display:flex; justify-content:flex-end; align-items:center;">
              <ul id="taskPagination" class="pagination" style="margin:0;"></ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Inlude Footer here -->
<script type="text/javascript">
function delete_project(task_Id){ 
var answer = confirm ("Are you sure you want to delete task?");
if (answer) {
        $.ajax({
                type: "POST",
                url: "<?php echo base_url('task/delete');?>",
                data: "task_Id="+task_Id,
				beforeSend: function() {
   							 $('#delTaskRow'+task_Id).html('<i class="fa fa-spinner"></i>');
 				 },success: function (response) { 	
					      
				       $("#delTaskRow"+task_Id).remove("#delTaskRow"+task_Id).html('');
			     }
            });
      }
}

// Dynamic pagination (50 rows per page) for task list.
(function($){
  var pageSize = 50;
  var currentPage = 1;
  var $table = $('#taskTable');
  if (!$table.length) return;
  var $allRows = $table.find('tbody tr');
  var $filteredRows = $allRows;

  function renderPage(page) {
    var totalRows = $filteredRows.length;
    var totalPages = Math.max(1, Math.ceil(totalRows / pageSize));
    currentPage = Math.min(Math.max(page, 1), totalPages);
    var start = (currentPage - 1) * pageSize;
    var end = start + pageSize;

    $allRows.hide();
    $filteredRows.slice(start, end).show();

    var from = totalRows === 0 ? 0 : (start + 1);
    var to = Math.min(end, totalRows);
    $('#taskPaginationInfo').text('Showing ' + from + ' to ' + to + ' of ' + totalRows + ' tasks');

    var html = '';
    var prevDisabled = currentPage === 1 ? ' class="disabled"' : '';
    var nextDisabled = currentPage === totalPages ? ' class="disabled"' : '';

    function pageBtn(p) {
      var active = p === currentPage ? ' class="active"' : '';
      return '<li' + active + '><a href="#" data-page="' + p + '">' + p + '</a></li>';
    }

    html += '<li' + prevDisabled + '><a href="#" data-page="' + (currentPage - 1) + '">Prev</a></li>';

    if (totalPages <= 9) {
      for (var p = 1; p <= totalPages; p++) {
        html += pageBtn(p);
      }
    } else {
      html += pageBtn(1);
      var startWindow = Math.max(2, currentPage - 2);
      var endWindow = Math.min(totalPages - 1, currentPage + 2);

      if (startWindow > 2) {
        html += '<li class="ellipsis"><span>...</span></li>';
      }

      for (var p2 = startWindow; p2 <= endWindow; p2++) {
        html += pageBtn(p2);
      }

      if (endWindow < totalPages - 1) {
        html += '<li class="ellipsis"><span>...</span></li>';
      }

      html += pageBtn(totalPages);
    }

    html += '<li' + nextDisabled + '><a href="#" data-page="' + (currentPage + 1) + '">Next</a></li>';

    $('#taskPagination').html(html);
  }

  function applySearch() {
    var term = $.trim($('#taskSearchBox').val()).toLowerCase();
    if (!term) {
      $filteredRows = $allRows;
    } else {
      $filteredRows = $allRows.filter(function(){
        return $(this).text().toLowerCase().indexOf(term) !== -1;
      });
    }
    renderPage(1);
  }

  $('#taskPagination').on('click', 'a', function(e){
    e.preventDefault();
    var page = parseInt($(this).data('page'), 10);
    if (!isNaN(page)) renderPage(page);
  });

  $('#taskSearchBox').on('keyup input', function(){
    applySearch();
  });

  renderPage(1);
})(jQuery);

$(document).on('click', '.task-status-toggle', function() {
  var $toggle = $(this);
  var taskId = $toggle.data('task-id');
  var currentStatus = ($toggle.data('current-status') || '').toString();

  $.ajax({
    type: 'POST',
    url: "<?php echo base_url('task/update_status'); ?>",
    dataType: 'json',
    data: { task_Id: taskId, current_status: currentStatus },
    success: function(res) {
      if (res && res.success) {
        var newStatus = (res.new_status || '').toString();
        var cls = 'label label-success';
        if (newStatus === 'Closed') cls = 'label label-danger';
        if (newStatus === 'Pending') cls = 'label label-warning';
        $toggle.data('current-status', newStatus);
        $toggle.find('span').attr('class', cls).text(newStatus);
        alert(res.message || ('Task status updated to ' + newStatus));
      } else {
        alert((res && res.message) ? res.message : 'Failed to update task status');
      }
    },
    error: function() {
      alert('Something went wrong while updating status');
    }
  });
});
</script>
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
