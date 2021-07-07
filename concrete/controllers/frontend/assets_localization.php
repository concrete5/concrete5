<?php

namespace Concrete\Controller\Frontend;

use Concrete\Core\File\Image\BitmapFormat;
use Concrete\Core\File\Type\Type as FileType;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Localization\Localization;
use Controller;
use Environment;

class AssetsLocalization extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCoreJavascript()
    {
        $content = '
var ccmi18n = ' . json_encode([
            'expand' => t('Expand'),
            'loadingText' => t('Loading'),
            'cancel' => t('Cancel'),
            'collapse' => t('Collapse'),
            'error' => t('Error'),
            'errorDetails' => t('Details'),
            'deleteBlockTitle' => t('Delete'),
            'deleteBlock' => t('Block Deleted'),
            'deleteBlockMsg' => t('The block has been removed successfully.'),
            'addBlock' => t('Add Block'),
            'addBlockNew' => t('Add Block'),
            'addBlockContainer' => t('Add Container'),
            'addBlockStack' => t('Add Stack'),
            'addBlockStackMsg' => t('The stack has been added successfully'),
            'addBlockPaste' => t('Paste from Clipboard'),
            'changeAreaCSS' => t('Design'),
            'editAreaLayout' => t('Edit Layout'),
            'addAreaLayout' => t('Add Layout'),
            'moveLayoutUp' => t('Move Up'),
            'moveLayoutDown' => t('Move Down'),
            'moveLayoutAtBoundary' => t('This layout section can not be moved further in this direction.'),
            'areaLayoutPresets' => t('Layout Presets'),
            'lockAreaLayout' => t('Lock Layout'),
            'unlockAreaLayout' => t('Unlock Layout'),
            'deleteLayout' => t('Delete'),
            'deleteLayoutOptsTitle' => t('Delete Layout'),
            'confirmLayoutPresetDelete' => t('Are you sure you want to delete this layout preset?'),
            'setAreaPermissions' => t('Set Permissions'),
            'addBlockMsg' => t('The block has been added successfully.'),
            'addBlockContainerMsg' => t('The container has been added successfully.'),
            'updateBlock' => t('Update Block'),
            'updateBlockMsg' => t('The block has been saved successfully.'),
            'copyBlockToScrapbookMsg' => t('The block has been added to your clipboard.'),
            'content' => t('Content'),
            'closeWindow' => t('Close'),
            'editBlock' => t('Edit'),
            'editBlockWithName' => tc('%s is a block type name', 'Edit %s'),
            'setPermissionsDeferredMsg' => t('Permission setting saved. You must complete the workflow before this change is active.'),
            'editStackContents' => t('Manage Stack Contents'),
            'compareVersions' => t('Compare Versions'),
            'blockAreaMenu' => t('Add Block'),
            'arrangeBlock' => t('Move'),
            'arrangeBlockMsg' => t('Blocks arranged successfully.'),
            'copyBlockToScrapbook' => t('Copy to Clipboard'),
            'changeBlockTemplate' => t('Block Template'),
            'changeBlockCSS' => t('Design'),
            'go' => t('Go'),
            'confirm' => t('Confirm'),
            'errorCustomStylePresetNoName' => t('You must give your custom style preset a name.'),
            'changeBlockBaseStyle' => t('Set Block Styles'),
            'confirmCssReset' => t('Are you sure you want to remove all of these custom styles?'),
            'confirmCssPresetDelete' => t('Are you sure you want to delete this custom style preset?'),
            'setBlockPermissions' => t('Set Permissions'),
            'setBlockAlias' => t('Setup on Child Pages'),
            'setBlockComposerSettings' => t('Composer Settings'),
            'themeBrowserTitle' => t('Get More Themes'),
            'themeBrowserLoading' => t('Retrieving theme data from concrete5.org marketplace.'),
            'addonBrowserLoading' => t('Retrieving add-on data from concrete5.org marketplace.'),
            'clear' => t('Clear'),
            'requestTimeout' => t('This request took too long.'),
            'generalRequestError' => t('An unexpected error occurred.'),
            'helpPopup' => t('Help'),
            'community' => t('concrete5 Marketplace'),
            'communityCheckout' => t('concrete5 Marketplace - Purchase & Checkout'),
            'communityDownload' => t('concrete5 Marketplace - Download'),
            'noIE6' => t('concrete5 does not support Internet Explorer 6 in edit mode.'),
            'helpPopupLoginMsg' => t('Get more help on your question by posting it to the concrete5 help center on concrete5.org'),
            'marketplaceErrorMsg' => t('<p>You package could not be installed.  An unknown error occurred.</p>'),
            'marketplaceInstallMsg' => t('<p>Your package will now be downloaded and installed.</p>'),
            'marketplaceLoadingMsg' => t('<p>Retrieving information from the concrete5 Marketplace.</p>'),
            'marketplaceLoginMsg' => t('<p>You must be logged into the concrete5 Marketplace to install add-ons and themes.  Please log in.</p>'),
            'marketplaceLoginSuccessMsg' => t('<p>You have successfully logged into the concrete5 Marketplace.</p>'),
            'marketplaceLogoutSuccessMsg' => t('<p>You are now logged out of concrete5 Marketplace.</p>'),
            'deleteAttributeValue' => t('Are you sure you want to remove this value?'),
            'search' => t('Search'),
            'advanced' => t('Advanced'),
            'advancedSearch' => t('Advanced Search'),
            'customizeSearch' => t('Customize Search'),
            'properties' => t('Page Saved'),
            'savePropertiesMsg' => t('Page Properties saved.'),
            'saveSpeedSettingsMsg' => t('Full page caching settings saved.'),
            'saveUserSettingsMsg' => t('User Settings saved.'),
            'ok' => t('Ok'),
            'scheduleGuestAccess' => t('Schedule Guest Access'),
            'scheduleGuestAccessSuccess' => t('Timed Access for Guest Users Updated Successfully.'),
            'newsflowLoading' => t('Checking for updates.'),
            'x' => t('x'),
            'user_activate' => t('Activate Users'),
            'user_deactivate' => t('Deactivate Users'),
            'user_delete' => t('Delete'),
            'user_group_remove' => t('Remove From Group'),
            'user_group_add' => t('Add to Group'),
            'chooseUser' => t('Choose a User'),
            'none' => t('None'),
            'editModeMsg' => t('Let\'s start editing a page.'),
            'editMode' => t('Edit Mode'),
            'save' => t('Save'),
            'currentImage' => t('Current Image'),
            'image' => t('Image'),
            'size' => t('Size'),
            'chooseFont' => t('Choose Font'),
            'fontWeight' => t('Font Weight'),
            'italic' => t('Italic'),
            'underline' => t('Underline'),
            'uppercase' => t('Uppercase'),
            'fontSize' => t('Font Size'),
            'letterSpacing' => t('Letter spacing'),
            'lineHeight' => t('Line Height'),
            'emptyArea' => t('Empty %s Area', '<%- area_handle %>'),
            'fullArea' => t('This area is full!'),
            'progressiveOperationLoading' => t('Determining items remaining...'),
            'notifications' => t('Notifications'),
            'selectNoResult' => t('No result for %s. Add it?', '{0}'),
            'permissionsUpdatedTitle' => t('Permissions Updated'),
            'permissionsUpdatedMessage' => t('The permissions has been successfully updated.'),
            'permissionsOverrideWarning' => t('Changing this setting will affect this page immediately. Are you sure?'),
            'addAccessEntityDialogTitle' => t('Add Access Entity')
        ]) . ';
