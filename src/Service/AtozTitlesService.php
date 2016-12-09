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

    public function findAllLetters(string $networkMedium = null)
    {
        return $this->repository->findAllLetters($networkMedium);
    }

    public function findTleosByFirstLetter(
        string $letter,
        string $networkMedium = null,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $entities = $this->repository->findTleosByFirstLetter(
            $letter,
            $networkMedium,
            false,
            $limit,
            $this->getOffset($limit, $page)
        );
        return $this->mapManyEntities($entities);
    }

    public function countTleosByFirstLetter(string $letter, string $networkMedium = null)
    {
        return $this->repository->countTleosByFirstLetter($letter, $networkMedium, false);
    }

    public function findAvailableTleosByFirstLetter(
        string $letter,
        string $networkMedium = null,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $entities = $this->repository->findTleosByFirstLetter(
            $letter,
            $networkMedium,
            true,
            $limit,
            $this->getOffset($limit, $page)
        );
        return $this->mapManyEntities($entities);
    }

    public function countAvailableTleosByFirstLetter(string $letter, string $networkMedium = null)
    {
        return $this->repository->countTleosByFirstLetter($letter, $networkMedium, true);
    }
}
