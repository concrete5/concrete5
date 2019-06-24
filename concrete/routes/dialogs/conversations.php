<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var $router \Concrete\Core\Routing\Router
 * Base path: /ccm/system/dialogs/conversation
 * Namespace: Concrete\Controller\Dialog\Conversation\
 */
$router->all('/subscribe/{cnvID}', 'Subscribe::view');
$router->all('/subscribe/subscribe/{cnvID}', 'Subscribe::subscribe');
$router->all('/subscribe/unsubscribe/{cnvID}', 'Subscribe::unsubscribe');
