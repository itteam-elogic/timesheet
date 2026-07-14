<?php $this->load->view('includes/header'); ?>
<!-- Middle Part Start -->

<div class="mainpanel">
  <!--<div class="pageheader">
      <h2><i class="fa fa-home"></i> Dashboard</h2>
    </div>-->
  <div class="contentpanel">
    <div class="row">
      <div class="col-md-9 col-lg-8 dash-left">
        <!-- row -->
        <div class="row panel-quick-page">
          <div class="col-xs-4 col-sm-5 col-md-4 page-user">
            
			<div class="panel">
			<a href="<?php echo base_url(); ?>employee">
              <div class="panel-heading">
                <h4 class="panel-title">Manage Emloyees</h4>
              </div>
              <div class="panel-body">
                <div class="page-icon"><i class="icon ion-person-stalker"></i></div>
              </div>
			 </a>
            </div>
			
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 page-products">
            <div class="panel">
              <div class="panel-heading">
                <h4 class="panel-title">Manage Clients</h4>
              </div>
              <div class="panel-body">
                <div class="page-icon"><i class="fa fa-user-plus"></i></div>
              </div>
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 page-events">
            <div class="panel">
              <div class="panel-heading">
                <h4 class="panel-title">Projects</h4>
              </div>
              <div class="panel-body">
                <div class="page-icon"><i class="icon ion-ios-calendar-outline"></i></div>
              </div>
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 page-reports">
            <div class="panel">
              <div class="panel-heading">
                <h4 class="panel-title">Tasks</h4>
              </div>
              <div class="panel-body">
                <div class="page-icon"><i class="icon ion-arrow-graph-up-right"></i></div>
              </div>
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 page-support">
            <div class="panel">
              <div class="panel-heading">
                <h4 class="panel-title">Timesheet</h4>
              </div>
              <div class="panel-body">
                <div class="page-icon"><i class="fa fa-clock-o"></i></div>
              </div>
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 page-statistics">
            <div class="panel">
              <div class="panel-heading">
                <h4 class="panel-title">Reports</h4>
              </div>
              <div class="panel-body">
                <div class="page-icon"><i class="fa fa-file-text"></i></div>
              </div>
            </div>
          </div>
        </div>
        <!-- row -->
        <div class="row">
        <div class="col-md-12">
          <div class="panel">
            <div class="panel-heading">
              <h4 class="panel-title">Recent Clients</h4>
            </div>
            <div class="panel-body">
              <div class="table-responsive">
                <table class="table table-bordered table-primary nomargin table-hover">
                  <thead>
                    <tr>
                      <th class="text-center">Sno</th>
                      <th>Client Name</th>
                      <th class="text-center">Email</th>
                      <th class="text-right">Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for($i=1; $i<=5; $i++): ?>
					<tr>
                      <td class="text-center"><?php echo $i; ?></td>
                      <td>Douglas R. Johnson</td>
                      <td class="text-center">douglas@gmail.com</td>
                      <td class="text-right">01/06/2017</td>
                    </tr>
					<?php endfor; ?>
                  </tbody>
                </table>
              </div><!-- table-responsive -->
            </div>
          </div><!-- panel -->
        </div>
        <div class="col-md-12">
          <div class="panel">
            <div class="panel-heading">
              <h4 class="panel-title">Recent Projects</h4>
              
            </div>
            <div class="panel-body">
              <div class="table-responsive">
                <table class="table table-bordered table-primary table-striped nomargin table-hover">
                  <thead>
                    <tr>
                      <th class="text-center">
                       Sno
                      </th>
                      <th>Project Name</th>
                      <th class="text-center">Client Name</th>
					   <th class="text-right">Manager</th>
                      <th class="text-right">Start date</th>
                 
                    </tr>
                  </thead>
                  <tbody>
				    <?php for($i=1; $i<=5; $i++): ?>
                    <tr>
                      <td class="text-center"><?php echo $i; ?></td>
                      <td>Human Resources</td>
                      <td class="text-center">Douglas R. Johnson</td>
                      <td class="text-right">xxxxxxxxx</td>
                      <td class="text-right">01/06/2017</td>
                    </tr>
					<?php endfor; ?>
                  </tbody>
                </table>
              </div><!-- table-responsive -->
            </div>
          </div><!-- panel -->
        </div>
      </div>
        <!-- table-responsive -->
        <!-- table-responsive -->
      </div>
      <!-- col-md-9 -->
      <div class="col-md-3 col-lg-4 dash-right">
        <div class="row">
          <div class="col-sm-5 col-md-12 col-lg-6">
            <div class="panel panel-danger panel-weather">
              <div class="panel-heading">
                <h4 class="panel-title">Today</h4>
              </div>
              <div class="panel-body inverse">
                <div class="row mb10">
                  <div class="col-xs-12">
                    <h1 id="todayDay" class="today-day">...</h1>
                    <h3 id="todayDate" class="today-date">...</h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- col-md-12 -->
        </div>
        <!-- row -->
        <div class="row">
          <div class="col-sm-5 col-md-12 col-lg-6">
            <div class="panel panel-success">
              <div class="panel-heading">
                <h4 class="panel-title">Recent Employees</h4>
              </div>
              <div class="panel-body">
                <ul class="media-list user-list">
                  <?php for($i=1; $i<=5; $i++): ?> 
				  <li class="media">
                    <div class="media-left"> <a href="#"> <img class="media-object img-circle" src="<?php echo HTTP_IMAGES_PATH; ?>photos/user2.png" alt=""> </a> </div>
                    <div class="media-body">
                      <h4 class="media-heading nomargin"><a href="">Laxmikanth</a></h4>
                      <small class="date"><i class="fa fa-briefcase"></i> Senior Software Engineer</small> </div>
                  </li>
				  <?php endfor; ?>
                  </ul>
              </div>
            </div>
            <!-- panel -->
          </div>
        </div>
        <!-- row -->
      </div>
      <!-- col-md-3 -->
    </div>
    <!-- row -->
  </div>
  <!-- contentpanel -->
</div>
<!-- Middle Part Start END -->
<?php $this->load->view('includes/footer'); ?>
