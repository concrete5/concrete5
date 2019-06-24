<?php defined('C5_EXECUTE') or die('Access Denied.');
$c = Page::getCurrentPage();
$siteType = null;
if ($c) {
    $pageType = $c->getPageTypeObject();
    if ($pageType) {
        $siteType = $pageType->getSiteTypeObject(); // gotta have this for editing defaults pages.
    } else {
        $tree = $c->getSiteTreeObject();
        if (is_object($tree)) {
            $siteType = $tree->getSiteType();
        }
    }
}
$form = Loader::helper('form/page_selector');
?>

<?=Loader::helper('concrete/ui')->tabs(array(
    array('page-list-settings', t('Settings'), true),
    array('page-list-preview', t('Preview'))
));?>

<div class="ccm-tab-content" id="ccm-tab-content-page-list-settings">
    <div class=" pagelist-form">

        <input type="hidden" name="pageListToolsDir" value="<?= Loader::helper('concrete/urls')->getBlockTypeToolsURL($bt) ?>/"/>

        <fieldset>
            <div class="form-group">
                <label class='control-label'><?= t('Number of Pages to Display') ?></label>
                <input type="text" name="num" value="<?= $num ?>" class="form-control">
            </div>

            <div class="form-group">
                <label class="control-label"><?= t('Page Type') ?></label>
                <?php
                $ctArray = PageType::getList(false, $siteType);

                if (is_array($ctArray)) {
                    ?>
                    <select class="form-control" name="ptID" id="selectPTID">
                        <option value="0">** <?php echo t('All') ?> **</option>
                        <?php
                        foreach ($ctArray as $ct) {
                            ?>
                            <option
                                value="<?= $ct->getPageTypeID() ?>" <?php if ($ptID == $ct->getPageTypeID()) {
                                ?> selected <?php
                            }
                            ?>>
                                <?= $ct->getPageTypeDisplayName() ?>
                            </option>
                            <?php

                        }
                        ?>
                    </select>
                    <?php

                }
                ?>
            </div>
        </fieldset>

        <fieldset>
            <div class="form-group">
                <label class="control-label"><?= t('Topics') ?></label>
                <div class="radio">
                    <label>
                        <input type="radio" name="topicFilter" id="topicFilter"
                               value="" <?php if (!$filterByRelated && !$filterByCustomTopic) {
                            ?> checked<?php
                        } ?> />
                        <?= t('No topic filtering') ?>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="topicFilter" id="topicFilterCustom"
                               value="custom" <?php if ($filterByCustomTopic) {
                            ?> checked<?php
                        } ?>>
                        <?= t('Custom Topic') ?>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="topicFilter" id="topicFilterRelated"
                               value="related" <?php if ($filterByRelated) {
                            ?> checked<?php
                        } ?> >
                        <?= t('Related Topic') ?>
                    </label>
                </div>
                <div data-row="custom-topic">
                    <select class="form-control" name="customTopicAttributeKeyHandle" id="customTopicAttributeKeyHandle">
                        <option value=""><?=t('Choose topics attribute.')?></option>
                        <?php foreach ($attributeKeys as $attributeKey) {
                            $attributeController = $attributeKey->getController();
                            ?>
                            <option data-topic-tree-id="<?=$attributeController->getTopicTreeID()?>" value="<?=$attributeKey->getAttributeKeyHandle()?>" <?php if ($attributeKey->getAttributeKeyHandle() == $customTopicAttributeKeyHandle) {
                            ?>selected<?php
                            }
                            ?>><?=$attributeKey->getAttributeKeyDisplayName()?></option>
                            <?php
                        } ?>
                    </select>
                    <div class="tree-view-container">
                        <div class="tree-view-template">
                        </div>
                    </div>
                    <input type="hidden" name="customTopicTreeNodeID" value="<?php echo $customTopicTreeNodeID ?>">

                </div>
                <div data-row="related-topic">
                    <span class="help-block"><?=t('Allows other blocks like the topic list block to pass search criteria to this page list block.')?></span>
                    <select class="form-control" name="relatedTopicAttributeKeyHandle" id="relatedTopicAttributeKeyHandle">
                        <option value=""><?=t('Choose topics attribute.')?></option>
                        <?php foreach ($attributeKeys as $attributeKey) {
                            ?>
                            <option value="<?=$attributeKey->getAttributeKeyHandle()?>" <?php if ($attributeKey->getAttributeKeyHandle() == $relatedTopicAttributeKeyHandle) {
                            ?>selected<?php
                            }
                            ?>><?=$attributeKey->getAttributeKeyDisplayName()?></option>
                            <?php
                        } ?>
                    </select>
                </div>
            </div>

        </fieldset>

        <fieldset>
            <div class="form-group">
                <label class="control-label"><?= t('Filter by Public Date') ?></label>
                <?php
                $filterDateOptions = array(
                    'all' => t('Show All'),
                    'now' => t('Today'),
                    'past' => t('Before Today'),
                    'future' => t('After Today'),
                    'between' => t('Between'),
                );

                foreach ($filterDateOptions as $filterDateOptionHandle => $filterDateOptionLabel) {
                    $isChecked = ($filterDateOption == $filterDateOptionHandle) ? 'checked' : '';
                    ?>
                    <div class="radio">
                        <label>
                            <input type="radio" class='filterDateOption' name="filterDateOption" value="<?=$filterDateOptionHandle?>" <?=$isChecked?> />
                            <?= $filterDateOptionLabel ?>
                        </label>
                    </div>
                    <?php
                } ?>

                <div class="filterDateOptionDetail" data-filterDateOption="past">
                    <div class="form-group">
                        <label class="control-label"><?=t('Days in the Past')?> <i class="launch-tooltip fa fa-question-circle" title="<?=t('Leave 0 to show all past dated pages')?>"></i></label>
                        <input type="text" name="filterDatePast" value="<?= $filterDateDays ? $filterDateDays : 0 ?>" class="form-control">
                    </div>
                </div>

                <div class="filterDateOptionDetail" data-filterDateOption="future">
                    <div class="form-group">
                        <label class="control-label"><?=t('Days in the Future')?> <i class="launch-tooltip fa fa-question-circle" title="<?=t('Leave 0 to show all future dated pages')?>"></i></label>
                        <input type="text" name="filterDateFuture" value="<?= $filterDateDays ? $filterDateDays : 0 ?>" class="form-control">
                    </div>
                </div>

                <div class="filterDateOptionDetail" data-filterDateOption="between">
                    <?php
                    $datetime = loader::helper('form/date_time');
                    echo $datetime->date('filterDateStart', $filterDateStart);
                    echo "<p>" . t('and') . "</p>";
                    echo $datetime->date('filterDateEnd', $filterDateEnd);
                    ?>
                </div>

            </div>

        </fieldset>

        <fieldset>
            <div class="form-group">
                <label class="control-label"><?= t('Other Filters') ?></label>
                <div class="checkbox">
                    <label>
                        <input <?php if (!is_object($featuredAttribute)) {
                            ?> disabled <?php
                        } ?> type="checkbox" name="displayFeaturedOnly"
                             value="1" <?php if ($displayFeaturedOnly == 1) {
                            ?> checked <?php
                        } ?>
                             style="vertical-align: middle"/>
                        <?= t('Featured pages only.') ?>
                    </label>
                    <?php if (!is_object($featuredAttribute)) {
                        ?>
                        <span class="help-block"><?=
                            t(
                                '(<strong>Note</strong>: You must create the "is_featured" page attribute first.)');
                            ?></span>
                        <?php
                    } ?>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="displayAliases"
                               value="1" <?php if ($displayAliases == 1) {
                            ?> checked <?php
                        } ?> />
                        <?= t('Display page aliases.') ?>
                    </label>
                </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="ignorePermissions"
                       value="1" <?php if ($ignorePermissions == 1) { ?> checked <?php } ?> />
                <?= t('Ignore page permissions.') ?>
            </label>
        </div>

            <div class="checkbox">
            <label>
                <input type="checkbox" name="enableExternalFiltering" value="1" <?php if ($enableExternalFiltering) { ?>checked<?php } ?> />
                <?= t('Enable Other Blocks to Filter This Page List.') ?>
            </label>
        </div>

        </fieldset>

        <fieldset>
            <div class="form-group">
                <label class="control-label"><?=t('Pagination')?></label>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="paginate" value="1" <?php if ($paginate == 1) {
                            ?> checked <?php
                        } ?> />
                        <?= t('Display pagination interface if more items are available than are displayed.') ?>
                    </label>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <div class="form-group">
                <label class="control-label"><?=t('Location')?></label>

                <div class="radio">
                    <label>
                        <input type="radio" name="cParentID" id="cEverywhereField"
                               value="0" <?php if ($cParentID == 0) {
                            ?> checked<?php
                        } ?> />
                        <?= t('Everywhere') ?>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="cParentID" id="cThisPageField"
                               value="<?= $c->getCollectionID() ?>" <?php if ($cThis) {
                            ?> checked<?php
                        } ?>>
                        <?= t('Beneath this page') ?>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="cParentID" id="cThisParentField"
                               value="<?= $c->getCollectionParentID() ?>" <?php if ($cThisParent) { ?> checked<?php } ?>>
                        <?= t('At the current level') ?>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="cParentID" id="cOtherField"
                               value="OTHER" <?php if ($isOtherPage) {
                            ?> checked<?php
                        } ?>>
                        <?= t('Beneath another page') ?>
                    </label>
                </div>

                <div class="ccm-page-list-page-other" <?php if (!$isOtherPage) {
                    ?> style="display: none" <?php
                } ?>>

                    <?= $form->selectPage('cParentIDValue', $isOtherPage ? $cParentID : false); ?>
                </div>

                <div class="ccm-page-list-all-descendents"
                     style="<?php echo ($cParentID === 0) ? ' display: none;' : ''; ?>">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="includeAllDescendents" id="includeAllDescendents"
                                   value="1" <?php echo $includeAllDescendents ? 'checked="checked"' : '' ?> />
                            <?php echo t('Include all child pages') ?>
                        </label>
                    </div>
                </div>

            </div>

        </fieldset>

        <fieldset>

            <div class="form-group">
                <label class="control-label"><?=t('Sort')?></label>
                <select name="orderBy" class="form-control">
                    <option value="display_asc" <?php if ($orderBy == 'display_asc') {
                        ?> selected <?php
                    } ?>>
                        <?= t('Sitemap order') ?>
                    </option>
                    <option value="display_desc" <?php if ($orderBy == 'display_desc') {
                        ?> selected <?php
                    } ?>>
                        <?= t('Reverse sitemap order') ?>
                    </option>
                    <option value="chrono_desc" <?php if ($orderBy == 'chrono_desc') {
                        ?> selected <?php
                    } ?>>
                        <?= t('Most recent first') ?>
                    </option>
                    <option value="chrono_asc" <?php if ($orderBy == 'chrono_asc') {
                        ?> selected <?php
                    } ?>>
                        <?= t('Earliest first') ?>
                    </option>
                    <option value="alpha_asc" <?php if ($orderBy == 'alpha_asc') {
                        ?> selected <?php
                    } ?>>
                        <?= t('Alphabetical order') ?>
                    </option>
                    <option value="alpha_desc" <?php if ($orderBy == 'alpha_desc') {
                        ?> selected <?php
                    } ?>>
                        <?= t('Reverse alphabetical order') ?>
                    </option>
                    <option value="modified_desc" <?php if ($orderBy == 'modified_desc') {
                        ?> selected <?php
                    } ?>>
                        <?= t('Most recently modified first') ?>
                    </option>
                    <option value="random" <?php if ($orderBy == 'random') {
                        ?> selected <?php
                    } ?>>
                        <?= t('Random') ?>
                    </option>
                </select>
            </div>
        </fieldset>

        <fieldset>
            <legend><?= t('Output') ?></legend>
            <div class="form-group">
                <label class="control-label"><?= t('Provide RSS Feed') ?></label>
                <div class="radio">
                    <label>
                        <input type="radio" name="rss" class="rssSelector"
                               value="0" <?= (is_object($rssFeed) ? "" : "checked=\"checked\"") ?>/> <?= t('No') ?>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input id="ccm-pagelist-rssSelectorOn" type="radio" name="rss" class="rssSelector"
                               value="1" <?= (is_object($rssFeed) ? "checked=\"checked\"" : "") ?>/> <?= t('Yes') ?>
                    </label>
                </div>
                <div id="ccm-pagelist-rssDetails" <?= (is_object($rssFeed) ? "" : "style=\"display:none;\"") ?>>
                    <?php if (is_object($rssFeed)) {
                        ?>
                        <?=t('RSS Feed can be found here: <a href="%s" target="_blank">%s</a>', $rssFeed->getFeedURL(), $rssFeed->getFeedURL())?>
                        <?php
                    } else {
                        ?>
                        <div class="form-group">
                            <label class="control-label"><?= t('RSS Feed Title') ?></label>
                            <input class="form-control" id="ccm-pagelist-rssTitle" type="text" name="rssTitle"
                                   value=""/>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= t('RSS Feed Description') ?></label>
                            <textarea name="rssDescription" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= t('RSS Feed Location') ?></label>
                            <div class="input-group">
                                <span class="input-group-addon"><?=URL::to('/rss')?>/</span>
                                <input type="text" name="rssHandle" value="" />
                            </div>
                        </div>
                        <?php
                    } ?>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label"><?= t('Include Page Name') ?></label>
                <div class="radio">
                    <label>
                        <input type="radio" name="includeName"
                               value="0" <?= ($includeName ? "" : "checked=\"checked\"") ?>/> <?= t('No') ?>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="includeName"
                               value="1" <?= ($includeName ? "checked=\"checked\"" : "") ?>/> <?= t('Yes') ?>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label"><?= t('Include Page Description') ?></label>
                <div class="radio">
                    <label>
                        <input type="radio" name="includeDescription"
                               value="0" <?= ($includeDescription ? "" : "checked=\"checked\"") ?>/> <?= t('No') ?>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="includeDescription"
                               value="1" <?= ($includeDescription ? "checked=\"checked\"" : "") ?>/> <?= t('Yes') ?>
                    </label>
                </div>
                <div class="ccm-page-list-truncate-description" <?= ($includeDescription ? "" : "style=\"display:none;\"") ?>>
                    <label class="control-label"><?=t('Display Truncated Description')?></label>
                    <div class="input-group">
            <span class="input-group-addon">
                <input id="ccm-pagelist-truncateSummariesOn" name="truncateSummaries" type="checkbox"
                       value="1" <?= ($truncateSummaries ? "checked=\"checked\"" : "") ?> />
            </span>
                        <input class="form-control" id="ccm-pagelist-truncateChars" <?= ($truncateSummaries ? "" : "disabled=\"disabled\"") ?>
                               type="text" name="truncateChars" size="3" value="<?= intval($truncateChars) ?>" />
            <span class="input-group-addon">
                <?= t('characters') ?>
            </span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label"><?= t('Include Public Page Date') ?></label>
                <div class="radio">
                    <label>
                        <input type="radio" name="includeDate"
                               value="0" <?= ($includeDate ? "" : "checked=\"checked\"") ?>/> <?= t('No') ?>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="includeDate"
                               value="1" <?= ($includeDate ? "checked=\"checked\"" : "") ?>/> <?= t('Yes') ?>
                    </label>
                </div>
                <span class="help-block"><?=t('This is usually the date the page is created. It can be changed from the page attributes panel.')?></span>
            </div>
            <div class="form-group">
                <label class="control-label"><?= t('Display Thumbnail Image') ?></label>
                <div class="radio">
                    <label>
                        <input type="radio" name="displayThumbnail"
                            <?= (!is_object($thumbnailAttribute) ? 'disabled ' : '')?>
                               value="0" <?= ($displayThumbnail ? "" : "checked=\"checked\"") ?>/> <?= t('No') ?>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="displayThumbnail"
                            <?= (!is_object($thumbnailAttribute) ? 'disabled ' : '')?>
                               value="1" <?= ($displayThumbnail ? "checked=\"checked\"" : "") ?>/> <?= t('Yes') ?>
                    </label>
                </div>
                <?php if (!is_object($thumbnailAttribute)) {
                    ?>
                    <div class="help-block">
                        <?=t('You must create an attribute with the \'thumbnail\' handle in order to use this option.')?>
                    </div>
                    <?php
                } ?>
            </div>

            <div class="form-group">
                <label class="control-label"><?= t('Use Different Link than Page Name') ?></label>
                <div class="radio">
                    <label>
                        <input type="radio" name="useButtonForLink"
                               value="0" <?= ($useButtonForLink ? "" : "checked=\"checked\"") ?>/> <?= t('No') ?>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="useButtonForLink"
                               value="1" <?= ($useButtonForLink ? "checked=\"checked\"" : "") ?>/> <?= t('Yes') ?>
                    </label>
                </div>
                <div class="ccm-page-list-button-text" <?= ($useButtonForLink ? "" : "style=\"display:none;\"") ?>>
                    <div class="form-group">
                        <label class="control-label"><?= t('Link Text') ?></label>
                        <input class="form-control" type="text" name="buttonLinkText" value="<?=$buttonLinkText?>" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label"><?= t('Title of Page List') ?></label>
                <input type="text" class="form-control" name="pageListTitle" value="<?=$pageListTitle?>" />
            </div>

            <div class="form-group">
                <label class="control-label"><?= t('Message to Display When No Pages Listed.') ?></label>
                <textarea class="form-control" name="noResultsMessage"><?=$noResultsMessage?></textarea>
            </div>
            <fieldset>


                <div class="loader">
                    <i class="fa fa-cog fa-spin"></i>
                </div>

    </div>
