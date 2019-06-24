<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div id="ccm-block-express-form-tabs">
<?php echo Loader::helper('concrete/ui')->tabs([
    ['form-add', t('Add')],
    ['form-edit', t('Edit')],
    ['form-express', t('Express Object')],
    ['form-results', t('Results')],
    ['form-options', t('Options')],
]); ?>
</div>

<div id="ccm-block-express-form-choose-type">

    <div class="spacer-row-6"></div>

    <fieldset>
        <legend><?=t('Form Type'); ?></legend>

        <div class="form-group">
            <label class="control-label launch-tooltip" title="<?=t('If you are creating a completely new form, choose New Form. If you have already created an express entity in the Dashboard and would like to embed its form on this page, choose Existing Express Entity Form.'); ?>"><?=t('What kind of form do you want to create?'); ?></label>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <button data-action="choose-new-form" type="button" class="btn btn-default btn-block"><i class="fa fa-plus-circle" style="opacity: 0.3"></i> &nbsp; <?=t('New Form'); ?></button>
                </div>
                <div class="col-sm-6">
                    <button data-action="choose-existing-form" type="button" class="btn btn-default btn-block"><i class="fa fa-database" style="opacity: 0.3"></i> &nbsp; <?=t('Existing Express Entity Form'); ?></button>
                </div>
            </div>
        </div>
    </fieldset>
</div>

<div id="ccm-tab-content-form-add" class="ccm-tab-content" data-action="<?=$view->action('add_control'); ?>">
    <div class="alert alert-success" style="display: none"><?=t('Field added successfully.'); ?></div>
    <fieldset>
        <legend><?php echo t('New Question'); ?></legend>

        <div data-view="add-question-inner">

        </div>

        <div class="form-group" data-group="add-question" style="display: none">

            <button type="button" class="btn btn-primary" data-action="add-question"><?=t('Add Question'); ?></button>
        </div>

    </fieldset>

</div>

<div id="ccm-tab-content-form-edit" class="ccm-tab-content" data-action="<?=$view->action('update_control'); ?>">

    <div class="alert alert-success" style="display: none"><?=t('Field updated successfully.'); ?></div>

    <div data-view="form-fields">

    <fieldset>
        <legend><?php echo t('Fields'); ?></legend>

        <ul class="list-group">
        </ul>
    </fieldset>

    </div>

    <div data-view="edit-question" style="display: none">

        <fieldset>
            <legend><?php echo t('Edit Question'); ?></legend>

            <div data-view="edit-question-inner">

            </div>

            <div class="form-group">
                <hr/>
                <button type="button" class="btn btn-default" data-action="cancel-edit"><?=t('Cancel'); ?></button>
                <button type="button" class="btn btn-primary pull-right" data-action="update-question"><?=t('Save Question'); ?></button>
            </div>

        </fieldset>

    </div>

</div>

<div id="ccm-tab-content-form-express" class="ccm-tab-content">
    <fieldset>

        <div class="form-group">
            <?=$form->label('exEntityID', t('Choose Entity Form')); ?>
            <select name="exFormID" class="form-control">
                <option value=""><?=t('** Choose Entity Form'); ?></option>
            <?php foreach ($entities as $entity) {
    ?>
                <optgroup label="<?=t('Entity: %s', $entity->getName()); ?>">
                    <?php foreach ($entity->getForms() as $entityForm) {
        ?>
                        <option value="<?=$entityForm->getID(); ?>" <?php if ($entityForm->getID() == $exFormID) {
            ?>selected<?php
        } ?>><?=h($entityForm->getName()); ?></option>
                    <?php
    } ?>
                </optgroup>
            <?php
} ?>
            </select>
        </div>

    </fieldset>

</div>


