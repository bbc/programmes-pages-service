<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Contribution;
use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgramme;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class ContributionMapper extends AbstractMapper
{
    private $cache = [];

    public function getDomainModel(array $dbContribution): Contribution
    {
        $cacheKey = $dbContribution['id'];

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = new Contribution(
                new Pid($dbContribution['pid']),
                $this->getContributorModel($dbContribution),
                $this->getContributedTo($dbContribution),
                $this->getCreditRoleName($dbContribution),
                $dbContribution['position'],
                $dbContribution['characterName']
            );
        }

        return $this->cache[$cacheKey];
    }

    private function getContributorModel($dbContribution, $key = 'contributor'): Contributor
    {
        if (!array_key_exists($key, $dbContribution) || is_null($dbContribution[$key])) {
            throw new DataNotFetchedException('All Contributions must be joined to a Contributor');
        }

        return $this->mapperFactory->getContributorMapper()->getDomainModel($dbContribution[$key]);
    }

    private function getContributedTo($dbContribution)
    {
        if (array_key_exists('contributionToSegment', $dbContribution) && !is_null($dbContribution['contributionToSegment'])) {
            return $this->mapperFactory->getSegmentMapper()->getDomainModel($dbContribution['contributionToSegment']);
        }
        if (array_key_exists('contributionToCoreEntity', $dbContribution) && !is_null($dbContribution['contributionToCoreEntity'])) {
            return $this->mapperFactory->getProgrammeMapper()->getDomainModel($dbContribution['contributionToCoreEntity']);
        }
        if (array_key_exists('contributionToVersion', $dbContribution) && !is_null($dbContribution['contributionToVersion'])) {
            return $this->mapperFactory->getVersionMapper()->getDomainModel($dbContribution['contributionToVersion']);
        }

        return new UnfetchedProgramme();
    }

    private function getCreditRoleName($dbContribution, $key = 'creditRole'): string
    {
        if (!array_key_exists($key, $dbContribution) || is_null($dbContribution[$key])) {
            throw new DataNotFetchedException('All Contributions must be joined to a CreditRole');
        }

        return $dbContribution[$key]['name'];
    }
}
