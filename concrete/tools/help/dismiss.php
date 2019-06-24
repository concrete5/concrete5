<?php

defined('C5_EXECUTE') or die('Access Denied.');
$u = new User();
$h = Loader::helper('concrete/ui/help');
if ($u->isRegistered() && Loader::helper('validation/token')->validate()) {
    if ($_POST['action'] == 'all') {
        // we don't want to hear about it anymore
        $h->disableAllHelpNotifications($u);
    } else {
        $h->disableThisHelpNotification($u, $_POST['type'], $_POST['identifier']);
    }
}
