<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CategoryMapper;
use Psr\Cache\CacheItemPoolInterface;

class CategoriesService extends AbstractService
{
    public function __construct(
        CategoryRepository $repository,
        CategoryMapper $mapper,
        CacheItemPoolInterface $cacheItemPoolInterface
    ) {
        parent::__construct($repository, $mapper, $cacheItemPoolInterface);
    }

    public function findFormats(): array
    {
        $formats = $this->repository->findAllByTypeAndMaxDepth('format', 2);
        return $this->mapManyEntities($formats);
    }

    public function findGenres(): array
    {
        $genres = $this->repository->findAllByTypeAndMaxDepth('genre', 2);
        return $this->mapManyEntities($genres);
    }

    public function findFormatByUrlKeyAncestry(string $formatUrlKey): ?Format
    {
        $format = $this->repository->findByUrlKeyAncestryAndType([$formatUrlKey], 'format');
        return $this->mapSingleEntity($format);
    }

    public function findGenreByUrlKeyAncestry(array $urlHierarchy): ?Genre
    {
        $genre = $this->repository->findByUrlKeyAncestryAndType($urlHierarchy, 'genre');
        return $this->mapSingleEntity($genre);
    }

    /**
     * @return Genre[]
     */
    public function findPopulatedChildGenres(Genre $genre): array
    {
        $subcategories = $this->repository->findPopulatedChildCategories(
            $genre->getDbId(),
            'genre'
        );
        return $this->mapManyEntities($subcategories);
    }
}
