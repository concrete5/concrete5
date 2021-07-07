<?php
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
    <form action="<?= \URL::to('/login/callback/google/handle_register'); ?>">
        <?php
        // It's best to show full name here for regional variations of display order of names
        if (isset($fullName) && !empty($fullName)) {
            ?>
        <span><?= t('Register an account for "%s"', $fullName); ?></span>
        <?php
        } else {
            ?>
        <span><?= t('Register an account for "%s"', $username); ?></span>
            <?php
        } ?>
        <hr />
        <div class="input-group">
            <input type="email" name="uEmail" placeholder="email" class="form-control" />
            <span class="input-group-btn">
                <button class="btn btn-primary"><?= t('Register'); ?></button>
            </span>
        </div>
        <?=$token->output('google_register'); ?>
    </form>
    <?php
} else {
            ?>
    <div class="form-group external-auth-option">
        <div class="d-grid">
            <a href="<?= \URL::to('/ccm/system/authentication/oauth2/google/attempt_auth'); ?>" class="btn btn-light btn-google">
                <svg aria-hidden="true" class="svg-icon native iconGoogle" width="18" height="18" viewBox="0 0 18 18"><path d="M16.51 8H8.98v3h4.3c-.18 1-.74 1.48-1.6 2.04v2.01h2.6a7.8 7.8 0 0 0 2.38-5.88c0-.57-.05-.66-.15-1.18z" fill="#4285F4"></path><path d="M8.98 17c2.16 0 3.97-.72 5.3-1.94l-2.6-2a4.8 4.8 0 0 1-7.18-2.54H1.83v2.07A8 8 0 0 0 8.98 17z" fill="#34A853"></path><path d="M4.5 10.52a4.8 4.8 0 0 1 0-3.04V5.41H1.83a8 8 0 0 0 0 7.18l2.67-2.07z" fill="#FBBC05"></path><path d="M8.98 4.18c1.17 0 2.23.4 3.06 1.2l2.3-2.3A8 8 0 0 0 1.83 5.4L4.5 7.49a4.77 4.77 0 0 1 4.48-3.3z" fill="#EA4335"></path></svg>
                <?= t('Log in with %s', 'Google'); ?>
            </a>
        </div>
    </div>

    <?php
        }
?>
<style>
    .ccm-ui .btn-google {
      border: 1px solid #D6D9DE !important;
      background-color: #FFF;
    }
    .ccm-ui .btn-google:hover {
      background-color: #FAFAFB;
    }
</style>
