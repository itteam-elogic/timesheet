<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');

function getParentCategoryName($categories, $parentId)
{
    foreach ($categories as $category) {
        if ($category['id'] == $parentId) {
            return $category['name'];
        }
    }
    return 'N/A';
}
?>