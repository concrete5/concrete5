<?php

defined('C5_EXECUTE') or die('Access Denied.');

$subject = $siteName . " " . t("Notification - Spam Detected");
$body = t("

Someone has attempted to send you spam through your website. Details below:

%s", $content);
