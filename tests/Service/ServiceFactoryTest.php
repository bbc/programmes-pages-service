<?php

namespace Tests\BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Cache\Cache;
use BBC\ProgrammesPagesService\Service\ServiceFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\NullAdapter;

/**
 * @covers BBC\ProgrammesPagesService\Service\ServiceFactory
 */
class ServiceFactoryTest extends TestCase
{
    const SERVICE_NS = 'BBC\ProgrammesPagesService\Service\\';

    const MAPPER_NS = 'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\\';

    const ENTITY_REPOSITORY_NS = 'BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\\';

    /**
     * @dataProvider serviceNamesDataProvider
     */
    public function testGetters($serviceName, $expectedRepositories, $expectedMapper)
    {
        $serviceFactory = new ServiceFactory(
            $this->entityManager($expectedRepositories),
            $this->mapperFactory($expectedMapper),
            $this->getMockBuilder(Cache::class)
                ->setConstructorArgs([new NullAdapter(), ''])
                ->setMethods(null)
                ->getMock()
        );

        $service = $serviceFactory->{'get' . $serviceName}();

        // Assert it returns an instance of the correct class
        $this->assertInstanceOf(self::SERVICE_NS . $serviceName, $service);

        // Requesting the same service multiple times reuses the same instance
        // of a service, rather than creating a new one every time
        $this->assertSame($service, $serviceFactory->{'get' . $serviceName}());
    }

    public function serviceNamesDataProvider()
    {
        return [
            ['AtozTitlesService', ['AtozTitle'], 'AtozTitleMapper'],
            ['BroadcastsService', ['Broadcast'], 'BroadcastMapper'],
            ['CategoriesService', ['Category'], 'CategoryMapper'],
            ['CollapsedBroadcastsService', ['CollapsedBroadcast', 'Service'], 'CollapsedBroadcastMapper'],
            ['ContributionsService', ['Contribution'], 'ContributionMapper'],
            ['ContributorsService', ['Contributor'], 'ContributorMapper'],
            ['NetworksService', ['Network'], 'NetworkMapper'],
            ['ProgrammesService', ['CoreEntity'], 'ProgrammeMapper'],
            ['RelatedLinksService', ['RelatedLink'], 'RelatedLinkMapper'],
            ['SegmentsService', ['Segment'], 'SegmentMapper'],
            ['SegmentEventsService', ['SegmentEvent'], 'SegmentEventMapper'],
            ['ServicesService', ['Service'], 'ServiceMapper'],
            ['VersionsService', ['Version'], 'VersionMapper'],
        ];
    }

    private function entityManager(array $repoNames)
    {
        $mockEntityManager = $this->createMock('Doctrine\ORM\EntityManager');

        $argMap = [];
        $valueMap = [];
        foreach ($repoNames as $repoName) {
            $arg = 'ProgrammesPagesService:' . $repoName;
            $mockRepo = $this->createMock(self::ENTITY_REPOSITORY_NS . $repoName . 'Repository');

            $argMap[] = [$arg];
            $valueMap[] = [$arg, $mockRepo];
        }

        $mockEntityManager->expects($this->exactly(count($repoNames)))
            ->method('getRepository')
            ->withConsecutive(...$argMap)
            ->will($this->returnValueMap($valueMap));

        return $mockEntityManager;
    }

    private function mapperFactory($mapperClass)
    {
        $mockMapperFactory = $this->createMock(self::MAPPER_NS . 'MapperFactory');

        $mockMapper = $this->createMock(self::MAPPER_NS . $mapperClass);

        $mockMapperFactory->expects($this->atLeastOnce())
            ->method('get' . $mapperClass)
            ->willReturn($mockMapper);

        return $mockMapperFactory;
    }
}
