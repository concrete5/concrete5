<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Controller\Dialog\Page\Bulk\Permissions;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Category;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\View\View;

/** @var array $permissionKeyList */
/** @var string $task */
/** @var Category $category */
/** @var array $pageIds */
/** @var Permissions $controller */
/** @var Page[] $pages */
/** @var bool $isPermissionsInheritOverride */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

?>

<form method="post" id="ccm-permission-list-form"
      action="<?php echo h($category->getTaskURL('save_permission_assignments', ['cID' => $pageIds])) ?>"
      data-dialog-form="pages-permissions"
      data-task="<?php echo t($task); ?>">

    <?php if ($task === 'add_access') { ?>
        <?php if ($isPermissionsInheritOverride) { ?>

        <div class="form-group">
            <?php echo $form->label("ccm-permission-key-selector", t("Permission Key")); ?>

            <div class="input-group">
                <?php echo $form->select("ccm-permission-key-selector", $permissionKeyList); ?>

                <a href="javascript:void(0);" class="btn btn-secondary" id="ccm-add-access-entity">
                    <?php echo t('Add Access Entity') ?>
                </a>
            </div>
        </div>

        <script id="ccm-access-entity-apply-method-template" type="text/template">
            <p>
                <?php echo t("Please select if the new access entity should replace or append to the selected page permission key."); ?>
            </p>

            <div class="form-check">
                <?php echo $form->radio("ccm-access-entity-append", "append", true, ["id" => "ccm-access-entity-append", "name" => "ccm-access-entity-apply-method"]); ?>
                <?php echo $form->label("ccm-access-entity-append", t("Append To Existing Permissions"), ["class" => "form-check-label"]); ?>
            </div>

            <div class="form-check">
                <?php echo $form->radio("ccm-access-entity-replace", "replace", ["id" => "ccm-access-entity-replace", "name" => "ccm-access-entity-apply-method"]); ?>
                <?php echo $form->label("ccm-access-entity-replace", t("Replace Permissions"), ["class" => "form-check-label"]); ?>
            </div>
        </script>

    <?php } else { ?>
        <p>
            <?php echo t("You may only add access to these selected pages if they have all been set to override parent or page defaults permissions."); ?>
        </p>
    <?php } ?>
    <?php } else if ($task === 'remove_access') { ?>
    <?php if ($isPermissionsInheritOverride) { ?>
        <div class="form-group">
            <?php echo $form->label("ccm-permission-key-selector", t("Permission Key")); ?>
            <?php echo $form->select("ccm-permission-key-selector", $permissionKeyList); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("", t("Access Entities")); ?>

            <div id="ccm-selected-access-entities">
                <?php echo t("Loading..."); ?>
            </div>
        </div>

        <script id="ccm-access-entity-list-template" type="text/template">
            <% if (typeof accessEntityItems === "undefined" || accessEntityItems.length === 0) { %>
            <p>
                <?php echo t("This Permission key has no access entities."); ?>
            </p>
            <% } else { %>
            <table class="ccm-permission-grid table table-striped">
                <tbody>
                <% _.each(accessEntityItems, function (accessEntityItem) {%>
                <tr>
                    <td class="ccm-permission-grid-name">
                        <div class="ccm-permission-access-line">
                            <span class="badge bg-secondary">
                                <%=accessEntityItem.label%>
                            </span>
                        </div>
                    </td>

                    <td>
                        <a class="btn btn-danger btn-sm float-end ccm-remove-access-entity" href="javascript:void(0);"
                           data-access-entity-id="<%=accessEntityItem.accessEntityId%>"
                           data-access-id="<%=accessEntityItem.accessId%>">
                            <i class="fa fa-trash"></i> <?php echo t("Remove Access Entity"); ?>
                        </a>
                    </td>
                </tr>
                <% }); %>
                </tbody>
            </table>
            <% }%>
        </script>
    <?php } else { ?>
        <p>
            <?php echo t("You may only remove access to these selected pages if they have all been set to override parent or page defaults permissions."); ?>
        </p>
    <?php } ?>
    <?php } else { ?>
        <div class="ccm-pane-options">
            <div class="form-group">
                <?php echo $form->label('ccm-page-permissions-inherit', t('Assign Permissions')); ?>

                <?php echo $form->select("ccm-page-permissions-inherit", [
                    "" => t("*** Please Select"),
                    "PARENT" => t('By Area of Site (Hierarchy)'),
                    "TEMPLATE" => t('From Page Type Defaults'),
                    "OVERRIDE" => t('Manually')
                ])
                ?>
            </div>

            <div class="form-group">
                <?php echo $form->label('ccm-subpage-defaults-inheritance', t('Subpage Permissions')); ?>

                <?php echo $form->select('ccm-subpage-defaults-inheritance', [
                    "" => t("*** Please Select"),
                    '0' => t('Inherit page type default permissions.'),
                    '1' => t('Inherit the permissions of this page.')
                ]); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("", t("Current Permission Set")); ?>

            <?php /** @noinspection PhpUnhandledExceptionInspection */
            View::element('permission/lists/page', [
                'pages' => $pages,
                'editPermissions' => $isPermissionsInheritOverride
            ]) ?>
        </div>
    <?php } ?>

    <script>
        (function ($) {
            $(function () {
                (function (config) {
                    $("[data-task='add_access'] #ccm-add-access-entity").click(function () {
                        window.ccm_addAccessEntity = function (permissionAccessEntityId, permissionDurationId, permissionAccessType) {
                            $.fn.dialog.closeTop();

                            ConcreteAlert.confirm(
                                $("#ccm-access-entity-apply-method-template").html(),
                                function () {
                                    var method = $("input[name='ccm-access-entity-apply-method']:checked").val();

                                    $.fn.dialog.closeTop();

                                    new ConcreteAjaxRequest({
                                        url: config.addAccessEntityUrl,
                                        method: 'POST',
                                        data: {
                                            cID: config.pageIds,
                                            pkID: $("#ccm-permission-key-selector").val(),
                                            peID: permissionAccessEntityId,
                                            pdID: permissionDurationId,
                                            accessType: permissionAccessType,
                                            replace: method === 'replace' ? 1 : 0
                                        },
                                        success: function () {
                                            $.fn.dialog.closeTop();

                                            ConcreteAlert.notify({
                                                title: ccmi18n.permissionsUpdatedTitle,
                                                message: ccmi18n.permissionsUpdatedMessage
                                            });
                                        }
                                    });
                                }
                            )
                        };

                        jQuery.fn.dialog.open({
                            title: ccmi18n.addAccessEntityDialogTitle,
                            href: CCM_DISPATCHER_FILENAME + '/ccm/system/permissions/access/entity?' + $.param({
                                pkCategoryHandle: 'page',
                                accessType: config.accessType
                            }),
                            modal: false,
                            width: 500,
                            height: 380
                        });
                    });

                    $("[data-task='remove_access'] #ccm-permission-key-selector").change(function () {
                        new ConcreteAjaxRequest({
                            url: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/bulk/permissions/get_all_access_entities',
                            method: 'POST',
                            data: {
                                item: config.pageIds,
                                permissionKeyId: $("#ccm-permission-key-selector").val()
                            },
                            success: function (response) {
                                $("#ccm-selected-access-entities").html(_.template($('#ccm-access-entity-list-template').html())(response));
                                $(".ccm-remove-access-entity").click(function () {
                                    var accessEntityId = $(this).data("accessEntityId");
                                    var accessId = $(this).data("accessId");

                                    ConcreteAlert.confirm(ccmi18n.areYouSure, function () {
                                        $.fn.dialog.closeTop();

                                        new ConcreteAjaxRequest({
                                            url: config.removeAccessEntityUrl,
                                            method: 'POST',
                                            data: {
                                                cID: config.pageIds,
                                                pkID: $("#ccm-permission-key-selector").val(),
                                                peID: accessEntityId,
                                                paID: accessId
                                            },
                                            success: function () {
                                                $.fn.dialog.closeTop();

                                                ConcreteAlert.notify({
                                                    title: ccmi18n.permissionsUpdatedTitle,
                                                    message: ccmi18n.permissionsUpdatedMessage
                                                });

                                                $("#ccm-permission-key-selector").trigger("change"); // refresh the list
                                            }
                                        });
                                    })
                                });
                            }
                        });
                    }).trigger("change");

                    $('#ccm-page-permissions-inherit').change(function () {
                        ConcreteAlert.confirm(ccmi18n.permissionsOverrideWarning, function () {
                            new ConcreteAjaxRequest({
                                url: config.changePermissionInheritanceUrl,
                                method: 'POST',
                                data: {
                                    cID: config.pageIds,
                                    mode: $('#ccm-page-permissions-inherit').val(),
                                },
                                success: function (response) {
                                    jQuery.fn.dialog.closeAll();
                                    jQuery.fn.dialog.hideLoader();

                                    if (response.deferred) {
                                        ConcreteAlert.notify({
                                            message: ccmi18n.setPermissionsDeferredMsg,
                                            title: ccmi18n.setPagePermissions
                                        });
                                    } else {
                                        ConcreteAlert.notify({
                                            title: ccmi18n.permissionsUpdatedTitle,
                                            message: ccmi18n.permissionsUpdatedMessage
                                        });
                                    }
                                }
                            })
                        });
                    });

                    $('#ccm-subpage-defaults-inheritance').change(function () {
                        ConcreteAlert.confirm(ccmi18n.permissionsOverrideWarning, function () {
                            new ConcreteAjaxRequest({
                                url: config.changeSubpageDefaultsInheritanceUrl,
                                method: 'POST',
                                data: {
                                    cID: config.pageIds,
                                    inherit: $('#ccm-subpage-defaults-inheritance').val(),
                                },
                                success: function (response) {
                                    jQuery.fn.dialog.closeAll();
                                    jQuery.fn.dialog.hideLoader();

                                    if (response.deferred) {
                                        ConcreteAlert.notify({
                                            message: ccmi18n.setPermissionsDeferredMsg,
                                            title: ccmi18n.setPagePermissions
                                        });
                                    } else {
                                        ConcreteAlert.notify({
                                            title: ccmi18n.permissionsUpdatedTitle,
                                            message: ccmi18n.permissionsUpdatedMessage
                                        });
                                    }
                                }
                            })
                        });
                    });
                })(<?php /** @noinspection PhpComposerExtensionStubsInspection */echo json_encode([
                    "pageIds" => $pageIds,
                    "addAccessEntityUrl" => $category->getTaskURL("add_access_entity"),
                    "removeAccessEntityUrl" => $category->getTaskURL("remove_access_entity"),
                    "changePermissionInheritanceUrl" => $category->getTaskURL("change_permission_inheritance"),
                    "changeSubpageDefaultsInheritanceUrl" => $category->getTaskURL("change_subpage_defaults_inheritance"),
                    "accessType" => Key::ACCESS_TYPE_INCLUDE
                ]); ?>);
            });
        })(jQuery);
    </script>

    <div class="dialog-buttons">
        <button type="button" data-dialog-action="cancel" class="btn btn-secondary">
            <?php echo t('Close') ?>
        </button>

        <button type="button" data-dialog-action="submit"
                class="btn btn-success <?php echo $isPermissionsInheritOverride && $task == '' ? "" : "d-none"; ?>">
            <?php echo t('Save Changes') ?>
        </button>
    </div>
</form>

