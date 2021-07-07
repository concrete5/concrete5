<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

	<?php $root = (new \Concrete\Core\File\Filesystem())->getRootFolder(); ?>

	<form method="post" action="<?=$view->action('save')?>" id="ccm-permission-list-form">

		<?=Loader::helper('validation/token')->output('save_permissions')?>
		<?php
		$tp = new TaskPermission();
		if ($tp->canAccessTaskPermissions()) {
			?>
			<?php Loader::element('permission/lists/tree/node', array('node' => $root, 'disableDialog' => true))?>
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
