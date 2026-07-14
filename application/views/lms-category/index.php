    <!-- Inlude Header here -->
    <?php $this->load->view('includes/cRMHeader'); ?>
    <!-- Inlude Header here END-->
    <style>
        .category-grid {
            display: grid;
            grid-template-columns: 1fr 100px;
            /* Adjust the width of the action column as needed */
            grid-gap: 10px;
            /* Adjust the gap between cells as needed */
            margin-bottom: 20px;
        }
    </style>
    <div class="content-wrapper">
        <div class="page-title">
            <div>
                <h1>Manage Learning videos </h1>
            </div>
            <div>

                <a class="btn btn-primary btn-flat" href="<?php echo base_url('lmscategory/addCategory'); ?>" data-toggle="tooltip" title="Add Category"><i class="fa fa-lg fa-plus"></i> Add Category</a> &nbsp; |<a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url('lms'); ?>"><i class="fa fa-lg fa-refresh"></i></a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <!-- <div id="treeview"></div> -->
                        <div class="accordion" id="categoriesAccordion">
                            <?php 
                            echo $this->load->view('lms-category/nested_categories_view', ['categories' => $categories], true); 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Inlude Footer here -->
    <style>
    </style>
    <script>
        $(document).ready(function() {
            var treeData = <?php echo $categories_json; ?>;
        //     $('#treeview').treeview({
        //         data: treeData,
        //         levels: 50,
        // enableLinks: true,
        
        //     });
            // $('#treeview').treeview('expanAll', { silent: true });

        });
    </script>

    <?php $this->load->view('includes/cRMFooter'); ?>
    <!-- Inlude Footer here END-->