<?php

defined('C5_EXECUTE') or die('Access Denied.');

if ($_REQUEST['cnvID'] > 0) {
    $conversation = Conversation::getByID($_REQUEST['cnvID']);
}

$csp = new Permissions($conversation);

if ($csp->canEditConversationPermissions()) {
    Loader::element('permission/details/conversation', array("conversation" => $conversation));
}
