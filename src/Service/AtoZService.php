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

    public function findAllLetters()
    {
        return $this->repository->findAllLetters();
    }

    public function findTLEOsByFirstLetter(
        string $letter,
        string $networkUrlKey = null,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array
    {
        $entities = $this->repository->findTLEOsByFirstLetter(
            $letter,
            $limit,
            $this->getOffset($limit, $page),
            $networkUrlKey
        );
        return $this->mapManyEntities($entities);
    }

    public function countTLEOsByFirstLetter(string $letter, string $networkUrlKey = null)
    {
        return $this->repository->countTLEOsByFirstLetter($letter, $networkUrlKey);
    }

    public function findAvailableTLEOsByFirstLetter(
        string $letter,
        string $networkUrlKey = null,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array
    {
        $entities = $this->repository->findTLEOsByFirstLetter(
            $letter,
            $limit,
            $this->getOffset($limit, $page),
            $networkUrlKey,
            true
        );
        return $this->mapManyEntities($entities);
    }

    public function countAvailableTLEOsByFirstLetter(string $letter, string $networkUrlKey = null)
    {
        return $this->repository->countTLEOsByFirstLetter($letter, $networkUrlKey, true);
    }
}
