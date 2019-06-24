<?php defined('C5_EXECUTE') or die('Access Denied.');

$save_url = \Concrete\Core\Url\Url::createFromUrl($view->action('save_avatar'));
$save_url = $save_url->setQuery(array(
    'ccm_token' => $token->generate('avatar/save_avatar'),
));
?>


<div vue-enabled>
<h2><?=$c->getCollectionName()?></h2>

	<p><?php echo t('Change the picture attached to my posts.')?></p>

    <avatar-cropper v-bind:height="<?= Config::get('concrete.icons.user_avatar.height') ?>"
                    v-bind:width="<?= Config::get('concrete.icons.user_avatar.width') ?>"
                    uploadurl="<?= $save_url?>"
                    src="<?= $profile->getUserAvatar()->getPath() ?>">
    </avatar-cropper>

    <?php if ($profile->hasAvatar()) { ?>
        <form method="post" action="<?=$view->action('delete')?>">
            <?=$token->output('delete_avatar')?>
            <button class="btn btn-danger btn-sm"><?=t('Delete Avatar')?></button>
        </form>
    <?php } ?>

    <br/>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/account')?>" class="btn btn-default" /><?=t('Back to Account')?></a>
        </div>
    </div>
</div>
