<?php
defined('C5_EXECUTE') or die('Access Denied.');

$subject = $siteName.' '.t("Registration - A New User Has Registered");

/*
 * HTML BODY START
 */
ob_start();

?>
<h2><?= t('New User Registration') ?></h2>
<?= t('A new user has registered on your website.') ?><br />
<br />
<?= t('User Name') ?>: <b><?= $uName ?></b><br />
<?= t('Email Address') ?>: <b><?= $uEmail ?></b><br />
<br />
<?php if ($attribs): ?>
	<ul>
	<?php foreach ($attribs as $item): ?>
		<li><?= $item ?></li>
	<?php endforeach ?>
	</ul>
<?php endif ?>
<br />
<?php t('This account may be managed directly at') ?><br />
<a href="<?= View::url('/dashboard/users/search', 'view', $uID) ?>"><?= View::url('/dashboard/users/search', 'view', $uID) ?></a>
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
<?= t('New User Registration') ?>

<?= t('A new user has registered on your website.') ?>

<?= t('User Name') ?>: <?= $uName ?>

<?= t('Email Address') ?>: <?= $uEmail ?>

<?php if ($attribs): ?>
	<?php foreach ($attribs as $item): ?>
		<?= $item ?>

	<?php endforeach ?>
<?php endif ?>

<?php t('This account may be managed directly at') ?>

<?= View::url('/dashboard/users/search', 'view', $uID) ?>
<?php

$body = ob_get_clean();
ob_end_clean();
/*
 * PLAIN TEXT BODY END
 */
