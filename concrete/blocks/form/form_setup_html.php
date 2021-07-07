<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var \Concrete\Core\Application\Service\UserInterface $uih
 * @var \Concrete\Core\Application\Service\Urls $uh
 * @var array $miniSurveyInfo
 * @var int $msqID
 * @var int $addSelected
 */
?>
<p>
    <?php echo $uih->tabs([
        ['form-add', t('Add'), $addSelected],
        ['form-edit', t('Edit')],
        ['form-preview', t('Preview')],
        ['form-options', t('Options')],
    ]); ?>
</p>

<input type="hidden" name="miniSurveyServices" value="<?= h($controller->getActionURL('services')) ?>"/>
<input type="hidden" name="miniSurveyServicesToken" value="<?= h(app('token')->generate('ccm-bt-form-service')) ?>" />
<?php /* these question ids have been deleted, or edited, and so shouldn't be duplicated for block versioning */ ?>
<input type="hidden" id="ccm-ignoreQuestionIDs" name="ignoreQuestionIDs" value=""/>
<input type="hidden" id="ccm-pendingDeleteIDs" name="pendingDeleteIDs" value=""/>
<input type="hidden" id="qsID" name="qsID"
       value="<?= isset($miniSurveyInfo['questionSetId']) ? (int) ($miniSurveyInfo['questionSetId']) : 0 ?>"/>
<input type="hidden" id="oldQsID" name="oldQsID"
       value="<?= isset($miniSurveyInfo['questionSetId']) ? (int) ($miniSurveyInfo['questionSetId']) : 0 ?>"/>
