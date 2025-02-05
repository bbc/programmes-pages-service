<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\AncestryRepository;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\AncestryMapper;


class AncestryService extends AbstractService
{
    /** @var AncestryMapper */
    protected $mapper;

    /** @var AncestryRepository */
    protected $repository;

    public function __construct(
        AncestryRepository $repository,
        AncestryMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }
}
