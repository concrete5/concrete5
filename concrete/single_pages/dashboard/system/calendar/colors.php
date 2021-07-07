<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php $color = \Core::make("helper/form/color"); ?>

<form method="post" action="<?=$view->action('save')?>">
    <?=$token->output('save')?>
    <fieldset>
        <legend><?=t('Default Colors')?></legend>
        <div class="row">
            <div class="form-group col-sm-6 col-sm-offset-1">
                <div class="row row-cols-auto g-0 align-items-center">
                    <div class="col-auto">
                        <?=$form->label('defaultBackgroundColor', t('Background'))?>
                    </div>
                    <div class="col-auto">
                        <?=$color->output('defaultBackgroundColor', $defaultBackgroundColor)?>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-5">
                <div class="row row-cols-auto g-0 align-items-center">
                    <div class="col-auto">
                        <?=$form->label('defaultTextColor', t('Text'))?>
                    </div>
                    <div class="col-auto">
                        <?=$color->output('defaultTextColor', $defaultTextColor)?>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend><?=t('Category Colors')?></legend>
    
        <?php if (!empty($topics) && is_array($topics)) {
    ?>
            <table class="table" data-table="category-colors">
                <thead>
                <tr>
                    <th><?=t('Override')?></th>
                    <th><?=t('Category')?></th>
                    <th><?=t('Background')?></th>
                    <th><?=t('Text')?></th>
                </tr>
                </thead>
                <tbody>
            <?php foreach ($topics as $topic) {
    $checked = array_key_exists($topic->getTreeNodeName(), $categories);
    $backgroundColor = $defaultBackgroundColor;
    $textColor = $defaultTextColor;
    if (isset($categories[$topic->getTreeNodeName()]['background'])) {
        $backgroundColor = $categories[$topic->getTreeNodeName()]['background'];
    }
    if (isset($categories[$topic->getTreeNodeName()]['text'])) {
        $textColor = $categories[$topic->getTreeNodeName()]['text'];
    }
    ?>
                <tr>
                    <td style="text-align: center; width: 10px"><?=$form->checkbox('override[]', $topic->getTreeNodeID(), $checked)?></td>
                    <td style="width: 50%"><?=$topic->getTreeNodeDisplayName()?></td>
                    <td><?=$color->output('backgroundColor[' . $topic->getTreeNodeID() . ']', $backgroundColor)?></td>
                    <td><?=$color->output('textColor[' . $topic->getTreeNodeID() . ']', $textColor)?></td>
                </tr>
            <?php 
}
    ?>
            </table>
        <?php 
} else {
    ?>
            <p><?=t('You have not defined any categories for your calendars.')?></p>
        <?php 
} ?>

    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-success" type="submit" ><?=t('Save')?></button>
        </div>
    </div>

    <script type="text/javascript">
        $(function() {
            $('table[data-table=category-colors] [data-color-picker]').each(function() {
                $(this).spectrum('disable');
            });
            $('table[data-table=category-colors] input[type=checkbox]').on('change', function() {
                if ($(this).is(':checked')) {
                    $(this).parent().parent().find('[data-color-picker]').spectrum('enable');
                } else {
                    var backgroundColor = $('input[name=defaultBackgroundColor]').val();
                    var textColor = $('input[name=defaultTextColor]').val();
                    var backgroundPicker = $($(this).parent().parent().find('[data-color-picker]')[0]);
                    var textPicker = $($(this).parent().parent().find('[data-color-picker]')[1]);
                    backgroundPicker.spectrum('set', backgroundColor);
                    textPicker.spectrum('set', textColor);
                    backgroundPicker.spectrum('disable');
                    textPicker.spectrum('disable');
                }
            }).trigger('change');

            $('input[name=defaultBackgroundColor]').on('change', function() {
                var color = $(this).val();
                $('table[data-table=category-colors] input[type=checkbox]').each(function() {
                    if (!$(this).is(':checked')) {
                        var backgroundPicker = $($(this).parent().parent().find('[data-color-picker]')[0]);
                        backgroundPicker.spectrum('set', color);
                    }
                });
            });
            $('input[name=defaultTextColor]').on('change', function() {
                var color = $(this).val();
                $('table[data-table=category-colors] input[type=checkbox]').each(function() {
                    if (!$(this).is(':checked')) {
                        var textPicker = $($(this).parent().parent().find('[data-color-picker]')[1]);
                        textPicker.spectrum('set', color);
                    }
                });
            });
        });
    </script>

</form>
