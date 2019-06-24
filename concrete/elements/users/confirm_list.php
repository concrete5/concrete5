<?php defined('C5_EXECUTE') or die('Access Denied.');
// used when confirming bulk user operations.. would you like to do xyz to the following users??
if (is_array($users)) {
    ?>
<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0" border="0">
	<thead>
		<tr>
			<th><?php echo t('Username')?></th>
			<th><?php echo t('Email')?></th>
			<th><?php echo t('# Logins')?></th>
		</tr>
	</thead>
	<tbody>
		<?php
        $nh = Loader::helper('number');
    foreach ($users as $ui) {
        ?>
		<tr>
			<td><?php echo $ui->getUserName();
        ?></td>
			<td><?php echo $ui->getUserEmail();
        ?></td>
			<td><?php echo $nh->format($ui->getNumLogins(), 0);
        ?></td>
		</tr>
		<?php 
    }
    ?>
	</tbody>
</table>
<?php 
}
