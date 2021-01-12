<?php

namespace Concrete\Tests\Validation;

use Concrete\Core\Validation\BannedWord\Service;
use Concrete\Tests\TestCase;

class BannedWordTest extends TestCase
{
    protected $asciiSingleParagraphString = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
    protected $asciiMultipleParagraphString = <<<EOT
Lorem ipsum dolor sit amet, consectetur adipiscing elit.
Quis vel eros "donec" ac odio tempor orci dapibus.

Tortor id aliquet lectus proin. Faucibus turpis in eu mi bibendum neque.
    A pellentesque sit amet porttitor eget dolor morbi non arcu.
EOT;
    protected $multibyteSingleParagraphString = 'Duis aute irure dolör in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla päriatur.';
    protected $cjkMultipleParagraphString = <<<EOT
吾輩は猫である。名前はまだ無い。
どこで生れたかとんと見当がつかぬ。

봄날의 소금이라 것은 위하여, 인간의 능히 있는 것이다. 

裡下候買想期；道驚非，過同病，風成男她；些通神草重，得辦已型車中可？
密能生式民的界度引……華課此傳上先，長同的數改好、命你書夫業角開人。
EOT;

    /**
     * @dataProvider matchBannedWordsProvider
     * @param string $bannedWord
     * @param string $string
     */
    public function testHasBannedWord(string $bannedWord, string $string): void
    {
        $service = new Service();
        self::assertTrue($service->hasBannedWord($bannedWord, $string));
    }

    public function matchBannedWordsProvider(): array
    {
        return [
            ['lorem', $this->asciiSingleParagraphString], // Ascii word, case insensitive
            ['donec', $this->asciiMultipleParagraphString], // Word wrapped by quotes
            ['dolör', $this->multibyteSingleParagraphString], // Word contains umlauts
            ['名前はまだ無い', $this->cjkMultipleParagraphString], // Agglutinative language word
            ['*esque', $this->asciiMultipleParagraphString], // Wildcard at the beginning of the word
            ['päria*', $this->multibyteSingleParagraphString], // Wildcard at the end of the word
            ['裡下*想期', $this->cjkMultipleParagraphString], // Wildcard at the middle of the word
            ['*吾輩*', $this->cjkMultipleParagraphString], // Word wrapped by wildcards
        ];
    }

    /**
     * @dataProvider doesNotMatchBannedWordsProvider
     * @param string $bannedWord
     * @param string $string
     */
    public function testHasNotBannedWord(string $bannedWord, string $string): void
    {
        $service = new Service();
        self::assertNotTrue($service->hasBannedWord($bannedWord, $string));
    }

    public function doesNotMatchBannedWordsProvider(): array
    {
        return [
            ['ore', $this->asciiSingleParagraphString], // The part of the word without wildcard
            ['lectusproin', $this->asciiMultipleParagraphString], // Combined words
            ['봄날의*위하여', $this->cjkMultipleParagraphString], // Wildcard should not match whitespaces
        ];
    }

    /**
     * @dataProvider invalidBannedWordsProvider
     * @param string $bannedWord
     * @param string $string
     */
    public function testEscapeBannedWord(string $bannedWord, string $string): void
    {
        $service = new Service();
        self::assertNotTrue($service->hasBannedWord($bannedWord, $string));
    }

    public function invalidBannedWordsProvider(): array
    {
        return [
            ['C[a-z]+', 'Concrete CMS'],
            ['.+', 'Concrete CMS'],
        ];
    }
}