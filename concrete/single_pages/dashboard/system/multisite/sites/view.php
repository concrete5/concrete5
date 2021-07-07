<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Multisite\Sites $controller
 * @var Concrete\Core\Entity\Site\Site[] $sites
 */
?>

<table class="ccm-search-results-table">
    <thead>
        <tr>
            <th><?= t('Name') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach($sites as $site) {
            ?>
            <tr data-details-url="<?= $controller->action('view_site', $site->getSiteID()) ?>">
                <td class="ccm-search-results-name"><?= h($site->getSiteName()) ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

<div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <div class="float-end">
            <a class="btn btn-primary" href="<?= $controller->action('add') ?>"><?= t('Add Site') ?></a>
        </div>
    </div>
</div>
