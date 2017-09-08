<?php

namespace Tests\BBC\ProgrammesPagesService\Service\RelatedLinksService;

use BBC\ProgrammesPagesService\Domain\Entity\RelatedLink;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\RelatedLinkMapper;
use BBC\ProgrammesPagesService\Service\RelatedLinksService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractRelatedLinksServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('RelatedLinkRepository');
        $this->setUpMapper(RelatedLinkMapper::class, function ($dbRelatedLink) {
            return $this->createConfiguredMock(RelatedLink::class, ['getTitle' => $dbRelatedLink['title']]);
        });
    }

    protected function service()
    {
        return new RelatedLinksService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
