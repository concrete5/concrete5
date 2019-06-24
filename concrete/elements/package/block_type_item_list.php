<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<legend><?=$category->getItemCategoryDisplayName()?></legend>

<ul id="ccm-block-type-package-list" class="item-select-list">
    <?php foreach ($category->getItems($package) as $bt) {
        ?>
        <li>
            <a href="<?= $view->url('/dashboard/blocks/types', 'inspect', $bt->getBlockTypeID());
            ?>"><img src="<?=$ci->getBlockTypeIconURL($bt)?>" /> <?=t($bt->getBlockTypeName());
                ?></a>
        </li>
        <?php
    }
    ?>
</ul>
