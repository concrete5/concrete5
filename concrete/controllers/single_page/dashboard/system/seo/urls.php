<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Service\Manager\ServiceManager;
use Concrete\Core\Url\UrlImmutable;
use Punic\Misc;

class Urls extends DashboardSitePageController
{
    protected const PRETTYURLSTATE_NOT_RECOGNIZED = 1;

    protected const PRETTYURLSTATE_NOT_READABLE = 2;

    protected const PRETTYURLSTATE_NOT_NEEDED = 3;

    protected const PRETTYURLSTATE_NOT_WRITABLE = 4;

    protected const PRETTYURLSTATE_UPDATED = 5;

    public function view()
    {
        $globalConfig = $this->app->make('config');
        $siteConfig = $this->getSite()->getConfigRepository();

        $this->set('canonicalUrl', (string) $siteConfig->get('seo.canonical_url'));
        $this->set('canonicalUrlAlternative', (string) $siteConfig->get('seo.canonical_url_alternative'));
        $this->set('redirectToCanonicalUrl', (bool) $globalConfig->get('concrete.seo.redirect_to_canonical_url'));
        $this->set('urlRewriting', (bool) $globalConfig->get('concrete.seo.url_rewriting'));
        $this->set('canonicalTag', (bool) $siteConfig->get('seo.canonical_tag.enabled'));
    }

