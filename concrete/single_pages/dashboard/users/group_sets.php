<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php if (in_array($this->controller->getTask(), array('update_set', 'update_set_groups', 'edit', 'delete_set'))) { ?>

    <div class="ccm-dashboard-header-buttons">
        <button class="btn btn-danger" data-launch-dialog="delete-set"><?=t("Delete Group Set")?></button>
    </div>

    <form method="post" action="<?php echo $view->action('update_set'); ?>">
        <?php echo Loader::helper('validation/token')->output('update_set'); ?>
        <input type="hidden" name="gsID" value="<?php echo $set->getGroupSetID(); ?>">

        <div class="form-group">
            <?php echo $form->label('gsName', t('Name')); ?>
            <?php echo $form->text('gsName', $set->getGroupSetName()); ?>
            <br>
        </div>
        <div class="form-group">
            <label class="control-label"><?php echo t('Groups'); ?></label>
            <?php
            $list = $set->getGroups();
            if (count($groups) > 0) { ?>
                <?php foreach ($groups as $g) { ?>
                <div class="checkbox">
                    <label>
                        <?php echo $form->checkbox('gID[]', $g->getGroupID(), $set->contains($g)) ?>
                        <span><?php echo $g->getGroupDisplayName(); ?></span>
                    </label>
                </div>
                <?php } ?>
            <?php
            } else { ?>
            <div class="control-group">
                <p><?php echo t('No groups found.'); ?></p>
            </div>
            <?php } ?>
        </div>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?=URL::to('/dashboard/users/group_sets')?>" class="btn pull-left btn-default"><?=t('Cancel')?></a>
                <button type="submit" class="btn btn-primary pull-right"><?=t('Update Set')?></button>
            </div>
        </div>

    </form>


    <div style="display: none">
        <div data-dialog="delete-set">
            <form method="post" action="<?php echo $view->action('delete_set'); ?>">
                <?php echo Loader::helper('validation/token')->output('delete_set'); ?>
                <input type="hidden" name="gsID" value="<?php echo $set->getGroupSetID(); ?>">
                <p><?=t('Are you sure you want to delete this group set? This cannot be undone.')?></p>
                <div class="dialog-buttons">
                    <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                    <button class="btn btn-danger pull-right" onclick="$('div[data-dialog=delete-set] form').submit()"><?=t('Delete Set')?></button>
                </div>
            </form>
        </div>
    </div>


            <?php
} else {
?>

    <?php if (Config::get('concrete.permissions.model') == 'advanced') { ?>

        <div class="ccm-dashboard-header-buttons">
            <button class="btn btn-primary" data-launch-dialog="add-set"><?=t("Add Group Set")?></button>
        </div>
        <?php if (count($groupSets) > 0) { ?>
        <ul class="item-select-list" id="ccm-group-list">
            <?php foreach ($groupSets as $gs) { ?>
                <li>
                    <a href="<?php echo $view->url('/dashboard/users/group_sets', 'edit', $gs->getGroupSetID()); ?>">
                        <i class="fa fa-users"></i> <?php echo $gs->getGroupSetDisplayName(); ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
        <?php
        } else {
        ?>
        <p><?php echo t('You have not added any group sets.'); ?></p>
        <?php } ?>

    <div style="display: None">
        <div data-dialog="add-set">
            <form method="post" action="<?php echo $view->action('add_set'); ?>">
                <?php echo Loader::helper('validation/token')->output('add_set'); ?>
                <fieldset>
                    <legend><?php echo t('Add Set'); ?></legend>
                    <input type="hidden" name="categoryID" value="<?php echo $categoryID?>">

                    <div class="form-group">
                        <?php echo $form->label('gsName', t('Name')); ?>
                        <?php echo $form->text('gsName'); ?>
                    </div>

                    <div class="form-group" style="margin-top: 10px;">
                        <label class="control-label"><?php echo t('Groups'); ?></label>
                        <?php foreach ($groups as $g) { ?>
                        <div class="checkbox">
                            <label><?php echo $form->checkbox('gID[]', $g->getGroupID()); ?> <span><?php echo $g->getGroupDisplayName(); ?></span></label>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="control-group">
                        <?php echo $form->submit('submit', t('Add Set'), array('class' => 'btn btn-primary')); ?>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <?php
    } else {
    ?>
        <p><?php echo t('You must enable <a href="%s">advanced permissions</a> to use group sets.', $view->url('/dashboard/system/permissions/advanced')); ?></p>
    <?php
    }
    ?>
<?php
}
?>


<script type="text/javascript">
    $('[data-launch-dialog]').on('click', function() {
        var $element = $('div[data-dialog=' + $(this).attr('data-launch-dialog') + ']');
        if ($(this).attr('data-dialog-title')) {
            var title = $(this).attr('data-dialog-title');
        } else {
            var title = $(this).text();
        }
        jQuery.fn.dialog.open({
            element: $element,
            modal: true,
            width: 320,
            title: title,
            height: 'auto'
        });
    });
</script>