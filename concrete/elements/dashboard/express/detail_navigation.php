<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;

/** @var Entity $entity */

$c = Page::getCurrentPage();
?>

<div class="col-4">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo ($c->getCollectionPath() === '/dashboard/system/express/entities' && $view->controller->getTask() === 'view_entity') ? ' active' : '' ?>"
               href="<?php echo (string)Url::to('/dashboard/system/express/entities', 'view_entity', $entity->getId()) ?>">
                <?php echo t('Details') ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo ($c->getCollectionPath() === '/dashboard/system/express/entities' && ($view->controller->getTask() === 'edit' || $view->controller->getTask() === 'update')) ? ' active' : '' ?>"
               href="<?php echo (string)Url::to('/dashboard/system/express/entities', 'edit', $entity->getId()) ?>">
                <?php echo t('Edit Entity') ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo ($c->getCollectionPath() === '/dashboard/system/express/entities/attributes') ? ' active' : '' ?>"
               href="<?php echo (string)Url::to('/dashboard/system/express/entities/attributes', $entity->getId()) ?>">
                <?php echo t('Attributes') ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo ($c->getCollectionPath() === '/dashboard/system/express/entities/associations') ? ' active' : '' ?>"
               href="<?php echo (string)Url::to('/dashboard/system/express/entities/associations', $entity->getId()) ?>">
                <?php echo t('Associations') ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo ($c->getCollectionPath() === '/dashboard/system/express/entities/forms') ? ' active' : '' ?>"
               href="<?php echo (string)Url::to('/dashboard/system/express/entities/forms', $entity->getId()) ?>">
                <?php echo t('Forms') ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo ($c->getCollectionPath() === '/dashboard/system/express/entities/customize_search') ? ' active' : '' ?>"
               href="<?php echo (string)Url::to('/dashboard/system/express/entities/customize_search', $entity->getId()) ?>">
                <?php echo t('Customize Search/Listing') ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo ($c->getCollectionPath() === '/dashboard/system/express/entities/order_entries') ? ' active' : '' ?>"
               href="<?php echo (string)Url::to('/dashboard/system/express/entities/order_entries', $entity->getId()) ?>">
                <?php echo t('Re-Order Entries') ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?php echo (string)Url::to('/dashboard/express/entries/results', $entity->getId()) ?>">
                <i class="fas fa-share float-end"></i>
                <?php echo tc(/*i18n: %s is an entity name*/ 'Express', 'View %s Entries', $entity->getEntityDisplayName()) ?>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"
               href="<?php echo (string)Url::to('/dashboard/system/express/entities', 'clear_entries', $entity->getId()) ?>">
                <i class="fas fa-trash-alt float-end text-danger"></i>
                <span class="text-danger"><?php echo t('Clear Entries') ?></span>
            </a>
        </li>
    </ul>
</div>
