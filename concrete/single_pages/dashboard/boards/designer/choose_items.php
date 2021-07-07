<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @TODO - this entire page needs to be abstracted so that the board data sources
 * can provide both the VueJS options for their selectors as well as the items that can be selected
 * Right now since calendar event and page are the only data sources we're going to hard-code those into this
 * page, because I don't want to add methods to the data source interfaces only to have us undo them in the future
 * So in the meantime, we're going to hard code this page for calendar event and page, and come back to making it
 * more abstracted sometime soon.
 */
?>


<form method="post" action="<?=$view->action('submit', $element->getID())?>">
    <?=$token->output('submit')?>

    <div data-form="choose-items" v-cloak>
        <div v-for="(item, index) in items" :key="index">

            <a @click="removeItem(index)" href="#" class="ccm-hover-icon float-end">
                <svg width="20" height="20"><use xlink:href="#icon-minus-circle" /></svg>
            </a>

            <div v-if="item.itemType === 'page'">
                <div class="form-group">
                    <label class="control-label form-label"><?=t('Page')?></label>
                    <concrete-page-input
                            choose-text="<?=t('Choose Page')?>"
                            input-name="field[page][]"
                            :page-id="item.data.pageId"
                    ></concrete-page-input>
                </div>
            </div>

            <div v-if="item.itemType === 'calendar_event'">
                <div class="form-group">
                    <label class="control-label form-label"><?=t('Choose Calendar')?></label>
                    <select v-model.number="items[index].data.calendarId" class="form-select">
                        <?php foreach($calendarSelect as $id => $calendar) { ?>
                            <option value="<?=$id?>"><?=$calendar?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group" v-if="items[index].data.calendarId > 0">
                    <label class="control-label form-label"><?=t('Event')?></label>
                    <concrete-event-occurrence-input
                            :calendar-id="items[index].data.calendarId"
                            choose-text="<?=t('Choose Event')?>"
                            input-name="field[calendar_event][]"
                            :event-occurrence-id="item.data.eventVersionOccurrenceId">

                    </concrete-event-occurrence-input>
                </div>
            </div>

        </div>

        <h3 class="fw-light"><?=t('Add Item')?></h3>
        <div class="dropdown">
            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <?=t('Choose Type')?>
            </button>

            <div class="dropdown-menu">
                <a href="#" @click="addItem('page')" class="dropdown-item"><?=t('Page')?></a>
                <a href="#" @click="addItem('calendar_event')" class="dropdown-item"><?=t('Calendar Event')?></a>
            </div>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button type="submit" class="btn float-end btn-secondary"><?=t('Next')?></button>
        </div>
    </div>
</form>

<script type="text/javascript">
$(function() {
    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-form=choose-items]',
            components: config.components,
            data: {
                items: <?=json_encode($items)?>,
            },

            watch: {},

            methods: {
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                addItem(itemTypeHandle) {
                    this.items.push({
                        itemType: itemTypeHandle,
                        data: {
                            'calendarId': '0'
                        }
                    })
                }
            }
        })
    })
});
</script>
