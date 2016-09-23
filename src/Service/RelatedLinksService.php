<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\RelatedLinkRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\RelatedLinkMapper;
use BBC\ProgrammesPagesService\Service\Util\ServiceConstants;

class RelatedLinksService extends AbstractService
{
    public function __construct(
        RelatedLinkRepository $repository,
        RelatedLinkMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findByRelatedToProgramme(
        Programme $programme,
        $limit = ServiceConstants::DEFAULT_LIMIT,
        int $page = ServiceConstants::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findByRelatedTo(
            [$programme->getDbId()],
            'programme',
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }
}
