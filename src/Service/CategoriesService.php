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

    public function findGenreByUrlKeyAncestry(
        string $category1,
        string $category2 = null,
        string $category3 = null
    ): Genre {
        $categoryWithAncestry = $this->repository->findByUrlKeyAncestryAndType(
            'genre',
            $category1,
            $category2,
            $category3
        );

        return $this->mapSingleEntity($categoryWithAncestry);
    }

    public function findFormatByUrlKeyAncestry(string $formatUrlKey): Format
    {
        $format = $this->repository->findByUrlKeyAncestryAndType('format', $formatUrlKey);
        return $this->mapSingleEntity($format);
    }

    public function findChildGenresUsedByTleosByParent(Genre $genre)
    {
        $subcategories = $this->repository->findChildCategoriesUsedByTleosByParentIdAndType(
            $genre->getDbId(),
            'genre'
        );
        return $this->mapManyEntities($subcategories);
    }

    public function findChildGenresUsedByTleosByParentAndNetworkUrlKey(Genre $genre, string $networkUrlKey)
    {
        $subcategories = $this->repository->findChildCategoriesUsedByTleosByParentIdAndTypeAndNetworkUrlKey(
            $genre->getDbId(),
            $networkUrlKey,
            'genre'
        );
        return $this->mapManyEntities($subcategories);
    }
}
