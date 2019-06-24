<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="ccm-lightbox-popup-white">

    <?php if ($isSubscribed) {
    ?>
        <p><?=t('Are you sure you want to unsubscribe from this conversation? You will no longer receive email updates when new messages are posted.')?></p>

        <form method="post" action="<?=$controller->action('unsubscribe', $conversation->getConversationID())?>" data-conversation-form="subscribe">
            <button type="submit" class="btn btn-primary"><?=t('Unsubscribe')?></button>
        </form>
    <?php 
} else {
    ?>
        <p><?=t('Subscribe to receive email updates when new messages are posted.')?></p>
        <form method="post" action="<?=$controller->action('subscribe', $conversation->getConversationID())?>" data-conversation-form="subscribe">
            <button type="submit" class="btn btn-primary"><?=t('Subscribe')?></button>
        </form>
    <?php 
} ?>

</div>