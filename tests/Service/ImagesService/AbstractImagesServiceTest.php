<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ImagesService;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ImageRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ImageMapper;
use BBC\ProgrammesPagesService\Service\ImagesService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractImagesServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo(ImageRepository::class);
        $this->setUpMapper(ImageMapper::class, function (array $dbImage) {
            return $this->createConfiguredMock(Image::class, ['getPid' => new Pid($dbImage['pid'])]);
        });
    }

    protected function service()
    {
        return new ImagesService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
