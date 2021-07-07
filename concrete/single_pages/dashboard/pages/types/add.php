<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<form method="post" action="<?= $view->action('submit') ?>">
    <?php View::element('page_types/form/base', ['siteType' => $siteType]) ?>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?= URL::to('/dashboard/pages/types') ?>" class="btn btn-secondary"><?= t('Cancel') ?></a>
            <button class="float-end btn btn-primary" type="submit"><?= t('Add') ?></button>
        </div>
    </div>
</form>
