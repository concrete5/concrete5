<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\File\Filesystem;
$filesystem = new Filesystem();
$rootfolder = $filesystem->getRootFolder();
$folders = $rootfolder->getHierarchicalNodesOfType('file_folder', 1, true, true);
?>

<?php /* ?>
<div class="form-group" id="ccm-folder-search">
    <form class="form-inline">
        <input type="search" class="form-control input-sm" data-field="folder-search" autocomplete="off" placeholder="<?=t('Filter Folders')?>" />
    </form>
</div>
 */ ?>

<div class="form-group" id="ccm-folder-list">
    <?php
    $selector = new \Concrete\Core\Form\Service\Widget\FileFolderSelector();
    print $selector->selectFileFolder('folderID', isset($folderID) ? $folderID : null);
    ?>
</div>

<?php /* ?>

<script type="text/javascript">
    $(function() {
        $('input[data-field=folder-search]').liveUpdate('ccm-folder-list', 'folder').closest('form').unbind('submit.liveupdate');
    });
</script>

 <? */ ?>