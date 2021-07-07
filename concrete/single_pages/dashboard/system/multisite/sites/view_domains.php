<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Multisite\Sites $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Entity\Site\Site $site
 * @var Concrete\Core\Filesystem\Element $siteMenu
 * @var League\Url\Components\Host $canonicalDomain
 * @var Concrete\Core\Entity\Site\Domain[] $domains
 */

$siteMenu->render();

?>
<div class="ccm-dashboard-dialog-wrapper">
    <div data-dialog-wrapper="add-domain">
        <form method="post" action="<?= $controller->action('add_domain') ?>">
            <?php $token->output('add_domain') ?>
            <input type="hidden" name="id" value="<?= $site->getSiteID() ?>" />
            <div class="form-group">
                <?= $form->label('domain', t('Domain')) ?>
                <?= $form->text('domain', '', ['required' => 'required']) ?>
            </div>
            <div class="dialog-buttons">
                <button class="btn btn-secondary" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
                <button class="btn btn-danger" onclick="$('div[data-dialog-wrapper=add-domain] form').submit()"><?= t('Add Domain') ?></button>
            </div>
        </form>
    </div>
</div>

<div class="form-group">
    <?= $form->label('', t('Canonical Domain'), ['class' => 'launch-tooltip form-label', 'title' => t('This domain is automatically constructred from the site\'s canonical URL.')]) ?>
    <div><?= h($canonicalDomain) ?></div>
</div>

<div class="form-group">
    <?= $form->label('', t('Additional Domains')) ?>
    <?php
    if ($domains === []) {
        ?>
        <div><?=t('None')?></div>
        <?php
    } else {
        ?>
        <ul class="item-select-list" style="max-width: 40rem">
            <?php
            foreach ($domains as $domain) {
                ?>
                <li>
                    <span>
                        <?= h($domain->getDomain()) ?>
                        <a href="<?= $controller->action('delete_domain', $domain->getDomainID(), $token->generate('delete_domain')) ?>" class="float-end icon-link"><i class="fas fa-trash-alt"></i></a>
                    </span>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php
    }
    ?>
</div>

<div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <div class="float-end">
            <button class="btn btn-primary" data-dialog="add-domain" data-dialog-title="<?= t('Add Domain') ?>"><?= t('Add Domain') ?></button>
        </div>
    </div>
</div>
