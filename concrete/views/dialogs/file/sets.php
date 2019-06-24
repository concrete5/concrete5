<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<form method="post" data-dialog-form="save-file-set" action="<?=$controller->action('submit')?>">

    <div class="ccm-ui">
        <?php Loader::element('files/add_to_sets', array(
            'displayFileSet' => function ($fileset) use ($f) {
                $fp = \FilePermissions::getGlobal();
                if (!$fp->canAddFiles() || !$fp->canAddFileType(strtolower($f->getExtension()))) {
                    return false;
                } else {
                    return true;
                }
            },
            'getCheckbox' => function ($fileset) use ($f) {
                $checkbox = id(new HtmlObject\Input('checkbox', 'fsID[]'))->value($fileset->getFileSetID());
                if ($f->inFileSet($fileset)) {
                    $checkbox->checked(true);
                }

                return $checkbox;
            },
        ));?>
    </div>

    <div class="dialog-buttons">
        <button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
        <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?=t('Save')?></button>
    </div>

</form>
