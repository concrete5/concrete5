<?php

defined('C5_EXECUTE') or die('Access Denied.');
$env = Environment::get();
$file = $env->getPath(DIRNAME_TOOLS . '/permissions/categories/tree_node.php');
require $file;
exit;
