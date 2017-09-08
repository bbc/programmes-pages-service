<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CoreEntityMapper;
use BBC\ProgrammesPagesService\Service\ProgrammesService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractProgrammesServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('CoreEntityRepository');
        $this->setUpMapper(CoreEntityMapper::class, function ($dbEntity) {
            return $this->createConfiguredMock(Programme::class, ['getPid' => new Pid($dbEntity['pid'])]);
        });
    }

    protected function service()
    {
        return new ProgrammesService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
