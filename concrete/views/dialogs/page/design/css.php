<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="ccm-ui">

    <div id="ccm-page-design-custom-css"><?=h($value)?></div>
    <textarea style="display: none" id="ccm-page-design-custom-css-textarea" name="value"></textarea>

    <div class="dialog-buttons">
    <button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
    <button type="button" data-dialog-action="submit-css-form" class="btn btn-primary pull-right"><?=t('Save')?></button>
    </div>

    <style type="text/css">
        #ccm-page-design-custom-css {
            width: 100%;
            border: 1px solid #eee;
            height: 420px;
        }
    </style>

    <script type="text/javascript">
        $(function() {
            var editor = ace.edit("ccm-page-design-custom-css");
            editor.setTheme("ace/theme/eclipse");
            editor.getSession().setMode("ace/mode/css");
            editor.getSession().on('change', function() {
                $('#ccm-page-design-custom-css-textarea').val(editor.getValue());
            });

            $('button[data-dialog-action=submit-css-form]').on('click', function() {
                $.concreteAjax({
                    url: '<?=$controller->action('submit')?>',
                    data: {
                        'value': editor.getValue()
                    },
                    success: function(r) {
                        jQuery.fn.dialog.closeTop();
                        $('form[data-form=panel-page-design-customize] input[name=sccRecordID]').val(r.sccRecordID);
                    }
                });
            });
        });
    </script>

</div>