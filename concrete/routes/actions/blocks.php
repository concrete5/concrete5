<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var $router \Concrete\Core\Routing\Router
 * Base path: /ccm/system/block
 * Namespace: Concrete\Controller\Backend\
 */
$router->all('/render/', 'Block::render');
$router->all('/action/add/{cID}/{arHandle}/{btID}/{action}', 'Block\Action::add')
    ->setRequirements(['action' => '.+']);
$router->all('/action/edit/{cID}/{arHandle}/{bID}/{action}', 'Block\Action::edit')
    ->setRequirements(['action' => '.+']);
$router->all('/action/add_composer/{ptComposerFormLayoutSetControlID}/{action}', 'Block\Action::add_composer');
$router->all('/action/edit_composer/{cID}/{arHandle}/{ptComposerFormLayoutSetControlID}/{action}', 'Block\Action::edit_composer');

