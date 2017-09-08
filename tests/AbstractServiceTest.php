<?php

namespace Tests\BBC\ProgrammesPagesService;

use BBC\ProgrammesPagesService\Cache\Cache;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\NullAdapter;

abstract class AbstractServiceTest extends TestCase
{
    protected $mockRepository;

    protected $mockMapper;

    protected $mockCache;

    protected function setUpRepo($repositoryName)
    {
        $this->mockRepository = $this->getRepo($repositoryName);
    }

    protected function getRepo($repositoryName)
    {
        return $this->createMock($repositoryName);
    }

    protected function setUpCache()
    {
        $this->mockCache = $this->getMockBuilder(Cache::class)
            ->setConstructorArgs([new NullAdapter(), ''])
            ->setMethods(null)
            ->getMock();
    }

    protected function setUpMapper($mapperName, $callback)
    {
        $this->mockMapper = $this->createMock($mapperName);
        $this->mockMapper->method('getDomainModel')->will($this->returnCallback($callback));
    }
}
