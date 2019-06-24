<?php
defined('C5_EXECUTE') or die('Access Denied.');
$h = Loader::helper('concrete/dashboard');
$ih = Loader::helper('concrete/ui');
$form = Loader::helper('form');
$view = View::getInstance();
if ($canUpgrade) {
    ?>

    <div class="ccm-dashboard-header-buttons">
        <a href="<?= $view->action('check_for_updates') ?>" class="btn btn-primary">
            <?= t('Check For Updates') ?>
        </a>
    </div>

   <?php if (is_object($update)) { ?>
        <div class="alert alert-warning">
            <p><?= t('Before updating, it is highly recommended to make a full site backup. A full site backup consists of site files and site database export. Please consult your hosting provider for guidance on backup processes.'); ?></p>
        </div>

        <div class="ccm-dashboard-update-details-wrapper">

            <div class="ccm-dashboard-update-details">
                <div class="ccm-dashboard-update-thumbnail"><img src="<?=ASSETS_URL_IMAGES?>/logo.svg" /></div>
                <h2><?=t('Version %s', $update->getVersion())?></h2>
                <div><i class="fa fa-cog"></i> <span class="ccm-dashboard-update-details-testing-text"><?=t('Testing System...')?></span></div>
            </div>

            <div class="ccm-dashboard-update-nav">
                <form method="post" action="<?=$view->action('do_update')?>">
                    <?=$token->output('do_update')?>
                    <input type="hidden" name="version" value="<?=$update->getVersion()?>" />
                    <div class="ccm-dashboard-update-apply">
                        <button class="btn btn-primary" disabled="disabled" type="submit" name="update" value="1"><?=t('Checking...')?></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="ccm-dashboard-update-detail-columns">
            <div class="row">
                <div class="col-md-4">
                    <ul class="list-group">
                        <li class="list-group-item"><span data-href="#notes" class="text-muted"><?php echo t('Release Notes')?></span></li>
                        <li class="list-group-item"><span data-href="#addons" class="text-muted"><?php echo t('Add-On Compatibility')?></span></li>
                        <li class="list-group-item"><span data-href="#notices" class="text-muted"><?php echo t('Important Notices')?></span></li>
                    </ul>
                </div>
                <div class="col-md-7 col-md-offset-1 ccm-dashboard-update-detail-main">
                    <a name="notes"></a>
                    <a href="#" target="_blank" data-url="info" style="display: none" class="btn btn-default pull-right btn-xs "><?=t('View Full Release Notes')?></a>
                    <h3><?=t('Release Notes')?></h3>
                    <div class="ccm-dashboard-update-detail-release-notes"><?=t('Retrieving Release Notes...')?></div>

                    <div class="spacer-row-5"></div>

                    <a name="addons"></a>
                    <a href="<?=URL::to('/dashboard/extend/update')?>" class="btn btn-default pull-right btn-xs "><?=t('Update Add-Ons')?></a>
                    <h3><?=t('Add-On Compatibility')?></h3>
                    <?php $list = \Package::getInstalledList();
    $ci = Core::make('helper/concrete/urls');
    if (count($list) == 0) {
        ?>
                        <p><?=t('No add-ons installed.')?></p>

                    <?php
    }
    foreach ($list as $pkg) {
        ?>

                        <div class="media" data-addon="<?=$pkg->getPackageHandle()?>">
                            <div class="pull-left"><img style="width: 49px" src="<?= $ci->getPackageIconURL($pkg);
        ?>" class"media-object" /></div>
                            <div class="media-body">
                                <i class="fa fa-question-circle text-muted pull-right"></i>
                                <h4 class="media-heading"><?= $pkg->getPackageName();
        ?> <span class="badge badge-info" style="margin-right: 10px"><?= tc('AddonVersion', 'v.%s', $pkg->getPackageVersion());
        ?></span></h4>
                                <div class="ccm-dashboard-update-detail-status-text"></div>
                            </div>
                        </div>

                    <?php
    }
    ?>

                    <div class="spacer-row-5"></div>

                    <h3><?=t('Upgrade Notices')?></h3>
                    <a name="notices"></a>
                    <div class="ccm-dashboard-update-detail-notices"><?=t('Loading...')?></div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
        $(function() {

            handleError = function(r) {
                var $statusIcon = $('.ccm-dashboard-update-details i'),
                    $statusText = $('.ccm-dashboard-update-details-testing-text');
                $statusIcon.removeClass().addClass('fa fa-warning text-info');
                $statusText.removeClass().addClass('text-info').text(<?=json_encode(t('Unable to retrieve information about this update from concrete5.org. You may upgrade but do so with caution.'))?>);
                $('.ccm-dashboard-update-detail-release-notes').html(<?=json_encode(t('Unable to retrieve release notes from concrete5.org.'))?>);
                $('.ccm-dashboard-update-detail-notices').html(<?=json_encode(t('Unable to retrieve upgrade notices from concrete5.org.'))?>);
            }

            $.ajax({
                dataType: 'json',
                type: 'post',
                data: {
                    'version': '<?=$update->getVersion()?>'
                },
                complete: function() {
                    $('.ccm-dashboard-update-apply button').prop('disabled', false).text(<?=json_encode(t('Install Update'))?>);
                },
                url: '<?=$view->action('get_update_diagnostic_information')?>',
                error: function(r) {
                    handleError(r);
                },
                success: function(r) {
                    if (!r.requestedVersion) {
                        handleError(r);
                        return false;
                    }

                    $('a[data-url=info]').attr('href', r.releaseNotesUrl).show();
                    $('.ccm-dashboard-update-detail-release-notes').html(r.releaseNotes);
                    $('span[data-href]').each(function() {
                        var $tag = $('<a />', {'href': $(this).attr('data-href'), text: $(this).text()});
                        $(this).replaceWith($tag);
                    });
                    var $wrapper = $('.ccm-dashboard-update-detail-notices');
                    $wrapper.html('');
                    if (r.notices && r.notices.length) {
                        $.each(r.notices, function(i, notice) {
                            var className = '';
                            var textClassName = '';
                            switch(notice.safety) {
                                case 'info':
                                    className = 'fa fa-question-circle text-info';
                                    textClassName = '';
                                    break;
                                case 'warning':
                                    className = 'fa fa-warning text-warning';
                                    textClassName = 'text-warning';
                                    break;
                                case 'danger':
                                    className = 'fa fa-exclamation-circle text-danger';
                                    textClassName = 'text-danger';
                                    break;
                            }
                            $wrapper.append('<div class="media"><div class="pull-left"><i class="' + className + '"></i></div><div class="media-body ' + textClassName + '">' + notice.status + '</div></div>');
                        });
                    } else {
                        $wrapper.append(<?=json_encode(t('No upgrade notices found.'))?>);
                    }
                    var $statusIcon = $('.ccm-dashboard-update-details i'),
                        $statusText = $('.ccm-dashboard-update-details-testing-text');
                    if (r.status) {
                        var className = '';
                        var textClassName = '';
                        switch(r.status.safety) {
                            case 'success':
                                className = 'fa fa-check text-success';
                                textClassName = 'text-success';
                                break;
                            case 'warning':
                                className = 'fa fa-warning text-warning';
                                textClassName = 'text-warning';
                                break;
                            case 'danger':
                                className = 'fa fa-exclamation-circle text-danger';
                                textClassName = 'text-danger';
                                break;
                            default:
                                className = 'fa fa-arrow-circle-right';
                                textClassName = '';
                        }
                        $statusIcon.removeClass().addClass(className);
                        $statusText.removeClass().addClass(textClassName).text(r.status.status);
                    } else {
                        $statusIcon.removeClass().addClass('fa fa-arrow-circle-right');
                        $statusText.removeClass().addClass(textClassName).text(<?=json_encode(t('Update Ready'))?>);
                    }
                    $('[data-addon]').each(function() {
                        var $addon = $(this);
                        var item = false;
                        var textClassName = '';
                        var mpHandle = $addon.attr('data-addon');
                        if (r.marketplaceItemStatuses) {
                            var item = _.find(r.marketplaceItemStatuses, function(item) {
                                return item.mpHandle == mpHandle;
                            });
                            if (item) {
                                var className = '';
                                switch(item.safety) {
                                    case 'success':
                                        className = 'fa fa-check text-success pull-right';
                                        textClassName = 'text-success';
                                        break;
                                    case 'warning':
                                        className = 'fa fa-warning text-warning pull-right';
                                        textClassName = 'text-warning';
                                        break;
                                    case 'danger':
                                        className = 'fa fa-exclamation-circle text-danger pull-right';
                                        textClassName = 'text-danger';
                                        break;
                                }

                                if (className) {
                                    $addon.find('i').removeClass().addClass(className);
                                }
                            }
                        }
                        if (item) {
                            $addon.find('.ccm-dashboard-update-detail-status-text').addClass(textClassName).html(item.status);
                        } else {
                            $addon.find('.ccm-dashboard-update-detail-status-text').html(<?=json_encode(t('No information about this add-on available.'))?>);
                        }
                    });
                }
            });
        });
        </script>


    <?php
} else {
    ?>


        <?php if ($downloadableUpgradeAvailable) {
    ?>

            <h2><?= t('Available Update for Download') ?></h2>
            <form method="post" action="<?= $view->action('download_update') ?>" id="ccm-download-update-form">

                <?= Loader::helper('validation/token')->output('download_update') ?>

                <legend style="line-height:40px">
                    <?= t('Version: %s', $remoteUpdate->getVersion()) ?>.
                    <?= t('Release Date: %s', date(t('F d, Y'), strtotime($remoteUpdate->getDate()))) ?>
                    <?= Loader::helper('concrete/ui')->submit(
                        t('Download'),
                        'ccm-download-update-form',
                        'right',
                        'btn-success') ?>
                </legend>
                <div id="ccm-release-notes">
                    <?= $remoteUpdate->getNotes() ?>
                </div>
                <hr/>
                <span class="help-block"><?= t('Note: Downloading an update will NOT automatically install it.') ?></span>

            </form>
            <script>
                $('header.ccm-dashboard-page-header').children().text(<?=json_encode(t('Currently Running %s', config::get('concrete.version'))) ?>);
            </script>

        <?php

}
    ?>

            <h2><?= t('Apply Downloaded Update') ?></h2>
        <?php if (count($updates)) {
    ?>
            <?php
            $ih = Loader::helper('concrete/ui');
    ?>

            <p><?= t('Several updates are available. Please choose the desired update from the list below.') ?></p>
            <span class="label"><?= t('Current Version') ?> <?= config::get('concrete.version') ?></span>
            <form method="post" class="form" action="<?= $view->action('start') ?>" id="ccm-update-form">
                <?php
                $checked = true;
    foreach ($updates as $upd) {
        ?>
                    <div class="radio">
                        <label>
                            <input type="radio" name="updateVersion"
                                   value="<?= $upd->getUpdateVersion() ?>" <?= (!$checked ? '' : "checked") ?> />
                            <?= $upd->getUpdateVersion() ?>
                        </label>
                    </div>
                    <?php
                    $checked = false;
    }
    ?>
                <div class="ccm-dashboard-form-actions-wrapper">
                    <div class="ccm-dashboard-form-actions">
                        <?= $ih->submit(t('Update'), false, 'right', 'btn-primary') ?>
                    </div>
                </div>
            </form>
            </div>
            <div class="clearfix">&nbsp;</div>
        <?php

} else {
    ?>
            <p><?=t('No updates are ready to be installed.')?></p>

        <?php
}
    ?>
    <?php
}
    ?>

<?php
} else {
    ?>
    <p><?=t('You do not have permission to upgrade this installation of concrete5.')?></p>
<?php
} ?>
