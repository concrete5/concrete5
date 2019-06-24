<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php
if (version_compare($latest_version, APP_VERSION, '>')) {
    ?>
    <div class="alert alert-info">
        <?php echo t('The latest version of concrete5 is <strong>%s</strong>. You are currently running concrete5 version <strong>%s</strong>.', $latest_version, APP_VERSION) ?>
        <a class="pull-right btn btn-info btn-xs" href="<?php echo $view->url('/dashboard/system/update/update')?>"><?php echo t('Update')?></a>
    </div>
    <?php
} elseif (version_compare(APP_VERSION, Config::get('concrete.version'), '>')) {
    ?>
    <div class="alert alert-warning">
        <?php echo t('You have downloaded a new version of concrete5 but have not upgraded to it yet.'); ?>
        <a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/upgrade" class="pull-right btn btn-warning btn-xs"><?php echo t('Update')?></a></div>
    <?php
}

if ($updates > 0) {
    ?>
    <div class="alert alert-info clearfix">
        <?php echo t2('There is currently %d add-on update available.', 'There are currently %d add-on updates available.', $updates) ?>
        <a class="btn btn-info btn-xs pull-right" href="<?php echo $view->url('/dashboard/extend/update') ?>"><?php echo t('Update') ?></a>
    </div>
    <?php
}