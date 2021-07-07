<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="row">
    <div class="col-4">
        <?php
        $element = Element::get('dashboard/boards/instances/menu', ['instance' => $instance, 'action' => 'details']);
        $element->render();
        ?>
    </div>
    <div class="col-8">

        <h2><?=$instance->getBoardInstanceName()?></h2>

        <hr>

        <form method="post" class="float-end" action="<?=$view->action('refresh_pool', $instance->getBoardInstanceID())?>">
            <?=$token->output('refresh_pool')?>
            <button type="submit" class="btn btn-sm btn-secondary"><?=t("Refresh Data Pool")?></button>
        </form>

        <h4><?=t('Data Source Objects')?></h4>
        <p><?=t('Total data stored in your data pool.')?></p>

        <table class="table table-striped">
            <thead>
            <tr>
                <th></th>
                <th class="w-100"><?=t('Data Source')?></th>
                <th class="text-center"><?=t('#')?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($configuredSources as $configuredSource) {
                $source = $configuredSource->getDataSource();
                $driver = $source->getDriver();
                $formatter = $driver->getIconFormatter();
                $itemCount = $itemRepository->getItemCount($configuredSource, $instance);
                ?>
                <tr>
                    <td><?=$formatter->getListIconElement()?></td>
                    <td><?=$configuredSource->getName()?></td>
                    <td class="text-center"><span class="badge bg-info"><?=$itemCount?></span></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <hr>

        <div>

        <h4 class="mb-4"><?=t('View Instance')?></h4>

        <div class="help-block"><?=t('View the generated board for this instance.')?></div>

            <div class="d-grid">
                <a href="<?=$view->action('view_instance', $instance->getBoardInstanceID())?>"
                   class="btn btn-block btn-secondary"><?=t("View Instance")?></a>
            </div>

        </div>

        <hr>

        <h4 class="mb-4"><?=t('Update Instance')?></h4>

        <div class="container-fluid">
            <form method="post" action="<?=$view->action('refresh_instance', $instance->getBoardInstanceID())?>">
                <?=$token->output('refresh_instance')?>
                <div class="row mb-3">
                    <div class="ps-0 col-8 col-offset-1">
                        <h5 class="fw-light"><?=t('Refresh')?></h5>
                        <p><?=t('Refresh the dynamic elements within board slots without getting new items or changing any positioning.')?></p>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn float-end btn-secondary"><?=t("Refresh")?></button>
                    </div>
                </div>
            </form>
            <form method="post" action="<?=$view->action('add_content', $instance->getBoardInstanceID())?>">
                <?=$token->output('add_content')?>
                <div class="row mb-3">
                    <div class="ps-0 col-8 col-offset-1">
                        <h5 class="fw-light"><?=t('Add Content')?></h5>
                        <p><?=t('Refreshes dynamic elements within board slots, and adds new items to the board in applicable spots.')?></p>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn float-end btn-secondary"><?=t("Add Content")?></button>
                    </div>
                </div>
            </form>
            <form method="post" action="<?=$view->action('regenerate_instance', $instance->getBoardInstanceID())?>">
                <?=$token->output('regenerate_instance')?>
                <div class="row mb-3">
                    <div class="ps-0 col-8 col-offset-1">
                        <h5 class="fw-light"><?=t('Regenerate')?></h5>
                        <p><?=t('Regenerate board instance based on current items. Completely removes and rebuilds any board contents.')?></p>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn float-end btn-secondary"><?=t("Regenerate")?></button>
                    </div>
                </div>
            </form>

        </div>

        <hr>

        <h4 class="mb-4"><?=t('Delete Instance')?></h4>

        <form method="post" action="<?=$view->action('delete_instance', $instance->getBoardInstanceID())?>">
            <?=$token->output('delete_instance')?>
            <div class="d-grid">
                <button type="button"
                        data-bs-toggle="modal" data-bs-target="#delete-instance-<?=$instance->getBoardInstanceID()?>"
                        class="btn btn-block btn-outline-danger"><?=t("Delete Instance")?></button>
            </div>
        </form>

        <div class="modal fade" id="delete-instance-<?=$instance->getBoardInstanceID()?>" tabindex="-1">
            <form method="post" action="<?=$view->action('delete_instance', $instance->getBoardInstanceID())?>">
                <?=$token->output('delete_instance')?>
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?=t('Delete Instance')?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                        </div>
                        <div class="modal-body">
                            <?=t('Are you sure you want to remove this board instance? If it is referenced on the front-end anywhere that block will be removed.')?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary me-auto" data-bs-dismiss="modal"><?=t('Cancel')?></button>
                            <button type="submit" class="btn btn-danger float-end"><?=t('Delete Instance')?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


    </div>
</div>
