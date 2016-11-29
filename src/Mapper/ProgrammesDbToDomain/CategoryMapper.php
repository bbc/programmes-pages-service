<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use InvalidArgumentException;

class CategoryMapper implements MapperInterface
{
    /**
     * @param array $dbCategory
     * @return Format|Genre
     */
    public function getDomainModel(array $dbCategory)
    {
        if ($dbCategory['type'] == 'genre') {
            return $this->getGenreDomainModel($dbCategory);
        } elseif ($dbCategory['type'] == 'format') {
            return $this->getFormatDomainModel($dbCategory);
        }

        throw new InvalidArgumentException('Could not build domain model for unknown category type "' . ($dbCategory['type'] ?? '') . '"');
    }

    private function getFormatDomainModel(array $dbCategory): Format
    {
        return new Format(
            [(int) $dbCategory['id']],
            $dbCategory['pipId'],
            $dbCategory['title'],
            $dbCategory['urlKey']
        );
    }

    private function getGenreDomainModel(array $dbCategory): Genre
    {
        return new Genre(
            $this->getAncestryArray($dbCategory),
            $dbCategory['pipId'],
            $dbCategory['title'],
            $dbCategory['urlKey'],
            $this->getGenreParentModel($dbCategory, 'parent')
        );
    }

    private function getGenreParentModel($dbCategory, $key = 'parent')
    {
        if (!array_key_exists($key, $dbCategory) || is_null($dbCategory[$key])) {
            return null;
        }

        return $this->getDomainModel($dbCategory[$key]);
    }

    private function getAncestryArray($dbCategory, $key = 'ancestry')
    {
        // ancestry contains a string of all IDs including the current one with
        // a trailing comma at the end (which makes it an empty item when exploding)
        // Thus we want an array of all but the final item (which is null)
        $ancestors = explode(',', $dbCategory[$key], -1) ?? [];
        return array_map(function ($a) {
            return (int) $a;
        }, $ancestors);
    }
}
