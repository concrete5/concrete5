<?php

defined('C5_EXECUTE') or die('Access Denied.');

$subject = $siteName . " " . t("Workflow Notification");
$body = t("Dear %s,

%s

You can review, approve or deny all pending actions from here:

%s
", $uName, $message, URL::to('/dashboard/welcome/me'));