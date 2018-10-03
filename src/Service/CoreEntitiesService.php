<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CoreEntityMapper;
use InvalidArgumentException;

class CoreEntitiesService extends AbstractService
{
    const ALL_VALID_ENTITY_TYPES = [
        'CoreEntity',
        'Programme',
        'ProgrammeContainer',
        'ProgrammeItem',
        'Brand',
        'Series',
        'Episode',
        'Clip',
        'Group',
        'Collection',
        'Gallery',
        'Season',
        'Franchise',
    ];

    /** @var CoreEntityRepository */
    protected $repository;

    public function __construct(
        CoreEntityRepository $repository,
        CoreEntityMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByPidFull(Pid $pid, string $entityType = 'CoreEntity', $ttl = CacheInterface::NORMAL): ?CoreEntity
    {
        $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);

        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, (string) $pid, $entityType, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($pid, $entityType) {
                $dbEntity = $this->repository->findByPidFull($pid, $entityType);
                return $this->mapSingleEntity($dbEntity);
            }
        );
    }

    /**
     * @param Pid[] $pids
     * @param string $entityType
     * @param string $ttl
     * @return CoreEntity[]
     */
    public function findByPids(array $pids, string $entityType = 'CoreEntity', $ttl = CacheInterface::NORMAL): array
    {
        $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);

        $validPids = [];

        foreach ($pids as $pid) {
            if ($pid instanceof Pid) {
                $validPids[] = (string) $pid;
            } else {
                throw new InvalidArgumentException('Called findByPids with an invalid type. Array must contain only Pids.');
            }
        }

        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, implode('|', $validPids), $entityType, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($validPids, $entityType) {
                $dbEntities = $this->repository->findByPids($validPids, $entityType);
                $mapEntities = $this->mapManyEntities($dbEntities);
                $indexedEntities = [];
                foreach ($mapEntities as $entity) {
                    $indexedEntities[(string)$entity->getPid()] = $entity;
                }
                return $indexedEntities;
            }
        );
    }

    private function assertEntityType($entityType, $validEntityTypes)
    {
        if (!in_array($entityType, $validEntityTypes)) {
            throw new InvalidArgumentException(sprintf(
                'Called %s with an invalid type. Expected one of %s but got "%s"',
                debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'],
                '"' . implode('", "', $validEntityTypes) . '"',
                $entityType
            ));
        }
    }
}
