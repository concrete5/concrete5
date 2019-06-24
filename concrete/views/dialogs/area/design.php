<?php

defined('C5_EXECUTE') or die('Access Denied.');

$ag = \Concrete\Core\Http\ResponseAssetGroup::get();
$ag->requireAsset('core/style-customizer');

$pt = $c->getCollectionThemeObject();

$areaClasses = $pt->getThemeAreaClasses();
$customClasses = array();

// Use the area handle as the key to map against area classes
$areaHandle = $a->getAreaHandle();

// If its a SubArea, find the parent handle and use that
if ($a instanceof \Concrete\Core\Area\SubArea) {
    $areaHandle = \Concrete\Core\Area\Area::getAreaHandleFromID($a->getAreaParentID());
}

if (isset($areaClasses[$areaHandle])) {
    $customClasses = $areaClasses[$areaHandle];
}

$gf = $pt->getThemeGridFrameworkObject();

Loader::element("custom_style", array(
    'saveAction' => $controller->action('submit'),
    'resetAction' => $controller->action('reset'),
    'customClasses' => $customClasses,
    'gf' => $gf,
    'style' => $c->getAreaCustomStyle($a, true),
));
