<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="form-group">

    <form method="post" action="<?=$view->action('add_element')?>">
        <?=$token->output('add_element')?>

        <h3 class="mb-4"><?=t('How do you want to create your custom element?')?></h3>

        <div class="pb-4">
            <div class="form-check">
                <input class="form-check-input" checked type="radio" name="creationMethod" id="creationMethod1" value="I">
                <label class="form-check-label" for="creationMethod1">
                    <?=t('Choose items first, slot form factors second.')?>
                </label>
                <div class="help-block"><?=t("If you'd like to link to one or more particular calendar events or pages, choose this option.")?></div>
            </div>
        </div>
        <div class="form-check">
            <input class="form-check-input" disabled type="radio" name="creationMethod" id="creationMethod2" value="C">
            <span class="badge bg-secondary float-end"><?=t('Coming Soon.')?></span>
            <label class="form-check-label" for="creationMethod2">
                <?=t('Create a completely custom slot element.')?>
            </label>
            <div class="help-block"><?=t("Choose this option if you'd like full control over your slot element's content.")?></div>
        </div>

        <hr>

        <div class="form-group">
            <label class="control-label form-label" for="elementName"><?=t('Name')?></label>
            <input class="form-control" name="elementName" />
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button type="submit" class="btn float-end btn-secondary"><?=t('Next')?></button>
            </div>
        </div>

    </form>


</div>
