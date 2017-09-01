<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\PromotableInterface;
use BBC\ProgrammesPagesService\Domain\Entity\Promotion;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use PHPUnit\Framework\TestCase;

class PromotionTest extends TestCase
{
    public function testConstructorPromoRequiredArgsWorksForImage()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('short synopsis', 'medium', 'long');
        $promo = new Promotion(
            $pid,
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Image'),
            'this is a title',
            $synopses,
            'www.something.url',
            3,
            false
        );

        $this->assertInstanceOf(PromotableInterface::class, $promo->getPromotedEntity());
        $this->assertSame($pid, $promo->getPid());
        $this->assertSame($synopses, $promo->getSynopses());
        $this->assertSame('short synopsis', $promo->getShortSynopsis());
        $this->assertSame('this is a title', $promo->getTitle());
        $this->assertSame('www.something.url', $promo->getUrl());
        $this->assertSame(3, $promo->getWeighting());
        $this->assertFalse($promo->isSuperPromotion());
    }

    public function testConstructorPromoRequiredArgsWorksForCoreEntity()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('short synopsis', 'medium', 'long');
        $promo = new Promotion(
            $pid,
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Series'),
            'this is a title',
            $synopses,
            'www.something.url',
            3,
            false
        );

        $this->assertInstanceOf(PromotableInterface::class, $promo->getPromotedEntity());
        $this->assertSame($pid, $promo->getPid());
        $this->assertSame($synopses, $promo->getSynopses());
        $this->assertSame('short synopsis', $promo->getShortSynopsis());
        $this->assertSame('this is a title', $promo->getTitle());
        $this->assertSame('www.something.url', $promo->getUrl());
        $this->assertSame(3, $promo->getWeighting());
        $this->assertFalse($promo->isSuperPromotion());
    }
}
