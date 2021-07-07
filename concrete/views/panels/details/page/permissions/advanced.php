<?php

use Concrete\Core\Page\Stack\Stack;

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\Page $c */
?>
<section class="ccm-ui">
    <header><h3><?=t('Page Permissions')?></h3></header>

    <?php
    $stack = null;
    if ($c->getPageTypeHandle() === STACKS_PAGE_TYPE) {
        $stack = $c instanceof Stack ? $c : Stack::getByID($c->getCollectionID());
    }
    $cpc = $c->getPermissionsCollectionObject();
    if ($c->getCollectionInheritance() == 'PARENT') {
        if ($c->isPageDraft()) {
            ?>
            <div class="alert alert-info">
                <?php
                if ($stack) {
                    if ($stack->getStackType() === Stack::ST_TYPE_GLOBAL_AREA) {
                        echo t('This global area inherits its permissions from the drafts area, as well as its edit page type drafts permission.');
                    } else {
                        echo t('This stack inherits its permissions from the drafts area, as well as its edit page type drafts permission.');
                    }
                } else {
                    echo t('This page inherits its permissions from the drafts area, as well as its edit page type drafts permission.');
                }
                ?>
            </div>
            <?php
        } else {
            ?>
            <div class="alert alert-info">
                <?php
                if ($stack) {
                    if ($stack->getStackType() === Stack::ST_TYPE_GLOBAL_AREA) {
                        echo t('This global area inherits its permissions from:');
                    } else {
                        echo t('This stack inherits its permissions from:');
                    }
                } else {
                    echo t('This page inherits its permissions from:');
                }
                ?>
                <a target="_blank" href="<?=URL::to($cpc)?>"><?=$cpc->getCollectionName()?></a>
            </div>
            <?php
        }
    }
    ?>
    <div>
        <div class="form-group">
            <label class="col-form-label" for="ccm-page-permissions-inherit"><?=t('Assign Permissions')?></label>
            <select id="ccm-page-permissions-inherit" class="form-select">
                <?php
                if ($c->getCollectionID() > 1) {
                    ?>
                    <option value="PARENT"<?= $c->getCollectionInheritance() === 'PARENT' ? ' selected="selected"' : ''?>><?=t('By Area of Site (Hierarchy)')?></option>
                    <?php
                }
                if ($c->getMasterCollectionID() > 1) {
                    ?>
                    <option value="TEMPLATE"<?= $c->getCollectionInheritance() === 'TEMPLATE' ? ' selected="selected"' : ''?>><?=t('From Page Type Defaults')?></option>
                    <?php
                }
                ?>
                <option value="OVERRIDE"<?= $c->getCollectionInheritance() === 'OVERRIDE' ? ' selected="selected"' : ''?>><?=t('Manually')?></option>
            </select>
        </div>
        <?php
        if (!$c->isMasterCollection()) {
            ?>
            <div class="form-group">
                <label class="col-form-label" for="ccm-page-permissions-subpages-override-template-permissions"><?=t('Subpage Permissions')?></label>
                <select id="ccm-page-permissions-subpages-override-template-permissions" class="form-select">
                    <option value="0"<?php if (!$c->overrideTemplatePermissions()) {
                    ?>selected<?php

                    }
                    ?>><?=t('Inherit page type default permissions.')?></option>
                    <option value="1"<?php if ($c->overrideTemplatePermissions()) {
                    ?>selected<?php

                    }
                    ?>><?=t('Inherit the permissions of this page.')?></option>
                </select>
            </div>
            <?php

        } ?>
    </div>

    <div class="form-group">
        <label class="col-form-label"><?=t('Current Permission Set')?></label>

        <?php $cat = PermissionKeyCategory::getByHandle('page');?>
        <form method="post" id="ccm-permission-list-form" data-dialog-form="permissions" data-panel-detail-form="permissions" action="<?= h($cat->getTaskURL('save_permission_assignments', ['cID' => $c->getCollectionID()])) ?>">
            <?php Loader::element('permission/lists/page', array(
                'page' => $c, 'editPermissions' => $editPermissions,
            ))?>
        </form>
    </div>
