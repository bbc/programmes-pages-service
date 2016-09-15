<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\SegmentMapper;

class SegmentsService extends AbstractService
{
    public function __construct(
        SegmentRepository $repository,
        SegmentMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    /**
     * @param Pid $pid
     * @return Segment|null
     */
    public function findByPid(Pid $pid)
    {
        $dbEntity = $this->repository->findByPid($pid);

        return $this->mapSingleEntity($dbEntity);
    }
}
