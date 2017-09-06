<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesAggregationService;

use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\Entity\Gallery;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\ProgrammesAggregationService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractProgrammesAggregationTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('CoreEntityRepository');
        $this->setUpMapper('CoreEntityMapper', function ($dbEntity) {
            if ($dbEntity['type'] === 'clip') {
                $class = Clip::class;
            }

            if ($dbEntity['type'] === 'gallery') {
                $class = Gallery::class;
            }

            return $this->createConfiguredMock($class, ['getPid' => new Pid($dbEntity['pid'])]);
        });
    }

    protected function service()
    {
        return new ProgrammesAggregationService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
