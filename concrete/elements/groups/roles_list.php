<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Group\GroupRole;
use Concrete\Core\Utility\Service\Identifier;

/** @var GroupRole[] $roles */
/** @var GroupRole $defaultRole */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Request $request */
$request = $app->make(Request::class);
/** @var Identifier $idHelper */
$idHelper = $app->make(Identifier::class);

if ($request->request->has("roles")) {
    $roles = [];

    foreach($request->request->get("roles") as $role) {
        $role["manager"] = isset($role["manager"]);
        $roles[] = $role;
    }
}

if ($request->request->has("defaultRole")) {
    $defaultRole = ["id" => $request->request->get("defaultRole")];
}

$id = "ccm-role-list-" . $idHelper->getString();
?>

<div id="<?php echo $id; ?>" class="ccm-role-list">
    <a href="javascript:void(0);" class="btn btn-primary ccm-add-role">
        <?php echo t("Add Role"); ?>
    </a>

    <div class="clearfix"></div>

    <div class="ccm-no-rows-available d-none">
        <?php echo t("No roles available yet. Click on the Add Role button above to create one."); ?>
    </div>

    <table class="table table-striped d-none">
        <thead>
        <tr>
            <th>
                <?php echo t("Name"); ?>
            </th>

            <th>
                <?php echo t("Manager"); ?>
            </th>

            <th>
                &nbsp;
            </th>
        </tr>
        </thead>

        <tbody>

        </tbody>
    </table>

    <div class="clearfix"></div>

    <div class="form-group ccm-default-role-selector d-none">
        <?php echo $form->label("defaultRole", t("Default Role")); ?>
        <?php echo $form->select("defaultRole", ['' => t("*** Please select")]); ?>
    </div>
</div>

<script id="ccm-roles-row" type="text/template">
    <tr id="ccm-row-<%=id%>">
        <input type="hidden" name="roles[<%=id%>][id]" value="<%=id%>" />

        <td>
            <input type="text" name="roles[<%=id%>][name]" value="<%=name%>" class="form-control ccm-role-name"
                   placeholder="<?php echo t("Please enter a role name..."); ?>"/>
        </td>

        <td>
            <% if (manager) { %>
            <input type="checkbox" name="roles[<%=id%>][manager]" value="1" checked class="ccm-role-checkbox"/>
            <% } else { %>
            <input type="checkbox" name="roles[<%=id%>][manager]" value="1" class="ccm-role-checkbox"/>
            <% } %>
        </td>

        <td>
            <div class="float-end">
                <a href="javascript:void(0);" class="btn btn-danger ccm-remove-role">
                    <?php echo t("Remove Role"); ?>
                </a>
            </div>
        </td>
    </tr>
</script>

<style type="text/css">
    .ccm-add-role {
        margin-bottom: 15px;
    }

    .ccm-role-checkbox {
        margin-top: 15px;
    }
</style>

<script>
    (function ($) {
        $(function () {
            var $rolesContainer = $("#<?php echo $id; ?>");
            var availableRoles = <?php /** @noinspection PhpComposerExtensionStubsInspection */echo json_encode($roles); ?>;
            var defaultRole = <?php /** @noinspection PhpComposerExtensionStubsInspection */echo json_encode($defaultRole); ?>;
            var lastInsertId = 0;

            var initRoleList = function () {

                var resetRoleList = function () {
                    $rolesContainer.find("tbody").html("");
                    $rolesContainer.find("select[name=defaultRole] option").each(function () {
                        if ($(this).val() != "") {
                            $(this).remove();
                        }
                    });
                };

                var ajaxRefreshRoleList = function (groupTypeId) {
                    $.ajax({
                        url: CCM_DISPATCHER_FILENAME + "/dashboard/users/group_types/get_group_type/" + groupTypeId,
                        dataType: "json",
                        method: "GET",
                        success: function (json) {
                            availableRoles = json.roles;
                            defaultRole = json.defaultRole;
                            lastInsertId = 0;

                            resetRoleList();
                            initRoleList();
                        }
                    });
                };

                var updateNoRowsMessage = function () {
                    if ($rolesContainer.find("tbody tr").length) {
                        $rolesContainer.find(".ccm-no-rows-available").addClass("d-none");
                        $rolesContainer.find(".ccm-default-role-selector").removeClass("d-none");
                        $rolesContainer.find("table").removeClass("d-none");
                    } else {
                        $rolesContainer.find(".ccm-no-rows-available").removeClass("d-none");
                        $rolesContainer.find(".ccm-default-role-selector").addClass("d-none");
                        $rolesContainer.find("table").addClass("d-none");
                    }
                };

                var addRole = function (role) {
                    $rolesContainer.find("tbody").append(_.template($("#ccm-roles-row").html())(role));
                    $rolesContainer.find("select[name=defaultRole]").append($("<option/>").attr("value", role.id).html(role.name));

                    var $row = $("#ccm-row-" + role.id);

                    $row.find(".ccm-role-name").change(function () {
                        var $option = $rolesContainer.find("select[name=defaultRole] option[value=" + role.id + "]");
                        $option.html($(this).val());
                    });

                    $row.find(".ccm-remove-role").click(function () {
                        var $option = $rolesContainer.find("select[name=defaultRole] option[value=" + role.id + "]");

                        if ($option.is(":checked")) {
                            $rolesContainer.find("select[name=defaultRole]").val("");
                        }

                        $option.remove();
                        $row.remove();

                        updateNoRowsMessage();
                    });

                    updateNoRowsMessage();
                };

                updateNoRowsMessage();

                for (var role of availableRoles) {
                    addRole(role);
                }

                $rolesContainer.find(".ccm-add-role").click(function () {
                    addRole({
                        id: '_' + new Date().getTime() + lastInsertId,
                        name: '',
                        manager: ''
                    });

                    lastInsertId++;
                });

                if (typeof defaultRole === "object" && defaultRole !== null) {
                    $rolesContainer.find("select[name=defaultRole]").val(defaultRole.id);
                }

                $rolesContainer.data("ajaxRefreshRoleList", ajaxRefreshRoleList);
            };

            initRoleList();
        });
    })(jQuery);
</script>
