<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var \Concrete\Core\Entity\Page\Feed $feed
 * @var \Concrete\Core\Validation\CSRF\Token $validationHelper
 * @var \Concrete\Core\Application\Service\FileManager $fmHelper
 * @var \Concrete\Core\Form\Service\Widget\PageSelector $psHelper
 * @var array $topicAttributes
 * @var array $areas
 * @var array $feeds;
 */
if ($controller->getAction() == 'add'
    || $controller->getAction() == 'add_feed'
    || $controller->getAction() == 'edit'
    || $controller->getAction() == 'edit_feed'
    || $controller->getAction() == 'delete_feed') {
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
        $ignorePermissions = !$feed->shouldCheckPagePermissions();
        $action = $view->action('edit_feed', $feed->getID());
        $tokenString = 'edit_feed';
        $button = t('Update');
    }
    ?>

    <div class="ccm-dashboard-header-buttons">
        <button data-dialog="delete-feed" class="btn btn-danger"><?php echo t('Delete Feed') ?></button>
    </div>

    <?php if (is_object($feed)) { ?>
        <div style="display: none">
            <div id="ccm-dialog-delete-feed" class="ccm-ui">
                <form method="post" class="form-stacked" action="<?= $view->action('delete_feed') ?>">
                    <?= $validationHelper->output('delete_feed') ?>
                    <input type="hidden" name="pfID" value="<?= $feed->getID() ?>"/>
                    <p><?= t('Are you sure? This action cannot be undone.') ?></p>
                </form>
                <div class="dialog-buttons">
                    <button class="btn btn-secondary float-start"
                            onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
                    <button class="btn btn-danger float-end"
                            onclick="$('#ccm-dialog-delete-feed form').submit()"><?= t('Delete Feed') ?></button>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    <script type="text/javascript">
        $(function () {
            $('button[data-dialog=delete-feed]').on('click', function () {
                jQuery.fn.dialog.open({
                    element: '#ccm-dialog-delete-feed',
                    modal: true,
                    width: 320,
                    title: '<?=t('Delete Feed')?>',
                    height: 'auto'
                });
            });
        });
    </script>

    <form method="post" class="form-stacked" action="<?= $action ?>">
        <?= $this->controller->token->output($tokenString) ?>
        <div class="form-group">
            <?= $form->label('pfTitle', t('Title')) ?>
            <?= $form->text('pfTitle', $pfTitle) ?>
        </div>
        <div class="form-group">
            <?= $form->label('pfHandle', t('Handle')) ?>
            <?= $form->text('pfHandle', $pfHandle) ?>
        </div>
        <div class="form-group">
            <?= $form->label('pfDescription', t('Description')) ?>
            <?= $form->textarea('pfDescription', $pfDescription, ['rows' => 5]) ?>
        </div>
        <div class="form-group">
            <?= $form->label('iconFID', t('Image')) ?>
            <?= $fmHelper->image('iconFID', 'iconFID', t('Choose Image'), $imageFile);
            ?>
        </div>
        <div class="form-group">
            <label class="control-label form-label"><?= t('Filter by Parent Page') ?></label>
            <?= $psHelper->selectPage('cParentID', $cParentID);
            ?>
        </div>
        <div class="form-group">
            <?= $form->label('ptID', t('Filter By Page Type')) ?>
            <?= $form->select('ptID', $pageTypes, $ptID) ?>
        </div>
        <div class="form-group">
            <?= $form->label('customTopicAttributeKeyHandle', t('Filter By Topic')) ?>
            <select class="form-select" name="customTopicAttributeKeyHandle" id="customTopicAttributeKeyHandle">
                <option value=""><?= t('** No Filtering') ?></option>
                <?php foreach ($topicAttributes as $attributeKey) {
                    $attributeController = $attributeKey->getController();
                    ?>
                    <option data-topic-tree-id="<?= $attributeController->getTopicTreeID() ?>"
                            value="<?= $attributeKey->getAttributeKeyHandle() ?>"
                            <?php if ($attributeKey->getAttributeKeyHandle() == $customTopicAttributeKeyHandle) {
                            ?>selected<?php
                    }
                    ?>><?= $attributeKey->getAttributeKeyDisplayName() ?></option>
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
            <label class="control-label form-label"><?= t('Include All Sub-Pages of Parent?') ?></label>
            <div class="form-check">
                <?= $form->radio('pfIncludeAllDescendents', 1, $pfIncludeAllDescendents) ?>
                <label class="form-check-label" for="pfIncludeAllDescendents1"><?= t('Yes'); ?></label>
            </div>
            <div class="form-check">
                <?= $form->radio('pfIncludeAllDescendents', 0, $pfIncludeAllDescendents) ?>
                <label class="form-check-label" for="pfIncludeAllDescendents2"><?= t('No'); ?></label>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label form-label"><?= t('Display Page Aliases?') ?></label>

            <div class="form-check">
                <?= $form->radio('pfDisplayAliases', 1, $pfDisplayAliases) ?>
                <label class="form-check-label" for="pfDisplayAliases3"><?= t('Yes'); ?></label>
            </div>
            <div class="form-check">
                <?= $form->radio('pfDisplayAliases', 0, $pfDisplayAliases) ?>
                <label class="form-check-label" for="pfDisplayAliases4"><?= t('No'); ?></label>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label form-label"><?= t('Display Featured Only?') ?></label>
            <div class="form-check">
                <?= $form->radio('pfDisplayFeaturedOnly', 1, $pfDisplayFeaturedOnly) ?>
                <label class="form-check-label" for="pfDisplayFeaturedOnly5"><?= t('Yes'); ?></label>
            </div>
            <div class="form-check">
                <?= $form->radio('pfDisplayFeaturedOnly', 0, $pfDisplayFeaturedOnly) ?>
                <label class="form-check-label" for="pfDisplayFeaturedOnly6"><?= t('No'); ?></label>
            </div>

        </div>
        <div class="form-group">
            <label class="control-label form-label"><?= t('Get Content From') ?></label>

            <div class="form-check">
                <?= $form->radio('pfContentToDisplay', 'S', $pfContentToDisplay) ?>
                <label class="form-check-label" for="pfContentToDisplay7"> <?= t('Short Description of Page') ?></label>
            </div>

            <div class="form-check">
                <?= $form->radio('pfContentToDisplay', 'A', $pfContentToDisplay) ?>
                <label class="form-check-label" for="pfContentToDisplay8"> <?= t('Pull Content from Area') ?></label>
            </div>
        </div>
        <div class="form-group" data-row="area" style="display: none">
            <?= $form->label('pfAreaHandleToDisplay', t('Select Area')) ?>
            <?= $form->select('pfAreaHandleToDisplay', $areas, $pfAreaHandleToDisplay) ?>
        </div>
        <div class="form-group">
            <?= $form->label('ignorePermissions', t('Ignore Page Permissions')) ?>

            <div class="form-check">
                <?= $form->checkbox('ignorePermissions', 1, $ignorePermissions) ?>
                <label class="form-check-label" for="ignorePermissions">
                    <?= t('Show all pages in the RSS Feed even if the guest can not view the pages.') ?>
                </label>
            </div>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?= URL::to('/dashboard/pages/feeds') ?>"
                   class="btn btn-primary float-start"><?= t('Cancel') ?></a>
                <button class="float-end btn btn-success" type="submit"><?= $button ?></button>
            </div>
        </div>
    </form>

    <script type="text/javascript">
        $(function () {
            var treeViewTemplate = $('.tree-view-template');

            $('select[name=customTopicAttributeKeyHandle]').on('change', function () {
                var chosenTree = $(this).find('option:selected').attr('data-topic-tree-id');
                $('.tree-view-template').remove();
                if (!chosenTree) {
                    return;
                }
                $('.tree-view-container').append(treeViewTemplate);
                $('.tree-view-template').concreteTree({
                    'treeID': chosenTree,
                    'chooseNodeInForm': true,
                    'selectNodesByKey': [<?=(int) $customTopicTreeNodeID?>],
                    'onSelect': function (nodes) {
                        if (nodes.length) {
                            $('input[name=customTopicTreeNodeID]').val(nodes[0]);
                        } else {
                            $('input[name=customTopicTreeNodeID]').val('');
                        }
                    }
                });
            }).trigger('change');

            $('input[name=pfContentToDisplay]').on('change', function () {
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
        <a href="<?= URL::to('/dashboard/pages/feeds', 'add') ?>"
           class="btn btn-primary"><?php echo t('Add Feed') ?></a>
    </div>

    <?php if (count($feeds) > 0) { ?>
        <ul class="item-select-list">
            <?php foreach ($feeds as $feed) {
                ?>
                <li>
                    <a href="<?= $view->action('edit', $feed->getID()) ?>">
                        <i class="fa fa-rss"></i> <?= $feed->getFeedDisplayTitle() ?>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php
    } else { ?>
        <p><?= t('You have not added any feeds.') ?></p>
        <?php
    }
} ?>
