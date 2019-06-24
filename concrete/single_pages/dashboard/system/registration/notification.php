<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php

$permissionAccess = $key->getPermissionAssignmentObject()->getPermissionAccessObject();
if (!is_object($permissionAccess)) {
	$permissionAccess = PermissionAccess::create($key);
}

?>
<form id="ccm-permissions-detail-form" onsubmit="return ccm_submitPermissionsDetailForm()" method="post" action="<?=$key->getPermissionAssignmentObject()->getPermissionKeyToolsURL()?>">


	<input type="hidden" name="paID" value="<?=$permissionAccess->getPermissionAccessID()?>" />

	<div id="ccm-tab-content-access-types">
		<?php View::element('permission/keys/notify_in_notification_center', array('permissionAccess' => $permissionAccess))?>

	</div>


	<div class="ccm-dashboard-form-actions-wrapper" style="display:none">
		<div class="ccm-dashboard-form-actions">
			<button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
		</div>
	</div>

</form>

<script type="text/javascript">
	var ccm_permissionDialogURL = '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/miscellaneous';
	ccm_deleteAccessEntityAssignment = function(peID) {
		jQuery.fn.dialog.showLoader();

		if (ccm_permissionDialogURL.indexOf('?') > 0) {
			var qs = '&';
		} else {
			var qs = '?';
		}

		$.get('<?=$key->getPermissionAssignmentObject()->getPermissionKeyToolsURL("remove_access_entity")?>&paID=<?=$permissionAccess->getPermissionAccessID()?>&peID=' + peID, function() {
			$.get(ccm_permissionDialogURL + qs + 'paID=<?=$permissionAccess->getPermissionAccessID()?>&message=entity_removed&pkID=<?=$key->getPermissionKeyID()?>', function(r) {
				window.location.reload();
			});
		});
	}

	ccm_addAccessEntity = function(peID, pdID, accessType) {
		jQuery.fn.dialog.closeTop();
		jQuery.fn.dialog.showLoader();

		if (ccm_permissionDialogURL.indexOf('?') > 0) {
			var qs = '&';
		} else {
			var qs = '?';
		}

		$.get('<?=$key->getPermissionAssignmentObject()->getPermissionKeyToolsURL("add_access_entity")?>&paID=<?=$permissionAccess->getPermissionAccessID()?>&pdID=' + pdID + '&accessType=' + accessType + '&peID=' + peID, function(r) {
			$.get(ccm_permissionDialogURL + qs + 'paID=<?=$permissionAccess->getPermissionAccessID()?>&message=entity_added&pkID=<?=$key->getPermissionKeyID()?>', function(r) {
				window.location.reload();
			});
		});
	}


	ccm_submitPermissionsDetailForm = function() {
		jQuery.fn.dialog.showLoader();
		$("#ccm-permissions-detail-form").ajaxSubmit(function(r) {
			window.location.reload();
		});
		return false;
	}

</script>