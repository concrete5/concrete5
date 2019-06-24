<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php $entrySelector = \Concrete\Core\Support\Facade\Application::getFacadeApplication()->make('form/express/entry_selector'); ?>
<div class="form-group">
    <?php if ($view->supportsLabel()) {
    ?>
        <label class="control-label" for="<?=$view->getControlID(); ?>"><?=$label; ?></label>
    <?php
} ?>

    <?php
    $selectedEntity = null;
    if (!empty($selectedEntities)) {
        $selectedEntity = $selectedEntities[0];
    }
    echo $entrySelector->selectEntry($control->getAssociation()->getTargetEntity(), 'express_association_' . $control->getId(), $selectedEntity);
    ?>
</div>
