<?php

defined('C5_EXECUTE') or die("Access Denied.");
$buttonText = t("Add");
/**
 * @var $templates \Concrete\Core\Entity\Board\Template[]
 */
?>

<form method="post" action="<?=$view->action('submit')?>">
    <?=$token->output('submit')?>
    <fieldset>
        <div class="form-group">
            <?=$form->label('boardName', t('Board Name'))?>
            <?=$form->text('boardName')?>
        </div>
        
        <div class="form-group">
            <?=$form->label('boardName', t('Template'))?>
            <?=$form->select('templateID', $templates)?>
        </div>

        <div class="form-group">
            <label class="control-label form-label"><?=t('Sort By')?></label>

            <div class="form-check">
                <?=$form->radio('sortBy', 'relevant_date_asc', true)?>
                <label class="form-check-label" for="sortBy1">
                    <?=t('Ascending Date.')?>
                </label>
            </div>

            <div class="form-check">
                <?=$form->radio('sortBy', 'relevant_date_desc')?>
                <label class="form-check-label" for="sortBy2">
                    <?=t('Descending Date.')?>
                </label>
            </div>
        </div>

        <?php if ($multisite) { ?>
            <div class="form-group">
                <label class="control-label form-label"><?=t('Site')?></label>
    
                <div class="form-check">
                    <?=$form->radio('sharedBoard', 0)?>
                    <label class="form-check-label" for="sharedBoard1">
                        <?=t('Add to current site.')?>
                    </label>
                </div>
    
                <div class="form-check">
                    <?=$form->radio('sharedBoard', 1)?>
                    <label class="form-check-label" for="sharedBoard2">
                        <?=t('Share board with all sites.')?>
                    </label>
                </div>
            </div>
        <?php } ?>
        
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions ">
                <a href="<?=$view->url('/dashboard/boards/boards')?>" class="btn btn-secondary float-start"><?=t("Cancel")?></a>
                <button type="submit" class="btn btn-primary float-end"><?=$buttonText?></button>
            </div>
        </div>
    </fieldset>
</form>
