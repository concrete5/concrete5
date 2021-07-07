<?php defined('C5_EXECUTE') or die('Access denied.');

if (isset($error)) {
    ?>
    <div class="alert alert-danger"><?= $error; ?></div>
    <?php
}
if (isset($message)) {
    ?>
    <div class="alert alert-success"><?= $message; ?></div>
<?php
}
?>

<div class="form-group external-auth-option">
    <div class="d-grid">
        <a href="<?= $authUrl; ?>" class="btn btn-success btn-login">
            <img src="<?= $assetBase; ?>/concrete/images/logo.svg" class="concrete5-icon"></i>
            <?= t('Log in with %s', h($name)); ?>
        </a>
    </div>
</div>

<style>
    .ccm-ui .btn-community {
        border-width: 0px;
        background: rgb(31,186,232);
        background: -moz-linear-gradient(top, rgba(31,186,232,1) 0%, rgba(18,155,211,1) 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(31,186,232,1)), color-stop(100%,rgba(18,155,211,1)));
        background: -webkit-linear-gradient(top, rgba(31,186,232,1) 0%,rgba(18,155,211,1) 100%);
        background: -o-linear-gradient(top, rgba(31,186,232,1) 0%,rgba(18,155,211,1) 100%);
        background: -ms-linear-gradient(top, rgba(31,186,232,1) 0%,rgba(18,155,211,1) 100%);
        background: linear-gradient(to bottom, rgba(31,186,232,1) 0%,rgba(18,155,211,1) 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#1fbae8', endColorstr='#129bd3',GradientType=0 );
    }

    .ccm-concrete-authentication-type-svg > svg {
      width: 16px;
    }

    img.concrete5-icon {
        width: 20px;
        margin-right:5px;
    }
</style>
<script>
    (function() {
        var svg = $('.ccm-concrete-authentication-type-svg > svg');

        if (svg) {
            var img = new Image();
            img.onerror = function() {
                svg.parent().replaceWith('<i class="fa fa-user"></i>');
            };
            img.src = svg.parent().data('src');
            $(function() {

                if (svg.closest('li').hasClass('active')) {
                    var color = $('ul.auth-types li.active').css('color');
                    svg.attr('fill', color);
                } else {
                    svg.attr('fill', 'rgb(155,155,155)');
                }
                Concrete.event.bind('AuthenticationTypeSelected', function(e, handle) {
                    if (handle === 'community') {
                        var color = $('ul.auth-types li.active').css('color');
                        svg.attr('fill', color);
                    } else {
                        svg.attr('fill', 'rgb(155,155,155)');
                    }
                });

            });
        }
    }());
</script>
