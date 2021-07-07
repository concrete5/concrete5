<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\ExpressEntrySelector;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;

/** @var array $entities */
/** @var Key[] $expressAttributes */
/** @var Entry|null $entry */
/** @var Entity|null $entity */
/** @var string $exEntityID */
/** @var int $exSpecificEntryID */
/** @var string $exEntryAttributeKeyHandle */
/** @var string $exFormID */
/** @var string $entryMode */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var ExpressEntrySelector $expressEntrySelector */
$expressEntrySelector = $app->make(ExpressEntrySelector::class);

$exForms = [];

if (is_object($entity)) {
    foreach ($entity->getForms() as $form) {
        $exForms[$form->getID()] = $form->getName();
    }
}
?>

<div id="ccm-block-express-entry-detail-edit">
    <div class="form-group">
        <?php echo $form->label('entryMode', t('Entry')) ?>
        <?php echo $form->select('entryMode', [
            'E' => t('Get entry from list block on another page'),
            'S' => t('Display specific entry'),
            'A' => t('Get entry from custom attribute on this page'),
        ], $entryMode);
        ?>
    </div>

    <div class="form-group" data-container="express-entity">
        <?php echo $form->label('exEntityID', t('Entity')) ?>
        <?php echo $form->select('exEntityID', $entities, $exEntityID, [
            'data-action' => $view->action('load_entity_data')
        ]); ?>
    </div>

    <div class="form-group" data-container="express-entry-specific-entry">
        <?php if (is_object($entity)) { ?>
            <?php print $expressEntrySelector->selectEntry($entity, 'exSpecificEntryID', $entry); ?>
        <?php } else { ?>
            <p>
                <?php echo t('You must select an entity before you can choose a specific entry from it.') ?>
            </p>
        <?php } ?>
    </div>

    <div class="form-group" data-container="express-entry-custom-attribute">
        <?php echo $form->label('akID', t('Express Entry Attribute')) ?>

        <?php if (count($expressAttributes)) { ?>
            <!--suppress HtmlFormInputWithoutLabel -->
            <select name="exEntryAttributeKeyHandle" class="form-select">
                <option value="">
                    <?php echo t('** Select Attribute') ?>
                </option>

                <?php foreach ($expressAttributes as $ak) { ?>
                    <?php $settings = $ak->getAttributeKeySettings(); ?>

                    <option data-entity-id="<?php echo $settings->getEntity()->getID() ?>"
                            <?php if ($ak->getAttributeKeyHandle() == $exEntryAttributeKeyHandle) { ?>selected="selected" <?php } ?>
                            value="<?php echo h($ak->getAttributeKeyHandle()) ?>">
                        <?php echo $ak->getAttributeKeyDisplayName() ?>
                    </option>
                <?php } ?>
            </select>
        <?php } else { ?>
            <p><?php echo t('There are no express entity page attributes defined.') ?></p>
        <?php } ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('exFormID', t('Display Data in Entity Form')) ?>

        <div data-container="express-entry-detail-form">
            <?php if (is_object($entity)) { ?>
                <?php echo $form->select('exFormID', $exForms, $exFormID); ?>
            <?php } else { ?>
                <?php echo t('You must select an entity before you can choose its display form.') ?>
            <?php } ?>
        </div>
    </div>
</div>

<script type="text/template" data-template="express-attribute-form-list">
    <!--suppress HtmlFormInputWithoutLabel -->
    <select name="exFormID" class="form-select">
        <% _.each(forms, function(form) { %>
        <option value="<%=form.exFormID%>"
        <% if (exFormID == form.exFormID) { %>selected<% } %>><%=form.exFormName%></option>
        <% }); %>
    </select>
</script>

<script type="application/javascript">
    $(function(){
        Concrete.event.publish('block.express_entry_detail.open', {
            exFormID: '<?php echo $exFormID?>'
        });
    });
</script>
