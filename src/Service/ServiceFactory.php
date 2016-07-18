<?php

namespace BBC\ProgrammesPagesService\Service;

use Doctrine\ORM\EntityManager;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperFactory;

class ServiceFactory
{
    protected $instances = [];

    protected $entityManager;

    protected $mapperFactory;

    public function __construct(
        EntityManager $entityManager,
        MapperFactory $mapperFactory
    ) {
        $this->entityManager = $entityManager;
        $this->mapperFactory = $mapperFactory;
    }

    public function getContributorsService(): ContributorsService
    {
        if (!array_key_exists('ContributorsService', $this->instances)) {
            $this->instances['ContributorsService'] = new ContributorsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Contributor'),
                $this->mapperFactory->getContributorMapper()
            );
        }

        return $this->instances['ContributorsService'];
    }

    public function getProgrammesService(): ProgrammesService
    {
        if (!array_key_exists('ProgrammesService', $this->instances)) {
            $this->instances['ProgrammesService'] = new ProgrammesService(
                $this->entityManager->getRepository('ProgrammesPagesService:CoreEntity'),
                $this->mapperFactory->getProgrammeMapper()
            );
        }

        return $this->instances['ProgrammesService'];
    }

    public function getRelatedLinksService(): RelatedLinksService
    {
        if (!array_key_exists('RelatedLinksService', $this->instances)) {
            $this->instances['RelatedLinksService'] = new RelatedLinksService(
                $this->entityManager->getRepository('ProgrammesPagesService:RelatedLink'),
                $this->mapperFactory->getRelatedLinkMapper()
            );
        }

        return $this->instances['RelatedLinksService'];
    }

    public function getSegmentEventsService(): SegmentEventsService
    {
        if (!array_key_exists('SegmentEventsService', $this->instances)) {
            $this->instances['SegmentEventsService'] = new SegmentEventsService(
                $this->entityManager->getRepository('ProgrammesPagesService:SegmentEvent'),
                $this->mapperFactory->getSegmentEventMapper()
            );
        }

        return $this->instances['SegmentEventsService'];
    }

    public function getVersionsService(): VersionsService
    {
        if (!array_key_exists('VersionsService', $this->instances)) {
            $this->instances['VersionsService'] = new VersionsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Version'),
                $this->mapperFactory->getVersionMapper()
            );
        }

        return $this->instances['VersionsService'];
    }
}
