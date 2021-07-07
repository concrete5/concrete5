<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Application\Service\FileManager;

$section = 'groups';

$app = Concrete\Core\Support\Facade\Application::getFacadeApplication();

$ih = $app->make('helper/concrete/ui');
$valt = $app->make('helper/validation/token');

$date = $app->make('helper/form/date_time');
$form = $app->make('helper/form');
/** @var FileManager $af */
$af = $app->make(FileManager::class);

$rootNode = $tree->getRootTreeNodeObject();

$guestGroupNode = GroupTreeNode::getTreeNodeByGroupID(GUEST_GROUP_ID);
$registeredGroupNode = GroupTreeNode::getTreeNodeByGroupID(REGISTERED_GROUP_ID);

$request = Request::getInstance();

use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\Group\GroupType; ?>

<form class="form-stacked" method="post" id="add-group-form" action="<?=$view->url('/dashboard/users/add_group/', 'do_add')?>">
    <?=$valt->output('add_or_update_group')?>
    <fieldset>
        <legend><?=t('Group Details')?></legend>
        <div class="form-group">
            <?=$form->label('gName', t('Name'))?>
            <input type="text" class="form-control" name="gName" value="<?=h($request->post('gName'))?>" />
        </div>
        <div class="form-group">
            <?=$form->label('gDescription', t('Description'))?>
            <div class="controls">
                <?=$form->textarea('gDescription', ['rows' => 6, 'class' => 'span6'])?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label('gtID', t('Group Type')); ?>
            <?php echo $form->select('gtID', GroupType::getSelectList()); ?>

            <div class="help-block">
                <?php echo t("Click %s to manage the group types.", sprintf(
                    "<a href=\"%s\">%s</a>",
                    (string)Url::to("/dashboard/users/group_types"),
                    t("here")
                )); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label('gOverrideGroupTypeSettings', t('Type Settings')); ?>
            <?php echo $form->select('gOverrideGroupTypeSettings', [
                0 => t("Inherit settings from group type"),
                1 => t("Override settings from group type"),
            ]); ?>
        </div>

        <div class="form-group override-group-type-setting">
            <div class="form-check">
                <?php echo $form->checkbox('gtPetitionForPublicEntry', 1, false, ["class" => "form-check-input"]); ?>
                <?php echo $form->label('gtPetitionForPublicEntry', t('Petition For Public Entry'), ["class" => "form-check-label"]); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label('gThumbnailFID', t('Thumbnail Image')); ?>
            <?php echo  $af->image('gThumbnailFID','gThumbnailFID', t("Please select")); ?>
        </div>

        <div class="form-group">
            <label class="control-label form-label"><?=t('Create Group Beneath')?></label>
            <div class="controls">
                <div class="groups-tree" style="width: 460px" data-groups-tree="<?=$tree->getTreeID()?>"></div>
                <?=$form->hidden('gParentNodeID')?>
                <script type="text/javascript">
                    $(function() {
                        $('[data-groups-tree=<?=$tree->getTreeID()?>]').concreteTree({
                            'treeID': '<?=$tree->getTreeID()?>',
                            'chooseNodeInForm': 'single',
                            'enableDragAndDrop': false,
                            ajaxData: {
                                displayOnly: 'group_folder'
                            },
                            <?php
                            if ($this->controller->isPost()) {
                                ?>
                                'selectNodesByKey': [<?=intval($request->post('gParentNodeID'))?>],
                                <?php

                            } else {
                                if (is_object($rootNode)) {
                                    ?>
                                    'selectNodesByKey': [<?=intval($rootNode->getTreeNodeID())?>],
                                    <?php
                                }
                            }
                            ?>
                            'removeNodesByKey': ['<?=$guestGroupNode->getTreeNodeID()?>','<?=$registeredGroupNode->getTreeNodeID()?>'],
                            'onSelect': function(nodes) {
                                if (nodes.length) {
                                    $('input[name=gParentNodeID]').val(nodes[0]);
                                } else {
                                    $('input[name=gParentNodeID]').val('');
                                }
                            }
                        });
                    });
                </script>
            </div>
        </div>
    </fieldset>

    <fieldset class="override-group-type-setting">
        <legend>
            <?php echo t("Roles"); ?>
        </legend>

        <?php /** @noinspection PhpUnhandledExceptionInspection */
        echo View::element("groups/roles_list", ["roles" => [], "defaultRole" => null]); ?>
    </fieldset>

    <fieldset>
        <div id="gUserBadgeOptions" style="display: none">
            <div class="form-group">
                <label class="control-label form-label"><?=t('Image')?></label>
                <div class="controls">
                    <?php
                    $af = $app->make('helper/concrete/asset_library');
                    echo $af->image('gBadgeFID', 'gBadgeFID', t('Choose Badge Image'), isset($badgeImage) ? $badgeImage : null);
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?=$form->label('gBadgeDescription', t('Badge Description'))?>
                <div class="controls">
                    <?=$form->textarea('gBadgeDescription', ['rows' => 6, 'class' => 'span6'])?>
                </div>
            </div>
            <div class="form-group">
                <?=$form->label('gBadgeCommunityPointValue', t('Community Points'))?>
                <div class="controls">
                    <?=$form->text('gBadgeCommunityPointValue', $app->make('config')->get('concrete.user.group.badge.default_point_value'), ['class' => 'span1'])?>
                </div>
            </div>
        </div>

    </fieldset>

    <fieldset>
        <legend><?=t('Automation')?></legend>
        <div class="form-group">
            <div class="form-check">
                <?=$form->checkbox('gIsAutomated', 1, false)?>
                <?=$form->label('gIsAutomated',t('This group is automatically entered.') . '<i class="fa fa-question-circle launch-tooltip" title="'.t("Automated Groups aren't assigned by administrators. They are checked against code at certain times that determines whether users should enter them.").'"></i>', ['class'=>'form-check-label'])?>
            </div>
        </div>

        <div id="gAutomationOptions" style="display: none">
            <div class="form-group">
                <label class="form-label"><?=t('Check Group')?></label>
                <div class="form-check">
                    <?=$form->checkbox('gCheckAutomationOnRegister', 1)?>
                    <?=$form->label('gCheckAutomationOnRegister',t('When a user registers.'), ['class'=>'form-check-label'])?>
                </div>
                <div class="form-check">
                    <?=$form->checkbox('gCheckAutomationOnLogin', 1)?>
                    <?=$form->label('gCheckAutomationOnLogin', t('When a user signs in.'), ['class'=>'form-check-label'])?>
                </div>
                <div class="form-check">
                    <?=$form->checkbox('gCheckAutomationOnJobRun', 1)?>
                    <?=$form->label('gCheckAutomationOnJobRun',t('When the "Check Automated Groups" Job runs.'), ['class'=>'form-check-label'])?>

                </div>
                <div class="alert alert-info">
                    <?php
                    echo t('For custom automated group actions, make sure an automation group controller exists.');
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="form-check">
                <?=$form->checkbox('gUserExpirationIsEnabled', 1, false)?>
                <?=$form->label('gUserExpirationIsEnabled',t('Automatically remove users from this group.'), ['class'=>'form-check-label'])?>
            </div>
            <div class="controls mt-2">
                <?=$form->select(
                    "gUserExpirationMethod",
                    [
                        'SET_TIME' => t('at a specific date and time'),
                        'INTERVAL' => t('once a certain amount of time has passed'),
                    ],
                    [
                        'disabled' => true,
                        'class' => 'form-control',
                    ]
                );?>
            </div>
        </div>

        <div id="gUserExpirationSetTimeOptions" style="display: none">
            <div class="form-group">
                <label class="form-label" for="gUserExpirationSetDateTime"><?=t('Expiration Date')?></label>
                <?=$date->datetime('gUserExpirationSetDateTime')?>
            </div>
        </div>

        <div id="gUserExpirationIntervalOptions" style="display: none">
            <div class="form-group">
                <label class="form-label"><?=t('Accounts expire after')?></label>
                <div class="controls">
                    <table class="table " style="width: auto">
                        <tr>
                            <th><?=t('Days')?></th>
                            <th><?=t('Hours')?></th>
                            <th><?=t('Minutes')?></th>
                        </tr>
                        <tr>
                            <td><?=$form->text('gUserExpirationIntervalDays', ['style' => isset($style) ? $style : null, 'class' => 'span1'])?></td>
                            <td><?=$form->text('gUserExpirationIntervalHours', ['style' => isset($style) ? $style : null, 'class' => 'span1'])?></td>
                            <td><?=$form->text('gUserExpirationIntervalMinutes', ['style' => isset($style) ? $style : null, 'class' => 'span1'])?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div id="gUserExpirationAction" style="display: none">
            <div class="form-group">
                <?=$form->label('gUserExpirationAction', t('Expiration Action'))?>
                <div class="controls">
                    <?=$form->select(
                        "gUserExpirationAction",
                        [
                            'REMOVE' => t('Remove the user from this group'),
                            'DEACTIVATE' => t('Deactivate the user account'),
                            'REMOVE_DEACTIVATE' => t('Remove the user from the group and deactivate the account'),
                        ]
                    );?>
                </div>
            </div>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=$app->make('url/manager')->resolve(['/dashboard/users/groups'])?>" class="btn btn-secondary float-start"><?=t('Cancel')?></a>
            <?=$form->submit('add', t('Add Group'), ['class' => 'btn btn-primary float-end'])?>
        </div>
    </div>

