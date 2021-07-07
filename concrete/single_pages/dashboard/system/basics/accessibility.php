<form action="<?= View::action('save') ?>" method="post">
    <?php
    Core::make('token')->output('accessibility');
    ?>
    <fieldset>
        <div class="form-group">
            <div class="form-check">
                <input class="form-check-input" name="show_titles" value="1" type="checkbox" <?= $show_titles ? 'checked' : '' ?> />
                <label class="form-check-label" for="show_titles">
                    <?= t('Enable Toolbar Titles') ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" name="show_tooltips" value="1" type="checkbox" <?= $show_tooltips ? 'checked' : '' ?> />
                <label class="form-check-label" for="show_tooltips">
                    <?= t('Enable Prominent Toolbar Tooltips') ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" name="increase_font_size" value="1" type="checkbox" <?= $increase_font_size ? 'checked' : '' ?> />
                <label class="form-check-label" for="increase_font_size">
                    <?= t('Increase Toolbar Font Size') ?>
                </label>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend><?=t('File Manager')?></legend>
        <div class="form-group">
            <div class="form-check">
                <input class="form-check-input" name="full_lisiting_thumbnails" value="1" type="checkbox" <?= $full_lisiting_thumbnails ? 'checked' : '' ?> />
                <label class="form-check-label" for="full_lisiting_thumbnails">
                    <?= t('Enable Full Size Image Thumbnails') ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" name="preview_popover" value="1" type="checkbox" <?= $preview_popover ? 'checked' : '' ?> />
                <label class="form-check-label" for="preview_popover">
                    <?= t('Enable Preview Image Popover') ?>
                </label>
            </div>
        </div>
    </fieldset>
    <h2></h2>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary">
                <?=t('Save')?>
            </button>
        </div>
    </div>
</form>
