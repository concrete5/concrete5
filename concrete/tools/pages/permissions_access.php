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
$cIDs = array();
foreach ($pages as $c) {
    $cp = new Permissions($c);
    if ($cp->canEditPagePermissions()) {
        $cIDs[] = $c->getCollectionID();
        $cIDStr .= '&cID[]=' . $c->getCollectionID();
        ++$pcnt;
    }
}

foreach ($pages as $_c) {
    $permissionsInherit[] = $_c->getCollectionInheritance();
}
$permissionsInherit = array_unique($permissionsInherit);
if (count($permissionsInherit) == 1) {
    $permissionsInherit = $permissionsInherit[0];
}

if ($_REQUEST['task'] == 'get_all_access_entities' && $pcnt > 0 && $permissionsInherit == 'OVERRIDE') {
    $paIDs = array();
    foreach ($pages as $c) {
        $pk = PermissionKey::getByID($_REQUEST['pkID']);
        $pk->setPermissionObject($c);
        $pa = $pk->getPermissionAccessObject();
        if (is_object($pa)) {
            $listItems = $pa->getAccessListItems(PermissionKey::ACCESS_TYPE_ALL);
            foreach ($listItems as $as) {
                $entity = $as->getAccessEntityObject();
                $aepdID = $entity->getAccessEntityID() . $as->getAccessType();
                $pd = $as->getPermissionDurationObject();
                $pdID = 0;
                if (is_object($pd)) {
                    $aepdID .= $pd->getPermissionDurationID();
                    $pdID = $pd->getPermissionDurationID();
                }
                if (in_array($aepdID, $paIDs)) {
                    continue;
                }
                $paIDs[] = $aepdID;

                $pdTitle = '';
                if ($as->getAccessType() == PermissionKey::ACCESS_TYPE_EXCLUDE) {
                    if (is_object($pd)) {
                        $class = 'label-warning';
                        $pdTitle = 'title="' . $pd->getTextRepresentation() . '"';
                    } else {
                        $class = 'label-important';
                    }
                } else {
                    if (is_object($pd)) {
                        $class = 'label-info';
                        $pdTitle = 'title="' . $pd->getTextRepresentation() . '"';
                    }
                }
                echo '<label class="checkbox"><input type="checkbox" name="listItem[]" value="' . $entity->getAccessEntityID() . ':' . $as->getAccessType() . ':' . $pdID . '	" /> <span class="label ' . $class . '" ' . $pdTitle . '>' . $entity->getAccessEntityLabel() . '</span></label>';
            }
        }
    }
    exit;
}

$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);

if ($_REQUEST['task'] == 'remove') {
    $task = 'bulk_remove_access';
} else {
    $task = 'bulk_add_access';
}

?>
<div class="ccm-ui" id="ccm-permission-detail">

