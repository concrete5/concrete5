<?php /** @noinspection DuplicatedCode */
/** @noinspection PhpDeprecationInspection */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Search\Groups;
use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Application\Service\UserInterface;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupType;
use Concrete\Core\User\User;
use Concrete\Core\Utility\Service\Text;
use Concrete\Core\Validation\CSRF\Token;
use \Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\View\View;

/** @var Group $group */
/** @var View $view */
/** @var bool $canAddGroup */

$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);
/** @var UserInterface $ih */
$ih = $app->make(UserInterface::class);
/** @var Form $form */
$form = $app->make(Form::class);
/** @var DateTime $date */
$date = $app->make(DateTime::class);
/** @var FileManager $af */
$af = $app->make(FileManager::class);
/** @var Text $text */
$text = $app->make(Text::class);

?>

<form method="post" id="update-group-form" class="form-stacked"
      action="<?php echo $view->url('/dashboard/users/groups/', 'update_group') ?>" role="form">
    <?php echo $token->output('add_or_update_group') ?>

    <?php
    /** @var User $u */
    $u = $app->make(User::class);

    $delConfirmJS = t('Are you sure you want to permanently remove this group?');

    if ($u->isSuperUser() == false) { ?>
        <?php echo t('You must be logged in as %s to remove groups.', USER_SUPER) ?>
    <?php } else { ?>
        <!--suppress ES6ConvertVarToLetConst -->
        <script type="text/javascript">
            var deleteGroup = function () {
                if (confirm('<?php echo $delConfirmJS?>')) {
                    window.location.href = "<?php echo $view->url('/dashboard/users/groups', 'delete', $group->getGroupID(), $token->generate('delete_group_' . $group->getGroupID()))?>";
                }
            }
        </script>
    <?php } ?>

    <fieldset>
        <legend>
            <?php echo t('Group Details') ?>
        </legend>

        <div class="form-group">
            <?php echo $form->label("gName", t('Name')); ?>
            <?php echo $form->text("gName", $text->entities($group->getGroupName())); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("gDescription", t('Description')); ?>
            <?php echo $form->textarea("gDescription", $text->entities($group->getGroupDescription()), ["rows" => 6]); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('gtID', t('Group Type')); ?>
            <?php echo $form->select('gtID', GroupType::getSelectList(), $group->getGroupTypeId()); ?>

            <div class="help-block">
                <?php echo t("Click %s to manage the group types.", sprintf(
                    "<a href=\"%s\">%s</a>",
                    (string)Url::to("/dashboard/users/group_types"),
                    t("here")
                )); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label('gOverrideGroupTypeSettings', t('Group Type Settings')); ?>
            <?php echo $form->select('gOverrideGroupTypeSettings', [
                0 => t("Inherit settings from group type"),
                1 => t("Override settings from group type"),
            ], (int)$group->getOverrideGroupTypeSettings()); ?>
        </div>

        <div class="form-group override-group-type-setting">
            <div class="form-check">
                <?php echo $form->checkbox('gtPetitionForPublicEntry', 1, $group->isPetitionForPublicEntry(), ["class" => "form-check-input"]); ?>
                <?php echo $form->label('gtPetitionForPublicEntry', t('Petition for public entry'), ["class" => "form-check-label"]); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label('gThumbnailFID', t('Thumbnail Image')); ?>
            <?php echo $af->image('gThumbnailFID', 'gThumbnailFID', t("Please select"), $group->getThumbnailImage()); ?>
        </div>
    </fieldset>

    <fieldset class="override-group-type-setting">
        <legend>
            <?php echo t("Roles"); ?>
        </legend>

        <?php /** @noinspection PhpUnhandledExceptionInspection */
        echo View::element("groups/roles_list", ["roles" => $group->getRoles(), "defaultRole" => $group->getDefaultRole()]); ?>
    </fieldset>

    <fieldset>
        <legend>
            <?php echo t('Automation') ?>
        </legend>

        <div class="form-group">
            <div class="form-check">
                <?php echo $form->checkbox('gIsAutomated', 1, $group->isGroupAutomated()) ?>
                <?php echo $form->label("gIsAutomated", t('This group is automatically entered.'), ["class" => "form-check-label launch-tooltip", "title" => t("Automated Groups aren't assigned by administrators. They are checked against code at certain times that determines whether users should enter them.")]) ?>
            </div>
        </div>

        <div id="gAutomationOptions" style="display: none">
            <div class="form-group">
                <?php echo $form->label("", t('Check Group')); ?>

                <div class="form-check">
                    <?php echo $form->checkbox('gCheckAutomationOnRegister', 1, $group->checkGroupAutomationOnRegister()) ?>
                    <?php echo $form->label('gCheckAutomationOnRegister', t('When a user registers.'), ["class" => "form-check-label"]) ?>
                </div>

                <div class="form-check">
                    <?php echo $form->checkbox('gCheckAutomationOnLogin', 1, $group->checkGroupAutomationOnLogin()) ?>
                    <?php echo $form->label('gCheckAutomationOnLogin', t('When a user signs in.'), ["class" => "form-check-label"]) ?>
                </div>

                <div class="form-check">
                    <?php echo $form->checkbox('gCheckAutomationOnJobRun', 1, $group->checkGroupAutomationOnJobRun()) ?>
                    <?php echo $form->label('gCheckAutomationOnJobRun', t('When the "Check Automated Groups" Job runs.'), ["class" => "form-check-label"]) ?>
                </div>
            </div>

            <div class="alert alert-info">
                <?php echo t('For custom automated group actions, make sure an automation group controller exists at %s', h($group->getGroupAutomationControllerClass())); ?>
            </div>
        </div>

        <div class="form-check">
            <?php echo $form->checkbox('gUserExpirationIsEnabled', 1, $group->isGroupExpirationEnabled()) ?>
            <?php echo $form->label("gUserExpirationIsEnabled", t('Automatically remove users from this group'), ["class" => "form-check-label"]); ?>
        </div>

        <div class="form-group">
            <?php echo $form->select("gUserExpirationMethod", [
                'SET_TIME' => t('at a specific date and time'),
                'INTERVAL' => t('once a certain amount of time has passed'),
            ], $group->getGroupExpirationMethod(), ['disabled' => true, 'class' => 'form-control']);
            ?>
        </div>

        <div id="gUserExpirationSetTimeOptions" style="display: none">
            <div class="form-group">
                <?php echo $form->label("gUserExpirationSetDateTime", t('Expiration Date')); ?>
                <?php echo $date->datetime('gUserExpirationSetDateTime', $group->getGroupExpirationDateTime()) ?>
            </div>
        </div>

        <div id="gUserExpirationIntervalOptions" style="display: none">
            <div class="form-group">
                <?php echo $form->label("", t('Accounts expire after')); ?>

                <div>
                    <table class="table" style="width: auto">
                        <tr>
                            <th>
                                <?php echo t('Days') ?>
                            </th>

                            <th>
                                <?php echo t('Hours') ?>
                            </th>

                            <th>
                                <?php echo t('Minutes') ?>
                            </th>
                        </tr>

                        <tr>
                            <?php
                            $days = $group->getGroupExpirationIntervalDays();
                            $hours = $group->getGroupExpirationIntervalHours();
                            $minutes = $group->getGroupExpirationIntervalMinutes();
                            $style = 'width: 60px';
                            ?>

                            <td>
                                <?php echo $form->text('gUserExpirationIntervalDays', $days, ['style' => $style]) ?>
                            </td>

                            <td>
                                <?php echo $form->text('gUserExpirationIntervalHours', $hours, ['style' => $style]) ?>
                            </td>

                            <td>
                                <?php echo $form->text('gUserExpirationIntervalMinutes', $minutes, ['style' => $style]) ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div id="gUserExpirationAction" style="display: none">
            <div class="form-group">
                <?php echo $form->label("gUserExpirationAction", t('Expiration Action')); ?>
                <?php echo $form->select("gUserExpirationAction", [
                    'REMOVE' => t('Remove the user from this group'),
                    'DEACTIVATE' => t('Deactivate the user account'),
                    'REMOVE_DEACTIVATE' => t('Remove the user from the group and deactivate the account'),
                ], $group->getGroupExpirationAction(),
                    ['class' => 'form-control']);
                ?>
            </div>
        </div>

        <?php echo $form->hidden("gID", $group->getGroupID()); ?>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo $view->url('/dashboard/users/groups') ?>" class="btn btn-secondary pull-left">
                <?php echo t('Cancel') ?>
            </a>

            <button class="btn float-end btn-primary" style="margin-left: 10px" type="submit">
                <?php echo t('Update Group') ?>
            </button>

            <?php if ($u->isSuperUser()) { ?>
                <?php echo $ih->button_js(t('Delete'), "deleteGroup()", 'right', 'btn-danger'); ?>
            <?php } ?>
        </div>
    </div>
