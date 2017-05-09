<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\ProgrammesService;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractProgrammesServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('CoreEntityRepository');
        $this->setUpMapper('ProgrammeMapper', 'programmeFromDbData');
    }

    protected function programmesFromDbData(array $entities)
    {
        return array_map([$this, 'programmeFromDbData'], $entities);
    }

    protected function programmeFromDbData(array $entity)
    {
        $mockProgramme = $this->createMock(self::ENTITY_NS . 'Programme');

        $mockProgramme->method('getPid')->willReturn(new Pid($entity['pid']));
        return $mockProgramme;
    }

    protected function service()
    {
        return new ProgrammesService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
