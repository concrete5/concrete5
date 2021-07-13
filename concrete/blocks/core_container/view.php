<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var $c Concrete\Core\Page\Page
 * @var $fileToRender string The file containing the container template.
 */
if ($container) {
    $container->startRender();

    $c = Page::getCurrentPage();
    if ($fileToRender) {
        include($fileToRender);
    } else {
        if ($c->isEditMode()) { ?>
            <div class="ccm-edit-mode-disabled-item">
               <?php echo t('Container: %s – no container template file found.', 
                   $container->getInstance()->getContainer()->getContainerDisplayName()); ?>
            </div>
        <?php }
    }

    $container->endRender();
} elseif (is_object($c) && $c->isEditMode()) {
    ?>
    <div class="ccm-edit-mode-disabled-item"><?=t('Empty Container Block.')?></div>
    <?php
}
