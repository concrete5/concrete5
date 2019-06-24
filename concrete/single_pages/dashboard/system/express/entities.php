<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-dashboard-header-buttons">
    <a href="<?=URL::to('/dashboard/system/express/entities', 'add')?>" class="btn btn-primary"><?=t("Add Object")?></a>
</div>

<?php if (count($entities)) {
    ?>

    <ul class="item-select-list" id="ccm-stack-list">
        <?php foreach ($entities as $entity) {
    ?>

            <li>
                <a href="<?=URL::to('/dashboard/system/express/entities', 'view_entity', $entity->getID())?>">
                    <i class="fa fa-database"></i> <?=$entity->getEntityDisplayName()?>
                </a>
            </li>
        <?php 
}
    ?>
    </ul>

<?php

} else {
    ?>
    <p><?=t('You have not created any data objects.')?></p>
<?php

} ?>