<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="ccm-dashboard-express-entry">
    <?php
    foreach ($form->getFieldSets() as $fieldSet) { ?>

        <fieldset>
            <?php if ($fieldSet->getTitle()) { ?>
                <legend><?= h($fieldSet->getTitle()) ?></legend>
            <?php } ?>

            <?php

            foreach($fieldSet->getControls() as $setControl) {
                $controlView = $setControl->getControlView($context);

                if (is_object($controlView)) {
                    $renderer = $controlView->getControlRenderer();
                    print $renderer->render();
                }
            }

            ?>
        </fieldset>
    <?php } ?>
</div>
