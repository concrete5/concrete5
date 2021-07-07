<?php

namespace Concrete\Block\Form;

use Core;
use Database;
use Request;

class MiniSurvey
{
    public $btTable = 'btForm';

    public $btQuestionsTablename = 'btFormQuestions';

    public $btAnswerSetTablename = 'btFormAnswerSet';

    public $btAnswersTablename = 'btFormAnswers';

    public $lastSavedMsqID = 0;

    public $lastSavedqID = 0;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function addEditQuestion($values, $withOutput = 1)
    {
        $jsonVals = [];
        $values['options'] = str_replace(["\r", "\n"], '%%', $values['options']);
        if (strtolower($values['inputType']) == 'undefined') {
            $values['inputType'] = 'field';
        }

        //set question set id, or create a new one if none exists
        if ((int) ($values['qsID']) == 0) {
            $values['qsID'] = time();
        }

        //validation
        if (strlen($values['question']) == 0 || strlen($values['inputType']) == 0 || $values['inputType'] == 'null') {
            //complete required fields
            $jsonVals['success'] = 0;
            $jsonVals['noRequired'] = 1;
        } else {
            if ((int) ($values['msqID'])) {
                $jsonVals['mode'] = '"Edit"';

                //questions that are edited are given a placeholder row in btFormQuestions with bID=0, until a bID is assign on block update
                $pendingEditExists = $this->db->fetchColumn('select count(*) as total from btFormQuestions where bID=0 AND msqID=' . (int) ($values['msqID']));

                //hideQID tells the interface to hide the old version of the question in the meantime
                $vals = [(int) ($values['msqID'])];
                $jsonVals['hideQID'] = (int) ($this->db->fetchColumn('SELECT MAX(qID) FROM btFormQuestions WHERE bID!=0 AND msqID=?', $vals));
            } else {
                $jsonVals['mode'] = '"Add"';
                $pendingEditExists = false;
            }

            //see if the 'send notification from' checkbox is checked and save this to the options field
            if ($values['inputType'] == 'email') {
                $options = [];
                if (array_key_exists('send_notification_from', $values) && $values['send_notification_from'] == 1) {
                    $options['send_notification_from'] = 1;
                } else {
                    $options['send_notification_from'] = 0;
                }
                $values['options'] = serialize($options);
            }
            if ($pendingEditExists) {
                $width = $height = 0;
                if ($values['inputType'] == 'text') {
                    $width = $this->limitRange((int) ($values['width']), 20, 500);
                    $height = $this->limitRange((int) ($values['height']), 1, 100);
                }
                $dataValues = [
                    (int) ($values['qsID']),
                    trim($values['question']),
                    $values['inputType'],
                    $values['options'],
                    (int) ($values['position']),
                    $width,
                    $height,
                    (int) ($values['required']),
                    $values['defaultDate'],
                    (int) ($values['msqID']),
                ];
                $sql = 'UPDATE btFormQuestions SET questionSetId=?, question=?, inputType=?, options=?, position=?, width=?, height=?, required=?, defaultDate=? WHERE msqID=? AND bID=0';
            } else {
                if (!isset($values['position'])) {
                    $values['position'] = 1000;
                }
                if (!(int) ($values['msqID'])) {
                    $values['msqID'] = (int) ($this->db->fetchColumn('SELECT MAX(msqID) FROM btFormQuestions') + 1);
                }
                $dataValues = [
                    $values['msqID'],
                    (int) ($values['qsID']),
                    trim($values['question']),
                    $values['inputType'],
                    $values['options'],
                    (int) ($values['position']),
                    (int) ($values['width']),
                    (int) ($values['height']),
                    (int) ($values['required']),
                    $values['defaultDate'],
                ];
                $sql = 'INSERT INTO btFormQuestions (msqID,questionSetId,question,inputType,options,position,width,height,required,defaultDate) VALUES (?,?,?,?,?,?,?,?,?,?)';
            }
            $result = $this->db->executeQuery($sql, $dataValues);
            $this->lastSavedMsqID = (int) ($values['msqID']);
            $this->lastSavedqID = (int) ($this->db->fetchColumn('SELECT MAX(qID) FROM btFormQuestions WHERE bID=0 AND msqID=?', [$values['msqID']]));
            $jsonVals['qID'] = $this->lastSavedqID;
            $jsonVals['success'] = 1;
        }

        $jsonVals['qsID'] = $values['qsID'];
        $jsonVals['msqID'] = (int) ($values['msqID']);
        //create json response object
        $jsonPairs = [];
        foreach ($jsonVals as $key => $val) {
            $jsonPairs[] = $key . ':' . $val;
        }
        if ($withOutput) {
            echo '{' . implode(',', $jsonPairs) . '}';
        }
    }

