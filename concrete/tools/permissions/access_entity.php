<?php
defined('C5_EXECUTE') or die('Access Denied.');
$form = Loader::helper("form");
use \Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use \Concrete\Core\Permission\Access\Entity\Type as PermissionAccessEntityType;
use \Concrete\Core\Permission\Duration as PermissionDuration;

$tp = new TaskPermission();
$tu = Loader::helper('concrete/user');
$dt = Loader::helper('form/date_time');
if (!$tu->canAccessUserSearchInterface() && !$tp->canAccessGroupSearch()) {
    die(t("You do not have user search or group search permissions."));
}
$pae = false;
if (!empty($_REQUEST['peID'])) {
    $pae = PermissionAccessEntity::getByID($_REQUEST['peID']);
}
if (!is_object($pae)) {
    $pae = false;
}

$pd = false;
if (!empty($_REQUEST['pdID'])) {
    $pd = PermissionDuration::getByID($_REQUEST['pdID']);
}
if (!is_object($pd)) {
    $pd = false;
}

if (isset($_POST['task']) && $_POST['task'] == 'save_permissions') {
    $js = Loader::helper('json');
    $r = new stdClass();

    if (is_object($pae)) {
        $pd = PermissionDuration::createFromRequest();
    } else {
        $r->error = true;
        $r->message = t('You must choose who this permission is for.');
    }

    if (empty($r->error)) {
        $r->peID = $pae->getAccessEntityID();
        if (is_object($pd)) {
            $r->pdID = $pd->getPermissionDurationID();
        } else {
            $r->pdID = 0;
        }
    }

    echo $js->encode($r);
    exit;
}

?>
<div class="ccm-ui" id="ccm-permissions-access-entity-wrapper">

<form id="ccm-permissions-access-entity-form" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/access_entity">
<input type="hidden" name="task" value="save_permissions" />
<?=$form->hidden('accessType');?>
<?=$form->hidden('peID');?>
<?=$form->hidden('pdID');?>

<h4><?=t('Access')?></h4>

<p><?=t('Who gets access to this permission?')?></p>

<div id="ccm-permissions-access-entity-label"><?php if (is_object($pae)) {
    ?><div class="alert alert-info"><?=$pae->getAccessEntityLabel()?></div><?php 
} else {
    ?><div class="alert alert-warning"><?=t('None Selected')?></div><?php 
} ?></div>

<?php if (!is_object($pae)) {
    ?>

<div class="btn-group">
	<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
	<i class="icon-plus-sign"></i> <?=t('Select')?>
	<span class="caret"></span>
		</a>
	<ul class="dropdown-menu">
	<?php
    $category = PermissionKeyCategory::getByHandle(isset($_REQUEST['pkCategoryHandle']) ? $_REQUEST['pkCategoryHandle'] : null);
    $entitytypes = PermissionAccessEntityType::getList($category);
    foreach ($entitytypes as $type) {
        ?>
		<li><?=$type->getAccessEntityTypeLinkHTML()?></li>
	<?php 
    }
    ?>
	</ul>
</div>
<br/><br/>

<?php foreach ($entitytypes as $type) {
    ?>

<?php if ($type->getPackageID() > 0) {
    ?>
	<?php Loader::packageElement('permission/access/entity/types/' . $type->getAccessEntityTypeHandle(), $type->getPackageHandle(), array('type' => $type));
    ?>
<?php 
} else {
    ?>
	<?php Loader::element('permission/access/entity/types/' . $type->getAccessEntityTypeHandle(), array('type' => $type));
    ?>
<?php 
}
    ?>


<?php 
}
    ?>

<?php 
} ?>


<?php if (!isset($_REQUEST['disableDuration'])) {
    ?>

<h4><?=t('Time Settings')?></h4>

<?=Loader::element('permission/duration', array('pd' => $pd));
    ?>

<?php 
} ?>

<div class="dialog-buttons">
	<input type="button" onclick="jQuery.fn.dialog.closeTop()" value="<?=t('Cancel')?>" class="btn btn-default pull-left" />
	<input type="submit" onclick="$('#ccm-permissions-access-entity-form').submit()" value="<?=t('Save')?>" class="btn btn-primary pull-right" />
</div>


</form>

</div>

<script type="text/javascript">
	$("#ccm-permissions-access-entity-form").ajaxForm({
		beforeSubmit: function(r) {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			r = eval('(' + r + ')');
			jQuery.fn.dialog.hideLoader();
			if (r.error) {
				ConcreteAlert.dialog('<?=t("Error")?>', r.message);
			} else {
				if (typeof(ccm_addAccessEntity) == 'function') {
					ccm_addAccessEntity(r.peID, r.pdID, <?= json_encode(isset($_REQUEST["accessType"]) ? h((string) $_REQUEST["accessType"]) : '') ?>);
				} else {
					alert(r.peID);
					alert(r.pdID);
				}
			}
		}
	});

</script>


