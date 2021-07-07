<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Url;

/** @var string $headerSearchAction */
/** @var Form $form */
?>

<div class="ccm-header-search-form ccm-ui" data-header="page-manager">
    <form method="get" class="row row-cols-auto g-0 align-items-center" action="<?php echo $headerSearchAction ?>">

        <div class="ccm-header-search-form-input input-group">
            <?php if (isset($query)): ?>
                <a href="javascript:void(0);"
                   data-launch-dialog="advanced-search"
                   class="ccm-header-launch-advanced-search"
                   data-advanced-search-dialog-url="<?php echo Url::to('/ccm/system/dialogs/page/advanced_search') ?>"
                   data-advanced-search-query="advanced-search-query">

                    <?php echo t('Advanced') ?>

                    <script type="text/concrete-query" data-query="advanced-search-query">
                        <?php echo $query ?>
                    </script>
                </a>
            <?php else: ?>
                <a href="javascript:void(0);"
                   data-launch-dialog="advanced-search"
                   class="ccm-header-launch-advanced-search"
                   data-advanced-search-dialog-url="<?php echo Url::to('/ccm/system/dialogs/page/advanced_search') ?>">

                    <?php echo t('Advanced') ?>
                </a>
            <?php endif; ?>

            <?php
                echo $form->search('keywords', [
                    'placeholder' => t('Search'),
                    'class' => 'border-right-0',
                    'autocomplete' => 'off'
                ]);
            ?>

            <button type="submit" class="input-group-icon">
                <svg width="16" height="16">
                    <use xlink:href="#icon-search"/>
                </svg>
            </button>
        </div>
    </form>
</div>

<script>
    (function ($) {
        $(function () {
            ConcreteEvent.subscribe('SavedSearchCreated', function () {
                window.location.reload();
            });
            
            ConcreteEvent.subscribe('SavedPresetSubmit', function (e, url) {
                window.location.href = url;
            });
        });
    })(jQuery);
</script>
