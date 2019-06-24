<?php
defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\ImageEditor\ImageEditor;
use Concrete\Core\File\Image\BitmapFormat;
use Whoops\Exception\ErrorException;

$editorid = substr(sha1(time()), 0, 5); // Just enough entropy.

$u = new User();
$form = Loader::helper('form');
/** @var FileVersion $fv */
$f = $fv->getFile();
$fp = new Permissions($f);
if (!$fp->canEditFileContents()) {
    die(t("Access Denied."));
}
$token = Core::make('token')->generate();

$req = ResponseAssetGroup::get();
$req->requireAsset('core/imageeditor');

/** @var ImageEditor $editor */
if (!isset($editor) || !$editor) {
    $editor = \Core::make('editor/image/core');
}

$filters = $editor->getFilterList();
$controls = $editor->getControlList()

?>
    <div class='table ccm-ui'>
        <div class='editorcontainer'>
            <div id='<?= $editorid ?>' class='Editor'></div>
            <div class='bottomBar'></div>
        </div>
        <div class='controls'>
            <div class='controlscontainer'>
                <div class='editorcontrols'>
                    <div class='control-sets'>
                        <?php
                        if (!$controls) {
                            echo "&nbsp;";
                        }
                        /** @var \Concrete\Core\ImageEditor\EditorExtensionInterface $control */
                        foreach ((array) $controls as $control) {
                            $control_handle = $control->getHandle();
                            $assets = $control->getAssets();
                            $javascript_asset = $control->getExtensionAsset();

                            foreach ($assets as $asset) {
                                $req->addOutputAsset($asset);
                            }
                            ?>
                            <div class="controlset controlset-<?= $control_handle ?> control control-<?= $control_handle ?>"
                                 data-namespace="<?= $control_handle ?>"
                                 data-src="<?= $javascript_asset->getAssetUrl() ?>">
                                <h4><?= $control->getName() ?></h4>

                                <div class="control">
                                    <div class="contents">
                                        <?php
                                        try {
                                            $view = $control->getView();
                                            $view->addScopeItems(array('editor' => $editor, 'fv' => $fv));
                                            echo $view->render();
                                        } catch (ErrorException $e) {
                                            echo t("Invalid View: '{$e->getMessage()}''");
                                        }
                            ?>
                                    </div>
                                </div>
                                <div class='border'></div>
                            </div>
                        <?php

                        }
                        ?>
                    </div>
                </div>
                <div class='save'>
                    <button class='cancel btn'><?= t('Cancel')?></button>
                    <button class='save btn pull-right btn-primary'><?= t('Save')?></button>
                </div>
            </div>
        </div>
    </div>

<?php
if (empty($settings)) {
    $settings = array();
}
$fnames = array();

foreach ($filters as $filter) {
    $assets = $filter->getAssets();
    $extension_asset = $filter->getExtensionAsset();
    $filter_handle = $filter->getHandle();

    foreach ($assets as $handle => $asset) {
        $req->addOutputAsset($asset);
    }

    $fnames[$filter_handle] = array(
        'src' => $extension_asset->getAssetURL(),
        'name' => h($filter->getName()),
        "selector" => ".filter.filter-{$filter_handle}",
    );
}
$url = $fv->getRelativePath();
?>
    <script>
        $(function () {
            _.defer(function () {
                var defaults = {
                    saveUrl: CCM_DISPATCHER_FILENAME + '/tools/required/files/importers/imageeditor',
                    src: '<?=$url?>',
                    fID: <?= $fv->getFileID() ?>,
                    token: '<?= $token ?>',
                    controlsets: {},
                    filters: {},
                    debug: false,
                    jpegCompression: <?= Core::make(BitmapFormat::class)->getDefaultJpegQuality() / 100 ?>,
                    mime: '<?= $fv->getMimeType() ?>'<?php
                    if (\Core::make('config')->get('concrete.misc.image_editor_cors_policy.enable_cross_origin', false) && preg_match('/^http[s]?:\/\/.*$/', $url)) {
                        echo ",\n                    crossOrigin: '" . (\Core::make('config')->get('concrete.misc.image_editor_cors_policy.anonymous_request', true) ? 'anonymous' : 'use-credentials') . "'";
                    }
                    ?>
                },
                    settings = _.extend(defaults, <?= json_encode($settings) ?>);
                $('div.controlset', 'div.controls').each(function () {
                    settings.controlsets[$(this).attr('data-namespace')] = {
                        src: $(this).attr('data-src'),
                        element: $(this).children('div.control').children('div.contents')
                    }
                });
                $('div.component', 'div.controls').each(function () {
                    settings.components[$(this).attr('data-namespace')] = {
                        src: $(this).attr('>getdata-src'),
                        element: $(this).children('div.control').children('div.contents')
                    }
                });
                settings.filters = <?= json_encode($fnames); ?>;
                var editor = $('div#<?=$editorid?>.Editor');
                window.im = editor.closest('.ui-dialog-content').css('padding', 0).end().ImageEditor(settings);
            });

            Concrete.event.unbind('ImageEditorDidSave.core');
            <?php
            if (!isset($no_bind) || !$no_bind) {
                ?>
                Concrete.event.bind('ImageEditorDidSave.core', function(e) {
                    Concrete.event.unbind(e);
                    window.location = window.location;
                    window.location.reload();
                });
                <?php

            }
            ?>
        });
    </script>