</section>

<div id="ccm-page-permissions-confirm-dialog" style="display: none">
    <?=t('Changing this setting will affect this page immediately. Are you sure?')?>
    <div id="dialog-buttons-start">
        <input type="button" class="btn btn-secondary me-2" value="Cancel" onclick="jQuery.fn.dialog.closeTop()" />
        <input type="button" class="btn btn-primary" value="Ok" onclick="ccm_pagePermissionsConfirmInheritanceChange()" />
    </div>
</div>

<?php if ($editPermissions) {
    ?>
    <div class="ccm-panel-detail-form-actions dialog-buttons d-flex justify-content-end">
        <button class="btn btn-secondary me-2" type="button" data-dialog-action="cancel" data-panel-detail-action="cancel"><?=t('Cancel')?></button>
        <button class="btn btn-success" type="button" data-dialog-action="submit" data-panel-detail-action="submit"><?=t('Save Changes')?></button>
    </div>
    <?php

} ?>


<script type="text/javascript">
    var inheritanceVal = '';

    ccm_pagePermissionsCancelInheritance = function() {
        $('#ccm-page-permissions-inherit').val(inheritanceVal);
    }

    ccm_pagePermissionsConfirmInheritanceChange = function() {
        jQuery.fn.dialog.showLoader();
        $.getJSON(<?= json_encode($cat->getTaskURL('change_permission_inheritance', ['cID' => $c->getCollectionID()])) ?> + '&mode=' + $('#ccm-page-permissions-inherit').val(), function(r) {
            if (r.deferred) {
                jQuery.fn.dialog.closeAll();
                jQuery.fn.dialog.hideLoader();
                ConcreteAlert.notify({
                    'message': ccmi18n.setPermissionsDeferredMsg,
                    'title': ccmi18n.setPagePermissions
                });
            } else {
                jQuery.fn.dialog.closeAll();
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
                        'message': ccmi18n.setPermissionsMsg,
                        'title': ccmi18n.setPagePermissions
                    });
                } else {
                    jQuery.fn.dialog.closeTop();
                    ConcreteAlert.notify({
                        'message': ccmi18n.setPermissionsDeferredMsg,
                        'title': ccmi18n.setPagePermissions
                    });
                }

            }
        });

        inheritanceVal = $('#ccm-page-permissions-inherit').val();
        $('#ccm-page-permissions-inherit').change(function() {
            $('#dialog-buttons-start').addClass('dialog-buttons');
            jQuery.fn.dialog.open({
                element: '#ccm-page-permissions-confirm-dialog',
                title: '<?=t('Confirm Change')?>',
                width: 280,
                height: 160,
                onClose: function() {
                    ccm_pagePermissionsCancelInheritance();
                }
            });
        });

        $('#ccm-page-permissions-subpages-override-template-permissions').change(function() {
            jQuery.fn.dialog.showLoader();
            $.getJSON(<?= json_encode($cat->getTaskURL('change_subpage_defaults_inheritance', ['cID' => $c->getCollectionID()])) ?> + '&inherit=' + $(this).val(), function(r) {
                if (r.deferred) {
                    ConcretePanelManager.exitPanelMode();
                    jQuery.fn.dialog.hideLoader();
                    ConcreteAlert.notify({
                        'message': ccmi18n.setPermissionsDeferredMsg,
                        'title': ccmi18n.setPagePermissions
                    });
                } else {
                    ccm_refreshPagePermissions();
                }
            });
        });

    });

    ccm_refreshPagePermissions = function() {
        var panel = ConcretePanelManager.getByIdentifier('page');
        if (panel) {
            panel.openPanelDetail({
                'identifier': 'page-permissions',
                'url': '<?=URL::to('/ccm/system/panels/details/page/permissions')?>',
                target: null
            });
        } else {
            jQuery.fn.dialog.showLoader();
            jQuery.fn.dialog.open({
                title: '<?=t("Permissions") ?>',
                href: '<?=URL::to('/ccm/system/panels/details/page/permissions?cID='.$c->getCollectionID())?>',
                modal: true,
                width: 500,
                height: 600,
            });

        }
    }

</script>
