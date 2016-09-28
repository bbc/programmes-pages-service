<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\BroadcastMapper;

class BroadcastsService extends AbstractService
{
    public function __construct(
        BroadcastRepository $repository,
        BroadcastMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findByVersion(
        Version $version,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findByVersion(
            [$version->getDbId()],
            'Broadcast',
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function findAllYearsAndMonthsByProgramme(Programme $programme): array
    {
        $dbYearsAndMonths = $this->repository->findAllYearsAndMonthsByProgramme(
            $programme->getDbAncestryIds()
        );

        return array_reduce($dbYearsAndMonths, function ($memo, $period) {
            $year = (int) $period['year'];
            if (!array_key_exists($year, $memo)) {
                $memo[$year] = [];
            }

            $memo[$year][] = (int) $period['month'];

            return $memo;
        }, []);
    }
}
