<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var $router \Concrete\Core\Routing\Router
 * Base path: /ccm/system/dialogs/page
 * Namespace: Concrete\Controller\Dialog\Page\
 */
$router->all('/add', 'Add::view');
$router->all('/add_block', 'AddBlock::view');
$router->all('/add_block/submit', 'AddBlock::submit');
$router->all('/add_block_list', 'AddBlockList::view');
$router->all('/add_external', 'AddExternal::view');
$router->all('/add_external/submit', 'AddExternal::submit');
$router->all('/add/compose/{ptID}/{cParentID}', 'Add\Compose::view');
$router->all('/add/compose/submit', 'Add\Compose::submit');
$router->all('/attributes', 'Attributes::view');
$router->all('/bulk/properties', 'Bulk\Properties::view');
$router->all('/bulk/properties/clear_attribute', 'Bulk\Properties::clearAttribute');
$router->all('/bulk/properties/update_attribute', 'Bulk\Properties::updateAttribute');
$router->all('/clipboard', 'Clipboard::view');
$router->all('/delete', 'Delete::view');
$router->all('/delete/submit', 'Delete::submit');
$router->all('/delete_alias', 'DeleteAlias::view');
$router->all('/delete_alias/submit', 'DeleteAlias::submit');
$router->all('/delete_from_sitemap', 'Delete::viewFromSitemap');
$router->all('/design', 'Design::view');
$router->all('/design/submit', 'Design::submit');
$router->all('/design/css', 'Design\Css::view');
$router->all('/design/css/submit', 'Design\Css::submit');
$router->all('/edit_external', 'EditExternal::view');
$router->all('/edit_external/submit', 'EditExternal::submit');
$router->all('/location', 'Location::view');
$router->all('/search', 'Search::view');
$router->all('/seo', 'Seo::view');
$router->all('/sitemap_selector', 'SitemapSelector::view');
$router->all('/drag_request', 'DragRequest::view');
$router->all('/drag_request/submit', 'DragRequest::submit');
$router->all('/drag_request/copy_all', 'DragRequest::doCopyAll');
$router->all('/advanced_search', 'AdvancedSearch::view');
$router->all('/advanced_search/add_field', 'AdvancedSearch::addField');
$router->all('/advanced_search/submit', 'AdvancedSearch::submit');
$router->all('/advanced_search/save_preset', 'AdvancedSearch::savePreset');

$router->all('/advanced_search/preset/edit', 'Preset\Edit::view');
$router->all('/advanced_search/preset/edit/edit_search_preset', 'Preset\Edit::edit_search_preset');
$router->all('/advanced_search/preset/delete', 'Preset\Delete::view');
$router->all('/advanced_search/preset/delete/remove_search_preset', 'Preset\Delete::remove_search_preset');