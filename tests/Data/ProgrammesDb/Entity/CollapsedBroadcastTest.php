<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CollapsedBroadcast;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CollapsedBroadcastTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(CollapsedBroadcast::class);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\PartnerPidTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $episode = new Episode('p0000001', 'episode_title');
        $start = new DateTime('2017-01-03T00:00:00');
        $end = new DateTime('2017-01-03T02:00:00');

        $broadcast = new CollapsedBroadcast(
            $episode,
            '1,2,3',
            '4,5,6',
            '0,0,0',
            $start,
            $end
        );

        $this->assertSame(null, $broadcast->getId());

        $this->assertSame('1,2,3', $broadcast->getBroadcastIds());
        $this->assertSame('4,5,6', $broadcast->getServiceIds());
        $this->assertSame('0,0,0', $broadcast->getAreWebcasts());
        $this->assertSame($episode, $broadcast->getProgrammeItem());
        $this->assertSame($start, $broadcast->getStart());
        $this->assertSame($end, $broadcast->getEnd());
        $this->assertSame(7200, $broadcast->getDuration()); // 2 hours in seconds
        $this->assertSame(false, $broadcast->getIsBlanked());
        $this->assertSame(false, $broadcast->getIsRepeat());
        $this->assertSame(false, $broadcast->getIsWebcastOnly());
        $this->assertSame(null, $broadcast->getTleo());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue, $additionalComputedValues = [])
    {
        $broadcast = new CollapsedBroadcast(
            new Episode('p0000001', 'episode_title'),
            '1,2,3',
            '4,5,6',
            '0,0,0',
            new DateTime('2017-01-03T00:00:00'),
            new DateTime('2017-01-03T02:00:00')
        );

        $broadcast->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $broadcast->{'get' . $name}());

        foreach ($additionalComputedValues as $getterName => $expectedValue) {
            $this->assertEquals($expectedValue, $broadcast->{'get' . $getterName}());
        }
    }

    public function setterDataProvider()
    {
        return [
            ['BroadcastIds', '2,3,4'],
            ['ServiceIds', '5,6,7'],
            ['AreWebcasts', '1,0,1', ['IsWebcastOnly' => false]],
            ['ProgrammeItem', new Episode('p0000002', 'New Title')],
            ['Tleo',  new Episode('p0000003', 'New Title')],
            ['IsBlanked', true],
            ['IsRepeat', true],
            ['Start', new DateTime('2017-01-03T01:00:00'), ['Duration' => 3600]],
            ['End', new DateTime('2017-01-03T03:00:00'), ['Duration' => 10800]],

            // Test Webcast Only logic
            ['AreWebcasts', '0,0,0', ['IsWebcastOnly' => false]],
            ['AreWebcasts', '1,1,1', ['IsWebcastOnly' => true]],
        ];
    }
}
