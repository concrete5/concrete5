<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<div class="ccm-block-testimonial-wrapper">
    <div class="ccm-block-testimonial">
        <?php if ($image): ?>
            <div class="ccm-block-testimonial-image"><?=$image?></div>
        <?php endif; ?>

        <div class="ccm-block-testimonial-text">

            <div class="ccm-block-testimonial-name">
                <?=h($name)?>
            </div>

        <?php if ($position && $company && $companyURL): ?>
            <div class="ccm-block-testimonial-position">
                <?=t('%s, <a href="%s">%s</a>', h($position), $companyURL, h($company))?>
            </div>
        <?php endif; ?>

        <?php if ($position && !$company && $companyURL): ?>
            <div class="ccm-block-testimonial-position">
                <?=t('<a href="%s">%s</a>', $companyURL, h($position))?>
            </div>
        <?php endif; ?>

        <?php if ($position && $company && !$companyURL): ?>
            <div class="ccm-block-testimonial-position">
                <?=t('%s, %s', h($position), h($company))?>
            </div>
        <?php endif; ?>

        <?php if ($position && !$company && !$companyURL): ?>
            <div class="ccm-block-testimonial-position">
                <?=h($position)?>
            </div>
        <?php endif; ?>

        <?php if ($paragraph): ?>
            <div class="ccm-block-testimonial-paragraph"><?=h($paragraph)?></div>
        <?php endif; ?>

        </div>

    </div>
</div>