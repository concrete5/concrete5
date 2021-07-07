<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<section class="ccm-ui">
    <header><h3><?= t('Page Permissions') ?></h3></header>
    <form method="post" action="<?= $controller->action('save_simple') ?>" data-dialog-form="permissions"
          data-panel-detail-form="permissions">
        <?= Loader::helper('concrete/ui/help')->display('panel', '/page/permissions') ?>

        <div class="form-group">
            <label class="col-form-label"><?= t('Who can view this page?') ?></label>

            <?php
            foreach ($gArray as $g) {
                ?>

                <div class="form-check"><input id="gr<?= $g->getGroupID() ?>" type="checkbox" class="form-check-input"
                                                name="readGID[]"
                                                value="<?= $g->getGroupID() ?>" <?php if (in_array($g->getGroupID(), $viewAccess)) {
                        ?> checked <?php
                    }
                    ?> /> <label class="form-check-label"
                                for="gr<?= $g->getGroupID() ?>"><?= $g->getGroupDisplayName(false) ?></label>
                </div>

                <?php
            } ?>
        </div>

        <hr/>

        <div class="form-group">
            <label class="col-form-label"><?= t('Who can edit this page?') ?></label>

            <?php
            foreach ($gArray as $g) {
                ?>

                <div class="form-check"><input id="ge<?= $g->getGroupID() ?>" class="form-check-input" type="checkbox" name="editGID[]"
                                                value="<?= $g->getGroupID() ?>" <?php if (in_array($g->getGroupID(), $editAccess)) {
                            ?> checked <?php
                        }
                        ?> /> <label class="form-check-label"
                                    for="ge<?= $g->getGroupID() ?>"><?= $g->getGroupDisplayName(false) ?></label></div>

                <?php
            } ?>
        </div>

    </form>
    <div class="dialog-buttons ccm-panel-detail-form-actions d-flex justify-content-end">
        <button class="btn btn-secondary me-2" type="button" data-dialog-action="cancel"
                data-panel-detail-action="cancel"><?= t('Cancel') ?></button>
        <button class="btn btn-success" type="button" data-dialog-action="submit"
                data-panel-detail-action="submit"><?= t('Save Changes') ?></button>
    </div>
</section>