</form>

<!--suppress JSJQueryEfficiency, ES6ConvertVarToLetConst -->
<script type="text/javascript">
    ccm_checkGroupExpirationOptions = function () {
        var sel = $("select[name=gUserExpirationMethod]");
        var cb = $("input[name=gUserExpirationIsEnabled]");
        if (cb.prop('checked')) {
            sel.attr('disabled', false);
            switch (sel.val()) {
                case 'SET_TIME':
                    $("#gUserExpirationSetTimeOptions").show();
                    $("#gUserExpirationIntervalOptions").hide();
                    break;
                case 'INTERVAL':
                    $("#gUserExpirationSetTimeOptions").hide();
                    $("#gUserExpirationIntervalOptions").show();
                    break;
            }
            $("#gUserExpirationAction").show();
        } else {
            sel.attr('disabled', true);
            $("#gUserExpirationSetTimeOptions").hide();
            $("#gUserExpirationIntervalOptions").hide();
            $("#gUserExpirationAction").hide();
        }
    }

    $(function () {
        $("input[name=gUserExpirationIsEnabled]").click(ccm_checkGroupExpirationOptions);
        $("select[name=gUserExpirationMethod]").change(ccm_checkGroupExpirationOptions);
        ccm_checkGroupExpirationOptions();
        $('input[name=gIsBadge]').on('click', function () {
            if ($(this).is(':checked')) {
                $('#gUserBadgeOptions').show();
            } else {
                $('#gUserBadgeOptions').hide();
            }
        }).triggerHandler('click');
        $('input[name=gIsAutomated]').on('click', function () {
            if ($(this).is(':checked')) {
                $('#gAutomationOptions').show();
            } else {
                $('#gAutomationOptions').hide();
            }
        }).triggerHandler('click');

        $("#gOverrideGroupTypeSettings").change(function() {
            if($(this).val() == 1) {
                $(".override-group-type-setting").removeClass("d-none");
            } else {
                $(".override-group-type-setting").addClass("d-none");
            }
        }).trigger("change");

        //$("#gtID").change(function () {
        //    if ($("#gOverrideGroupTypeSettings").val() == 0) {
        //        $(".ccm-role-list").data("ajaxRefreshRoleList")($(this).val());
        //    }
        //});
    });

</script>
