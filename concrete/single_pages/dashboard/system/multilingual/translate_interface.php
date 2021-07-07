<?php use Concrete\Core\Url\Url;

defined('C5_EXECUTE') or die('Access Denied.');

$app = Concrete\Core\Support\Facade\Application::getFacadeApplication();

$valt = $app->make('helper/validation/token');

if ($this->controller->getTask() == 'translate_po') {
    /** @var string $textDirection */
    $url = Url::createFromUrl($this->controller->action('save_translation'));
    $url = $url->setQuery([
        'ccm_token' => $app->make('token')->generate('translate/save'),
    ]);

    /* @var $section \Concrete\Core\Multilingual\Page\Section\Section */
    ?>
    <script>
    $(document).ready(function() {
      ccmTranslator.initialize({
        container: '#ccm-translator-interface',
        height: $(window).height() - 300,
        saveAction: <?php echo json_encode((string) $url); ?>,
        plurals: <?php echo json_encode($section->getPluralsCases()); ?>,
        translations: <?php echo json_encode($translations); ?>,
        approvalSupport: false,
        textDirection: <?= json_encode(isset($textDirection) ? (string) $textDirection : '') ?>
      });
      var saveToFileToken = <?php echo json_encode($app->make('token')->generate('export_translations')); ?>;
      $('.ccm-save-to-file').on('click', function() {
        var $btn = $(this);
        $btn.addClass('disabled').css('width', $btn.outerWidth() + 'px').html('<span class="fa fa-spinner fa-spin"></span>');
        $.ajax({
          cache: false,
          dataType: 'json',
          method: 'POST',
          data: {ccm_token: saveToFileToken},
          url: <?php echo json_encode($controller->action('export_translations', $section->getLocale())); ?>
        })
        .done(function(data) {
          if (data && data.message) {
            alert(data.message);
          }
          if (data && data.newToken) {
            saveToFileToken = data.newToken;
          }
        })
        .fail(function(xhr, status, error) {
          alert(xhr.responseText || error);
        })
        .always(function() {
          $btn.removeClass('disabled').css('width', 'auto').text(<?php echo json_encode(t('Save to file')); ?>);
        });
      });
    });
    </script>
    <div id="ccm-translator-interface" class="ccm-translator"></div>

    <div class="ccm-dashboard-header-buttons">
        <a href="<?php echo $controller->action('view'); ?>" class="btn btn-secondary"><?php echo t('Back to List'); ?></a>
        <a href="javascript:void(0)" class="btn btn-primary ccm-save-to-file"><?php echo t('Save to file'); ?></a>
    </div>
    <?php

} else {
    if (!is_dir(DIR_LANGUAGES_SITE_INTERFACE) || !is_writable(DIR_LANGUAGES_SITE_INTERFACE)) {
        ?><div class="alert alert-warning"><?php echo t('You must create the directory %s and make it writable before you may run this tool. Additionally, all files within this directory must be writable.', DIRNAME_APPLICATION.'/'.DIRNAME_LANGUAGES.'/'.DIRNAME_LANGUAGES_SITE_INTERFACE); ?></div><?php

    }
    $nav = $app->make('helper/navigation');
    $pages = \Concrete\Core\Multilingual\Page\Section\Section::getList($site);
    $defaultSourceLocale = $site->getConfigRepository()->get('multilingual.default_source_locale');

    $ch = $app->make('multilingual/interface/flag');
    $dh = $app->make('helper/date');
    if (count($pages) > 0) {
        ?>
        <div class="table-responsive">
            <table class="ccm-search-results-table">
                <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th><span><?php echo t('Name'); ?></span></th>
                    <th><span><?php echo t('Locale'); ?></span></th>
                    <th colspan="2"><span><?php echo t('Completion'); ?></span></th>
                    <th><span><?php echo t('Last Updated'); ?></span></th>
                    <th></th>
                </tr>
                </thead>
                <tbody><?php
                    foreach ($pages as $pc) {
                        $pcl = \Concrete\Core\Multilingual\Page\Section\Section::getByID($pc->getCollectionID()); ?><tr>
                            <td><?php echo $ch->getSectionFlagIcon($pc); ?></td>
                            <td><a href="<?php echo $nav->getLinkToCollection($pc); ?>"><?php echo $pc->getCollectionName(); ?></a></td>
                            <td style="white-space: nowrap">
                                <?php
                                echo $pc->getLocale();
                                if ($pc->getLocale() != $defaultSourceLocale) {
                                    ?><a href="#" class="icon-link launch-tooltip" title="<?php echo REL_DIR_LANGUAGES_SITE_INTERFACE; ?>/<?php echo $pc->getLocale(); ?>.mo"><i class="fa fa-question-circle"></i></a><?php
                                }
                                ?>
                            </td>
                            <td style="width: 40%">
                                <?php
                                if ($pc->getLocale() != $defaultSourceLocale) {
                                    $data = $extractor->getSectionSiteInterfaceCompletionData($pc);
                                    ?>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo $data['completionPercentage']; ?>%">&nbsp;</div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="white-space: nowrap">
                                <?php
                                if ($pc->getLocale() != $defaultSourceLocale) {
                                    ?>
                                    <span class="percent"><?php echo $data['completionPercentage']; ?>%</span>
                                    -
                                    <?php echo t(/*i18n: %1$s is the partial number, %2$s is the total number. Example: 2 of 3 */'%1$s of %2$s', '<span class="translated">'.$data['translatedCount'].'</span>', '<span class="total">'.$data['messageCount'].'</span>'); ?>
                                    <?php
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($pc->getLocale() != $defaultSourceLocale) {
                                    if (file_exists(DIR_LANGUAGES_SITE_INTERFACE.'/'.$pc->getLocale().'.mo')) {
                                        echo $dh->formatDateTime(filemtime(DIR_LANGUAGES_SITE_INTERFACE.'/'.$pc->getLocale().'.mo'), true);
                                    } else {
                                        echo t('File not found.');
                                    }
                                } else {
                                    echo t('N/A');
                                }
                                ?>
                            </td>
                            <?php
                            if ($pc->getLocale() == $defaultSourceLocale) {
                                ?>
                                <td></td>
                                <?php
                            } else {
                                ?><td><a href="<?php echo $this->action('translate_po', $pc->getCollectionID()); ?>" class="icon-link"><i class="fas fa-pencil-alt"></i></a></td><?php
                            } ?>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php
        if (is_dir(DIR_LANGUAGES_SITE_INTERFACE) && is_writable(DIR_LANGUAGES_SITE_INTERFACE)) {
            ?>
            <form method="post" action="<?php echo $controller->action('submit'); ?>">
                <div class="ccm-dashboard-header-buttons btn-group">
                    <button class="btn btn-secondary" type="submit" name="action" value="reload"><?php echo t('Reload Strings'); ?></button>
                    <button class="btn btn-secondary" type="submit" name="action" value="export"><?php echo t('Save to file'); ?></button>
                    <?php echo $valt->output(); ?>
                    <button class="btn btn-danger" type="button" data-dialog="reset" value="reset"><?php echo t('Reset All'); ?></button>
                </div>
            </form>

            <div style="display: none">
                <div id="ccm-dialog-reset-languages" class="ccm-ui">
                    <?php
                    $u = $app->make(Concrete\Core\User\User::class);
                    if ($u->isSuperUser()) {
                        ?>
                        <form method="post" class="form-stacked" style="padding-left: 0px" action="<?php echo $view->action('reset_languages'); ?>">
                            <?php echo $app->make('helper/validation/token')->output('reset_languages'); ?>
                            <p><?php echo t('Are you sure? This will remove all translations from all languages, in the database and in your site PO files. This cannot be undone.'); ?></p>
                        </form>
                        <div class="dialog-buttons">
                            <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?php echo t('Cancel'); ?></button>
                            <button class="btn btn-danger float-end" onclick="$('#ccm-dialog-reset-languages form').submit()"><?php echo t('Confirm Reset'); ?></button>
                        </div>
                        <?php
                    } else {
                        ?>
                        <p><?php echo t('Only the admin user may reset all languages.'); ?></p>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <script type="text/javascript">
                $(function() {
                    $('button[data-dialog=reset]').on('click', function() {
                        jQuery.fn.dialog.open({
                            element: '#ccm-dialog-reset-languages',
                            modal: true,
                            width: 320,
                            title: <?php echo json_encode(t('Reset Languages')); ?>,
                            height: 'auto',
                            resizable: false
                        });
                    });
                });
            </script>
            <?php
        }
        ?>

        <style type="text/css">
            table.ccm-search-results-table div.progress {
                margin-bottom: 0px;
            }
        </style>

        <?php

    } else {
        ?>
        <p><?php echo t('You have not created any multilingual content sections yet.'); ?></p>
        <?php
    }
}
