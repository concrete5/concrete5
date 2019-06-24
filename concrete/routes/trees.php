<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var $router \Concrete\Core\Routing\Router
 */

$router->all('/ccm/system/tree/load', '\Concrete\Controller\Backend\Tree::load');
$router->all('/ccm/system/tree/node/load', '\Concrete\Controller\Backend\Tree\Node::load');
$router->all('/ccm/system/tree/node/load_starting', '\Concrete\Controller\Backend\Tree\Node::load_starting');
$router->all('/ccm/system/tree/node/drag_request', '\Concrete\Controller\Backend\Tree\Node\DragRequest::execute');
$router->all('/ccm/system/tree/node/duplicate', '\Concrete\Controller\Backend\Tree\Node\Duplicate::execute');
$router->all('/ccm/system/tree/node/update_order', '\Concrete\Controller\Backend\Tree\Node\DragRequest::updateChildren');

$router->all('/ccm/system/dialogs/tree/node/add/category', '\Concrete\Controller\Dialog\Tree\Node\Category\Add::view');
$router->all('/ccm/system/dialogs/tree/node/add/category/add_category_node', '\Concrete\Controller\Dialog\Tree\Node\Category\Add::add_category_node');

$router->all('/ccm/system/dialogs/tree/node/add/topic', '\Concrete\Controller\Dialog\Tree\Node\Topic\Add::view');
$router->all('/ccm/system/dialogs/tree/node/add/topic/add_topic_node', '\Concrete\Controller\Dialog\Tree\Node\Topic\Add::add_topic_node');

$router->all('/ccm/system/dialogs/tree/node/edit/topic', '\Concrete\Controller\Dialog\Tree\Node\Topic\Edit::view');
$router->all('/ccm/system/dialogs/tree/node/edit/topic/update_topic_node', '\Concrete\Controller\Dialog\Tree\Node\Topic\Edit::update_topic_node');

$router->all('/ccm/system/dialogs/tree/node/edit/category', '\Concrete\Controller\Dialog\Tree\Node\Category\Edit::view');
$router->all('/ccm/system/dialogs/tree/node/edit/category/update_category_node', '\Concrete\Controller\Dialog\Tree\Node\Category\Edit::update_category_node');

$router->all('/ccm/system/dialogs/tree/node/delete', '\Concrete\Controller\Dialog\Tree\Node\Delete::view');
$router->all('/ccm/system/dialogs/tree/node/delete/remove_tree_node', '\Concrete\Controller\Dialog\Tree\Node\Delete::remove_tree_node');
$router->all('/ccm/system/dialogs/tree/node/permissions', '\Concrete\Controller\Dialog\Tree\Node\Permissions::view');
$router->all('/ccm/system/dialogs/tree/node/category/delete_express', '\Concrete\Controller\Dialog\Tree\Node\Category\DeleteExpress::view');
$router->all('/ccm/system/dialogs/tree/node/category/delete_express/remove_tree_node', '\Concrete\Controller\Dialog\Tree\Node\Category\DeleteExpress::remove_tree_node');
