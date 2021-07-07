<?php defined('C5_EXECUTE') or die("Access Denied.");
/* @var $h ConcreteDashboardHelper */
$h = Loader::helper('concrete/dashboard');
/* @var $ih ConcreteInterfaceHelper */
$ih = Loader::helper('concrete/ui');
/* @var $nh NavigationHelper */
$nh = Loader::helper('navigation');
/* @var $text TextHelper */
$text = Loader::helper('text');
/* @var $dh \Concrete\Core\Localization\Service\Date */
$dh = Loader::helper('date');
/* @var $urlhelper UrlHelper */
$urlhelper = Loader::helper('url');
/* @var $json JsonHelper */
$json = Loader::helper('json');
/* @var $valt ValidationTokenHelper */
$valt = Loader::helper('validation/token');
/* @var $db DataBase */
$db = Loader::db();
?>
<script>
    jQuery(function ($) {
        var deleteResponse = '<?php echo t('Are you sure you want to delete this form submission?') ?>',
            deleteForm = '<?php echo t('Are you sure you want to delete this form and its form submissions?') ?>',
            deleteFormAnswers = '<?php echo t('Are you sure you want to delete this form submissions?') ?>';
        $('.delete-response').on('click', function (e) {
            if (!confirm(deleteResponse)) {
                e.preventDefault();
            }
        });
        $('.delete-form').on('click', function (e) {
            if (!confirm(deleteForm)) {
                e.preventDefault();
            }
        });
        $('.delete-form-answers').on('click', function (e) {
            if (!confirm(deleteFormAnswers)) {
                e.preventDefault();
            }
        });
    });
</script>
<style>
    ::-webkit-scrollbar {
        -webkit-appearance: none;
        width: 7px;
        height: 6px;
    }

    ::-webkit-scrollbar-thumb {
        border-radius: 4px;
        background-color: rgba(0, 0, 0, .5);
        -webkit-box-shadow: 0 0 1px rgba(255, 255, 255, .5);
    }

    #wide-content-notification {
        margin-left: 5px;
        display: none;
        color: #aaa;
    }

    .form-results-container {
        width: 100%;
        overflow: auto;
    }

