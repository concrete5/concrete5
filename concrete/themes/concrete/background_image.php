<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php
$view->requireAsset('javascript', 'backstretch');
$view->inc('elements/header.php'); ?>

<div class="container">
<div class="row">
<div class="col-sm-10 col-sm-offset-1">
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

<style>
    body {
        background: url("<?= ASSETS_URL_IMAGES ?>/bg_login.png");
    }
</style>

<?php echo $innerContent ?>

<div class="ccm-page-background-credit" style="display:none">
    <div class="ccm-page-background-privacy-notice pull-left">
        <?=t('Image served from concrete5.org. <a href="%s" target="_blank">View Privacy Policy</a>.',
            Config::get('concrete.urls.privacy_policy'))?>
    </div>
    <div class="ccm-page-background-photo-credit pull-right">
        <?= t('Photo Credit:') ?>
        <a href="#"></a>
    </div>
</div>

</div>
</div>

<?php
$image = date('Ymd') . '.jpg';
?>

<script type="text/javascript">
$(function() {

    setTimeout(function() {

        var fade_div = $('<div/>').css({
            position: 'absolute',
            top: 0,
            left: 0,
            width: '100%'
        }).prependTo('body').height('200px');

        fade_div.hide()
            .append(
            $('<img/>')
                .css({ width: '100%', height: '100%' })
                .attr('src', '<?= ASSETS_URL_IMAGES ?>/login_fade.png'))
            .fadeIn();
    }, 0);

     <?php if (Config::get('concrete.white_label.background_image') !== 'none' && !Config::get('concrete.white_label.background_url')) {
    ?>
    $(function () {
        var shown = false, info;
        $.getJSON('<?= Core::getApplicationURL() . '/' . DISPATCHER_FILENAME . '/tools/required/dashboard/get_image_data' ?>', { image: '<?= $image ?>' }, function (data) {
            if (shown) {
                $('div.ccm-page-background-credit').fadeIn().children().attr('href', data.link).text(data.author.join());
            } else {
                info = data;
            }
        });
        $(window).on('backstretch.show', function() {
            shown = true;

            if (info) {
                $('div.ccm-page-background-credit').fadeIn().find('div.ccm-page-background-photo-credit').children().attr('href', info.link).text(info.author.join());
            }

        });
        $.backstretch("<?= Config::get('concrete.urls.background_feed') . '/' . $image ?>", {
            fade: 500
        });
    });
    <?php 
} elseif (Config::get('concrete.white_label.background_url')) {
    ?>
        $.backstretch("<?= Config::get('concrete.white_label.background_url') ?>", {
            fade: 500
        });
    <?php 
} ?>
});
</script>

<?php $view->inc('elements/footer.php'); ?>
