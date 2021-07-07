<?php defined('C5_EXECUTE') or die("Access Denied."); ?>


<div class="ccm-ui">

    <div id="ccm-calendar-event-version-reload" class="alert alert-info" style="display: none">
        <button class="float-end btn btn-xs btn-secondary" onclick="window.location.reload()" type="button"><?=t('Reload')?></button>
        <?=t('Reload the page to refresh the events.')?>
    </div>

    <table id="ccm-calendar-event-versions" class="table">
        <tr>
            <th>&nbsp;</th>
            <th><?= t('Version') ?></th>
            <th><?= t('Title') ?></th>
            <th><?= t('Creator') ?></th>
            <th><?= t('Added On') ?></th>
            <th><?= t('Activated') ?></th>
            <th>&nbsp;</th>
        </tr>
        <?php
        $versions = $event->getVersions();
        $count = count($versions);
        $unapprovedChecked = true;
        foreach ($versions as $version) {
            $author = t('(Unknown User)');
            if (!empty($version->getAuthor())) {
                $author = $version->getAuthor()->getUserName();
            }
            if ($version->isApproved()) {
                $unapprovedChecked = false;
            }
            ?>
            <tr <?php if ($version->isApproved()) { ?> class="success" <?php } ?>
                data-calendar-event-version-id="<?= $version->getID() ?>">
                <td style="text-align: center">
                    <div class="form-check">
                        <input type="radio" name="eventVersionID" class="form-check-input" data-token="<?= Core::make('token')->generate('calendar/event/version/approve/' . $version->getID()) ?>" value="<?= $version->getID() ?>"
                           <?php if ($version->isApproved()) { ?>checked<?php } ?> />
                    </div>
                </td>
                <td>
                    <div>
                        <a href="#" class="dialog-launch"><?=$count?></a>
                    </div>
                </td>
                <td>
                    <div style="width: 150px; word-wrap: break-word">
                        <a style="color: #00a3da" href="<?=URL::to('/ccm/calendar/dialogs/event/version/view') . '?eventVersionID=' . $version->getID()?>" class="dialog-launch" dialog-width="500" dialog-height="500"><?= h($version->getName()) ?></a>
                    </div>
                </td>
                <td><?= $author ?></td>
                <td><?php
                    $dateAdded = $version->getDateAdded();
                    print $dateAdded->format('n/j/Y g:i a');
                    ?>
                </td>
                <td><?php
                    $dateActivated = $version->getDateActivated();
                    if ($dateActivated) {
                        print $dateActivated->format('n/j/Y g:i a');
                    }
                    ?>
                </td>
                    <td><a <?php if ($version->isApproved()) { ?>style="display: none"<?php } ?> data-action="delete-version"
                           data-calendar-event-version-id="<?= $version->getID() ?>"
                           data-token="<?= Core::make('token')->generate('calendar/event/version/delete/' . $version->getID()) ?>"
                           href="javascript:void(0)"><i class="fa fa-trash"></i></a></td>
            </tr>
            <?php
            $count--;
        }
        ?>

        <tr>
            <td style="text-align: center">
                <div class="form-check">
                <input type="radio" name="eventVersionID" class="form-check-input" data-event-id="<?=$event->getID()?>" data-token="<?= Core::make('token')->generate('unapprove_event') ?>" value="-1"
                       <?php if ($unapprovedChecked) { ?>checked<?php } ?>
                </div>
            </td>
            <td colspan="6" class="text-muted"><?=t('No approved version.')?></td>
        </tr>
    </table>

</div>

<script type="text/javascript">
    $(function() {
        ConcreteCalendarAdmin.setupVersionsTable($('table#ccm-calendar-event-versions'));
    });
</script>
