<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="btn-group">


    <?php if (Config::get('concrete.user.registration.validate_email') == true && $canActivateUser) : ?>
        <?php if ($user->isValidated() < 1) : ?>
            <div class="btn-group">
                <button type="button" class="btn dropdown-toggle btn-secondary" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="bstb-undefined">
                    <span><?php echo t('Validate') ?></span> <span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    <a href="<?=$view->action('update_status', 'validate', $user->getUserID(), $token_validator->generate())?>" class="dropdown-item"><?php echo t('Mark Email As Valid') ?></a>
                    <a href="<?=$view->action('update_status', 'send_email_validation', $user->getUserID(), $token_validator->generate())?>" class="dropdown-item"><?php echo t('Re-Send Email Validation') ?></a>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php
    if ($user->getAttribute('profile_private_messages_enabled')) {
        $u = Core::make(Concrete\Core\User\User::class);
        if ($u->getUserID() != $user->getUserID()) { ?>
            <a href="<?php echo View::url('/account/messages', 'write', $user->getUserID())?>" class="btn btn-secondary"><?php echo t("Send Private Message")?></a>
        <?php } ?>
    <?php } ?>

    <?php if ($canActivateUser) { ?>
        <?php if ($user->isActive()) { ?>
            <?php if (!in_array("deactivate", $workflowRequestActions)) { ?>
                <button type="button" name="task" data-bs-toggle="modal" data-bs-target="#deactivate-user-modal"
                        class="btn btn-secondary"><?= t('Deactivate User') ?></button>
            <?php } ?>
        <?php } else { ?>
            <?php if ((!in_array("activate", $workflowRequestActions) && !in_array("register_activate", $workflowRequestActions))) { ?>
                <button type="button" name="task" data-bs-toggle="modal" data-bs-target="#activate-user-modal"
                        class="btn btn-secondary"><?= t('Activate User') ?></button>
            <?php } ?>
        <?php } ?>
    <?php } ?>

    <?php if ($canSignInAsUser) {
        ?>
        <button type="button" data-bs-toggle="modal" data-bs-target="#sudo-user-modal"
                class="btn btn-secondary"><?= t('Sign in As User') ?></button>
        <?php
    }
    ?>
    <?php if ($canDeleteUser) {
        ?>
        <button type="button" data-bs-toggle="modal"
                data-bs-target="#delete-user-modal" class="btn btn-danger"><?= t('Delete') ?></button>
        <?php
    }
    ?>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="deactivate-user-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?=$view->action('update_status', 'deactivate', $user->getUserID())?>">
            <div class="modal-header">
                <h5 class="modal-title"><?=t('Deactivate User')?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
            </div>
            <div class="modal-body">
                <?=$token_validator->output()?>
                <?=('Are you sure you want to deactivate this user?')?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=t('Close')?></button>
                <button type="submit" class="btn btn-danger"><?=t('Deactivate User')?></button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="activate-user-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?=$view->action('update_status', 'activate', $user->getUserID())?>">
                <div class="modal-header">
                    <h5 class="modal-title"><?=t('Deactivate User')?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <?=$token_validator->output()?>
                    <?=('Click below to activate this user.')?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=t('Close')?></button>
                    <button type="submit" class="btn btn-primary"><?=t('Activate User')?></button>
                </div>
            </form>
        </div>
    </div>
</div>



<div class="modal fade" tabindex="-1" role="dialog" id="sudo-user-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?=$view->action('update_status', 'sudo', $user->getUserID())?>">
                <div class="modal-header">
                    <h5 class="modal-title"><?=t('Sign In As User')?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <?=$token_validator->output()?>
                    <?=t("This will end your current session and sign you in as %s", $user->getUserName())?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=t('Close')?></button>
                    <button type="submit" class="btn btn-primary"><?=t('Sign In')?></button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="delete-user-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?=$view->action('update_status', 'delete', $user->getUserID())?>">
                <div class="modal-header">
                    <h5 class="modal-title"><?=t('Delete User')?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <?=$token_validator->output()?>
                    <?=t("Are you sure you want to permanently remove this user?")?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=t('Close')?></button>
                    <button type="submit" class="btn btn-danger"><?=t('Delete')?></button>
                </div>
            </form>
        </div>
    </div>
</div>
