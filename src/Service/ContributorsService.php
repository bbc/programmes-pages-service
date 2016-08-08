<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ContributorRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ContributorMapper;
use DateTimeImmutable;

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

    public function findAllMostPlayed(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        Service $service = null
    ): array {
        $serviceId = $service ? $service->getDbId() : null;

        $results = $this->repository->findAllMostPlayedWithPlays(
            $from,
            $to,
            $serviceId
        );

        return array_map(function ($result) {
            // The doctrine result array that is returned will have
            // the entity at index 0, and the scalar by its name. i.e
            // [
            //     0 => [],
            //     'contributorPlayCount' => 2
            // ]
            return (object) [
                'contributor' => $this->mapSingleEntity($result[0]),
                'plays' => (int) $result['contributorPlayCount'],
            ];
        }, $results);
    }
}
