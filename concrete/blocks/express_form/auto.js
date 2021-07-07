$(function() {

    function ConcreteBlockForm(data) {
        'use strict';
        this.data = data;
        this.setupEvents(data);
        this.init(data);
    }


    ConcreteBlockForm.prototype.setupEvents = function(data) {
        var my = this,
            $chooseContainer = $('#ccm-block-express-form-choose-type'),
            $tabsContainer = $('#ccm-block-express-form-tabs');

        Concrete.event.unbind('add_control.block_express_form');
        Concrete.event.bind('add_control.block_express_form', function(e, data) {

            var $tabEdit = $('#ccm-block-express-form-edit'),
                $list = $tabEdit.find('ul');
            $list.append(data.controlTemplate({'control': data.control}));

            my.rescanEmailFields();


        });

        Concrete.event.unbind('update_control.block_express_form');
        Concrete.event.bind('update_control.block_express_form', function(e, data) {

            var $tabEdit = $('#ccm-block-express-form-edit'),
                $control = $tabEdit.find('[data-form-control-id=' + data.control.id + ']');
            if ($control) {
                $control.replaceWith(data.controlTemplate({'control': data.control}));
            }

            my.rescanEmailFields();

        });

        if (data.task == 'add') {
            $tabsContainer.hide();
        } else {
            my.chooseFormType(data.mode);
        }

        $chooseContainer.find('button').on('click', function() {
            var action = $(this).attr('data-action');
            if (action == 'choose-new-form') {
                my.chooseFormType('new');
            } else {
                my.chooseFormType('existing');
            }
        });
    }

    ConcreteBlockForm.prototype.chooseFormType = function(mode) {
        var my = this,
            $chooseContainer = $('#ccm-block-express-form-choose-type'),
            $tabsContainer = $('#ccm-block-express-form-tabs');

        switch(mode) {
            case 'new':
                $tabsContainer.find('li').eq(2).remove();
                break;
            case 'existing':
                $tabsContainer.find('li').eq(0).remove();
                $tabsContainer.find('li').eq(0).remove();
                break;
        }

        const firstTab = new bootstrap.Tab($tabsContainer.find('li').eq(0).find('a'))
        firstTab.show()

        $tabsContainer.show();
        $chooseContainer.hide();
    }

    ConcreteBlockForm.prototype.destroyContents = function($element) {
        if (typeof CKEDITOR != 'undefined') {
            for (name in CKEDITOR.instances) {
                var instance = CKEDITOR.instances[name];
                if ($.contains($element.get(0), instance.container.$)) {
                    instance.destroy(true);
                }
            }
        }
        $element.children().remove().hide();
    }

    ConcreteBlockForm.prototype.init = function(data) {

        var my = this,
            $tabAdd = $('#ccm-block-express-form-add'),
            $tabEdit = $('#ccm-block-express-form-edit'),
            $tabResults = $('#ccm-block-express-form-results'),
            $tabOptions = $('#ccm-block-express-form-options'),
            $storeFormSubmission = $tabResults.find('input[name=storeFormSubmission]'),
            $formResultsFolderSection = $tabResults.find('div[data-section=form-results-folder]'),
            controlTemplate = _.template($('script[data-template=express-form-form-control]').html()),
            questionTemplate = _.template($('script[data-template=express-form-form-question]').html());

        $tabOptions.on('change', 'input[name=notifyMeOnSubmission]', function() {
            var $emailReplyToView = $('#ccm-block-express-form-options div[data-view=form-options-email-reply-to]');
            $('input[name=recipientEmail]').focus();
            if ($(this).is(':checked')) {
                $emailReplyToView.show();
            } else {
                $emailReplyToView.hide();
            }
        }).trigger('change');

        $storeFormSubmission.on('change', function() {
            var $submissionAlert = $tabResults.find('div.alert');
            if (!$(this).is(':checked')) {
                $submissionAlert.show();
                $formResultsFolderSection.hide();
            } else {
                $submissionAlert.hide();
                $formResultsFolderSection.show();
            }
        }).trigger('change');


        $tabAdd.find('div[data-view=add-question-inner]').html(questionTemplate({
            'question': '',
            'id': null,
            'isRequired': false,
            'selectedType': '',
            'types': data.types,
            'typeContent': null
        }));

        var $types = $('div[data-group=field-types]'),
            $typeData = $('div[data-group=field-type-data]'),
            $controlName = $('div[data-group=control-name]'),
            $controlRequired = $('div[data-group=control-required]'),
            $addQuestionGroup = $('div[data-group=add-question]');

        $tabAdd.on('click', 'button[data-action=add-question]', function() {
            var $form = $tabAdd.find(':input');
            var data = $form.serializeArray();
            jQuery.fn.dialog.showLoader();
            $.concreteAjax({
                url: $tabAdd.attr('data-action'),
                data: data,
                success: function(r) {
                    $types.find('option').eq(0).prop('selected', true);
                    $types.find('select').trigger('change');
                    $types.closest('.ui-dialog-content').scrollTop(0);
                    $tabAdd.find('input[name=question]').val('');
                    $tabAdd.find('input[name=required][value=0]').prop('checked', true);
                    $tabAdd.find('div.alert-success').show().addClass("animated fadeIn");
                    Concrete.event.publish('add_control.block_express_form', {
                        'control': r,
                        'controlTemplate': controlTemplate
                    });

                    setTimeout(function() {
                        $tabAdd.find('div.alert-success').hide();
                    }, 2000);
                }
            });
        });

        $tabEdit.on('click', 'button[data-action=update-question]', function() {
            var $form = $tabEdit.find(':input');
            var data = $form.serializeArray();
            jQuery.fn.dialog.showLoader();
            $.concreteAjax({
                url: $tabEdit.attr('data-action'),
                data: data,
                success: function(r) {
                    var $fields = $tabEdit.find('[data-view=form-fields]'),
                        $editQuestion = $tabEdit.find('[data-view=edit-question]'),
                        $editQuestionInner = $tabEdit.find('[data-view=edit-question-inner]');

                    $tabEdit.find('div.alert-success').show().addClass("animated fadeIn");

                    $editQuestion.hide();
                    $fields.show();
                    my.destroyContents($editQuestionInner);


                    Concrete.event.publish('update_control.block_express_form', {
                        'control': r,
                        'controlTemplate': controlTemplate
                    });

                    setTimeout(function() {
                        $tabEdit.find('div.alert-success').hide();
                    }, 2000);
                }
            });
        });


        $tabEdit.on('click', 'a[data-action=delete-control]', function() {
            $(this).closest('li').queue(function() {
                $(this).addClass('animated bounceOutLeft');
                $(this).dequeue();
            }).delay(500).queue(function () {
                $(this).remove();
                my.rescanEmailFields();
                $(this).dequeue();
            })
        });

        $tabEdit.on('click', 'button[data-action=cancel-edit]', function() {
            var $fields = $tabEdit.find('[data-view=form-fields]'),
                $editQuestion = $tabEdit.find('[data-view=edit-question]'),
                $editQuestionInner = $tabEdit.find('[data-view=edit-question-inner]');

            $editQuestion.hide();
            my.destroyContents($editQuestionInner);
            $fields.show();
        });

        $tabEdit.on('click', 'a[data-action=edit-control]', function() {
            var $control = $(this).closest('[data-form-control-id]');
            $.concreteAjax({
                url: $control.attr('data-action'),
                type: 'get',
                data: {'control': $control.attr('data-form-control-id')},
                success: function(r) {
                    var $fields = $tabEdit.find('[data-view=form-fields]'),
                        $editQuestion = $tabEdit.find('[data-view=edit-question]'),
                        $editQuestionInner = $tabEdit.find('[data-view=edit-question-inner]');

                    $fields.hide();

                    _.each(r.assets.css, function(css) {
                        ConcreteAssetLoader.loadCSS(css);
                    });
                    _.each(r.assets.javascript, function(javascript) {
                        ConcreteAssetLoader.loadJavaScript(javascript);
                    });

                    $editQuestionInner.html(questionTemplate({
                        'id': r.id,
                        'question': r.question,
                        'isRequired': r.isRequired,
                        'selectedType': r.type,
                        'selectedTypeDisplayName': r.typeDisplayName,
                        'types': data.types,
                        'typeContent': r.typeContent
                    }));

                    $editQuestion.show();

                    if (r.showControlName) {
                        $editQuestion.find('div[data-group=control-name]').show();
                    }
                    if (r.showControlRequired) {
                        $editQuestion.find('div[data-group=control-required]').show();
                    }
                }
            });
        });

        $tabEdit.find('ul').sortable({
            placeholder: "ui-state-highlight",
            axis: "y",
            handle: "i.fa-arrows-alt",
            cursor: "move",
            update: function() {

            }
        });

        $types.find('select').on('change', function() {
            my.destroyContents($typeData);
            $controlName.hide();
            $controlRequired.hide();
            $addQuestionGroup.hide();
            var value = $(this).val();
            if (value) {
                $types.find('i.fa-refresh').show();
                $.concreteAjax({
                    url: $types.attr('data-action'),
                    data: {'id': value},
                    loader: false,
                    success: function(r) {
                        _.each(r.assets.css, function(css) {
                            ConcreteAssetLoader.loadCSS(css);
                        });
                        _.each(r.assets.javascript, function(javascript) {
                            ConcreteAssetLoader.loadJavaScript(javascript);
                        });
                        if (r.showControlName) {
                            $controlName.show();
                        }
                        if (r.showControlRequired) {
                            $controlRequired.show();
                        }
                        $typeData.html(r.content);
                        $typeData.show();
                        $addQuestionGroup.show();
                    },
                    complete: function() {
                        $types.find('i.fa-refresh').hide();
                    }
                });
            }
        });

        if (data.controls) {
            _.each(data.controls, function(control) {
                Concrete.event.publish('add_control.block_express_form', {
                    'control': control,
                    'controlTemplate': controlTemplate
                });
            });
        }

        $('[data-tree]').each(function() {
            $(this).concreteTree({
                ajaxData: {
                    displayOnly: 'express_entry_category'
                },
                treeNodeParentID: $(this).attr('data-root-tree-node-id'),
                selectNodesByKey: [$('input[name=resultsFolder]').val()],
                onSelect : function(nodes) {
                    if (nodes.length) {
                        $('input[name=resultsFolder]').val(nodes[0]);
                    } else {
                        $('input[name=resultsFolder]').val('');
                    }
                },
                chooseNodeInForm: 'single'
            });
        });


    }

    ConcreteBlockForm.prototype.rescanEmailFields = function($emailReplyToView) {
        // Gather all the email controls
        var $tabEdit = $('#ccm-block-express-form-edit'),
            $emailReplyToView = $('#ccm-block-express-form-options div[data-view=form-options-email-reply-to]'),
            emailReplyToTemplate = _.template($('script[data-template=express-form-reply-to-email]').html()),
            $controls = $tabEdit.find('li[data-form-control-id]'),
            selected = $emailReplyToView.find('select[name=replyToEmailControlID]').val(),
            controls = [];

        if (!selected) {
            selected = this.data.settings.replyToEmailControlID;
        }
        $controls.each(function() {
            var $control = $(this);
            if ($control.attr('data-form-control-field-type') == 'email') {
                controls.push({
                   key: $control.attr('data-form-control-id'),
                   value: $control.attr('data-form-control-label')
               });
            }
        });

        if (!controls.length) {
            $emailReplyToView.html('');
        } else {
            $emailReplyToView.html(emailReplyToTemplate({'controls': controls, 'selected': selected}));
        }
    }


    Concrete.event.bind('open.block_express_form', function(e, data) {

        var form = new ConcreteBlockForm(data);

    });


});
