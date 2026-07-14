<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
$createdUser = $this->session->userdata['logged_in_timesheet']['empId'];
?>
<!-- Inlude Header here END-->


<link href="<?php echo HTTP_CSS_PATH; ?>kpi-style.css" rel="stylesheet" />
<body id="kpiPage">
    
<div class="content-wrapper">
  <div  class="page-title">      
    <div>
      <h1>Manage KPI</h1>
    </div>
  </div>
  <div class="card">
		<h3 class="card-title"></h3>
		<div class="card-body">
            
<div class="four-report-btn " style="margin-left: 9px;">
    
    <button onclick="redirectToDashboard()" class="btn btn-primary"  style="background-color: #014b88; font-weight: bold; border: 2px solid white;">Dashboard</button>   
    <button onclick="redirectToMonthlyReport()" class="btn btn-primary"> Monthly KPI Report</button>
    <button onclick="redirectToConsolidatedReport()" class="btn btn-primary">Consolidated KPI Report</button>
<!--    <button onclick="redirectToDA()" class="btn btn-primary">Detailed Analysis</button>-->

</div>        
            
<div class="row mt-4">  
    <div style="margin-left: 8%;">
        <h5>Dashboard</h5>
    </div>  
    
    
    
    
    
    </div>                  
    </div>
	</div>    
    
  
  <div  class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
            <div id="content-wrapper" class="d-flex flex-column">
                <!-- Begin Page Content -->
                <div class="container-fluid">                    


<div class="row mt-4 d-flex justify-content-center">
    <div class="col-md-11 d-flex justify-content-center">
        
       
          <div class="col-lg-6" >
            <div  class="card border-left-primary shadow" >
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Total Business Hours for each month</div>
                    
                    <!-- Scrollable preview of business hours for months -->
                     <div class="month-item" style="">
<div  class="scrollable-months mt-3" style="max-width: 100%; overflow-x: auto; white-space: nowrap;">
   
                         <div class="month-item">
                            <div class="month-details">
                                <span class="month-name"><strong>January:</strong></span>
                                <span class="month-hours">120 hours</span>
                            </div>
                        </div>
                        <div class="month-item">
                            <div class="month-details">
                                <span class="month-name"><strong>February:</strong></span>
                                <span class="month-hours">140 hours</span>
                            </div>
                        </div>
                        <div class="month-item">
                            <div class="month-details">
                                <span class="month-name"><strong>March:</strong></span>
                                <span class="month-hours">160 hours</span>
                            </div>
                        </div>
                        <div class="month-item">
                            <div class="month-details">
                                <span class="month-name"><strong>April:</strong></span>
                                <span class="month-hours">130 hours</span>
                            </div>
                        </div>
                        <div class="month-item">
                            <div class="month-details">
                                <span class="month-name"><strong>May:</strong></span>
                                <span class="month-hours">145 hours</span>
                            </div>
                        </div>
                        <div class="month-item">
                            <div class="month-details">
                                <span class="month-name"><strong>June:</strong></span>
                                <span class="month-hours">155 hours</span>
                            </div>
                        </div>
                        <div class="month-item">
                            <div class="month-details">
                                <span class="month-name"><strong>July:</strong></span>
                                <span class="month-hours">170 hours</span>
                            </div>
                        </div>
                        <div class="month-item">
                            <div class="month-details">
                                <span class="month-name"><strong>August:</strong></span>
                                <span class="month-hours">180 hours</span>
                            </div>
                        </div>
                        <div class="month-item">
                            <div class="month-details">
                                <span class="month-name"><strong>September:</strong></span>
                                <span class="month-hours">160 hours</span>
                            </div>
                        </div>
                        <div class="month-item">
                            <div class="month-details">
                                <span class="month-name"><strong>October:</strong></span>
                                <span class="month-hours">155 hours</span>
                            </div>
                        </div>
                        <div class="month-item">
                            <div class="month-details">
                                <span class="month-name"><strong>November:</strong></span>
                                <span class="month-hours">140 hours</span>
                            </div>
                        </div>
                        <div class="month-item">
                            <div class="month-details">
                                <span class="month-name"><strong>December:</strong></span>
                                <span class="month-hours">150 hours</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>


        
