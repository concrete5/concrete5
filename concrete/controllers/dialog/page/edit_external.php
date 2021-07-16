<?php

namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Page\EditResponse;

class EditExternal extends BackendInterfacePageController
{
    protected $viewPath = '/dialogs/page/add_external';

    public function view()
    {
        $this->set('form', $this->app->make('helper/form'));
        $this->set('name', $this->page->getCollectionName());
        $this->set('link', $this->page->getCollectionPointerExternalLink());
        $this->set('openInNewWindow', (bool) $this->page->openCollectionPointerExternalLinkInNewWindow());
        $this->set('isEditingExisting', true);
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $c = $this->page;
            $request = \Request::getInstance();
            $this->page->updateCollectionAliasExternal(
                $request->request->get('name'),
                trim($request->request->get('link')),
                $request->request->get('openInNewWindow')
            );
            $pr = new EditResponse();
            $pr->setMessage(t('External Link updated.'));
            $pr->setPage($c);
            $pr->outputJSON();
        }
    }

    protected function canAccess()
    {
        return $this->permissions->canEditPageProperties() && $this->page->isExternalLink();
    }
}
