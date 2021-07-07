<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">
	<?php
    $c = Page::getCurrentPage();
    $a = $b->getBlockAreaObject();

$enablePermissions = false;
if (!$b->overrideAreaPermissions()) {
    ?>

	<div class="block-message alert-message notice">
	<p>
	<?=t("Permissions for this block are currently dependent on the area containing this block.")?>
	</p>
	<a href="javascript:void(0)" class="btn btn-secondary btn-sm" onclick="ccm_setBlockPermissionsToOverride()"><?=t('Override Permissions')?></a>
	<br/>
	<br/>
	</div>

<?php 
} else {
    $enablePermissions = true;
    ?>

	<div class="block-message alert-message notice">
	<p><?=t("Permissions for this block currently override those of the area and page.")?></p>
	<a href="javascript:void(0)" class="btn btn-sm btn-secondary" onclick="ccm_revertToAreaPermissions()"><?=t('Revert to Area Permissions')?></a>
	<br/>
	<br/>
	</div>

<?php 
} ?>


<?=Loader::element('permission/help');?>

<?php $cat = PermissionKeyCategory::getByHandle('block');?>
<form method="post" id="ccm-permission-list-form" action="<?= h($cat->getTaskURL('save_permission_assignments', ['cID' => $c->getCollectionID(), 'arHandle' => $b->getAreaHandle(), 'cvID' => $c->getVersionID(), 'bID' => $b->getBlockID()])) ?>">

<table class="ccm-permission-grid table table-striped">

<?php
$permissions = PermissionKey::getList('block');
foreach ($permissions as $pk) {
    $pk->setPermissionObject($b);

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
<div class="dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?=t('Cancel')?></a>
	<button onclick="$('#ccm-permission-list-form').submit()" class="btn btn-primary float-end"><?=t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
</div>
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
		href: '<?=URL::to('/ccm/system/dialogs/block/permissions/detail')?>?duplicate=' + dupe + '&bID=<?=$b->getBlockID()?>&arHandle=<?=urlencode($b->getAreaHandle())?>&cvID=<?=$c->getVersionID()?>&bID=<?=$b->getBlockID()?>&cID=<?=$c->getCollectionID()?>&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
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
            ConcreteToolbar.disableDirectExit();
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
		}
	});
});

ccm_revertToAreaPermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get(<?= json_encode($pk->getPermissionAssignmentObject()->getPermissionKeyTaskURL('revert_to_area_permissions', ['bID' => $b->getBlockID(), 'cvID' => $c->getVersionID(), 'arHandle' => $b->getAreaHandle(), 'cID' => $c->getCollectionID()])) ?>, function() {
        ConcreteToolbar.disableDirectExit();
		ccm_refreshBlockPermissions();
	});
}

ccm_setBlockPermissionsToOverride = function() {
	jQuery.fn.dialog.showLoader();
	$.get(<?= json_encode($pk->getPermissionAssignmentObject()->getPermissionKeyTaskURL('override_area_permissions', ['bID' => $b->getBlockID(), 'cvID' => $c->getVersionID(), 'arHandle' => $b->getAreaHandle(), 'cID' => $c->getCollectionID()])) ?>, function() {
        ConcreteToolbar.disableDirectExit();
		ccm_refreshBlockPermissions();
	});
}

ccm_refreshBlockPermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=URL::to('/ccm/system/dialogs/block/permissions/list')?>?bID=<?=$b->getBlockID()?>&cvID=<?=$c->getVersionID()?>&arHandle=<?=urlencode($b->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function(r) {
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});
}

</script>