    public function getQuestionInfo($qsID, $qID)
    {
        $questionRS = $this->db->executeQuery('SELECT * FROM btFormQuestions WHERE questionSetId=' . (int) $qsID . ' AND qID=' . (int) $qID . ' LIMIT 1');
        $questionRow = $questionRS->fetch();
        $jsonPairs = [];
        foreach ($questionRow as $key => $val) {
            if ($key == 'options') {
                $key = 'optionVals';
                if ($questionRow['inputType'] == 'email') {
                    $options = unserialize($val);
                    if (is_array($options)) {
                        foreach ($options as $o_key => $o_val) {
                            $val = $o_key . '::' . $o_val . ';';
                        }
                    }
                }
            }

            $jsonPairs[] = $key . ':"' . str_replace(["\r", "\n"], '%%', addslashes($val)) . '"';
        }
        echo '{' . implode(',', $jsonPairs) . '}';
    }

    public function deleteQuestion($qsID, $msqID)
    {
        $sql = 'DELETE FROM btFormQuestions WHERE questionSetId=' . (int) $qsID . ' AND msqID=' . (int) $msqID . ' AND bID=0';
        $this->db->executeQuery($sql);
    }

    public function loadQuestions($qsID, $bID = 0, $showPending = 0)
    {
        $db = Database::connection();
        if ((int) $bID) {
            $bIDClause = ' AND ( bID=' . (int) $bID . ' ';
            if ($showPending) {
                $bIDClause .= ' OR bID=0) ';
            } else {
                $bIDClause .= ' ) ';
            }
        } else {
            $bIDClause = '';
        }

        return $db->executeQuery('SELECT * FROM btFormQuestions WHERE questionSetId=' . (int) $qsID . ' ' . $bIDClause . ' ORDER BY position, msqID');
    }

    public static function getAnswerCount($qsID)
    {
        $db = Database::connection();

        return $db->fetchColumn('SELECT count(*) FROM btFormAnswerSet WHERE questionSetId=' . (int) $qsID);
    }

    public function loadSurvey($qsID, $showEdit = false, $bID = 0, $hideQIDs = [], $showPending = 0, $editmode = 0)
    {
        //loading questions
        $questionsRS = $this->loadQuestions($qsID, $bID, $showPending);

        if (!$showEdit) {
            echo '<div>';
            while ($questionRow = $questionsRS->fetch()) {
                if (in_array($questionRow['qID'], $hideQIDs)) {
                    continue;
                }
                // this special view logic for the checkbox list isn't doing it for me
                /*
                if ($questionRow['inputType'] == 'checkboxlist' && strpos($questionRow['options'], '%%') === false) {
                    echo '<tr>
                        <td valign="top" colspan="2" class="question">
                            <div class="checkboxItem">
                                <div class="checkboxPair">'.$this->loadInputType($questionRow,$showEdit).$questionRow['question'].'</div>
                            </div>
                        </td>
                    </tr>';
                } else {
                 */
                $requiredSymbol = ($questionRow['required']) ? '&nbsp;<span class="required">*</span>' : '';
                echo '<div class="form-group">
                    <label class="control-label form-label" for="Question' . (int) ($questionRow['msqID']) . '">' . $questionRow['question'] . '' . $requiredSymbol . '</label></td>
                    <div>' . $this->loadInputType($questionRow, $showEdit) . '</div>
                </div>';
                //}
            }
            $surveyBlockInfo = $this->getMiniSurveyBlockInfoByQuestionId($qsID, (int) $bID);
            if (!strlen($surveyBlockInfo['submitText'])) {
                $surveyBlockInfo['submitText'] = 'Submit';
            }

            if (!empty($surveyBlockInfo['displayCaptcha'])) {
                echo '<div class="ccm-edit-mode-disabled-item">' . t('Form Captcha') . '</div><br/>';
            }

            if ($editmode) {
                echo '<div class="form-group"><input class="btn btn-primary" name="Submit" type="button" value="' . t($surveyBlockInfo['submitText']) . '" /></div>'; //make this a button
            } else {
                echo '<div class="form-group"><input class="btn btn-primary" name="Submit" type="submit" value="' . t($surveyBlockInfo['submitText']) . '" /></div>';
            }
            echo '</div>';
        } else {
            echo '<div id="miniSurveyTableWrap"><ul id="miniSurveyPreviewTable" class="list-group">';
            while ($questionRow = $questionsRS->fetch()) {
                if (in_array($questionRow['qID'], $hideQIDs)) {
                    continue;
                }

                $requiredSymbol = ($questionRow['required']) ? '<span class="required">*</span>' : '';
                ?>
					<li id="miniSurveyQuestionRow<?php echo $questionRow['msqID']?>" class="miniSurveyQuestionRow list-group-item">
						<div class="miniSurveyQuestion"><?php echo $questionRow['question'] . ' ' . $requiredSymbol?></div>
						<?php  /* <div class="miniSurveyResponse"><?php echo $this->loadInputType($questionRow,$showEdit)?></div> */ ?>
						<div class="miniSurveyOptions">
							<a href="javascript:void(0)" class="ccm-icon-wrapper" onclick="miniSurvey.moveUp(this,<?php echo $questionRow['msqID']?>);return false"><i class="fas fa-chevron-up"></i></a>
							<a href="javascript:void(0)" class="ccm-icon-wrapper" onclick="miniSurvey.moveDown(this,<?php echo $questionRow['msqID']?>);return false"><i class="fas fa-chevron-down"></i></a>
							<a href="javascript:void(0)" class="ccm-icon-wrapper" onclick="miniSurvey.reloadQuestion(<?=(int) ($questionRow['qID']) ?>);return false"><i class="fas fa-pencil-alt"></i></a>
							<a href="javascript:void(0)" class="ccm-icon-wrapper" onclick="miniSurvey.deleteQuestion(this,<?=(int) ($questionRow['msqID']) ?>,<?=(int) ($questionRow['qID'])?>);return false"><i class="fas fa-trash"></i></a>
						</div>
						<div class="clearfix"></div>
					</li>
				<?php
            }
            echo '</div></div>';
        }
    }

