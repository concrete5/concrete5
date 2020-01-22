<?php

use Concrete\Core\Asset\Asset;
use Concrete\Core\File\Type\Type as FileType;

return [
    'debug' => false,
    'namespace' => 'Application',

    /*
     * Core Aliases
     */
    'aliases' => [
        'Area' => '\Concrete\Core\Area\Area',
        'Asset' => '\Concrete\Core\Asset\Asset',
        'AssetList' => '\Concrete\Core\Asset\AssetList',
        'AttributeSet' => '\Concrete\Core\Attribute\Set',
        'AuthenticationType' => '\Concrete\Core\Authentication\AuthenticationType',
        'Block' => '\Concrete\Core\Block\Block',
        'BlockType' => '\Concrete\Core\Block\BlockType\BlockType',
        'BlockTypeList' => '\Concrete\Core\Block\BlockType\BlockTypeList',
        'BlockTypeSet' => '\Concrete\Core\Block\BlockType\Set',
        'Cache' => '\Concrete\Core\Cache\Cache',
        'Request' => '\Concrete\Core\Http\Request',
        'CacheLocal' => '\Concrete\Core\Cache\CacheLocal',
        'Collection' => '\Concrete\Core\Page\Collection\Collection',
        'CollectionAttributeKey' => '\Concrete\Core\Attribute\Key\CollectionKey',
        'CollectionVersion' => '\Concrete\Core\Page\Collection\Version\Version',
        'ConcreteAuthenticationTypeController' => '\Concrete\Authentication\Concrete\Controller',
        'Controller' => '\Concrete\Core\Controller\Controller',
        'Conversation' => '\Concrete\Core\Conversation\Conversation',
        'ConversationEditor' => '\Concrete\Core\Conversation\Editor\Editor',
        'ConversationFlagType' => '\Concrete\Core\Conversation\FlagType\FlagType',
        'ConversationMessage' => '\Concrete\Core\Conversation\Message\Message',
        'ConversationRatingType' => '\Concrete\Core\Conversation\Rating\Type',
        'Environment' => '\Concrete\Core\Foundation\Environment',
        'FacebookAuthenticationTypeController' => '\Concrete\Authentication\Facebook\Controller',
        'File' => '\Concrete\Core\File\File',
        'FileAttributeKey' => '\Concrete\Core\Attribute\Key\FileKey',
        'FileImporter' => '\Concrete\Core\File\Importer',
        'FileList' => '\Concrete\Core\File\FileList',
        'FilePermissions' => '\Concrete\Core\Legacy\FilePermissions',
        'FileSet' => '\Concrete\Core\File\Set\Set',
        'GlobalArea' => '\Concrete\Core\Area\GlobalArea',
        'Group' => '\Concrete\Core\User\Group\Group',
        'GroupList' => '\Concrete\Core\User\Group\GroupList',
        'GroupSet' => '\Concrete\Core\User\Group\GroupSet',
        'GroupSetList' => '\Concrete\Core\User\Group\GroupSetList',
        'GroupTree' => '\Concrete\Core\Tree\Type\Group',
        'GroupTreeNode' => '\Concrete\Core\Tree\Node\Type\Group',
        'Job' => '\Concrete\Core\Job\Job',
        'JobSet' => '\Concrete\Core\Job\Set',
        'Loader' => '\Concrete\Core\Legacy\Loader',
        'Localization' => '\Concrete\Core\Localization\Localization',
        'Marketplace' => '\Concrete\Core\Marketplace\Marketplace',
        'Package' => '\Concrete\Core\Package\Package',
        'Page' => '\Concrete\Core\Page\Page',
        'PageCache' => '\Concrete\Core\Cache\Page\PageCache',
        'PageController' => '\Concrete\Core\Page\Controller\PageController',
        'PageEditResponse' => '\Concrete\Core\Page\EditResponse',
        'PageList' => '\Concrete\Core\Page\PageList',
        'PageTemplate' => '\Concrete\Core\Page\Template',
        'PageTheme' => '\Concrete\Core\Page\Theme\Theme',
        'PageType' => '\Concrete\Core\Page\Type\Type',
        'PermissionAccess' => '\Concrete\Core\Permission\Access\Access',
        'PermissionKey' => '\Concrete\Core\Permission\Key\Key',
        'PermissionKeyCategory' => '\Concrete\Core\Permission\Category',
        'Permissions' => '\Concrete\Core\Permission\Checker',
        'Queue' => '\Concrete\Core\Foundation\Queue\Queue',
        'QueueableJob' => '\Concrete\Core\Job\QueueableJob',
        'Redirect' => '\Concrete\Core\Routing\Redirect',
        'RedirectResponse' => '\Concrete\Core\Routing\RedirectResponse',
        'Response' => '\Concrete\Core\Http\Response',
        'Router' => '\Concrete\Core\Routing\Router',
        'SinglePage' => '\Concrete\Core\Page\Single',
        'Stack' => '\Concrete\Core\Page\Stack\Stack',
        'StackList' => '\Concrete\Core\Page\Stack\StackList',
        'StartingPointPackage' => '\Concrete\Core\Package\StartingPointPackage',
        'TaskPermission' => '\Concrete\Core\Legacy\TaskPermission',
        'User' => '\Concrete\Core\User\User',
        'UserAttributeKey' => '\Concrete\Core\Attribute\Key\UserKey',
        'UserList' => '\Concrete\Core\User\UserList',
        'View' => '\Concrete\Core\View\View',
        'Workflow' => '\Concrete\Core\Workflow\Workflow',
    ],

    /*
     * Core Providers
     */
    'providers' => [
        // Router service provider
        'core_router' => 'Concrete\Core\Routing\RoutingServiceProvider',
        'core_cache' => '\Concrete\Core\Cache\CacheServiceProvider', // needs to come before api
        'core_file' => '\Concrete\Core\File\FileServiceProvider',
        'core_encryption' => '\Concrete\Core\Encryption\EncryptionServiceProvider',
        'core_validation' => '\Concrete\Core\Validation\ValidationServiceProvider',
        'core_localization' => '\Concrete\Core\Localization\LocalizationServiceProvider',
        'core_exporter' => '\Concrete\Core\Export\ExportServiceProvider',
        'core_multilingual' => '\Concrete\Core\Multilingual\MultilingualServiceProvider',
        'core_feed' => '\Concrete\Core\Feed\FeedServiceProvider',
        'core_html' => '\Concrete\Core\Html\HtmlServiceProvider',
        'core_editor' => '\Concrete\Core\Editor\EditorServiceProvider',
        'core_mail' => '\Concrete\Core\Mail\MailServiceProvider',
        'core_application' => '\Concrete\Core\Application\ApplicationServiceProvider',
        'core_utility' => '\Concrete\Core\Utility\UtilityServiceProvider',
        'core_content_importer' => '\Concrete\Core\Backup\ContentImporter\ContentImporterServiceProvider',
        'core_manager_grid_framework' => '\Concrete\Core\Page\Theme\GridFramework\ManagerServiceProvider',
        'core_manager_pagination_view' => '\Concrete\Core\Search\Pagination\View\ManagerServiceProvider',
        'core_manager_page_type' => '\Concrete\Core\Page\Type\ManagerServiceProvider',
        'core_manager_layout_preset_provider' => '\Concrete\Core\Area\Layout\Preset\Provider\ManagerServiceProvider',
        'core_manager_search_fields' => '\Concrete\Core\Search\Field\ManagerServiceProvider',
        'core_permissions' => '\Concrete\Core\Permission\PermissionServiceProvider',
        'core_database' => '\Concrete\Core\Database\DatabaseServiceProvider',
        'core_api' => 'Concrete\Core\Api\ApiServiceProvider',
        'core_form' => '\Concrete\Core\Form\FormServiceProvider',
        'core_session' => '\Concrete\Core\Session\SessionServiceProvider',
        'core_system' => '\Concrete\Core\System\SystemServiceProvider',
        'core_cookie' => '\Concrete\Core\Cookie\CookieServiceProvider',
        'core_http' => '\Concrete\Core\Http\HttpServiceProvider',
        'core_events' => '\Concrete\Core\Events\EventsServiceProvider',
        'core_whoops' => '\Concrete\Core\Error\Provider\WhoopsServiceProvider',
        'core_logging' => '\Concrete\Core\Logging\LoggingServiceProvider',
        'core_element' => '\Concrete\Core\Filesystem\FilesystemServiceProvider',
        'core_notification' => '\Concrete\Core\Notification\NotificationServiceProvider',
        'core_package' => '\Concrete\Core\Package\PackageServiceProvider',
        'core_url' => '\Concrete\Core\Url\UrlServiceProvider',
        'core_devices' => '\Concrete\Core\Device\DeviceServiceProvider',
        'core_imageeditor' => '\Concrete\Core\ImageEditor\EditorServiceProvider',
        'core_user' => '\Concrete\Core\User\UserServiceProvider',
        'core_service_manager' => '\Concrete\Core\Service\Manager\ServiceManagerServiceProvider',
        'core_site' => '\Concrete\Core\Site\ServiceProvider',
        'core_search' => \Concrete\Core\Search\SearchServiceProvider::class,
        'core_geolocator' => 'Concrete\Core\Geolocator\GeolocatorServiceProvider',
        'core_calendar' => 'Concrete\Core\Calendar\CalendarServiceProvider',

        // Console CLI commands
        'core_console' => \Concrete\Core\Console\ServiceProvider::class,

        // Authentication
        'core_oauth' => '\Concrete\Core\Authentication\Type\OAuth\ServiceProvider',
        'core_auth_community' => '\Concrete\Core\Authentication\Type\Community\ServiceProvider',
        'core_auth_google' => '\Concrete\Core\Authentication\Type\Google\ServiceProvider',
        'core_auth_external_concrete5' => '\Concrete\Core\Authentication\Type\ExternalConcrete5\ServiceProvider',

        // Validator
        'core_validator' => '\Concrete\Core\Validator\ValidatorServiceProvider',
        'core_validator_password' => '\Concrete\Core\Validator\PasswordValidatorServiceProvider',
        'core_validator_user_name' => '\Concrete\Core\Validator\UserNameValidatorServiceProvider',
        'core_validator_user_email' => '\Concrete\Core\Validator\UserEmailValidatorServiceProvider',

        // Express
        'core_attribute' => '\Concrete\Core\Attribute\AttributeServiceProvider',
        'core_express' => '\Concrete\Core\Express\ExpressServiceProvider',

        // Tracker
        'core_usagetracker' => '\Concrete\Core\Statistics\UsageTracker\ServiceProvider',
    ],

    /*
     * Core Facades
     */
    'facades' => [
        'Core' => '\Concrete\Core\Support\Facade\Application',
        'Session' => '\Concrete\Core\Support\Facade\Session',
        'Cookie' => '\Concrete\Core\Support\Facade\Cookie',
        'Database' => '\Concrete\Core\Support\Facade\Database',
        'ORM' => '\Concrete\Core\Support\Facade\DatabaseORM',
        'Events' => '\Concrete\Core\Support\Facade\Events',
        'Express' => '\Concrete\Core\Support\Facade\Express',
        'Route' => '\Concrete\Core\Support\Facade\Route',
        'Site' => '\Concrete\Core\Support\Facade\Site',
        'UserInfo' => '\Concrete\Core\Support\Facade\UserInfo',
        'Element' => '\Concrete\Core\Support\Facade\Element',
        'Log' => '\Concrete\Core\Support\Facade\Log',
        'Image' => '\Concrete\Core\Support\Facade\Image',
        'Config' => '\Concrete\Core\Support\Facade\Config',
        'URL' => '\Concrete\Core\Support\Facade\Url',
    ],

    'entity_namespaces' => [
        'calendar' => 'Concrete\Core\Entity\Calendar',
    ],

    'package_items' => [
        'antispam_library',
        'attribute_key_category',
        'attribute_key',
        'attribute_set',
        'attribute_type',
        'authentication_type',
        'block_type',
        'block_type_set',
        'express_entity',
        'captcha_library',
        'content_editor_snippet',
        'conversation_rating_type',
        'geolocator_library',
        'group',
        'group_set',
        'ip_access_control_category',
        'job',
        'mail_importer',
        'permission_access_entity_type',
        'permission_key',
        'permission_key_category',
        'page_template',
        'site_type',
        'page_type',
        'page_type_composer_control_type',
        'page_type_publish_target_type',
        'single_page',
        'storage_location_type',
        'theme',
        'user_point_action',
        'workflow',
        'workflow_progress_category',
        'workflow_type',
    ],

    'importer_routines' => [
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSiteTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportGroupsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSinglePageStructureRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportStacksStructureRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportBlockTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportBlockTypeSetsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportConversationEditorsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportConversationRatingTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportConversationFlagTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTypePublishTargetTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTypeComposerControlTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportBannedWordsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSocialLinksRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportTreesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportFileImportantThumbnailTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportFeaturesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportFeatureCategoriesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportGatheringDataSourcesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportGatheringItemTemplateTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportGatheringItemTemplatesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportAttributeCategoriesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportAttributeTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportWorkflowTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportWorkflowProgressCategoriesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportWorkflowsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportAttributesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportAttributeSetsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportExpressEntitiesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportExpressAssociationsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportExpressFormsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportExpressRelationsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportThemesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPermissionKeyCategoriesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPermissionAccessEntityTypesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPermissionsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportJobsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportJobSetsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTemplatesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTypesBaseRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageStructureRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageFeedsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTypeTargetsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTypeDefaultsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSiteTypeSkeletonsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSinglePageContentRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportStacksContentRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageContentRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPackagesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportConfigValuesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSystemCaptchaLibrariesRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportSystemContentEditorSnippetsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportGeolocatorsRoutine',
        'Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportIpAccessControlCategoriesRoutine',
    ],

    /*
     * Core Routes - no longer used in the core in this way. Look to the routes/ directories instead.
     */
    'routes' => [
    ],

/*
 * Route themes
 */
    'theme_paths' => [
        '/dashboard' => 'dashboard',
        '/dashboard/*' => 'dashboard',
        '/install' => VIEW_CORE_THEME,
        '/login' => [
            VIEW_CORE_THEME,
            VIEW_CORE_THEME_TEMPLATE_BACKGROUND_IMAGE,
        ],
        '/register' => VIEW_CORE_THEME,
        '/frontend/maintenance_mode' => VIEW_CORE_THEME,
        '/upgrade' => VIEW_CORE_THEME,
    ],

    /*
     * File Types
     * Keys are the type name
     * Values are arrays with:
     * - comma-separated extensions
     * - file type
     * - handle of an importer (or false)
     * - handle of the inline viewer (of false)
     * - handle of the editor
     * - handle of the package
     */
    'file_types' => [
        'JPEG' => ['jpg,jpeg,jpe', FileType::T_IMAGE, 'image', 'image', 'image'],
        'GIF' => ['gif', FileType::T_IMAGE, 'image', 'image', 'image'],
        'PNG' => ['png', FileType::T_IMAGE, 'image', 'image', 'image'],
        'WebP' => ['webp', FileType::T_IMAGE, 'image', 'image', 'image'],
        'Windows Bitmap' => ['bmp', FileType::T_IMAGE, 'image'],
        'TIFF' => ['tif,tiff', FileType::T_IMAGE, 'image'],
        'HTML' => ['htm,html', FileType::T_IMAGE],
        'Flash' => ['swf', FileType::T_IMAGE, 'image'],
        'Icon' => ['ico', FileType::T_IMAGE],
        'SVG' => ['svg', FileType::T_IMAGE, false, 'image'],
        'Windows Video' => ['asf,wmv', FileType::T_VIDEO, false, 'video'],
        'Quicktime' => ['mov,qt', FileType::T_VIDEO, false, 'video'],
        'AVI' => ['avi', FileType::T_VIDEO, false, 'video'],
        '3GP' => ['3gp', FileType::T_VIDEO, false, 'video'],
        'Plain Text' => ['txt', FileType::T_TEXT, false, 'text'],
        'CSV' => ['csv', FileType::T_TEXT, false, 'text'],
        'XML' => ['xml', FileType::T_TEXT],
        'PHP' => ['php', FileType::T_TEXT],
        'MS Word' => ['doc,docx', FileType::T_DOCUMENT],
        'Stylesheet' => ['css', FileType::T_TEXT],
        'MP4' => ['mp4', FileType::T_VIDEO, false, 'video'],
        'FLV' => ['flv', FileType::T_VIDEO, 'flv'],
        'MP3' => ['mp3', FileType::T_AUDIO, false, 'audio'],
        'MP4 Audio' => ['m4a', FileType::T_AUDIO, false, 'audio'],
        'Realaudio' => ['ra,ram', FileType::T_AUDIO],
        'Windows Audio' => ['wma', FileType::T_AUDIO],
        'Rich Text' => ['rtf', FileType::T_DOCUMENT],
        'JavaScript' => ['js', FileType::T_TEXT],
        'PDF' => ['pdf', FileType::T_DOCUMENT],
        'Photoshop' => ['psd', FileType::T_IMAGE],
        'MPEG' => ['mpeg,mpg', FileType::T_VIDEO],
        'MS Excel' => ['xla,xls,xlsx,xlt,xlw', FileType::T_DOCUMENT],
        'MS Powerpoint' => ['pps,ppt,pptx,pot', FileType::T_DOCUMENT],
        'TAR Archive' => ['tar', FileType::T_APPLICATION],
        'Zip Archive' => ['zip', FileType::T_APPLICATION],
        'GZip Archive' => ['gz,gzip', FileType::T_APPLICATION],
        'OGG' => ['ogg', FileType::T_AUDIO, false, 'audio'],
        'OGG Video' => ['ogv', FileType::T_VIDEO, false, 'video'],
        'WebM' => ['webm', FileType::T_VIDEO, false, 'video'],
    ],

    /*
     * Importer Attributes
     */
    'importer_attributes' => [
        'width' => ['Width', 'NUMBER', false],
        'height' => ['Height', 'NUMBER', false],
        'duration' => ['Duration', 'NUMBER', false],
    ],

    /*
     * Importer processors
     */
    'import_processors' => [
        'ccm.file.exists' => Concrete\Core\File\Import\Processor\FileExistingValidator::class,
        'ccm.file.extension' => Concrete\Core\File\Import\Processor\FileExtensionValidator::class,
        'ccm.image.autorotate' => Concrete\Core\File\Import\Processor\ImageAutorotator::class,
        'ccm.image.svg' => Concrete\Core\File\Import\Processor\SvgProcessor::class,
        'ccm.image.resize' => Concrete\Core\File\Import\Processor\ImageSizeConstrain::class,
        'ccm.image.thumbnails' => Concrete\Core\File\Import\Processor\ThumbnailGenerator::class,
    ],

    /*
     * Assets
     */
    'assets' => [
        'google-charts' => [
            [
                'javascript',
                'https://www.gstatic.com/charts/loader.js',
                ['local' => false],
            ],
        ],

        'jquery' => [
            [
                'javascript',
                'js/jquery.js',
                ['position' => Asset::ASSET_POSITION_HEADER, 'minify' => false, 'combine' => false],
            ],
        ],
        'jquery/ui' => [
            ['javascript', 'js/jquery-ui.js', ['minify' => false, 'combine' => false]],
            ['javascript-localized', '/ccm/assets/localization/jquery/ui/js'],
            ['css', 'css/jquery-ui.css', ['minify' => false]],
        ],
        'jquery/visualize' => [
            ['javascript', 'js/jquery-visualize.js', ['minify' => false, 'combine' => false]],
            ['css', 'css/jquery-visualize.css', ['minify' => false]],
        ],
        'jquery/touch-punch' => [
            ['javascript', 'js/jquery-ui-touch-punch.js'],
        ],
        'jquery/tristate' => [
            ['javascript', 'js/jquery-tristate.js'],
        ],
        'select2' => [
            ['javascript', 'js/select2.js', ['minify' => false, 'combine' => false]],
            ['javascript-localized', '/ccm/assets/localization/select2/js'],
            ['css', 'css/select2.css', ['minify' => false]],
        ],
        'selectize' => [
            ['javascript', 'js/selectize.js', ['minify' => false, 'combine' => false]],
            ['css', 'css/selectize.css', ['minify' => false]],
        ],
        'underscore' => [
            ['javascript', 'js/underscore.js', ['minify' => false]],
        ],
        'backbone' => [
            ['javascript', 'js/backbone.js', ['minify' => false]],
        ],
        'dropzone' => [
            ['javascript', 'js/dropzone.js'],
            ['javascript-localized', '/ccm/assets/localization/dropzone/js'],
            ['css', 'css/dropzone.css', ['minify' => false]],
        ],
        'jquery/form' => [
            ['javascript', 'js/jquery-form.js'],
        ],
        'picturefill' => [
            ['javascript', 'js/picturefill.js', ['minify' => false]],
        ],
        'responsive-slides' => [
            ['javascript', 'js/responsive-slides.js', ['minify' => false]],
            ['css', 'css/responsive-slides.css', ['minify' => false]],
        ],
        'fullcalendar' => [
            ['javascript', 'js/fullcalendar/fullcalendar.js', ['minify' => false, 'combine' => false]],
            ['css', 'js/fullcalendar/fullcalendar.css', ['minify' => false]],
        ],
        'fullcalendar/localization' => [
            ['javascript', 'js/fullcalendar/locale-all.js', ['minify' => false, 'combine' => false]],
        ],
        'fullcalendar/print' => [
            ['css', 'js/fullcalendar/fullcalendar.print.css', ['minify' => false]],
        ],
        'vue' => [
            ['javascript', 'js/vue.js', ['minify' => false, 'combine' => false]],
        ],
        'html5-shiv' => [
            [
                'javascript-conditional',
                'js/ie/html5-shiv.js',
                ['conditional' => 'lt IE 9'],
            ],
        ],
        'respond' => [
            [
                'javascript-conditional',
                'js/ie/respond.js',
                ['conditional' => 'lt IE 9'],
            ],
        ],
        'spectrum' => [
            ['javascript', 'js/spectrum.js', ['minify' => false]],
            ['css', 'css/spectrum.css', ['minify' => false]],
        ],
        'core/composer-save-coordinator' => [
            ['javascript', 'js/composer-save-coordinator.js', ['minify' => false]],
        ],
        'font-awesome' => [
            ['css', 'css/font-awesome.css', ['minify' => false]],
        ],
        'core/events' => [
            ['javascript', 'js/events.js', ['minify' => false]],
        ],
        'core/asset-loader' => [
            ['javascript', 'js/asset-loader.js', ['minify' => false]],
        ],
        'core/style-customizer' => [
            ['javascript', 'js/style-customizer.js', ['minify' => false]],
            ['css', 'css/style-customizer.css', ['minify' => false]],
        ],
        'core/localization' => [
            ['javascript-localized', '/ccm/assets/localization/core/js'],
        ],
        'core/frontend/parallax-image' => [
            ['javascript', 'js/frontend/parallax-image.js', ['minify' => false]],
        ],
        'core/frontend/thumbnail-builder' => [
            ['javascript', 'js/frontend/thumbnail-builder.js'],
        ],
        'core/imageeditor/control/position' => [
            ['css', 'css/image-editor/controls/position.css'],
            ['javascript', 'js/image-editor/controls/position.js'],
        ],
        'core/imageeditor/control/colors' => [
            ['javascript', 'js/image-editor/controls/colors.js'],
        ],
        'core/duration' => [
            ['javascript', 'js/duration.js'],
            ['css', 'css/duration.css'],
        ],
        'core/imageeditor/control/filter' => [
            ['css', 'css/image-editor/controls/filter.css'],
            ['javascript', 'js/image-editor/controls/filter.js'],
        ],
        'core/imageeditor/filter/gaussian_blur' => [
            ['css', 'css/image-editor/filters/gaussian_blur.css'],
            ['javascript', 'js/image-editor/filters/gaussian_blur.js'],
        ],
        'core/imageeditor/filter/none' => [
            ['css', 'css/image-editor/filters/none.css'],
            ['javascript', 'js/image-editor/filters/none.js'],
        ],
        'core/imageeditor/filter/sepia' => [
            ['css', 'css/image-editor/filters/sepia.css'],
            ['javascript', 'js/image-editor/filters/sepia.js'],
        ],
        'core/imageeditor/filter/vignette' => [
            ['css', 'css/image-editor/filters/vignette.css'],
            ['javascript', 'js/image-editor/filters/vignette.js'],
        ],
        'core/imageeditor/filter/grayscale' => [
            ['css', 'css/image-editor/filters/grayscale.css'],
            ['javascript', 'js/image-editor/filters/grayscale.js'],
        ],
        'jquery/awesome-rating' => [
            ['javascript', 'js/jquery-awesome-rating.js', ['minify' => false]],
            ['css', 'css/jquery-awesome-rating.css', ['minify' => false]],
        ],
        'jquery/fileupload' => [
            ['javascript', 'js/jquery-fileupload.js'],
        ],
        'jquery/textcounter' => [
            ['javascript', 'js/textcounter.js'],
        ],
        'swfobject' => [
            ['javascript', 'js/swfobject.js'],
        ],
        'redactor' => [
            ['javascript', 'js/redactor.js', ['minify' => false]],
            ['javascript-localized', '/ccm/assets/localization/redactor/js'],
            ['css', 'css/redactor.css'],
        ],
        'ace' => [
            ['javascript', 'js/ace/ace.js', ['minify' => false]],
        ],
        'backstretch' => [
            ['javascript', 'js/backstretch.js'],
        ],
        'background-check' => [
            ['javascript', 'js/background-check.js'],
        ],
        /*
        'dynatree' => array(
            array('javascript', 'js/dynatree.js', array('minify' => false)),
            array('javascript-localized', '/ccm/assets/localization/dynatree/js', array('minify' => false)),
            array('css', 'css/dynatree.css', array('minify' => false)),
        ),
        */
        'fancytree' => [
            ['javascript', 'js/fancytree.js', ['minify' => false, 'version' => '2.18.0']],
            ['javascript-localized', '/ccm/assets/localization/fancytree/js', ['minify' => false]],
            ['css', 'css/fancytree.css', ['minify' => false]],
        ],
        'moment' => [
            ['javascript', 'js/moment.js', ['minify' => false, 'version' => '2.24.0']],
            ['javascript-localized', '/ccm/assets/localization/moment/js'],
        ],
        'moment-timezone' => [
            ['javascript', 'js/moment-timezone-with-data.js', ['minify' => false, 'version' => '0.5.25']],
        ],
        'bootstrap/dropdown' => [
            ['javascript', 'js/bootstrap/dropdown.js'],
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'bootstrap/tooltip' => [
            ['javascript', 'js/bootstrap/tooltip.js'],
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'bootstrap/popover' => [
            ['javascript', 'js/bootstrap/popover.js'],
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'bootstrap/collapse' => [
            ['javascript', 'js/bootstrap/collapse.js'],
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'bootstrap/alert' => [
            ['javascript', 'js/bootstrap/alert.js'],
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'bootstrap/button' => [
            ['javascript', 'js/bootstrap/button.js'],
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'bootstrap/transition' => [
            ['javascript', 'js/bootstrap/transition.js'],
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'bootstrap' => [
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'core/app' => [
            ['javascript', 'js/app.js', ['minify' => false, 'combine' => false]],
            ['css', 'css/app.css', ['minify' => false]],
        ],
        'bootstrap-editable' => [
            ['javascript', 'js/bootstrap-editable.js', ['minify' => false]],
        ],
        'core/app/editable-fields' => [
            ['css', 'css/editable-fields.css', ['minify' => false]],
        ],
        'kinetic' => [
            ['javascript', 'js/kinetic.js'],
        ],
        'core/imageeditor' => [
            ['javascript', 'js/image-editor.js'],
            ['javascript-localized', '/ccm/assets/localization/imageeditor/js'],
            ['css', 'css/image-editor.css'],
        ],
        'dashboard' => [
            ['javascript', 'js/dashboard.js'],
        ],
        'core/frontend/captcha' => [
            ['css', 'css/frontend/captcha.css'],
        ],
        'core/frontend/pagination' => [
            ['css', 'css/frontend/pagination.css'],
        ],
        'core/frontend/errors' => [
            ['css', 'css/frontend/errors.css'],
        ],
        'core/file-manager' => [
            ['javascript', 'js/file-manager.js', ['minify' => false]],
            ['css', 'css/file-manager.css', ['minify' => false]],
        ],
        'core/file-uploader' => [
            ['javascript', 'js/file-uploader.js', ['minify' => false]],
        ],
        'core/express' => [
            ['javascript', 'js/express.js', ['minify' => false]],
        ],
        'core/sitemap' => [
            ['javascript', 'js/sitemap.js', ['minify' => false]],
            ['css', 'css/sitemap.css', ['minify' => false]],
        ],
        'core/users' => [
            ['javascript', 'js/users.js', ['minify' => false]],
        ],
        'core/calendar/event-selector' => [
            ['javascript', 'js/calendar/event-selector.js', ['minify' => false]],
        ],
        'core/calendar/admin' => [
            ['javascript', 'js/calendar/admin.js', ['minify' => false]],
        ],
        'core/avatar' => [
            ['javascript', 'js/components/avatar.bundle.js', ['minify' => false]],
        ],
        'core/notification' => [
            ['javascript', 'js/notification.js', ['minify' => false]],
        ],
        'core/draft_list' => [
            ['javascript', 'js/draft-list.js', ['minify' => false]],
        ],
        'core/tree' => [
            ['javascript', 'js/tree.js', ['minify' => false]],
        ],
        'core/groups' => [
            ['javascript', 'js/groups.js', ['minify' => false]],
        ],
        'core/gathering' => [
            ['javascript', 'js/gathering.js'],
        ],
        'core/gathering/display' => [
            ['css', 'css/gathering/display.css'],
        ],
        'core/gathering/base' => [
            ['css', 'css/gathering/base.css'],
        ],
        'core/conversation' => [
            ['javascript', 'js/conversations.js'],
            ['javascript-localized', '/ccm/assets/localization/conversations/js'],
            ['css', 'css/conversations.css'],
        ],
        'core/lightbox' => [
            ['javascript', 'js/jquery-magnific-popup.js'],
            ['css', 'css/jquery-magnific-popup.css'],
        ],
        'core/lightbox/launcher' => [
            ['javascript', 'js/lightbox.js'],
        ],
        'core/account' => [
            ['javascript', 'js/account.js'],
            ['css', 'css/account.css'],
        ],
        'core/translator' => [
            ['javascript', 'js/translator.js', ['minify' => false]],
            ['javascript-localized', '/ccm/assets/localization/translator/js'],
            ['css', 'css/translator.css', ['minify' => false]],
        ],
        'core/country-stateprovince-link' => [
            ['javascript', 'js/country-stateprovince-link.js', ['minify' => false]],
        ],
        'core/country-data-link' => [
            ['javascript', 'js/country-data-link.js', ['minify' => false]],
        ],
    ],
    'asset_groups' => [
        'jquery/ui' => [
            [
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['css', 'jquery/ui'],
            ],
        ],
        'jquery/visualize' => [
            [
                ['javascript', 'jquery/visualize'],
                ['css', 'jquery/visualize'],
            ],
        ],
        /*
         * @deprecated
         */
        'select2' => [
            [
                ['javascript', 'select2'],
                ['javascript-localized', 'select2'],
                ['css', 'select2'],
            ],
        ],
        'selectize' => [
            [
                ['javascript', 'selectize'],
                ['css', 'selectize'],
            ],
        ],
        'fullcalendar' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'moment'],
                ['javascript', 'fullcalendar'],
                ['javascript', 'fullcalendar/localization'],
                ['css', 'fullcalendar'],
            ],
        ],
        'dropzone' => [
            [
                ['javascript', 'dropzone'],
                ['javascript-localized', 'dropzone'],
                ['css', 'dropzone'],
            ],
        ],
        'responsive-slides' => [
            [
                ['javascript', 'responsive-slides'],
                ['css', 'responsive-slides'],
            ],
        ],
        'ace' => [
            [
                ['javascript', 'ace'],
            ],
        ],
        'core/avatar' => [
            [
                ['javascript', 'dropzone'],
                ['javascript-localized', 'dropzone'],
                ['javascript', 'vue'],
                ['javascript', 'core/avatar'],
            ],
        ],
        'core/notification' => [
            [
                ['javascript', 'core/notification'],
            ],
        ],
        'core/draft_list' => [
            [
                ['javascript', 'core/draft_list'],
                ['javascript', 'core/events'],
            ],
        ],
        'core/colorpicker' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'core/events'],
                ['javascript-localized', 'core/localization'],
                ['javascript', 'spectrum'],
                ['css', 'spectrum'],
            ],
        ],
        'font-awesome' => [
            [
                ['css', 'font-awesome'],
            ],
        ],
        'core/rating' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'jquery/awesome-rating'],
                ['css', 'font-awesome'],
                ['css', 'jquery/awesome-rating'],
            ],
        ],
        'core/frontend/thumbnail-builder' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'underscore'],
                ['javascript', 'core/frontend/thumbnail-builder'],
            ],
        ],
        'core/style-customizer' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'core/events'],
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript', 'core/colorpicker'],
                ['javascript-localized', 'core/localization'],
                ['javascript', 'core/app'],
                ['javascript', 'jquery/fileupload'],
                ['javascript', 'core/file-manager'],
                ['javascript', 'core/style-customizer'],
                ['css', 'core/app'],
                ['css', 'core/file-manager'],
                ['css', 'jquery/ui'],
                ['css', 'core/colorpicker'],
                ['css', 'core/style-customizer'],
            ],
        ],
        'jquery/fileupload' => [
            [
                ['javascript', 'jquery/fileupload'],
            ],
        ],
        'swfobject' => [
            [
                ['javascript', 'swfobject'],
            ],
        ],
        'redactor' => [
            [
                ['javascript', 'redactor'],
                ['javascript-localized', 'redactor'],
                ['css', 'redactor'],
                ['css', 'font-awesome'],
            ],
        ],
        'moment' => [
            [
                ['javascript', 'moment'],
                ['javascript', 'moment-timezone'],
                ['javascript-localized', 'moment'],
            ],
        ],
        'fancytree' => [
            [
                ['javascript', 'fancytree'],
                ['javascript-localized', 'fancytree'],
                ['css', 'fancytree'],
            ],
        ],
        'core/app' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'core/events'],
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript', 'bootstrap/dropdown'],
                ['javascript', 'bootstrap/tooltip'],
                ['javascript', 'bootstrap/popover'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript-localized', 'core/localization'],
                ['javascript', 'core/app'],
                ['css', 'core/app'],
                ['css', 'font-awesome'],
                ['css', 'jquery/ui'],
            ],
        ],
        'core/app/editable-fields' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'bootstrap/dropdown'],
                ['javascript', 'bootstrap/tooltip'],
                ['javascript', 'bootstrap/popover'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'core/events'],
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript-localized', 'core/localization'],
                ['javascript', 'core/app'],
                ['javascript', 'bootstrap-editable'],
                ['css', 'core/app/editable-fields'],
                ['javascript', 'jquery/fileupload'],
            ],
        ],
        'core/imageeditor' => [
            [
                ['javascript', 'kinetic'],
                ['javascript-localized', 'core/imageeditor'],
                ['javascript', 'core/imageeditor'],
                ['css', 'core/imageeditor'],
            ],
        ],
        'dashboard' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'jquery/touch-punch'],
                ['javascript', 'selectize'],
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript', 'dashboard'],
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'bootstrap/dropdown'],
                ['javascript', 'bootstrap/tooltip'],
                ['javascript', 'bootstrap/collapse'],
                ['javascript', 'bootstrap/popover'],
                ['javascript', 'bootstrap/transition'],
                ['javascript', 'bootstrap/alert'],
                ['javascript-localized', 'core/localization'],
                ['javascript', 'core/app'],
                ['javascript-conditional', 'respond'],
                ['javascript-conditional', 'html5-shiv'],
                ['css', 'selectize'],
                ['css', 'core/app'],
                ['css', 'jquery/ui'],
                ['css', 'font-awesome'],
            ],
        ],
        'core/file-manager' => [
            [
                ['css', 'core/app'],
                ['css', 'jquery/ui'],
                ['css', 'core/file-manager'],
                ['css', 'selectize'],
                ['javascript', 'dropzone'],
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'bootstrap/tooltip'],
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'selectize'],
                ['javascript-localized', 'core/localization'],
                ['javascript-localized', 'dropzone'],
                ['javascript', 'core/app'],
                ['javascript', 'jquery/fileupload'],
                ['javascript', 'core/tree'],
                ['javascript', 'core/file-uploader'],
                ['javascript', 'core/file-manager'],
            ],
        ],
        'core/file-uploader' => [
            [
                ['css', 'dropzone'],
                ['javascript', 'dropzone'],
                ['javascript-localized', 'dropzone'],
                ['javascript', 'core/file-uploader'],
            ],
        ],
        'core/duration' => [
            [
                ['css', 'selectize'],
                ['css', 'jquery/ui'],
                ['css', 'core/duration'],
                ['javascript', 'selectize'],
                ['javascript', 'moment'],
                ['javascript', 'moment-timezone'],
                ['javascript', 'core/duration'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
            ],
        ],

        'core/file-folder-selector' => [
            [
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'underscore'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'fancytree'],
                ['javascript-localized', 'fancytree'],
                ['javascript', 'core/tree'],
                ['css', 'fancytree'],
            ],
        ],

        'core/sitemap' => [
            [
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'fancytree'],
                ['javascript', 'selectize'],
                ['javascript-localized', 'fancytree'],
                ['javascript-localized', 'core/localization'],
                ['javascript', 'core/app'],
                ['javascript', 'core/sitemap'],
                ['css', 'fancytree'],
                ['css', 'selectize'],
                ['css', 'core/sitemap'],
            ],
        ],
        'core/users' => [
            [
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'underscore'],
                ['javascript', 'core/users'],
            ],
        ],
        'core/calendar/event-selector' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'core/events'],
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript', 'core/calendar/event-selector'],
                ['css', 'core/app'],
                ['css', 'jquery/ui'],
            ],
        ],
        'core/calendar/admin' => [
            [
                ['javascript-localized', 'core/localization'],
                ['javascript', 'jquery'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'core/events'],
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript', 'core/app'],
                ['javascript', 'core/calendar/admin'],
                ['css', 'core/app'],
                ['css', 'jquery/ui'],
            ],
        ],
        'core/express' => [
            [
                ['javascript', 'underscore'],
                ['javascript', 'backbone'],
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'bootstrap/tooltip'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'core/localization'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'core/app'],
                ['javascript', 'core/express'],
                ['css', 'core/app'],
                ['css', 'core/express'],
            ],
        ],
        'core/topics' => [
            [
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'underscore'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'fancytree'],
                ['javascript-localized', 'fancytree'],
                ['javascript', 'core/tree'],
                ['css', 'fancytree'],
            ],
        ],
        'core/tree' => [
            [
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'underscore'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'fancytree'],
                ['javascript-localized', 'fancytree'],
                ['javascript', 'core/tree'],
                ['css', 'fancytree'],
            ],
        ],
        'core/groups' => [
            [
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'underscore'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'fancytree'],
                ['javascript-localized', 'fancytree'],
                ['javascript', 'core/tree'],
                ['css', 'fancytree'],
            ],
        ],
        'core/gathering' => [
            [
                ['javascript', 'core/gathering'],
                ['javascript', 'redactor'],
                ['javascript-localized', 'redactor'],
                ['css', 'core/gathering/base'],
                ['css', 'core/conversation'],
                ['css', 'core/gathering/display'],
                ['css', 'redactor'],
            ],
        ],
        'core/conversation' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'jquery/ui'],
                ['javascript-localized', 'jquery/ui'],
                ['javascript', 'underscore'],
                ['javascript', 'core/lightbox'],
                ['javascript', 'dropzone'],
                ['javascript-localized', 'dropzone'],
                ['javascript', 'bootstrap/dropdown'],
                ['javascript', 'core/events'],
                ['javascript', 'core/asset-loader'],
                ['javascript', 'core/conversation'],
                ['javascript-localized', 'core/conversation'],
                ['css', 'core/conversation'],
                ['css', 'core/frontend/errors'],
                ['css', 'font-awesome'],
                ['css', 'bootstrap/dropdown'],
                ['css', 'core/lightbox'],
                ['css', 'jquery/ui'],
            ],
            true,
        ],
        'core/lightbox' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'core/lightbox'],
                ['javascript', 'core/lightbox/launcher'],
                ['css', 'core/lightbox'],
            ],
        ],
        'core/account' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'core/account'],
                ['javascript', 'bootstrap/dropdown'],
                ['css', 'bootstrap/dropdown'],
                ['css', 'core/account'],
            ],
        ],
        'core/translator' => [
            [
                ['javascript', 'core/translator'],
                ['javascript-localized', 'core/translator'],
                ['css', 'core/translator'],
            ],
        ],
        'core/country-stateprovince-link' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'core/country-stateprovince-link'],
            ],
        ],
        'core/country-data-link' => [
            [
                ['javascript', 'jquery'],
                ['javascript', 'core/country-data-link'],
            ],
        ],
        /* @deprecated keeping this around because certain themes reference it and we don't want to break them. */
        'core/legacy' => [
            [
            ],
        ],
    ],
    // HTTP Client options
    'http_client' => [
        // FALSE to stop from verifying the peer's certificate.
        'sslverifypeer' => true,
        // FALSE to stop from verifying the peer's name (used only with Socket connections, not with cURL ones).
        'sslverifypeername' => false,
        // The name of a file holding one or more certificates to verify the peer with (used only if sslverifypeer is not falsy).
        'sslcafile' => null,
        // A directory that holds multiple CA certificates to verify the peer with (used only if sslverifypeer is not falsy).
        'sslcapath' => null,
        // The number of seconds to wait while trying to connect.
        'connecttimeout' => 5,
        // The maximum number of seconds to allow response from remote server.
        'timeout' => 60,
        // Whether to enable keep-alive connections with the server. Useful and might improve performance if several consecutive requests to the same server are performed.
        'keepalive' => false,
        // Maximum number of redirections to follow (0 = none).
        'maxredirects' => 5,
        // Whether to strictly adhere to RFC 3986 (in practice, this means replacing "+" with "%20").
        'rfc3986strict' => false,
        // Path to a PEM encoded SSL certificate.
        'sslcert' => null,
        // Passphrase for the SSL certificate file.
        'sslpassphrase' => null,
        // Whether to store last response for later retrieval with getLastResponse(). If set to FALSE, getLastResponse() will return NULL.
        'storeresponse' => true,
        // Directory where to store temporary streams by default (if empty, we'll use the default concrete5 temporry directory).
        'streamtmpdir' => null,
        // Whether to strictly follow the RFC when redirecting (see https://framework.zend.com/manual/2.4/en/modules/zend.http.client.advanced.html#http-redirections )
        'strictredirects' => false,
        // User agent identifier string.
        'useragent' => 'concrete5 CMS',
        // Whether to pass the cookie value through urlencode/urldecode. Enabling this breaks support with some web servers. Disabling this limits the range of values the cookies can contain.
        'encodecookies' => true,
        // HTTP protocol version (usually '1.1' or '1.0').
        'httpversion' => '1.1',
        // SSL transport layer ['ssl', 'sslv2', 'sslv3', 'tls'] (applicable only to Socket adapters).
        'ssltransport' => 'tls',
        // Whether to allow self-signed certificates (applicable only to Socket adapters).
        'sslallowselfsigned' => false,
        // Whether to use persistent TCP connections (applicable only to Socket adapters).
        'persistent' => false,
        // The name of a class that implements Psr\Log\LoggerInterface
        'logger' => null,
    ],

    // HTTP middleware for processing http requests
    'middleware' => [
        [
            'priority' => 1,
            'class' => \Concrete\Core\Http\Middleware\ApplicationMiddleware::class,
        ],
        'core_cookie' => \Concrete\Core\Http\Middleware\CookieMiddleware::class,
        'core_xframeoptions' => \Concrete\Core\Http\Middleware\FrameOptionsMiddleware::class,
        'core_thumbnails' => '\Concrete\Core\Http\Middleware\ThumbnailMiddleware',
    ],
];
