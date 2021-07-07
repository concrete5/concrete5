<?php defined('C5_EXECUTE') or die("Access Denied.");

use \Concrete\Core\Tree\Node\Node as TreeNode;

?>
<div id="topics-tree-node-permissions">
<?php
$handle = $node->getPermissionObjectKeyCategoryHandle();
$enablePermissions = false;

if (!isset($disableDialog) || !$disableDialog) {

	if (!$node->overrideParentTreeNodePermissions()) {
		$permNode = TreeNode::getByID($node->getTreeNodePermissionsNodeID());
		?>

		<div class="alert alert-info">
		<?=t("Permissions for this node are currently inherited from <strong>%s</strong>.", $permNode->getTreeNodeDisplayName())?>
		<br/><br/>
		<a href="javascript:void(0)" class="btn btn-sm btn-warning" onclick="TopicsPermissions.setTreeNodePermissionsToOverride()"><?=t('Override Permissions')?></a>
		</div>

	<?php
	} else {
		$enablePermissions = true;
		?>

		<div class="alert alert-info">
		<?=t("Permissions for this node currently override its parents' permissions.")?>
		<?php if ($node->getTreeNodeParentID() > 0) {
		?>
		<br/><br/>
			<a href="javascript:void(0)" class="btn btn-sm btn-warning" onclick="TopicsPermissions.setTreeNodePermissionsToInherit()"><?=t('Revert to Parent Permissions')?></a>
		<?php
	}
		?>
	</div>

<?php
	}

} else {
	$enablePermissions = true;
}?>


	<?php if (!isset($disableDialog) || !$disableDialog) { ?>
		<?=Loader::element('permission/help');?>
	<?php } ?>

<?php $cat = PermissionKeyCategory::getByHandle($handle);?>

	<?php if (!isset($disableDialog) || !$disableDialog) { ?>
		<form method="post" id="ccm-permission-list-form" action="<?=h ($cat->getTaskURL('save_permission_assignments', ['treeNodeID' => $node->getTreeNodeID()])) ?>">
	<?php } ?>

<table class="ccm-permission-grid table table-striped">
<?php
$permissions = PermissionKey::getList($handle);
foreach ($permissions as $pk) {
    $pk->setPermissionObject($node);
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
	<?php if (!isset($disableDialog) || !$disableDialog) { ?>
	</form>
<?php } ?>
<?php if ($enablePermissions) {
    ?>
	<?php if (!isset($disableDialog) || !$disableDialog) { ?>

		<div id="topics-tree-node-permissions-buttons" class="dialog-buttons">
		<button href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?=t('Cancel')?></button>
		<button onclick="$('#ccm-permission-list-form').submit()" class="btn btn-primary float-end"><?=t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
	</div>
	<?php } ?>
<?php
} else {
    ?>
	<div class="dialog-buttons"></div>
<?php
} ?>

</div>

<script type="text/javascript">

ccm_permissionLaunchDialog = function(link) {
	var dupe = $(link).attr('data-duplicate');
	if (dupe != 1) {
		dupe = 0;
	}
	jQuery.fn.dialog.open({
		title: $(link).attr('dialog-title'),
		href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/permissions/tree/node?duplicate=' + dupe + '&treeNodeID=<?=$node->getTreeNodeID()?>&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
		modal: true,
		width: 500,
		height: 380
	});
}

<?php if (!isset($disableDialog) || !$disableDialog) { ?>

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


	var TopicsPermissions = {

		refresh: function() {
			jQuery.fn.dialog.showLoader();
			$.get('<?=URL::to('/ccm/system/dialogs/tree/node/permissions')?>?treeNodeID=<?=$node->getTreeNodeID()?>', function(r) {
				jQuery.fn.dialog.replaceTop(r);
				jQuery.fn.dialog.hideLoader();
			});
		},

		setTreeNodePermissionsToInherit: function() {
			jQuery.fn.dialog.showLoader();
			$.get(<?= json_encode($pk->getPermissionAssignmentObject()->getPermissionKeyTaskURL('revert_to_global_node_permissions', ['treeNodeID' => $node->getTreeNodeID()])) ?>, function() {
				TopicsPermissions.refresh();
			});
		},

		setTreeNodePermissionsToOverride: function() {
			jQuery.fn.dialog.showLoader();
			$.get(<?= json_encode($pk->getPermissionAssignmentObject()->getPermissionKeyTaskURL('override_global_node_permissions', ['treeNodeID' => $node->getTreeNodeID()])) ?>, function() {
				TopicsPermissions.refresh();
			});
		}

	};

	<?php } ?>

</script>
