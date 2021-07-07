<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Multisite\Sites $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Entity\Site\Site $site
 * @var Concrete\Core\Filesystem\Element $siteMenu
 * @var Concrete\Core\Validation\CSRF\Token $token
 */
$siteMenu->render();
?>
<div class="ccm-dashboard-dialog-wrapper">
    <div data-dialog-wrapper="delete-site">
        <form method="post" action="<?= $controller->action('delete_site') ?>">
            <?php $token->output('delete_site') ?>
            <input type="hidden" name="id" value="<?= $site->getSiteID() ?>" />
            <p><?=t('Are you sure you want to delete this site? This cannot be undone.') ?></p>
            <div class="dialog-buttons">
                <button class="btn btn-secondary" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
                <button class="btn btn-danger" onclick="$('div[data-dialog-wrapper=delete-site] form').submit()"><?= t('Delete Site') ?></button>
            </div>
        </form>
    </div>
</div>

<div class="form-group">
    <?= $form->label('', t('Handle')) ?>
    <div><?= h($site->getSiteHandle()) ?></div>
</div>
<div class="form-group">
    <?= $form->label('', t('Name')) ?>
    <div><?= h($site->getSiteName()) ?></div>
</div>
<div class="form-group">
    <?= $form->label('', t('Canonical URL')) ?>
    <div><?= h($site->getSiteCanonicalURL()) ?></div>
</div>
<div class="form-group">
    <?= $form->label('', t('Site Type')) ?>
    <div><?= h($site->getType()->getSiteTypeName()) ?></div>
</div>
<div class="form-group">
    <?= $form->label('', t('Time Zone')) ?>
    <div><?= h($site->getConfigRepository()->get('timezone')) ?></div>
</div>

<div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <div class="float-end">
            <a href="javascript:void(0)" class="btn btn-danger" data-dialog="delete-site" data-dialog-title="<?= t('Delete Site') ?>" data-dialog-width="400"><?= t('Delete Site') ?></a>            
        </div>
    </div>
</div>
