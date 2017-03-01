<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\AtozTitleRepository;
use BBC\ProgrammesPagesService\Mapper\MapperInterface;

class AtozTitlesService extends AbstractService
{
    public function __construct(
        AtozTitleRepository $repository,
        MapperInterface $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findAllLetters(): array
    {
        return $this->repository->findAllLetters();
    }

    public function findTleosByFirstLetter(
        string $letter,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $entities = $this->repository->findTleosByFirstLetter(
            $letter,
            false,
            $limit,
            $this->getOffset($limit, $page)
        );
        return $this->mapManyEntities($entities);
    }

    public function countTleosByFirstLetter(string $letter): int
    {
        return $this->repository->countTleosByFirstLetter($letter, false);
    }

    public function findAvailableTleosByFirstLetter(
        string $letter,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $entities = $this->repository->findTleosByFirstLetter(
            $letter,
            true,
            $limit,
            $this->getOffset($limit, $page)
        );
        return $this->mapManyEntities($entities);
    }

    public function countAvailableTleosByFirstLetter(string $letter)
    {
        return $this->repository->countTleosByFirstLetter($letter, true);
    }
}
