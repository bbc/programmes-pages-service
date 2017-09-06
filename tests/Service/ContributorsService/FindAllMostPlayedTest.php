<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributorsService;

use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use DateTimeImmutable;
use stdClass;

class FindAllMostPlayedTest extends AbstractContributorsServiceTest
{
    /**
     * @param $expectedDbId int|null
     *
     * @dataProvider serviceSelectorProvider
     */
    public function testProtocolWithDatabase($expectedDbId, array $service)
    {
        $from = new DateTimeImmutable();
        $to = new DateTimeImmutable();

        $this->mockRepository->expects($this->once())
            ->method('findAllMostPlayedWithPlays')
            ->with($from, $to, $expectedDbId);

        $this->service()->findAllMostPlayed($from, $to, ...$service);
    }

    public function serviceSelectorProvider(): array
    {
        return [
            'CASE: expectations when passing a service' => [
                999,
                [new Service(999, new Sid('sid'), new Pid('b0000001'), 'name')],
            ],
            'CASE: expectations when no passing a service' => [
                null,
                [],
            ],
        ];
    }

    public function testContributorsAreReceivedWithCountOfPlays()
    {
        $this->mockRepository
            ->method('findAllMostPlayedWithPlays')
            ->willReturn([
                    [0 => ['musicBrainzId' => '7746d775-9550-4360-b8d5-c37bd448ce01'], 'contributorPlayCount' => 10],
                    [0 => ['musicBrainzId' => '9c9f1380-2516-4fc9-a3e6-f9f61941d090'], 'contributorPlayCount' => 5],
            ]);

        $contributorsAndPlays = $this->service()->findAllMostPlayed(new DateTimeImmutable(), new DateTimeImmutable());

        // test structure
        $this->assertCount(2, $contributorsAndPlays);
        $this->assertContainsOnly(stdClass::class, $contributorsAndPlays);
        $this->assertObjectHasAttribute('contributor', $contributorsAndPlays[0]);
        $this->assertObjectHasAttribute('plays', $contributorsAndPlays[0]);
        $this->assertInstanceOf(Contributor::class, $contributorsAndPlays[0]->contributor);
        // test data content, order, ...
        $this->assertEquals('7746d775-9550-4360-b8d5-c37bd448ce01', $contributorsAndPlays[0]->contributor->getMusicBrainzId());
        $this->assertEquals('9c9f1380-2516-4fc9-a3e6-f9f61941d090', $contributorsAndPlays[1]->contributor->getMusicBrainzId());
    }

    public function testResultsAreAnEmptyArrayWhenNotFound()
    {
        $this->mockRepository->method('findAllMostPlayedWithPlays')->willReturn([]);

        $this->assertEquals(
            [],
            $this->service()->findAllMostPlayed(new DateTimeImmutable(), new DateTimeImmutable())
        );
    }
}
