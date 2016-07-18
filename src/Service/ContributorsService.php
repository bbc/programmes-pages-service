<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ContributorRepository;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ContributorMapper;

class ContributorsService extends AbstractService
{
    public function __construct(
        ContributorRepository $repository,
        ContributorMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findByMusicBrainzId(
        string $musicBrainzId
    ) {
        $dbEntity = $this->repository->findByMusicBrainzId($musicBrainzId);
        return $this->mapSingleEntity($dbEntity);
    }
}