<div class="col-lg-6">
    <div class="card border-left-primary shadow" >
        <div class="card-body d-flex flex-column justify-content-center">
            <!-- Heading Section -->
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                Employee Overview
            </div>

            <!-- Department Dropdown -->
            <div class="mb-3">
                <label for="departmentSelect" class="form-label">Select Department</label>
                <select id="departmentSelect" class="form-control" onchange="updateProjectManagerOptions()">
                    <option value="" disabled selected>Select a Department</option>
                    <option value="HR">HR</option>
                    <option value="IT">IT</option>
                    <option value="Sales">Sales</option>
                    <option value="Sales">Sales</option>
                    <option value="Sales">Sales</option>
                    <option value="Sales">Sales</option>
                    <option value="Sales">Sales</option>
                    <!-- Add more departments here -->
                </select>
            </div>

            <!-- Project Manager Dropdown (Initially Hidden) -->
            <div class="mb-3" id="projectManagerSection" style="display: none;">
                <label for="projectManagerSelect" class="form-label">Select Project Manager</label>
                <select id="projectManagerSelect" class="form-control" onchange="updateEmployeeCount()">
                    <option value="" disabled selected>Select a Project Manager</option>
                </select>
            </div>

            <!-- Employee Count Display (Initially Hidden) -->
            <div id="employeeCountSection" style="display: none;">
                <p id="employeeCountText"></p>
            </div>
        </div>
    </div>
</div>
        
        
        
<script>
    // Function to update Project Manager options based on selected department
    function updateProjectManagerOptions() {
        const departmentSelect = document.getElementById('departmentSelect');
        const projectManagerSelect = document.getElementById('projectManagerSelect');
        const projectManagerSection = document.getElementById('projectManagerSection');
        const employeeCountSection = document.getElementById('employeeCountSection');
        
        const department = departmentSelect.value;

        // Reset the Project Manager dropdown and Employee Count section
        projectManagerSelect.innerHTML = '<option value="" disabled selected>Select a Project Manager</option>';
        employeeCountSection.style.display = 'none';

        if (department) {
            projectManagerSection.style.display = 'block';

            // Dynamically populate project managers based on department
            let projectManagers = [];
            if (department === 'HR') {
                projectManagers = ['Alice', 'Bob','Ras','Jess'];
            } else if (department === 'IT') {
                projectManagers = ['Charlie', 'David'];
            } else if (department === 'Sales') {
                projectManagers = ['Eve', 'Frank'];
            }

            // Add project managers to the dropdown
            projectManagers.forEach(manager => {
                const option = document.createElement('option');
                option.value = manager;
                option.textContent = manager;
                projectManagerSelect.appendChild(option);
            });
        } else {
            projectManagerSection.style.display = 'none';
        }
    }

    // Function to update the Employee count based on selected Project Manager
    function updateEmployeeCount() {
        const projectManagerSelect = document.getElementById('projectManagerSelect');
        const employeeCountSection = document.getElementById('employeeCountSection');
        const employeeCountText = document.getElementById('employeeCountText');

        const projectManager = projectManagerSelect.value;

        // Display employee count based on selected project manager
        let employeeCount = 0;
        if (projectManager === 'Alice') {
            employeeCount = 10;
        } else if (projectManager === 'Bob') {
            employeeCount = 8;
        } else if (projectManager === 'Charlie') {
            employeeCount = 15;
        } else if (projectManager === 'David') {
            employeeCount = 12;
        } else if (projectManager === 'Eve') {
            employeeCount = 9;
        } else if (projectManager === 'Frank') {
            employeeCount = 11;
        }
        else if (projectManager === 'Ras') {
            employeeCount = 6;
        }
        else if (projectManager === 'Jess') {
            employeeCount = 1;
        }



        // Show the employee count
        employeeCountText.textContent = `Number of employees: ${employeeCount}`;
        employeeCountSection.style.display = 'block';
    }
</script>
        
</div> 
    
 </div>  
                    
<div>&nbsp;</div> 
		<div class="card-body">           
<div class="row mt-4">  
    <div >
        <h5 style="margin-left: -620%;">Month-wise</h5>
    </div> 
    
                      
<div class="container-fluid d-flex align-items-center" >     
    <div class="row">    
    <div class="col-lg-3">
            <div class="card border-left-primary shadow" >
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                       Overall Performer </div>
             <div class="d-flex align-items-center mb-2">
                        
<div style="display: flex; justify-content: center; align-items: center; height: 100%; min-height: 200px; text-align: center;">
    <div style="display: flex; flex-direction: column; align-items: center;">
        <i class='fas fa-trophy' style='font-size:70px;color:orange; margin-left:100px;'></i>
          <div class="text-xs font-weight-bold text-main text-uppercase mb-0" style= "margin-left:96px"> Mark</div>
                    
    </div>
</div>


   
    
</div> 
            
     </div>
            </div>
        </div>
        
        <div class="col-lg-3">
            <div class="card border-left-primary shadow">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Top 5 members based on productivity</div>
                    <div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 85%; background-color: green;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">85%</span>
</div>               
<div class="d-flex align-items-center mb-2 ">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark333333344444444444444</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="height: 6px; width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 75%; background-color: green;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">75%</span>
</div>                  
<div class="d-flex align-items-center mb-2 ">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark333333344444444444444</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="height: 6px; width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 60%; background-color: orange;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">60%</span>
</div>
<div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 54%; background-color: orange;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">54%</span>
</div>
<div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 48%; background-color: orangered;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">48%</span>
</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3">
            <div class="card border-left-primary shadow">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Top 5 members based on utilization</div>
                    <div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 85%; background-color: green;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">85%</span>
