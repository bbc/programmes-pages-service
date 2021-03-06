<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CoreEntitiesService;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CoreEntityMapper;
use BBC\ProgrammesPagesService\Service\CoreEntitiesService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractCoreEntitiesServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo(CoreEntityRepository::class);
        $this->setUpMapper(CoreEntityMapper::class, function ($dbCoreEntity) {
            return $this->createConfiguredMock(CoreEntity::class, ['getPid' => new Pid($dbCoreEntity['pid'])]);
        });
    }

    protected function service()
    {
        return new CoreEntitiesService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
