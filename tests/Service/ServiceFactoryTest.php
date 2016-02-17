<?php

namespace Tests\BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Service\ServiceFactory;
use PHPUnit_Framework_TestCase;

/**
 * @covers BBC\ProgrammesPagesService\Service\ServiceFactory
 */
class ServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    private $mockEntityManager;

    private $mockMapperProvider;

    public function setUp()
    {
        $this->mockEntityManager = $this->getMockWithoutInvokingTheOriginalConstructor(
            'Doctrine\ORM\EntityManager'
        );

        $this->mockMapperProvider = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperProvider'
        );
    }

    public function testGetProgrammesService()
    {
        $this->setUpEntityManager(
            'ProgrammesPagesService:CoreEntity',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository'
        );

        $this->setUpMapperProvider(
            'getProgrammeMapper',
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper'
        );

        $serviceFactory = new ServiceFactory(
            $this->mockEntityManager,
            $this->mockMapperProvider
        );

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Service\ProgrammesService',
            $serviceFactory->getProgrammesService()
        );
    }

    private function setUpEntityManager($repoName, $repoClass)
    {
        $mockRepo = $this->getMockWithoutInvokingTheOriginalConstructor($repoClass);

        $this->mockEntityManager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo($repoName))
            ->willReturn($mockRepo);
    }

    private function setUpMapperProvider($method, $mapperClass)
    {
        $mockMapper = $this->getMockWithoutInvokingTheOriginalConstructor($mapperClass);

        $this->mockMapperProvider->expects($this->once())
            ->method($method)
            ->willReturn($mockMapper);
    }
}
