<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Cache\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\SegmentMapper;

class SegmentsService extends AbstractService
{
    public function __construct(
        SegmentRepository $repository,
        SegmentMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByPidFull(Pid $pid): ?Segment
    {
        $dbEntity = $this->repository->findByPidFull($pid);

        return $this->mapSingleEntity($dbEntity);
    }
}
