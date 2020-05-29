<?php

namespace Concrete\Core\Logging;

use Concrete\Core\Support\Facade\Application;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Zend\Http\Request as ZendRequest;
use Zend\Http\Response as ZendResponse;

/**
 * Check if a http request or http response body can be logged (doesn't contain binary data)
 */
trait HttpLoggableAwareTrait
{
    /**
     * Check if request body can be logged (is not binary).
     *
     * @param ZendRequest|SymfonyRequest|RequestInterface $request
     *
     * @return bool
     */
    public function isRequestBodyLoggable($request)
    {
        if ($request instanceof SymfonyRequest) {
            $headers = array_change_key_case($request->headers->all());
        } elseif ($request instanceof ZendRequest) {
            $headers = array_change_key_case($request->getHeaders()->toArray());
        } elseif ($request instanceof RequestInterface) {
            $headers = array_change_key_case($request->getHeaders());
        } else {
            $headers = [];
        }

        return $this->isLoggable($headers, $this->getLoggableContentTypes());
    }

    /**
     * Check if response body can be logged (is not binary).
     *
     * @param ZendResponse|SymfonyResponse|ResponseInterface $response
     *
     * @return bool
     */
    public function isResponseBodyLoggable($response)
    {
        if ($response instanceof SymfonyResponse) {
            $headers = $response->headers->all();
        } elseif ($response instanceof ZendResponse) {
            $headers = $response->getHeaders()->toArray();
        } elseif ($response instanceof ResponseInterface) {
            $headers = $response->getHeaders();
        } else {
            $headers = [];
        }

        return $this->isLoggable($headers, $this->getLoggableContentTypes());
    }

    /**
     * Compare the headers of the request or response with the whitelist
     * If the content type matches a entry in the whitelist,
     * the response or request body can be logged.
     *
     * @param array $headers
     * @param array $types
     *
     * @return bool
     */
    public function isLoggable(array $headers, array $types)
    {
        $isLoggable = false;
        $headers = array_change_key_case($headers);

        if (array_key_exists('content-type', $headers)) {
            $responseContentType = $headers['content-type'];
            if (is_string($responseContentType)) {
                foreach ($types as $type) {
                    if ($type && preg_match($type, $responseContentType)) {
                        $isLoggable = true;
                        break;
                    }
                }
            }
        }

        return $isLoggable;
    }

    /**
     * Get whitelist of http content types which can be logged.
     *
     * @return array
     */
    public function getLoggableContentTypes()
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');

        return $config->get('concrete.log.http.content_types');
    }

    /**
     * Get content-type string from response.
     *
     * @param ZendRequest|SymfonyRequest|RequestInterface $request
     *
     * @return string
     */
    public function getRequestContentType($request)
    {
        $contentType = '';

        if ($request instanceof SymfonyRequest) {
            $contentType = array_change_key_case($request->headers->get('content-type'));
        } elseif ($request instanceof ZendRequest) {
            $headers = array_change_key_case($request->getHeaders()->toArray());
            if (array_key_exists('content-type', $headers)) {
                $contentType = $headers['content-type'];
            }
        }
        // PSR-7
        elseif ($request instanceof RequestInterface) {
            $contentType = $request->getHeader('content-type');
        }

        return $contentType;
    }

    /**
     * Get content-type string from response.
     *
     * @param ZendResponse|SymfonyResponse|ResponseInterface $response
     *
     * @return string
     */
    public function getResponseContentType($response)
    {
        $contentType = '';

        if ($response instanceof SymfonyResponse) {
            $contentType = array_change_key_case($response->headers->get('content-type'));
        } elseif ($response instanceof ZendResponse) {
            $headers = array_change_key_case($response->getHeaders()->toArray());
            if (array_key_exists('content-type', $headers)) {
                $contentType = $headers['content-type'];
            }
        }
        // PSR-7
        elseif ($response instanceof ResponseInterface) {
            $contentType = $response->getHeader('content-type');
        }

        return $contentType;
    }
}
