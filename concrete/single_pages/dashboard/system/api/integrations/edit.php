<?php defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\OAuth\Client;
/**
 * @var \Concrete\Core\Entity\OAuth\Client $client
 */

// Get the consent type from the client
$consentType = $client->getConsentType();
?>

<form method="post" class="ccm-dashboard-content-form" action="<?=$view->action('update', $client->getIdentifier())?>">
    <?=$this->controller->token->output('update')?>
    <fieldset>
        <legend><?=t('Update OAuth2 Integration')?></legend>
        <div class="form-group">
            <label for="name" ><?php echo t('Name'); ?></label>
            <div class="input-group">
                <?php echo $form->text('name', $client->getName(), array('autofocus' => 'autofocus', 'autocomplete' => 'off', 'required' => 'required')); ?>
                <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
            </div>
        </div>

        <div class="form-group">
            <label for="redirect"><?php echo t('Redirect'); ?></label>
            <div class="input-group">
                <?php echo $form->url('redirect', implode('|', (array) $client->getRedirectUri()), array('autocomplete' => 'off')); ?>
                <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
            </div>
            <span class="help-block"><?= t('Separate multiple redirect urls using %s (pipe) characters', '<code>|</code>') ?></span>
        </div>

        <div class="form-group">
            <label class="control-label form-label"><?=t('User Consent Level')?></label>
            <div class="form-check">
                <input id="consent-type-standard" class="form-check-input" type="radio" name="consentType" value="<?= Client::CONSENT_SIMPLE ?>" <?= $consentType === Client::CONSENT_SIMPLE ? 'checked' : '' ?> />
                <label for="consent-type-standard" class="form-check-label"><?= t('Standard') ?></label>
            </div>
            <div class="form-check">
                <input id="consent-type-none" class="form-check-input" type="radio" name="consentType" value="<?= Client::CONSENT_NONE ?>" <?= $consentType === Client::CONSENT_NONE ? 'checked' : '' ?> />
                <label for="consent-type-none" class="form-check-label"><?= t('None') ?></label>
            </div>

            <div class="consent-warning alert alert-danger mt-3 <?= $consentType !== Client::CONSENT_NONE ? 'd-none' : '' ?>" >
                <?= t("Only disable user consent if you trust this integration fully. By disabling user consent, you remove the user's ability to deny access.") ?>
            </div>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/dashboard/users/oauth2/view_client', $client->getIdentifier())?>" class="float-start btn btn-secondary"><?=t('Cancel')?></a>
            <button class="float-end btn btn-primary" type="submit" ><?=t('Update')?></button>
        </div>
    </div>

</form>

<script>
    (function() {
        var consentWarning = $('.consent-warning').removeClass('d-none').hide()

        $('[name="consentType"]').change(function() {
            if ($(this).val() === "<?= Client::CONSENT_NONE ?>") {
                consentWarning.fadeIn()
            } else {
                consentWarning.fadeOut()
            }
        })
    }())
</script>
