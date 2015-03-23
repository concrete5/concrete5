<?php
namespace Concrete\Controller\Frontend;

use Controller;
use Concrete\Core\File\Type\Type as FileType;
use Concrete\Core\Localization\Localization;
use Core;
use Environment;

class AssetsLocalization extends Controller
{
    protected static function sendJavascriptHeader()
    {
        header('Content-type: text/javascript; charset='.APP_CHARSET);
    }

    public static function getCoreJavascript($setResponseHeaders = true)
    {
        if ($setResponseHeaders) {
            static::sendJavascriptHeader();
        }
        ?>
var ccmi18n = {
  expand: <?=json_encode(t('Expand'))?>,
  cancel: <?=json_encode(t('Cancel'))?>,
  collapse: <?=json_encode(t('Collapse'))?>,
  error: <?=json_encode(t('Error'))?>,
  deleteBlock: <?=json_encode(t('Block Deleted'))?>,
  deleteBlockMsg: <?=json_encode(t('The block has been removed successfully.'))?>,
  addBlock: <?=json_encode(t('Add Block'))?>,
  addBlockNew: <?=json_encode(t('Add Block'))?>,
  addBlockStack: <?=json_encode(t('Add Stack'))?>,
  addBlockStackMsg: <?=json_encode(t('The stack has been added successfully'))?>,
  addBlockPaste: <?=json_encode(t('Paste from Clipboard'))?>,
  changeAreaCSS: <?=json_encode(t('Design'))?>,
  editAreaLayout: <?=json_encode(t('Edit Layout'))?>,
  addAreaLayout: <?=json_encode(t('Add Layout'))?>,
  moveLayoutUp: <?=json_encode(t('Move Up'))?>,
  moveLayoutDown: <?=json_encode(t('Move Down'))?>,
  moveLayoutAtBoundary: <?=json_encode(t('This layout section can not be moved further in this direction.'))?>,
  areaLayoutPresets: <?=json_encode(t('Layout Presets'))?>,
  lockAreaLayout: <?=json_encode(t('Lock Layout'))?>,
  unlockAreaLayout: <?=json_encode(t('Unlock Layout'))?>,
  deleteLayout: <?=json_encode(t('Delete'))?>,
  deleteLayoutOptsTitle: <?=json_encode(t('Delete Layout'))?>,
  confirmLayoutPresetDelete: <?=json_encode(t('Are you sure you want to delete this layout preset?'))?>,
  setAreaPermissions: <?=json_encode(t('Set Permissions'))?>,
  addBlockMsg: <?=json_encode(t('The block has been added successfully.'))?>,
  updateBlock: <?=json_encode(t('Update Block'))?>,
  updateBlockMsg: <?=json_encode(t('The block has been saved successfully.'))?>,
  copyBlockToScrapbookMsg: <?=json_encode(t('The block has been added to your clipboard.'))?>,
  content: <?=json_encode(t('Content'))?>,
  closeWindow: <?=json_encode(t('Close'))?>,
  editBlock: <?=json_encode(t('Edit'))?>,
  editBlockWithName: <?=json_encode(tc('%s is a block type name', 'Edit %s'))?>,
  setPermissionsDeferredMsg: <?=json_encode(t('Permission setting saved. You must complete the workflow before this change is active.'))?>,
  editStackContents: <?=json_encode(t('Manage Stack Contents'))?>,
  compareVersions: <?=json_encode(t('Compare Versions'))?>,
  blockAreaMenu: <?=json_encode(t('Add Block'))?>,
  arrangeBlock: <?=json_encode(t('Move'))?>,
  arrangeBlockMsg: <?=json_encode(t('Blocks arranged successfully.'))?>,
  copyBlockToScrapbook: <?=json_encode(t('Copy to Clipboard'))?>,
  changeBlockTemplate: <?=json_encode(t('Custom Template'))?>,
  changeBlockCSS: <?=json_encode(t('Design'))?>,
  errorCustomStylePresetNoName: <?=json_encode(t('You must give your custom style preset a name.'))?>,
  changeBlockBaseStyle: <?=json_encode(t('Set Block Styles'))?>,
  confirmCssReset: <?=json_encode(t('Are you sure you want to remove all of these custom styles?'))?>,
  confirmCssPresetDelete: <?=json_encode(t('Are you sure you want to delete this custom style preset?'))?>,
  setBlockPermissions: <?=json_encode(t('Set Permissions'))?>,
  setBlockAlias: <?=json_encode(t('Setup on Child Pages'))?>,
  setBlockComposerSettings: <?=json_encode(t('Composer Settings'))?>,
  themeBrowserTitle: <?=json_encode(t('Get More Themes'))?>,
  themeBrowserLoading: <?=json_encode(t('Retrieving theme data from concrete5.org marketplace.'))?>,
  addonBrowserLoading: <?=json_encode(t('Retrieving add-on data from concrete5.org marketplace.'))?>,
  clear: <?=json_encode(t('Clear'))?>,
  requestTimeout: <?=json_encode(t('This request took too long.'))?>,
  generalRequestError: <?=json_encode(t('An unexpected error occurred.'))?>,
  helpPopup: <?=json_encode(t('Help'))?>,
  community: <?=json_encode(t('concrete5 Marketplace'))?>,
  communityCheckout: <?=json_encode(t('concrete5 Marketplace - Purchase & Checkout'))?>,
  communityDownload: <?=json_encode(t('concrete5 Marketplace - Download'))?>,
  noIE6: <?=json_encode(t('concrete5 does not support Internet Explorer 6 in edit mode.'))?>,
  helpPopupLoginMsg: <?=json_encode(t('Get more help on your question by posting it to the concrete5 help center on concrete5.org'))?>,
  marketplaceErrorMsg: <?=json_encode(t('<p>You package could not be installed.  An unknown error occured.</p>'))?>,
  marketplaceInstallMsg: <?=json_encode(t('<p>Your package will now be downloaded and installed.</p>'))?>,
  marketplaceLoadingMsg: <?=json_encode(t('<p>Retrieving information from the concrete5 Marketplace.</p>'))?>,
  marketplaceLoginMsg: <?=json_encode(t('<p>You must be logged into the concrete5 Marketplace to install add-ons and themes.  Please log in.</p>'))?>,
  marketplaceLoginSuccessMsg: <?=json_encode(t('<p>You have successfully logged into the concrete5 Marketplace.</p>'))?>,
  marketplaceLogoutSuccessMsg: <?=json_encode(t('<p>You are now logged out of concrete5 Marketplace.</p>'))?>,
  deleteAttributeValue: <?=json_encode(t('Are you sure you want to remove this value?'))?>,
  customizeSearch: <?=json_encode(t('Customize Search'))?>,
  properties: <?=json_encode(t('Page Saved'))?>,
  savePropertiesMsg: <?=json_encode(t('Page Properties saved.'))?>,
  saveSpeedSettingsMsg: <?=json_encode(t('Full page caching settings saved.'))?>,
  saveUserSettingsMsg: <?=json_encode(t('User Settings saved.'))?>,
  ok: <?=json_encode(t('Ok'))?>,
  scheduleGuestAccess: <?=json_encode(t('Schedule Guest Access'))?>,
  scheduleGuestAccessSuccess: <?=json_encode(t('Timed Access for Guest Users Updated Successfully.'))?>,
  newsflowLoading: <?=json_encode(t('Checking for updates.'))?>,
  x: <?=json_encode(t('x'))?>,
  user_activate: <?=json_encode(t('Activate Users'))?>,
  user_deactivate: <?=json_encode(t('Deactivate Users'))?>,
  user_delete: <?=json_encode(t('Delete'))?>,
  user_group_remove: <?=json_encode(t('Remove From Group'))?>,
  user_group_add: <?=json_encode(t('Add to Group'))?>,
  none: <?=json_encode(t('None'))?>,
  editModeMsg: <?=json_encode(t('Let\'s start editing a page.'))?>,
  editMode: <?=json_encode(t('Edit Mode'))?>,
  save: <?=json_encode(t('Save'))?>,
  currentImage: <?=json_encode(t('Current Image'))?>,
  image: <?=json_encode(t('Image'))?>,
  size: <?=json_encode(t('Size'))?>,
  chooseFont: <?=json_encode(t('Choose Font'))?>,
  fontWeight: <?=json_encode(t('Font Weight'))?>,
  italic: <?=json_encode(t('Italic'))?>,
  underline: <?=json_encode(t('Underline'))?>,
  uppercase: <?=json_encode(t('Uppercase'))?>,
  fontSize: <?=json_encode(t('Font Size'))?>,
  letterSpacing: <?=json_encode(t('Letter spacing'))?>,
  lineHeight: <?=json_encode(t('Line Height'))?>,
  emptyArea: <?=json_encode(t('Empty %s Area', '<%- area_handle %>'))?>
};

var ccmi18n_editor = {
  insertLinkToFile: <?=json_encode(t('Insert Link to File'))?>,
  insertImage: <?=json_encode(t('Insert Image'))?>,
  insertLinkToPage: <?=json_encode(t('Link to Page'))?>
};

var ccmi18n_sitemap = {
  seo: <?=json_encode(t('SEO'))?>,
  pageLocation: <?=json_encode(t('Location'))?>,
  pageLocationTitle: <?=json_encode(t('Location'))?>,
  visitExternalLink: <?=json_encode(t('Visit'))?>,
  editExternalLink: <?=json_encode(t('Edit External Link'))?>,
  deleteExternalLink: <?=json_encode(t('Delete'))?>,
  copyProgressTitle: <?=json_encode(t('Copy Progress'))?>,
  addExternalLink: <?=json_encode(t('Add External Link'))?>,
  sendToTop: <?=json_encode(t('Send To Top'))?>,
  sendToBottom: <?=json_encode(t('Send To Bottom'))?>,
  emptyTrash: <?=json_encode(t('Empty Trash'))?>,
  restorePage: <?=json_encode(t('Restore Page'))?>,
  deletePageForever: <?=json_encode(t('Delete Forever'))?>,
  previewPage: <?=json_encode(t('Preview'))?>,
  visitPage: <?=json_encode(t('Visit'))?>,
  pageAttributes: <?=json_encode(t('Attributes'))?>,
  speedSettings: <?=json_encode(t('Caching'))?>,
  speedSettingsTitle: <?=json_encode(t('Caching'))?>,
  pageAttributesTitle: <?=json_encode(t('Attributes'))?>,
  pagePermissionsTitle: <?=json_encode(t('Page Permissions'))?>,
  setPagePermissions: <?=json_encode(t('Permissions'))?>,
  setPagePermissionsMsg: <?=json_encode(t('Page permissions updated successfully.'))?>,
  pageDesignMsg: <?=json_encode(t('Theme and page type updated successfully.'))?>,
  pageDesign: <?=json_encode(t('Design &amp; Type'))?>,
  pageVersions: <?=json_encode(t('Versions'))?>,
  deletePage: <?=json_encode(t('Delete'))?>,
  deletePages: <?=json_encode(t('Delete Pages'))?>,
  deletePageSuccessMsg: <?=json_encode(t('The page has been removed successfully.'))?>,
  deletePageSuccessDeferredMsg: <?=json_encode(t('Delete request saved. You must complete the workflow before the page is fully removed.'))?>,
  addPage: <?=json_encode(t('Add Page'))?>,
  moveCopyPage: <?=json_encode(t('Move/Copy'))?>,
  reorderPage: <?=json_encode(t('Change Page Order'))?>,
  reorderPageMessage: <?=json_encode(t('Move or reorder pages by dragging their icons.'))?>,
  moveCopyPageMessage: <?=json_encode(t('Choose a new parent page from the sitemap.'))?>,
  editInComposer: <?=json_encode(t('Edit in Composer'))?>,
  searchPages: <?=json_encode(t('Search Pages'))?>,
  explorePages: <?=json_encode(t('Flat View'))?>,
  backToSitemap: <?=json_encode(t('Back to Sitemap'))?>,
  searchResults: <?=json_encode(t('Search Results'))?>,
  createdBy: <?=json_encode(t('Created By'))?>,
  choosePage: <?=json_encode(t('Choose a Page'))?>,
  viewing: <?=json_encode(t('Viewing'))?>,
  results: <?=json_encode(t('Result(s)'))?>,
  max: <?=json_encode(t('max'))?>,
  noResults: <?=json_encode(t('No results found.'))?>,
  areYouSure: <?=json_encode(t('Are you sure?'))?>,
  loadingText: <?=json_encode(t('Loading'))?>,
  loadError: <?=json_encode(t('Unable to load sitemap data. Response received: '))?>,
  loadErrorTitle: <?=json_encode(t('Unable to load sitemap data.'))?>,
  on: <?=json_encode(t('on'))?>
};

var ccmi18n_spellchecker = {
  resumeEditing: <?=json_encode(t('Resume Editing'))?>,
  noSuggestions: <?=json_encode(t('No Suggestions'))?>
};

var ccmi18n_groups = {
  editGroup: <?=json_encode(t('Edit Group'))?>,
  editPermissions: <?=json_encode(t('Edit Permissions'))?>
};

var ccmi18n_filemanager = {
  view: <?=json_encode(t('View'))?>,
  download: <?=json_encode(t('Download'))?>,
  select: <?=json_encode(t('Choose'))?>,
  duplicateFile: <?=json_encode(t('Copy File'))?>,
  clear: <?=json_encode(t('Clear'))?>,
  edit: <?=json_encode(t('Edit'))?>,
  replace: <?=json_encode(t('Replace'))?>,
  duplicate: <?=json_encode(t('Copy'))?>,
  chooseNew: <?=json_encode(t('Choose New File'))?>,
  sets: <?=json_encode(t('Sets'))?>,
  permissions: <?=json_encode(t('Permissions'))?>,
  properties: <?=json_encode(t('Properties'))?>,
  deleteFile: <?=json_encode(t('Delete'))?>,
  title: <?=json_encode(t('File Manager'))?>,
  uploadErrorChooseFile: <?=json_encode(t('You must choose a file.'))?>,
  rescan: <?=json_encode(t('Rescan'))?>,
  pending: <?=json_encode(t('Pending'))?>,
  uploadComplete: <?=json_encode(t('Upload Complete'))?>,
  uploadFailed: <?=json_encode(t('One or more files failed to upload'))?>,
  uploadProgress: <?=json_encode(t('Upload Progress'))?>,
  chosenTooMany: <?=json_encode(t('You may only select a single file.'))?>,
  PTYPE_CUSTOM: <?=json_encode(/*FilePermissions::PTYPE_CUSTOM*/ '')?>,
  PTYPE_NONE: <?=json_encode(/*FilePermissions::PTYPE_NONE*/ '')?>,
  PTYPE_ALL: <?=json_encode(/*FilePermissions::PTYPE_ALL*/ '')?>,
  FTYPE_IMAGE: <?=json_encode(FileType::T_IMAGE)?>,
  FTYPE_VIDEO: <?=json_encode(FileType::T_VIDEO)?>,
  FTYPE_TEXT: <?=json_encode(FileType::T_TEXT)?>,
  FTYPE_AUDIO: <?=json_encode(FileType::T_AUDIO)?>,
  FTYPE_DOCUMENT: <?=json_encode(FileType::T_DOCUMENT)?>,
  FTYPE_APPLICATION: <?=json_encode(FileType::T_APPLICATION)?>
};

var ccmi18n_chosen = {
  placeholder_text_multiple: <?=json_encode(t('Select Some Options'))?>,
  placeholder_text_single: <?=json_encode(t('Select an Option'))?>,
  no_results_text: <?=json_encode(t(/*i18n After this text we have a search criteria: for instance 'No results match "Criteria"'*/'No results match'))?>
};

var ccmi18n_topics = {
  addCategory: <?=json_encode(t('Add Category'))?>,
  editCategory: <?=json_encode(t('Edit Category'))?>,
  deleteCategory: <?=json_encode(t('Delete Category'))?>,
  cloneCategory: <?=json_encode(t('Clone Category'))?>,
  addTopic: <?=json_encode(t('Add Topic'))?>,
  editTopic: <?=json_encode(t('Edit Topic'))?>,
  deleteTopic: <?=json_encode(t('Delete Topic'))?>,
  cloneTopic: <?=json_encode(t('Clone Topic'))?>,
  editPermissions: <?=json_encode(t('Edit Permissions'))?>
};
<?php

    }

