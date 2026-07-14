<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>

<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h1>Architecture Modeling Videos </h1> 
        </div>
        <div><span class="text-end"><a href="<?php echo base_url('lms/catTopics/topices');?>">Back to Topices</a></span></div>
    </div>

    
    <!-- Demo header-->
 <div  class="col-sm-12">
        
       
        <div class="col-12">
          <!-- Tab panes -->
          <div class="tab-content">
              
            <div class="tab-pane active" id="home-v">
                
               <div class="row" style="padding-top:30px;">

                    <?php foreach ($this->lms_model->recentLmsVideos() as $key => $recentLmsVideos) : ?>

                    <div class="col-lg-4">
                        <div class="card">
                            <video width="100%" controls  style="pointer-events: none;">
                                <source src="<?php echo base_url().'lms_videos/'.$recentLmsVideos->uplode_file_location; ?>" type="video/mp4">
                                <source src="<?php echo base_url().'lms_videos/'.$recentLmsVideos->uplode_file_location; ?>" type="video/ogg">
                            </video>
                            <div class="card-body">
                                <h3 class="card-title"><?php echo $recentLmsVideos->lms_video_name;?></h3>

                                <a href="<?php echo base_url(); ?>lms/member_watching_video_info/<?php echo $recentLmsVideos->videoId; ?>" class="btn btn-primary">Click To Learn</a>

                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>  
                
              </div>
              
           
              
          </div>
        </div>

        <div class="clearfix"></div>

      </div>

      

    
  
    <style>
       .tabs-left {
  border-bottom: none;
  border-right: 1px solid #ddd;
}

.tabs-left>li {
  float: none;
 margin:0px;
  
}

.tabs-left>li.active>a,
.tabs-left>li.active>a:hover,
.tabs-left>li.active>a:focus {
  border-bottom-color: #ddd;
  border-right-color: transparent;
  background:#f90;
  border:none;
  border-radius:0px;
  margin:0px;
}
.nav-tabs>li>a:hover {
    /* margin-right: 2px; */
    line-height: 1.42857143;
    border: 1px solid transparent;
    /* border-radius: 4px 4px 0 0; */
}
.tabs-left>li.active>a::after{content: "";
    position: absolute;
    top: 10px;
    right: -10px;
    border-top: 10px solid transparent;
  border-bottom: 10px solid transparent;
  
  border-left: 10px solid #f90;
    display: block;
    width: 0;}
    </style>




<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->