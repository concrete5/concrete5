<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Multisite\Types $controller
 * @var Concrete\Core\Entity\Site\Type[] $types
 */

if (count($types) === 0) {
    ?>
    <div class="alert alert-info">
        <?= t('No site type is currently defined.') ?>
    </div>
    <?php
} else {
    ?>
    <ul class="item-select-list">
        <?php
        foreach ($types as $type) {
            ?>
            <li>
                <a href="<?=$controller->action('view_type', $type->getSiteTypeID())?>"><i class="fas fa-database"></i> <?= h($type->getSiteTypeName()) ?></a>
            </li>
            <?php
        }
        ?>
    </ul>
    <?php
}
?>

<div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <div class="float-end">
            <a class="btn btn-primary" href="<?= $controller->action('add') ?>"><?= t('Add Site Type') ?></a>
        </div>
    </div>
</div>
