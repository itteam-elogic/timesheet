<?php $this->load->view('includes/header'); ?>
<!-- Middle Part Start -->

<div class="mainpanel">
  <div class="contentpanel">
  <div class="row">
  
  <div class="col-md-11">
    <ol class="breadcrumb breadcrumb-quirk">
      <li><a href="<?php echo base_url(); ?>Home"><i class="fa fa-home mr5"></i> Home</a></li>
      <li class="active"><i class="fa fa-users"></i> Manage Employees</li>
	
	 </ol>
	 </div>
	   <div class="col-md-1">
	  <a href="<?php echo base_url(); ?>employee/add" class="btn btn-success" title="Add Employee" ><i class="fa fa-lg fa-user-plus" aria-hidden="true"> Add </i></a>
	   </div>
	</div>
	<br />
    <div class="panel">
      <div class="panel-heading">
        <h4 class="panel-title">Manage Employees</h4>
        <p>List of employees.</p>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table id="dataTable1" class="table table-bordered table-striped-col">
            <thead>
              <tr>
			    <th>Sno</th>
                <th>Name</th>
                <th>Position</th>
                <th>Status</th>
                <th>Start date</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
			    <th>Sno</th>
                <th>Name</th>
                <th>Position</th>
                <th>Status</th>
                <th>Start date</th>
                <th class="text-center">Action</th>
              </tr>
            </tfoot>
            <tbody>
               <?php for($i=1; $i<=200; $i++): ?>
			  <tr>
                <td><?php echo $i; ?></td>
				<td>Tiger Nixon</td>
                <td>System Architect</td>
                <td>Edinburgh</td>
                <td>61</td>
                <td>
                    <ul class="table-options">
                      <li><a href=""><i class="fa fa-pencil"></i></a></li>
                      <li><a href=""><i class="fa fa-trash"></i></a></li>
                    </ul>
                 </td>
              </tr>
			  <?php endfor; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- panel -->
  </div>
  <!-- contentpanel -->
</div>
<!-- Middle Part Start END -->
<?php $this->load->view('includes/footer'); ?>
