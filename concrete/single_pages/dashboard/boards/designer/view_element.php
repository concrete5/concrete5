<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="mb-3">
    <a href="<?=URL::to('/dashboard/boards/designer')?>">
        <i class="fa fa-arrow-up"></i> <?=t('Back to Library')?>
    </a>
</div>

<h2 class="mb-3"><?=$element->getElementName()?></h2>

<h5><?=t('Template')?></h5>
<div class="mb-3">
    <?=$element->getSlotTemplate()->getName()?>
</div>

<h5><?=t('Preview')?></h5>

<iframe class="mb-3" style="border: 1px solid #ccc; width: 100%; height: 500px"
    src="<?=URL::to('/ccm/system/board/element/preview/', $element->getID())?>"></iframe>

<div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <a href="#"
           class="btn btn-danger"
           data-bs-toggle="modal" data-bs-target="#delete-element"><?=t('Delete')?></a>
        <a href="<?=URL::to('/dashboard/boards/scheduler', 'view', $element->getID())?>"
           class="btn btn-secondary float-end "><?=t('Schedule')?></a>
    </div>
</div>

<div class="modal fade" id="delete-element" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form method="post" action="<?=$view->action('delete_element', $element->getId())?>">
            <?=$token->output('delete_element')?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?=t('Delete Element')?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <?=t('Remove this element from your library?')?>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger float-start"><?=t('Delete Element')?></button>
                </div>
            </div>
        </form>
    </div>
</div>