    public function save_urls()
    {
        $post = $this->request->request;
        if (!$this->token->validate('save_urls')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $canonicalUrl = $this->getPostedCanonicalUrl('canonical_url');
        $canonicalUrlAlternative = $this->getPostedCanonicalUrl('canonical_url_alternative');
        $urlRewriting = (bool) $post->get('url_rewriting');
        if ($this->error->has()) {
            return $this->view();
        }
        $globalConfig = $this->app->make('config');
        $siteConfig = $this->getSite()->getConfigRepository();
        $siteConfig->save('seo.canonical_url', $canonicalUrl);
        $siteConfig->save('seo.canonical_url_alternative', $canonicalUrlAlternative);
        $globalConfig->save('concrete.seo.redirect_to_canonical_url', (bool) $post->get('redirect_to_canonical_url'));
        $siteConfig->save('seo.canonical_tag.enabled', (bool) $post->get('canonical_tag'));
        $globalConfig->save('concrete.seo.url_rewriting', $urlRewriting);
        $prettyUrlState = $this->applyUrlRewriting($urlRewriting);
        $this->flash('success', h('Settings Saved.') . '<br />' . $this->getPrettyUrlStateMessage($urlRewriting, $prettyUrlState), true);

        return $this->buildRedirect($this->action(''));
    }

    protected function getPostedCanonicalUrl(string $name): string
    {
        $value = trim($this->request->request->get($name, ''));
        if ($value === '') {
            return '';
        }
        if (!preg_match('_^https?://._i', $value)) {
            $this->error->add(t('The URL %1$s is not valid (it must start with %2$s or %3$s)', $value, 'http://', 'https://'));

            return '';
        }
        try {
            $normalizedValue = (string) UrlImmutable::createFromUrl($value);
        } catch (\RuntimeException $x) {
            $this->error->add(t('The URL %s is not valid.', $value));

            return '';
        }

        return $normalizedValue;
    }

    protected function applyUrlRewriting(bool $enabled): int
    {
        $manager = $this->app->make(ServiceManager::class);
        $services = $manager->getActiveServices();
        if ($services === []) {
            return static::PRETTYURLSTATE_NOT_RECOGNIZED;
        }
        $service = $services[0];
        if (!$service->getStorage()->canRead()) {
            return static::PRETTYURLSTATE_NOT_READABLE;
        }
        $rule = $service->getGenerator()->getRule('pretty_urls');
        if ($rule === null) {
            return static::PRETTYURLSTATE_NOT_NEEDED;
        }
        $configuration = $service->getStorage()->read();
        if ($service->getConfigurator()->hasRule($configuration, $rule) === $enabled) {
            return static::PRETTYURLSTATE_NOT_NEEDED;
        }
        if ($service->getStorage()->canWrite() === false) {
            return static::PRETTYURLSTATE_NOT_WRITABLE;
        }
        if ($enabled) {
            $configuration = $service->getConfigurator()->addRule($configuration, $rule);
        } else {
            $configuration = $service->getConfigurator()->removeRule($configuration, $rule);
        }
        $service->getStorage()->write($configuration);

        return static::PRETTYURLSTATE_UPDATED;
    }

    protected function getPrettyUrlStateMessage(bool $urlRewriting, int $state): string
    {
        switch ($state) {
            case static::PRETTYURLSTATE_NOT_RECOGNIZED:
                $codes = [];
                $manager = $this->app->make(ServiceManager::class);
                foreach ($manager->getAllServices() as $service) {
                    $rule = $service->getGenerator()->getRule('pretty_urls');
                    if ($rule !== null) {
                        if (isset($codes[$rule->getCode()])) {
                            $codes[$rule->getCode()][] = $service->getName();
                        } else {
                            $codes[$rule->getCode()] = [$service->getName()];
                        }
                    }
                }
                $message = h(t('It was not possible to detect your server kind.'));
                if ($urlRewriting) {
                    $message .= ' ' . h(t("Here's the configuration section for every supported server: please manually add the applicable one to your server configuration."));
                } else {
                    $message .= ' ' . h(t("Here's the configuration section for every supported server: please manually remove the applicable one from your server configuration."));
                }
                $message .= '<dl>';
                foreach ($codes as $code => $serviceNames) {
                    $message .= '<dt>' . h(tc(/*i18n %s is one or more server names */'For server', 'For %s', Misc::joinAnd($serviceNames))) . '</dt>';
                    $message .= '<dd>' . $this->getServerCodeHtml($code) . '</dd>';
                }

                return $message;
            case static::PRETTYURLSTATE_NOT_READABLE:
            case static::PRETTYURLSTATE_NOT_NEEDED:
            case static::PRETTYURLSTATE_NOT_WRITABLE:
            case static::PRETTYURLSTATE_UPDATED:
                $manager = $this->app->make(ServiceManager::class);
                $services = $manager->getActiveServices();
                $service = $services[0];
                $rule = $service->getGenerator()->getRule('pretty_urls');
                switch ($state) {
                    case static::PRETTYURLSTATE_NOT_READABLE:
                        $message = t('It was not possible read your server configuration.');
                        if ($urlRewriting) {
                            $message .= ' ' . t('Please add this configuration section to your server configuration:');
                        } else {
                            $message .= ' ' . t('Please remove this configuration section from your server configuration');
                        }
                        break;
                    case static::PRETTYURLSTATE_NOT_NEEDED:
                        if ($urlRewriting) {
                            $message = t('The following rule was already in your server configuration');
                        } else {
                            $message = t('The following rule was already missing in your server configuration');
                        }
                        break;
                    case static::PRETTYURLSTATE_NOT_WRITABLE:
                        $message = t('It was not possible write your server configuration.');
                        if ($urlRewriting) {
                            $message .= ' ' . t('Please add this configuration section to your server configuration:');
                        } else {
                            $message .= ' ' . t('Please remove this configuration section from your server configuration');
                        }
                        break;
                    case static::PRETTYURLSTATE_UPDATED:
                        if ($urlRewriting) {
                            $message = t('The following rule has been added to the server configuration');
                        } else {
                            $message = t('The following rule has been removed from the server configuration');
                        }
                        break;
                }

                return h($message) . $this->getServerCodeHtml($rule->getCode());
        }
    }

    protected function getServerCodeHtml(string $ruleCode): string
    {
        return $this->app->make(Form::class)->textarea('', h($ruleCode), ['rows' => substr_count($ruleCode, "\n") + 1, 'onclick' => 'this.select()', 'readonly' => 'readonly', 'class' => 'font-monospace']);
    }
}
