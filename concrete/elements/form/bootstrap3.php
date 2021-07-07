<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group">
    <?php if ($view->supportsLabel()) { ?>
        <label class="control-label form-label" for="<?=$view->getControlID()?>"><?=$view->getLabel()?></label>
    <?php } ?>

    <?php if ($view->isRequired()) { ?>
        <span class="text-muted small"><?=t('Required')?></span>
    <?php } ?>

    <?php $view->renderControl()?>
</div>
