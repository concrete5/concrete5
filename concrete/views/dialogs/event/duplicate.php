<?php defined('C5_EXECUTE') or die("Access Denied."); ?>


<div class="ccm-ui">
    <form method="post" data-dialog-form="duplicate-event" action="<?= $controller->action('submit') ?>">

        <h4><?= h($event->getName()) ?></h4>
        <?= $event->getDescription() ?>

        <hr/>

        <div class="form-group">
            <label class="control-label form-label"><?=t('Duplicate to Calendar')?></label>
            <?=$form->select('caID', $calendars, $caID)?>
        </div>

        <input type="hidden" name="eventID" value="<?=$event->getID()?>">
        <?php if ($year) { ?>
            <input type="hidden" name="year" value="<?=h($year)?>">
        <?php } ?>
        <?php if ($month) { ?>
            <input type="hidden" name="month" value="<?=h($month)?>">
        <?php } ?>

        <div class="dialog-buttons">
            <button class="btn btn-secondary float-start" data-dialog-action="cancel"><?= t('Cancel') ?></button>
            <button type="button" data-dialog-action="submit"
                    class="btn btn-primary float-end"><?= t('Duplicate Event') ?></button>
        </div>


    </form>
</div>
