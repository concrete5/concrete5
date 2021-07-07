<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;

/** @var Entity $entity */
/** @var Entity[] $entities */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);

?>

<form method="post" class="ccm-dashboard-content-form" action="<?php echo $view->action('add', $entity->getID()) ?>">
    <?php echo $token->output('add_association') ?>

    <fieldset>
        <div class="form-group">
            <?php echo $form->label('', t('Source Object')) ?>
            <p>
                <?php echo $entity->getEntityDisplayName() ?>
            </p>
        </div>

        <div class="form-group">
            <?php echo $form->label('type', t('Type')) ?>
            <?php echo $form->select('type', $types) ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('target_entity', t('Target Object')) ?>

            <!--suppress HtmlFormInputWithoutLabel -->
            <select name="target_entity" class="form-select">
                <?php foreach ($entities as $targetEntity) { ?>
                    <option value="<?php echo h($targetEntity->getID()) ?>"
                            data-plural="<?php echo h($targetEntity->getPluralHandle()) ?>"
                            data-singular="<?php echo h($targetEntity->getHandle()) ?>">
                        <?php echo $targetEntity->getEntityDisplayName() ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <?php echo $form->label('overrideTarget', t('Target Property Name')) ?>

            <div class="input-group">
                
                <div class="input-group-text">
                    <input type="checkbox" name="overrideTarget" value="1" data-toggle="association-property">
                </div>
                
                <?php echo $form->hidden('target_property_name', '') ?>
                <?php echo $form->text('target_property_name', '', ['disabled' => 'disabled']) ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label('overrideInverse', t('Inversed Property Name')) ?>

            <div class="input-group">
                <div class="input-group-text">
                    <input type="checkbox" name="overrideInverse" value="1" data-toggle="association-property">
                </div>

                <?php echo $form->hidden('inversed_property_name', $entity->getHandle()) ?>
                <?php echo $form->text('inversed_property_name', $entity->getHandle(), ['disabled' => 'disabled']) ?>
            </div>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo (string)Url::to('/dashboard/system/express/entities/associations', $entity->getId()) ?>"
               class="float-start btn btn-secondary" type="button">
                <?php echo t('Back to Associations') ?>
            </a>

            <button class="float-end btn btn-primary" type="submit">
                <?php echo t('Save') ?>
            </button>
        </div>
    </div>
</form>

<!--suppress ES6ConvertVarToLetConst, EqualityComparisonWithCoercionJS, JSJQueryEfficiency, JSDuplicatedDeclaration -->
<script type="text/javascript">
    $(function () {
        $('input[data-toggle=association-property]').on('change', function () {
            var disabled;
            if ($(this).is(':checked')) {
                disabled = false;
            } else {
                disabled = true;
                $('select[name=target_entity]').trigger('change');
            }
            $(this).closest('.form-group').find('.form-control').prop('disabled', disabled);
        }).trigger('change');

        $('select[name=target_entity],select[name=type]').on('change', function () {
            if ($('select[name=type]').val() == 'OneToMany' || $('select[name=type]').val() == 'ManyToMany') {
                var value = $('select[name=target_entity]').find('option:selected').attr('data-plural');
            } else {
                var value = $('select[name=target_entity]').find('option:selected').attr('data-singular');
            }

            $('input[name=target_property_name]').val(value);

            if ($('select[name=type]').val() == 'ManyToMany' || $('select[name=type]').val() == 'ManyToOne') {
                var value = '<?php echo $entity->getPluralHandle()?>';
            } else {
                var value = '<?php echo $entity->getHandle()?>';
            }
            $('input[name=inversed_property_name]').val(value);

        }).trigger('change');
    });
</script>
