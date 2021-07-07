<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Block\View\BlockView $view */

// The text label as configured by the user
/* @var string $label */

// Array keys are the multilingual section IDs, array values are the language names (in their own language)
/* @var array $languages */

// The list of multilanguage language sections
/* @var Concrete\Core\Multilingual\Page\Section\Section[] $languageSections */

// The ID of the currently active multilingual section (if available)
/* @var int|null $activeLanguage */

// The default multilingual section
/* @var Concrete\Core\Multilingual\Page\Section\Section $defaultLocale */

// The current language code (without Country code)
/* @var string $locale */

// The ID of the current page
/* @var int $cID */
$ih = Core::make('multilingual/interface/flag');
?>

<div class="ccm-block-language-list-set-default-wrapper">
    <form method="post" action="<?= $view->action('set_current_language') ?>" class="form-stacked">
        <?php
        if (isset($_REQUEST['rcID']) && Core::make('helper/validation/numbers')->integer($_REQUEST['rcID'])) {
            ?>
            <input type="hidden" name="rcID" value="<?= $_REQUEST['rcID'] ?>" />
            <?php
        }
        ?>
        <div class="form-group">
            <label class="control-label form-label"><?= $label ?></label>
            <?php
            $i = 1;
            foreach ($languageSections as $ml) {
                ?>
                <div class="form-check">
                        <input type="radio" name="language" class="form-check-input" id="language<?=$i?>" value="<?= $ml->getCollectionID() ?>" <?php if (is_object($defaultLocale) && $defaultLocale->getCollectionID() == $ml->getCollectionID()) { ?> checked="checked" <?php } ?> />
                        <label class="form-check-label" for="language<?=$i?>">
                            <?= $ih->getSectionFlagIcon($ml) ?>
                            <?= $languages[$ml->getCollectionID()] ?>
                        </label>
                </div>
                <?php
                $i++;
            }
            ?>
        </div>
        <div class="form-group">
            <div class="form-check">

                    <?= $form->checkbox('remember', 1, 1) ?>

                    <?=$form->label('remember', t('Remember my choice on this computer.')) ?>

            </div>
        </div>
        <button class="btn btn-primary" type="submit"><?= t('Save') ?></button>
    </form>
</div>