    public function loadInputType($questionData, $showEdit)
    {
        $options = explode('%%', $questionData['options']);
        $defaultDate = $questionData['defaultDate'];
        $msqID = (int) ($questionData['msqID']);
        $datetime = Core::make('helper/form/date_time');
        $html = '';
        switch ($questionData['inputType']) {
            case 'checkboxlist':
                // this is looking really crappy so i'm going to make it behave the same way all the time - andrew
                /*
                if (count($options) == 1) {
                    if(strlen(trim($options[0]))==0) continue;
                    $checked=(Request::request('Question'.$msqID.'_0')==trim($options[0]))?'checked':'';
                    $html.= '<input name="Question'.$msqID.'_0" type="checkbox" value="'.trim($options[0]).'" '.$checked.' />';
                } else {
                 */
                $html .= '<div class="checkboxList">' . "\r\n";
                for ($i = 0; $i < count($options); $i++) {
                    if (strlen(trim($options[$i])) == 0) {
                        continue;
                    }
                    $checked = (Request::request('Question' . $msqID . '_' . $i) == trim($options[$i])) ? 'checked' : '';
                    $html .= '  <div class="form-check"><input id="question_'. $msqID . '_'.$i.($showEdit ? '_preview' : '').'" name="Question' . $msqID . '_' . $i . '" class="form-check-input" type="checkbox" value="' . trim($options[$i]) . '" ' . $checked . ' /><label class="form-check-label" for="question_'. $msqID . '_'.$i. ($showEdit ? '_preview' : '').'"> <span>' . $options[$i] . '</span></label></div>' . "\r\n";
                }
                $html .= '</div>';
                //}
                return $html;
            case 'select':
                if ($this->frontEndMode) {
                    $selected = (!Request::request('Question' . $msqID)) ? 'selected="selected"' : '';
                    $html .= '<option value="" ' . $selected . '>----</option>';
                }
                foreach ($options as $option) {
                    $checked = (Request::request('Question' . $msqID) == trim($option)) ? 'selected="selected"' : '';
                    $html .= '<option ' . $checked . '>' . trim($option) . '</option>';
                }

                return '<select class="form-control" name="Question' . $msqID . '" id="Question' . $msqID . '" >' . $html . '</select>';
            case 'radios':
                $index = 1;
                foreach ($options as $option) {
                    if (strlen(trim($option)) == 0) {
                        continue;
                    }
                    $checked = (Request::request('Question' . $msqID) == trim($option)) ? 'checked' : '';
                    $html .= '<div class="form-check"><input class="form-check-input" id="Question' . $msqID . '_'.$index.'" name="Question' . $msqID . '" type="radio" value="' . trim($option) . '" ' . $checked . ' /><label for="Question' . $msqID . '_'.$index.'" class="form-check-label"> <span>' . $option . '</span></label></div>';
                    $index++;
                }

                return $html;
            case 'fileupload':
                $html = '<input type="file" name="Question' . $msqID . '" class="form-control" id="Question' . $msqID . '" />';

                return $html;
            case 'text':
                $val = (Request::request('Question' . $msqID)) ? Core::make('helper/text')->entities(Request::request('Question' . $msqID)) : '';

                return '<textarea name="Question' . $msqID . '" class="form-control" id="Question' . $msqID . '" cols="' . $questionData['width'] . '" rows="' . $questionData['height'] . '">' . $val . '</textarea>';
            case 'url':
                $val = (Request::request('Question' . $msqID)) ? Request::request('Question' . $msqID) : '';

                return '<input name="Question' . $msqID . '" id="Question' . $msqID . '" class="form-control" type="url" value="' . stripslashes(htmlspecialchars($val)) . '" />';
            case 'telephone':
                $val = (Request::request('Question' . $msqID)) ? Request::request('Question' . $msqID) : '';

                return '<input name="Question' . $msqID . '" id="Question' . $msqID . '" class="form-control" type="tel" value="' . stripslashes(htmlspecialchars($val)) . '" />';
            case 'email':
                $val = (Request::request('Question' . $msqID)) ? Request::request('Question' . $msqID) : '';

                return '<input name="Question' . $msqID . '" id="Question' . $msqID . '" class="form-control" type="email" value="' . stripslashes(htmlspecialchars($val)) . '" />';
            case 'date':
                $val = (Request::request('Question' . $msqID)) ? Request::request('Question' . $msqID) : $defaultDate;

                return $datetime->date('Question' . $msqID, $val);
            case 'datetime':
                $val = Request::request('Question' . $msqID);
                if (!isset($val)) {
                    if (
                        Request::request('Question' . $msqID . '_dt') && Request::request('Question' . $msqID . '_h')
                        && Request::request('Question' . $msqID . '_m') && Request::request('Question' . $msqID . '_a')
                    ) {
                        $val = Request::request('Question' . $msqID . '_dt') . ' ' . Request::request('Question' . $msqID . '_h')
                            . ':' . Request::request('Question' . $msqID . '_m') . ' ' . Request::request('Question' . $msqID . '_a');
                    } else {
                        $val = $defaultDate;
                    }
                }

                return $datetime->datetime('Question' . $msqID, $val);
            case 'field':
            default:
                $val = (Request::request('Question' . $msqID)) ? Request::request('Question' . $msqID) : '';

                return '<input name="Question' . $msqID . '" id="Question' . $msqID . '" class="form-control" type="text" value="' . stripslashes(htmlspecialchars($val)) . '" />';
        }
    }

