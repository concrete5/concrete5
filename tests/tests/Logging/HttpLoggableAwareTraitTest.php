<?php

namespace Concrete\Tests\Logging;

use Concrete\Core\Logging\HttpLoggableAwareTrait;
use PHPUnit_Framework_TestCase;

class HttpLoggableAwareTraitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider routeDestinationProvider
     *
     * @return array
     */
    public function contentTypesProvider()
    {
        return [
            // contentType, expected
            // true = loggable http body types
            [['Content-Type' => 'text/plain'], true],
            [['Content-Type' => 'text/css'], true],
            [['Content-Type' => 'text/html'], true],
            [['Content-Type' => 'text/csv'], true],
            [['Content-Type' => 'application/json'], true],
            [['Content-Type' => 'application/xml'], true],
            [['Content-Type' => 'application/rss+xml'], true],
            // false
            [['Content-Type' => 'application/rtf'], false],
            [['Content-Type' => 'application/javascript'], false],
            [['Content-Type' => 'image/png'], false],
            [['Content-Type' => 'image/jpeg'], false],
        ];
    }

    /**
     * Stub config values from log -> http -> Content-Types.
     *
     * @return array
     */
    public function getLoggableContentTypes()
    {
        return [
            'disabled' => false,
            'text' => '#^text/#i',
            'json' => '#^application/json$#i',
            'xml' => '#^application/(.*\+)?xml$#i',
        ];
    }

    /**
     * @dataProvider contentTypesProvider
     *
     * @param string $contentType
     * @param bool $expected
     */
    public function testIsLoggable($contentType, $expected)
    {
        $mockTrait = $this->getMockForTrait(HttpLoggableAwareTrait::class);
        $mockTrait->expects($this->any())
            ->method('getLoggableContentTypes')
            ->willReturn($this->getLoggableContentTypes());

        $this->assertEquals($expected, $mockTrait->isLoggable($contentType, $mockTrait->getLoggableContentTypes()));
    }
}
