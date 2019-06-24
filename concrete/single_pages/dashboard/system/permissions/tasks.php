<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

	<?php ob_start(); ?>
	<?=Loader::element('permission/help');?>
	<?php $help = ob_get_contents(); ?>
	<?php ob_end_clean(); ?>
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Task Permissions'), $help, 'span8 offset2', false)?>
	<form method="post" action="<?=$view->action('save')?>">
	<?=Loader::helper('validation/token')->output('save_permissions')?>
	
	<?php
    $tp = new TaskPermission();
    if ($tp->canAccessTaskPermissions()) {
        ?>	
		<?php Loader::element('permission/lists/miscellaneous')?>
	<?php 
    } else {
        ?>
		<p><?=t('You cannot access task permissions.')?></p>
	<?php 
    } ?>

	<div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=$view->url('/dashboard/system/permissions/tasks')?>" class="btn btn-default pull-left"><?=t('Cancel')?></a>
            <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>
	</form>
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>