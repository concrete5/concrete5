<?php

defined('C5_EXECUTE') or die('Access Denied.');
$c = Page::getByID($_REQUEST['cID']);
$cp = new Permissions($c);
if ($cp->canViewPageVersions()) {
	$c->loadVersionObject(Loader::helper('security')->sanitizeInt($_REQUEST['cvID']));
	$req = Request::getInstance();
	$req->setCustomRequestUser(-1);
	$req->setCurrentPage($c);
	$controller = $c->getPageController();
	$controller->on_start();
	$controller->runAction('view');
	$controller->on_before_render();
	$view = $controller->getViewObject();
	$response = new Response();
	$content = $view->render();
	$response->setContent($content);
	$response->send();
	exit;
}
