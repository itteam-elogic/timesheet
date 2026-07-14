    <!-- Inlude Header here -->
<?php $this->load->view('includes/cRMHeader'); ?>
<!-- Inlude Header here END-->
<style>
.category-grid {
            display: grid;
            grid-template-columns: 1fr 100px; /* Adjust the width of the action column as needed */
            grid-gap: 10px; /* Adjust the gap between cells as needed */
            margin-bottom: 20px;
        }
</style>
<div class="content-wrapper">
  <div class="page-title">
    <div>   
      <h1>Manage Learning videos </h1>
    </div>
    <div>
<!--
        <a class="btn btn-primary btn-flat" href="<?php echo base_url('lms/addCategory'); ?>" data-toggle="tooltip" title="Add Category"><i class="fa fa-lg fa-plus"></i> Add Category</a> &nbsp; | 
        
-->
        <a class="btn btn-info btn-flat" data-toggle="tooltip" title="Refresh" href="<?php echo base_url('lms'); ?>"><i class="fa fa-lg fa-refresh"></i></a></div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
           <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <div class="category-grid">
                    <div><?php echo $category['name']; ?></div>
                    <div>
                        <?php if (!empty($category['children'])): ?>
                            <ul class="list-group">
                                <?php foreach ($category['children'] as $child): ?>
                                    <li class="list-group-item">
                                        <?php echo $child['name']; ?>
                                        <a href="<?php echo base_url('category/edit/'.$child['catId']); ?>" class="btn btn-primary btn-sm float-right">Edit</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <?php if (empty($category['children'])): ?>
                            <a href="<?php echo base_url('category/edit/'.$category['catId']); ?>" class="btn btn-primary btn-sm float-right">Edit</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php echo $links; ?>
                </ul>
            </nav>
        <?php else: ?>
            <p>No categories found.</p>
        <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Inlude Footer here -->

<?php $this->load->view('includes/cRMFooter'); ?>
<!-- Inlude Footer here END-->
