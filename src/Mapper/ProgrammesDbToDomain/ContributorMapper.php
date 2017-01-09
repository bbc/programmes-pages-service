<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use InvalidArgumentException;

class ContributorMapper extends AbstractMapper
{
    private $cache = [];

    public function getCacheKey(array $dbContributor): string
    {
        return $this->buildCacheKey($dbContributor, 'id');
    }

    public function getDomainModel(array $dbContributor): Contributor
    {
        $cacheKey = $this->getCacheKey($dbContributor);

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = new Contributor(
                $dbContributor['id'],
                new Pid($dbContributor['pid']),
                $dbContributor['type'],
                $dbContributor['name'],
                $dbContributor['sortName'],
                $dbContributor['givenName'],
                $dbContributor['familyName'],
                $dbContributor['musicBrainzId']
            );
        }

        return $this->cache[$cacheKey];
    }
}
