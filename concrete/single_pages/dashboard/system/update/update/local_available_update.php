<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Service\Dashboard $dashboard
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Controller\SinglePage\Dashboard\System\Update\Update $controller
 * @var Concrete\Core\Application\Service\Urls $ci
 * @var Concrete\Core\Updater\ApplicationUpdate $update
 * @var Concrete\Core\Url\UrlImmutable $updatePackagesUrl
 * @var Concrete\Core\Entity\Package[] $installedPackages
 */

?>

<form id="ccm-dashboard-update-form" method="POST" action="<?= $controller->action('do_update') ?>" v-cloak v-on:submit="handleSubmit">
    <?php $token->output('do_update') ?>
    <input type="hidden" name="version" value="<?= h($update->getVersion()) ?>" />

    <div class="alert alert-warning">
        <?= t('Before updating, it is highly recommended to make a full site backup. A full site backup consists of site files and site database export. Please consult your hosting provider for guidance on backup processes.') ?>
    </div>

    <div class="ccm-dashboard-update-details">
        <div class="ccm-dashboard-update-thumbnail"><img src="<?= ASSETS_URL_IMAGES ?>/logo.svg" /></div>
        <h2><?= t('Version %s', $update->getVersion()) ?></h2>
        <i v-bind:class="stateData.iconClass"></i> <span v-bind:class="stateData.textClass">{{ stateData.text }} </span>
    </div>

    <div class="ccm-dashboard-update-detail-columns container-fluid" v-if="state === STATE.SUCCESS">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <ul class="list-group">
                    <li class="list-group-item"><a href="#notes"><?= t('Release Notes') ?></a></li>
                    <li class="list-group-item"><a href="#addons"><?= t('Add-On Compatibility') ?></a></li>
                    <li class="list-group-item"><a href="#notices"><?= t('Important Notices') ?></a></li>
                </ul>
            </div>
            <div class="col ccm-dashboard-update-detail-main">
                <a v-if="details &amp;&amp; details.releaseNotesUrl" v-bind:href="details.releaseNotesUrl" target="_blank" class="btn btn-secondary btn-sm float-end"><?= t('View Full Release Notes') ?></a>
                <h3 id="notes"><?= t('Release Notes') ?></h3>
                <div class="ccm-dashboard-update-detail-release-notes" v-html="releaseNotes"></div>

                <div class="my-5"></div>

                <a href="<?= $updatePackagesUrl ?>" class="btn btn-secondary btn-sm float-end"><?= t('Update Add-Ons') ?></a>
                <h3 id="addons"><?= t('Add-On Compatibility') ?></h3>
                <?php
                if ($installedPackages === []) {
                    ?>
                    <div><i><?= t('No add-ons installed.') ?></i></div>
                    <?php
                } else {
                    foreach ($installedPackages as $installedPackage) {
                        $packageData = h('getAddonData(' . json_encode($installedPackage->getPackageHandle()) . ')');
                        ?>
                        <div class="media">
                            <img src="<?= $ci->getPackageIconURL($installedPackage) ?>" class="me-3" style="width: 49px" />
                            <div class="media-body">
                                <i class="float-end" v-bind:class="<?= $packageData?>.iconClass"></i>
                                <h5 class="my-0">
                                    <?= h($installedPackage->getPackageName()) ?>
                                    <span class="badge rounded-pill bg-secondary"><?= tc('AddonVersion', 'v.%s', $installedPackage->getPackageVersion()) ?></span>
                                </h5>
                                <div v-bind:class="<?= $packageData . '.stateClass' ?>" v-html="<?= $packageData ?>.stateHtml"></div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>

                <div class="my-5"></div>

                <h3 id="notices"><?= t('Upgrade Notices') ?></h3>
                <div>
                    <i v-if="state === STATE.FAILED"><?= t('Unable to retrieve upgrade notices from concrete5.org.') ?></i>
                    <i v-else-if="!details.notices || !details.notices.length"><?= t('No upgrade notices found.') ?></i>
                    <ul v-else class="fa-ul">
                        <li v-for="(notice, noticeIndex) in details.notices" v-bind:key="noticeIndex" class="my-3">
                            <span class="fa-li"><i v-bind:class="getNoticeData(notice).iconClass"></i></span>
                            <span v-bind:class="getNoticeData(notice).textClass" v-html="notice.status"></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-end">
                <a href="<?= $controller->action('check_for_updates') ?>" class="btn btn-primary" v-bind:class="this.state === this.STATE.LOADING ? 'disabled' : ''">
                    <?= t('Check For Updates') ?>
                </a>
                <button class="btn btn-primary" v-bind:disabled="this.state === this.STATE.LOADING" type="submit">{{ submitText }}</button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {

    Concrete.Vue.activateContext('backend', function (Vue, config) {
        new Vue({ 
            el: '#ccm-dashboard-update-form',
            data: function() {
                return {
                    STATE: {
                        LOADING: 0,
                        FAILED: 1,
                        SUCCESS: 2,
                    },
                    state: 0,
                    details: null,
                }
            },
            computed: {
                stateData: function() {
                    switch (this.state) {
                        case this.STATE.LOADING:
                            return {
                                iconClass: 'fas fa-cog fa-spin',
                                textClass: '',
                                text: <?= json_encode(t('Testing System...')) ?>,
                            };
                        case this.STATE.FAILED:
                            return {
                                iconClass: 'fas fa-exclamation-triangle text-info',
                                textClass: 'text-info',
                                text: <?= json_encode(t('Unable to retrieve information about this update from concrete5.org. You may upgrade but do so with caution.')) ?>,
                            };
                        case this.STATE.SUCCESS:
                            var data = {
                                iconClass: 'fas fa-arrow-circle-right',
                                textClass: '',
                                text: <?= json_encode(t('Update Ready')) ?>,
                            };
                            if (this.details.status) {
                                data.text = this.details.status.status || data.text;;
                                switch (this.details.status.safety) {
                                    case 'success':
                                        data.iconClass = 'fas fa-check text-success';
                                        data.textClass = 'text-success';
                                        break;
                                    case 'warning':
                                        data.iconClass = 'fas fa-exclamation-triangle text-warning';
                                        data.textClass = 'text-warning';
                                        break;
                                    case 'danger':
                                        data.iconClass = 'fas fa-exclamation-circle text-danger';
                                        data.textClass = 'text-danger';
                                        break;
                                }
                            }
                            return data;
                    }
                },
                releaseNotes: function() {
                    if (this.state === this.STATE.FAILED) {
                        return <?= json_encode('<i>' . t('Unable to retrieve release notes from concrete5.org.') . '</i>') ?>;
                    }
                    return this.details && this.details.releaseNotes ? this.details.releaseNotes : <?= json_encode('<i>' . t('Release notes not available.') . '</i>') ?>;
                },
                submitText: function() {
                    return this.state === this.STATE.LOADING ? <?= json_encode(t('Checking...')) ?> : <?= json_encode(t('Install Update')) ?>;
                },
            },
            methods: {
                getAddonData: function(mpHandle) {
                    if (this.state === this.STATE.LOADING) {
                        return {
                            iconClass: 'fa fa-question-circle text-muted',
                            stateClass: '',
                            stateHtml: <?= json_encode('<i>' . t('Loading... ') . '</i>') ?>,
                        }
                    }
                    var item = null;
                    if (this.details && this.details.marketplaceItemStatuses) {
                        this.details.marketplaceItemStatuses.some(function(i) {
                            if (i.mpHandle === mpHandle) {
                                item = i;
                                return true;
                            }
                        });
                    }
                    var result = {
                        iconClass: 'fa fa-question-circle text-muted',
                        stateClass: '',
                        stateHtml: <?= json_encode('<i>' . t('No information about this add-on available.') . '</i>') ?>,
                    }
                    if (item === null) {
                        return result;
                    }
                    result.stateHtml = item.status || '';
                    switch(item.safety) {
                        case 'success':
                            result.iconClass = 'fas fa-check text-success';
                            result.stateClass = 'text-success';
                            break;
                        case 'warning':
                            result.className = 'fas fa-exclamation-triangle text-warning';
                            result.stateClass  = 'text-warning';
                            break;
                        case 'danger':
                            result.className = 'fas fa-exclamation-circle text-danger';
                            result.stateClass = 'text-danger';
                            break;
                    }
                    return result;
                },
                getNoticeData: function(notice) {
                    var result = {
                        iconClass: '',
                        textClass: '',
                    };
                    switch(notice.safety) {
                        case 'info':
                            result.iconClass = 'fa fa-question-circle text-info';
                            result.textClass = '';
                            break;
                        case 'warning':
                            result.iconClass  = 'fa fa-warning text-warning';
                            result.textClass = 'text-warning';
                            break;
                        case 'danger':
                            result.iconClass  = 'fa fa-exclamation-circle text-danger';
                            result.textClass = 'text-danger';
                            break;
                    }
                    return result;
                },
                handleSubmit: function(e) {
                    if (this.state === my.STATE.LOADING) {
                        e.preventDefault();
                        return false;
                    }
                }
            },
            mounted: function() {
                var my = this;
                $.ajax({
                    data: {
                        version: <?= json_encode((string) $update->getVersion()) ?>,
                    },
                    dataType: 'json',
                    method: 'POST',
                    url: <?= json_encode((string) $controller->action('get_update_diagnostic_information')) ?>,
                })
                .done(function(data, status, xhr) {
                    ConcreteAjaxRequest.validateResponse(data, function(ok, details) {
                        if (ok) {
                            my.details = details;
                            my.state = my.STATE.SUCCESS;
                        } else {
                            my.state = my.STATE.FAILED;
                        }
                    });
                })
                .fail(function(xhr, status, error) {
                    my.state = my.STATE.FAILED;
                    ConcreteAlert.dialog(ccmi18n.error, ConcreteAjaxRequest.renderErrorResponse(xhr, true));
                });
            }
        });
    });
});
</script>
