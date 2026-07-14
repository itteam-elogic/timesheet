<!-- Include Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<!-- Include Header here END-->

<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h1><i class="fa fa-clock-o"></i> Timesheet</h1>
        </div>
        <div>
            <a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url('tsr'); ?>">
                <i class="fa fa-lg fa-refresh"></i>
            </a>
        </div>
    </div>
    <div class="card">
        <h3 class="card-title"></h3>
        <div class="card-body">
            <form name="timesheet_search" id="timesheet_search" method="post" action="<?php base_url('timesheet'); ?>">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Client's</label>
                            <select class="form-control" id="client_Id" name="client_Id" onChange="searchProjects(this.value);">
                                <option value="all">All</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Project's</label>
                            <select class="form-control" id="project_Id" name="project_Id" onchange="searchProjectWiseTask(this.value)">
                                <option value="all">All</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Task's</label>
                            <select class="form-control" id="task_Id" name="task_Id">
                                <option value="all">All</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Employee's</label>
                            <select class="form-control" id="empId" name="empId">
                                <option value="all">All</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">From</label>
                            <input class="form-control" type="text" id="form_date" name="form_date" placeholder="Select From Date" value="" readonly="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">To</label>
                        <input class="form-control" type="text" id="to_date" name="to_date" placeholder="Select To Date" value="" readonly="">
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary icon-btn">
                        <i class="fa fa-fw fa-lg fa-check-circle"></i> Search
                    </button>
                    <a href="<?php echo base_url(); ?>empreports" data-toggle="Go To Report Log!" title="Cancel">
                        <button class="btn btn-default icon-btn" type="button">
                            <i class="fa fa-chevron-circle-left"></i> Back
                        </button>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php if (count($allTsrResult) != 0) : ?>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <?php if (!empty($allTsrResult)) : ?>
                        <div align="center">
                            <a href="<?php echo base_url()?>tsr/excel?form_date=<?php echo $_REQUEST['form_date'];?>&to_date=<?php echo $_REQUEST['to_date'];?>">
                                <button class="btn btn-primary icon-btn">
                                    <i class="fa fa-fw fa-lg fa-check-circle"></i> Export To Excel Report
                                </button>
                            </a>
                        </div>
                    <?php endif; ?>
                    <!-- Displaying Search Result -->
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="organisationTable">
                                <thead>
                                    <tr>
                                        <th>Sno</th>
                                        <th>Name</th>
                                        <th>Client Name</th>
                                        <th>Project Name</th>
                                        <th>Task Name</th>
                                        <th>Hours</th>                                        
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>E.Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;                                   
                                    if (!empty($allTsrResult)) :
                                        foreach ($allTsrResult as $key => $reportResult) :
                                            
                                    ?>
                                            <tr>
                                                <td><?php echo $i ?></td>
                                                <td nowrap="nowrap"><span class="label label-info"><?php echo ucfirst($reportResult->name); ?></span></td>
                                                <td nowrap="nowrap"><?php echo ucfirst($reportResult->client_name); ?> </td>
                                                <td nowrap="nowrap"><?php echo ucfirst($reportResult->project_name); ?> </td>
                                                <td nowrap="nowrap"><a href="#" data-toggle="tooltip"><?php echo $reportResult->task_name; ?></a></td>
                                                <td nowrap="nowrap"><?php echo ucfirst($reportResult->emp_time_hours); ?> </td>
                                                <td nowrap="nowrap"> <span class="<?php echo ($reportResult->status == 'Approved') ? 'fa fa-check-circle label label-success' : (($reportResult->status == 'Rejected') ? 'fa fa-registered label label-warning' : 'fa fa-ban label label-danger'); ?>"> <?php echo $reportResult->status; ?></span></td>
                                                <th nowrap="nowrap"><?php echo date('d-M-Y', strtotime($reportResult->emp_report_dates)); ?></th>
                                                <th nowrap="nowrap"><span class="me-1 badge bg-secondary"><?php echo date('d-M-Y', strtotime($reportResult->created_at)); ?></span></th>
                                            </tr>
                                    <?php
                                            $i++;
                                        endforeach;
                                    endif;
                                    ?>                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Displaying Search Result -->
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script language="javascript" type="text/javascript">
    $(function() {
        $("form[name='timesheet_search']").validate({
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

    var searchAllVar;

    $(document).ready(function() {
        var today = $("#form_date").val();
        $("#form_date, #to_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            numberOfMonths: 1,
            onSelect: function(selectedDate) {
                if (this.id == 'form_date') {
                    var dateMin = $('#form_date').datepicker("getDate");
                    var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(), dateMin.getDate());
                    var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(), dateMin.getDate() + 365);
                    $('#to_date').datepicker("option", "minDate", rMin);
                    $('#to_date').datepicker("option", "maxDate", rMax);
                }
            }
        });
        $('#to_date').datepicker("option", "minDate", new Date(today));
    });

    $('#client_Id,#project_Id,#empId,#task_Id').select2(); // Autosuggest list
</script>

<!-- Include Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Include Footer here END-->
