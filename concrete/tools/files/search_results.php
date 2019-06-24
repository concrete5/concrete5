<?php

defined('C5_EXECUTE') or die('Access Denied.');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
    die(t("Unable to access the file manager."));
}

$u = new User();

$cnt = Loader::controller('/dashboard/files/search');
$fileList = $cnt->getRequestedSearchResults();

$files = $fileList->getPage();
$pagination = $fileList->getPagination();
$searchType = Loader::helper('text')->entities($_REQUEST['searchType']);
$searchRequest = $cnt->get('searchRequest');
$columns = $cnt->get('columns');

Loader::element('files/search_results', array('files' => $files, 'columns' => $columns, 'searchType' => $searchType, 'searchRequest' => $searchRequest,  'fileList' => $fileList, 'pagination' => $pagination));
