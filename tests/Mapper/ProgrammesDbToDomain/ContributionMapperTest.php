<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Contribution;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgramme;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ContributionMapper;

class ContributionMapperTest extends BaseMapperTestCase
{
    protected $mockContributorMapper;

    protected $mockProgrammeMapper;

    protected $mockSegmentMapper;

    protected $mockVersionMapper;

    public function setUp()
    {
        $this->mockContributorMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ContributorMapper'
        );

        $this->mockProgrammeMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CoreEntityMapper'
        );

        $this->mockSegmentMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\SegmentMapper'
        );

        $this->mockVersionMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionMapper'
        );
    }

    public function testGetDomainModelWithContributedToSegment()
    {
        $contributorDbEntity = ['pid' => 'p01v0q3w'];
        $segmentDbEntity = ['pid' => 'b0000000'];

        $expectedContributorDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Contributor'
        );

        $expectedSegmentDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Segment'
        );

        $this->mockContributorMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($contributorDbEntity)
            ->willReturn($expectedContributorDomainEntity);

        $this->mockSegmentMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($segmentDbEntity)
            ->willReturn($expectedSegmentDomainEntity);

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

        $expectedEntity = new Contribution(
            new Pid('p0258652'),
            $expectedContributorDomainEntity,
            $expectedSegmentDomainEntity,
            'Actor',
            1,
            'Malcolm Tucker'
        );

        $mapper = $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModel($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
    }

    public function testGetDomainModelWithContributedToProgramme()
    {
        $contributorDbEntity = ['pid' => 'p01v0q3w'];
        $programmeDbEntity = ['pid' => 'b0000000', 'type' => 'Programme'];

        $expectedContributorDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Contributor'
        );

        $expectedProgrammeDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Programme'
        );

        $this->mockContributorMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($contributorDbEntity)
            ->willReturn($expectedContributorDomainEntity);

        $this->mockProgrammeMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($programmeDbEntity)
            ->willReturn($expectedProgrammeDomainEntity);

        $dbEntityArray = [
            'id' => 1,
            'pid' => 'p0258652',
            'position' => '1',
            'characterName' => 'Malcolm Tucker',
            'contributionToCoreEntity' => $programmeDbEntity,
            'contributor' => $contributorDbEntity,
            'creditRole' => [
                'id' => 1,
                'name' => 'Actor',
            ],
        ];

        $expectedEntity = new Contribution(
            new Pid('p0258652'),
            $expectedContributorDomainEntity,
            $expectedProgrammeDomainEntity,
            'Actor',
            1,
            'Malcolm Tucker'
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelWithContributedToVersion()
    {
        $contributorDbEntity = ['pid' => 'p01v0q3w'];
        $versionDbEntity = ['pid' => 'b0000000'];

        $expectedContributorDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Contributor'
        );

        $expectedVersionDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Version'
        );

        $this->mockContributorMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($contributorDbEntity)
            ->willReturn($expectedContributorDomainEntity);

        $this->mockVersionMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($versionDbEntity)
            ->willReturn($expectedVersionDomainEntity);

        $dbEntityArray = [
            'id' => 1,
            'pid' => 'p0258652',
            'position' => '1',
            'characterName' => 'Malcolm Tucker',
            'contributionToVersion' => $versionDbEntity,
            'contributor' => $contributorDbEntity,
            'creditRole' => [
                'id' => 1,
                'name' => 'Actor',
            ],
        ];

        $expectedEntity = new Contribution(
            new Pid('p0258652'),
            $expectedContributorDomainEntity,
            $expectedVersionDomainEntity,
            'Actor',
            1,
            'Malcolm Tucker'
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelWithNoContributedTo()
    {
        $contributorDbEntity = ['pid' => 'p01v0q3w'];

        $expectedContributorDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Contributor'
        );

        $expectedUnfetchedProgrammeDomainEntity = new UnfetchedProgramme();

        $this->mockContributorMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($contributorDbEntity)
            ->willReturn($expectedContributorDomainEntity);

        $dbEntityArray = [
            'id' => 1,
            'pid' => 'p0258652',
            'position' => '1',
            'characterName' => 'Malcolm Tucker',
            'contributor' => $contributorDbEntity,
            'creditRole' => [
                'id' => 1,
                'name' => 'Actor',
            ],
        ];

        $expectedEntity = new Contribution(
            new Pid('p0258652'),
            $expectedContributorDomainEntity,
            $expectedUnfetchedProgrammeDomainEntity,
            'Actor',
            1,
            'Malcolm Tucker'
        );

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
            'CoreEntityMapper' => $this->mockProgrammeMapper,
            'SegmentMapper' => $this->mockSegmentMapper,
            'VersionMapper' => $this->mockVersionMapper,
        ]));
    }
}
