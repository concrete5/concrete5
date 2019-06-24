<?php
defined('C5_EXECUTE') or die('Access Denied.');
$this->inc('elements/header.php', array('enableEditing' => true));
?>

<div class="ccm-ui" id="newsflow">
	<?php $this->inc('elements/header_newsflow.php'); ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-8">
				<div id="ccm-dashboard-welcome-back">
				<?php $a = new Area('Primary'); $a->display($c); ?>
				</div>
				<div class="row">
					<div class="col-md-6">
						<?php $a = new Area('Secondary 3'); $a->display($c); ?>
					</div>
					<div class="col-md-6">
						<?php $a = new Area('Secondary 4'); $a->display($c); ?>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-accented">

				<?php $a = new Area('Secondary 1'); $a->display($c); ?>
				<?php $a = new Area('Secondary 2'); $a->display($c); ?>
				<?php $a = new Area('Secondary 5'); $a->display($c); ?>

			</div>
		</div>
	</div>
</div>

<?php $this->inc('elements/footer.php'); ?>