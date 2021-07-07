<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;

/** @var array $entities */
/** @var ErrorList $error */

$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);
/** @var Form $form */
$form = $app->make(Form::class);

?>

<form method="post" class="ccm-dashboard-content-form" action="<?php echo $view->action('add') ?>">
    <?php echo $token->output('add_entity') ?>

    <div class="form-group <?php if ($error->containsField('name')) { ?>has-error<?php } ?>">
        <?php echo $form->label('name', t('Name')); ?>

        <div class="float-end">
            <span class="text-muted small">
                <?php echo t('Required') ?>
            </span>
        </div>

        <?php echo $form->text('name', '', ['autofocus' => 'autofocus']) ?>

        <p class="help-block">
            <?php echo t('The name is how your entity will appear in the Dashboard.') ?>
        </p>
    </div>

    <div class="form-group <?php if ($error->containsField('handle')) { ?>has-error<?php } ?>">
        <?php echo $form->label('handle', t('Handle')); ?>

        <div class="float-end">
            <span class="text-muted small">
                <?php echo t('Required') ?>
            </span>
        </div>

        <?php echo $form->text('handle') ?>

        <p class="help-block">
            <?php echo t('A unique string consisting of lowercase letters and underscores only.') ?>
        </p>
    </div>

    <div class="form-group">
        <?php echo $form->label('plural_handle', t('Plural Handle')); ?>
        <?php echo $form->text('plural_handle') ?>

        <p class="help-block">
            <?php echo t('The plural representation of the handle above. Used to retrieve this entity if it is used in associations.') ?>
        </p>
    </div>

    <div class="form-group">
        <?php echo $form->label('label_mask', t('Name Mask')); ?>
        <?php echo $form->text('label_mask') ?>

        <p class="help-block">
            <?php echo t('Example <code>Entry %name%</code> or <code>Complaint %date% at %hotel%</code>') ?>
        </p>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingThree">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-bs-toggle="collapse" href="#advanced">
                    <?php echo t('Advanced Options') ?>
                </a>
            </h4>
        </div>

        <div id="advanced"
             class="panel-collapse collapse <?php if ($controller->getRequest()->request->get('description') || $controller->getRequest()->request('supports_custom_display_order') || $controller->getRequest()->request->get('owned_by')) { ?>in <?php } ?>">
            <div class="panel-body">
                <div class="form-group">
                    <?php echo $form->label('description', t('Description')); ?>

                    <?php echo $form->textarea('description', ['rows' => 5]) ?>

                    <p class="help-block">
                        <?php echo t('An internal description. This is not publicly displayed.') ?>
                    </p>
                </div>

                <div class="form-group">
                    <?php echo $form->label('supports_custom_display_order', t('Custom Display Order')); ?>
                    <div class="form-check">
                        <?php echo $form->checkbox('supports_custom_display_order', 1) ?>
                        <?php echo $form->label("supports_custom_display_order", t('This entity supports custom display ordering via Dashboard interfaces.'), ["class" => "form-check-label"]) ?>
                    </div>
                </div>

                <div class="form-group">
                    <?php echo $form->label('owned_by', t('Owned By')); ?>
                    <?php echo $form->select('owned_by', $entities) ?>
                </div>

                <div class="form-group" style="display: none" data-group="owned_by_type">
                    <?php echo $form->label('owning_type', t('Owning Type')); ?>
                    <?php echo $form->select('owning_type', [
                        'many' => t('Many'),
                        'one' => t('One')
                    ]); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo (string)Url::to('/dashboard/system/express/entities') ?>"
               class="float-start btn btn-secondary" type="button">
                <?php echo t('Back to List') ?>
            </a>

            <button class="float-end btn btn-primary" type="submit">
                <?php echo t('Save') ?>
            </button>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(function () {
        $('select[name=owned_by]').on('change', function () {
            if ($(this).val()) {
                $('div[data-group=owned_by_type]').show();
            } else {
                $('div[data-group=owned_by_type]').hide();
            }
        }).trigger('change');
    });
</script>
