<?php defined('C5_EXECUTE') or die('Access Denied.');?>

    <div class="ccm-dashboard-header-buttons">

        <?php
        $manage = new \Concrete\Controller\Element\Dashboard\Express\Menu($entity);
        $manage->render();
        ?>

    </div>

<div class="row">
    <?php View::element('dashboard/express/detail_navigation', array('entity' => $entity))?>
    <div class="col-md-8">
        <form method="post" action="<?=$view->action('save', $entity->getID())?>">
            <?=$token->output('save')?>

            <table class="table" data-table="entries">
                <thead>
                <tr>
                    <th></th>
                <?php
                /**
                 * @var $result \Concrete\Core\Express\Entry\Search\Result\Result
                 */
                foreach($result->getListColumns()->getColumns() as $column) { ?>
                    <th><span><?=$column->getColumnName()?></span></th>
                <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach($result->getItemListObject()->getResults() as $entry) { ?>
                    <tr>
                        <td style="width: 1px;"><input type="hidden" name="entry[]" value="<?=$entry->getID()?>"><a href="#" class="icon-link" data-command="move-entry"><i class="fa fa-arrows"></i></a></td>
                        <?php
                        $details = $result->getItemDetails($entry);
                        foreach($details->getColumns() as $column) { ?>
                            <td><?php echo $column->getColumnValue(); ?></td>
                        <?php } ?>
                    </tr>
                <?php }  ?>
                </tbody>
            </table>

            <script type="text/javascript">
                $(function() {
                    $('table[data-table=entries] tbody').sortable({
                        handle: 'a[data-command=move-entry]',
                        cursor: 'move',
                        axis: 'y',
                        helper: function(e, ui) { // prevent table columns from collapsing
                            ui.addClass('active');
                            ui.children().each(function () {
                                $(this).width($(this).width());
                            });
                            return ui;
                        },
                        stop: function(e, ui) {
                            ui.item.removeClass('active');
                        }
                    });
                });
            </script>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
                </div>
            </div>

        </form>
    </div>
</div>