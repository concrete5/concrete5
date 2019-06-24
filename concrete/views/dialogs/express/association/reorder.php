<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>


<form method="post" data-dialog-form="reorder-association-entries" action="<?=$controller->action('submit')?>">

    <input type="hidden" name="entryID" value="<?=$entry->getID()?>">
    <input type="hidden" name="controlID" value="<?=$control->getID()?>">

    <p class="lead"><?=t('Drag the entries into the desired order and click the save button below.')?></p>

    <hr/>

    <div class="form-group">
        <?php
        if (!empty($selectedEntries) && count($selectedEntries)) { ?>
            <ul class="item-select-list" data-sortable-list="express-entries">
                <?php foreach($selectedEntries as $selectedEntry) { ?>
                    <li>
                        <input type="hidden" name="express_association_<?=$control->getID()?>[]" value="<?=$selectedEntry->getID()?>">
                        <?=$formatter->getEntryDisplayName($control, $selectedEntry)?>
                        <i class="ccm-item-select-list-sort"></i>

                    </li>
                <?php } ?>
            </ul>
            <?php
        } else {
            ?><p><?=t('No available entries found.')?></p><?php
        }
        ?>
    </div>

    <script type="text/javascript">
        $(function() {
            $('ul[data-sortable-list=express-entries]').sortable();
        });
    </script>

    <div class="dialog-buttons">
        <button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
        <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?=t('Save')?></button>
    </div>

</form>
