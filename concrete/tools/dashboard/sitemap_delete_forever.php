<?php

defined('C5_EXECUTE') or die('Access Denied.');
$dh = Loader::helper('concrete/dashboard/sitemap');
if (!$dh->canRead()) {
    die(t("Access Denied."));
}

$q = Queue::get('delete_page');
$isEmptyTrash = false;

if ($_POST['process']) {
	$obj = new stdClass;
	$js = Loader::helper('json');
	$messages = $q->receive(Config::get('concrete.limits.delete_pages'));
	foreach($messages as $key => $p) {
		// delete the page here
		$page = unserialize($p->body);
		$c = Page::getByID($page['cID']);
		$c->delete();
		$q->deleteMessage($p);
	}
	$obj->totalItems = $q->count();
	if ($q->count() == 0) {
		$q->deleteQueue('delete_page');
	}
	print $js->encode($obj);
	exit;
} else if ($q->count() == 0) {
	$c = Page::getByID($_REQUEST['cID']);
	if ($c->getCollectionPath() == Config::get('concrete.paths.trash')) {
		$isEmptyTrash = true;
	}
	if (is_object($c) && !$c->isError()) {
		$cp = new Permissions($c);
		if ($cp->canDeletePage()) {
			$c->queueForDeletion();
		}
	}
}

$totalItems = $q->count();
Loader::element('progress_bar', array('totalItems' => $totalItems, 'totalItemsSummary' => t2("%d page", "%d pages", $totalItems)));