</style>
<?php if (!isset($questionSet)) { ?>
    <?php
    $showTable = false;
    foreach ($surveys as $qsid => $survey) {
        $block = Block::getByID((int)$survey['bID']);
        if (is_object($block)) {
            $showTable = true;
            break;
        }
    }

    if ($showTable) { ?>

        <table class="table table-striped">
            <thead>
            <tr>
                <th class="col-sm-5"><?php echo t('Form') ?></th>
                <th><?php echo t('Submissions') ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>

            <?php
            $now = Core::make('date')->getOverridableNow();
            foreach ($surveys as $qsid => $survey) {
                $block = Block::getByID(intval($survey['bID'], 10));
                if (!is_object($block)) {
                    continue;
                }
                $in_use = (int)$db->getOne(
                    <<<'EOT'
SELECT
    count(*)
FROM
    CollectionVersionBlocks
    INNER JOIN Pages
        ON CollectionVersionBlocks.cID = Pages.cID
    INNER JOIN CollectionVersions
        ON CollectionVersions.cID = Pages.cID
WHERE
    CollectionVersions.cvIsApproved = 1 AND (CollectionVersions.cvPublishDate IS NULL OR CollectionVersions.cvPublishDate <= ?) AND (CollectionVersions.cvPublishEndDate IS NULL OR CollectionVersions.cvPublishEndDate >= ?)
    AND CollectionVersionBlocks.cvID = CollectionVersions.cvID
    AND CollectionVersionBlocks.bID = ?
EOT
                    ,
                    [$now, $now, $block->bID]);
                $url = $nh->getLinkToCollection($block->getBlockCollectionObject());
                ?>
                <tr>
                    <td><?php echo $text->entities($survey['surveyName']) ?></td>
                    <td><?php echo $text->entities($survey['answerSetCount']) ?></td>
                    <td style="min-width: 380px" class="text-end">
                        <form method="post" action="" style="display: inline">
                            <input type="hidden" name="qsID" value="<?php echo intval($qsid) ?>"/>
                            <input type="hidden" name="action" value="deleteFormAnswers"/>
                            <?php $valt->output('deleteFormAnswers') ?>
                            <div class="btn-group">
                                <a href="<?php echo URL::to($c->getCollectionPath() . '?qsid=' . $qsid) ?>"
                                   class="btn btn-default btn-sm">
                                    <?php echo t('View Responses') ?>
                                </a>
                                <a class="btn btn-default btn-sm" href="<?php echo $url?>">
                                    <?php echo t('Open Page') ?>
                                </a>
                                <button class="btn btn-danger btn-sm delete-form-answers"
                                        name='ccm-submit-button'>
                                    <?php echo t('Delete Submissions') ?>
                                </button>
                            </div>
                        </form>
                        <?php if (!$in_use) { ?>
                            <form method="post" action="" style="display: inline">
                                <input type="hidden" name="bID" value="<?php echo intval($survey['bID']) ?>"/>
                                <input type="hidden" name="qsID" value="<?php echo intval($qsid) ?>"/>
                                <input type="hidden" name="action" value="deleteForm"/>
                                <?php $valt->output('deleteForm') ?>
                                <?php echo $ih->submit(t('Delete'), false, 'left', 'small error delete-form') ?>
                            </form>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php

} else { ?>
        <p><?php echo t('There are no available forms in your site.') ?></p>
    <?php } ?>
<?php } else {
    $bID = $surveys[$questionSet]['bID'];
    $block = Block::getByID($bID);
    $formPage = null;
    if ($block) {
        $formPage = $block->getBlockCollectionObject();}

    ?>

<div class="ccm-pane-body <?php if (!$paginator || !strlen($paginator->getPages()) > 0) { ?> ccm-pane-body-footer <?php } ?>">
    <?php if (count($answerSets) == 0) { ?>
        <div><?php echo t('No one has yet submitted this form.') ?></div>
    <?php } else { ?>

        <div class="ccm-dashboard-header-buttons">
            <a id="ccm-export-results" class="btn btn-success" href="<?php echo $view->action('csv')?>?qsid=<?php echo $questionSet ?>">
                <i class='fa fa-download'></i> <?php echo t('Export to CSV') ?>
            </a>
        </div>

        <div class="form-results-container">
            <script>
                $(document).ready(function () {
                    if ($('.form-results-container')[0].scrollWidth > $('.ccm-pane-body').width()) {
                        $('#wide-content-notification').show();
                    }
                });
            </script>
            <p id="wide-content-notification"><?php echo t('* Scroll right to view full results'); ?></p>
            <table class="table table-striped">
                <thead>
                <tr>
                    <?php if ($_REQUEST['sortBy'] == 'chrono') { ?>
                    <th class="header headerSortDown">
                        <a href="<?php echo $text->entities($urlhelper->unsetVariable('sortBy')) ?>">
                            <?php } else { ?>
                            <th class="header headerSortUp">
                                <a href="<?php echo $text->entities($urlhelper->setVariable('sortBy', 'chrono')) ?>">
                                    <?php } ?>
                                    <?php echo t('Date') ?>
                                </a>
                            </th>
                            <th><?php echo t('User') ?></th>
                            <?php foreach ($questions as $question): { ?>
                                <th><?php echo $question['question'] ?></th>
                            <?php }endforeach ?>
                            <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($answerSets as $answerSetId => $answerSet) { ?>
                    <tr>
                        <td><?php echo date($dh::DB_FORMAT, strtotime($answerSet['created'])) ?></td>
                        <td><?php
                            if ($answerSet['uID'] > 0) {
                                $ui = UserInfo::getByID($answerSet['uID']);
                                if (is_object($ui)) {
                                    print $ui->getUserName() . ' ';
                                }
                                print t('(User ID: %s)', $answerSet['uID']);
                            }
                            ?></td>
                        <?php foreach ($questions as $questionId => $question) {
                            if ($question['inputType'] == 'fileupload') {
                                $fID = (int)$answerSet['answers'][$questionId]['answer'];
                                $file = File::getByID($fID);
                                if ($fID && $file) {
                                    $fileVersion = $file->getApprovedVersion();
                                    echo '<td><a href="' . $fileVersion->getRelativePath() . '">' .
                                        $text->entities($fileVersion->getFileName()) . '</a></td>';
                                } else {
                                    echo '<td>' . t('File not found') . '</td>';
                                }
                            } else if ($question['inputType'] == 'datetime') {

                                if ($formPage) {
                                    $site = $formPage->getSite();
                                    $timezone = $site->getTimezone();
                                    $date = Core::make('date');
                                    $datetime = $date->formatDateTime($answerSet['answers'][$questionId]['answer'], false, false, $timezone);
                                } else {
                                    $datetime = $answerSet['answers'][$questionId]['answer'];
                                }

                                echo '<td>' . $datetime . '</td>';
                            } else {
                                if ($question['inputType'] == 'text') {
                                    echo '<td>' . $text->entities(
                                            $answerSet['answers'][$questionId]['answerLong']) . '</td>';
                                } else {
                                    echo '<td>' . $text->entities(
                                            $answerSet['answers'][$questionId]['answer']) . '</td>';
                                }
                            }
                        }
                        ?>
                        <td>
                            <form method="post" action="" class='pull-right'>
                                <input type="hidden" name="qsid" value="<?php echo intval($answerSet['questionSetId']) ?>"/>
                                <input type="hidden" name="asid" value="<?php echo intval($answerSet['asID']) ?>"/>
                                <input type="hidden" name="action" value="deleteResponse"/>
                                <?php $valt->output('deleteResponse') ?>
                                <?php echo $ih->submit(t('Delete'), false, 'left', 'btn pull-right btn-danger delete-response') ?>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        </div>
        <?php if ($paginator && strlen($paginator->getPages()) > 0) { ?>
            <div class="ccm-search-results-pagination">
                <ul class="pagination">
                    <li class="prev"><?php echo $paginator->getPrevious() ?></li>

                    <?php // Call to pagination helper's 'getPages' method with new $wrapper var ?>
                    <?php echo $paginator->getPages('li') ?>

                    <li class="next"><?php echo $paginator->getNext() ?></li>
                </ul>
            </div>
        <?php } ?>
    <?php } ?>
<?php } ?>
