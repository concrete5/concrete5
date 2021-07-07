<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<script type="text/javascript">
<?php
$ci = Loader::helper("concrete/urls");
$url = $ci->getBlockTypeJavaScriptURL($blockType);
if ($url !== '') {
    ?>ConcreteAssetLoader.loadJavaScript(<?= json_encode($url) ?>);<?php
}
$url = $ci->getBlockTypeCssURL($blockType);
if ($url !== '') {
    ?>ConcreteAssetLoader.loadCSS(<?= json_encode($url) ?>);<?php
}
$identifier = strtoupper('BLOCK_CONTROLLER_' . $btHandle);
if (isset($headerItems) && is_array($headerItems[$identifier])) {
    foreach ($headerItems[$identifier] as $item) {
        if ($item instanceof CSSOutputObject) { ?>
	        ConcreteAssetLoader.loadCSS("<?=$item->file?>");
	        <?php
        } else { ?>
	        ConcreteAssetLoader.loadJavaScript("<?=$item->file?>");
	        <?php
        }
    }
}
?>
</script>

<?php

$hih = Core::make("help/block_type");
$message = $hih->getMessage($blockType->getBlockTypeHandle());

if (!$message && $blockTypeController->getBlockTypeHelp()) {
    $message = new \Concrete\Core\Application\Service\UserInterface\Help\Message();
    $message->setIdentifier($blockType->getBlockTypeHandle());
    $message->setMessageContent($blockTypeController->getBlockTypeHelp());
}

if (isset($message) && is_object($message) && !$blockType->supportsInlineAdd()) {
    ?>
	<div class="dialog-help" id="ccm-menu-help-content"><?php echo $message->getContent() ?></div>
<?php
}

if ($blockType->supportsInlineAdd()) {
    $pt = $c->getCollectionThemeObject();
    if (
        $pt->supportsGridFramework()
        && $area->isGridContainerEnabled()
        && !$blockType->ignorePageThemeGridFrameworkContainer()
    ) {
        $gf = $pt->getThemeGridFrameworkObject();
        echo $gf->getPageThemeGridFrameworkContainerStartHTML();
        echo $gf->getPageThemeGridFrameworkRowStartHTML();
        printf('<div class="%s">', $gf->getPageThemeGridFrameworkColumnClassesForSpan(
                $gf->getPageThemeGridFrameworkNumColumns()
            ));
    }
}
?>

<div <?php if (!$blockType->supportsInlineAdd()) {
    ?>class="ccm-ui"<?php
} else {
    ?>data-container="inline-toolbar"<?php
} ?>>


<form method="post" action="<?=$controller->action('submit')?>" id="ccm-block-form" enctype="multipart/form-data" class="validate">

<input type="hidden" name="btID" value="<?=$blockType->getBlockTypeID()?>">
<input type="hidden" name="arHandle" value="<?=$area->getAreaHandle()?>">
<input type="hidden" name="cID" value="<?=$c->getCollectionID()?>">

<input type="hidden" name="dragAreaBlockID" value="0" />

<?php foreach ($blockTypeController->getJavaScriptStrings() as $key => $val) {
    ?>
	<input type="hidden" name="ccm-string-<?=$key?>" value="<?=h($val)?>" />
<?php
} ?>

<?php foreach ($area->getAreaCustomTemplates() as $btHandle => $template) {
    ?>
	<input type="hidden" name="arCustomTemplates[<?=$btHandle?>]" value="<?=$template?>" />
<?php
} ?>

<?php if (!$blockType->supportsInlineAdd()) {
    ?>
<div id="ccm-block-fields">
<?php
} else {
    ?>
<div>
<?php
} ?>

<?php $blockView->render('add');?>

</div>

<?php if (!$blockType->supportsInlineAdd()) {
    ?>

	<div class="ccm-buttons dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary me-auto"><?=t('Cancel')?></a>
	<a href="javascript:void(0)" onclick="$('#ccm-form-submit-button').get(0).click()" class="btn btn-primary"><?=t('Add')?></a>
	</div>

<?php
} ?>

	<!-- we do it this way so we still trip javascript validation. stupid javascript. //-->
	<input type="submit" name="ccm-add-block-submit" value="submit" style="display: none" id="ccm-form-submit-button" />
</form>

</div>

<?php
if ($blockType->supportsInlineAdd()) {
    $pt = $c->getCollectionThemeObject();
    if (
        $pt->supportsGridFramework()
        && $area->isGridContainerEnabled()
        && !$blockType->ignorePageThemeGridFrameworkContainer()
    ) {
        $gf = $pt->getThemeGridFrameworkObject();
        echo '</div>';
        echo $gf->getPageThemeGridFrameworkRowEndHTML();
        echo $gf->getPageThemeGridFrameworkContainerEndHTML();
    }
}
