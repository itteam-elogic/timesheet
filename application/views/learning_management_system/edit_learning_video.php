<!-- Inlude Header here -->
<?php 
$this->load->view('includes/cRMHeader'); 

foreach($viewdetails as $key => $getResult) {
    
    if($getResult->lms_status == '0'):

        $activeStatus = 'Active';

    else:

        $activeStatus = 'Inactive';

    endif;
    
   // echo 'kanth++'.$viewdetails[0]->catId ;
    
}  


    
function recursive_dropdown($categories, $depth = 0) {
        $output = '';
        //$selectCatVal = $getResult->catId;
    
        foreach ($categories as $category) {
            
            $output .= '<option value="' . $category['catId']. '"';
            
            if($category['catId'] == 9){ 
            $output .= ' selected ';
            }
            $output .='>';
            $output .= str_repeat('--', $depth) . $category['name'];
            $output .= '</option>';
            if (!empty($category['children'])) {
                $output .= recursive_dropdown($category['children'], $depth + 1);
            }
        }
    
        return $output;
    }

?>



<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h1>Learning Videos Information</h1>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div>
                    <h4 class="line-head">Update Videos</h4>
                    <span style="float:right; position:relative; top:-45px;"><a data-toggle="tooltip" title="List of Learning Videos" href="<?php echo base_url('lms'); ?>"><img src="<?php echo HTTP_IMAGES_PATH;?>new.png"></a> </span>
                </div>
                <div style="clear:both;"></div>
                <form class="form-horizontal" method="post" name="edit_learning_videos" id="edit_learning_videos" enctype="multipart/form-data" action="<?php echo base_url('lms/edit_learning_videos');?>">
                    <input type="hidden" name="videoId" value="<?=$getResult->videoId?>">
                    <div class="form-group">
                        <label class="control-label col-md-3">Video Name : <span class="required-star">*</span></label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" name="lms_video_name" id="lms_video_name" placeholder="Enter learning video name" value="<?php echo $getResult->lms_video_name; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Video Category<?=$viewdetails[0]->catId?> : <span class="required-star">*</span></label>
                        <div class="col-md-4">
                            <select  class="form-control" name="catId" id="catId">
                                <option value="">Select Category</option>
                                <?php 
                                    echo recursive_dropdown($categories_hierarchy); 
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Upload Video : <span class="required-star">*</span> </label>
                        <div class="col-md-4">
                            <input class="form-control" type="file" name="uplode_file_location" id="uplode_file_location" placeholder="Please Choose Lms Videos" value="">
                        </div>
                        <br/>
                        <div class="text-center">
                         <video width="50%"  controls>
                              <source src="<?php echo base_url().'lms_videos/'.$getResult->catId.'/'.$getResult->videoId.'/'.$getResult->uplode_file_location; ?>" type="video/mp4">
                              <source src="<?php echo base_url().'lms_videos/'.$getResult->catId.'/'.$getResult->videoId.'/'.$getResult->uplode_file_location; ?>" type="video/ogg"> 
                        </video>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="control-label col-md-3">Description : <span class="required-star">*</span> </label>
                        <div class="col-md-4"><textarea class="form-control" name="lms_desc" id="lms_desc" placeholder="Enter Learning Management Description" rows="3"><?php echo $getResult->lms_desc; ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Status : <span class="required-star">*</span></label>
                        <div class="col-md-4">
                            <select class="form-control" id="lms_status" name="lms_status">
                                <option value="">Please select status</option>
                                <option value="0" <?php echo $getResult->lms_status == '0' ? 'selected' : ''?>>Active</option>
                                <option value="1" <?php echo $getResult->lms_status == '1' ? 'selected' : ''?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3">Upload Any PDF/PPT :  </label>
                        <div class="col-md-4">
                            <input class="form-control" type="file" name="video_document1" id="video_document1" placeholder="Please Choose PDF/PPT">
                            <?php if(!empty($getResult->video_doc1)){ ?>
<!--                        <br/>-->
                        
                        <div class="text-right" >
                         <a target="_blank" href="<?php echo base_url().'lms_videos/'.$getResult->catId.'/'.$getResult->videoId.'/'.$getResult->video_doc1; ?>">
                             <img src="<?php echo base_url().'assets/images/doc_svg.svg'; ?>" width="48px">
                             <?php /*if(mime_content_type($getResult->video_doc1) == 'application/pdf'){ ?>
                            <img src="<?php echo base_url().'assets/images/pdf_svg.svg'; ?>" width="48px">
                             <?=$getResult->video_doc1?>
                             <?php }else{ ?>
                             <img src="<?php echo base_url().'assets/images/ppt_svg.svg'; ?>" width="32px">
                             <?=$getResult->video_doc1?>
                             <?php }*/ ?>
                            <?=$getResult->video_doc1?>
                            </a>
                            
                        </div>
                            <?php } ?>
                        </div>
                        
                        
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3">Upload Any PDF/PPT :  </label>
                        <div class="col-md-4">
                            <input class="form-control" type="file" name="video_document2" id="video_document2" placeholder="Please Choose PDF/PPT">
                        <?php if(!empty($getResult->video_doc2)){ ?>
                        <div class="text-right" >
                         <a target="_blank"  href="<?php echo base_url().'lms_videos/'.$getResult->catId.'/'.$getResult->videoId.'/'.$getResult->video_doc2; ?>">
                            <img src="<?php echo base_url().'assets/images/doc_svg.svg'; ?>" width="48px">
                             <?php /*if(mime_content_type($getResult->video_doc1) == 'application/pdf'){ ?>
                            <img src="<?php echo base_url().'assets/images/doc_svg.svg'; ?>" width="48px">
                             <?=$getResult->video_doc1?>
                             <?php }else{ ?>
                             <img src="<?php echo base_url().'assets/images/ppt_svg.svg'; ?>" width="32px">
                             <?=$getResult->video_doc1?>
                             <?php }*/ ?>
                             <?=$getResult->video_doc2?>
                            
                            </a>
                        </div>
                            <?php } ?>
                        </div>
                        
                    </div>


                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-3">
                                <button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update LMS Information</button>
                                   <a class="btn btn-default icon-btn" href="<?php echo base_url('lms');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


</div>
<!-- Organizatoin form validation -->
<script type="text/javascript" language="javascript">
    // Wait for the DOM to be ready
    $(function() {
        $("form[name='edit_learning_videos']").validate({
            rules: {
                lms_video_name: {
                    required: true
                },
                lms_video_type: {
                    required: true
                },
                lms_desc: {
                    required: true
                },
                
                
                lms_status: {
                    required: true
                }

            },
            messages: {
                lms_video_name: "Please enter video name",
                lms_video_type: "please select video type",
                lms_desc: "Please Enter  description",
                lms_status: "Please select status"

            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });


    $('#lms_video_type,#lms_status').select2(); // Autosuggest list on clients
</script>
<!-- Organizatoin form validation -->
<style>
    .mandaysradio {
        position: absolute;
        margin-top: 13px;
    }
</style>
<!-- Inlude Footer here -->
<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->