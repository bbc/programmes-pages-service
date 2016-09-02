<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ContributionMapper;
use BBC\ProgrammesPagesService\Domain\Entity\Contribution;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class ContributionMapperTest extends BaseMapperTestCase
{
    protected $mockContributorMapper;

    public function setUp()
    {
        $this->mockContributorMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ContributorMapper'
        );
    }

    public function testGetDomainModel()
    {
        $contributorDbEntity = ['pid' => 'p01v0q3w'];
        $segmentDbEntity = ['pid' => 'b0000000'];

        $expectedContributorDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Contributor'
        );

        $this->mockContributorMapper->expects($this->exactly(2))
            ->method('getDomainModel')
            ->with($contributorDbEntity)
            ->willReturn($expectedContributorDomainEntity);

        $dbEntityArray = [
            'id' => 1,
            'pid' => 'p0258652',
            'position' => '1',
            'characterName' => 'Malcolm Tucker',
            'contributionToSegment' => $segmentDbEntity,
            'contributor' => $contributorDbEntity,
            'creditRole' => [
                'id' => 1,
                'name' => 'Actor',
            ],
        ];

        // Get the mocked Segment, yes we are matching from the result but this is not the test
        $segment = $this->getMapper()->getDomainModel($dbEntityArray)->getContributedTo();

        $pid = new Pid('p0258652');
        $expectedEntity = new Contribution($pid, $expectedContributorDomainEntity, $segment, 'Actor', 1, 'Malcolm Tucker');

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage All Contributions must be joined to a Contributor
     */
    public function testGetDomainModelWithNoContributor()
    {
        $dbEntityArray = [
            'id' => 1,
            'pid' => 'p0258652',
            'position' => '1',
            'characterName' => 'Malcolm Tucker',
            'creditRole' => [
                'id' => 1,
                'name' => 'Actor',
            ],
        ];

        $this->getMapper()->getDomainModel($dbEntityArray);
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage All Contributions must be joined to a CreditRole
     */
    public function testGetDomainModelWithNoCreditRole()
    {
        $contributorDbEntity = ['pid' => 'p01v0q3w'];
        $segmentDbEntity = ['pid' => 'b0000000'];

        $expectedContributorDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Contributor'
        );

        $this->mockContributorMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($contributorDbEntity)
            ->willReturn($expectedContributorDomainEntity);

        $dbEntityArray = [
            'id' => 1,
            'pid' => 'p0258652',
            'position' => '1',
            'contributionToSegment' => $segmentDbEntity,
            'characterName' => 'Malcolm Tucker',
            'contributor' => $contributorDbEntity,
        ];

        $this->getMapper()->getDomainModel($dbEntityArray);
    }

    private function getMapper(): ContributionMapper
    {
        return new ContributionMapper($this->getMapperFactory([
            'ContributorMapper' => $this->mockContributorMapper,
        ]));
    }
}
