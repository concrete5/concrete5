<?php defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Core\Attribute\Type as AttributeType;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use \Concrete\Core\Attribute\PendingType as PendingAttributeType;

$types = AttributeType::getAttributeTypeList();
$categories = AttributeKeyCategory::getList();
$txt = Loader::helper('text');
$form = Loader::helper('form');
$interface = Loader::helper('concrete/ui');
$valt = Core::make('token');

?>
	<form method="post" class="" id="attribute_type_associations_form" action="<?=$view->action('save_attribute_type_associations')?>">
	<?php $valt->output('save_attribute_type_associations'); ?>
	<table border="0" cellspacing="1" cellpadding="0" border="0" class="table">
		<tr>
			<th><?=t('Name')?></th>
			<?php foreach ($categories as $cat) {
    ?>
				<th><?=$txt->unhandle($cat->getAttributeKeyCategoryHandle())?></th>
			<?php 
} ?>
		</tr>
		<?php foreach ($types as $at) {
    ?>

			<tr>
				<td><?=$at->getAttributeTypeDisplayName()?></td>
				<?php foreach ($categories as $cat) {
    ?>
					<td style="width: 1px; text-align: center"><?=$form->checkbox($cat->getAttributeKeyCategoryHandle() . '[]', $at->getAttributeTypeID(), $at->isAssociatedWithCategory($cat))?></td>
				<?php 
}
    ?>
			</tr>

		<?php 
} ?>

	</table>
	<div class="ccm-dashboard-form-actions-wrapper">
		<div class="ccm-dashboard-form-actions">
	<?php
    $b1 = $interface->submit(t('Save'), 'attribute_type_associations_form', 'right', 'btn-primary');
    echo $b1;
    ?>
		</div>
	</div>
</form>

<h4><?=t('Custom Attribute Types')?></h4>
<?php
$ch = Loader::helper('concrete/ui');
$types = PendingAttributeType::getList(); ?>
<?php if (count($types) == 0) {
    ?>
	<?=t('There are no available attribute types awaiting installation.')?>
<?php 
} else {
    ?>
	<ul class="item-select-list">
		<?php foreach ($types as $at) {
    ?>
			<li>
					<span>
		        		<form id="attribute_type_install_form_<?=$at->getAttributeTypeHandle()?>" style="margin: 0px" method="post" action="<?=$view->action('add_attribute_type')?>">
                            <?php
							$controller = $at->getController();
							$formatter = $controller->getIconFormatter();
                            echo $form->hidden("atHandle", $at->getAttributeTypeHandle());
							print $formatter->getListIconElement();
    ?>
                        <?=$at->getAttributeTypeDisplayName()?>

                        <?=$ch->submit(t("Install"), 'submit', 'right', 'btn-default btn-xs')?>
                        </form>

                    </span>
			</li>
		<?php 
}
    ?>
	</ul>

<?php 
} ?>