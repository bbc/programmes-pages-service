<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RelatedLink;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Clip;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class RelatedLinkTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(RelatedLink::CLASS);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\PartnerPidTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\SynopsesTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $coreEntity = $this->mockCoreEntity();
        $promotion = $this->mockPromotion();
        $image = $this->mockImage();

        $link = new RelatedLink('pid', 'title', 'uri', 'type', $coreEntity, false);
        $this->assertSame(null, $link->getId());
        $this->assertSame('pid', $link->getPid());
        $this->assertSame('title', $link->getTitle());
        $this->assertSame('uri', $link->getUri());
        $this->assertSame('type', $link->getType());
        $this->assertSame($coreEntity, $link->getRelatedTo());
        $this->assertSame($coreEntity, $link->getRelatedToCoreEntity());
        $this->assertSame(null, $link->getRelatedToPromotion());
        $this->assertSame(null, $link->getRelatedToImage());
        $this->assertSame(false, $link->getIsExternal());
        $this->assertSame(null, $link->getPosition());
        $this->assertSame(null, $link->getStartDate());
        $this->assertSame(null, $link->getEndDate());

        $link = new RelatedLink('pid', 'title', 'uri', 'type', $promotion, false);
        $this->assertSame($promotion, $link->getRelatedTo());
        $this->assertSame(null, $link->getRelatedToCoreEntity());
        $this->assertSame($promotion, $link->getRelatedToPromotion());
        $this->assertSame(null, $link->getRelatedToImage());

        $link = new RelatedLink('pid', 'title', 'uri', 'type', $image, false);
        $this->assertSame($image, $link->getRelatedTo());
        $this->assertSame(null, $link->getRelatedToCoreEntity());
        $this->assertSame(null, $link->getRelatedToPromotion());
        $this->assertSame($image, $link->getRelatedToImage());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $coreEntity = $this->mockCoreEntity();
        $link = new RelatedLink('pid', '', '', '', $coreEntity, false);

        $link->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $link->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Pid', 'a-string'],
            ['Title', 'a-string'],
            ['Uri', 'a-string'],
            ['Type', 'a-string'],
            ['Position', 2],
            ['IsExternal', true],
            ['StartDate', new \DateTime('2016-01-01 00:00:00')],
            ['EndDate', new \DateTime('2017-01-01 00:00:00')],
        ];
    }

    /**
     * @dataProvider setRelatedToDataProvider
     */
    public function testSetRelatedTo($relatedTo, $expectedCoreEntity, $expectedImage, $expectedPromotion)
    {
        $coreEntity = $this->mockCoreEntity();
        $promotion = $this->mockPromotion();
        $image = $this->mockImage();

        $link = new RelatedLink('pid', '', '', '', $coreEntity, false);
        $link->setRelatedTo($relatedTo);

        $this->assertSame($relatedTo, $link->getRelatedTo());
        $this->assertSame($expectedCoreEntity, $link->getRelatedToCoreEntity());
        $this->assertSame($expectedImage, $link->getRelatedToImage());
        $this->assertSame($expectedPromotion, $link->getRelatedToPromotion());
    }

    public function setRelatedToDataProvider()
    {
        $coreEntity = $this->mockCoreEntity();
        $promotion = $this->mockPromotion();
        $image = $this->mockImage();

        return [
            [$coreEntity, $coreEntity, null, null],
            [$promotion, null, null, $promotion],
            [$image, null, $image, null],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidRelatedToThrowsExceptionOnConstruct()
    {
        new RelatedLink('pid', '', '', '', 'wrongwrongwrong', false);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidRelatedToThrowsExceptionOnSet()
    {
        $link = new RelatedLink('pid', '', '', '', $this->mockCoreEntity(), false);
        $link->setRelatedTo('wrongwrongwrong');
    }

    private function mockCoreEntity()
    {
        return $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity');
    }

    private function mockPromotion()
    {
        return $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Promotion');
    }

    private function mockImage()
    {
        return $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image');
    }
}
