<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-pane-body">
<fieldset>
    <legend><?=t('Frequently Used'); ?></legend>
    <ul class="item-select-list" data-sort="frequently-used">
    <?php foreach ($frequent as $pt) {
    ?>
        <li data-page-type-id="<?=$pt->getPageTypeID(); ?>"><span><?=$pt->getPageTypeDisplayName(); ?> <i class="fas fa-arrows-alt-v ccm-item-select-list-sort"></i></span></li>
    <?php
} ?>
    </ul>
</fieldset>

<fieldset>
    <legend><?=t('Others'); ?></legend>
    <ul class="item-select-list" data-sort="other">
        <?php foreach ($infrequent as $pt) {
        ?>
            <li data-page-type-id="<?=$pt->getPageTypeID(); ?>"><span><?=$pt->getPageTypeDisplayName(); ?> <i class="fas fa-arrows-alt-v ccm-item-select-list-sort"></i></span></li>
        <?php
    } ?>
    </ul>
</fieldset>
</div>
<div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <a href="<?=URL::to('/dashboard/pages/types', $siteTypeID); ?>" class="btn float-start btn-secondary"><?=t('Back to Page Types'); ?></a>
        <button class="float-end btn btn-primary" type="button" data-submit="save"><?=t('Save Ordering'); ?></button>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('ul[data-sort]').sortable({
            connectWith: 'ul[data-sort]'
        });

        $('button[data-submit=save]').on('click', function() {
           var frequent = [],
               infrequent = [];

            $('ul[data-sort=frequently-used] li').each(function() {
                frequent.push($(this).attr('data-page-type-id'));
            });

            $('ul[data-sort=other] li').each(function() {
                infrequent.push($(this).attr('data-page-type-id'));
            });

            $.concreteAjax({
               url: '<?=$view->action('submit'); ?>',
               data: {
                   ccm_token: '<?= $token->generate('submit'); ?>',
                   frequent: frequent,
                   infrequent: infrequent
               },
                success: function(r) {
                    ConcreteAlert.notify({
                        'message': r.message,
                        'title': r.title
                    });
                }
            });
        });
    });
</script>
