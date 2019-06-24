<?php

defined('C5_EXECUTE') or die('Access Denied.');

$subject = t('Error Processing Importer Mail');

$body .= t("There was a problem processing your email.");
$body .= "\n\n";
$body .= t("Subject: %s", $originalSubject);
$body .= "\n\n";
$body .= t("Error: %s", $error);
$body .= "\n\n";
