<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Utility\Service\Validation\Strings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware for applying security related headers
 * @package Concrete\Core\Http\Middleware
 */
class SecurityHeaderMiddleware implements MiddlewareInterface
{

    /**
     * @var Repository
     */
    private $config;

    /**
     * @var Strings
     */
    private $stringValidator;

    public function __construct(Repository $config, Strings $stringValidator)
    {
        $this->config = $config;
        $this->stringValidator = $stringValidator;
    }

    /**
     * @param DelegateInterface $frame
     * @return Response
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        $response = $frame->next($request);
        $options = $this->config->get('concrete.security.misc');

        if ($response->headers->has('Content-Security-Policy') === false) {
            $content_security_policies = $options['content_security_policy'];
            if ($content_security_policies !== false) {
                if (is_array($content_security_policies) || $this->stringValidator->notempty($content_security_policies)) {
                    $response->headers->set('Content-Security-Policy', $content_security_policies);
                }
            }
        }

        if ($response->headers->has('Strict-Transport-Security') === false) {
            $strict_transport_security = $options['strict_transport_security'];
            if ($strict_transport_security !== false && $this->stringValidator->notempty($strict_transport_security)) {
                $response->headers->set('Strict-Transport-Security', $strict_transport_security);
            }
        }

        if ($response->headers->has('X-Frame-Options') === false) {
            $x_frame_options = $options['x_frame_options'];
            if ($x_frame_options !== false && $this->stringValidator->notempty($x_frame_options)) {
                $response->headers->set('X-Frame-Options', $x_frame_options);
            }
        }

        if ($response->headers->has('X-XSS-Protection') === false) {
            $x_xss_protection = $options['x_xss_protection'];
            if ($x_xss_protection !== false && $this->stringValidator->notempty($x_xss_protection)) {
                $response->headers->set('X-XSS-Protection', $x_xss_protection);
            }
        }

        return $response;
    }

}
