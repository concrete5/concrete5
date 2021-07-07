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
?>

<div class="ccm-block-switch-language">

    <form method="post" class="row row-cols-auto g-0 align-items-center">
        <div class="col-auto">
            <?= $label ?>
        </div>
        <div class="col-auto">
            <?= $form->select(
                'language',
                $languages,
                $activeLanguage,
                [
                    'data-select' => 'multilingual-switch-language',
                    'data-action' => $view->action('switch_language', $cID, '--language--'),
                ]
            ) ?>
        </div>
    </form>

</div>
