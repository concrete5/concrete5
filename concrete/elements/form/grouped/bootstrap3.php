<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<fieldset>
    <?php if ($view->supportsLabel()) { ?>
        <legend class="control-label form-label"><?=$view->getLabel()?></legend>
    <?php } ?>

    <?php $view->renderControl()?>
</fieldset>
