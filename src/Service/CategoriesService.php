<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository;
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
}
