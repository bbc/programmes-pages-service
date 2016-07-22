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

        $data = [];
        // loop through the results, mapping the objects
        foreach ($results as $result) {
            $contributor = $this->mapSingleEntity($result);
            $data[] = (object) [
                'contributor' => $contributor,
                'plays' => (int) $result['contributionPlays']
            ];
        }

        return $data;
    }

    /**
     * @param Contributor[] $contributors
     * @param DateTimeImmutable $from
     * @param DateTimeImmutable $to
     * @param Service|null $service
     * @return array
     */
    public function countPlaysForTimeByPid(
        array $contributors,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        Service $service = null
    ) {
        $databaseIds = array_map(function($contributor) {
           return $contributor->getDbId();
        }, $contributors);
        $serviceId = $service ? $service->getDbId() : null;

        $results = $results = $this->repository->countPlaysForContributorIds(
            $databaseIds,
            $from,
            $to,
            $serviceId
        );

        $data = [];
        foreach($results as $result) {
            $data[$result['pid']] = (int) $result['contributionPlays'];
        }
        return $data;
    }
}
