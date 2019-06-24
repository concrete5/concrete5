<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var $router \Concrete\Core\Routing\Router
 * Base path: /ccm/system/page
 * Namespace: Concrete\Controller\Backend\
 */
$router->all('/arrange_blocks/', 'Page\ArrangeBlocks::arrange');
$router->all('/check_in/{cID}/{token}', 'Page::exitEditMode');
$router->all('/create/{ptID}', 'Page::create');
$router->all('/create/{ptID}/{parentID}', 'Page::create');
$router->all('/get_json', 'Page::getJSON');
$router->all('/multilingual/assign', 'Page\Multilingual::assign');
$router->all('/multilingual/create_new', 'Page\Multilingual::create_new');
$router->all('/multilingual/ignore', 'Page\Multilingual::ignore');
$router->all('/multilingual/unmap', 'Page\Multilingual::unmap');
$router->all('/select_sitemap', 'Page\SitemapSelector::view');
$router->all('/sitemap_data', 'Page\SitemapData::view');
$router->all('/sitemap_overlay', '\Concrete\Controller\Element\Dashboard\Sitemap\SitemapOverlay::view');