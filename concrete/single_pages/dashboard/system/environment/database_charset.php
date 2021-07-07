<?php

use Concrete\Core\Error\ErrorList\Error\HtmlAwareErrorInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Page\View\PageView $view
 * @var array $charsetsAndCollations
 * @var string $collation
 * @var Concrete\Core\Error\ErrorList\ErrorList|null $set_connection_collation_warnings
 */
if (isset($set_connection_collation_warnings)) {
    ?>
    <div class="alert alert-warning alert-dismissible">
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <?= t('The character set and the collation of the connection and have been updated, but the following warnings occurred while updating the database tables'); ?>
        <ul>
        <?php
        foreach ($set_connection_collation_warnings->getList() as $warning) {
            ?>
            <li>
                <?php
                if ($warning instanceof HtmlAwareErrorInterface && $warning->messageContainsHtml()) {
                    echo $warning->getMessage();
                } else {
                    echo nl2br(h((string) $warning));
                } ?>
            </li>
            <?php
        } ?>
        </ul>
    </div>
    <?php
}
?>

<form method="POST" action="<?= $view->action('set_connection_collation'); ?>">

    <?php $token->output('set_connection_collation'); ?>

    <div class="form-group">
        <?= $form->label('collation', t('Collation')); ?>
        <div class="ccm-search-field-content">
            <?= $form->select('collation', $charsetsAndCollations, $collation, ['required' => 'required']); ?>
        </div>
    </div>

    <div class="alert alert-danger">
        <?= t('Warning: changing the character set may result in data loss!'); ?>
    </div>

    <div class="alert alert-info">
        <?= t('Changing the character set may require a lot of time. If the operation times out, you can re-apply the setting more times.'); ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary" type="submit" ><?= t('Save'); ?></button>
        </div>
    </div>

</form>

<script>
$(document).ready(function() {
    var submitted = false;
    $('#collation')
        .selectpicker()
        .closest('form')
            .on('submit', function(e) {
                if (submitted) {
                    e.preventDefault();
                    return;
                }

                if ($('#collation').val() !== <?= json_encode($collation); ?>) {
                    if (!window.confirm(<?= json_encode(t('Warning: changing the character set may result in data loss!') . "\n\n" . t('Are you sure you want to proceed?')); ?>)) {
                        e.preventDefault();
                        return;
                    }
                }

                submitted = true;
                setTimeout(
                    function() {
                        $(window).on('beforeunload', function() {
                            return true;
                        });
                    },
                    0
                );
            })
    ;
});
</script>
