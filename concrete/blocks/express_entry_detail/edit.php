<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div id="ccm-block-express-entry-detail-edit">

    <div class="form-group">
        <?=$form->label('entryMode', t('Entry'))?>
        <?=$form->select('entryMode', [
            'E' => t('Get entry from list block on another page'),
            'S' => t('Display specific entry'),
            'A' => t('Get entry from custom attribute on this page'),
            ], $entryMode);
        ?>
    </div>

    <div class="form-group" data-container="express-entity">
        <?=$form->label('exEntityID', t('Entity'))?>
        <?=$form->select('exEntityID', $entities, $exEntityID, [
            'data-action' => $view->action('load_entity_data')
        ]);?>
    </div>


    <div class="form-group" data-container="express-entry-specific-entry">
        <?php if (is_object($entity)) {
            $form_selector = \Core::make('form/express/entry_selector');
            print $form_selector->selectEntry($entity, 'exSpecificEntryID', $entry);
        } else { ?>
            <p><?=t('You must select an entity before you can choose a specific entry from it.')?></p>
        <?php } ?>
    </div>

    <div class="form-group" data-container="express-entry-custom-attribute">
        <?=$form->label('akID', t('Express Entry Attribute'))?>
        <?php if (count($expressAttributes)) { ?>
        <select name="exEntryAttributeKeyHandle" class="form-control">
            <option value=""><?=t('** Select Attribute')?></option>
            <?php foreach($expressAttributes as $ak) {
                $settings = $ak->getAttributeKeySettings();
                ?>
                <option data-entity-id="<?=$settings->getEntity()->getID()?>" <?php if ($ak->getAttributeKeyHandle() == $exEntryAttributeKeyHandle) { ?>selected="selected" <?php } ?> value="<?=$ak->getAttributeKeyHandle()?>"><?=$ak->getAttributeKeyDisplayName()?></option>
            <?php } ?>
        </select>
        <?php } else { ?>
            <p><?=t('There are no express entity page attributes defined.')?></p>
        <?php } ?>
    </div>

    <div class="form-group">
        <?=$form->label('exFormID', t('Display Data in Entity Form'))?>
        <div data-container="express-entry-detail-form">
            <?php if (is_object($entity)) { ?>
                <select name="exFormID" class="form-control">
                    <?php foreach($entity->getForms() as $form) { ?>
                        <option <?php if ($exFormID == $form->getID()) { ?>selected="selected" <?php } ?> value="<?=$form->getID()?>"><?=h($form->getName())?></option>
                    <?php } ?>
                </select>
            <?php } else { ?>
                <?=t('You must select an entity before you can choose its display form.')?>
            <?php } ?>
        </div>

    </div>


</div>

<script type="text/template" data-template="express-attribute-form-list">
    <select name="exFormID" class="form-control">
    <% _.each(forms, function(form) { %>
        <option value="<%=form.exFormID%>" <% if (exFormID == form.exFormID) { %>selected<% } %>><%=form.exFormName%></option>
    <% }); %>
    </select>
</script>



<script type="application/javascript">
    Concrete.event.publish('block.express_entry_detail.open', {
        exFormID: '<?=$exFormID?>'
    });
</script>