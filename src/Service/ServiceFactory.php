<?php

namespace BBC\ProgrammesPagesService\Service;

use Doctrine\ORM\EntityManagerInterface;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperFactory;
use Psr\Cache\CacheItemPoolInterface;

class ServiceFactory
{
    protected $instances = [];

    protected $entityManager;

    protected $mapperFactory;
    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPoolInterface;

    public function __construct(
        EntityManagerInterface $entityManager,
        MapperFactory $mapperFactory,
        CacheItemPoolInterface $cacheItemPoolInterface
    ) {
        $this->entityManager = $entityManager;
        $this->mapperFactory = $mapperFactory;
        $this->cacheItemPoolInterface = $cacheItemPoolInterface;
    }

    public function getAtozTitlesService(): AtozTitlesService
    {
        if (!isset($this->instances['AtozTitlesService'])) {
            $this->instances['AtozTitlesService'] = new AtozTitlesService(
                $this->entityManager->getRepository('ProgrammesPagesService:AtozTitle'),
                $this->mapperFactory->getAtozTitleMapper(),
                $this->cacheItemPoolInterface
            );
        }

        return $this->instances['AtozTitlesService'];
    }

    public function getBroadcastsService(): BroadcastsService
    {
        if (!isset($this->instances['BroadcastsService'])) {
            $this->instances['BroadcastsService'] = new BroadcastsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Broadcast'),
                $this->mapperFactory->getBroadcastMapper(),
                $this->cacheItemPoolInterface
            );
        }

        return $this->instances['BroadcastsService'];
    }

    public function getCategoriesService(): CategoriesService
    {
        if (!isset($this->instances['CategoriesService'])) {
            $this->instances['CategoriesService'] = new CategoriesService(
                $this->entityManager->getRepository('ProgrammesPagesService:Category'),
                $this->mapperFactory->getCategoryMapper(),
                $this->cacheItemPoolInterface
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
                $this->cacheItemPoolInterface,
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
                $this->mapperFactory->getContributionMapper(),
                $this->cacheItemPoolInterface
            );
        }

        return $this->instances['ContributionsService'];
    }

    public function getContributorsService(): ContributorsService
    {
        if (!isset($this->instances['ContributorsService'])) {
            $this->instances['ContributorsService'] = new ContributorsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Contributor'),
                $this->mapperFactory->getContributorMapper(),
                $this->cacheItemPoolInterface
            );
        }

        return $this->instances['ContributorsService'];
    }

    public function getNetworksService(): NetworksService
    {
        if (!isset($this->instances['NetworksService'])) {
            $this->instances['NetworksService'] = new NetworksService(
                $this->entityManager->getRepository('ProgrammesPagesService:Network'),
                $this->mapperFactory->getNetworkMapper(),
                $this->cacheItemPoolInterface
            );
        }

        return $this->instances['NetworksService'];
    }

    public function getProgrammesService(): ProgrammesService
    {
        if (!isset($this->instances['ProgrammesService'])) {
            $this->instances['ProgrammesService'] = new ProgrammesService(
                $this->entityManager->getRepository('ProgrammesPagesService:CoreEntity'),
                $this->mapperFactory->getProgrammeMapper(),
                $this->cacheItemPoolInterface
            );
        }

        return $this->instances['ProgrammesService'];
    }

    public function getRelatedLinksService(): RelatedLinksService
    {
        if (!isset($this->instances['RelatedLinksService'])) {
            $this->instances['RelatedLinksService'] = new RelatedLinksService(
                $this->entityManager->getRepository('ProgrammesPagesService:RelatedLink'),
                $this->mapperFactory->getRelatedLinkMapper(),
                $this->cacheItemPoolInterface
            );
        }

        return $this->instances['RelatedLinksService'];
    }

    public function getSegmentEventsService(): SegmentEventsService
    {
        if (!isset($this->instances['SegmentEventsService'])) {
            $this->instances['SegmentEventsService'] = new SegmentEventsService(
                $this->entityManager->getRepository('ProgrammesPagesService:SegmentEvent'),
                $this->mapperFactory->getSegmentEventMapper(),
                $this->cacheItemPoolInterface
            );
        }

        return $this->instances['SegmentEventsService'];
    }

    public function getSegmentsService(): SegmentsService
    {
        if (!isset($this->instances['SegmentsService'])) {
            $this->instances['SegmentsService'] = new SegmentsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Segment'),
                $this->mapperFactory->getSegmentMapper(),
                $this->cacheItemPoolInterface
            );
        }

        return $this->instances['SegmentsService'];
    }

    public function getServicesService(): ServicesService
    {
        if (!isset($this->instances['ServicesService'])) {
            $this->instances['ServicesService'] = new ServicesService(
                $this->entityManager->getRepository('ProgrammesPagesService:Service'),
                $this->mapperFactory->getServiceMapper(),
                $this->cacheItemPoolInterface
            );
        }

        return $this->instances['ServicesService'];
    }

    public function getVersionsService(): VersionsService
    {
        if (!isset($this->instances['VersionsService'])) {
            $this->instances['VersionsService'] = new VersionsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Version'),
                $this->mapperFactory->getVersionMapper(),
                $this->cacheItemPoolInterface
            );
        }

        return $this->instances['VersionsService'];
    }
}
