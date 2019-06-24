<?php defined('C5_EXECUTE') or die('Access Denied.');?>

<?php if ($controller->getTask() == 'add'
    || $controller->getTask() == 'add_feed'
    || $controller->getTask() == 'edit'
    || $controller->getTask() == 'edit_feed'
    || $controller->getTask() == 'delete_feed') {
    $action = $view->action('add_feed');
    $tokenString = 'add_feed';
    $pfTitle = '';
    $pfDescription = '';
    $pfHandle = '';
    $cParentID = null;
    $ptID = null;
    $pfIncludeAllDescendents = false;
    $pfDisplayAliases = false;
    $pfDisplayFeaturedOnly = false;
    $pfContentToDisplay = 'S';
    $pfAreaHandleToDisplay = 'Main';
    $customTopicAttributeKeyHandle = null;
    $customTopicTreeNodeID = 0;
    $iconFID = 0;
    $imageFile = null;
    $button = t('Add');
    if (is_object($feed)) {
        $pfTitle = $feed->getTitle();
        $pfDescription = h($feed->getDescription());
        $pfHandle = $feed->getHandle();
        $cParentID = $feed->getParentID();
        $ptID = $feed->getPageTypeID();
        $pfIncludeAllDescendents = $feed->getIncludeAllDescendents();
        $pfDisplayAliases = $feed->getDisplayAliases();
        $pfDisplayFeaturedOnly = $feed->getDisplayFeaturedOnly();
        $pfContentToDisplay = $feed->getTypeOfContentToDisplay();
        $pfAreaHandleToDisplay = $feed->getAreaHandleToDisplay();
        $customTopicAttributeKeyHandle = $feed->getCustomTopicAttributeKeyHandle();
        $customTopicTreeNodeID = $feed->getCustomTopicTreeNodeID();
        $iconFID = $feed->getIconFileID();
        if ($iconFID) {
            $imageFile = File::getByID($iconFID);
        }
        $action = $view->action('edit_feed', $feed->getID());
        $tokenString = 'edit_feed';
        $button = t('Update');
    }
    ?>

    <div class="ccm-dashboard-header-buttons">
        <button data-dialog="delete-feed" class="btn btn-danger"><?php echo t("Delete Feed")?></button>
    </div>

    <?php if (is_object($feed)) {
    ?>

        <div style="display: none">
            <div id="ccm-dialog-delete-feed" class="ccm-ui">
                <form method="post" class="form-stacked" action="<?=$view->action('delete_feed')?>">
                    <?=Loader::helper("validation/token")->output('delete_feed')?>
                    <input type="hidden" name="pfID" value="<?=$feed->getID()?>" />
                    <p><?=t('Are you sure? This action cannot be undone.')?></p>
                </form>
                <div class="dialog-buttons">
                    <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                    <button class="btn btn-danger pull-right" onclick="$('#ccm-dialog-delete-feed form').submit()"><?=t('Delete Feed')?></button>
                </div>
            </div>
        </div>

    <?php 
}
    ?>

    <script type="text/javascript">
        $(function() {
            $('button[data-dialog=delete-feed]').on('click', function() {
                jQuery.fn.dialog.open({
                    element: '#ccm-dialog-delete-feed',
                    modal: true,
                    width: 320,
                    title: '<?=t("Delete Feed")?>',
                    height: 'auto'
                });
            });
        });
    </script>

    <form method="post" class="form-stacked" action="<?=$action?>">
        <?=$this->controller->token->output($tokenString)?>
        <div class="form-group">
            <?=$form->label('pfTitle', t('Title'))?>
            <?=$form->text('pfTitle', $pfTitle)?>
        </div>
        <div class="form-group">
            <?=$form->label('pfHandle', t('Handle'))?>
            <?=$form->text('pfHandle', $pfHandle)?>
        </div>
        <div class="form-group">
            <?=$form->label('pfDescription', t('Description'))?>
            <?=$form->textarea('pfDescription', $pfDescription, array('rows' => 5))?>
        </div>
        <div class="form-group">
            <?=$form->label('iconFID', t('Image'))?>
            <?=Core::make('helper/concrete/asset_library')->image('iconFID', 'iconFID', t('Choose Image'), $imageFile);
    ?>
        </div>
        <div class="form-group">
            <label class="control-label"><?=t('Filter by Parent Page')?></label>
            <?php
            echo Loader::helper('form/page_selector')->selectPage('cParentID', $cParentID);
    ?>
        </div>
        <div class="form-group">
            <?=$form->label('ptID', t('Filter By Page Type'))?>
            <?=$form->select('ptID', $pageTypes, $ptID)?>
        </div>
        <div class="form-group">
            <?=$form->label('customTopicAttributeKeyHandle', t('Filter By Topic'))?>
            <select class="form-control" name="customTopicAttributeKeyHandle" id="customTopicAttributeKeyHandle">
                <option value=""><?=t('** No Filtering')?></option>
                <?php foreach ($topicAttributes as $attributeKey) {
    $attributeController = $attributeKey->getController();
    ?>
                    <option data-topic-tree-id="<?=$attributeController->getTopicTreeID()?>" value="<?=$attributeKey->getAttributeKeyHandle()?>" <?php if ($attributeKey->getAttributeKeyHandle() == $customTopicAttributeKeyHandle) {
    ?>selected<?php 
}
    ?>><?=$attributeKey->getAttributeKeyDisplayName()?></option>
                <?php 
}
    ?>
            </select>
            <div class="tree-view-container" style="margin-top: 20px">
                <div class="tree-view-template">
                </div>
            </div>
            <input type="hidden" name="customTopicTreeNodeID" value="<?php echo $customTopicTreeNodeID ?>">
        </div>
        <div class="form-group">
            <label class="control-label"><?=t('Include All Sub-Pages of Parent?')?></label>
            <div class="radio">
                <label>
                    <?=$form->radio('pfIncludeAllDescendents', 1, $pfIncludeAllDescendents)?>
                    <?=t('Yes')?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?=$form->radio('pfIncludeAllDescendents', 0, $pfIncludeAllDescendents)?>
                    <?=t('No')?>
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label"><?=t('Display Page Aliases?')?></label>
            <div class="radio">
                <label>
                    <?=$form->radio('pfDisplayAliases', 1, $pfDisplayAliases)?>
                    <?=t('Yes')?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?=$form->radio('pfDisplayAliases', 0, $pfDisplayAliases)?>
                    <?=t('No')?>
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label"><?=t('Display Featured Only?')?></label>
            <div class="radio">
                <label>
                    <?=$form->radio('pfDisplayFeaturedOnly', 1, $pfDisplayFeaturedOnly)?>
                    <?=t('Yes')?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?=$form->radio('pfDisplayFeaturedOnly', 0, $pfDisplayFeaturedOnly)?>
                    <?=t('No')?>
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label"><?=t('Get Content From')?></label>
            <div class="radio">
                <label>
                    <?=$form->radio('pfContentToDisplay', 'S', $pfContentToDisplay)?>
                    <?=t('Short Description of Page')?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?=$form->radio('pfContentToDisplay', 'A', $pfContentToDisplay)?>
                    <?=t('Pull Content from Area')?>
                </label>
            </div>
        </div>
        <div class="form-group" data-row="area" style="display: none">
            <?=$form->label('pfAreaHandleToDisplay', t('Select Area'))?>
            <?=$form->select('pfAreaHandleToDisplay', $areas, $pfAreaHandleToDisplay)?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?=URL::to('/dashboard/pages/feeds')?>" class="btn btn-default pull-left"><?=t("Cancel")?></a>
                <button class="pull-right btn btn-success" type="submit" ><?=$button?></button>
            </div>
        </div>
    </form>

    <script type="text/javascript">
        $(function() {
            var treeViewTemplate = $('.tree-view-template');

            $('select[name=customTopicAttributeKeyHandle]').on('change', function() {
                var toolsURL = '<?php echo Loader::helper('concrete/urls')->getToolsURL('tree/load');
    ?>';
                var chosenTree = $(this).find('option:selected').attr('data-topic-tree-id');
                $('.tree-view-template').remove();
                if (!chosenTree) {
                    return;
                }
                $('.tree-view-container').append(treeViewTemplate);
                $('.tree-view-template').concreteTree({
                    'treeID': chosenTree,
                    'chooseNodeInForm': true,
                    'selectNodesByKey': [<?=intval($customTopicTreeNodeID)?>],
                    'onSelect' : function(nodes) {
                        if (nodes.length) {
                            $('input[name=customTopicTreeNodeID]').val(nodes[0]);
                        } else {
                            $('input[name=customTopicTreeNodeID]').val('');
                        }
                    }
                });
            }).trigger('change');

            $('input[name=pfContentToDisplay]').on('change', function() {
                var pfContentToDisplay = $('input[name=pfContentToDisplay]:checked').val();
                if (pfContentToDisplay == 'A') {
                    $('div[data-row=area]').show();
                } else {
                    $('div[data-row=area]').hide();
                }
            }).trigger("change");
        });

    </script>

<?php 
} else {
    ?>


    <div class="ccm-dashboard-header-buttons">
        <a href="<?php echo View::url('/dashboard/pages/feeds', 'add')?>" class="btn btn-primary"><?php echo t("Add Feed")?></a>
    </div>


    <?php if (count($feeds) > 0) {
    ?>
        <ul class="item-select-list">
            <?php foreach ($feeds as $feed) {
    ?>
                <li><a href="<?=$view->action('edit', $feed->getID())?>"><i class="fa fa-rss"></i> <?=$feed->getFeedDisplayTitle()?></a></li>
            <?php 
}
    ?>
        </ul>
    <?php 
} else {
    ?>
        <p><?=t("You have not added any feeds.")?></p>
    <?php 
}
    ?>

<?php 
} ?>