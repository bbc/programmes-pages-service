<?php

namespace Tests\BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\VersionsService;
use BBC\ProgrammesPagesService\Service\LoggingServiceFactory;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Stopwatch\Stopwatch;
use Psr\Log\NullLogger;
use PHPUnit\Framework\TestCase;

/**
 * @covers BBC\ProgrammesPagesService\Service\LoggingServiceFactory
 */
class LoggingServiceFactoryTest extends TestCase
{
    public function testFactoryCallingFunction()
    {
        $logger = $this->createMock('Psr\Log\LoggerInterface');
        $logger->expects($this->once())->method('info')
            ->with($this->callback(function ($msg) {
                $expectedService = preg_quote('BBC\ProgrammesPagesService\Service\ProgrammesService::findByPidFull');
                return preg_match(
                    '/^ProgrammesPagesService: Called ' . $expectedService . ' \(Time Taken: \d{1,}ms\)$/',
                    $msg
                );
            }));

        $stopwatch = new Stopwatch();
        $factory = $this->serviceFactory($logger, $stopwatch);

        $factory->getProgrammesService()->findByPidFull(new Pid('b0000001'));

         // Assert items has been added to the stopwatch
        $this->assertCount(1, $stopwatch->getSectionEvents('__root__'));

        $this->assertArrayHasKey(
            'BBC\ProgrammesPagesService\Service\ProgrammesService::findByPidFull',
            $stopwatch->getSectionEvents('__root__')
        );
    }

    public function testFactoryCallingNonExistantFunctionThrowsError()
    {
        // We want a real service rather than a mock here so we can assert on the error messages
        $service = new VersionsService(
            $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository'),
            $this->createMock('BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionMapper'),
            $this->createMock('BBC\ProgrammesPagesService\Cache\CacheInterface')
        );

        $logger = $this->createMock('Psr\Log\LoggerInterface');
        $stopwatch = new Stopwatch();
        $factory = $this->serviceFactory($logger, $stopwatch);

        $this->expectException('Error');
        $this->expectExceptionMessage('Call to undefined method BBC\ProgrammesPagesService\Service\ServiceFactory::zzzzzGarbage()');

        $factory->zzzzzGarbage();
    }

    public function testProxyCallingFunction()
    {
        $mockVersion = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Version');
        $service = $this->createMock(VersionsService::CLASS);
        $service->method('findByPidFull')->willReturn($mockVersion);

        $stopwatch = new Stopwatch();

        $timedService = $this->serviceProxyClass($service, new NullLogger(), $stopwatch);

        $result = $timedService->findByPidFull(new Pid('b0000001'));

        // We don't really care what the result is, just as long as it doesn't
        // explode
        $this->assertSame($mockVersion, $result);

        // Assert items has been added to the stopwatch
        $this->assertCount(1, $stopwatch->getSectionEvents('__root__'));

        $this->assertArrayHasKey(
            get_class($service) . '::findByPidFull',
            $stopwatch->getSectionEvents('__root__')
        );
    }

    public function testProxyCallingFunctionWithBadArgumentsThrowsError()
    {
        // We want a real service rather than a mock here so we can assert on the error messages
        $service = new VersionsService(
            $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository'),
            $this->createMock('BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionMapper'),
            $this->createMock('BBC\ProgrammesPagesService\Cache\CacheInterface')
        );

        $timedService = $this->serviceProxyClass($service, new NullLogger(), new Stopwatch());

        $this->expectException('TypeError');
        $this->expectExceptionMessage('Too few arguments to function ' . VersionsService::CLASS . '::findByPidFull(), 0 passed in');

        $timedService->findByPidFull();
    }

    public function testProxyCallingNonExistantFunctionThrowsError()
    {
        // We want a real service rather than a mock here so we can assert on the error messages
        $service = new VersionsService(
            $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository'),
            $this->createMock('BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionMapper'),
            $this->createMock('BBC\ProgrammesPagesService\Cache\CacheInterface')
        );

        $timedService = $this->serviceProxyClass($service, new NullLogger(), new Stopwatch());

        $this->expectException('Error');
        $this->expectExceptionMessage('Call to undefined method BBC\ProgrammesPagesService\Service\VersionsService::zzzzzGarbage()');

        $timedService->zzzzzGarbage();
    }

    private function serviceFactory($logger, $stopwatch)
    {
        $mockEntityManager = $this->createMock('Doctrine\ORM\EntityManager');
        $mockEntityManager->method('getRepository')->willReturn(
            $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository')
        );

        return new LoggingServiceFactory(
            $mockEntityManager,
            $this->createMock('BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperFactory'),
            $this->createMock('BBC\ProgrammesPagesService\Cache\CacheInterface'),
            $logger,
            $stopwatch
        );
    }

    private function serviceProxyClass($service, $logger, $stopwatch)
    {
        $factory = $this->serviceFactory($logger, $stopwatch);

        $reflection = new \ReflectionClass($factory);
        $method = $reflection->getMethod('proxyClass');
        $method->setAccessible(true);

        return $method->invoke($factory, $service, $logger, $stopwatch);
    }
}