<?php if ($pcnt == 0) {
    ?>
	<?=t("You do not have permission to change permissions on any of the selected pages.");
    ?>
<?php 
} else {
    if ($permissionsInherit == 'OVERRIDE') {
        $cat = PermissionKeyCategory::getByHandle('page');
        ?>


		<form id="ccm-permissions-bulk-access-form" action="<?=$cat->getToolsURL($task)?>">


			<?php foreach ($cIDs as $cID) {
    ?>
				<input type="hidden" name="cID[]" value="<?=$cID?>" />
			<?php 
}
        ?>

			<?php if ($task == 'bulk_remove_access') {
    ?>
				<div class="alert alert-warning"><strong><?=t('Warning:')?></strong> <?=t("Any users or groups selected will be removed from the permissions on the selected pages.")?></div>
			<?php 
}
        ?>

			<div class="<?php if ($task == 'bulk_add_access') {
    ?>form-inline<?php 
}
        ?>">

			<table class="ccm-permission-grid table">
				<tr>
				<td class="ccm-permission-grid-name" id="ccm-permission-grid-name-0">
					<select name="pkID">
					<?php
                    $permissions = PermissionKey::getList('page');
        foreach ($permissions as $pk) {
            ?>
						<option value="<?=$pk->getPermissionKeyID()?>"><?=$pk->getPermissionKeyDisplayName()?></option>
					<?php 
        }
        ?>
					</select>
				</td>
				<td id="ccm-permission-grid-cell-0" class="ccm-permission-grid-cell-value" style="vertical-align: middle">
			<?php if ($task == 'bulk_remove_access') {
    ?>
				<div id="ccm-permissions-bulk-access-remove"></div>
			<?php 
} else {
    ?>
				<div class="ccm-permission-access-line"><button class="btn" type="button" id="ccm-bulk-access-form-add-entity"><?=t('Add Access Entity')?></button></div>
			<?php 
}
        ?>
			</td>
			</tr>
			</table>
		</div>

			<?php if ($task == 'bulk_add_access') {
    ?>
			<div class="form-horizontal">
			<div class="control-group">
				<label class="control-label"><?=t('Permissions Should')?></label>
				<div class="controls">
				<div class="radio"><label><input type="radio" name="paReplaceAll" value="add" checked="checked" /> <span><?=t("Add To Existing Permissions")?></span></label></div>
				<div class="radio"><label><input type="radio" name="paReplaceAll" value="replace" /> <span><?=t("Replace Permissions")?></span></label></div>
				</div>
			</div>
			</div>
			<?php 
}
        ?>

				<div id="ccm-permissions-bulk-access-form-buttons" class="dialog-buttons">
					<button class="btn" type="button" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
					<button class="btn primary pull-right" disabled="disabled" type="button" onclick="ccm_bulkPermissionsFormSubmit()"><?=t('Save')?></button>
				</div>
			

		</form>

		<script type="text/javascript">
		ccm_submitPermissionsDetailFormPost = function() {
			$('#ccm-permissions-bulk-access-form select').prop('disabled', true);
			$("#ccm-permissions-bulk-access-form-buttons button").prop('disabled', false);
		}

		ccm_bulkPermissionsFormSubmit = function() {
			$('#ccm-permissions-bulk-access-form').ajaxForm({
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

			}).submit();
		}

		$('#ccm-bulk-access-form-add-entity').on('click', function() {
			if ($('#ccm-permissions-bulk-access-form select').val() > 0) {
				jQuery.fn.dialog.open({
					title: '<?=t("Add Access Entity")?>',
					href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/permissions?subtask=set<?=$cIDStr?>&pkID=' + $('#ccm-permissions-bulk-access-form select').val(),
					modal: false,
					width: 500,
					height: 380
				});		
			}
		});


		<?php if ($task == 'bulk_remove_access') {
    ?>
			$('#ccm-permissions-bulk-access-form select').on('change', function() {
				jQuery.fn.dialog.showLoader();
				$('#ccm-permissions-bulk-access-remove').load('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/permissions_access?task=get_all_access_entities<?=$cIDStr?>&pkID=' + $(this).val(), function() {
					$("#ccm-permissions-bulk-access-form-buttons button").prop('disabled', false);
					jQuery.fn.dialog.hideLoader();
				});
			});

			$('#ccm-permissions-bulk-access-remove').on('hover', 'span.label', function() {
				$(this).tooltip();
			});

			$(function() {
				$('#ccm-permissions-bulk-access-form select').trigger('change');
			});

		<?php 
} else {
    ?>
			$('#ccm-permissions-bulk-access-form select').on('change', function() {
				$('.ccm-permission-grid-cell-value').attr('id', 'ccm-permission-grid-cell-' + $(this).val());
			}).trigger('change');
		<?php 
}
        ?>
		</script>


	<?php 
    } else {
        ?>
		<br/><br/>

		<p><?=t('You may only add access to these selected pages if they have all been set to override parent or page defaults permissions.')?></p>

	<?php 
    }
}
