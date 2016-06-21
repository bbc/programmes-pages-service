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

    private function getCreditRoleName($dbContribution, $key = 'creditRole'): string
    {
        if (!array_key_exists($key, $dbContribution) || is_null($dbContribution[$key])) {
            throw new DataNotFetchedException('All Contributions must be joined to a CreditRole');
        }

        return $dbContribution[$key]['name'];
    }
}
