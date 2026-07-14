<!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); 

 function recursive_dropdown($categories, $depth = 0) {
        $output = '';
        foreach ($categories as $category) {
            $output .= '<option value="' . $category['catId'] . '">';
            $output .= str_repeat('--', $depth) . $category['name'];
            $output .= '</option>';
            if (!empty($category['children'])) {
                $output .= recursive_dropdown($category['children'], $depth + 1);
            }
        }
        return $output;
    }

    function build_category_dropdown($categories, $prefix = '') {
        $html = '';
        foreach ($categories as $category) {
            $html .= '<option value="' . $category['catId'] . '">' . $prefix . $category['name'] . '</option>';
            if (!empty($category['children'])) {
                $html .= build_category_dropdown($category['children'], $prefix . '--');
            }
        }
        return $html;
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
                    <h4 class="line-head">Add Category</h4>
                    <span style="float:right; position:relative; top:-45px;"><a data-toggle="tooltip" title="List of Learning Videos" href="#"><img src="<?php echo HTTP_IMAGES_PATH;?>new.png"></a> </span>
                </div>
                <div style="clear:both;"></div>
                <form class="form-horizontal" method="post" name="add_category_frm" id="add_category_frm" enctype="multipart/form-data" action="<?php echo base_url('lmscategory/saveCategory');?>">
                    <div class="form-group">
                        <label class="control-label col-md-3">Parent Category : <span class="required-star">*</span></label>
                        <div class="col-md-4">
                            <select  class="form-control" name="parent_id" id="parent_id">
                                <option value="">Select Parent Category</option>
                                <?php 
                                echo build_category_dropdown($categories_hierarchy); 
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Parent Hierarchy : </label>
                        <div  class="col-md-6" id="category-hierarchy"></div>
                    </div>


                    <div class="form-group">
                        <label class="control-label col-md-3">Category Name : <span class="required-star">*</span></label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" name="name" id="name" placeholder="Enter Category Name" >
                            <?php echo form_error('name'); ?>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-3">
                                <button class="btn btn-primary icon-btn"><i class="fa fa-fw fa-lg fa-check-circle"></i>Add Category</button> &nbsp;<a class="btn btn-default icon-btn" href="<?php echo base_url('lms');?>"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


</div>
<!-- Organizatoin form validation -->
<script>
        $(document).ready(function() {
            $('select[id="parent_id"]').change(function() {
                var categoryId = $(this).val();
                if (categoryId) {
                    $.ajax({
                        url: '<?php echo base_url("lmscategory/getUpwardsParentCategoriesById"); ?>',
                        type: 'POST',
                        data: { catId: categoryId },
                        success: function(data) {
                            var hierarchy = JSON.parse(data);
                            var breadcrumbHtml = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
                            $.each(hierarchy, function(index, category) {
                                breadcrumbHtml += '<li class="breadcrumb-item">' + category.name + '</li>';
                            });
                            breadcrumbHtml += '</ol></nav>';
                            $('#category-hierarchy').html(breadcrumbHtml);
                        }
                    });
                } else {
                    $('#category-hierarchy').html('');
                }
            });
        });
    </script>
<script type="text/javascript" language="javascript">
    // Wait for the DOM to be ready
    $(function() {
        $("form[name='add_category_frm']").validate({
            rules: {
                parent_id: {
                    required: true
                },
                name: {
                    required: true
                }

            },
            messages: {
                parent_id: "Please Select Parent Category",
                name: "Please Enter Category Name"

            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });


    $('#parent_id').select2(); // Autosuggest list on clients
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