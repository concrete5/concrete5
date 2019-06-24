<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<section class="ccm-ui">
	<header><?=t('Page Permissions')?></header>
	<form method="post" action="<?=$controller->action('save_simple')?>" data-dialog-form="permissions" data-panel-detail-form="permissions">
		<?=Loader::helper('concrete/ui/help')->display('panel', '/page/permissions')?>
		
		<label class="control-label"><?=t('Who can view this page?')?></label>



		<?php

        foreach ($gArray as $g) {
            ?>

		<div class="radio"><label><input type="checkbox" name="readGID[]" value="<?=$g->getGroupID()?>" <?php if (in_array($g->getGroupID(), $viewAccess)) {
    ?> checked <?php 
}
            ?> /> <?=$g->getGroupDisplayName(false)?></label></div>

		<?php 
        } ?>

		<hr/>

		<label class="control-label"><?=t('Who can edit this page?')?></label>

		<?php

        foreach ($gArray as $g) {
            ?>

		<div class="radio"><label><input type="checkbox" name="editGID[]" value="<?=$g->getGroupID()?>" <?php if (in_array($g->getGroupID(), $editAccess)) {
    ?> checked <?php 
}
            ?> /> <?=$g->getGroupDisplayName(false)?></label></div>

		<?php 
        } ?>

	</form>
	<div class="dialog-buttons ccm-panel-detail-form-actions">
		<button class="pull-left btn btn-default" type="button" data-dialog-action="cancel" data-panel-detail-action="cancel"><?=t('Cancel')?></button>
		<button class="pull-right btn btn-success" type="button" data-dialog-action="submit" data-panel-detail-action="submit"><?=t('Save Changes')?></button>
	</div>
</section>