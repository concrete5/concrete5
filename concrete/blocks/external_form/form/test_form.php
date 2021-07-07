<?php
$form = Loader::helper('form');
defined('C5_EXECUTE') or die("Access Denied.");
if (isset($response)) {
    ?>
	<div class="alert alert-info"><?=$response?></div>
<?php 
} ?>


<form method="post" action="<?=$view->action('test_search')?>">

    <p><?=$message?></p>

    <div class="form-group">
        <label class="control-label form-label"><?=t('Test')?></label>
        <?=$form->text('test_text_field')?>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="submit" class="btn btn-default" />
    </div>

</form>
