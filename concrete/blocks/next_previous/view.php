<?php
defined('C5_EXECUTE') or die('Access Denied.');

if (!$previousLinkURL && !$nextLinkURL && !$parentLabel) {
    return false;
}
?>

<div class="ccm-block-next-previous-wrapper">
    <?php
    if ($previousLinkURL && $previousLabel) {
        ?>
        <div class="ccm-block-next-previous-header">
            <h5><?php echo $previousLabel ?></h5>
        </div>
        <?php
    }

    if ($previousLinkText) {
        ?>
        <p class="ccm-block-next-previous-previous-link">
            <?php echo $previousLinkURL ? '<a href="' . $previousLinkURL . '">' . $previousLinkText . '</a>' : '' ?>
        </p>
        <?php
    }

    if ($nextLinkURL && $nextLabel) {
        ?>
        <div class="ccm-block-next-previous-header">
            <h5><?php echo $nextLabel ?></h5>
        </div>
        <?php
    }

    if ($nextLinkText) {
        ?>
        <p class="ccm-block-next-previous-next-link">
            <?php echo $nextLinkURL ? '<a href="' . $nextLinkURL . '">' . $nextLinkText . '</a>' : '' ?>
        </p>
        <?php
    }

    if ($parentLabel) {
        ?>
        <p class="ccm-block-next-previous-parent-link">
            <?php echo $parentLinkURL ? '<a href="' . $parentLinkURL . '">' . $parentLabel . '</a>' : '' ?>
        </p>
        <?php
    }
    ?>
</div>
