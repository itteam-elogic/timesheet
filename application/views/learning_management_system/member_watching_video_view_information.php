<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 


    foreach($viewdetails as $key => $getResult) {
    
                            if($getResult->lms_status == '0'):
                  
                            $activeStatus = 'Active';
                    
                            $statusClass = 'class="label label-success"';
                  
                            else:
                  
                             $activeStatus = 'Inactive';
                            
                             $statusClass = 'class="label label-danger"'; 
                  
                        endif;
    
    }  

?>
<style>
        /* Adjustments for the layout */
        .video-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 */
            height: 0;
        }
        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
     .info-container {
            border: 1px solid #ddd; /* Border between video player and info block */
            padding-right: 15px;
        }
    </style>
<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h1>Learning Videos Information</h1>
        </div>
    </div>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url('lms');?>">Home</a></li>
                <?php 
                foreach ($breadcrumbs as $breadcrumb):  ?>
                <li class="breadcrumb-item ">
                <a href="<?php echo base_url('lms/index/'.$breadcrumb['catId']);?>"><?php echo $breadcrumb['name']; ?></a></li>
                <?php 
                endforeach; ?>
                <li><?=$getResult->lms_video_name?></li>
            </ol>
        </nav>
    </div>
    

    <div class="col-md-12">
        <div class="card" style="padding:2%">
            <div class="card-body">
                <div class="row">
                    <h4 class="line-head">Learning Video for - <span <?php echo $statusClass;?>> <?php echo ucfirst($this->session->userdata['logged_in_timesheet']['username']); ?></span></h4>
                    <span style="float:right; position:relative; top:-45px;"><a data-toggle="tooltip" title="List of Learning Videos" href="<?php echo base_url('lms'); ?>"><img src="<?php echo HTTP_IMAGES_PATH;?>new.png"></a> </span>
                    <p style="float: left;">Watch Time: <span id="watchTimeDisplay"><?php echo $watch_time; ?> seconds</span></p>
                    <p style="float: right;">Active Time: <span id="activeTimeDisplay"> seconds</span></p>
                </div>
                
                <?php /* ?>
                <div class="row d-flex justify-content-center">
                    <div class="col-md-8">
                        <div class="card-body">
                            <h2><?php echo $getResult->lms_video_name; ?></h2>
                            <p><?php echo $getResult->lms_desc; ?></p>
                        </div>

                        <video width="100%" controls 
                        ontimeupdate="updateWatchTime()"
                        onloadstart="resumeVideo()"
                        id="myVideo<?=$getResult->videoId?>">
                            <source src="<?php echo base_url().'lms_videos/'.$getResult->uplode_file_location; ?>" type="video/mp4">
                            <source src="<?php echo base_url().'lms_videos/'.$getResult->uplode_file_location; ?>" type="video/ogg">
                        </video>
                    </div>


                </div>
                <?php */ ?>
                <div class="row">
            <div class="col-md-8">
                <!-- Video Player -->
                <div class="video-container">
                    <video width="100%" controls 
                        ontimeupdate="updateWatchTime()"
                        onloadstart="resumeVideo()"
                        id="myVideo<?=$getResult->videoId?>">
                            <source src="<?php echo base_url().'lms_videos/'.$getResult->catId.'/'.$getResult->videoId.'/'.$getResult->uplode_file_location; ?>" type="video/mp4">
                            <source src="<?php echo base_url().'lms_videos/'.$getResult->catId.'/'.$getResult->videoId.'/'.$getResult->uplode_file_location; ?>" type="video/ogg">
                        </video>
                </div>
            </div>
            <div class="col-md-4 info-container">
                <!-- Video Information -->
                <h2>Video Information</h2>
                <p><strong>Title:</strong> <?php echo $getResult->lms_video_name?></p>
                <p><strong>Description:</strong> <?php echo $getResult->lms_desc?></p>

                <!-- Attachments -->
                <h2>Attachments</h2>
                <ul>
                    <?php if(!empty($getResult->video_doc1)){ ?>
                         <li><a target="_blank"  href="<?php echo base_url().'lms_videos/'.$getResult->catId.'/'.$getResult->videoId.'/'.$getResult->video_doc1; ?>">
                            <img src="<?php echo base_url().'assets/images/doc_svg.svg'; ?>" width="48px">
                             <?=$getResult->video_doc1?>
                            </a>
                        </li>
                            <?php } ?>
                    <?php if(!empty($getResult->video_doc2)){ ?>
                         <li><a target="_blank"  href="<?php echo base_url().'lms_videos/'.$getResult->catId.'/'.$getResult->videoId.'/'.$getResult->video_doc2; ?>">
                            <img src="<?php echo base_url().'assets/images/doc_svg.svg'; ?>" width="48px">
                             <?=$getResult->video_doc2?>
                            
                            </a>
                        </li>
                    <?php } ?>
                    <?php if(empty($getResult->video_doc1) && empty($getResult->video_doc2)){ ?>
                    <li>No Attachments</li>
                    <?php } ?>
                
                    <!-- You can add more attachments here -->
                </ul>
            </div>
        </div>
                


                <!-- Recent uploaded Videos -->

                <div class="row" style="padding-top:30px;">

                    <div class="col-lg-12">
                        <h2 class="line-head">Related Videos for <?php echo $breadcrumb['name']; ?></h2>

                    </div>

                    <?php foreach ($this->lms_model->recentLmsVideos($breadcrumb['catId']) as $key => $recentLmsVideos) : ?>

                    <div class="col-lg-4">
                        <div class="card">
                            <video width="100%" controls  style="pointer-events: none; height:188px;">
                                <source src="<?php echo base_url().'lms_videos/'.$recentLmsVideos->catId.'/'.$recentLmsVideos->videoId.'/'.$recentLmsVideos->uplode_file_location; ?>" type="video/mp4">
                                <source src="<?php echo base_url().'lms_videos/'.$recentLmsVideos->catId.'/'.$recentLmsVideos->videoId.'/'.$recentLmsVideos->uplode_file_location; ?>" type="video/ogg">
                            </video>
                            <div class="card-body">
                                <h3 class="card-title"><?php echo strlen($recentLmsVideos->lms_video_name) > 35 ? substr($recentLmsVideos->lms_video_name, 0, 35) . '...' : $recentLmsVideos->lms_video_name; ?></h3>
                                <a href="<?php echo base_url(); ?>lms/member_watching_video_info/<?php echo $recentLmsVideos->videoId; ?>" class="btn btn-primary">Click To Watch</a>

                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <!-- Recent uploaded Videos -->
            </div>
        </div>


    </div>
    <!-- Organizatoin form validation -->
    <style>
        .mandaysradio {
            position: absolute;
            margin-top: 13px;
        }

    </style>
    <script type="text/javascript" language="javascript">
        var video = document.getElementById('myVideo<?=$getResult->videoId?>');
        var watchTimeDisplay = document.getElementById('watchTimeDisplay');
        var activeTimeDisplay = document.getElementById('activeTimeDisplay');
        watchTimeDisplay.textContent = secondsToHms('<?=$watch_time?>');
        var spent_time = <?=$spent_time?>;
        activeTimeDisplay.textContent = secondsToHms('<?=$spent_time?>');
        var videoDuration = <?=$video_duration?>;
        var startTime;
        video.addEventListener('play', function() {
            startTime = Date.now();
            if(!videoDuration){
                $.get('<?=base_url().'/lms/updateVideoDuration/'. $getResult->videoId?>/'+ video.duration,(r)=>{
                    console.log('duration logged')
                }); 
            }
            console.log( '$$$$$$$$$$', video.duration);
        });

        window.addEventListener('focus', function() {
            console.log('3333333333333333333', video.duration);
        });

        video.addEventListener('pause', function() {
            updateWatchTime1();
            // startTime = Date.now();
        });

        // Log end time when video finishes playing
        video.addEventListener('ended', function() {
            var elapsedTime = Date.now() - startTime;
            console.log(elapsedTime / 1000, 'elapsedTime', elapsedTime);
            spent_time = spent_time +  (elapsedTime / 1000);
            console.log('spent_time', spent_time);
            activeTimeDisplay.textContent = secondsToHms(spent_time);
            
            
            var watchTime = Math.floor(video.currentTime);
            watchTimeDisplay.textContent = secondsToHms(watchTime);
            // Make an AJAX request to update watch time
            var updateUrl = '<?php echo base_url().'lms/logEndTime/' . $getResult->videoId; ?>/' + watchTime + '/' + spent_time;
            $.get(updateUrl, (res)=>{
                console.log('Watch completed time updated successfully.');
            });
        });

       function resumeVideo(){
            video.currentTime = <?=$watch_time?>;
        }
        function updateWatchTime() {}
        /** 
         * START: This Method to watch time of video played
         * 
         */
        function updateWatchTime1() {
            var elapsedTime = Date.now() - startTime;
            console.log(elapsedTime / 1000, 'elapsedTime', elapsedTime);
            spent_time = spent_time +  (elapsedTime / 1000);
            console.log('spent_time', spent_time);
            activeTimeDisplay.textContent = secondsToHms(spent_time);
            
            
            var watchTime = Math.floor(video.currentTime);
            watchTimeDisplay.textContent = secondsToHms(watchTime);
            // Make an AJAX request to update watch time
            var updateUrl = '<?php echo base_url().'lms/update_watch_time/' . $getResult->videoId; ?>/' + watchTime + '/' + spent_time;
            $.get(updateUrl, (res)=>{
                console.log('Watch time updated successfully.');
            });
        }
        /**
         * END
         */

        // Check for Page Visibility API support
        if ('hidden' in document) {
            var visibilityChangeEvent = 'visibilitychange';
            var hiddenProperty = 'hidden';
        } else if ('msHidden' in document) {
            var visibilityChangeEvent = 'msvisibilitychange';
            var hiddenProperty = 'msHidden';
        } else if ('webkitHidden' in document) {
            var visibilityChangeEvent = 'webkitvisibilitychange';
            var hiddenProperty = 'webkitHidden';
        }

        // Function to handle visibility change
        function handleVisibilityChange() {
            if (document[hiddenProperty]) {
                // Page is not visible, pause the video
                video.pause();
                
            } else {
                // Page is visible, resume the video
                // video.play();
            }
        }

        function secondsToHms(d) {
            d = Number(d);
            var h = Math.floor(d / 3600);
            var m = Math.floor(d % 3600 / 60);
            var s = Math.floor(d % 3600 % 60);

            var hDisplay = h > 0 ? h + (h == 1 ? " hr, " : " hrs, ") : "";
            var mDisplay = m > 0 ? m + (m == 1 ? " min, " : " min, ") : "";
            var sDisplay = s > 0 ? s + (s == 1 ? " sec" : " secs") : "";
            return hDisplay + mDisplay + sDisplay; 
        }

        // Add event listener for visibility change
        document.addEventListener(visibilityChangeEvent, handleVisibilityChange, false);
    </script>
    
    <!-- Inlude Footer here -->
    <?php $this->load->view('includes/cRMFooter'); ?>
    <!-- Inlude Footer here END-->