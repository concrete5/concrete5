<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var $router \Concrete\Core\Routing\Router
 * Base path: /ccm/system/panels
 * Namespace: Concrete\Controller\Panel\
 */
$router->all('/add', 'Add::view');
$router->all('/add/get_stack_contents', 'Add::getStackContents');
$router->all('/dashboard', 'Dashboard::view');
$router->all('/dashboard/add_favorite', 'Dashboard::addFavorite');
$router->all('/dashboard/remove_favorite', 'Dashboard::removeFavorite');
$router->all('/page/relations', 'PageRelations::view');
$router->all('/page', 'Page::view');
$router->all('/page/attributes', 'Page\Attributes::view');
$router->all('/page/check_in', 'Page\CheckIn::__construct');
$router->all('/page/check_in/submit', 'Page\CheckIn::submit');
$router->all('/page/design', 'Page\Design::view');
$router->all('/page/design/customize/reset_page_customizations', 'Page\Design\Customize::reset_page_customizations');
$router->all('/page/design/customize/apply_to_page/{pThemeID}', 'Page\Design\Customize::apply_to_page');
$router->all('/page/design/customize/apply_to_site/{pThemeID}', 'Page\Design\Customize::apply_to_site');
$router->all('/page/design/customize/preview/{pThemeID}', 'Page\Design\Customize::preview');
$router->all('/page/design/customize/reset_site_customizations/{pThemeID}', 'Page\Design\Customize::reset_site_customizations');
$router->all('/page/design/customize/{pThemeID}', 'Page\Design\Customize::view');
$router->all('/page/design/preview_contents', 'Page\Design::preview_contents');
$router->all('/page/design/submit', 'Page\Design::submit');
$router->all('/page/preview_as_user', 'Page\PreviewAsUser::view');
$router->all('/page/preview_as_user/preview', 'Page\PreviewAsUser::frame_page');
$router->all('/page/preview_as_user/render', 'Page\PreviewAsUser::preview_page');
$router->all('/page/versions', 'Page\Versions::view');
$router->all('/page/versions/get_json', 'Page\Versions::get_json');
$router->all('/page/versions/duplicate', 'Page\Versions::duplicate');
$router->all('/page/versions/new_page', 'Page\Versions::new_page');
$router->all('/page/versions/delete', 'Page\Versions::delete');
$router->all('/page/versions/approve', 'Page\Versions::approve');
$router->all('/page/versions/unapprove', 'Page\Versions::unapprove');
$router->all('/page/devices', 'Page\Devices::view');
$router->all('/page/devices/preview', 'Page\Devices::preview');
$router->get('/sitemap', 'Sitemap::view');