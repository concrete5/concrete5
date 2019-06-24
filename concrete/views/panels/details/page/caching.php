<?php
defined('C5_EXECUTE') or die('Access Denied.');

$form = Loader::helper('form');
switch (Config::get('concrete.cache.pages')) {
    case 'blocks':
        $globalSetting = t('cache page if all blocks support it.');
        $enableCache = 1;
        break;
    case 'all':
        $globalSetting = t('enable full page cache.');
        $enableCache = 1;
        break;
    case 0:
        $globalSetting = t('disable full page cache.');
        $enableCache = 0;
        break;
}
switch (Config::get('concrete.cache.full_page_lifetime')) {
    case 'default':
        $globalSettingLifetime = Loader::helper('date')->describeInterval(Config::get('concrete.cache.lifetime'));
        break;
    case 'custom':
        $globalSettingLifetime = Loader::helper('date')->describeInterval(Config::get('concrete.cache.full_page_lifetime_value') * 60);
        break;
    case 'forever':
        $globalSettingLifetime = t('Until manually cleared');
        break;
}
?>
<section class="ccm-ui">
	<header><?=t('Page Caching')?></header>
	<form method="post" action="<?=$controller->action('submit')?>" data-dialog-form="caching" data-panel-detail-form="caching">
		<label class="control-label"><?=t('Enable Cache')?></label>

		<div class="radio">
		<label>
			<?=$form->radio('cCacheFullPageContent', -1, $c->getCollectionFullPageCaching(), array('enable-cache' => $enableCache))?>
			<?=t('Use global setting - %s', $globalSetting)?>
		</label>
		</div>

		<div class="radio">
		<label>
			<?=$form->radio('cCacheFullPageContent', 0, $c->getCollectionFullPageCaching(), array('enable-cache' => 0))?>
			<?=t('Do not cache this page.')?>
		</label>
		</div>

		<div class="radio">
		<label>
			<?=$form->radio('cCacheFullPageContent', 1, $c->getCollectionFullPageCaching(), array('enable-cache' => 1))?>
			<?=t('Cache this page.')?>
		</label>
		</div>

		<hr/>

		<label class="control-label"><?=t('Duration')?></label>

		<div class="ccm-properties-cache-lifetime input">
		<?php $val = ($c->getCollectionFullPageCachingLifetimeCustomValue() > 0 && $c->getCollectionFullPageCachingLifetime()) ? $c->getCollectionFullPageCachingLifetimeCustomValue() : ''; ?>

		<div class="radio">
		<label>
			<input type="radio" name="cCacheFullPageContentOverrideLifetime" value="0" <?php if ($c->getCollectionFullPageCachingLifetime() == '0') {
    ?> checked="checked" <?php
} ?> />
			<?=t('Use global setting - %s', $globalSettingLifetime)?>
		</label>
		</div>

		<div class="radio">
		<label>
			<?=$form->radio('cCacheFullPageContentOverrideLifetime', 'forever', $c->getCollectionFullPageCachingLifetime())?>
			<?=t('Until manually cleared')?>
		</label>
		</div>

		<div class="radio">
		<label>
			<?=$form->radio('cCacheFullPageContentOverrideLifetime', 'custom', $c->getCollectionFullPageCachingLifetime())?>
			<?=t('Custom')?>
		</label>
		</div>

		<div class="form-inline"><?=$form->number('cCacheFullPageContentLifetimeCustom', $val, array('style' => 'width: 110px', 'min' => 1))?> <?=t('minutes')?></div>

		</div>

		<hr/>
		<label class="control-label"><?=t('Cache Status')?></label>

		<?php
        $cache = PageCache::getLibrary();
        $rec = $cache->getRecord($c);
        if ($rec instanceof \Concrete\Core\Cache\Page\PageCacheRecord) {
            ?>
			<div class="alert alert-success">
				<?=t('This page currently exists in the full page cache. It expires %s.', Core::make('date')->formatDateTime($rec->getCacheRecordExpiration()))?>
				&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-default pull-right" id="ccm-button-remove-page-from-cache"><?=t('Purge')?></button>
			</div>
		<?php
        } elseif ($rec instanceof \Concrete\Core\Cache\Page\UnknownPageCacheRecord) {
            ?>
			<div class="alert alert-info">
				<?=t('This page <strong>may</strong> exist in the page cache.')?>
				&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-default pull-right" id="ccm-button-remove-page-from-cache"><?=t('Purge')?></button>
			</div>
			<?php
        } else {
            ?>
			<div class="alert alert-info"><?=t('This page is not currently in the full page cache.')?></div>
		<?php
        } ?>

  		<span class="help-block"><?=t('Note: You can enable site-wide caching from the System & Settings area of the Dashboard.')?></span>

	</form>
	<div class="ccm-panel-detail-form-actions dialog-buttons">
		<button class="pull-left btn btn-default" type="button" data-dialog-action="cancel" data-panel-detail-action="cancel"><?=t('Cancel')?></button>
		<button class="pull-right btn btn-success" type="button" data-dialog-action="submit" data-panel-detail-action="submit"><?=t('Save Changes')?></button>
	</div>

</section>

<script type="text/javascript">
	ccm_settingsSetupCacheForm = function(reset) {
		var obj = $('input[name=cCacheFullPageContent]:checked');
		if (obj.attr('enable-cache') == 1) {
			$('div.ccm-properties-cache-lifetime input').attr('disabled', false);
		} else {
			$('div.ccm-properties-cache-lifetime input').attr('disabled', true);
			$('input[name=cCacheFullPageContentOverrideLifetime][value=0]').attr('checked', true);
		}
		var obj2 = $('input[name=cCacheFullPageContentOverrideLifetime]:checked');
		if (obj2.val() == 'custom') {
			$('input[name=cCacheFullPageContentLifetimeCustom]').attr('disabled', false);
		} else {
			$('input[name=cCacheFullPageContentLifetimeCustom]').attr('disabled', true);
			if (reset) {
				$('input[name=cCacheFullPageContentLifetimeCustom]').val('');
			}
		}
	}
	$(function() {
		$('#ccm-button-remove-page-from-cache').on('click', function() {
			jQuery.fn.dialog.showLoader();
			$.getJSON('<?=$controller->action("purge")?>', function(r) {
				jQuery.fn.dialog.hideLoader();
				ConcreteAlert.notify({
				'message': r.message
				});
				ConcretePanelManager.exitPanelMode();
			});
		});


		$("input[name=cCacheFullPageContent]").click(function() {
			ccm_settingsSetupCacheForm(true);
		});
		$("input[name=cCacheFullPageContentOverrideLifetime]").click(function() {
			ccm_settingsSetupCacheForm(true);
		});
		$("input[name=cCacheFullPageContentOverrideLifetime][value=custom]").click(function() {
			$('input[name=cCacheFullPageContentLifetimeCustom]').get(0).focus();
		});
		ccm_settingsSetupCacheForm();
	});
</script>
