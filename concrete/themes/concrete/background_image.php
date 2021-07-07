<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$view->inc('elements/header.php'); ?>

<style>
    body {
        background: url("<?= ASSETS_URL_IMAGES ?>/bg_login.png");
    }
</style>

<div>
    <div class="container">
    <div class="row justify-content-center">
    <div class="col-sm-10">
    <?php
    View::element(
        'system_errors',
        array(
            'format' => 'block',
            'error' => isset($error) ? $error : null,
            'success' => isset($success) ? $success : null,
            'message' => isset($message) ? $message : null,
        )
    );
    ?>
    </div>
    </div>
    
    <?php echo $innerContent ?>
    
    </div>
</div>

<div id="ccm-page-background-credit" 
     data-background-fade="<?= ASSETS_URL_IMAGES ?>/login_fade.png" 
     data-background-image="<?=Config::get('concrete.white_label.background_image')?>" 
     data-background-url="<?=Config::get('concrete.white_label.background_url')?>"
     data-background-feed="<?=Config::get('concrete.urls.background_feed')?>"
     class="ccm-page-background-credit" style="display:none">
    <div class="ccm-page-background-privacy-notice float-start">
        <?=t('Image served from concrete5.org. <a href="%s" target="_blank">View Privacy Policy</a>.',
            Config::get('concrete.urls.privacy_policy'))?>
    </div>
    <div class="ccm-page-background-photo-credit float-end">
        <?= t('Photo Credit:') ?>
        <a href="#"></a>
    </div>
</div>

</div>
</div>

<?php $view->inc('elements/footer.php'); ?>
