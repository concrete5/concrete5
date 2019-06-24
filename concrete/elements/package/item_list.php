<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<legend><?=$category->getItemCategoryDisplayName()?></legend>

<ul class="list-unstyled">
    <?php foreach ($category->getItems($package) as $item) {
        ?>
        <li><?= $category->getItemName($item);
            ?></li>
        <?php
    }
    ?>
</ul>
