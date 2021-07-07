<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php ob_start(); ?>
<?=Loader::element('permission/help');?>
<?php $help = ob_get_contents(); ?>
<?php ob_end_clean(); ?>

<form method="post" action="<?=$view->action('save')?>">
    <?=Loader::helper('validation/token')->output('save_permissions')?>

    <?php
    $tp = new TaskPermission();
    if ($tp->canAccessTaskPermissions()) {
        ?>
        <?php View::element('permission/lists/board_admin');
        ?>
    <?php 
    } else {
        ?>
        <p><?=t('You cannot access task permissions.')?></p>
    <?php 
    } ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>
</form>