var ccmi18n_editor = ' . json_encode([
            'insertLinkToFile' => t('Insert Link to File'),
            'insertImage' => t('Insert Image'),
            'insertLinkToPage' => t('Link to Page'),
        ]) . ';
var ccmi18n_express = ' . json_encode([
            'chooseEntry' => t('Choose Entry'),
            'entriesTitle' => t('Entries'),
        ]) . ';
var ccmi18n_sitemap = ' . json_encode([
            'seo' => t('SEO'),
            'pageLocation' => t('Location'),
            'pageLocationTitle' => t('Location'),
            'visitExternalLink' => t('Visit'),
            'editExternalLink' => t('Edit External Link'),
            'editAlias' => t('Edit Alias'),
            'deleteExternalLink' => t('Delete'),
            'copyProgressTitle' => t('Copy Progress'),
            'addExternalLink' => t('Add External Link'),
            'sendToTop' => t('Send To Top'),
            'sendToBottom' => t('Send To Bottom'),
            'emptyTrash' => t('Empty Trash'),
            'restorePage' => t('Restore Page'),
            'deletePageForever' => t('Delete Forever'),
            'previewPage' => t('Preview'),
            'visitPage' => t('Visit'),
            'pageAttributes' => t('Attributes'),
            'speedSettings' => t('Caching'),
            'speedSettingsTitle' => t('Caching'),
            'pageAttributesTitle' => t('Attributes'),
            'pagePermissionsTitle' => t('Page Permissions'),
            'setPagePermissions' => t('Permissions'),
            'setPagePermissionsMsg' => t('Page permissions updated successfully.'),
            'pageDesignMsg' => t('Theme and page type updated successfully.'),
            'pageDesign' => t('Design &amp; Type'),
            'pageVersions' => t('Versions'),
            'deletePage' => t('Delete'),
            'deletePages' => t('Delete Pages'),
            'deletePageSuccessMsg' => t('The page has been removed successfully.'),
            'deletePageSuccessDeferredMsg' => t('Delete request saved. You must complete the workflow before the page is fully removed.'),
            'addPage' => t('Add Page'),
            'moveCopyPage' => t('Move/Copy'),
            'reorderPage' => t('Change Page Order'),
            'reorderPageMessage' => t('Move or reorder pages by dragging their icons.'),
            'moveCopyPageMessage' => t('Choose a new parent page from the sitemap.'),
            'editInComposer' => t('Edit in Composer'),
            'searchPages' => t('Search Pages'),
            'explorePages' => t('Flat View'),
            'backToSitemap' => t('Back to Sitemap'),
            'searchResults' => t('Search Results'),
            'createdBy' => t('Created By'),
            'choosePage' => t('Choose a Page'),
            'viewing' => t('Viewing'),
            'results' => t('Result(s)'),
            'max' => t('max'),
            'noResults' => t('No results found.'),
            'areYouSure' => t('Are you sure?'),
            'loadingText' => t('Loading'),
            'loadError' => t('Unable to load sitemap data. Response received: '),
            'loadErrorTitle' => t('Unable to load sitemap data.'),
            'on' => t('on'),
        ]) . ';