<input type="hidden" id="msqID" name="msqID" value="<?= isset($msqID) ? (int) $msqID : 0 ?>"/>
<div class="tab-content">
    <div class="tab-pane<?= $addSelected ? ' show active' : '' ?>" id="form-add" role="tabpanel">
        <fieldset id="newQuestionBox">
            <legend><?php echo t('New Question') ?></legend>

            <div id="questionAddedMsg" class="alert alert alert-info" style="display:none">
                <?= t('Question added. To view it click the preview tab.') ?>
            </div>

            <div class="form-group">
                <?= $form->label('question', t('Question')) ?>
                <?= $form->text('question', ['maxlength' => '255']) ?>
            </div>
            <div class="form-group">
                <?= $form->label('answerType', t('Answer Type')) ?>
                <select class="form-select" name="answerType" id="answerType">
                    <option value="field"><?= t('Text Field') ?></option>
                    <option value="text"><?= t('Text Area') ?></option>
                    <option value="radios"><?= t('Radio Buttons') ?></option>
                    <option value="select"><?= t('Select Box') ?></option>
                    <option value="checkboxlist"><?= t('Checkbox List') ?></option>
                    <option value="fileupload"><?= t('File Upload') ?></option>
                    <option value="email"><?= t('Email Address') ?></option>
                    <option value="telephone"><?= t('Telephone') ?></option>
                    <option value="url"><?= t('Web Address') ?></option>
                    <option value="date"><?= t('Date Field') ?></option>
                    <option value="datetime"><?= t('DateTime Field') ?></option>
                </select>
            </div>

            <div id="answerOptionsArea">
                <div class="form-group">
                    <?= $form->label('answerOptions', t('Answer Options')) ?>
                    <?= $form->textarea('answerOptions', ['rows' => 3]) ?>
                    <span class="help-block"><?= t('Put each answer options on a new line') ?></span>
                </div>
            </div>

            <div id="answerSettings">
                <div class="form-group">
                    <?= $form->label('width', t('Text Area Width')) ?>
                    <?= $form->text('width', 50) ?>
                </div>
                <div class="form-group">
                    <?= $form->label('height', t('Text Area Height')) ?>
                    <?= $form->text('height', 3) ?>
                </div>
            </div>

            <div id="answerDateDefault">
                <div class="form-group">
                    <?= $form->label('defaultDate', t('Default Value')) ?>
                    <?= $form->select(
        'defaultDate',
        [
            '' => t('Blank'),
            'now' => t('Current Date/Time'),
        ],
        'blank'
    ) ?>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label form-label"><?= t('Required') ?></label>

                <div class="form-check">
                    <?= $form->radio('required', 1) ?>
                    <label class="form-check-label" for="required1">
                        <?= t('Yes') ?>
                    </label>
                </div>
                <div class="form-check">
                    <?= $form->radio('required', 0) ?>
                    <label class="form-check-label" for="required2">
                        <?= t('No') ?>
                    </label>
                </div>
            </div>

            <div id="emailSettings">
                <div class="form-group">
                    <?php echo $form->label('send_notification_from', t('Reply to this email address')); ?>
                    <div class="form-check">
                        <?php echo $form->checkbox('send_notification_from', 1); ?>
                        <label class="form-check-label" for="send_notification_from"></label>
                    </div>
                </div>
            </div>

            <?= $ih->button(t('Add Question'), '#', '', '', ['id' => 'addQuestion']) ?>

        </fieldset>

        <input type="hidden" id="position" name="position" value="1000"/>

    </div>

    <div class="tab-pane" id="form-edit" role="tabpanel">

        <div id="questionEditedMsg" class="alert alert-success" style="display:none">
            <?php echo t('Your question has been saved.') ?>
        </div>

        <div id="editQuestionForm" style="display:none">
            <fieldset>
                <legend id="editQuestionTitle"><?= t('Edit Question') ?></legend>

                <div class="form-group">
                    <?= $form->label('questionEdit', t('Question')) ?>
                    <?= $form->text('questionEdit') ?>
                </div>
                <div class="form-group">
                    <?= $form->label('answerTypeEdit', t('Answer Type')) ?>
                    <select class="form-select" name="answerTypeEdit" id="answerTypeEdit">
                        <option value="field"><?= t('Text Field') ?></option>
                        <option value="text"><?= t('Text Area') ?></option>
                        <option value="radios"><?= t('Radio Buttons') ?></option>
                        <option value="select"><?= t('Select Box') ?></option>
                        <option value="checkboxlist"><?= t('Checkbox List') ?></option>
                        <option value="fileupload"><?= t('File Upload') ?></option>
                        <option value="email"><?= t('Email Address') ?></option>
                        <option value="telephone"><?= t('Telephone') ?></option>
                        <option value="url"><?= t('Web Address') ?></option>
                        <option value="date"><?= t('Date Field') ?></option>
                        <option value="datetime"><?= t('DateTime Field') ?></option>
                    </select>
                </div>


                <div id="answerOptionsAreaEdit">
                    <div class="form-group">
                        <?= $form->label('answerOptionsEdit', t('Answer Options')) ?>
                        <?= $form->textarea('answerOptionsEdit', ['rows' => 3]) ?>
                        <span class="help-block"><?= t('Put each answer options on a new line') ?></span>
                    </div>
                </div>

                <div id="answerSettingsEdit">
                    <div class="form-group">
                        <?= $form->label('widthEdit', t('Text Area Width')) ?>
                        <?= $form->text('widthEdit', 50) ?>
                    </div>
                    <div class="form-group">
                        <?= $form->label('heightEdit', t('Text Area Height')) ?>
                        <?= $form->text('heightEdit', 3) ?>
                    </div>
                </div>

                <div id="answerDateDefaultEdit">
                    <div class="form-group">
                        <?= $form->label('defaultDateEdit', t('Default Value')) ?>
                        <?= $form->select(
        'defaultDateEdit',
        [
            '' => t('Blank'),
            'now' => t('Current Date/Time'),
        ],
        'blank'
    ) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label form-label"><?= t('Required') ?></label>
                    <div class="form-check">
                        <?= $form->radio('requiredEdit', 1) ?>
                        <label class="form-check-label" for="requiredEdit1">
                            <?= t('Yes') ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <?= $form->radio('requiredEdit', 0) ?>
                        <label class="form-check-label" for="requiredEdit2">
                            <?= t('No') ?>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <div id="emailSettingsEdit">
                        <?php echo $form->label('send_notification_from_edit', t('Reply to this email address')); ?>
                        <div class="form-check">
                            <?php echo $form->checkbox('send_notification_from_edit', 1); ?>
                            <label class="form-check-label" for="send_notification_from_edit"></label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <input type="hidden" id="positionEdit" name="position" value="1000"/>

            <div>
                <?= $ih->button(t('Cancel'), 'javascript:void(0)', 'left', '', ['id' => 'cancelEditQuestion']) ?>
                <?= $ih->button(t('Save Changes'), 'javascript:void(0)', 'right', 'primary', ['id' => 'editQuestion']) ?>
            </div>
        </div>

        <div id="miniSurvey">
            <fieldset>
                <legend><?= t('Edit Survey') ?></legend>
                <div id="miniSurveyWrap"></div>
            </fieldset>
        </div>
    </div>

    <div class="tab-pane" id="form-options" role="tabpanel">
        <?php
        $c = Page::getCurrentPage();
        if (!isset($miniSurveyInfo['surveyName']) || strlen($miniSurveyInfo['surveyName']) == 0) {
            $miniSurveyInfo['surveyName'] = $c->getCollectionName();
        }
        ?>
        <fieldset>
            <legend><?= t('Options') ?></legend>
            <div class="form-group">
                <?= $form->label('surveyName', t('Form Name')) ?>
                <?= $form->text('surveyName', $miniSurveyInfo['surveyName']) ?>
            </div>
            <div class="form-group">
                <?= $form->label('submitText', t('Submit Text')) ?>
                <?= $form->text('submitText', $this->controller->submitText) ?>
            </div>
            <div class="form-group">
                <?= $form->label('thankyouMsg', t('Message to display when completed')) ?>
                <?= $form->textarea('thankyouMsg', $this->controller->thankyouMsg, ['rows' => 3]) ?>
            </div>
            <div class="form-group">

                <span class="input-group-text btn btn-secondary">
                    <div class="form-check">
                        <?= $form->checkbox('notifyMeOnSubmission', 1, isset($miniSurveyInfo['notifyMeOnSubmission']) && $miniSurveyInfo['notifyMeOnSubmission'] == 1, ['onclick' => "$('input[name=recipientEmail]').focus()"]) ?>
                        <label class="form-check-label" for="notifyMeOnSubmission"></label>
                    </div>
                </span>

                <?= $form->text('recipientEmail', empty($miniSurveyInfo['recipientEmail']) ? '' : $miniSurveyInfo['recipientEmail'], ['style' => 'z-index:2000;']) ?>

                <span class="help-block"><?= t('(Separate multiple emails with a comma)') ?></span>
            </div>
            <div class="form-group">
                <label class="control-label form-label"><?= t('Solving a <a href="%s" target="_blank">CAPTCHA</a> Required to Post?', t('http://en.wikipedia.org/wiki/Captcha')) ?></label>
                <div class="form-check">
                    <?= $form->radio('displayCaptcha', 1, empty($miniSurveyInfo['displayCaptcha']) ? 0 : (int) $miniSurveyInfo['displayCaptcha']) ?>
                    <label class="form-check-label" for="displayCaptcha1">
                        <?= t('Yes') ?>
                    </label>
                </div>
                <div class="form-check">
                    <?= $form->radio('displayCaptcha', 0, empty($miniSurveyInfo['displayCaptcha']) ? 0 : (int) $miniSurveyInfo['displayCaptcha']) ?>
                    <label class="form-check-label" for="displayCaptcha2">
                        <?= t('No') ?>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label form-label"
                       for="ccm-form-redirect"><?= t('Redirect to another page after form submission?') ?></label>
                <div id="ccm-form-redirect-page">
                    <?php
                    if (!empty($miniSurveyInfo['redirectCID'])) {
                        echo $page_selector->selectPage('redirectCID', $miniSurveyInfo['redirectCID']);
                    } else {
                        echo $page_selector->selectPage('redirectCID');
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label form-label" for="ccm-form-fileset"><?= t('Add uploaded files to a set?') ?></label>
                <div id="ccm-form-fileset">
                    <?php

                    $fs = new FileSet();
                    $fileSets = $fs->getMySets();
                    $sets = [0 => t('None')];
                    foreach ($fileSets as $fileSet) {
                        $sets[$fileSet->fsID] = $fileSet->fsName;
                    }
                    echo $form->select('addFilesToSet', $sets, $miniSurveyInfo['addFilesToSet'] ?? null);
                    ?>
                </div>
            </div>
        </fieldset>
    </div>

    <div class="tab-pane" id="form-preview" role="tabpanel">
        <fieldset>
            <legend><?= t('Preview Survey') ?></legend>
            <div id="miniSurveyPreviewWrap"></div>
        </fieldset>
    </div>
</div>


<style type="text/css">
    div.miniSurveyQuestion {
        float: left;
        width: 80%;
    }

    div.miniSurveyOptions {
        float: left;
        width: 20%;
        text-align: right;
    }
</style>

<script type="text/javascript">
    //safari was loading the auto.js too late. This ensures it's initialized
    function initFormBlockWhenReady() {
        if (miniSurvey && typeof (miniSurvey.init) == 'function') {
            miniSurvey.cID = parseInt(<?php echo $c->getCollectionID()?>);
            miniSurvey.arHandle = "<?php echo urlencode($_REQUEST['arHandle'])?>";
            miniSurvey.bID = thisbID;
            miniSurvey.btID = thisbtID;
            miniSurvey.qsID = parseInt(<?= $miniSurveyInfo['questionSetId'] ?? null ?>);
            miniSurvey.init();
            miniSurvey.refreshSurvey();
        } else setTimeout('initFormBlockWhenReady()', 100);
    }

    initFormBlockWhenReady();
</script>
