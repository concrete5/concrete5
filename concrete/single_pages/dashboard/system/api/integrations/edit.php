<?php defined('C5_EXECUTE') or die('Access Denied.');

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
            <label for="name" class="control-label"><?php echo t('Name'); ?></label>
            <div class="input-group">
                <?php echo $form->text('name', $client->getName(), array('autofocus' => 'autofocus', 'autocomplete' => 'off', 'required' => 'required')); ?>
                <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
            </div>
        </div>

        <div class="form-group">
            <label for="redirect" class="control-label"><?php echo t('Redirect'); ?></label>
            <div class="input-group">
                <?php echo $form->url('redirect', $client->getRedirectUri(), array('autocomplete' => 'off')); ?>
                <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label"><?=t('User Consent Level')?></label>
            <div class="checkbox">
                <div class="form-check">
                    <label class="radio">
                        <input type="radio" name="consentType" value="<?= Client::CONSENT_SIMPLE ?>" <?= $consentType === Client::CONSENT_SIMPLE ? 'checked' : '' ?> />
                        <?= t('Standard') ?>
                    </label>
                </div>
                <div class="form-check">
                    <label class="radio">
                        <input type="radio" name="consentType" value="<?= Client::CONSENT_NONE ?>" <?= $consentType === Client::CONSENT_NONE ? 'checked' : '' ?> />
                        <?= t('None') ?>
                    </label>
                </div>
            </div>

            <div class="consent-warning alert alert-danger <?= $consentType !== Client::CONSENT_NONE ? 'hidden' : '' ?>" >
                <?= t("Only disable user consent if you trust this integration fully. By disabling user consent, you remove the user's ability to deny access.") ?>
            </div>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/dashboard/users/oauth2/view_client', $client->getIdentifier())?>" class="pull-left btn btn-default"><?=t('Cancel')?></a>
            <button class="pull-right btn btn-primary" type="submit" ><?=t('Update')?></button>
        </div>
    </div>

</form>

<script>
    (function() {
        var consentWarning = $('.consent-warning').removeClass('hidden').hide()

        $('[name="consentType"]').change(function() {
            if ($(this).val() === "<?= Client::CONSENT_NONE ?>") {
                consentWarning.fadeIn()
            } else {
                consentWarning.fadeOut()
            }
        })
    }())
</script>
