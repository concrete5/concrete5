<?php
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getCurrentPage();
?>
<div class="col-md-4">
	<div class="list-group">
		<a class="list-group-item <? if ($c->getCollectionPath() == '/dashboard/express/entities') { ?>active<? } ?>" href="<?=URL::to('/dashboard/express/entities', 'view_entity', $entity->getId())?>"><?=t('Overview')?></a>
		<a class="list-group-item <? if ($c->getCollectionPath() == '/dashboard/express/entities/attributes') { ?>active<? } ?>" href="<?=URL::to('/dashboard/express/entities/attributes', $entity->getId())?>"><?=t('Attributes')?></a>
		<a class="list-group-item <? if ($c->getCollectionPath() == '/dashboard/express/entities/associations') { ?>active<? } ?>" href="<?=URL::to('/dashboard/express/entities/associations', $entity->getId())?>"><?=t('Associations')?></a>

	</div>
</div>
