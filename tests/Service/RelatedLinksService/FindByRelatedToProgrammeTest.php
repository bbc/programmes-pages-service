<?php

namespace Tests\BBC\ProgrammesPagesService\Service\RelatedLinksService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\RelatedLink;

class FindByRelatedToProgrammeTest extends AbstractRelatedLinksServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testCommunicationProtocolWithDb(int $expectedLimit, int $expectedOffset, array $paramsPagination)
    {
        $programme = $this->createConfiguredMock(Programme::class, ['getDbId' => 101]);

        $this->mockRepository->expects($this->once())
            ->method('findByRelatedTo')
            ->with([$programme->getDbId()], 'programme', $expectedLimit, $expectedOffset);

        $this->service()->findByRelatedToProgramme($programme, ...$paramsPagination);
    }

    public function paginationProvider(): array
    {
        return [
            // [expectedLimit, expectedOffset, [limit, page]]
            'default pagination' => [300, 0, []],
            'custom pagination' => [3, 12, [3, 5]],
        ];
    }

    /**
     * @dataProvider dbRelatedlinksProvider
     */
    public function testFindByRelatedToProgrammeWithNonExistantPid(array $expectedTitles, array $relatedLinksProvided)
    {
        $dbId = 999;
        $programme = $this->mockEntity('Programme', $dbId);

        $this->mockRepository->method('findByRelatedTo')->willReturn($relatedLinksProvided);

        $relatedLinks = $this->service()->findByRelatedToProgramme($programme);

        $this->assertCount(count($relatedLinksProvided), $relatedLinks);
        $this->assertContainsOnlyInstancesOf(RelatedLink::class, $relatedLinks);
        foreach ($expectedTitles as $i => $expectedTitle) {
            $this->assertEquals($expectedTitle, $relatedLinks[$i]->getTitle());
        }
    }

    public function dbRelatedlinksProvider(): array
    {
        return [
            'CASE: ' => [
                [],
                [['title' => 'RelatedLink1'], ['title' => 'RelatedLink2']],
            ],
            'CASE: ' => [
                [],
                []
            ],
        ];
    }
}
