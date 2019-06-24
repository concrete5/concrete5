<?php
defined('C5_EXECUTE') or die('Access Denied.');
$u = new User();
$form = Loader::helper('form');
$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
    die(t('Access Denied'));
}

$form = Loader::helper('form');

$pages = array();
if (is_array($_REQUEST['cID'])) {
    foreach ($_REQUEST['cID'] as $cID) {
        $pages[] = Page::getByID($cID);
    }
} else {
    $pages[] = Page::getByID($_REQUEST['cID']);
}

$pcnt = 0;
$cIDStr = '';
foreach ($pages as $c) {
    $cp = new Permissions($c);
    if ($cp->canEditPagePermissions()) {
        $cIDStr .= '&cID[]=' . $c->getCollectionID();
        ++$pcnt;
    }
}

$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);

?>
<div class="ccm-ui">

<?php if ($pcnt == 0) {
    ?>
	<?=t("You do not have permission to change permissions on any of the selected pages.");
    ?>
<?php 
} else {
    $dh = Loader::helper('date');
    $dt = Loader::helper('form/date_time');
    $editPermissions = true;
    $permissionsInherit = array();
    foreach ($pages as $_c) {
        $permissionsInherit[] = $_c->getCollectionInheritance();
        $permissionsSubpageOverride[] = $_c->overrideTemplatePermissions();
    }
    $permissionsInherit = array_unique($permissionsInherit);
    $permissionsSubpageOverride = array_unique($permissionsSubpageOverride);
    if (count($permissionsInherit) == 1) {
        $permissionsInherit = $permissionsInherit[0];
    } else {
        $permissionsInherit = '-1';
    }
    if (count($permissionsSubpageOverride) == 1) {
        $permissionsSubpageOverride = $permissionsSubpageOverride[0];
    } else {
        $permissionsSubpageOverride = '-1';
    }

    if ($_REQUEST['subtask'] == 'set' && $permissionsInherit == 'OVERRIDE') {
        ?>
		
		
	<?php
    $pk = PagePermissionKey::getByID($_REQUEST['pkID']);
        $pk->setPermissionObject($pages[0]);
        $pk->setMultiplePageArray($pages);

        ?>
	
	<?php Loader::element("permission/detail", array('permissionKey' => $pk));
        ?>
	
	
	<script type="text/javascript">
	var ccm_permissionDialogURL = '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/permissions?subtask=set<?=$cIDStr?>'; 
	</script>

	
	
	<?php 
    } else {
        ?>

	<form>
	
	<div class="ccm-pane-options" style="padding-bottom: 0px">
	<div class="clearfix">
	<label for="ccm-page-permissions-inherit"><?=t('Assign Permissions')?></label>
	<div class="input">
	   <select id="ccm-page-permissions-inherit" style="width: 220px">
	   <?php if ($permissionsInherit == '-1') {
    ?>	<option value="-1" selected><?=t('** Multiple Settings')?></option><?php 
}
        ?>
		<option value="PARENT" <?php if ($permissionsInherit == 'PARENT') {
    ?>selected<?php 
}
        ?>><?=t('By Area of Site (Hierarchy)')?></option>
		<option value="TEMPLATE" <?php if ($permissionsInherit == 'TEMPLATE') {
    ?>selected<?php 
}
        ?>><?=t('From Page Type Defaults')?></option>
		<option value="OVERRIDE" <?php if ($permissionsInherit == 'OVERRIDE') {
    ?>selected<?php 
}
        ?>><?=t('Manually')?></option>
	  </select>
	</div>
	</div>

	<div class="clearfix">
	<label for="ccm-page-permissions-subpages-override-template-permissions"><?=t('Subpage Permissions')?></label>
	<div class="input">
		<select id="ccm-page-permissions-subpages-override-template-permissions" style="width: 260px">
		   	<?php if ($permissionsSubpageOverride == '-1') {
    ?><option value="-1" selected><?=t('** Multiple Settings')?></option><?php 
}
        ?>
			<option value="0" <?php if ($permissionsSubpageOverride == '0') {
    ?>selected<?php 
}
        ?>><?=t('Inherit page type default permissions.')?></option>
			<option value="1" <?php if ($permissionsSubpageOverride == '1') {
    ?>selected<?php 
}
        ?>><?=t('Inherit the permissions of this page.')?></option>
		</select>
	</div>
	</div>
	
	</div>
	</form>
	
	<br/>
	
	
	<?php if ($permissionsInherit == 'OVERRIDE') {
    ?>

<?=Loader::element('permission/help');
    ?>

<?php $cat = PermissionKeyCategory::getByHandle('page');
    ?>
<form method="post" id="ccm-permission-list-form" action="<?=$cat->getToolsURL("save_permission_assignments")?><?=$cIDStr?>">

<table class="ccm-permission-grid table table-striped">
<?php
$permissions = PermissionKey::getList('page');
    foreach ($permissions as $pk) {
        $pk->setPermissionObject($c);
        ?>
	<tr>
	<td class="ccm-permission-grid-name" id="ccm-permission-grid-name-<?=$pk->getPermissionKeyID()?>"><strong><?php if ($editPermissions) {
    ?><a dialog-title="<?=$pk->getPermissionKeyDisplayName()?>" data-pkID="<?=$pk->getPermissionKeyID()?>" data-paID="<?=$pk->getPermissionAccessID()?>" onclick="ccm_permissionLaunchDialog(this)" href="javascript:void(0)"><?php 
}
        ?><?=$pk->getPermissionKeyDisplayName()?><?php if ($editPermissions) {
    ?></a><?php 
}
        ?></strong></td>
	<td id="ccm-permission-grid-cell-<?=$pk->getPermissionKeyID()?>" <?php if ($editPermissions) {
    ?>class="ccm-permission-grid-cell"<?php 
}
        ?>><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
</tr>
<?php 
    }
    ?>
</table>

</form>

<script type="text/javascript">
ccm_permissionLaunchDialog = function(link) {
	jQuery.fn.dialog.open({
		title: $(link).attr('dialog-title'),
		href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/permissions?subtask=set<?=$cIDStr?>&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
		modal: false,
		width: 500,
		height: 380
	});		
}
</script>

 <?php if ($editPermissions) {
    ?>
<div class="dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="btn btn-default pull-left"><?=t('Cancel')?></a>
	<button onclick="$('#ccm-permission-list-form').submit()" class="btn btn-primary pull-right"><?=t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
</div>
<?php 
}
    ?>

	
	<?php 
} else {
    ?>
		<?php $pkl = PermissionKey::getList('page');
    $pk = $pkl[0];
    ?>
		<p><?=t('You may only set specific permissions for pages if they are set to override defaults or their parent pages.')?></p>
	<?php 
}
        ?>
	
	
	<div id="ccm-page-permissions-confirm-dialog" style="display: none">
	<?=t('Changing this setting will affect this page immediately. Are you sure?')?>
	<div id="dialog-buttons-start">
		<input type="button" class="btn" value="<?=t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" />
		<input type="button" class="btn btn-primary pull-right" value="<?=t('Ok')?>" onclick="ccm_pagePermissionsConfirmInheritanceChange()" />
	</div>
	</div>
	
	
	<?php $pk->setPermissionObject($pages[0]);
        ?>
	<?php $pk->setMultiplePageArray($pages);
        ?>

	<script type="text/javascript">
	var inheritanceVal = '';
	
	ccm_pagePermissionsCancelInheritance = function() {
		$('#ccm-page-permissions-inherit').val(inheritanceVal);
	}
	
	ccm_pagePermissionsConfirmInheritanceChange = function() { 
		jQuery.fn.dialog.showLoader();
		$.getJSON('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("change_permission_inheritance")?>&mode=' + $('#ccm-page-permissions-inherit').val(), function(r) { 
			if (r.deferred) {
				jQuery.fn.dialog.closeAll();
				jQuery.fn.dialog.hideLoader();
				ConcreteAlert.notify({
				'message': ccmi18n_sitemap.setPermissionsDeferredMsg,
				'title': ccmi18n_sitemap.setPagePermissions
				});
			} else {
				jQuery.fn.dialog.closeTop();
				ccm_refreshPagePermissions();
			}
		});
	}
	
	
	$(function() {

		$('#ccm-permission-list-form').ajaxForm({
			dataType: 'json',
			
			beforeSubmit: function() {
				jQuery.fn.dialog.showLoader();
			},
			
			success: function(r) {
				jQuery.fn.dialog.hideLoader();
				jQuery.fn.dialog.closeTop();
				if (!r.deferred) {
					ConcreteAlert.notify({
					'message': ccmi18n_sitemap.setPagePermissionsMsg,
					'title': ccmi18n_sitemap.setPagePermissions
					});
				} else {
					jQuery.fn.dialog.closeTop();
					ConcreteAlert.notify({
					'message': ccmi18n_sitemap.setPermissionsDeferredMsg,
					'title': ccmi18n_sitemap.setPagePermissions
					});
				}
	
			}		
		});

		inheritanceVal = $('#ccm-page-permissions-inherit').val();
		$('#ccm-page-permissions-inherit').change(function() {
			if ($(this).val() == '-1') { 
				ccm_pagePermissionsCancelInheritance();
			} else { 
				$('#dialog-buttons-start').addClass('dialog-buttons');
				jQuery.fn.dialog.open({
					element: '#ccm-page-permissions-confirm-dialog',
					title: '<?=t("Confirm Change")?>',
					width: 280,
					height: 100,
					onClose: function() {
						ccm_pagePermissionsCancelInheritance();
					}
				});
			}
		});

		$('#ccm-page-permissions-subpages-override-template-permissions').change(function() {
			jQuery.fn.dialog.showLoader();
			$.getJSON('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("change_subpage_defaults_inheritance")?>&inherit=' + $(this).val(), function(r) { 
				if (r.deferred) {
					jQuery.fn.dialog.closeTop();
					jQuery.fn.dialog.hideLoader();
					ConcreteAlert.notify({
					'message': ccmi18n_sitemap.setPermissionsDeferredMsg,
					'title': ccmi18n_sitemap.setPagePermissions
					});
				} else {
					ccm_refreshPagePermissions();
				}
			});
		});
		
		
	});
	
	ccm_refreshPagePermissions = function() {
		jQuery.fn.dialog.showLoader();
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/permissions?foo=1<?=$cIDStr?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});	
	}
	
	</script>
	
	
<?php 
    }

    ?>
</div>

<?php 
} ?>
