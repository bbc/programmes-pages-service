<?php

namespace BBC\ProgrammesPagesService\Service;

use Doctrine\ORM\EntityManager;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperProvider;

class ServiceFactory
{
    protected $instances = [];

    protected $entityManager;

    protected $mapperProvider;

    public function __construct(
        EntityManager $entityManager,
        MapperProvider $mapperProvider
    ) {
        $this->entityManager = $entityManager;
        $this->mapperProvider = $mapperProvider;
    }

    public function getProgrammesService(): ProgrammesService
    {
        if (!array_key_exists('ProgrammesService', $this->instances)) {
            $this->instances['ProgrammesService'] = new ProgrammesService(
                $this->entityManager->getRepository('ProgrammesPagesService:CoreEntity'),
                $this->mapperProvider->getProgrammeMapper()
            );
        }

        return $this->instances['ProgrammesService'];
    }

    public function getRelatedLinksService(): RelatedLinksService
    {
        if (!array_key_exists('RelatedLinksService', $this->instances)) {
            $this->instances['RelatedLinksService'] = new RelatedLinksService(
                $this->entityManager->getRepository('ProgrammesPagesService:RelatedLink'),
                $this->mapperProvider->getRelatedLinkMapper()
            );
        }

        return $this->instances['RelatedLinksService'];
    }
}
