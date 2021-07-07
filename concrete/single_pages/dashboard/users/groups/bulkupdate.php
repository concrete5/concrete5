<?php defined('C5_EXECUTE') or die('Access Denied.');
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$form = $app->make('helper/form');
$request = $controller->getRequest();
/* @var Concrete\Core\Http\Request $request */
?>

<?php if (isset($selectedGroups) && is_array($selectedGroups)) { ?>
<label class="control-label form-label"><?=t('Confirm')?></label>
<?php if ($gParent instanceof Group) { ?>
<p><?=t('Move the following group(s) beneath <strong>%s</strong>.', $gParent->getGroupDisplayName())?></p>
<?php } else { ?>
<p><?=t('Move the following group(s) <strong>to the top level of groups</strong>.')?></p>
<?php } ?>

<ul>
    <?php foreach ($selectedGroups as $g) { ?>
	<li><?=$g->getGroupDisplayName()?></li>
    <?php } ?>
</ul>

<form method="post" action="<?=$view->action('confirm')?>" role="form">
    <input type="hidden" name="gParentNodeID" value="<?=h($request->get('gParentNodeID'))?>" />

	<?php foreach ($request->get('gID', []) as $gID) { ?>
	<input type="hidden" name="gID[]" value="<?=h($gID)?>" />
	<?php } ?>
	<br/>
	<input type="hidden" name="gName" value="<?=h($request->get('gName'))?>" />

	<div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php echo $interface->submit(t('Move Groups'), '', 'right', 'btn-primary'); ?>
        </div>
    </div>
</form>
<?php } elseif (isset($groups) && is_array($groups)) { ?>
<form action="<?=$view->action('move')?>" method="post" data-form="move-groups">
    <div class="row">
        <div class="col-md-6">
            <label class="control-label form-label"><?=t('Choose Groups to Move')?></label>
            <div class="form-check ml-2">
                <input class="form-check-input" data-toggle="checkbox" type="checkbox" id="select_all" />
                <label class="form-check-label" for="select_all" style="user-select: none; -moz-user-select: none; -webkit-user-select: none">
                    <strong><?=t('Select All')?></strong>
                </label>
            </div>

            <?php foreach ($groups as $g) { ?>
            <div class="form-check ml-2" data-checkbox="group-list">
                <input class="form-check-input" name="gID[]" id="gID_<?=$g->getGroupID()?>" type="checkbox" <?php
                    if (is_array($request->request->get('gID')) && in_array($g->getGroupID(), $request->request->get('gID'))) {
                    ?>checked<?php } ?> value="<?=$g->getGroupID()?>" />
                <label class="form-check-label" for="gID_<?=$g->getGroupID()?>">
                    <?=$g->getGroupDisplayName()?>
                </label>
            </div>
            <?php } ?>
        </div>

        <div class="col-md-6">
            <label class="control-label form-label"><?=t('Choose New Parent Location')?></label>
            <?=$form->hidden('gParentNodeID')?>

            <div class="nested-groups-tree" data-groups-tree="<?=$tree->getTreeID()?>"></div>
            <?php
            $guestGroupNode = GroupTreeNode::getTreeNodeByGroupID(GUEST_GROUP_ID);
            $registeredGroupNode = GroupTreeNode::getTreeNodeByGroupID(REGISTERED_GROUP_ID);
            ?>
        </div>
    </div>
    <hr/>

    <div class="row">
        <div class="col-md-12">
            <label class="control-label form-label"><?=t('Move')?></label>
            <p><?=t('Move selected groups (left column) beneath selected group (right column)')?></p>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <?php echo $interface->submit(t('Move'), '', 'right', 'btn-primary'); ?>
                </div>
            </div>
        </div>
    </div>

	<input type="hidden" name="gName" value="<?=h($request->get('gName'))?>" />
</form>

<script>
$(function() {
	$('input[data-toggle=checkbox]').on('click', function() {
		if ($(this).is(':checked')) {
			$('div[data-checkbox=group-list] input[type=checkbox]').prop('checked', true);
		} else {
			$('div[data-checkbox=group-list]  input[type=checkbox]').prop('checked', false);
		}
	});

   $('[data-groups-tree=<?=$tree->getTreeID()?>]').concreteTree({
      'treeID': '<?=$tree->getTreeID()?>',
      'chooseNodeInForm': 'single',
	  'enableDragAndDrop': false,
      <?php if ($this->controller->isPost()) { ?>
         'selectNodesByKey': [<?=intval($request->request->get('gParentNodeID'))?>],
      <?php } ?>
      'removeNodesByKey': ['<?=$guestGroupNode->getTreeNodeID()?>','<?=$registeredGroupNode->getTreeNodeID()?>'],
      'onSelect': function(nodes) {
         if (nodes.length) {
            $('input[name=gParentNodeID]').val(nodes[0]);
         } else {
            $('input[name=gParentNodeID]').val('');
         }
      }
   });
});
</script>
<?php } else { ?>
<form method="POST" action="<?=$view->action('search')?>">
    <div class="form-group">
        <?=$form->label('gName', t('Search for Groups to Move'))?>
        <?=$form->text('gName')?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php echo $interface->submit(t('Search'), '', 'right', 'btn-primary'); ?>
        </div>
    </div>
</form>
<?php } ?>
