<?php

defined('C5_EXECUTE') or die('Access Denied.');
$subject = t('New Message on Conversation: %s', $title);
$body = t("
%s has posted a new message to the conversation \"%s\":

%s

You can view the whole conversation at

%s

", $poster, $title, $body, $link);
