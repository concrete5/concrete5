<?php defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Entity\OAuth\Client; ?>
<?php
/**
 * @var \Concrete\Core\Entity\OAuth\Client $client
 */
$clientSecret = isset($clientSecret) ? $clientSecret : null;

// Make sure the client secret matches this client
if ($clientSecret && !password_verify($clientSecret, $client->getClientSecret())) {
    $clientSecret = null;
}

// Get the consent type from the cclient
$consentType = $client->getConsentType();
?>

<div class="ccm-dashboard-header-buttons">
    <div class="btn-group">
        <a href="<?=URL::to('/dashboard/system/api/settings'); ?>" class="btn btn-default"><?php echo t('Back to Settings'); ?></a>
        <button class="btn btn-danger" data-dialog="delete-client"><?=t("Delete")?></button>
        <a href="<?=URL::to('/dashboard/system/api/integrations', 'edit', $client->getIdentifier()); ?>" class="btn btn-primary"><?php echo t('Edit'); ?></a>
    </div>
</div>

<fieldset>
    <legend><?=t('Integration Details')?></legend>

    <div class="form-group">
        <label class="control-label"><?=t('Name')?></label>
        <div><?=$client->getName()?></div>
    </div>

    <div class="form-group">
        <label class="control-label"><?=t('Redirect URI')?></label>
        <div><?=$client->getRedirectUri() ?: t('None provided') ?></div>
    </div>

    <div class="form-group">
        <label class="control-label"><?=t('Client ID')?></label>
        <input type="text" class="form-control" onclick="this.select()" value="<?=$client->getClientKey()?>">
    </div>

    <div class="form-group <?= $clientSecret ? 'has-warning' : '' ?>">
        <label class="control-label"><?=t('Client Secret')?></label>
        <input type="<?= $clientSecret ? 'text' : 'password' ?>" class="form-control" onclick="this.select()" value="<?= $clientSecret ?: str_repeat('*', 96) ?>" <?= $clientSecret ? '' : 'disabled' ?>>
        <div class="help-block">
            <?php
            if ($clientSecret) {
                echo t('Make sure to copy this API secret, this is the last time it will be displayed.');
            } else {
                echo t('This API secret was displayed when this client was first created. It can no longer be displayed.');
            }
            ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label"><?=t('User Consent Level')?></label>
        <div class="checkbox">
            <div class="form-check">
                <label class="radio">
                    <input disabled type="radio" name="consentLevel" value="<?= Client::CONSENT_SIMPLE ?>" <?= $consentType === Client::CONSENT_SIMPLE ? 'checked' : '' ?> />
                    <?= t('Standard') ?>
                </label>
            </div>
            <div class="form-check">
                <label class="radio">
                    <input disabled type="radio" name="consentLevel" value="<?= Client::CONSENT_NONE ?>" <?= $consentType === Client::CONSENT_NONE ? 'checked' : '' ?> />
                    <?= t('None') ?>
                </label>
            </div>
        </div>

        <div class="consent-warning alert alert-danger <?= $consentType !== Client::CONSENT_NONE ? 'hidden' : '' ?>" >
            <?= t("Only disable user consent if you trust this integration fully. By disabling user consent, you remove the user's ability to deny access.") ?>
        </div>
    </div>

</fieldset>

<div style="display: none">
    <div data-dialog-wrapper="delete-client">
        <form method="post" action="<?php echo $view->action('delete'); ?>">
            <?php echo Loader::helper('validation/token')->output('delete'); ?>
            <input type="hidden" name="clientID" value="<?php echo $client->getIdentifier(); ?>">
            <p><?=t('Are you sure you want to delete this credentials set? This cannot be undone.')?></p>
            <div class="dialog-buttons">
                <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                <button class="btn btn-danger pull-right" onclick="$('div[data-dialog-wrapper=delete-client] form').submit()"><?=t('Delete')?></button>
            </div>
        </form>
    </div>
</div>
