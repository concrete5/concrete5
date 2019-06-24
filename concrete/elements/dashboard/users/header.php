<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<form action="<?= $view->action('update_status', $user->getUserID()) ?>" method="post">
    <?= $token_validator->output() ?>
    <div class="btn-group">


        <?php if (Config::get('concrete.user.registration.validate_email') == true && $canActivateUser) : ?>
            <?php if ($user->isValidated() < 1) : ?>
                <div class="btn-group">
                    <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="bstb-undefined">
                        <span><?php echo t('Validate') ?></span> <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <button type="submit" name="task" class="btn-link" value="validate"><?php echo t('Mark Email As Valid') ?></button>
                        </li>
                        <li>
                            <button type="submit" name="task" class="btn-link" value="send_email_validation"><?php echo t('Send Email Validation') ?></button>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php
        if ($user->getAttribute('profile_private_messages_enabled')) {
            $u = new User();
            if ($u->getUserID() != $user->getUserID()) { ?>
                <a href="<?php echo View::url('/account/messages', 'write', $user->getUserID())?>" class="btn btn-default"><?php echo t("Send Private Message")?></a>
            <?php } ?>
        <?php } ?>

        <?php if ($canActivateUser) { ?>
            <?php if ($user->isActive()) { ?>
                <?php if (!in_array("deactivate", $workflowRequestActions)) { ?>
                    <button type="submit" name="task" value="deactivate"
                            class="btn btn-default"><?= t('Deactivate User') ?></button>
                <?php } ?>
            <?php } else { ?>
                <?php if ((!in_array("activate", $workflowRequestActions) && !in_array("register_activate", $workflowRequestActions))) { ?>
                    <button type="submit" name="task" value="activate"
                            class="btn btn-default"><?= t('Activate User') ?></button>
                <?php } ?>
            <?php } ?>
        <?php } ?>

        <?php if ($canSignInAsUser) {
            ?>
            <button type="submit" name="task" value="sudo"
                    class="btn btn-default"><?= t('Sign in As User') ?></button>
            <?php
        }
        ?>
        <?php if ($canDeleteUser) {
            ?>
            <button type="submit" name="task" value="delete" class="btn btn-danger"><?= t('Delete') ?></button>
            <?php
        }
        ?>
    </div>
</form>
