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
        <label for="questionEntry" class="control-label form-label"><?= t('Question') ?></label>
        <input type="text" name="question" value="<?= $controller->getQuestion() ?>"
               class="form-control"/>
    </div>
    <div class="form-group">
        <label for="requiresRegistration" class="control-label form-label"><?= t('Target Audience') ?></label>
        <div class="form-check">
            <?=$form->radio('requiresRegistration', '0', $controller->requiresRegistration())?>
            <?=$form->label('requiresRegistration1',t('Public'), ['class'=>'form-check-label'])?>
        </div>
        <div class="form-check">
            <?=$form->radio('requiresRegistration', '1', $controller->requiresRegistration())?>
            <?=$form->label('requiresRegistration2',t('Only Registered Users'), ['class'=>'form-check-label'])?>
        </div>
    </div>

    <div class="form-group">
        <label for="showResults" class="control-label form-label"><?= t('Survey Results') ?></label>
        <div class="form-check">
            <?=$form->checkbox('showResults', 1, ($controller->getShowResults() ? '1':'0'), ['class'=>'show-custom-message form-check-input'])?>
            <?=$form->label('showResults', t('Hide Survey Results and Show Custom Message'), ['class'=>'form-check-label']) ?>
        </div>
    </div>
    <div class="form-group custom-message-container" style="display: none;">
        <label for="customMessage" class="control-label form-label"><?= t('Custom Message') ?></label>
        <input type="text" name="customMessage" value="<?= $controller->getCustomMessage() ?>"
               class="form-control" placeholder="Thank you for filling out this form!"/>
    </div>

    <div class="form-group">
        <label class="control-label form-label"><?= t('Survey Options') ?></label>

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
                        <a href="javascript:void(0);" class="float-end ccm-icon-wrapper link-danger delete">
                            <i class="fas fa-trash-alt"></i>
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
        <label for="optionEntry" class="control-label form-label"><?= t('Add Option') ?></label>

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
            <a href="javascript:void(0);" class="float-end ccm-icon-wrapper link-danger delete">
                <i class="fas fa-trash-alt"></i>
            </a>
            <%- value %>
            <input type="hidden" name="pollOption[]"
                   value="<%- value %>"/>
        </div>
    </script>
</div>
<script type="text/javascript">
    Concrete.event.fire('survey-edit-open');

    $(document).ready(function(){
      let showCustomMessage = <?= (int) $controller->getShowResults() ?>;

      if(showCustomMessage) {
        $('.custom-message-container').show();
      }

      $('.show-custom-message').on('change', function(e){
        e.preventDefault();
        if($(this).prop('checked')){
          $('.custom-message-container').show();
        }
        else {
          $('.custom-message-container').hide();
        }
      });
    });
</script>
