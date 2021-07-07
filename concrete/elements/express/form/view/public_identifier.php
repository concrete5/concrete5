<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if ($entry) { ?>
<div class="form-group">
    <div>
        <label class="control-label form-label"><?=$label?></label>
    </div>
    <div>
        <?=$entry->getPublicIdentifier()?>
    </div>
</div>
<?php } ?>
