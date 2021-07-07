<?php

defined('C5_EXECUTE') or die('Access denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Form\Service\Widget\GroupSelector $groupSelector
 * @var string $apikey
 * @var string $apisecret
 * @var bool $registrationEnabled
 * @var int|null $registrationGroup
 * @var string[] $whitelist
 * @var string[] $blacklist
 */
?>

<div class="alert alert-info">
    <?= t('<a href="%s" target="_blank">Click here</a> to obtain your access keys.', 'https://console.developers.google.com/project') ?>
</div>

<div class="form-group">
    <?= $form->label('apikey', t('App ID')) ?>
    <?= $form->text('apikey', $apikey, ['autocomplete' => 'off', 'class' => 'font-monospace', 'spellcheck' => 'false']) ?>
</div>
<div class="form-group">
    <?= $form->label('apisecret', t('App Secret')) ?>
    <div class="input-group">
        <?= $form->password('apisecret', $apisecret, ['autocomplete' => 'off', 'class' => 'font-monospace', 'spellcheck' => 'false']) ?>
        <button id="showsecret" class="btn btn-outline-secondary" title="<?= t('Show secret key') ?>"><i class="far fa-eye"></i></button>
    </div>
</div>

<div class="form-group">
    <?= $form->label('', t('Registration')) ?>
    <div class="form-check">
        <?= $form->checkbox('registration_enabled', '1', $registrationEnabled) ?>
        <label class="form-check-label" for="registration_enabled"><?= t('Allow automatic registration') ?></label>
    </div>
</div>
<div class="form-group registration-group">
    <?= $form->label('registration_group', t('Group to enter on registration')) ?>
    <?= $groupSelector->selectGroup('registration_group', $registrationGroup, tc('Group', 'None')) ?>
</div>

<fieldset>
    <legend><?= t('Domain Filtering') ?></legend>
    <p><?= h(t('Google allows accounts be created against custom domains like "example.com". These lists allow you to use standard PHP regular expressions to filter against the domain name or email address. For example user@example.com would filter against "example.com".')) ?></p>
    <div class="form-group">
        <?= $form->label('whitelist', t('Domain Whitelist regex')) ?>
        <?= $form->textarea('whitelist', implode("\n", $whitelist), ['class' => 'font-monospace', 'spellcheck' => 'false']) ?>
        <small class="text-muted"><?= t('One per line, to whitelist all %s domains: %s', '<code>concrete5.org</code>', '<code>~^concrete5\\.org$~i</code>') ?></small>
    </div>
    <div class="form-group">
        <?= $form->label('blacklist', t('Domain Blacklist regex')) ?>
        <?= $form->textarea('blacklist', implode("\n", $blacklist), ['class' => 'font-monospace', 'spellcheck' => 'false']) ?>
        <small class="text-muted">
            <?= t('One per line') ?><br />
            <?= t('Format: %s.', sprintf('<code>[ "~%s~i", "%s" ]</code>', t('Regex'), t('Error Message'))) ?><br />
            <?= t('To disallow everything other than whitelist: %s.', sprintf('<code>[ "~.*~", "%s" ]</code>', t('Invalid domain.'))) ?>
        </small>
    </div>
</fieldset>

<script>
$(document).ready(function() {

    $('#showsecret').on('click', function(e) {
        e.preventDefault();
        var $apisecret = $('#apisecret');
        if ($apisecret.attr('type') == 'password') {
            $apisecret.attr('type', 'text');
            $('#showsecret')
                .attr('title', <?= json_encode(t('Hide secret key')) ?>)
                .html('<i class="far fa-eye-slash"></i>')
            ;
        } else {
            $apisecret.attr('type', 'password');
            $('#showsecret')
                .attr('title', <?= json_encode(t('Show secret key')) ?>)
                .html('<i class="far fa-eye"></i>')
            ;
        }
    });

    $('input[name="registration_enabled"]')
        .on('change', function () {
            $('div.registration-group').toggle($(this).is(':checked'));
        })
        .trigger('change')
    ;

}());
</script>
