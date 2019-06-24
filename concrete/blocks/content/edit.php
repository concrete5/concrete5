<?php

defined('C5_EXECUTE') or die('Access Denied.');

echo Core::make("editor")->outputPageInlineEditor('content', $controller->getContentEditMode());
