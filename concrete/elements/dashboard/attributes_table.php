
<?php
//Used on both page and file attributes
$c = Page::getCurrentPage();

$sets = array();
if (is_object($category) && $category->allowAttributeSets()) {
    $sets = $category->getAttributeSets();
}
?>


<div class="ccm-dashboard-header-buttons">
	<?php if (count($sets) > 0) {
    ?>
		<button type="button" class="btn btn-secondary" data-bs-toggle="dropdown">
		<?=t('View')?> <span class="caret"></span>
		</button>
		<ul class="dropdown-menu" role="menu">
		<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($c)?>?asGroupAttributes=1"><?=t('Grouped by Set')?></a></li>
		<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($c)?>?asGroupAttributes=0"><?=t('In One List')?></a></li>
		</ul>
	<?php 
} ?>
	<a href="<?=URL::to('/dashboard/system/attributes/sets', 'category', $category->getAttributeKeyCategoryID())?>" class="btn btn-secondary"><?=t('Manage Sets')?></a>
</div>

<?php
if (count($attribs) > 0) {
    ?>


	<?php
    $ih = Loader::helper('concrete/ui');

    if (count($sets) > 0 && ($_REQUEST['asGroupAttributes'] !== '0')) {
        foreach ($sets as $as) {
            ?>

		<fieldset>
			<legend><?=$as->getAttributeSetDisplayName()?></legend>
	
		<?php

        $setattribs = $as->getAttributeKeys();
            if (count($setattribs) == 0) {
                ?>
		
			<div class="ccm-attribute-list-wrapper"><?=t('No attributes defined.')?></div>
		
		<?php 
            } else {
                ?>
            <ul class="item-select-list ccm-sortable-attribute-set-list-wrapper" attribute-set-id="<?=$as->getAttributeSetID()?>" id="asID_<?=$as->getAttributeSetID()?>">
			<?php
            foreach ($setattribs as $ak) {
                $controller = $ak->getController();
                $formatter = $controller->getIconFormatter();
                ?>
                <li class="ccm-attribute" id="akID_<?=$ak->getAttributeKeyID()?>">
                    <a href="<?=URL::to($editURL, 'edit', $ak->getAttributeKeyID())?>" title="<?php echo t('Handle')?>: <?php echo $ak->getAttributeKeyHandle();
                ?>">
                        <?=$formatter->getListIconElement()?>
                        <?=$ak->getAttributeKeyDisplayName()?>
                    </a>
                    <i class="ccm-item-select-list-sort"></i>
                </li>
			<?php 
            }
                ?>
			</ul>
			
		<?php 
            }
            ?>
			
		</fieldset>

		<?php 
        }

        $unsetattribs = $category->getController()->getSetManager()->getUnassignedAttributeKeys();
        if (count($unsetattribs) > 0) {
            ?>
		
			<fieldset>
				<legend><?=t('Other')?></legend>
				<ul class="ccm-attribute-list-wrapper item-select-list">
				<?php
                foreach ($unsetattribs as $ak) {
                    $controller = $ak->getController();
                    $formatter = $controller->getIconFormatter();
                    ?>
                    <li class="ccm-attribute">
                        <a href="<?=URL::to($editURL, 'edit', $ak->getAttributeKeyID())?>" title="<?php echo t('Handle')?>: <?php echo $ak->getAttributeKeyHandle();
                    ?>">
                            <?=$formatter->getListIconElement()?>
                            <?=$ak->getAttributeKeyDisplayName()?>
                        </a>
                    </li>
	            <?php 
                }
            ?>
                </ul>
			</fieldset>
		
		<?php

        }
    } else {
        ?>
		<ul class="item-select-list <?=($sortable ? 'ccm-sortable-attribute-list-wrapper' : 'ccm-attribute-list-wrapper');
        ?>">
		<?php
        foreach ($attribs as $ak) {
            $controller = $ak->getController();
            $formatter = $controller->getIconFormatter();
            ?>
            <li class="ccm-attribute" id="akID_<?=$ak->getAttributeKeyID()?>">
			    <a href="<?=URL::to($editURL, 'edit', $ak->getAttributeKeyID())?>" title="<?php echo t('Handle')?>: <?php echo $ak->getAttributeKeyHandle();
            ?>">
                    <?=$formatter->getListIconElement()?>
                    <?=$ak->getAttributeKeyDisplayName()?></a>
                <?php if ($sortable) {
    ?><i class="ccm-item-select-list-sort"></i><?php 
}
            ?>
		    </li>
		
		<?php 
        }
        ?>
		</ul>
	
	<?php 
    }
    ?>
	
<?php 
} else {
    ?>
	
	<p>
		<?php
     echo t('No attributes defined.');
    ?>
	</p>
	
<?php 
} ?>

<script type="text/javascript">
$(function() {
    $("ul.ccm-sortable-attribute-set-list-wrapper").sortable({
		handle: 'i.ccm-item-select-list-sort',
		cursor: 'move',
		opacity: 0.5,
		stop: function() {
			var ualist = $(this).sortable('serialize');
			ualist += '&cID=<?=$c->getCollectionID()?>';
			ualist += '&asID=' + $(this).attr('attribute-set-id');
            ualist += '&ccm_token=' + '<?=Loader::helper('validation/token')->generate('attribute_sort')?>';
			$.post('<?=URL::to('/ccm/system/attribute/attribute_sort/set')?>', ualist, function(r) {});
		}
	});
});
</script>


<?php $form = Loader::helper('form'); ?>
<?php if (isset($types) && is_array($types) && count($types) > 0) {
    ?>
<form method="get" action="<?=$view->action('select_type')?>" id="ccm-attribute-type-form">
	<label class="form-label" for="atID"><?=t('Add Attribute')?></label>
	<div class="row row-cols-auto g-0 align-items-center">
        <div class="col-auto">
            <div class="form-group">
                <?=$form->select('atID', $types)?>
            </div>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary"><?=t('Go')?></button>
        </div>
	</div>
</form>
<?php 
} ?>
