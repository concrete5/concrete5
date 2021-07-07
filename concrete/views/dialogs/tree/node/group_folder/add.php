<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Dialog\Tree\Node\GroupFolder\Add;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Validation\CSRF\Token;

/** @var Add $controller */
/** @var Node $node */
/** @var array $containsList */
/** @var array $allGroupTypes */
/** @var array $selectedGroupTypeIds */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);
?>

<div class="ccm-ui">
    <form method="post" data-dialog-form="add-group-folder-node" class="form-horizontal"
          action="<?php echo $controller->action('add_group_folder_node') ?>">
        <?php echo $token->output('add_group_folder_node') ?>
        <?php echo $form->hidden("treeNodeID", $node->getTreeNodeID()); ?>

        <div class="form-group">
            <?php echo $form->label('treeNodeGroupFolderName', t('Name')) ?>
            <?php echo $form->text('treeNodeGroupFolderName') ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('contains', t('Contains')) ?>
            <?php echo $form->select('contains', $containsList, \Concrete\Core\Tree\Node\Type\GroupFolder::CONTAINS_GROUP_FOLDERS_AND_GROUPS) ?>
        </div>

        <div id="ccm-group-types">
            <div class="form-group">
                <?php echo $form->label('', t('Group Types')) ?>

                <?php foreach ($allGroupTypes as $groupTypeId => $groupTypeName) { ?>
                    <div class="form-check">
                        <?php echo $form->checkbox('groupTypes[' . $groupTypeId . ']', $groupTypeId, false, ["class" => "form-check-input", "id" => "ccm-group-type-" . $groupTypeId]) ?>
                        <?php echo $form->label("ccm-group-type-" . $groupTypeId, $groupTypeName, ["class" => "form-check-label"]) ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="dialog-buttons">
            <button class="btn btn-secondary" data-dialog-action="cancel">
                <?php echo t('Cancel') ?>
            </button>

            <button class="btn btn-primary float-end" data-dialog-action="submit" type="submit">
                <?php echo t('Add') ?>
            </button>
        </div>
    </form>

    <script type="text/javascript">
        $(function () {
            _.defer(function () {
                $('input[name=treeNodegroup_folderName]').focus();
            });
            ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.addTreeNode');
            ConcreteEvent.subscribe('AjaxFormSubmitSuccess.addTreeNode', function (e, data) {
                if (data.form == 'add-group-folder-node') {
                    ConcreteEvent.publish('ConcreteTreeAddTreeNode', {'node': data.response});
                }
            });

            $("#contains").change(function () {
                if ($(this).val() == 2) {
                    $("#ccm-group-types").removeClass("d-none");
                } else {
                    $("#ccm-group-types").addClass("d-none");
                }
            }).trigger("change");
        });
    </script>
</div>
