<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php $cat = PermissionKeyCategory::getByHandle('conversation');?>

<table class="ccm-permission-grid table table-striped">
<?php
$cnvID = 0;
if (is_object($conversation)) {
    $cnvID = $conversation->getConversationID();
}
$permissions = PermissionKey::getList('conversation');

foreach ($permissions as $pk) {
    $pk->setPermissionObject($conversation);
    ?>
	<tr>
	<td class="ccm-permission-grid-name" id="ccm-permission-grid-name-<?=$pk->getPermissionKeyID()?>"><strong><a dialog-title="<?=$pk->getPermissionKeyDisplayName()?>" data-pkID="<?=$pk->getPermissionKeyID()?>" data-paID="<?=$pk->getPermissionAccessID()?>" onclick="ccm_permissionLaunchDialog(this)" href="javascript:void(0)"><?=$pk->getPermissionKeyDisplayName()?></a></strong></td>
	<td id="ccm-permission-grid-cell-<?=$pk->getPermissionKeyID()?>" class="ccm-permission-grid-cell"><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
</tr>
<?php 
} ?>
<tr>
	<td class="ccm-permission-grid-name" ></td>
	<td>
	<?=Loader::element('permission/clipboard', array('pkCategory' => $cat))?>
	</td>
</tr>

</table>


	<script type="text/javascript">
	ccm_permissionLaunchDialog = function(link) {
		var dupe = $(link).attr('data-duplicate');
		if (dupe != 1) {
			dupe = 0;
		}

		jQuery.fn.dialog.open({
			title: $(link).attr('dialog-title'),
			href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/conversation?duplicate=' + dupe + '&cnvID=<?=$cnvID?>&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
			modal: true,
			width: 500,
			height: 380
		});		
	}
	</script>
