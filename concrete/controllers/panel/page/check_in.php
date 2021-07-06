<?php
namespace Concrete\Controller\Panel\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Page\Collection\Version\Event;
use Concrete\Core\Support\Facade\Config;
use Concrete\Core\User\User;
use Permissions;
use CollectionVersion;
use Loader;
use Page;
use Concrete\Core\Workflow\Request\ApprovePageRequest as ApprovePagePageWorkflowRequest;
use PageEditResponse;
use Concrete\Core\Http\ResponseFactoryInterface;

class CheckIn extends BackendInterfacePageController
{
    protected $viewPath = '/panels/page/check_in';
    // we need this extra because this controller gets called by another page
    // and that page needs to know how to submit it.
    protected $controllerActionPath = '/ccm/system/panels/page/check_in';

    public function canAccess()
    {
        return $this->permissions->canApprovePageVersions() || $this->permissions->canEditPageContents();
    }

    public function on_start()
    {
        parent::on_start();
        if ($this->page) {
            $v = CollectionVersion::get($this->page, "RECENT");

            $this->set('publishDate', $v->getPublishDate());
            $this->set('publishErrors', $this->checkForPublishing());
        }
    }

    protected function checkForPublishing()
    {
        $c = $this->page;
        // verify this page type has all the items necessary to be approved.
        $e = Loader::helper('validation/error');
        if ($c->isPageDraft()) {
            if (!$c->getPageDraftTargetParentPageID()) {
                $e->add(t('You haven\'t chosen where to publish this page.'));
            }
        }
        $pagetype = $c->getPageTypeObject();
        if (is_object($pagetype)) {
            $validator = $pagetype->getPageTypeValidatorObject();
            $e->add($validator->validatePublishDraftRequest($c));
        }

        if ($c->isPageDraft() && !$e->has()) {
            $targetParentID = $c->getPageDraftTargetParentPageID();
            if ($targetParentID) {
                $tp = Page::getByID($targetParentID, 'ACTIVE');
                $pp = new Permissions($tp);
                if (!is_object($tp) || $tp->isError()) {
                    $e->add(t('Invalid target page.'));
                } else {
                    if (!$pp->canAddSubCollection($pagetype)) {
                        $e->add(
                            t(
                                'You do not have permissions to add a page of this type in the selected location.'
                            )
                        );
                    }
                }
            }
        }

        return $e;
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $comments = $this->request->request('comments');
            $comments = is_string($comments) ? trim($comments) : '';
            if ($comments === '' && $this->app->make('config')->get('concrete.misc.require_version_comments')) {
                $rf = $this->app->make(ResponseFactoryInterface::class);
                return $rf->create(t('Please specify the version comments'), 400);
            }
            $c = $this->page;
            $u = $this->app->make(User::class);
            $v = CollectionVersion::get($c, "RECENT");
            $v->setComment($comments);
            $pr = new PageEditResponse();
            if (($this->request->request->get('action') == 'publish'
                    || $this->request->request->get('action') == 'schedule')
                && $this->permissions->canApprovePageVersions()
            ) {
                $e = $this->checkForPublishing();
                $pr->setError($e);
                if (!$e->has()) {
                    $pkr = new ApprovePagePageWorkflowRequest();
                    $pkr->setRequestedPage($c);
                    $pkr->setRequestedVersionID($v->getVersionID());
                    $pkr->setRequesterUserID($u->getUserID());
                    $u->unloadCollectionEdit($c);

                    if ($this->request->request->get('action') == 'schedule') {
                        $dateTime = new DateTime();
                        $publishDateTime = $dateTime->translate('cvPublishDate');
                        $publishEndDateTime = $dateTime->translate('cvPublishEndDate');
                        $pkr->scheduleVersion($publishDateTime, $publishEndDateTime);

                        if ($this->request->request->get('unapproveOtherVersions')) {
                            $v->deny();

                            $ev = new Event($c);
                            $ev->setCollectionVersionObject($v);
                            $this->app->make('director')->dispatch('on_page_version_submit_deny', $ev);
                        }
                    }

                    if ($c->isPageDraft()) {
                        $pagetype = $c->getPageTypeObject();
                        $pagetype->publish($c, $pkr);
                    } else {
                        $pkr->trigger();
                    }
                }
            } else {
                if ($this->request->request->get('action') == 'discard') {
                    if ($c->isPageDraft() && $this->permissions->canDeletePage()) {
                        $cID = $u->getPreviousFrontendPageID();
                        $this->page->delete();
                        $pr->setRedirectURL(DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID);
                        $pr->outputJSON();
                    } else {
                        if ($v->canDiscard()) {
                            $v->discard();
                        }
                    }
                } else {
                    $v->removeNewStatus();
                }
            }
            $nc = Page::getByID($c->getCollectionID(), $v->getVersionID());
            $u->unloadCollectionEdit();
            $pr->setRedirectURL(Loader::helper('navigation')->getLinkToCollection($nc, true));
            $pr->outputJSON();
        }
    }

    protected function validateAction()
    {
        if (parent::validateAction()) {
            if ($this->permissions->canEditPageContents() || $this->permissions->canEditPageProperties(
                ) || $this->permissions->canApprovePageVersions()
            ) {
                return true;
            }
        }
    }
}
