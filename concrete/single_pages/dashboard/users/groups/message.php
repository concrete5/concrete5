<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var View $view */
/** @var array $groups */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);
?>

<form class="ccm-dashboard-content-form" method="post" action="<?php echo $view->action('process') ?>">
    <?php echo $token->output('send_message') ?>

    <div class="form-group">
        <?php echo $form->label('subject', t('Subject')) ?>
        <?php echo $form->text('subject', $subject, ['class' => 'form-control']) ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('group', t('Select a Group')); ?>
        <?php echo $form->select('group', $groups, $group, ['class' => 'form-control']); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('message', t('Message')) ?>
        <?php echo $form->textArea('message', $message, ['class' => 'form-control']) ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary" type="submit">
                <i class="fas fa-paper-plane"></i> <?php echo t('Send Message') ?>
            </button>
        </div>
    </div>
</form>
