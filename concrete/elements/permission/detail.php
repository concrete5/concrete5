<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php
if ($_REQUEST['paID'] && $_REQUEST['paID'] > 0) {
    $pa = PermissionAccess::getByID($_REQUEST['paID'], $permissionKey);
    if ($pa->isPermissionAccessInUse() || (isset($_REQUEST['duplicate']) && $_REQUEST['duplicate'] == '1')) {
        $pa = $pa->duplicate();
    }
} else {
    $pa = PermissionAccess::create($permissionKey);
}

?>

<div class="ccm-ui" id="ccm-permission-detail">
<form id="ccm-permissions-detail-form" onsubmit="return ccm_submitPermissionsDetailForm()" method="post" action="<?=$permissionKey->getPermissionAssignmentObject()->getPermissionKeyToolsURL()?>">

<input type="hidden" name="paID" value="<?=$pa->getPermissionAccessID()?>" />

<?php $workflows = Workflow::getList();?>

<?php Loader::element('permission/message_list'); ?>

<?php
$tabs = array();

 if ($permissionKey->hasCustomOptionsForm() || ($permissionKey->canPermissionKeyTriggerWorkflow() && count($workflows) > 0)) {
     ?>
	<?php
    $tabs[] = array('access-types', t('Access'), true);
     if ($permissionKey->canPermissionKeyTriggerWorkflow() && count($workflows) > 0) {
         $tabs[] = array('workflow', t('Workflow'));
     }
     if ($permissionKey->hasCustomOptionsForm()) {
         $tabs[] = array('custom-options', t('Details'));
     }
     ?>
	<?=Loader::helper('concrete/ui')->tabs($tabs);
     ?>
<?php
 } ?>

<?php if ($permissionKey->getPermissionKeyDisplayDescription()) {
    ?>
<div class="dialog-help">
<?=$permissionKey->getPermissionKeyDisplayDescription()?>
</div>
<?php
} ?>


<div id="ccm-tab-content-access-types" <?php if (count($tabs) > 0) {
    ?>class="ccm-tab-content"<?php
} ?>>
<?php
$pkCategoryHandle = $permissionKey->getPermissionKeyCategoryHandle();
$accessTypes = $permissionKey->getSupportedAccessTypes();
Loader::element('permission/access/list', array('pkCategoryHandle' => $pkCategoryHandle, 'permissionAccess' => $pa, 'accessTypes' => $accessTypes)); ?>
</div>

<?php if ($permissionKey->hasCustomOptionsForm()) {
    ?>
<div id="ccm-tab-content-custom-options" class="ccm-tab-content">

<?php if ($permissionKey->getPackageID() > 0) {
    ?>
	<?php Loader::packageElement('permission/keys/' . $permissionKey->getPermissionKeyHandle(), $permissionKey->getPackageHandle(), array('permissionAccess' => $pa));
    ?>
<?php
} else {
    ?>
	<?php Loader::element('permission/keys/' . $permissionKey->getPermissionKeyHandle(), array('permissionAccess' => $pa));
    ?>
<?php
}
    ?>

</div>

<?php
} ?>

<?php if ($permissionKey->canPermissionKeyTriggerWorkflow() && count($workflows) > 0) {
    ?>
	<?php
    $selectedWorkflows = $pa->getWorkflows();
    $workflowIDs = array();
    foreach ($selectedWorkflows as $swf) {
        $workflowIDs[] = $swf->getWorkflowID();
    }
    ?>

	<div id="ccm-tab-content-workflow" class="ccm-tab-content">
			<div class="form-group">
    			<label class="control-label"><?=t('Attach Workflow to this Permission')?></label>
				<?php foreach ($workflows as $wf) {
    ?>
					<div class="checkbox"><label><input type="checkbox" name="wfID[]" value="<?=$wf->getWorkflowID()?>" <?php if (count($wf->getRestrictedToPermissionKeyHandles()) > 0 && (!in_array($permissionKey->getPermissionKeyHandle(), $wf->getRestrictedToPermissionKeyHandles()))) {
    ?> disabled="disabled" <?php
}
    ?>
					<?php if (in_array($wf->getWorkflowID(), $workflowIDs)) {
    ?> checked="checked" <?php
}
    ?> /> <?=$wf->getWorkflowDisplayName()?></label></div>
				<?php
}
    ?>
			</div>
	</div>
<?php
} ?>

	<div class="dialog-buttons">
		<button href="javascript:void(0)" class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
		<button type="submit" class="btn btn-primary pull-right" onclick="$('#ccm-permissions-detail-form').submit()"><?=t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
	</div>
