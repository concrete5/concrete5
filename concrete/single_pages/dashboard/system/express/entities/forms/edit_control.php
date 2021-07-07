<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\Form\Control\Type\EntityPropertyType;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;

/** @var Control $control */
/** @var EntityPropertyType $type */

$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);
?>

<form data-form="edit-control" data-form-control="<?php echo $control->getID() ?>"
      action="<?php echo $view->action('save_control', $control->getID()) ?>" method="post">

    <?php
    $options = $control->getControlOptionsController();
    /** @noinspection PhpUndefinedClassInspection */
    $element = Element::get($options->getElement());

    if ($element->exists()) {
        /** @noinspection PhpDeprecationInspection */
        echo $options->render();
    }
    ?>

    <div class="form-group">
        <?php echo $form->label('customLabel', t('Custom Label')) ?>
        <?php echo $form->text('customLabel', $control->getCustomLabel()) ?>
    </div>

    <?php if (is_object($type->getValidator())) { ?>
        <div class="form-group">
            <?php echo $form->label('', t('Required')) ?>

            <div class="form-check">
                <?php echo $form->checkbox('isRequired', 1, $control->isRequired()) ?>
                <?php echo $form->label("isRequired", t('Yes, require this form element'), ["class" => "form-check-label"]) ?>
            </div>
        </div>
    <?php } ?>

    <?php echo $token->output('save_control') ?>
</form>

<div class="dialog-buttons">
    <button class="btn btn-secondary float-start" data-dialog-action="cancel">
        <?php echo t('Cancel') ?>
    </button>

    <button type="button" data-action="submit-edit-control" class="btn btn-primary float-end">
        <?php echo t('Save') ?>
    </button>
</div>

<!--suppress ES6ConvertVarToLetConst -->
<script type="text/javascript">
    $(function () {
        $('form[data-form=edit-control]').each(function () {
            var controlID = $(this).attr('data-form-control');
            $(this).concreteAjaxForm({
                'dataType': 'html',
                success: function (r) {
                    var control = $('tr[data-field-set-control=' + controlID + ']');
                    control.replaceWith(r);
                    jQuery.fn.dialog.closeTop();
                }
            });
        });
        $('button[data-action=submit-edit-control]').on('click', function () {
            $('form[data-form=edit-control]').trigger('submit');
        });
    });
</script>
