<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Element\Dashboard\Express\Menu;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Tree\Node\Type\ExpressEntryResults;
use Concrete\Core\View\View;

/** @var Entity $entity */
/** @var ExpressEntryResults|null $resultsNode */
?>

<div class="ccm-dashboard-header-buttons">
    <?php
    $manage = new Menu($entity);
    /** @noinspection PhpDeprecationInspection */
    $manage->render();
    ?>
</div>

<div class="row">
    <?php /** @noinspection PhpUnhandledExceptionInspection */
    View::element('dashboard/express/detail_navigation', ['entity' => $entity]) ?>

    <div class="col-md-8">
        <div class="mb-4">
            <h3>
                <?php echo t('Details') ?>
            </h3>

            <h4>
                <?php echo t('Name') ?>
            </h4>

            <p>
                <?php echo $entity->getEntityDisplayName() ?>
            </p>

            <h4>
                <?php echo t('Handle') ?>
            </h4>

            <p>
                <?php echo $entity->getHandle() ?>
            </p>

            <h4>
                <?php echo t('Description') ?>
            </h4>

            <p>
                <?php echo $entity->getEntityDisplayDescription() ?>
            </p>

            <?php if ($owned_by = $entity->getOwnedBy()) { ?>
                <h4>
                    <?php echo t('Owned By') ?>
                </h4>

                <p>
                    <a href="<?php echo (string)Url::to('/dashboard/system/express/entities', 'view_entity', $owned_by->getID()) ?>">
                        <?php echo $owned_by->getName() ?>
                    </a>
                </p>
            <?php } ?>

            <?php if (!$entity->usesSeparateSiteResultsBuckets()) { ?>
                <h4>
                    <?php echo t('Total Entries') ?>
                </h4>

                <?php if ($resultsNode) { ?>
                    <p>
                        <?php echo $resultsNode->getTotalResultsInFolder(); ?>
                    </p>
                <?php } ?>
            <?php } ?>
        </div>

        <?php if ($entity->usesSeparateSiteResultsBuckets()) { ?>

            <hr>

            <h3 class="mt-4">
                <?php echo t('Results Summary') ?>
            </h3>

            <table class="table">
                <thead>
                <tr>
                    <th class="w-100">
                        <?php echo t('Site') ?>
                    </th>

                    <th>
                        <?php echo t('Results') ?>
                    </th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($sites as $site) { ?>
                    <tr>
                        <td>
                            <?php echo $site->getSiteName() ?>
                        </td>

                        <td style="white-space: nowrap" class="text-center">
                            <?php
                            $siteResultsNode = $resultsNode->getSiteResultsNode($site);
                            if ($siteResultsNode) { ?>
                                <?php echo $siteResultsNode->getTotalResultsInFolder(); ?>
                            <?php } else { ?>
                                <span class="text-warning">
                                    <?php echo t('Unknown') ?>
                                </span>

                                <i class="launch-tooltip fa fa-info-circle"
                                   title="<?php echo h(t('Unable to find site results folder. Please rescan folders below.')) ?>"></i>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <form method="post" action="<?php echo h($view->action('rescan_entries')) ?>">
                <?php echo $form->hidden('entity_id', $entity->getId()) ?>
                <?php echo $token->output('rescan_entries') ?>

                <button type="submit" name="rescan" class="btn btn-secondary btn-sm float-end">
                    <?php echo t('Rescan Entries') ?>
                </button>
            </form>
        <?php } ?>
    </div>
</div>
