<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Cache\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ImageRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ImageMapper;

class ImagesService extends AbstractService
{
    /** @var ImageMapper */
    protected $mapper;

    /** @var ImageRepository */
    protected $repository;

    public function __construct(
        ImageRepository $repository,
        ImageMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByPid(Pid $pid, $ttl = CacheInterface::NORMAL): ?Image
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, (string) $pid, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($pid) {
                $dbEntity = $this->repository->findByPid($pid);
                return $this->mapSingleEntity($dbEntity);
            }
        );
    }
}
