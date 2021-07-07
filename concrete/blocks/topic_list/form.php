<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<fieldset>
    <div class="form-group">
        <label class="control-label form-label" for="modeSelect"><?=t('Mode')?></label>
        <select class="form-select" name="mode" id="modeSelect">
            <option value="S" <?php if ($mode == 'S') {
    ?>selected<?php 
} ?>><?=t('Search – Display a list of all topics for use on a search sidebar.')?></option>
            <option value="P" <?php if ($mode == 'P') {
    ?>selected<?php 
} ?>><?=t('Page – Display a list of topics for the current page.')?></option>
        </select>
    </div>
    <div class="form-group" data-row="mode-search">
        <label class="control-label form-label" for="topicTreeIDSelect"><?=t('Topic Tree')?></label>
        <select class="form-select" name="topicTreeID" id="topicTreeIDSelect">
            <?php foreach ($trees as $stree) {
    ?>
                <option value="<?=$stree->getTreeID()?>" <?php if ($tree->getTreeID() == $stree->getTreeID()) {
    ?>selected<?php 
}
    ?>><?=$stree->getTreeDisplayName()?></option>
            <?php 
} ?>
        </select>
    </div>

    <div class="form-group" data-row="mode-page">
        <label class="control-label form-label" for="attributeKeySelect"><?=t('Topic Attribute To Display')?></label>
        <select class="form-select" name="topicAttributeKeyHandle" id="attributeKeySelect">
            <?php foreach ($attributeKeys as $attributeKey) {
    ?>
                <option value="<?=$attributeKey->getAttributeKeyHandle()?>" <?php if ($attributeKey->getAttributeKeyHandle() == $topicAttributeKeyHandle) {
    ?>selected<?php 
}
    ?>><?=$attributeKey->getAttributeKeyDisplayName()?></option>
            <?php 
} ?>
        </select>
    </div>

    <div class='form-group'>
        <label for='title' class="control-label form-label"><?=t('Results Page')?>:</label>
        <div class="form-check">
            <input class="form-check-input" id="ccm-search-block-external-target" <?php if (intval($cParentID) > 0) {
    ?>checked<?php 
} ?> name="externalTarget" type="checkbox" value="1" />
            <label for="ccm-search-block-external-target" class="form-check-label">
                <?=t('Post Results to a Different Page')?>
            </label>
        </div>
        <div id="ccm-search-block-external-target-page">
        <?php
        echo Loader::helper('form/page_selector')->selectPage('cParentID', $cParentID);
        ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label("title", t('Title')); ?>
	    <div class="input-group">
		    <?php echo $form->text('title', $title); ?>
			<?php echo $form->select('titleFormat', \Concrete\Core\Block\BlockController::$btTitleFormats, $titleFormat, array('style' => 'width:105px;flex-grow:0;', 'class' => 'form-select')); ?>
		</div>
	</div>

</fieldset>

<script type="text/javascript">
$(function() {
    $("select#modeSelect").on('change', function() {
        if ($(this).val() == 'S') {
            $('div[data-row=mode-page]').hide();
            $('div[data-row=mode-search]').show();
        } else {
            $('div[data-row=mode-search]').hide();
            $('div[data-row=mode-page]').show();
        }
    }).trigger('change');

   $("input[name=externalTarget]").on('change', function() {
       if ($(this).is(":checked")) {
           $('#ccm-search-block-external-target-page').show();
       } else {
           $('#ccm-search-block-external-target-page').hide();
       }
   }).trigger('change');
});
</script>
