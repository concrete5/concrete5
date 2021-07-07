<?php
defined('C5_EXECUTE') or die("Access Denied.");

$nav = Loader::helper('navigation');

if (count($sections) > 0) {
    ?>

    <form role="form" action="<?= $controller->action('view') ?>" data-form="search-multilingual-pages" class="ccm-search-fields">
        <input type="hidden" name="sectionID" value="<?= $sectionID ?>" />
        <div class="ccm-search-fields-row">
            <div class="form-group">
                <?= $form->label('keywords', t('Search')) ?>
                <div class="ccm-search-field-content row">
                    <div class="input-group col-md-11">
                        <?= $form->search('keywords', array('placeholder' => t('Keywords'))) ?>
                        <span class="input-group-text ccm-search-main-lookup-field"><i class="fa fa-search"></i></span>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary" tabindex="-1"><?= t('Search') ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="ccm-search-fields-row">
            <div class="form-group">
                <label class="control-label form-label" for="sectionIDSelect"><?= t('Choose Source') ?></label>
                <div class="ccm-search-field-content">
                    <?= $form->select('sectionIDSelect', $sections, $sectionID) ?>
                </div>
            </div>
        </div>
        <div class="ccm-search-fields-row" data-list="multilingual-targets">
            <div class="form-group">
                <label class="control-label form-label"><?= t('Choose Targets') ?></label>
                <div class="ccm-search-field-content">
                    <?php
                    foreach ($sectionList as $sc) {
                        $args = array('style' => 'vertical-align: middle');
                        if ($sectionID == $sc->getCollectionID()) {
                            $args['disabled'] = 'disabled';
                        }
                        ?>
                        <div class="form-check form-check-inline">
                            <?= $form->checkbox('targets[' . $sc->getCollectionID() . ']', $sc->getCollectionID(), in_array($sc->getCollectionID(), $targets), $args) ?>
                            <label class="form-check-label">
                                <?= $fh->getSectionFlagIcon($sc) ?>
                                <?= $sc->getLanguageText(). " (".$sc->getLocale().")" ?>
                            </label>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="ccm-search-fields-row">
            <div class="form-group">
                <label><?= t('Display') ?></label>
                <div class="ccm-search-field-content">
                    <div class="form-check form-check-inline">
                        <?= $form->radio('showAllPages', 0, 0) ?>
                        <label class="form-check-label">
                            <?= t('Only Missing Pages') ?>
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <?= $form->radio('showAllPages', 1, false) ?>
                        <label class="form-check-label">
                            <?= t('All Pages') ?>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="ccm-search-fields-submit">
            <button type="submit" class="btn btn-primary float-end"><?= t('Search') ?></button>
        </div>
    </form>

    <?php
    if (count($sections) > 1) {
            $width = 100 / count($sections);
    } else {
        $width = '100';
    }
    $sourceMS = Concrete\Core\Multilingual\Page\Section\Section::getByID($sectionID);
    ?>
    <div class="table-responsive pt-2">
        <table class="ccm-search-results-table">
            <thead>
                <tr>
                    <th style="width: <?= $width ?>%">
                        <span><?= t('%s (%s)', $sourceMS->getLanguageText(), $sourceMS->getLocale()) ?></span>
                    </th>
                    <?php
                    foreach ($targetList as $sc) {
                        if ($section->getCollectionID() != $sc->getCollectionID()) {
                            ?>
                            <th style="width:<?= $width ?>%">
                                <span><?= $fh->getSectionFlagIcon($sc) . '&nbsp;' . t('%s (%s)', $sc->getLanguageText(), $sc->getLocale()) ?></span>
                            </th>
                            <?php
                        }
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($pages) > 0) {
                    foreach ($pages as $pc) {
                        ?>
                        <tr>
                            <td>
                                <a href="<?= $pc->getCollectionLink() ?>"><?= $pc->getCollectionName() ?></a>
                                <div><small><?= $pc->getCollectionPath() ?></small></div>
                            </td>
                            <?php
                            foreach ($targetList as $sc) {
                                $multilingualController = Core::make('\Concrete\Controller\Backend\Page\Multilingual');
                                $multilingualController->setPageObject($pc);
                                if ($section->getCollectionID() != $sc->getCollectionID()) {
                                    ?>
                                    <td>
                                        <div data-multilingual-page-section="<?= $sc->getCollectionID() ?>" data-multilingual-page-source="<?= $pc->getCollectionID() ?>">
                                            <div data-wrapper="page">
                                                <?php
                                                $cID = $sc->getTranslatedPageID($pc);
                                                if ($cID) {
                                                    $p = Page::getByID($cID);
                                                    echo '<a href="' . $nav->getLinkToCollection($p) . '">' . $p->getCollectionName() . '</a>';
                                                } elseif ($cID === '0') {
                                                    echo t('Ignored');
                                                }
                                                ?>
                                            </div>
                                            <div data-wrapper="buttons">
                                                <?php
                                                $cParentID = $pc->getCollectionParentID();
                                                $cParent = Page::getByID($cParentID);
                                                $cParentRelatedID = $sc->getTranslatedPageID($cParent);
                                                if ($cParentRelatedID) {
                                                    $assignLang = t('Re-Map');
                                                    if (!$cID) {
                                                        $assignLang = t('Map');
                                                    }
                                                    if (!$cID) {
                                                        ?>
                                                        <button class="btn btn-success btn-xs" type="button"
                                                            data-btn-action="create"
                                                            data-btn-url="<?= $multilingualController->action('create_new') ?>"
                                                            data-btn-multilingual-page-source="<?= $pc->getCollectionID() ?>"
                                                            data-btn-multilingual-section="<?= $sc->getCollectionID() ?>"
                                                        ><?= t('Create Page') ?></button>
                                                        <?php
                                                    }
                                                    ?>
                                                    <button class="btn btn-info btn-xs" type="button"
                                                        data-btn-action="map"
                                                        data-btn-url="<?= $multilingualController->action('assign') ?>"
                                                        data-btn-multilingual-page-source="<?= $pc->getCollectionID() ?>"
                                                        data-btn-multilingual-section="<?= $sc->getCollectionID() ?>"
                                                    ><?= $assignLang ?></button>
                                                    <?php
                                                    if ($cID !== '0' && !$cID) {
                                                        ?>
                                                        <button class="btn btn-warning btn-xs" type="button"
                                                            data-btn-action="ignore"
                                                            data-btn-url="<?= $multilingualController->action('ignore') ?>"
                                                            data-btn-multilingual-page-source="<?= $pc->getCollectionID() ?>"
                                                            data-btn-multilingual-section="<?= $sc->getCollectionID() ?>"
                                                        ><?= t('Ignore') ?></button>
                                                        <?php
                                                    }
                                                    if ($cID) {
                                                        ?>
                                                        <button class="btn btn-danger btn-xs" type="button"
                                                            data-btn-action="unmap"
                                                            data-btn-url="<?= $multilingualController->action('unmap') ?>"
                                                            data-btn-multilingual-page-source="<?= $pc->getCollectionID() ?>"
                                                            data-btn-multilingual-section="<?= $sc->getCollectionID() ?>"
                                                        ><?= t('Un-Map') ?></button>
                                                        <?php
                                                    }
                                                } else {
                                                    ?><div class="ccm-note"><?= t("Create the parent page first.") ?></div><?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </td>
                                    <?php
                                }
                            }
                            ?>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="4"><?= t('No pages found.') ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
    function replaceLinkWithPage(cID, sectionID, link, icon, name) {
        var $wrapper = $('div[data-multilingual-page-section=' + sectionID + '][data-multilingual-page-source=' + cID + ']');
        var newLink = '<a href="' + link + '">' + name + '<\/a>';
        $wrapper.find('div[data-wrapper=page]').html(newLink);
        $wrapper.find('div[data-wrapper=buttons]').hide();
    }
    $(function() {
        $("select[name=sectionIDSelect]").change(function() {
            $("div[data-list=multilingual-targets] input").attr('disabled', false);
            $("div[data-list=multilingual-targets] input[value=" + $(this).val() + "]").attr('disabled', true).attr('checked', false);
            $("input[name=sectionID]").val($(this).val());
            $("form[data-form=multilingual-search-pages]").submit();
        });
        $('button[data-btn-action=create]').on('click', function(e) {
            var sectionID = $(this).attr('data-btn-multilingual-section'),
                cID = $(this).attr('data-btn-multilingual-page-source');
            e.preventDefault();
            $.concreteAjax({
                url: $(this).attr('data-btn-url'),
                method: 'post',
                data: {
                    'section': sectionID,
                    'cID': cID
                },
                success: function(r) {
                    ConcreteAlert.notify({
                        'message': r.message,
                        'title': r.title
                    });
                    if (r.pages[0]) {
                        replaceLinkWithPage(cID, sectionID, r.link, r.icon, r.name);
                    }
                }
            });
        });
        $('button[data-btn-action=map]').on('click', function(e) {
            var sectionID = $(this).attr('data-btn-multilingual-section'),
                cID = $(this).attr('data-btn-multilingual-page-source'),
                url = $(this).attr('data-btn-url');
            e.preventDefault();
            ConcretePageAjaxSearch.launchDialog(function(data) {
                $.concreteAjax({
                    url: url,
                    method: 'post',
                    data: {
                        'destID': data.cID,
                        'cID': cID
                    },
                    success: function(r) {
                        replaceLinkWithPage(cID, sectionID, r.link, r.icon, r.name);
                    }
                });
            });
        });
        $('button[data-btn-action=ignore]').on('click', function(e) {
            var sectionID = $(this).attr('data-btn-multilingual-section'),
                cID = $(this).attr('data-btn-multilingual-page-source');
            e.preventDefault();
            $.concreteAjax({
                url: $(this).attr('data-btn-url'),
                method: 'post',
                data: {
                    'section': sectionID,
                    'cID': cID
                },
                success: function(r) {
                    var $wrapper = $('div[data-multilingual-page-section=' + sectionID + '][data-multilingual-page-source=' + cID + ']');
                    $wrapper.find('div[data-wrapper=page]').html(<?= json_encode(t('Ignored')) ?>);
                    $wrapper.find('div[data-wrapper=buttons]').hide();
                }
            });
        });
        $('button[data-btn-action=unmap]').on('click', function(e) {
            var sectionID = $(this).attr('data-btn-multilingual-section'),
              cID = $(this).attr('data-btn-multilingual-page-source');
            e.preventDefault();
            $.concreteAjax({
                url: $(this).attr('data-btn-url'),
                method: 'post',
                data: {
                    'section': sectionID,
                    'cID': cID
                },
                success: function(r) {
                    var $wrapper = $('div[data-multilingual-page-section=' + sectionID + '][data-multilingual-page-source=' + cID + ']');
                    $wrapper.find('div[data-wrapper=page]').html(<?= json_encode(t('Unmapped')) ?>);
                    $wrapper.find('div[data-wrapper=buttons]').hide();
                }
            });
        });
    });
    </script>
    <div class="ccm-search-results-pagination">
        <?php
        if ($pagination->haveToPaginate()) {
            echo $pagination->renderView('dashboard');
        }
        ?>
    </div>
    <?php
} else {
    ?>
    <p><?= t('You have not defined any multilingual sections for your site yet.') ?></p>
    <?php
}
