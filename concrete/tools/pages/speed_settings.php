<?php
defined('C5_EXECUTE') or die('Access Denied.');
$u = new User();
$form = Loader::helper('form');
$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
    die(t('Access Denied'));
}

if ($_POST['task'] == 'edit_speed_settings') {
    $json['error'] = false;

    if (is_array($_POST['cID'])) {
        foreach ($_POST['cID'] as $cID) {
            $c = Page::getByID($cID);
            $cp = new Permissions($c);
            if ($cp->canEditPageSpeedSettings()) {
                $data = array();
                if ($_POST['cCacheFullPageContent'] > -2) {
                    $data['cCacheFullPageContent'] = $_POST['cCacheFullPageContent'];
                }
                if ($_POST['cCacheFullPageContentOverrideLifetime'] > -1) {
                    $data['cCacheFullPageContentLifetimeCustom'] = $_POST['cCacheFullPageContentLifetimeCustom'];
                    $data['cCacheFullPageContentOverrideLifetime'] = $_POST['cCacheFullPageContentOverrideLifetime'];
                }
                $c->update($data);
            } else {
                $json['error'] = t('Unable to delete one or more pages.');
            }
        }
    }

    $js = Loader::helper('json');
    echo $js->encode($json);
    exit;
}

$form = Loader::helper('form');

$pages = array();
if (is_array($_REQUEST['item'])) {
    foreach ($_REQUEST['item'] as $cID) {
        $pages[] = Page::getByID($cID);
    }
} else {
    $pages[] = Page::getByID($_REQUEST['cID']);
}

$pcnt = 0;
$fullPageCaching = -3;
$cCacheFullPageContentOverrideLifetime = -2;
$cCacheFullPageContentOverrideLifetimeCustomValue = -1;
foreach ($pages as $c) {
    $cp = new Permissions($c);
    if ($cp->canEditPageSpeedSettings()) {
        if ($c->getCollectionFullPageCaching() != $fullPageCaching && $fullPageCaching != -3) {
            $fullPageCaching = -2;
        } else {
            $fullPageCaching = $c->getCollectionFullPageCaching();
        }
        if ($c->getCollectionFullPageCachingLifetime() != $cCacheFullPageContentOverrideLifetime && $cCacheFullPageContentOverrideLifetime != -2) {
            $cCacheFullPageContentOverrideLifetime = -1;
        } else {
            $cCacheFullPageContentOverrideLifetime = $c->getCollectionFullPageCachingLifetime();
        }
        if ($c->getCollectionFullPageCachingLifetimeCustomValue() != $cCacheFullPageContentOverrideLifetimeCustomValue && $cCacheFullPageContentOverrideLifetimeCustomValue != -1) {
            $cCacheFullPageContentOverrideLifetimeCustomValue = 0;
        } else {
            $cCacheFullPageContentOverrideLifetimeCustomValue = $c->getCollectionFullPageCachingLifetimeCustomValue();
        }
        ++$pcnt;
    }
}

$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);

?>
<div class="ccm-ui">

