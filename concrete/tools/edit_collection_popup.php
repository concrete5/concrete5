<?php

defined('C5_EXECUTE') or die('Access Denied.');

if (!Loader::helper('validation/numbers')->integer($_GET['cID'])) {
    die(t('Access Denied'));
}

$c = Page::getByID($_GET['cID'], 'RECENT');
$cp = new Permissions($c);
$canViewPane = false;

$additionalArgs = array();

switch ($_GET['ctask']) {
    case 'edit_permissions':
        $toolSection = "permission/lists/collection";
        $canViewPane = $cp->canEditPagePermissions();
        break;
    case 'set_advanced_permissions':
        $toolSection = "permission/details/collection";
        $canViewPane = $cp->canEditPagePermissions();
        break;
    case 'preview_page_as_user':
        $toolSection = "collection_preview_as_user";
        $canViewPane = ($cp->canPreviewPageAsUser() && Config::get('concrete.permissions.model') == 'advanced');
        break;
    case 'view_timed_permission_list':
        $toolSection = "collection_timed_permission_list";
        $canViewPane = ($cp->canPreviewPageAsUser() && Config::get('concrete.permissions.model') == 'advanced');
        break;
}

if (!isset($divID)) {
    $divID = 'ccm-edit-collection';
}

if (!$canViewPane) {
    die(t("Access Denied."));
}

?>

<div id="<?=$divID?>">

<?php if (!$_GET['close']) {
    if (!$c->isEditMode() && (!in_array($_GET['ctask'], array('add', 'edit_external', 'delete_external')))) {
        // first, we attempt to check the user in as editing the collection
        $u = new User();
        if ($u->isRegistered()) {
            $u->loadCollectionEdit($c);
        }
    }

    if (($c->isEditMode() || (in_array($_GET['ctask'], array('add', 'edit_external', 'delete_external')))) && $toolSection) {
        $args = array(
            'c' => $c,
            'cp' => $cp,
            'ct' => $ct,
        );
        $args = array_merge($args, $additionalArgs);
        Loader::element($toolSection, $args);
    } else {
        $error = t("Someone has already checked out this page for editing.");
    }
}

if ($error) {
    echo $error;
} ?>
<div class="ccm-spacer">&nbsp;</div>

</div>
