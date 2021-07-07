<?php
defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var string $file_access_file_types
 * @var string[] $file_access_file_types_blacklist
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Page\View\PageView $view
 */
?>
<form method="POST" action="<?= h($view->action('file_access_extensions')) ?>">
    <?= $token->output('file_access_extensions') ?>

    <div class="form-group">
        <?= $form->label('file-access-file-types', t('File Extensions to Accept'), ['class' => 'launch-tooltip form-label', 'title' => t('Only files with the following extensions will be allowed. Separate extensions with commas. Periods and spaces will be ignored.')]) ?>
        <?= $form->textarea('file-access-file-types', h($file_access_file_types), ['rows' => 3]) ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <input type="submit" class="float-end btn btn-primary" value="<?= t('Save') ?>" />
        </div>
    </div>

    <?php
    if ($file_access_file_types_blacklist !== []) {
        ?>
        <div class="alert alert-info">
            <?= t('These file extensions will always be blocked: %s', '<code>' . implode('</code>, <code>', $file_access_file_types_blacklist) . '</code>') ?><br />
            <br />
            <?= t('If you want to unblock these extensions, you have to manually set the %s configuration key.', '<code>concrete.upload.extensions_blacklist</code>') ?>
        </div>
        <?php
    }
    ?>
</form>
