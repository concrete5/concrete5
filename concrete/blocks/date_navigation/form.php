<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<fieldset>
    <legend><?=t('Filtering')?></legend>
    <div class='form-group'>
        <label for='title' class="control-label"><?=t('By Parent Page')?>:</label>
        <div class="checkbox">
            <label>
                <input <?php if (isset($cParentID) && (int) $cParentID > 0) {
    ?>checked<?php 
} ?> name="filterByParent" type="checkbox" value="1" />
                <?=t('Filter by Parent Page')?>
            </label>
        </div>
        <div id="ccm-block-related-pages-parent-page">
            <?php
            echo Loader::helper('form/page_selector')->selectPage('cParentID', isset($cParentID) ? $cParentID : null);
            ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label"><?= t('By Page Type') ?></label>
        <?php ?>
            <select class="form-control" name="ptID" id="selectPTID">
                <option value="0">** <?php echo t('All') ?> **</option>
                <?php
                foreach ($pagetypes as $ct) {
                    ?>
                    <option
                        value="<?= $ct->getPageTypeID() ?>" <?php if ((isset($ptID) ? $ptID : null) == $ct->getPageTypeID()) {
    ?> selected <?php 
}
                    ?>>
                        <?= $ct->getPageTypeDisplayName() ?>
                    </option>
                <?php

                }
                ?>
            </select>
        <?php ?>
    </div>
</fieldset>
<fieldset>
    <legend><?=t("Results")?></legend>
    <div class="form-group">
        <div class="checkbox">
            <label>
                <input <?php if (isset($cTargetID) && (int) $cTargetID > 0) {
    ?>checked<?php 
} ?> name="redirectToResults" type="checkbox" value="1" />
                <?=t('Redirect to Different Page on Click')?>
            </label>
        </div>
        <div id="ccm-block-related-pages-search-page">
            <?php
            echo Loader::helper('form/page_selector')->selectPage('cTargetID', isset($cTargetID) ? $cTargetID : null);
            ?>
        </div>
    </div>
</fieldset>
<fieldset>
    <legend><?=t('Formatting')?></legend>
    <div class="form-group">
        <label class="control-label" for="title"><?=t('Title')?></label>
        <input class="form-control" name="title" id="title" value="<?=$title?>" />
    </div>
</fieldset>


<script type="text/javascript">
    $(function() {
        $("input[name=filterByParent]").on('change', function() {
            if ($(this).is(":checked")) {
                $('#ccm-block-related-pages-parent-page').show();
            } else {
                $('#ccm-block-related-pages-parent-page').hide();
            }
        }).trigger('change');
        $("input[name=redirectToResults]").on('change', function() {
            if ($(this).is(":checked")) {
                $('#ccm-block-related-pages-search-page').show();
            } else {
                $('#ccm-block-related-pages-search-page').hide();
            }
        }).trigger('change');
    });
</script>
