<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;

/** @var Entity $entity */
/** @var Association $association */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);

?>

<form method="post" class="ccm-dashboard-content-form"
      action="<?php echo $view->action('save_association', $entity->getID()) ?>">

    <?php echo $form->hidden("association_id", $association->getID()); ?>
    <?php echo $token->output() ?>

    <div class="form-group">
        <?php echo $form->label('target_property_name', t('Target Property Name')) ?>
        <?php echo $form->text('target_property_name', $association->getTargetPropertyName()) ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('inversed_property_name', t('Inversed Property Name')) ?>
        <?php echo $form->text('inversed_property_name', $association->getInversedByPropertyName()) ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('', t('Owning Association')) ?>

        <div class="form-check">
            <?php echo $form->radio('is_owning_association', 1, $association->isOwningAssociation(), ["name" => "is_owning_association", "id" => "is_owning_association_yes"]) ?>
            <?php echo $form->label('is_owning_association_yes', t('Yes'), ["class" => "form-check-label"]) ?>
        </div>

        <div class="form-check">
            <?php echo $form->radio('is_owning_association', 0, $association->isOwningAssociation(), ["name" => "is_owning_association", "id" => "is_owning_association_no"]) ?>
            <?php echo $form->label('is_owning_association_no', t('No'), ["class" => "form-check-label"]) ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label('', t('Owned By Association')) ?>

        <div class="form-check">
            <?php echo $form->radio('is_owned_by_association', 1, $association->isOwnedByAssociation(), ["name" => "is_owned_by_association", "id" => "is_owned_by_association_yes"]) ?>
            <?php echo $form->label('is_owned_by_association_yes', t('Yes'), ["class" => "form-check-label"]) ?>
        </div>

        <div class="form-check">
            <?php echo $form->radio('is_owned_by_association', 0, $association->isOwnedByAssociation(), ["name" => "is_owned_by_association", "id" => "is_owned_by_association_no"]) ?>
            <?php echo $form->label('is_owned_by_association_no', t('No'), ["class" => "form-check-label"]) ?>
        </div>
    </div>


    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo (string)Url::to('/dashboard/system/express/entities/associations', 'view_association_details', $association->getID()) ?>"
               class="float-start btn btn-secondary" type="button">
                <?php echo t('Back to Association') ?>
            </a>

            <button class="float-end btn btn-primary" type="submit">
                <?php echo t('Save') ?>
            </button>
        </div>
    </div>
</form>
