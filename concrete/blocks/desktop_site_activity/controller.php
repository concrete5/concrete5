<?php
namespace Concrete\Block\DesktopSiteActivity;

defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\Block\BlockController;
use Concrete\Core\Workflow\Progress\Category;
use Core;

class Controller extends BlockController
{
    public $helpers = array('form');

    protected $btInterfaceWidth = 450;
    protected $btInterfaceHeight = 560;
    protected $btTable = 'btDesktopSiteActivity';

    public function getBlockTypeDescription()
    {
        return t("Displays a graph of recent activity on your site.");
    }

    public function getBlockTypeName()
    {
        return t("Site Activity");
    }

    public function save($args)
    {
        $types = json_encode($args['types']);
        parent::save(['types' => $types]);
    }

    public function add()
    {
        $this->set('types', array());
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('javascript', 'google-charts');
    }

    protected function loadTypes()
    {
        $types = @json_decode($this->types);
        if (!is_array($types)) {
            $types = array();
        }
        $this->set('types', $types);
    }

    protected function getLatestSignups($since)
    {
        $db = \Database::connection();
        $r = $db->query('select count(uID) from Users where UNIX_TIMESTAMP(uDateAdded) >= ? and uIsActive = 1', array($since));
        return $r->fetchColumn();
    }

    protected function getLatestSurveyResults($since)
    {
        $db = \Database::connection();
        $r = $db->query('select count(uID) from btSurveyResults where UNIX_TIMESTAMP(timestamp) >= ?', array($since));
        return $r->fetchColumn();
    }

    protected function getLatestMessages($since)
    {
        $db = \Database::connection();
        $r = $db->query('select count(uID) from ConversationMessages where UNIX_TIMESTAMP(cnvMessageDateCreated) >= ?', array($since));
        return $r->fetchColumn();
    }

    protected function getLatestFormSubmissions($since)
    {
        $since = date('Y-m-d H:i:s', $since);

        // legacy
        $db = \Database::connection();
        $r = $db->query('select count(uID) from btFormAnswerSet where created >= ?', array($since));
        $legacy = $r->fetchColumn();

        // new
        $entityManager = $db->getEntityManager();
        $forms = $entityManager->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findExpressForms();

        $ids = array();
        $new = 0;
        foreach($forms as $form) {
            $ids[] = $form->getID();
        }

        if (count($ids)) {
            $q = $entityManager->createQuery(
                'select count(e) from Concrete\Core\Entity\Express\Entry e where e.entity in (:entities) and e.exEntryDateCreated >= :date'
            );
            $q->setParameter('entities', $forms);
            $q->setParameter('date', $since);
            $new = $q->getSingleScalarResult();
        }

        return $legacy + $new;
    }

    public function export(\SimpleXMLElement $blockNode)
    {
        $data = $blockNode->addChild('data');
        $this->loadTypes();
        $types = $this->get('types');
        foreach($types as $type) {
            $data->addChild('type', $type);
        }
    }

    public function getImportData($blockNode, $page)
    {
        $args = array();
        foreach ($blockNode->data->type as $type) {
            $args['types'][] = (string) $type;
        }

        return $args;
    }

    protected function getWorkflowProgressItems()
    {
        $categories = Category::getList();
        $items = 0;
        foreach($categories as $category) {
            $list = $category->getPendingWorkflowProgressList();
            if (is_object($list)) {
                foreach($list->get() as $it) {
                    $wp = $it->getWorkflowProgressObject();
                    $wf = $wp->getWorkflowObject();
                    if ($wf->canApproveWorkflowProgressObject($wp)) {
                        $items++;
                    }
                }
            }
        }
        return $items;
    }

    public function view()
    {
        $this->loadTypes();
        $types = $this->get('types');
        $u = new \User();
        $ui = \UserInfo::getByID($u->getUserID());

        if (in_array('signups', $types)) {
            $signups = $this->getLatestSignups($ui->getLastLogin());
            $this->set('signups', $signups);
        }
        if (in_array('form_submissions', $types)) {
            $submissions = $this->getLatestFormSubmissions($ui->getLastLogin());
            $this->set('formResults', $submissions);
        }
        if (in_array('survey_results', $types)) {
            $results = $this->getLatestSurveyResults($ui->getLastLogin());
            $this->set('surveyResults', $results);
        }
        if (in_array('conversation_messages', $types)) {
            $messages = $this->getLatestMessages($ui->getLastLogin());
            $this->set('messages', $messages);
        }
        if (in_array('workflow', $types)) {
            $approvals = $this->getWorkflowProgressItems();
            $this->set('approvals', $approvals);
        }


    }

    public function edit()
    {
        $this->loadTypes();
    }
}
