<?php defined('C5_EXECUTE') or die('Access Denied.');

// Content
if ($this->controller->getTask() == 'viewDetail') { ?>
    <?= Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
        t('Results for &#34;%s&#34;', $current_survey),
        false,
        false,
        false);
    ?>

    <div class="ccm-dashboard-header-buttons">
        <a href="<?= $view->action('view'); ?>" class="btn btn-default"><?= t('Go back'); ?></a>
    </div>

    <div>
        <div class="text-center">
            <?= $pie_chart; ?>
        </div>
        <?= $chart_options; ?>
    </div>

    <div class="table-responsive">
        <table class="ccm-search-results-table compact-results">
            <thead>
                <tr>
                    <th><span><?= t('Option'); ?></span></th>
                    <th><span><?= t('IP Address'); ?></span></th>
                    <th><span><?= t('Date'); ?></span></th>
                    <th><span><?= t('User'); ?></span></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($survey_details as $detail) { ?>
                <tr>
                    <td><?= $detail['option']; ?></td>
                    <td><?= $detail['ipAddress']; ?></td>
                    <td><?= $detail['date']; ?></td>
                    <td><?= $detail['user']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <?= Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>
<?php } else { ?>
    <?= Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Surveys'), false, false); ?>

    <?php if (count($surveys) == 0) { ?>
    <p><?= t('You have not created any surveys.'); ?></p>
    <?php } else { ?>
    <div class="ccm-dashboard-content-full">
        <div class="table-responsive">
            <table class="ccm-search-results-table">
                <thead>
                    <tr>
                        <th class="<?= $surveyList->getSearchResultsClass('question'); ?>">
                            <a href="<?= $surveyList->getSortByURL('question', 'asc'); ?>">
                                <?= t('Name'); ?>
                            </a>
                        </th>
                        <th class="<?= $surveyList->getSearchResultsClass('cvName'); ?>">
                            <a href="<?= $surveyList->getSortByURL('cvName', 'asc'); ?>">
                                <?= t('Found on Page'); ?>
                            </a>
                        </th>
                        <th class="<?= $surveyList->getSearchResultsClass('lastResponse'); ?>">
                            <a href="<?= $surveyList->getSortByURL('lastResponse', 'desc'); ?>">
                                <?= t('Last Response'); ?>
                            </a>
                        </th>
                        <th class="<?= $surveyList->getSearchResultsClass('numberOfResponses'); ?>">
                            <a href="<?= $surveyList->getSortByURL('numberOfResponses', 'desc'); ?>">
                                <?= t('Number of Responses'); ?>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($surveys as $survey) { ?>
                    <tr>
                        <td>
                            <strong>
                                <a href="<?= $view->action(
                                    'viewDetail',
                                    $survey['bID'],
                                    $survey['cID']); ?>">
                                    <?= $survey['question']; ?>
                                </a>
                            </strong>
                        </td>
                        <td>
                            <?= $survey['cvName']; ?>
                        </td>
                        <td>
                            <?= $this->controller->formatDate($survey['lastResponse']); ?>
                        </td>
                        <td>
                            <?= $survey['numberOfResponses']; ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php }
    $surveyList->displayPagingV2();
    ?>

    <?= Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(); ?>

<?php } ?>
