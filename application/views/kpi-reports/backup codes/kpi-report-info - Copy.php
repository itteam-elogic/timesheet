<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 
$createdUser = $this->session->userdata['logged_in_timesheet']['empId'];
?>
<!-- Inlude Header here END-->

<div class="content-wrapper">
  <div class="page-title">
    
      <h1>Manage KPI</h1>
        
                <!-- Begin Page Content -->
                <div class="container-fluid">

                    
                     <!-- Toggle Switch (Detailed Analysis/Consolidated) -->
                    <div class="toggle-switch-container" id="kpiPage">
                        <div class="toggle" id="toggleSwitch">
                            <span class="label-left">KPI Report</span>
                            <div class="slider"></div>
                            <span class="label-right">Consolidated Report</span>
                        </div>
                    </div>
        </div>
      </div>
      <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">              
<!-- Page Heading -->
<div class="row mt-4">  
    <div>
        <h3>KPI Report</h3>
    </div>
    <div class="col-md-8">&nbsp;</div>    
    
    <div class="generate-report-btn " style="margin-left: -45px;">
    <a href="#" class="btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-download fa-sm"></i>&nbsp;Generate Report
    </a>
</div>   
</div>

                    
                    <!-- 2x3 Dropdowns (Adjustable Width) -->
                    <div class="row">
                        
                        
                        <div class="col-md-3">
                            <label for="dropdown2">Select a Department</label>
                            <select id="dropdown2" class="form-control" style="width: 325px;">
                                <option selected>Select a Department</option>
                                <option>Architecture</option>
                                <option>MEP</option>                  
                            </select>
                            </div>
                        
                    
                     <div class="col-md-3">
                            <label for="dropdown2">Reporting Manager's</label>
                            <select id="dropdown2" class="form-control" style="width: 325px;">
                                <option selected>Select a Reporting Manager</option>
                                <option>Option 1</option>
                                <option>Option 2</option>
                                <option>Option 3</option>                      
                            </select>
                            </div>
                        
                        
                         <div class="col-md-3">
                            <label for="dropdown2">Employee's</label>
                            <select id="dropdown2" class="form-control" style="width: 325px;">
                                <option selected>Select a Employee</option>
                                <option>Option 1</option>
                                <option>Option 2</option>
                                <option>Option 3</option>                      
                            </select>
                            </div>
                        
                        </div>
 
                    <div class="row">
                        
                        <div class="col-md-3">
                            <label for="dropdown2">Select a Month</label>
                            <select id="dropdown2" class="form-control" style="width: 325px;">
                                <option selected>Select a Month</option>
                                <option>January</option>
                                <option>February</option>
                                <option>March</option>
                                <option>April</option>
                                <option>May</option>
                                <option>June</option>
                                <option>July</option>
                                <option>August</option>
                                <option>September</option>
                                <option>October</option>
                                <option>November</option>
                                <option>December</option>
                            </select>
                            </div>
                
                        </div>
                    
                    
                    <!-- January KPI Report Heading -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h3 id="kpiReportHeading">January KPI Report</h3>
                        </div>
                    </div>

  
<div class="row mt-3">
    
    <!-- Total Business Days Box -->
    <div class="col-md-6">
        <div class="info-box">
            <h4 id="business_days">Total Business Days</h4>
            <p>100 Days</p>
        </div>
    </div>

    <!-- Business Hours Box -->
    <div class="col-md-6">
        <div class="info-box">
            <h4 id="business_hours">Total Business Hours</h4>
            <p>100 Hours</p>
        </div>
    </div>
    
</div>


<div class="row mt-4">
    <div class="col-md-12">
        <table id="employeeTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Reporting Manager</th>
                    <th>EE ID</th>
                    <th>Employee</th>
                    <th>Productive Hours</th>
                    <th>Project General Hours</th>
                    <th>eLogic General Hours</th>
                    <th>Total Available Hours</th>
                    <th>Productive Hours %</th>
                    <th>Project General Hours %</th>
                    <th>eLogic Hours %</th>
                    <th>Availability %</th>
                    <th>Utilization %</th>
                </tr>
            </thead>
            <tbody>
                
                <!--Bob Johnson as Reporting Manager -->
                <tr><td>Architecture</td><td rowspan="3" style="background-color: #c5e1a5;">Bob Johnson</td><td>127</td><td>Emily White</td><td>78</td><td>80</td><td>5</td><td>9</td><td>87</td><td>91</td><td>83</td><td>90</td><td>92</td></tr>
                <tr><td>Architecture</td><td>128</td><td>Chris Blue</td><td>79</td><td>76</td><td>82</td><td>80</td><td>83</td><td>85</td><td>2</td><td>92</td><td>78</td></tr>
                <tr><td>Architecture</td><td>129</td><td>Jordan Black</td><td>80</td><td>82</td><td>85</td><td>0</td><td>84</td><td>79</td><td>88</td><td>90</td><td>92</td></tr>

                <!--David Brown as Reporting Manager -->
                <tr><td>MEP</td><td rowspan="6" style="background-color: #80deea;">David Brown</td><td>130</td><td>Alex Green</td><td>95</td><td>93</td><td>91</td><td>96</td><td>90</td><td>94</td><td>9</td><td>91</td><td>92</td></tr>
                <tr><td>MEP</td><td>131</td><td>Sam Brown</td><td>85</td><td>90</td><td>78</td><td>91</td><td>88</td><td>79</td><td>92</td><td>80</td><td>92</td></tr>
                <tr><td>MEP</td><td>132</td><td>Emily White</td><td>0</td><td>0</td><td>8</td><td>5</td><td>7</td><td>2</td><td>88</td><td>0</td><td>4</td></tr>
                <tr><td>MEP</td><td>133</td><td>Chris Blue</td><td>78</td><td>76</td><td>2</td><td>80</td><td>83</td><td>85</td><td>88</td><td>8</td><td>78</td></tr>
                <tr><td>MEP</td><td>134</td><td>Jordan Black</td><td>85</td><td>87</td><td>79</td><td>91</td><td>80</td><td>90</td><td>84</td><td>88</td><td>89</td></tr>
                <tr><td>MEP</td><td>135</td><td>John Doe</td><td>90</td><td>85</td><td>92</td><td>86</td><td>81</td><td>88</td><td>88</td><td>90</td><td>88</td></tr>
            </tbody>
        </table>
    </div>
</div>
               
                <!-- End of Page Content -->  
        
    </div>
   
        </div></div></div>
</div>
<!-- Inlude Footer here -->

<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
