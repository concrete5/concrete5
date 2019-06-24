<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<fieldset>
    <legend><?=t('Display')?></legend>
    <div class="form-group ccm-block-feature-select-icon">
        <label class="control-label" for="icon"><?=t('Icon')?></label>
        <?=$form->select('icon', $icons, $icon);?>
    </div>
    <div class="form-group">
        <label class="control-label"><?=t('Preview')?></label>
        <div>
        <i data-preview="icon" <?php if ($icon) {
        ?>class="fa fa-<?=$icon?>"<?php
        } ?>></i>
        </div>
    </div>

    <div class="form-group">
        <?=$form->label('title', t('Title'))?>
        <?php echo $form->text('title', $title); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('paragraph', t('Paragraph:'));?>
        <?php
            $editor = Core::make('editor');
            echo $editor->outputBlockEditModeEditor('paragraph', $controller->getParagraphEditMode());
        ?>
    </div>

</fieldset>

<fieldset>
    <legend><?=t('Link')?></legend>

    <div class="form-group">
        <select name="linkType" data-select="feature-link-type" class="form-control">
            <option value="0" <?=(empty($externalLink) && empty($internalLinkCID) ? 'selected="selected"' : '')?>><?=t('None')?></option>
            <option value="1" <?=(empty($externalLink) && !empty($internalLinkCID) ? 'selected="selected"' : '')?>><?=t('Another Page')?></option>
            <option value="2" <?=(!empty($externalLink) ? 'selected="selected"' : '')?>><?=t('External URL')?></option>
        </select>
    </div>

    <div data-select-contents="feature-link-type-internal" style="display: none;" class="form-group">
        <?=$form->label('internalLinkCID', t('Choose Page:'))?>
        <?= Loader::helper('form/page_selector')->selectPage('internalLinkCID', $internalLinkCID); ?>
    </div>

    <div data-select-contents="feature-link-type-external" style="display: none;" class="form-group">
        <?=$form->label('externalLink', t('URL'))?>
        <?= $form->text('externalLink', $externalLink); ?>
    </div>

</fieldset>

<script type="text/javascript">
$(function() {
    $('div.ccm-block-feature-select-icon').on('change', 'select', function() {
        $('i[data-preview="icon"]').removeClass();
        if($(this).val()) {
            $('i[data-preview="icon"]').addClass('fa fa-' + $(this).val());
        }
    });
    $('select[data-select=feature-link-type]').on('change', function() {
       if ($(this).val() == '0') {
           $('div[data-select-contents=feature-link-type-internal]').hide();
           $('div[data-select-contents=feature-link-type-external]').hide();
       }
       if ($(this).val() == '1') {
           $('div[data-select-contents=feature-link-type-internal]').show();
           $('div[data-select-contents=feature-link-type-external]').hide();
       }
       if ($(this).val() == '2') {
           $('div[data-select-contents=feature-link-type-internal]').hide();
           $('div[data-select-contents=feature-link-type-external]').show();
       }
    }).trigger('change');
});
</script>

<style type="text/css">
    div.ccm-block-feature-select-icon {
        position: relative;
    }
    div.ccm-block-feature-select-icon i {
        position: absolute;
        right: -25px;
        top: 10px;
    }
    [data-preview="icon"] {
        font-size: 50px;
    }
</style>
