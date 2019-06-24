<?php

defined('C5_EXECUTE') or die('Access Denied.');

/*
 * ----------------------------------------------------------------------------
 * Ensure we're not accessing this file directly.
 * ----------------------------------------------------------------------------
 */
if (basename($_SERVER['PHP_SELF']) == DISPATCHER_FILENAME_CORE) {
    die("Access Denied.");
}

/*
 * ----------------------------------------------------------------------------
 * Handle text encoding.
 * ----------------------------------------------------------------------------
 */
\Patchwork\Utf8\Bootup::initAll();

/*
 * ----------------------------------------------------------------------------
 * Instantiate concrete5.
 * ----------------------------------------------------------------------------
 */
/** @var \Concrete\Core\Application\Application $app */
$app = require DIR_APPLICATION . '/bootstrap/start.php';
$app->instance('app', $app);

// Bind fully application qualified class names
$app->instance('Concrete\Core\Application\Application', $app);
$app->instance('Illuminate\Container\Container', $app);

// Boot the runtime
$app->getRuntime()->boot();

return $app;
