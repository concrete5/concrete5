<?php
defined('C5_EXECUTE') or die('Access Denied.');

$subject = $siteName.' '.t("Registration - Approval Required");

/*
 * HTML BODY START
 */
ob_start()

?>
<h2><?= t('Registration Approval Required') ?></h2>
<?= t("You have registered on %s. Your account will be approved by the administrator.", $siteName) ?><br />
<?= t('User Name') ?>: <b><?= $uName ?></b><br />
<?= t('Email') ?>: <b><?= $uEmail ?></b><br />
<br />

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

<?= t("You have registered on %s. Your account will be approved by the administrator.", $siteName) ?>

<?= t('User Name') ?>: <?= $uName ?>
<?= t('Email') ?>: <?= $uEmail ?>

<?php

$body = ob_get_clean();
/*
 * PLAIN TEXT BODY END
 */
