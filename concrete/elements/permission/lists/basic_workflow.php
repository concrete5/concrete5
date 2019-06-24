<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php $cat = PermissionKeyCategory::getByHandle('basic_workflow');?>

<table class="ccm-permission-grid table table-striped">
<?php
$permissions = PermissionKey::getList('basic_workflow');

foreach ($permissions as $pk) {
    $pk->setPermissionObject($workflow);
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
<?php if ($enablePermissions) {
    ?>

	<script type="text/javascript">
	ccm_permissionLaunchDialog = function(link) {
		var dupe = $(link).attr('data-duplicate');
		if (dupe != 1) {
			dupe = 0;
		}
		jQuery.fn.dialog.open({
			title: $(link).attr('dialog-title'),
			href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/basic_workflow?duplicate=' + dupe + '&wfID=<?=$workflow->getWorkflowID()?>&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
			modal: true,
			width: 500,
			height: 380
		});		
	}
	</script>
	
<?php 
} ?>
