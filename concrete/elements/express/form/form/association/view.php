<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="form-group">
    <div>
    <label class="control-label form-label"><?=$label?></label>
    </div>
    <?php
    if (count($selectedEntries)) {
        ?>
        <?php foreach ($selectedEntries as $entry) {
    ?>
            <div><a href="<?=URL::to('/dashboard/express/entries', 'view_entry', $entry->getID())?>"><?=$formatter->getEntryDisplayName($control, $entry)?></a></div>
        <?php 
}
        ?>
    <?php 
    } ?>
</div>