    public function getMiniSurveyBlockInfo($bID)
    {
        $rs = $this->db->executeQuery('SELECT * FROM btForm WHERE bID=' . (int) $bID . ' LIMIT 1');

        return $rs->fetch();
    }

    public function getMiniSurveyBlockInfoByQuestionId($qsID, $bID = 0)
    {
        $sql = 'SELECT * FROM btForm WHERE questionSetId=' . (int) $qsID;
        if ((int) $bID > 0) {
            $sql .= ' AND bID=' . $bID;
        }
        $sql .= ' LIMIT 1';
        $rs = $this->db->executeQuery($sql);

        return $rs->fetch();
    }

    public function reorderQuestions($qsID = 0, $qIDs)
    {
        $qIDs = explode(',', $qIDs);
        if (!is_array($qIDs)) {
            $qIDs = [$qIDs];
        }
        $positionNum = 0;
        foreach ($qIDs as $qID) {
            $vals = [$positionNum, (int) $qID, (int) $qsID];
            $sql = 'UPDATE btFormQuestions SET position=? WHERE msqID=? AND questionSetId=?';
            $rs = $this->db->executeQuery($sql, $vals);
            $positionNum++;
        }
    }

    public function limitRange($val, $min, $max)
    {
        $val = ($val < $min) ? $min : $val;

        return ($val > $max) ? $max : $val;
    }

    //Run on Form block edit
    public static function questionCleanup($qsID = 0, $bID = 0)
    {
        $db = Database::connection();

        //First make sure that the bID column has been set for this questionSetId (for backwards compatibility)
        $vals = [(int) $qsID];
        $questionsWithBIDs = $db->fetchColumn('SELECT count(*) FROM btFormQuestions WHERE bID!=0 AND questionSetId=? ', $vals);

        //form block was just upgraded, so set the bID column
        if (!$questionsWithBIDs) {
            $vals = [(int) $bID, (int) $qsID];
            $rs = $db->executeQuery('UPDATE btFormQuestions SET bID=? WHERE bID=0 AND questionSetId=?', $vals);

            return;
        }

        //Then remove all temp/placeholder questions for this questionSetId that haven't been assigned to a block
        $vals = [(int) $qsID];
        $rs = $db->executeQuery('DELETE FROM btFormQuestions WHERE bID=0 AND questionSetId=?', $vals);
    }
}
