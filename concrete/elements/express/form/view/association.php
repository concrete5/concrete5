<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="form-group">
    <div>
    <label class="control-label form-label"><?=$label?></label>
    </div>
    <?php
    if (count($entities)) {
        ?>
        <?php foreach ($entities as $entity) {
    ?>
            <div><?=$formatter->getEntryDisplayName($control, $entity)?></div>
        <?php 
}
        ?>
    <?php 
    } ?>
</div>
