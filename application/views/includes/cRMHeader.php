<?php header('Content-Type: text/html; charset=ISO-8859-1');  ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="<?php echo HTTP_IMAGES_PATH;?>ico/favicon.png">
	<!-- CSS-->
	<link rel="stylesheet" type="text/css" href="<?php echo HTTP_CSS_PATH; ?>main.css">
	<link href="<?php echo HTTP_CSS_PATH; ?>timeline.css" rel="stylesheet" />
	<script src="<?php echo HTTP_JS_PATH; ?>jquery-2.1.4.min.js"></script>
	<script src="<?php echo HTTP_JS_PATH; ?>jquery-migrate-1.2.1.js"></script>
	<script src="<?php echo HTTP_JS_PATH; ?>jquery-ui.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo HTTP_CSS_PATH; ?>jquery-ui.css">
	<script src="<?php echo HTTP_JS_PATH; ?>jquery.validate.min.js"></script>
	<script src="<?php echo HTTP_JS_PATH; ?>angular.min.js"></script>
	<script type="text/javascript" src="<?php echo HTTP_JS_PATH; ?>plugins/select2.min.js"></script>
	<title>eLogic Timesheet</title>
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries-->
	<!--if lt IE 9
    script(src='https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js')
    script(src='https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js')
    -->
</head>
<body class="sidebar-mini fixed">
	<div class="wrapper">
		<!-- Navbar-->
		<?php if(!empty($this->session->userdata['logged_in_timesheet']['username'])): 
		    $userDetails =  $this->timesheet_login->user_information($this->session->userdata['logged_in_timesheet']['username']);
		    foreach($userDetails as $key) { $fullname = ucwords($key->name); $designation = ucwords($key->designation);}
		  ?>
		<header class="main-header hidden-print"><a class="logo" href="<?php echo base_url(); ?>"><img src="<?php echo HTTP_IMAGES_PATH; ?>main_logo.png" alt="logo"></a>
			<nav class="navbar navbar-static-top">
				<!-- Sidebar toggle button-->
				<a class="sidebar-toggle" href="#" data-toggle="offcanvas"></a>
				<!-- Navbar Right Menu-->
				<div class="navbar-custom-menu">
					<ul class="top-nav">
						<!-- <li><a href="<?php echo base_url(); ?>lms"><i class="fa fa-file-video-o"></i> <span>LMS</span></a></li> -->
						
						<!--Notification Menu-->
<?php
$username = strtolower($this->session->userdata['logged_in_timesheet']['username']);

