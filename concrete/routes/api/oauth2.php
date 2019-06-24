<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Api\OAuth\Controller as OAuthController;

/**
 * @var $router \Concrete\Core\Routing\Router
 * @var $app \Concrete\Core\Application\Application
 */

$router->post('/oauth/2.0/token', [OAuthController::class, 'token']);
$router->all('/oauth/2.0/authorize', [OAuthController::class, 'authorize']);
