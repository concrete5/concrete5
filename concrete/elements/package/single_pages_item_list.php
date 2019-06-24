<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<legend><?=$category->getItemCategoryDisplayName()?></legend>

<ul class="list-unstyled">
    <?php foreach ($category->getItems($package) as $page) {
        ?>
        <li class="clearfix row">
            <span class="col-sm-2"><a href="<?=$page->getCollectionLink()?>"><?=$page->getCollectionName()?></a></span>
            <span class="col-sm-3"><code><?=$page->getCollectionPath()?></code></span>
            <span class="col-sm-5"><?=$page->getCollectionDescription()?></span>
        </li>
        <?php
    }
    ?>
</ul>
