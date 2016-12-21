<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Promotion;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use DateTime;

class PromotionTest extends PHPUnit_Framework_TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Promotion::CLASS);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\PartnerPidTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\SynopsesTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $coreEntity = $this->mockCoreEntity();
        $image = $this->mockImage();
        $startDate = new DateTime();
        $endDate = new DateTime();

        $promotion = new Promotion('pid', $coreEntity, $startDate, $endDate, 1);
        $this->assertSame(null, $promotion->getId());
        $this->assertSame('pid', $promotion->getPid());
        $this->assertSame($coreEntity, $promotion->getPromotionOf());
        $this->assertSame($coreEntity, $promotion->getPromotionOfCoreEntity());
        $this->assertSame(null, $promotion->getPromotionOfImage());
        $this->assertSame($startDate, $promotion->getStartDate());
        $this->assertSame($endDate, $promotion->getEndDate());
        $this->assertSame(1, $promotion->getWeighting());
        $this->assertSame(false, $promotion->getIsActive());
        $this->assertSame(null, $promotion->getContext());
        $this->assertSame(null, $promotion->getPromotedFor());
        $this->assertSame('', $promotion->getTitle());
        $this->assertSame('', $promotion->getUri());
        $this->assertSame(false, $promotion->getCascadesToDescendants());

        $promotion = new Promotion('pid', $image, $startDate, $endDate, 1);
        $this->assertSame($image, $promotion->getPromotionOf());
        $this->assertSame(null, $promotion->getPromotionOfCoreEntity());
        $this->assertSame($image, $promotion->getPromotionOfImage());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $coreEntity = $this->mockCoreEntity();
        $startDate = new DateTime();
        $endDate = new DateTime();

        $promotion = new Promotion('pid', $coreEntity, $startDate, $endDate, 1);

        $promotion->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $promotion->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Pid', 'pid'],
            ['StartDate', new DateTime()],
            ['EndDate', new DateTime()],
            ['IsActive', true],
            ['Weighting', 20],
            ['Context', $this->mockCoreEntity()],
            ['PromotedFor', 'a-string'],
            ['Title', 'a-string'],
            ['Uri', 'a-string'],
            ['CascadesToDescendants', true],
        ];
    }

    /**
     * @dataProvider setPromotionOfDataProvider
     */
    public function testSetPromotionOf($promotedItem, $expectedCoreEntity, $expectedImage)
    {
        $promotion = new Promotion(
            'pid',
            $this->mockCoreEntity(),
            new DateTime(),
            new DateTime(),
            1
        );
        $promotion->setPromotionOf($promotedItem);

        $this->assertSame($promotedItem, $promotion->getPromotionOf());
        $this->assertSame($expectedCoreEntity, $promotion->getPromotionOfCoreEntity());
        $this->assertSame($expectedImage, $promotion->getPromotionOfImage());
    }

    public function setPromotionOfDataProvider()
    {
        $coreEntity = $this->mockCoreEntity();
        $image = $this->mockImage();

        return [
            [$coreEntity, $coreEntity, null],
            [$image, null, $image],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidPromotedItemThrowsExceptionOnConstruct()
    {
        new Promotion(
            'pid',
            'wrongwrongwrong',
            new DateTime(),
            new DateTime(),
            1
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidPromotedItemThrowsExceptionOnSet()
    {
        $promotion = new Promotion(
            'pid',
            $this->mockCoreEntity(),
            new DateTime(),
            new DateTime(),
            1
        );
        $promotion->setPromotionOf('wrongwrongwrong');
    }

    private function mockCoreEntity()
    {
        return $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity');
    }

    private function mockImage()
    {
        return $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image');
    }
}
