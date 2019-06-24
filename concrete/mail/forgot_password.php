<?php

defined('C5_EXECUTE') or die('Access Denied.');

$subject = t("Forgot Password");
$body = t("

Dear %s,

You have requested a new password for the site %s 

Your username is: %s

You may change your password at the following address:

%s

Thanks for browsing the site!

", $uName, $siteName, $uName, $changePassURL);
