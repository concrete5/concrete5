<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<form action="<?php echo View::action('save'); ?>" method="post">
    <?php
    $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
    $app->make('token')->output('addresses');
    ?>
    <fieldset>
        <legend><?php echo t('Default'); ?></legend>
        <div class="form-group">
            <?php echo $form->label('defaultName', t('Email From Name')); ?>
            <?php echo $form->text('defaultName', $defaultName); ?>
        </div>
        <div class="form-group">
            <?php echo $form->label('defaultAddress', t('Email Address')); ?>
            <?php echo $form->email('defaultAddress', $defaultAddress); ?>
        </div>
    </fieldset>

    <fieldset>
        <legend><?php echo t('Forgot Password'); ?></legend>
        <div class="form-group">
            <?php echo $form->label('forgotPasswordName', t('Email From Name')); ?>
            <?php echo $form->text('forgotPasswordName', $forgotPasswordName); ?>
        </div>
        <div class="form-group">
            <?php echo $form->label('forgotPasswordAddress', t('Email Address')); ?>
            <?php echo $form->email('forgotPasswordAddress', $forgotPasswordAddress); ?>
        </div>
    </fieldset>

    <fieldset>
        <legend><?php echo t('Form Block'); ?></legend>
        <div class="form-group">
            <?php echo $form->label('formBlockAddress', t('Email Address')); ?>
            <?php echo $form->email('formBlockAddress', $formBlockAddress); ?>
        </div>
    </fieldset>

    <fieldset>
        <legend><?php echo t('Spam Notification'); ?></legend>
        <div class="form-group">
            <?php echo $form->label('spamNotificationAddress', t('Email Address')); ?>
            <?php echo $form->email('spamNotificationAddress', $spamNotificationAddress); ?>
        </div>
    </fieldset>

    <fieldset>
        <legend><?php echo t('Website Registration Notification'); ?></legend>
        <div class="form-group">
            <?php echo $form->label('registerNotificationName', t('Email From Name')); ?>
            <?php echo $form->email('registerNotificationName', $registerNotificationName); ?>
        </div>
        <div class="form-group">
            <?php echo $form->label('registerNotificationAddress', t('Email Address')); ?>
            <?php echo $form->email('registerNotificationAddress', $registerNotificationAddress); ?>
        </div>
    </fieldset>

    <fieldset>
        <legend><?php echo t('Validate Registration'); ?></legend>
        <div class="form-group">
            <?php echo $form->label('validateRegistrationName', t('Email From Name')); ?>
            <?php echo $form->text('validateRegistrationName', $validateRegistrationName); ?>
        </div>
        <div class="form-group">
            <?php echo $form->label('validateRegistrationAddress', t('Email Address')); ?>
            <?php echo $form->email('validateRegistrationAddress', $validateRegistrationAddress); ?>
        </div>
    </fieldset>

    <fieldset>
        <legend><?php echo t('Workflow Notification'); ?></legend>
        <div class="form-group">
            <?php echo $form->label('workflowNotificationName', t('Email From Name')); ?>
            <?php echo $form->text('workflowNotificationName', $workflowNotificationName); ?>
        </div>
        <div class="form-group">
            <?php echo $form->label('workflowNotificationAddress', t('Email Address')); ?>
            <?php echo $form->email('workflowNotificationAddress', $workflowNotificationAddress); ?>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary">
                <?php echo t('Save'); ?>
            </button>
        </div>
    </div>
</form>