<div id="ccm-tab-content-form-results" class="ccm-tab-content" >
    <fieldset>
        <input type="hidden" name="resultsFolder" value="<?=$resultsFolder; ?>">

        <legend><?php echo t('Results'); ?></legend>

        <div class="form-group">
            <div class="checkbox"><label>
                    <?php
                    $storeFormSubmissionFormFields = [];
                    if ($formSubmissionConfig === true || $formSubmissionConfig === false) {
                        $storeFormSubmissionFormFields['disabled'] = 'disabled';
                    }
                    ?>
                    <?=$form->checkbox('storeFormSubmission', 1, $storeFormSubmission, $storeFormSubmissionFormFields); ?>
                    <?=t('Store submitted results of form.'); ?>
                </label></div>
            <?php if ($formSubmissionConfig === false) { ?>
                <div class="alert alert-warning"><?=t('<strong>Warning!</strong> Form submissions are not allowed to be stored in the database. You must set a valid email in the Options tab.'); ?>                </div>
            <?php } else { ?>
                <div class="alert alert-warning"><?=t('<strong>Warning!</strong> If not checked submitted data will be only sent by email. You must set a valid email in the Options tab.'); ?></div>
            <?php } ?>
        </div>

        <div class="form-group" data-section="form-results-folder">
            <label class="control-label"><?=t('Express Folder to Receive Form Results')?></label>
            <?php if ($tree) { ?>
                <div data-root-tree-node-id="<?=$formResultsRootFolderNodeID; ?>" data-tree="<?=$tree->getTreeID(); ?>">

                </div>
            <?php } ?>
        </div>

    </fieldset>

</div>

<div id="ccm-tab-content-form-options" class="ccm-tab-content">
    <fieldset>
        <legend><?=t('Basics'); ?></legend>
        <div class="form-group">
            <?=$form->label('formName', t('Form Name')); ?>
            <?=$form->text('formName', $formName); ?>
        </div>
        <div class="form-group">
            <?=$form->label('submitLabel', t('Submit Button Label')); ?>
            <?=$form->text('submitLabel', $submitLabel); ?>
        </div>
        <div class="form-group">
            <label class="control-label"><?=t('Solving a <a href="%s" target="_blank">CAPTCHA</a> Required to Post?', t('http://en.wikipedia.org/wiki/Captcha')); ?></label>
            <div class="radio">
                <label>
                    <?=$form->radio('displayCaptcha', 1, (int) $displayCaptcha); ?>
                    <span><?=t('Yes'); ?></span>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?=$form->radio('displayCaptcha', 0, (int) $displayCaptcha); ?>
                    <span><?=t('No'); ?></span>
                </label>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend><?=t('Success'); ?></legend>
        <div class="form-group">
            <?=$form->label('thankyouMsg', t('Message to display when completed')); ?>
            <?=$form->textarea('thankyouMsg', $thankyouMsg, ['rows' => 3]); ?>
        </div>
        <div class="form-group">
            <label class="control-label" for="ccm-form-redirect"><?=t('Redirect to another page after form submission?'); ?></label>
            <div id="ccm-form-redirect-page">
                <?php
                $page_selector = Loader::helper('form/page_selector');
                if ($redirectCID) {
                    echo $page_selector->selectPage('redirectCID', $redirectCID);
                } else {
                    echo $page_selector->selectPage('redirectCID');
                }
                ?>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend><?=t('Email'); ?></legend>
        <div class="form-group">
            <?=$form->label('recipientEmail', t('Send form submissions to email addresses')); ?>
            <div class="input-group">
				<span class="input-group-addon" style="z-index: 2000">
				<?=$form->checkbox('notifyMeOnSubmission', 1, 1 == $notifyMeOnSubmission); ?>
				</span><?=$form->text('recipientEmail', $recipientEmail, ['autocomplete' => 'off', 'style' => 'z-index:2000;']); ?>
            </div>
            <span class="help-block"><?=t('(Separate multiple emails with a comma)'); ?></span>
        </div>
        <div data-view="form-options-email-reply-to"></div>
    </fieldset>
    <fieldset>
        <legend><?=t('Files'); ?></legend>
        <div class="form-group">
            <label class="control-label" for="ccm-form-fileset"><?=t('Add uploaded files to a set?'); ?></label>
                <?php

                $fileSets = Concrete\Core\File\Set\Set::getMySets();
                $sets = [0 => t('None')];
                foreach ($fileSets as $fileSet) {
                    $sets[$fileSet->getFileSetID()] = $fileSet->getFileSetDisplayName();
                }
                echo $form->select('addFilesToSet', $sets, $addFilesToSet);
                ?>
        </div>
        <div class="form-group">
            <label class="control-label"><?=t('Add uploaded files to folder'); ?></label>
            <?php
            $selector = new \Concrete\Core\Form\Service\Widget\FileFolderSelector();
            echo $selector->selectFileFolder('addFilesToFolder', $addFilesToFolder);
            ?>
        </div>
    </fieldset>
