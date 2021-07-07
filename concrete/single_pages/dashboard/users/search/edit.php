<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Widget\FileFolderSelector;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Support\Facade\Application;

/**
 * @var Concrete\Core\User\UserInfo $user
 */

$app = Application::getFacadeApplication();
/** @var FileFolderSelector $fileFolderSelector */
$fileFolderSelector = $app->make(FileFolderSelector::class);

$dh = $app->make('helper/date');
// @var $dh \Concrete\Core\Localization\Service\Date
$languages = Localization::getAvailableInterfaceLanguages();
$locales = [];
if (count($languages) > 0) {
    array_unshift($languages, Localization::BASE_LOCALE);
}
if (count($languages) > 0) {
    foreach ($languages as $lang) {
        $locales[$lang] = \Punic\Language::getName($lang, $lang);
    }
    asort($locales);
    $locales = array_merge(['' => tc('Default locale', '** Default')], $locales);
}

?>

<h3 class="mb-4"><?= t('Basic Details') ?></h3>
<section data-section="basics" class="row">
    <div class="col-lg-12">
        <div class="ccm-user-detail-basics">
            <div class="ccm-user-detail-basics-avatar">
                <?php if ($canEditAvatar) { ?>
                    <avatar-cropper v-bind:height="<?= Config::get('concrete.icons.user_avatar.height') ?>"
                                    v-bind:width="<?= Config::get('concrete.icons.user_avatar.width') ?>"
                                    uploadurl="<?= $saveAvatarUrl ?>"
                                    src="<?= $user->getUserAvatar()->getPath() ?>">
                    </avatar-cropper>
                <?php } else { ?>
                    <?= $user->getUserAvatar()->output() ?>
                <?php } ?>
            </div>
            <div>
                <h5><?= $user->getUserName() ?></h5>
                <a href="mailto:<?= $user->getUserEmail() ?>"><?= $user->getUserEmail() ?></a>
            </div>
        </div>
    </div>
</section>

<hr class="mt-2 mb-4"/>

<div id="folderSelectorSourceContainer" class="d-none">
    <?php echo $fileFolderSelector->selectFileFolder('uHomeFileManagerFolderID', $user->getUserHomeFolderId()); ?>
</div>

