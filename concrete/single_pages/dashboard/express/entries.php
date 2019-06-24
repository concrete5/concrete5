<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-dashboard-header-buttons">
    <a href="<?=URL::to('/dashboard/system/express/entities/add')?>" class="btn btn-primary"><?=t('Add Object')?></a>
</div>

<h3><?=t('Express Objects')?></h3>

<?php if (count($entities)) { ?>
<ul class="item-select-list">
<?php foreach ($entities as $entity) {
?>
    <li><a href="<?=$this->action('view', $entity->getID())?>"><i class="fa fa-database"></i> <?=$entity->getEntityDisplayName()?></a></li>
<?php
}
?>
</ul>
<?php } else { ?>

    <p><?=t('No entities created yet. First, create an Express object, then you can add entries to it.')?></p>

<?php }
