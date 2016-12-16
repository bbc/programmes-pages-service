<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

class MapperFactory
{
    protected $instances = [];

    public function getAtozTitleMapper(): AtozTitleMapper
    {
        if (!isset($this->instances['AtozTitleMapper'])) {
            $this->instances['AtozTitleMapper'] = new AtozTitleMapper($this);
        }

        return $this->instances['AtozTitleMapper'];
    }

    public function getBroadcastMapper(): BroadcastMapper
    {
        if (!isset($this->instances['BroadcastMapper'])) {
            $this->instances['BroadcastMapper'] = new BroadcastMapper($this);
        }

        return $this->instances['BroadcastMapper'];
    }

    public function getCategoryMapper(): CategoryMapper
    {
        if (!isset($this->instances['CategoryMapper'])) {
            $this->instances['CategoryMapper'] = new CategoryMapper();
        }

        return $this->instances['CategoryMapper'];
    }

    public function getContributionMapper(): ContributionMapper
    {
        if (!isset($this->instances['ContributionMapper'])) {
            $this->instances['ContributionMapper'] = new ContributionMapper($this);
        }

        return $this->instances['ContributionMapper'];
    }

    public function getContributorMapper(): ContributorMapper
    {
        if (!isset($this->instances['ContributorMapper'])) {
            $this->instances['ContributorMapper'] = new ContributorMapper();
        }

        return $this->instances['ContributorMapper'];
    }

    public function getCollapsedBroadcastMapper(): CollapsedBroadcastMapper
    {
        if (!isset($this->instances['CollapsedBroadcastMapper'])) {
            $this->instances['CollapsedBroadcastMapper'] = new CollapsedBroadcastMapper($this);
        }

        return $this->instances['CollapsedBroadcastMapper'];
    }

    public function getImageMapper(): ImageMapper
    {
        if (!isset($this->instances['ImageMapper'])) {
            $this->instances['ImageMapper'] = new ImageMapper();
        }

        return $this->instances['ImageMapper'];
    }

    public function getMasterBrandMapper(): MasterBrandMapper
    {
        if (!isset($this->instances['MasterBrandMapper'])) {
            $this->instances['MasterBrandMapper'] = new MasterBrandMapper($this);
        }

        return $this->instances['MasterBrandMapper'];
    }

    public function getNetworkMapper(): NetworkMapper
    {
        if (!isset($this->instances['NetworkMapper'])) {
            $this->instances['NetworkMapper'] = new NetworkMapper($this);
        }

        return $this->instances['NetworkMapper'];
    }

    public function getProgrammeMapper(): ProgrammeMapper
    {
        if (!isset($this->instances['ProgrammeMapper'])) {
            $this->instances['ProgrammeMapper'] = new ProgrammeMapper($this);
        }

        return $this->instances['ProgrammeMapper'];
    }

    public function getRelatedLinkMapper(): RelatedLinkMapper
    {
        if (!isset($this->instances['RelatedLinkMapper'])) {
            $this->instances['RelatedLinkMapper'] = new RelatedLinkMapper();
        }

        return $this->instances['RelatedLinkMapper'];
    }

    public function getSegmentMapper(): SegmentMapper
    {
        if (!isset($this->instances['SegmentMapper'])) {
            $this->instances['SegmentMapper'] = new SegmentMapper($this);
        }

        return $this->instances['SegmentMapper'];
    }

    public function getSegmentEventMapper(): SegmentEventMapper
    {
        if (!isset($this->instances['SegmentEventMapper'])) {
            $this->instances['SegmentEventMapper'] = new SegmentEventMapper($this);
        }

        return $this->instances['SegmentEventMapper'];
    }

    public function getServiceMapper(): ServiceMapper
    {
        if (!isset($this->instances['ServiceMapper'])) {
            $this->instances['ServiceMapper'] = new ServiceMapper($this);
        }

        return $this->instances['ServiceMapper'];
    }

    public function getVersionMapper(): VersionMapper
    {
        if (!isset($this->instances['VersionMapper'])) {
            $this->instances['VersionMapper'] = new VersionMapper($this);
        }

        return $this->instances['VersionMapper'];
    }
}
