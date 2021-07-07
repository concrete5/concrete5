<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\File\FileList;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;

/** @var int $fsType */
/** @var Set $fs */
/** @var Token $validation_token */
$app = Application::getFacadeApplication();
$app->make('helper/concrete/ui');
/** @var Date $dh */
$dh = $app->make(Date::class);

?>

<?php if ($this->controller->getTask() == 'view_detail'): ?>

    <script type="text/javascript">
        let deleteFileSet = function () {
            if (confirm('<?php echo h(t('Are you sure you want to permanently remove this file set?')); ?>')) {
                location.href = "<?php echo Url::to('/dashboard/files/sets', 'delete', $fs->getFileSetID(), $validation_token->generate('delete_file_set'))?>";
            }
        }
    </script>

    <div class="ccm-dashboard-header-buttons">
        <button class="btn btn-danger" onclick="deleteFileSet()">
            <?php echo t('Delete Set') ?>
        </button>
    </div>

    <form method="post" class="form-horizontal" id="file_sets_edit"
          action="<?php echo Url::to('/dashboard/files/sets', 'file_sets_edit') ?>">

        <?php echo $validation_token->output('file_sets_edit'); ?>

        <div class="form-group">
            <?php echo $form->label('file_set_name', t('Name')) ?>
            <?php echo $form->text('file_set_name', $fs->fsName, ['class' => 'span5']); ?>
        </div>

        <?php echo $form->hidden('fsID', $fs->getFileSetID()); ?>

        <?php
            $fl = new FileList();
            $fl->filterBySet($fs);
            $fl->sortByFileSetDisplayOrder();
            /** @noinspection PhpDeprecationInspection */
            $files = $fl->get();
        ?>

        <?php if (count($files) > 0): ?>

            <p class="help-block">
                <?php echo t('Click and drag to reorder the files in this set. New files added to this set will automatically be appended to the end.') ?>
            </p>

            <div class="ccm-spacer">
                &nbsp;
            </div>

            <table class="ccm-search-results-table compact-results">
                <thead>
                    <tr>
                        <th>
                            &nbsp;
                        </th>

                        <th>
                            <span>
                                <?php echo t('Thumbnail') ?>
                            </span>
                        </th>

                        <th>
                            <a href="javascript:void(0)" class="sort-link" data-sort="type">
                                <?php echo t('Type') ?>
                            </a>
                        </th>

                        <th>
                            <a href="javascript:void(0)" class="sort-link" data-sort="title">
                                <?php echo t('Title') ?>
                            </a>
                        </th>

                        <th>
                            <a href="javascript:void(0)" class="sort-link" data-sort="filename">
                                <?php echo t('File name') ?>
                            </a>
                        </th>

                        <th>
                            <a href="javascript:void(0)" class="sort-link" data-sort="added">
                                <?php echo t('Added') ?>
                            </a>
                        </th>
                    </tr>
                </thead>

                <tbody class="ccm-file-set-file-list">
                    <?php foreach ($files as $f): ?>
                        <tr id="fID_<?php echo $f->getFileID() ?>" class="">
                            <td>
                                <i class="fa fa-arrows-v"></i>
                            </td>

                            <td class="ccm-file-manager-search-results-thumbnail">
                                <?php echo $f->getListingThumbnailImage() ?>

                                <input type="hidden" name="fsDisplayOrder[]" value="<?php echo $f->getFileID() ?>"/>
                            </td>

                            <td data-key="type">
                                <?php echo $f->getGenericTypetext() ?>/<?php echo $f->getType() ?>
                            </td>

                            <td data-key="title">
                                <?php echo h($f->getTitle()) ?>
                            </td>

                            <td data-key="filename">
                                <?php echo h($f->getFileName()) ?>
                            </td>

                            <td data-key="added" data-sort="<?php echo $f->getDateAdded()->getTimestamp() ?>">
                                <?php
                                    /** @noinspection PhpUnhandledExceptionInspection */
                                    echo $dh->formatDateTime($f->getDateAdded()->getTimestamp())
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <div class="alert alert-info">
                <?php echo t('There are no files in this set.') ?>
            </div>
        <?php endif; ?>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?php echo Url::to('/dashboard/files/sets') ?>" class="btn btn-secondary float-start">
                    <?php echo t('Back') ?>
                </a>

                <?php echo $form->submit('save', t('Save'), ['class' => 'btn btn-primary float-end']); ?>
            </div>
        </div>
    </form>

    <script>
        (function($) {
            $(function () {
                let baseClass = "ccm-results-list-active-sort-"; // asc desc

                function ccmFileSetResetSortIcons() {
                    $(".ccm-search-results-table thead tr th")
                        .removeClass(baseClass + 'asc')
                        .removeClass(baseClass + 'desc');

                    $(".ccm-search-results-table thead tr th a").css("color", "#999");
                }

                function ccmFileSetDoSort() {
                    let $this = $(this);
                    let $parent = $(this).parent();
                    let asc = $parent.hasClass(baseClass + 'asc');
                    let key = $this.attr('data-sort');

                    ccmFileSetResetSortIcons();

                    let sortableList = $('.ccm-file-set-file-list');
                    let listItems = $('tr', sortableList);

                    if (asc) {
                        $parent.addClass(baseClass + 'desc');
                        $(".ccm-search-results-table thead tr th." + baseClass + "desc a").css("color", "#333");
                    } else {
                        $parent.addClass(baseClass + 'asc');
                        $(".ccm-search-results-table thead tr th." + baseClass + "asc a").css("color", "#333");
                    }

                    listItems.sort(function (a, b) {
                        let aTD = $('td[data-key=' + key + ']', $(a));
                        let bTD = $('td[data-key=' + key + ']', $(b));

                        let aVal = typeof (aTD.attr('data-sort')) == 'undefined' ? aTD.text().toUpperCase() : parseInt(aTD.attr('data-sort'));
                        let bVal = typeof (bTD.attr('data-sort')) == 'undefined' ? bTD.text().toUpperCase() : parseInt(bTD.attr('data-sort'));

                        if (asc) {
                            return bVal < aVal ? -1 : 1;
                        } else {
                            return aVal < bVal ? -1 : 1;
                        }
                    });

                    sortableList.append(listItems);
                }

                $('.ccm-search-results-table thead th a.sort-link').click(ccmFileSetDoSort);

                $(".ccm-file-set-file-list").sortable({
                    cursor: 'move',
                    opacity: 0.5,
                    axis: 'y',
                    helper: function (evt, elem) {
                        let ret = $(elem).clone();
                        let i;

                        ret.width(elem.outerWidth());

                        let retChilds = $(ret.children());
                        let elemChilds = $(elem.children());

                        for (i = 0; i < elemChilds.length; i++)
                            $(retChilds[i]).width($(elemChilds[i]).outerWidth());

                        return ret;
                    },
                    placeholder: "ccm-file-set-file-placeholder",
                    stop: function () {
                        ccmFileSetResetSortIcons();
                    }
                });
            });
        })(jQuery);
    </script>

    <!--suppress CssUnusedSymbol -->
    <style type="text/css">
        .ccm-file-set-file-list:hover {
            cursor: move
        }

        .ccm-file-set-file-placeholder {
            background-color: #ffd !important;
        }

        .ccm-file-set-file-placeholder td {
            background: transparent !important;
        }
    </style>