var ccmi18n_spellchecker = ' . json_encode([
            'resumeEditing' => t('Resume Editing'),
            'noSuggestions' => t('No Suggestions'),
        ]) . ';
var ccmi18n_groups = ' . json_encode([
            'editGroup' => t('Edit Group'),
            'editPermissions' => t('Edit Permissions'),
        ]) . ';

var ccmi18n_fileuploader = ' . json_encode([
            'dialogTitle' => t('Import Files'),
            'yourComputer' => t('Your Computer'),
            'incomingDirectory' => t('Incoming Directory'),
            'remoteFiles' => t('Remote Files'),
            'dropFilesHere' => t('Drop files here or click to upload'),
            'uploadFilesTo' => t('Upload files to'),
            'createNewDirectoryButton' => t('Create New Folder'),
            'directoryName' => t('Name'),
            'directoryPlaceholder' => t('Please enter a name...'),
            'uploadSuccessfulTitle' => t('Complete'),
            'uploadSuccessfulMessage' => t('Upload complete.'),
            'cancelButton' => t('Cancel'),
            'continueButton' => t('Continue'),
            'loading' => t('Loading...'),
            'confirmMessage' => t('Are you sure that you want to close this dialog? All pending files will be removed.'),
            'confirmButton' => t('Remove pending files'),
            'filename' => t('Filename'),
            'size' => t('Size'),
            'removeFilesAfterPost' => t('Remove files from {0} directory of {1} storage location.'),
            'invalidFileExtension' => t('Invalid file extension'),
            'enterSingleUrl' => t('Enter URL to valid file'),
            'errorNotificationTitle' => t('Error'),
            'enterMultipleUrls' => t('Enter URL to valid file(s), one URL per line'),
            'noFilesFound' => t('No files found in {0} for the storage location "{1}".'),
        ]) . ';

var ccmi18n_filemanager = ' . json_encode([
            'view' => t('View'),
            'download' => t('Download'),
            'select' => t('Choose'),
            'duplicateFile' => t('Copy File'),
            'clear' => t('Clear'),
            'edit' => t('Edit'),
            'thumbnailImages' => t('Thumbnail Images'),
            'replace' => t('Replace'),
            'duplicate' => t('Copy'),
            'chooseNew' => t('Choose New File'),
            'sets' => t('Sets'),
            'permissions' => t('Permissions'),
            'properties' => t('Properties'),
            'deleteFile' => t('Delete'),
            'title' => t('File Manager'),
            'chooseFile' => t('Choose File'),
            'uploadErrorChooseFile' => t('You must choose a file.'),
            'addFiles' => t('Add Files'),
            'rescan' => t('Rescan'),
            'jumpToFolder' => t('Jump to Folder'),
            'pending' => t('Pending'),
            'uploadComplete' => t('Upload Complete'),
            'uploadFailed' => t('Upload Failed'),
            'uploadProgress' => t('Upload Progress'),
            'chosenTooMany' => t('You may only select a single file.'),
            'PTYPE_CUSTOM' => '', // FilePermissions::PTYPE_CUSTOM
            'PTYPE_NONE' => '', // FilePermissions::PTYPE_NONE
            'PTYPE_ALL' => '', // /*FilePermissions::PTYPE_ALL
            'FTYPE_IMAGE' => FileType::T_IMAGE,
            'FTYPE_VIDEO' => FileType::T_VIDEO,
            'FTYPE_TEXT' => FileType::T_TEXT,
            'FTYPE_AUDIO' => FileType::T_AUDIO,
            'FTYPE_DOCUMENT' => FileType::T_DOCUMENT,
            'FTYPE_APPLICATION' => FileType::T_APPLICATION,
        ]) . ';
