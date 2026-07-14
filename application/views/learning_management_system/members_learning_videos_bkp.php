    <!-- Inlude Header here -->
    <?php 

$this->load->view('includes/cRMHeader');

$userType = $this->session->userdata['logged_in_timesheet']['user_type'];

$member_emp_id = $this->session->userdata['logged_in_timesheet']['empId'];

?>
    <!-- Inlude Header here END-->

    <div class="content-wrapper">
        <div class="page-title">
            <div>
                <h1>Member Learning videos </h1>
            </div>
            <div>
                <?php if($userType != 'developer'):?>
                <a class="btn btn-primary btn-flat" href="<?php echo base_url('lms/add'); ?>" data-toggle="tooltip" title="Add Learning Videos"><i class="fa fa-lg fa-plus"></i></a> |<?php endif;?> <a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url('lms'); ?>"><i class="fa fa-lg fa-refresh"></i></a>
            </div>
        </div>
        <div class="card" style="min-height: auto;">
            <h3 class="card-title"></h3>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bs-component">
                            <div class="tab-content" id="myTabContent">
                                <form class="" name="reportForm" id="reportForm" method="post" action="<?php echo base_url('lms/index');?>">
                                    <div class="tab-pane fade active in" id="Add">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group align-middle">
                                                    <label class="control-label">Search Video</label>
                                                    <input class="form-control" type="text" name="search_term" placeholder="Please Enter Search Text Here" value="<?=!empty($search_term) ? $search_term: NULL?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4 form-group align-middle p-5">
                                                <button class="btn btn-primary icon-btn align-middle" style="margin-top:28px;"><i class="fa fa-fw fa-lg fa-check-circle"></i>Search</button>
                                                <button type="reset" onclick='location.href="<?php echo base_url('lms');?>"' class="btn btn-primary icon-btn" style="margin-top:28px;"><i class="fa fa-fw fa-lg fa-check-circle"></i>Clear</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">

                <?php foreach ($getLmsVideos as $key => $videoDetails) : ?>

                <div class="col-lg-4">
                    <div class="card">
                        <?php if(empty($videoDetails->completed_date) && !empty($videoDetails->completion_date)){ ?>
                        <div class="col-lg-12" style="padding:0;">
                            <!-- <div class="col-lg-6"></div> -->
                            <div class="col-lg-12 badge bg-danger" style="background-color: #dc3545 !important; float:left !important;" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="padding:6px;">
                                Complete By:<span class="visually-hidden "></span> <?=date_format(date_create($videoDetails->completion_date), 'd M, Y')?>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if(!empty($videoDetails->completed_date)){ ?>
                        <div class="col-lg-12" style="padding:0;">
                            <!-- <div class="col-lg-6"></div> -->
                            <div class="col-lg-12 badge bg-danger" style="background-color: #28a745 !important; float:right !important;" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success" style="padding:6px;">
                                Completed On:<span class="visually-hidden"></span> <?=date_format(date_create($videoDetails->completed_date), 'd M, Y')?>
                            </div>
                        </div>
                        <?php } ?>
                        <label style="margin-bottom:15px;">&nbsp;</label>
                        <!-- <span class="badge badge-warning">Warning</span> -->
                        <a href="<?php echo base_url(); ?>lms/member_watching_video_info/<?php echo $videoDetails->videoId; ?>">
                            <video width="100%" controls style="pointer-events: none;">
                                <source src="<?php echo base_url().'lms_videos/'.$videoDetails->uplode_file_location; ?>" type="video/mp4">
                                <source src="<?php echo base_url().'lms_videos/'.$videoDetails->uplode_file_location; ?>" type="video/ogg">
                            </video>
                        </a>
                        <div class="card-body">
                            <h3 class="card-title"><?php echo $videoDetails->lms_video_name;?></h3>
                            <p class="card-text">
                                <?php echo $videoDetails->lms_desc;?>
                            </p>
                            <a href="<?php echo base_url(); ?>lms/member_watching_video_info/<?php echo $videoDetails->videoId; ?>" class="btn btn-primary">Click To Learning Video</a>
                            <!-- <a href="#!" class="btn btn-primary" onclick="memberwatching_video('<?php echo $videoDetails->videoId;?>')">Click To Learning Video</a> -->
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Member Watching video details  -->

    <script type="application/javascript">
        function memberwatching_video(watching_id) { // Getting wath

            alert('hi' + watching_id);
            jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url('lms/memberWachingVideos');?>",
                data: 'watching_id=' + watching_id,
                success: function(data) {
                    console.log('Data stored successfully' + watching_id);

                }
            });
        }
    </script>

    <style>
        .card .card-title {
            margin-bottom: 10px !important;
            font-size: 20px;
        }

        .card {
            min-height: 450px;
        }
    </style>

    <!-- Inlude Footer here -->
    <style>
        #blink-text {
            font-weight: bold;
            font-size: 20px;
            animation-name: blinkTxt;
            animation-duration: 5s;
            animation-iteration-count: infinite;
        }

        @keyframes blinkTxt {
            0% {
                color: red
            }

            50% {
                color: black;
            }

            100% {
                color: red;
            }
        }
    </style>
    <?php $this->load->view('includes/cRMFooter'); ?>
    <!-- Inlude Footer here END-->