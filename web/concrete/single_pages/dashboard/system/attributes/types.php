<? defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Attribute\Type as AttributeType;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use \Concrete\Core\Attribute\PendingType as PendingAttributeType;
$types = AttributeType::getList();
$categories = AttributeKeyCategory::getList();
$txt = Loader::helper('text');
$form = Loader::helper('form');
$interface = Loader::helper('concrete/ui');

echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Attribute Type Associations'), false, 'span10 offset1');?>
<form method="post" class="" id="attribute_type_associations_form" action="<?=$view->action('save_attribute_type_associations')?>">
	<table border="0" cellspacing="1" cellpadding="0" border="0" class="table">
		<tr>
			<th><?=t('Name')?></th>
			<? foreach($categories as $cat) { ?>
				<th><?=$txt->unhandle($cat->getAttributeKeyCategoryHandle())?></th>
			<? } ?>
		</tr>
		<?php foreach($types as $at) { ?>

			<tr>
				<td><?=$at->getAttributeTypeDisplayName()?></td>
				<? foreach($categories as $cat) { ?>
					<td style="width: 1px; text-align: center"><?=$form->checkbox($cat->getAttributeKeyCategoryHandle() . '[]', $at->getAttributeTypeID(), $at->isAssociatedWithCategory($cat))?></td>
				<? } ?>
			</tr>

		<? } ?>

	</table>
	<div class="well clearfix">
	<?
	$b1 = $interface->submit(t('Save'), 'attribute_type_associations_form', 'right', 'primary');
	print $b1;
	?>
	</div>
</form>

<h3><?=t('Custom Attribute Types')?></h3>
<?
$ch = Loader::helper('concrete/ui');
$types = PendingAttributeType::getList(); ?>
<? if (count($types) == 0) { ?>
	<?=t('There are no available attribute types awaiting installation.')?>
<? } else { ?>
	<ul class="item-select-list">
		<? foreach($types as $at) { ?>
			<li>
				<form id="attribute_type_install_form_<?=$at->getAttributeTypeHandle()?>" style="margin: 0px" method="post" action="<?=$view->action('add_attribute_type')?>">
					<?
					print $form->hidden("atHandle", $at->getAttributeTypeHandle());
					?>
					<p style="background-image: url(<?=$at->getAttributeTypeIconSRC()?>)" class="ccm-block-type-inner"><?=$ch->submit(t("Install"), 'submit', 'right', 'small')?><?=$at->getAttributeTypeDisplayName()?></p>
				</form>
			</li>
		<? } ?>
	</ul>

<? } ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);