<?php else: ?>
    <?php if (count($fileSets) > 0): ?>
        <div class="table-responsive">
            <table class="ccm-search-results-table">
                <thead>
                    <tr>
                        <th class="ccm-results-list-active-sort-asc"><a><?php echo t('Set Name') ?></a></th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($fileSets as $fs): ?>
                        <tr data-details-url="<?php echo Url::to('/dashboard/files/sets/', 'view_detail', $fs->getFileSetID()) ?>">
                            <td>
                                <?php echo $fs->getFileSetDisplayName() ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($fsl->requiresPaging()): ?>
            <?php $fsl->displayPagingV2(); ?>
        <?php endif; ?>

    <?php else: ?>
        <section>
            <p>
                <?php echo t('No file sets found.') ?>
            </p>
        </section>
    <?php endif; ?>

    <div class="ccm-dashboard-header-buttons">
        <form class="row row-cols-auto g-0 align-items-center" method="get" action="#">

            <div class="col-auto">
                <?php echo $form->search('fsKeywords', [
                    'placeholder' => t('Search'),
                    'class' => 'form-control-sm',
                    'autocomplete' => 'off']);
                ?>
            </div>

            <div class="col-auto">
                <?php echo $form->select(
                    "fsType",
                    [
                        Set::TYPE_PUBLIC => t("Public Sets"),
                        Set::TYPE_PRIVATE => t("My Sets")
                    ],
                    $fsType == Set::TYPE_PRIVATE ? $fsType : Set::TYPE_PUBLIC,
                    [
                        "class" => "ms-2 form-select-sm"
                    ]
                ); ?>
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-secondary ms-2 btn-sm">
                    <svg width="16" height="16">
                        <use xlink:href="#icon-search"/>
                    </svg>
                </button>
            </div>

            <div class="col-auto">
                <a class="btn btn-secondary btn-sm ms-2"
                   href="<?php echo Url::to('/dashboard/files/add_set') ?>"
                   title="<?php echo t('Add File Set') ?>">
                    <?php echo t('Add File Set') ?> <i class="fa fa-plus-circle"></i>
                </a>
            </div>
        </form>
    </div>
<?php endif; ?>



