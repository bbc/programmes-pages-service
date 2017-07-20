<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Cache\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Group;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\GroupMapper;
use InvalidArgumentException;

class GroupsService extends AbstractService
{
    private const ALL_VALID_ENTITY_TYPES = [
        'Collection',
        'Franchise',
        'Gallery',
        'Group',
        'Season',
    ];

    /** @var CoreEntityRepository */
    protected $repository;

    public function __construct(
        CoreEntityRepository $repository,
        GroupMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByPidFull(Pid $pid, string $entityType = 'Group', $ttl = CacheInterface::NORMAL): ?Group
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