</div>               
<div class="d-flex align-items-center mb-2 ">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark333333344444444444444</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="height: 6px; width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 75%; background-color: green;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">75%</span>
</div>                  
<div class="d-flex align-items-center mb-2 ">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark333333344444444444444</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="height: 6px; width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 60%; background-color: orange;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">60%</span>
</div>
<div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 54%; background-color: orange;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">54%</span>
</div>
<div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 48%; background-color: orangered;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">48%</span>
</div>
                </div>
            </div>
        </div>  
        
        <div class="col-lg-3">
            <div class="card border-left-primary shadow">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Top 5 members based on lowest project general hours</div>
                    <div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 85%; background-color: green;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">85%</span>
</div>               
<div class="d-flex align-items-center mb-2 ">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark333333344444444444444</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="height: 6px; width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 75%; background-color: green;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">75%</span>
</div>                  
<div class="d-flex align-items-center mb-2 ">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark333333344444444444444</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="height: 6px; width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 60%; background-color: orange;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">60%</span>
</div>
<div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 54%; background-color: orange;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">54%</span>
</div>
<div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 48%; background-color: orangered;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">48%</span>
</div>
                </div>
            </div>
        </div> 
        
    </div> 
</div>     
    
  
    </div> 
          <div class="row mt-4">    
                    <div >
        <h5 style="margin-left: 17%;">Overall</h5>
    </div> 
    
    <div class="container-fluid d-flex align-items-center" >
    <div class="row">    
    <div class="col-lg-3">
            <div class="card border-left-primary shadow" >
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Overall Performer</div>
                    
              <div class="d-flex align-items-center mb-2">
                        
<div style="display: flex; justify-content: center; align-items: center; height: 100%; min-height: 200px; text-align: center;">
    <div style="display: flex; flex-direction: column; align-items: center;">
        <i class='fas fa-trophy' style='font-size:70px;color:orange; margin-left:100px;'></i>
          <div class="text-xs font-weight-bold text-main text-uppercase mb-0" style= "margin-left:96px"> Mark</div>
                    
    </div>
</div>


   
    
</div> 



                </div>
            </div>
        </div>
        
        <div class="col-lg-3">
            <div class="card border-left-primary shadow">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Top 5 members based on productivity</div>
<div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 85%; background-color: green;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">85%</span>
</div>               
<div class="d-flex align-items-center mb-2 ">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark333333344444444444444</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="height: 6px; width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 75%; background-color: green;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">75%</span>
</div>                  
<div class="d-flex align-items-center mb-2 ">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark333333344444444444444</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="height: 6px; width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 60%; background-color: orange;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">60%</span>
</div>
<div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 54%; background-color: orange;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">54%</span>
</div>
<div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 48%; background-color: orangered;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">48%</span>
</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3">
            <div class="card border-left-primary shadow">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Top 5 members based on utilization</div>
     <div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 85%; background-color: green;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">85%</span>
</div>               
<div class="d-flex align-items-center mb-2 ">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark333333344444444444444</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="height: 6px; width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 75%; background-color: green;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">75%</span>
</div>                  
<div class="d-flex align-items-center mb-2 ">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark333333344444444444444</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="height: 6px; width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 60%; background-color: orange;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">60%</span>
</div>
<div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 54%; background-color: orange;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">54%</span>
</div>
<div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 48%; background-color: orangered;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">48%</span>
</div>
                </div>
            </div>
        </div>
                <div class="col-lg-3">
            <div class="card border-left-primary shadow">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Top 5 members based on least project general hours</div>
     <div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 85%; background-color: green;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">85%</span>
</div>               
<div class="d-flex align-items-center mb-2 ">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark333333344444444444444</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="height: 6px; width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 75%; background-color: green;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">75%</span>
</div>                  
<div class="d-flex align-items-center mb-2 ">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark333333344444444444444</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="height: 6px; width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 60%; background-color: orange;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">60%</span>
</div>
<div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 54%; background-color: orange;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">54%</span>
</div>
<div class="d-flex align-items-center mb-2">
    <!-- Dot (Green) -->
    <span class="dot"></span>
    <div class="h6 mb-0 font text-800 col-v-space">Mark</div>
    
    <!-- Progress Bar -->
    <div class="progress" style="width: 150px; height: 6px; margin-left: 45px;">
        <!-- Green Progress (80% of total width) -->
        <div class="progress-bar progress-bar" role="progressbar" style="width: 48%; background-color: orangered;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- 80% Text -->
    <span class="ml-2" style="font-size: 14px; color: gray;">48%</span>
</div>
                </div>
            </div>
        </div>
    </div>    
 </div>   
    
  
    </div>                  
    </div>
                   






                </div>
                <!-- End of Page Content -->
        </div>
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