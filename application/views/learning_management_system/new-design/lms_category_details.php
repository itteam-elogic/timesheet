<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>

<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h1>Sevice Wise Topic List - Architectural</h1>
        </div>
        <div><span class="text-end"><a href="<?php echo base_url('lms/catTopics/catTopics');?>">Back to services</a></span></div>
    </div>

    <div class="row" style="padding-top:30px;">

        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <a href="<?php echo base_url('lms/catTopics/topices');?>" class="">
                        <h3 class="card-title"><i class="fa fa-folder"></i> Revit Architecture</h3>
                    </a>

                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <a href="<?php echo base_url('lms/catTopics/topices');?>" class="">
                        <h3 class="card-title"><i class="fa fa-folder"></i> Auto Cad</h3>
                    </a>

                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <a href="<?php echo base_url('lms/catTopics/topices');?>" class="">
                        <h3 class="card-title"><i class="fa fa-folder"></i> Dynamo</h3>
                    </a>

                </div>
            </div>
        </div>
         <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <a href="<?php echo base_url('lms/catTopics/topices');?>" class="">
                        <h3 class="card-title"><i class="fa fa-folder"></i> Rivit Families</h3>
                    </a>

                </div>
            </div>
        </div>
    </div>





    <!-- Organizatoin form validation -->
    <style>
        .card-title {
            text-align: center;
            color: #333;
        }
    </style>




    <!-- Inlude Footer here -->
    <?php $this->load->view('includes/cRMFooter'); ?>
    <!-- Inlude Footer here END-->