<?php defined('C5_EXECUTE') or die("Access Denied."); ?>


<div class="form-group">
    <div>
        <label class="control-label form-label"><?=$label?></label>
    </div>
    <div>
        <?php
        $author = $entry->getAuthor();
        if ($author) {
            print $author->getUserInfoObject()->getUserDisplayName();
        } else { ?>
        <span class="text-muted"><?=t('None')?></span>
        <?php
        }
        ?>
    </div>
</div>
