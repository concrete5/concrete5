<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Multisite\Sites $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Core\Site\Type\Service $service
 * @var Concrete\Core\Page\View\PageView $this
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Doctrine\ORM\LazyCriteriaCollection $types
 * @var Concrete\Core\Page\View\PageView $view
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $urlResolver
 */

if (count($types) === 0) {
    ?>
    <div class="alert alert-info">
        <?= t('No site type is currently defined.') ?>
        <?= t('You can defined site types <a href="%s">here</a>.', $urlResolver->resolve(['/dashboard/system/multisite/types'])) ?>
    </div>
    <?php
}

foreach($types as $type) {
    $siteTypeController = $service->getController($type);
    $formatter = $siteTypeController->getFormatter($type);
    $description = $formatter->getSiteTypeDescription();
    ?>
    <div class="ccm-details-panel card" data-details-url="<?= $controller->action('add', $type->getSiteTypeID()) ?>">
        <div class="card-body">
            <div class="media">
                <?= $formatter->getSiteTypeIconElement()->addClass('me-3') ?>
                <div class="media-body">
                    <h4 class="mt-0<?= $description === '' ? ' mb-0' : ''?>"><?= $type->getSiteTypeName() ?></h4>
                    <?= $description ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

<div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <div class="float-end">
            <a class="btn btn-secondary" href="<?= $controller->action('') ?>"><?= t('Back to Sites') ?></a>
        </div>
    </div>
</div>
