<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ClipRepository;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CoreEntityMapper;
use BBC\ProgrammesPagesService\Cache\CacheInterface;
use InvalidArgumentException;

class ProgrammesAggregationService extends AbstractService
{
    const AGGREGATION_VALID_TYPES = [
        'Series',
        'Episode',
        'Clip',
    ];

    public function __construct(
        CoreEntityRepository $repository,
        CoreEntityMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findChildrenByType(
        Programme $programme,
        string $type,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ) {
        $this->assertEntityType($type, self::AGGREGATION_VALID_TYPES);

        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $type, $limit, $page, $ttl);
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme, $type, $limit, $page) {
                $children = $this->repository->findProgrammesByAncestryAndType(
                    $programme->getDbAncestryIds(),
                    $type,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($children);
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
