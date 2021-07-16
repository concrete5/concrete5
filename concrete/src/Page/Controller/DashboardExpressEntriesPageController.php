<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Core\Csv\WriterFactory;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Export\EntryList\CsvWriter;
use Concrete\Core\Express\Form\Context\DashboardFormContext;
use Concrete\Core\Express\Form\Context\DashboardViewContext;
use Concrete\Core\Express\Form\Processor\ProcessorInterface;
use Concrete\Core\Express\Form\Renderer;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Form\Context\ContextFactory;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Type\ExpressEntryResults;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class DashboardExpressEntriesPageController extends DashboardPageController
{
    protected function getBackURL(Entity $entity)
    {
        return \URL::to($this->getPageObject()
            ->getCollectionPath(), 'view', $entity->getEntityResultsNodeID());
    }

    protected function getCreateURL(Entity $entity, Entry $ownedBy = null)
    {
        $ownedByID = null;
        if (is_object($ownedBy)) {
            $ownedByID = $ownedBy->getID();
        }

        return \URL::to($this->getPageObject()
            ->getCollectionPath(), 'create_entry', $entity->getID(), $ownedByID);
    }

    protected function getEditEntryURL(Entry $entry)
    {
        return \URL::to($this->getPageObject()
            ->getCollectionPath(), 'edit_entry', $entry->getID());
    }

    protected function getViewEntryURL(Entry $entry)
    {
        return \URL::to($this->getPageObject()
            ->getCollectionPath(), 'view_entry', $entry->getID());
    }

    protected function getResultsTreeNodeObject()
    {
        $tree = ExpressEntryResults::get();

        return $tree->getRootTreeNodeObject();
    }

    protected function renderList($treeNodeParentID = null)
    {
        $parent = $this->getParentNode($treeNodeParentID);

        $this->set('breadcrumb', $this->getBreadcrumb($parent));

        if (isset($parent) && $parent instanceof \Concrete\Core\Tree\Node\Type\ExpressEntryResults) {
            $entity = $this->getEntity($parent);
            $permissions = new \Permissions($entity);
            if (!$permissions->canViewExpressEntries()) {
                throw new \Exception(t('Access Denied'));
            }
            $search = new \Concrete\Controller\Search\Express\Entries();
            $search->search($entity);
            $this->set('list', $search->getListObject());
            $this->set('searchController', $search);
            $this->set('entity', $entity);
            $this->render('/dashboard/express/entries/entries', false);
        } else {
            $parent->populateDirectChildrenOnly();
            $this->set('nodes', $parent->getChildNodes());
            $this->render('/dashboard/express/entries/folder', false);
        }
    }

    /**
     * Export Express entries into a CSV.
     *
     * @param int|null $treeNodeParentID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function csv_export($treeNodeParentID = null)
    {
        $me = $this;
        $parent = $me->getParentNode($treeNodeParentID);
        $entity = $me->getEntity($parent);
        $permissions = new \Permissions($entity);
        if (!$permissions->canViewExpressEntries()) {
            throw new \Exception(t('Access Denied'));
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $entity->getHandle() . '.csv',
        ];
        $config = $this->app->make('config');
        $bom = $config->get('concrete.export.csv.include_bom') ? $config->get('concrete.charset_bom') : '';
        $datetime_format = $config->get('concrete.export.csv.datetime_format');

        return StreamedResponse::create(function () use ($entity, $me, $bom, $datetime_format) {
            $entryList = new EntryList($entity);

            $writer = $this->app->make(CsvWriter::class, [
                $this->app->make(WriterFactory::class)->createFromPath('php://output', 'w'),
                new Date(),
                $datetime_format
            ]);
            echo $bom;
            $writer->insertHeaders($entity);
            $writer->insertEntryList($entryList);
        }, 200, $headers);
    }

    /**
     * @param \Concrete\Core\Tree\Node\Type\ExpressEntryResults $parent
     *
     * @return \Concrete\Core\Entity\Express\Entity
     */
    protected function getEntity(\Concrete\Core\Tree\Node\Type\ExpressEntryResults $parent)
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneByResultsNode($parent);
    }

    protected function getBreadcrumb(Node $node = null)
    {
        $c = $this->getPageObject();
        $breadcrumb = [[
            'active' => false,
            'name' => t('Results'),
            'url' => \URL::to($c),
        ]];

        if (is_object($node)) {
            $items = $node->getTreeNodeParentArray();
            $items = array_slice($items, 0, count($items) - 1);
            $items = array_reverse($items);
            $items[] = $node;
            for ($i = 1; $i < count($items); ++$i) {
                $item = $items[$i];
                $breadcrumb[] = [
                    'id' => $item->getTreeNodeID(),
                    'active' => $item->getTreeNodeID() == $node->getTreeNodeID(),
                    'name' => $item->getTreeNodeDisplayName(),
                    'url' => \URL::to('/dashboard/reports/forms', 'view', $item->getTreeNodeID()),
                ];
            }
        }

        if (1 == count($breadcrumb)) {
            array_pop($breadcrumb);
        }

        return $breadcrumb;
    }

    public function delete_entry()
    {
        $entry = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry')
            ->findOneById($this->request->request->get('entry_id'));

        $permissions = new \Permissions($entry);
        if (!$permissions->canDeleteExpressEntry()) {
            $this->error->add(t('You do not have access to delete entries of this entity type.'));
        }
        if (!$this->token->validate('delete_entry')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $entity = $entry->getEntity();
            $url = $this->getBackURL($entity);
            $controller = \Core::make('express')->getEntityController($entity);
            $manager = $controller->getEntryManager($this->request);
            $manager->deleteEntry($entry);

            $this->flash('success', t('Entry deleted successfully.'));
            $this->redirect($url);
        }
    }

    public function view_entry($id = null)
    {
        $entry = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry')
            ->findOneById($id);

        $permissions = new \Permissions($entry);
        if (!$permissions->canViewExpressEntry()) {
            throw new \Exception(t('Access Denied'));
        }

        $this->set('entry', $entry);
        $this->set('entity', $entry->getEntity());
        $entity = $entry->getEntity();
        $this->entityManager->refresh($entity); // sometimes this isn't eagerly loaded (?)

        $express = \Core::make('express');
        $controller = $express->getEntityController($entity);
        $factory = new ContextFactory($controller);
        $context = $factory->getContext(new DashboardViewContext());

        $renderer = new Renderer(
            $context,
            $entity->getDefaultViewForm()
        );

        $this->set('renderer', $renderer);
        if ($entity->getOwnedBy()) {
            // the back url is the detail of what is the owner
            $ownerEntry = $entry->getOwnedByEntry();
            if (is_object($ownerEntry)) {
                $this->set('backURL', $this->getViewEntryURL($ownerEntry));
            }
        } else {
            $this->set('backURL', $this->getBackURL($entry->getEntity()));
        }
        if ($permissions->canEditExpressEntry()) {
            $this->set('editURL', $this->getEditEntryURL($entry));
        }
        if ($permissions->canDeleteExpressEntry()) {
            $this->set('allowDelete', true);
        } else {
            $this->set('allowDelete', false);
        }
        $subEntities = [];
        foreach ($entry->getEntity()->getAssociations() as $association) {
            if ($association->isOwningAssociation()) {
                $subEntities[] = $association->getTargetEntity();
            }
        }
        $this->set('subEntities', $subEntities);
        $this->set('pageTitle', t('View %s Entry', h($entity->getName())));
        $this->render('/dashboard/express/entries/view_entry', false);
    }

    public function edit_entry($id = null)
    {
        $entry = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry')
            ->findOneById($id);

        $permissions = new \Permissions($entry);
        if (!$permissions->canEditExpressEntry()) {
            throw new \Exception(t('Access Denied'));
        }

        $entity = $entry->getEntity();
        $this->set('entry', $entry);
        $this->set('entity', $entity);
        $entity = $entry->getEntity();
        $this->entityManager->refresh($entity); // sometimes this isn't eagerly loaded (?)

        $express = \Core::make('express');
        $controller = $express->getEntityController($entity);
        $factory = new ContextFactory($controller);
        $context = $factory->getContext(new DashboardFormContext());

        $renderer = new Renderer(
            $context,
            $entity->getDefaultEditForm()
        );

        $this->set('renderer', $renderer);
        $this->set('backURL', $this->getBackURL($entry->getEntity()));
        $this->set('pageTitle', t('Edit %s Entry', h($entity->getName())));
        $this->render('/dashboard/express/entries/update', false);
    }

    public function submit($id = null)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entity = $r->findOneById($id);

        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Form');
        $form = $r->findOneById($this->request->request->get('express_form_id'));

        $entry = null;
        if ($this->request->request->has('entry_id')) {
            $entry = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry')
                ->findOneById($this->request->request->get('entry_id'));
        }

        if (null !== $form) {
            $express = $this->app->make('express');
            $controller = $express->getEntityController($entity);
            $processor = $controller->getFormProcessor();
            $validator = $processor->getValidator($this->request);

            if (null === $entry) {
                $validator->validate($form, ProcessorInterface::REQUEST_TYPE_ADD);
            } else {
                $validator->validate($form, ProcessorInterface::REQUEST_TYPE_UPDATE);
            }

            $this->error = $validator->getErrorList();
            if ($this->error->has()) {
                if (null === $entry) {
                    $this->create_entry($entity->getID());
                } else {
                    $this->edit_entry($entry->getID());
                }
            } else {
                $notifier = $controller->getNotifier();
                $notifications = $notifier->getNotificationList();

                $manager = $controller->getEntryManager($this->request);
                if (null === $entry) {
                    // create
                    $entry = $manager->addEntry($entity);
                    $entry = $manager->saveEntryAttributesForm($form, $entry);
                    $notifier->sendNotifications($notifications, $entry, ProcessorInterface::REQUEST_TYPE_ADD);

                    $this->flash(
                        'success',
                        tc(/*i18n: %s is an Express entity name*/'Express', 'New record %s added successfully.', $entity->getEntityDisplayName())
                        . '<br />'
                        . '<a class="btn btn-default" href="' . \URL::to(\Page::getCurrentPage(), 'view_entry', $entry->getID()) . '">' . t('View Record Here') . '</a>',
                        true
                    );
                    if (is_object($entry->getOwnedByEntry())) {
                        $this->redirect(\URL::to(\Page::getCurrentPage(), 'create_entry', $entity->getID(), $entry->getOwnedByEntry()->getID()));
                    } else {
                        $this->redirect(\URL::to(\Page::getCurrentPage(), 'create_entry', $entity->getID()));
                    }
                } else {
                    // update
                    $manager->saveEntryAttributesForm($form, $entry);
                    $notifier->sendNotifications($notifications, $entry, ProcessorInterface::REQUEST_TYPE_UPDATE);
                    $this->flash('success', t('%s updated successfully.', $entity->getEntityDisplayName()));
                    $this->redirect($this->getBackURL($entity));
                }
            }
        } else {
            throw new \Exception(t('Invalid form.'));
        }
    }

    /**
     * @param $treeNodeParentID
     */
    protected function getParentNode($treeNodeParentID)
    {
        $parent = null;
        if ($treeNodeParentID) {
            $parent = Node::getByID($treeNodeParentID);
            if (is_object($parent)) {
                $tree = $parent->getTreeObject();
                if (!($tree instanceof ExpressEntryResults)) {
                    unset($parent);
                }
            }
        }
        if (!isset($parent)) {
            $parent = $this->getResultsTreeNodeObject();
        }

        return $parent;
    }
}
