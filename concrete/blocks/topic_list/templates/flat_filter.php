<?php  defined('C5_EXECUTE') or die('Access Denied.');
if (!isset($selectedTopicID)) {
    $selectedTopicID = null;
}
?>

<div class="ccm-block-topic-list-flat-filter">
<?php
if (is_object($tree)) {
    $node = $tree->getRootTreeNodeObject();
    if (is_object($node)) {
        $node->populateDirectChildrenOnly();
        ?>
        <ol class="breadcrumb">
            <li><a href="<?=$view->controller->getTopicLink()?>"
                <?php if (!$selectedTopicID) {
    ?>class="ccm-block-topic-list-topic-selected active"<?php 
}
        ?>><?=t('All')?></a></li>

        <?php foreach ($node->getChildNodes() as $child) {
    ?>
            <li><a href="<?=$view->controller->getTopicLink($child)?>"
                    <?php if (isset($selectedTopicID) && $selectedTopicID == $child->getTreeNodeID()) {
    ?>
                        class="ccm-block-topic-list-topic-selected active"
                    <?php 
}
    ?> ><?=$child->getTreeNodeDisplayName()?></a></li>
        <?php 
}
        ?>
        </ol>
    <?php 
    }
    ?>
    </div>
<?php 
} ?>