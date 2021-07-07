<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Application;

/**
 * @var Concrete\Core\Permission\Key\Key $permissionKey
 */

$app = Application::getFacadeApplication();
$form = $app->make('helper/form');
$request = $app->make(Request::class);
$paID = $request->get('paID');

if ($paID > 0) {
    $pa = PermissionAccess::getByID($paID, $permissionKey);
    if ($pa->isPermissionAccessInUse() || $request->get('duplicate') == '1') {
        $pa = $pa->duplicate();
    }
} else {
    $pa = PermissionAccess::create($permissionKey);
}

?>

<div class="ccm-ui" id="ccm-permission-detail">
    <form id="ccm-permissions-detail-form" onsubmit="return ccm_submitPermissionsDetailForm()" method="post"
          action="<?= h($permissionKey->getPermissionAssignmentObject()->getPermissionKeyTaskURL()) ?>">

        <input type="hidden" name="paID" value="<?= $pa->getPermissionAccessID() ?>"/>

        <?php
            $workflows = Workflow::getList();

            View::element('permission/message_list');

            $tabs = [];
            if ($permissionKey->hasCustomOptionsForm() || ($permissionKey->canPermissionKeyTriggerWorkflow() && count($workflows) > 0)) {
                $tabs[] = ['ccm-tab-content-access-types', t('Access'), true];
                if ($permissionKey->canPermissionKeyTriggerWorkflow() && count($workflows) > 0) {
                    $tabs[] = ['ccm-tab-content-workflow', t('Workflow')];
                }

                if ($permissionKey->hasCustomOptionsForm()) {
                    $tabs[] = ['ccm-tab-content-custom-options', t('Details')];
                }

                echo $app->make('helper/concrete/ui')->tabs($tabs);
            }
        ?>

        <?php if ($permissionKey->getPermissionKeyDisplayDescription()) { ?>
            <div class="dialog-help">
                <?= $permissionKey->getPermissionKeyDisplayDescription() ?>
            </div>
        <?php } ?>

        <div class="tab-content">
            <div id="ccm-tab-content-access-types" <?php if (count($tabs) > 0) { ?>class="tab-pane active"<?php } ?>>
                <?php
                    $pkCategoryHandle = $permissionKey->getPermissionKeyCategoryHandle();
                    $accessTypes = $permissionKey->getSupportedAccessTypes();
                    View::element('permission/access/list', ['pkCategoryHandle' => $pkCategoryHandle, 'permissionAccess' => $pa, 'accessTypes' => $accessTypes]);
                ?>
            </div>

            <?php if ($permissionKey->hasCustomOptionsForm()) { ?>
                <div id="ccm-tab-content-custom-options" class="tab-pane">
                    <?php
                        View::element(
                            "permission/keys/{$permissionKey->getPermissionKeyHandle()}",
                            ['permissionAccess' => $pa],
                            ($permissionKey->getPackageID() > 0) ? $permissionKey->getPackageHandle() : null
                        )
                    ?>
                </div>
                <?php
            }

            if ($permissionKey->canPermissionKeyTriggerWorkflow() && count($workflows) > 0) {
                $selectedWorkflows = $pa->getWorkflows();
                $workflowIDs = [];
                foreach ($selectedWorkflows as $swf) {
                    $workflowIDs[] = $swf->getWorkflowID();
                }
            ?>

                <div id="ccm-tab-content-workflow" class="tab-pane">
                    <div class="form-group">
                        <label class="col-form-label"><?= t('Attach Workflow to this Permission') ?></label>
                        <?php
                            foreach ($workflows as $wf) {
                                $checkboxAttributes = [];
                                if (count($wf->getRestrictedToPermissionKeyHandles()) > 0 && (!in_array($permissionKey->getPermissionKeyHandle(), $wf->getRestrictedToPermissionKeyHandles()))) {
                                    $checkboxAttributes['disabled'] = 'disabled';
                                }
                            ?>
                            <div class="form-check">
                                <?= $form->checkbox('wfID[]', $wf->getWorkflowID(), in_array($wf->getWorkflowID(), $workflowIDs), $checkboxAttributes) ?>
                                <?= $form->label("wfID_{$wf->getWorkflowID()}", $wf->getWorkflowDisplayName(), ['class' => 'form-check-label']) ?>
                            </div>
                            <?php
                            }
                        ?>
                    </div>
                </div>
                <?php
            } ?>
        </div>

        <div class="dialog-buttons">
            <button href="javascript:void(0)" class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
            <button type="submit" class="btn btn-primary float-end" onclick="$('#ccm-permissions-detail-form').submit()"><?= t('Save') ?></button>
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

    $(function () {

        ccm_addAccessEntity = function (peID, pdID, accessType) {
            jQuery.fn.dialog.closeTop();
            jQuery.fn.dialog.showLoader();

            var qs = (ccm_permissionDialogURL.indexOf('?') > 0) ? '&' : '?';

            $.get(<?= json_encode($permissionKey->getPermissionAssignmentObject()->getPermissionKeyTaskURL('add_access_entity', ['paID' => $pa->getPermissionAccessID()])) ?> + '&pdID=' + pdID + '&accessType=' + accessType + '&peID=' + peID, function (r) {
                $.get(ccm_permissionDialogURL + qs + 'paID=<?=$pa->getPermissionAccessID()?>&message=entity_added&pkID=<?=$permissionKey->getPermissionKeyID()?>', function (r) {
                    jQuery.fn.dialog.replaceTop(r);
                    jQuery.fn.dialog.hideLoader();
                });
            });
        }

        ccm_deleteAccessEntityAssignment = function (peID) {
            jQuery.fn.dialog.showLoader();

            var qs = (ccm_permissionDialogURL.indexOf('?') > 0) ? '&' : '?';

            $.get(<?= json_encode($permissionKey->getPermissionAssignmentObject()->getPermissionKeyTaskURL('remove_access_entity', ['paID' => $pa->getPermissionAccessID()])) ?> + '&peID=' + peID, function () {
                $.get(ccm_permissionDialogURL + qs + 'paID=<?=$pa->getPermissionAccessID()?>&message=entity_removed&pkID=<?=$permissionKey->getPermissionKeyID()?>', function (r) {
                    jQuery.fn.dialog.replaceTop(r);
                    jQuery.fn.dialog.hideLoader();
                });
            });
        }

        ccm_submitPermissionsDetailForm = function () {
            jQuery.fn.dialog.showLoader();
            $("#ccm-permissions-detail-form").ajaxSubmit(function (r) {
                jQuery.fn.dialog.hideLoader();
                jQuery.fn.dialog.closeTop();
                // now we reload the permission key to use the new permission assignment
                var gc = $('#ccm-permission-grid-cell-<?=$permissionKey->getPermissionKeyID()?>');
                if (gc.length > 0) {
                    gc.load(<?= json_encode($permissionKey->getPermissionAssignmentObject()->getPermissionKeyTaskURL('display_access_cell', ['paID' => $pa->getPermissionAccessID()])) ?>, function () {
                        $('#ccm-permission-grid-name-<?=$permissionKey->getPermissionKeyID()?> a').attr('data-paID', '<?=$pa->getPermissionAccessID()?>');
                        if (typeof (ccm_submitPermissionsDetailFormPost) != 'undefined') {
                            ccm_submitPermissionsDetailFormPost();
                        }
                    });
                } else {
                    if (typeof (ccm_submitPermissionsDetailFormPost) != 'undefined') {
                        ccm_submitPermissionsDetailFormPost();
                    }
                }
            });
            return false;
        }

        <?php
            if ($request->get('message') == 'custom_options_saved') {
        ?>
        $('a[data-tab=custom-options]').click();
        <?php
            }

            if ($request->get('message') == 'workflows_saved') {
        ?>
        $('a[data-tab=workflow]').click();
        <?php
            }
        ?>
    });
</script>
