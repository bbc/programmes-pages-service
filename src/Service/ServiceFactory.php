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

    public function getAtozTitlesService(): AtozTitlesService
    {
        if (!isset($this->instances['AtozTitlesService'])) {
            $this->instances['AtozTitlesService'] = new AtozTitlesService(
                $this->entityManager->getRepository('ProgrammesPagesService:AtozTitle'),
                $this->mapperFactory->getAtozTitleMapper()
            );
        }

        return $this->instances['AtozTitlesService'];
    }

    public function getBroadcastsService(): BroadcastsService
    {
        if (!isset($this->instances['BroadcastsService'])) {
            $this->instances['BroadcastsService'] = new BroadcastsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Broadcast'),
                $this->mapperFactory->getBroadcastMapper()
            );
        }

        return $this->instances['BroadcastsService'];
    }

    public function getCategoriesService(): CategoriesService
    {
        if (!isset($this->instances['CategoriesService'])) {
            $this->instances['CategoriesService'] = new CategoriesService(
                $this->entityManager->getRepository('ProgrammesPagesService:Category'),
                $this->mapperFactory->getCategoryMapper()
            );
        }

        return $this->instances['CategoriesService'];
    }

    public function getCollapsedBroadcastsService(): CollapsedBroadcastsService
    {
        if (!isset($this->instances['CollapsedBroadcastsService'])) {
            $this->instances['CollapsedBroadcastsService'] = new CollapsedBroadcastsService(
                $this->entityManager->getRepository('ProgrammesPagesService:CollapsedBroadcast'),
                $this->mapperFactory->getCollapsedBroadcastMapper(),
                $this->entityManager->getRepository('ProgrammesPagesService:Service')
            );
        }

        return $this->instances['CollapsedBroadcastsService'];
    }

    public function getContributionsService(): ContributionsService
    {
        if (!isset($this->instances['ContributionsService'])) {
            $this->instances['ContributionsService'] = new ContributionsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Contribution'),
                $this->mapperFactory->getContributionMapper()
            );
        }

        return $this->instances['ContributionsService'];
    }

    public function getContributorsService(): ContributorsService
    {
        if (!isset($this->instances['ContributorsService'])) {
            $this->instances['ContributorsService'] = new ContributorsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Contributor'),
                $this->mapperFactory->getContributorMapper()
            );
        }

        return $this->instances['ContributorsService'];
    }

    public function getNetworksService(): NetworksService
    {
        if (!isset($this->instances['NetworksService'])) {
            $this->instances['NetworksService'] = new NetworksService(
                $this->entityManager->getRepository('ProgrammesPagesService:Network'),
                $this->mapperFactory->getNetworkMapper()
            );
        }

        return $this->instances['NetworksService'];
    }

    public function getProgrammesService(): ProgrammesService
    {
        if (!isset($this->instances['ProgrammesService'])) {
            $this->instances['ProgrammesService'] = new ProgrammesService(
                $this->entityManager->getRepository('ProgrammesPagesService:CoreEntity'),
                $this->mapperFactory->getProgrammeMapper()
            );
        }

        return $this->instances['ProgrammesService'];
    }

    public function getRelatedLinksService(): RelatedLinksService
    {
        if (!isset($this->instances['RelatedLinksService'])) {
            $this->instances['RelatedLinksService'] = new RelatedLinksService(
                $this->entityManager->getRepository('ProgrammesPagesService:RelatedLink'),
                $this->mapperFactory->getRelatedLinkMapper()
            );
        }

        return $this->instances['RelatedLinksService'];
    }

    public function getSegmentEventsService(): SegmentEventsService
    {
        if (!isset($this->instances['SegmentEventsService'])) {
            $this->instances['SegmentEventsService'] = new SegmentEventsService(
                $this->entityManager->getRepository('ProgrammesPagesService:SegmentEvent'),
                $this->mapperFactory->getSegmentEventMapper()
            );
        }

        return $this->instances['SegmentEventsService'];
    }

    public function getSegmentsService(): SegmentsService
    {
        if (!isset($this->instances['SegmentsService'])) {
            $this->instances['SegmentsService'] = new SegmentsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Segment'),
                $this->mapperFactory->getSegmentMapper()
            );
        }

        return $this->instances['SegmentsService'];
    }

    public function getVersionsService(): VersionsService
    {
        if (!isset($this->instances['VersionsService'])) {
            $this->instances['VersionsService'] = new VersionsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Version'),
                $this->mapperFactory->getVersionMapper()
            );
        }

        return $this->instances['VersionsService'];
    }
}
