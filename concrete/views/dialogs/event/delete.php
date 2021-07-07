<?php defined('C5_EXECUTE') or die("Access Denied."); ?>


<div class="ccm-ui">
    <form method="post" data-dialog-form="delete-event-occurrence" action="<?= $controller->action('submit') ?>">

        <div class="alert alert-danger">
            <?= t('Are you sure you want to delete this event? The entire event, all its data and all versions will be deleted.') ?>
        </div>

        <h4><?= h($event->getName()) ?></h4>
        <?= $event->getDescription() ?>

        <hr/>

        <?php
        $attributes = \Concrete\Core\Attribute\Key\EventKey::getList();
        foreach ($attributes as $ak) {
            $av = $event->getAttributeValueObject($ak);
            if (is_object($av)) { ?>

                <div class="form-group">
                    <label class="control-label form-label"><?=$ak->getAttributeKeyDisplayName()?></label>
                    <div><?=$av->getValue('displaySanitized', 'display')?></div>
                </div>

                <?php
            }
        }
        ?>

        <input type="hidden" name="eventID" value="<?=$event->getID()?>">

        <div class="dialog-buttons">
            <button class="btn btn-secondary float-start" data-dialog-action="cancel"><?= t('Cancel') ?></button>
            <button type="button" data-dialog-action="submit"
                    class="btn btn-danger float-end"><?= t('Delete') ?></button>
        </div>


    </form>
</div>
