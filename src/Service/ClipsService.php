<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ClipRepository;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CoreEntityMapper;
use BBC\ProgrammesPagesService\Cache\CacheInterface;

class ClipsService extends AbstractService
{
    private const CLIP = 'Clip';

    public function __construct(
        CoreEntityRepository $repository,
        CoreEntityMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByAncestryIds(array $findByAncestryIds)
    {
        return $this->repository->findProgrammesByAncestryAndType($findByAncestryIds, 'Episode');
    }
}
