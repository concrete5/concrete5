<?php defined('C5_EXECUTE') or die('Access Denied.');?>

<form method="post" action="<?=$view->action('submit')?>">
    <?=$token->output('submit')?>
    <p class="lead"><?=t('Click below to reset edit mode for the entire site. Any users actively editing a page will be forced out of edit mode.')?></p>

    <button type="submit" class="btn btn-block btn-primary"><?=t('Reset Edit Mode')?></button>

</form>
