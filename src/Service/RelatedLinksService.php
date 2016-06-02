<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\RelatedLinkRepository;
use BBC\ProgrammesPagesService\Domain\Entity\RelatedLink;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\RelatedLinkMapper;

class RelatedLinksService extends AbstractService
{
    public function __construct(
        RelatedLinkRepository $repository,
        RelatedLinkMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findByRelatedToProgrammeDbId(
        int $dbid,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findByRelatedTo(
            [$dbid],
            'programme',
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }
}
