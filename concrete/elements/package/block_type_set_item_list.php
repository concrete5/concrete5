<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<legend><?=$category->getItemCategoryDisplayName()?></legend>

<ul class="list-unstyled">
    <?php foreach ($category->getItems($package) as $set) {
        ?>
            <li><?=ucfirst($set->getBlockTypeSetName())?></li>
        <?php
    }
    ?>
</ul>
