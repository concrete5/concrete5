<?php

/** @noinspection PhpDeprecationInspection */
/** @noinspection PhpUnusedParameterInspection */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Dialog\File\Sets;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\View\View;
use HtmlObject\Input;
use HtmlObject\Element;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Entity\File\File;

/** @var Sets $controller */
?>

<form method="post" data-dialog-form="save-file-set" action="<?php echo $controller->action('submit') ?>">
    <div class="ccm-ui">
        <?php
            /** @noinspection PhpUnhandledExceptionInspection */
            View::element('files/add_to_sets', [
                'displayFileSet' => function ($fileset) use ($f) {
                    /** @var Version $f */

                    $fp = FilePermissions::getGlobal();

                    /** @noinspection PhpUndefinedMethodInspection */
                    return ($fp->canAddFiles() || $fp->canAddFileType(strtolower($f->getExtension())));
                },

                'getCheckbox' => function ($fileset) use ($f) {
                    /** @var Set $fileset */
                    /** @var File[] $f */

                    $app = Application::getFacadeApplication();
                    /** @var Identifier $idHelper */
                    $idHelper = $app->make(Identifier::class);

                    $id = "checkbox-" . $idHelper->getString();

                    /** @var Input $checkbox */
                    $checkbox = id(new Input('checkbox', 'fsID[]'))->value($fileset->getFileSetID());
                    $checkbox->addClass("form-check-input");
                    $checkbox->setAttribute("id", $id);

                    if ($f->inFileSet($fileset)) {
                        $checkbox->setAttribute("checked", "true");
                    }

                    $label = new Element('label');
                    $label->addClass("form-check-label");
                    $label->setAttribute("for", $id);
                    $label->setValue($fileset->getFileSetDisplayName());

                    $div = new Element('div');

                    $div->addClass("form-check");
                    $div->addClass('li');

                    /** @noinspection PhpParamsInspection */
                    $div->appendChild($checkbox)->appendChild($label);

                    return $div;
                },
            ]);
        ?>
    </div>

    <div class="dialog-buttons">
        <button class="btn btn-secondary float-start" data-dialog-action="cancel">
            <?php echo t('Cancel') ?>
        </button>

        <button type="button" data-dialog-action="submit" class="btn btn-primary float-end">
            <?php echo t('Save') ?>
        </button>
    </div>
</form>

<script>
    $(function() {
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function(e, data) {
            if (data.form === 'save-file-set') {
                window.location.reload();
            }
        });
    })
</script>
