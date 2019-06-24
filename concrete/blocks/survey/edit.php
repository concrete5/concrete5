<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<style type="text/css">
    div.survey-block-option {
        position: relative;
        padding: 6px;
    }

    div.survey-block-option:hover {
        background: #e7e7e7;
        border-radius: 4px;
    }

    div.survey-block-option img {
        position: absolute;
        top: 3px;
        right: 0px;
    }

</style>
<div class="ccm-ui survey-block-edit">
    <div class="form-group">
        <label for="questionEntry" class="control-label"><?= t('Question') ?></label>
        <input type="text" name="question" value="<?= $controller->getQuestion() ?>"
               class="form-control"/>
    </div>
    <div class="form-group">
        <label for="requiresRegistration" class="control-label"><?= t('Target Audience') ?></label>

        <div class="radio">
            <label>
                <input id="requiresRegistration" type="radio" value="0" name="requiresRegistration"
                       style="vertical-align: middle" <?php if (!$controller->requiresRegistration()) {
        ?> checked <?php
    } ?> />&nbsp;<?= t(
                    'Public') ?>
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" value="1" name="requiresRegistration"
                       style="vertical-align: middle" <?php if ($controller->requiresRegistration()) {
        ?> checked <?php
    } ?> />&nbsp;<?= t(
                    'Only Registered Users') ?>
            </label>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label"><?= t('Survey Options') ?></label>

        <div class="poll-options">
            <?php
            $options = $controller->getPollOptions();
            if (count($options) == 0) {
                ?>
                <div class="empty">
                    <?= t('None') ?>
                </div>
                <?php

            } else {
                foreach ($options as $opt) {
                    ?>
                    <div class="survey-block-option">
                        <a href="#" class="pull-right delete">
                            <i class="fa fa-trash-o"></i>
                        </a>
                        <?= h($opt->getOptionName()) ?>
                        <input type="hidden" name="survivingOptionNames[]"
                               value="<?= h($opt->getOptionName()) ?>"/>
                    </div>
                <?php

                }
            } ?>
        </div>
    </div>

    <div class="form-group">
        <label for="optionEntry" class="control-label"><?= t('Add Option') ?></label>

        <div class="input-group">
            <input type="text" name="optionValue" class="option-value form-control"/>
            <span class="input-group-btn">
                <button class="add-option btn btn-primary" type="button">
                    <?php echo t('Add'); ?>
                </button>
            </span>
        </div>
    </div>
    <script type="text/template" role="option">
        <div class="survey-block-option">
            <a href="#" class="delete pull-right">
                <i class="fa fa-trash-o"></i>
            </a>
            <%- value %>
            <input type="hidden" name="pollOption[]"
                   value="<%- value %>"/>
        </div>
    </script>
</div>
<script type="text/javascript">
    Concrete.event.fire('survey-edit-open');
</script>
