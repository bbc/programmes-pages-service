<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use InvalidArgumentException;

class ContributorMapper implements MapperInterface
{
    private $cache = [];

    public function getDomainModel(array $dbContributor): Contributor
    {
        $cacheKey = $dbContributor['id'];

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
