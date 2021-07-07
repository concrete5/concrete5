<?php

use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\Group\GroupType;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/** @var GroupType[] $groupTypes */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);
?>


<?php if ($this->controller->getTask() === "add") { ?>
    <form method="post" action="<?php echo (string)Url::to("/dashboard/users/group_types/add"); ?>">
        <?php echo $token->output('save_group_type'); ?>

        <fieldset>
            <legend>
                <?php echo t("General"); ?>
            </legend>

            <div class="form-group">
                <?php echo $form->label('gtName', t('Name')); ?>
                <?php echo $form->text('gtName'); ?>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <?php echo $form->checkbox('gtPetitionForPublicEntry', 1, false, ["class" => "form-check-input"]); ?>
                    <?php echo $form->label('gtPetitionForPublicEntry', t('Petition for public entry'), ["class" => "form-check-label"]); ?>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>
                <?php echo t("Roles"); ?>
            </legend>

            <?php /** @noinspection PhpUnhandledExceptionInspection */
            echo View::element("groups/roles_list", ["roles" => [], "defaultRole" => null]); ?>
        </fieldset>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?php echo (string)Url::to('/dashboard/users/group_types') ?>"
                   class="btn float-start btn-secondary">
                    <?php echo t('Cancel') ?>
                </a>

                <button type="submit" class="btn btn-primary float-end">
                    <?php echo t('Add') ?>
                </button>
            </div>
        </div>
    </form>
<?php } else if (in_array($this->controller->getTask(), ["edit", "remove"])) { ?>
    <?php echo $token->output('save_group_type'); ?>

    <?php /** @var GroupType $groupType */ ?>

    <div class="ccm-dashboard-header-buttons">
        <a href="<?php echo (string)Url::to("/dashboard/users/group_types/remove", $groupType->getId()); ?>"
           class="btn btn-danger">
            <?php echo t("Remove"); ?>
        </a>
    </div>

    <form method="post"
          action="<?php echo (string)Url::to("/dashboard/users/group_types/edit", $groupType->getId()); ?>">
        <?php echo $token->output('save_group_type'); ?>


        <fieldset>
            <legend>
                <?php echo t("General"); ?>
            </legend>

            <div class="form-group">
                <?php echo $form->label('gtName', t('Name')); ?>
                <?php echo $form->text('gtName', $groupType->getName()); ?>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <?php echo $form->checkbox('gtPetitionForPublicEntry', 1, $groupType->isPetitionForPublicEntry(), ["class" => "form-check-input"]); ?>
                    <?php echo $form->label('gtPetitionForPublicEntry', t('Petition for public entry'), ["class" => "form-check-label"]); ?>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>
                <?php echo t("Roles"); ?>
            </legend>

            <?php /** @noinspection PhpUnhandledExceptionInspection */
            echo View::element("groups/roles_list", ["roles" => $groupType->getRoles(), "defaultRole" => $groupType->getDefaultRole()]); ?>
        </fieldset>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?php echo (string)Url::to('/dashboard/users/group_types') ?>"
                   class="btn float-start btn-secondary">
                    <?php echo t('Cancel') ?>
                </a>

                <button type="submit" class="btn btn-primary float-end">
                    <?php echo t('Update') ?>
                </button>
            </div>
        </div>
    </form>
<?php } else { ?>
    <div class="ccm-dashboard-header-buttons">
        <a href="<?php echo (string)Url::to("/dashboard/users/group_types/add"); ?>" class="btn btn-primary">
            <?php echo t("Add Group Type"); ?>
        </a>
    </div>

    <ul class="item-select-list">
        <?php foreach ($groupTypes as $groupType) { ?>
            <li>
                <a href="<?php echo (string)Url::to('/dashboard/users/group_types', 'edit', $groupType->getId()); ?>">
                    <?php echo $groupType->getName(); ?>
                </a>
            </li>
        <?php } ?>
    </ul>
<?php } ?>
