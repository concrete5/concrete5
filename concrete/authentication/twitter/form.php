<?php
defined('C5_EXECUTE') or die('Access Denied');
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

if (isset($show_email) && $show_email) {
    ?>
    <form action="<?= \URL::to('/login/callback/twitter/handle_register'); ?>">
        <span><?= t('Register an account for "%s"', "@{$username}"); ?></span>
        <hr />
        <div class="input-group">
            <input type="email" name="uEmail" placeholder="email" class="form-control" />
            <span class="input-group-btn">
                <button class="btn btn-primary"><?= t('Register'); ?></button>
            </span>
        </div>
        <?=$token->output('twitter_register'); ?>
    </form>
    <?php
} else {
        ?>
    <div class="form-group external-auth-option">
        <div class="d-grid">
            <a href="<?= \URL::to('/ccm/system/authentication/oauth2/twitter/attempt_auth'); ?>" class="btn btn-primary btn-twitter">
            <svg style="width: 18px; height: 18px;" viewBox="328 355 335 276" xmlns="http://www.w3.org/2000/svg">
            <path d="
            M 630, 425
            A 195, 195 0 0 1 331, 600
            A 142, 142 0 0 0 428, 570
            A  70,  70 0 0 1 370, 523
            A  70,  70 0 0 0 401, 521
            A  70,  70 0 0 1 344, 455
            A  70,  70 0 0 0 372, 460
            A  70,  70 0 0 1 354, 370
            A 195, 195 0 0 0 495, 442
            A  67,  67 0 0 1 611, 380
            A 117, 117 0 0 0 654, 363
            A  65,  65 0 0 1 623, 401
            A 117, 117 0 0 0 662, 390
            A  65,  65 0 0 1 630, 425
            Z"
            style="fill:#3BA9EE;"/>
            </svg>
                <?= t('Log in with %s', 'Twitter'); ?>
            </a>
        </div>
    </div>
    <?php
    }
?>
<?php
    ?>
    <style>
        .ccm-ui .btn-twitter {
            border-width: 0px;
            background: #00aced;
        }

        .btn-twitter .fa-twitter {
            margin: 0 6px 0 3px;
        }
    </style>