</form>

<script type="text/javascript">
ccm_checkGroupExpirationOptions = function() {
    var sel = $("select[name=gUserExpirationMethod]");
    var cb = $("input[name=gUserExpirationIsEnabled]");
    if (cb.prop('checked')) {
        sel.attr('disabled', false);
        switch(sel.val()) {
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

$(function() {
    $("input[name=gUserExpirationIsEnabled]").click(ccm_checkGroupExpirationOptions);
    $("select[name=gUserExpirationMethod]").change(ccm_checkGroupExpirationOptions);
    $('input[name=gIsBadge]').on('click', function() {
        if ($(this).is(':checked')) {
            $('#gUserBadgeOptions').show();
            } else {
                $('#gUserBadgeOptions').hide();
                }
        }).triggerHandler('click');
    $('input[name=gIsAutomated]').on('click', function() {
        if ($(this).is(':checked')) {
            $('#gAutomationOptions').show();
            } else {
                $('#gAutomationOptions').hide();
                }
        }).triggerHandler('click');
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('.icon-question-sign'))
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
    ccm_checkGroupExpirationOptions();

    $("#gOverrideGroupTypeSettings").change(function() {
        if($(this).val() == 1) {
            $(".override-group-type-setting").removeClass("d-none");
        } else {
            $(".override-group-type-setting").addClass("d-none");
        }
    }).trigger("change");
});
</script>
