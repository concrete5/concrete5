<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<section class="ccm-ui" data-view="choose-summary-templates">
    <header><h3><?= t('Summary Templates') ?></h3></header>
    <form method="post" action="<?= $controller->action('submit') ?>" data-dialog-form="summary-templates"
          data-panel-detail-form="summary-templates">

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-8 ps-0">
                    <p class="text-muted"><small><?=t('Review your summary templates to make sure they look good at different sizes of the browser window.')?></small></p>
                </div>
                <div class="col-4 text-end">
                    <toggle-button class="pe-2 mb-0" v-model="hasCustomSummaryTemplates" name="hasCustomSummaryTemplates"></toggle-button>
                    <span class="text-muted"><small><?= t('Use only specific templates.') ?></small></span>
                </div>
            </div>
            <div class="row">
                <div class="col-12 ps-0">
                    <ul class="nav nav-tabs nav-fill border-bottom mb-3">
                        <li class="nav-item"><a @click="setWidth(1140)" :class="{'nav-link': true, 'active': previewWidth === 1140}" href="#"><?=t('Extra Large')?></a></li>
                        <li class="nav-item"><a @click="setWidth(992)"  :class="{'nav-link': true, 'active': previewWidth === 992}" href="#"><?=t('Large')?></a></li>
                        <li class="nav-item"><a @click="setWidth(768)"  :class="{'nav-link': true, 'active': previewWidth === 768}" href="#"><?=t('Medium')?></a></li>
                        <li class="nav-item"><a @click="setWidth(576)"  :class="{'nav-link': true, 'active': previewWidth === 576}" href="#"><?=t('Small')?></a></li>
                        <li class="nav-item"><a @click="setWidth(480)"  :class="{'nav-link': true, 'active': previewWidth === 480}" href="#"><?=t('Extra Small')?></a></li>
                    </ul>
                </div>
            </div>

            <div>
                <?php
                foreach ($templates as $instanceTemplate) {
                    $template = $instanceTemplate->getTemplate();
                    $checked = "false";
                    if (!$object->hasCustomSummaryTemplates() || in_array($template->getID(), $selectedTemplateIDs)) {
                        $checked = "true";
                    }

                    ?>
                    <div class="row">
                        <div class="col-6 ps-0">
                            <p class="text-muted"><?=$template->getName()?></p>
                        </div>
                        <div class="col-6 text-end">
                            <toggle-button v-show="hasCustomSummaryTemplates" name="template_<?=$template->getID()?>" :value="<?=$checked?>"></toggle-button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 ps-0 text-center">

                            <iframe width="1140" class="ccm-summary-templates-preview" src="<?=
                            URL::to('/ccm/system/summary_template/render',
                                $categoryHandle,
                                $memberIdentifier,
                                $instanceTemplate->getId()
                            )?>"></iframe>

                            <hr>

                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>


    </form>
    <div class="ccm-panel-detail-form-actions dialog-buttons">
        <button class="float-start btn btn-secondary" type="button" data-dialog-action="cancel"
                data-panel-detail-action="cancel"><?= t('Cancel') ?></button>
        <button class="float-end btn btn-success" type="button" data-dialog-action="submit"
                data-panel-detail-action="submit"><?= t('Save Changes') ?></button>
    </div>

</section>

<script type="text/javascript">

    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'section[data-view=choose-summary-templates]',
            components: config.components,
            mounted() {
                $(this.$el).find('iframe').on('load', function() {
                    var offsetHeight = this.contentWindow.document.body.offsetHeight,
                        frameHeight = offsetHeight > 480 ? 480 : offsetHeight;

                    $(this).css('width', $('[data-nav=summary-template-form-factors] a.active').attr('data-width'));
                    $(this).css('height', frameHeight);

                    $(this).parent().find('div[data-text=loading]').remove();
                });
            },
            methods: {
                setWidth(width) {
                    this.previewWidth = width
                    $(this.$el).find('iframe').css('width', width)
                }
            },
            data: {
                hasCustomSummaryTemplates: <?=$object->hasCustomSummaryTemplates() ? 'true' : 'false' ?>,
                previewWidth: 1140
            }
        })
    })


</script>
