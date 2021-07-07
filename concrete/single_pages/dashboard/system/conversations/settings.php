<?php defined('C5_EXECUTE') or die("Access Denied.");

$form = \Core::make('helper/form');
$file = \Core::make('helper/file');
$token = \Core::make('token');

/**
 * @var string[] $file_access_file_types_blacklist
 */

?>
<form action="<?= $view->action('save') ?>" method='post'>
    <?php $token->output('conversations.settings.save') ?>

    <fieldset>
        <legend><?php echo t('Attachment Settings'); ?></legend>
        <p class="help-block"><?php echo t('Note: These settings can be overridden in the block edit form for individual conversations.'); ?></p>
        <div class="form-group">
            <label class="control-label form-label"><?= t('Attachments') ?></label>
            <div class="form-check">
                <?= $form->checkbox('attachmentsEnabled', 1, $attachmentsEnabled) ?>
                <?= $form->label('attachmentsEnabled', t('Enable File Attachments'), ['class'=>'form-check-label']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label form-label"><?= t('Max Attachment Size for Guest Users. (MB)') ?></label>
            <?= $form->text('maxFileSizeGuest', $maxFileSizeGuest > 0 ? $maxFileSizeGuest : '') ?>
        </div>
        <div class="form-group">
            <label class="control-label form-label"><?= t('Max Attachment Size for Registered Users. (MB)') ?></label>
            <?= $form->text('maxFileSizeRegistered', $maxFileSizeRegistered > 0 ? $maxFileSizeRegistered : '') ?>
        </div>
        <div class="form-group">
            <label class="control-label form-label"><?= t('Max Attachments Per Message for Guest Users.') ?></label>
            <?= $form->text('maxFilesGuest', $maxFilesGuest > 0 ? $maxFilesGuest : '') ?>
        </div>
        <div class="form-group">
            <label class="control-label form-label"><?= t('Max Attachments Per Message for Registered Users') ?></label>
            <?= $form->text('maxFilesRegistered', $maxFilesRegistered > 0 ? $maxFilesRegistered : '') ?>
        </div>
        <div class="form-group">
            <label class="control-label form-label"><?= t('Allowed File Extensions (Comma separated, no periods).') ?></label>
            <?= $form->textarea('fileExtensions', $fileExtensions) ?>
            <?php
            if ($file_access_file_types_blacklist !== []) {
                ?>
                <div class="text-muted small">
                    <?= t('These file extensions will always be blocked: %s', '<code>' . implode('</code>, <code>', $file_access_file_types_blacklist) . '</code>') ?><br />
                    <?= t('If you want to unblock these extensions, you have to manually set the %s configuration key.', '<code>conversations.files.disallowed_types</code>') ?>
                </div>
                <?php
            }
            ?>
        </div>
    </fieldset>
    <fieldset>
        <legend><?= t('Editor') ?></legend>
        <div class="form-group">
            <?= $form->label('activeEditor', t('Active Conversation Editor')) ?>
            <?= $form->select('activeEditor', $editors, $active); ?>
        </div>
    </fieldset>
    <fieldset>
        <legend><?= t('Notification') ?></legend>
        <div class="form-group">
            <label class="control-label form-label"><?= t('Users To Receive Conversation Notifications') ?></label>
            <?= Core::make("helper/form/user_selector")->selectMultipleUsers('defaultUsers', $notificationUsers) ?>
        </div>
        <div class="form-group">
            <label class="control-label form-label"><?= t('Subscribe Option') ?></label>

            <div class="form-check">
                <?= $form->checkbox('subscriptionEnabled', 1, $subscriptionEnabled) ?>
                <?= $form->label('subscriptionEnabled',t('Yes, allow registered users to choose to subscribe to conversations.'), ['class'=>'form-check-label']) ?>
            </div>
        </div>
    </fieldset>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class='btn btn-primary pull-right'><?php echo t('Save'); ?></button>
        </div>
    </div>
</form>
