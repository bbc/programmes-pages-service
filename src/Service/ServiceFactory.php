<?php

namespace BBC\ProgrammesPagesService\Service;

use Doctrine\ORM\EntityManager;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperProvider;

class ServiceFactory
{
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
        return new ProgrammesService(
            $this->entityManager->getRepository('ProgrammesPagesService:CoreEntity'),
            $this->mapperProvider->getProgrammeMapper()
        );
    }
}
