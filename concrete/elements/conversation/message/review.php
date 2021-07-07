<?php
/**
 * @type int $review A number between 0 and 5
 * @type bool $starsOnly Should editing be disabled
 */
if (!isset($starsOnly) || !$starsOnly) {
    ?>
    <label for="review" class="float-start">
        <?= t('Rating') ?>&nbsp;
    </label>
    <?php
}
?>
<div class="star-rating <?= $selector = uniqid('rating') ?>" data-name="review" data-score="<?= intval($review) ?>""></div>

<script>
    (function() {
        var stars = $('.<?= $selector ?>').awesomeStarRating();
        <?php
        if (isset($starsOnly) && $starsOnly) {
            ?>
            $('.<?= $selector ?>').children().unbind();
            <?php
        }
        ?>
    }());
</script>
