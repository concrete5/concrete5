<?php
defined('C5_EXECUTE') or die('Access Denied.');

$subject = $siteName.' '.t("Registration - Approval Required");

/*
 * HTML BODY START
 */
ob_start()

?>
<h2><?= t('Registration Approval Required') ?></h2>
<?= t('A new user has registered on your website. This account must be approved before it is active and may login.') ?><br />
<?= t('User Name') ?>: <b><?= $uName ?></b><br />
<?= t('Email') ?>: <b><?= $uEmail ?></b><br />
<br />
<?= t('You may approve or remove this user account here:') ?><br />
<a href="<?= View::url('/dashboard/users/search', 'view', $uID) ?>"><?= View::url('/dashboard/users/search', 'view', $uID) ?></a>
<?php if ($attribs): ?>
	<ul>
	<?php foreach ($attribs as $item): ?>
		<li><?= $item ?></li>
	<?php endforeach ?>
	</ul>
<?php endif ?>
<?php

$bodyHTML = ob_get_clean();
/*
 * HTML BODY END
 *
 * ======================
 *
 * PLAIN TEXT BODY START
 */
ob_start();

?>
<?= t('Registration Approval Required') ?>

<?= t('A new user has registered on your website. This account must be approved before it is active and may login.') ?>

<?= t('User Name') ?>: <?= $uName ?>

<?= t('Email Address') ?>: <?= $uEmail ?>

<?php if ($attribs): ?>
	<?php foreach ($attribs as $item): ?>
		<?= $item ?>

	<?php endforeach ?>
<?php endif ?>

<?= t('You may approve or remove this user account here') ?>:

<?= View::url('/dashboard/users/search', 'view', $uID) ?>
<?php

$body = ob_get_clean();
/*
 * PLAIN TEXT BODY END
 */
