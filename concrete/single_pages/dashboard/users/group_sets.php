<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupSet;
use Concrete\Core\Validation\CSRF\Token;

/** @var Group[] $groups */
/** @var GroupSet[] $groupSets */

$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);
/** @var Repository $config */
$config = $app->make(Repository::class);
/** @var Form $form */
$form = $app->make(Form::class);

?>

<?php if (in_array($this->controller->getTask(), ['update_set', 'update_set_groups', 'edit', 'delete_set'])) { ?>
    <div class="ccm-dashboard-header-buttons">
        <button class="btn btn-danger" data-launch-dialog="delete-set">
            <?php echo t("Delete Group Set") ?>
        </button>
    </div>

    <form method="post" action="<?php echo $view->action('update_set'); ?>">
        <?php echo $token->output('update_set'); ?>

        <?php echo $form->hidden('gsID', $set->getGroupSetID()); ?>

        <div class="form-group">
            <?php echo $form->label('gsName', t('Name')); ?>
            <?php echo $form->text('gsName', $set->getGroupSetName()); ?>
            <br>
        </div>

        <div class="form-group">
            <label class="control-label form-label"><?php echo t('Groups'); ?></label>
            <?php $list = $set->getGroups(); ?>
            <?php if (count($groups) > 0) { ?>
                <?php foreach ($groups as $g) { ?>
                    <div class="form-check">
                        <?php echo $form->checkbox('gID[]', $g->getGroupID(), $set->contains($g), ["name" => "gID[]", "id" => 'gID_' . $g->getGroupID()]); ?>
                        <?php echo $form->label('gID_' . $g->getGroupID(), $g->getGroupDisplayName(), ["class" => "form-check-label"]) ?>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="control-group">
                    <p><?php echo t('No groups found.'); ?></p>
                </div>
            <?php } ?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?php echo (string)Url::to('/dashboard/users/group_sets') ?>"
                   class="btn float-start btn-secondary">
                    <?php echo t('Cancel') ?>
                </a>

                <button type="submit" class="btn btn-primary float-end">
                    <?php echo t('Update Set') ?>
                </button>
            </div>
        </div>
    </form>

    <div style="display: none">
        <div data-dialog="delete-set">
            <form method="post" action="<?php echo $view->action('delete_set'); ?>">
                <?php echo $token->output('delete_set'); ?>

                <?php echo $form->hidden('gsID', $set->getGroupSetID()); ?>

                <p>
                    <?php echo t('Are you sure you want to delete this group set? This cannot be undone.') ?>
                </p>

                <div class="dialog-buttons">
                    <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()">
                        <?php echo t('Cancel') ?>
                    </button>

                    <button class="btn btn-danger float-end" onclick="$('div[data-dialog=delete-set] form').submit()">
                        <?php echo t('Delete Set') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php } else { ?>
    <?php if ($config->get('concrete.permissions.model') == 'advanced') { ?>
        <div class="ccm-dashboard-header-buttons">
            <button class="btn btn-primary" data-launch-dialog="add-set"><?php echo t("Add Group Set") ?></button>
        </div>

        <?php if (count($groupSets) > 0) { ?>
            <ul class="item-select-list" id="ccm-group-list">
                <?php foreach ($groupSets as $gs) { ?>
                    <li>
                        <a href="<?php echo (string)Url::to('/dashboard/users/group_sets', 'edit', $gs->getGroupSetID()); ?>">
                            <i class="fa fa-users"></i> <?php echo $gs->getGroupSetDisplayName(); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p>
                <?php echo t('You have not added any group sets.'); ?>
            </p>
        <?php } ?>

        <div style="display: None">
            <div data-dialog="add-set">
                <form method="post" action="<?php echo $view->action('add_set'); ?>">
                    <?php echo $token->output('add_set'); ?>

                    <fieldset>
                        <legend>
                            <?php echo t('Add Set'); ?>
                        </legend>

                        <?php echo $form->hidden('categoryID', $categoryID); ?>

                        <div class="form-group">
                            <?php echo $form->label('gsName', t('Name')); ?>
                            <?php echo $form->text('gsName'); ?>
                        </div>

                        <div class="form-group" style="margin-top: 10px;">
                            <label class="control-label form-label">
                                <?php echo t('Groups'); ?>
                            </label>

                            <?php foreach ($groups as $g) { ?>
                                <div class="form-check">
                                    <?php echo $form->checkbox('gID[]', $g->getGroupID(), false, ["name" => "gID[]", "id" => 'gID_' . $g->getGroupID()]); ?>
                                    <?php echo $form->label('gID_' . $g->getGroupID(), $g->getGroupDisplayName(), ["class" => "form-check-label"]) ?>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="control-group">
                            <?php echo $form->submit('submit', t('Add Set'), ['class' => 'btn btn-primary float-end']); ?>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>

    <?php } else { ?>
        <p>
            <?php /** @noinspection HtmlUnknownTarget */
            echo t('You must enable <a href="%s">advanced permissions</a> to use group sets.', (string)Url::to('/dashboard/system/permissions/advanced')); ?>
        </p>
    <?php } ?>
<?php } ?>


<!--suppress ES6ConvertVarToLetConst, JSDuplicatedDeclaration -->
<script type="text/javascript">
    $('[data-launch-dialog]').on('click', function () {
        var $element = $('div[data-dialog=' + $(this).attr('data-launch-dialog') + ']');
        if ($(this).attr('data-dialog-title')) {
            var title = $(this).attr('data-dialog-title');
        } else {
            var title = $(this).text();
        }
        jQuery.fn.dialog.open({
            element: $element,
            modal: true,
            width: 320,
            title: title,
            height: 'auto'
        });
    });
</script>
