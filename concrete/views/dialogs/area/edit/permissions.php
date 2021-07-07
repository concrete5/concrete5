<?php

use Concrete\Core\Area\SubArea;

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\Page $c */
/* @var Concrete\Core\Permission\Checker $cp */

/* @var Concrete\Core\Area\Area $a */
/* @var Concrete\Core\Permission\Checker $ap */
/* @var Concrete\Core\Validation\CSRF\Token $token */

/* @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $resolverManager */
?>
<div class="ccm-ui">
    <?php
    $enablePermissions = false;
    if ($a instanceof SubArea && !$a->overrideCollectionPermissions()) {
        ?>
        <div class="alert alert-info">
            <p><?= t('The following area permissions are inherited from a parent area.') ?></p>
           <br/>
           <a href="javascript:void(0)" class="btn btn-sm btn-default" onclick="ccm_setAreaPermissionsToOverride()"><?= t('Override Permissions') ?></a>
        </div>
        <?php
    } elseif ($a->getAreaCollectionInheritID() != $c->getCollectionID() && $a->getAreaCollectionInheritID() > 0) {
        $areac = Page::getByID($a->getAreaCollectionInheritID());
        ?>
        <div class="alert alert-info">
            <p>
                <?php
                if ($areac->isMasterCollection()) {
                    $ptName = $areac->getPageTypeName();
                    echo t('The following area permissions are inherited from an area set in <strong>%s</strong> defaults.', h($ptName));
                } else {
                    echo t(
                        /*i18n: %s is the name of a page */
                        'The following area permissions are inherited from an area set on %s.',
                        '<a href="' . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $areac->getCollectionID() . '">' . h($areac->getCollectionName()) . '</a>'
                    );
                }
                ?>
            </p>
            <br/>
            <a href="javascript:void(0)" class="btn btn-sm btn-default" onclick="ccm_setAreaPermissionsToOverride()"><?= t('Override Permissions') ?></a>
        </div>
        <?php
    } elseif (!$a->overrideCollectionPermissions()) {
        ?>
        <div class="alert alert-info">
            <p><?= t("The following area permissions are inherited from the page's permissions.") ?></p>
           <br/>
           <a href="javascript:void(0)" class="btn btn-sm btn-default" onclick="ccm_setAreaPermissionsToOverride()"><?= t('Override Permissions') ?></a>
        </div>
       <?php
    } else {
        $enablePermissions = true;
        ?>
        <div class="alert alert-info">
            <p><?= t('Permissions for this area currently override those of the page.') ?></p>
            <br/>
           <a href="javascript:void(0)" class="btn btn-sm btn-default" onclick="ccm_revertToPagePermissions()"><?= t('Revert to Page Permissions') ?></a>
        </div>
        <?php
    }
    View::element('permission/help');
    $cat = PermissionKeyCategory::getByHandle('area');
    ?>
    <form method="post" id="ccm-permission-list-form" action="<?= h($cat->getTaskURL('save_permission_assignments', ['cID' => $c->getCollectionID(), 'arHandle' => $a->getAreaHandle()])) ?>">
        <table class="ccm-permission-grid table table-striped">
            <?php
            $permissions = PermissionKey::getList('area');
            foreach ($permissions as $pk) {
                $pk->setPermissionObject($a);
                ?>
                <tr>
                    <td class="ccm-permission-grid-name" id="ccm-permission-grid-name-<?= $pk->getPermissionKeyID() ?>">
                        <strong>
                            <?php
                            if ($enablePermissions) {
                                ?><a dialog-title="<?= $pk->getPermissionKeyDisplayName() ?>" data-pkID="<?= $pk->getPermissionKeyID() ?>" data-paID="<?= $pk->getPermissionAccessID() ?>" onclick="ccm_permissionLaunchDialog(this)" href="javascript:void(0)"><?php
                            }
                            echo $pk->getPermissionKeyDisplayName();
                            if ($enablePermissions) {
                                ?></a><?php
                            }
                            ?>
                        </strong>
                    </td>
                    <td id="ccm-permission-grid-cell-<?= $pk->getPermissionKeyID() ?>"<?= $enablePermissions ? ' class="ccm-permission-grid-cell"' : '' ?>>
                        <?php View::element('permission/labels', ['pk' => $pk]) ?>
                    </td>
                </tr>
                <?php
            }
            if ($enablePermissions) {
                ?>
                <tr>
                    <td class="ccm-permission-grid-name"></td>
                    <td>
                        <?php View::element('permission/clipboard', ['pkCategory' => $cat]) ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
    </form>
    <?php
    if ($enablePermissions) {
        ?>
        <div class="dialog-buttons">
            <a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="btn btn-default pull-left"><?= t('Cancel') ?></a>
            <button onclick="$('#ccm-permission-list-form').submit()" class="btn btn-primary float-end"><?= t('Save') ?> <i class="fa fa-ok-sign icon-white"></i></button>
        </div>
        <?php
    }
    ?>
</div>

<script>
window.ccm_permissionLaunchDialog = function(link) {
    var dupe = $(link).attr('data-duplicate');
    if (dupe != 1) {
        dupe = 0;
    }
    jQuery.fn.dialog.open({
        title: $(link).attr('dialog-title'),
        href: <?= json_encode((string) $resolverManager->resolve(['/ccm/system/dialogs/area/edit/advanced_permissions?arHandle=' . urlencode($a->getAreaHandle()) . '&cID=' . $c->getCollectionID()])) ?> + '&duplicate=' + dupe + '&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
        modal: true,
        width: 500,
        height: 380
    });
};
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
window.ccm_revertToPagePermissions = function() {
    jQuery.fn.dialog.showLoader();
    $.get(
        <?= json_encode($pk->getPermissionAssignmentObject()->getPermissionKeyTaskURL('revert_to_page_permissions', ['arHandle' => $a->getAreaHandle(), 'cID' => $c->getCollectionID()])) ?>,
        function() {
            ccm_refreshAreaPermissions();
        }
    );
};
window.ccm_setAreaPermissionsToOverride = function() {
    jQuery.fn.dialog.showLoader();
    $.get(
        <?= json_encode($pk->getPermissionAssignmentObject()->getPermissionKeyTaskURL('override_page_permissions', ['arHandle' => $a->getAreaHandle(), 'cID' => $c->getCollectionID()])) ?>,
        function() {
            ccm_refreshAreaPermissions();
        }
    );
};
window.ccm_refreshAreaPermissions = function() {
    jQuery.fn.dialog.showLoader();
    $.get(
        <?= json_encode((string) $resolverManager->resolve(['/ccm/system/dialogs/area/edit/permissions' . '?arHandle=' . urlencode($a->getAreaHandle()) . '&cID=' . $c->getCollectionID()])) ?>,
        function(r) {
            jQuery.fn.dialog.replaceTop(r);
            jQuery.fn.dialog.hideLoader();
        }
    );
};
</script>
