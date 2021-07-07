<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\File\Set\Set;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;

$sets = Set::getMySets();
$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

/** @var Closure $displayFileSet */
/** @var Closure $getCheckbox */
?>

<div class="row row-cols-auto g-0 align-items-center float-end mb-3">
    <div class="col-auto">
        <label for="search" class="me-2"><?=t('Search')?></label>
    </div>
    <div class="col-auto">
        <?php
        echo $form->search("search", "", [
            "class" => "form-control",
            "data-field" => "file-set-search",
            "autocomplete" => "off",
            "placeholder" => t('Filter Sets')
        ]);
        ?>
    </div>
</div>

<h3><?=t('File Sets')?></h3>

<div class="form-group" id="ccm-file-set-list">
    <?php
        if (count($sets)) {
            foreach ($sets as $fs) {
                if ($displayFileSet($fs)) {
                    echo $getCheckbox($fs);
                }
            }
        }
    ?>
</div>

<button type="button" class="btn-sm btn btn-secondary" data-action="add-file-set">
    <?php echo t('Add Set') ?> <i class="fa fa-plus-circle"></i>
</button>

<script type="text/template" class="ccm-template-file-set-checkbox">
    <div class="input-group mt-3">
        <!--suppress HtmlFormInputWithoutLabel -->
        <input type="text" placeholder="<?php echo t('Set Name') ?>" class="form-control" name="fsNew[]">

        <div class="input-group-text">
            <input type="checkbox" name="fsNewShare[]" value="1" checked/>
        </div>
        <div class="input-group-text border-left-0 ps-0">
                <span class="small">
                    <?php echo t('Public Set.') ?>
                </span>
        </div>
        <div class="input-group-text">
            <a href="javascript:void(0);" class="ccm-hover-icon">
                <i class="fa fa-minus-circle"></i>
            </a>
        </div>
    </div>
</script>

<script type="text/javascript">
    (function($) {
        $(function () {
            let _checkbox = _.template($('script.ccm-template-file-set-checkbox').html());

            $('button[data-action=add-file-set]').on('click', function () {
                $('#ccm-file-set-list').append(_checkbox)
            });

            $('#ccm-file-set-list').on('click', 'a', function (e) {
                e.preventDefault();
                let $row = $(this).parents('.input-group');
                $row.remove();
            });

            $('input[data-field=file-set-search]').liveUpdate('ccm-file-set-list', 'fileset').closest('form').unbind('submit.liveupdate');
        });
    })(jQuery);
</script>

<!--suppress CssUnusedSymbol -->
<style type="text/css">
    div.form-group-file-set-checkbox {
        position: relative;
        margin-left: 20px;
    }

    div.form-group-file-set-checkbox a {
        position: absolute;
        left: -20px;
        top: 7px;
    }
</style>
