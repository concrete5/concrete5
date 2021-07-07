<?php defined('C5_EXECUTE') or die("Access Denied.");
$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
?>
<div class="ccm-ui">

<?php if ($total == 0) { ?>

    <?=t("There are no pages of this type added to your website.")?>

<?php } else { ?>

    <form method="post" id="ccmBlockMasterCollectionForm" data-dialog-form-processing="progressive" data-dialog-form="master-collection-alias" data-dialog-form-processing-title="<?=t('Update Defaults')?>" action="<?=$controller->action('submit')?>">

        <p><?=t('This block will be added to all pages of this type. If it has been previously added it will be updated.')?></p>

        <div class="form-group">
            <label class="control-label form-label"><?=t('If this block does not appear on a page of this type')?></label>
            <div class="form-check"><input class="form-check-input" type="radio" name="addBlock" id="addBlock1" value="1" checked><label class="form-check-label" for="addBlock1"> <?=t('Add a new instance of the block to the page.')?></label></div>
            <div class="form-check"><input class="form-check-input" type="radio" name="addBlock" id="addBlock2" value="0"><label class="form-check-label" for="addBlock2"> <?=t('Keep this block off that page.')?></label></div>
        </div>

        <div class="form-group">
            <label class="control-label form-label"><?=t('Would you like to update forked blocks?')?></label>
            <div class="form-check"><input class="form-check-input" type="radio" name="updateForkedBlocks" id="updateForkedBlocks1" value="1"><label class="form-check-label" for="updateForkedBlocks1"> <?=t('Yes')?></label></div>
            <div class="form-check"><input class="form-check-input" type="radio" name="updateForkedBlocks" id="updateForkedBlocks2" value="0" checked><label class="form-check-label" for="updateForkedBlocks2"> <?=t('No')?></label></div>
        </div>

        <div data-dialog-form-element="progress-bar"></div>

        <div class="dialog-buttons">
            <button class="btn btn-secondary pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
            <button class="btn btn-primary pull-right" data-dialog-action="submit"><?=t('Save')?></button>
        </div>

    </form>

<?php } ?>

</div>
