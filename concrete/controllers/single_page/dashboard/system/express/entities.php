<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Express;

use Concrete\Core\Attribute\Category\SearchIndexer\ExpressSearchIndexer;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Support\Facade\Express;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Type\ExpressEntryResults;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Routing\Redirect;

class Entities extends DashboardPageController
{
    public function add()
    {
        $this->set('pageTitle', t('Add Data Object'));
        if ($this->request->isMethod('POST')) {
            if (!$this->token->validate('add_entity')) {
                $this->error->add($this->token->getErrorMessage());
            }
            $sec = \Core::make('helper/security');
            $vs = \Core::make('helper/validation/strings');

            $name = $sec->sanitizeString($this->request->request->get('name'));
            $handle = $sec->sanitizeString($this->request->request->get('handle'));

            if (!$vs->handle($handle)) {
                $this->error->add(t('You must create a handle for your data object. It may contain only lowercase letters and underscores.'), 'handle');
            } else {
                $entity = Express::getObjectByHandle($handle);
                if (is_object($entity)) {
                    $this->error->add(t('An express object with this handle already exists.'));
                }
            }

            if (!$name) {
                $this->error->add(t('You must give your data object a name.'), 'name');
            }

            if (!$this->error->has()) {
                $entity = new Entity();
                $entity->setName($this->request->request->get('name'));
                $entity->setHandle($this->request->request->get('handle'));
                $entity->setPluralHandle($this->request->request->get('plural_handle'));
                $entity->setLabelMask($this->request->request->get('label_mask'));
                $entity->setDescription($this->request->request->get('description'));

                if ($this->request->request->get('supports_custom_display_order')) {
                    $entity->setSupportsCustomDisplayOrder(true);
                }

                $form = new Form();
                $form->setEntity($entity);
                $form->setName('Form');
                $entity->setDefaultEditForm($form);
                $entity->setDefaultViewForm($form);

                $this->entityManager->persist($entity);
                $this->entityManager->flush();

                if ($owned_by = $this->request->request->get('owned_by')) {
                    $owned_by = $this->entityManager->find('\Concrete\Core\Entity\Express\Entity', $owned_by);
                    if (is_object($owned_by)) {
                        // Create the owned by relationship
                        $builder = \Core::make('express/builder/association');
                        if ($this->request->request->get('owning_type') == 'many') {
                            $builder->addOneToMany(
                                $owned_by, $entity, $entity->getPluralHandle(), $owned_by->getHandle(), true
                            );
                        } else {
                            $builder->addOneToOne(
                                $owned_by, $entity, $entity->getHandle(), $owned_by->getHandle(), true
                            );
                        }
                        $this->entityManager->persist($entity);
                        $this->entityManager->flush();
                    }
                }

                $this->flash('success', t('Object added successfully.'));
                return Redirect::to('/dashboard/system/express/entities', 'view_entity', $entity->getId());
            }
        }

        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entities = $r->findAll(array(), array('name' => 'asc'));
        $select = ['' => t('** Choose Entity')];
        foreach ($entities as $entity) {
            $select[$entity->getID()] = $entity->getEntityDisplayName();
        }
        $this->set('entities', $select);
        $this->render('/dashboard/system/express/entities/add');
    }

