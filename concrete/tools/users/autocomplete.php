<?php

defined('C5_EXECUTE') or die('Access Denied.');
$valt = Loader::helper('validation/token');
if ($valt->validate('quick_user_select_' . $_REQUEST['key'], $_REQUEST['token'])) {
    $u = new User();
    $db = Loader::db();
    $userList = new UserList();
    $userList->filterByFuzzyUserName($_GET['term']);
    $userList->sortByUserName();
    $userList->setItemsPerPage(7);
    $pagination = $userList->getPagination();
    $users = $pagination->getCurrentPageResults();
    $userNames = array();
    foreach ($users as $ui) {
        $userNames[] = array('label' => $ui->getUserDisplayName(), 'value' => $ui->getUserID());
    }
    $jh = Loader::helper('json');
    echo $jh->encode($userNames);
}
