<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<form method="post" id="clear-cache-form" action="<?php echo $view->url('/dashboard/system/optimization/clearcache', 'do_clear')?>">
	<?php echo $this->controller->token->output('clear_cache')?>
    <div class="form-group">
        <p><?php echo t('If your site is displaying out-dated information, or behaving unexpectedly, it may help to clear your cache.')?></p>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="thumbnails" value="1" <?= \Config::get('concrete.cache.clear.thumbnails', true) ? 'checked' : '' ?> />
            <?= t('Clear cached thumbnails') ?>
        </label>
    </div>
    <div class="form-group">
        <?= $interface->submit(t('Clear Cache'), 'clear-cache-form', '', 'btn-primary'); ?>
    </div>
</form>


