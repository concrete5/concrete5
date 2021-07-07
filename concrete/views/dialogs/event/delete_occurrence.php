<?php defined('C5_EXECUTE') or die("Access Denied."); ?>


<div class="ccm-ui">
    <form method="post" data-dialog-form="delete-event-occurrence" action="<?= $controller->action('submit') ?>">

        <p><?= t('Are you sure you want to delete this occurrence?') ?></p>

        <div class="well">
            <h4><?= $occurrence->getVersion()->getName() ?></h4>
            <?= $dateFormatter->getOccurrenceDateString($occurrence) ?>
        </div>

        <input type="hidden" name="versionOccurrenceID" value="<?=$occurrence->getID()?>">

        <div class="dialog-buttons">
            <button class="btn btn-secondary float-start" data-dialog-action="cancel"><?= t('Cancel') ?></button>
            <button type="button" data-dialog-action="submit"
                    class="btn btn-danger float-end"><?= t('Delete') ?></button>
        </div>


    </form>
</div>
