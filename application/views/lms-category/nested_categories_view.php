<?php foreach ($categories as $index => $category): ?>
    <div class="card">
        <div class="card-header" id="heading-<?php echo $category['catId']; ?>">
            <h2 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-<?php echo $category['catId']; ?>" aria-expanded="true" aria-controls="collapse-<?php echo $category['catId']; ?>">
                    <?php echo $category['name']; ?>
                </button>
                <button onClick="window.location.href='<?php echo base_url('lmscategory/editCategory/'. $category['catId']); ?>'" class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-<?php echo $category['catId']; ?>'" aria-expanded="true" aria-controls="collapse-<?php echo $category['catId']; ?>">
                <i class="fa fa-edit"></i>EDIT
                </button>

            </h2>
        </div>

        <div id="collapse-<?php echo $category['catId']; ?>" class="collapse" aria-labelledby="heading-<?php echo $category['catId']; ?>" data-parent="#categoriesAccordion-<?php echo $category['catId']; ?>">
            <div class="card-body">
                <?php if (!empty($category['children'])): ?>
                    <?php echo $this->load->view('lms-category/nested_categories_view', ['categories' => $category['children']], true); ?>
                <?php else: ?>
                    No sub-categories
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