var ccmi18n_chosen = ' . json_encode([
            'placeholder_text_multiple' => t('Select Some Options'),
            'placeholder_text_single' => t('Select an Option'),
            'no_results_text' => t(/*i18n After this text we have a search criteria: for instance 'No results match "Criteria"'*/'No results match'),
        ]) . ';
var ccmi18n_topics = ' . json_encode([
            'addCategory' => t('Add Category'),
            'editCategory' => t('Edit Category'),
            'deleteCategory' => t('Delete Category'),
            'cloneCategory' => t('Clone Category'),
            'addTopic' => t('Add Topic'),
            'editTopic' => t('Edit Topic'),
            'deleteTopic' => t('Delete Topic'),
            'cloneTopic' => t('Clone Topic'),
            'editPermissions' => t('Edit Permissions'),
        ]) . ';
var ccmi18n_tree = ' . json_encode([
            'add' => t('Add'),
            'edit' => t('Edit'),
            'delete' => t('Delete'),
        ]) . ';
var ccmi18n_tourist = ' . json_encode([
            'template' => implode('', [
                '<div class="popover ccm-help-tour" role="tooltip">',
                '<div class="arrow"></div>',
                '<a class="ccm-help-tour-close fas fa-times" href="#" data-role="end"></a>',
                '<div class="popover-body"></div>',
                '<div class="ccm-help-tour-footer d-flex justify-content-between">',
                '<div class="ccm-help-tour-position">',
                t(/*i18n: %1$s and %2$s are two numbers*/'%1$s of %2$s', '<span class="ccm-help-tour-position-index"></span>', '<span class="ccm-help-tour-position-count"></span>'),
                '</div>',
                '<div class="popover-navigation text-end">',
                '<div class="btn-group">',
                '<a href="#" data-role="prev">&lt; ' . t('Prev') . '</a>',
                '<a href="#" data-role="next">' . t('Next') . ' &gt;</a>',
                '<a href="#" data-role="pause-resume" data-pause-text="' . t('Pause') . '" data-resume-text="' . t('Resume') . '">' . t('Pause') . '</a>',
                '</div>',
                '</div>',
                '</div>',
                '<div>',
            ]),
            'localization' => [
                'buttonTexts' => [
                    'prevButton' => t('Prev'),
                    'nextButton' => t('Next'),
                    'pauseButton' => t('Pause'),
                    'resumeButton' => t('Resume'),
                    'endTourButton' => t('End Tour'),
                ],
            ],
        ]) . ';