<?php if ($pcnt == 0) {
    ?>
	<?=t("You do not have permission to modify speed settings on any of the selected pages.");
    ?>
<?php 
} else {
    ?>

	<form id="ccm-<?=$searchInstance?>-speed-settings-form" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/speed_settings">
	<?=$form->hidden('task', 'edit_speed_settings')?>
	<?php foreach ($pages as $c) {
    ?>
		<?=$form->hidden('cID[]', $c->getCollectionID())?>
	<?php 
}
    ?>
	<div id="ccm-properties-cache-tab">

		<?php $form = Loader::helper('form');
    ?>
		<?php
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
                $globalSettingLifetime = t('%s minutes', Config::get('concrete.cache.lifetime') / 60);
                break;
            case 'custom':
                $custom = Config::get('concrete.cache.full_page_lifetime_value');
                $globalSettingLifetime = t('%s minutes', $custom);
                break;
            case 'forever':
                $globalSettingLifetime = t('Until manually cleared');
                break;
        }
    ?>

		<div class="clearfix">
		<label><?=t('Full Page Caching')?></label>

		<div class="input">
		<ul class="inputs-list">
		<li><label><?=$form->radio('cCacheFullPageContent', -2, $fullPageCaching)?>
		<span><?=t('Multiple values')?></span>
		</label></li>
		<li><label><?=$form->radio('cCacheFullPageContent', -1, $fullPageCaching, array('enable-cache' => $enableCache))?>
		<span><?=t('Use global setting - %s', $globalSetting)?></span>
		</label></li>
		<li><label><?=$form->radio('cCacheFullPageContent', 0, $fullPageCaching, array('enable-cache' => 0))?>
		<span><?=t('Do not cache this page.')?></span>
		</label></li>
		<li><label><?=$form->radio('cCacheFullPageContent', 1, $fullPageCaching, array('enable-cache' => 1))?>
		<span><?=t('Cache this page.')?></span>
		</label>
		</li>
		</ul>
		</div>

		</div>

		<div class="clearfix">
		<label><?=t('Cache for how long?')?></label>

		<div class="ccm-properties-cache-lifetime input">
		<ul class="inputs-list">
			<?php $val = ($cCacheFullPageContentLifetimeCustomValue > 0 && $cCacheFullPageContentOverrideLifetime) ? $cCacheFullPageContentLifetimeCustomValue : '';
    ?>
			<li><label><?=$form->radio('cCacheFullPageContentOverrideLifetime', -1, $cCacheFullPageContentOverrideLifetime)?>
			<span><?=t('Multiple values')?></span>
			</label></li>
			<li><label><span><?=$form->radio('cCacheFullPageContentOverrideLifetime', 0, $cCacheFullPageContentOverrideLifetime)?>
			<?=t('Use global setting - %s', $globalSettingLifetime)?>
			</span></label></li>
			<li><label><span><?=$form->radio('cCacheFullPageContentOverrideLifetime', 'default', $cCacheFullPageContentOverrideLifetime)?>
			<?=t('Default - %s minutes', Config::get('concrete.cache.lifetime') / 60)?>
			</span></label></li>
			<li><label><span><?=$form->radio('cCacheFullPageContentOverrideLifetime', 'forever', $cCacheFullPageContentOverrideLifetime)?>
			<?=t('Until manually cleared')?>
			</span></label></li>
			<li><label><span><?=$form->radio('cCacheFullPageContentOverrideLifetime', 'custom', $cCacheFullPageContentOverrideLifetime)?>
			<?=t('Custom')?>
			</span></label>
			<div style="margin-top: 4px; margin-left: 16px">
				<label><?=$form->text('cCacheFullPageContentLifetimeCustom', $val, array('style' => 'width: 40px'))?> <?=t('minutes')?></label>
			</div>
			</li>
		</ul>
		</div>
		</div>
	</div>
	</form>
	<div class="dialog-buttons">
	<?php $ih = Loader::helper('concrete/ui')?>
	<?=$ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left', 'btn')?>
	<?=$ih->button_js(t('Update'), "$('#ccm-" . $searchInstance . "-speed-settings-form').submit()", 'right', 'btn primary')?>
	</div>

	<?php

}
?>
</div>

	<script type="text/javascript">

		ccm_settingsSetupCacheForm = function() {
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
				$('input[name=cCacheFullPageContentLifetimeCustom]').val('');
			}

		}

		$(function() {
			$("input[name=cCacheFullPageContent]").click(function() {
				ccm_settingsSetupCacheForm();
			});
			$("input[name=cCacheFullPageContentOverrideLifetime]").click(function() {
				ccm_settingsSetupCacheForm();
			});
			$("input[name=cCacheFullPageContentOverrideLifetime][value=custom]").click(function() {
				$('input[name=cCacheFullPageContentLifetimeCustom]').get(0).focus();
			});
			ccm_settingsSetupCacheForm();
			$("#ccm-<?=$searchInstance?>-speed-settings-form").ajaxForm({
				type: 'POST',
				iframe: true,
				beforeSubmit: function() {
					jQuery.fn.dialog.showLoader();
				},
				success: function(r) {
					ccm_parseJSON(r, function() {
						jQuery.fn.dialog.closeTop();
						jQuery.fn.dialog.hideLoader();
						ccm_deactivateSearchResults('<?=$searchInstance?>');
						ConcreteAlert.notify({
						'message': ccmi18n.saveSpeedSettingsMsg,
						'title': ccmi18n.properties
						});
						$("#ccm-<?=$searchInstance?>-advanced-search").ajaxSubmit(function(r) {
							ccm_parseAdvancedSearchResponse(r, '<?=$searchInstance?>');
						});
					});
				}
			});
		});
	</script>
