<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div data-view="edit-board-block">
    <div class="form-group">
        <?php echo $form->label('boardID', t('Board'))?>
        <?php echo $form->select('boardID', $boardSelect, $boardID, [
            'v-model' => 'boardID'
        ])?>
    </div>

    <div class="form-group" v-show="boardID > 0">
        <label class="control-label form-label"><?=t('Board Instance')?></label>
        <div class="form-check">
            <input class="form-check-input" v-model="createNewInstance" type="radio"
                   name="newInstance" id="createNewInstance" value="1">
            <label class="form-check-label" for="createNewInstance">
                <?=t('Create new instance.')?>
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" v-model="createNewInstance"  :disabled="instances.length < 1" type="radio"
                   name="newInstance" id="useExistingInstance" value="0">
            <label class="form-check-label" for="useExistingInstance">
                <?=t('Use existing instance.')?>
            </label>
        </div>
    </div>

    <div class="form-group" v-show="createNewInstance == 0 && instances.length > 0">
        <label class="control-label form-label"><?=t('Select Instance')?></label>
        <select class="form-select" name="boardInstanceID">
            <option value=""><?=t('(Choose Instance)')?></option>
            <option v-for="instance in instances" :selected="boardInstanceID == instance.boardInstanceID"
                    :key="instance.boardInstanceID" :value="instance.boardInstanceID">
                {{instance.name}}
            </option>
        </select>
    </div>

</div>

<script type="text/javascript">

    Concrete.Vue.activateContext('cms', function (Vue, config) {
        Vue.config.devtools = true;
        new Vue({
            el: 'div[data-view=edit-board-block]',
            components: config.components,
            watch: {
                'boardID': {
                    immediate: true,
                    handler(value) {
                        my = this
                        new ConcreteAjaxRequest({
                            url: '<?=$view->action('get_instances')?>',
                            data: {
                                'boardID': value,
                            },
                            success: function(r) {
                                my.instances = r
                            }
                        })
                    }
                }
            },
            data: {
                boardID: '<?=$boardID?>',
                boardInstanceID: '<?=$boardInstanceID?>',
                instances: [],
                createNewInstance: <?=$boardInstanceID > 0 ? '0' : '1' ?>
            }
        })
    })


</script>

