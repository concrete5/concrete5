<?php defined('C5_EXECUTE') or die("Access Denied.");

/* @var $form \Concrete\Core\Form\Service\Form */

?>
<div class="form-group">
    <?= $form->label('url', t('Feed URL')) ?>
    <input name="url" class="form-control" placeholder="<?= h(t('Feed URL')) ?>" value="<?= h($rssObj->url) ?>" type="text" required="required" />
</div>
<div class="form-group">
    <?php echo $form->label("title", t('Feed Title')); ?>
    <div class="input-group">
    	<input name="title" class="form-control" placeholder="<?= h(t('Feed Title')) ?>" value="<?= h($rssObj->title) ?>"/>
		<?php echo $form->select('titleFormat', \Concrete\Core\Block\BlockController::$btTitleFormats, $titleFormat, array('style' => 'width:105px;flex-grow:0;', 'class' => 'form-select')); ?>
	</div>
</div>
<div class="form-group">
    <?= $form->label('standardDateFormat', t('Date Format')) ?>
    <?php
    $dateFormats = $rssObj->getDefaultDateTimeFormats();
    $dateFormats[':custom:'] = t('Custom date/time format');
    $standardDateFormat = $rssObj->dateFormat;
    $customDateFormat = '';
    if (!$standardDateFormat) {
        reset($dateFormats);
        $standardDateFormat = key($dateFormats);
    }
    if (!array_key_exists($standardDateFormat, $dateFormats)) {
        $customDateFormat = $standardDateFormat;
        $standardDateFormat = ':custom:';
    }
    echo $form->select('standardDateFormat', $dateFormats, $standardDateFormat);
    ?>
</div>
<div class="form-group"<?php echo ($standardDateFormat === ':custom:') ? '' : ' style="display: none"'; ?>>
    <?php echo $form->label('customDateFormat', t('Custom Date Format')); ?>
    <?php echo $form->text('customDateFormat', $customDateFormat); ?>
    <div class="help-block"><?php echo sprintf(t('See the formatting options for date at %s.'), '<a href="http://www.php.net/date" target="_blank">php.net/date</a>'); ?></div>
</div>
<script>
$(document).ready(function() {
	function update() {
		$('#customDateFormat').closest('div.form-group')[($('#standardDateFormat').val() === ':custom:') ? 'show' : 'hide']('fast');
	}
	$('#standardDateFormat').on('change', function() { update(); });
});
</script>
<div class="form-group">
    <?= $form->label('itemsToDisplay', t('Items to Show')) ?>
    <input name="itemsToDisplay" class="form-control" placeholder="10" value="<?= h($rssObj->itemsToDisplay) ?>"/>
</div>
<div class="form-group">
    <div class="form-check">
        <input type="checkbox" value="1" id="showSummary" class="form-check-input" name="showSummary"<?= ((bool) $rssObj->showSummary ? ' checked' : '') ?> />
        <label for="showSummary" class="form-check-label">
            <?= t('Include Summary') ?>
        </label>
    </div>
</div>
<div class="form-group">
    <div class="form-check">
        <input type="checkbox" class="form-check-input" value="1" name="launchInNewWindow"<?= ((bool) $rssObj->launchInNewWindow ? ' checked' : '') ?> />
        <label for="launchInNewWindow" class="form-check-label">
            <?= t('Open links in a new window') ?>
        </label>
    </div>
</div>
