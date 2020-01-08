<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

class MapperFactory
{
    /** @var AbstractMapper[] */
    protected $instances = [];

    /** @var mixed[] */
    private $options = [
        // Most systems will want to look up the parent hierarchy to see if
        // there is a MasterBrand we can attach to the current item (i.e. use
        // the MasterBrand of the parent if the child has no MasterBrand).
        // However some legacy APIs we still need to maintain (e.g. Clifton) do
        // not expose the inherited MasterBrand, thus we need the ability to
        // switch between these two behaviors.
        'core_entity_inherit_master_brand' => true,
    ];

    /**
     * @param mixed[] $options An array of options for configuring mapper
     *                         behavior. See the $options property for valid
     *                         key names.
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    public function getOption(string $key)
    {
        return $this->options[$key];
    }

    public function getAtozTitleMapper(): AtozTitleMapper
    {
        if (!isset($this->instances[AtozTitleMapper::class])) {
            $this->instances[AtozTitleMapper::class] = new AtozTitleMapper($this);
        }

        return $this->instances[AtozTitleMapper::class];
    }

    public function getBroadcastMapper(): BroadcastMapper
    {
        if (!isset($this->instances[BroadcastMapper::class])) {
            $this->instances[BroadcastMapper::class] = new BroadcastMapper($this);
        }

        return $this->instances[BroadcastMapper::class];
    }

    public function getCategoryMapper(): CategoryMapper
    {
        if (!isset($this->instances[CategoryMapper::class])) {
            $this->instances[CategoryMapper::class] = new CategoryMapper($this);
        }

        return $this->instances[CategoryMapper::class];
    }

    public function getContributionMapper(): ContributionMapper
    {
        if (!isset($this->instances[ContributionMapper::class])) {
            $this->instances[ContributionMapper::class] = new ContributionMapper($this);
        }

        return $this->instances[ContributionMapper::class];
    }

    public function getContributorMapper(): ContributorMapper
    {
        if (!isset($this->instances[ContributorMapper::class])) {
            $this->instances[ContributorMapper::class] = new ContributorMapper($this);
        }

        return $this->instances[ContributorMapper::class];
    }

    public function getCollapsedBroadcastMapper(): CollapsedBroadcastMapper
    {
        if (!isset($this->instances[CollapsedBroadcastMapper::class])) {
            $this->instances[CollapsedBroadcastMapper::class] = new CollapsedBroadcastMapper($this);
        }

        return $this->instances[CollapsedBroadcastMapper::class];
    }

    public function getCoreEntityMapper(): CoreEntityMapper
    {
        if (!isset($this->instances[CoreEntityMapper::class])) {
            $this->instances[CoreEntityMapper::class] = new CoreEntityMapper($this);
        }

        return $this->instances[CoreEntityMapper::class];
    }

    public function getImageMapper(): ImageMapper
    {
        if (!isset($this->instances[ImageMapper::class])) {
            $this->instances[ImageMapper::class] = new ImageMapper($this);
        }

        return $this->instances[ImageMapper::class];
    }

    public function getMasterBrandMapper(): MasterBrandMapper
    {
        if (!isset($this->instances[MasterBrandMapper::class])) {
            $this->instances[MasterBrandMapper::class] = new MasterBrandMapper($this);
        }

        return $this->instances[MasterBrandMapper::class];
    }

    public function getNetworkMapper(): NetworkMapper
    {
        if (!isset($this->instances[NetworkMapper::class])) {
            $this->instances[NetworkMapper::class] = new NetworkMapper($this);
        }

        return $this->instances[NetworkMapper::class];
    }

    public function getOptionsMapper(): OptionsMapper
    {
        if (!isset($this->instances[OptionsMapper::class])) {
            $this->instances[OptionsMapper::class] = new OptionsMapper($this);
        }

        return $this->instances[OptionsMapper::class];
    }

    public function getPodcastMapper(): PodcastMapper
    {
        if (!isset($this->instances[PodcastMapper::class])) {
            $this->instances[PodcastMapper::class] = new PodcastMapper($this);
        }

        return $this->instances[PodcastMapper::class];
    }

    public function getPromotionMapper(): PromotionMapper
    {
        if (!isset($this->instances[PromotionMapper::class])) {
            $this->instances[PromotionMapper::class] = new PromotionMapper($this);
        }

        return $this->instances[PromotionMapper::class];
    }

    public function getRelatedLinkMapper(): RelatedLinkMapper
    {
        if (!isset($this->instances[RelatedLinkMapper::class])) {
            $this->instances[RelatedLinkMapper::class] = new RelatedLinkMapper($this);
        }

        return $this->instances[RelatedLinkMapper::class];
    }

    public function getSegmentMapper(): SegmentMapper
    {
        if (!isset($this->instances[SegmentMapper::class])) {
            $this->instances[SegmentMapper::class] = new SegmentMapper($this);
        }

        return $this->instances[SegmentMapper::class];
    }

    public function getSegmentEventMapper(): SegmentEventMapper
    {
        if (!isset($this->instances[SegmentEventMapper::class])) {
            $this->instances[SegmentEventMapper::class] = new SegmentEventMapper($this);
        }

        return $this->instances[SegmentEventMapper::class];
    }

    public function getServiceMapper(): ServiceMapper
    {
        if (!isset($this->instances[ServiceMapper::class])) {
            $this->instances[ServiceMapper::class] = new ServiceMapper($this);
        }

        return $this->instances[ServiceMapper::class];
    }

    public function getThingMapper(): ThingMapper
    {
        if (!isset($this->instances[ThingMapper::class])) {
            $this->instances[ThingMapper::class] = new ThingMapper($this);
        }

        return $this->instances[ThingMapper::class];
    }

    public function getVersionMapper(): VersionMapper
    {
        if (!isset($this->instances[VersionMapper::class])) {
            $this->instances[VersionMapper::class] = new VersionMapper($this);
        }

        return $this->instances[VersionMapper::class];
    }

    public function getVersionTypeMapper(): VersionTypeMapper
    {
        if (!isset($this->instances[VersionTypeMapper::class])) {
            $this->instances[VersionTypeMapper::class] = new VersionTypeMapper($this);
        }

        return $this->instances[VersionTypeMapper::class];
    }
}
