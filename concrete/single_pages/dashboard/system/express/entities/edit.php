<?php /** @noinspection PhpDeprecationInspection */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Element\Dashboard\Express\Menu;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Type\ExpressEntryResults;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var ExpressEntryResults $tree */
/** @var Node $resultsParentFolder */
/** @var bool $isMultisiteEnabled */
/** @var int $defaultEditFormID */
/** @var int $defaultViewFormID */
/** @var int $ownedByID */
/** @var array $forms  */
/** @var Entity $entity */
/** @var string $pageTitle */

$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);
/** @var Form $form */
$form = $app->make(Form::class);
?>

<div class="ccm-dashboard-header-buttons">
    <?php
    $manage = new Menu($entity);
    $manage->render();
    ?>
</div>

<div class="row">
    <?php /** @noinspection PhpUnhandledExceptionInspection */
    View::element('dashboard/express/detail_navigation', ['entity' => $entity]) ?>
    <div class="col-md-8">

        <form method="post" action="<?php echo $view->action('update', $entity->getID()) ?>">
            <?php echo $token->output('update_entity') ?>

            <fieldset>
                <legend>
                    <?php echo t("Basics") ?>
                </legend>

                <div class="form-group">
                    <?php echo $form->label("name", t('Name')); ?>

                    <div class="float-end">
                        <span class="text-muted small">
                            <?php echo t('Required')?>
                        </span>
                    </div>

                    <?php echo $form->text('name', $entity->getName()) ?>
                </div>

                <div class="form-group">
                    <?php echo $form->label("handle", t('Handle')); ?>

                    <div class="float-end">
                        <span class="text-muted small">
                            <?php echo t('Required')?>
                        </span>
                    </div>

                    <?php echo $form->text('handle', $entity->getHandle()) ?>
                </div>

                <div class="form-group">
                    <?php echo $form->label("plural_handle", t('Plural Handle')); ?>

                    <div class="float-end">
                        <span class="text-muted small">
                            <?php echo t('Required')?>
                        </span>
                    </div>

                    <?php echo $form->text('plural_handle', $entity->getPluralHandle()) ?>
                </div>

                <div class="form-group">
                    <?php echo $form->label("label_mask", t('Name Mask')); ?>
                    <?php echo $form->text('label_mask', $entity->getLabelMask()) ?>

                    <p class="help-block">
                        <?php echo t('Example <code>Entry %name%</code> or <code>Complaint %date% at %hotel%</code>') ?>
                    </p>
                </div>

                <div class="form-group">
                    <?php echo $form->label("description", t('Description')); ?>
                    <?php echo $form->textarea('description', $entity->getEntityDisplayDescription(), ['rows' => 5]) ?>
                </div>
            </fieldset>

            <fieldset>
                <legend>
                    <?php echo t('Advanced') ?>
                </legend>

                <div class="form-group">
                    <?php echo $form->label("supports_custom_display_order", t('Custom Display Order')); ?>

                    <div class="form-check">
                        <?php echo $form->checkbox('supports_custom_display_order', 1, $entity->supportsCustomDisplayOrder()) ?>
                        <?php echo $form->label("supports_custom_display_order", t('This entity supports custom display ordering via Dashboard interfaces.'), ["class" => "form-check-label"]) ?>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>
                    <?php echo t('Views') ?>
                </legend>

                <div class="form-group">
                    <?php echo $form->label("default_edit_form_id", t('Default Edit Form')); ?>
                    <?php echo $form->select('default_edit_form_id', $forms, $defaultEditFormID) ?>
                </div>

                <div class="form-group">
                    <?php echo $form->label("default_view_form_id", t('Default View Form')); ?>
                    <?php echo $form->select('default_view_form_id', $forms, $defaultViewFormID) ?>
                </div>
            </fieldset>

            <fieldset>
                <legend>
                    <?php echo t('Results') ?>
                </legend>

                <?php echo $form->hidden("entity_results_parent_node_id", $resultsParentFolder->getTreeNodeID()); ?>

                <?php if ($isMultisiteEnabled) { ?>
                    <div class="form-group">
                        <?php echo $form->label("use_separate_site_result_buckets", t('Share results across all sites')); ?>

                        <div class="form-check">
                            <?php echo $form->radio('use_separate_site_result_buckets', false, $entity->usesSeparateSiteResultsBuckets(), ["class" => "form-check-input", "id" => "use_separate_site_result_buckets1"]) ?>
                            <?php echo $form->label('use_separate_site_result_buckets1', t('Disabled - results are shared across all sites.'), ["form-check-label"]); ?>
                        </div>

                        <div class="form-check">
                            <?php echo $form->radio('use_separate_site_result_buckets', true, $entity->usesSeparateSiteResultsBuckets(), ["class" => "form-check-input", "id" => "use_separate_site_result_buckets2"]) ?>
                            <?php echo $form->label('use_separate_site_result_buckets2', t('Enabled - results are split and not shared between sites.'), ["form-check-label"]); ?>
                        </div>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <?php echo $form->label("", t('Folder Location')); ?>

                    <div data-tree="<?php echo $tree->getTreeID() ?>"></div>

                    <!--suppress ES6ConvertVarToLetConst, JSDuplicatedDeclaration -->
                    <script type="text/javascript">
                        $(function () {

                            $('[data-tree]').concreteTree({
                                treeID: '<?php echo $tree->getTreeID()?>',
                                ajaxData: {
                                    displayOnly: 'express_entry_category'
                                },
                                <?php if (is_object($resultsParentFolder)) { ?>
                                selectNodesByKey: [<?php echo $resultsParentFolder->getTreeNodeID()?>],
                                onSelect: function (nodes) {
                                    if (nodes.length) {
                                        $('input[name=entity_results_parent_node_id]').val(nodes[0]);
                                    } else {
                                        $('input[name=entity_results_parent_node_id]').val('');
                                    }
                                },
                                <?php } ?>
                                'chooseNodeInForm': 'single'
                            });

                            $('[data-dialog]').on('click', function () {
                                var $element = $('#ccm-dialog-' + $(this).attr('data-dialog'));
                                if ($(this).attr('data-dialog-title')) {
                                    var title = $(this).attr('data-dialog-title');
                                } else {
                                    var title = $(this).text();
                                }
                                jQuery.fn.dialog.open({
                                    element: $element,
                                    modal: true,
                                    width: 320,
                                    title: title,
                                    height: 'auto'
                                });
                            });
                        });
                    </script>
                </div>
            </fieldset>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <button type="button" data-dialog="delete-entity" class="float-start btn btn-danger">
                        <?php echo t('Delete') ?>
                    </button>

                    <button class="float-end btn btn-primary" type="submit">
                        <?php echo t('Save') ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div style="display: none">
    <div id="ccm-dialog-delete-entity" class="ccm-ui">
        <form method="post" action="<?php echo $view->action('delete') ?>">
            <?php echo $token->output('delete_entity') ?>
            <?php echo $form->hidden("entity_id", $entity->getID()); ?>

            <p>
                <?php echo t('Are you sure you want to delete this entity? All data entries and all its associations to other entities will be removed. This cannot be undone.') ?>
            </p>

            <div class="dialog-buttons">
                <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()">
                    <?php echo t('Cancel') ?>
                </button>

                <button class="btn btn-danger float-end" onclick="$('#ccm-dialog-delete-entity form').submit()">
                    <?php echo t('Delete Entity') ?>
                </button>
            </div>
        </form>
    </div>
</div>
