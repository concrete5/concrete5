<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php
$c = Page::getCurrentPage();
?>

<?php if (isset($success) && $success) { ?>
    <div class="alert alert-success"><?=$success?></div>
<?php } ?>

<?php if ($tableName) { ?>
    <h2><?=$tableName?></h2>
<?php } ?>

<?php if ($tableDescription) {  ?>
    <p><?=$tableDescription?></p>
<?php } ?>


<?php if ($enableSearch) { ?>
    <form method="get" action="<?=$c->getCollectionLink()?>">
        <div class="row row-cols-auto align-items-center">
            <div class="form-group col-auto">
                <?=$form->label('keywords', t('Keyword Search'))?>
                <?=$form->text('keywords')?>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary" name="search"><?=t('Search')?></button>
            </div>
            <div class="col-auto">
                <?php if (count($tableSearchProperties)) { ?>
                    <a href="#" data-document-library-advanced-search="<?=$bID?>"
                       class="ccm-block-document-library-advanced-search"><?=t('Advanced Search')?></a>
                <?php } ?>
            </div>
            <div class="col-auto">
                <?php if ($canAddFiles) { ?>
                    <a href="#" data-document-library-add-files="<?=$bID?>"
                       class="ccm-block-document-library-add-files"><?=t('Add Files')?></a>
                <?php } ?>
            </div>
        </div>

        <?php if (count($tableSearchProperties)) { ?>
            <div data-document-library-advanced-search-fields="<?=$bID?>"
                 class="ccm-block-document-library-advanced-search-fields">
                <input type="hidden" name="advancedSearchDisplayed" value="">
                <?php foreach($tableSearchProperties as $column) { ?>
                    <h4><?=$controller->getColumnTitle($column)?></h4>
                    <div><?=$controller->getSearchValue($column)?></div>
                <?php } ?>
            </div>
        <?php } ?>
        <br/>
    </form>
<?php } else if ($canAddFiles) { ?>
    <div>
        <a href="#" data-document-library-add-files="<?=$bID?>"
           class="ccm-block-document-library-add-files"><?=t('Add Files')?></a>
    </div>
<br/>
<?php } ?>

<?php if ($canAddFiles) { ?>
    <div data-document-library-upload-action="<?=$view->action('upload')?>" data-document-library-add-files="<?=$bID?>" class="ccm-block-document-library-add-files-uploader">
        <div class="ccm-block-document-library-add-files-pending"><?=t('Upload Files')?></div>
        <div class="ccm-block-document-library-add-files-uploading"><?=t('Uploading')?> <i class="fa fa-spin fa-spinner"></i></div>
        <input type="file" name="file" />
        <?=Core::make('token')->output()?>
    </div>
<?php } ?>

<?php
if (isset($breadcrumbs) && $breadcrumbs) { ?>
    <div class='ccm-block-document-library-breadcrumbs'>
        <?php
        $first = true;
        foreach ($breadcrumbs as $url => $name) {
            if (!$first) {
                echo "&gt;";
            }
            $first = false;
            ?>
            <a href="<?= $url ?>"><?= $name ?></a>
            <?php
        }
        ?>
    </div>
    <?php
}
?>

<?php if (count($results)) {?>



    <div id="ccm-block-document-library-wrapper-<?=$bID?>">

    <table
        id="ccm-block-document-library-table-<?=$bID?>"
        class="table ccm-block-document-library-table <?php if ($tableStriped) { ?><?php } ?>">
    <thead>
    <tr>
        <?php foreach($tableColumns as $column) { ?>
            <th class="<?=$controller->getColumnClass($list, $column)?>">
                <?php if ($controller->isColumnSortable($column)) { ?>
                    <a href="<?=$controller->getSortAction($c, $list, $column)?>"><?=$controller->getColumnTitle($column)?></a>
                <?php } else { ?>
                    <span><?=$controller->getColumnTitle($column)?></span>
                <?php } ?>
            </th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php
    $rowClass = 'ccm-block-document-library-row-a';
    foreach($results as $f) { ?>
        <tr class="<?=$rowClass?>">
        <?php foreach($tableColumns as $column) { ?>
            <td><?=$controller->getColumnValue($column, $f)?></td>
        <?php } ?>
        </tr>
        <?php
        if (count($tableExpandableProperties)) {
            if ($f instanceof \Concrete\Core\Tree\Node\Type\File) {
                $fileID = $f->getTreeNodeFileID();
            } else {
                $fileID = $f->getTreeNodeID();
            }
            ?>
            <tr class="ccm-block-document-library-table-expanded-properties" data-document-library-details="<?=$fileID?>">
                <td colspan="<?=count($tableColumns)?>">
                    <?php foreach($tableExpandableProperties as $column) { ?>
                        <h4><?=$controller->getColumnTitle($column)?></h4>
                        <?=$controller->getColumnValue($column, $f)?>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    <?php
        $rowClass = ($rowClass == 'ccm-block-document-library-row-a') ? 'ccm-block-document-library-row-b' : 'ccm-block-document-library-row-a';
    } ?>
    </tbody>
    </table>
    </div>

    <?php if (isset($pagination)) { ?>
        <?=$pagination?>
    <?php } ?>

<?php } else { ?>
    <p><?=t('No files found.')?></p>
<?php } ?>

<style type="text/css">
<?php if ($headerBackgroundColor) { ?>
    #ccm-block-document-library-table-<?=$bID?> thead th {
        background-color: <?=$headerBackgroundColor?>;
    }
<?php } ?>
<?php if ($headerTextColor) { ?>
    #ccm-block-document-library-table-<?=$bID?> thead th,
    #ccm-block-document-library-table-<?=$bID?> thead th a {
        color: <?=$headerTextColor?>;
    }
    #ccm-block-document-library-table-<?=$bID?> thead th.ccm-block-document-library-active-sort-asc a:after {
        border-color: transparent transparent <?=$headerTextColor?> transparent;
    }
    #ccm-block-document-library-table-<?=$bID?> thead th.ccm-block-document-library-active-sort-desc a:after {
        border-color: <?=$headerTextColor?> transparent transparent transparent;
    }
<?php } ?>
<?php if ($headerBackgroundColorActiveSort) { ?>
    #ccm-block-document-library-table-<?=$bID?> thead th.ccm-block-document-library-active-sort-asc,
    #ccm-block-document-library-table-<?=$bID?> thead th.ccm-block-document-library-active-sort-desc {
        background-color: <?=$headerBackgroundColorActiveSort?>;
    }
<?php } ?>

<?php if ($rowBackgroundColorAlternate && $tableStriped) { ?>
    #ccm-block-document-library-table-<?=$bID?> > tbody > tr.ccm-block-document-library-row-b td {
        background-color: <?=$rowBackgroundColorAlternate?>;
    }
<?php } ?>

<?php if ($heightMode == 'fixed') { ?>
    #ccm-block-document-library-wrapper-<?=$bID?>  {
        height: <?=$fixedHeightSize?>px;
        overflow: scroll;
    }
<?php } ?>
</style>

<script type="text/javascript">
$(function() {
    $.concreteDocumentLibrary({
        'bID': '<?=$bID?>',
        'allowFileUploading': <?php if ($allowFileUploading) { ?>true<?php } else { ?>false<?php } ?>,
        'allowInPageFileManagement': <?php if ($allowInPageFileManagement) { ?>true<?php } else { ?>false<?php } ?>
    });
});
</script>
