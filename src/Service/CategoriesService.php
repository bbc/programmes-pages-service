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
                $genres = $this->repository->findAllByTypeAndMaxDepth('genre', 2);
                return $this->mapManyEntities($genres);
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

    public function findGenreByUrlKeyAncestry(array $urlHierarchy, $ttl = CacheInterface::NORMAL): ?Genre
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, implode('|', $urlHierarchy), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($urlHierarchy) {
                $genre = $this->repository->findByUrlKeyAncestryAndType($urlHierarchy, 'genre');
                return $this->mapSingleEntity($genre);
            }
        );
    }

    /**
     * @return Genre[]
     */
    public function findPopulatedChildGenres(Genre $genre, $ttl = CacheInterface::NORMAL): array
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $genre->getDbId(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($genre) {
                $subcategories = $this->repository->findPopulatedChildCategories(
                    $genre->getDbId(),
                    'genre'
                );
                return $this->mapManyEntities($subcategories);
            }
        );
    }
}
