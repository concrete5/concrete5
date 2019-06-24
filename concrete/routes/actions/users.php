<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var $router \Concrete\Core\Routing\Router
 * Base path: /ccm/system/user
 * Namespace: Concrete\Controller\Backend\
 */
$router->all('/add_group', 'User::addGroup');
$router->all('/remove_group', 'User::removeGroup');
$router->all('/get_json', 'User::getJSON');
