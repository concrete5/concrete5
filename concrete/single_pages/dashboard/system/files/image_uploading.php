<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\View\PageView $view */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Form\Service\Form $form */

/* @var array $manipulation_libraries */
/* @var string $manipulation_library */
/* @var int $jpeg_quality */
/* @var int $png_compression */
/* @var int $restrict_max_width */
/* @var int $restrict_max_height */
/* @var string $svg_processor_action */
/* @var array $svg_processor_actions */
/* @var bool $use_exif_data_to_rotate_images */
/* @var bool $exif_reader_supported */
/* @var string|null $thumbnailOptionsURL */
/** @var bool $use_exif_data_for_file_name_attribute */
/** @var bool $use_exif_data_for_description_attribute */
/** @var bool $use_exif_data_for_keyword_attribute */
/** @var bool $use_exif_data_for_additional_attributes */
?>
<form method="POST" action="<?= $view->action('save') ?>">
    <?= $token->output('image-options') ?>

    <div class="form-group">
        <?= $form->label('manipulation_library', t('Image Manipulation Library')) ?>
        <?php
        foreach ($manipulation_libraries as $id => $name) {
            ?>
            <div class="form-check">
                <?= $form->radio('manipulation_library', $id, $id === $manipulation_library, ['required' => 'required']) ?>
                <label>
                    <?= h($name) ?>
                    <?= t('(currently working: %s)', '<span class="ccm-check-manipulation-library" data-check-src="' . h($view->action('test_manipulation_library', $id, $token->generate('thumbnail-check-library-' . $id))) . '"><i class="fa fa-spinner fa-spin"></i></span>')?>
                </label>
            </div>
            <?php
        }
        ?>
    </div>

    <div class="form-group">
        <?= $form->label('jpeg_quality', t('JPEG quality'), ['class' => 'launch-tooltip control-label form-label', 'title' => t('JPEG quality ranges from 0 (worst quality, smaller file) to 100 (best quality, biggest file)')]) ?>
        <?= $form->number('jpeg_quality', $jpeg_quality, ['required' => 'required', 'min' => '0', 'max' => '100']) ?>
    </div>

    <div class="form-group">
        <?= $form->label('png_compression', t('PNG compression quality'), ['class' => 'launch-tooltip control-label form-label', 'title' => t('PNG compression quality ranges from 0 (no compression) to 9 (maximum compression)')]) ?>
        <?= $form->number('png_compression', $png_compression, ['required' => 'required', 'min' => '0', 'max' => '9']) ?>
    </div>

    <div class="form-group">
        <?= $form->label('restrict_max_width', t('Maximum width of uploaded images'), ['class' => 'launch-tooltip form-label', 'title' => t('Here you can set the maximum width of uploaded images: images wider that this value will be scaled down. Leave empty to allow any image width.')]) ?>
        <div class="input-group">
            <?= $form->number('restrict_max_width', $restrict_max_width > 0 ? $restrict_max_width : '', ['min' => '0']) ?>
            <div class="input-group-text"><?= t(/* i18n: short for pixels */ 'px') ?></div>
        </div>
    </div>
    <div class="form-group">
        <?= $form->label('restrict_max_height', t('Maximum height of uploaded images'), ['class' => 'launch-tooltip form-label', 'title' => t('Here you can set the maximum height of uploaded images: images taller that this value will be scaled down. Leave empty to allow any image height.')]) ?>
        <div class="input-group">
            <?= $form->number('restrict_max_height', $restrict_max_height > 0 ? $restrict_max_height : '', ['min' => '0']) ?>
            <div class="input-group-text"><?= t(/* i18n: short for pixels */ 'px') ?></div>
        </div>
    </div>

    <div class="form-group">
        <?= $form->label('svg_processor_action', t('SVG Processing'), ['class' => 'launch-tooltip form-label', 'title' => t("SVG images may contain interactive contents. Albeit this feature may be useful, it can be used by an attacker to store malicious code in the images. Disable the SVG checks only if you trust the users that will upload images to the site.")]) ?>
        <?= $form->select('svg_processor_action', $svg_processor_actions, $svg_processor_action, ['required' => 'required']) ?>
    </div>

    <div class="form-group">
        <?= $form->label('', t('Other Options')) ?>
        <div class="form-check">
            <?= $form->checkbox('use_exif_data_to_rotate_images', 1, $use_exif_data_to_rotate_images) ?>
            <label for="use_exif_data_to_rotate_images" class="form-check-label">
                <?= t('Use EXIF metadata to auto-rotate images uploaded images.') ?>
                <?php
                if (!$exif_reader_supported) {
                    ?>
                    <span class="small text-muted" id="use_exif_data_to_rotate_images-warning"<?= $use_exif_data_to_rotate_images ? '' : ' style="display:none"' ?>>
                        <br>
                        <i class="fa fa-exclamation-triangle" aria-hidden="true" style="color: red"></i>
                        <?= t('This feature requires the %s PHP extension, which is not currently enabled.', '<code>EXIF</code>') ?>
                    </span>
                    <script>
                    $(document).ready(function() {
                        $('#use_exif_data_to_rotate_images')
                            .on('change', function() {
                                $('#use_exif_data_to_rotate_images-warning').toggle(this.checked);
                            })
                            .trigger('change')
                        ;
                    });
                    </script>
                    <?php
                }
                ?>
            </label>
        </div>

        <div class="form-check">
            <?php echo $form->checkbox("use_exif_data_for_file_name_attribute", 1, $use_exif_data_for_file_name_attribute, ["class" => "form-check-input"]); ?>
            <?php echo $form->label("use_exif_data_for_file_name_attribute", t("Use EXIF metadata to populate the file name."), ["class" => "form-check-label"]); ?>
        </div>

        <div class="form-check">
            <?php echo $form->checkbox("use_exif_data_for_description_attribute", 1, $use_exif_data_for_description_attribute, ["class" => "form-check-input"]); ?>
            <?php echo $form->label("use_exif_data_for_description_attribute", t("Use EXIF metadata to populate the description."), ["class" => "form-check-label"]); ?>
        </div>

        <div class="form-check">
            <?php echo $form->checkbox("use_exif_data_for_keyword_attribute", 1, $use_exif_data_for_keyword_attribute, ["class" => "form-check-input"]); ?>
            <?php echo $form->label("use_exif_data_for_keyword_attribute", t("Use EXIF metadata to populate the keywords."), ["class" => "form-check-label"]); ?>
        </div>

        <div class="form-check">
            <?php echo $form->checkbox("use_exif_data_for_additional_attributes", 1, $use_exif_data_for_additional_attributes, ["class" => "form-check-input"]); ?>
            <?php echo $form->label("use_exif_data_for_additional_attributes", t("Use EXIF metadata to populate additional data if available."), ["class" => "form-check-label"]); ?>
        </div>



    </div>

    <?php
    if ($thumbnailOptionsURL !== null) {
        ?>
        <div class="alert alert-info">
            <?= t('Looking for thumbnail options? You can find them <a href="%s">here</a>.', $thumbnailOptionsURL) ?>
        </div>
        <?php
    }
    ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary" type="submit"><?= t('Save') ?></button>
        </div>
    </div>
</form>
<script>
$(window).load(function() {
    function checked($container, ok) {
        $container.html(
            ok ? '<i class="fa fa-check" style="color: green"></i>' : '<i class="fa fa-remove" style="color: red"></i>'
        );
    }
    $('.ccm-check-manipulation-library').each(function() {
        var $container = $(this);
        $container.append($('<img style="visibility: hidden; width: 1px; height: 1px" />')
            .on('load', function() {
                checked($container, true);
            })
            .on('error', function() {
                checked($container, false);
            })
            .attr('src', $container.data('check-src'))
        );
    });
});
</script>
