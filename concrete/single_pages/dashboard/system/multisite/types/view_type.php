<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Multisite\Types $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Core\Entity\Site\Site[] $sites
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Entity\Site\Type $type
 * @var Concrete\Core\Filesystem\Element $typeMenu
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $urlResolver
 */

$typeMenu->render();

if (!$type->isDefault()) {
    ?>
    <div class="ccm-dashboard-dialog-wrapper">
        <div data-dialog-wrapper="delete-type">
            <form method="post" action="<?= $controller->action('delete_type') ?>">
                <?php $token->output('delete_type') ?>
                <input type="hidden" name="id" value="<?= $type->getSiteTypeID() ?>" />
                <p><?= t('Are you sure you want to delete this site type? This cannot be undone.') ?></p>
                <div class="dialog-buttons">
                    <button class="btn btn-secondary" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
                    <button class="btn btn-danger" onclick="$('div[data-dialog-wrapper=delete-type] form').submit()"><?= t('Delete Site Type') ?></button>
                </div>
            </form>
        </div>
    </div>
    <?php
}

?>
<h4><?= t('Sites') ?></h4>
<?php
if ($sites === []) {
    ?>
    <div class="alert alert-info">
        <?= t('You have not created any sites of this type.') ?>
    </div>
    <?php
} else {
    ?>
    <ul class="item-select-list">
        <?php
        foreach ($sites as $site) {
            ?>
            <li>
                <a href="<?= $urlResolver->resolve(['/dashboard/system/multisite/sites', 'view_site', $site->getSiteID()]) ?>"><i class="fa fa-link"></i> <?= h($site->getSiteName()) ?></a>
            </li>
            <?php
        }
        ?>
    </ul>
    <?php
}
if (!$type->isDefault()) {
    ?>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-end">
                <a href="javascript:void(0)" class="btn btn-danger" data-dialog="delete-type" data-dialog-title="<?= t('Delete Site Type') ?>" data-dialog-width="400"><?= t('Delete Site Type') ?></a>            
            </div>
        </div>
    </div>
    <?php
}
