<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Category;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository;
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
        array $categories
    ) {
        /** @var Category $categoryWithAncestry */
        $categoryWithAncestry = $this->repository->findByUrlKeyAncestryAndType(
            'genre',
            $categories
        );

        return $this->mapSingleEntity($categoryWithAncestry);
    }

    public function findChildGenresUsedByTleosByParent(Genre $genre)
    {
        $subcategories = $this->repository->findChildCategoriesUsedByTleosByParentIdAndType(
            $genre->getDbId(),
            'genre'
        );
        return $this->mapManyEntities($subcategories);
    }

    public function findFormatByUrlKeyAncestry(
        array $categories
    ) {
        /** @var Category $categoryWithAncestry */
        $categoryWithAncestry = $this->repository->findByUrlKeyAncestryAndType(
            'format',
            $categories
        );
        return $this->mapManyEntities($categoryWithAncestry);
    }
}
