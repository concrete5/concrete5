<?php
namespace Concrete\Core\Url;

use RuntimeException;

class Url extends \League\Url\Url
{

    public static function createFromUrl($url, $trailing_slashes = false)
    {
        $url = (string)$url;
        $url = trim($url);
        $original_url = $url;
        $url = self::sanitizeUrl($url);

        //if no valid scheme is found we add one
        if (is_null($url)) {
            throw new RuntimeException(
                sprintf(
                    'The given URL: `%s` could not be parse',
                    $original_url));
        }
        $components = @parse_url($url);
        if (false === $components) {
            throw new RuntimeException(
                sprintf(
                    'The given URL: `%s` could not be parse',
                    $original_url));
        }

        $components = array_merge(
            array(
                'scheme' => null,
                'user' => null,
                'pass' => null,
                'host' => null,
                'port' => null,
                'path' => null,
                'query' => null,
                'fragment' => null,
            ),
            $components);
        $components = self::formatAuthComponent($components);
        $components = self::formatPathComponent($components, $original_url);

        return new static(
            new      \League\Url\Components\Scheme($components['scheme']),
            new        \League\Url\Components\User($components['user']),
            new        \League\Url\Components\Pass($components['pass']),
            new        \League\Url\Components\Host($components['host']),
            new        \League\Url\Components\Port($components['port']),
            new \Concrete\Core\Url\Components\Path($components['path'], $trailing_slashes),
            new       \League\Url\Components\Query($components['query']),
            new    \League\Url\Components\Fragment($components['fragment'])
        );
    }

}
