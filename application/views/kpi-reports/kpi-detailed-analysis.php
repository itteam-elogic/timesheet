<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
$createdUser = $this->session->userdata['logged_in_timesheet']['empId'];
?>
<!-- Inlude Header here END-->


<link href="<?php echo HTTP_CSS_PATH; ?>kpi-style.css" rel="stylesheet" />
<body id="kpiPage">
<div class="content-wrapper">
  <div class="page-title">      
    <div>
      <h1>Manage KPI</h1>
    </div>

  </div>
  <div class="card">
		<h3 class="card-title"></h3>
		<div class="card-body">
            
<div class="four-report-btn " style="margin-left: 9px;">
    
    <button onclick="redirectToDashboard()" class="btn btn-primary">Dashboard</button>   
    <button onclick="redirectToMonthlyReport()" class="btn btn-primary"> Monthly KPI Report</button>
    <button onclick="redirectToConsolidatedReport()" class="btn btn-primary">Consolidated KPI Report</button>
    <button onclick="redirectToDA()" class="btn btn-primary"   style="background-color: #014b88; font-weight: bold; border: 2px solid white;">Detailed Analysis</button>

</div>         
                        
<div class="row mt-4">  
    <div style="margin-left: 67%;">
        <h5>Detailed Analysis</h5>
    </div>
           
</div>                  
		</div>
	</div>    


<script>
function redirectToDashboard() {
   

    // Add animation effect (optional)
    const button = document.querySelector('.four-report-btn button:nth-child(3)');
    button.classList.add('active');

    // Wait for 300ms (like toggle switch) and then redirect
    setTimeout(function() {
        window.location.href = "<?php echo base_url('kpi_reports/dashboard');?>";
    }, 300);
}      
        
      // Function to handle Consolidated KPI Report button click
function redirectToMonthlyReport() {
    // Add animation effect (optional)
    const button = document.querySelector('.four-report-btn button:nth-child(3)');
    button.classList.add('active');

    // Wait for 300ms (like toggle switch) and then redirect
    setTimeout(function() {
        window.location.href = "<?php echo base_url('kpi_reports/index');?>";
    }, 300); // 300ms delay
}

    // Function to handle Consolidated KPI Report button click
function redirectToConsolidatedReport() {
    

    // Add animation effect (optional)
    const button = document.querySelector('.four-report-btn button:nth-child(3)');
    button.classList.add('active');

    // Wait for 300ms (like toggle switch) and then redirect
    setTimeout(function() {
        window.location.href = "<?php echo base_url('kpi_reports/consolidatedReport');?>";
    }, 300);
}

function redirectToDA() {
    

    // Add animation effect (optional)
    const button = document.querySelector('.four-report-btn button:nth-child(3)');
    button.classList.add('active');

    // Wait for 300ms (like toggle switch) and then redirect
    setTimeout(function() {
        window.location.href = "<?php echo base_url('kpi_reports/detailed_analysis');?>";
    }, 300);
}       

</script>
<?php $this->load->view('includes/cRMFooter'); ?>