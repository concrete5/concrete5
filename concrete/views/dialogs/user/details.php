<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">


    <div class="form-group">
        <div>
            <label class="control-label form-label"><?= t('Username') ?></label>
        </div>
        <div>
            <?= $uName ?>
        </div>
    </div>

    <div class="form-group">
        <div>
            <label class="control-label form-label"><?= t('Email') ?></label>
        </div>
        <div>
            <?= $uEmail ?>
        </div>
    </div>

    <!-- user group starts -->
    <?php if (count($userGroups) > 0) { ?>
        <h4><?= t('Groups') ?></h4>
        <ul class="list-unstyled">
            <?php foreach ($userGroups as $group) { ?>
                <li><?= $group->getGroupDisplayName() ?></li>
            <?php } ?>
        </ul>
    <?php } ?>
    <!-- user group ends -->

    <!-- user attribut starts -->
    <?php if (count($attributeSets) > 0) : ?>
        <h4><?php echo t('User Attributes') ?></h4>
        <br>
        <?php foreach ($attributeSets as $set) { ?>
            <h5><?php echo $set->getAttributeSetDisplayName() ?></h5>
            <?php foreach ($set->getAttributeKeys() as $ak) {
                $value = $user->getAttributeValueObject($ak);
                ?>
                <div class="form-group">
                    <div>
                        <label class="control-label form-label"><?= $ak->getAttributeKeyDisplayName() ?></label>
                    </div>
                    <div>
                        <?= $value ?>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    <?php endif; ?>
    <?php if (count($unassigned)) {
        if (count($attributeSets)) { ?>
            <h5><?php echo t('Other') ?></h5>
        <?php } ?>
        <?php foreach ($unassigned as $ak) {
            $value = $user->getAttributeValueObject($ak);
            ?>
            <div class="form-group">
                <div>
                    <label class="control-label form-label"><?= $ak->getAttributeKeyDisplayName() ?></label>
                </div>
                <div>
                    <?= $value ?>
                </div>
            </div>
        <?php } ?>
    <?php } ?>

    <!-- // user attribut end -->

    <div class="dialog-buttons">
        <button class="pull-left btn btn-default" onclick="jQuery.fn.dialog.closeTop()"><?= t('Close') ?></button>
        <a href="<?= URL::to('/dashboard/users/search/view', $user->getUserID()) ?>"
           class="btn btn-primary pull-right"><?= t('Edit') ?></a>
    </div>
</div><!-- // div ccm-ui end -->
