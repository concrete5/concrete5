<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Multisite\Types $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Entity\Site\Type $type
 * @var Concrete\Core\Filesystem\Element $typeMenu
 * @var Concrete\Core\Page\View\PageView $view
 * @var Concrete\Core\Entity\Site\Group\Group|null $group
 * @var Concrete\Core\Entity\Site\Group\Group[] $groups (when $group is null)
 */
$typeMenu->render();

if ($group !== null) {
    if ($group->getSiteGroupID() !== null) {
        ?>
        <div class="ccm-dashboard-dialog-wrapper">
            <div data-dialog-wrapper="delete-group">
                <form method="post" action="<?= $controller->action('delete_group') ?>">
                    <?php $token->output('delete_group') ?>
                    <input type="hidden" name="siteGID" value="<?= $group->getSiteGroupID() ?>" />
                    <?= t('Are you sure? This action cannot be undone.') ?>
                    <div class="dialog-buttons">
                        <button class="btn btn-secondary" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
                        <button class="btn btn-danger" onclick="$('div[data-dialog-wrapper=delete-group] form').submit()"><?= t('Delete Group') ?></button>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }
    ?>
    <form method="post" action="<?= $group->getSiteGroupID() === null ? $controller->action('create_group', $type->getSiteTypeID()) : $controller->action('update_group', $group->getSiteGroupID()) ?>">
        <?php $token->output($group->getSiteGroupID() === null ? 'create_group' : 'update_group') ?>
        <div class="form-group">
            <?= $form->label('groupName', t('Name')) ?>
            <?= $form->text('groupName', $group->getSiteGroupName(), ['required' => 'required']) ?>
        </div>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <div class="float-end">
                    <a class="btn btn-secondary" href="<?= $controller->action('view_groups', $type->getSiteTypeID()) ?>"><?= t('Cancel') ?></a>
                    <?php
                    if ($group->getSiteGroupID() !== null) {
                        ?>
                        <a href="javascript:void(0)" class="btn btn-danger" data-dialog="delete-group" data-dialog-title="<?= t('Delete Group') ?>" data-dialog-width="400"><?= t('Delete Group') ?></a>
                        <?php
                    }
                    ?>
                    <button class="pull-right btn btn-primary" type="submit" ><?= $group->getSiteGroupID() === null ? t('Add') : t('Save') ?></button>
                </div>
            </div>
        </div>
    </form>
    <?php
} else {
    if ($groups === []) {
        ?>
        <div class="alert alert-info">
            <?= t('You have not added any site groups.') ?>
        </div>
        <?php
    } else {
        ?>
        <ul class="item-select-list">
            <?php
            foreach ($groups as $group) {
                ?>
                <li>
                    <a href="<?= $controller->action('edit_group', $group->getSiteGroupID()) ?>"><i class="fas fa-users"></i> <?= h($group->getSiteGroupName()) ?></a>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php
    }
    ?>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-end">
                <a class="btn btn-primary" href="<?= $controller->action('add_group', $type->getSiteTypeID()) ?>"><?= t('Add Group') ?></a>
            </div>
        </div>
    </div>
    <?php
}
