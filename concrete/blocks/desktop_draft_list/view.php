<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<div class="ccm-block-desktop-draft-list">
    <h3><?= t('Page Drafts'); ?></h3>
    <?php if (!empty($drafts)) { ?>
        <div class="draft-list">
            <?php foreach ($drafts as $draft) { ?>
                <div class="draft-list-item">
                    <a href="<?= $draft['link']; ?>">
                        <?= t('%s created by %s on %s', $draft['name'], $draft['user'], $draft['dateAdded']); ?>
                    </a>
                    <?php if (!empty($draft['deleteLink'])) { ?>
                        <a class="dialog-launch btn btn-danger btn-xs" href="<?= $draft['deleteLink']; ?>" dialog-modal="true" dialog-title="<?= t('Delete Draft'); ?>" dialog-width="400" dialog-height="250">
                            <?= t('Delete Draft'); ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php
                if ($showPagination) {
                    echo $pagination;
                }
            ?>
        </div>
    <?php } else { ?>
        <p><?= t('There are no drafts.'); ?></p>
    <?php } ?>
</div>
