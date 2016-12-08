<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\AtoZTitleRepository;
use BBC\ProgrammesPagesService\Mapper\MapperInterface;

class AToZService extends AbstractService
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

    public function findTleosByFirstLetter(
        string $letter,
        string $networkMedium = null,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $entities = $this->repository->findTleosByFirstLetter(
            $letter,
            $limit,
            $this->getOffset($limit, $page),
            $networkMedium
        );
        return $this->mapManyEntities($entities);
    }

    public function countTleosByFirstLetter(string $letter, string $networkMedium = null)
    {
        return $this->repository->countTleosByFirstLetter($letter, $networkMedium);
    }

    public function findAvailableTleosByFirstLetter(
        string $letter,
        string $networkMedium = null,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $entities = $this->repository->findTleosByFirstLetter(
            $letter,
            $limit,
            $this->getOffset($limit, $page),
            $networkMedium,
            true
        );
        return $this->mapManyEntities($entities);
    }

    public function countAvailableTleosByFirstLetter(string $letter, string $networkMedium = null)
    {
        return $this->repository->countTleosByFirstLetter($letter, $networkMedium, true);
    }
}
