<?php
defined('C5_EXECUTE') or die('Access Denied.');
$ih = Loader::helper('concrete/ui');

echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Single Pages'), false);?>
	<div class="clearfix">
		<h4><?php echo t('Add Single Page')?></h4>
		<?php if(Config::get('concrete.seo.url_rewriting')) {
			$base = Core::getApplicationURL();
		} else {
			$base = Core::getApplicationURL() . '/' . DISPATCHER_FILENAME;
		}?>
		<form class="form-inline" method="post" id="add_static_page_form" action="<?php echo $view->url('/dashboard/pages/single')?>">
			<?php echo $this->controller->token->output('add_single_page')?>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon"><?php echo $base?>/</div>
                    <input type="text" style="width: 200px" class="form-control" name="pageURL" value="<?php echo h($this->post('pageURL'))?>" />
                    &nbsp; <button class="btn btn-default" type="submit"><?=t('Add')?></button>
                </div>
            </div>
		</form>
        <hr>
		<h4><?php echo t('Already Installed')?></h4>
		<table border="0" cellspacing="1" cellpadding="0" class="table table-striped">
			<thead>
				<tr>
					<th class="subheader"><?php echo t('Name')?></th>
					<th class="subheader"><?php echo t('Path')?></th>
					<th class="subheader"><?php echo t('Package')?></th>
					<th class="subheader"></th>
				</tr>
			</thead>
			<?php if (count($generated) == 0) {
    ?>
				<tr>
					<td colspan="4">
						<?php echo t('No pages found.')?>
					</td>
				</tr>
			<?php
} else {
    ?>

				<?php foreach ($generated as $p) {
    $cp = new Permissions($p);
    if ($p->getPackageID() > 0) {
        $package = Package::getByID($p->getPackageID());
        if (is_object($package)) {
            $packageHandle = $package->getPackageHandle();
            $packageName = $package->getPackageName();
        }
    } else {
        $packageName = t('None');
    }
    ?>
					<tr>
						<td style="width: 30%"><a href="<?=URL::to($p)?>"><?php echo $p->getCollectionName()?></a></td>
						<td style="width: 40%"><?php echo $p->getCollectionPath()?></td>
						<td style="width: 30%"><?php echo $packageName;
    ?></td>
						<td style="width: 1">
							<?php if ($cp->canAdmin()) {
    ?>
                                <a href="<?=$view->action('refresh', $p->getCollectionID(), $this->controller->token->generate('refresh'))?>" title="<?=t('Refreshes the page, rebuilding its permissions and its name.')?>" class="icon-link launch-tooltip"><i class="fa fa-refresh"></i></a>
                            <?php
}
    ?>
						</td>
					</tr>
				<?php
}
} ?>
		</table>

	</div>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);
