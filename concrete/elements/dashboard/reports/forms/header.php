<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if (isset($entity)) { ?>

    <div class="ccm-header-search-form ccm-ui" data-header="express-search">
        <form class="row row-cols-auto g-0 align-items-center" method="get" action="<?php echo URL::to('/ccm/system/search/express/basic')?>?exEntityID=<?=$entity->getID()?>">

            <div class="col-auto">
                <div class="ccm-header-search-form-input">
                    <a class="ccm-header-reset-search" href="#"
                       data-button-action-url="<?= URL::to('/ccm/system/search/express/clear') ?>?exEntityID=<?=$entity->getID()?>"
                       data-button-action="clear-search"><?= t('Reset Search') ?></a>
                    <a class="ccm-header-launch-advanced-search"
                       href="<?php echo URL::to('/ccm/system/dialogs/express/advanced_search')?>?exEntityID=<?=$entity->getID()?>"
                       data-launch-dialog="advanced-search"><?= t('Advanced') ?></a>
                    <input type="text" class="form-control" autocomplete="off" name="eKeywords"
                           placeholder="<?= t('Search') ?>">
                </div>
            </div>
            <div class="col-auto">
                <button class="btn btn-info" type="submit"><i class="fa fa-search"></i></button>
            </div>
            <div class="col-auto">
                <ul class="ccm-header-search-navigation">
                    <li>
                        <a href="<?= $exportURL ?>" class="link-primary">
                            <i class="fa fa-download"></i> <?= t('Export to CSV') ?>
                        </a>
                    </li>
                    <?php if ($manageURL) { ?>
                        <li>
                            <a href="<?= $manageURL ?>" class="link-primary">
                                <i class="fa fa-cog"></i> <?= t('Manage Data Object') ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>

        </form>
    </div>

 <?php } else { ?>

    <?php if (!empty($supportsLegacy)): ?>
        <a href="<?=URL::to('/dashboard/reports/forms/legacy')?>" class="btn btn-secondary"><?=t('Legacy Forms')?></a>
    <?php endif ?>

<?php } ?>