</div>

<script type="text/template" data-template="express-form-form-control">
<li class="list-group-item"
    data-action="<?=$view->action('get_control'); ?>"
    data-form-control-field-type="<%=control.attributeType%>"
    data-form-control-label="<%=control.displayLabel%>"
    data-form-control-id="<%=control.id%>">
    <input type="hidden" name="controlID[]" value="<%=control.id%>">
    <%=control.displayLabel%>
    <span class="pull-right">
        <i style="cursor: move" class="fa fa-arrows"></i>
        <a href="javascript:void(0)" class="icon-link" data-action="edit-control"><i class="fa fa-pencil"></i></a>
        <a href="javascript:void(0)" class="icon-link" data-action="delete-control"><i class="fa fa-trash"></i></a>
        </span>
    <% if (control.isRequired) { %>
    <span style="margin-right: 20px" class="badge badge-info"><?=t('Required'); ?></span>
    <% } %>
</li>
</script>

<script type="text/template" data-template="express-form-reply-to-email">
    <div class="form-group">
        <?=$form->label('replyToEmailControlID', t('Set value of Reply-To to Email Field')); ?>
        <select name="replyToEmailControlID" class="form-control">
            <option value=""><?=t('** None'); ?></option>
            <% _.each(controls, function(control){ %>
            <option value="<%=control.key%>" <% if (selected == control.key) { %>selected<% } %>><%=_.escape(control.value)%></option>
            <% }); %>
        </select>
    </div>
</script>

<script type="text/template" data-template="express-form-form-question">

    <% if (id) { %>
        <input type="hidden" name="id" value="<%=id%>">
    <% } %>

    <div class="form-group" data-action="<?=$view->action('get_type_form'); ?>" data-group="field-types">
        <?=$form->label('type', t('Answer Type')); ?>

        <% if (!id) { %>
            &nbsp; <i class="fa fa-refresh fa-spin" style="display: none"></i>
            <select name="type" class="form-control">
                <option value=""><?=t('** Choose Field'); ?></option>
            <% _.each(types, function(group) { %>
                <optgroup label="<%=group.label%>">
                    <% _.each(group.fields, function(type) { %>
                        <option value="<%=type.id%>" <% if (selectedType == type.id) { %>selected<% } %>><%=_.escape(type.displayName)%></option>
                    <% }); %>
                </optgroup>
            <% }); %>
            </select>
        <% } else { %>
            <input type="hidden" name="type" value="<%=selectedType%>">
            <div><strong><%=selectedTypeDisplayName%></strong></div>
        <% } %>
    </div>

    <div class="form-group" data-group="control-name" style="display: none">
        <?=$form->label('question', t('Question')); ?>
        <input type="text" name="question" class="form-control" maxlength="255" value="<%=question%>">
    </div>

    <% if (typeContent) { %>
        <div data-group="field-type-data"><%=typeContent%></div>
    <% } else { %>
        <div style="display: none" data-group="field-type-data"></div>
    <% } %>

    <div class="form-group" data-group="control-required" style="display: none">
        <label class="control-label"><?=t('Required'); ?></label>
        <div class="radio"><label>
            <input type="radio" name="required<% if (id) { %>Edit<% } %>" value="1" <% if (isRequired) { %>checked<% } %>>
            <?=t('Yes'); ?>
        </label></div>
        <div class="radio"><label>
            <input type="radio" name="required<% if (id) { %>Edit<% } %>" value="0" <% if (!isRequired) { %>checked<% } %>>
            <?=t('No'); ?>
        </label></div>
    </div>
</script>


<script type="application/javascript">
    Concrete.event.publish('open.block_express_form', {
        task: '<?=$controller->getTask(); ?>',
        <?php if ('edit' == $controller->getTask()) {
                ?>
            <?php if (isset($expressEntity) && is_object($expressEntity) && $expressEntity->getIncludeInPublicList()) {
                    ?>
                mode: 'existing',
            <?php
                } else {
                    ?>
                mode: 'new',
            <?php
                } ?>
        <?php
            } ?>
        controls: <?=json_encode($controls);?>,
        types: <?=json_encode($types_select);?>,
        settings: {
            'replyToEmailControlID': <?=json_encode($replyToEmailControlID);?>
        }
    });
</script>