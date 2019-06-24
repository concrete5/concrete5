<?php

defined('C5_EXECUTE') or die('Access Denied.');

$editor = \Core::make('editor/image/core');
echo $editor->getView($fv)->render();