    public function view()
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entities = $r->findBy(array(), array('name' => 'asc'));
        $this->set('entities', $entities);
    }

    public function delete()
    {
        $entity = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneById($this->request->request->get('entity_id'));

        if (!is_object($entity)) {
            $this->error->add(t("Invalid express entity."));
        }
        if (!$this->token->validate('delete_entity')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            // Note there's very little logic here because Concrete\Core\Express\Entity\Listener takes care of it
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
            $this->flash('success', t('Entity deleted successfully.'));
            return Redirect::to('/dashboard/system/express/entities');
        }

    }

    public function view_entity($id = null)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entity = $r->findOneById($id);
        if (is_object($entity)) {
            $this->set('entity', $entity);
            $this->set('pageTitle', t('Object Details'));
            $this->render('/dashboard/system/express/entities/view_details');
        } else {
            $this->view();
        }
    }

    /**
     * @return \Concrete\Core\Routing\RedirectResponse
     */
    public function delete_entries()
    {
        /** @var \Concrete\Core\Entity\Express\Entity $entity */
        $entity = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity')->findOneById($this->request->request->get('entity_id'));

        if (!is_object($entity)) {
            $this->error->add(t('Invalid express entity.'));
        }

        if (!$this->token->validate('clear_entries')) {
            $this->error->add($this->token->getErrorMessage());
        }

        foreach ($entity->getEntries() as $entry){
            $this->entityManager->remove($entry);
        }

        $this->entityManager->flush();

        $this->flash('success', t('All Entries were successfully cleared.'));
        return Redirect::to('/dashboard/system/express/entities', 'view_entity', $entity->getId());
    }

    public function clear_entries($id = null)
    {
        $entity = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity')->findOneById($id);

        if (!is_object($entity)) {
            $this->error->add(t('Invalid express entity.'));
        }

        $this->set('entity', $entity);
        $this->set('token', new Token());
        $this->render('/dashboard/system/express/entities/clear_entries');
    }

    public function edit($id = null)
    {
        $tree = ExpressEntryResults::get();
        $this->set('tree', $tree);
        $this->requireAsset('core/topics');
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $this->entity = $r->findOneById($id);
        if (is_object($this->entity)) {
            $node = Node::getByID($this->entity->getEntityResultsNodeId());
            if (is_object($node)) {
                $folder = $node->getTreeNodeParentObject();
                $this->set('folder', $folder);
            }
            $forms = array('' => t('** Select Form'));
            $defaultViewFormID = 0;
            $defaultEditFormID = 0;
            $ownedByID = 0;
            $entities = array('' => t('** No Owner'));
            foreach ($r->findAll() as $ownedByEntity) {
                $entities[$ownedByEntity->getID()] = $ownedByEntity->getName();
            }
            foreach ($this->entity->getForms() as $form) {
                $forms[$form->getID()] = $form->getName();
            }
            if (is_object($this->entity->getDefaultViewForm())) {
                $defaultViewFormID = $this->entity->getDefaultViewForm()->getID();
            }
            if (is_object($this->entity->getDefaultEditForm())) {
                $defaultEditFormID = $this->entity->getDefaultEditForm()->getID();
            }
            if (is_object($this->entity->getOwnedBy())) {
                $ownedByID = $this->entity->getOwnedBy()->getID();
            }
            $this->set('defaultEditFormID', $defaultEditFormID);
            $this->set('defaultViewFormID', $defaultViewFormID);
            $this->set('ownedByID', $ownedByID);
            $this->set('forms', $forms);
            $this->set('entity', $this->entity);
            $this->set('pageTitle', t('Edit Entity'));
            $this->render('/dashboard/system/express/entities/edit');
        } else {
            $this->view();
        }
    }


    public function update($id = null)
    {
        $this->edit($id);
        $entity = $this->entity;
        if (!$this->token->validate('update_entity')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $sec = \Core::make('helper/security');
        $vs = \Core::make('helper/validation/strings');

        $name = $sec->sanitizeString($this->request->request->get('name'));
        $handle = $sec->sanitizeString($this->request->request->get('handle'));

        if (!$vs->handle($handle)) {
            $this->error->add(t('You must create a handle for your data object. It may contain only lowercase letters and underscores.'), 'handle');
        } else {
            $exist = Express::getObjectByHandle($handle);
            if (is_object($exist) && $exist->getID() != $id) {
                $this->error->add(t('An express object with this handle already exists.'));
            }
        }

        if (!$name) {
            $this->error->add(t('You must give your data object a name.'), 'name');
        }

        if (!$this->request->request->get('entity_results_node_id')) {
            $this->error->add(t('You must choose where the results for your entity are going live.'));
        }

        if ($this->request->request->get('owned_by') && $this->request->request->get('owned_by') == $this->entity->getID()) {
            $this->error->add(t('An entity cannot own itself.'));
        }
        $viewForm = null;
        $editForm = null;
        foreach ($this->entity->getForms() as $form) {
            if ($form->getID() == $this->request->request->get('default_edit_form_id')) {
                $editForm = $form;
            }
            if ($form->getID() == $this->request->request->get('default_view_form_id')) {
                $viewForm = $form;
            }
        }
        if (!is_object($viewForm)) {
            $this->error->add(t('You must specify a valid default view form.'));
        }
        if (!is_object($editForm)) {
            $this->error->add(t('You must specify a valid default edit form.'));
        }
        if (!$this->error->has()) {

            $previousEntity = clone $entity;

            $entity->setName($name);
            $entity->setHandle($handle);
            $entity->setPluralHandle($this->request->request->get('plural_handle'));
            $entity->setLabelMask($this->request->request->get('label_mask'));
            $entity->setDescription($this->request->request->get('description'));
            $entity->setDefaultViewForm($viewForm);
            $entity->setDefaultEditForm($editForm);
            $entity->setSupportsCustomDisplayOrder(false);

            if ($this->request->request->get('supports_custom_display_order')) {
                $entity->setSupportsCustomDisplayOrder(true);
            }

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            /**
             * @var $indexer ExpressSearchIndexer
             */
            $indexer = $entity->getAttributeKeyCategory()->getSearchIndexer();
            $indexer->updateRepository($previousEntity, $entity);

            $resultsNode = Node::getByID($entity->getEntityResultsNodeId());
            $folder = Node::getByID($this->request->request('entity_results_node_id'));
            if (is_object($folder)) {
                $resultsNode->move($folder);
            }

            $this->flash('success', t('Object updated successfully.'));
            return Redirect::to('/dashboard/system/express/entities', 'view_entity', $entity->getId());
        }
    }

}
