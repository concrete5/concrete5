<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="checkbox">
    <label>
        <input
            type="checkbox"
            value="1"
            name="<?=$view->field('value')?>"
            <?php if ($checked) { ?> checked <?php } ?>
        >
        <?=t($controller->getCheckboxLabel())?>
    </label>
</div>