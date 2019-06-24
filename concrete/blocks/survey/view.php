<?php
defined('C5_EXECUTE') or die('Access Denied.');
// basically a stub that includes some other files
$u = new User();
$uID = $u->getUserID();
$c = Page::getCurrentPage();

//available chart colors are duplicated in content/surveys.php
$availableChartColors = array(
    '00CCdd',
    'cc3333',
    '330099',
    'FF6600',
    '9966FF',
    'dd7700',
    '66DD00',
    '6699FF',
    'FFFF33',
    'FFCC33',
    '00CCdd',
    'cc3333',
    '330099',
    'FF6600',
    '9966FF',
    'dd7700',
    '66DD00',
    '6699FF',
    'FFFF33',
    'FFCC33', );
$options = $controller->getPollOptions();
$optionNames = array();
$optionResults = array();
$graphColors = array();
$i = 1;
$totalVotes = 0;
$optionNamesAbbrev = [];
foreach ($options as $opt) {
    $optionNamesAbbrev[] = $i;
    $optionResults[] = $opt->getResults();
    ++$i;
    $graphColors[] = array_pop($availableChartColors);
    $totalVotes += intval($opt->getResults());
}
foreach ($optionResults as &$value) {
    if ($totalVotes) {
        $value = round($value / $totalVotes * 100, 0);
    }
}
$show_graph = (count($optionNamesAbbrev) && !$_GET['dontGraphPoll'] && $totalVotes > 0);
?>

<div class="poll">
    <?php
    if ($controller->hasVoted()) {
        ?>
        <h3><?= t("You've voted on this survey.") ?></h3>

        <div class="row">
            <div<?= $show_graph ? ' class="col-sm-9"' : '' ?>>
                <div id="surveyQuestion">
                    <strong><?= t("Question") ?>:</strong> <span><?= $controller->getQuestion() ?></span>
                </div>

                <div id="surveyResults">
                    <table class="table">
                        <?php
                        $i = 1;
        foreach ($options as $opt) {
            ?>
                            <tr>

                                <td class="col-sm-2" style="white-space: nowrap">
                                    <div class="surveySwatch" style="background:#<?= $graphColors[$i - 1] ?>"></div>
                                    &nbsp;<?= ($totalVotes > 0) ? round($opt->getResults() / $totalVotes * 100) : 0 ?>%
                                </td>
                                <td>
                                    <strong>
                                        <?= $opt->getOptionName() ?>
                                    </strong>
                                </td>
                            </tr>
                            <?php
                            ++$i;
            ?>
                        <?php

        }
        ?>
                    </table>
                    <div class="help-block">
                        <?= t2('%d Vote', '%d Votes', intval($totalVotes), intval($totalVotes)) ?>
                    </div>
                </div>
            </div>
            <?php
            if ($show_graph) {
                ?>
                <div class="col-sm-3">
                    <img
                        border=""
                        src="//chart.apis.google.com/chart?chf=bg,s,FFFFFF00&cht=p&chd=t:<?= implode(
                            ',',
                            $optionResults) ?>&chs=180x180&chco=<?= implode(
                            ',',
                            $graphColors) ?>"
                        alt="<?php echo t('survey results');
                ?>"/>
                </div>
            <?php

            }
        ?>
        </div>
        <div class="spacer">&nbsp;</div>

        <?php
        if ($_GET['dontGraphPoll']) {
            ?>
            <div class="small right" style="margin-top:8px">
                <a class="arrow" href="<?= DIR_REL ?>/?cID=<?= $b->getBlockCollectionID() ?>">
                    <?= t('View Full Results') ?>
                </a>
            </div>
        <?php

        }
        ?>

        <div class="spacer">&nbsp;</div>

    <?php

    } else {
        ?>

        <div id="surveyQuestion" class="form-group">
            <?= $controller->getQuestion() ?>
        </div>

        <?php
        if (!$controller->requiresRegistration() || intval($uID) > 0) {
            ?>
            <form method="post" action="<?= $view->action('form_save_vote') ?>">
                <input type="hidden" name="rcID" value="<?= $c->getCollectionID() ?>"/>
        <?php

        }
        $options = $controller->getPollOptions();
        foreach ($options as $opt) {
            ?>
            <div class="radio">
                <label>
                    <input type="radio" name="optionID" value="<?= $opt->getOptionID() ?>"/>
                    <?= $opt->getOptionName() ?>
                </label>
            </div>
        <?php

        }
        if (!$controller->requiresRegistration() || intval($uID) > 0) {
            ?>
            <div class="form-group">
                <button class="btn btn-primary">
                    <?= t('Vote') ?>
                </button>
            </div>
        <?php

        } else {
            ?>
            <span class="help-block">
                <?= t('Please Login to Vote') ?>
            </span>
        <?php

        }
        ?>

        <?php
        if (!$controller->requiresRegistration() || intval($uID) > 0) {
            ?>
            </form>
        <?php

        }
        ?>

    <?php

    }
    ?>

</div>
