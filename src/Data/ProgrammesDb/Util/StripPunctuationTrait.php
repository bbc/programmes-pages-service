<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Util;

trait StripPunctuationTrait
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
}
