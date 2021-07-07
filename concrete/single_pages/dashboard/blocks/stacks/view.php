<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Workflow\Progress\PageProgress as PageWorkflowProgress;

/**
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\Composer $composer
 * @var Concrete\Core\Application\Service\Dashboard $dashboard
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Page\View\PageView $view
 * @var Concrete\Controller\SinglePage\Dashboard\Blocks\Stacks $controller
 * @var Concrete\Core\Page\Page $c
 * @var string $localeName
 * @var string $localeCode
 * @var array $blocks
 */

if (isset($neutralStack)) {
    /**
     * @var Stack $neutralStack
     * @var Stack|null $stackToEdit
     * @var bool $isGlobalArea
     */
    ?>
    <div class="ccm-dashboard-header-buttons">
        <?php if ($isGlobalArea) { ?>
            <a href="<?= $view->action('view_global_areas') ?>" class="btn btn-secondary">
                <i class="fas fa-angle-double-left"></i> <?= t('Back to Global Areas') ?>
            </a>
        <?php } else { ?>
            <a href="<?= $view->action('view_details', $neutralStack->getCollectionParentID()) ?>" class="btn btn-secondary">
                <i class="fas fa-angle-double-left"></i> <?= t('Back to Stacks') ?>
            </a>
        <?php } ?>
    </div>
    <div id="ccm-dashboard-content-regular">
        <p class="lead"><?= h($neutralStack->getCollectionName()) ?></p>
        <?php
        if ($stackToEdit === null) {
            ?>
            <div class="alert alert-info">
                <p>
                    <?= t(/*i18n: %1$s is a language name, %2$s is a language code*/ 'This stack is not defined for %1$s (%2$s): the default version will be used.', $localeName, $localeCode); ?>
                </p>
                <?php
                $cpc = new Checker($neutralStack);
                if ($cpc->canAddSubpage()) {
                    ?>
                    <form method="post" action="<?= $view->action('add_localized_stack') ?>">
                        <?php $token->output('add_localized_stack') ?>
                        <?= $form->hidden('stackID', $neutralStack->getCollectionID()) ?>
                        <?= $form->hidden('locale', $localeCode) ?>
                        <p>
                            <button class="btn btn-primary" type="submit">
                                <?= $isGlobalArea ? t('Create localized global area version') : t('Create localized stack version') ?>
                            </button>
                            <br/>
                        </p>
                    </form>
                    <?php
                }
                ?>
            </div>
        <?php
        } else {
            $a = Area::get($stackToEdit, STACKS_AREA_NAME);
            $cpc = new Checker($stackToEdit);
            $cpcNeutral = $stackToEdit === $neutralStack ? $cpc : new Checker($neutralStack);
            $areaPermissions = new Checker($a);
            $showApprovalButton = false;
            $hasPendingPageApproval = false;
            $workflowList = PageWorkflowProgress::getList($stackToEdit);
            foreach ($workflowList as $wl) {
                $wr = $wl->getWorkflowRequestObject();
                $wrk = $wr->getWorkflowRequestPermissionKeyObject();
                if ($wrk->getPermissionKeyHandle() == 'approve_page_versions') {
                    $hasPendingPageApproval = true;
                    break;
                }
            }

            if (!$hasPendingPageApproval) {
                $vo = $stackToEdit->getVersionObject();
                if ($cpc->canApprovePageVersions()) {
                    $publishTitle = $composer->getPublishButtonTitle($stackToEdit);
                    $showApprovalButton = true;
                }
            }
            $deleteLabels = null;
        ?>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav me-auto">
                        <?php
                        if ($areaPermissions->canAddBlocks()) {
                            ?>
                            <li class="nav-item dropdown">
                                <a class="dropdown-toggle nav-link" data-bs-toggle="dropdown" href="#">
                                    <?= t('Add') ?> <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item dialog-launch" dialog-modal="false" dialog-width="550"
                                           dialog-height="380" dialog-title="<?= t('Add') ?>"
                                           href="<?= Url::to('/ccm/system/dialogs/page/add_block_list')->setQuery(['cID' => $stackToEdit->getCollectionID(), 'arHandle' => STACKS_AREA_NAME]) ?>">
                                            <?= t('Add Block') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item dialog-launch" dialog-modal="false" dialog-width="550"
                                           dialog-height="380" dialog-title="<?= t('Paste From Clipboard') ?>"
                                           href="<?= Url::to('/ccm/system/dialogs/page/clipboard')->setQuery(['cID' => $stackToEdit->getCollectionID(), 'arHandle' => STACKS_AREA_NAME]) ?>">
                                            <?= t('Paste From Clipboard') ?>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <?php
                        }

                        if ($cpc->canViewPageVersions()) {
                            ?>
                            <li class="nav-item">
                                <a dialog-width="640" dialog-height="340"
                                   class="nav-link dialog-launch" id="stackVersions"
                                   dialog-title="<?= t('Version History') ?>"
                                   href="<?= Url::to('/ccm/system/panels/page/versions')->setQuery(['cID' => $stackToEdit->getCollectionID()]) ?>">
                                    <?= t('Version History') ?>
                                </a>
                            </li>
                            <?php
                        }

                        if (!$isGlobalArea && $cpcNeutral->canEditPageProperties()) {
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $view->action('rename', $neutralStack->getCollectionID()) ?>">
                                    <?= t('Rename') ?>
                                </a>
                            </li>
                            <?php
                        }

                        if ($cpc->canEditPagePermissions() && Config::get('concrete.permissions.model') == 'advanced') {
                            ?>
                            <li class="nav-item">
                                <a dialog-width="580" class="nav-link dialog-launch"
                                   dialog-append-buttons="true" dialog-height="420"
                                   dialog-title="<?= t('Stack Permissions') ?>" id="stackPermissions"
                                   href="<?= Url::to('/ccm/system/panels/details/page/permissions')->setQuery(['cID' => $stackToEdit->getCollectionID()]) ?>">
                                    <?= t('Permissions') ?>
                                </a>
                            </li>
                            <?php
                        }

                        if (!$isGlobalArea && $cpc->canMoveOrCopyPage()) {
                            ?>
                            <li class="nav-item">
                                <a class="nav-link"
                                   href="<?= $view->action('duplicate', $neutralStack->getCollectionID()) ?>"
                                   style="margin-right: 4px;">
                                    <?= t('Duplicate') ?>
                                </a>
                            </li>
                            <?php
                        }

                        if (!$isGlobalArea) {
                            ?>
                            <li class="nav-item">
                                <a dialog-width="640" dialog-height="340"
                                   class="nav-link dialog-launch" id="stackUsage"
                                   dialog-title="<?= t('Usage') ?>"
                                   href="<?= $view->action('usage', $stackToEdit->getCollectionID()) ?>">
                                    <?= t('Stack Usage') ?>
                                </a>
                            </li>
                            <?php
                        }

                        if ($cpc->canDeletePage()) {
                            if ($isGlobalArea) {
                                if ($stackToEdit !== $neutralStack) {
                                    $deleteLabels = ['title' => t('Delete Localized Version'), 'button' => t('Delete')];
                                    ?>
                                    <li class="nav-item">
                                        <a href="javascript:void(0)" data-dialog="delete-stack" class="nav-link">
                                            <span class="text-danger"><?= t('Clear Global Area') ?></span>
                                        </a>
                                    </li><?php
                                } else {
                                    $deleteLabels = ['title' => t('Clear Global Area Contents'), 'button' => t('Clear area'), 'canUndo' => true];
                                    ?>
                                    <li class="nav-item">
                                        <a href="javascript:void(0)" data-dialog="delete-stack" class="nav-link">
                                            <span class="text-danger"><?= t('Clear Global Area') ?></span>
                                        </a>
                                    </li><?php
                                }
                            } else {
                                if ($stackToEdit !== $neutralStack) {
                                    $deleteLabels = ['title' => t('Delete Localized Version'), 'button' => t('Delete')];
                                    ?>
                                    <li class="nav-item">
                                        <a href="javascript:void(0)" data-dialog="delete-stack" class="nav-link">
                                            <span class="text-danger"><?= t('Delete') ?></span>
                                        </a>
                                    </li><?php
                                } else {
                                    $deleteLabels = ['title' => t('Delete Stack'), 'button' => t('Delete')];
                                    ?>
                                    <li class="nav-item">
                                        <a href="javascript:void(0)" data-dialog="delete-stack" class="nav-link">
                                            <span class="text-danger"><?= t('Delete') ?></span>
                                        </a>
                                    </li>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </ul>

                    <?php if ($showApprovalButton) { ?>
                        <ul class="nav navbar-nav navbar-right">
                            <li id="ccm-stack-list-approve-button" class="nav-item navbar-form"<?= $vo->isApprovedNow() ? ' style="display: none;"' : '' ?>>
                                <button class="btn btn-success" onclick="window.location.href='<?= $view->action('approve_stack', $stackToEdit->getCollectionID(), $token->generate('approve_stack')) ?>'">
                                    <?= $publishTitle ?>
                                </button>
                            </li>
                        </ul>
                    <?php } ?>
                </div>
            </nav>

            <div id="ccm-stack-container">
                <?php
                    $a->forceControlsToDisplay();

                    View::element('block_area_header', ['a' => $a]);

                    foreach ($blocks as $b) {
                        $bv = new BlockView($b);
                        $bv->setAreaObject($a);
                        $p = new Checker($b);
                        if ($p->canViewBlock()) {
                            $bv->render('view');
                        }
                    }

                    //View::element('block_area_footer', array('a' => $a));
                    echo '</div>';
                    // No, we don't include the footer because we don't want all area controls available.
                    // But the footer element has a closing DIV we need.
                ?>
            </div>

        <?php
        if ($deleteLabels !== null) {
        ?>
            <div style="display: none">
                <div id="ccm-dialog-delete-stack" class="ccm-ui">
                    <form method="post" class="form-stacked" style="padding-left: 0" action="<?= $view->action('delete_stack') ?>">
                        <?php $token->output('delete_stack') ?>
                        <input type="hidden" name="stackID" value="<?= $stackToEdit->getCollectionID() ?>"/>
                        <p>
                            <?= (isset($deleteLabels['canUndo']) && $deleteLabels['canUndo']) ?
                                    t('Are you sure you want to proceed?') :
                                    t('Are you sure? This action cannot be undone.');
                            ?>
                        </p>
                    </form>

                    <div class="dialog-buttons">
                        <button class="btn btn-secondary" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
                        <button class="btn btn-danger ms-auto" onclick="$('#ccm-dialog-delete-stack form').submit()"><?= $deleteLabels['button'] ?></button>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>

        <script type="text/javascript">
            var showApprovalButton = function () {
                $('#ccm-stack-list-approve-button').show().addClass("animated fadeIn");
            };

            $(function () {
                var ConcreteEvent = Concrete.event;

                ConcreteEvent.on('ClipboardAddBlock', function (event, data) {
                    var editor = Concrete.getEditMode();
                    var area = editor.getAreaByID(<?=$a->getAreaID()?>);
                    block = new Concrete.DuplicateBlock(data.$launcher, editor);
                    block.addToDragArea(_.last(area.getDragAreas()));
                    return false;
                });

                ConcreteEvent.on('AddBlockListAddBlock', function (event, data) {
                    var editor = Concrete.getEditMode();
                    var area = editor.getAreaByID(<?=$a->getAreaID()?>);
                    blockType = new Concrete.BlockType(data.$launcher, editor);
                    blockType.addToDragArea(_.last(area.getDragAreas()));
                    return false;
                });

                ConcreteEvent.on('EditModeAddClipboardComplete', function (event, data) {
                    showApprovalButton();
                    Concrete.getEditMode().scanBlocks();
                });

                ConcreteEvent.on('EditModeAddBlockComplete', function (event, data) {
                    showApprovalButton();
                    Concrete.getEditMode().scanBlocks();
                });

                ConcreteEvent.on('EditModeUpdateBlockComplete', function (event, data) {
                    showApprovalButton();
                    Concrete.getEditMode().scanBlocks();
                });

                ConcreteEvent.on('EditModeBlockDeleteAfterComplete', function (event, data) {
                    showApprovalButton();
                    _.defer(function () {
                        Concrete.getEditMode().scanBlocks();
                    });
                });

                ConcreteEvent.on('EditModeBlockMove', function (event, data) {
                    showApprovalButton();
                    Concrete.getEditMode().scanBlocks();
                });

                <?php
                if ($deleteLabels !== null) {
                ?>
                $('a[data-dialog=delete-stack]').on('click', function () {
                    jQuery.fn.dialog.open({
                        element: '#ccm-dialog-delete-stack',
                        modal: true,
                        width: 320,
                        title: <?=json_encode($deleteLabels['title'])?>,
                        height: 'auto'
                    });
                });
                <?php
                }
                ?>
            });
        </script>
        <?php
        }
        ?>
    </div>
    <?php
} elseif
(isset($duplicateStack)) {
    /**
     * @var Stack $duplicateStack
     */
    $sv = CollectionVersion::get($duplicateStack, 'ACTIVE');
    ?>
    <div id="ccm-dashboard-content-regular">
        <form name="duplicate_form" action="<?= $view->action('duplicate', $duplicateStack->getCollectionID()) ?>"
              method="POST">
            <fieldset class="mt-0">
                <?= $token->output('duplicate_stack') ?>
                <legend><?= t('Duplicate Stack') ?></legend>
                <div class="form-group">
                    <?= $form->label('stackName', t('Name')) ?>
                    <?= $form->text('stackName', $duplicateStack->getStackName()) ?>
                </div>
            </fieldset>
            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <a href="<?= $view->action('view_details', $duplicateStack->getCollectionID()) ?>"
                       class="btn btn-secondary"><?= t('Cancel') ?></a>
                    <button type="submit" class="btn float-end btn-primary"><?= t('Duplicate') ?></button>
                </div>
            </div>
        </form>

    </div>
    <?php
} elseif
(isset($renamePage)) {
    /**
     * @var Concrete\Core\Page\Page $renamePage
     * @var bool $isFolder
     * @var string $oldName
     */
    $sv = CollectionVersion::get($renamePage, 'ACTIVE');
    ?>
    <div id="ccm-dashboard-content-regular">
        <form action="<?= $view->action('rename', $renamePage->getCollectionID()) ?>" method="POST">
            <fieldset class="mt-0">
                <legend><?= $isFolder ? t('Rename Stack Folder') : t('Rename Stack') ?></legend>
                <?= $token->output('rename_stack') ?>
                <div class="form-group">
                    <?= $form->label('newName', t('Name')) ?>
                    <?= $form->text('newName', $oldName) ?>
                </div>
            </fieldset>
            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <a href="<?= $view->action('view_details', $renamePage->getCollectionID()) ?>"
                       class="btn btn-secondary"><?= t('Cancel') ?></a>
                    <button type="submit" class="btn float-end btn-primary"><?= t('Rename') ?></button>
                </div>
            </div>
        </form>
    </div>
    <?php
} else {
    if (!isset($showGlobalAreasFolder)) {
        $showGlobalAreasFolder = false;
    }
    if (!isset($canMoveStacks)) {
        $canMoveStacks = false;
    }
/**
 * @var Concrete\Core\Page\Stack\StackList $list
 * @var Concrete\Core\Page\Page[] $stacks
 */
if ($showGlobalAreasFolder || !empty($stacks)) {
    $dh = Core::make('date');
    ?>
    <div class="table-responsive">
        <table class="ccm-search-results-table">
            <thead>
            <tr>
                <th></th>
                <th class="<?= $list->getSortClassName('cv.cvName') ?>">
                    <a href="<?= $list->getSortURL('cv.cvName') ?>"><?= t('Name') ?></a>
                </th>
                <th class="<?= $list->getSortClassName('c.cDateAdded') ?>">
                    <a href="<?= $list->getSortURL('c.cDateAdded') ?>"><?= t('Date Added') ?></a>
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($showGlobalAreasFolder) {
                ?>
                <tr class="ccm-search-results-folder ccm-search-results-globalareafolder" data-details-url="<?= $view->action('view_global_areas') ?>">
                    <td class="ccm-search-results-icon"><i class="fas fa-object-group"></i></td>
                    <td class="ccm-search-results-name"><?= t('Global Areas') ?></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php
            }
            foreach ($stacks as $st) {
                $formatter = new Concrete\Core\Page\Stack\Formatter($st);
                ?>
                <tr class="<?= $formatter->getSearchResultsClass() ?>"
                    data-details-url="<?= $view->action('view_details', $st->getCollectionID()) ?>"
                    data-collection-id="<?= $st->getCollectionID() ?>">
                    <td class="ccm-search-results-icon"><?= $formatter->getIconElement() ?></td>
                    <td class="ccm-search-results-name"><?= h($st->getCollectionName()) ?></td>
                    <td><?= $dh->formatDateTime($st->getCollectionDateAdded()) ?></td>
                    <td class="ccm-search-results-menu-launcher">
                        <?php if ($st->getPageTypeHandle() === STACK_CATEGORY_PAGE_TYPE) { ?>
                            <div class="dropdown">
                                <button class="btn btn-icon" data-boundary="viewport" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg width="16" height="4"><use xlink:href="#icon-menu-launcher"/></svg>
                                </button>
                                <div class="dropdown-menu">
                                    <a data-action="rename" class="dropdown-item" href="<?= $view->action('rename', $st->getCollectionID()) ?>">
                                        <?= t('Rename Folder') ?>
                                    </a>
                                    <a data-action="delete" class="dropdown-item" data-folder-id="<?= $st->getCollectionID() ?>" href="javascript:void(0)">
                                        <?= t('Delete Folder') ?>
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <script type="text/javascript">
        $(function () {
            var $tbody = $('table.ccm-search-results-table tbody');
            $('.ccm-search-results-menu-launcher a[data-action=delete]').on('click', function () {
                var folderID = $(this).data('folder-id'),
                    $dialog = $('#ccm-dialog-delete-stackfolder');
                $dialog.find('input[name=stackfolderID]').val(folderID);
                jQuery.fn.dialog.open({
                    element: '#ccm-dialog-delete-stackfolder',
                    modal: true,
                    width: 'auto',
                    title: <?= json_encode(t('Delete Folder')) ?>,
                    height: 200
                });
            });
            $tbody.find('>tr').each(function () {
                var $this = $(this), className = $this.attr('class');
                $this
                <?php if ($canMoveStacks) { ?>
                    .not('.ccm-search-results-globalareafolder')
                    .draggable({
                        delay: 300,
                        start: function () {
                            $this.addClass('ccm-stack-folder-dragging');
                            $('.ccm-undroppable-search-item').css('opacity', '0.4');
                        },
                        stop: function () {
                            $('.ccm-undroppable-search-item').css('opacity', '');
                        },
                        revert: 'invalid',
                        helper: function () {
                            var $selected = $this.add($tbody.find('.ccm-stack-folder-dragging'));
                            return $('<div class="' + className + ' ccm-draggable-search-item"><span>' + $selected.length + '</span></div>').data('$selected', $selected);
                        },
                        cursorAt: {
                            left: -20,
                            top: 5
                        }
                    })
                    .end()
                <?php } ?>
                ;
            });
            <?php if ($canMoveStacks) { ?>
            $('.ccm-droppable-search-item').droppable({
                accept: '.ccm-search-results-stackfolder, .ccm-search-results-stack',
                //activeClass: 'ui-state-highlight',
                hoverClass: 'ccm-search-select-active-droppable',
                drop: function (event, ui) {
                    var $sourceItems = ui.helper.data('$selected'),
                        sourceIDs = [],
                        destinationID = $(this).data('collection-id')
                    ;
                    $sourceItems.each(function () {
                        var $sourceItem = $(this);
                        var sourceID = $sourceItem.data('collection-id');
                        if (sourceID == destinationID) {
                            $sourceItems = $sourceItems.not(this);
                        } else {
                            sourceIDs.push($(this).data('collection-id'));
                        }
                    });
                    if (sourceIDs.length === 0) {
                        return;
                    }
                    $sourceItems.hide();
                    new ConcreteAjaxRequest({
                        url: <?=json_encode($view->action('move_to_folder'))?>,
                        data: {
                            ccm_token:<?=json_encode($token->generate('move_to_folder'))?>,
                            sourceIDs: sourceIDs,
                            destinationID: destinationID
                        },
                        success: function (msg) {
                            $sourceItems.remove();
                            ConcreteAlert.notify({
                                message: msg
                            });
                        },
                        error: function (xhr) {
                            $sourceItems.show();
                            var msg = xhr.responseText;
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                msg = xhr.responseJSON.errors.join("<br/>");
                            }
                            ConcreteAlert.dialog(<?=json_encode(t('Error'))?>, msg);
                        }
                    });
                }
            });
            <?php } ?>
        });
    </script>

    <div style="display: none">
        <div id="ccm-dialog-delete-stackfolder" class="ccm-ui" title="<?= t('Delete Folder') ?>">
            <form method="post" class="form-stacked" style="padding-left: 0" action="<?= $view->action('delete_stackfolder') ?>">
                <?php $token->output('delete_stackfolder') ?>
                <input type="hidden" name="stackfolderID"/>
                <p><?= t('Are you sure? This action cannot be undone.'); ?></p>
            </form>
            <div class="dialog-buttons">
                <button class="btn btn-secondary" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
                <button class="btn btn-danger ms-auto" onclick="$('#ccm-dialog-delete-stackfolder form').submit()">
                    <?= t('Delete Folder') ?>
                </button>
            </div>
        </div>
    </div>

    <?php
} else {
    ?>
    <div id="ccm-dashboard-content-regular">
        <div class="alert alert-info"><?php
            if ($controller->getAction() == 'view_global_areas') {
                echo t('No global areas have been added.');
            } else {
                echo t('No stacks found in this folder.');
            }
            ?></div><?php
        }
        ?>
    </div>
    <div class="ccm-dashboard-header-buttons">
        <?php
        if ($controller->getAction() != 'view_global_areas') {
            ?>
            <div class="btn-group">
                <button data-dialog="add-stack" class="btn btn-secondary"><i class="fas fa-bars"></i> <?= t('New Stack') ?></button>
                <button data-dialog="add-folder" class="btn btn-secondary"><i class="fas fa-folder"></i> <?= t('New Folder') ?></button>
            </div>
            <?php
        }
        ?>
    </div>

    <div style="display: none">
        <div id="ccm-dialog-add-stack" class="ccm-ui">
            <form method="post" class="form-stacked" style="padding-left: 0"
                  action="<?= $view->action('add_stack') ?>">
                <?= $token->output('add_stack') ?>
                <?= $form->hidden('stackFolderID', $currentStackFolderID ?? ''); ?>
                <div class="form-group">
                    <?= $form->label('stackName', t('Stack Name')) ?>
                    <?= $form->text('stackName') ?>
                </div>
            </form>
            <div class="dialog-buttons">
                <button class="btn btn-secondary me-auto"
                        onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
                <button class="btn btn-primary float-end"
                        onclick="$('#ccm-dialog-add-stack form').submit()"><?= t('Add Stack') ?></button>
            </div>
        </div>
        <div id="ccm-dialog-add-folder" class="ccm-ui">
            <form method="post" class="form-stacked" style="padding-left: 0"
                  action="<?= $view->action('add_folder') ?>">
                <?= $token->output('add_folder') ?>
                <?= $form->hidden('stackFolderID', $currentStackFolderID ?? ''); ?>
                <div class="form-group">
                    <?= $form->label('folderName', t('Folder Name')) ?>
                    <?= $form->text('folderName') ?>
                </div>
            </form>
            <div class="dialog-buttons">
                <button class="btn btn-secondary me-auto" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
                <button class="btn btn-primary float-end" onclick="$('#ccm-dialog-add-folder form').submit()"><?= t('Add Folder') ?></button>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(function () {
            $('button[data-dialog=add-stack]').on('click', function () {
                jQuery.fn.dialog.open({
                    element: '#ccm-dialog-add-stack',
                    modal: true,
                    width: 900,
                    title: <?=json_encode(t('Add Stack'))?>,
                    height: 'auto'
                });
            });

            $('button[data-dialog=add-folder]').on('click', function () {
                jQuery.fn.dialog.open({
                    element: '#ccm-dialog-add-folder',
                    modal: true,
                    width: 320,
                    title: <?=json_encode(t('Add Folder'))?>,
                    height: 'auto'
                });
            });
        });
    </script>
    <?php
}

if (isset($flashMessage)) {
    ?>
    <script>
        $(document).ready(function () {
            ConcreteAlert.notify({
                message: <?=json_encode($flashMessage)?>
            });
        });
    </script><?php
}
