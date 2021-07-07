<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Association\Formatter\FormatterInterface;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var FormatterInterface $formatter */
/** @var View $view */
/** @var Entity $entity */
/** @var Association $association */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);

?>

<div class="ccm-dashboard-header-buttons btn-group">
    <a href="<?php echo (string)Url::to('/dashboard/system/express/entities/associations', $entity->getID()) ?>"
       class="btn btn-secondary">
        <?php echo t("Back to Object") ?>
    </a>

    <a href="<?php echo (string)Url::to('/dashboard/system/express/entities/associations', 'edit', $association->getID()) ?>"
       class="btn btn-secondary">
        <?php echo t("Edit Details") ?>
    </a>

    <button type="button" class="btn btn-danger" data-action="delete-association">
        <?php echo t('Delete Association') ?>
    </button>
</div>

<div style="display: none">
    <div id="ccm-dialog-delete-association" class="ccm-ui">
        <form method="post" action="<?php echo $view->action('delete_association', $entity->getID()) ?>">
            <?php echo $token->output('delete_association') ?>
            <?php echo $form->hidden('association_id', $association->getID()) ?>

            <p>
                <?php echo t('Are you sure you want to delete this association? This cannot be undone.') ?>
            </p>

            <div class="dialog-buttons">
                <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()">
                    <?php echo t('Cancel') ?>
                </button>

                <button class="btn btn-danger float-end" onclick="$('#ccm-dialog-delete-association form').submit()">
                    <?php echo t('Delete Association') ?>
                </button>
            </div>
        </form>
    </div>
</div>

<!--suppress ES6ConvertVarToLetConst -->
<script type="text/javascript">
    $(function () {
        $('button[data-action=delete-association]').on('click', function () {
            var $element = $('#ccm-dialog-delete-association');
            jQuery.fn.dialog.open({
                element: $element,
                modal: true,
                width: 320,
                title: '<?php echo t('Delete Association')?>',
                height: 'auto'
            });
        });
    });
</script>

<h3>
    <?php echo $formatter->getDisplayName() ?>
</h3>

<h4>
    <?php echo t('Type') ?>
</h4>

<p>
    <?php echo $formatter->getTypeDisplayName() ?>
</p>

<h4>
    <?php echo t('Source Object') ?>
</h4>

<p>
    <a href="<?php echo (string)Url::to('/dashboard/system/express/entities', 'view_entity', $entity->getID()) ?>">
        <?php echo $entity->getEntityDisplayName() ?>
    </a>
</p>

<h4>
    <?php echo t('Inversed Property Name') ?>
</h4>

<p>
    <?php echo $association->getInversedByPropertyName() ?>
</p>

<h4>
    <?php echo t('Target Object') ?>
</h4>

<p>
    <a href="<?php echo (string)Url::to('/dashboard/system/express/entities', 'view_entity', $association->getTargetEntity()->getID()) ?>">
        <?php echo $association->getTargetEntity()->getEntityDisplayName() ?>
    </a>
</p>

<h4>
    <?php echo t('Target Property Name') ?>
</h4>

<p>
    <?php echo $association->getTargetPropertyName() ?>
</p>

<?php if ($association->isOwningAssociation()) { ?>
    <h4>
        <?php echo t('Association Type') ?>
    </h4>

    <p>
        <?php echo t('Owning') ?>
    </p>
<?php } else if ($association->isOwnedByAssociation()) { ?>
    <h4>
        <?php echo t('Association Type') ?>
    </h4>

    <p>
        <?php echo t('Owned By') ?>
    </p>
<?php } ?>
