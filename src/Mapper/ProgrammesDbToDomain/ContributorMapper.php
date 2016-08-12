<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use InvalidArgumentException;

class ContributorMapper implements MapperInterface
{
    public function getDomainModel(array $dbContributor): Contributor
    {
        return new Contributor(
            $dbContributor['id'],
            new Pid($dbContributor['pid']),
            $dbContributor['type'],
            $dbContributor['name'],
            $dbContributor['givenName'],
            $dbContributor['familyName'],
            $dbContributor['musicBrainzId']
        );
    }
}
