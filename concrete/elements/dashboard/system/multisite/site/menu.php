<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\Element\Dashboard\System\Multisite\Site\Menu $controller
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $urlResolver
 * @var Concrete\Core\Entity\Site\Site $site
 * @var string $active
 */
?>
<nav class="navbar navbar-light navbar-expand-lg bg-light mb-3">
    <span class="navbar-brand"><?= h($site->getSiteName()) ?></span>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#ccm-site-menu" aria-controls="ccm-site-menu" aria-expanded="false" aria-label="<?= t('Toggle navigation') ?>">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="ccm-site-menu">
        <ul class="navbar-nav me-auto">
            <li class="nav-item<?= $active === 'details' ? ' active' : ''?>">
                <a class="nav-link"  href="<?= $urlResolver->resolve(['/dashboard/system/multisite/sites', 'view_site', $site->getSiteID()]) ?>"><?= t('Details') ?></a>
            </li>
            <li class="nav-item<?= $active === 'domains' ? ' active' : ''?>">
                <a class="nav-link"  href="<?= $urlResolver->resolve(['/dashboard/system/multisite/sites', 'view_domains', $site->getSiteID()]) ?>"><?= t('Domains') ?></a>
            </li>
        </ul>
    </div>
</nav>
