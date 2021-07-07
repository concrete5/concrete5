<template>
    <div class="row mb-5">
        <div class="col-4">
            <span v-if="hasStartDate">
                {{startDate}}
            </span>
            <span v-else class="text-muted">
                No Start Date
            </span>

            <span v-if="hasStartDate && hasEndDate">
                –
                {{endDate}}
            </span>
        </div>
        <div class="col-7">
            <div>{{rule.name}} (<a data-dialog="preview" href="javascript:void(0)" @click="showPreview">preview</a>)</div>
            <div class="fw-light font-italic">{{rule.actionDescription}}</div>
        </div>
        <div class="col-1">
            <a href="javascript:void(0)" @click="$emit('delete', rule)" class="ccm-hover-icon" v-if="showDeleteControls && rule.canDeleteRule">
                <svg width="20" height="20"><use xlink:href="#icon-minus-circle" /></svg>
            </a>
        </div>
        <div style="display: none">
            <div :id="'preview-container-' + rule.id">
                <iframe v-if="previewLoaded" style="border: 0px; width: 100%; height: 100%" :src="previewUrl"></iframe>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment-timezone'

export default {
    props: {
        rule: Object,
        showDeleteControls: {
            type: Boolean,
            default: false
        }
    },
    data: () => ({
        previewLoaded: false
    }),
    methods: {
        showPreview() {
            var my = this

            if (!this.previewLoaded) {
                this.previewLoaded = true
            }

            $.fn.dialog.open({
                width: '90%',
                height: '70%',
                title: 'Preview',
                element: '#preview-container-' + my.rule.id
            })
        }
    },
    computed: {
        previewUrl: function() {
            return CCM_DISPATCHER_FILENAME + '/ccm/system/board/instance/preview_rule/' + this.rule.id
        },
        hasStartDate: function() {
            return this.rule.startDate > 0;
        },
        hasEndDate: function() {
            return this.rule.endDate > 0;
        },
        startDate: function() {
            if (this.rule.startDate > 0) {
                let momentDate = moment.unix(this.rule.startDate).tz(this.rule.timezone)
                return momentDate.format('MMMM D, YYYY')
            }
        },
        endDate: function() {
            if (this.rule.endDate > 0) {
                let momentDate = moment.unix(this.rule.endDate).tz(this.rule.timezone)
                return momentDate.format('MMMM D, YYYY')
            }
        }
    }
}
</script>

