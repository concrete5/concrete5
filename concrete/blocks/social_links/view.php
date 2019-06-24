<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div id="ccm-block-social-links<?php echo $bID; ?>" class="ccm-block-social-links">
    <ul class="list-inline">
    <?php foreach ($links as $link) {
    $service = $link->getServiceObject();
    if ($service) {
        ?>
            <li>
                <a target="_blank" rel="noopener noreferrer" href="<?php echo h($link->getURL()); ?>"
                    aria-label="<?php echo $service->getDisplayName(); ?>"><?php echo $service->getServiceIconHTML(); ?></a>
            </li>
        <?php
    }
}?>
    </ul>
</div>
