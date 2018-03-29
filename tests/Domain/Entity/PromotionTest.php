<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\PromotableInterface;
use BBC\ProgrammesPagesService\Domain\Entity\Promotion;
use BBC\ProgrammesPagesService\Domain\Entity\RelatedLink;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PromotionTest extends TestCase
{
    public function testConstructorPromoRequiredArgsWorksForImage()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('short synopsis', 'medium', 'long');
        $promo = new Promotion(
            $pid,
            $this->createMock(Image::class),
            'this is a title',
            $synopses,
            'www.something.url',
            3,
            false,
            [$this->createMock(RelatedLink::class)]
        );

        $this->assertInstanceOf(PromotableInterface::class, $promo->getPromotedEntity());
        $this->assertSame($pid, $promo->getPid());
        $this->assertSame($synopses, $promo->getSynopses());
        $this->assertSame('short synopsis', $promo->getShortSynopsis());
        $this->assertSame('this is a title', $promo->getTitle());
        $this->assertSame('www.something.url', $promo->getUrl());
        $this->assertSame(3, $promo->getWeighting());
        $this->assertFalse($promo->isSuperPromotion());
        $this->assertContainsOnlyInstancesOf(RelatedLink::class, $promo->getRelatedLinks());
    }

    public function testConstructorPromoRequiredArgsWorksForCoreEntity()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('short synopsis', 'medium', 'long');
        $promo = new Promotion(
            $pid,
            $this->createMock(Series::class),
            'this is a title',
            $synopses,
            'www.something.url',
            3,
            false,
            [$this->createMock(RelatedLink::class)]
        );

        $this->assertInstanceOf(PromotableInterface::class, $promo->getPromotedEntity());
        $this->assertSame($pid, $promo->getPid());
        $this->assertSame($synopses, $promo->getSynopses());
        $this->assertSame('short synopsis', $promo->getShortSynopsis());
        $this->assertSame('this is a title', $promo->getTitle());
        $this->assertSame('www.something.url', $promo->getUrl());
        $this->assertSame(3, $promo->getWeighting());
        $this->assertFalse($promo->isSuperPromotion());
        $this->assertContainsOnlyInstancesOf(RelatedLink::class, $promo->getRelatedLinks());
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get Related Links of Promotion "p01m5mss" as they were not fetched
     */
    public function testUnfetchedRelatedLinks()
    {
        $promo = new Promotion(
            new Pid('p01m5mss'),
            $this->createMock(Series::class),
            'this is a title',
            new Synopses('short synopsis', 'medium', 'long'),
            'www.something.url',
            3,
            false,
            null
        );

        $promo->getRelatedLinks();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Tried to create a Promotion with invalid related links. Expected an array of BBC\ProgrammesPagesService\Domain\Entity\RelatedLink but the array contained an instance of "string"
     */
    public function testInvalidRelatedLinks()
    {
        new Promotion(
            new Pid('p01m5mss'),
            $this->createMock(Series::class),
            'this is a title',
            new Synopses('short synopsis', 'medium', 'long'),
            'www.something.url',
            3,
            false,
            ['wrongwrongwrong']
        );
    }
}
