<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="row">
<div class="span10 offset1">

<div class="page-header"><h1><?=t('Members')?></div>

<div class="navbar">
<div class="navbar-inner">

<form method="get" action="<?=$view->action('search_members')?>" class="navbar-form">
	<input name="keywords" type="text" value="<?=$keywords?>" size="20" class="" placeholder="<?=t('Search')?>" />
	<input name="submit" type="button" value="<?=t('Search')?>" class="btn" />
</form>

</div>
</div>

<?php if ($total == 0) { ?>

		<div><?=t('No users found.')?></div>

	<?php } else { ?>

		<table class="table table-striped" id="ccm-members-directory">


		<?php
		$av = Loader::helper('concrete/avatar');
		$u = new User();

		foreach($users as $user) { 	?>

		<tr>
			<td class="ccm-members-directory-avatar"><a href="<?=$view->url('/members/profile','view', $user->getUserID())?>"><?=$av->outputUserAvatar($user)?></a></td>
			<td class="ccm-members-directory-name"><a href="<?=$view->url('/members/profile','view', $user->getUserID())?>"><?=ucfirst($user->getUserName())?></a></td>
			<?php
			foreach($attribs as $ak) { ?>
				<td>
					<?=$user->getAttribute($ak, 'displaySanitized', 'display'); ?>
				</td>
			<?php } ?>
		</tr>

		<?php } ?>

		</table>

        <?php if ($pagination->haveToPaginate()) { ?>

            <?=$pagination->renderDefaultView();?>

        <?php } ?>

	<?php

	} ?>


</div>
</div>
