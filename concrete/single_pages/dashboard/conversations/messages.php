<?php defined('C5_EXECUTE') or die('Access Denied.');

$valt = Loader::helper('validation/token');
$th = Loader::helper('text');
$ip = Loader::helper('validation/ip');
?>

<div class="ccm-dashboard-content-full">
    <div data-search-element="results">
        <div class="table-responsive">
            <table id="ccm-conversation-messages" class="ccm-search-results-table">
                <thead>
                    <tr>
                        <th class="<?=$list->getSearchResultsClass('cnvMessageDateCreated')?>"><a href="<?=$list->getSortByURL('cnvMessageDateCreated', 'desc')?>"><?=t('Posted')?></a></th>
                        <th><span><?=t('Author')?></span></th>
                        <th><span><?=t('Message')?></span></th>
                        <th style="text-align: center"><span><?=t('Status')?></span></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($messages) > 0) {
                $dh = Core::make('date');
                foreach ($messages as $msg) {
                    $cnv = $msg->getConversationObject();
                    if (is_object($cnv)) {
                        $page = $cnv->getConversationPageObject();
                    }

                    $msgID = $msg->getConversationMessageID();
                    $cnvID = $cnv->getConversationID();
                    $p = new Permissions($cnv);
                    $author = $msg->getConversationMessageAuthorObject();
                    $formatter = $author->getFormatter();

                    $displayUnflagOption = $p->canFlagConversationMessage() && $msg->isConversationMessageFlagged();
                    $displayUndeleteOption = $p->canDeleteConversationMessage() && $msg->isConversationMessageDeleted();

                    $displayApproveOption = $p->canApproveConversationMessage() && (!$msg->isConversationMessageDeleted() && !$msg->isConversationMessageApproved() && !$msg->isConversationMessageFlagged());
                    if (!$displayUnflagOption) {
                        $displayFlagOption = $p->canFlagConversationMessage() && !$msg->isConversationMessageDeleted();
                    }
                    $displayDeleteOption = $p->canDeleteConversationMessage() && !$msg->isConversationMessageDeleted();
                    ?>
                    <tr>
                        <!-- <td><?=$form->checkbox('cnvMessageID[]', $msg->getConversationMessageID())?></td> -->
                        <td>
                            <?=$dh->formatDateTime(strtotime($msg->getConversationMessageDateTime()))?>
                        </td>
                        <td>
                            <div class="ccm-popover ccm-conversation-message-popover popover fade" data-menu="<?=$msg->getConversationMessageID()?>">
                                <div class="arrow"></div><div class="popover-inner">
                                    <ul class="dropdown-menu">
                                    <?php if (is_object($page)) { ?>
                                        <li><a href="<?=$page->getCollectionLink()?>#cnv<?=$cnv->getConversationID()?>Message<?=$msg->getConversationMessageID()?>"><?=t('View Conversation')?></a></li>
                                        <?php if ($displayFlagOption || $displayApproveOption || $displayDeleteOption || $displayUnflagOption || $displayUndeleteOption) { ?>
                                        <li class="divider"></li>
                                        <?php } ?>
                                    <?php } ?>

                                        <?php if ($displayApproveOption) { ?>
                                        <li><a href="#" data-message-action="approve" data-message-id="<?=$msg->getConversationMessageID()?>"><?=t('Approve')?></a></li>
                                        <?php } ?>

                                        <?php if ($displayFlagOption) { ?>
                                        <li><a href="#" data-message-action="flag" data-message-id="<?=$msg->getConversationMessageID()?>"><?=t('Flag as Spam')?></a></li>
                                        <?php } ?>

                                        <?php if ($displayDeleteOption) { ?>
                                        <li><a href="#" data-message-action="delete" data-message-id="<?=$msg->getConversationMessageID()?>"><?=t('Delete')?></a></li>
                                        <?php } ?>

                                        <?php if ($displayUnflagOption) { ?>
                                        <li><a href="#" data-message-action="unflag" data-message-id="<?=$msg->getConversationMessageID()?>"><?=t('Un-Flag As Spam')?></a></li>
                                        <?php } ?>

                                        <?php if ($displayUndeleteOption) { ?>
                                        <li><a href="#" data-message-action="undelete" data-message-id="<?=$msg->getConversationMessageID()?>"><?=t('Un-Delete Message')?></a></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>

                            <p><span class="ccm-conversation-display-author-name"><?php
                                    echo tc(/*i18n: %s is the name of the author */ 'Authored', 'By %s', $formatter->getLinkedAdministrativeDisplayName());
                            ?></span></p>

                            <?php if (is_object($page)) { ?>
                            <div><?=$page->getCollectionPath()?></div>
                            <?php } ?>
                        </td>

                        <td class="message-cell" style="width: 33%">
                            <div class="ccm-conversation-message-summary">
                                <div class="message-output">
                                    <?=$msg->getConversationMessageBodyOutput(true)?>
                                </div>
                            </div>
                        </td>

                        <td style="text-align: center">
                        <?php if (!$msg->isConversationMessageApproved() && !$msg->isConversationMessageDeleted()) { ?>
                            <i class="fa fa-warning text-warning launch-tooltip" title="<?php echo t('Message has not been approved.')?>"></i>
                        <?php }
                        if ($msg->isConversationMessageDeleted()) { ?>
                            <i class="fa fa-trash launch-tooltip" title="<?php echo t('Message is deleted.')?>"></i>
                        <?php }
                        if ($msg->isConversationMessageFlagged()) { ?>
                            <i class="fa fa-flag text-danger launch-tooltip" title="<?php echo t('Message is flagged as spam.')?>"></i>
                        <?php }
                        if ($msg->isConversationMessageApproved() && !$msg->isConversationMessageDeleted()) { ?>
                            <i class="fa fa-thumbs-up launch-tooltip" title="<?php echo t('Message is approved.')?>"></i>
                        <?php } ?>
                        </td>
                    </tr>
                    <?php }
                } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="ccm-dashboard-header-buttons">
    <div class="ccm-header-search-form ccm-ui">
        <form role="form" action="<?=$controller->action('view')?>">
            <div class="input-group">
                <input type="text" class="form-control" autocomplete="off" name="cmpMessageKeywords" value="<?=h($_REQUEST['cmpMessageKeywords'])?>" placeholder="<?=t('Keywords')?>">
                <span class="input-group-btn">
                <select data-select="bootstrap" name="cmpMessageFilter">
                    <?php foreach ($cmpFilterTypes as $optionValue => $optionText) { ?>
                        <option value="<?php echo $optionValue; ?>" <?php if ($optionValue == $cmpMessageFilter) { echo 'selected'; } ?>><?php echo $optionText; ?></option>
                    <?php } ?>
                </select>
                <button class="btn btn-info" type="submit"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </form>
    </div>
</div>


<script>
$(function() {
    $('#ccm-conversation-messages tbody tr').each(function() {
        $(this).concreteMenu({
            menu: $(this).find('div[data-menu]')
        });
    });

    $('a[data-message-action=flag]').on('click', function(e) {
        e.preventDefault();
        $.concreteAjax({
            url: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/conversations/flag_message',
            data: {
                'cnvMessageID': $(this).attr('data-message-id'),
                'token': '<?= $valt->generate('flag_conversation_message'); ?>'
            },
            success: function(r) {
                window.location.reload();
            }
        });
    });

    $('a[data-message-action=delete]').on('click', function(e) {
        e.preventDefault();
        $.concreteAjax({
            url: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/conversations/delete_message',
            data: {
                'cnvMessageID': $(this).attr('data-message-id'),
                'token': '<?= $valt->generate('delete_conversation_message'); ?>'
            },
            success: function(r) {
                window.location.reload();
            }
        });
    });

    $('a[data-message-action=approve]').on('click', function(e) {
        e.preventDefault();
        $.concreteAjax({
            url: '<?=$controller->action('approve_message')?>',
            data: {
                'cnvMessageID': $(this).attr('data-message-id')
            },
            success: function(r) {
               window.location.reload();
            }
        });
    });

    $('a[data-message-action=unflag]').on('click', function(e) {
        e.preventDefault();
        $.concreteAjax({
            url: '<?=$controller->action('unflag_message')?>',
            data: {
                'cnvMessageID': $(this).attr('data-message-id')
            },
            success: function(r) {
                window.location.reload();
            }
        });
    });

    $('a[data-message-action=undelete]').on('click', function(e) {
        e.preventDefault();
        $.concreteAjax({
            url: '<?=$controller->action('undelete_message')?>',
            data: {
                'cnvMessageID': $(this).attr('data-message-id')
            },
            success: function(r) {
                window.location.reload();
            }
        });
    });
});
</script>

<style>
span.ccm-conversation-display-author-name,
#ccm-conversation-messages i.fa {
    position: relative;
    z-index: 800;
}

div.ccm-popover.ccm-conversation-message-popover {
    z-index: 801;
}
</style>

<!-- END Body Pane -->
<?=$list->displayPagingV2()?>
</div>
