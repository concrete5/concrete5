<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<h1 class="error"><?=t('Page Not Found')?></h1>

<?=t('No page could be found at this address.')?>

<?php $a = new Area('Main'); ?>
<?php $a->display($c); ?>

<a href="<?=DIR_REL?>/"><?=t('Back to Home')?></a>.