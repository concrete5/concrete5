<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if ('add' == $controller->getTask()
    || 'add_link' == $controller->getTask()
    || 'edit' == $controller->getTask()
    || 'edit_link' == $controller->getTask()
    || 'delete_link' == $controller->getTask()) {
    $url = '';
    $ssHandle = '';
    $action = $view->action('add_link');
    $tokenString = 'add_link';
    $buttonText = t('Add');
    if (isset($link) && is_object($link)) {
        $url = $link->getURL();
        $ssHandle = $link->getServiceHandle();
        $action = $view->action('edit_link', $link->getID());
        $tokenString = 'edit_link';
        $buttonText = t('Save');
    } else {
        $link = null;
    }
    ?>

    <?php if ($link !== null) {
        ?>
        <div class="ccm-dashboard-header-buttons">
            <button data-dialog="delete-link" class="btn btn-danger"><?php echo t("Delete Link"); ?></button>
        </div>

    <div style="display: none">
        <div id="ccm-dialog-delete-social-link" class="ccm-ui">
            <form method="post" class="form-stacked" action="<?=$view->action('delete_link'); ?>">
                <?=Loader::helper("validation/token")->output('delete_link'); ?>
                <input type="hidden" name="slID" value="<?=$link->getID(); ?>" />
                <p><?=t('Are you sure? This action cannot be undone.'); ?></p>
            </form>
            <div class="dialog-buttons">
                <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel'); ?></button>
                <button class="btn btn-danger float-end" onclick="$('#ccm-dialog-delete-social-link form').submit()"><?=t('Delete Link'); ?></button>
            </div>
        </div>
    </div>

    <?php
    } ?>

    <script type="text/javascript">
        $(function() {
            $('button[data-dialog=delete-link]').on('click', function() {
                jQuery.fn.dialog.open({
                    element: '#ccm-dialog-delete-social-link',
                    modal: true,
                    width: 320,
                    title: '<?=t("Delete Social Link"); ?>',
                    height: 'auto'
                });
            });
        });
    </script>

    <form method="post" class="form-horizontal" action="<?=$action; ?>">
        <?=$this->controller->token->output($tokenString); ?>

        <div class="form-group">
            <?=$form->label('ssHandle', t('Service'), ['class' => 'col-md-2 form-label']); ?>
            <div class="col-md-5">
            <?=$form->select('ssHandle', $services, $ssHandle); ?>
            </div>
        </div>

        <div class="form-group">
            <?=$form->label('url', t('URL'), ['class' => 'col-md-2 form-label']); ?>
            <div class="col-md-5">
                <?=$form->text('url', $url); ?>
            </div>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?=URL::to('/dashboard/system/basics/social'); ?>" class="btn btn-secondary float-start"><?=t("Cancel"); ?></a>
                <button class="float-end btn btn-primary" type="submit" ><?=$buttonText; ?></button>
            </div>
        </div>

    </form>
<?php
} else {
        ?>


    <div class="ccm-dashboard-header-buttons">
        <a href="<?php echo View::url('/dashboard/system/basics/social', 'add'); ?>" class="btn btn-primary"><?php echo t("Add Link"); ?></a>
    </div>


    <?php if (count($links) > 0) {
            ?>
        <table class="table table-striped">
        <?php foreach ($links as $link) {
                $service = $link->getServiceObject();
                if ($service) {
                    ?>
                <tr>
                    <td style="width: 48px"><?= $service->getServiceIconHTML(); ?></td>
                    <td><a href="<?= $view->action('edit', $link->getID()); ?>"><?= $service->getDisplayName(); ?></a>
                    </td>
                    <td><?= h($link->getURL()); ?></td>
                </tr>
                <?php
                }
            } ?>
        </table>

    <?php
        } else {
            ?>
        <p><?=t("You have not added any social links."); ?></p>
    <?php
        } ?>


<?php
    } ?>
