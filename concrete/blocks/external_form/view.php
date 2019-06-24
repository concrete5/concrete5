<?php
    defined('C5_EXECUTE') or die('Access Denied.');
$path = $controller->getExternalFormFilenamePath();
if ($path) {
    include $path;
}
