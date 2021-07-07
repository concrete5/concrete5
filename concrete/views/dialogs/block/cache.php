<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Block\Block;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

$app = Application::getFacadeApplication();

/** @var int $btCacheBlockOutputLifetime */
/** @var bool $btCacheBlockOutputOnPost */
/** @var bool $btCacheBlockOutputForRegisteredUsers */
/** @var bool $btCacheBlockOutput */
/** @var bool $cbOverrideBlockTypeCacheSettings */
/** @var string $bName */
/** @var Block $b */

/* @var $dh Date */
$dh = $app->make(Date::class);
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Repository $config */
$config = $app->make(Repository::class);

$permissionChecker = new Checker($b);
$responseObject = $permissionChecker->getResponseObject();

?>

<div class="ccm-ui">
    <form method="post" data-dialog-form="block-cache" action="<?php echo $controller->action('submit') ?>">
        <?php /** @noinspection PhpUnhandledExceptionInspection */
        if ($responseObject->validate("edit_block_name")) { ?>
            <fieldset>
                <legend>
                    <?php echo t('Name') ?>
                </legend>

                <div class="form-group">
                    <label class="control-label form-label" for="bName">
                        <?php echo t('Block Name') ?>

                        <i class="fa fa-question-circle launch-tooltip"
                           title="<?php echo h(t('This can be useful when working with a block programmatically. This is rarely used or needed.')) ?>"></i>
                    </label>

                    <?php echo $form->text('bName', $bName); ?>
                </div>
            </fieldset>
        <?php } ?>

        <?php /** @noinspection PhpUnhandledExceptionInspection */
        if ($responseObject->validate("edit_block_cache_settings")) { ?>
            <fieldset>
                <legend>
                    <?php echo t('Caching') ?>
                </legend>

                <?php if (!$config->get('concrete.cache.blocks')) { ?>
                    <div class="alert alert-warning">
                        <?php echo t('Block caching is currently disabled globally. These settings won\'t take affect until block caching is turned on. You can turn these settings on from the %s page in the Dashboard.', '<a href="' . (string)Url::to('/dashboard/system/optimization/cache') . '">' . t('Cache and Speed Settings') . '</a>') ?>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <?php echo $form->label('cbOverrideBlockTypeCacheSettings', t('Override Block Type')); ?>

                    <div class="form-check">
                        <?php echo $form->checkbox('cbOverrideBlockTypeCacheSettings', 1, $cbOverrideBlockTypeCacheSettings) ?>
                        <?php echo $form->label("cbOverrideBlockTypeCacheSettings", t('Override block type cache settings.'), ["class" => "form-check-label"]) ?>
                    </div>
                </div>

                <div class="form-group" data-settings="block-cache">
                    <?php echo $form->label('btCacheBlockOutput', t('Settings')); ?>

                    <div class="form-check">
                        <?php echo $form->checkbox('btCacheBlockOutput', 1, $btCacheBlockOutput) ?>
                        <?php echo $form->label("btCacheBlockOutput", t('Cache block output.'), ["class" => "form-check-label"]) ?>
                    </div>

                    <div class="form-check">
                        <?php echo $form->checkbox('btCacheBlockOutputForRegisteredUsers', 1, $btCacheBlockOutputForRegisteredUsers) ?>
                        <?php echo $form->label("btCacheBlockOutputForRegisteredUsers", t('Cache block output even for logged in users.'), ["class" => "form-check-label"]) ?>
                    </div>

                    <div class="form-check">
                        <?php echo $form->checkbox('btCacheBlockOutputOnPost', 1, $btCacheBlockOutputOnPost) ?>
                        <?php echo $form->label("btCacheBlockOutputOnPost", t('Cache block output on HTTP POST.'), ["class" => "form-check-label"]) ?>
                    </div>
                </div>

                <div class="form-group" data-settings="block-cache">
                    <?php echo $form->label('btCacheBlockOutputLifetime', t('Cache for how long (in seconds)?')); ?>
                    <?php echo $form->text('btCacheBlockOutputLifetime', $btCacheBlockOutputLifetime); ?>
                </div>
            </fieldset>
        <?php } ?>

        <div class="dialog-buttons">
            <button class="btn btn-secondary float-start" data-dialog-action="cancel">
                <?php echo t('Cancel') ?>
            </button>

            <button type="button" data-dialog-action="submit"
                    class="btn btn-primary float-end">
                <?php echo t('Save') ?>
            </button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(function () {
        $('input[name=cbOverrideBlockTypeCacheSettings]').on('change', function () {
            if ($(this).is(':checked')) {
                $('div[data-settings=block-cache]').show();
            } else {
                $('div[data-settings=block-cache]').hide();
            }
        }).trigger('change');
    });
</script>
