<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<style>
    iframe {
        border: 0;
        height: 0;
        overflow: hidden;
        width: 1400px;
    }
</style>

    <form method="post" action="<?=$view->action('submit', $element->getID())?>">
    <?=$token->output('submit')?>

    <div data-form="customize-slot" v-cloak>

        <h3><?=t('Choose Template')?></h3>
        
        <input type="hidden" name="selectedTemplateJson" :value="selectedTemplateJson">

        <div v-for="(templateOption, index) in templateOptions">

            <div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="radio" class="form-check-input" :value="index" name="selectedTemplateOption"
                               v-model="selectedTemplateOption">
                        <span class="badge bg-dark me-3">{{templateOption.template.name}}</span>

                        <span v-for="contentObject in templateOption.collection.objects">
                            <span class="badge bg-light me-3" v-if="contentObject.title">{{contentObject.title}}</span>
                        </span>

                        <i class="ms-2 fa fa-spinner fa-spin" v-if="!loadedTemplateOptions.includes(index)"></i>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 ps-0">

                    <iframe :data-index="index" src="<?=$view->action('load_preview_window')?>"></iframe>

                    <hr>

                </div>
            </div>

        </div>

        <div v-if="templateOptions.length === 0">
            <p class="text-muted"><?=t('There are no summary templates available for the item or items you have selected.')?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button type="submit" class="btn float-end btn-secondary" :disabled="selectedTemplateOption < 0"><?=t('Next')?></button>
            </div>
        </div>

    </div>

</form>

<script type="text/javascript">
    $(function() {
        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'div[data-form=customize-slot]',
                components: config.components,
                computed: {
                    'selectedTemplateJson': function() {
                        return JSON.stringify(this.templateOptions[this.selectedTemplateOption])
                    }
                },

                mounted() {
                    var my = this
                    this.templateOptions.forEach(function(templateOption, i) {

                        const iframe = $('iframe[data-index=' + i + ']').get(0)
                        iframe.onload = function() {
                            const innerDoc = iframe.contentWindow.document
                            const innerPage = innerDoc.querySelector('div.ccm-page')
                            innerPage.innerHTML = templateOption.content

                            const offsetHeight = iframe.contentWindow.document.body.offsetHeight
                            var frameHeight = offsetHeight > 650 ? 650 : offsetHeight
                            frameHeight = frameHeight < 300 ? 300 : frameHeight

                            $(iframe).css('height', frameHeight);


                            $(innerPage).find('a').click(function(e) {
                                e.preventDefault()
                                e.stopPropagation()
                            })


                            $(innerPage).find('.ew-stripe-clickable').removeAttr('onclick')

                            my.loadedTemplateOptions.push(i)
                        }
                    });
                },
                data: {
                    templateOptions: <?=$templateOptions?>,
                    selectedTemplateOption: -1,
                    loadedTemplateOptions: []
                },

                watch: {},

                methods: {
                }
            })
        })
    });
</script>
