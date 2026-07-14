<!-- Inlude Header here -->
<?php 
$this->load->view('includes/cRMHeader');
?>

<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h1>Learning Management System (LMS)</h1>
        </div>
    </div>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url('lms');?>">Home</a></li>
                <?php 
                $cnt = count($breadcrumbs);$b=0;
                foreach ($breadcrumbs as $breadcrumb): $b++;  ?>
                <?php if($b != $cnt){ ?>
                <li class="breadcrumb-item ">
                    <a href="<?php echo base_url('lms/index/'.$breadcrumb['catId']);?>"><?php echo $breadcrumb['name']; ?></a>
                </li>
                <?php }else{?>
                <li class="breadcrumb-item "><?php echo $breadcrumb['name']; ?></li>
                <?php } ?>
                <?php 
                endforeach; ?>
            </ol>
        </nav>
    </div>
    <?php if(count($getLmsVideos) > 0){ ?>
    <div class="card" style="min-height: auto;">
        <h3 class="card-title"></h3>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="bs-component">
                        <div class="tab-content" id="myTabContent">
                            <form class="" name="reportForm" id="reportForm" method="post" action="<?php echo base_url('lms/index/'. (!empty($catInfo['catId']) ? $catInfo['catId'] : ''));?>">
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
    <?php } ?>
    <div class="row" style="padding-top:30px;">
        <?php 
//        echo "<pre>";print_r($subCategories);exit;
        if(count($subCategories) > 0){  
                for($v=0;$v<count($subCategories);$v++){
        ?>
        <div class="col-lg-3">
            <div class="card-cat">
                <div class="card-body">
                    <a href="<?php echo base_url('lms/index/'.$subCategories[$v]['catId']);?>" class="">
                        <h3 class="card-title-main" style="font-size:24px !important;"><i class="fa fa-folder"></i> <?php echo $subCategories[$v]['name']?></h3>
                    </a>

                </div>
            </div>
        </div>
        <?php }//for
        
        }else{ 
        
        if(empty($getLmsVideos)): ?>
        
                <div class="col-lg-12">
                    <div class="bs-component">
                        <div class="alert alert-dismissible alert-danger">
                            <h4>No records found !!!.</h4>
                        </div>
                    </div>
                </div>


        <?php endif;  } ?>
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
                    <label style="margin-bottom:0px;">&nbsp;</label>
                    <!-- <span class="badge badge-warning">Warning</span> -->
                    <a href="<?php echo base_url(); ?>lms/member_watching_video_info/<?php echo $videoDetails->videoId; ?>">
                        <video width="100%" controls style="pointer-events: none;height:188px;">
                            <source src="<?php echo base_url().'lms_videos/'.$videoDetails->catId.'/'.$videoDetails->videoId.'/'.$videoDetails->uplode_file_location; ?>" type="video/mp4">
                            <source src="<?php echo base_url().'lms_videos/'.$videoDetails->catId.'/'.$videoDetails->videoId.'/'.$videoDetails->uplode_file_location; ?>" type="video/ogg">
                        </video>
                    </a>
                    <div class="card-body">
                        
						<h3 class="card-title"><?php echo strlen($videoDetails->lms_video_name) > 35 ? substr($videoDetails->lms_video_name, 0, 35) . '...' : $videoDetails->lms_video_name; ?></h3>
                        <a href="<?php echo base_url(); ?>lms/member_watching_video_info/<?php echo $videoDetails->videoId; ?>" class="btn btn-primary">Click To Watch</a>
                        <!-- <a href="#!" class="btn btn-primary" onclick="memberwatching_video('<?php echo $videoDetails->videoId;?>')">Click To Learning Video</a> -->
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

<style>
        .card-title-main {
            text-align: center;
			font-size: 24px !important;
        }
    </style>
    <!-- Organizatoin form validation -->
    
    <!-- Inlude Footer here -->
    <?php $this->load->view('includes/cRMFooter'); ?>
    <!-- Inlude Footer here END-->