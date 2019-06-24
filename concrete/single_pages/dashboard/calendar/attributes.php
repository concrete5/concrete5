<?php defined('C5_EXECUTE') or die('Access Denied.');?>

<?php
if ($controller->getTask() == 'view') {
    ?>

    <div class="alert alert-info">
        <?=t('Event attributes must be added to an attribute set. The attribute set name will be displayed as a tab in the add/edit event modal.')?>
    </div>

<?php } ?>

<?php
$attributeView->render();
?>