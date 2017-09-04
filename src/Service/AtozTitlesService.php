<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Cache\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\AtozTitle;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\AtozTitleRepository;
use BBC\ProgrammesPagesService\Mapper\MapperInterface;

class AtozTitlesService extends AbstractService
{
    public const LETTERS = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', AtozTitle::NUMERIC_KEY];

    /* @var AtozTitleRepository */
    protected $repository;

    public function __construct(
        AtozTitleRepository $repository,
        MapperInterface $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findAllLetters(): array
    {
        return self::LETTERS;
    }

    public function findTleosByFirstLetter(
        string $letter,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $letter, $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($letter, $limit, $page) {
                $entities = $this->repository->findTleosByFirstLetter(
                    $letter,
                    false,
                    $limit,
                    $this->getOffset($limit, $page)
                );
                return $this->mapManyEntities($entities);
            }
        );
    }

    public function countTleosByFirstLetter(string $letter, $ttl = CacheInterface::NORMAL): int
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $letter, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($letter) {
                return $this->repository->countTleosByFirstLetter($letter, false);
            }
        );
    }

    public function findAvailableTleosByFirstLetter(
        string $letter,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $letter, $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($letter, $limit, $page) {
                $entities = $this->repository->findTleosByFirstLetter(
                    $letter,
                    true,
                    $limit,
                    $this->getOffset($limit, $page)
                );
                return $this->mapManyEntities($entities);
            }
        );
    }

    public function countAvailableTleosByFirstLetter(string $letter, $ttl = CacheInterface::NORMAL)
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $letter, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($letter) {
                return $this->repository->countTleosByFirstLetter($letter, true);
            }
        );
    }
}
