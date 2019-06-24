<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<legend><?=$category->getItemCategoryDisplayName()?></legend>

<dl class="dl-horizontal">
    <?php foreach ($category->getItems($package) as $at) {
        $controller = $at->getController();
        $formatter = $controller->getIconFormatter();
        ?>
        <dt><?=$formatter->getListIconElement()?></dt>
        <dd>
            <?=$at->getAttributeTypeName()?>
            <?php
            foreach ($categories as $cat) {
                if (!$at->isAssociatedWithCategory($cat)) {
                    continue;
                }
                ?>
                <span class="badge"><?=$text->unhandle($cat->getAttributeKeyCategoryHandle())?></span>
                <?php
            }
            ?>
        </dd>
        <?php
    }
    ?>
</dl>