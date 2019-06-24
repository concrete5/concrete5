<?php

defined('C5_EXECUTE') or die('Access Denied.');
$tp = new TaskPermission();
if ($tp->canAccessTaskPermissions()) {
    Loader::element('permission/details/block_type');
}
