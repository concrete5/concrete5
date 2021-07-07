<?php defined('C5_EXECUTE') or die("Access Denied.");?>

<form method="post" action="<?=$view->action('submit')?>">
    <?=$token->output('submit')?>
    <p class="lead"><?=t('Click below to reset clipboard and edit mode for the entire site. Any users actively editing a page will be forced out of edit mode.')?></p>
    <div class="d-grid">
        <button type="submit" class="btn btn-primary"><?=t('Reset Clipboard and Edit Mode')?></button>
    </div>

</form>
