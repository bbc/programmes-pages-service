<?php

namespace Tests\BBC\ProgrammesPagesService\Service\GroupsService;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Group;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CoreEntityMapper;
use BBC\ProgrammesPagesService\Service\GroupsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractGroupsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo(CoreEntityRepository::class);
        $this->setUpMapper(CoreEntityMapper::class, function ($dbCoreEntity) {
            return $this->createConfiguredMock(Group::class, ['getPid' => new Pid($dbCoreEntity['pid'])]);
        });
    }

    protected function service()
    {
        return new GroupsService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }

    protected function setUpMapper($mapperName, $callback)
    {
        $this->mockMapper = $this->createMock($mapperName);
        $this->mockMapper->method('getDomainModelForGroup')->will($this->returnCallback($callback));
    }
}
