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

    public function getAtoZTitlesService(): AtoZTitlesService
    {
        if (!array_key_exists('AtoZTitlesService', $this->instances)) {
            $this->instances['AtoZTitlesService'] = new AtoZTitlesService(
                $this->entityManager->getRepository('ProgrammesPagesService:AtoZTitle'),
                $this->mapperFactory->getAtoZTitleMapper()
            );
        }

        return $this->instances['AtoZTitlesService'];
    }

    public function getBroadcastsService(): BroadcastsService
    {
        if (!array_key_exists('BroadcastsService', $this->instances)) {
            $this->instances['BroadcastsService'] = new BroadcastsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Broadcast'),
                $this->mapperFactory->getBroadcastMapper()
            );
        }

        return $this->instances['BroadcastsService'];
    }

    public function getCategoriesService(): CategoriesService
    {
        if (!array_key_exists('CategoriesService', $this->instances)) {
            $this->instances['CategoriesService'] = new CategoriesService(
                $this->entityManager->getRepository('ProgrammesPagesService:Category'),
                $this->mapperFactory->getCategoryMapper()
            );
        }

        return $this->instances['CategoriesService'];
    }

    public function getCollapsedBroadcastsService(): CollapsedBroadcastsService
    {
        if (!array_key_exists('CollapsedBroadcastsService', $this->instances)) {
            $this->instances['CollapsedBroadcastsService'] = new CollapsedBroadcastsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Broadcast'),
                $this->mapperFactory->getCollapsedBroadcastMapper(),
                $this->entityManager->getRepository('ProgrammesPagesService:Service')
            );
        }

        return $this->instances['CollapsedBroadcastsService'];
    }

    public function getContributionsService(): ContributionsService
    {
        if (!array_key_exists('ContributionsService', $this->instances)) {
            $this->instances['ContributionsService'] = new ContributionsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Contribution'),
                $this->mapperFactory->getContributionMapper()
            );
        }

        return $this->instances['ContributionsService'];
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

    public function getNetworksService(): NetworksService
    {
        if (!array_key_exists('NetworksService', $this->instances)) {
            $this->instances['NetworksService'] = new NetworksService(
                $this->entityManager->getRepository('ProgrammesPagesService:Network'),
                $this->mapperFactory->getNetworkMapper()
            );
        }

        return $this->instances['NetworksService'];
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

    public function getSegmentsService(): SegmentsService
    {
        if (!array_key_exists('SegmentsService', $this->instances)) {
            $this->instances['SegmentsService'] = new SegmentsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Segment'),
                $this->mapperFactory->getSegmentMapper()
            );
        }

        return $this->instances['SegmentsService'];
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
