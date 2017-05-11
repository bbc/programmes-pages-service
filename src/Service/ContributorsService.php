<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Cache\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ContributorRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ContributorMapper;
use DateTimeImmutable;

class ContributorsService extends AbstractService
{
    public function __construct(
        ContributorRepository $repository,
        ContributorMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByMusicBrainzId(string $musicBrainzId, $ttl = CacheInterface::NORMAL): ?Contributor
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $musicBrainzId, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($musicBrainzId) {
                $dbEntity = $this->repository->findByMusicBrainzId($musicBrainzId);
                return $this->mapSingleEntity($dbEntity);
            }
        );
    }

    public function findAllMostPlayed(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        ?Service $service = null,
        $ttl = CacheInterface::NORMAL
    ): array {
        if (is_null($service)) {
            $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $from->getTimestamp(), $to->getTimestamp(), $ttl);
        } else {
            $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $from->getTimestamp(), $to->getTimestamp(), $service->getDbId(), $ttl);
        }

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($from, $to, $service) {
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
        );
    }
}
