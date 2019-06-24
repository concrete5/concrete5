<?php
defined('C5_EXECUTE') or die('Access Denied.');
$pk = PermissionKey::getByHandle('customize_themes');
?>


<section id="ccm-panel-page-design-customize">
    <form data-form="panel-page-design-customize" target="ccm-page-preview-frame" method="post" action="<?=$controller->action("preview", $theme->getThemeID())?>">
    <header><a href="" data-panel-navigation="back" class="ccm-panel-back"><span class="fa fa-chevron-left"></span></a> <a href="" data-panel-navigation="back"><?=t('Customize Theme')?></a></header>

    <?php if (count($presets) > 1) {
    ?>

    <div class="ccm-panel-content-inner">

    <div class="list-group" data-panel-menu-id="page-design-presets"  data-panel-menu="collapsible-list-group">
        <div class="list-group-item list-group-item-header"><?=t('Preset')?></div>
        <?php
        $i = 0;
    foreach ($presets as $preset) {
        $selected = false;
        if (is_object($selectedPreset) && $selectedPreset->getPresetHandle() == $preset->getPresetHandle()) {
            $selected = true;
        }
        ?>
            <label class="list-group-item clearfix"><input type="radio" class="ccm-flat-radio" value="<?=$preset->getPresetHandle()?>" name="handle" <?php if ($selected) {
    ?>checked="checked"<?php 
}
        ?> /> <?=$preset->getPresetDisplayName()?>
                <?=$preset->getPresetIconHTML()?>
            </label>
            <?php if ($i == 0) {
    ?>
                <div class="list-group-item-collapse-wrapper">
            <?php 
}
        ?>
        <?php  ++$i;
    }
    ?>

            </div>

        <a class="list-group-item list-group-item-collapse" href="#"><span><?=t('Expand')?></span></a>
    </div>

    </div>

    <?php 
} ?>

    <?php
    // output basic values –these are ones we don't have any
    // kind of special mapping for and that don't appear in our customizer style sets.
    foreach ($valueList->getValues() as $value) {
        if ($value instanceof \Concrete\Core\StyleCustomizer\Style\Value\BasicValue) {
            ?>
           <input type="hidden" name="<?=$value->getVariable()?>" value="<?=$value->getValue()?>" />
        <?php 
        }
    }
    ?>
    <div id="ccm-panel-page-design-customize-list">
    <?php foreach ($styleSets as $set) {
    ?>
        <div class="ccm-panel-page-design-customize-style-set">
            <h5 class="ccm-panel-page-design-customize-style-set-collapse"><?=$set->getDisplayName()?></h5>
            <ul class="list-unstyled">
            <?php foreach ($set->getStyles() as $style) {
    ?>
                <li><?=$style->getDisplayName()?>
                <?php
                $value = $style->getValueFromList($valueList);
    ?>
                <?=$style->render($value)?>
                </li>
            <?php 
}
    ?>
            </ul>
        </div>
    <?php 
} ?>
    <div class="ccm-panel-page-design-customize-style-set">
        <h5 class="ccm-panel-page-design-customize-style-set-collapse"><?=t('Advanced')?></h5>
        <ul class="list-unstyled">
            <li>
                <?=t('Custom CSS')?>
                <input type="hidden" name="sccRecordID" value="<?=$sccRecordID?>" />
                <span class="ccm-style-customizer-display-swatch-wrapper" data-custom-css-selector="custom"><span class="ccm-style-customizer-display-swatch"><i class="fa fa-cog"></i></span></span>
            </li>
        </ul>
    </div>
    </div>

    <div style="text-align: center">
        <br/>
       <button class="btn-danger btn" data-panel-detail-action="reset"><?=t('Reset Customizations')?></button>
        <br/><br/>
   </div>

    </form>
</section>

<div class="ccm-panel-detail-form-actions">
    <button class="pull-right btn btn-success" type="button" data-panel-detail-action="customize-design-submit"><?=t('Save Changes')?></button>
</div>


