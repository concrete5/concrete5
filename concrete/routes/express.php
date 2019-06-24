<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var $router \Concrete\Core\Routing\Router
 */

$router->all('/ccm/system/dialogs/express/association/reorder', '\Concrete\Controller\Dialog\Express\Association\Reorder::view');
$router->all('/ccm/system/dialogs/express/association/reorder/submit', '\Concrete\Controller\Dialog\Express\Association\Reorder::submit');
$router->all('/ccm/system/dialogs/express/entry/search', '\Concrete\Controller\Dialog\Express\Search::entries');
$router->all('/ccm/system/search/express/entries/submit/{entityID}', '\Concrete\Controller\Search\Express\Entries::submit');
$router->all('/ccm/system/express/entry/get_json', '\Concrete\Controller\Backend\Express\Entry::getJSON');
$router->all('/ccm/system/dialogs/express/advanced_search/', '\Concrete\Controller\Dialog\Express\AdvancedSearch::view');
$router->all('/ccm/system/dialogs/express/advanced_search/add_field/', '\Concrete\Controller\Dialog\Express\AdvancedSearch::addField');
$router->all('/ccm/system/dialogs/express/advanced_search/submit', '\Concrete\Controller\Dialog\Express\AdvancedSearch::submit');


$router->all('/ccm/system/dialogs/express/advanced_search/save_preset', '\Concrete\Controller\Dialog\Express\AdvancedSearch::savePreset');
$router->all('/ccm/system/dialogs/express/advanced_search/preset/edit', '\Concrete\Controller\Dialog\Express\Preset\Edit::view');
$router->all('/ccm/system/dialogs/express/advanced_search/preset/edit/edit_search_preset', '\Concrete\Controller\Dialog\Express\Preset\Edit::edit_search_preset');
$router->all('/ccm/system/dialogs/express/advanced_search/preset/delete', '\Concrete\Controller\Dialog\Express\Preset\Delete::view');
$router->all('/ccm/system/dialogs/express/advanced_search/preset/delete/remove_search_preset', '\Concrete\Controller\Dialog\Express\Preset\Delete::remove_search_preset');
$router->all('/ccm/system/search/express/preset/{entityID}/{presetID}', '\Concrete\Controller\Search\Express::expressSearchPreset');