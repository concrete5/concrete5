<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<p>
<?php

$enablePermissions = false;
if (!$f->overrideFileFolderPermissions()) {
    ?>

	<div class="alert alert-notice">
	<p>
	<?=t("Permissions for this file are currently dependent on its folder and global file permissions.")?>
	</p>
	<br/>
	<a href="javascript:void(0)" class="btn btn-default btn-sm" onclick="ccm_setFilePermissionsToOverride()"><?=t('Override Permissions')?></a>
	</div>
	
<?php 
} else {
    $enablePermissions = true;
    ?>

	<div class="alert alert-notice">
	<p><?=t("Permissions for this file currently override its sets and the global file permissions.")?></p>
	<br/>
	<a href="javascript:void(0)" class="btn btn-default btn-sm" onclick="ccm_revertToGlobalFilePermissions()"><?=t('Revert to Folder and Global Permissions')?></a>
	</div>

<?php 
} ?>

</p>

<?=Loader::element('permission/help');?>

<?php $cat = PermissionKeyCategory::getByHandle('file');?>

<form method="post" id="ccm-permission-list-form" action="<?=$cat->getToolsURL("save_permission_assignments")?>&fID=<?=$f->getFileID()?>">

<table class="ccm-permission-grid table table-striped">
<?php
$permissions = PermissionKey::getList('file');
foreach ($permissions as $pk) {
    $pk->setPermissionObject($f);
    ?>
	<tr>
	<td class="ccm-permission-grid-name" id="ccm-permission-grid-name-<?=$pk->getPermissionKeyID()?>"><strong><?php if ($enablePermissions) {
    ?><a dialog-title="<?=$pk->getPermissionKeyDisplayName()?>" data-pkID="<?=$pk->getPermissionKeyID()?>" data-paID="<?=$pk->getPermissionAccessID()?>" onclick="ccm_permissionLaunchDialog(this)" href="javascript:void(0)"><?php 
}
    ?><?=$pk->getPermissionKeyDisplayName()?><?php if ($enablePermissions) {
    ?></a><?php 
}
    ?></strong></td>
	<td id="ccm-permission-grid-cell-<?=$pk->getPermissionKeyID()?>" <?php if ($enablePermissions) {
    ?>class="ccm-permission-grid-cell"<?php 
}
    ?>><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
</tr>
<?php 
} ?>
<?php if ($enablePermissions) {
    ?>
<tr>
	<td class="ccm-permission-grid-name" ></td>
	<td>
	<?=Loader::element('permission/clipboard', array('pkCategory' => $cat))?>
	</td>
</tr>
<?php 
} ?>

</table>
</form>

<?php if ($enablePermissions) {
    ?>
<div id="ccm-file-permissions-advanced-buttons" style="display: none">
	<button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-default pull-left"><?=t('Cancel')?></button>
	<button onclick="$('#ccm-permission-list-form').submit()" class="btn btn-primary pull-right"><?=t('Save')?></i></button>
</div>
<?php 
} ?>

<script type="text/javascript">

ccm_permissionLaunchDialog = function(link) {
	var dupe = $(link).attr('data-duplicate');
	if (dupe != 1) {
		dupe = 0;
	}
	jQuery.fn.dialog.open({
		title: $(link).attr('dialog-title'),
		href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/file?duplicate=' + dupe + '&fID=<?=$f->getFileID()?>&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
		modal: true,
		width: 500,
		height: 380
	});		
}

$(function() {
	$('#ccm-permission-list-form').ajaxForm({
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		
		success: function(r) {
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
		}		
	});
});

ccm_revertToGlobalFilePermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("revert_to_global_file_permissions")?>&fID=<?=$f->getFileID()?>', function() { 
		ccm_refreshFilePermissions();
	});
}

ccm_setFilePermissionsToOverride = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("override_global_file_permissions")?>&fID=<?=$f->getFileID()?>', function() { 
		ccm_refreshFilePermissions();
	});
}

ccm_refreshFilePermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/permissions?fID=<?=$f->getFileID()?>', function(r) { 
		jQuery.fn.dialog.replaceTop(r);
		ccm_filePermissionsSetupButtons();
		jQuery.fn.dialog.hideLoader();
	});
}

</script>