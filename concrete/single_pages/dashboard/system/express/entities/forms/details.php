<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Form as ExpressForm;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var View $view */
/** @var Entity $entity */
/** @var ExpressForm $expressForm */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);

?>

<form method="post" class="ccm-dashboard-content-form" action="<?php echo $view->action('save') ?>">
    <?php echo $form->hidden('entity_id', $entity->getID()) ?>

    <?php if (isset($expressForm)) { ?>
        <?php echo $form->hidden('form_id', $expressForm->getID()) ?>
    <?php } ?>

    <?php echo $token->output() ?>

    <fieldset>
        <div class="form-group">
            <?php echo $form->label('name', t('Name')) ?>
            <?php echo $form->text('name', $name) ?>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo (string)Url::to('/dashboard/system/express/entities/forms', $entity->getID()) ?>"
               class="float-start btn btn-secondary" type="button">
                <?php echo t('Back to Forms') ?>
            </a>

            <button class="float-end btn btn-primary" type="submit">
                <?php echo t('Save') ?>
            </button>
        </div>
    </div>
</form>
