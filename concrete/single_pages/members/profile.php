<?php defined('C5_EXECUTE') or die("Access Denied.");

$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
/** @var \Concrete\Core\Package\PackageService $packageService */
$packageService = $app->make(\Concrete\Core\Package\PackageService::class);

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
    <?php if ($packageService->getByHandle("community_badges") instanceof \Concrete\Core\Entity\Package) {?>
	<div class="ccm-profile-statistics-item">
		<i class="icon-fire"></i> <?=number_format(\PortlandLabs\Community\User\Point\Entry::getTotal($profile))?> <?=t('Community Points')?>
	</div>
    <?php } ?>
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
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('.launch-tooltip'))
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            placement: 'bottom'
        })
    })
});
</script>
