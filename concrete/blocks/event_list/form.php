<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<fieldset>
    <legend><?=t('Data Source')?></legend>

    <?php View::element('calendar/block/data_source', ['multiple' => true, 'caID' => $caID, 'calendarAttributeKeyHandle' => $calendarAttributeKeyHandle]) ?>

</fieldset>

<fieldset>
    <legend><?=t('Filtering')?></legend>

    <div class="form-group">
        <label class="control-label"><?= t('Filter By Topic') ?></label>
        <div class="radio">
            <label>
                <input type="radio" name="filterByTopic" value="none" <?php if ($filterByTopic == 'none') {
    ?> checked <?php 
} ?>>
                No topic filtering.
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="filterByTopic" value="specific" <?php if ($filterByTopic == 'specific') {
    ?> checked <?php 
} ?>>
                Specific Topic
            </label>
        </div>
        <?php if (count($pageAttributeKeys)) {
    ?>
            <div class="radio">
                <label>
                    <input type="radio" name="filterByTopic" value="page_attribute" <?php if ($filterByTopic == 'page_attribute') {
    ?> checked <?php 
}
    ?>>
                    Current Page
                </label>
            </div>
            <div data-row="page-attribute">
                <div class="form-group">
                    <select class="form-control" name="filterByPageTopicAttributeKeyHandle" id="filterByPageTopicAttributeKeyHandle">
                        <option value=""><?=t('** Select Page Attribute')?></option>
                        <?php foreach ($pageAttributeKeys as $attributeKey) {
    ?>
                            <option value="<?=$attributeKey->getAttributeKeyHandle()?>" <?php if ($attributeKey->getAttributeKeyHandle() == $filterByPageTopicAttributeKeyHandle) {
    ?>selected<?php

}
    ?>><?=$attributeKey->getAttributeKeyDisplayName()?></option>
                            <?php

}
    ?>
                    </select>
                </div>
            </div>
        <?php 
} ?>
        <div data-row="specific-topic">
            <div class="form-group">
                <select class="form-control" name="filterByTopicAttributeKeyID">
                    <option value=""><?=t('** Select Topic Attribute')?></option>
                    <?php foreach ($attributeKeys as $ak) {
    $attributeController = $ak->getController();
    ?>
                        <option value="<?=$ak->getAttributeKeyID()?>" <?php if ($ak->getAttributeKeyID() == $filterByTopicAttributeKeyID) {
    ?>selected<?php

}
    ?> data-tree-id="<?=$attributeController->getTopicTreeID()?>"><?=$ak->getAttributeKeyDisplayName()?></option>
                        <?php

} ?>
                </select>
                <input type="hidden" name="filterByTopicID" value="<?=$filterByTopicID?>">
                <div id="ccm-block-event-list-topic-tree-wrapper"></div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label"><?= t('Featured Events') ?></label>
        <div class="checkbox">
            <label>
                <input <?php if (!is_object($featuredAttribute)) {
    ?> disabled <?php 
} ?>
                    type="checkbox" name="filterByFeatured" value="1" <?php if ($filterByFeatured == 1) {
    ?> checked <?php 
} ?>
                    style="vertical-align: middle" />
                    <?= t('Display featured events only.') ?>
            </label>
        </div>
        <?php if (!is_object($featuredAttribute)) {
    ?>
            <div class="alert alert-info">
                <?=t('(<strong>Note</strong>: You must create the "is_featured" event attribute first.)');
    ?>
            </div>
        <?php 
} ?>
    </div>

</fieldset>

<fieldset>
    <legend><?=t('Results')?></legend>
    <div class="form-group">
        <label class="control-label" for="eventListTitle"><?= t('Title') ?></label>
        <?=$form->text('eventListTitle', $eventListTitle)?>
    </div>

    <div class="form-group">
        <label class="control-label" for="totalToRetrieve"><?= t('Total Number of Events to Retrieve') ?></label>
        <input id="totalToRetrieve" type="text" name="totalToRetrieve" value="<?= $totalToRetrieve ?>" class="form-control">
    </div>

    <div class="form-group">
        <label class="control-label" for="totalPerPage"><?= t('Events to Display Per Page') ?></label>
        <input id="totalPerPage" type="text" name="totalPerPage" value="<?= $totalPerPage ?>" class="form-control">
    </div>

    <div class="form-group">
        <label class="control-label" for="linkToPage"><?= t('Link To More Events Calendar/Page') ?></label>
        <?=Core::make('helper/form/page_selector')->selectPage('linkToPage', $linkToPage)?>
    </div>


</fieldset>

<script type="text/javascript">
    $(function() {

        $('input[name=filterByTopic]').on('change', function() {
            var selected = $('input[name=filterByTopic]:checked').val();
            if (selected == 'page_attribute') {
                $('div[data-row=specific-topic]').hide();
                $('div[data-row=page-attribute]').show();
            } else if (selected == 'specific') {
                $('div[data-row=page-attribute]').hide();
                $('div[data-row=specific-topic]').show();
            } else {
                $('div[data-row=specific-topic]').hide();
                $('div[data-row=page-attribute]').hide();
            }
        }).trigger('change');

        $('select[name=filterByTopicAttributeKeyID]').on('change', function() {
            $('#ccm-block-event-list-topic-tree').remove();
            var toolsURL = '<?php echo Loader::helper('concrete/urls')->getToolsURL('tree/load'); ?>';
            var chosenTree = $(this).find('option:selected').attr('data-tree-id');
            if (!chosenTree) {
                return;
            }
            $('#ccm-block-event-list-topic-tree-wrapper').append($('<div id=ccm-block-event-list-topic-tree>'));


            $('#ccm-block-event-list-topic-tree').concreteTree({
                'treeID': chosenTree,
                'chooseNodeInForm': true,
                <?php if ($filterByTopicID) { ?>
                'selectNodesByKey': [<?php echo intval($filterByTopicID) ?>],
                <?php } ?>
                'onSelect' : function(nodes) {
                    if (nodes.length) {
                        $('input[name=filterByTopicID]').val(nodes[0]);
                    } else {
                        $('input[name=filterByTopicID]').val('');
                    }
                }
            });
        }).trigger('change');
    });
</script>