if(
    in_array(
        $this->session->userdata['logged_in_timesheet']['user_type'],
        array('admin','business_head','manager','superadmin')
    )
    ||
    in_array(
        $username,
        array(
            'krishna',
            'fayaz',
            'prathyusha',
            'moinpatel',
            'ashish.mattaparthi',
            'yacoob',
            'hyder'
        )
    )
):
?>							
						<!-- <li><a href="<?php echo base_url(); ?>empreports/execution_plan"><i class="fa fa-paper-plane"></i> Execution Plan</a></li> -->
						 <li style="text-align: left;">
							<a href="<?php echo base_url();?>clients/rs_vs_ts" >
								<i class="fa fa-circle blink-icon" style="color: red; font-size: 1em;margin-top: -4px;" ></i>
								<span style="font-size: 1.15em;"> Live Project Allocation & Actual Hours</span>
							</a>
						</li>
						<style>
							.blink-icon {
								animation: blink-animation 0.7s steps(2, start) infinite;
								-webkit-animation: blink-animation 0.7s steps(2, start) infinite;
								font-size: 2em;
								vertical-align: middle;
							}
							@keyframes blink-animation {
								to {
									visibility: hidden;
								}
							}
							@-webkit-keyframes blink-animation {
								to {
									visibility: hidden;
								}
							}
						</style>
						<!-- <li><a href="<?php echo base_url();?>clients/cs_reports"><i class="fa fa-paper-plane" style="color: #008000; font-size: 1em;margin-top: -4px;"></i> Client TSR</a></li> -->
                         <li><a href="<?php echo base_url(); ?>service_agreement"><i class="fa fa-file" style="color: #FFDE21; font-size: 1em;margin-top: -4px;"></i> SOW </a></li>	
						 <!-- <li><a href="<?php echo base_url(); ?>empreports/resource_billability"><i class="fa fa-bell"></i> Resource Billability</a></li>
						 <li><a href="<?php echo base_url(); ?>empreports/email_unapproved_pms"><i class="fa fa-envelope-o"></i> Approved Status</a></li>  -->                       
						<?php endif; ?>
                        <li><a href="<?php echo base_url(); ?>ticket"><i class="fa fa-life-ring" style="color: #F59E0B; font-size: 1em;margin-top: -4px;"></i> IT Help Desk</a></li>
						<!-- <li>
							<a href="<?php echo base_url(); ?>notifications">
								<i class="fa fa-bell" style="color:red; font-size: 1em;margin-top: -4px;"></i>
								<span style="font-size: 1.1em;">Notifications</span> 
							</a>
						</li>-->
						<li>
							<h5 style="color:#FFF;font-size: 1em;">Welcome
								<?php echo $fullname; ?>
							</h5>
						</li>
						<!-- User Menu-->
						<!-- User Menu-->
						<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user fa-lg"></i></a>
							<ul class="dropdown-menu settings-menu">
								<!-- <li><a href="page-user.html"><i class="fa fa-cog fa-lg"></i> Settings</a></li>
                  				<li><a href="page-user.html"><i class="fa fa-user fa-lg"></i> Profile</a></li>-->
								<li><a href="<?php echo base_url(); ?>empreports/cpass"><i class="fa fa-cog fa-lg"></i>Settings</a></li>
								<li><a href="<?php echo base_url(); ?>stickynote"><i class="fa fa-commenting-o"></i> Sticky Note</a></li>
								<li><a  data-toggle="modal" data-target="#2018_holiday_list_img" style="cursor:pointer;"><i class="fa fa-calendar"></i> Holidays List</a></li>
								<li><a href="<?php echo base_url(); ?>home/logout"><i class="fa fa-sign-out fa-lg"></i> Logout</a></li>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
		</header>
		<!-- Side-Nav-->
		<aside class="main-sidebar hidden-print">
			<section class="sidebar">

				<div class="user-panel">
					<div class="pull-left image">
						<?php if(!empty($key->avatar)): ?>
						<img class="img-circle" src="<?php echo base_url().'uploads/employee_pic/'.$key->avatar; ?>" alt="User Image">
						<?php else: ?>
						<img class="img-circle" src="<?php echo HTTP_IMAGES_PATH; ?>default.jpg" alt="User Image">
						<?php endif; ?>
					</div>
					<div class="pull-left info">
						<p>
							<?php echo $fullname; ?>
						</p>
						<p class="designation">
							<?php echo $designation; ?>
						</p>
					</div>
				</div>

				<!-- Sidebar Menu-->
				<ul class="sidebar-menu">
					<li class="active"><a href="<?php echo base_url(); ?>home"><i class="fa fa-dashboard"></i><span>Dashboard</span></a></li>
                    
					 <?php
 
                        $departments = ['Architectural', 'Structural', '3D Visualization','MEP','HR','Business Development','Accounting','2D Auto CAD'];

 
                  if (in_array($key->department,$departments ) || $this->session->userdata['logged_in_timesheet']['user_type'] == 'admin' ): ?> 
                        <li><a href="<?php echo base_url(); ?>kpi_reports/index"><i class="fa fa-folder-open"></i><span>Reports</span></a></li>	
                    <?php endif; ?>
					
					<li><a href="<?php echo base_url(); ?>resource_schedule"><i class="fa fa-key"></i><span>Resource Schedule</span></a></li>					
                    
                    <li><a href="<?php echo base_url(); ?>defaulter/user_defaulter"><i class="fa fa-envelope-o"></i> Timesheet Defaulter</a></li>
                    
                   <!--  <li><a href="<?php echo base_url(); ?>kpi_reports"><i class="fa fa-calendar"></i> KPI Reports </a></li> -->
                    
                    <!-- <li><a href="<?php echo base_url(); ?>lms"><i class="fa fa-file-video-o"></i> <span>LMS</span></a></li> -->
					
					<?php if($this->session->userdata['logged_in_timesheet']['username']=='kanth' ): ?> 
                    <li><a href="<?php echo base_url(); ?>kpi_reports/index"><i class="fa fa-folder-open"></i><span>Reports</span></a></li>	
                    <li><a href="<?php echo base_url(); ?>emp_record_inactivity"><i class="fa fa-exclamation-triangle"></i><span>Timesheet Inactivity (6 Months)</span></a></li>
                    <li><a href="<?php echo base_url(); ?>projects/hours_notifications"><i class="fa fa-envelope"></i><span>Hours Notifications</span></a></li>
                    <li><a href="<?php echo base_url(); ?>data_allocation"><i class="fa fa-exchange"></i><span>Data Allocation</span></a></li>
                    <li><a href="<?php echo base_url(); ?>management_plan"><i class="fa fa-briefcase"></i><span>Management Plan</span></a></li>
                    
					<!-- <li><a href="<?php echo base_url(); ?>lmscategory"><i class="fa fa-users"></i><span>LMS Categories</span></a></li> -->
                    
					<?php endif; ?>
					
                    
					<?php if(in_array($this->session->userdata['logged_in_timesheet']['user_type'], array('admin', 'superadmin'))): ?>
                    
					<!-- <li><a href="<?php echo base_url(); ?>lmsreport"><i class="fa fa-users"></i><span>LMS Report</span></a></li> -->
					
                   <li><a href="<?php echo base_url(); ?>quality_error_log"><i class="fa fa-search"></i><span>Quality Error Log</span></a></li>
                    
					<li><a href="<?php echo base_url(); ?>employee/employee_list_information"><i class="fa fa-users"></i><span>Employees</span></a></li>
                    
					<li><a href="<?php echo base_url(); ?>clients/client_list_information"><i class="fa fa-user-plus"></i><span>Clients</span></a></li>
                    
                    <li><a href="<?php echo base_url(); ?>projects"><i class="fa fa-clone"></i><span>Project Master Report</span></a></li>
                    
                    <li><a href="<?php echo base_url(); ?>management_plan"><i class="fa fa-briefcase"></i><span>Management Plan</span></a></li>
                    
					<li><a href="<?php echo base_url(); ?>task"><i class="fa fa-tasks"></i><span>Task</span></a></li>	
                    
					<li><a href="<?php echo base_url(); ?>timesheet"><i class="fa fa-clock-o"></i><span>Timesheet Reports</span></a></li>
                    
					<li><a href="<?php echo base_url(); ?>empreports"><i class="fa fa-indent"></i><span>Timesheet Logs</span></a></li>
                    
					<li><a href="<?php echo base_url(); ?>empreports/unapproved"><i class="fa fa-ban"></i><span>Unapproved Report Logs</span></a></li>
                    
					 <?php elseif(in_array($this->session->userdata['logged_in_timesheet']['user_type'], array('business_head'))): ?>
                    
					<!-- <li><a href="<?php echo base_url(); ?>lmsreport"><i class="fa fa-users"></i><span>LMS Report</span></a></li> -->                    

                    <li><a href="<?php echo base_url(); ?>quality_error_log"><i class="fa fa-search"></i><span>Quality Error Log</span></a></li>
                    
					<li><a href="<?php echo base_url(); ?>clients/client_list_information"><i class="fa fa-user-plus"></i><span>Clients</span></a></li>
                    
                    <li><a href="<?php echo base_url(); ?>projects"><i class="fa fa-clone"></i><span>Project Master Report</span></a></li>
                    
                    <li><a href="<?php echo base_url(); ?>management_plan"><i class="fa fa-briefcase"></i><span>Management Plan</span></a></li>
                    
					<li><a href="<?php echo base_url(); ?>task"><i class="fa fa-tasks"></i><span>Task</span></a></li>
                    
					<li><a href="<?php echo base_url(); ?>timesheet"><i class="fa fa-clock-o"></i><span>Timesheet Reports</span></a></li>
                    
					<li><a href="<?php echo base_url(); ?>empreports"><i class="fa fa-indent"></i><span>Timesheet Logs</span></a></li>
                    
					
                    <li><a href="<?php echo base_url(); ?>empreports/unapproved"><i class="fa fa-ban"></i><span>Unapproved Report Logs</span></a></li>
					
                    <?php elseif(in_array($this->session->userdata['logged_in_timesheet']['user_type'], array('manager'))): ?>
                    
					<!-- <li><a href="<?php echo base_url(); ?>lmsreport"><i class="fa fa-users"></i><span>LMS Report</span></a></li> -->
					
                    
					<?php if(!empty($this->session->userdata['logged_in_timesheet']['username']=='suman') || $this->session->userdata['logged_in_timesheet']['username']=='kanth' || $this->session->userdata['logged_in_timesheet']['username']=='shirley'): ?>
                    
					<li><a href="<?php echo base_url(); ?>employee/employee_list_information"><i class="fa fa-users"></i><span>Employees</span></a></li>
                    
					<?php endif; ?>
                    
                    <li><a href="<?php echo base_url(); ?>quality_error_log"><i class="fa fa-search"></i><span>Quality Error Log</span></a></li>
                    
					<li><a href="<?php echo base_url(); ?>clients/client_list_information"><i class="fa fa-user-plus"></i><span>Clients</span></a></li>
                    
                    <li><a href="<?php echo base_url(); ?>projects"><i class="fa fa-clone"></i><span>Project Master Report</span></a></li>
                    
                    <li><a href="<?php echo base_url(); ?>management_plan"><i class="fa fa-briefcase"></i><span>Management Plan</span></a></li>
                    
					<li><a href="<?php echo base_url(); ?>task"><i class="fa fa-tasks"></i><span>Task</span></a></li>
                    
					<li><a href="<?php echo base_url(); ?>timesheet"><i class="fa fa-clock-o"></i><span>Timesheet Reports</span></a></li>
                    
					<li><a href="<?php echo base_url(); ?>empreports"><i class="fa fa-indent"></i><span>Timesheet Logs</span></a></li>
                    
					
                    <li><a href="<?php echo base_url(); ?>empreports/unapproved"><i class="fa fa-ban"></i><span>Unapproved Report Logs</span></a></li>
					
                   <?php if(!empty($this->session->userdata['logged_in_timesheet']['username']=='sandeep')): ?>
                    
					<!-- <li><a href="<?php echo base_url(); ?>empreports/pmreportlogs"><i class="fa fa-search"></i><span>PM's Timesheet Logs</span></a></li> -->
                    
					<?php endif; ?>
					
                    <?php elseif($this->session->userdata['logged_in_timesheet']['user_type'] == 'developer'): ?>
					
						<?php 
							
							$userCTRPermissions = array('391', '285', '371', '339', '245','108','136','205','227','248','64','391','475','168','426','191');
							
							if( in_array($this->session->userdata['logged_in_timesheet']['empId'] , $userCTRPermissions)): ?> 	
                    
                                     <li><a href="<?php echo base_url(); ?>quality_error_log"><i class="fa fa-search"></i><span>Quality Error Log</span></a></li>

									 <li><a href="<?php echo base_url();?>clients/cs_reports"><i class="fa fa-paper-plane"></i> Client TS Report</a></li>

							<?php endif; ?>

                    <li><a href="<?php echo base_url(); ?>defaulter/user_defaulter"><i class="fa fa-envelope-o"></i> Timesheet Defaulter</a></li>
					<?php if(!empty($this->session->userdata['logged_in_timesheet']['username']=='krishna')): ?>
					<li><a href="<?php echo base_url(); ?>employee/employee_list_information"><i class="fa fa-users"></i><span>Employees</span></a></li>
					<?php endif; ?>
					<li><a href="<?php echo base_url(); ?>empreports"><i class="fa fa-indent"></i><span>Timesheet Logs</span></a></li>
					<li><a href="<?php echo base_url(); ?>empreports/searchreportlog"><i class="fa fa-search"></i><span>Search Timesheet Logs</span></a></li>
                    <li><a href="<?php echo base_url(); ?>empreports/unapproved"><i class="fa fa-ban"></i><span>Unapproved Report Logs</span></a></li>
					<li><a href="<?php echo base_url(); ?>empreports/cpass"><i class="fa fa-key"></i><span>User Information</span></a></li>
					<?php endif; ?>
                    
				</ul>
			</section>
		</aside>
		<?php endif; ?>
       <!-- <div style="padding-top:30px;"><marquee style="color: #af1313;font-size:20px;position:relative; top:36px; font-size:32px;"  scrolldelay="90">Timesheet Deadline Alert: Timesheet will be locked at midnight on the 4th of every month.</marquee>
			
        </div> -->
<!-- Holiday list of eLogic -->		
		<div id="2018_holiday_list_img" class="modal fade" role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<a type="button" class="close" data-dismiss="modal">&times;</a>
						<h4 class="modal-title">List of Holidays(2024)</h4>
					</div>
					<div class="modal-body">
						<img alt="" src="<?php echo HTTP_IMAGES_PATH;?>holidays/holidays_2025.png" width="100%">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>
				</div>

			</div>
		</div>		
<!-- Holiday List of eLogic -->