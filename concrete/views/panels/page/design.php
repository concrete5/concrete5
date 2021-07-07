<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<section id="ccm-panel-page-design">
    <form method="post" action="<?= $controller->action('submit') ?>" data-panel-detail-form="design">
        <input type="hidden" name="update_theme" value="1" class="accept">
        <input type="hidden" name="processCollection" value="1">
        <input type="hidden" name="ptID" value="<?= $c->getPageTypeID() ?>" />

        <header>
            <a href="" data-panel-navigation="back" class="ccm-panel-back">
                <svg><use xlink:href="#icon-arrow-left" /></svg>
                <?= t('Page Settings') ?>
            </a>

            <h5><?= t('Design') ?></h5>
        </header>


        <div class="ccm-panel-content-inner">

        <?php
        if ($cp->canEditPageTemplate() && !$c->isGeneratedCollection()) {
            ?>
            <div class="ccm-panel-page-design-page-group" id="ccm-panel-page-design-page-templates"
                 class="ccm-panel-page-design-page-group" data-panel-menu-id="page-templates" data-panel-menu="collapsible-list-group">
                <div class="ccm-panel-page-design-title">
                    <?= t('Page Template') ?>
                </div>
                <?php
                foreach ($templates as $tmp) {
                    $selected = false;
                    if (is_object($selectedTemplate) && $tmp->getPageTemplateID() === $selectedTemplate->getPageTemplateID()) {
                        $selected = true;
                    }
                    ?>
                    <div class="form-check">
                        <input type="radio" class="ccm-flat-radio form-check-input" id="<?= $tmp->getPageTemplateID() ?>" value="<?= $tmp->getPageTemplateID() ?>" name="pTemplateID" <?= $selected ? 'checked' : '' ?>/>
                        <label class="form-check-label" for="<?= $tmp->getPageTemplateID() ?>">
                            <?= $tmp->getPageTemplateDisplayName() ?>
                            <?= $tmp->getPageTemplateIconImage() ?>
                        </label>
                    </div>
                    <?php
                }
                ?>

            </div>
            <hr>
            <div class="ccm-panel-page-design-page-group">
                <div class="ccm-panel-page-design-title">
                    <?=t('Summary Templates')?>
                </div>
                <p class="text-muted"><?=t2('One summary template available for this page.', '%s summary templates available for this page.',
                        $availableSummaryTemplatesCount)?></p>

                <div><small><a dialog-title="<?=t('Summary Templates')?>" class="dialog-launch" dialog-width="90%" dialog-height="70%"
                               href="<?=URL::to('/ccm/system/dialogs/page/summary_templates')?>?cID=<?=$c->getCollectionID()?>">
                            <?=t('Choose summary templates.')?>
                        </a></small></div>

            </div>

            <?php
        }
        ?>

        <?php
        if ($cp->canEditPageTheme()) {
            ?>
            <hr>
            <div id="ccm-panel-page-design-themes" class="" data-panel-menu-id="themes" data-panel-menu="collapsible-list-group">
                <input type="hidden" name="pThemeID" value="<?= $selectedTheme->getThemeID() ?>" />

                <div class="ccm-panel-page-design-title">
                    <?= t('Theme') ?>
                </div>
                <?php
                foreach ($themes as $th) {
                    $selected = false;
                    if (is_object($selectedTheme) && $th->getThemeID() === $selectedTheme->getThemeID()) {
                        $selected = true;
                    }
                    ?>
                    <div data-theme-id="<?= $th->getThemeID() ?>" class="ccm-page-design-theme-thumbnail <?= $selected ? 'ccm-page-design-theme-thumbnail-selected' : '' ?>">
                        <span>
                            <i>
                                <?= $th->getThemeThumbnail() ?>
                                <?php
                                if ($th->isThemeCustomizable()) {
                                    ?>
                                    <span class="ccm-page-design-theme-customize">
                                        <a href="#" data-launch-panel-detail="page-design-customize" data-panel-detail-url="<?= URL::to('/ccm/system/panels/details/page/preview') ?>" data-launch-sub-panel-url="<?= URL::to('/ccm/system/panels/page/design/customize', $th->getThemeID()) ?>">
                                            <?= t('Customize') ?>
                                            <i class="fa fa-share"></i>
                                        </a>
                                    </span>
                                    <?php
                                }
                                ?>
                            </i>
                            <div class="ccm-panel-page-design-theme-description">
                                <h5><?= $th->getThemeName() ?></h5>
                            </div>
                        </span>
                    </div>
                    <?php
                }
                ?>

            </div>

            <?php
            if (Config::get('concrete.marketplace.enabled')) {
                ?>
                <div class="ccm-marketplace-btn-wrapper d-grid">
                <button type="button" onclick="window.location.href='<?= URL::to('/dashboard/extend/themes') ?>'" class="btn-info btn btn-large"><?= t("Get More Themes") ?></button>
                </div>
                <?php
            }
            ?>
            <?php
        }
        ?>

        </div>
    </form>
</section>



<script type="text/javascript">
$(function() {

    function swapElements(elm1, elm2) {
        var parent1, next1,
            parent2, next2;

        parent1 = elm1.parentNode;
        next1   = elm1.nextSibling;
        parent2 = elm2.parentNode;
        next2   = elm2.nextSibling;

        parent1.insertBefore(elm2, next1);
        parent2.insertBefore(elm1, next2);
    }

    $('.list-group-item[data-theme-id]').on('click', function() {
        $('#ccm-panel-page-design-themes input[name=pThemeID]').val($(this).attr('data-theme-id')).trigger('change');
        $('.ccm-page-design-theme-thumbnail-selected').removeClass('ccm-page-design-theme-thumbnail-selected');
        $(this).addClass('ccm-page-design-theme-thumbnail-selected');
    });


    ConcreteEvent.subscribe('PanelCollapsibleListGroupCollapse', function(e, menuID) {
        switch(menuID) {
            case 'page-templates':
                var $topitem = $('#ccm-panel-page-design-page-templates > label.list-group-item input[type=radio]');
                var $checkeditem = $('#ccm-panel-page-design-page-templates input[type=radio]:checked');
                if (!$topitem.is(':checked')) {
                    swapElements($checkeditem.parent()[0], $topitem.parent()[0]);
                }
                break;
            case 'themes':
                var $topitem = $('#ccm-panel-page-design-themes > div.list-group-item[data-theme-id]');
                var $checkeditem = $('#ccm-panel-page-design-themes .ccm-page-design-theme-thumbnail-selected');
                if ($topitem.attr('data-theme-id') !== $checkeditem.attr('data-theme-id')) {
                    swapElements($checkeditem[0], $topitem[0]);
                }
                break;
        }
    });

    $('#ccm-panel-page-design input[name=pThemeID], #ccm-panel-page-design input[name=pTemplateID]').on('change', function() {
        var pThemeID = $('#ccm-panel-page-design input[name=pThemeID]').val();
        var pTemplateID = $('#ccm-panel-page-design input[name=pTemplateID]:checked').val();
        var src = '<?= $controller->action("preview_contents") ?>&pThemeID=' + pThemeID + '&pTemplateID=' + pTemplateID;
        $('#ccm-page-preview-frame').get(0).src = src;
    });

});
</script>
