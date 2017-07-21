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

    public function getCacheKey(array $dbContribution): string
    {
        return $this->buildCacheKey($dbContribution, 'id', [
            'contributionToSegment' => 'Segment',
            'contributionToCoreEntity' => 'Programme',
            'contributionToVersion' => 'Version',
        ]);
    }

    public function getDomainModel(array $dbContribution): Contribution
    {
        $cacheKey = $this->getCacheKey($dbContribution);

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
        if (!isset($dbContribution[$key])) {
            throw new DataNotFetchedException('All Contributions must be joined to a Contributor');
        }

        return $this->mapperFactory->getContributorMapper()->getDomainModel($dbContribution[$key]);
    }

    private function getContributedTo(array $dbContribution)
    {
        if (isset($dbContribution['contributionToSegment'])) {
            return $this->mapperFactory->getSegmentMapper()->getDomainModel($dbContribution['contributionToSegment']);
        }
        if (isset($dbContribution['contributionToCoreEntity'])) {
            $type = $dbContribution['contributionToCoreEntity']['type'];
            if (in_array($type, ['Collection', 'Franchise', 'Gallery', 'Group', 'Season'])) {
                return $this->mapperFactory->getGroupMapper()->getDomainModel($dbContribution['contributionToCoreEntity']);
            }
            return $this->mapperFactory->getProgrammeMapper()->getDomainModel($dbContribution['contributionToCoreEntity']);
        }
        if (isset($dbContribution['contributionToVersion'])) {
            return $this->mapperFactory->getVersionMapper()->getDomainModel($dbContribution['contributionToVersion']);
        }

        return new UnfetchedProgramme();
    }

    private function getCreditRoleName(array $dbContribution, string $key = 'creditRole'): string
    {
        if (!isset($dbContribution[$key])) {
            throw new DataNotFetchedException('All Contributions must be joined to a CreditRole');
        }

        return $dbContribution[$key]['name'];
    }
}
