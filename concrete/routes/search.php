<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var $router \Concrete\Core\Routing\Router
 */

$router->all('/ccm/system/search/files/basic', '\Concrete\Controller\Search\Files::searchBasic');
$router->all('/ccm/system/search/files/current', '\Concrete\Controller\Search\Files::searchCurrent');
$router->all('/ccm/system/search/files/preset/{presetID}', '\Concrete\Controller\Search\Files::searchPreset');
$router->all('/ccm/system/search/files/clear', '\Concrete\Controller\Search\Files::clearSearch');
$router->all('/ccm/system/search/pages/basic', '\Concrete\Controller\Search\Pages::searchBasic');
$router->all('/ccm/system/search/pages/current', '\Concrete\Controller\Search\Pages::searchCurrent');
$router->all('/ccm/system/search/pages/preset/{presetID}', '\Concrete\Controller\Search\Pages::searchPreset');
$router->all('/ccm/system/search/pages/clear', '\Concrete\Controller\Search\Pages::clearSearch');

$router->all('/ccm/system/search/users/basic', '\Concrete\Controller\Search\Users::searchBasic');
$router->all('/ccm/system/search/users/current', '\Concrete\Controller\Search\Users::searchCurrent');
$router->all('/ccm/system/search/users/preset/{presetID}', '\Concrete\Controller\Search\Users::searchPreset');
$router->all('/ccm/system/search/users/clear', '\Concrete\Controller\Search\Users::clearSearch');

$router->all('/ccm/system/search/express/basic', '\Concrete\Controller\Search\Express::searchBasic');
$router->all('/ccm/system/search/express/current', '\Concrete\Controller\Search\Express::searchCurrent');
$router->all('/ccm/system/search/express/clear', '\Concrete\Controller\Search\Express::clearSearch');

$router->all('/ccm/system/search/groups/submit', '\Concrete\Controller\Search\Groups::submit');

