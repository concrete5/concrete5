<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $board \Concrete\Core\Entity\Board\Board
 */
?>

<div class="row">
    <div class="col-4">
        <?php
        $element = Element::get('dashboard/boards/menu', ['board' => $board, 'action' => 'weighting']);
        $element->render();
        ?>
    </div>
    <div class="col-8">

        <?php
        if (!$board->hasCustomWeightingRules()) { ?>
            <div class="alert alert-info"><?=t('This board currently has no custom weighting rules. If you would like to weight your data sources in a custom way, enter values below.')?></div>
        <?php }
        ?>
        <form method="post" action="<?=$view->action('update_weighting', $board->getBoardID())?>">
            <?=$token->output('update_weighting')?>

            <?php if (count($configuredSources)) {
                ?>

                    <?php foreach ($configuredSources as $configuredSource) {
                        $source = $configuredSource->getDataSource();
                        $driver = $source->getDriver();
                        $formatter = $driver->getIconFormatter();
                        ?>

                    <div class="form-row">
                        <div class="col-9">
                            <label class="control-label form-label"><?=$formatter->getListIconElement()?> <?=$configuredSource->getName()?></label>
                        </div>
                        <div class="col-3">
                            <?=$form->text('weighting[' . $configuredSource->getConfiguredDataSourceID() . ']',
                                $configuredSource->getCustomWeight(),
                                ['placeholder' => t('Score')])?>
                        </div>
                    </div>
                    <hr/>


                        <?php
                    }
                    ?>


                <?php

            } else {
                ?>
                <p><?=t('You have not added any data sources to this board.')?></p>
                <?php

            } ?>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions ">
                    <button type="submit" class="btn btn-primary float-end"><?=t('Save')?></button>
                    <button type="button" class="btn float-end btn-danger me-1" data-bs-toggle="modal" data-bs-target="#reset-weighting"><?=t('Reset')?></button>
                </div>
            </div>

        </form>

        
    </div>
</div>

<div class="modal fade" id="reset-weighting" tabindex="-1">
    <form method="post" action="<?=$view->action('reset_weighting', $board->getBoardID())?>">
        <?=$token->output('reset_weighting')?>
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?=t('Reset Weighting')?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <?=t('Are you sure you want to reset custom weighting?')?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=t('Cancel')?></button>
                    <button type="submit" class="btn btn-danger float-end"><?=t('Reset')?></button>
                </div>
            </div>
        </div>
    </form>
</div>

