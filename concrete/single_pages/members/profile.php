<?php defined('C5_EXECUTE') or die('Access Denied.');

$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
?>
<div id="ccm-profile-header">

<div id="ccm-profile-avatar">
<?php echo $profile->getUserAvatar()->output(); ?>
</div>

<h1><?=$profile->getUserName()?></h1>

<div id="ccm-profile-controls">
	<?php if ($canEdit) {
    ?>
		<div class="btn-group">
			<a href="<?=$view->url('/account/edit_profile')?>" class="btn btn-sm btn-default"><i class="fa fa-cog"></i> <?=t('Edit')?></a>
			<a href="<?=$view->url('/')?>" class="btn btn-sm btn-default"><i class="fa fa-home"></i> <?=t('Home')?></a>
		</div>
	<?php 
} else {
    ?>
		<?php if ($profile->getAttribute('profile_private_messages_enabled')) {
    ?>
			<a href="<?=$view->url('/account/messages', 'write', $profile->getUserID())?>" class="btn btn-sm btn-default"><i class="fa-user fa"></i> <?=t('Connect')?></a>
		<?php 
}
    ?>
	<?php 
} ?>
</div>


</div>

<div id="ccm-profile-statistics-bar">
	<div class="ccm-profile-statistics-item">
		<i class="icon-time"></i> <?=t(/*i18n: %s is a date */'Joined on %s', $dh->formatDate($profile->getUserDateAdded(), true))?>
	</div>
	<div class="ccm-profile-statistics-item">
		<i class="icon-fire"></i> <?=number_format(\Concrete\Core\User\Point\Entry::getTotal($profile))?> <?=t('Community Points')?>
	</div>
	<div class="ccm-profile-statistics-item">
		<i class="icon-bookmark"></i> <a href="#badges"><?=number_format(count($badges))?> <?=t2('Badge', 'Badges', count($badges))?></a>
	</div>
	<div class="clearfix"></div>
</div>


<div id="ccm-profile-wrapper">

	<div id="ccm-profile-detail">


        <?php
        $uaks = UserAttributeKey::getPublicProfileList();
        foreach ($uaks as $ua) {
            ?>
		<div>
			<h4><?php echo $ua->getAttributeKeyDisplayName()?></h4>
			<?php
            $r = $profile->getAttribute($ua, 'displaySanitized', 'display');
            if ($r) {
                echo $r;
            } else {
                echo t('None');
            }
            ?>
		</div>
        <?php 
        } ?>

		<h4><?=t("Badges")?></h4>
		<?php if (count($badges) > 0) {
    ?>


		<ul class="thumbnails">

			<?php foreach ($badges as $ub) {
    $uf = $ub->getGroupBadgeImageObject();
    if (is_object($uf)) {
        ?>

			  <li class="span2">

			    <div class="thumbnail launch-tooltip ccm-profile-badge-image" title="<?=h($ub->getGroupBadgeDescription())?>">
			      <div><img src="<?=$uf->getRelativePath()?>" /></div>
			      <div><?=t("Awarded %s", $dh->formatDate($ub->getGroupDateTimeEntered($profile)))?></div>
			    </div>

			</li>

			    <?php 
    }
    ?>

			<?php 
}
    ?>

		</ul>

		<?php 
} else {
    ?>
			<p><?=t("This user hasn't won any badges.")?></p>
		<?php 
} ?>


		<?php
            $a = new Area('Main');
            //$a->setAttribute('profile', $profile);
            $a->setBlockWrapperStart('<div class="ccm-profile-body-item">');
            $a->setBlockWrapperEnd('</div>');
            $a->display($c);
        ?>

	</div>
</div>

<script type="text/javascript">
$(function() {
	$(".launch-tooltip").tooltip({
		placement: 'bottom'
	});
});
</script>
