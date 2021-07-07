<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var array $attributes
 * @var bool $isBulkMode
 * @var string $selectedAttributes
 * @var string $selectAttributeUrl
 */
?>

<div data-view="attributes">

    <div v-for="attribute in selectedAttributes" :key="attribute.akID">
        <div class="form-group">
            <a class="float-end ccm-hover-icon" href="#" @click.prevent="removeAttribute(attribute.akID)">
                <i class="fa fa-minus-circle"></i>
            </a>
            <label class="control-label form-label" :for="attribute.controlID">{{attribute.label}}</label>
            <div v-if="isBulkMode && attribute.hasMultipleValues" class="ccm-attribute-key-multiple-values card card-body p-2">
                <a :href="'#ccm-attribute-key-mv-body-' + attribute.akID" data-bs-toggle="collapse"
                   class="d-block text-decoration-none link-primary" role="button"
                   aria-expanded="false"
                   :aria-controls="'ccm-attribute-key-mv-body-' + attribute.akID"
                   @click="attribute.mvBoxExpanded = !attribute.mvBoxExpanded">
                    Multiple Values
                    <span class="float-end mt-1">
                        <Icon :icon="attribute.mvBoxExpanded ? 'ban' : 'edit'" type="fas" :color="attribute.mvBoxExpanded ? '#c32a2a' : 'currentColor'"/>
                    </span>
                </a>
                <div :id="'ccm-attribute-key-mv-body-' + attribute.akID" class="collapse mt-3">
                    <div :id="'ccm-attribute-key-' + attribute.akID"></div>
                </div>
                <input type="hidden" :name="attribute.mvBoxExpanded ? 'selectedKeys[]' : 'ignoredKeys[]'" :value="attribute.akID" />
            </div>
            <div v-else>
                <div  :id="'ccm-attribute-key-' + attribute.akID"></div>
                <input type="hidden" name="selectedKeys[]" :value="attribute.akID" />
            </div>
        </div>
    </div>

    <h4><?=t('Add Attribute')?></h4>
    <div class="input-group">
        <select class="form-select" v-model="selectedAttributeToAdd">
            <option value=""><?=t('** Choose Attribute')?></option>
            <optgroup v-for="attributeSet in attributes.sets" :label="attributeSet.name">
                <option :disabled="isSelected(attribute.akID)" v-for="attribute in attributeSet.keys" :value="attribute.akID">{{attribute.akName}}</option>
            </optgroup>

            <template v-if="attributes.sets.length > 0 && attributes.unassigned.length > 0">
                <optgroup label="<?=t('Unassigned')?>">
                    <option :disabled="isSelected(attribute.akID)" v-for="attribute in attributes.unassigned" :value="attribute.akID">{{attribute.akName}}</option>
                </optgroup>
            </template>
            <template v-else>
                <option :disabled="isSelected(attribute.akID)" v-for="attribute in attributes.unassigned" :value="attribute.akID">{{attribute.akName}}</option>
            </template>

        </select>
        <button class="btn btn-primary" type="button" @click="addSelectedAttribute"><?=t('Go')?></button>
    </div>

</div>

<script>
    Concrete.Vue.activateContext('backend', function (Vue, config) {
        new Vue({
            components: config.components,
            el: '[data-view=attributes]',
            data: () => {
                const selectedAttributes = <?=$selectedAttributes?>, isBulkMode = <?=$isBulkMode?>;
                if (isBulkMode) {
                    for (let i = 0; i < selectedAttributes.length; i++) {
                        if (selectedAttributes[i].hasMultipleValues) {
                            selectedAttributes[i].mvBoxExpanderIconClasses = ['fas', 'float-end', 'mt-1', 'fa-edit']
                            selectedAttributes[i].mvBoxExpanded = false
                        }
                    }
                }

                return {
                    selectedAttributeToAdd: '',
                    selectedAttributes: selectedAttributes,
                    attributes: <?=$attributes?>,
                    isBulkMode: isBulkMode
                }
            },
            mounted() {
                this.$nextTick(() => {
                    this.selectedAttributes.forEach(attribute => this.loadContent(attribute))
                })
            },
            methods: {
                loadAssets(attribute) {
                    if (attribute.assets.css) {
                        attribute.assets.css.forEach(css => ConcreteAssetLoader.loadCSS(css))
                    }
                    if (attribute.assets.javascript) {
                        attribute.assets.javascript.forEach(javascript => ConcreteAssetLoader.loadJavaScript(javascript))
                    }
                },
                loadContent(attribute) {
                    $(this.$el).find("#ccm-attribute-key-" + attribute.akID).html(attribute.content)
                },
                isSelected(akID) {
                    return this.selectedAttributes.findIndex(attribute => attribute.akID == akID) > -1
                },
                removeAttribute(akID) {
                    var my = this
                    const index = my.selectedAttributes.findIndex(attribute => attribute.akID == akID)
                    my.selectedAttributes.splice(index, 1)
                },
                addSelectedAttribute() {
                    const my = this;
                    if (this.selectedAttributeToAdd) {
                        new ConcreteAjaxRequest({
                            url: '<?=$selectAttributeUrl?>',
                            data: {
                                'akID': my.selectedAttributeToAdd
                            },
                            success: function(r) {
                                my.selectedAttributeToAdd = ''
                                my.loadAssets(r)
                                my.selectedAttributes.push(r)
                                my.$nextTick(() => {
                                    my.loadContent(r)
                                })
                            }
                        })
                    }
                }
            }
        })
    })
</script>
