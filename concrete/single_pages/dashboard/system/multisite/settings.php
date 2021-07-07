<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Multisite\Settings $controller
 * @var Concrete\Core\Site\InstallationService $service
 * @var Concrete\Core\Validation\CSRF\Token $token
 */

if ($service->isMultisiteEnabled()) {
    ?>
    <div class="alert alert-info">
        <?= t('Multiple sites are enabled.') ?>
    </div>
    <?php
} else {
    ?>
    <form action="<?= $controller->action('enable_multisite')?>" method="POST">
        <?php $token->output('enable_multisite') ?>
        <?php
        if ($controller->getAction() === 'multisite_required') { ?>
            <div class="alert alert-danger">
                <?= t('You must enable multiple site hosting before you can access multiple sites or site types.') ?>
            </div>
        <?php } else { ?>
            <p><?= t('Multiple sites are not currently enabled. Enable them below.') ?></p>
        <?php } ?>
        <div class="alert alert-info">
            <?= t('<strong>Note:</strong> Enabling multisite support will create a top-level Sites group, and a top-level folder in the file manager named Shared Files. Once enabled, multisite mode cannot be turned off.') ?>
        </div>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="btn btn-primary float-end" type="submit"><?=t('Enable Multiple Sites')?></button>
            </div>
        </div>
    </form>
    <?php
}
