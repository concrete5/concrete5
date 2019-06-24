<?php

defined('C5_EXECUTE') or die('Access Denied.');
$valt = Loader::helper('validation/token');
if ($valt->validate('quick_page_select_' . $_REQUEST['key'], $_REQUEST['token'])) {
    $u = new User();
    $db = Loader::db();
    $pl = new PageList();
    if ($_GET['term'] != '') {
        $pl->filterByName($_GET['term']);
    }
    $pages = $pl->getPagination();
    $pageNames = array();
    foreach ($pages as $c) {
        $obj = new stdClass();
        $obj->label = $c->getCollectionName();
        $obj->value = $c->getCollectionID();
        $pageNames[] = $obj;
    }
    $jh = Loader::helper('json');
    echo $jh->encode($pageNames);
}
