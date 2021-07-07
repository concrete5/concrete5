<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Service\Dashboard $dashboard
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Controller\SinglePage\Dashboard\System\Registration\Notification $controller
 * @var Concrete\Core\Permission\Key\NotifyInNotificationCenterNotificationKey $key
 * @var Concrete\Core\Permission\Access\NotifyInNotificationCenterNotificationAccess $permissionAccess
 */

?>
<form id="ccm-permissions-detail-form" onsubmit="return ccm_submitPermissionsDetailForm()" method="POST" action="<?= h($key->getPermissionAssignmentObject()->getPermissionKeyTaskURL()) ?>">
    <input type="hidden" name="paID" value="<?= $permissionAccess->getPermissionAccessID() ?>" />

    <div id="ccm-tab-content-access-types">
        <?php View::element('permission/keys/notify_in_notification_center', ['permissionAccess' => $permissionAccess]) ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper d-none">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary" type="submit" ><?= t('Save') ?></button>
        </div>
    </div>

</form>

<script>
var ccm_permissionDialogURL = CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/permissions/miscellaneous';
ccm_deleteAccessEntityAssignment = function(peID) {
    jQuery.fn.dialog.showLoader();

    if (ccm_permissionDialogURL.indexOf('?') > 0) {
        var qs = '&';
    } else {
        var qs = '?';
    }

    $.get(<?= json_encode($key->getPermissionAssignmentObject()->getPermissionKeyTaskURL('remove_access_entity', ['paID' => $permissionAccess->getPermissionAccessID()])) ?> + '&peID=' + peID, function() {
        $.get(ccm_permissionDialogURL + qs + 'paID=<?= $permissionAccess->getPermissionAccessID() ?>&message=entity_removed&pkID=<?= $key->getPermissionKeyID()?>', function(r) {
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

    $.get(<?= json_encode($key->getPermissionAssignmentObject()->getPermissionKeyTaskURL('add_access_entity', ['paID' => $permissionAccess->getPermissionAccessID()])) ?> + '&pdID=' + pdID + '&accessType=' + accessType + '&peID=' + peID, function(r) {
        $.get(ccm_permissionDialogURL + qs + 'paID=<?= $permissionAccess->getPermissionAccessID() ?>&message=entity_added&pkID=<?= $key->getPermissionKeyID() ?>', function(r) {
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
