<?php

defined('C5_EXECUTE') or die('Access Denied.');

$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
    die(t('Access Denied'));
}

$cnt = Loader::controller('/dashboard/sitemap/search');
$pageList = $cnt->getRequestedSearchResults();
$columns = $cnt->get('columns');
$pages = $pageList->getPage();
$pagination = $pageList->getPagination();
if (!isset($sitemap_select_mode)) {
    $sitemap_select_mode = $_REQUEST['sitemap_select_mode'];
    if (!$_REQUEST['sitemap_select_mode']) {
        $sitemap_select_mode = 'move_copy_delete';
    }
}
$sitemap_select_callback = $_REQUEST['callback'];
if (!$_REQUEST['callback']) {
    $sitemap_select_callback = 'ccm_selectSitemapNode';
}
$searchInstance = $page . time();
$searchRequest = $pageList->getSearchRequest();

Loader::element('pages/search_form_advanced', array('columns' => $columns, 'searchRequest' => $searchRequest));
Loader::element('pages/search_results', array('columns' => $columns, 'pages' => $pages, 'pageList' => $pageList, 'pagination' => $pagination));
