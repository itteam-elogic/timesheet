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
<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h1>Learning Videos Information</h1>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div>
                    <h4 class="line-head">View Learning Videos</h4>
                    <span style="float:right; position:relative; top:-45px;"><a data-toggle="tooltip" title="List of Learning Videos" href="<?php echo base_url('lms'); ?>"><img src="<?php echo HTTP_IMAGES_PATH;?>new.png"></a> </span>
                </div>
                <div style="clear:both;"></div>
                <form class="form-horizontal" method="post" name="add_learning_videos" id="add_learning_videos" enctype="multipart/form-data" action="#">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label class="control-label col-md-3">Video Name : </label>
                            <div class="col-md-4">
                                <?php echo $getResult->lms_video_name; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label class="control-label col-md-3">Video Type : </label>
                            <div class="col-md-4">
                                <?php $catName = $this->lms_model->get_category_and_parents($getResult->catId); ?>
                                <?php 
                                   for($ci = 0; $ci < count($catName); $ci++){  
                                        
                                       echo $catName[$ci]['name'].'</br>';
                                       
                                   }
                                ?> 
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label class="control-label col-md-3">Video : </label>
                            <div class="col-md-4">
                                 <video width="100%"  controls>
                              <source src="<?php echo base_url().'lms_videos/'.$getResult->catId.'/'.$getResult->videoId.'/'.$getResult->uplode_file_location; ?>" type="video/mp4">
                              <source src="<?php echo base_url().'lms_videos/'.$getResult->catId.'/'.$getResult->videoId.'/'.$getResult->uplode_file_location; ?>" type="video/ogg"> 
                        </video>
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label class="control-label col-md-3">Description : </label>
                            <div class="col-md-4">
                                <?php echo $getResult->lms_desc; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label class="control-label col-md-3">Status : </label>
                            <div class="col-md-4">
                                <?php echo $activeStatus; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4" style="">
        <div class="card" style="padding: 0px;">
            <div class="card-body" style="max-height: 432px;overflow: auto;overflow-x: hidden;">

<?php for($i=0; $i < count($video_report['employees']) ; $i++) { 
                    $progressValue = $video_report['employees'][$i]['spent_time'] ? round(($video_report['employees'][$i]['spent_time'] / $video_report['video_duration']) * 100) : 0;
                    $leftVal = round(100-$progressValue);
// exit()
    //  for($j=1; $j<5; $j++){
    ?>
                        <div class="row mb-1">
                            <div class="col-12"><?=($video_report['employees'][$i]['empName'])?></div>
                            <div class="col-12">
                                <div class="progress" style="height: 15px">
                                <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar"  
                                    style="width: <?=$progressValue?>%" 
                                    aria-valuenow="<?=$progressValue?>" 
                                    aria-valuemin="0" aria-valuemax="100"><?=$progressValue?>%</div>
                                    <div class="progress-bar progress-bar-striped bg-warning progress-bar-animated" role="progressbar" 
                                    aria-valuenow="<?=$leftVal?>" 
                                    aria-valuemin="0" aria-valuemax="100"
                                    style="width: <?=$leftVal?>%"><span><?=$leftVal?>% left</span></div>
                                    
                                </div>
                                <!-- <?=$leftVal?>% -->
                            </div>
                        </div>
      <?php 
    //   }
    } ?>
      </div>
                            </div>
                        </div>
    <div class="col-md-12" style="display: none;">
        <div class="card">
            <div class="card-body">
                <canvas id="videoChart" width="800" height="400"></canvas>
            </div>
        </div>
    </div>


</div>
<!-- Organizatoin form validation -->
<script>
        createVideoChart();

// Function to create a chart using Chart.js
function createVideoChart() {
    var videoReport = <?= json_encode($video_report) ?>; 
    var labels = [];
    var datasets = [];

    // Extract data for the chart
    for (var i = 0; i < videoReport.employees.length; i++) {
        labels.push('Emp# ' + videoReport.employees[i]['empId']);

        datasets.push({
            label: 'Emp#' + videoReport.employees[i]['empId'],
            data: [videoReport.employees[i]['time_left']],
            backgroundColor: getRandomColor(),
            borderColor: getRandomColor(),
            borderWidth: 1
        });
    }

    console.log(labels, datasets);

    // Create a bar chart
    var ctx = document.getElementById('videoChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            indexAxis: 'y',
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}
function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}
</script>


<style>
    .mandaysradio {
        position: absolute;
        margin-top: 13px;
    }
</style>
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->