<?php

namespace Tests\BBC\ProgrammesPagesService\Service\RelatedLinksService;

use BBC\ProgrammesPagesService\Service\RelatedLinksService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractRelatedLinksServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('RelatedLinkRepository');
        $this->setUpMapper('RelatedLinkMapper', 'relatedLinkFromDbData');
    }

    protected function relatedLinksFromDbData(array $entities)
    {
        return array_map([$this, 'relatedLinkFromDbData'], $entities);
    }

    protected function relatedLinkFromDbData(array $entity)
    {
        $mockRelatedLink = $this->createMock(self::ENTITY_NS . 'RelatedLink');

        $mockRelatedLink->method('getTitle')->willReturn($entity['title']);
        return $mockRelatedLink;
    }

    protected function service()
    {
        return new RelatedLinksService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
