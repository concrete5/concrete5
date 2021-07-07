<?php

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Controller\Dialog\Page\DeleteAlias $controller */
/* @var Concrete\Core\View\DialogView $view */
/* @var Concrete\Core\Page\Page $c */

?>
<div class="ccm-ui">

    <form class="form-stacked" data-dialog-form="delete-alias" method="post"
          action="<?php echo h($controller->action('submit')) ?>">

        <p>
            <?php echo $c->isExternalLink() ? t('Remove this external link?') : t('Remove this alias?') ?>
        </p>

        <div class="dialog-buttons">
            <button class="btn btn-secondary float-start" data-dialog-action="cancel">
                <?php echo t('Cancel') ?>
            </button>

            <button type="button" data-dialog-action="submit"
                    class="btn btn-danger float-end">
                <?php echo t('Delete') ?>
            </button>
        </div>
    </form>

    <!--suppress EqualityComparisonWithCoercionJS -->
    <script type="text/javascript">
        $(function () {
            ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.sitemapDelete');
            ConcreteEvent.subscribe('AjaxFormSubmitSuccess.sitemapDelete', function (e, data) {
                if (data.form == 'delete-alias') {
                    ConcreteEvent.publish('SitemapDeleteRequestComplete', {'cID': '<?php echo $c->getCollectionID()?>'});
                }
            });
        });
    </script>
</div>
