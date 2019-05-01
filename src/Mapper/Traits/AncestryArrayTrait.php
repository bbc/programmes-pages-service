<?php
declare(strict_types = 1);

namespace BBC\ProgrammesPagesService\Mapper\Traits;

trait AncestryArrayTrait
{
    private function getAncestryArray(array $dbEntity, string $key = 'ancestry'): array
    {
        // ancestry contains a string of all IDs including the current one with
        // a trailing comma at the end (which makes it an empty item when exploding)
        // Thus we want an array of all but the final item (which is null)
        $ancestors = explode(',', $dbEntity[$key], -1) ?? [];
        return array_map(function ($a) {
            return (int) $a;
        }, $ancestors);
    }
}
