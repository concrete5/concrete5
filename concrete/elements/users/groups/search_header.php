<?php

defined('C5_EXECUTE') or die("Access Denied.");

/** @var bool $canAddGroup */

?>

<div class="ccm-dashboard-header-buttons">
    <?php if ($canAddGroup) { ?>
        <a class="btn btn-secondary btn-sm ms-2"
           href="<?php echo (string)Url::to('/dashboard/users/add_group') ?>"
           title="<?php echo t('Add Group') ?>">
            <?php echo t('Add Group') ?> <i class="fa fa-plus-circle"></i>
        </a>
    <?php } ?>
</div>
