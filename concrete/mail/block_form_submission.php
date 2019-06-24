<?php

defined('C5_EXECUTE') or die('Access Denied.');

$subject = t('%s Form Submission', $formName);

$submittedData = '';
foreach ($questionAnswerPairs as $questionAnswerPair) {
    $submittedData .= $questionAnswerPair['question']."\r\n".$questionAnswerPair['answerDisplay']."\r\n"."\r\n";
}
$formDisplayUrl = URL::to('/dashboard/reports/forms') . '?qsid='.$questionSetId;

$body = t("
There has been a submission of the form %s through your concrete5 website.

%s

To view all of this form's submissions, visit %s 

", $formName, $submittedData, $formDisplayUrl);
