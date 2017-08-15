<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Cache\CacheInterface;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperFactory;
use Doctrine\ORM\EntityManagerInterface;

class ServiceFactory
{
    protected $instances = [];

    protected $entityManager;

    protected $mapperFactory;
    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(
        EntityManagerInterface $entityManager,
        MapperFactory $mapperFactory,
        CacheInterface $cache
    ) {
        $this->entityManager = $entityManager;
        $this->mapperFactory = $mapperFactory;
        $this->cache = $cache;
    }

    public function getAtozTitlesService(): AtozTitlesService
    {
        if (!isset($this->instances[AtozTitlesService::class])) {
            $this->instances[AtozTitlesService::class] = new AtozTitlesService(
                $this->entityManager->getRepository('ProgrammesPagesService:AtozTitle'),
                $this->mapperFactory->getAtozTitleMapper(),
                $this->cache
            );
        }

        return $this->instances[AtozTitlesService::class];
    }

    public function getBroadcastsService(): BroadcastsService
    {
        if (!isset($this->instances[BroadcastsService::class])) {
            $this->instances[BroadcastsService::class] = new BroadcastsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Broadcast'),
                $this->mapperFactory->getBroadcastMapper(),
                $this->cache
            );
        }

        return $this->instances[BroadcastsService::class];
    }

    public function getCategoriesService(): CategoriesService
    {
        if (!isset($this->instances[CategoriesService::class])) {
            $this->instances[CategoriesService::class] = new CategoriesService(
                $this->entityManager->getRepository('ProgrammesPagesService:Category'),
                $this->mapperFactory->getCategoryMapper(),
                $this->cache
            );
        }

        return $this->instances[CategoriesService::class];
    }

    public function getCollapsedBroadcastsService(): CollapsedBroadcastsService
    {
        if (!isset($this->instances[CollapsedBroadcastsService::class])) {
            $this->instances[CollapsedBroadcastsService::class] = new CollapsedBroadcastsService(
                $this->entityManager->getRepository('ProgrammesPagesService:CollapsedBroadcast'),
                $this->mapperFactory->getCollapsedBroadcastMapper(),
                $this->cache,
                $this->entityManager->getRepository('ProgrammesPagesService:Service')
            );
        }

        return $this->instances[CollapsedBroadcastsService::class];
    }

    public function getContributionsService(): ContributionsService
    {
        if (!isset($this->instances[ContributionsService::class])) {
            $this->instances[ContributionsService::class] = new ContributionsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Contribution'),
                $this->mapperFactory->getContributionMapper(),
                $this->cache
            );
        }

        return $this->instances[ContributionsService::class];
    }

    public function getContributorsService(): ContributorsService
    {
        if (!isset($this->instances[ContributorsService::class])) {
            $this->instances[ContributorsService::class] = new ContributorsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Contributor'),
                $this->mapperFactory->getContributorMapper(),
                $this->cache
            );
        }

        return $this->instances[ContributorsService::class];
    }

    public function getCoreEntitiesService(): CoreEntitiesService
    {
        if (!isset($this->instances[CoreEntitiesService::class])) {
            $this->instances[CoreEntitiesService::class] = new CoreEntitiesService(
                $this->entityManager->getRepository('ProgrammesPagesService:CoreEntity'),
                $this->mapperFactory->getCoreEntityMapper(),
                $this->cache
            );
        }

        return $this->instances[CoreEntitiesService::class];
    }

    public function getGroupsService(): GroupsService
    {
        if (!isset($this->instances[GroupsService::class])) {
            $this->instances[GroupsService::class] = new GroupsService(
                $this->entityManager->getRepository('ProgrammesPagesService:CoreEntity'),
                $this->mapperFactory->getCoreEntityMapper(),
                $this->cache
            );
        }

        return $this->instances[GroupsService::class];
    }

    public function getNetworksService(): NetworksService
    {
        if (!isset($this->instances[NetworksService::class])) {
            $this->instances[NetworksService::class] = new NetworksService(
                $this->entityManager->getRepository('ProgrammesPagesService:Network'),
                $this->mapperFactory->getNetworkMapper(),
                $this->cache
            );
        }

        return $this->instances[NetworksService::class];
    }

    public function getProgrammesAggregationService(): ProgrammesAggregationService
    {
        if (!isset($this->instances[ProgrammesAggregationService::class])) {
            $this->instances[ProgrammesAggregationService::class] = new ProgrammesAggregationService(
                $this->entityManager->getRepository('ProgrammesPagesService:CoreEntity'),
                $this->mapperFactory->getCoreEntityMapper(),
                $this->cache
            );
        }

        return $this->instances[ProgrammesAggregationService::class];
    }

    public function getProgrammesService(): ProgrammesService
    {
        if (!isset($this->instances[ProgrammesService::class])) {
            $this->instances[ProgrammesService::class] = new ProgrammesService(
                $this->entityManager->getRepository('ProgrammesPagesService:CoreEntity'),
                $this->mapperFactory->getCoreEntityMapper(),
                $this->cache
            );
        }

        return $this->instances[ProgrammesService::class];
    }

    public function getPromotionsService(): PromotionsService
    {
        if (!isset($this->instances[PromotionsService::class])) {
            $this->instances[PromotionsService::class] = new PromotionsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Promotion'),
                $this->mapperFactory->getPromotionMapper(),
                $this->cache
            );
        }

        return $this->instances[PromotionsService::class];
    }

    public function getRelatedLinksService(): RelatedLinksService
    {
        if (!isset($this->instances[RelatedLinksService::class])) {
            $this->instances[RelatedLinksService::class] = new RelatedLinksService(
                $this->entityManager->getRepository('ProgrammesPagesService:RelatedLink'),
                $this->mapperFactory->getRelatedLinkMapper(),
                $this->cache
            );
        }

        return $this->instances[RelatedLinksService::class];
    }

    public function getSegmentEventsService(): SegmentEventsService
    {
        if (!isset($this->instances[SegmentEventsService::class])) {
            $this->instances[SegmentEventsService::class] = new SegmentEventsService(
                $this->entityManager->getRepository('ProgrammesPagesService:SegmentEvent'),
                $this->mapperFactory->getSegmentEventMapper(),
                $this->cache
            );
        }

        return $this->instances[SegmentEventsService::class];
    }

    public function getSegmentsService(): SegmentsService
    {
        if (!isset($this->instances[SegmentsService::class])) {
            $this->instances[SegmentsService::class] = new SegmentsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Segment'),
                $this->mapperFactory->getSegmentMapper(),
                $this->cache
            );
        }

        return $this->instances[SegmentsService::class];
    }

    public function getServicesService(): ServicesService
    {
        if (!isset($this->instances[ServicesService::class])) {
            $this->instances[ServicesService::class] = new ServicesService(
                $this->entityManager->getRepository('ProgrammesPagesService:Service'),
                $this->mapperFactory->getServiceMapper(),
                $this->cache
            );
        }

        return $this->instances[ServicesService::class];
    }

    public function getVersionsService(): VersionsService
    {
        if (!isset($this->instances[VersionsService::class])) {
            $this->instances[VersionsService::class] = new VersionsService(
                $this->entityManager->getRepository('ProgrammesPagesService:Version'),
                $this->mapperFactory->getVersionMapper(),
                $this->cache
            );
        }

        return $this->instances[VersionsService::class];
    }
}
