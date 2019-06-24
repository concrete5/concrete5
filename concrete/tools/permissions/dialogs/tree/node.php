<?php

defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Core\Tree\Node\Node as TreeNode;

if ($_REQUEST['treeNodeID'] > 0) {
    $node = TreeNode::getByID($_REQUEST['treeNodeID']);
    $np = new Permissions($node);
    if ($np->canEditTreeNodePermissions()) {
        Loader::element('permission/details/tree/node', array("node" => $node));
    }
}
