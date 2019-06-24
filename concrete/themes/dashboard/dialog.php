<?php
defined('C5_EXECUTE') or die('Access Denied.');

$group = \Concrete\Core\Http\ResponseAssetGroup::get();
$formatter = new \Concrete\Core\Asset\Output\JavascriptFormatter();
$output = $group->getAssetsToOutput();
foreach ($output as $position => $assets) {
    foreach ($assets as $asset) {
        if ($asset instanceof Concrete\Core\Asset\Asset) {
            print $formatter->output($asset);
        }
    }
}
?>

<div class="ccm-ui">

<?php echo $innerContent; ?>

</div>