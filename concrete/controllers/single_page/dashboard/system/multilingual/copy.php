<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Multilingual;

use Concrete\Controller\Panel\Multilingual;
use Concrete\Core\Foundation\Queue\Queue;
use Concrete\Core\Multilingual\Page\Section\Processor\MultilingualProcessorTarget;
use Concrete\Core\Multilingual\Page\Section\Processor\Processor;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Controller\DashboardSitePageController;

defined('C5_EXECUTE') or die('Access Denied.');

class Copy extends DashboardSitePageController
{
    public function view()
    {
        $this->set('locales', $this->getSite()->getLocales());
    }

    public function tree_copied()
    {
        $this->set('message', t('Multilingual tree copied. You might consider rescanning links in the section you copied to.'));
        $this->view();
    }

    public function links_rescanned()
    {
        $this->set('message', t('Multilingual tree links rescanned.'));
        $this->view();
    }

    public function rescan_locale()
    {
        if ($this->token->validate('rescan_locale')) {
            $u = new \User();
            if ($u->isSuperUser()) {
                \Core::make('cache/request')->disable();
                $section = Section::getByID($_REQUEST['locale']);
                $target = new MultilingualProcessorTarget($section);
                $processor = new Processor($target);
                if (!empty($_POST['process'])) {
                    foreach ($processor->receive() as $task) {
                        $processor->execute($task);
                    }
                    $obj = new \stdClass();
                    $obj->totalItems = $processor->getTotalTasks();
                    echo json_encode($obj);
                    exit;
                } else {
                    $processor->process();
                }
                $totalItems = $processor->getTotalTasks();
                \View::element('progress_bar', array('totalItems' => $totalItems, 'totalItemsSummary' => t2("%d task", "%d tasks", $totalItems)));
                exit;
            }
        }
    }
}
