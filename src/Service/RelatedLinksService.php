<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\RelatedLinkRepository;
use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\RelatedLink;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\RelatedLinkMapper;

class RelatedLinksService extends AbstractService
{
    /* @var RelatedLinkMapper */
    protected $mapper;

    /* @var RelatedLinkRepository */
    protected $repository;

    public function __construct(
        RelatedLinkRepository $repository,
        RelatedLinkMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    /**
     * @param CoreEntity $coreEntity
     * @param array $linkTypes
     * @param int|null $limit
     * @param int $page
     * @param string $ttl
     * @return RelatedLink[]
     */
    public function findByRelatedToProgramme(
        CoreEntity $coreEntity,
        array $linkTypes = [],
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $coreEntity->getDbId(), implode('|', $linkTypes), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($coreEntity, $linkTypes, $limit, $page) {
                $dbEntities = $this->repository->findByRelatedTo(
                    [$coreEntity->getDbId()],
                    'programme',
                    $linkTypes,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }
}
