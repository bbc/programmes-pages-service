<?php

namespace Tests\BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Service\ServiceFactory;
use PHPUnit_Framework_TestCase;

/**
 * @covers BBC\ProgrammesPagesService\Service\ServiceFactory
 */
class ServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    const SERVICE_NS = 'BBC\ProgrammesPagesService\Service\\';

    const MAPPER_NS = 'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\\';

    const ENTITY_REPOSITORY_NS = 'BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\\';

    /**
     * @dataProvider serviceNamesDataProvider
     */
    public function testGetters($serviceName, $expectedRepository, $expectedMapper)
    {
        $serviceFactory = new ServiceFactory(
            $this->entityManager($expectedRepository),
            $this->mapperFactory($expectedMapper)
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
            ['ProgrammesService', 'CoreEntity', 'ProgrammeMapper'],
            ['RelatedLinksService', 'RelatedLink', 'RelatedLinkMapper'],
            ['VersionsService', 'Version', 'VersionMapper'],
        ];
    }

    private function entityManager($repoName)
    {
        $mockEntityManager = $this->getMockBuilder(
            'Doctrine\ORM\EntityManager'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $mockRepo = $this->getMockBuilder(
            self::ENTITY_REPOSITORY_NS . $repoName . 'Repository'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $mockEntityManager->expects($this->atLeastOnce())
            ->method('getRepository')
            ->with('ProgrammesPagesService:' . $repoName)
            ->willReturn($mockRepo);

        return $mockEntityManager;
    }

    private function mapperFactory($mapperClass)
    {
        $mockMapperFactory = $this->getMockBuilder(
            self::MAPPER_NS . 'MapperFactory'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $mockMapper = $this->getMockBuilder(
            self::MAPPER_NS . $mapperClass
        )
            ->disableOriginalConstructor()
            ->getMock();

        $mockMapperFactory->expects($this->atLeastOnce())
            ->method('get' . $mapperClass)
            ->willReturn($mockMapper);

        return $mockMapperFactory;
    }
}
