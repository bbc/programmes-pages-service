<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\SegmentEventMapper;

class SegmentEventsService extends AbstractService
{
    public function __construct(
        SegmentEventRepository $repository,
        SegmentEventMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findLatestBroadcastedForContributorDbId(
        int $dbid,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findFullLatestBroadcastedForContributor(
            $dbid,
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }
}
