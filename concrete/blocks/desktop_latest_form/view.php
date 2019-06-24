<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div
    <?php if (isset($link) && $link) { ?>data-link="<?=$link?>"<?php } ?>
    class="<?php if (isset($link) && $link) { ?>ccm-block-desktop-latest-form-linked<?php } ?> ccm-block-desktop-latest-form">

    <div class="ccm-block-desktop-latest-form-inner">
        <div class="ccm-block-desktop-latest-form-icon">
            <i class="fa fa-upload fa-lg"></i>
        </div>

        <h3><?=t('Latest Form')?></h3>

        <?php if (isset($formName) && $formName) { ?>

            <span class="ccm-block-desktop-latest-form-name"><?=$formName?></span>
            <span class="ccm-block-desktop-latest-form-date"><?=$date?></span>

        <?php } else { ?>
            <span class="ccm-block-desktop-latest-form-name"><?=t('None')?></span>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('div.ccm-block-desktop-latest-form-linked').on('click', function() {
            window.location.href = $(this).attr("data-link");
        });
    });
</script>