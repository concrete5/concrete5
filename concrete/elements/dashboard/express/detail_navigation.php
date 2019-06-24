<?php
defined('C5_EXECUTE') or die('Access Denied.');
$c = Page::getCurrentPage();
?>
<div class="col-md-4">
    <div class="list-group">
        <a
            class="list-group-item <?=($c->getCollectionPath() == '/dashboard/system/express/entities' && $view->controller->getTask() == 'view_entity') ? ' active' : ''?>"
            href="<?=URL::to('/dashboard/system/express/entities', 'view_entity', $entity->getId())?>"
        >
            <?=t('Details')?>
        </a>
        <a
            class="list-group-item<?=($c->getCollectionPath() == '/dashboard/system/express/entities' && ($view->controller->getTask() == 'edit' || $view->controller->getTask() == 'update')) ? ' active' : ''?>"
            href="<?=URL::to('/dashboard/system/express/entities', 'edit', $entity->getId())?>"
        >
            <?=t('Edit Entity')?>
        </a>
        <a
            class="list-group-item<?=($c->getCollectionPath() == '/dashboard/system/express/entities/attributes') ? ' active' : ''?>"
            href="<?=URL::to('/dashboard/system/express/entities/attributes', $entity->getId())?>"
        >
            <?=t('Attributes')?>
        </a>
        <a
            class="list-group-item<?=($c->getCollectionPath() == '/dashboard/system/express/entities/associations') ? ' active' : ''?>"
            href="<?=URL::to('/dashboard/system/express/entities/associations', $entity->getId())?>"
        >
            <?=t('Associations')?>
        </a>
        <a
            class="list-group-item<?=($c->getCollectionPath() == '/dashboard/system/express/entities/forms') ? ' active' : ''?>"
            href="<?=URL::to('/dashboard/system/express/entities/forms', $entity->getId())?>"
        >
            <?=t('Forms')?>
        </a>
        <a
            class="list-group-item<?=($c->getCollectionPath() == '/dashboard/system/express/entities/customize_search') ? ' active' : ''?>"
            href="<?=URL::to('/dashboard/system/express/entities/customize_search', $entity->getId())?>"
        >
            <?=t('Customize Search/Listing')?>
        </a>
        <?php if ($entity->supportsCustomDisplayOrder()) { ?>
            <a
                class="list-group-item<?=($c->getCollectionPath() == '/dashboard/system/express/entities/order_entries') ? ' active' : ''?>"
                href="<?=URL::to('/dashboard/system/express/entities/order_entries', $entity->getId())?>"
            >
                <?=t('Re-Order Entries')?>
            </a>
        <?php } ?>
        <a
            class="list-group-item"
            href="<?=URL::to('/dashboard/express/entries', $entity->getId())?>"
        >
            <i class="fa fa-share pull-right" style="margin-top: 4px"></i>
            <?=tc(/*i18n: %s is an entity name*/'Express', 'View %s Entries', $entity->getEntityDisplayName())?>
        </a>

        <a href="<?=URL::to('/dashboard/system/express/entities', 'clear_entries', $entity->getId())?>" class="list-group-item">
            <i class="fa fa-trash pull-right text-danger" style="margin-top:4px"></i>
            <span class="text-danger"><?= t('Clear Entries') ?></span>
        </a>
    </div>
</div>
