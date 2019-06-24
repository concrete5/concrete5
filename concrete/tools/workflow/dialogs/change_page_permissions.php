<?php
defined('C5_EXECUTE') or die('Access Denied.');
$wp = PageWorkflowProgress::getByID($_REQUEST['wpID']);
if (is_object($wp)) {
    $w = $wp->getWorkflowObject();
    if ($w->canApproveWorkflowProgressObject($wp)) {
        $req = $wp->getWorkflowRequestObject();
        if (is_object($req)) {
            $c = Page::getByID($req->getRequestedPageID(), 'RECENT');
            ?>

<div class="ccm-ui">
<table class="ccm-permission-grid table table-striped">

<?php
$ps = $req->getPagePermissionSet();
            foreach ($ps->getPermissionAssignments() as $pkID => $paID) {
                $pk = PermissionKey::getByID($pkID);
                $pk->setPermissionObject($c);
                ?>
<tr>
	<td class="ccm-permission-grid-name"><strong><?=$pk->getPermissionKeyDisplayName()?></strong></td>
	<td>
	<?php $pa = PermissionAccess::getByID($paID, $pk);
                Loader::element('permission/labels', array('pa' => $pa, 'pk' => $pk))?>
	</td>
</tr>
<?php 
            }
            ?>
</table>
</div>
		
		
		
		<?php

        }
    }
}
