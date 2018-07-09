<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Collection;
use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\Entity\Podcast;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedCoreEntity;

class PodcastMapper extends AbstractMapper
{
    private $cache = [];

    public function getCacheKey(array $dbPodcast): string
    {
        return $this->buildCacheKey($dbPodcast, 'id', [
            'programmeItem' => 'CoreEntity',
        ]);
    }

    public function getDomainModel(array $dbPodcast)
    {
        $cacheKey = $this->getCacheKey($dbPodcast);

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = new Podcast(
                $this->getCoreEntityModel($dbPodcast),
                $dbPodcast['frequency'],
                $dbPodcast['availability'],
                $dbPodcast['isUkOnly'],
                $dbPodcast['isLowBitrate']
            );
        }

        return $this->cache[$cacheKey];
    }

    /**
     * @param array $dbPodcast
     * @param string $key
     * @return ProgrammeItem|Collection
     */
    private function getCoreEntityModel(array $dbPodcast, string $key = 'coreEntity'): CoreEntity
    {
        return $this->mapperFactory->getCoreEntityMapper()->getDomainModel($dbPodcast[$key]);
    }
}
