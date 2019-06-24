<?php

defined('C5_EXECUTE') or die('Access Denied.');
$ax = Loader::helper('ajax');
$vs = Loader::helper('validation/strings');
$ve = Loader::helper('validation/error');

if (Loader::helper('validation/numbers')->integer($_POST['cnvID']) && $_POST['cnvID'] > 0) {
    $conversation = Conversation::getByID($_POST['cnvID']);
    if (is_object($conversation)) {
        Loader::element('conversation/count_header', array('conversation' => $conversation));
    }
}