<script type="text/javascript">

    ConcretePageDesignPanel = {

        applyDesignToPage: function() {
            var $form = $('form[data-form=panel-page-design-customize]'),
                panel = ConcretePanelManager.getByIdentifier('page');

            $form.prop('target', null);
            $form.attr('action', '<?=$controller->action("apply_to_page", $theme->getThemeID())?>');
            $form.concreteAjaxForm();
            $form.submit();
        },

        applyDesignToSite: function() {
            var $form = $('form[data-form=panel-page-design-customize]'),
                panel = ConcretePanelManager.getByIdentifier('page');

            $form.prop('target', null);
            $form.attr('action', '<?=$controller->action("apply_to_site", $theme->getThemeID())?>');
            $form.concreteAjaxForm();
            $form.submit();
        },

        resetPageDesign: function() {
            var $form = $('form[data-form=panel-page-design-customize]'),
                panel = ConcretePanelManager.getByIdentifier('page');

            $form.prop('target', null);
            $form.attr('action', '<?=$controller->action("reset_page_customizations")?>');
            $form.concreteAjaxForm();
            $form.submit();
        },

        resetSiteDesign: function() {
            var $form = $('form[data-form=panel-page-design-customize]'),
                panel = ConcretePanelManager.getByIdentifier('page');

            $form.prop('target', null);
            $form.attr('action', '<?=$controller->action("reset_site_customizations", $theme->getThemeID())?>');
            $form.concreteAjaxForm();
            $form.submit();
        }


    }

    $(function() {
        panel = ConcretePanelManager.getByIdentifier('page');
        $('button[data-panel-detail-action=customize-design-submit]').on('click', function() {
            <?php if ($pk->can()) {
    ?>
                panel.showPanelConfirmationMessage('page-design-customize-apply', "<?=t('Apply this design to just this page, or your entire site?')?>", [
                    {'class': 'btn btn-primary pull-right', 'onclick': 'ConcretePageDesignPanel.applyDesignToSite()', 'style': 'margin-left: 10px', 'text': '<?=t("Entire Site")?>'},
                    {'class': 'btn btn-default pull-right', 'onclick': 'ConcretePageDesignPanel.applyDesignToPage()', 'text': '<?=t("This Page")?>'}
                ]);
            <?php 
} else {
    ?>
                ConcretePageDesignPanel.applyDesignToPage();
            <?php 
} ?>
            return false;
        });
        $('div[data-panel-menu-id=page-design-presets]').on('change', $('input[type=radio]'), function() {
            var selectedpreset = $('div[data-panel-menu-id=page-design-presets] input[type=radio]:checked');
            if (selectedpreset.length) {
                var panel = ConcretePanelManager.getByIdentifier('page');
                var $panel = $('#' + panel.getDOMID());
                var url = "<?=URL::to('/ccm/system/panels/page/design/customize', $theme->getThemeID())?>?cID=<?=$c->getCollectionID()?>";
                var content = $(this).closest('div.ccm-panel-content');
                $.concreteAjax({
                    url: url,
                    dataType: 'html',
                    data: {'handle': $(this).find(':checked').val()},
                    success: function(r) {
                        content.html(r);
                        panel.onPanelLoad(this);
                        $('form[data-form=panel-page-design-customize]').submit();
                    }
                });
            }
        });
        $('div.ccm-panel-page-design-customize-style-set').on('click', 'h5', function() {
            var $list = $(this).parent().find('> ul');
            var height = $list.height();
            var $header = $(this);
            if ($(this).hasClass('ccm-panel-page-design-customize-style-set-expand')) {
                $list.queue(function() {
                    $(this).css('height', 0);
                    $(this).show();
                    $(this).dequeue();
                }).
                delay(5).
                queue(function() {
                    $(this).css('height', height);
                    $header.removeClass('ccm-panel-page-design-customize-style-set-expand').addClass('ccm-panel-page-design-customize-style-set-collapse');
                    $(this).dequeue();
                });
            } else {
                $list.css('height', height);
                $list.queue(function() {
                    $(this).css('height', height);
                    $(this).dequeue();
                }).
                delay(0).
                queue(function() {
                    $(this).css('height', 0);
                    $header.removeClass('ccm-panel-page-design-customize-style-set-collapse').addClass('ccm-panel-page-design-customize-style-set-expand');
                    $(this).dequeue();
                }).
                delay(305).
                queue(function() {
                    $(this).hide();
                    $(this).css('height', 'auto');
                    $(this).dequeue();
                });
            }
        });
        $('span[data-custom-css-selector=custom]').on('click', function() {
            var sccRecordID = $('form[data-form=panel-page-design-customize] input[name=sccRecordID]').val();
            jQuery.fn.dialog.open({
                title: '<?=t('Custom CSS')?>',
                href: '<?=URL::to('/ccm/system/dialogs/page/design/css')?>?cID=<?=$c->getCollectionID()?>&sccRecordID=' + sccRecordID,
                modal: false,
                width: 640,
                height: 500
            });

        })
        $('button[data-panel-detail-action=reset]').unbind().on('click', function() {
            <?php if ($pk->can()) {
    ?>
                panel.showPanelConfirmationMessage('page-design-customize-apply', "<?=t('Reset the theme customizations for just this page, or your entire site?')?>", [
                    {'class': 'btn btn-sm btn-primary pull-right', 'onclick': 'ConcretePageDesignPanel.resetSiteDesign()', 'style': 'margin-left: 10px', 'text': '<?=t("Entire Site")?>'},
                    {'class': 'btn btn-sm btn-default pull-right', 'onclick': 'ConcretePageDesignPanel.resetPageDesign()', 'text': '<?=t("This Page")?>'}
                ]);
            <?php 
} else {
    ?>
                ConcretePageDesignPanel.resetPageDesign();
            <?php 
} ?>
            return false;
        });

        ConcreteEvent.unsubscribe('StyleCustomizerControlUpdate');
        ConcreteEvent.subscribe('StyleCustomizerControlUpdate', function() {
            $('form[data-form=panel-page-design-customize]').submit();
            $('div[data-panel-menu-id=page-design-presets] input[type=radio]').prop('checked', false);
        })
    });
</script>
