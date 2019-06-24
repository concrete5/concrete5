<?php defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;

$form = Loader::helper('form');
$txt = Loader::helper('text');?>
<?php if (in_array($this->controller->getTask(), array('update_set', 'update_set_attributes', 'edit', 'delete_set'))) {
    ?>
    <div class="row">
		<div class="col-md-8">
            <form class="" method="post" action="<?php echo $view->action('update_set')?>">
                <input type="hidden" name="asID" value="<?php echo $set->getAttributeSetID()?>" />
                <?php echo Loader::helper('validation/token')->output('update_set')?>

                <fieldset>
                    <legend><?=t('Update Set Details')?></legend>

                    <?php if ($set->isAttributeSetLocked()) {
    ?>
                        <div class="alert alert-warning">
                            <p><?php echo t('This attribute set is locked. It cannot be deleted, and its handle cannot be changed.')?></p>
                        </div>
                        <script type="text/javascript">
                            $(function() {
                                $('#asHandle').attr('disabled','disabled');
                            });
                        </script>
                    <?php
}
    ?>

                    <div class="form-group">
                        <?php echo $form->label('asHandle', t('Handle'))?>
                        <?php if ($set->isAttributeSetLocked()) {
    ?>
                            <?php echo $form->text('asHandle', $set->getAttributeSetHandle(), array('disabled' => 'disabled'))?>
                        <?php
} else {
    ?>
                            <?php echo $form->text('asHandle', $set->getAttributeSetHandle())?>
                        <?php
}
    ?>
                    </div>

                    <div class="form-group">
                        <?php echo $form->label('asName', t('Name'))?>
                        <?php echo $form->text('asName', $set->getAttributeSetName())?>
                    </div>

                    <div class="form-group">
                        <?php echo $form->submit('submit', t('Update Set'), array('class' => 'btn btn-primary'))?>
                    </div>
                    </fieldset>
            </form>

		</div>
        <div class="col-md-4">
            <?php if (!$set->isAttributeSetLocked()) {
    ?>
                <form method="post" action="<?php echo $view->action('delete_set')?>" class="">
                    <input type="hidden" name="asID" value="<?php echo $set->getAttributeSetID()?>" />
                    <?php echo Loader::helper('validation/token')->output('delete_set')?>

                    <fieldset>
                        <legend><?=t('Delete Set')?></legend>
                        <span class="help-block"><?php echo t('Warning, this cannot be undone. No attributes will be deleted but they will no longer be grouped together.')?></span>

                        <div class="form-group">
                            <?php echo $form->submit('submit', t('Delete Set'), array('class' => 'btn btn-danger'))?>
                        </div>
                    </fieldset>
                </form>
            <?php
}
    ?>
        </div>
    </div>
    <div class="row">
		<div class="col-md-12">
            <form class="" method="post" action="<?php echo $view->action('update_set_attributes')?>">
                <input type="hidden" name="asID" value="<?php echo $set->getAttributeSetID()?>" />
                <?php echo Loader::helper('validation/token')->output('update_set_attributes')?>
                <fieldset>

                    <legend><?=t('Add Attributes to Set')?></legend>

                    <?php
                    $cat = AttributeKeyCategory::getByID($set->getAttributeSetKeyCategoryID());
    $cat = $cat->getAttributeKeyCategory();
    $list = $cat->getList();
    $unassigned = $cat->getSetManager()->getUnassignedAttributeKeys();
    if (count($list) > 0) {
        ?>

                        <div class="form-group">
                            <?php foreach ($list as $key) {
    $keySets = \Concrete\Core\Attribute\Set::getByAttributeKey($key);

    $disabled = '';
    if (!$key->inAttributeSet($set) && count($keySets)) {
        $disabled = array('disabled' => 'disabled');
    }
    ?>
                                <div class="checkbox">
                                    <label>
                                        <?php echo $form->checkbox('akID[]', $key->getAttributeKeyID(), $key->inAttributeSet($set), $disabled)?>
                                        <span><?php echo $key->getAttributeKeyDisplayName()?></span>
                                        <span class="help-inline"><?php echo $key->getAttributeKeyHandle()?></span>
                                    </label>
                                </div>
                            <?php
}
        ?>
                        </div>

                        <div class="form-group">
                            <?php echo $form->submit('submit', t('Update Attributes'), array('class' => 'btn btn-primary'))?>
                        </div>
                    <?php
    } else {
        ?>
                        <p><?php echo t('No attributes found.')?></p>
                    <?php
    }
    ?>
                </fieldset>
            </form>
        </div>
	</div>

<?php
} elseif ($this->controller->getTask() == 'category' || $this->controller->getTask() == 'add_set') {
    ?>



	<?php if (count($sets) > 0) {
    ?>

        <ul class="item-select-list ccm-attribute-sortable-set-list">
			<?php foreach ($sets as $asl) {
    ?>
				<li id="asID_<?php echo $asl->getAttributeSetID()?>" class="ccm-item-select-list-sort">
                    <a href="<?php echo $view->url('/dashboard/system/attributes/sets/', 'edit', $asl->getAttributeSetID())?>">
                        <i class="fa fa-cubes"></i>
                        <?php echo $asl->getAttributeSetDisplayName()?>
                    </a>
                    <i class="ccm-item-select-list-sort"></i>
				</li>
			<?php
}
    ?>
		</ul>

	<?php
} else {
    ?>
		<?php echo t('No attribute sets currently defined.')?>
	<?php
}
    ?>


    <form method="post" action="<?php echo $view->action('add_set')?>">
        <?php echo Loader::helper('validation/token')->output('add_set')?>
        <input type="hidden" name="categoryID" value="<?php echo $categoryID?>" />

        <fieldset>
            <legend><?=t('Add Set')?></legend>
            <div class="form-group">
                <?php echo $form->label('asHandle', t('Handle'))?>
                <?php echo $form->text('asHandle')?>
            </div>

            <div class="form-group">
                <?php echo $form->label('asName', t('Name'))?>
                <?php echo $form->text('asName')?>
            </div>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <?php echo $form->submit('submit', t('Add Set'), array('class' => 'btn btn-primary pull-right'))?>
                </div>
            </div>
        </fieldset>

	</form>


<?php
} else { // Attribute Category List  ?>

        <h3><?=t('Attribute Categories')?></h3>
		<span class="help-block"><?php echo t('Attribute Categories are used to group different types of sets.')?></span>
		<ul class="item-select-list">
			<?php
            if (count($categories) > 0) {
                foreach ($categories as $cat) {
                    ?>
					<li class="ccm-group" id="acID_<?php echo $cat->getAttributeKeyCategoryID()?>">

						<a class="ccm-group-inner" href="<?php echo $view->url('/dashboard/system/attributes/sets/', 'category', $cat->getAttributeKeyCategoryID())?>">
                            <i class="fa fa-cubes"></i>
                            <?php echo $txt->unhandle($cat->getAttributeKeyCategoryHandle())?>
                        </a>
					</li>
				<?php
                }
            } else {
                echo t('No attribute categories currently defined.');
            }
    ?>
		</ul>

<?php
} ?>

