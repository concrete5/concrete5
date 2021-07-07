<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Optimization\Cache $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var bool $clearThumbnails
 */

?>
<form method="post" action="<?= $controller->action('do_clear') ?>">
    <?php $token->output('clear_cache') ?>

    <p><?= t('If your site is displaying out-dated information, or behaving unexpectedly, it may help to clear your cache.') ?></p>

    <div class="form-group">
        <div class="form-check">
            <?= $form->checkbox('thumbnails', '1', $clearThumbnails) ?>
            <label class="form-check-label" for="thumbnails"><?= t('Clear cached thumbnails') ?></label>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary" type="submit" ><?= t('Clear Cache') ?></button>
        </div>
    </div>

</form>
