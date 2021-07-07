<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<script type="text/template" class="attribute">
    <div class="form-group <% if (pending) { %>ccm-page-attribute-adding<% } %>" data-attribute-key-id="<%=akID%>">
        <a href="javascript:void(0)" data-remove-attribute-key="<%=akID%>"><i class="fa fa-minus-circle"></i></a>
        <label class="col-form-label" for="<%=controlID%>"><%=label%></label>
        <div>
            <%=content%>
        </div>
        <input type="hidden" name="selectedAKIDs[]" value="<%=akID%>"/>
    </div>
</script>

<div id="ccm-detail-page-attributes">

    <section class="ccm-ui">
        <form method="post" action="<?= $controller->action('submit') ?>" data-dialog-form="attributes"
              data-panel-detail-form="attributes" data-action-after-save="reload">

            <?php if (isset($sitemap) && $sitemap) {
                ?>
                <input type="hidden" name="sitemap" value="1"/>
                <?php
            } ?>

            <span class="ccm-detail-page-attributes-id text-muted"><?= t('Page ID: %s', $c->getCollectionID()) ?></span>
            <h3>Attributes</h3>

            <?php if ($assignment->allowEditName()) {
                ?>
                <div class="form-group">
                    <label for="cName" class="col-form-label"><?= t('Name') ?></label>
                    <div>
                        <input type="text" class="form-control" id="cName" name="cName"
                               value="<?= htmlentities($c->getCollectionName(), ENT_QUOTES, APP_CHARSET) ?>"/>
                    </div>
                </div>
                <?php
            } ?>

            <?php if ($assignment->allowEditDateTime()) {
                ?>
                <div class="form-group">
                    <label for="cDatePublic" class="col-form-label"><?= t('Created Time') ?></label>
                    <div>
                        <?= $dt->datetime('cDatePublic', $c->getCollectionDatePublic()) ?>
                    </div>
                </div>
                <?php
            } ?>

            <?php if ($assignment->allowEditUserID()) {
                ?>
                <div class="form-group">
                    <label for="uID" class="col-form-label"><?= t('Author') ?></label>
                    <div>
                        <?= $uh->selectUser('uID', $c->getCollectionUserID()) ?>
                    </div>
                </div>
                <?php
            } ?>


            <?php if ($assignment->allowEditDescription()) {
                ?>
                <div class="form-group">
                    <label for="cDescription" class="col-form-label"><?= t('Description') ?></label>
                    <div>
                        <textarea id="cDescription" name="cDescription" class="form-control"
                                  rows="8"><?= htmlentities($c->getCollectionDescription(), ENT_QUOTES, APP_CHARSET) ?></textarea>
                    </div>
                </div>
                <?php
            } ?>

        </form>
        <div class="ccm-panel-detail-form-actions dialog-buttons">
            <button class="float-start btn btn-secondary" type="button" data-dialog-action="cancel"
                    data-panel-detail-action="cancel"><?= t('Cancel') ?></button>
            <button class="float-end btn btn-primary" type="button" data-dialog-action="submit"
                    data-panel-detail-action="submit"><?= t('Save Changes') ?></button>
        </div>

    </section>
</div>

<script type="text/javascript">

    var renderAttribute = _.template(
        $('script.attribute').html()
    );

    ConcretePageAttributesDetail = {

        removeAttributeKey: function (akID) {
            var $attribute = $('div[data-attribute-key-id=' + akID + ']');
            $attribute.queue(function () {
                $(this).addClass('ccm-page-attribute-removing');
                ConcreteMenuPageAttributes.deselectAttributeKey(akID);
                $(this).dequeue();
            }).delay(150).queue(function () {
                if (typeof CKEDITOR != 'undefined') {
                    for (name in CKEDITOR.instances) {
                        var instance = CKEDITOR.instances[name];
                        if ($.contains($(this).get(0), instance.container.$)) {
                            instance.destroy(true);
                        }
                    }
                }
                $(this).remove();
                $(this).dequeue();
            });
        },

        addAttributeKey: function (akID) {
            jQuery.fn.dialog.showLoader();
            $.ajax({
                url: '<?=$controller->action("add_attribute")?>',
                dataType: 'json',
                data: {
                    'akID': akID
                },
                type: 'get',
                success: function (r) {
                    _.each(r.assets.css, function (css) {
                        ConcreteAssetLoader.loadCSS(css);
                    });
                    _.each(r.assets.javascript, function (javascript) {
                        ConcreteAssetLoader.loadJavaScript(javascript);
                    });

                    var $form = $('form[data-panel-detail-form=attributes]');
                    $form.append(
                        renderAttribute(r)
                    );
                    $form.delay(1).queue(function () {
                        $('[data-attribute-key-id=' + r.akID + ']').removeClass('ccm-page-attribute-adding');
                        $(this).dequeue();
                    });
                },
                complete: function () {
                    jQuery.fn.dialog.hideLoader();
                    $('#ccm-panel-detail-page-attributes').scrollTop(100000000);
                }
            });
        }
    };

    $(function () {

        var $form = $('form[data-panel-detail-form=attributes]');
        var selectedAttributes = <?=$selectedAttributes?>;
        _.each(selectedAttributes, function (attribute) {
            $form.append(renderAttribute(attribute));
        });
        $form.on('click', 'a[data-remove-attribute-key]', function () {
            var akID = $(this).attr('data-remove-attribute-key');
            ConcretePageAttributesDetail.removeAttributeKey(akID);
        });

        $(function () {
            ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.saveAttributes');
            ConcreteEvent.subscribe('AjaxFormSubmitSuccess.saveAttributes', function (e, data) {
                if (data.form == 'attributes') {
                    ConcreteToolbar.disableDirectExit();
                    ConcreteEvent.publish('SitemapUpdatePageRequestComplete', {'cID': data.response.cID});
                }
            });
        });

    });

</script>
