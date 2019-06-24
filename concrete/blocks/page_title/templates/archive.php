<?php
defined('C5_EXECUTE') or die('Access Denied.');

if (isset($currentTopic) && is_object($currentTopic)) {
    $title = t('Topic Archives: %s', $currentTopic->getTreeNodeDisplayName());
}
if (isset($title)) {
    ?><h1 class="page-title"><?=h($title)?></h1><?php
}
