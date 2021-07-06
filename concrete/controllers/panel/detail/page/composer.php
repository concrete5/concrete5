<?php

namespace Concrete\Controller\Panel\Detail\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Page\Collection\Version\Event;
use Concrete\Core\Page\Collection\Version\Version;
use Concrete\Core\Page\EditResponse as PageEditResponse;
use Concrete\Core\Page\Template as PageTemplate;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\Core\Page\Page;
use Concrete\Core\User\User;
use Concrete\Core\View\View;
use Exception;

class Composer extends BackendInterfacePageController
{
    protected $viewPath = '/panels/details/page/composer';

    public function view()
    {
        $this->requireAsset('javascript', 'core/composer-save-coordinator');
        $pagetype = PageType::getByID($this->page->getPageTypeID());
        $id = $this->page->getCollectionID();
        $saveURL = View::url('/dashboard/composer/write', 'save', 'draft', $id);
        $viewURL = View::url('/dashboard/composer/write', 'draft', $id);
        $this->set('ui', $this->app->make('helper/concrete/ui/help'));
        $this->set('composer', $this->app->make('helper/concrete/composer'));
        $this->set('token', $this->app->make('token'));
        $this->set('saveURL', $saveURL);
        $this->set('viewURL', $viewURL);
        $this->set('pagetype', $pagetype);
        $this->set('c', $this->page);
        $this->set('cID', (int) $id);
        $config = $this->app->make('config');
        $idleTimeout = (float) $config->get('concrete.composer.idle_timeout');
        $this->set('idleTimeout', $idleTimeout > 0 ? $idleTimeout : null);
    }

    public function autosave()
    {
        if ($this->validateAction()) {
            $r = $this->save();
            $ptr = $r[0];
            if (!$ptr->error->has()) {
                $ptr->setMessage(t('Page saved on %s', $this->app->make('helper/date')->formatDateTime($ptr->time, true, true)));
            }
            $ptr->outputJSON();
        } else {
            throw new Exception(t('Access Denied.'));
        }
    }

    public function saveAndExit()
    {
        if ($this->validateAction()) {
            $r = $this->save();
            $ptr = $r[0];
            $c = Page::getCurrentPage();
            $ptr->setRedirectURL($c->getCollectionLink(true));
            $ptr->outputJSON();
        } else {
            throw new Exception(t('Access Denied.'));
        }
    }

    public function publish()
    {
        if ($this->validateAction()) {
            $r = $this->save();
            $ptr = $r[0];
            $pagetype = $r[1];
            $outputControls = $r[2];

            $c = $this->page;
            $e = $ptr->error;
            $validator = $pagetype->getPageTypeValidatorObject();
            if ($this->page->isPageDraft()) {
                $target = Page::getByID($this->page->getPageDraftTargetParentPageID());
            } else {
                $target = Page::getByID($this->page->getCollectionParentID());
            }
            $e->add($validator->validatePublishLocationRequest($target, $c));
            $e->add($validator->validatePublishDraftRequest($c));

            $ptr->setError($e);

            if (!$e->has()) {
                $publishDateTime = false;
                $publishEndDateTime = false;
                if ($this->request->request->get('action') == 'schedule') {
                    $dateTime = new DateTime();
                    $publishDateTime = $dateTime->translate('cvPublishDate');
                    $publishEndDateTime = $dateTime->translate('cvPublishEndDate');

                    if ($this->request->request->get('unapproveOtherVersions')) {
                        $v = Version::get($c, 'RECENT');
                        $v->deny();

                        $ev = new Event($c);
                        $ev->setCollectionVersionObject($v);
                        $this->app->make('director')->dispatch('on_page_version_submit_deny', $ev);
                    }
                }

                $pagetype->publish($c, $publishDateTime, $publishEndDateTime);
                $ptr->setRedirectURL($this->app->make('helper/navigation')->getLinkToCollection($c));
            }
            $ptr->outputJSON();
        } else {
            throw new Exception(t('Access Denied.'));
        }
    }

    public function discard()
    {
        if ($this->validateAction()) {
            $ptr = new PageEditResponse();
            if ($this->permissions->canDeletePage() && $this->page->isPageDraft()) {
                $this->page->delete();
                $u = $this->app->make(User::class);
                $cID = $u->getPreviousFrontendPageID();
                $ptr->setRedirectURL(DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID);
            } else {
                $e = $this->app->make('helper/validation/error');
                $e->add(t('You do not have permission to discard this page.'));
                $ptr->setError($e);
            }

            $ptr->outputJSON();
        } else {
            throw new Exception(t('Access Denied.'));
        }
    }

    protected function canAccess()
    {
        return $this->permissions->canEditPageContents();
    }

    protected function save()
    {
        $c = $this->page;
        $ptr = new PageEditResponse();
        $ptr->setPage($c);

        $pagetype = $c->getPageTypeObject();
        $pt = null;
        $ptComposerPageTemplateID = (int) $this->request->post('ptComposerPageTemplateID');
        if ($ptComposerPageTemplateID !== 0) {
            $pt = PageTemplate::getByID($ptComposerPageTemplateID);
        }
        if ($pt === null) {
            $pt = $pagetype->getPageTypeDefaultPageTemplateObject();
        }
        $validator = $pagetype->getPageTypeValidatorObject();
        $e = $validator->validateCreateDraftRequest($pt);
        $outputControls = [];
        if (!$e->has()) {
            $c = $c->getVersionToModify();
            $this->page = $c;

            if ($c->isPageDraft()) {
                /// set the target
                $configuredTarget = $pagetype->getPageTypePublishTargetObject();
                $targetPageID = (int) $configuredTarget->getPageTypePublishTargetConfiguredTargetParentPageID();
                if ($targetPageID === 0) {
                    $targetPageID = (int) $this->request->post('cParentID');
                    if ($targetPageID === 0) {
                        $targetPageID = $c->getPageDraftTargetParentPageID();
                    }
                }

                $c->setPageDraftTargetParentPageID($targetPageID);
            }

            $saver = $pagetype->getPageTypeSaverObject();
            $outputControls = $saver->saveForm($c);
        }
        $ptr->setError($e);

        return [$ptr, $pagetype, $outputControls];
    }
}
