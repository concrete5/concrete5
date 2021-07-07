<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $board \Concrete\Core\Entity\Board\Board
 */
?>

<div class="row">
    <div class="col-4">
        <?php
        $element = Element::get('dashboard/boards/menu', ['board' => $board, 'action' => 'appearance']);
        $element->render();
        ?>
    </div>
    <div class="col-8">

        <form method="post" action="<?=$view->action('update_appearance', $board->getBoardID())?>">
            <?=$token->output('update_appearance')?>

            <h2><?=t('Slots')?></h2>
            
            <div class="form-group form-check">
                <label class="form-check-label">
                    <input type="checkbox" <?php if ($board->hasCustomSlotTemplates()) { ?>checked<?php } ?>
                           name="hasCustomSlotTemplates" class="form-check-input">
                    <?= t('Force board to use a sub-set of templates.') ?>
                </label>
            </div>

            <h4 class="fw-light"><?=t("Available Slot Templates")?></h4>

            <div class="form-group" data-list="slot-templates">
                <?php foreach($templates as $template) { ?>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox"
                                   name="templateIDs[]" <?php if (!$board->hasCustomSlotTemplates() || in_array($template->getID(), $selectedTemplateIDs)) { ?>checked<?php } ?> value="<?=$template->getID()?>" class="form-check-input">
                            <?=$template->getName()?>
                        </label>
                    </div>
                <?php } ?>
            </div>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions ">
                    <button type="submit" class="btn btn-primary float-end"><?=t('Save')?></button>
                </div>
            </div>

        </form>

        
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('input[name=hasCustomSlotTemplates]').on('change', function() {
            if ($(this).is(':checked')) {
                $('div[data-list=slot-templates] input[type=checkbox]').prop('disabled', false);
            } else {
                $('div[data-list=slot-templates] input[type=checkbox]').prop('disabled', true).prop('checked', true);
            }
        }).trigger('change');
    });
</script>