</div>

<div class="ccm-tab-content" id="ccm-tab-content-page-list-preview">
    <div class="preview">
        <div class="render">

        </div>
        <div class="cover"></div>
    </div>
</div>

<style type="text/css">

    div.pagelist-form div.cover {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }

    div.pagelist-form div.render .ccm-page-list-title {
        font-size: 12px;
        font-weight: normal;
    }

    div.pagelist-form label.checkbox,
    div.pagelist-form label.radio {
        font-weight: 300;
    }

</style>
<script type="application/javascript">
    Concrete.event.publish('pagelist.edit.open');
    $(function() {
        $('input[name=topicFilter]').on('change', function() {
            if ($(this).val() == 'related') {
                $('div[data-row=related-topic]').show();
                $('div[data-row=custom-topic]').hide();
            } else if ($(this).val() == 'custom') {
                $('div[data-row=custom-topic]').show();
                $('div[data-row=related-topic]').hide();
            } else {
                $('div[data-row=related-topic]').hide();
                $('div[data-row=custom-topic]').hide();
            }
        });

        var treeViewTemplate = $('.tree-view-template');

        $('select[name=customTopicAttributeKeyHandle]').on('change', function() {
            var toolsURL = '<?php echo Loader::helper('concrete/urls')->getToolsURL('tree/load'); ?>';
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
                    Concrete.event.publish('pagelist.topictree.select');
                }
            });
        });
        $('input[name=topicFilter]:checked').trigger('change');
        if ($('#topicFilterCustom').is(':checked')) {
            $('select[name=customTopicAttributeKeyHandle]').trigger('change');
        }
    });

</script>