</form>
</div>

<script type="text/javascript">

	<?php
	$permissionObject = $permissionKey->getPermissionObject();
	if (is_object($permissionObject)) {
	?>
	var ccm_permissionObjectID = '<?=$permissionObject->getPermissionObjectIdentifier()?>';
	var ccm_permissionObjectKeyCategoryHandle = '<?=$permissionObject->getPermissionObjectKeyCategoryHandle()?>';
	<?php } ?>

	$(function() {

	ccm_addAccessEntity = function(peID, pdID, accessType) {
		jQuery.fn.dialog.closeTop();
		jQuery.fn.dialog.showLoader();

		if (ccm_permissionDialogURL.indexOf('?') > 0) {
			var qs = '&';
		} else {
			var qs = '?';
		}

		$.get('<?=$permissionKey->getPermissionAssignmentObject()->getPermissionKeyToolsURL("add_access_entity")?>&paID=<?=$pa->getPermissionAccessID()?>&pdID=' + pdID + '&accessType=' + accessType + '&peID=' + peID, function(r) {
			$.get(ccm_permissionDialogURL + qs + 'paID=<?=$pa->getPermissionAccessID()?>&message=entity_added&pkID=<?=$permissionKey->getPermissionKeyID()?>', function(r) {
				jQuery.fn.dialog.replaceTop(r);
				jQuery.fn.dialog.hideLoader();
			});
		});
	}

	ccm_deleteAccessEntityAssignment = function(peID) {
		jQuery.fn.dialog.showLoader();

		if (ccm_permissionDialogURL.indexOf('?') > 0) {
			var qs = '&';
		} else {
			var qs = '?';
		}

		$.get('<?=$permissionKey->getPermissionAssignmentObject()->getPermissionKeyToolsURL("remove_access_entity")?>&paID=<?=$pa->getPermissionAccessID()?>&peID=' + peID, function() {
			$.get(ccm_permissionDialogURL + qs + 'paID=<?=$pa->getPermissionAccessID()?>&message=entity_removed&pkID=<?=$permissionKey->getPermissionKeyID()?>', function(r) {
				jQuery.fn.dialog.replaceTop(r);
				jQuery.fn.dialog.hideLoader();
			});
		});
	}

	ccm_submitPermissionsDetailForm = function() {
		jQuery.fn.dialog.showLoader();
		$("#ccm-permissions-detail-form").ajaxSubmit(function(r) {
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
			// now we reload the permission key to use the new permission assignment
			var gc = $('#ccm-permission-grid-cell-<?=$permissionKey->getPermissionKeyID()?>');
			if (gc.length > 0) {
				gc.load('<?=$permissionKey->getPermissionAssignmentObject()->getPermissionKeyToolsURL("display_access_cell")?>&paID=<?=$pa->getPermissionAccessID()?>', function() {
					$('#ccm-permission-grid-name-<?=$permissionKey->getPermissionKeyID()?> a').attr('data-paID', '<?=$pa->getPermissionAccessID()?>');
					if (typeof(ccm_submitPermissionsDetailFormPost) != 'undefined') {
						ccm_submitPermissionsDetailFormPost();
					}
				});
			} else {
				if (typeof(ccm_submitPermissionsDetailFormPost) != 'undefined') {
					ccm_submitPermissionsDetailFormPost();
				}
			}
		});
		return false;
	}

	<?php if (isset($_REQUEST['message']) && $_REQUEST['message'] == 'custom_options_saved') {
    ?>
		$('a[data-tab=custom-options]').click();
	<?php
} ?>

	<?php if (isset($_REQUEST['message']) && $_REQUEST['message'] == 'workflows_saved') {
    ?>
		$('a[data-tab=workflow]').click();
	<?php
} ?>


});
</script>
