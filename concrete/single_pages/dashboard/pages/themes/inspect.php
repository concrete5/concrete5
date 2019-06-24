<?php
defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Core\Page\Theme\File as PageThemeFile;

// HELPERS
$ci = Loader::helper('concrete/ui');

?>


    <form method="post" id="ccm-inspect-form" action="<?=$view->url('/dashboard/pages/themes/inspect/', 'activate_files', $pThemeID)?>">
    
    	<p class="lead"><?=t("%s Theme", $pageTheme->getThemeDisplayName())?></p>

        <div class="row">
            <div class="col-sm-2">
                <?=$pageTheme->getThemeThumbnail()?>
            </div>

            <div class="col-sm-10">
                <table border="0" cellspacing="0" cellpadding="0" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th><?=t('File')?></th>
                            <th><?=t('Type')?></th>
                            <th><?=t('Action to take')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    <?php
                    $txt = Loader::helper('text');
                    $pf = 0;

                        if (count($files) == 0) {
                            ?>
                        <tr>
                            <td colspan="3">
                                <?=t('There are no templates in this file.')?>
                            </td>
                        </tr>
                        <?php 
                        }

                        foreach ($files as $f) {
                            ?>
                        <tr class="inputs-list">
                            <td><?=$f->getFilename()?></td>
                            <td><?php
                                switch ($f->getType()) {
                                    case PageThemeFile::TFTYPE_VIEW:
                                        print t("Wrapper for static pages.");
                                        break;
                                    case PageThemeFile::TFTYPE_PAGE_CLASS:
                                        print t("Custom page class file.");
                                        break;
                                    case PageThemeFile::TFTYPE_DEFAULT:
                                        print t("Default template.");
                                        break;
                                    case PageThemeFile::TFTYPE_SINGLE_PAGE:
                                        print t("Template for internal concrete5 page.");
                                        break;
                                    case PageThemeFile::TFTYPE_PAGE_TEMPLATE_NEW:
                                        print t("New template.");
                                        break;
                                    case PageThemeFile::TFTYPE_PAGE_TEMPLATE_EXISTING:
                                        print t("Existing page template.");
                                        break;
                                }
                                // END Switch
                            ?>
                            </td>
                            <td><?php
                                switch ($f->getType()) {
                                    case PageThemeFile::TFTYPE_VIEW:
                                        print t('None. This file will automatically be used.');
                                        break;
                                    case PageThemeFile::TFTYPE_DEFAULT:
                                        print t('None. This file will automatically be used for pages without a template.');
                                        break;
                                    case PageThemeFile::TFTYPE_SINGLE_PAGE:
                                        print t('None. This file will automatically be used by the <strong>%s</strong> page.', $txt->unhandle($f->getHandle()));
                                        break;
                                    case PageThemeFile::TFTYPE_PAGE_TEMPLATE_NEW:
                                        $pf++;
                                        echo '<label><input type="checkbox" value="' . $f->getHandle() . '" name="pageTemplates[]" checked /> <span>'.t('Create page template.').'</span></label>';
                                        break;
                                    case PageThemeFile::TFTYPE_PAGE_TEMPLATE_EXISTING:
                                        print t('None. This file will be used by pages with the <strong>%s</strong> template.', $txt->unhandle($f->getHandle()));
                                        break;
                                }
                                // END Switch
                            ?></td>
                        </tr>
                        
                        <?php 
                        } // END FOREACH ?>
                    
                    </tbody>
                </table>        
            </div>
        
        </div>
	

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
        <?php
        echo $ci->button(t('Return to Themes'), $view->url('/dashboard/pages/themes'), 'left');
        if ($pf > 0) {
            echo $ci->submit(t('Ok'), 'ccm-inspect-form', 'right', 'btn-primary');
            ?>
        <?php 
        }?>
        </div>
    </div>

    </form>
