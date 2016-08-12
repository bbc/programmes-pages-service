<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\Entity\SegmentEvent;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\SegmentEventMapper;

class SegmentEventsService extends AbstractService
{
    public function __construct(
        SegmentEventRepository $repository,
        SegmentEventMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findLatestBroadcastedForContributor(
        Contributor $contributor,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findFullLatestBroadcastedForContributor(
            $contributor->getDbId(),
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    /**
     * @param Version $version
     * @return SegmentEvent[]
     */
    public function findSegmentEventsOfVersion(Version $version)
    {
        $events = $this->repository->findSegmentEventsOfVersionId($version->getDbId());
        /** @var SegmentEvent[] $mappedEvents */
        $mappedEvents = [];
        foreach ($events as $event) {
            $mappedEvents[] = $this->mapper->getDomainModel($event);
        }
        return $mappedEvents;
    }
}
