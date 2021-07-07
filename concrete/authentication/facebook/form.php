<?php
/* @var $user \Concrete\Core\User\User;
 * @var $authenticationType \Concrete\Core\Authentication\AuthenticationType
 */

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
    <form action="<?= \URL::to('/login/callback/facebook/handle_register'); ?>">
        <?php // It's best to show full name here for regional variations
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
        <?=$token->output('facebook_register'); ?>
    </form>
    <?php
} else {
            ?>
    <div class="form-group external-auth-option">
        <div class="d-grid">
            <a href="<?= \URL::to('/ccm/system/authentication/oauth2/facebook/attempt_auth'); ?>" class="btn btn-facebook">
                <svg aria-hidden="true" class="svg-icon iconFacebook" width="18" height="18" viewBox="0 0 18 18"><path d="M3 1a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H3zm6.55 16v-6.2H7.46V8.4h2.09V6.61c0-2.07 1.26-3.2 3.1-3.2.88 0 1.64.07 1.87.1v2.16h-1.29c-1 0-1.19.48-1.19 1.18V8.4h2.39l-.31 2.42h-2.08V17h-2.5z" fill="#FFFFFF"></path></svg>
                <?= t('Log in with %s', 'Facebook'); ?>
            </a>
        </div>
    </div>
    <?php
        }
?>
<style>
    .ccm-ui .btn-facebook {
        background-color: #385499;
        color: #FFF;
    }
    .ccm-ui .btn-facebook:focus,
    .ccm-ui .btn-facebook:hover {
        background-color: #314a86;
    }
</style>
