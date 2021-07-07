<?php if ($style instanceof \Concrete\Core\Block\CustomStyle) { ?>

<form method="post" action="<?=$saveAction?>" id="ccm-inline-design-form">
    <ul class="ccm-inline-toolbar ccm-ui">
        <li class="ccm-inline-toolbar-select">
            <select id="bFilename" name="bFilename" class="form-select">
                <option value="">(<?=t('None selected')?>)</option>
                <?php
                foreach ($templates as $tpl) { ?>
                    <option value="<?=$tpl->getTemplateFileFilename()?>" <?php if ($bFilename == $tpl->getTemplateFileFilename()) { ?> selected <?php } ?>><?=$tpl->getTemplateFileDisplayName()?></option>
                <?php } ?>
            </select>
        </li>
        <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel">
            <button data-action="cancel-design" type="button" class="btn btn-mini"><?=t("Cancel")?></button>
        </li>
        <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save">
            <button data-action="save-design" class="btn btn-primary" type="button"><?=t('Save')?></button>
        </li>
    </ul>
</form>
<script>
  $('#ccm-inline-design-form').concreteBlockInlineStyleCustomizer();
</script>
<?php } ?>

