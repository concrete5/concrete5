<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Permission\Checker as PermissionChecker;

ob_start();
View::element('permission/help');
$help = ob_get_clean();
?>

<form method="post" action="<?= $view->action('save') ?>">
    <?php app('helper/validation/token')->output('save_permissions') ?>
    <?php
    $tp = new PermissionChecker();
    if ($tp->canAccessTaskPermissions()) {
        View::element('permission/lists/block_type');
    } else {
        ?>
        <p><?= t('You cannot access these permissions.') ?></p>
        <?php
    }
    ?>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button type="submit" value="<?= t('Save') ?>" class="btn btn-primary float-end"><?= t('Save') ?></button>
        </div>
    </div>
</form>
