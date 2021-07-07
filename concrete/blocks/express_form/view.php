<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var \Concrete\Core\Block\View\BlockView $view */
/** @var \Concrete\Core\Express\Form\Renderer|null $renderer */
/** @var string|null $success */
/** @var string $bID */
/** @var \Concrete\Core\Error\ErrorList\ErrorList|null $error */
/** @var \Concrete\Core\Captcha\CaptchaInterface|null $captcha */
/** @var string $displayCaptcha "0" or "1" */
/** @var string $submitLabel */
?>
<div class="ccm-block-express-form">
    <?php if (isset($renderer)) {
    ?>
        <div class="ccm-form">
            <a name="form<?=$bID; ?>"></a>

            <?php if (isset($success)) {
        ?>
                <div class="alert alert-success">
                    <?=$success; ?>
                </div>
            <?php
    } ?>

            <?php if (isset($error) && is_object($error)) {
        ?>
                <div class="alert alert-danger">
                    <?=$error->output(); ?>
                </div>
            <?php } ?>

            <form enctype="multipart/form-data" class="form-stacked" method="post" action="<?=$view->action('submit')?>#form<?=$bID?>">
                <?php
                $renderer->render();

                if ($displayCaptcha) {
                    ?>
                    <div class="form-group captcha">
                        <?php
                        $captchaLabel = $captcha->label();
                        if (!empty($captchaLabel)) {
                            ?>
                            <label class="control-label form-label"><?php echo $captchaLabel; ?></label>
                            <?php } ?>

                        <div><?php $captcha->display(); ?></div>
                        <div><?php $captcha->showInput(); ?></div>
                    </div>
                <?php } ?>

                <div class="form-actions">
                    <button type="submit" name="Submit" class="btn btn-primary"><?=t($submitLabel); ?></button>
                </div>
            </form>
        </div>
    <?php
} else {
        ?>
        <p><?=t('This form is unavailable.'); ?></p>
    <?php
    } ?>
</div>
