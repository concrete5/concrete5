<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Page\Controller\DashboardSitePageController;

class Codes extends DashboardSitePageController
{
    public function view()
    {
        $config = $this->getSite()->getConfigRepository();
        $this->set('tracking_code_header', (string) $config->get('seo.tracking.code.header'));
        $this->set('tracking_code_body', (string) $config->get('seo.tracking.code.body'));
        $this->set('tracking_code_footer', (string) $config->get('seo.tracking.code.footer'));
    }

    public function save()
    {
        $post = $this->request->request;
        if (!$this->token->validate('update_tracking_code')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if ($this->error->has()) {
            return $this->view();
        }
        $config = $this->getSite()->getConfigRepository();
        $config->save('seo.tracking.code.header', (string) $post->get('tracking_code_header'));
        $config->save('seo.tracking.code.body', (string) $post->get('tracking_code_body'));
        $config->save('seo.tracking.code.footer', (string) $post->get('tracking_code_footer'));
        PageCache::getLibrary()->flush();
        $this->flash('success', t('Tracking code settings updated successfully.') . "\n" . t('Cached files removed.'));

        return $this->buildRedirect($this->action());
    }
}