    public static function getSelect2Javascript($setResponseHeaders = true)
    {
        if ($setResponseHeaders) {
            static::sendJavascriptHeader();
        }
        $locale = str_replace('_', '-', Localization::activeLocale());
        if ($locale === 'en-US') {
            echo '// No needs to translate '.$locale;
        } else {
            $language = Localization::activeLanguage();
            $alternatives = array($locale);
            if (strcmp($locale, $language) !== 0) {
                $alternatives[] = $language;
            }
            $content = false;
            foreach ($alternatives as $alternative) {
                $path = DIR_BASE_CORE.'/'.DIRNAME_JAVASCRIPT."/i18n/select2_locale_{$alternative}.js";
                if (is_file($path) && is_readable($path)) {
                    $content = @file_get_contents($path);
                    if (is_string($content)) {
                        break;
                    }
                }
            }
            if (is_string($content)) {
                echo $content;
            } else {
                echo '// No select2 translations for '.implode(', ', $alternatives);
            }
        }
    }

    public static function getRedactorJavascript($setResponseHeaders = true)
    {
        if ($setResponseHeaders) {
            static::sendJavascriptHeader();
        }
        $locale = Localization::activeLocale();
        ?>
jQuery.Redactor.opts.langs[<?=json_encode($locale)?>] = {
  html: <?=json_encode(t('HTML'))?>,
  video: <?=json_encode(t('Insert Video'))?>,
  image: <?=json_encode(t('Insert Image'))?>,
  table: <?=json_encode(t('Table'))?>,
  link: <?=json_encode(t('Link'))?>,
  link_insert: <?=json_encode(t('Insert link'))?>,
  link_edit: <?=json_encode(t('Edit link'))?>,
  unlink: <?=json_encode(t('Unlink'))?>,
  formatting: <?=json_encode(t('Formatting'))?>,
  paragraph: <?=json_encode(t('Normal text'))?>,
  quote: <?=json_encode(t('Quote'))?>,
  code: <?=json_encode(t('Code'))?>,
  header1: <?=json_encode(t('Header 1'))?>,
  header2: <?=json_encode(t('Header 2'))?>,
  header3: <?=json_encode(t('Header 3'))?>,
  header4: <?=json_encode(t('Header 4'))?>,
  header5: <?=json_encode(t('Header 5'))?>,
  /* concrete5 */
  header6: <?=json_encode(t('Header 6'))?>,
  customStyles: <?=json_encode(t('Custom Styles'))?>,
  /* end concrete5 */
  bold: <?=json_encode(t('Bold'))?>,
  italic: <?=json_encode(t('Italic'))?>,
  fontcolor: <?=json_encode(t('Font Color'))?>,
  backcolor: <?=json_encode(t('Back Color'))?>,
  unorderedlist: <?=json_encode(t('Unordered List'))?>,
  orderedlist: <?=json_encode(t('Ordered List'))?>,
  outdent: <?=json_encode(t('Outdent'))?>,
  indent: <?=json_encode(t('Indent'))?>,
  cancel: <?=json_encode(t('Cancel'))?>,
  insert: <?=json_encode(t('Insert'))?>,
  save: <?=json_encode(t('Save'))?>,
  _delete: <?=json_encode(t('Delete'))?>,
  insert_table: <?=json_encode(t('Insert Table'))?>,
  insert_row_above: <?=json_encode(t('Add Row Above'))?>,
  insert_row_below: <?=json_encode(t('Add Row Below'))?>,
  insert_column_left: <?=json_encode(t('Add Column Left'))?>,
  insert_column_right: <?=json_encode(t('Add Column Right'))?>,
  delete_column: <?=json_encode(t('Delete Column'))?>,
  delete_row: <?=json_encode(t('Delete Row'))?>,
  delete_table: <?=json_encode(t('Delete Table'))?>,
  rows: <?=json_encode(t('Rows'))?>,
  columns: <?=json_encode(t('Columns'))?>,
  add_head: <?=json_encode(t('Add Head'))?>,
  delete_head: <?=json_encode(t('Delete Head'))?>,
  title: <?=json_encode(t('Title'))?>,
  image_position: <?=json_encode(t('Position'))?>,
  none: <?=json_encode(t('None'))?>,
  left: <?=json_encode(t('Left'))?>,
  right: <?=json_encode(t('Right'))?>,
  center: <?=json_encode(t('Center'))?>,
  image_web_link: <?=json_encode(t('Image Web Link'))?>,
  text: <?=json_encode(t('Text'))?>,
  mailto: <?=json_encode(t('Email'))?>,
  web: <?=json_encode(t('URL'))?>,
  video_html_code: <?=json_encode(t('Video Embed Code'))?>,
  file: <?=json_encode(t('Insert File'))?>,
  upload: <?=json_encode(t('Upload'))?>,
  download: <?=json_encode(t('Download'))?>,
  choose: <?=json_encode(t('Choose'))?>,
  or_choose: <?=json_encode(t('Or choose'))?>,
  drop_file_here: <?=json_encode(t('Drop file here'))?>,
  align_left: <?=json_encode(t('Align text to the left'))?>,
  align_center: <?=json_encode(t('Center text'))?>,
  align_right: <?=json_encode(t('Align text to the right'))?>,
  align_justify: <?=json_encode(t('Justify text'))?>,
  horizontalrule: <?=json_encode(t('Insert Horizontal Rule'))?>,
  deleted: <?=json_encode(t('Deleted'))?>,
  anchor: <?=json_encode(t('Anchor'))?>,
  open_link: <?=json_encode(t('Open link'))?>,
  default_behavior: <?=json_encode(t('Default Behavior'))?>,
  in_lightbox: <?=json_encode(t('In a Lightbox'))?>,
  open_link_in_lightbox: <?=json_encode(t('Open Link in Lightbox'))?>,
  link_new_tab: <?=json_encode(t('Open link in new tab'))?>,
  underline: <?=json_encode(t('Underline'))?>,
  alignment: <?=json_encode(t('Alignment'))?>,
  filename: <?=json_encode(t('Name (optional)'))?>,
  edit: <?=json_encode(t('Edit'))?>
};

jQuery.Redactor.opts.lang = <?=json_encode($locale)?>;
jQuery.each(jQuery.Redactor.opts.langs.en, function(key, value) {
  if(!(key in jQuery.Redactor.opts.langs[<?=json_encode($locale)?>])) {
    jQuery.Redactor.opts.langs[<?=json_encode($locale)?>][key] = value;
  }
});

var ccmi18n_redactor = {
  remove_font: <?=json_encode(t('Remove font'))?>,
  change_font_family: <?=json_encode(t('Change font family'))?>,
  remove_font_size: <?=json_encode(t('Remove font size'))?>,
  change_font_size: <?=json_encode(t('Change font size'))?>,
  cancel: <?=json_encode(t('Cancel'))?>,
  save: <?=json_encode(t('Save'))?>,
  remove_style: <?=json_encode(t('Remove Style'))?>
};
<?php

    }