var ccmi18n_helpGuides = ' . json_encode([
            'add-page' => [
                ['title' => t('Pages Panel'), 'text' => t('The pages is where you go to add a new page to your site, or jump between existing pages. To open the pages panel, click the icon.')],
                ['title' => t('Page Types'), 'text' => t('This is your list of page types. Click any of them to add a page.')],
                ['title' => t('Sitemap'), 'text' => t('This is your sitemap. Use it to easily navigate your site.')],
            ],
            'change-content-edit-mode' => [
                ['title' => t('Edit Mode Active'), 'text' => t('The highlighted button makes it obvious you\'re in edit mode.')],
                ['title' => t('Edit the Block'), 'text' => t('Just roll over any content on the page. Click or tap to get the edit menu for that block.')],
                ['title' => t('Edit Menu'), 'text' => t('Use this menu to edit a block\'s contents, change its display, or remove it entirely.')],
                ['title' => t('Save Changes'), 'text' => t("When you're done editing you can Save Changes for other editors to see, or Publish Changes to make your changes live immediately.")],
            ],
            'add-content-edit-mode' => [
                ['title' => t('Add Mode Active'), 'text' => t('The highlighted button makes it obvious you\'re in Add Content mode.')],
                ['title' => t('Add Panel'), 'text' => t('This is the Add Content Panel.')],
                ['title' => t('Content Selector'), 'text' => t('Click here to choose between adding blocks, clipboard items, stacks and stack contents.')],
                ['title' => t('Search Blocks'), 'text' => t('You can easily filter the blocks in the panel by searching here.')],
                ['title' => t('Add Blocks'), 'text' => t('Click and drag blocks from the add panel into the page to add them.')],
            ],
            'change-content' => [
                ['title' => t('Enter Edit Mode'), 'text' => t('First, click the "Edit Page" button. This will enter edit mode for this page.')],
            ],
            'add-content' => [
                ['title' => t('Enter Edit Mode'), 'text' => t('Click the "Add Content" button to enter edit mode, with the Add Content panel active.')],
            ],
            'dashboard' => [
                ['title' => t('Dashboard Panel'), 'text' => t('The dashboard is where you go to manage aspects of your site that have to do with more than the content on just one page. Click the sliders icon.')],
                ['title' => t('Sitemap'), 'text' => t("The sitemap lets manage the structure of your website. You can delete pages you don't need, or drag them around the tree to suit your needs.")],
            ],
            'location-panel' => [
                ['title' => t('Choose Location'), 'text' => t('Click this button to choose the location of the page in your sitemap. If saved, the page will be moved to this location.')],
                ['title' => t('Page URLs'), 'text' => t('Control the URLs used to access your page here. Non-canonical URLs will redirect to your page; canonical URLs can be either generated or automatically or overridden. Sub-pages to this page start with canonical URLs by default.')],
            ],
            'personalize' => [
                ['title' => t('Properties Panel'), 'text' => t('The properties panel controls data and details about the current page including design customizations. To open the properties panel, click the gear icon.')],
                ['title' => t('Page Design'), 'text' => t('From here you can change your page template and customize your page\'s styles.')],
                ['title' => t('Customize'), 'text' => t('Click here to load the theme customizer for the page.')],
            ],
            'toolbar' => [
                ['title' => t('Edit Mode'), 'text' => t('Edit anything on this page by clicking the pencil icon.')],
                ['title' => t('Settings'), 'text' => t('Change the general look and options like SEO and permissions. Delete the page or roll versions back from here as well.')],
                ['title' => t('Add Content'), 'text' => t('Place a new block on the page. Copy one using the clipboard, or try a reusable stack.')],
                ['title' => t('Intelligent Search'), 'text' => t('At a loss? Try searching here. You can find anything from pages in your site to settings and how-to documentation.')],
                ['title' => t('Add Page'), 'text' => t('Add a new page to your site, or quickly jump around your sitemap.')],
                ['title' => t('Dashboard'), 'text' => t('Anything that isn\'t specific to this page happens here. Manage users, files, reporting data, and site-wide settings.')],
            ],
        ]) . ';
