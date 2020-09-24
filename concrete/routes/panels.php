<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var \Concrete\Core\Routing\Router $router
 * Base path: /ccm/system/panels
 * Namespace: Concrete\Controller\Panel\
 */
$router->all('/add', 'Add::view', 'ccm_system_panels_add_all');
$router->all('/add/get_stack_contents', 'Add::getStackContents', 'ccm_system_panels_add_get_stack_contents_all');
$router->all('/add/get_stack_folder_contents', 'Add::getStackFolderContents', 'ccm_system_panels_add_get_stack_folder_contents_all');
$router->all('/dashboard', 'Dashboard::view', 'ccm_system_panels_dashboard_all');
$router->all('/dashboard/add_favorite', 'Dashboard::addFavorite', 'ccm_system_panels_dashboard_add_favorite_all');
$router->all('/dashboard/remove_favorite', 'Dashboard::removeFavorite', 'ccm_system_panels_dashboard_remove_favorite_all');
$router->all('/page/relations', 'PageRelations::view', 'ccm_system_panels_page_relations_all');
$router->all('/page', 'Page::view', 'ccm_system_panels_page_all');
$router->all('/page/attributes', 'Page\Attributes::view', 'ccm_system_panels_page_attributes_all');
$router->all('/page/check_in', 'Page\CheckIn::__construct', 'ccm_system_panels_page_check_in_all');
$router->all('/page/check_in/submit', 'Page\CheckIn::submit', 'ccm_system_panels_page_check_in_submit_all');
$router->all('/page/design', 'Page\Design::view', 'ccm_system_panels_page_design_all');
$router->all('/page/design/customize/reset_page_customizations', 'Page\Design\Customize::reset_page_customizations', 'ccm_system_panels_page_design_customize_reset_page_customizations_all');
$router->all('/page/design/customize/apply_to_page/{pThemeID}', 'Page\Design\Customize::apply_to_page', 'ccm_system_panels_page_design_customize_apply_to_page_all');
$router->all('/page/design/customize/apply_to_site/{pThemeID}', 'Page\Design\Customize::apply_to_site', 'ccm_system_panels_page_design_customize_apply_to_site_pall');
$router->all('/page/design/customize/preview/{pThemeID}', 'Page\Design\Customize::preview', 'ccm_system_panels_page_design_customize_preview_all');
$router->all('/page/design/customize/reset_site_customizations/{pThemeID}', 'Page\Design\Customize::reset_site_customizations', 'ccm_system_panels_page_design_customize_reset_site_customizations_all');
$router->all('/page/design/customize/{pThemeID}', 'Page\Design\Customize::view', 'ccm_system_panels_page_design_customize_all');
$router->all('/page/design/preview_contents', 'Page\Design::preview_contents', 'ccm_system_panels_page_design_preview_contents_all');
$router->all('/page/design/submit', 'Page\Design::submit', 'ccm_system_panels_page_design_submit_all');
$router->all('/page/preview_as_user', 'Page\PreviewAsUser::view', 'ccm_system_panels_page_preview_as_user_all');
$router->all('/page/preview_as_user/preview', 'Page\PreviewAsUser::frame_page', 'ccm_system_panels_page_preview_as_user_preview_all');
$router->all('/page/preview_as_user/render', 'Page\PreviewAsUser::preview_page', 'ccm_system_panels_page_preview_as_user_render_all');
$router->all('/page/versions', 'Page\Versions::view', 'ccm_system_panels_page_versions_all');
$router->all('/page/versions/get_json', 'Page\Versions::get_json', 'ccm_system_panels_page_versions_get_json_all');
$router->all('/page/versions/duplicate', 'Page\Versions::duplicate', 'ccm_system_panels_page_versions_duplicate_all');
$router->all('/page/versions/new_page', 'Page\Versions::new_page', 'ccm_system_panels_page_versions_new_page_all');
$router->all('/page/versions/delete', 'Page\Versions::delete', 'ccm_system_panels_page_versions_delete_all');
$router->all('/page/versions/approve', 'Page\Versions::approve', 'ccm_system_panels_page_versions_approve_all');
$router->all('/page/versions/unapprove', 'Page\Versions::unapprove', 'ccm_system_panels_page_versions_unapprove_all');
$router->all('/page/devices', 'Page\Devices::view', 'ccm_system_panels_page_devices_all');
$router->all('/page/devices/preview', 'Page\Devices::preview', 'ccm_system_panels_page_devices_preview_all');
$router->get('/sitemap', 'Sitemap::view', 'ccm_system_panels_sitemap_get');
$router->get('/help', 'Help::view', 'ccm_system_panels_help_get');
