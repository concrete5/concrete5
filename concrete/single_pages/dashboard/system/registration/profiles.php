<?php defined('C5_EXECUTE') or die('Access Denied.');
$token = \Core::make('token');
?>

<form method="post" id="public-profiles-form" action="<?php echo $view->url('/dashboard/system/registration/profiles', 'update_profiles')?>">
    <?php $token->output('update_profile'); ?>

    <div class="form-group">
        <label id="optionsCheckboxes" for="public_profiles" class="control-label"><?php echo t('Profile Options')?></label>
        <div class="checkbox">
            <label>
                <input type="checkbox" id="public_profiles" name="public_profiles" value="1" <?php if ($public_profiles) { ?> checked <?php } ?>>
                <span><?php echo t('Enable public profiles.')?></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label('gravatar_fallback', t('Fall Back To Gravatar')); ?>
        <div class="checkbox">
            <label>
                <?php echo $form->checkbox('gravatar_fallback', 1, $gravatar_fallback); ?>
                <span><?php echo t('Use image from <a href="http://gravatar.com" target="_blank">gravatar.com</a> if the user has not uploaded one.')?></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label('display_account_menu', t('Account Menu')); ?>
        <div class="checkbox">
            <label>
                <input name="display_account_menu" id="display_account_menu" value="1" type="checkbox" <?= $display_account_menu ? 'checked' : '' ?> />
                <span class="launch-tooltip" title="<?= h(t('Site themes may override this value.')) ?>"><?= t('Show the account menu when logged in.') ?></span>
            </label>
        </div>
    </div>

    <div id="gravatar-options">
        <div class="form-group">
            <?php echo $form->label('gravatar_max_level', t('Maximum Gravatar Rating')); ?>
            <?php echo $form->select('gravatar_max_level', $gravatar_level_options, $gravatar_max_level); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('gravatar_image_set', t('Gravatar Image Set')); ?>
            <?php echo $form->select('gravatar_image_set', $gravatar_set_options, $gravatar_image_set); ?>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
    $('#gravatar_fallback').change(function(){
        if($(this).prop('checked') == true) {
            $('#gravatar-options').css('display', 'block');
        } else {
            $('#gravatar-options').css('display', 'none');
        }
    });
});
</script>

<style>
#gravatar-options {
    display: <?php echo $gravatar_fallback ? 'block' : 'none'; ?>;
}
</style>
