<?php

use Concrete\Core\Attribute\Key\Key;

defined('C5_EXECUTE') or die('Access denied.');

$form = Loader::helper('form');

if (isset($authType) && $authType) {
    $active = $authType;
    $activeAuths = [$authType];
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

/* @var Key[] $required_attributes */

$attribute_mode = (isset($required_attributes) && count($required_attributes));

// See if we have a user object and if that user is registered
$loggedIn = !$attribute_mode && isset($user) && $user->isRegistered();

// Determine login header title
$title = t('Please sign in here.');
$alreadyLoggedInMessage = t('You are already logged in.');
if ($attribute_mode) {
    $title = t('Required Attributes');
}

if ($loggedIn) {
    $title = $alreadyLoggedInMessage;
}
?>
<?php
// Check for custom background image
if (Config::get('concrete.white_label.background_image') !== 'none' && !Config::get('concrete.white_label.background_url')) {
    //Uncomment below two lines if you want picture of the day to load
    //$image = date('Ymd') . '.jpg';
    //$imagePath = Config::get('concrete.urls.background_feed') . '/' . $image;
} elseif (Config::get('concrete.white_label.background_url')) {
    $imagePath = Config::get('concrete.white_label.background_url');
}
?>
<style type="text/css">
html,body {
    height: 100%;
    background-color: #94959A;
}
<?php
if (!empty($imagePath)) {
    ?>
    body {
        background-image: url('<?=$imagePath; ?>');
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
    }
<?php
}?>
</style>

<div class="login-page">
    <div class="container">
        <div class="login-page-header">
            <?php
            $disclaimer = new Area('Disclaimer');
            if ($disclaimer->getTotalBlocksInArea($c) || $c->isEditMode()) {
                ?>
                <div class="row login-page-disclaimer-area">
                    <div class="col-12">
                        <?= $disclaimer->display($c); ?>
                    </div>
                </div>
            <?php
            } ?>
            <div class="row">
                <div class="col-12">
                    <h2 class="login-page-title">
                        <?php if (!$attribute_mode) {
                ?>
                            <?=t('Welcome back!'); ?><br>
                        <?php
            }?>
                        <?= $title; ?>
                    </h2>
                </div>
            </div>
        </div>

        <?php if ($attribute_mode) {
                $attribute_helper = new Concrete\Core\Form\Service\Widget\Attribute(); ?>
            <div class="row login-page-content attribute-mode">
                <form action="<?= View::action('fill_attributes'); ?>" method="POST">
                    <div data-handle="required_attributes"
                    class="authentication-type authentication-type-required-attributes">
                    <div class="ccm-required-attribute-form">
                        <?php
                        foreach ($required_attributes as $key) {
                            echo $attribute_helper->display($key, true);
                        } ?>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary pull-right"><?= t('Submit'); ?></button>
                    </div>

                </div>
            </form>
        </div>
        <?php
            } else {
                ?>
        <div class="row gx-5 login-page-content">
            <div class="col-12">
                <?php if ($loggedIn) {
                    ?>
                    <div class="text-center">
                        <h3><?=$alreadyLoggedInMessage; ?></h3>
                        <?= Core::make('helper/navigation')->getLogInOutLink(); ?>
                    </div>
                <?php
                } else {
                    $i = 0;
                    foreach ($activeAuths as $auth) {
                        ?>
                        <div data-handle="<?= $auth->getAuthenticationTypeHandle(); ?>" class="authentication-type authentication-type-<?= $auth->getAuthenticationTypeHandle(); ?>">
                            <?php $auth->renderForm($authTypeElement ?: 'form', $authTypeParams ?: []); ?>
                        </div>
                        <?php
                        if ($i == 0 && count($activeAuths) > 1 && Config::get('concrete.user.registration.enabled')) {
                            echo '<div class="text-center" style="margin-bottom: 5px;">';
                            echo t('or');
                            echo '</div>';
                        } elseif ($i == 0 && count($activeAuths) > 1) {
                            echo '<hr>';
                        }
                        ++$i;
                    }
                } ?>
            </div>
        </div>
        <?php
            } // END OPENING IF STATEMENT
    ?>
    </div>
</div>
