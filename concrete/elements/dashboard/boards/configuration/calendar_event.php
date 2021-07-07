<?php
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $topicsKeyField \Concrete\Core\Search\Field\AttributeKeyField
 */
?>

<div class="form-group">
    <?=$form->label('calendarID', t('Calendar'))?>
    <?=$form->select('calendarID', $calendars, $calendarID);?>
</div>

<div class="form-group">
    <?=$form->label('maxOccurrencesOfSameEvent', t('Maximum Occurrences of Same Event'))?>
    <?=$form->number('maxOccurrencesOfSameEvent', (int) $maxOccurrencesOfSameEvent);?>
</div>


<h3 class="fw-light"><?=t('Filters')?></h3>
<p><small class="text-muted"><?=t("Add search fields below to limit the pages added.")?></small></p>
<?php
$fieldSelector->render();
?>
