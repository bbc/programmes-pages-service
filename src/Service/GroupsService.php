<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Collection;
use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\Entity\Gallery;
use BBC\ProgrammesPagesService\Domain\Entity\Group;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CoreEntityMapper;
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

    /** @var CoreEntityMapper */
    protected $mapper;

    /** @var CoreEntityRepository */
    protected $repository;

    public function __construct(
        CoreEntityRepository $repository,
        CoreEntityMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    /**
     * @param CoreEntity $coreEntity
     * @param null|string $groupType
     * @param int|null $limit
     * @param int $page
     * @param string $ttl
     * @param string $nullTtl
     * @return Group[]
     */
    public function findByCoreEntityMembership(
        CoreEntity $coreEntity,
        string $groupType = 'Group',
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NORMAL
    ): array {
        if (!($coreEntity instanceof Programme || $coreEntity instanceof Collection || $coreEntity instanceof Gallery)) {
            throw new InvalidArgumentException(sprintf(
                'Called %s with an invalid type. Expected a Programme, Collection or Gallery but got "%s"',
                debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'],
                \get_class($coreEntity)
            ));
        }

        if (!\in_array($groupType, ['Group', 'Collection', 'Franchise', 'Gallery', 'Season'])) {
            throw new InvalidArgumentException(sprintf(
                'Called %s with an invalid type. Expected Group, Collection, Franchise, Gallery or Season but got "%s"',
                debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'],
                \get_class($coreEntity)
            ));
        }

        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $coreEntity->getDbId(), $groupType, $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($coreEntity, $groupType, $limit, $page) {
                $dbEntities = $this->repository->findByCoreEntityMembership(
                    $coreEntity->getDbId(),
                    $groupType,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            },
            [],
            $nullTtl
        );
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

    protected function mapSingleEntity(?array $dbEntity, ...$additionalArgs)
    {
        if (is_null($dbEntity)) {
            return null;
        }

        return $this->mapper->getDomainModelForGroup($dbEntity, ...$additionalArgs);
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