    public static function getDynatreeJavascript($setResponseHeaders = true)
    {
        if ($setResponseHeaders) {
            static::sendJavascriptHeader();
        }
        ?>
jQuery.ui.dynatree.prototype.options.strings.loading = <?=json_encode(t('Loading...'))?>;
jQuery.ui.dynatree.prototype.options.strings.loadError = <?=json_encode(t('Load error!'))?>;
<?php

    }

    public static function getImageEditorJavascript($setResponseHeaders = true)
    {
        if ($setResponseHeaders) {
            static::sendJavascriptHeader();
        }
        ?>
var ccmi18n_imageeditor = {
  loadingControlSets: <?=json_encode(t('Loading Control Sets...'))?>,
  loadingComponents: <?=json_encode(t('Loading Components...'))?>,
  loadingFilters: <?=json_encode(t('Loading Filters...'))?>,
  loadingImage: <?=json_encode(t('Loading Image...'))?>,
  areYouSure: <?=json_encode(t('Are you sure?'))?>
};
        <?php

    }

    public static function getJQueryUIJavascript($setResponseHeaders = true)
    {
        if ($setResponseHeaders) {
            static::sendJavascriptHeader();
        }
        $env = Environment::get();
        /* @var $env \Concrete\Core\Foundation\Environment */
        $r = $env->getRecord($path);
        $alternatives = array(Localization::activeLocale());
        if (Localization::activeLocale() !== Localization::activeLanguage()) {
            $alternatives[] = Localization::activeLanguage();
        }
        $found = null;
        foreach ($alternatives as $alternative) {
            $r = $env->getRecord('js/i18n/ui.datepicker-'.str_replace('_', '-', $alternative).'.js');
            if (is_file($r->file)) {
                $found = $r->file;
                break;
            }
        }
        if (isset($found)) {
            readfile($found);
        } else {
            echo '// No jQueryUI translations for '.Localization::activeLocale();
        }
    }
    public static function getTranslatorJavascript($setResponseHeaders = true)
    {
        if ($setResponseHeaders) {
            static::sendJavascriptHeader();
        }
        ?>
ccmTranslator.setI18NDictionart({
  AskDiscardDirtyTranslation: <?=json_encode(t("The current item has changed.\nIf you proceed you will lose your changes.\n\nDo you want to proceed anyway?"))?>,
  Comments: <?=json_encode(t('Comments'))?>,
  Context: <?=json_encode(t('Context'))?>,
  ExamplePH: <?=json_encode(t('Example: %s'))?>,
  Filter: <?=json_encode(t('Filter'))?>,
  Original_String: <?=json_encode(t('Original String'))?>,
  Please_fill_in_all_plurals: <?=json_encode(t('Please fill-in all plural forms'))?>,
  Plural_Original_String: <?=json_encode(t('Plural Original String'))?>,
  References: <?=json_encode(t('References'))?>,
  Save_and_Continue: <?=json_encode(t('Save & Continue'))?>,
  Search_for_: <?=json_encode(t('Search for...'))?>,
  Search_in_contexts: <?=json_encode(t('Search in contexts'))?>,
  Search_in_originals: <?=json_encode(t('Search in originals'))?>,
  Search_in_translations: <?=json_encode(t('Search in translations'))?>,
  Show_approved: <?=json_encode(t('Show approved'))?>,
  Show_translated: <?=json_encode(t('Show translated'))?>,
  Show_unapproved: <?=json_encode(t('Show unapproved'))?>,
  Show_untranslated: <?=json_encode(t('Show untranslated'))?>,
  Singular_Original_String: <?=json_encode(t('Singular Original String'))?>,
  Toggle_Dropdown: <?=json_encode(t('Toggle Dropdown'))?>,
  TAB: <?=json_encode(t('[TAB] Forward'))?>,
  TAB_SHIFT: <?=json_encode(t('[SHIFT]+[TAB] Backward'))?>,
  Translate: <?=json_encode(t('Translate'))?>,
  Translation: <?=json_encode(t('Translation'))?>,
  PluralNames: {
    zero: <?=json_encode(tc('PluralCase', 'Zero'))?>,
    one: <?=json_encode(tc('PluralCase', 'One'))?>,
    two: <?=json_encode(tc('PluralCase', 'Two'))?>,
    few: <?=json_encode(tc('PluralCase', 'Few'))?>,
    many: <?=json_encode(tc('PluralCase', 'Many'))?>,
    other: <?=json_encode(tc('PluralCase', 'Other'))?>
  }
});<?php

    }
}
