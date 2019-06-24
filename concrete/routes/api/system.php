<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var $router \Concrete\Core\Routing\Router
 * @var $app \Concrete\Core\Application\Application
 */

use Concrete\Core\System\Info;
use Concrete\Core\System\InfoTransformer;
use League\Fractal\Resource\Item;

$router->get('/system/info', function() {
    return new Item(new Info(), new InfoTransformer());
})->setScopes('system:info:read');
