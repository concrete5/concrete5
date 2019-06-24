<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var $router \Concrete\Core\Routing\Router
 */
$router->get('/rss/{identifier}', 'Concrete\Controller\Feed::output')
    ->setName('rss');
$router->get('/ccm/calendar/feed/{identifier}', 'Concrete\Controller\CalendarFeed::view')
    ->setName('calendar_rss');
