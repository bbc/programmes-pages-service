<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

class MapperFactory
{
    protected $instances = [];

    public function getCategoryMapper(): CategoryMapper
    {
        if (!array_key_exists('CategoryMapper', $this->instances)) {
            $this->instances['CategoryMapper'] = new CategoryMapper();
        }

        return $this->instances['CategoryMapper'];
    }

    public function getContributionMapper(): ContributionMapper
    {
        if (!array_key_exists('ContributionMapper', $this->instances)) {
            $this->instances['ContributionMapper'] = new ContributionMapper($this);
        }

        return $this->instances['ContributionMapper'];
    }

    public function getContributorMapper(): ContributorMapper
    {
        if (!array_key_exists('ContributorMapper', $this->instances)) {
            $this->instances['ContributorMapper'] = new ContributorMapper();
        }

        return $this->instances['ContributorMapper'];
    }

    public function getImageMapper(): ImageMapper
    {
        if (!array_key_exists('ImageMapper', $this->instances)) {
            $this->instances['ImageMapper'] = new ImageMapper();
        }

        return $this->instances['ImageMapper'];
    }

    public function getMasterBrandMapper(): MasterBrandMapper
    {
        if (!array_key_exists('MasterBrandMapper', $this->instances)) {
            $this->instances['MasterBrandMapper'] = new MasterBrandMapper($this);
        }

        return $this->instances['MasterBrandMapper'];
    }

    public function getNetworkMapper(): NetworkMapper
    {
        if (!array_key_exists('NetworkMapper', $this->instances)) {
            $this->instances['NetworkMapper'] = new NetworkMapper($this);
        }

        return $this->instances['NetworkMapper'];
    }

    public function getProgrammeMapper(): ProgrammeMapper
    {
        if (!array_key_exists('ProgrammeMapper', $this->instances)) {
            $this->instances['ProgrammeMapper'] = new ProgrammeMapper($this);
        }

        return $this->instances['ProgrammeMapper'];
    }

    public function getRelatedLinkMapper(): RelatedLinkMapper
    {
        if (!array_key_exists('RelatedLinkMapper', $this->instances)) {
            $this->instances['RelatedLinkMapper'] = new RelatedLinkMapper();
        }

        return $this->instances['RelatedLinkMapper'];
    }

    public function getSegmentMapper(): SegmentMapper
    {
        if (!array_key_exists('SegmentMapper', $this->instances)) {
            $this->instances['SegmentMapper'] = new SegmentMapper();
        }

        return $this->instances['SegmentMapper'];
    }

    public function getSegmentEventMapper(): SegmentEventMapper
    {
        if (!array_key_exists('SegmentEventMapper', $this->instances)) {
            $this->instances['SegmentEventMapper'] = new SegmentEventMapper($this);
        }

        return $this->instances['SegmentEventMapper'];
    }

    public function getServiceMapper(): ServiceMapper
    {
        if (!array_key_exists('ServiceMapper', $this->instances)) {
            $this->instances['ServiceMapper'] = new ServiceMapper($this);
        }

        return $this->instances['ServiceMapper'];
    }

    public function getVersionMapper(): VersionMapper
    {
        if (!array_key_exists('VersionMapper', $this->instances)) {
            $this->instances['VersionMapper'] = new VersionMapper($this);
        }

        return $this->instances['VersionMapper'];
    }

    public function getBroadcastMapper(): BroadcastMapper
    {
        if (!array_key_exists('BroadcastMapper', $this->instances)) {
            $this->instances['BroadcastMapper'] = new BroadcastMapper($this);
        }

        return $this->instances['BroadcastMapper'];
    }
}
