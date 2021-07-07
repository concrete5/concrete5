<?php

use Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRecord;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;

/** @var FileUsageRecord[] $records */

?>
<div class="ccm-ui">
    <table id="ccm-file-versions" class="table">
        <tr>
            <th>&nbsp;</th>
            <th><?= t('Filename') ?></th>
            <th><?= t('Title') ?></th>
            <th><?= t('Comments') ?></th>
            <th><?= t('Creator') ?></th>
            <th><?= t('Added On') ?></th>
            <?php if ($fp->canEditFileContents() && $fp->canEditFileProperties()) {
                ?>
                <th>&nbsp;</th>
                <?php
            }
            ?>
        </tr>
        <?php
        $versions = $f->getVersionList();
        foreach ($versions as $fvv) {
            ?>
            <tr <?php if ($fvv->getFileVersionID() == $fv->getFileVersionID()) {
                ?> class="table-success" <?php
            }
            ?>
                    data-file-version-id="<?= $fvv->getFileVersionID() ?>">
                <td style="text-align: center">
                    <input type="radio" name="fvID" value="<?= $fvv->getFileVersionID() ?>"
                           <?php if ($fvv->getFileVersionID() == $fv->getFileVersionID()) {
                           ?>checked<?php
                    }
                    ?> />
                </td>
                <td width="100">
                    <div style="width: 150px; word-wrap: break-word">
                        <?= h($fvv->getFilename()) ?>
                    </div>
                </td>
                <td>
                    <div style="width: 150px; word-wrap: break-word">
                        <?= h($fvv->getTitle()) ?>
                    </div>
                </td>
                <td><?php
                    $comments = $fvv->getVersionLogComments();
                    if (count($comments) > 0) {
                        echo t('Updated ');

                        for ($i = 0; $i < count($comments); ++$i) {
                            echo $comments[$i];
                            if (count($comments) > ($i + 1)) {
                                echo ', ';
                            }
                        }

                        echo '.';
                    }
                    ?>
                </td>
                <td><?= $fvv->getAuthorName() ?></td>
                <td><?= $dh->formatDateTime($fvv->getDateAdded(), true) ?></td>
                <?php if ($fp->canEditFileContents() || $fp->canEditFileProperties()) {
                    ?>
                    <td>
                        <?php if ($fp->canEditFileProperties()) { ?>
                            <?php if ($fvv->getFileVersionID() != $fv->getFileVersionID()) { ?>
                                <a class="me-2 ccm-hover-icon" href="<?= URL::to(
                                    '/dashboard/files/details', 'preview_version', $f->getFileID(), $fvv->getFileVersionID()) ?>">
                                    <i class="fa fa-search"></i></a>
                                <?php } else { ?>
                                    <i class="me-2 fa fa-search" style="opacity: 0.2"></i>
                                <?php } ?>
                        <?php } ?>

                        <?php if ($fp->canEditFileContents()) { ?>

                        <a data-action="delete-version"
                           data-file-version-id="<?= $fvv->getFileVersionID() ?>"
                           data-token="<?= $token->generate('version/delete/' . $fvv->getFileID() . "/" . $fvv->getFileVersionId()) ?>"
                           href="javascript:void(0)" class="ccm-hover-icon"><i class="fa fa-trash"></i></a>
                        <?php } ?>

                    </td>
                    <?php
                }
                ?>
            </tr>

            <?php
        }
        ?>

    </table>

</div>

<script type="text/javascript">

    var ConcreteFilePropertiesDialog = function () {
        var my = this;
        my.setupFileVersionsTable();
    }

    ConcreteFilePropertiesDialog.prototype = {

        handleAjaxResponse: function (r, callback) {
            if (callback) {
                callback(r);
            } else {
                ConcreteAlert.notify({
                    'message': r.message,
                    'appendTo': '#ccm-file-properties-response'
                });
            }
        },

        setupFileVersionsTable: function () {
            var my = this;
            $versions = $('#ccm-file-versions');
            $versions.on('click', 'input[name=fvID]', function () {
                var fvID = $(this).val();
                $.concreteAjax({
                    url: '<?=URL::to('/ccm/system/file/approve_version')?>',
                    data: {'fID': '<?=$f->getFileID()?>', 'fvID': fvID},
                    success: function (r) {
                        $versions.find('tr[class=table-success]').removeClass();
                        $versions.find('tr[data-file-version-id=' + fvID + ']').addClass('table-success');
                    }
                });
            });
            $versions.on('click', 'a[data-action=delete-version]', function () {
                var fvID = $(this).attr('data-file-version-id');
                $.concreteAjax({
                    url: '<?=URL::to('/ccm/system/file/delete_version')?>',
                    data: {'fID': '<?=$f->getFileID()?>', 'fvID': fvID, ccm_token: $(this).data('token')},
                    success: function (r) {
                        var $row = $versions.find('tr[data-file-version-id=' + fvID + ']');
                        $row.queue(function () {
                            $(this).addClass('animated fadeOutDown');
                            $(this).dequeue();
                        }).delay(500).queue(function () {
                            $(this).remove();
                            $(this).dequeue();
                        });
                    }
                });
            });

        }
    }

    $(function () {
        var dialog = new ConcreteFilePropertiesDialog();
    });

</script>
