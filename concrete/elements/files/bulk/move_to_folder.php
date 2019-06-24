<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php Loader::element('files/move_to_folder', array(
    'isCurrentFolder' => function ($folder) use ($files) {
        if (isset($files[0])) {
            $fileFolderObject = $files[0]->getFileFolderObject();
            if (is_object($fileFolderObject) && $fileFolderObject->getTreeNodeID() === $folder->getTreeNodeID()) {
                return true;
            }
        }
        return false;
    },
    'getRadioButton' => function ($folder, $checked = false) use ($f) {
        $radio = id(new HtmlObject\Input('radio', 'folderID', $folder->getTreeNodeID(), array('checked' => $checked)));
                
        return $radio;
    },
));
?>
