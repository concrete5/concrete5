#!/usr/bin/env php
<?php
define('FILE_PERMISSIONS_MODE', 0777);
define('DIRECTORY_PERMISSIONS_MODE', 0777);
define('C5_ENVIRONMENT_ONLY', true);

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
ini_set('display_errors', 1);
define('C5_EXECUTE', true);
define('DIR_BASE', substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), '/')) . '/web');

$corePath = DIR_BASE . '/concrete';

require $corePath . '/bootstrap/configure.php';
require $corePath . '/bootstrap/autoload.php';
$cms = require $corePath . '/bootstrap/start.php';

$generator = new \Concrete\Core\Support\Symbol\SymbolGenerator();
$symbols = $generator->render();

file_put_contents(DIR_BASE . '/concrete/core/__IDE_SYMBOLS__.php', $symbols);
die("Generation Complete.\n");
