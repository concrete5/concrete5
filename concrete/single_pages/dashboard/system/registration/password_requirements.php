<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Controller\SinglePage\Dashboard\System\Registration\PasswordRequirements $controller
 * @var int $min
 * @var int|null $max
 * @var int $specialCharacters
 * @var int $upperCase
 * @var int $lowerCase
 * @var int $passwordReuse
 * @var array $customRegex
 */

$customRegexList = [];
foreach ($customRegex as $regex => $description) {
    $customRegexList[] = compact('regex', 'description');
}
?>

<form method="POST" action="<?= $controller->action('save') ?>" id="ccm-passwordrequirements" v-cloak>
    <?php $token->output('save_password_requirements') ?>

    <fieldset>
        <legend><?= t('User Password Requirements') ?></legend>
        <div class="form-group">
            <div class="form-group row row-cols-auto g-0 align-items-center">
                <?= t('Require at least %s minimum characters', $form->number('min', $min, ['style' => 'width: 5rem;', 'class' => 'form-control-sm ms-1 me-1', 'required' => 'required'])) ?>
            </div>
            <div class="form-group row row-cols-auto g-0 align-items-center">
                <?= t('Require at least %s maximum characters', $form->number('max', $max ?: '', ['style' => 'width: 5rem;', 'class' => 'form-control-sm ms-1 me-1', 'min' => '1'])) ?>
            </div>
            <div class="form-group row row-cols-auto g-0 align-items-center">
                <?= t('Require at least %s special characters', $form->number('specialCharacters', $specialCharacters, ['style' => 'width: 5rem;', 'class' => 'form-control-sm ms-1 me-1', 'required' => 'required'])) ?>
            </div>
            <div class="form-group row row-cols-auto g-0 align-items-center">
                <?= t('Require at least %s lowercase characters', $form->number('lowerCase', $lowerCase, ['style' => 'width: 5rem;', 'class' => 'form-control-sm ms-1 me-1', 'required' => 'required'])) ?>
            </div>
            <div class="form-group row row-cols-auto g-0 align-items-center">
                <?= t('Require at least %s uppercase characters', $form->number('upperCase', $upperCase, ['style' => 'width: 5rem;', 'class' => 'form-control-sm ms-1 me-1', 'required' => 'required'])) ?>
            </div>
            <div class="form-check">
                <?= $form->checkbox('isPasswordReuse', '1', false, ['v-model' => 'isPasswordReuse']) ?>
                <label class="form-check-label" for="isPasswordReuse"><?= t('Prevent password reuse') ?></label>
                <div class="form-group row row-cols-auto g-0 align-items-center" v-if="isPasswordReuse">
                    <?= t('Track previous %s passwords', $form->number('passwordReuse', ['v-model.trim' => 'passwordReuse', 'v-bind:required' => 'isPasswordReuse', 'style' => 'width: 5rem;', 'class' => 'form-control-sm ms-1 me-1'])) ?>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            <?= t('Custom Regular Expression Requirements') ?>
            <a href="#" class="btn btn-sm btn-success" v-on:click.prevent="addRow"><i class="fas fa-plus"></i> Add</a>
        </legend>
        <div class="alert alert-info">
            <?= t('One or more custom regular expressions which the password must match in order for it to be valid. The password will be tested against each regular expression in order.') ?>
        </div>

        <div v-for="(regex, regexIndex) in regexes" v-bind:key="regexIndex" class="form-group">
            <div class="input-group">
                <?= $form->text('regex[]', '', ['autocomplete' => 'off', 'v-model.trim' => 'regex.regex', 'class' => 'font-monospace', 'spellcheck' => 'false', 'placeholder' => t('Regular Expression')]) ?>
                <?= $form->text('regex_desc[]', '', ['autocomplete' => 'off', 'v-model.trim' => 'regex.description', 'placeholder' => t('Description (optional)')]) ?>
                <a href="#" class="btn btn-outline-danger" v-on:click.prevent="removeRegex(regexIndex)"><i class="fas fa-trash"></i></a>
            </div>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-end">
                <a href="#" class="btn btn-danger" v-on:click.prevent="restoreDefaultValues"><?= t('Restore to Default Values') ?></a>
                <button class="btn btn-primary" type="submit"><?= t('Save') ?></button>
            </div>
        </div>
    </div>
</form>


<form method="POST" action="<?= $controller->action('reset') ?>" id="restore-defaults-form" class="d-none">
    <?php $token->output('restore_defaults') ?>
</form>

<script>
$(document).ready(function() {

    Concrete.Vue.activateContext('backend', function (Vue, config) {
        new Vue({
            el: '#ccm-passwordrequirements',
            data: function() {
                return {
                    isPasswordReuse: <?= json_encode($passwordReuse > 0) ?>,
                    passwordReuse: <?= json_encode($passwordReuse > 0 ? $passwordReuse : null) ?>,
                    regexes: <?= json_encode($customRegexList) ?>
                };
            },
            methods: {
                addRow: function() {
                    this.regexes.push({regex: '', description: ''});
                    this.$nextTick(function() {
                        $('#ccm-passwordrequirements input[name="regex[]"]:last').focus();
                    });
                },
                removeRegex: function(regexIndex) {
                    this.regexes.splice(regexIndex, 1);
                },
                restoreDefaultValues: function() {
                    ConcreteAlert.confirm(
                            <?= json_encode(implode('', [
                                t('Are you sure you want to restore to default values?'),
                                '<br />',
                                '<strong>' . t('Warning:') . '</strong> ',
                                t('This action cannot be undone.'),
                            ])) ?>,
                            function() {
                                $('#restore-defaults-form').submit();
                            },
                            'btn-danger',
                            <?= json_encode(t('Restore Defaults')) ?>
                        );

                }
            },
        });
    });

});
</script>
