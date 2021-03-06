<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CategoryMapper;

class CategoriesService extends AbstractService
{
    /* @var CategoryMapper */
    protected $mapper;

    /* @var CategoryRepository */
    protected $repository;

    public function __construct(
        CategoryRepository $repository,
        CategoryMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findFormats($ttl = CacheInterface::NORMAL): array
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () {
                $formats = $this->repository->findAllByTypeAndMaxDepth('format', 2);
                return $this->mapManyEntities($formats);
            }
        );
    }

    public function findGenres($ttl = CacheInterface::NORMAL): array
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () {
                return $this->mapManyEntities($this->repository->findTopLevelGenresAndChildren());
            }
        );
    }

    public function findFormatByUrlKeyAncestry(string $formatUrlKey, $ttl = CacheInterface::NORMAL): ?Format
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $formatUrlKey, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($formatUrlKey) {
                $format = $this->repository->findByUrlKeyAncestryAndType([$formatUrlKey], 'format');
                return $this->mapSingleEntity($format);
            }
        );
    }

    public function findGenreByUrlKeyAncestryWithDescendants(array $urlHierarchy, $ttl = CacheInterface::NORMAL): ?Genre
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, implode('|', $urlHierarchy), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($urlHierarchy) {
                $result = $this->repository->findByUrlKeyAncestryAndType($urlHierarchy, 'genre');

                if (!$result) {
                    // not found, return null
                    return $result;
                }
                // get descendants
                $result['children'] = $this->repository->findAllDescendantsByParentId(
                    $result['id'],
                    'genre'
                );
                return $this->mapSingleEntity($result);
            }
        );
    }
}