<section data-section="account">
    <?php if ($canViewAccountModal) { ?>
        <button
                data-bs-toggle="modal" data-bs-target="#edit-account-modal"
                class="btn-section btn btn-secondary"><?= t('Edit') ?></button>
    <?php } ?>
    <h3><?= t('Account') ?></h3>
    <dl class="ccm-user-detail-account">
        <dt><?= t('Date Created') ?></dt>
        <dd>
            <div><?= $dh->formatDateTime($user->getUserDateAdded()) ?></div>
        </dd>
        <dt><?= t('Last IP Address') ?></dt>
        <dd>
            <div><?= $user->getLastIPAddress() ? $user->getLastIPAddress() : t('None') ?></div>
        </dd>
        <?php
        if (Config::get('concrete.misc.user_timezones')) {
            $uTimezone = $user->getUserTimezone();
            if (empty($uTimezone)) {
                $uTimezone = date_default_timezone_get();
            }
            ?>
            <dt><?= t('Timezone') ?></dt>
            <dd>
                <div><?= $dh->getTimezoneDisplayName($uTimezone) ?></div>
            </dd>
            <?php
        }
        ?>

        <?php
        if (count($languages) > 0) {
            ?>
            <dt><?= t('Language') ?></dt>
            <?php if ($user->getUserDefaultLanguage()) { ?>
                <dd>
                    <div><?= h(Punic\Language::getName($user->getUserDefaultLanguage())) ?></div>
                </dd>
            <?php } else { ?>
                <dd>
                    <div><?= h(Punic\Language::getName(Config::get('concrete.locale'))) ?></div>
                </dd>
            <?php } ?>
            <?php
        }
        ?>
        <dt>
            <?php echo t('Home Folder') ?>
        </dt>
        <dd>
            <div>
                <?php echo isset($folderList[$user->getUserHomeFolderId()]) && $user->getUserHomeFolderId() !== null ? $folderList[$user->getUserHomeFolderId()] : t('None') ?>
            </div>
        </dd>

        <?php
        if (Config::get('concrete.user.registration.validate_email')) {
            ?>
            <dt><?= t('Full Registration Record') ?></dt>
            <dd>
                <div><?= ($user->isFullRecord()) ? t('Yes') : t('No') ?></div>
            </dd>
            <dt><?= t('Email Validated') ?></dt>
            <dd>
                <div>
                    <?php
                    switch ($user->isValidated()) {
                        case '-1':
                            print t('Unknown');
                            break;
                        case '0':
                            print t('No');
                            break;
                        case '1':
                            print t('Yes');
                            break;
                    }
                    ?>
                </div>
            </dd>
            <?php
        }
        ?>

    </dl>

    <?php if ($canViewAccountModal) { ?>

        <form method="post" @submit.prevent="saveAccount">
            <div class="modal fade" tabindex="-1" role="dialog" id="edit-account-modal">
                <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= t('Edit Account') ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                        </div>
                        <div class="modal-body">
                            <fieldset>
                                <legend><?= t('Basic Information'); ?></legend>
                                <?php if ($canEditUserName) { ?>
                                    <div class="form-group">
                                        <?= $form->label('uName', t('Username')); ?>
                                        <?= $form->text('uName', $user->getUserName()); ?>
                                    </div>
                                <?php } ?>
                                <?php if ($canEditEmail) { ?>
                                    <div class="form-group">
                                        <?= $form->label('uEmail', t('Email')); ?>
                                        <?= $form->text('uEmail', $user->getUserEmail()); ?>
                                    </div>
                                <?php } ?>
                                <?php if ($canEditTimezone) { ?>
                                    <?php if (Config::get('concrete.misc.user_timezones')) { ?>
                                        <div class="form-group">
                                            <?= $form->label('uTimezone', t('Time Zone')); ?>
                                            <?= $form->select('uTimezone', $dh->getTimezones(), ($user->getUserTimezone() ? $user->getUserTimezone() : date_default_timezone_get())); ?>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ($canEditLanguage) { ?>
                                    <?php if (is_array($locales) && count($locales)) { ?>
                                        <div class="form-group">
                                            <?= $form->label('uDefaultLanguage', t('Language')); ?>
                                            <?= $form->select('uDefaultLanguage', $locales, Localization::activeLocale()); ?>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ($canEditHomeFileManagerFolderID) { ?>
                                    <div class="form-group">
                                        <?php echo $form->label('uHomeFileManagerFolderID', t('Home Folder')); ?>
                                        <div id="folderSelectorDestinationContainer"></div>
                                    </div>
                                <?php } ?>
                            </fieldset>
                            <?php if ($canEditPassword) { ?>
                                <fieldset>
                                    <legend><?= t('Change Password'); ?></legend>
                                    <div class="form-group">
                                        <?= $form->label('uPassword', t('New Password')); ?>
                                        <?= $form->password('uPassword', ['autocomplete' => 'off']); ?>
                                        <a href="javascript:void(0)" title="<?= t('Leave blank to keep current password.'); ?>"><i
                                                    class="icon-question-sign"></i></a>
                                    </div>

                                    <div class="form-group">
                                        <?= $form->label('uPasswordConfirm', t('Confirm New Password')); ?>
                                        <div class="controls">
                                            <?= $form->password('uPasswordConfirm', ['autocomplete' => 'off']); ?>
                                        </div>
                                    </div>
                                </fieldset>
                            <?php } ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= t('Close') ?></button>
                            <button type="submit" class="btn btn-primary"><?= t('Save') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php } ?>

</section>

<hr class="mt-5 mb-4"/>

<section data-section="groups" v-cloak>
    <?php
    if ($canAddGroup) {
    ?>
    <button class="btn-section btn btn-secondary"
            data-bs-toggle="modal" data-bs-target="#edit-groups-modal"
    ><?= t('Edit') ?></button>
    <?php } ?>

    <h3><?= t('Groups') ?></h3>
    <dl class="ccm-user-detail-groups">
        <template v-for="group in groups">
            <dt><span v-html="group.gDisplayName"></span></dt>
            <dd>
                <div>{{group.gDateTimeEntered}}</div>
            </dd>
        </template>
    </dl>

    <div class="modal fade" tabindex="-1" role="dialog" id="edit-groups-modal">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= t('Edit Groups') ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <h4><?= t('Selected Groups') ?></h4>
                    <div class="mb-3" v-if="groups.length > 0">
                        <div class="d-flex" v-for="group in groups">
                            <div class="me-auto"><span v-html="group.gDisplayName"></span></div>
                            <div><a class="ccm-hover-icon" href="#" @click.prevent="removeGroup(group.gID)"><i
                                            class="fa fa-minus-circle"></i></a></div>
                        </div>
                    </div>
                    <div v-if="groups.length === 0" class="mb-3"><?= t('None') ?></div>

                    <h4><?= t('Add Group') ?></h4>
                    <concrete-group-chooser mode="select" @select="addGroup"></concrete-group-chooser>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= t('Close') ?></button>
                </div>
            </div>
        </div>
    </div>

</section>

<hr class="mt-5 mb-4"/>

<section data-section="attributes">
    <?php
    if (count($allowedEditAttributes)) {
        ?>
        <a class="dialog-launch btn-section btn btn-secondary"
           href="<?=URL::to('/ccm/system/dialogs/user/attributes', $user->getUserID())?>"
           dialog-width="800" dialog-height="640" dialog-title="<?=t('Edit Attributes')?>">
        <?= t('Edit') ?></a>
    <?php } ?>

    <h3><?= t('Custom Attributes') ?></h3>

    <?php foreach ($attributeSets as $set) { ?>
        <h5 class="mt-3"><?php echo $set->getAttributeSetDisplayName() ?></h5>
        <dl class="ccm-user-detail-attributes">
            <?php foreach ($set->getAttributeKeys() as $key) { ?>
                <dt><?= $key->getAttributeKeyDisplayName() ?></dt>
                <dd><?php
                    $value = $user->getAttributeValueObject($key);
                    if ($value) {
                        echo $value->getDisplayValue();
                    }
                    ?>
                </dd>
            <?php } ?>
        </dl>
    <?php } ?>


    <?php if (count($unassigned)) { ?>
        <?php if (count($attributeSets)) { ?>
            <h5 class="mt-3"><?= t('Other') ?></h5>
        <?php } ?>

        <dl class="ccm-user-detail-attributes">
            <?php foreach ($unassigned as $key) { ?>
                <dt><?= $key->getAttributeKeyDisplayName() ?></dt>
                <dd>
                    <div><?php
                        $value = $user->getAttributeValueObject($key);
                        if ($value) {
                            echo $value->getDisplayValue();
                        }
                        ?></div>
                </dd>
            <?php } ?>

        </dl>
    <?php } ?>

</section>

<script>
    $(document).ready(function () {
        Concrete.Vue.activateContext('frontend', function (Vue, config) {
            new Vue({
                el: '[data-section=basics]',
                components: config.components
            })
        })

        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                components: config.components,
                el: 'section[data-section=account]',
                data: {},
                mounted() {

                },
                methods: {
                    saveAccount() {
                        var my = this
                        var data = $(this.$el).find("form").serializeArray()
                        data.push({'name': 'ccm_token', 'value': '<?=$token->generate('save_account')?>'})

                        $.concreteAjax({
                            url: "<?=$view->action('save_account', $user->getUserID())?>",
                            data: data,
                            success: function (r) {
                                window.location.reload()
                            }
                        });
                    },
                },
            });

            new Vue({
                components: config.components,
                el: 'section[data-section=groups]',
                data: {
                    'groups': <?=$groupsJSON?>
                },
                mounted() {
                    $('[data-button=assign-groups]').dialog();
                },
                methods: {
                    addGroup(group) {
                        var my = this
                        $.concreteAjax({
                            url: "<?=URL::to('/ccm/system/user/add_group')?>",
                            data: {
                                gID: group.gID,
                                uID: '<?=$user->getUserID()?>',
                                ccm_token: '<?= $token->generate('add_group') ?>'
                            },
                            success: function (r) {
                                if (r.groups) {
                                    r.groups.forEach(group => my.groups.push(group))
                                }
                            }
                        });
                    },
                    removeGroup(gID) {
                        var my = this
                        $.concreteAjax({
                            url: "<?=URL::to('/ccm/system/user/remove_group')?>",
                            data: {
                                gID: gID,
                                uID: '<?=$user->getUserID()?>',
                                ccm_token: '<?= $token->generate('remove_group') ?>'
                            },
                            success: function (r) {
                                const index = my.groups.findIndex(function (group) {
                                    return group.gID == r.group.gID
                                });
                                my.groups.splice(index, 1)
                            }
                        });
                    }
                },
            });
        })

        /*
         * Small hack to get the folder selector running within a vue context.
         * The folder selector contains a script tag which is not allowed within a vue context.
         * So we render the component outside and swap the containers.
         *
         * Not a nice way - but it works.
         *
         * @todo: Create a vue version of file folder selector and replace this hacky workaround with the vue component
         */
        $("#folderSelectorDestinationContainer").before($("#folderSelectorSourceContainer").removeClass("d-none"));
    });
</script>
