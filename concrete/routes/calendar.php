<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var $router \Concrete\Core\Routing\Router
 */
$router->all('/ccm/calendar/dialogs/event/edit', '\Concrete\Controller\Dialog\Event\Edit::edit');
$router->all('/ccm/calendar/dialogs/event/add', '\Concrete\Controller\Dialog\Event\Edit::add');
$router->all('/ccm/calendar/dialogs/event/add/save', '\Concrete\Controller\Dialog\Event\Edit::addEvent');
$router->all('/ccm/calendar/dialogs/event/edit/save', '\Concrete\Controller\Dialog\Event\Edit::updateEvent');
$router->all('/ccm/calendar/dialogs/event/duplicate', '\Concrete\Controller\Dialog\Event\Duplicate::view');
$router->all('/ccm/calendar/dialogs/event/duplicate/submit', '\Concrete\Controller\Dialog\Event\Duplicate::submit');
$router->all('/ccm/calendar/dialogs/event/delete', '\Concrete\Controller\Dialog\Event\Delete::view');
$router->all('/ccm/calendar/dialogs/event/delete_occurrence', '\Concrete\Controller\Dialog\Event\DeleteOccurrence::view');
$router->all('/ccm/calendar/dialogs/event/delete/submit', '\Concrete\Controller\Dialog\Event\Delete::submit');
$router->all('/ccm/calendar/dialogs/event/delete_occurrence/submit', '\Concrete\Controller\Dialog\Event\DeleteOccurrence::submit');
$router->all('/ccm/calendar/dialogs/event/versions', '\Concrete\Controller\Dialog\Event\Versions::view');
$router->all('/ccm/calendar/dialogs/event/version/view', '\Concrete\Controller\Dialog\Event\ViewVersion::view');
$router->all('/ccm/calendar/event/version/delete', '\Concrete\Controller\Event\EventVersion::delete');
$router->all('/ccm/calendar/event/version/approve', '\Concrete\Controller\Event\EventVersion::approve');
$router->all('/ccm/calendar/event/version/unapprove_all', '\Concrete\Controller\Event\Event::unapprove');
$router->get('/ccm/calendar/view_event/{bID}/{occurrence_id}', '\Concrete\Controller\Dialog\Frontend\Event::view')
    ->setName('view_event_occurrence')
    ->setRequirements(['occurrence_id' => '[0-9]+']);
$router->all('/ccm/calendar/dialogs/event/occurrence', '\Concrete\Controller\Dialog\EventOccurrence::view');
$router->all('/ccm/calendar/dialogs/choose_event', '\Concrete\Controller\Dialog\ChooseEvent::view');
$router->all('/ccm/calendar/dialogs/choose_event/get_events', '\Concrete\Controller\Dialog\ChooseEvent::getEvents');
$router->all('/ccm/calendar/event/get_json', '\Concrete\Controller\Event\Event::getJSON');
$router->all('/ccm/calendar/dialogs/permissions/{pkCategoryHandle}', '\Concrete\Controller\Dialog\Calendar\Permissions::view');

/* Permissions Tools Hack */
$router->all('/tools/required/permissions/categories/calendar_admin', '\Concrete\Controller\Event\Permissions::process');
$router->all('/tools/required/permissions/categories/calendar', '\Concrete\Controller\Event\Permissions::processCalendar');