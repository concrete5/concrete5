<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\Element\Dashboard\System\Multisite\SiteType\Menu $controller
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $urlResolver
 * @var Concrete\Core\Entity\Site\Type $type
 * @var string $active
 */
?>
<nav class="navbar navbar-light navbar-expand-lg bg-light mb-3">
    <span class="navbar-brand"><?= h($type->getSiteTypeName()) ?></span>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#ccm-sitetype-menu" aria-controls="ccm-sitetype-menu" aria-expanded="false" aria-label="<?= t('Toggle navigation') ?>">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="ccm-sitetype-menu">
        <ul class="navbar-nav me-auto">
            <li class="nav-item<?= $active === 'details' ? ' active' : ''?>">
                <a class="nav-link"  href="<?= $urlResolver->resolve(['/dashboard/system/multisite/types', 'view_type', $type->getSiteTypeID()]) ?>"><?= t('Details') ?></a>
            </li>
            <li class="nav-item<?= $active === 'edit' ? ' active' : ''?>">
                <a class="nav-link"  href="<?= $urlResolver->resolve(['/dashboard/system/multisite/types', 'edit', $type->getSiteTypeID()]) ?>"><?= t('Edit') ?></a>
            </li>
            <li class="nav-item<?= $active === 'skeleton' ? ' active' : ''?>">
                <a class="nav-link"  href="<?= $urlResolver->resolve(['/dashboard/system/multisite/types', 'view_skeleton', $type->getSiteTypeID()]) ?>"><?= t('Skeleton') ?></a>
            </li>
            <li class="nav-item<?= $active === 'groups' ? ' active' : ''?>">
                <a class="nav-link"  href="<?= $urlResolver->resolve(['/dashboard/system/multisite/types', 'view_groups', $type->getSiteTypeID()]) ?>"><?= t('Default Groups') ?></a>
            </li>
            <li class="nav-item<?= $active === 'attributes' ? ' active' : ''?>">
                <a class="nav-link"  href="<?= $urlResolver->resolve(['/dashboard/system/multisite/types', 'view_attributes', $type->getSiteTypeID()]) ?>"><?= t('Attributes') ?></a>
            </li>
        </ul>
    </div>
</nav>
