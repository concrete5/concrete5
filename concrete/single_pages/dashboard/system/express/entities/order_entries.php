<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Element\Dashboard\Express\Menu;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Entry\Search\Result\Result;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var Entity $entity */
/** @var Result $result */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);

?>

<div class="ccm-dashboard-header-buttons">
    <?php
    $manage = new Menu($entity);
    /** @noinspection PhpDeprecationInspection */
    $manage->render();
    ?>
</div>

<div class="row">
    <?php /** @noinspection PhpUnhandledExceptionInspection */
    View::element('dashboard/express/detail_navigation', ['entity' => $entity]) ?>

    <div class="col-md-8">
        <form method="post" action="<?php echo $view->action('save', $entity->getID()) ?>">
            <?php echo $token->output('save') ?>

            <table class="table" data-table="entries">
                <thead>
                <tr>
                    <th>

                    </th>

                    <?php foreach ($result->getListColumns()->getColumns() as $column) { ?>
                        <th>
                            <span>
                                <?php echo $column->getColumnName() ?>
                            </span>
                        </th>
                    <?php } ?>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($result->getItemListObject()->getResults() as $entry) { ?>
                    <tr>
                        <td style="width: 1px;">
                            <?php echo $form->hidden("entry[]", $entry->getID()); ?>

                            <a href="#" class="icon-link" data-command="move-entry">
                                <i class="fa fa-arrows"></i>
                            </a>
                        </td>

                        <?php $details = $result->getItemDetails($entry); ?>

                        <?php foreach ($details->getColumns() as $column) { ?>
                            <td>
                                <?php echo $column->getColumnValue(); ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <script type="text/javascript">
                $(function () {
                    $('table[data-table=entries] tbody').sortable({
                        handle: 'a[data-command=move-entry]',
                        cursor: 'move',
                        axis: 'y',
                        helper: function (e, ui) { // prevent table columns from collapsing
                            ui.addClass('active');
                            ui.children().each(function () {
                                $(this).width($(this).width());
                            });
                            return ui;
                        },
                        stop: function (e, ui) {
                            ui.item.removeClass('active');
                        }
                    });
                });
            </script>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <button class="float-end btn btn-primary" type="submit">
                        <?php echo t('Save') ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
