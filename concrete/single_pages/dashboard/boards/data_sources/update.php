<?php

defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $dataSource \Concrete\Core\Entity\Board\DataSource;
 * @var $configuredDataSource \Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource
 */
$driver = $dataSource->getDriver();
?>

<div class="row">
    <div class="col-4">
        <?php
        $element = Element::get('dashboard/boards/menu', ['board' => $board, 'action' => 'data_sources']);
        $element->render();
        ?>
    </div>
    <div class="col-8">

        <h3 class="fw-light"><?=t('Update Data Source')?></h3>
        <form method="post" action="<?=$view->action('update_data_source', $configuredDataSource->getConfiguredDataSourceID())?>">
            <?=$token->output('update_data_source')?>

            <div class="form-group">
                <?=$form->label('dataSourceName', t('Data Source Name'))?>
                <?=$form->text('dataSourceName', $configuredDataSource->getName())?>
            </div>

            <h3 class="fw-light"><?=t('Population Interval')?></h3>

            <div class="help-block"><?=t('Choose how far into the future and how far into the past to populate this board. This is a rolling window as the board is updated in the future.')?></div>
            <div class="row">
                <div class="form-group col-6">
                    <?=$form->label('populationDayIntervalFuture', t('Days into Future'))?>
                    <?=$form->number('populationDayIntervalFuture', $configuredDataSource->getPopulationDayIntervalFuture())?>
                </div>

                <div class="form-group col-6">
                    <?=$form->label('populationDayIntervalPast', t('Days into Past'))?>
                    <?=$form->number('populationDayIntervalPast', $configuredDataSource->getPopulationDayIntervalPast())?>
                </div>
            </div>



            <?php
            $element = $driver->getConfigurationFormElement();
            $element->getElementController()->setConfiguredDataSource($configuredDataSource);
            $element->render();
            ?>
            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions ">
                    <a href="<?=$view->url('/dashboard/boards/data_sources', $board->getBoardID())?>" class="btn btn-secondary float-start"><?=t("Cancel")?></a>
                    <button type="submit" class="btn btn-primary float-end"><?=t('Update Data Source')?></button>
                    <button type="button" class="btn float-end btn-danger me-1" data-bs-toggle="modal" data-bs-target="#delete-data-source"><?=t('Delete')?></button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="delete-data-source" tabindex="-1">
    <form method="post" action="<?=$view->action('delete_data_source', $configuredDataSource->getConfiguredDataSourceID())?>">
        <?=$token->output('delete_data_source')?>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?=t('Delete Data Source')?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <?=t('Are you sure you want to remove this data source from this board?')?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-auto" data-bs-dismiss="modal"><?=t('Cancel')?></button>
                    <button type="submit" class="btn btn-danger float-end"><?=t('Delete Data Source')?></button>
                </div>
            </div>
        </div>
    </form>
</div>

