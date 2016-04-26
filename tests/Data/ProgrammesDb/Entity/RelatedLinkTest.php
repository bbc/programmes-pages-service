<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RelatedLink;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Clip;
use PHPUnit_Framework_TestCase;

class RelatedLinkTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $coreEntity = new Clip('pid', 'title');
        $link = new RelatedLink($coreEntity, 'pid', 'title', 'uri', 'type', 1, false);
        $this->assertSame($coreEntity, $link->getRelatedTo());
        $this->assertSame(null, $link->getId());
        $this->assertSame('pid', $link->getPid());
        $this->assertSame('title', $link->getTitle());
        $this->assertSame('uri', $link->getUri());
        $this->assertSame('type', $link->getType());
        $this->assertSame(1, $link->getPosition());
        $this->assertSame(false, $link->isExternal());
    }

    public function testSetters()
    {
        $coreEntity = new Clip('pid', 'title');
        $link = new RelatedLink($coreEntity, '', '', '', '', 1, false);

        $link->setPid('pid');
        $link->setTitle('title');
        $link->setUri('uri');
        $link->setType('type');
        $link->setPosition(2);
        $link->setExternal(false);

        $this->assertSame($coreEntity, $link->getRelatedTo());
        $this->assertSame(null, $link->getId());
        $this->assertSame('pid', $link->getPid());
        $this->assertSame('title', $link->getTitle());
        $this->assertSame('uri', $link->getUri());
        $this->assertSame('type', $link->getType());
        $this->assertSame(2, $link->getPosition());
        $this->assertSame(false, $link->isExternal());
    }
}
