<?php

defined('C5_EXECUTE') or die('Access Denied.');
$u = new User();

$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
    die(t('Access Denied'));
}

$cnt = Loader::controller('/dashboard/sitemap/search');
$pageList = $cnt->getRequestedSearchResults();
$columns = $cnt->get('columns');
$pages = $pageList->getPage();
$pagination = $pageList->getPagination();

Loader::element('pages/search_results', array('pages' => $pages, 'columns' => $columns,  'pageList' => $pageList, 'pagination' => $pagination));
