<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>  

<div id="ccm-block-html-value"><?php echo htmlspecialchars($content,ENT_QUOTES,APP_CHARSET) ?></div>
<textarea style="display: none" id="ccm-block-html-value-textarea" name="content"></textarea>

<style type="text/css">
    #ccm-block-html-value {
        width: 100%;
        border: 1px solid #eee;
        height: 490px;
    }
</style>

<script type="text/javascript">
    $(function() {
        var editor = ace.edit("ccm-block-html-value");
        editor.setTheme("ace/theme/eclipse");
        editor.getSession().setMode("ace/mode/html");
        refreshTextarea(editor.getValue());
        editor.getSession().on('change', function() {
            refreshTextarea(editor.getValue());
        });
    });

    function refreshTextarea(contents) {
        $('#ccm-block-html-value-textarea').val(contents);
    }
</script>
