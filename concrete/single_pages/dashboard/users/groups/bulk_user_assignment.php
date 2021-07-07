<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\GroupSelector;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var GroupSelector $groupSelector */
$groupSelector = $app->make(GroupSelector::class);
/** @var Token $token */
$token = $app->make(Token::class);

?>

<form action="#" method="post" enctype="multipart/form-data">
    <?php echo $token->output("bulk_user_assignment"); ?>

    <div class="form-group">
        <?php echo $form->label('csvFile', t('Source CSV File')) ?>
        <?php echo $form->file('csvFile') ?>
    </div>

    <p class="help-block">
        <?php echo t("Please select a valid CSV file which only has one column containing the users mail address. A header line, multiple columns or any other formatting is not supported yet."); ?>
    </p>

    <div class="form-group">
        <?php echo $form->label('targetGroup', t('Target Group')) ?>
        <?php $groupSelector->selectGroup('targetGroup') ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('', t('Options')) ?>

        <div class="form-check">
            <?php echo $form->checkbox('removeUnlistedUsers', 1, false, ["class" => "form-check-input"]) ?>
            <?php echo $form->label('removeUnlistedUsers', t("Remove users from this group if they don't appear in the CSV file."), ["class" => "form-check-label"]) ?>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button type="submit" class="btn btn-primary float-end">
                <?php echo t('Assign Users'); ?>
            </button>
        </div>
    </div>
</form>
