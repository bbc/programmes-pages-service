<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Contribution;
use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class ContributionMapper extends AbstractMapper
{
    public function getDomainModel(array $dbContribution): Contribution
    {
        return new Contribution(
            new Pid($dbContribution['pid']),
            $this->getContributorModel($dbContribution),
            $this->getContributedTo($dbContribution),
            $this->getCreditRoleName($dbContribution),
            $dbContribution['position'],
            $dbContribution['characterName']
        );
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
        } elseif (array_key_exists('contributionToCoreEntity', $dbContribution) && !is_null($dbContribution['contributionToCoreEntity'])) {
            return $this->mapperFactory->getSegmentMapper()->getDomainModel($dbContribution['contributionToCoreEntity']);
        } elseif (array_key_exists('contributionToVersion', $dbContribution) && !is_null($dbContribution['contributionToVersion'])) {
            return $this->mapperFactory->getSegmentMapper()->getDomainModel($dbContribution['contributionToVersion']);
        }

        throw new DataNotFetchedException('Contribution must be to a Segment, Core Entity or to a Version');
    }

    private function getCreditRoleName($dbContribution, $key = 'creditRole'): string
    {
        if (!array_key_exists($key, $dbContribution) || is_null($dbContribution[$key])) {
            throw new DataNotFetchedException('All Contributions must be joined to a CreditRole');
        }

        return $dbContribution[$key]['name'];
    }
}
