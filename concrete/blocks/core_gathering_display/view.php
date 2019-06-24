<?php

defined('C5_EXECUTE') or die('Access Denied.');
Loader::element('gathering/display', array(
    'gathering' => $gathering,
    'list' => $itemList,
));
