<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $validation_token */

?>

<form method="post" id="file-sets-add" action="<?php echo Url::to('/dashboard/files/add_set', 'do_add') ?>">
    <?php echo $validation_token->output('file_sets_add'); ?>

    <div class="form-group">
        <?php echo $form->label('file_set_name', t('Name')) ?>
        <?php echo $form->text('file_set_name', '', ['autofocus' => 'autofocus']) ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo Url::to('/dashboard/files/sets') ?>" class="btn btn-secondary float-start">
                <?php echo t('Cancel') ?>
            </a>

            <?php echo $form->submit('add', t('Add'), ['class' => 'btn btn-primary float-end']); ?>
        </div>
    </div>
</form>
