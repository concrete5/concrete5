<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<section id="ccm-menu-page-attributes">
	<header><a href="" data-panel-navigation="back" class="ccm-panel-back"><span class="fa fa-chevron-left"></span></a> <a href="" data-panel-navigation="back"><?=t('Attributes')?></a></header>
	<div class="ccm-panel-header-search">
		<i class="fa fa-search"></i>
		<input type="text" name="" id="ccm-panel-header-search-input" placeholder="<?=t('Search')?>" autocomplete="false" />
	</div>

	<div class="ccm-panel-content-inner" id="ccm-menu-page-attributes-list">
	<?php foreach ($attributes as $set) {
    ?>
		<div class="ccm-menu-page-attributes-set">
			<h5><?=$set->title?></h5>
			<ul>
			<?php foreach ($set->attributes as $key) {
    ?>
				<li><a data-attribute-key="<?=$key->getAttributeKeyID()?>" <?php if (in_array($key->getAttributeKeyID(), $selectedAttributeIDs)) {
    ?>class="ccm-menu-page-attribute-selected" <?php
}
    ?> href="javascript:void(0)"><?=$key->getAttributeKeyDisplayName()?></a></li>
			<?php
}
    ?>
			</ul>
		</div>
	<?php
} ?>
	</div>

</section>

<script type="text/javascript">
ConcreteMenuPageAttributes = {

	selectAttributeKey: function(akID) {
		$attribute = $('a[data-attribute-key=' + akID + ']');
		$attribute.addClass('ccm-menu-page-attribute-selected');
		ConcretePageAttributesDetail.addAttributeKey(akID);
	},

	deselectAttributeKey: function(akID) {
		$attribute = $('a[data-attribute-key=' + akID + ']');
		$attribute.removeClass('ccm-menu-page-attribute-selected');
	},

}
$(function() {
	$('#ccm-menu-page-attributes #ccm-panel-header-search-input').liveUpdate('ccm-menu-page-attributes-list', 'attributes');
	$('#ccm-menu-page-attributes').on('click', 'a[data-attribute-key]:not(.ccm-menu-page-attribute-selected)', function() {
		ConcreteMenuPageAttributes.selectAttributeKey($(this).attr('data-attribute-key'));
	});
});
</script>
