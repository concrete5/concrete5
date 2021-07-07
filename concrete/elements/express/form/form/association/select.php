<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="form-group">
    <?php if ($view->supportsLabel()) { ?>
        <label class="control-label form-label" for="<?=$view->getControlID()?>"><?=$label?></label>
    <?php } ?>
    <?php if ($view->isRequired()) { ?>
        <span class="text-muted small"><?=t('Required')?></span>
    <?php } ?>

    <?php
    if (!empty($allEntries)) {
        $selectedEntry = $selectedEntries[0];
        ?>
        <select class="form-select" id="<?=$view->getControlID()?>" name="express_association_<?=$control->getId()?>">
            <option value=""><?=t('** Choose %s', $control->getControlLabel())?></option>
            <?php
            foreach ($allEntries as $entry) {
                ?>
                <option
                    value="<?=$entry->getId()?>"
                    <?php if (is_object($selectedEntry) && $selectedEntry->getID() == $entry->getID()) { ?>selected<?php } ?>
                >
                    <?=$formatter->getEntryDisplayName($control, $entry)?>
                </option>
                <?php
            }
            ?>
        </select>
    <?php
    } else {
        ?><p><?=t('No available entries found.')?></p><?php
    } ?>
</div>
