<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionMapper;

class VersionsService extends AbstractService
{
    public function __construct(
        VersionRepository $repository,
        VersionMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    /**
     * @param Pid $pid
     * @return Version|null
     */
    public function findByPidFull(Pid $pid)
    {
        $dbEntity = $this->repository->findByPidFull($pid);

        return $this->mapSingleEntity($dbEntity);
    }

    public function findByProgrammeItem(ProgrammeItem $programmeItem): array
    {
        $dbEntities = $this->repository->findByProgrammeItem($programmeItem->getDbId());

        return $this->mapManyEntities($dbEntities);
    }
}
