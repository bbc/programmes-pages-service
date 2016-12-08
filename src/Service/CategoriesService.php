<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CategoryMapper;

class CategoriesService extends AbstractService
{
    public function __construct(
        CategoryRepository $repository,
        CategoryMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findUsedFormats(): array
    {
        $usedByType = $this->repository->findUsedByType('format');
        return $this->mapManyEntities($usedByType);
    }

    public function findUsedGenres(): array
    {
        $usedByType = $this->repository->findUsedByType('genre');
        return $this->mapManyEntities($usedByType);
    }

    public function findGenreByUrlKeyAncestry(array $urlHierarchy)
    {
        $genre = $this->repository->findByUrlKeyAncestryAndType($urlHierarchy, 'genre');
        return $this->mapSingleEntity($genre);
    }

    public function findFormatByUrlKeyAncestry(string $formatUrlKey): Format
    {
        $format = $this->repository->findByUrlKeyAncestryAndType([$formatUrlKey], 'format');
        return $this->mapSingleEntity($format);
    }

    public function findPopulatedChildGenres(Genre $genre, string $medium = null)
    {
        $subcategories = $this->repository->findPopulatedChildCategoriesByNetworkMedium(
            $genre->getDbId(),
            'genre',
            $medium
        );
        return $this->mapManyEntities($subcategories);
    }
}
