<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var $router \Concrete\Core\Routing\Router
 * Base path: /ccm/system/dialogs/block
 * Namespace: Concrete\Controller\Dialog\Block\
 */
$router->all('/aliasing/', 'Aliasing::view');
$router->all('/aliasing/submit', 'Aliasing::submit');
$router->all('/edit/', 'Edit::view');
$router->all('/edit/submit/', 'Edit::submit');
$router->all('/cache/', 'Cache::view');
$router->all('/cache/submit', 'Cache::submit');
$router->all('/design/', 'Design::view');
$router->all('/design/reset', 'Design::reset');
$router->all('/design/submit', 'Design::submit');
$router->all('/permissions/detail/', 'Permissions::viewDetail');
$router->all('/permissions/guest_access/', 'Permissions\GuestAccess::__construct');
$router->all('/permissions/list/', 'Permissions::viewList');
$router->all('/delete/', 'Delete::view');
$router->all('/delete/submit/', 'Delete::submit');
$router->all('/delete/submit_all/', 'Delete::submit_all');

