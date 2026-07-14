    <!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<!-- Inlude Header here END-->

<div class="content-wrapper">
  <div class="page-title">
    <div>   
      <h1>Manage Learning videos </h1>
    </div>
    <div>
<!--
        <a class="btn btn-primary btn-flat" href="<?php echo base_url('lms/addCategory'); ?>" data-toggle="tooltip" title="Add Category"><i class="fa fa-lg fa-plus"></i> Add Category</a> &nbsp; | 
        
-->
        &nbsp; <a class="btn btn-primary btn-flat" href="<?php echo base_url('lms/add'); ?>" data-toggle="tooltip" title="Add Learning Videos"><i class="fa fa-lg fa-plus"></i> Add Video</a> &nbsp; | &nbsp; <a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url('lms'); ?>"><i class="fa fa-lg fa-refresh"></i></a></div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="organisationTable">
              <thead>
                <tr>
                  <th>Sno</th>
                  <th>Video Name</th>    
                  <th>Video Category</th>
                  <!-- <th>Video</th>    
                  <th>Short Desc</th> -->
                  <th>Status</th>     
				  <th>Created By</th>
				  <th>Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                
                <?php  
                     $i=1;
                  
                     foreach ($getLmsVideos as $key => $videoDetails) : 
                  
                     if($i%2 == 0): $showRowColour = 'class="success"'; else: $showRowColour = 'class="info"'; endif;
                    
                        if($videoDetails->lms_status == '0'):
                  
                            $activeStatus = 'Active';
                    
                            $statusClass = 'class="label label-success"';
                  
                            else:
                  
                             $activeStatus = 'Inactive';
                            
                             $statusClass = 'class="label label-danger"'; 
                  
                        endif;
                  
                  ?>  
                  <tr <?php echo $showRowColour; ?>>
                      <td><?php echo $i; ?></td>
                      <td><?php echo $videoDetails->lms_video_name;?></td>
                      <td><?php echo $videoDetails->categoryName;?></td>
                      <td><span <?php echo $statusClass; ?>><?php echo $activeStatus;?></span></td>
                      <td><span class="label label-info"><?php echo $videoDetails->name;?></span></td>
                      <td><?php echo $videoDetails->created_by;?></td>
                     
                      <td>
                        <a href="<?php echo base_url(); ?>lms/add/<?php echo $videoDetails->videoId; ?>"data-toggle="tooltip" title="Edit">
                          <i class="fa fa-edit"></i></a> 
                        | <a href="<?php echo base_url(); ?>lms/viewinformation/<?php echo $videoDetails->videoId; ?>" data-toggle="tooltip" title="" data-original-title="View">
                          <i class="fa fa-eye"></i></a> 
                           
                          | <a  href="<?php echo base_url(); ?>lms/assignVideoToEmployee/<?php echo $videoDetails->videoId; ?>" data-toggle="tooltip" title="" data-original-title="View">
                              <i class="fa fa-tasks"></i><span style="color: black; margin-left: 5px;text-align:center">Assign</span>
                            </a>
                        </td>
                  </tr>
                  
               <?php $i++; endforeach; ?> 
                  
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Inlude Footer here -->

<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
