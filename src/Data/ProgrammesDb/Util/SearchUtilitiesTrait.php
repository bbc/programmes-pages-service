<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Util;

trait SearchUtilitiesTrait
{
    protected function stripPunctuation(string $string): string
    {
        $string = preg_replace(
            '/[\x{2000}-\x{206F}\x{2E00}-\x{2E7F}\'!"#$%&()*+,\-.\/:;<=>?@\[\]^_`{|}~]/u',
            '',
            $string
        );
        return $string ?? '';
    }

    protected function makeBooleanSearchQuery(string $keywords): ?string
    {
        $keywords = $this->stripPunctuation($keywords);
        // Split into words
        $words = preg_split('/\s+/', $keywords, 20, PREG_SPLIT_NO_EMPTY);
        // Filter to words >= 2 chars
        $validWords = array_filter($words, function ($word) {
            return (strlen($word) > 2);
        });
        if (!empty($validWords)) {
            // Join into MySQL compatible boolean query string
            $booleanKeywords = join(' +', $validWords);
            return '+' . $booleanKeywords;
        }
        return null;
    }
}
