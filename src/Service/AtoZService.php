<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\AtoZTitleRepository;
use BBC\ProgrammesPagesService\Mapper\MapperInterface;

class AtoZService extends AbstractService
{
    public function __construct(
        AtoZTitleRepository $repository,
        MapperInterface $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findAllLetters(string $networkMedium = null)
    {
        return $this->repository->findAllLetters($networkMedium);
    }

    public function findTLEOsByFirstLetter(
        string $letter,
        string $networkMedium = null,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $entities = $this->repository->findTLEOsByFirstLetter(
            $letter,
            $limit,
            $this->getOffset($limit, $page),
            $networkMedium
        );
        return $this->mapManyEntities($entities);
    }

    public function countTLEOsByFirstLetter(string $letter, string $networkMedium = null)
    {
        return $this->repository->countTLEOsByFirstLetter($letter, $networkMedium);
    }

    public function findAvailableTLEOsByFirstLetter(
        string $letter,
        string $networkMedium = null,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $entities = $this->repository->findTLEOsByFirstLetter(
            $letter,
            $limit,
            $this->getOffset($limit, $page),
            $networkMedium,
            true
        );
        return $this->mapManyEntities($entities);
    }

    public function countAvailableTLEOsByFirstLetter(string $letter, string $networkMedium = null)
    {
        return $this->repository->countTLEOsByFirstLetter($letter, $networkMedium, true);
    }
}
