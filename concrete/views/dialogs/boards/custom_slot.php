<?php
defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="ccm-ui">
    <div data-view="populate-custom-slot" class="ccm-board-slot-designer">
        <div v-show="currentStep == 'items'">
            <ul class="nav nav-tabs nav-fill mb-3">
                <li class="nav-item" v-for="dataSource in dataSources" :key="dataSource.id">
                    <a :class="{'nav-link': true, 'active': activeDataSource === dataSource.id}"
                       @click="activeDataSource = dataSource.id"
                       href="javascript:void(0)">
                        {{dataSource.name}}
                    </a>
                </li>
            </ul>

            <form @submit="performSearch">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group mb-3">
                                <input type="text" v-model="searchKeywords" class="form-control" placeholder="<?=t('Search items')?>">
                                <button class="btn btn-secondary" type="submit"><?=t('Search')?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="container" v-show="activeDataSource === dataSource.id" v-for="dataSource in dataSources"
                 :key="dataSource.id">
                <div class="row row-cols-1 row-cols-md-3">
                    <div class="col mb-4"
                         v-for="item in dataSource.items" @click="toggleChecked(item)" style="cursor: pointer">
                        <div class="card mb-4">
                            <img :src="item.thumbnail" class="ccm-board-slot-designer-thumbnail" alt="">
                            <div class="card-body">
                                <h5 class="card-title">{{item.name}}</h5>
                                <div class="text-muted">{{item.relevantDateString}}</div>
                            </div>
                            <input type="checkbox" :value="item.id" v-model="selectedItemIds">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-show="currentStep == 'templates'">
            <div v-for="(templateOption, index) in templateOptions">

                <div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="radio" class="form-check-input" :value="index" name="selectedTemplateOption"
                                   v-model="selectedTemplateOption">
                            <span class="text-muted"><?= t('Template Name:') ?> {{templateOption.template.name}}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 ps-0">

                        <span v-html="templateOption.content"></span>
                        <hr>

                    </div>
                </div>

            </div>
            <div v-if="templateOptions.length === 0">
                <?=t('No available templates found for selected items.')?>
            </div>
        </div>

        <div class="dialog-buttons">
            <button class="btn btn-secondary" @click="handleCancelButton">{{cancelButtonText}}</button>
            <button type="button" @click="handleSaveButton"
                    :disabled="selectedItemIds.length === 0 || (currentStep == 'templates' && selectedTemplateOption < 0)"
                    class="btn btn-primary">{{saveButtonText}}
            </button>
        </div>


    </div>
</div>

<script type="text/javascript">

    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-view=populate-custom-slot]',
            components: config.components,
            mounted() {
                this.activeDataSource = this.dataSources[0].id
            },
            methods: {
                getTemplates() {
                    var my = this
                    new ConcreteAjaxRequest({
                        url: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/boards/custom_slot/get_templates?boardInstanceID=' +
                            my.boardInstanceID,
                        method: 'POST',
                        data: {
                            slot: my.slot,
                            selectedItemIds: my.selectedItemIds
                        },
                        success: function (r) {
                            my.currentStep = 'templates'
                            my.templateOptions = r;
                        }
                    })
                },
                handleCancelButton() {
                    if (this.currentStep === 'items') {
                        jQuery.fn.dialog.closeTop();
                    }
                    if (this.currentStep === 'templates') {
                        this.currentStep = 'items'
                    }
                },
                toggleChecked(item) {
                    if (this.selectedItemIds.includes(item.id)) {
                        this.selectedItemIds.splice(this.selectedItemIds.indexOf(item.id), 1)
                    } else {
                        this.selectedItemIds.push(item.id)
                    }
                },
                performSearch(event) {
                    event.preventDefault()
                    var my = this
                    new ConcreteAjaxRequest({
                        url: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/boards/custom_slot/search_items?boardInstanceID=' +
                            my.boardInstanceID,
                        method: 'POST',
                        data: {
                            keywords: my.searchKeywords
                        },
                        success: function (r) {
                            my.dataSources = r;
                        }
                    })
                },
                handleSaveButton() {
                    var my = this
                    if (this.currentStep === 'items') {
                        this.getTemplates();
                    }
                    if (this.currentStep === 'templates') {
                        new ConcreteAjaxRequest({
                            url: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/boards/custom_slot/save_template?boardInstanceID=' +
                                my.boardInstanceID,
                            method: 'POST',
                            dataType: 'html',
                            data: {
                                slot: my.slot,
                                selectedTemplateOption: my.templateOptions[my.selectedTemplateOption]
                            },
                            success: function (r) {
                                var customSlotData = {
                                    content: r,
                                    slot: my.slot,
                                }
                                ConcreteEvent.fire('SaveCustomSlot', customSlotData)
                                jQuery.fn.dialog.closeTop()
                            }
                        })
                    }
                }
            },
            computed: {
                cancelButtonText: function () {
                    if (this.currentStep == 'items') {
                        return '<?=t('Close')?>'
                    } else if (this.currentStep === 'templates') {
                        return '<?=t('Back')?>'
                    }
                },
                saveButtonText: function () {
                    if (this.currentStep == 'items') {
                        return '<?=t('Next')?>'
                    } else if (this.currentStep === 'templates') {
                        return '<?=t('Save')?>'
                    }
                }
            },
            watch: {},
            data: {
                currentStep: 'items',
                searchKeywords: '',
                selectedItemIds: [],
                boardInstanceID: <?=$instance->getBoardInstanceID()?>,
                slot: <?=(int)$slot?>,
                dataSources: <?=$dataSourcesJson?>,
                activeDataSource: 0,
                templateOptions: [],
                selectedTemplateOption: -1
            }
        })
    })


</script>
