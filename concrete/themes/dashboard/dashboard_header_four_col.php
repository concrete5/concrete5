<?php
defined('C5_EXECUTE') or die('Access Denied.');
$this->inc('elements/header.php', array('enableEditing' => true));
?>

	<div class="ccm-ui" id="newsflow">
		<?php $this->inc('elements/header_newsflow.php'); ?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?php $a = new Area('Header'); $a->display($c); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<?php $a = new Area('Column 1'); $a->display($c); ?>
				</div>
				<div class="col-md-12">
					<?php $a = new Area('Column 2'); $a->display($c); ?>
				</div>
				<div class="col-md-12">
					<?php $a = new Area('Column 3'); $a->display($c); ?>
				</div>
				<div class="col-md-12">
					<?php $a = new Area('Column 4'); $a->display($c); ?>
				</div>
			</div>
		</div>
	</div>

<?php $this->inc('elements/footer.php'); ?>