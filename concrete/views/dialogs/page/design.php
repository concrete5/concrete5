<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::Helper('form');
$selectedThemeID = 0;
$selectedTemplateID = 0;
if (is_object($selectedTheme)) {
    $selectedThemeID = $selectedTheme->getThemeID();
}
if (is_object($selectedTemplate)) {
    $selectedTemplateID = $selectedTemplate->getPageTemplateID();
}
if (is_object($selectedType)) {
    $selectedTypeID = $selectedType->getPageTypeID();
}

?>

<div class="ccm-ui">
<form method="post" data-dialog-form="design" action="<?=$controller->action('submit')?>">

    <input type="hidden" name="sitemap" value="1" />
    
    <?php if ($cp->canEditPageTemplate()) {
    ?>
        <?=$form->label('pTemplateID', t('Page Template'))?>

        <?php if ($c->isGeneratedCollection()) {
    ?>
            <div class="alert alert-info"><?=t('This is a single page. It does not have a page template.')?></div>
        <?php 
} else {
    ?>
            <div class="form-group">
                <?=$form->select('pTemplateID', $templatesSelect, $selectedTemplateID)?>
            </div>
        <?php 
}
    ?>
    <?php 
} ?>

    <?php if ($cp->canEditPageType()) {
    ?>
        <?=$form->label('ptID', t('Page Type'))?>

        <?php if ($c->isGeneratedCollection()) {
    ?>
            <div class="alert alert-info"><?=t('This is a single page. It does not have a page type.')?></div>
        <?php 
} else {
    ?>
            <div class="form-group">
                <?=$form->select('ptID', $typesSelect, isset($selectedTypeID) ? $selectedTypeID : null)?>
            </div>
            <div class="alert alert-warning">
                <?=t('Changing page types of existing pages could result in unexpected behavior.')?>
            </div>
        <?php 
}
    ?>
    <?php 
} ?>

    <div class="form-group">
		<?=$form->label('pThemeID', t('Theme'))?>
		<?=$form->select('pThemeID', $themesSelect, $selectedThemeID)?>
	</div>



	<div class="dialog-buttons">
	<button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
	<button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?=t('Save')?></button>
	</div>

</form>
</div>
