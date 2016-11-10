<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository;
use BBC\ProgrammesPagesService\Domain\Enumeration\CategoryTypeEnum;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CategoryMapper;
use InvalidArgumentException;

class CategoriesService extends AbstractService
{
    public function __construct(
        CategoryRepository $repository,
        CategoryMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findUsedByType(string $type): array
    {
        $usedByType = $this->repository->findUsedByType($this->getCategoryType($type));
        return $this->mapManyEntities($usedByType);
    }

    private function getCategoryType($type): string
    {
        switch ($type) {
            case 'formats':
                return CategoryTypeEnum::FORMAT;
            case 'genres':
                return CategoryTypeEnum::GENRE;
            default:
                throw new InvalidArgumentException(
                    "'" . $type . "' is not a valid category type. Valid types are : " .
                    CategoryTypeEnum::FORMAT . " or " .
                    CategoryTypeEnum::GENRE
                );
        }
    }
}
