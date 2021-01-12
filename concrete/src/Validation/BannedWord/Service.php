<?php

namespace Concrete\Core\Validation\BannedWord;

class Service
{
    /**
     * @var string[]
     */
    private $bannedWords = [];

    /**
     * @return string[]
     */
    public function getBannedWords(): array
    {
        return $this->bannedWords;
    }

    /**
     * @param string[] $bannedWords
     */
    public function setBannedWords(array $bannedWords): void
    {
        $this->bannedWords = $bannedWords;
    }

    /**
     * Check if the given sentence has the banned words.
     *
     * @param string $string The sentence to check for
     *
     * @return bool
     */
    public function hasBannedWords(string $string)
    {
        $hasBannedWords = false;
        $bannedWords = $this->getBannedWords();
        foreach ($bannedWords as $bannedWord) {
            if ($this->hasBannedWord($bannedWord, $string)) {
                $hasBannedWords = true;
                break;
            }
        }

        return $hasBannedWords;
    }

    /**
     * Check if the given sentence has the banned word.
     *
     * @param string $bannedWord The banned word to search for, can contain * as a wildcard, case insensitive
     * @param string $sentence The string being searched, also known as the haystack
     *
     * @return bool
     */
    public function hasBannedWord(string $bannedWord, string $sentence): bool
    {
        $hasBannedWord = false;
        $escaped = preg_replace(['/(?!\*)\P{L}/u', '/\*/'], ['', '(\w+|)'], $bannedWord);
        if (!empty($escaped)) {
            $pattern = '/^' . $escaped . '$/ui';
            $strings = $this->explodeString($sentence);
            foreach ($strings as $chunk) {
                if (preg_match($pattern, $chunk)) {
                    $hasBannedWord = true;
                    break;
                }
            }
        }

        return $hasBannedWord;
    }

    /**
     * Explode string by whitespaces, line separators, tabs, punctuations, etc.
     *
     * @param string $string Original string to explode
     *
     * @return string[] Array of words
     */
    public function explodeString(string $string): array
    {
        $result = preg_split('/((\p{P}*\s+\p{P}*)|(\p{P}))/u', $string);
        if ($result === false) {
            $result = [];
        }

        return $result;
    }
}
