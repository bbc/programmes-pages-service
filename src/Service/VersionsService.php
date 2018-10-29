<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Domain\Entity\Group;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeContainer;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionMapper;

class VersionsService extends AbstractService
{
    /* @var VersionMapper */
    protected $mapper;

    /* @var VersionRepository */
    protected $repository;

    public function __construct(
        VersionRepository $repository,
        VersionMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByPidFull(Pid $pid, $ttl = CacheInterface::NORMAL): ?Version
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, (string) $pid, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($pid) {
                $dbEntity = $this->repository->findByPidFull($pid);

                return $this->mapSingleEntity($dbEntity);
            }
        );
    }

    public function findByProgrammeItem(ProgrammeItem $programmeItem, $ttl = CacheInterface::NORMAL): array
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programmeItem->getDbId(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programmeItem) {
                $dbEntities = $this->repository->findByProgrammeItem($programmeItem->getDbId());

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    public function findOriginalVersionForProgrammeItem(ProgrammeItem $programmeItem, $ttl = CacheInterface::NORMAL): ?Version
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programmeItem->getDbId(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programmeItem) {
                $dbEntity = $this->repository->findOriginalVersionForProgrammeItem($programmeItem->getDbId());

                return $this->mapSingleEntity($dbEntity);
            }
        );
    }

    /**
     * Returns all streamable versions for a programme item. The canonical streamable version first.
     *
     * @param ProgrammeItem $programmeItem
     * @param string $ttl
     * @return Version[]
     */
    public function findAllStreamableByProgrammeItem(ProgrammeItem $programmeItem, $ttl = CacheInterface::NORMAL): array
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programmeItem->getDbId(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programmeItem) {
                $dbEntity = $this->repository->findAllStreamableByProgrammeItem((string) $programmeItem->getDbId());

                return $this->mapManyEntities($dbEntity);
            }
        );
    }

    /**
     * This method gets all of the special versions Faucet links against a ProgrammeItem. That is,
     * the streamableVersion (the version that should be played out/linked to in playout), the
     * canonicalVersion (the version that should be used to display segment events), and the
     * downloadableVersion (the version that should be linked to for downloads/podcasts)
     *
     * It returns an array of those in the format below
     *
     * @param ProgrammeItem $programmeItem
     * @param string $ttl
     * @return array [
     *      'streamableVersion' => Linked Streamable Version,
     *      'downloadableVersion' => Linked Downloadable Version,
     *      'canonicalVersion' => Linked version for Segment Events,
     * ]
     */
    public function findLinkedVersionsForProgrammeItem(ProgrammeItem $programmeItem, $ttl = CacheInterface::NORMAL): array
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programmeItem->getDbId(), $ttl);
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programmeItem) {
                $programmeEntity = $this->repository->findLinkedVersionsForProgrammeItem($programmeItem->getDbId());
                $dataArray = [
                    'streamableVersion' => null,
                    'downloadableVersion' => null,
                    'canonicalVersion' => null,
                ];

                if (!empty($programmeEntity['streamableVersion'])) {
                    $dataArray['streamableVersion'] = $this->mapSingleEntity($programmeEntity['streamableVersion']);
                }

                if (!empty($programmeEntity['downloadableVersion'])) {
                    $dataArray['downloadableVersion'] = $this->mapSingleEntity($programmeEntity['downloadableVersion']);
                }

                if (!empty($programmeEntity['canonicalVersion'])) {
                    $dataArray['canonicalVersion'] = $this->mapSingleEntity($programmeEntity['canonicalVersion']);
                }
                return $dataArray;
            }
        );
    }

    /**
     * @param ProgrammeItem $programmeItem
     * @param string $ttl
     * @return mixed
     */
    public function findAlternateVersionsForProgrammeItem(ProgrammeItem $programmeItem, $ttl = CacheInterface::NORMAL)
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programmeItem->getDbId(), $ttl);
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programmeItem) {
                $dbEntity = $this->repository->findAlternateVersionsForProgrammeItem((string) $programmeItem->getDbId());
                return $this->mapManyEntities($dbEntity);
            }
        );
    }

    public function findDownloadableDescendantEpisodesForProgramme(
        ProgrammeContainer $programmeContainer,
        ?int $limit,
        int $page,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programmeContainer->getPid(), $limit, $page, $ttl);
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programmeContainer, $limit, $page) {
                $programmes = $this->repository->findDownloadableDescendantEpisodesForProgramme(
                    $programmeContainer->getDbAncestryIds(),
                    $limit,
                    $this->getOffset($limit, $page)
                );
                $versions = [];
                foreach ($programmes as $programme) {
                    if (isset($programme['downloadableVersion'])) {
                        $versions[] = $programme['downloadableVersion'];
                    }
                }
                return $this->mapManyEntities($versions);
            },
            [],
            $nullTtl
        );
    }

    public function findDownloadableDescendantEpisodesForGroup(
        Group $group,
        ?int $limit,
        int $page,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $group->getPid(), $limit, $page, $ttl);
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($group, $limit, $page) {
                $programmes = $this->repository->findDownloadableDescendantEpisodesForGroup(
                    $group->getDbId(),
                    $limit,
                    $this->getOffset($limit, $page)
                );
                $versions = [];
                foreach ($programmes as $programme) {
                    if (isset($programme['downloadableVersion'])) {
                        $versions[] = $programme['downloadableVersion'];
                    }
                }
                return $this->mapManyEntities($versions);
            },
            [],
            $nullTtl
        );
    }

    /**
     * Find for each clip the version that should be played out/linked to in playout
     *
     * @param ProgrammeItem[] $programmeItems
     * @param string $ttl
     * @return Version[]
     */
    public function findStreamableVersionForProgrammeItems(array $programmeItems, $ttl = CacheInterface::NORMAL): array
    {
        $programmeItemsIds = [];
        foreach ($programmeItems as $programmeItem) {
            $programmeItemsIds[] = $programmeItem->getDbId();
        }

        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, implode('|', $programmeItemsIds), $ttl);
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programmeItemsIds) {
                $programmeEntities = $this->repository->findStreamableVersionForProgrammeItems($programmeItemsIds);
                $dataArray = [];

                foreach ($programmeEntities as $programmeEntity) {
                    if (!empty($programmeEntity['streamableVersion'])) {
                        $dataArray[$programmeEntity['pid']] = $this->mapSingleEntity($programmeEntity['streamableVersion']);
                    }
                }
                return $dataArray;
            }
        );
    }
}
