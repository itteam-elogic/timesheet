<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
?>
<!-- Inlude Header here END-->

<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1>Client Report Information</h1>
    </div>
   
  </div>
  
	<div class="row" style="padding:6%;">
        <div class="col-md-6">
          <p class="bs-component d-grid">
			  <a href="<?php echo base_url();?>clients/client_ts_report"><button class="btn btn-primary btn-lg btn-block" type="button">Induvidual Client wise Report </button></a>
            </p>
        </div>
        <!-- <div class="col-md-4">
         <p class="bs-component d-grid">
			 <a href="<?php echo base_url();?>clients/client_ts_report"><button class="btn btn btn-info btn-lg btn-block" type="button">Consolidated Client Report</button></a>
            </p>
        </div> -->
        <div class="col-md-6">
          <p class="bs-component d-grid">
			  <a href="<?php echo base_url();?>clients/all_clients_reports"><button class="btn btn-success btn-lg btn-block" type="button">Client TSR</button></a>
            </p>
        </div>
        
        <div class="col-md-6">
          <p class="bs-component d-grid">
			  <!-- <a href="<?php echo base_url();?>clients/rs_vs_ts"><button class="btn btn-primary btn-lg btn-block" type="button">Comparing Resource Schedule and Timesheet Tracking</button></a> -->
            </p>
        </div>
       
      </div>
	
</div>
<!-- Inlude Footer here -->

<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
