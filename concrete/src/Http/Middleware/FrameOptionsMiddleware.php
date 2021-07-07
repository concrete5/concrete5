<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Utility\Service\Validation\Strings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated Use SecurityHeaderMiddleware instead
 *
 * Middleware for applying frame options
 * @package Concrete\Core\Http\Middleware
 */
class FrameOptionsMiddleware implements MiddlewareInterface
{

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    private $config;

    /**
     * @var \Concrete\Core\Utility\Service\Validation\Strings
     */
    private $stringValidator;

    public function __construct(Repository $config, Strings $stringValidator)
    {
        $this->config = $config;
        $this->stringValidator = $stringValidator;
    }

    /**
     * @param \Concrete\Core\Http\Middleware\DelegateInterface $frame
     * @return Response
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        $response = $frame->next($request);

        if ($response->headers->has('X-Frame-Options') === false) {
            $x_frame_options = $this->config->get('concrete.security.misc.x_frame_options');
            if ($this->stringValidator->notempty($x_frame_options)) {
                $response->headers->set('X-Frame-Options', $x_frame_options);
            }
        }

        return $response;
    }

}
