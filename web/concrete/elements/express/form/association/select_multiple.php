<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="control-group">
    <label><?=$label?></label>
    <?php
    if (count($entities)) {
        foreach($entities as $entity) { ?>
            <label><input type="checkbox" value="<?=$entity->getId()?>"> <?=$entity->getDisplayName()?></label>
        <? } ?>
    <? } else { ?>
        <p><?=t('None found.')?></p>
    <? } ?>
</div>