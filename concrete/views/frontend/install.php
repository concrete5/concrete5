<?php

use Concrete\Core\Error\ErrorList\Error\AbstractError;
use Concrete\Core\Install\PreconditionResult;
use Concrete\Core\Install\WebPreconditionInterface;
use Concrete\Core\Localization\Localization;

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Controller\Install $controller */
/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Html\Service\Html $html */
/* @var Concrete\Core\View\View $this */
/* @var Concrete\Core\View\View $view */
/* @var Concrete\Core\Url\Resolver\UrlResolverInterface $urlResolver */
/* @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $urlResolver */

/* @var int $backgroundFade */
/* @var string $pageTitle */
/* @var string $image */
/* @var string $imagePath */
/* @var string $concreteVersion */

/* @var int $installStep */

$install_config = Config::get('install_overrides');
$uh = Core::make('helper/concrete/urls');
if ($install_config) {
    $_POST = $install_config;
}

?>

<div id="ccm-page-install">
    <div class="ccm-install-version">
        <span class="badge bg-info"><?= t('Version %s', $concreteVersion) ?></span>
    </div>
    <div class="ccm-install-title">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><?= t('Install concrete5') ?></li>
            <?php switch ($installStep) {
                case $controller::STEP_CHOOSELOCALE: ?>
                    <li class="breadcrumb-item active"><?= t('Choose Language') ?></li>
                    <?php
                    break;
                case $controller::STEP_CONFIGURATION: ?>
                    <li class="breadcrumb-item active"><?= t('Site Information') ?></li>
                    <?php
                    break;
                case $controller::STEP_INSTALL: ?>
                    <li class="breadcrumb-item active"><?= t('Installing...') ?></li>
                    <?php
                    break;
                case $controller::STEP_PRECONDITIONS: ?>
                    <li class="breadcrumb-item active"><?= t('Testing Environment') ?></li>
                    <?php
                    break; ?>
                <?php } ?>
        </ul>
    </div>


    <?php if ($installStep === $controller::STEP_CHOOSELOCALE) { ?>

        <form method="post" id="ccm-install-language-form"
              action="<?= $urlResolver->resolve(['install', 'select_language']) ?>" class="w-100">
            <div class="form-group">
                <p class="lead"><?=t('Choose the language you want to run your website in.')?></p>

                <div class="input-group-lg input-group">
                    <?php
                    $selectOptions = $locales;
                    if (!empty($onlineLocales)) {
                        $selectOptions[t('Online Languages')] = $onlineLocales;
                    }
                    ?>
                    <?= $form->select('wantedLocale', $selectOptions, Localization::BASE_LOCALE, [
                        'class' => 'form-select'
                    ]); ?>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </form>

    <?php } else if ($installStep === $controller::STEP_PRECONDITIONS) { ?>

    <?php
    $showRerunTests = false;
    $requiredPreconditionFailed = false;
    $pendingPreconditions = [];
    list($requiredPreconditions, $optionalPreconditions) = $controller->getPreconditions();
    $i = 0;
    foreach ([
        t('Required Items') => $requiredPreconditions,
        t('Optional Items') => $optionalPreconditions,
    ] as $preconditionsTitle => $preconditions) {
    $numPreconditions = count($preconditions);
    if ($numPreconditions === 0) {
        continue;
    }
    $leftRightPreconditions = array_chunk($preconditions, ceil($numPreconditions / 2));
    ?>
        <div class="card card-default <?php if ($i == 0) { ?>mb-4<?php } ?>">
            <div class="card-header"><?= $preconditionsTitle ?></div>
            <div class="card-body">
                <div class="row">
                    <?php
                    foreach ($leftRightPreconditions as $preconditions) {
                        ?>
                        <div class="col-sm-6">
                            <table class="table requirements-table">
                                <tbody>
                                <?php
                                foreach ($preconditions as $precondition) {
                                    /* @var Concrete\Core\Install\PreconditionInterface $precondition */
                                    if ($precondition instanceof WebPreconditionInterface) {
                                        echo $precondition->getHtml();
                                        $preconditionState = $precondition->getInitialState();
                                        $preconditionMessage = $precondition->getInitialMessage();
                                        $pendingPreconditions[] = $precondition->getUniqueIdentifier();
                                    } else {
                                        $preconditionResult = $precondition->performCheck();
                                        $preconditionState = $preconditionResult->getState();
                                        $preconditionMessage = $preconditionResult->getMessage();
                                    }
                                    ?>
                                    <tr id="precondition-<?= $precondition->getUniqueIdentifier() ?>">
                                        <td>
                                            <?php
                                            if ($preconditionState === null) {
                                                echo '<i class="precondition-state fa fa-spinner fa-spin"></i>';
                                            } else {
                                                switch ($preconditionState) {
                                                    case PreconditionResult::STATE_PASSED:
                                                        echo '<i class="precondition-state fa fa-check"></i>';
                                                        break;
                                                    case PreconditionResult::STATE_WARNING:
                                                        if (!$precondition instanceof WebPreconditionInterface) {
                                                            $showRerunTests = true;
                                                        }
                                                        echo '<i class="precondition-state fa fa-warning"></i>';
                                                        break;
                                                    case PreconditionResult::STATE_SKIPPED:
                                                        break;
                                                    case PreconditionResult::STATE_FAILED:
                                                    default:
                                                        if (!$precondition instanceof WebPreconditionInterface) {
                                                            $showRerunTests = true;
                                                            if (!$precondition->isOptional()) {
                                                                $requiredPreconditionFailed = true;
                                                            }
                                                        }
                                                        echo '<i class="precondition-state fa fa-exclamation-circle"></i>';
                                                        break;
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td style="width: 100%">
                                            <?= h($precondition->getName()) ?>
                                        </td>
                                        <td class="preconditionmessage">
                                            <?php
                                            if ($preconditionMessage !== '') {
                                                ?>
                                                <i class="fa fa-question-circle launch-tooltip"
                                                   title="<?= h($preconditionMessage) ?>"></i>
                                                <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php
        $i++;
    }
    ?>
        <script>
            (function () {
                var showRerunTests = <?= json_encode($showRerunTests) ?>,
                    requiredPreconditionFailed = <?= json_encode($requiredPreconditionFailed) ?>,
                    pendingPreconditions = <?= json_encode($pendingPreconditions) ?>;

                function checkDone() {
                    if (pendingPreconditions.length > 0) {
                        return;
                    }
                    if (showRerunTests) {
                        $('#rerun-tests').css('visibility', 'visible');
                    }
                    if (!requiredPreconditionFailed) {
                        $('#continue-to-installation').css('visibility', 'visible');
                    }
                }

                window.setWebPreconditionResult = function (id, success, message, isOptional) {
                    if (!success) {
                        showRerunTests = true;
                        if (!isOptional) {
                            requiredPreconditionFailed = true;
                        }
                    }
                    var index = pendingPreconditions.indexOf(id);
                    if (index >= 0) {
                        pendingPreconditions.splice(index, 1);
                    }
                    var $tr = $('#precondition-' + id),
                        $state = $tr.find('.precondition-state'),
                        $message = $tr.find('.preconditionmessage');
                    $state
                        .removeClass('fa-spinner fa-spin fa-check fa-warning fa-exclamation-circle')
                        .addClass(success ? 'fa-check' : 'fa-exclamation-circle');
                    $message.empty();
                    if (message) {
                        const $icon = $('<i class="fa fa-question-circle launch-tooltip" />').attr('title', message)
                        const tooltip = new bootstrap.Tooltip($icon)
                        $message.append($icon);
                    }
                    checkDone();
                };
                $(document).ready(function () {
                    checkDone();
                });
            })();
        </script>
        <style>
            #install-errors {
                display: none
            }

            #rerun-tests {
                display: none
            }
        </style>
        <noscript>
            <style>
                #install-errors {
                    display: block
                }

                #rerun-tests {
                    display: block
                }
            </style>
        </noscript>
        <div class="alert alert-danger" id="install-errors">
            <?= t('There are problems with your installation environment. Please correct them and click the button below to re-run the pre-installation tests.') ?>
            <?= t('Having trouble? Check the <a href="%s">installation help forums</a>, or <a href="%s">have us host a copy</a> for you.',
                'http://www.concrete5.org/community/forums', 'http://www.concrete5.org/services/hosting') ?>
        </div>
        <div class="ccm-install-actions">
            <form method="post" action="<?= $urlResolver->resolve(['install']) ?>" id="rerun-tests"
                  class="pull-left">
                <input type="hidden" name="locale" value="<?= h($locale) ?>"/>
                <button class="btn btn-danger" type="submit">
                    <?= t('Run Tests Again') ?>
                    <i class="fa fa-refresh"></i>
                </button>
            </form>
            <form method="post" action="<?= $urlResolver->resolve(['install', 'setup']) ?>"
                  id="continue-to-installation" style="visibility: hidden" class="pull-right">
                <input type="hidden" name="locale" value="<?= h($locale) ?>"/>
                <a class="float-start btn btn-secondary btn-sm" href="<?=URL::to('/')?>">
                    <?= t('Back') ?>
                </a>

                <button class="float-end btn btn-primary btn-sm" onclick="$(this).parent().submit()">
                    <?= t('Continue to Installation') ?>
                </button>
            </form>
        </div>

    <?php } else if ($installStep === $controller::STEP_CONFIGURATION) { ?>

        <script type="text/javascript">
            $(function () {
                $("#sample-content-selector td").click(function () {
                    $(this).parent().find('input[type=radio]').prop('checked', true);
                    $(this).parent().parent().find('tr').removeClass();
                    $(this).parent().addClass('package-selected');
                });

                function updateCanonicalURLState() {
                    $.each([
                        [$('#canonicalUrlChecked').is(':checked'), $('#canonicalUrl')],
                        [$('#canonicalUrlAlternativeChecked').is(':checked'), $('#canonicalUrlAlternative')]
                    ], function () {
                        if (this[0]) {
                            this[1].attr('required', 'required');
                            this[1].removeAttr('disabled');
                        } else {
                            this[1].removeAttr('required');
                            this[1].attr('disabled', 'disabled');
                        }
                    });
                }

                $('#canonicalUrlChecked,#canonicalUrlAlternativeChecked').change(updateCanonicalURLState);
                <?php
                if ($setInitialState) {
                ?>
                $('#canonicalUrlChecked').prop('checked', <?=$canonicalUrlChecked ? 'true' : 'false'?>);
                $('#canonicalUrlAlternativeChecked').prop('checked', <?=$canonicalUrlAlternativeChecked ? 'true' : 'false'?>);
                <?php
                }
                ?>
                updateCanonicalURLState();
            });
        </script>

        <form action="<?= $urlResolver->resolve(['install', 'configure']) ?>" method="post">
            <?php
            if (isset($warnings) && $warnings->has()) {
                /* @var Concrete\Core\Error\ErrorList\ErrorList $warnings */
                ?>
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        <div class="ccm-system-errors alert alert-warning">
                            <?php
                            foreach ($warnings->getList() as $warning) {
                                ?>
                                <div>
                                    <?php
                                    if ($warning instanceof AbstractError && $warning->messageContainsHtml()) {
                                        echo $warning->getMessage();
                                    } else {
                                        echo nl2br(h($warning));
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="form-check">
                                <?= $form->checkbox('ignore-warnings', '1') ?>
                                <?=$form->label('ignore-warnings', t('Ignore warnings')) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>

            <div class="card card-default mb-4">
                <div class="card-header"><?= t('Site') ?></div>
                <div id="site" class="">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="SITE" class="control-label form-label"><?= t('Name') ?></label>
                                    <?= $form->text('SITE',
                                        ['autofocus' => 'autofocus', 'required' => 'required']) ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="uEmail"
                                           class="control-label form-label"><?= t('Administrator Email Address') ?></label>
                                    <?= $form->email('uEmail', ['required' => 'required']) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="uPassword"
                                           class="control-label form-label"><?= t('Administrator Password') ?></label>
                                    <?= $form->password('uPassword', $passwordAttributes) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="uPassword"
                                           class="control-label form-label"><?= t('Confirm Password') ?></label>
                                    <?= $form->password('uPasswordConfirm', $passwordAttributes) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-default mb-4">
                <div class="card-header"><?= t('Starting Point') ?></div>
                <div id="starting-point" class="container">
                    <div class="card-body row">
                        <?php
                        $availableSampleContent = StartingPointPackage::getAvailableList();
                        $i = 1;
                        foreach ($availableSampleContent as $spl) {
                            $pkgHandle = $spl->getPackageHandle();
                            ?>
                            <div class="col-md-6">
                                <div class="form-check">

                                        <?= $form->radio('SAMPLE_CONTENT', $pkgHandle,
                                            ($pkgHandle == 'elemental_full' || count($availableSampleContent) == 1)) ?>
                                    <?=$form->label('SAMPLE_CONTENT' . $i,"<strong>".$spl->getPackageName()."</strong><br/>". $spl->getPackageDescription()) ?>

                                </div>
                            </div>
                            <?php
                            $i++;
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="card card-default mb-4">
                <div class="card-header">
                    <?= t('Database') ?>
                </div>
                <div id="database" class="card-collapse collapse show">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label form-label" for="DB_SERVER"><?= t('Server') ?></label>
                                    <?= $form->text('DB_SERVER', ['required' => 'required']) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label form-label"
                                           for="DB_USERNAME"><?= t('MySQL Username') ?></label>
                                    <?= $form->text('DB_USERNAME') ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label form-label"
                                           for="DB_PASSWORD"><?= t('MySQL Password') ?></label>
                                    <?= $form->password('DB_PASSWORD', ['autocomplete' => 'off']) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label form-label"
                                           for="DB_DATABASE"><?= t('Database Name') ?></label>
                                    <?= $form->text('DB_DATABASE', ['required' => 'required']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-default mb-4">
                <div class="card-header">
                    <?= t('Privacy Policy') ?>
                </div>
                <div class="card-body">
                    <p class="text-muted"><?= t('concrete5 collects some information about your website to assist in upgrading and checking add-on compatibility. This information can be disabled in configuration.') ?></p>
                    <div class="form-check">
                        <?= $form->checkbox('privacy', 1, false, ['required' => 'required']) ?>
                        <label class="form-check-label" for="privacy">
                            <?= t('Yes, I understand and agree to the <a target="_blank" href="%s">Privacy Policy</a>.',
                                Config::get('concrete.urls.privacy_policy')) ?>
                        </label>
                    </div>
                </div>
            </div>
            <div class="card card-default">
                <div class="card-header" role="tab" id="headingThree">
                    <a class="collapsed" role="button" data-bs-toggle="collapse"
                       href="#advanced"><?= t('Advanced Options') ?>
                    </a>
                </div>

                <div id="advanced" class="card-collapse collapse">
                    <div class="card-body container">

                        <div class="row">

                            <div class="col-sm-6">
                                <h4><?= t('URLs & Session') ?></h4>

                                <div class="form-group">
                                    <label class="control-label form-label">
                                        <div class="form-check">
                                            <?= $form->checkbox('canonicalUrlChecked', '1') ?>
                                            <label class="form-check-label" for="canonicalUrlChecked">
                                                <?= t('Set main canonical URL') ?>:
                                            </label>
                                        </div>
                                    </label>
                                    <?= $form->url('canonicalUrl', h($canonicalUrl), [
                                        'pattern' => 'https?:.+',
                                        'placeholder' => t('%s or %s', 'http://...', 'https://...')
                                    ]) ?>
                                </div>

                                <div class="form-group">
                                    <label class="control-label form-label">
                                        <div class="form-check">
                                            <?= $form->checkbox('canonicalUrlAlternativeChecked', '1') ?>
                                            <label class="form-check-label" for="canonicalUrlAlternativeChecked">
                                                <?= t('Set alternative canonical URL') ?>:
                                            </label>
                                        </div>
                                    </label>
                                    <?= $form->url('canonicalUrlAlternative', h($canonicalUrlAlternative), [
                                        'pattern' => 'https?:.+',
                                        'placeholder' => t('%s or %s', 'http://...', 'https://...')
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label class="control-label form-label"
                                           for="sessionHandler"><?= t('Session Handler') ?></label>
                                    <?= $form->select('sessionHandler', [
                                        '' => t('Default Handler (Recommended)'),
                                        'database' => t('Database')
                                    ]) ?>
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <h4><?= t('Locale') ?></h4>

                                <div class="form-group">
                                    <label class="control-label form-label"
                                           for="sessionHandler"><?= t('Language') ?></label>
                                    <?= $form->select('siteLocaleLanguage', $languages,
                                        $computedSiteLocaleLanguage) ?>
                                </div>

                                <div class="form-group">
                                    <label class="control-label form-label"
                                           for="sessionHandler"><?= t('Country') ?></label>
                                    <?= $form->select('siteLocaleCountry', $countries,
                                        $computedSiteLocaleCountry) ?>
                                </div>

                                <div class="form-group">
                                    <label class="control-label form-label"
                                           for="SERVER_TIMEZONE"><?= t('System Time Zone') ?></label>
                                    <?= $form->select('SERVER_TIMEZONE', $availableTimezones,
                                        $SERVER_TIMEZONE,
                                        ['required' => 'required']) ?>
                                </div>

                                <script>
                                    $('#siteLocaleLanguage').on('change', function () {
                                        $.ajax(
                                            <?= json_encode((string)$urlResolver->resolve([
                                                'install',
                                                'get_site_locale_countries'
                                            ])) ?> +'/' + encodeURIComponent(<?= json_encode(Localization::activeLocale()) ?>) + '/' + encodeURIComponent(this.value) + '/' + encodeURIComponent($('#siteLocaleCountry').val()),
                                            {
                                                dataType: 'json'
                                            }
                                        )
                                            .done(function (r) {
                                                $('#siteLocaleCountry').replaceWith(r);
                                            });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="locale" value="<?= h($locale) ?>"/>
            <div class="ccm-install-actions">
                <div class="w-100">
                    <button type="submit" class="btn btn-primary btn-sm float-end">
                        <?= t('Install concrete5') ?>
                    </button>
                </div>
            </div>

        </form>

    <?php } else if ($installStep === $controller::STEP_INSTALL) { ?>

    <script type="text/javascript">
        $(function () {
            var inviteToStayHere = false;

            showFailure = function (message) {
                NProgress.done();
                inviteToStayHere = false;
                $("#install-progress-errors").append('<div class="alert alert-danger">' + message + '</div>');
                $("#interstitial-message").hide();
                $("#install-progress-error-wrapper").show();
                $('button[data-button=installation-complete]').prop('disabled', false).html(<?=json_encode(t('Back'))?>).on('click', function () {
                    window.location.href = <?= json_encode((string)$urlResolver->resolve(['install'])) ?>;
                });
                $("#install-progress-summary").html('<span class="text-danger"><?=t('An error occurred.')?></span>');
                $('div.ccm-install-title ul.breadcrumb li.active').text(<?= json_encode(t('Installation Failed.')) ?>);
            }

            window.onbeforeunload = function () {
                if (inviteToStayHere) {
                    return <?=json_encode(t("concrete5 installation is still in progress: you shouldn't close this page at the moment"))?>;
                }
            };
            NProgress.configure({showSpinner: false});
            <?php
            for ($i = 1; $i <= count($installRoutines); ++$i) {
            $routine = $installRoutines[$i - 1];
            ?>
            ccm_installRoutine<?=$i?> = function () {
                <?php
                if ($routine->getText() != '') {
                ?>
                $("#install-progress-summary").html(<?= json_encode($routine->getText()) ?>);
                <?php
                }
                ?>
                $.ajax(
                    <?= json_encode((string)$urlResolver->resolve([
                        'install',
                        'run_routine',
                        $installPackage,
                        $routine->getMethod()
                    ])) ?>,
                    {
                        dataType: 'json'
                    }
                )
                    .fail(function (r) {
                        showFailure(r.responseText);
                    })
                    .done(function (r) {
                        if (r.error) {
                            showFailure(r.message);
                        } else {
                            NProgress.set(<?=$routine->getProgress() / 100?>);
                            <?php
                            if ($i < count($installRoutines)) {
                            ?>
                            ccm_installRoutine<?=$i + 1?>();
                            <?php
                            } else {
                            ?>
                            inviteToStayHere = false;
                            $("#install-progress-summary").html(<?= json_encode(t('All Done.')) ?>);
                            NProgress.done();
                            $('button[data-button=installation-complete]').prop('disabled', false).html(<?=json_encode(t('Edit Your Site') . ' <i class="fa fa-thumbs-up"></i>')?>);
                            $('div.ccm-install-title ul.breadcrumb li.active').text(<?= json_encode(t('Installation Complete.')) ?>);
                            setTimeout(function () {
                                $("#interstitial-message").hide();
                                $("#success-message").show().addClass('animated fadeInDown');
                            }, 500);
                            <?php
                            }
                            ?>
                        }
                    });
            }
            <?php
            }
            ?>
            inviteToStayHere = true;
            ccm_installRoutine1();
        });
    </script>


    <div id="interstitial-message">
        <div class="card card-info">
            <div class="card-header"><?= t('While You Wait') ?></div>
            <div class="card-body">
                <h4 class=""><?= t('Forums') ?></h4>
                <p>
                <?= t('<a href="%s" target="_blank">The forum</a> on concrete5.org is full of helpful community members that make concrete5 so great.',
                    Config::get('concrete.urls.help.forum')) ?>
                </p>

                <h4 class=""><?= t('Slack') ?></h4>
                <p>
                <?= t('In the <a href="%s" target="_blank">concrete5 Slack channels</a> you can get in touch with a lot of concrete5 lovers and developers.',
                    Config::get('concrete.urls.help.slack')) ?>
                </p>

                <h4 class=""><?= t('User Documentation') ?></h4>
                <p>
                <?= t('Read the <a href="%s" target="_blank">User Documentation</a> to learn editing and site management with concrete5.',
                    Config::get('concrete.urls.help.user')) ?>
                </p>

                <h4 class=""><?= t('Screencasts') ?></h4>
                <p>
                <?= t('The concrete5 <a href="%s" target="_blank">YouTube Channel</a> is full of useful videos covering how to use concrete5.',
                    Config::get('concrete.urls.videos')) ?>
                </p>

                <h4 class=""><?= t('Developer Documentation') ?></h4>
                <p>
                <?= t('The <a href="%s" target="_blank">Developer Documentation</a> covers theming, building add-ons and custom concrete5 development.',
                    Config::get('concrete.urls.help.developer')) ?>
                </p>

            </div>
        </div>
    </div>

    <div id="success-message">
        <div class="card">
            <div class="card-header"><?= t('Installation Complete') ?></div>
            <div class="card-body">
                <?= $successMessage ?>
            </div>
        </div>
    </div>

    <div id="install-progress-error-wrapper">
        <div id="install-progress-errors">
        </div>
    </div>

    <div class="ccm-install-actions">
        <div class="w-100">
            <div id="install-progress-summary"><?= t('Beginning Installation') ?></div>
            <button type="submit" disabled="disabled" onclick="window.location.href='<?= URL::to('/') ?>'"
                    data-button="installation-complete" class="float-end btn btn-sm btn-primary">
                <?= t('Installing...') ?>
                <i class="fa fa-spinner fa-spin"></i>
            </button>
        </div>
    </div>

<?php } ?>


</div>
