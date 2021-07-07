<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php

if (is_object($renderer)) {
    ?>

    <form method="post" action="<?=$view->action('submit', $entity->getId())?>">
        <input type="hidden" name="entry_id" value="<?=$entry->getID()?>">
    <?php
        echo $renderer->render($entry);
    ?>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <?php if ($backURL) { ?>
                    <a class="btn btn-secondary" href="<?=$backURL?>"><?=t('Back')?></a>
                <?php } ?>
                <button class="float-end btn btn-primary" type="submit"><?=t('Save %s', $entity->getEntityDisplayName())?></button>
            </div>
        </div>


<?php

} else {
    ?>
    <p><?=t('You have not created any forms for this data type.')?></p>
<?php 
} ?>
