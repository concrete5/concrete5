<?php  defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-block-topic-list-wrapper">

    <div class="ccm-block-topic-list-header">
        <h5><?php echo h($title); ?></h5>
    </div>

    <?php
    if ($mode == 'S' && is_object($tree)) {
        $node = $tree->getRootTreeNodeObject();
        $node->populateChildren();
        if (is_object($node)) {
            if (!isset($selectedTopicID)) {
                $selectedTopicID = null;
            }
            $walk = function ($node) use (&$walk, &$view, $selectedTopicID) {
                ?><ul class="ccm-block-topic-list-list"><?php
                foreach ($node->getChildNodes() as $topic) {
                    if ($topic instanceof \Concrete\Core\Tree\Node\Type\Category) {
                        ?><li><?php echo $topic->getTreeNodeDisplayName(); ?>
                        <?php
                    } else {
                        ?><li><a href="<?php echo $view->controller->getTopicLink($topic); ?>" <?php
                        if (isset($selectedTopicID) && $selectedTopicID == $topic->getTreeNodeID()) {
                            ?> class="ccm-block-topic-list-topic-selected"<?php
                        }
                        ?>><?php echo $topic->getTreeNodeDisplayName(); ?></a><?php
                    }
                    if (count($topic->getChildNodes())) {
                        $walk($topic);
                    } ?>
                    </li>
                    <?php
                }
                ?></ul><?php
            };
            $walk($node);
        }
    }

    if ($mode == 'P') {
        if (isset($topics) && count($topics)) {
            ?><ul class="ccm-block-topic-list-page-topics"><?php
            foreach ($topics as $topic) {
                ?><li><a href="<?php echo $view->controller->getTopicLink($topic); ?>"><?php echo $topic->getTreeNodeDisplayName(); ?></a></li><?php
            }
            ?></ul><?php
        } else {
            echo t('No topics.');
        }
    }
    ?>

</div>
