<?php

defined('C5_EXECUTE') or die('Access denied.');

/**
 * @var Concrete\Core\Form\Service\Widget\GroupSelector $groupSelector
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Url\UrlImmutable $oauthRedirectUri
 * @var Concrete\Core\Url\UrlImmutable $oauthDeauthorizeUri
 * @var string $apikey
 * @var string $apisecret
 * @var bool $registrationEnabled
 * @var int|null $registrationGroup
 */
?>

<div class="alert alert-info">
    <?= t('<a href="%s" target="_blank">Click here</a> to obtain your access keys.', 'https://developers.facebook.com/apps/') ?>
    <ol class="mb-0">
        <li><?= h(t('Add the "Facebook Login" product to a Facebook app.')) ?></li>
        <li><?= t('Set the "Valid OAuth redirect URIs" to: %s', '<code>' . $oauthRedirectUri . '</code>') ?></li>
        <li><?= t('Set the "Deauthorize Callback URL" to: %s', '<code>' . $oauthDeauthorizeUri . '</code>') ?></li>
    </ol>
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
