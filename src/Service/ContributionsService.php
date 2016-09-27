<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ContributionRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ContributionMapper;

class ContributionsService extends AbstractService
{
    public function __construct(
        ContributionRepository $repository,
        ContributionMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findByContributionToProgramme(
        Programme $programme,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findByContributionTo(
            [$programme->getDbId()],
            'programme',
            false,
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function findByContributionToVersion(
        Version $version,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findByContributionTo(
            [$version->getDbId()],
            'version',
            false,
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function findByContributionToSegment(
        Segment $segment,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findByContributionTo(
            [$segment->getDbId()],
            'segment',
            false,
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function findByContributionToSegments(
        array $segments,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $segmentIds = array_map(function ($s) {
            return $s->getDbId();
        }, $segments);

        $dbEntities = $this->repository->findByContributionTo(
            $segmentIds,
            'segment',
            true,
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }
}
