<?php
use Concrete\Core\Attribute\Key\Key;
use Concrete\Core\Http\ResponseAssetGroup;

defined('C5_EXECUTE') or die('Access denied.');

/* @var \Concrete\Core\Error\ErrorList\ErrorList $error */
/* @var \League\OAuth2\Server\RequestTypes\AuthorizationRequest $auth */
/* @var \Concrete\Core\Http\Request $request */
/* @var \Concrete\Core\Entity\OAuth\Client $client */
/* @var \Concrete\Core\View\View $consentView */

$r = ResponseAssetGroup::get();
$r->requireAsset('javascript', 'underscore');
$r->requireAsset('javascript', 'core/events');

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$form = $app->make(\Concrete\Core\Form\Service\Form::class);
$token = $app->make(\Concrete\Core\Validation\CSRF\Token::class);

if (isset($authType) && $authType) {
    $active = $authType;
    $activeAuths = array($authType);
} else {
    $active = null;
    $activeAuths = AuthenticationType::getList(true, true);
}
if (!isset($authTypeElement)) {
    $authTypeElement = null;
}
if (!isset($authTypeParams)) {
    $authTypeParams = null;
}

// Always use last week's picture
$image = (date('Ymd') - 7) . '.jpg';
?>

<div class="login-page">
    <div class="col-sm-6 col-sm-offset-3">
        <h1><?= t('Authorize') ?></h1>
    </div>
    <div class="col-sm-6 col-sm-offset-3 login-form">
        <div class="row login-row">
            <div class="controls col-sm-12 col-xs-12" style="display:flex; flex-direction: column; overflow: auto">
                <?php
                if (!$authorize) {
                    ?>
                    <h3 class="text-center"><?= t('Sign in to %s', "<strong>{$client->getName()}</strong>") ?></h3>
                    <?php
                }
                ?>

                <form style="display:flex; flex-direction: column; flex: 1" method="post" action="<?= $request->getUri() ?>">
                    <?php
                    if (!$authorize) {
                        ?>
                        <div class="form-group">
                            <label class="control-label form-label"
                                   for="uName"><?= $emailLogin ? t('Email Address') : t('Username') ?></label>
                            <input name="uName" id="uName" class="form-control" autofocus="autofocus"/>
                        </div>

                        <div class="form-group">
                            <label class="control-label form-label" for="uPassword"><?= t('Password') ?></label>
                            <input name="uPassword" id="uPassword" class="form-control" type="password" autocomplete="off"/>
                        </div>

                        <?php if (isset($locales) && is_array($locales) && count($locales) > 0) {
                            ?>
                            <div class="form-group">
                                <label for="USER_LOCALE" class="control-label form-label"><?= t('Language') ?></label>
                                <?= $form->select('USER_LOCALE', $locales) ?>
                            </div>
                            <?php
                        } ?>

                        <div class="form-group">
                            <button class="btn btn-primary"><?= t('Log in') ?></button>
                        </div>

                        <?php $token->output('oauth_login_' . $client->getClientKey()); ?>

                        <?php if (Config::get('concrete.user.registration.enabled')) {
                            ?>
                            <br/>
                            <hr/>
                            <div class="d-grid">
                                <a href="<?= URL::to('/register') ?>" class="btn btn-success" target="_blank">
                                    <?= t('Not a member? Register') ?>
                                </a>
                            </div>
                            <?php
                        } ?>

                        <?php
                    } elseif ($consentView) {
                        $consentView->addScopeItems($view->getScopeItems());
                        echo $consentView->render();
                    }
                    ?>

                </form>

            </div>
        </div>
    </div>
    <div style="clear:both"></div>

    <style type="text/css">
        body {
            background: url("<?= ASSETS_URL_IMAGES ?>/bg_login.png");
        }
        div.login-form hr {
            margin-top: 10px !important;
            margin-bottom: 5px !important;
        }

        ul.auth-types {
            margin: 20px 0px 0px 0px;
            padding: 0;
        }

        ul.auth-types > li > .fa,
        ul.auth-types > li svg,
        ul.auth-types > li .ccm-auth-type-icon {
            position: absolute;
            top: 2px;
            left: 0px;
        }

        ul.auth-types > li {
            list-style-type: none;
            cursor: pointer;
            padding-left: 25px;
            margin-bottom: 15px;
            transition: color .25s;
            position: relative;
        }

        ul.auth-types > li:hover {
            color: #cfcfcf;
        }

        ul.auth-types > li.active {
            font-weight: bold;
            cursor: auto;
        }
    </style>

    <script type="text/javascript">
        (function ($) {
            "use strict";

            var forms = $('div.controls').find('div.authentication-type').hide(),
                select = $('div.ccm-authentication-type-select > select');
            var types = $('ul.auth-types > li').each(function () {
                var me = $(this),
                    form = forms.filter('[data-handle="' + me.data('handle') + '"]');
                me.click(function () {
                    select.val(me.data('handle'));
                    if (typeof Concrete !== 'undefined') {
                        Concrete.event.fire('AuthenticationTypeSelected', me.data('handle'));
                    }

                    if (form.hasClass('active')) return;
                    types.removeClass('active');
                    me.addClass('active');
                    if (forms.filter('.active').length) {
                        forms.stop().filter('.active').removeClass('active').fadeOut(250, function () {
                            form.addClass('active').fadeIn(250);
                        });
                    } else {
                        form.addClass('active').show();
                    }
                });
            });
        })(jQuery);
    </script>
</div>
