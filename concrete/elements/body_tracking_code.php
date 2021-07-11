<?php
use Concrete\Core\Support\Facade\Application;

defined('C5_EXECUTE') or die("Access Denied.");

$disableTrackingCode = $disableTrackingCode ?: null;
$app = Application::getFacadeApplication();
$site = $app->make('site')->getSite();
$config = $site->getConfigRepository();
if (empty($disableTrackingCode)) {
    echo $config->get('seo.tracking.code.body');
}
