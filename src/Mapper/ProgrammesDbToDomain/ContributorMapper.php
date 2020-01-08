<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\Entity\Thing;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

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
            if (isset($dbContributor['thing'])) {
                $dbThing = $dbContributor['thing'];
                $thing = new Thing(
                    $dbThing['id'],
                    $dbThing['preferredLabel'],
                    $dbThing['disambiguationHint']
                );
            } else {
                $thing = null;
            }

            $this->cache[$cacheKey] = new Contributor(
                $dbContributor['id'],
                new Pid($dbContributor['pid']),
                $dbContributor['type'],
                $dbContributor['name'],
                $dbContributor['sortName'],
                $dbContributor['givenName'],
                $dbContributor['familyName'],
                $dbContributor['musicBrainzId'],
                $thing
            );
        }

        return $this->cache[$cacheKey];
    }
}
