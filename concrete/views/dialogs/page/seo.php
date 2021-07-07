<?php /** @noinspection DuplicatedCode */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Attribute\Context\ComposerContext;

use Concrete\Core\Form\Control\Renderer;
use Concrete\Core\Form\Control\View;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Form\Service\Form;
use Concrete\Controller\Dialog\Page\Seo;
use Concrete\Core\Entity\Attribute\Key\Key;

/** @var Page $c */
/** @var Seo $controller */
/** @var bool $allowEditName */
/** @var bool $allowEditPaths */
/** @var Key[] $attributes */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
?>

<section class="ccm-ui">
    <div class="col-sm-12">
        <header>
            <h3>
                <?php echo t('SEO') ?>
            </h3>
        </header>

        <form method="post" action="<?php echo h($controller->action('submit')) ?>"
              class="pt-4 ccm-panel-detail-content-form"
              data-dialog-form="seo" data-panel-detail-form="seo" data-action-after-save="reload">

            <?php if ($allowEditName) { ?>
                <div class="form-group">
                    <?php echo $form->label("cName", t('Name')); ?>
                    <?php echo $form->text("cName", $c->getCollectionName()); ?>
                </div>
            <?php } ?>

            <?php if ($allowEditPaths && !$c->isGeneratedCollection()) { ?>
                <div class="form-group">
                    <?php echo $form->label("cHandle", t('URL Slug'), [
                        "class" => "launch-tooltip form-label",
                        "title" => t('This page must always be available from at least one URL. This is that URL.')
                    ]); ?>
                    <?php echo $form->text("cHandle", $c->getCollectionHandle()); ?>
                    <?php echo $form->hidden("oldCHandle", $c->getCollectionHandle()); ?>
                </div>
            <?php } ?>

            <?php foreach ($attributes as $ak) {
                $av = $c->getAttributeValueObject($ak);
                /** @var View $view */
                $view = $ak->getControlView(new ComposerContext());
                /** @var Renderer $renderer */
                $renderer = $view->getControlRenderer();
                /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                $view->setValue($av);
                /** @noinspection PhpUnhandledExceptionInspection */
                $renderer->render();
            }

            if (isset($sitemap) && $sitemap) {
                echo $form->hidden('sitemap', 1);
            }
            ?>
        </form>

        <div class="ccm-panel-detail-form-actions dialog-buttons d-flex justify-content-end">
            <button class="btn btn-secondary float-start" type="button" data-dialog-action="cancel"
                    data-panel-detail-action="cancel">
                <?php echo t('Cancel') ?>
            </button>

            <button class="btn btn-success" type="button" data-dialog-action="submit"
                    data-panel-detail-action="submit">
                <?php echo t('Save Changes') ?>
            </button>
        </div>
    </div>
</section>

<!--suppress EqualityComparisonWithCoercionJS -->
<script type="text/javascript">
    $(function () {
        ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.saveSeo');
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess.saveSeo', function (e, data) {
            if (data.form == 'seo') {
                ConcreteToolbar.disableDirectExit();
                ConcreteEvent.publish('SitemapUpdatePageRequestComplete', {'cID': data.response.cID});
            }
        });
        $('#ccm-panel-detail-page-seo .form-control').textcounter({
            type: "character",
            max: -1,
            countSpaces: true,
            stopInputAtMaximum: false,
            counterText: '<?php echo t('Characters'); ?>: %d',
            countContainerClass: 'form-text text-muted'
        });
    });
</script>
