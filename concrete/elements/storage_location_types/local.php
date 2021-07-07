<?php
defined('C5_EXECUTE') or die('Access Denied');

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$form = $app->make('helper/form');

$locationHasFiles = false;
if (is_object($location)) {
    $locationHasFiles = $location->hasFiles();
}
if (is_object($configuration)) {
    $path = $configuration->getRootPath();
    $relativePath = $configuration->getWebRootRelativePath();
}
?>
<div class="form-group">
    <label for="path"><?= t('Root Path'); ?></label>
    <div class="input-group">
        <?php
            $fslTypeOptions = [
                'placeholder' => t('Example: %ssome/path', $_SERVER['DOCUMENT_ROOT'].'/')
            ];
            if ($locationHasFiles) {
                $fslTypeOptions['readonly'] = true;
            }
            echo $form->text('fslType[path]', $path, $fslTypeOptions);
        ?>
        <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
    </div>
    <?php if ($locationHasFiles) { ?>
        <span class="help-block form-text"><?= t('You can not change the root path of this storage location because it contains files.') ?></span>
    <?php } ?>
</div>
<div class="form-group">
    <label for="path"><?= t('Relative Path'); ?></label>
    <?= $form->text('fslType[relativePath]', $relativePath, ['placeholder' => t('Example: %ssome/path', '/')]); ?>
</div>
