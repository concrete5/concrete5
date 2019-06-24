<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php

if (is_object($renderer)) {
    ?>

    <form method="post" action="<?=$view->action('submit', $entity->getId())?>">

    <?php
        echo $renderer->render(); ?>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <?php if ($backURL) {
    ?>
                    <a class="pull-left btn btn-default" href="<?=$backURL?>"><?=t('Back')?></a>
                <?php 
} ?>
                <button class="pull-right btn btn-primary" type="submit"><?=t('Add %s', $entity->getEntityDisplayName())?></button>
            </div>
        </div>

    </form>
<?php

} else {
    ?>
    <p><?=t('You have not created any forms for this data type.')?></p>
<?php 
} ?>


<script type="text/javascript">
    $(function() {
        $('form input, form select, form textarea').each(function() {
            if ($(this).is(':visible')) {
                $(this).get(0).focus();
                return false;
            }
        });
    });
</script>