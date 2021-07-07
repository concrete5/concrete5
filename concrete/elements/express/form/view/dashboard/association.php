<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="form-group">
    <?php if ($entry) { ?>
        <div>
            <?php
            if (isset($association) && $association->getTargetEntity() && $association->getTargetEntity()->supportsCustomDisplayOrder()) {
            ?>
            <a href="<?= URL::to('/ccm/system/dialogs/express/association/reorder')?>?entryID=<?=$entry->getId()?>&amp;controlID=<?=$control->getId()?>"
               dialog-title="<?= t('Reorder Entries') ?>" dialog-width="400" dialog-height="350"
               class="dialog-launch btn btn-secondary btn-xs float-end"><?= t('Reorder Entries') ?></a>
            <?php } ?>
            <label class="control-label form-label">
                <?= $label ?></label>
        </div>
        <?php
    }

    if (count($entities)) {
        ?>
        <?php foreach ($entities as $entity) {
    ?>
            <div><a href="<?=URL::to('/dashboard/express/entries', 'view_entry', $entity->getID())?>"><?=$formatter->getEntryDisplayName($control, $entity)?></a></div>
        <?php 
}
        ?>
    <?php 
    } ?>
</div>
