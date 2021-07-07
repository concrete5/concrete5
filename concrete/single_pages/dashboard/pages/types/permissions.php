<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<p class="lead"><?= $pagetype->getPageTypeDisplayName(); ?></p>

<form method="post" id="ccm-permission-list-form"   action="<?= $view->action('save'); ?>">
    <?php $token->output('save_permissions'); ?>
    <input type="hidden" name="ptID" value="<?= $pagetype->getPageTypeID(); ?>" />
    <fieldset>
        <legend><?= t('Permissions for This Page Type'); ?></legend>
        <?php
            View::element('permission/lists/page_type', [
                'pagetype' => $pagetype,
            ]);
        ?>
    </fieldset>

    <fieldset>
        <legend><?= t('Permissions for All Pages Created Of This Type'); ?></legend>
        <?php if (Config::get('concrete.permissions.model') == 'advanced') {
            View::element('permission/lists/page', [
                'page' => $defaultPage,
                'editPermissions' => true,
            ]);
        } else {
            ?>
            <div class="alert alert-info"><?= t('You must <a href="%s">enable advanced permissions</a> to set permissions for pages created of a certain type.', URL::to('/dashboard/system/permissions/advanced')); ?></div>
        <?php
        } ?>
    </fieldset>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?= URL::to('/dashboard/pages/types'); ?>" class="btn btn-secondary"><?= t('Back'); ?></a>
            <button type="submit" value="<?= t('Save'); ?>" class="btn btn-primary float-end"><?= t('Save'); ?></button>
        </div>
    </div>
</form>
