<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Entity\File\Image\Editor;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;

/** @var Editor $activeEditor */
/** @var array $editorList */

$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);
/** @var Form $form */
$form = $app->make(Form::class);
?>

<form action="#" method="POST">
    <?php echo $token->output("save_editor_settings"); ?>

    <div class="form-group">
        <?php echo $form->label('activeEditor', t('Active Editor')) ?>
        <?php echo $form->select('activeEditor', $editorList, $activeEditor->getHandle()) ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-end">
                <button type="submit" class="btn btn-primary">
                    <?php echo t('Save') ?>
                </button>
            </div>
        </div>
    </div>
</form>
