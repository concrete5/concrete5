<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<form method="post" action="<?=$view->action('save')?>">
    <?=$token->output('save')?>

    <div class="control-group">
        <label class="control-label" for="topicAttribute"><?=t('Calendar Topics Attribute')?></label>
        <?=$form->select('topicAttribute', $topicAttributes, $topicAttribute)?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-right btn btn-success" type="submit" ><?=t('Save')?></button>
        </div>
    </div>


</form>