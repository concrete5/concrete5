<?php
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getCurrentPage();
?>
<div class="col-md-4">
	<div class="list-group">
		<a class="list-group-item <?php if ($c->getCollectionPath() == '/dashboard/express/entities') {
    ?>active<?php 
} ?>" href="<?=URL::to('/dashboard/express/entities', 'view_entity', $entity->getId())?>"><?=t('Edit Object')?></a>
		<a class="list-group-item <?php if ($c->getCollectionPath() == '/dashboard/express/entities/attributes') {
    ?>active<?php 
} ?>" href="<?=URL::to('/dashboard/express/entities/attributes', $entity->getId())?>"><?=t('Attributes')?></a>
		<a class="list-group-item <?php if ($c->getCollectionPath() == '/dashboard/express/entities/associations') {
    ?>active<?php 
} ?>" href="<?=URL::to('/dashboard/express/entities/associations', $entity->getId())?>"><?=t('Associations')?></a>
		<a class="list-group-item <?php if ($c->getCollectionPath() == '/dashboard/express/entities/forms') {
    ?>active<?php 
} ?>" href="<?=URL::to('/dashboard/express/entities/forms', $entity->getId())?>"><?=t('Forms')?></a>
		<a class="list-group-item <?php if ($c->getCollectionPath() == '/dashboard/express/entities/customize_search') {
			?>active<?php
		} ?>" href="<?=URL::to('/dashboard/express/entities/customize_search', $entity->getId())?>"><?=t('Customize Search')?></a>
		<a class="list-group-item-info list-group-item" href="<?=URL::to('/dashboard/express/entries', $entity->getId())?>"><i class="fa fa-chevron-left"></i> <?=t('Back to %s Entries', $entity->getName())?></a>

	</div>
</div>
