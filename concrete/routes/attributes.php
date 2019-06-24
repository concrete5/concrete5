<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var $router \Concrete\Core\Routing\Router
 */

$router->all('/ccm/system/attribute/action/{action}', 'Concrete\Controller\Backend\Attribute\Action::dispatch')
    ->setName('attribute_action')
    ->setRequirements(['action' => '.+']);
$router->all('/ccm/system/attributes/attribute_sort/set', 'Concrete\Controller\Backend\Attributes::sortInSet');