';

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSelect2Javascript()
    {
        $locale = str_replace('_', '-', Localization::activeLocale());
        if ($locale === 'en-US') {
            $content = "/* select2: no needs to translate {$locale} */\n";
        } else {
            $env = Environment::get();
            // @var $env \Concrete\Core\Foundation\Environment
            $language = Localization::activeLanguage();
            $alternatives = [$locale];
            if (strcmp($locale, $language) !== 0) {
                $alternatives[] = $language;
            }
            $found = null;
            foreach ($alternatives as $alternative) {
                $r = $env->getRecord(DIRNAME_JAVASCRIPT . "/i18n/select2_locale_{$alternative}.js");
                if (is_file($r->file)) {
                    $found = $r->file;
                    break;
                }
            }
            if (isset($found)) {
                $content = @file_get_contents($found);
                if ($content === false) {
                    $content = "/* select2: failed to read translations for {$alternative} */";
                }
            } else {
                $content = '/* select2: no translations for ' . implode(', ', $alternatives) . ' */';
            }
        }

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRedactorJavascript()
    {
        $locale = Localization::activeLocale();
        $content =
            'jQuery.Redactor.opts.langs[' . json_encode($locale) . '] = ' . json_encode([
                'html' => t('HTML'),
                'video' => t('Insert Video'),
                'image' => t('Insert Image'),
                'table' => t('Table'),
                'link' => t('Link'),
                'link_insert' => t('Insert link'),
                'link_edit' => t('Edit link'),
                'unlink' => t('Unlink'),
                'formatting' => t('Formatting'),
                'paragraph' => t('Normal text'),
                'quote' => t('Quote'),
                'code' => t('Code'),
                'header1' => t('Header 1'),
                'header2' => t('Header 2'),
                'header3' => t('Header 3'),
                'header4' => t('Header 4'),
                'header5' => t('Header 5'),
                'bold' => t('Bold'),
                'italic' => t('Italic'),
                'fontcolor' => t('Font Color'),
                'backcolor' => t('Back Color'),
                'unorderedlist' => t('Unordered List'),
                'orderedlist' => t('Ordered List'),
                'outdent' => t('Outdent'),
                'indent' => t('Indent'),
                'cancel' => t('Cancel'),
                'insert' => t('Insert'),
                'save' => t('Save'),
                '_delete' => t('Delete'),
                'insert_table' => t('Insert Table'),
                'insert_row_above' => t('Add Row Above'),
                'insert_row_below' => t('Add Row Below'),
                'insert_column_left' => t('Add Column Left'),
                'insert_column_right' => t('Add Column Right'),
                'delete_column' => t('Delete Column'),
                'delete_row' => t('Delete Row'),
                'delete_table' => t('Delete Table'),
                'rows' => t('Rows'),
                'columns' => t('Columns'),
                'add_head' => t('Add Head'),
                'delete_head' => t('Delete Head'),
                'title' => t('Title'),
                'image_position' => t('Position'),
                'none' => t('None'),
                'left' => t('Left'),
                'right' => t('Right'),
                'center' => t('Center'),
                'image_web_link' => t('Image Web Link'),
                'text' => t('Text'),
                'mailto' => t('Email'),
                'web' => t('URL'),
                'video_html_code' => t('Video Embed Code or Youtube/Vimeo Link'),
                'file' => t('Insert File'),
                'upload' => t('Upload'),
                'download' => t('Download'),
                'choose' => t('Choose'),
                'or_choose' => t('Or choose'),
                'drop_file_here' => t('Drop file here'),
                'align_left' => t('Align text to the left'),
                'align_center' => t('Center text'),
                'align_right' => t('Align text to the right'),
                'align_justify' => t('Justify text'),
                'horizontalrule' => t('Insert Horizontal Rule'),
                'deleted' => t('Deleted'),
                'anchor' => t('Anchor'),
                'open_link' => t('Open Link'),
                'link_new_tab' => t('Open link in new tab'),
                // concrete5
                'link_same_window' => t('Open link in same window'),
                'in_lightbox' => t('Open link in Lightbox'),
                'lightbox_link_type' => t('Link Type'),
                'lightbox_link_type_iframe' => t('Web Page'),
                'lightbox_link_type_image' => t('Image'),
                'lightbox_link_type_iframe_options' => t('Frame Options'),
                'lightbox_link_type_iframe_width' => t('Width'),
                'lightbox_link_type_iframe_height' => t('Height'),
                'customStyles' => t('Custom Styles'),
                'remove_font' => t('Remove font'),
                'change_font_family' => t('Change Font Family'),
                'remove_style' => t('Remove Style'),
                'insert_character' => t('Insert Character'),
                'undo' => t('Undo'),
                'redo' => t('Redo'),
                'remove_font_family' => t('Remove Font Family'),
                'remove_font_size' => t('Remove Font Size'),
                'change_font_size' => t('Change Font Size'),
                // end concrete5
                'underline' => t('Underline'),
                'alignment' => t('Alignment'),
                'filename' => t('Name (optional)'),
                'edit' => t('Edit'),
                'upload_label' => t('Drop file here or '),
            ]) . ';
jQuery.Redactor.opts.lang = ' . json_encode($locale) . ';
jQuery.each(jQuery.Redactor.opts.langs.en, function(key, value) {
  if(!(key in jQuery.Redactor.opts.langs[' . json_encode($locale) . '])) {
    jQuery.Redactor.opts.langs[' . json_encode($locale) . '][key] = value;
  }
});
';

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getFancytreeJavascript()
    {
        $content =
            'jQuery.ui.fancytree.prototype.options.strings.loading = ' . json_encode(t('Loading...')) . ';
jQuery.ui.fancytree.prototype.options.strings.loadError = ' . json_encode(t('Load error!')) . ';
';

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getImageEditorJavascript()
    {
        $content =
            'var ccmi18n_imageeditor = ' . json_encode([
                'loadingControlSets' => t('Loading Control Sets...'),
                'loadingComponents' => t('Loading Components...'),
                'loadingFilters' => t('Loading Filters...'),
                'loadingImage' => t('Loading Image...'),
                'areYouSure' => t('Are you sure?'),
            ]) . ';
';

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getJQueryUIJavascript()
    {
        $env = Environment::get();
        // @var $env \Concrete\Core\Foundation\Environment
        $alternatives = [Localization::activeLocale()];
        if (Localization::activeLocale() !== Localization::activeLanguage()) {
            $alternatives[] = Localization::activeLanguage();
        }
        $found = null;
        foreach ($alternatives as $alternative) {
            $r = $env->getRecord(DIRNAME_JAVASCRIPT . '/i18n/jquery-ui/datepicker-' . str_replace('_', '-', $alternative) . '.js');
            if (is_file($r->file)) {
                $found = $r->file;
                break;
            }
        }
        if (isset($found)) {
            $content = @file_get_contents($found);
            if ($content === false) {
                $content = "/* jQueryUI: failed to read translations for {$alternative} */";
            }
        } else {
            $content = '/* jQueryUI: no translations for ' . implode(', ', $alternatives) . ' */';
        }

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTranslatorJavascript()
    {
        $content =
            'ccmTranslator.setI18NDictionart(' . json_encode([
                'AskDiscardDirtyTranslation' => t("The current item has changed.\nIf you proceed you will lose your changes.\n\nDo you want to proceed anyway?"),
                'Approve_and_Continue' => t('Approve & Continue'),
                'Approved' => tc('Translation', 'Approved'),
                'Comments' => t('Comments'),
                'Context' => t('Context'),
                'ExamplePH' => t('Example: %s'),
                'Filter' => t('Filter'),
                'Keystroke_ctrl_return' => t('[CTRL]+[RETURN]'),
                'Keystroke_ctrl_shift_return' => t('[CTRL]+[SHIFT]+[RETURN]'),
                'No_newlines_in_translations_please' => t('Please don\'t use new lines in translations (there\'s no new line in the source string)'),
                'Original_String' => t('Original String'),
                'Please_fill_in_all_plurals' => t('Please fill-in all plural forms'),
                'Plural_Original_String' => t('Plural Original String'),
                'References' => t('References'),
                'Save_and_Continue' => t('Save & Continue'),
                'Search_for_' => t('Search for...'),
                'Search_in_contexts' => t('Search in contexts'),
                'Search_in_originals' => t('Search in originals'),
                'Search_in_translations' => t('Search in translations'),
                'Show_approved' => t('Show approved'),
                'Show_translated' => t('Show translated'),
                'Show_unapproved' => t('Show unapproved'),
                'Show_untranslated' => t('Show untranslated'),
                'Singular_Original_String' => t('Singular Original String'),
                'Toggle_Dropdown' => t('Toggle Dropdown'),
                'Translate' => t('Translate'),
                'Translation' => t('Translation'),
                'TranslationIsApproved_WillNeedApproval' => t('This translation is approved: your changes will need approval.'),
                'TranslationIsNotApproved' => t('This translation is not approved.'),
                'PluralNames' => [
                    'zero' => tc('PluralCase', 'Zero'),
                    'one' => tc('PluralCase', 'One'),
                    'two' => tc('PluralCase', 'Two'),
                    'few' => tc('PluralCase', 'Few'),
                    'many' => tc('PluralCase', 'Many'),
                    'other' => tc('PluralCase', 'Other'),
                ],
            ]) . ');
';

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDropzoneJavascript()
    {
        $config = $this->app->make('config');
        $token = $this->app->make('token');

        $maxExecutionTime = (int) ini_get('max_execution_time');
        $maxInputType = (int) ini_get('max_input_time');
        $timeout = $maxExecutionTime <= 0 ? 24 * 60 * 60 : $maxExecutionTime;
        if ($maxInputType === 0) {
            $timeout += 24 * 60 * 60;
        } elseif ($maxInputType > 0) {
            $timeout += $maxInputType;
        }
        $options = [
            'dictDefaultMessage' => t('Drop files here or click to upload.'),
            'dictFallbackMessage' => t("Your browser does not support drag'n'drop file uploads."),
            'dictFallbackText' => t('Please use the fallback form below to upload your files like in the olden days.'),
            'dictFileTooBig' => t('File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.'),
            'dictInvalidFileType' => t('You can\'t upload files of this type.'),
            'dictResponseError' => t('Server responded with {{statusCode}} code.'),
            'dictCancelUpload' => t('Cancel upload'),
            'dictCancelUploadConfirmation' => t('Are you sure you want to cancel this upload?'),
            'dictRemoveFile' => t('Remove file'),
            'dictMaxFilesExceeded' => t('You can not upload any more files.'),
            'resizeQuality' => $this->app->make(BitmapFormat::class)->getDefaultJpegQuality() / 100,
            'chunking' => (bool) $config->get('concrete.upload.chunking.enabled'),
            'chunkSize' => $this->getDropzoneChunkSize(),
            'timeout' => 1000 * $timeout,
        ];
        $maxWidth = (int) $config->get('concrete.file_manager.restrict_max_width');
        if ($maxWidth > 0) {
            $options['resizeWidth'] = $maxWidth;
        }
        $maxHeight = (int) $config->get('concrete.file_manager.restrict_max_height');
        if ($maxHeight > 0) {
            $options['resizeHeight'] = $maxHeight;
        }
        $content = '';
        foreach ($options as $optionKey => $optionValue) {
            $content .= 'Dropzone.prototype.defaultOptions[' . json_encode($optionKey) . '] = ' . json_encode($optionValue) . ";\n";
        }
        if ($maxWidth > 0 || $maxHeight > 0) {
            $content .= <<<'EOT'
Dropzone.prototype.defaultOptions.accept = function(file, done) {
    if (file && file.type === 'image/gif') {
        this.options.resizeWidth = null;
        this.options.resizeHeight = null;
    } else {
        this.options.resizeWidth = Dropzone.prototype.defaultOptions.resizeWidth;
        this.options.resizeHeight = Dropzone.prototype.defaultOptions.resizeHeight;
    }
    return done();
};
EOT
            ;
        }

        // Add extra parameters to the default params by calling the original
        // method first which adds the chunked file transfer headers to the
        // params to be sent to the server.
        $extraParamsString = json_encode([
            $token::DEFAULT_TOKEN_NAME => $token->generate(),
        ]);
        $content .= 'Dropzone.prototype.defaultOptions.defaultParams = Dropzone.prototype.defaultOptions.params;' . "\n";
        $content .= <<<EOT
Dropzone.prototype.defaultOptions.params = function(files, xhr, chunk) {
    var params = this.options.defaultParams.call(this, files, xhr, chunk) || {};
    var extraParams = {$extraParamsString};

    var keys = Object.keys(extraParams);
    for (var i = 0; i < keys.length; i++) {
        params[keys[i]] = extraParams[keys[i]];
    }

    return params;
};
EOT
        ;

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getConversationsJavascript()
    {
        $content =
            'jQuery.fn.concreteConversation.localize(' . json_encode([
                'Confirm_remove_message' => t('Remove this message? Replies to it will not be removed'),
                'Confirm_mark_as_spam' => t('Are you sure you want to flag this message as spam?'),
                'Warn_currently_editing' => t('Please complete or cancel the current message editing session before editing this message.'),
                'Unspecified_error_occurred' => t('An unspecified error occurred.'),
                'Error_deleting_message' => t('Something went wrong while deleting this message, please refresh and try again.'),
                'Error_flagging_message' => t('Something went wrong while flagging this message, please refresh and try again.'),
            ]) . ');
jQuery.fn.concreteConversationAttachments.localize(' . json_encode([
                'Too_many_files' => t('Too many files'),
                'Invalid_file_extension' => t('Invalid file extension'),
                'Max_file_size_exceeded' => t('Max file size exceeded'),
                'Error_deleting_attachment' => t('Something went wrong while deleting this attachment, please refresh and try again.'),
                'Confirm_remove_attachment' => t('Remove this attachment?'),
            ]) . ');
';

        return $this->createJavascriptResponse($content);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getMomentJavascript()
    {
        $localeParts = explode('-', str_replace('_', '-', strtolower(Localization::activeLocale())));
        $alternatives = [];
        if (isset($localeParts[1])) {
            $alternatives[] = "{$localeParts[0]}-{$localeParts[1]}";
        }
        $alternatives[] = $localeParts[0];
        $locator = $this->app->make(FileLocator::class);
        $found = false;
        foreach ($alternatives as $alternative) {
            foreach ($alternatives as $alternative) {
                $r = $locator->getRecord(DIRNAME_JAVASCRIPT . "/i18n/moment/{$alternative}.js");
                if ($r->exists()) {
                    $found = true;
                    $content = file_get_contents($r->getFile()) . ";\n;moment.locale(" . json_encode($alternative) . ");\n";
                    break;
                }
            }
        }
        if ($found === false) {
            $content = '/* moment: no translations for ' . implode(', ', $alternatives) . ' */';
        }

        return $this->createJavascriptResponse($content);
    }

    /**
     * @param string $content
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function createJavascriptResponse($content)
    {
        $rf = $this->app->make(ResponseFactoryInterface::class);

        return $rf->create(
            $content,
            200,
            [
                'Content-Type' => 'application/javascript; charset=' . APP_CHARSET,
                'Content-Length' => strlen($content),
            ]
        );
    }

    /**
     * @return int
     */
    private function getDropzoneChunkSize()
    {
        $config = $this->app->make('config');
        $chunkSize = (int) $config->get('concrete.upload.chunking.chunkSize');

        return $chunkSize > 0 ? $chunkSize : $this->getDropzoneAutomaticChunkSize();
    }

    /**
     * @return int
     */
    private function getDropzoneAutomaticChunkSize()
    {
        $nh = $this->app->make('helper/number');
        // Maximum size of an uploaded file, minus a small value (just in case)
        $uploadMaxFilesize = (int) $nh->getBytes(ini_get('upload_max_filesize')) - 100;
        // Max size of post data allowed, minus enough space to consider other posted fields.
        $postMaxSize = (int) $nh->getBytes(ini_get('post_max_size')) - 10000;
        if ($uploadMaxFilesize < 1 && $postMaxSize < 1) {
            return 2000000;
        }
        if ($uploadMaxFilesize < 1) {
            return $postMaxSize;
        }
        if ($postMaxSize < 1) {
            return $uploadMaxFilesize;
        }

        return min($uploadMaxFilesize, $postMaxSize);
    }
}
