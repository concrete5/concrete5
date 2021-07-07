<?php

defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $dataSource \Concrete\Core\Entity\Board\DataSource;
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

        <h3 class="fw-light"><?=t('Add Data Source')?></h3>
        <form method="post" action="<?=$view->action('add_data_source', $board->getBoardID(), $dataSource->getID())?>">
            <?=$token->output('add_data_source')?>

            <div class="form-group">
                <?=$form->label('dataSourceName', t('Data Source Name'))?>
                <?=$form->text('dataSourceName')?>
            </div>

            <h3 class="fw-light"><?=t('Population Interval')?></h3>

            <div class="help-block"><?=t('Choose how far into the future and how far into the past to populate this board. This is a rolling window as the board is updated in the future.')?></div>
            <div class="row">
                <div class="form-group col-6">
                    <?=$form->label('populationDayIntervalFuture', t('Days into Future'))?>
                    <?=$form->number('populationDayIntervalFuture', 60)?>
                </div>

                <div class="form-group col-6">
                    <?=$form->label('populationDayIntervalPast', t('Days into Past'))?>
                    <?=$form->number('populationDayIntervalPast', 356)?>
                </div>
            </div>


            <?php
            $element = $driver->getConfigurationFormElement();
            $element->render();
            ?>



            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions ">
                    <a href="<?=$view->url('/dashboard/boards/data_sources', $board->getBoardID())?>" class="btn btn-secondary float-start"><?=t("Cancel")?></a>
                    <button type="submit" class="btn btn-primary float-end"><?=t('Add Data Source')?></button>
                </div>
            </div>
        </form>
    </div>
</div>
