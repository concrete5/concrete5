<?php

defined('C5_EXECUTE') or die('Access Denied.');

$content = $controller->getContent();
$forbiddenTags = array('iframe', 'script', 'object', 'embed');
foreach ($forbiddenTags as $forbiddenTag) {
    $content = preg_replace("~<".$forbiddenTag."[^>]*>(.*)</".$forbiddenTag.">~i", "$2", $content);
    $content = preg_replace("~<".$forbiddenTag."[^>]*>~i", "", $content);
}
echo ''.$content;
