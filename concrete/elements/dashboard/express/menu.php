<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>


<div class="btn-group">

    <button type="button" class="btn btn-secondary dropdown-toggle"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
        <?= $currentType->getEntityDisplayName() ?>
    </button>

    <div class="dropdown-menu">
        <h6 class="dropdown-header"><?= t('Types') ?></h6>
        <?php
        foreach ($types as $type) {
            if ($entityAction === 'view') {
                $action = URL::to('/dashboard/express/entries', $type->getID());
            } else {
                $action = URL::to('/dashboard/system/express/entities', 'view_entity', $type->getID());
            }
            ?>
            <a class="dropdown-item" href="<?= $action ?>">
                <?= $type->getEntityDisplayName() ?>
            </a>
            <?php
        }
        ?>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="<?= URL::to('/dashboard/system/express/entities', 'add') ?>">
            <?= t('Add Data Object') ?>
        </a>
    </div>
</div>
