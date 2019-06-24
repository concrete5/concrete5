<?php

defined('C5_EXECUTE') or die('Access Denied.');

$subject = $siteName . " – " . t("You Won a Badge!");
$body = t("Dear %s,

You've just won the %s badge!

View your profile here:
%s

", $uDisplayName, $badgeName, $uProfileURL);
