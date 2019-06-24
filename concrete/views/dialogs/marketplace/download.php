<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="ccm-ui">

<?php if (!$error->has()) {
    ?>
    <?php
    $_pkg = Package::getByHandle($mri->getHandle());
    if ($_pkg->hasInstallPostScreen()) {
        Loader::element('dashboard/install_post', false, $_pkg->getPackageHandle());
    } else {
        echo t('The package was successfully installed.');
    }
    ?>
    <div class="dialog-buttons">
        <button class="btn btn-primary pull-right" type="button" onclick="ConcreteEvent.publish('MarketplaceRequestComplete', {'type': 'download'})"><?=t('Ok')?></button>
    </div>

<?php 
} else {
    ?>
	<p><?= t("The package could not be installed:") ?></p>

    <div class="alert alert-danger">
	<?php $error->output();
    ?>
    </div>

    <hr/>
    <?php if (is_object($mri)) {
    ?>
	<p><?= t("To install the package manually:") ?></p>
	<ol>
		<li><?=t('Download the package from <a href="%s">here</a>.', $mri->getRemoteURL())?></li>
		<li><?=t('Upload and unpack the package on your web server. Place the unpacked files in the packages directory of the root of your concrete5 installation.')?></li>
		<li><?=t('Go to the <a href="%s">Add Functionality</a> page in your concrete5 Dashboard.', View::url('/dashboard/extend/install'))?></li>
        <li><?=t('Click the Install button next to the package name.')?></li>
	</ol>
	<?php 
}
    ?>
<?php 
} ?>

</div>
